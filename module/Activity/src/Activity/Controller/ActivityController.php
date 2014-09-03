<?php   
####################Activity Controller #################################

namespace Activity\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;	//Needed for checking User sessi on
use Zend\Authentication\Adapter\DbTable as AuthAdapter;	//Db apaptor 
use Zend\View\Renderer\PhpRenderer;

use Zend\Crypt\BlockCipher;		# For encryption 
use Groups\Model\Groups;  	#Planet/Galaxy  class
use User\Model\User;  	#User  class
use Groups\Model\GroupsTable; #Planet/Galaxy table class
use Groups\Model\UserGroup;	#user group class
use \Exception;		#Exception class for handling exception
use Activity\Model\Activity; #Activity class for loading activity
use Activity\Model\ActivityInvite; #Activity class for loading activity Invite
use Activity\Model\ActivityRsvp; #Activity class for loading activity Rsvp
use Notification\Model\UserNotification; 
use Activity\Model\ActivityTagTable;
 
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
#activity form
use Activity\Form\ActivityAddForm;
use Activity\Form\ActivityAddFormFilter;   

class ActivityController extends AbstractActionController
{
    protected $groupTable;		#variable to hold the group model configration
	protected $userGroupTable;	#variable to hold the user group model configration
	protected $userTable;		#variable to hold the user model configration
	protected $userProfileTable;	#variable to hold the user profile model configration
	protected $activityTable;		#variable to hold the activity model confirgration
	protected $activityInviteTable;		#variable to hold the activity Invite model configration
	protected $activityRsvpTable;		#variable to hold the activity Rsvp configration
	protected $userNotificationTable;
	protected $groupSettings;
	protected $usergroupSettings;
	protected $groupActivityTagp;
	protected $groupActivityTag;
	protected $likeTable;
	protected $commentTable;
	protected $groupjoiningrequestTable;
	protected $groupTagTable;
	protected $grouppermissionsTable;
	#this function will load the css and javascript need for particular action
	protected function getViewHelper($helperName)	{
    	return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}   
 	#This function will load all activites.if you supply planet Id then it will load Id of Planet else it will load all activitiws
    public function indexAction(){
		$error =array(); 
		$success =array(); 
		$identity ="";
		$userData =array();
		$subGroupId ="";
		$groupData =array();
		$subGroupData =array();	
		$activities =array();	
		$finalActivity =array();
		$activityData_upcoming = 	array();	
		$order =array();
		$where =array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$incVar = 0;
		$userRegisteredGroup =array();    
		if ($auth->hasIdentity()) {  
			$sm = $this->getServiceLocator();		
			$request   = $this->getRequest();            
           	$identity = $auth->getIdentity();			
			$userData = $this->getUserTable()->getUser($identity->user_id);		
			$subGroupId= $this->params('group_id'); 		
			if(!empty($subGroupId)){			
				$subGroupData = $this->getGroupTable()->getSubGroup($subGroupId);				
				if(isset($subGroupData->group_id) && !empty($subGroupData->group_id) && isset($subGroupData->group_parent_group_id) && !empty($subGroupData->group_parent_group_id)){				 
					$groupData = $this->getGroupTable()->getGroup($subGroupData->group_parent_group_id);				 
					$userRegisteredGroup =$this->getUserGroupTable()->getUserGroup($userData->user_id, $subGroupData->group_id);		
				}
				$page = 0;
				$request   = $this->getRequest();				 		
				$post = $request->getPost();		
				if ($request->isPost()){
					$page =$post->get('page');
					if(!$page)
					$page = 0;
				}
				$offset = $page*10;
				$activities_upcoming   = $this->getActivityTable()->fetchAll_upcomingWithLikes($subGroupData->group_id,1,$identity->user_id,$offset);
				if($activities_upcoming->count()){
					foreach($activities_upcoming as $row){
				
						$row = get_object_vars($row);					 
						$blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
						$blockCipher->setKey('sfds^&*%(^$%^$%^KMNVHDrt#$$$%#@@');
						$row['encrypted_activity_id'] = $blockCipher->encrypt($row['group_activity_id']);				 
						$activityData_upcoming[$incVar] = $row;					
						$order = array();
						$order = array("group_activity_rsvp_id DESC");						
						$whereRsvp =array();
						$whereRsvp =array('y2m_group_activity_rsvp.group_activity_rsvp_activity_id'=> $row['group_activity_id']);				
						$rsvpactivityData_upcoming = $this->getActivityRsvpTable()->fetchAll($whereRsvp, $order, 6);
						if($rsvpactivityData_upcoming->count()){
							$jncVar = 0;
							foreach($rsvpactivityData_upcoming as $rsvpRow){
								$activityData_upcoming[$incVar]['rsvp'][$jncVar] =	get_object_vars($rsvpRow);
								$jncVar++;
							} 
						}	
						$incVar++;			
					}				
				}else{
					$error[] ='No upcoming activities found in this group';
				}
			}else{
				$error[] ='This group is corrently not existing';
			}			
        }else{
			$error[] ='Invalid access';
		}	
		$viewModel = new ViewModel(array('identity' => $identity, 'groupData' => $groupData, 'userData' => $userData, 'userRegisteredGroup' =>$userRegisteredGroup, 'subGroupData' => $subGroupData, 'activityData_upcoming' => $activityData_upcoming,'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages(),'page'=>$page));
    	$viewModel->setTerminal($request->isXmlHttpRequest());
    	return $viewModel;	 
    }	
	#this fuction will save user rsvp
	public function activityrsvpAction(){
		$sm = $this->getServiceLocator(); 
		$error =array();
		$success =array();
		$identity ="";		 		
		$auth = new AuthenticationService();	
		$identity = null;        
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			 		 
			if(isset($identity->user_id) && !empty($identity->user_id)){ 
				$request   = $this->getRequest();				 		
				$post = $request->getPost();		
				if ($request->isPost()){
					$activity_id =$post->get('activity_id');					
					$activityData =$this->getActivityTable()->getActivity($activity_id);				 
					if(!empty($activityData)&& $activityData->group_activity_id!=''){						
						$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($activityData->group_activity_group_id);
						if(isset($subGroupData->group_id) && !empty($subGroupData->group_id) && isset($subGroupData->group_parent_group_id) && !empty($subGroupData->group_parent_group_id)){	
							if($this->getGroupTable()->is_member($activityData->group_activity_group_id,$identity->user_id)){
							if(strtotime($activityData->group_activity_start_timestamp)>strtotime(date("Y-m-d h:s"))){
								if($activityData->group_activity_type == 'invited'){
									if($this->getActivityInviteTable()->checkInvited($activity_id,$identity->user_id)){
										$groupData = $this->getGroupTable()->getGroup($subGroupData->group_parent_group_id);	
										$activityRsvpData = array();
										$activityRsvpData['group_activity_rsvp_user_id'] = $identity->user_id;
										$activityRsvpData['group_activity_rsvp_activity_id'] =  $activityData->group_activity_id; 
										$objUser = new User();
										$activityRsvpData['group_activity_rsvp_added_ip_address'] = $objUser->getUserIp();
										$activityRsvpData['group_activity_rsvp_group_id'] = $activityData->group_activity_group_id; 
										$activityRsvpObject = new ActivityRsvp();
										$activityRsvpObject->exchangeArray($activityRsvpData);
										$insertedActivityRsvpId =""; 
										$insertedActivityRsvpId = $this->getActivityRsvpTable()->saveActivityRsvp($activityRsvpObject); 
										if(isset($insertedActivityRsvpId) && !empty($insertedActivityRsvpId)){
											$success[] ='Rsvp done Succesfully';
											$joinedMembers = $this->getActivityRsvpTable()->getAllJoinedMembers($activityData->group_activity_id,$activityData->group_activity_group_id);											 
											foreach($joinedMembers as $members){
												if($members->user_id!=$activityData->group_activity_owner_user_id && $members->activity!='no'&&$members->user_id!=$identity->user_id){
													$UserGroupNotificationData = array();						
													$UserGroupNotificationData['user_notification_user_id'] = $members->user_id;
													$config = $this->getServiceLocator()->get('Config');
													$base_url = $config['pathInfo']['base_url'];
													$msg = $identity->user_given_name." joined the activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activityData->group_activity_id."'>".$activityData->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
													$UserGroupNotificationData['user_notification_content'] = $msg;								 
													$userObject = new user(array());
													$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
													$UserGroupNotificationData['user_notification_status'] = 0;									
													$UserGroupNotificationSaveObject = new UserNotification();
													$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
													$insertedUserGroupNotificationId ="";
													$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
													$this->sendNotificationMail($msg,'Activity Join',$members->user_email);													
												}
											}
											$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
											$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($activityData->group_activity_owner_user_id,$activityData->group_activity_group_id);
											$permission = 1;
											
											if((isset($user_group_settings->activity)&&$user_group_settings->activity=='no')){
												$permission =0;
											}
											if($permission&&$activityData->group_activity_owner_user_id!=$identity->user_id){ 
												$UserGroupNotificationData = array();						
												$UserGroupNotificationData['user_notification_user_id'] = $activityData->group_activity_owner_user_id;
												$config = $this->getServiceLocator()->get('Config');
													$base_url = $config['pathInfo']['base_url'];
													$msg = $identity->user_given_name." joined the activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activityData->group_activity_id."'>".$activityData->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
												$UserGroupNotificationData['user_notification_content'] = $msg;								 
												$userObject = new user(array());
												$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
												$UserGroupNotificationData['user_notification_status'] = 0;									
												$UserGroupNotificationSaveObject = new UserNotification();
												$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
												$insertedUserGroupNotificationId ="";
												$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
												$userData = $this->getUserTable()->getUser($activityData->group_activity_owner_user_id);
												$this->sendNotificationMail($msg,'Activity Join',$userData->user_email);
												
											}
											//$activityData->group_activity_owner_user_id
											$return_array = array('msg'=>'Rsvp done Succesfully','error'=>0);
											echo json_encode($return_array);die();	
										}else{											  
											$return_array = array('msg'=>'Error in Rsvp','error'=>1);
											echo json_encode($return_array);die();
										}
									}
									else{										 
										$return_array = array('msg'=>'This activitu is only for invited members','error'=>1);
										echo json_encode($return_array);die();
									}
								}else{
									$groupData = $this->getGroupTable()->getGroup($subGroupData->group_parent_group_id);	
									$rsvpUser =$this->getActivityRsvpTable()->getActivityRsvpOfUser($identity->user_id, $activityData->group_activity_id);							
									if(isset($rsvpUser->group_activity_rsvp_id) && !empty($rsvpUser->group_activity_rsvp_id)){							 
										$return_array = array('msg'=>'already attending','error'=>1);
										echo json_encode($return_array);die();									
									}else{ 
										$activityRsvpData = array();
										$activityRsvpData['group_activity_rsvp_user_id'] = $identity->user_id;
										$activityRsvpData['group_activity_rsvp_activity_id'] =  $activityData->group_activity_id;
										$objUser = new User();										
										$activityRsvpData['group_activity_rsvp_added_ip_address'] = $objUser->getUserIp();
										$activityRsvpData['group_activity_rsvp_group_id'] = $activityData->group_activity_group_id; 
										$activityRsvpObject = new ActivityRsvp();
										$activityRsvpObject->exchangeArray($activityRsvpData);
										$insertedActivityRsvpId =""; 
										$insertedActivityRsvpId = $this->getActivityRsvpTable()->saveActivityRsvp($activityRsvpObject); 
										if(isset($insertedActivityRsvpId) && !empty($insertedActivityRsvpId)){
											$success[] ='Rsvp done Succesfully';
											$joinedMembers = $this->getActivityRsvpTable()->getAllJoinedMembers($activityData->group_activity_id,$activityData->group_activity_group_id);
											foreach($joinedMembers as $members){
												if($members->user_id!=$activityData->group_activity_owner_user_id && $members->activity!='no'&&$members->user_id!=$identity->user_id){
													$UserGroupNotificationData = array();						
													$UserGroupNotificationData['user_notification_user_id'] = $members->user_id;
													$config = $this->getServiceLocator()->get('Config');
													$base_url = $config['pathInfo']['base_url'];
													$msg = $identity->user_given_name." joined the activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activityData->group_activity_id."'>".$activityData->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";													
													$UserGroupNotificationData['user_notification_content'] = $msg;								 
													$userObject = new user(array());
													$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
													$UserGroupNotificationData['user_notification_status'] = 0;									
													$UserGroupNotificationSaveObject = new UserNotification();
													$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
													$insertedUserGroupNotificationId ="";
													$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
													$this->sendNotificationMail($msg,'Activity Join',$members->user_email);													
												}
											}
											$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
											$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($activityData->group_activity_owner_user_id,$activityData->group_activity_group_id);
											$permission = 1;
											if((isset($user_group_settings->activity)&&$user_group_settings->activity=='no')){
												$permission =0;
											}
											if($permission&&$activityData->group_activity_owner_user_id!=$identity->user_id){
												$UserGroupNotificationData = array();						
												$UserGroupNotificationData['user_notification_user_id'] = $activityData->group_activity_owner_user_id;
												$config = $this->getServiceLocator()->get('Config');
													$base_url = $config['pathInfo']['base_url'];
													$msg = $identity->user_given_name." joined the activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activityData->group_activity_id."'>".$activityData->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
												$UserGroupNotificationData['user_notification_content'] = $msg;								 
												$userObject = new user(array());
												$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
												$UserGroupNotificationData['user_notification_status'] = 0;									
												$UserGroupNotificationSaveObject = new UserNotification();
												$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
												$insertedUserGroupNotificationId ="";
												$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
												$userData = $this->getUserTable()->getUser($activityData->group_activity_owner_user_id);
												$this->sendNotificationMail($msg,'Activity Join',$userData->user_email);
												
											}
											$return_array = array('msg'=>'Rsvp done Succesfully','error'=>0);
											echo json_encode($return_array);die();	
										}else{
											 
											$return_array = array('msg'=>'Error in Rsvp','error'=>1);
											echo json_encode($return_array);die();											
										}							
									}
								}
								}else{								 
								$return_array = array('msg'=>'Sorry This event is already completed','error'=>1);
								echo json_encode($return_array);die();										
								}
							}else{								 
								$return_array = array('msg'=>'You are not member of this planet','error'=>1);
								echo json_encode($return_array);die();										
							}
						}else{
							$return_array = array('msg'=>'Invalid Planet','error'=>1);
							echo json_encode($return_array);die();	
							 		
						}
					 }else{
						$return_array = array('msg'=>'Activity Does not Exist','error'=>1);
						echo json_encode($return_array);die();	
					 	 			 
					 }			 
				 }else{
					$return_array = array('msg'=>'Invalid access','error'=>1);
					echo json_encode($return_array);die();	
					 
				 }
			 }else{
				$return_array = array('msg'=>'Your session expired..Please try again after login','error'=>1);
				echo json_encode($return_array);die();					 
			 }
		}else{
			$return_array = array('msg'=>'Your session expired..Please try again after login','error'=>1);
			echo json_encode($return_array);die();			 
		}		
		  
	}
	public function quitrsvpAction(){
		$error =array();
		$success =array();
		$identity ="";		 		
		$auth = new AuthenticationService();	
		$identity = null;   
		$return_array = array();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			if(isset($identity->user_id) && !empty($identity->user_id)){ 
				$request   = $this->getRequest();				 		
				$post = $request->getPost();
				if ($request->isPost()){
					$activity_id =$post->get('activity_id');
					$activityData =$this->getActivityTable()->getActivity($activity_id);
					if(!empty($activityData)&& $activityData->group_activity_id!=''){		
						$subGroupData = $this->getGroupTable()->getSubGroup($activityData->group_activity_group_id);
						if(isset($subGroupData->group_id) && !empty($subGroupData->group_id) && isset($subGroupData->group_parent_group_id) && !empty($subGroupData->group_parent_group_id)){
							if(strtotime($activityData->group_activity_start_timestamp)>strtotime(date("Y-m-d h:s"))){
							$groupData = $this->getGroupTable()->getGroup($subGroupData->group_parent_group_id);	
							$rsvpUser =$this->getActivityRsvpTable()->getActivityRsvpOfUser($identity->user_id, $activityData->group_activity_id);
							if(isset($rsvpUser->group_activity_rsvp_id) && !empty($rsvpUser->group_activity_rsvp_id)){
								if($this->getActivityRsvpTable()->removeActivityRsvp($activity_id,$identity->user_id)){
									$return_array = array("msg"=>"You are removed from rsvp list",
									'error'=>0,									 
									); 
									if($identity->user_id!=$activityData->group_activity_owner_user_id){
										$UserGroupNotificationData = array();						
										$UserGroupNotificationData['user_notification_user_id'] = $activityData->group_activity_owner_user_id;							
										$UserGroupNotificationData['user_notification_content'] = $identity->user_given_name." quit from the activity of Planet <a href='".$this->url()->fromRoute('groups/planethome', array('action' => 'planethome', 'group_id'=>$groupData->group_seo_title, 'planet_id'=>$subGroupData->group_seo_title), $options=array())."'>".$subGroupData->group_title."</a>";								 
										$userObject = new user(array());
										$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
										$UserGroupNotificationData['user_notification_status'] = 0;									
										$UserGroupNotificationSaveObject = new UserNotification();
										$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
										$insertedUserGroupNotificationId ="";
										$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);	
									}
								}else{
									$return_array = array("msg"=>"Error in RSVP",
									'error'=>1,									 
									);	
								}
							}else{
								$return_array = array("msg"=>"You are not joined in this event",
									'error'=>1,									 
									);	 
							}
						}else{
							$return_array = array("msg"=>"This event is already completed",
									'error'=>1,									 
									); 
						}
						}else{
							$return_array = array("msg"=>"Invalid Planet",
									'error'=>1,									 
									); 
						}
					}else{
						$return_array = array("msg"=>"Activity Does not Exist",
									'error'=>1,									 
									);  
					}					
				}else{
					$return_array = array("msg"=>"Invalid access",
									'error'=>1,									 
									);  
				}
			}else{
				$return_array = array("msg"=>"User Not Found",
									'error'=>1,									 
									);   
			}
		}else{
			$return_array = array("msg"=>"Invalid access",
									'error'=>1,									 
									);   
		}
		 echo json_encode($return_array);die();
	}
	public function viewAction(){	  
		$this->layout('layout/planet_home');
		$auth = new AuthenticationService();	
		$identity = null;
		$error = array();
		$viewModel = new ViewModel();
		$is_admin = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('planet_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);				
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$activity_id = $this->getEvent()->getRouteMatch()->getParam('id');
					if($activity_id){
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
						$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}						 
						$activity_approval = 0;
						if($admin_status->is_admin){							 
							$activity_approval = 1;
						}
						$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);						 
						foreach($permissions as $row){							 
							if($row->function_id == 8){
								$activity_approval = 1;	
							}
						}
						$viewModel->setVariable('activity_approval', $activity_approval);
						$viewModel->setVariable('is_admin', $is_admin);
						$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
						$viewModel->setVariable('group_settings', $group_settings);					 
						$groupTopWidget = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'groupTop',
									'group_id'     => $group_seo,
									'sub_group_id' => $planet_seo,
								));
						$activity_permission = 0;
						if(!empty($group_settings)){
							if($admin_status->is_admin){						 
								$activity_permission = 1;						 
							}elseif($group_settings->group_activity_settings == 'Any'){
								$activity_permission = 1;	
							}
							elseif($group_settings->group_activity_settings == 'AdminApproval'){
								$activity_permission = 1;								
							}
							elseif($group_settings->group_activity_settings == 'OnlyAdmin'){
								 
								if(!empty($user_role)){
									$activity_permission = 1;
								}else{
									$activity_permission = 0;
								}
							}
							else{
								$activity_permission = 0;
							} 
						}
						if($this->getUserGroupJoiningRequestTable()->checkIfrequestExist($identity->user_id,$planet_id)){
							$viewModel->setVariable('is_request', 1);
						}
						else{
							$viewModel->setVariable('is_request', 0);
						}
						$viewModel->setVariable('admin_status', $admin_status);	
						$viewModel->setVariable('user_id', $identity->user_id);	
						$viewModel->setVariable('planetdetails', $planet_data);	
						$viewModel->addChild($groupTopWidget, 'groupTopWidget');
						$viewModel->setVariable('activity_permission', $activity_permission);
						$activity = $this->getActivityTable()->getOneActivityWithMembercount($identity->user_id,$planet_id,$activity_id);
						if(!empty($activity)){
							$activity_details = array(
													"group_activity_title" => $activity->group_activity_title,
													"group_activity_location" => $activity->group_activity_location,
													"group_activity_content" => $activity->group_activity_content,
													"group_activity_start_timestamp" => $activity->group_activity_start_timestamp,
													"group_activity_owner_user_id" => $activity->group_activity_owner_user_id,
													"group_activity_id" => $activity->group_activity_id,
													"group_activity_type" => $activity->group_activity_type,
													"group_activity_status" => $activity->group_activity_status,
													"is_member" => $activity->is_member,
													"user_given_name" => $activity->user_given_name,
													"user_id" => $activity->user_id,
													"user_profile_name" => $activity->user_profile_name,
													"user_register_type" => $activity->user_register_type,
													"user_fbid" => $activity->user_fbid,
													"profile_photo" => $activity->profile_photo,
													"member_count" => $activity->member_count,
													"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id),
													"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activity->group_activity_id)->comment_count,
													'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id,2,0),
													'members' =>$this->getActivityRsvpTable()->getJoinMembersWithFriendshipStatus($activity->group_activity_id,$identity->user_id,25,0)
													);
							$viewModel->setVariable('activity', $activity_details);
						}else{
							return $this->redirect()->toRoute('groups/planethome', array('group_id' => $group_seo,'planet_id'=>$planet_seo));
						}
						$LimitedgroupTag =	$this->getGroupTagTable()->fetchAllTagsOfGroup($planet_id,10,0);
						$viewModel->setVariable('LimitedgroupTag', $LimitedgroupTag);
						$ActivityTags =	$this->getActivityTagTable()->getActivityTags($activity_id);
						$viewModel->setVariable('ActivityTags', $ActivityTags);
						
					}else{
						return $this->redirect()->toRoute('groups/planethome', array('group_id' => $group_seo,'planet_id'=>$planet_seo));
					}
				}else{	
					$error[] ='This group is not existing in the system';
				}
			}else{
				$error[] ='Invalid access';
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		return $viewModel;
	} 
	#Activity tab for Planet Main page	
    public function activitydetailAction(){		
		$error =array();
		$success =array();
		$identity = null;
		$form = '';
		$auth = new AuthenticationService();
		$selectAllUserForPlanet = '';
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			$subGroupId= $this->params('group_id');
			if(isset($identity->user_id) && !empty($identity->user_id) && isset($subGroupId) && !empty($subGroupId)){
				$subGroupData = $this->getGroupTable()->getSubGroup($subGroupId);
				$GroupData = $this->getGroupTable()->getGroup($subGroupData->group_parent_group_id);				 
				if(!empty($subGroupData)){
					$userRegisteredGroup =$this->getUserGroupTable()->getUserGroup($identity->user_id, $subGroupData->group_id);
					if(!empty($userRegisteredGroup)){
						$userList =$this->getUserGroupTable()->fetchAllUserListForGroup($subGroupData->group_id,$identity->user_id);					
						$excludeUser =array();						
						$excludeUser['user_id'] = $identity->user_id;
						$selectAllUserForPlanet = UserGroup::selectFormatAllUserListGroupEncrypted($userList, $excludeUser); 
						$form = new ActivityAddForm($selectAllUserForPlanet);
						$form->get('submit')->setAttribute('value', 'Add');	
					}
					$order = array("group_activity_id DESC");				
					$where =array('y2m_group_activity.group_activity_group_id'=> $subGroupData->group_id);
					$activities_past       =$this->getActivityTable()->getAllActivityWithLikes($subGroupData->group_id,1,$identity->user_id);
					//$activities_past       = $this->getActivityTable()->fetchAll_past($where, $order, 10);
					$activityData_past     = array();
					$incVar=0;				
					if($activities_past->count()){
						foreach($activities_past as $row){				
							$row = get_object_vars($row);					
							$blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
							$blockCipher->setKey('sfds^&*%(^$%^$%^KMNVHDrt#$$$%#@@');
							$row['encrypted_activity_id'] = $blockCipher->encrypt($row['group_activity_id']);				 
							$activityData_past[$incVar] = $row;
							$order = array();
							$order = array("group_activity_rsvp_id DESC");
							$whereRsvp =array();
							$whereRsvp =array('y2m_group_activity_rsvp.group_activity_rsvp_activity_id'=> $row['group_activity_id']);
							$rsvpactivityData_past = $this->getActivityRsvpTable()->fetchAll($whereRsvp, $order, 6);
							if($rsvpactivityData_past->count()){
								$jncVar = 0;
								foreach($rsvpactivityData_past as $rsvpRow){
									$activityData_past[$incVar]['rsvp'][$jncVar] =	get_object_vars($rsvpRow);
									$jncVar++;
								} 
							}	
							$incVar++;			
						}
					}else{
				
					}//	echo "<pre>";	print_r($activityData_past);die();
					$order = array("group_activity_id DESC");				
					$where =array('y2m_group_activity.group_activity_group_id'=> $subGroupData->group_id);
					$activities            = $this->getActivityTable()->fetchAll($where, $order, 10);
					$activityData          = array();
					$incVar=0;			
					if($activities->count()){ 
						foreach($activities as $row){				
							$row = get_object_vars($row);					
							$blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
							$blockCipher->setKey('sfds^&*%(^$%^$%^KMNVHDrt#$$$%#@@');
							$row['encrypted_activity_id'] = $blockCipher->encrypt($row['group_activity_id']);				 
							$activityData[$incVar] = $row;					
							$order = array();
							$order = array("group_activity_rsvp_id DESC");					
							$whereRsvp =array();
							$whereRsvp =array('y2m_group_activity_rsvp.group_activity_rsvp_activity_id'=> $row['group_activity_id']);		
							$rsvpActivityData = $this->getActivityRsvpTable()->fetchAll($whereRsvp, $order, 6);
							if($rsvpActivityData->count()){
								$jncVar = 0;
								foreach($rsvpActivityData as $rsvpRow){
									$activityData[$incVar]['rsvp'][$jncVar] =	get_object_vars($rsvpRow);
									$jncVar++;
								} 
							}	
							$incVar++;			
						}				
					} 		
					$return_arr = array();	 
					foreach($activityData as $row) { 
						$year = date('Y',strtotime($row['group_activity_start_timestamp']));
						$month = date('m',strtotime($row['group_activity_start_timestamp'])); 
						$date = date('d',strtotime($row['group_activity_start_timestamp']));
						$url =  $this->url()->fromRoute('activity/activity-view', array('action' => 'view', 'id'=> $row['group_activity_id']), $options=array());
						$row_array['id'] = $row['group_activity_id'];
						$row_array['title'] = $row['group_activity_title'];
						$row_array['start'] = "$year-$month-$date";
						$row_array['url'] = $url; 
						array_push($return_arr,$row_array);
					}
					$json_events = json_encode($return_arr);
					$viewModel = new ViewModel(array('form' => $form,'json'=> $json_events, 'selectAllUserForPlanet' => $selectAllUserForPlanet, 'userRegisteredGroup' => $userRegisteredGroup ,'activityData_past'=>$activityData_past,'userData' => $identity, 'subGroupData' => $subGroupData,'groupData' => $GroupData,'activities' =>$activities,'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages(),'pastactivity_page'=>1));
					$viewModel->setTerminal($request->isXmlHttpRequest());
					return $viewModel;	   
				}
				else{
					$error[] = 'Planet is not existing';
				}				
			}
			else{
				$error[] = 'Unauthorized access';
			}
		}
		else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}		   
    }	
	public function joinAction(){
		$planet = $this->params('planet_id');
		if($planet!=''){
			$auth = new AuthenticationService();
			if ($auth->hasIdentity()) {
				$identity = $auth->getIdentity();
				$planetdetails       = $this->getGroupTable()->getGroupIdFromSEO($planet);
				$groupseoTitle		 = $this->getGroupTable()->getSeotitle($planetdetails->group_parent_group_id);
				if($planetdetails->group_id!=''){
					$request   = $this->getRequest();
					$userList = $this->getUserGroupTable()->fetchAllUserListForGroup($planetdetails->group_id,$identity->user_id);
					
					$excludeUser =array();
					$excludeUser['user_id'] = $identity->user_id;
					$selectAllUserForPlanet = UserGroup::selectFormatAllUserListGroupEncrypted($userList, $excludeUser); 
					$form = new ActivityAddForm($selectAllUserForPlanet);
					$form->get('submit')->setAttribute('value', 'Add');	
					if ($request->isPost()) {
						$activity = new Activity();
						$form->setInputFilter(new ActivityAddFormFilter());	
						$form->setData($request->getPost());
						if ($form->isValid()) {
							$post = $request->getPost();						 									
							$InviteAct =array();
							$InviteAct =$post->get('InviteAct');
					
							$activityData = array();
							$activityData['group_activity_title'] = $post->get('group_activity_title');
							$activityData['group_activity_content'] = $post->get('group_activity_content');
							$activityData['group_activity_type'] = $post->get('group_activity_type');								 
							$activityData['group_activity_start_timestamp'] = $post->get('group_activity_start_timestamp');
							$activityData['group_activity_owner_user_id'] = $identity->user_id;
							$activityData['group_activity_location'] = $post->get('group_activity_location');
							$activityData['group_activity_group_id'] = $planetdetails->group_id;							
							$activityData['group_activity_added_ip_address'] =  User::getUserIp();
							$activityData['group_activity_added_timestamp'] =  date('Y-m-d');
							$activityData['group_activity_status'] = "1";							 
							$activityObject = new Activity();
							$activityObject->exchangeArray($activityData);
							$insertedActivityId ="";	#this will hold the latest inserted id value
							$insertedActivityId = $this->getActivityTable()->saveActivity($activityObject);
							if(isset($insertedActivityId) && !empty($insertedActivityId)){
								$success[] = 'Activity saved successfully';
								if($post->get('group_activity_type') == "public") { 
									$userList = $this->getUserGroupTable()->fetchAllUserListForGroup($planetdetails->group_id,$identity->user_id);
									foreach ($userList as $row){
										if($row->user_id!=$identity->user_id){
											$isMember = $this->userGroupTable->getUserGroup($row->user_id, $planetdetails->group_id);
											if(isset($isMember->user_group_id) && !empty($isMember->user_group_id)){
												$this->activityInvitation($identity->user_id,$row->user_id,$insertedActivityId,$planetdetails->group_id);												
											}
											else{												
												$error[] = $row->user_given_name.' not a member in this group';
											}
										}
									}
								}
								else{
									if(isset($InviteAct) && count($InviteAct)) {
										foreach($InviteAct as $row) {
											$filter = new \Zend\Filter\StripTags();
											$row =$filter->filter($row);									
											$filter = new \Zend\Filter\StringTrim();
											$row =$filter->filter($row);
											$filter = new \Zend\Filter\HtmlEntities();
											$row =$filter->filter($row);
											$blockCipher = BlockCipher::factory('mcrypt', array('algorithm' => 'aes')); 
											$blockCipher->setKey('JHHU98789*&^&^%^$^^&g53$@8');  
											$decryptTagId = $blockCipher->decrypt($row); 							
											$user_id = (int) $decryptTagId;
											$isMember = $this->userGroupTable->getUserGroup($user_id, $planetdetails->group_id);	
											if(isset($isMember->user_group_id) && !empty($isMember->user_group_id)){
												$this->activityInvitation($identity->user_id,$user_id,$insertedActivityId,$planetdetails->group_id);												 
											}
											else{												
												$error[] = $row->user_given_name.' not a member in this group';
											}
										}
									}
								}
								$this->flashMessenger()->addMessage('Activity Added Successfully');		//echo $planetdetails->group_seo_title;die();
								$this->redirect()->toRoute('groups/planethome', array('action' => 'planethome','group_id'=>$groupseoTitle->group_seo_title,'planet_id'=>$planetdetails->group_seo_title));
							}
							else {
								 $error[] = 'Oops an error is occured while saving activity';	
							}
						}
						else{
							$this->flashMessenger()->addMessage('Field validation failed');												
							return $this->redirect()->toRoute('groups/planethome', array('action' => 'planethome','group_id'=>$groupseoTitle->group_seo_title,'planet_id'=>$planetdetails->group_seo_title));
						}
					}
					else{
						$this->flashMessenger()->addMessage('Unautherized access');												
						return $this->redirect()->toRoute('groups/planethome', array('action' => 'planethome','group_id'=>$groupseoTitle->group_seo_title,'planet_id'=>$planetdetails->group_seo_title));
					}
				}
				else{
					$this->flashMessenger()->addMessage('Unautherized access');
					return $this->redirect()->toRoute('groups/index', array('action' => 'index'));		 
				}
			 }
			 else{
				return $this->redirect()->toRoute('user/login', array('action' => 'login'));
			 }
		}
		else{			
			$this->flashMessenger()->addMessage('Unautherized access');
			return $this->redirect()->toRoute('groups/index', array('action' => 'index'));
		}
	}
	public function activityInvitation($sender_id,$user_id,$ActivityId,$planet_id){
		$userData = $this->getUserTable()->getUser($sender_id);	
		$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
		$config = $this->getServiceLocator()->get('Config');
		$base_url = $config['pathInfo']['base_url'];
		$activityInviteData = array();
		$activityInviteData['group_activity_invite_sender_user_id'] = $user_id;
		$activityInviteData['group_activity_invite_receiver_user_id'] = $user_id;
		$activityInviteData['group_activity_invite_status'] = 0;
		$activityInviteData['group_activity_invite_added_date'] =  date('Y-m-d');					
		$activityInviteData['group_activity_invite_added_ip_address'] =  User::getUserIp();
		$activityInviteData['group_activity_invite_activity_id'] = $ActivityId;
		$activityInviteObject = new ActivityInvite();
		$activityInviteObject->exchangeArray($activityInviteData); 
		$insertedActivityInviteId ="";	#this will hold the latest inserted id value echo "here";die();
		$insertedActivityInviteId = $this->getActivityInviteTable()->saveActivityInvite($activityInviteObject); 
		if(isset($ActivityId) && !empty($ActivityId)){ 
			$UserGroupNotificationData = array();						
			$UserGroupNotificationData['user_notification_user_id'] = $userData->user_id;
			$msg	= $userData->user_first_name." ".$userData->user_last_name." has invited you for activity of Planet <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$ActivityId."'>".$subGroupData->group_title."</a>";
			$UserGroupNotificationData['user_notification_content']  = $msg;
			$UserGroupNotificationData['user_notification_added_timestamp'] = date('Y-m-d H:i:s');			
			$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
			$UserGroupNotificationData['user_notification_status'] = 0;		
			#lets Save the User Notification
			$UserGroupNotificationSaveObject = new UserNotification();
			$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);	
			$insertedUserGroupNotificationId ="";	#this will hold the latest inserted id value
			$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
			$reciver_data = $this->getUserTable()->getUser($user_id);	
			$this->sendNotificationMail($msg,'Activity Invitation',$reciver_data->user_email);
			
		}
		return true;		
	}
	public function activityNotification($sender_id,$user_id,$ActivityId,$planet_id){
		$userData = $this->getUserTable()->getUser($sender_id);	
		$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
		$config = $this->getServiceLocator()->get('Config');
		$base_url = $config['pathInfo']['base_url'];		
		if(isset($ActivityId) && !empty($ActivityId)){ 
			$UserGroupNotificationData = array();						
			$UserGroupNotificationData['user_notification_user_id'] = $user_id;
			$msg	= $userData->user_first_name." ".$userData->user_last_name." has created a new activity under Planet <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$ActivityId."'>".$subGroupData->group_title."</a>";
			$UserGroupNotificationData['user_notification_content']  = $msg;
			$UserGroupNotificationData['user_notification_added_timestamp'] = date('Y-m-d H:i:s');			
			$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
			$UserGroupNotificationData['user_notification_status'] = 0;		
			#lets Save the User Notification
			$UserGroupNotificationSaveObject = new UserNotification();
			$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);	
			$insertedUserGroupNotificationId ="";	#this will hold the latest inserted id value
			$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
			$reciver_data = $this->getUserTable()->getUser($user_id);	
			$this->sendNotificationMail($msg,'Activity Notification',$reciver_data->user_email);			
		}
		return true;		
	}
	public function loadpasteventsAction(){
		$error = array();
		$success = array();
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$planet_id= $this->params('planet_id');
			if(isset($identity->user_id) && !empty($identity->user_id) && isset($planet_id) && !empty($planet_id)){
				$subGroupData = $this->getGroupTable()->getSubGroup($planet_id);
				$GroupData = $this->getGroupTable()->getGroup($subGroupData->group_parent_group_id);	
				if(!empty($subGroupData)){
					$page = 0;
					$request   = $this->getRequest();				 		
					$post = $request->getPost();		
					if ($request->isPost()){
						$page =$post->get('page');
						if(!$page)
						$page = 0;
					}
					$offset = $page*10;
					$order = array("group_activity_id DESC");				
					$where =array('y2m_group_activity.group_activity_group_id'=> $subGroupData->group_id);
					$activities_past       =$this->getActivityTable()->getAllActivityWithLikes($subGroupData->group_id,1,$identity->user_id,$offset);
					$activityData_past     = array();
					$incVar=0;				
					if($activities_past->count()){
						foreach($activities_past as $row){				
							$row = get_object_vars($row);					
							$blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
							$blockCipher->setKey('sfds^&*%(^$%^$%^KMNVHDrt#$$$%#@@');
							$row['encrypted_activity_id'] = $blockCipher->encrypt($row['group_activity_id']);				 
							$activityData_past[$incVar] = $row;
							$order = array();
							$order = array("group_activity_rsvp_id DESC");
							$whereRsvp =array();
							$whereRsvp =array('y2m_group_activity_rsvp.group_activity_rsvp_activity_id'=> $row['group_activity_id']);
							$rsvpactivityData_past = $this->getActivityRsvpTable()->fetchAll($whereRsvp, $order, 6);
							if($rsvpactivityData_past->count()){
								$jncVar = 0;
								foreach($rsvpactivityData_past as $rsvpRow){
									$activityData_past[$incVar]['rsvp'][$jncVar] =	get_object_vars($rsvpRow);
									$jncVar++;
								} 
							}	
							$incVar++;			
						}
						$viewModel = new ViewModel(array('activityData_past'=>$activityData_past,'userData' => $identity, 'subGroupData' => $subGroupData,'groupData' => $GroupData,'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages(),'pastactivity_page'=>$page+1));
						$viewModel->setTerminal($request->isXmlHttpRequest());
						return $viewModel;	 
					}
				}else{
					$error[] = 'Planet is not existing';
				}
			}else{
				$error[] = 'Unauthorized access';
			}
		}
		else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
	}
	public function sendNotificationMail($msg,$subject,$emailId){
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');
		
		$body = $this->renderer->render('activity/email/emailinvitation.phtml', array('msg'=>$msg));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";

		$textPart = new MimePart($body);
		$textPart->type = "text/plain";

		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));

		$message = new Mail\Message();
		$message->setFrom('admin@jeera.com');
		$message->addTo($emailId);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject($subject);
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');

		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
	}
	public function popularAction(){
		$auth = new AuthenticationService();
		$page_limit = 10;
		$offset = 0;
		$user_id = 0;
		$viewModel = new ViewModel();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$this->layout()->page = 'Activities';
			$user_id = $identity->user_id;
			$planetSugessions = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'planetSuggestions',								 
								));
			$viewModel->addChild($planetSugessions, 'planetSugessions');
		}
		$allActivity  = $this->getActivityTable()->getAllUpcomingActivityWithUserCount($user_id,$offset,$page_limit);
		$viewModel->setVariable('activities',$allActivity);
		return $viewModel;
	}	
	#access Activity Table Module
	public function getActivityTable(){
        if (!$this->activityTable) {
            $sm = $this->getServiceLocator();
            $this->activityTable = $sm->get('Activity\Model\ActivityTable');
        }
        return $this->activityTable;
    }	
	#access Activity Invite Table Module
	public function getActivityInviteTable(){
        if (!$this->activityInviteTable) {
            $sm = $this->getServiceLocator();
            $this->activityInviteTable = $sm->get('Activity\Model\ActivityInviteTable');
        }
        return $this->activityInviteTable;
    }	
	#access Activity Rsvp Table Module
	public function getActivityRsvpTable(){
        if (!$this->activityRsvpTable) {
            $sm = $this->getServiceLocator();
            $this->activityRsvpTable = $sm->get('Activity\Model\ActivityRsvpTable');
        }
        return $this->activityRsvpTable;
    }	
	#access Galaxy/Planet Table Module
	public function getGroupTable(){
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    } 	
	#access User Galaxy/Planet Module
	public function getUserGroupTable(){
        if (!$this->userGroupTable) {
            $sm = $this->getServiceLocator();
			$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
        }
        return $this->userGroupTable;
    }	
	#access User Module 
    public function getUserTable(){
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }	
	#access User Profile Module
    public function getUserProfileTable(){
        if (!$this->userProfileTable) {
            $sm = $this->getServiceLocator();
            $this->userProfileTable = $sm->get('User\Model\UserProfileTable');
        }
        return $this->userProfileTable;
    }	
	#access User Notification
    public function getUserNotificationTable(){
        if (!$this->userNotificationTable) {
            $sm = $this->getServiceLocator();
            $this->userNotificationTable = $sm->get('Notification\Model\UserNotificationTable');
        }
        return $this->userNotificationTable;
    }
	public function ajaxLoadActivityAction(){
		$auth = new AuthenticationService();
		$page_limit = 10;
		$offset = 0;
		$user_id = 0;
		$request   = $this->getRequest();
		$post = $request->getPost();	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;			 
			$identity->profile_pic = '';
			$user_id = $identity->user_id;
			$profilepic = $this->getUserTable()->getUserProfilePic($user_id);
			foreach($profilepic as $pic){$identity->profile_pic = $pic->biopic;}
		}
		if ($request->isPost()){
			$page =$post->get('page');
			if(!$page)
			$page = 0;	
			$offset	= $page*10;
		}
		$allActivity  = $this->getActivityTable()->getAllUpcomingActivityWithUserCount($user_id,$offset,$page_limit);
		$viewModel = new ViewModel(array('activities' => $allActivity));		 
		$viewModel->setTerminal($request->isXmlHttpRequest());		 
		return $viewModel;
	}
	public function calendarAction(){
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) {
			
		}
	}
	public function ajaxCreateActivityAction(){
		$auth = new AuthenticationService();
		$error ='';
		$error_count = 0;
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
			if($group_seo!=''&&$planet_seo!=''){	 
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {  
						$is_persmission = 0;
						$data['group_activity_status'] = "1";	
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){						 
								$is_persmission = 1;						 
						}else{
							$settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
							if($settings->group_activity_settings == 'Any'){
								$is_persmission = 1;	
							}
							else if($settings->group_activity_settings == 'AdminApproval'){
								$is_persmission = 1;
								$data['group_activity_status'] = "0";
							}
							else if($settings->group_activity_settings == 'OnlyAdmin'){
								$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
								if(!empty($user_role)){
									$is_persmission = 1;
								}else{
									$is_persmission = 0;
								}
							}
							else{
								$is_persmission = 0;
							} 
						}
						if($is_persmission){ 	
								$post = $request->getPost();
								if($post->get('activity_title')==''||$post->get('activity_title')=='undefined'){
									$error = 'Please enter activity title';
									$error_count++;
								}
								if($post->get('address')==''||$post->get('address')=='undefined'){
									$error = 'Please enter location';
									$error_count++;
								}
								if($post->get('group_activity_start_timestamp')==''||$post->get('group_activity_start_timestamp')=='undefined'){
									$error = 'Please enter start time';
									$error_count++;
								}
								if($error_count==0){
									$data['group_activity_title'] = $post->get('activity_title');
									$data['group_activity_content'] = $post->get('activity_title');
									$data['group_activity_type'] = $post->get('members_type');
									$data['group_activity_location'] = $post->get('address');
									$data['group_activity_start_timestamp'] =  $post->get('group_activity_start_timestamp');
									$data['group_activity_owner_user_id'] = $identity->user_id;
									$data['group_activity_group_id'] = $planet_id;
									$data['tags']  = $post->get('tags');
									if($post->get('members_type') == 'invited'){
										$data['invited_members'] =  $post->get('users');
									}
									$this->createActivity($data);
								}else{
									$error = 'You don\'t have the permission to do this';
									$error_count++;
								}
							}else{
								$error = 'You don\'t have the permission to do this';
								$error_count++;
							}
						 
						}else{
							$error = 'Invalid access';
							$error_count++;
						}
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
					$error = 'Invalid access';
					$error_count++;
				}
			 } else{
				$error = 'Your session expired';
				$error_count++;
			 }
		 if($error_count==0){
			$return_array['msg'] = "Tags are added into the group tag cloud";
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function createActivity($data){
		$user = new User();
		$activityData = array();
		$activityData['group_activity_title'] = $data['group_activity_title'];
		$activityData['group_activity_content'] = $data['group_activity_content'];
		$activityData['group_activity_type'] = $data['group_activity_type'];	
		$activityData['group_activity_start_timestamp'] = $data['group_activity_start_timestamp'];
		$activityData['group_activity_owner_user_id'] = $data['group_activity_owner_user_id'];
		$activityData['group_activity_location'] = $data['group_activity_location'];
		$activityData['group_activity_group_id'] = $data['group_activity_group_id'];						
		$activityData['group_activity_added_ip_address'] =  $user->getUserIp();
		$activityData['group_activity_added_timestamp'] =  date('Y-m-d');
		$activityData['group_activity_status'] = $data['group_activity_status'];						 
		$activityObject = new Activity();
		$activityObject->exchangeArray($activityData);
		$insertedActivityId ="";	#this will hold the latest inserted id value
		$insertedActivityId = $this->getActivityTable()->saveActivity($activityObject);
		if(isset($insertedActivityId) && !empty($insertedActivityId)){ 
			$success[] = 'Activity saved successfully';
			$activityRsvpData = array();
			$activityRsvpData['group_activity_rsvp_user_id'] = $data['group_activity_owner_user_id'];
			$activityRsvpData['group_activity_rsvp_activity_id'] =  $insertedActivityId; 
			$activityRsvpData['group_activity_rsvp_added_ip_address'] = $user->getUserIp();
			$activityRsvpData['group_activity_rsvp_group_id'] = $data['group_activity_group_id']; 
			$activityRsvpObject = new ActivityRsvp();
			$activityRsvpObject->exchangeArray($activityRsvpData);
			$insertedActivityRsvpId =""; 
			$insertedActivityRsvpId = $this->getActivityRsvpTable()->saveActivityRsvp($activityRsvpObject); 
			$tags = $data['tags'];
			$arr_tags = explode(",",$tags);
			foreach($arr_tags as $values){
				$tag_data['activity_id'] = $insertedActivityId;
				$tag_data['group_id'] = $data['group_activity_group_id'];
				$tag_data['group_tag_id'] = $values;
				$this->getActivityTagTable()->saveActivityTags($tag_data);
			}
			if($activityData['group_activity_type'] == "public") { 
				$userList = $this->getUserGroupTable()->fetchAllUserListForGroupWithSettings($data['group_activity_group_id'],$data['group_activity_owner_user_id']);
				foreach ($userList as $row){
					if($row->user_id!=$data['group_activity_owner_user_id']){
						if($row->activity!='no'){
							$this->activityNotification($data['group_activity_owner_user_id'],$row->user_id,$insertedActivityId,$data['group_activity_group_id']);
						}							
					}
				}
			}else{
				if(isset($data['invited_members']) && count($data['invited_members'])) {
					$arr_invited_users = explode(',',$data['invited_members']);
					foreach($arr_invited_users as $row) {						
						$isMember = $this->getUserGroupTable()->getUserGroup($row, $data['group_activity_group_id']);	
						if(isset($isMember->user_group_id) && !empty($isMember->user_group_id)){
							$this->activityInvitation($data['group_activity_owner_user_id'],$row,$insertedActivityId, $data['group_activity_group_id']);												 
						}							 
					}
				}
			}
		}else {
		 $error[] = 'Oops an error is occured while saving activity';	
		}
	}
	public function getGroupSettingsTable(){
        if (!$this->groupSettings) {
            $sm = $this->getServiceLocator();
            $this->groupSettings = $sm->get('Groups\Model\GroupSettingsTable');
        }
        return $this->groupSettings;
    }
	public function getActivityTagTable(){
        if (!$this->groupActivityTag) {
            $sm = $this->getServiceLocator();
            $this->groupActivityTag = $sm->get('Tag\Model\ActivityTagTable');
        }
        return $this->groupActivityTag;
    }
	public function loadmoreActivityAction(){
		$error = array();
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$arr_upcaoming_activities = array();
		$is_admin = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$planet = $post['planet'];
				$group = $post['group'];
				$page = $post['page'];
				$type = $post['type'];
				$viewModel->setVariable('type', $type);
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group);
				$viewModel->setVariable('galexy_seo_title', $group);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet);
				$viewModel->setVariable('planetdetails', $planetdetails);
				$viewModel->setVariable('user_id', $identity->user_id);
				$planet_id = $planetdetails->group_id;
				if($this->getGroupTable()->is_member($planet_id,$identity->user_id)){
					$viewModel->setVariable('planet_member', 1);
				}else{	
					$viewModel->setVariable('planet_member', 0);
				}
				$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
				if($admin_status->is_admin){
					$is_admin = 1;
				}
				$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
				if(!empty($user_role)){
					$is_admin = 1;
				}
				$viewModel->setVariable('is_admin', $is_admin);
				if($group_id && $planet_id){
					$page_limit = 0;
					if($page>0){
					$page_limit = 10*($page);
					}
					if($type=='past'){
						$upcoming_activities = $this->getActivityTable()->getAllPastActivityWithCountofUsersLikeComment($identity->user_id,$planet_id,$page_limit,10);
					}else{
						$upcoming_activities = $this->getActivityTable()->getAllUpcomingActivityWithCountofUsersLikeComment($identity->user_id,$planet_id,$page_limit,10);
					}
					$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
					foreach($upcoming_activities as $activities){	
						$arr_upcaoming_activities[] = array(
													"group_activity_title" => $activities->group_activity_title,
													"group_activity_location" => $activities->group_activity_location,
													"group_activity_content" => $activities->group_activity_content,
													"group_activity_start_timestamp" => $activities->group_activity_start_timestamp,
													"group_activity_id" => $activities->group_activity_id,
													"is_member" => $activities->is_member,
													"user_given_name" => $activities->user_given_name,
													"user_id" => $activities->user_id,
													"user_profile_name" => $activities->user_profile_name,
													"user_register_type" => $activities->user_register_type,
													"user_fbid" => $activities->user_fbid,
													"profile_photo" => $activities->profile_photo,
													"member_count" => $activities->member_count,
													"group_activity_owner_user_id" => $activities->group_activity_owner_user_id,
													"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activities->group_activity_id,$identity->user_id),
													"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activities->group_activity_id)->comment_count,
													'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activities->group_activity_id,$identity->user_id,2,0),
													'members' =>$this->getActivityRsvpTable()->getJoinMembers($activities->group_activity_id,3,0)
													);
					}
					
				}else{
					$error[] = "Invalid access..";
				}
			}else{
				$error[] = "Invalid access..";
			}
		}
		else{
			$error[] = "Your session expired. Please login and try again";
		}
		$viewModel->setVariable('upcoming_activities', $arr_upcaoming_activities);
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function memberslistAction(){
				$error = array();
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$members = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$activity_id = $post['activity_id'];			 
				 
				if($activity_id){
					 
					$members = $this->getActivityRsvpTable()->getJoinMembers($activity_id,'All',0);
													 
					
				}else{
					$error[] = "Invalid access..";
				}
			}else{
				$error[] = "Invalid access..";
			}
		}
		else{
			$error[] = "Your session expired. Please login and try again";
		}
		$viewModel->setVariable('members', $members);
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function getCommentTable(){
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
            $this->commentTable = $sm->get('Comment\Model\CommentTable');
        }
        return $this->commentTable;
    }
	public function getLikeTable(){
        if (!$this->likeTable) {
            $sm = $this->getServiceLocator();
            $this->likeTable = $sm->get('Like\Model\LikeTable');
        }
        return $this->likeTable;
    }
	public function OneDayOnlyAction(){ 
		$error = array();
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$arr_upcaoming_activities = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();  
				$planet = $post['planet'];
				$group = $post['group'];		 
				$selected_date = date('Y-m-d', strtotime(str_replace('/', '-', $post['selected_date'])));
				$return_selected = date('mdY', strtotime($post['selected_date']));
				//$str_$selected_date = strtotime($selected_date);
				//$selected_date = date("Y-m-d",$str_$selected_date);
				$viewModel->setVariable('selected_date',$return_selected);
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group);
				$viewModel->setVariable('galexy_seo_title', $group);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet);
				$viewModel->setVariable('planetdetails', $planetdetails);
				$planet_id = $planetdetails->group_id;
				if($this->getGroupTable()->is_member($planet_id,$identity->user_id)){
					$viewModel->setVariable('planet_member', 1);
				}else{	
					$viewModel->setVariable('planet_member', 0);
				}
				if($group_id && $planet_id){
					$today_activities = $this->getActivityTable()->getAllTodayActivityWithCountofUsersLikeComment($identity->user_id,$planet_id,$selected_date); 
					$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
					foreach($today_activities as $activities){	
						$arr_upcaoming_activities[] = array(
													"group_activity_title" => $activities->group_activity_title,
													"group_activity_location" => $activities->group_activity_location,
													"group_activity_content" => $activities->group_activity_content,
													"group_activity_start_timestamp" => $activities->group_activity_start_timestamp,
													"group_activity_id" => $activities->group_activity_id,
													"is_member" => $activities->is_member,
													"user_given_name" => $activities->user_given_name,
													"user_id" => $activities->user_id,
													"user_profile_name" => $activities->user_profile_name,
													"user_register_type" => $activities->user_register_type,
													"user_fbid" => $activities->user_fbid,
													"profile_photo" => $activities->profile_photo,
													"member_count" => $activities->member_count,
													"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activities->group_activity_id,$identity->user_id),
													"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activities->group_activity_id)->comment_count,
													 
													'members' =>$this->getActivityRsvpTable()->getJoinMembers($activities->group_activity_id,3,0)
													);
					}
					
				}else{
					$error[] = "Invalid access..";
				}
			}else{
				$error[] = "Invalid access..";
			}
		}
		else{
			$error[] = "Your session expired. Please login and try again";
		}
		$viewModel->setVariable('upcoming_activities', $arr_upcaoming_activities);
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function ajaxLoadMoreMembersAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			$viewModel->setVariable('user_id', $identity->user_id);	
			$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
			$post = $request->getPost();		
			if ($request->isPost()){
				$page =$post->get('page');
				if(!$page)
				$page = 0;
				$activity_id  = $post->get('activity_id');
				if($activity_id){
					$offset = $page*25;
					$activityData =$this->getActivityTable()->getActivity($activity_id);
					if(!empty($activityData)&& $activityData->group_activity_id!=''){		
						$admin_status = $this->getGroupTable()->getAdminStatus($activityData->group_activity_group_id,$identity->user_id);
						$delete_permission = 0;
						if($admin_status->is_admin){
						$delete_permission = 1;
						}else if($activityData->group_activity_owner_user_id == $identity->user_id){
							$delete_permission = 1;
						}else{
							$delete_permission = 0;
						}
						$members = $this->getActivityRsvpTable()->getJoinMembersWithFriendshipStatus($activity_id,$identity->user_id,25,$offset);
						$viewModel->setVariable('delete_permission', $delete_permission);
						$viewModel->setVariable('members', $members);	
						$viewModel->setVariable('user_id', $identity->user_id);
					}						
					else{
						$error[] = "Invalid access";
					}
				}else{
					$error[] = "Invalid access";
				}
			}else{
				$error[] = "Invalid access";
			}					 
		}else{
			$error[] = "Your session has to be expired";
		}
		$viewModel->setVariable('error', $error);	
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
		die();
	}
	public function rsvpRemoveAction(){
		$error =array();
		$success =array();
		$identity ="";		 		
		$auth = new AuthenticationService();	
		$identity = null;   
		$return_array = array();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			if(isset($identity->user_id) && !empty($identity->user_id)){ 
				$request   = $this->getRequest();				 		
				$post = $request->getPost();
				if ($request->isPost()){
					$activity_id =$post->get('activity_id');
					$user = $post->get('user');
					$activityData =$this->getActivityTable()->getActivity($activity_id);
					if(!empty($activityData)&& $activityData->group_activity_id!=''){		
						$subGroupData = $this->getGroupTable()->getSubGroup($activityData->group_activity_group_id);
						if(isset($subGroupData->group_id) && !empty($subGroupData->group_id) && isset($subGroupData->group_parent_group_id) && !empty($subGroupData->group_parent_group_id)){
							$groupData = $this->getGroupTable()->getGroup($subGroupData->group_parent_group_id);
							$admin_status = $this->getGroupTable()->getAdminStatus($activityData->group_activity_group_id,$identity->user_id);							
							if($activityData->group_activity_owner_user_id ==$identity->user_id || $admin_status->is_admin){
								$rsvpUser =$this->getActivityRsvpTable()->getActivityRsvpOfUser($user, $activityData->group_activity_id);
								if(isset($rsvpUser->group_activity_rsvp_id) && !empty($rsvpUser->group_activity_rsvp_id)){
									if($this->getActivityRsvpTable()->removeActivityRsvp($activity_id,$user)){
										$return_array = array("msg"=>"You are removed from rsvp list",
										'error'=>0,									 
										); 
										if($identity->user_id!=$activityData->group_activity_owner_user_id){
											$UserGroupNotificationData = array();						
											$UserGroupNotificationData['user_notification_user_id'] = $activityData->group_activity_owner_user_id;							
											$UserGroupNotificationData['user_notification_content'] = $identity->user_given_name." quit from the activity of Planet <a href='".$this->url()->fromRoute('groups/planethome', array('action' => 'planethome', 'group_id'=>$groupData->group_seo_title, 'planet_id'=>$subGroupData->group_seo_title), $options=array())."'>".$subGroupData->group_title."</a>";								 
											$userObject = new user(array());
											$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
											$UserGroupNotificationData['user_notification_status'] = 0;									
											$UserGroupNotificationSaveObject = new UserNotification();
											$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
											$insertedUserGroupNotificationId ="";
											$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);	
										}
										$UserGroupNotificationData = array();						
											$UserGroupNotificationData['user_notification_user_id'] = $user;							
											$UserGroupNotificationData['user_notification_content'] = $identity->user_given_name." remove you from  <a href='".$this->url()->fromRoute('groups/planethome', array('action' => 'planethome', 'group_id'=>$groupData->group_seo_title, 'planet_id'=>$subGroupData->group_seo_title), $options=array())."'>".$subGroupData->group_title."</a>";								 
											$userObject = new user(array());
											$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
											$UserGroupNotificationData['user_notification_status'] = 0;									
											$UserGroupNotificationSaveObject = new UserNotification();
											$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
											$insertedUserGroupNotificationId ="";
											$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
									}else{
										$return_array = array("msg"=>"Error in RSVP",
										'error'=>1,									 
										);	
									}
								}else{
									$return_array = array("msg"=>"You don't have the permisssions for removing users",
										'error'=>1,									 
										);
								}
							
							}else{
								$return_array = array("msg"=>"You are not joined in this event",
									'error'=>1,									 
									);	 
							}
						}else{
							$return_array = array("msg"=>"Invalid Planet",
									'error'=>1,									 
									); 
						}
					}else{
						$return_array = array("msg"=>"Activity Does not Exist",
									'error'=>1,									 
									);  
					}					
				}else{
					$return_array = array("msg"=>"Invalid access",
									'error'=>1,									 
									);  
				}
			}else{
				$return_array = array("msg"=>"User Not Found",
									'error'=>1,									 
									);   
			}
		}else{
			$return_array = array("msg"=>"Invalid access",
									'error'=>1,									 
									);   
		}
		 echo json_encode($return_array);die();
	}
	public function getUserGroupJoiningRequestTable(){
        if (!$this->groupjoiningrequestTable) {
            $sm = $this->getServiceLocator();
            $this->groupjoiningrequestTable = $sm->get('Groups\Model\UserGroupJoiningRequestTable');
        }
        return $this->groupjoiningrequestTable;
    }
	public function getGroupTagTable(){
        if (!$this->groupTagTable) {
            $sm = $this->getServiceLocator();
            $this->groupTagTable = $sm->get('Tag\Model\GroupTagTable');
        }
        return $this->groupTagTable;
    }
	public function ajaxEditActivityAction(){
		$auth = new AuthenticationService();
		 $error ='';
		 $error_count = 0;
		 if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
			if($group_seo!=''&&$planet_seo!=''){	 
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						
						$is_persmission = 0;
						$data['group_activity_status'] = "1";	
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){						 
								$is_persmission = 1;						 
						}else{
							$settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
							if($settings->group_activity_settings == 'Any'){
								$is_persmission = 1;	
							}
							else if($settings->group_activity_settings == 'AdminApproval'){
								$is_persmission = 1;
								$data['group_activity_status'] = "0";
							}
							else if($settings->group_activity_settings == 'OnlyAdmin'){
								$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
								if(!empty($user_role)){
									$is_persmission = 1;
								}else{
									$is_persmission = 0;
								}
							}
							else{
								$is_persmission = 0;
							} 
						}
						if($is_persmission){ 	
								$post = $request->getPost();
								if($post->get('activity_title')==''||$post->get('activity_title')=='undefined'){
									$error = 'Please enter activity title';
									$error_count++;
								}
								if($post->get('address')==''||$post->get('address')=='undefined'){
									$error = 'Please enter location';
									$error_count++;
								}
								if($post->get('group_activity_start_timestamp')==''||$post->get('group_activity_start_timestamp')=='undefined'){
									$error = 'Please enter start time';
									$error_count++;
								}
								if($post->get('activity_id')==''||$post->get('activity_id')=='undefined'){
									$error = 'Activity is not exist in this system';
									$error_count++;
								}
								$activity_id = $post->get('activity_id');
								if($error_count==0){
									$oldactivityData =$this->getActivityTable()->getActivity($activity_id);	
									if(strtotime($oldactivityData->group_activity_start_timestamp)>strtotime(date("Y-m-d h:s"))){
										if(!empty($oldactivityData)&& $oldactivityData->group_activity_id!=''){
											$data['group_activity_title'] = $post->get('activity_title');
											$data['group_activity_content'] = $post->get('activity_title');
											$data['group_activity_type'] = $post->get('members_type');
											$data['group_activity_location'] = $post->get('address');
											$data['group_activity_start_timestamp'] =  $post->get('group_activity_start_timestamp');
											$data['group_activity_owner_user_id'] = $oldactivityData->group_activity_owner_user_id;
											$data['group_activity_group_id'] = $planet_id;
											$data['group_activity_id'] =$post->get('activity_id');
											$data['tags']  = $post->get('tags');
											if($post->get('members_type') == 'invited'){
												$data['invited_members'] =  $post->get('users');
											}
											$activityData = array();
											$activityData['group_activity_title'] = $data['group_activity_title'];
											$activityData['group_activity_content'] = $data['group_activity_content'];
											$activityData['group_activity_type'] = $data['group_activity_type'];	
											$activityData['group_activity_start_timestamp'] = $data['group_activity_start_timestamp'];
											$activityData['group_activity_owner_user_id'] = $data['group_activity_owner_user_id'];
											$activityData['group_activity_location'] = $data['group_activity_location'];
											$activityData['group_activity_group_id'] = $data['group_activity_group_id'];						
											$activityData['group_activity_added_ip_address'] =  User::getUserIp();
											$activityData['group_activity_added_timestamp'] =  date('Y-m-d');
											$activityData['group_activity_status'] = $data['group_activity_status'];	
											$activityData['group_activity_status'] = $data['group_activity_status'];	
											$activityData['group_activity_id'] = $data['group_activity_id'];
											$activityObject = new Activity();
											$activityObject->exchangeArray($activityData);
											$insertedActivityId ="";	#this will hold the latest inserted id value
											$insertedActivityId = $this->getActivityTable()->saveActivity($activityObject);
											if(isset($insertedActivityId) && !empty($insertedActivityId)){
												$success[] = 'Activity saved successfully';
												$ActivityTags =	$this->getActivityTagTable()->getActivityTags($activity_id);
												$tags = $data['tags'];
												$arr_tags = explode(",",$tags);
												$arr_tag_ids = array();
												foreach($ActivityTags as $tags){
													if(!in_array($tags->tag_id, $arr_tags)){
														$this->getActivityTagTable()->RemoveActivityTags($tags->tag_id,$insertedActivityId);
													}
													$arr_tag_ids[] = $tags->tag_id;
												}
												foreach($arr_tags as $values){
													if(!in_array($values, $arr_tag_ids)){
														$tag_data['activity_id'] = $insertedActivityId;
														$tag_data['group_id'] = $data['group_activity_group_id'];
														$tag_data['group_tag_id'] = $values;
														$this->getActivityTagTable()->saveActivityTags($tag_data);
													}	
												}
												if($oldactivityData->group_activity_type=='public'&&$activityData['group_activity_type']=='invited'){
													if(isset($data['invited_members']) && count($data['invited_members'])) {
														$arr_invited_users = explode(',',$data['invited_members']);
														foreach($arr_invited_users as $row) {						
															$isMember = $this->getUserGroupTable()->getUserGroup($row, $data['group_activity_group_id']);	
															if(isset($isMember->user_group_id) && !empty($isMember->user_group_id)){
																$this->activityInvitation($data['group_activity_owner_user_id'],$row,$insertedActivityId, $data['group_activity_group_id']);												 
															}							 
														}
													}
												}
												if($oldactivityData->group_activity_type=='invited'&&$activityData['group_activity_type']=='public'){
													$userList = $this->getUserGroupTable()->fetchAllUserListForGroup($data['group_activity_group_id'],$identity->user_id);
													foreach ($userList as $row){
														if($row!=$data['group_activity_owner_user_id']){
															$this->activityInvitation($data['group_activity_owner_user_id'],$row->user_id,$insertedActivityId,$data['group_activity_group_id']);							
														}
													}
												}
											
											}else{
												$error = 'Some error occured.. Please try again';
												$error_count++;
											}
										}else{
											$error = 'Activity is not exist in this system';
											$error_count++;
										}									 
				 
									}else{
										$error = "You can't edit past activities";
										$error_count++;
									}
								}else{
									$error = 'You don\'t have the permission to do this';
									$error_count++;
								}
							}else{
								$error = 'You don\'t have the permission to do this';
								$error_count++;
							}
						 
						}else{
							$error = 'Invalid access';
							$error_count++;
						}
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
					$error = 'Invalid access';
					$error_count++;
				}
			 } else{
				$error = 'Your session expired';
				$error_count++;
			 }
		 if($error_count==0){
			$return_array['msg'] = "Tags are added into the group tag cloud";
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function ajaxDeleteActivityAction(){
		$auth = new AuthenticationService();
		 $error ='';
		 $error_count = 0;
		 if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
			if($group_seo!=''&&$planet_seo!=''){	 
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$post = $request->getPost();
						$activity_id = $post->get('activity_id');
						if($activity_id!=''){
							$is_persmission = 0;						 	
							$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
							if($admin_status->is_admin){						 
									$is_persmission = 1;						 
							}else{
								$oldactivityData =$this->getActivityTable()->getActivity($activity_id);	
								if(!empty($oldactivityData)&& $oldactivityData->group_activity_id!=''){
									if($identity->user_id ==  $oldactivityData->group_activity_owner_user_id){
										$is_persmission = 1;
									}
								}
							}
							if($is_persmission){
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
									$this->getLikeTable()->deleteEventCommentLike($SystemTypeData->system_type_id,$activity_id) ;
									$this->getLikeTable()->deleteEventLike($SystemTypeData->system_type_id,$activity_id);
									$this->getCommentTable()->deleteEventComments($SystemTypeData->system_type_id,$activity_id);
									$this->getActivityInviteTable()->deleteAllInviteActivity($activity_id);
									$this->getActivityRsvpTable()->deleteAllActivityRsvp($activity_id);
									if($this->getActivityTable()->deleteActivity($activity_id)){
										$error_count=0;
									}else{
										$error = "Some error occured. Please try again";
										$error_count++;
									}
								
								
							}else{
								$error = "You don't have the permissions to do this";
								$error_count++;
							}
						}else{
							$error = 'Activity not exist in this system';
							$error_count++;
						}				 
						 
						}else{
							$error = 'Invalid access';
							$error_count++;
						}
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
					$error = 'Invalid access';
					$error_count++;
				}
			 } else{
				$error = 'Your session expired';
				$error_count++;
			 }
		 if($error_count==0){
			$return_array['msg'] = "Tags are added into the group tag cloud";
			$return_array['success'] = 1;
			$return_array['url'] = $this->url()->fromRoute('groups/planethome',array('group_id'=>$groupdetails->group_seo_title,'planet_id'=>$planetdetails->group_seo_title));
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
			$return_array['url'] = '';
		 }
		 echo json_encode($return_array);die();
	}
	public function approveActivityAction(){
		$auth = new AuthenticationService();
		$error ='';
		$error_count = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
			if($group_seo!=''&&$planet_seo!=''){	
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$post = $request->getPost();	
						 if($this->getGroupTable()->is_member($planet_id,$identity->user_id)){
							$delete_permission = 0;							
							$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);							 
							$member_approval = 0;
							$activity_approval = 0;
							if($admin_status->is_admin){
								$member_approval = 1;
								$activity_approval = 1;
							}
							$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);
							$member_delete_permission = 0;	
							foreach($permissions as $row){
								if($row->function_id == 2){
									$member_approval = 1;	
								}
								if($row->function_id == 8){
									$activity_approval = 1;	
								}
							}
							if($activity_approval){
								 $activity = $post['activity'];
								 if($activity){
									$checkRequestExist =$this->getActivityTable()->checkRequestExist($planet_id,$activity); 
									if(!empty($checkRequestExist)&&$checkRequestExist->group_activity_id!=''){
											if($this->getActivityTable()->makeActivityActive($activity)){
												$UserGroupNotificationData = array();						
												$UserGroupNotificationData['user_notification_user_id'] = $checkRequestExist->group_activity_owner_user_id;									
												$UserGroupNotificationData['user_notification_content'] = $identity->user_given_name." Approved your request to create new activity in the group".$planetdetails->group_title;
												$UserGroupNotificationData['user_notification_added_timestamp'] = date('Y-m-d H:i:s');
												$userObject = new user(array());
												$UserGroupNotificationData['user_notification_notification_type_id'] = "1";
												$UserGroupNotificationData['user_notification_status'] = 0;																		
												#lets Save the User Notification
												$UserGroupNotificationSaveObject = new UserNotification();
												$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
												$insertedUserGroupNotificationId ="";	#this will hold the latest inserted id value
												$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
												$error = 'Successfully updated';
												$error_count = 0;
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}		 
										 
									}else{
										$error = 'Activity is not exist in this system';
										$error_count++;
									}
								 }else{
									$error = 'Activity is not exist in this system';
									$error_count++;
								 }
							}else{
								$error = 'You don\'t have the permissions to do this';
								$error_count++;
							}
							 
						 }else{
							$error = 'You are not member of this group';
							$error_count++;
						 }
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
					$error = 'Invalid access';
					$error_count++;
				}
			}else{
				$error = 'Invalid access';
				$error_count++;
			}
		 } else{
			$error = 'Your session expired';
			$error_count++;
		 }
		 if($error_count==0){
			$return_array['msg'] = " ";
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function IgnoreActivityAction(){
		$auth = new AuthenticationService();
		$error ='';
		$error_count = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
			if($group_seo!=''&&$planet_seo!=''){	
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$post = $request->getPost();	
						 if($this->getGroupTable()->is_member($planet_id,$identity->user_id)){
							$delete_permission = 0;							
							$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);							 
							$member_approval = 0;
							$activity_approval = 0;
							if($admin_status->is_admin){
								$member_approval = 1;
								$activity_approval = 1;
							}
							$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);
							$member_delete_permission = 0;	
							foreach($permissions as $row){
								if($row->function_id == 2){
									$member_approval = 1;	
								}
								if($row->function_id == 8){
									$activity_approval = 1;	
								}
							}
							if($activity_approval){
								 $activity = $post['activity'];
								 if($activity){
									$checkRequestExist =$this->getActivityTable()->checkRequestExist($planet_id,$activity); 
									if(!empty($checkRequestExist)&&$checkRequestExist->group_activity_id!=''){
											if($this->getActivityTable()->makeActivityIgnore($activity)){												 
												$error = 'Successfully updated';
												$error_count = 0;
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}		 
										 
									}else{
										$error = 'Activity is not exist in this system';
										$error_count++;
									}
								 }else{
									$error = 'Activity is not exist in this system';
									$error_count++;
								 }
							}else{
								$error = 'You don\'t have the permissions to do this';
								$error_count++;
							}
							 
						 }else{
							$error = 'You are not member of this group';
							$error_count++;
						 }
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
					$error = 'Invalid access';
					$error_count++;
				}
			}else{
				$error = 'Invalid access';
				$error_count++;
			}
		 } else{
			$error = 'Your session expired';
			$error_count++;
		 }
		 if($error_count==0){
			$return_array['msg'] = " ";
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function removeActivityAction(){
		$auth = new AuthenticationService();
		$error ='';
		$error_count = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
			if($group_seo!=''&&$planet_seo!=''){	
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$post = $request->getPost();	
						 if($this->getGroupTable()->is_member($planet_id,$identity->user_id)){
							$delete_permission = 0;							
							$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);							 
							$member_approval = 0;
							$activity_approval = 0;
							if($admin_status->is_admin){
								$member_approval = 1;
								$activity_approval = 1;
							}
							$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);
							$member_delete_permission = 0;	
							foreach($permissions as $row){
								if($row->function_id == 2){
									$member_approval = 1;	
								}
								if($row->function_id == 8){
									$activity_approval = 1;	
								}
							}
							if($activity_approval){
								 $activity = $post['activity'];
								 if($activity){
									$checkRequestExist =$this->getActivityTable()->checkRequestExist($planet_id,$activity); 
									if(!empty($checkRequestExist)&&$checkRequestExist->group_activity_id!=''){
											$this->getActivityRsvpTable()->deleteAllActivityRsvp($activity);
											if($this->getActivityTable()->deleteActivity($activity)){												 
												$error = 'Successfully updated';
												$error_count = 0;
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}		 
										 
									}else{
										$error = 'Activity is not exist in this system';
										$error_count++;
									}
								 }else{
									$error = 'Activity is not exist in this system';
									$error_count++;
								 }
							}else{
								$error = 'You don\'t have the permissions to do this';
								$error_count++;
							}
							 
						 }else{
							$error = 'You are not member of this group';
							$error_count++;
						 }
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
					$error = 'Invalid access';
					$error_count++;
				}
			}else{
				$error = 'Invalid access';
				$error_count++;
			}
		 } else{
			$error = 'Your session expired';
			$error_count++;
		 }
		 if($error_count==0){
			$return_array['msg'] = " ";
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function getUserGroupPermissionsTable(){
        if (!$this->grouppermissionsTable) {
            $sm = $this->getServiceLocator();
            $this->grouppermissionsTable = $sm->get('Groups\Model\UserGroupPermissionsTable');
        }
        return $this->grouppermissionsTable;
    }
}