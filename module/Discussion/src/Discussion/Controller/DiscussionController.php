<?php   
####################Discussion Controller #################################
namespace Discussion\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;	//Needed for checking User session
use Zend\Authentication\Adapter\DbTable as AuthAdapter;	//Db adapter
use Zend\Crypt\BlockCipher;		# For encryption
#Group class
use Groups\Model\Groups;  	#Planet/Galaxy class
use User\Model\User;  	#User class
use Groups\Model\GroupsTable; #Planet/Galaxy table class
use Groups\Model\UserGroup;	#user group class
#discussion form
use \Exception;		#Exception class for handling exception
use Discussion\Model\Discussion; #Discussion class for loading Discussion
use Comment\Model\Comment; #comment class for loading discussion comment
use Discussion\Form\DiscussionForm;
use Discussion\Form\DiscussionFilter;
use Notification\Model\UserNotification; 
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
class DiscussionController extends AbstractActionController
{
    protected $groupTable;		#variable to hold the group model configuration
	protected $userGroupTable;	#variable to hold the user group model configuration
	protected $userTable;		#variable to hold the user model configuration
	protected $userProfileTable;	#variable to hold the user profile model configuration
	protected $discussionTable;		#variable to hold the discussion model configuration
	protected $commentTable;		#variable to hold the discussion model configuration
	protected $remoteAddr;
	protected $groupfunctionTable;
	protected $groupSettings;
	protected $groupTagTable;
	protected $tagTable;
	protected $likeTable;
	protected $groupjoiningrequestTable;
	protected $userNotificationTable;
	public function __construct(){
		return $this;
	}	
	#load the css and javascript need for particular action
	protected function getViewHelper($helperName){
		return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}   
 	#It is not using right now
    public function indexAction(){	
		$this->layout('layout/planet_home');
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();
		$SystemTypeId = 'Discussion';
		$is_admin = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();				
			$this->layout()->identity = $identity;
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
					if($planet_data->is_member){
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
						$viewModel->setVariable('group_settings', $group_settings);
						$groupTopWidget = $this->forward()->dispatch('Groups\Controller\Groups', array(
								'action' => 'groupTop',
								'group_id'     => $group_seo,
								'sub_group_id' => $planet_seo,
							));						 
						$viewModel->addChild($groupTopWidget, 'groupTopWidget');
						$discussion_permission = 0;
						$discussion_edit_permission = 0;
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}
						$viewModel->setVariable('is_admin', $is_admin);						
						if(!empty($group_settings)){
							if($admin_status->is_admin){						 
								$discussion_permission = 1;	
								$discussion_edit_permission = 1;									
							}elseif($group_settings->group_discussion_settings == 'None'){
								$discussion_permission = 0;	
							}
							elseif($group_settings->group_discussion_settings == 'Members'){
								$discussion_permission = 1;								
							}
							elseif($group_settings->group_discussion_settings == 'Admin'){
								 
								if(!empty($user_role)){
									$discussion_permission = 1;
									$discussion_edit_permission = 1;
								}else{
									$discussion_permission = 0;
								}
							}
							else{
								$discussion_permission = 0;
							} 
						}
						$viewModel->setVariable('planetdetails', $planet_data);	
						$viewModel->setVariable('discussion_edit_permission', $discussion_edit_permission);
						$viewModel->setVariable('discussion_permission', $discussion_permission);
						$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
						$SystemTypeData = $this->groupTable->fetchSystemType($SystemTypeId);
						$discussions = $this->discussionTable->getAllDiscussionWithOwnerdetails($planet_id,10,0);
						$arr_descussion = array();
						foreach($discussions as $rows){
							$arr_descussion[] = array(
												'group_discussion_content' =>$rows->group_discussion_content,
												'group_discussion_id' =>$rows->group_discussion_id,
												'user_given_name' =>$rows->user_given_name,
												'user_id' =>$rows->user_id,
												'user_profile_name' =>$rows->user_profile_name,
												'user_register_type' =>$rows->user_register_type,
												'user_fbid' =>$rows->user_fbid,
												'profile_photo' =>$rows->profile_photo,
												"descussion_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$rows->group_discussion_id,$identity->user_id),
												"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$rows->group_discussion_id)->comment_count,
												'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$rows->group_discussion_id,$identity->user_id,2,0),
												);
						}
						$viewModel->setVariable('discussions', $arr_descussion);	
						if($this->getUserGroupJoiningRequestTable()->checkIfrequestExist($identity->user_id,$planet_id)){
							$viewModel->setVariable('is_request', 1);
						}
						else{
							$viewModel->setVariable('is_request', 0);
						}
					}
					else{
						$error[] = "You are not the member of this group. if you want to see all features in this group you must be a member of this group";
					}
				}else{
					$error[] = "The groups you are requested is not existing in this system";
				}
			}else{
				$error[] = "The groups you are requested is not existing in this system";
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		$viewModel->setVariable('error', $error);	
		return $viewModel;		 
    }
	public function loadmoreAction(){
		$error = array();
		$success = array();
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$planet_id= $this->params('planet_id');
			if(isset($identity->user_id) && !empty($identity->user_id) && isset($planet_id) && !empty($planet_id)){
				$this->groupTable = $sm->get('Groups\Model\GroupsTable');
				$subGroupData = $this->groupTable->getSubGroup($planet_id);
				$groupData = $this->groupTable->getGroup($subGroupData->group_parent_group_id);
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
					$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
					$SystemTypeData = $this->groupTable->fetchSystemType('Discussion');
					$discussions = $this->discussionTable->getGroupAllDiscussionWithLikes($subGroupData->group_id,$SystemTypeData->system_type_id,$identity->user_id,$offset);
					$viewModel = new ViewModel(array('groupData' => $groupData,'subGroupData' => $subGroupData,'discussions' =>$discussions,'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages(),'discussion_page'=>$page+1));
					$viewModel->setTerminal($request->isXmlHttpRequest());
					return $viewModel;
				}
				else{	
					$error[] = 'Unauthorized access';
				}
			}else{
				$error[] = 'Unauthorized access';
			}
		}
		else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
	}	
	#Fetch All the Discussion of a Planet .This will used in Profile page as ajax Call
    public function discussionAction(){	
		$sm = $this->getServiceLocator(); 
		$sm->get('ControllerPluginManager')->get('GenericPlugin')->getHeaderFiles();		
		$error = array();	#Error variable
		$success = array();	#success message variable		
		$subGroupId = "";	//Id of Planet 
		$userData = array();	///this will hold data from y2m_user table
		$groupData = array();	//This will hold data for Galaxy
		$subGroupData = array();	//This will hold the data for Planet
		$discussions = array();	//this will hold all the activites of Planet		
		try {		
			$request   = $this->getRequest();		
			$subGroupId= $this->params('group_id'); 			
			$auth = new AuthenticationService();	
			$identity = null; 			
			if ($auth->hasIdentity()) {
            	// Identity exists get it
           		$identity = $auth->getIdentity();				
				#check the identity against the DB
				$this->userTable = $sm->get('User\Model\UserTable');
				$userData = $this->userTable->getUser($identity->user_id);	
				$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
				$this->groupTable = $sm->get('Group\Model\GroupTable');							
				if(isset($userData->user_id) && !empty($userData->user_id) && isset($subGroupId) && !empty($subGroupId)){				
					#Get Planet Info
					$subGroupData = $this->groupTable->getSubGroup($subGroupId);	
					if(isset($subGroupData->group_id) && !empty($subGroupData->group_id) && isset($subGroupData->group_parent_group_id) && !empty($subGroupData->group_parent_group_id)){						
						#fetch the Galaxy Info
						$groupData = $this->groupTable->getGroup($subGroupData->group_parent_group_id);	
						
						#fetch all activity of Planet
						$discussions = $this->discussionTable->getGroupAlldiscussion($subGroupData->group_id);						
					} 
				} 
			} 	
		} catch (\Exception $e) {
			//	echo "Caught exception: " . get_class($e) . "\n";
   			//	echo "Message: " . $e->getMessage() . "\n";			 
   		}   		
   		$viewModel = new ViewModel(array('userData' => $userData,'groupData' => $groupData,'subGroupData' => $subGroupData,'discussions' =>$discussions,'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()));
    	$viewModel->setTerminal($request->isXmlHttpRequest());
    	return $viewModel;	   
    }
	public function getGroupTable(){
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    }
	public function getGroupfunctionTable(){
        if (!$this->groupfunctionTable) {
            $sm = $this->getServiceLocator();
            $this->groupfunctionTable = $sm->get('Groupfunction\Model\GroupfunctionTable');
        }
        return $this->groupfunctionTable;
    }
	public function getGroupSettingsTable(){
        if (!$this->groupSettings) {
            $sm = $this->getServiceLocator();
            $this->groupSettings = $sm->get('Groups\Model\GroupSettingsTable');
        }
        return $this->groupSettings;
    }
	public function getGroupTagTable(){
        if (!$this->groupTagTable) {
            $sm = $this->getServiceLocator();
            $this->groupTagTable = $sm->get('Tag\Model\GroupTagTable');
        }
        return $this->groupTagTable;
    }
	public function getTagTable(){
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Tag\Model\TagTable');
        }
        return $this->tagTable;
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
	public function ajaxAddDiscussionAction(){		 
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = '';;
		$identity = null;	
		$request   = $this->getRequest();
		$return_array = array();
		$error_cnt = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('planet_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
					if($planet_data->is_member){
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);		
						$discussion_permission = 0;						 						
						if(!empty($group_settings)){
							if($admin_status->is_admin){						 
								$discussion_permission = 1;						 
							}elseif($group_settings->group_discussion_settings == 'None'){
								$discussion_permission = 0;	
							}
							elseif($group_settings->group_discussion_settings == 'Members'){
								$discussion_permission = 1;								
							}
							elseif($group_settings->group_discussion_settings == 'Admin'){
								$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
								if(!empty($user_role)){
									$discussion_permission = 1;
								}else{
									$discussion_permission = 0;
								}
							}
							else{
								$discussion_permission = 0;
							} 
						}
						if($discussion_permission){	
							$this->remoteAddr = $sm->get('ControllerPluginManager')->get('GenericPlugin')->getRemoteAddress();
							$post = $request->getPost();
							if(!empty($post)&&$post['discussion_mater']!=''){
								$discussionData = array();
								$discussionData['group_discussion_content'] = $post['discussion_mater'];
								$discussionData['group_discussion_owner_user_id'] = $identity->user_id;
								$discussionData['group_discussion_group_id'] = $planet_id;						 
								$discussionData['group_discussion_added_ip_address'] =  $this->remoteAddr;
								$discussionData['group_discussion_modified_ip_address'] =  $this->remoteAddr;
								$discussionData['group_discussion_modified_timestamp'] = date("Y-m-d H:i:s");
								$discussionData['group_discussion_status'] = 1;	
								$discussionObject = new discussion();
								$discussionObject->exchangeArray($discussionData);
								$inserteddiscussionId = "";	#this will hold the latest inserted id value
								$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
								$inserteddiscussionId = $this->discussionTable->saveDiscussion($discussionObject);								 
							}						
							if(!isset($inserteddiscussionId) && empty($inserteddiscussionId)) {
								$error = 'Oops an error has occured while posting discussion';
								$error_cnt++;
							}else{							 
								$joinedMembers =$this->getUserGroupTable()->getPlanetMembersWithGroupSettings($planet_id); 
								$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
								foreach($joinedMembers as $members){ 
									$permission = 1;
									if((isset($members->discussion)&&$members->discussion=='no')){
										$permission =0;
									}
									if($members->user_group_user_id!=$identity->user_id&&$permission){
										$config = $this->getServiceLocator()->get('Config');
										$base_url = $config['pathInfo']['base_url'];
										$msg = $identity->user_given_name." added a new discussion under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/discussion'>".$subGroupData->group_title."</a>";
										$subject = 'New discussion started';
										$from = 'admin@jeera.com';
										$this->UpdateNotifications($members->user_group_user_id,$msg,2,$subject,$from);
									}
								}										 
							}
						}else{
							$error  = "You don't have the permissions to add discussions on this group.";
							$error_cnt++;
						}
					 
					}
					else{
						$error  = "You are not the member of this group. if you want to see all features in this group you must be a member of this group";
						$error_cnt++;
					}
				}else{
					$error  = "The groups you are requested is not existing in this system";
					$error_cnt++;
				}
			}else{
				$error = "The groups you are requested is not existing in this system";
				$error_cnt++;
			}
		}else{
				$error = "Your session expired. Please try again after login";
				$error_cnt++; 
		}
		if($error_cnt == 0){
			$return_array = array('error' =>0,'msg'=>$error);
		}else{
			$return_array = array('error' =>1,'msg'=>$error);
		}
	 echo json_encode($return_array);die();
		
	}
	public function loadmoreDiscussionAction(){
		$error = array();
		$auth = new AuthenticationService();
		$sm = $this->getServiceLocator();
		$viewModel = new ViewModel();
		$arr_descussion = array();
		$is_admin = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			$viewModel->setVariable('user_id', $identity->user_id);
			if ($request->isPost()) {
				$post = $request->getPost();
				$planet = $post['planet'];
				$group = $post['group'];
				$page = $post['page'];			 
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet);
				$planet_id = $planetdetails->group_id;
				$discussion_edit_permission = 0;
				if($this->getGroupTable()->is_member($planet_id,$identity->user_id)){
					$viewModel->setVariable('planet_member', 1);
					$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
					if($admin_status->is_admin){
						$discussion_edit_permission = 1;	
					}
					else{
						$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
						if(!empty($user_role)){						 
							$discussion_edit_permission = 1;
						}
						 
					}
				}else{	
					$viewModel->setVariable('planet_member', 0);
				}
				 $viewModel->setVariable('discussion_edit_permission', $discussion_edit_permission);
				if($group_id && $planet_id){
					$page_limit = 0;
					if($page>0){
					$page_limit = 10*($page);
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
					 $this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');					 
					 $discussions = $this->discussionTable->getAllDiscussionWithOwnerdetails($planet_id,10,$page_limit);
					$SystemTypeData = $this->getGroupTable()->fetchSystemType('Discussion');
					foreach($discussions as $rows){
						$arr_descussion[] = array(
											'group_discussion_content' =>$rows->group_discussion_content,
											'group_discussion_id' =>$rows->group_discussion_id,
											'user_given_name' =>$rows->user_given_name,
											'user_id' =>$rows->user_id,
											'user_profile_name' =>$rows->user_profile_name,
											'user_register_type' =>$rows->user_register_type,
											'user_fbid' =>$rows->user_fbid,
											'profile_photo' =>$rows->profile_photo,											
											"descussion_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$rows->group_discussion_id,$identity->user_id),
											"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$rows->group_discussion_id)->comment_count,
											'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$rows->group_discussion_id,$identity->user_id,2,0),
											);
					}
					$viewModel->setVariable('discussions', $arr_descussion);	
					
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
		//$viewModel->setVariable('upcoming_activities', $arr_upcaoming_activities);
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function getUserGroupTable(){
        if (!$this->userGroupTable) {
            $sm = $this->getServiceLocator();
			$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
        }
        return $this->userGroupTable;
    }
	public function ajaxEditDiscussionAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = '';;
		$identity = null;	
		$request   = $this->getRequest();
		$return_array = array();
		$error_cnt = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('planet_id');
			
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
					if($planet_data->is_member){
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);		
						$discussion_permission = 0;						 						
						if(!empty($group_settings)){
							if($admin_status->is_admin){ 			 
								$discussion_permission = 1;						 
							}else {
								$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
								if(!empty($user_role)){  
									$discussion_permission = 1;
								}
								$post = $request->getPost();
								$discussion_id = $post['discussion_id'];
								$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
								$discussion_details = $this->discussionTable->getDiscussion($discussion_id);
								if($discussion_details->group_discussion_owner_user_id == $identity->user_id){
									$discussion_permission = 1;  
								}
							}							 
						}
						if($discussion_permission){	
							$this->remoteAddr = $sm->get('ControllerPluginManager')->get('GenericPlugin')->getRemoteAddress();
							$post = $request->getPost();
							if(!empty($post)&&$post['discussion']!=''){
								$discussionData = array();
								$discussionData['group_discussion_content'] = $post['discussion'];								 
								$discussionData['group_discussion_modified_ip_address'] =  $this->remoteAddr;
								$discussionData['group_discussion_modified_timestamp'] = date("Y-m-d H:i:s");								 
								$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
								if($this->discussionTable->updateDiscussionData($discussionData,$post['discussion_id'])){
									$error = 'Successfully updated the discussions';
									$error_cnt = 0;
								}else{
									$error = 'Some error occured. Please try again';
									$error_cnt = 0;
								}
								 
							}else{
								$error = 'Content required';
								$error_cnt = 0;
							}							
						
							 
						}else{
							$error  = "You don't have the permissions to add discussions on this group.";
							$error_cnt++;
						}
					 
					}
					else{
						$error  = "You are not the member of this group. if you want to see all features in this group you must be a member of this group";
						$error_cnt++;
					}
				}else{
					$error  = "The groups you are requested is not existing in this system";
					$error_cnt++;
				}
			}else{
				$error = "The groups you are requested is not existing in this system";
				$error_cnt++;
			}
		}else{
				$error = "Your session expired. Please try again after login";
				$error_cnt++; 
		}
		if($error_cnt == 0){
			$return_array = array('error' =>0,'msg'=>$error);
		}else{
			$return_array = array('error' =>1,'msg'=>$error);
		}
	 echo json_encode($return_array);die();
		
	}
	public function ajaxDeleteDiscussionAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = '';;
		$identity = null;	
		$request   = $this->getRequest();
		$return_array = array();
		$error_cnt = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('planet_id');
			
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
					if($planet_data->is_member){
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);		
						$discussion_permission = 0;						 						
						if(!empty($group_settings)){
							if($admin_status->is_admin){ 			 
								$discussion_permission = 1;						 
							}else {
								$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
								if(!empty($user_role)){  
									$discussion_permission = 1;
								}
								$post = $request->getPost();
								$discussion_id = $post['discussion_id'];
								$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
								$discussion_details = $this->discussionTable->getDiscussion($discussion_id);
								if($discussion_details->group_discussion_owner_user_id == $identity->user_id){
									$discussion_permission = 1;  
								}
							}							 
						}
						if($discussion_permission){	
							$post = $request->getPost();
							if(!empty($post)&&$post['discussion_id']!=''){
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Discussion');
								$this->getLikeTable()->deleteEventCommentLike($SystemTypeData->system_type_id,$post['discussion_id']) ;
								$this->getLikeTable()->deleteEventLike($SystemTypeData->system_type_id,$post['discussion_id']);	
								$this->getCommentTable()->deleteEventComments($SystemTypeData->system_type_id,$post['discussion_id']);	
								$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
								if($this->discussionTable->deleteDiscussion($post['discussion_id'])	){
									$error = 'Successfully removed';
									$error_cnt=0;
								}else{
									$error = 'Unable to process';
									$error_cnt++;
								}
							}else{
								$error = 'Unable to process';
								$error_cnt++;
							}							
						
							 
						}else{
							$error  = "You don't have the permissions to add discussions on this group.";
							$error_cnt++;
						}
					 
					}
					else{
						$error  = "You are not the member of this group. if you want to see all features in this group you must be a member of this group";
						$error_cnt++;
					}
				}else{
					$error  = "The groups you are requested is not existing in this system";
					$error_cnt++;
				}
			}else{
				$error = "The groups you are requested is not existing in this system";
				$error_cnt++;
			}
		}else{
				$error = "Your session expired. Please try again after login";
				$error_cnt++; 
		}
		if($error_cnt == 0){
			$return_array = array('error' =>0,'msg'=>$error);
		}else{
			$return_array = array('error' =>1,'msg'=>$error);
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
	public function UpdateNotifications($user_notification_user_id,$msg,$type,$subject,$from){
		$UserGroupNotificationData = array();						
		$UserGroupNotificationData['user_notification_user_id'] =$user_notification_user_id;		 
		$UserGroupNotificationData['user_notification_content']  = $msg;
		$UserGroupNotificationData['user_notification_added_timestamp'] = date('Y-m-d H:i:s');			
		$UserGroupNotificationData['user_notification_notification_type_id'] = $type;
		$UserGroupNotificationData['user_notification_status'] = 0;		
		#lets Save the User Notification
		$UserGroupNotificationSaveObject = new UserNotification();
		$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);	
		$insertedUserGroupNotificationId ="";	#this will hold the latest inserted id value
		$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
		$userData = $this->getUserTable()->getUser($user_notification_user_id); 
		$this->sendNotificationMail($msg,$subject,$userData->user_email,$from);
	}
	public function sendNotificationMail($msg,$subject,$emailId,$from){
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');		
		$body = $this->renderer->render('activity/email/emailinvitation.phtml', array('msg'=>$msg));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";
		$textPart = new MimePart($body);
		$textPart->type = "text/plain";
		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));
		$message = new Mail\Message();
		$message->setFrom($from);
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
	public function getUserNotificationTable(){
        if (!$this->userNotificationTable) {
            $sm = $this->getServiceLocator();
            $this->userNotificationTable = $sm->get('Notification\Model\UserNotificationTable');
        }
        return $this->userNotificationTable;
    }
	public function getUserTable(){
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
}
