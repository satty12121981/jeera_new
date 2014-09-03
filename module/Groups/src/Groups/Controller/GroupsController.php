<?php 
namespace Groups\Controller;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Groups\Model\Groups; 
use User\Model\User; 
use Groups\Model\UserGroup;
use Notification\Model\UserNotification;
use Groups\Model\UserGroupJoiningRequest;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
class GroupsController extends AbstractActionController
{
	protected $groupTable;
	protected $groupThumb ="";	
	protected $Group_Timeline_Path="";	//Image path of Group Timelime
	protected $Group_Thumb_Smaller ="";
	protected $photoTable="";
	protected $userGroupRequestTable;
	protected $userGroupTable;
	protected $groupTagTable=""; 
	protected $ActivityController;
	protected $userNotificationTable;
	protected $userTable;
	protected $countryTable;
	protected $albumTable;
	protected $albumDataTable;
	protected $groupfunctionTable;
	protected $grouppermissionsTable;
	protected $groupSettings;
	protected $tagTable;
	protected $commentTable;
	protected $activityRsvpTable;
	protected $likeTable;
	protected $groupjoiningrequestTable;
	protected $groupJoiningQuestionnaireTable;
	protected $groupQuestionnaireAnswersTable;
	protected $groupQuestionnaireOptionsTable;
	public function __construct(){
        // do some stuff!
		$this->groupThumb = Groups::Group_Thumb_Path;  
		$this->Group_Timeline_Path = Groups::Group_Timeline_Path;  
		$this->Group_Thumb_Smaller = Groups::Group_Thumb_Smaller; 
    }
	public function indexAction(){			
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$allGroups = $this->getGroupTable()->fetchAllGroups();
			$viewModel = new ViewModel(array('allGroups' => $allGroups, 'groupThumb' => $this->groupThumb));
			return $viewModel;
		}
		else{	
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
	}
	public function planetAction(){
		$auth = new AuthenticationService();
		$string_search		 = '';
		$sort_content ='';
		$page_limit = 10;
		$offset = 0;
		$user_id = 0;
		$group_id = 0;
		$galexy = 'all';
		$viewModel = new ViewModel();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$this->layout()->page = 'Planet';
			$identity->profile_pic = '';
			$user_id = $identity->user_id;
			$profilepic = $this->getUserTable()->getUserProfilePic($user_id);
			foreach($profilepic as $pic){$identity->profile_pic = $pic->biopic;}
			
		}
		$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 		 
		if($group_seo!=''){
			if($group_seo=='all'){
				$planet = $this->getGroupTable()->getAllPlanetsWithUsers($user_id,$group_id,$offset,$page_limit,$string_search,$sort_content);
				$viewModel->setVariable('planet', $planet);
				$viewModel->setVariable('galexy', $galexy);
				if ($auth->hasIdentity()) {
					$planetSugessions = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'planetSuggestions',								 
								));
					$viewModel->addChild($planetSugessions, 'planetSugessions');
				}
				
			
			}else{
				$galexy = $group_seo;
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planet = $this->getGroupTable()->getAllPlanetsWithUsers($user_id,$group_id,$offset,$page_limit,$string_search,$sort_content);
				$viewModel->setVariable('planet', $planet);
				$viewModel->setVariable('galexy', $galexy);
				if ($auth->hasIdentity()) {
					$planetSugessions = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'planetSuggestions',								 
								));
					$viewModel->addChild($planetSugessions, 'planetSugessions');
				} 
				 
			}
			return $viewModel;
		}
		else{
			return $this->redirect()->toRoute('groups', array('action' => 'index'));
		}	 
	}
	public function ajaxPlanetSearchAction(){
		$auth = new AuthenticationService();
		$vm = new ViewModel();	
		$request   = $this->getRequest();
		$post = $request->getPost();	
		$string_search		 = '';
		$sort_content ='';
		$page_limit = 10;
		$offset = 0;
		$user_id = 0;
		$group_id = 0;
		$galexy = 'all';
		if ($request->isPost()){
			$string_search =$post->get('search_content');
			$galexy = $post->get('galexy');
			if($galexy==''){$galexy = 'all';}
		}
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			
			$user_id = $identity->user_id;
		}
		if($galexy=='all'){		
			$group_id = 0;
		}else{
			$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($galexy);
			$group_id = $groupdetails->group_id;
		}
		$planet = $this->getGroupTable()->getAllPlanetsWithUsers($user_id,$group_id,$offset,$page_limit,$string_search,$sort_content); 
		$vm->setVariable('planet', $planet);
		$vm->setTemplate('groups/groups/ajax-planet-sort');
		$vm->setTerminal($request->isXmlHttpRequest());
		return $vm;
	}
	public function ajaxPlanetSortAction(){
		$auth = new AuthenticationService();
		$vm = new ViewModel();	
		$request   = $this->getRequest();
		$post = $request->getPost();	
		$string_search		 = '';
		$sort_content ='';
		$page_limit = 10;
		$offset = 0;
		$user_id = 0;
		$group_id = 0;
		if ($request->isPost()){
			$sort_content =$post->get('sort_content');
			$galexy = $post->get('galexy');
			if($galexy==''){$galexy = 'all';}			
		}
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			
			$user_id = $identity->user_id;
		}
		if($galexy=='all'){		
			$group_id = 0;
		}else{
			$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($galexy);
			$group_id = $groupdetails->group_id;
		}		
		$planet = $this->getGroupTable()->getAllPlanetsWithUsers($user_id,$group_id,$offset,$page_limit,$string_search,$sort_content); 
		$vm->setVariable('planet', $planet);
		$vm->setTemplate('groups/groups/ajax-planet-sort');
		$vm->setTerminal($request->isXmlHttpRequest());
		return $vm;
	}
	public function ajaxLoadPlanetAction(){
		$auth = new AuthenticationService();
		$vm = new ViewModel();	
		$request   = $this->getRequest();
		$post = $request->getPost();	
		$string_search		 = '';
		$sort_content ='';
		$page_limit = 10;
		$offset = 0;
		$user_id = 0;
		$group_id = 0;
		if ($request->isPost()){
			$sort_content =$post->get('sort_content');
			$string_search	= $post->get('search_content');
			$galexy = $post->get('galexy');
			if($galexy==''){$galexy = 'all';}
			$page =$post->get('page');
			if(!$page)
			$page = 0;	
			$offset	= $page*10;
		}
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			
			$user_id = $identity->user_id;
		}
		if($galexy=='all'){		
			$group_id = 0;
		}else{
			$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($galexy);
			$group_id = $groupdetails->group_id;
		}		
		$planet = $this->getGroupTable()->getAllPlanetsWithUsers($user_id,$group_id,$offset,$page_limit,$string_search,$sort_content); 
		$vm->setVariable('planet', $planet);
		$vm->setTemplate('groups/groups/ajax-planet-sort');
		$vm->setTerminal($request->isXmlHttpRequest());
		return $vm;
	}
	public function planethomeAction(){
		$auth = new AuthenticationService();	
		$viewModel = new ViewModel();
		if ($auth->hasIdentity()) {
			$this->layout('layout/planet_home');
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('planet_id'); 
			$is_admin = 0;
			if($group_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
					$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
					$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
					$viewModel->setVariable('group_settings', $group_settings);					 
					$groupTopWidget = $this->forward()->dispatch('Groups\Controller\Groups', array(
								'action' => 'groupTop',
								'group_id'     => $group_seo,
								'sub_group_id' => $planet_seo,
							));
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}
					$viewModel->addChild($groupTopWidget, 'groupTopWidget');
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
					$viewModel->setVariable('is_admin', $is_admin);
					$viewModel->setVariable('activity_permission', $activity_permission);
					$upcoming_activities = $this->getActivityTable()->getAllUpcomingActivityWithCountofUsersLikeComment($identity->user_id,$planet_id,0,10);
					$arr_upcaoming_activities = array();
					$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
					foreach($upcoming_activities as $activities){	
						$arr_upcaoming_activities[] = array(
													"group_activity_title" => $activities->group_activity_title,
													"group_activity_location" => $activities->group_activity_location,
													"group_activity_content" => $activities->group_activity_content,
													"group_activity_start_timestamp" => $activities->group_activity_start_timestamp,
													"group_activity_id" => $activities->group_activity_id,
													"group_activity_owner_user_id" => $activities->group_activity_owner_user_id,
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
													'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activities->group_activity_id,$identity->user_id,2,0),
													'members' =>$this->getActivityRsvpTable()->getJoinMembers($activities->group_activity_id,3,0)
													);
					}
					if($this->getUserGroupJoiningRequestTable()->checkIfrequestExist($identity->user_id,$planet_id)){
						$viewModel->setVariable('is_request', 1);
					}
					else{
						$viewModel->setVariable('is_request', 0);
					}
					$LimitedgroupTag =	$this->getGroupTagTable()->fetchAllTagsOfGroup($planet_id,10,0);
					$viewModel->setVariable('LimitedgroupTag', $LimitedgroupTag);
					$viewModel->setVariable('upcoming_activities', $arr_upcaoming_activities);
					$past_activities = $this->getActivityTable()->getAllPastActivityWithCountofUsersLikeComment($identity->user_id,$planet_id,0,10);
					$arr_past_activities = array();					
					foreach($past_activities as $activities){	
						$arr_past_activities[] = array(
													"group_activity_title" => $activities->group_activity_title,
													"group_activity_location" => $activities->group_activity_location,
													"group_activity_content" => $activities->group_activity_content,
													"group_activity_start_timestamp" => $activities->group_activity_start_timestamp,
													"group_activity_id" => $activities->group_activity_id,
													"group_activity_owner_user_id" => $activities->group_activity_owner_user_id,
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
													'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activities->group_activity_id,$identity->user_id,2,0),
													'members' =>$this->getActivityRsvpTable()->getJoinMembers($activities->group_activity_id,3,0)
													);
					}
					$viewModel->setVariable('past_activities', $arr_past_activities);
					$today_activities = $this->getActivityTable()->getAllTodayActivityWithCountofUsersLikeComment($identity->user_id,$planet_id);
					$arr_today_activities = array();					
					foreach($today_activities as $activities){	
						$arr_today_activities[] = array(
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
													//'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activities->group_activity_id,$identity->user_id,2,0),
													'members' =>$this->getActivityRsvpTable()->getJoinMembers($activities->group_activity_id,3,0)
													);
					}
					$viewModel->setVariable('today_activities', $arr_today_activities);
					//print_r($upcoming_activities);die();
					//$subGroupPhotoData = $this->getPhotoTable()->getPhoto($planetdetails->group_photo_id);
					//$groupTag =	$this->getGroupTagTable()->fetchAllTagsOfGroup($planetdetails->group_id);
					//$userSubGroupData =$this->getUserGroupTable()->getUserGroup($identity->user_id, $planet_id);
				//	$groupActivityWidget = $this->forward()->dispatch('Activity\Controller\Activity', array('action' => 'activitydetail', 'group_id' => $planetdetails->group_id));	
					//$viewModel = new ViewModel(array('planetdetails' => $planetdetails, 'groupThumb' => $this->groupThumb,'parent_name'=>$groupdetails->group_title));
					//$viewModel = new ViewModel(array('groupThumb' => $this->groupThumb, 'Group_Timeline_Path' => $this->Group_Timeline_Path, 'Group_Thumb_Smaller' => $this->Group_Thumb_Smaller, 'groupData' => $groupdetails, 'subGroupData' => $planetdetails, 'subGroupPhotoData' => $subGroupPhotoData,'userSubGroupData' => $userSubGroupData, 'groupTag' => $groupTag));
					//if(!empty($groupActivityWidget)){	
				//		$viewModel->addChild($groupActivityWidget, 'groupActivityWidget');	
				//	}
					$viewModel->setVariable('planetdetails', $planet_data);					 
					return $viewModel;
				}
				else{
					return $this->redirect()->toRoute('home', array('action' => 'index'));
				}			
			}
			else{
				return $this->redirect()->toRoute('home', array('action' => 'index'));
			}
		}
		else{	
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
	}
	public function joinAction(){
		$return_array = array();
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('planet_id'); 
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$subGroupAlreadyRegisterInfo =$this->getUserGroupTable()->getUserGroup($identity->user_id,  $planet_id);
						if(isset($subGroupAlreadyRegisterInfo->user_group_id) && !empty($subGroupAlreadyRegisterInfo->user_group_id)){	 
							$return_array = array("msg"=>"You are already a member of this group",
								'error'=>1,
								'button_hide'=>0
							);						 
						}else{	
							$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
							if($group_settings->group_member_join_type == 'Any'){
								$userGroupData = array();
								$userGroupData['user_group_user_id'] = $identity->user_id;
								$userGroupData['user_group_group_id'] = $planet_id;	
								$objUser = new User();
								$userGroupData['user_group_added_ip_address'] = $objUser->getUserIp();
								$userGroupData['user_group_status'] = "1";
								$userGroupData['user_group_is_owner'] = "0";
								$userGroup = new UserGroup();
								$userGroup->exchangeArray($userGroupData);
								$insertedUserGroupId ="";
								$insertedUserGroupId = $this->getUserGroupTable()->saveUserGroup($userGroup);
								if(isset($insertedUserGroupId) && !empty($insertedUserGroupId)){
									$return_array = array("msg"=>"You are joined in this group",
									'error'=>0,
									'button_hide'=>1
									);
								   
								   $admin_users = $this->getUserGroupTable()->getAllAdminUsersWithGroupSettings($planet_id);
								   $subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
								   foreach($admin_users as $users){
									 $permission = 1;
									if((isset($users->member)&&$users->member!='no')){
										$permission =0;
									}
									 $config = $this->getServiceLocator()->get('Config');
									 $base_url = $config['pathInfo']['base_url'];
									 $msg = $identity->user_given_name." joined in <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."' />".$subGroupData->group_title."</a>";
									$subject = 'Group join request';
									$from = 'admin@jeera.com';
									$this->UpdateNotifications($users->user_group_user_id,$msg,2,$subject,$from);
								  }
									  
								}else{
									$return_array = array("msg"=>"Some error occured.Please try again later",
									'error'=>1,
									'button_hide'=>0
									);	
								}
							}elseif($group_settings->group_member_join_type == 'AdminApproval'){
								if($this->getUserGroupJoiningRequestTable()->checkIfrequestExist($identity->user_id,$planet_id)){
									$return_array = array("msg"=>"You are already requested",
									'error'=>1,
									'button_hide'=>0
									);
								}
								else{
									$userJoiningRequest = array();
									$userJoiningRequest['user_group_joining_request_user_id'] = $identity->user_id;
									$userJoiningRequest['user_group_joining_request_group_id'] = $planet_id;
									$objUser = new User();
									$userJoiningRequest['user_group_joining_request_added_ip_address'] = $objUser->getUserIp();
									$UserGroupJoiningRequestObject = new UserGroupJoiningRequest();
									$UserGroupJoiningRequestObject->exchangeArray($userJoiningRequest);
									if($insertedUserGroupJoiningRequest = $this->getUserGroupJoiningRequestTable()->saveUserGroupJoiningRequest($UserGroupJoiningRequestObject)){
										$admin_users = $this->getUserGroupTable()->getAllAdminUsers($planet_id);
										$return_array = array("msg"=>"Your joining request is send to admin..",
										'error'=>1,
										'button_hide'=>1
										);
										$admin_users = $this->getUserGroupTable()->getAllAdminUsersWithGroupSettings($planet_id);
										$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
										foreach($admin_users as $users){
											$permission = 1;
											if((isset($users->member)&&$users->member!='no')){
												$permission =0;
											}
											$config = $this->getServiceLocator()->get('Config');
											$base_url = $config['pathInfo']['base_url'];
											$msg = $identity->user_given_name." requested to join  <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."' />".$subGroupData->group_title."</a>";
											$subject = 'Group join request';
											$from = 'admin@jeera.com';
											$this->UpdateNotifications($users->user_group_user_id,$msg,2,$subject,$from);
										}
									}
									else{
										$return_array = array("msg"=>"Sorry some error occured. Please try again later..",
										'error'=>1,
										'button_hide'=>0
										);
									}
								}
							}
							else{
								$return_array = array("msg"=>"Sorry this is a closed group..You can't join in this group",
								'error'=>1,
								'button_hide'=>0
								);	
							}
						}	
					}else{
						$return_array = array("msg"=>"Invalid access..",
								'error'=>1,
								'button_hide'=>0
							);	
					}
				}else{
					$return_array = array("msg"=>"Invalid access..",
								'error'=>1,
								'button_hide'=>0
							);	
				}
			}else{
				$return_array = array("msg"=>"Invalid access..",
								'error'=>1,
								'button_hide'=>0
							);		
			}		
			 		
		}
		else{
			$return_array = array("msg"=>"Your session expired. Please login and try again",
								'error'=>1,
								'button_hide'=>0
							);		 	
		}
		echo json_encode($return_array);die();
	}
	protected function loadPlanetMembersAction() {
		$this->layout('layout/planet_home');
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();		 
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
						if($admin_status->is_admin){
							$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
							$viewModel->setVariable('group_settings', $group_settings);					 
					 	}
					}
					$groupTopWidget = $this->forward()->dispatch('Groups\Controller\Groups', array(
								'action' => 'groupTop',
								'group_id'     => $group_seo,
								'sub_group_id' => $planet_seo,
							));						 
					$viewModel->addChild($groupTopWidget, 'groupTopWidget');
					$viewModel->setVariable('planetdetails', $planet_data);	
					$viewModel->setVariable('user_id', $identity->user_id);	
					$this->userTable = $sm->get('User\Model\UserTable');
					$userData = $this->userTable->getUser($identity->user_id);              
					$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
					$members_count = $this->userGroupTable->countGroupMembers($planet_id)->memberCount;
					$viewModel->setVariable('members_count', $members_count);
					$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
					$viewModel->setVariable('group_settings', $group_settings);
					$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);
					$member_delete_permission = 0;	
					foreach($permissions as $row){
						if($row->function_id == 4){
							$member_delete_permission = 1;	
						}
					}
					$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
					if($admin_status->is_admin){
						$member_delete_permission = 1;
					}
					if($member_delete_permission){
						$groupUsersList = $this->userGroupTable->fetchAllUserListForGroup($planet_id,$identity->user_id,0,25);					
					}else{
						$groupUsersList = $this->userGroupTable->fetchAllActiveUserListForGroup($planet_id,$identity->user_id,0,25);			
					}
					$viewModel->setVariable('groupUsersList', $groupUsersList);
					$viewModel->setVariable('member_delete_permission', $member_delete_permission);
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
	public function ajaxSettingsAction(){
		 $auth = new AuthenticationService();
		 $error ='';
		 $error_count = 0;
		 if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('planet_id'); 
			if($group_seo!=''&&$planet_seo!=''){	
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								switch($post['type']){
									case "basic":
										if(isset($post['title'])&&$post['title']==''){
											$error = 'Group name Cannot be blank';
											$error_count++;
										}
										if(isset($post['country'])&&$post['country']==''){
											$error = 'Select Country';
											$error_count++;
										}
										if(isset($post['city'])&&$post['city']==''){
											$error = 'Select city';
											$error_count++;
										}
										if(isset($post['location'])&&$post['location']==''){
											$error = 'Select location';
											$error_count++;
										}
										if(isset($post['description'])&&$post['description']==''){
											$error = 'Say something about your group';
											$error_count++;
										}
										if($error_count==0){
											$planet_data = array();
											if(isset($post['title'])&&$post['title']!='')
												$planet_data['group_title'] = $post['title'];
											if(isset($post['location'])&&$post['location']!='')
												$planet_data['group_location'] = $post['location'];
											if(isset($post['city'])&&$post['city']!='')
												$planet_data['group_city_id'] = $post['city'];
											if(isset($post['country'])&&$post['country']!=''){
												$country = $this->getCountryTable()->getCountryIdFromGeoCode($post['country']);
												$planet_data['group_country_id'] = $country->country_id;
											}
											if(isset($post['webaddress'])&&$post['webaddress']!='')
												$planet_data['group_web_address'] = $post['webaddress'];
											if(isset($post['description'])&&$post['description']!='')
												$planet_data['group_discription'] = $post['description'];
											if(isset($post['welcome_message'])&&$post['welcome_message']!='')
												$planet_data['group_welcome_message_members'] = $post['welcome_message'];
											if(isset($post['geo_latitude'])&&$post['geo_latitude']!='')
												$planet_data['y2m_group_location_lat'] = $post['geo_latitude'];
											if(isset($post['geo_longitude'])&&$post['geo_longitude']!='')
												$planet_data['y2m_group_location_lng'] = $post['geo_longitude'];
											 if(isset($_FILES)&&!empty($_FILES)){ 
												
												// $album_id = $this->getAlbumTable()->CreateCoverPicAlbumIfNotExist($planet_id,$identity->user_id); 
												 $album_id =0;											 
												 $config = $this->getServiceLocator()->get('Config'); 
												 $output_dir = $config['pathInfo']['AlbumUploadPath']; 
												 $base_url = $config['pathInfo']['base_url'];
												 $imagesizedata = getimagesize($_FILES[0]["tmp_name"]); 
												 if ($imagesizedata === FALSE)
												 {
													$error = 'Image size is not valid';
													$error_count++;
												 }else{  
													if ($_FILES[0]["error"] > 0)
													{
														$error = $_FILES[0]["error"];
														$error_count++;
													}
													else{  
														$newfilename = time().$_FILES[0]["name"];
														$target = $output_dir.$planet_id.'/';
														if(!is_dir($target)) {
															mkdir($target);	
														}
														$target = $output_dir.$planet_id ."/main/";
														$target_root = $output_dir.$planet_id;
														if(!is_dir($target)) {
															mkdir($target);	
														}
														$output_dir = $target. $newfilename; 
														move_uploaded_file($_FILES[0]["tmp_name"],$output_dir);
														$resizeObj = $this->ResizePlugin();
														$image_path = $base_url.'/public/album/'.$planet_id.'/main/'.$newfilename; 
														$resizeObj->assignImage($image_path);

														//*** 2) Resize image (options: exact, portrait, landscape, auto, crop)
														$resizeObj -> resizeImage(194, 138, 'auto');

														//*** 3) Save image
														
														$target_small = $target_root."/small/";
														if(!is_dir($target_small)) {
															mkdir($target_small);	
														}
														$resizeObj -> saveImage($target_small.$newfilename, 75);
														
														$resizeObj -> resizeImage(194, 138, 'auto');

														//*** 3) Save image
														$target_medium = $target_root."/medium/";
														if(!is_dir($target_medium)) {
															mkdir($target_medium);	
														}
														$resizeObj -> saveImage($target_medium.$newfilename, 75);
														$resizeObj -> resizeImage(1024, 192, 'auto');

														//*** 3) Save image
														$target_medium = $target_root."/cover/";
														if(!is_dir($target_medium)) {
															mkdir($target_medium);	
														}
														$resizeObj -> saveImage($target_medium.$newfilename, 75);
														$photo_data['parent_album_id'] = $album_id;
														$photo_data['data_type'] = 'image';														
														$photo_data['data_content'] = $newfilename;
														$photo_id = $this->getAlbumDataTable()->addToAlbumData($photo_data);
														$planet_data['group_photo_id'] = $photo_id;
													}
												 }
											 }
											 if(!empty($planet_data)){
												$this->getGroupTable()->updateGroup($planet_data,$planet_id);
												$error_count = 0;
											 }
											 
										}
									break;
									default:
									echo "here";die();
								}
							}else{
								$error = 'Invalid access';
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
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function getActivityController(){
		if (!$this->ActivityController) {
            $sm = $this->getServiceLocator();
            $this->ActivityController = $sm->get('Activity\Controller\Activity');
        }
        return $this->ActivityController;
	}
	public function getActivityTable(){
        if (!$this->groupTagTable) {
            $sm = $this->getServiceLocator();
            $this->TagTable = $sm->get('Activity\Model\ActivityTable');
        }
        return $this->TagTable;
    }
	public function getGroupTagTable(){
        if (!$this->groupTagTable) {
            $sm = $this->getServiceLocator();
            $this->TagTable = $sm->get('Tag\Model\GroupTagTable');
        }
        return $this->TagTable;
    }
	public function getGroupTable(){
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    }
	public function getPhotoTable(){
        if (!$this->photoTable) {
            $sm = $this->getServiceLocator();
            $this->photoTable = $sm->get('Photo\Model\PhotoTable');
        }
        return $this->photoTable;
    } 
	public function getUserGroupTable(){
        if (!$this->userGroupTable) {
            $sm = $this->getServiceLocator();
			$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
        }
        return $this->userGroupTable;
    }
	public function getUserNotificationTable(){
        if (!$this->userNotificationTable) {
            $sm = $this->getServiceLocator();
            $this->userNotificationTable = $sm->get('Notification\Model\UserNotificationTable');
        }
        return $this->userNotificationTable;
    }
	public function getGroupSettingsTable(){
        if (!$this->groupSettings) {
            $sm = $this->getServiceLocator();
            $this->groupSettings = $sm->get('Groups\Model\GroupSettingsTable');
        }
        return $this->groupSettings;
    }
	public function ajaxLoadGalaxyAction(){
		$vm = new ViewModel();	
		$request   = $this->getRequest();
		$post = $request->getPost();		
		if ($request->isPost()){
			$page =$post->get('page');
			if(!$page)
			$page = 0;
		}
		$offset = $page*10;
		$galexies = $this->getGroupTable()->getGalexyWithUsers($offset,10);
		$vm->setVariable('galexies', $galexies);
		$vm->setTerminal($request->isXmlHttpRequest());
		return $vm;
	}
	public function getUserTable(){
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
			$this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
	public function getCountryTable(){
		 if (!$this->countryTable) {
            $sm = $this->getServiceLocator();
            $this->countryTable = $sm->get('Country\Model\CountryTable');
        }
        return $this->countryTable;
	}
	public function getAlbumTable(){
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
	public function getAlbumDataTable(){
        if (!$this->albumDataTable) {
            $sm = $this->getServiceLocator();
            $this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
        }
        return $this->albumDataTable;
    }
	public function getGroupfunctionTable(){
        if (!$this->groupfunctionTable) {
            $sm = $this->getServiceLocator();
            $this->groupfunctionTable = $sm->get('Groupfunction\Model\GroupfunctionTable');
        }
        return $this->groupfunctionTable;
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
	public function getUserGroupPermissionsTable(){
        if (!$this->grouppermissionsTable) {
            $sm = $this->getServiceLocator();
            $this->grouppermissionsTable = $sm->get('Groups\Model\UserGroupPermissionsTable');
        }
        return $this->grouppermissionsTable;
    }
	public function getTagTable(){
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Tag\Model\TagTable');
        }
        return $this->tagTable;
    }
	public function getActivityRsvpTable(){
        if (!$this->activityRsvpTable) {
            $sm = $this->getServiceLocator();
            $this->activityRsvpTable = $sm->get('Activity\Model\ActivityRsvpTable');
        }
        return $this->activityRsvpTable;
    }
	public function ajaxMembersListAction(){  
		$auth = new AuthenticationService();
		$error =array();	
		$members = array();
		$existing_roles = array();
		$request = $this->getRequest();		
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
					if ($request->isPost()) {
						$post = $request->getPost();
						$role = $post['role'];
						if($role!=''){
						$existing_roles = $this->getGroupTable()->getAllExistingRolesWithoutAdmin($planet_id,$role,$identity->user_id);
						$members = $this->getGroupTable()->getAllGroupMembersWithoutAdmin($planet_id,$identity->user_id);
						}
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
				$error[] =  'Invalid access';				 
				}
			}else{
				$error[] =  'Invalid access';				 
			}	 
		}else{
			$error[] = 'Your session expired';			 
		 }
		 $vm = new ViewModel();	
		 $vm->setVariable('error', $error);
		 $vm->setVariable('members', $members);
		 $vm->setVariable('existing_roles', $existing_roles);
		 $vm->setTerminal($request->isXmlHttpRequest());
		 return $vm;
	}
	public function ajaxAddRolesAction(){	
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
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$users = $post['users'];
								$role = $post['roles'];
								foreach($users as $user)
								{
									if($this->getGroupTable()->is_member($planet_id,$user)){  
										$insert_id[] = $this->getGroupTable()->AddRoles($planet_id,$user,$role); 
										if(!empty($insert_id)){
											 
											 $this->getGroupTable()->removeOldRoles($planet_id,$insert_id,$role);
										}
									}else{
										$error = 'Unknown users';
										$error_count++;
									}
								}
							}else{
								$error = 'Invalid access';
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
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
			
	}
	public function ajaxPermissionsAction(){
		$auth = new AuthenticationService();
		$error =array();	
		$group_role_permissions = array();
		$group_functions = array();
		$request = $this->getRequest();		
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
					if ($request->isPost()) {
						$post = $request->getPost();
						$role = $post['role'];
						if($role!=''){
							$group_functions =  $this->getGroupfunctionTable()->fetchAll();
							$group_role_permissions = $this->getGroupTable()->getAllPermissionsOfRoles($planet_id,$role);
						}
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
				$error[] =  'Invalid access';				 
				}
			}else{
				$error[] =  'Invalid access';				 
			}	 
		}else{
			$error[] = 'Your session expired';			 
		 }
		 $vm = new ViewModel();	
		 $vm->setVariable('error', $error);
		 $vm->setVariable('group_functions', $group_functions);
		 $vm->setVariable('group_role_permissions', $group_role_permissions);
		 $vm->setTerminal($request->isXmlHttpRequest());
		 return $vm;
	}
	public function ajaxSavePermissionsAction(){
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
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$functions = $post['functions'];
								if(!empty($functions)){
									$role = $post['role'];
									foreach($functions as $function)
									{
										 $insert_id[] = $this->getUserGroupPermissionsTable()->savePermissionsIfNotExist($planet_id,$role,$function);
										 $error_count = 0;
										 if(!empty($insert_id))
											$this->getUserGroupPermissionsTable()->removeSelected($planet_id,$role,$insert_id);
									}
								}else{
									$this->getUserGroupPermissionsTable()->removeAllPermissions($planet_id,$role);
								}
							}else{
								$error = 'Invalid access';
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
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function ajaxDiscussionSettingsAction(){
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
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$settings = $post['settings'];
								if(!empty($settings)){
									 $group_settings = $this->getGroupSettingsTable()->saveSettings($planet_id,$settings,'group_discussion_settings');
								} 
							}else{
								$error = 'Invalid access';
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
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function ajaxActivitySettingsAction(){
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
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$settings = $post['settings'];
								if(!empty($settings)){
									 $group_settings = $this->getGroupSettingsTable()->saveSettings($planet_id,$settings,'group_activity_settings');
								} 
							}else{
								$error = 'Invalid access';
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
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}		
	public function ajaxMemberSettingsAction(){
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
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$settings = $post['settings'];
								if(!empty($settings)){
									 $group_settings = $this->getGroupSettingsTable()->saveSettings($planet_id,$settings,'group_member_join_type');
								} 
								$email_content = $post['email_content'];
								if(!empty($email_content)){
									 $group_settings = $this->getGroupSettingsTable()->saveSettings($planet_id,$email_content,'group_welcome_email');
								}
								$questionnaire = $post['questionnaire'];
								if($questionnaire){
									$group_settings = $this->getGroupSettingsTable()->saveSettings($planet_id,'Allow','group_joining_questionnaire');
								}else{
									$group_settings = $this->getGroupSettingsTable()->saveSettings($planet_id,'Notallow','group_joining_questionnaire');
								}
							}else{
								$error = 'Invalid access';
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
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function ajaxPrivacySettingsAction(){
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
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$settings = $post['settings'];
								if(!empty($settings)){
									 $group_settings = $this->getGroupSettingsTable()->saveSettings($planet_id,$settings,'group_privacy_settings');
								} 
								 
							}else{
								$error = 'Invalid access';
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
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function ajaxSaveGroupTagsAction(){
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
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$tags = $post['tags'];
								if(!empty($tags)){
									
									foreach($tags as $value){
										$inserted_tag = $this->getGroupTagTable()->saveTags($planet_id,$value);
									}
									$arr_failed_tags = array();
									$arr_failed_tags  = $this->getGroupTagTable()->RemoveGroupUnusedTags($planet_id,$tags);
									$existing_tags = array();
									if(!empty($arr_failed_tags)){
										$existing_tags = $this->getTagTable()->getTagDetails($arr_failed_tags);
										$content = 'Tags ';
										foreach($existing_tags as $tags){
											$content.= $tags->tag_title." , ";
										}
										$content.= "are already exists";
										$error = $content;
										$error_count++;
									}
								} 
								else{
									$error = 'Atleast one tag required';
									$error_count++;
								}
							}else{
								$error = 'Invalid access';
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
	public function ajaxVisibilitySettingsAction(){
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
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$visibility = $post['settings'];
								$this->getGroupTable()->changeGroupsStatus($planet_id,$visibility);
								 
							}else{
								$error = 'Invalid access';
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
	public function ajaxGroupTagsAction(){
		$request = $this->getRequest();
		$page = $request->getPost('page');
		$tag_string = $request->getPost('search_string');
		$return_string = '';
		$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
		$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
		if($group_seo!=''&&$planet_seo!=''){	
			$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
			$group_id = $groupdetails->group_id;
			$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
			$planet_id = $planetdetails->group_id;
			if($group_id && $planet_id){
				if($page>0){
					$page_limit = 10+($page-1);
					$selectAllTags = $this->getGroupTagTable()->fetchAllTagsOfGroup($planet_id,1,$page_limit,$tag_string); 
					foreach($selectAllTags as $tags){
						$return_string .= '<a href="javascript:void(0)" class="add-option"  id="'.$tags->tag_id.'" >'.$tags->tag_title.'</a>';
					}			
				}
			}
		}
		echo $return_string;die();
	}
	public function ajaxGroupTagSearchAction(){
		$request = $this->getRequest();
		$page = $request->getPost('page');
		$tag_string = $request->getPost('search_string');
		$return_string = '';
		$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
		$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
		if($group_seo!=''&&$planet_seo!=''){	
			$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
			$group_id = $groupdetails->group_id;
			$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
			$planet_id = $planetdetails->group_id;
			if($group_id && $planet_id){				
				$selectAllTags = $this->getGroupTagTable()->fetchAllTagsOfGroup($planet_id,10,0,$tag_string); 
				foreach($selectAllTags as $tags){
					$return_string .= '<a href="javascript:void(0)" class="add-option"  id="'.$tags->tag_id.'" >'.$tags->tag_title.'</a>';
				}				
			}
		}	 
		echo $return_string;die();
	}
	public function ajaxAllMembersExepctLoggedOneAction(){	
		$auth = new AuthenticationService();
		$error =array();	
		$members = array();		 
		$request = $this->getRequest();		
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
					if ($request->isPost()) {						 
						$members = $this->getGroupTable()->getAllGroupMembersWithoutAdmin($planet_id,$identity->user_id);						 
					}else{
						$error = 'Invalid access';
						$error_count++;
					}
				}else{
				$error[] =  'Invalid access';				 
				}
			}else{
				$error[] =  'Invalid access';				 
			}	 
		}else{
			$error[] = 'Your session expired';			 
		 }
		 $vm = new ViewModel();	
		 $vm->setVariable('error', $error);
		 $vm->setVariable('members', $members);		 
		 $vm->setTerminal($request->isXmlHttpRequest());
		 return $vm;die();
	}
	public function getUserGroupJoiningRequestTable(){
        if (!$this->groupjoiningrequestTable) {
            $sm = $this->getServiceLocator();
            $this->groupjoiningrequestTable = $sm->get('Groups\Model\UserGroupJoiningRequestTable');
        }
        return $this->groupjoiningrequestTable;
    }
	public function groupTopAction(){
		$auth = new AuthenticationService();	
		$viewModel = new ViewModel();
		if ($auth->hasIdentity()) {
			//$this->layout('layout/planet_home');
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id'); 
			if($group_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
					$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
					$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
					$viewModel->setVariable('group_settings', $group_settings);
					if($admin_status->is_admin){
						$role_data = $this->getGroupTable()->getPlanetRoleDetails($planet_id);
						foreach($role_data as $data){
							$roles_array[$data->group_roles_name][] = array('user_given_name'=>$data->user_given_name,'data_content'=>$data->profile_photo,'user_profile_name'=>$data->user_profile_name,'user_register_type'=>$data->user_register_type,'user_fbid'=>$data->user_fbid,'user_id'=>$data->user_id);
						}
						$viewModel->setVariable('role_data',$roles_array);
						$group_functions  = array();
						$group_functions =  $this->getGroupfunctionTable()->fetchAll();
						$viewModel->setVariable('group_functions',$group_functions);
						$group_role_permissions = $this->getGroupTable()->getAllRolesWithPermissions($planet_id);
						$viewModel->setVariable('group_role_permissions', $group_role_permissions);
						
						$groupTag =	$this->getGroupTagTable()->fetchAllTagsOfGroup($planet_id);
						$viewModel->setVariable('groupTag', $groupTag);
						$selectAllTags = $this->getTagTable()->getPopularUserTags(0,12);
						$viewModel->setVariable('popularTags', $selectAllTags);
					}
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
								$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
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
					
					$viewModel->setVariable('activity_permission', $activity_permission);
					$questionnaire = $this->getGroupJoiningQuestionnaireTable()->getQuestionnaire($planet_id);
					$viewModel->setVariable('questionnaire', $questionnaire);
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
					 $approvals = array();
					 
					 if($member_approval){
						$memberRequests =$this->getUserGroupJoiningRequestTable()->getUserRequests($planet_id);
						$approvals[] = array("type"=>"Member Request",
									"memberRequests"=>$memberRequests
									);
					 }
					 if($activity_approval){
						$activityRequests =$this->getActivityTable()->getActivityRequests($planet_id);
						$approvals[] = array("type"=>"Activity Request",
									"activityRequests"=>$activityRequests
									);
					 }
					$viewModel->setVariable('approvals', $approvals);	
					$viewModel->setVariable('planetdetails', $planet_data);					 
					return $viewModel;
				}
				else{
					return $this->redirect()->toRoute('home', array('action' => 'index'));
				}			
			}
			else{
				return $this->redirect()->toRoute('home', array('action' => 'index'));
			}
		}
		else{	
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		 $vm = new ViewModel();
		 return $vm;		 
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
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					 
					$viewModel->setVariable('user_id', $identity->user_id);	
					               
					$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
					 
					$search_string='';
					$post = $request->getPost();		
					if ($request->isPost()){
						$page =$post->get('page');
						if(!$page)
						$page = 0;
						$search_string=$post->get('search_string');
					}
					$offset = $page*25;
					
					$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);
					$member_delete_permission = 0;	
					foreach($permissions as $row){
						if($row->function_id == 4){
							$member_delete_permission = 1;	
						}
					}
					$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
					$viewModel->setVariable('group_settings', $group_settings);
					$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
					if($admin_status->is_admin){
						$member_delete_permission = 1;
					}
					if($member_delete_permission)
					$groupUsersList = $this->userGroupTable->fetchAllUserListForGroup($planet_id,$identity->user_id,$offset,25,$search_string);
					else
					$groupUsersList = $this->userGroupTable->fetchAllActiveUserListForGroup($planet_id,$identity->user_id,$offset,25,$search_string);
					$viewModel->setVariable('groupUsersList', $groupUsersList); 
					$viewModel->setVariable('member_delete_permission', $member_delete_permission);
				}else{
					$error[] = "The groups you are requested is not existing in this system";
				}
			}else{
				$error[] = "The groups you are requested is not existing in this system";
			}
		}else{
			$error[] = "Your session has to be expired";
		}
		$viewModel->setVariable('error', $error);	
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
		die();
	}
	public function quitGroupAction(){
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
						 if($this->getGroupTable()->is_member($planet_id,$identity->user_id)){
							$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
							if(!$admin_status->is_admin){
								$this->getUserGroupJoiningRequestTable()->RemoveRequest($identity->user_id,$planet_id);
								if($this->getUserGroupTable()->QuitGroup($identity->user_id,$planet_id)){
									$error = 'Successfully quit this group';
									$error_count=0;
								}else{
									$error = 'Some error occured. Please try again later';
									$error_count++;
								}
							}else{
								$error = 'You are the group admin. You can\'t quit this group';
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
	public function ajaxRemoveUserAction(){
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
							if($admin_status->is_admin){
								$delete_permission = 1;							
							}
							$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);							 
							foreach($permissions as $row){
								if($row->function_id == 4){
									$delete_permission = 1;	
								}
							}
							if($delete_permission){
								$user_status = $this->getGroupTable()->getAdminStatus($planet_id,$post['user']);
								if(!$user_status->is_admin){
									$this->getUserGroupJoiningRequestTable()->RemoveRequest($post['user'],$planet_id);
									if($this->getUserGroupTable()->QuitGroup($post['user'],$planet_id)){
										$error = 'Successfully quit this group';
										$error_count=0;
									}else{
										$error = 'Some error occured. Please try again later';
										$error_count++;
									}
								}
								else{
									$error = 'This user is special of this group. You can\'t delete this user';
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
	public function ajaxSuspendUserAction(){
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
							if($admin_status->is_admin){
								$delete_permission = 1;							
							}
							$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);							 
							foreach($permissions as $row){
								if($row->function_id == 4){
									$delete_permission = 1;	
								}
							}
							if($delete_permission){
								$user_status = $this->getGroupTable()->getAdminStatus($planet_id,$post['user']);
								if(!$user_status->is_admin){
									 
									if($this->getUserGroupTable()->suspendUser($post['user'],$planet_id)){
										$UserGroupNotificationData = array();						
										$UserGroupNotificationData['user_notification_user_id'] = $post['user'];							
										$UserGroupNotificationData['user_notification_content'] = $identity->user_given_name." Suspend your membership from the group ".$planetdetails->group_title;								 
										$userObject = new user(array());
										$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
										$UserGroupNotificationData['user_notification_status'] = 0;									
										$UserGroupNotificationSaveObject = new UserNotification();
										$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
										$insertedUserGroupNotificationId ="";
										$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
										$error = 'Successfully quit this group';
										$error_count=0;
									}else{
										$error = 'Some error occured. Please try again later';
										$error_count++;
									}
								}
								else{
									$error = 'This user is special of this group. You can\'t delete this user';
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
	public function ajaxRemoveSuspensionAction(){
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
							if($admin_status->is_admin){
								$delete_permission = 1;							
							}
							$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);							 
							foreach($permissions as $row){
								if($row->function_id == 4){
									$delete_permission = 1;	
								}
							}
							if($delete_permission){
								$user_status = $this->getGroupTable()->getAdminStatus($planet_id,$post['user']);
								if(!$user_status->is_admin){
									 
									if($this->getUserGroupTable()->RemoveSuspenssion($post['user'],$planet_id)){
										$UserGroupNotificationData = array();						
										$UserGroupNotificationData['user_notification_user_id'] = $post['user'];							
										$UserGroupNotificationData['user_notification_content'] = $identity->user_given_name." Remove your suspension status over the group ".$planetdetails->group_title;								 
										$userObject = new user(array());
										$UserGroupNotificationData['user_notification_notification_type_id'] = "2";
										$UserGroupNotificationData['user_notification_status'] = 0;									
										$UserGroupNotificationSaveObject = new UserNotification();
										$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
										$insertedUserGroupNotificationId ="";
										$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
										$error = 'Successfully quit this group';
										$error_count=0;
									}else{
										$error = 'Some error occured. Please try again later';
										$error_count++;
									}
								}
								else{
									$error = 'This user is special of this group. You can\'t delete this user';
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
	public function approveMembersAction(){
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
							if($member_approval){
								 $user = $post['user'];
								 if($user){
									$checkRequestExist =$this->getUserGroupJoiningRequestTable()->checkRequestExist($planet_id,$user); 
									if(!empty($checkRequestExist)&&$checkRequestExist->user_group_joining_request_user_id!=''){
										 if($this->getGroupTable()->is_member($planet_id,$user)){
											$error = 'You are already member of this system';
											$error_count++;
											$this->getUserGroupJoiningRequestTable()->ChangeStatusTOProcessed($planet_id,$user);
										 }else{
											$UserGroup = new UserGroup();
											$userGroupData['user_group_user_id'] = $user;
											$userGroupData['user_group_group_id'] = $planet_id;	
											$objUser = new User();
											$userGroupData['user_group_added_ip_address'] = $objUser->getUserIp();
											$userGroupData['user_group_status'] = "1";
											$userGroupData['user_group_is_owner'] = "0";
											$userGroup = new UserGroup();
											$userGroup->exchangeArray($userGroupData);
											$insertedUserGroupId ="";
											$insertedUserGroupId = $this->getUserGroupTable()->saveUserGroup($userGroup);
											if(isset($insertedUserGroupId) && !empty($insertedUserGroupId)){									 
												$this->getUserGroupJoiningRequestTable()->ChangeStatusTOProcessed($planet_id,$user);
												$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
												$config = $this->getServiceLocator()->get('Config');
												$base_url = $config['pathInfo']['base_url'];												 
												$msg = '<a href="'.$base_url.'groups/'.$subGroupData->parent_seo_title.'/'.$subGroupData->group_seo_title.'">'.$identity->user_given_name." approved your join request to the planet ".$subGroupData->group_title."</a>";
												$subject = 'Member request approved';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($user,$msg,2,$subject,$from);													 									 
												$error = 'Successfully updated';
												$error_count = 0;												
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}
										} 
									}else{
										$error = 'user is not exist in this system';
										$error_count++;
									}
								 }else{
									$error = 'user is not exist in this system';
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
	public function IgnoreMembersAction(){
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
							if($member_approval){
								 $user = $post['user'];
								 if($user){
									$checkRequestExist =$this->getUserGroupJoiningRequestTable()->checkRequestExist($planet_id,$user); 
									if(!empty($checkRequestExist)&&$checkRequestExist->user_group_joining_request_user_id!=''){
										 if($this->getGroupTable()->is_member($planet_id,$user)){
											$error = 'You are already member of this system';
											$error_count++;
											$this->getUserGroupJoiningRequestTable()->ChangeStatusTOProcessed($planet_id,$user);
										 }else{
											if($this->getUserGroupJoiningRequestTable()->ChangeStatusTOIgnored($planet_id,$user)){
												$error = 'Successfully updated';
												$error_count = 0;
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}
											
										} 
									}else{
										$error = 'user is not exist in this system';
										$error_count++;
									}
								 }else{
									$error = 'user is not exist in this system';
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
	public function RemoveMemberRequestAction(){
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
							if($member_approval){
								 $user = $post['user'];
								 if($user){
									$checkRequestExist =$this->getUserGroupJoiningRequestTable()->checkRequestExist($planet_id,$user); 
									if(!empty($checkRequestExist)&&$checkRequestExist->user_group_joining_request_user_id!=''){
										 if($this->getGroupTable()->is_member($planet_id,$user)){
											$error = 'You are already member of this system';
											$error_count++;
											$this->getUserGroupJoiningRequestTable()->ChangeStatusTOProcessed($planet_id,$user);
										 }else{
											if($this->getUserGroupJoiningRequestTable()->ChangeStatusTORemoved($planet_id,$user)){
												$error = 'Successfully updated';
												$error_count = 0;
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}
											
										} 
									}else{
										$error = 'user is not exist in this system';
										$error_count++;
									}
								 }else{
									$error = 'user is not exist in this system';
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
	public function ajaxmemberSearchAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					 
					$viewModel->setVariable('user_id', $identity->user_id);	
					               
					$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
							
					$permissions = $this->getUserGroupPermissionsTable()->getUserPermissions($identity->user_id,$planet_id);
					$member_delete_permission = 0;	
					foreach($permissions as $row){
						if($row->function_id == 4){
							$member_delete_permission = 1;	
						}
					}
					$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
					$viewModel->setVariable('group_settings', $group_settings);
					$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
					if($admin_status->is_admin){
						$member_delete_permission = 1;
					}
					$offset = 0;
					$search_string = '';
					$post = $request->getPost();
					$search_string = $post['search_string'];
					if($member_delete_permission)
					$groupUsersList = $this->userGroupTable->fetchAllUserListForGroup($planet_id,$identity->user_id,$offset,25,$search_string);
					else
					$groupUsersList = $this->userGroupTable->fetchAllActiveUserListForGroup($planet_id,$identity->user_id,$offset,25,$search_string);
					$viewModel->setVariable('groupUsersList', $groupUsersList); 
					$viewModel->setVariable('member_delete_permission', $member_delete_permission);
				}else{
					$error[] = "The groups you are requested is not existing in this system";
				}
			}else{
				$error[] = "The groups you are requested is not existing in this system";
			}
		}else{
			$error[] = "Your session has to be expired";
		}
		$viewModel->setTemplate('groups/groups/ajax-load-more-members');
		$viewModel->setVariable('error', $error);	
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
		die();
	}
	public function planetSuggestionsAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$arr_planetSuggestion = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$arr_planetSuggestion = $this->getGroupTable()->getPlanetSuggestions($identity->user_id,5,0);
		}
		$viewModel->setVariable('planetSuggestions', $arr_planetSuggestion);	
		return $viewModel;
	}
	public function checkPlanetExistAction(){
		$request   = $this->getRequest();
		$post = $request->getPost();
		$result_array = array();
		if(!empty($post)&&$post['planet_name']!=''){
			if($this->getGroupTable()->checkPlanetExist($post['planet_name'])){
				$result_array['error'] = 1;
			}else{
				$result_array['error'] = 0;
			}
		}else{
			$result_array['error'] = 1;
		}
		echo json_encode($result_array);die();
	}
	public function createPlanetAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = '';
		$error_count = 0;
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();	
		$return_array = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$tags = $post['tags'];
				if($post['title']==''){
					$error = 'Group name required';
					$error_count++;
				}				
				if($post['galaxy']==''){
					$error = 'select one galexy';
					$error_count++;
				}
				if($this->getGroupTable()->checkPlanetExist($post['title'])){
					$error = 'Planet name already exist';
					$error_count++;
				}
				if($tags==''){
					$error = 'Select one Tag';
					$error_count++;
				}
				if(isset($post['chckQuestionnaire'])&&$post['chckQuestionnaire']){
					if($post['question1']==''){
						$error = 'Three Questions are required';
						$error_count++;
					} 
					if($post['question2']==''){
						$error = 'Three Questions are required';
						$error_count++;
					} 
					if($post['question3']==''){
						$error = 'Three Questions are required';
						$error_count++;
					}
					if($post['q1_answer_type']=='radio'||$post['q1_answer_type']=='checkbox'){
						if(($post['q1_option_1']==''&&$post['q1_option_2']=='')||($post['q1_option_1']==''&&$post['q1_option_3']=='')||($post['q1_option_2']==''&&$post['q1_option_3']=='')){
							$error = 'Add Atleast two options';
							$error_count++;
						}
					}
					if($post['q2_answer_type']=='radio'||$post['q2_answer_type']=='checkbox'){
						if(($post['q2_option_1']==''&&$post['q2_option_2']=='')||($post['q2_option_1']==''&&$post['q2_option_3']=='')||($post['q2_option_2']==''&&$post['q2_option_3']=='')){
							$error = 'Add Atleast two options';
							$error_count++;
						}
					}
					if($post['q3_answer_type']=='radio'||$post['q3_answer_type']=='checkbox'){
						if(($post['q3_option_1']==''&&$post['q3_option_2']=='')||($post['q3_option_1']==''&&$post['q3_option_3']=='')||($post['q3_option_2']==''&&$post['q3_option_3']=='')){
							$error = 'Add Atleast two options';
							$error_count++;
						}
					}
				}				
				if($error_count==0){
					$data['group_title'] = $post['title'];
					$data['group_seo_title'] = $this->creatSeotitle($post['title']);
					$data['group_status'] = 1;
					$data['group_discription'] = $post['description'];
					$data['group_parent_group_id'] = $post['galaxy'];
					$data['group_location'] = $post['location'];
					$data['group_city_id'] = $post['city'];
					$data['group_country_id'] = $post['country'];
					$data['y2m_group_location_lat'] = $post['geo_latitude'];
					$data['y2m_group_location_lng'] = $post['geo_longitude'];
					$data['group_web_address'] = $post['webaddress'];
					$data['group_welcome_message_members'] = $post['welcome_message'];
					$objUser = new User();
					$data['group_added_ip_address'] = $objUser->getUserIp();
					$insert_id = $this->getGroupTable()->createPlanet($data);
					if($insert_id){
						$user_data['user_group_user_id'] = $identity->user_id;
						$user_data['user_group_group_id'] = $insert_id;
						$user_data['user_group_status'] = 1;
						$user_data['user_group_is_owner'] = 1;
						$user_data['user_group_role'] = 1;
						$objUser = new User();
						$user_data['user_group_added_ip_address'] = $objUser->getUserIp();
						$this->getUserGroupTable()->AddMembersTOGroup($user_data);
						
						$arr_tags = array();
						$arr_tags = explode(",",$tags);
						foreach($arr_tags as $tag_id){
							$tag_data['group_tag_group_id'] =  $insert_id;
							$tag_data['group_tag_tag_id'] =  $tag_id;
							$objUser = new User();
							$tag_data['group_tag_added_ip_address'] = $objUser->getUserIp();
							$this->getGroupTagTable()->AddGroupTags($tag_data);
						}
						$join_questionnaire = "Notallow";
						if(isset($post['chckQuestionnaire'])){
							if($post['chckQuestionnaire']==1){
								$join_questionnaire = "Allow";
							}else{
								$join_questionnaire = "Notallow";
							}							
						}
						$this->getGroupSettingsTable()->saveSettings($insert_id,$join_questionnaire,'group_joining_questionnaire');
						if($join_questionnaire == "Allow"){							
								$questionnaire_data = array();
								$questionnaire_data['group_id'] = $insert_id;								
								$questionnaire_data['question_status'] = 1;
								$objUser = new User();
								$questionnaire_data['added_ip'] = $objUser->getUserIp();
								$questionnaire_data['modified_timestamp'] = date("Y-m-d H:m:s");
								$questionnaire_data['modified_ip'] = $objUser->getUserIp();
								$questionnaire_data['added_user_id'] = $identity->user_id;
								$questionnaire_data['modified_user_id'] = $identity->user_id;
								if($post['question1']!=''){
									$questionnaire_data['question'] = $post['question1'];
									$questionnaire_data['answer_type'] = $post['q1_answer_type'];
									$insert_id = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($questionnaire_data);
									if($post['q1_answer_type']=='radio'||$post['q1_answer_type']=='checkbox'){
										$option_data = array();
										$option_data['question_id'] = $insert_id;
										if($post['q1_option_1']!=''){
											$option_data['option'] = $post['q1_option_1'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
										if($post['q1_option_2']!=''){
											$option_data['option'] = $post['q1_option_2'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
										if($post['q1_option_3']!=''){
											$option_data['option'] = $post['q1_option_3'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
									}
								}
								if($post['question2']!=''){
									$questionnaire_data['question'] = $post['question2'];
									$questionnaire_data['answer_type'] = $post['q2_answer_type'];
									$insert_id = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($questionnaire_data);
									if($post['q2_answer_type']=='radio'||$post['q2_answer_type']=='checkbox'){
										$option_data = array();
										$option_data['question_id'] = $insert_id;
										if($post['q2_option_1']!=''){
											$option_data['option'] = $post['q2_option_1'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
										if($post['q2_option_2']!=''){
											$option_data['option'] = $post['q2_option_2'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
										if($post['q2_option_3']!=''){
											$option_data['option'] = $post['q2_option_3'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
									}
								}
								if($post['question3']!=''){
									$questionnaire_data['question'] = $post['question3'];
									$questionnaire_data['answer_type'] = $post['q3_answer_type'];
									$insert_id = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($questionnaire_data);
									if($post['q3_answer_type']=='radio'||$post['q3_answer_type']=='checkbox'){
										$option_data = array();
										$option_data['question_id'] = $insert_id;
										if($post['q3_option_1']!=''){
											$option_data['option'] = $post['q3_option_1'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
										if($post['q3_option_2']!=''){
											$option_data['option'] = $post['q3_option_2'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
										if($post['q3_option_3']!=''){
											$option_data['option'] = $post['q3_option_3'];
											$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
										}
									}
								}
						}
						if(isset($_FILES)&&!empty($_FILES)){ 
							$album_id =0;											 
							$config = $this->getServiceLocator()->get('Config'); 
							$output_dir = $config['pathInfo']['AlbumUploadPath']; 
							$base_url = $config['pathInfo']['base_url'];
							$imagesizedata = getimagesize($_FILES[0]["tmp_name"]); 
							if ($imagesizedata === FALSE)
							{
								$error = 'Image size is not valid';
								$error_count++;
							}else{  
								if ($_FILES[0]["error"] > 0){
									$error = $_FILES[0]["error"];
									$error_count++;
								}
								else{  
									$newfilename = time().$_FILES[0]["name"];
									$target = $output_dir.$insert_id;
									if(!is_dir($target)) {
										mkdir($target);	
									}
									$target = $output_dir.$insert_id ."/main/";
									$target_root = $output_dir.$insert_id;
									if(!is_dir($target)) {
										mkdir($target);	
									}
									$output_dir = $target. $newfilename; 
									move_uploaded_file($_FILES[0]["tmp_name"],$output_dir);
									$resizeObj = $this->ResizePlugin();
									$image_path = $base_url.'/public/album/'.$insert_id.'/main/'.$newfilename; 
									$resizeObj->assignImage($image_path);

									//*** 2) Resize image (options: exact, portrait, landscape, auto, crop)
									$resizeObj -> resizeImage(403, 138, 'auto');

									//*** 3) Save image
									
									$target_small = $target_root."/small/";
									if(!is_dir($target_small)) {
										mkdir($target_small);	
									}
									$resizeObj -> saveImage($target_small.$newfilename, 75);
									
									$resizeObj -> resizeImage(403, 138, 'auto');

									//*** 3) Save image
									$target_medium = $target_root."/medium/";
									if(!is_dir($target_medium)) {
										mkdir($target_medium);	
									}
									$resizeObj -> saveImage($target_medium.$newfilename, 75);
									$resizeObj -> resizeImage(1024, 192, 'auto');

									//*** 3) Save image
									$target_medium = $target_root."/cover/";
									if(!is_dir($target_medium)) {
										mkdir($target_medium);	
									}
									$resizeObj -> saveImage($target_medium.$newfilename, 75);
									$photo_data['parent_album_id'] = $album_id;
									$photo_data['added_user_id'] = $identity->user_id;
									$photo_data['data_type'] = 'image';														
									$photo_data['data_content'] = $newfilename;
									$photo_id = $this->getAlbumDataTable()->addToAlbumData($photo_data);
									$planet_data['group_photo_id'] = $photo_id;
									$this->getGroupTable()->updateGroup($planet_data,$insert_id);
									$error_count = 0;
								}
							}
						}
					}else{
						$error = 'Some error occured';
						$error_count++;
					}
				}
			}else{
				$error = 'Invalid access';
				$error_count++;
			}
		}else{
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
	public function creatSeotitle($planet_name){
		$string = trim($planet_name);		
		$string = preg_replace('/(\W\B)/', '',  $string);		
		$string = preg_replace('/[\W]+/',  '_', $string);		
		$string = str_replace('-', '_', $string);
		if(!$this->checkSeotitleExist($string)){
			return $string; 
		}
		$length = 5;
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$string = strtolower($string).'_'.$randomString;
		if(!$this->checkSeotitleExist($string)){
			return $string; 
		}		
		$string = strtolower($string).'_'.time();
		return $string; 
	}
	public function checkSeotitleExist($seo_title){		 
		if($this->getGroupTable()->checkSeotitleExist($seo_title)){
			return true;				
		}
		else{
			return false;
		}
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
	public function getGroupJoiningQuestionnaireTable(){
        if (!$this->groupJoiningQuestionnaireTable) {
            $sm = $this->getServiceLocator();
			$this->groupJoiningQuestionnaireTable = $sm->get('Groups\Model\GroupJoiningQuestionnaireTable');
        }
        return $this->groupJoiningQuestionnaireTable;
    }
	public function updateQuestionAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = '';
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$error_count = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$question_id = $post['question_id'];
								$question = $post['question'];
								$answer_type = $post['answer_type'];
								$option1 = $post['option1'];
								$option2 = $post['option2'];
								$option3 = $post['option3'];
								if($answer_type=='radio'||$answer_type=='checkbox'){
									if(($option1 ==''&&$option2=='')||($option1==''&&$option3=='')||($option2 ==''&&$option3 =='')){
										$error = 'Add Atleast two options';
										$error_count++;
									}
								}
								if($question_id!='' && $question!=''){
									if($error_count==0){
										$question_old = $this->getGroupJoiningQuestionnaireTable()->getQuestionFromQuestionId($question_id);
										if($question_old->questionnaire_id!=''){
											$quesdtion_data['question'] = $question;										
											$quesdtion_data['modified_timestamp'] = date("Y-m-d H:m:s");
											$objUser = new User();
											$quesdtion_data['modified_ip'] = $objUser->getUserIp();								 
											$quesdtion_data['modified_user_id'] = $identity->user_id;
											$quesdtion_data['answer_type'] = $answer_type;
											if($this->getGroupJoiningQuestionnaireTable()->updateQuestion($quesdtion_data,$question_id)){
												$arr_options = array();
												$arr_options[0] = $post['option1'];
												$arr_options[1] = $post['option2'];
												$arr_options[2] = $post['option3'];
												$old_options = $this->getGroupQuestionnaireOptionTable()->getoptionOfOneQuestion($question_id);
												if($answer_type=='radio'||$answer_type=='checkbox'){
													$i=0;
													foreach($old_options as $options){
														$this->getGroupQuestionnaireOptionTable()->UpdateOptions($arr_options[$i],$options->option_id);
														$i++;
													}
													if($i==0){
														$data_options = array();
														$data_options['question_id'] = $question_id;
														if($arr_options[0]!=''){
															$data_options['option'] = $arr_options[0];
															$this->getGroupQuestionnaireOptionTable()->AddOptions($data_options);
														}
														if($arr_options[1]!=''){
															$data_options['option'] = $arr_options[0];
															$this->getGroupQuestionnaireOptionTable()->AddOptions($data_options);
														}
														if($arr_options[2]!=''){
															$data_options['option'] = $arr_options[0];
															$this->getGroupQuestionnaireOptionTable()->AddOptions($data_options);
														}
													}
													if($i==1){
														$data_options = array();
														$data_options['question_id'] = $question_id;														 
														if($arr_options[2]!=''){
															$data_options['option'] = $arr_options[0];
															$this->getGroupQuestionnaireOptionTable()->AddOptions($data_options);
														}
													}
												}else{
													$this->getGroupQuestionnaireOptionTable()->DeleteOptions($question_id);
												}
												$error = '';
												$error_count=0;
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}
										}else{	
											$error = 'This question is not existing in this system';
											$error_count++;
										}
									}
								}else{ 
									$error = 'Question field couldnot be empty';
									$error_count++;
								}
							}else{
								$error = 'Invalid access';
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
					$error = "The groups you are requested is not existing in this system";
					$error_count++;
				}
			}else{
				$error = "The groups you are requested is not existing in this system";
				$error_count++;
			}
		}else{
			$error = 'Your session has to be expired';
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
	public function AddQuestionAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$error_count= 0;
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$question1 = $post['question1'];
								$question2 = $post['question2'];
								$question3 = $post['question3'];
								if($question1!=''&&$question2!=''&&$question3!=''){
									if($post['q1_answer_type']=='radio'||$post['q1_answer_type']=='checkbox'){
										if(($post['q1_option_1']==''&&$post['q1_option_2']=='')||($post['q1_option_1']==''&&$post['q1_option_3']=='')||($post['q1_option_2']==''&&$post['q1_option_3']=='')){
											$error = 'Add Atleast two options';
											$error_count++;
										}
									}
									if($post['q2_answer_type']=='radio'||$post['q2_answer_type']=='checkbox'){
										if(($post['q2_option_1']==''&&$post['q2_option_2']=='')||($post['q2_option_1']==''&&$post['q2_option_3']=='')||($post['q2_option_2']==''&&$post['q2_option_3']=='')){
											$error = 'Add Atleast two options';
											$error_count++;
										}
									}
									if($post['q3_answer_type']=='radio'||$post['q3_answer_type']=='checkbox'){
										if(($post['q3_option_1']==''&&$post['q3_option_2']=='')||($post['q3_option_1']==''&&$post['q3_option_3']=='')||($post['q3_option_2']==''&&$post['q3_option_3']=='')){
											$error = 'Add Atleast two options';
											$error_count++;
										}
									}
									if($error_count==0){										
										$quesdtion_data['group_id'] = $planet_id;												
										$quesdtion_data['question_status'] = 1;	
										$objUser = new User();
										$quesdtion_data['added_ip'] = $objUser->getUserIp();				
										$quesdtion_data['modified_timestamp'] = date("Y-m-d H:m:s");
										$quesdtion_data['modified_ip'] = $objUser->getUserIp();								 
										$quesdtion_data['modified_user_id'] = $identity->user_id;
										$quesdtion_data['added_user_id'] = $identity->user_id;
										if($question1!=''){
											$quesdtion_data['question'] = $question1;
											$quesdtion_data['answer_type'] = $post['q1_answer_type'];
											$insert_id = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($quesdtion_data);
											if($insert_id){
												if($post['q1_answer_type']=='radio'||$post['q1_answer_type']=='checkbox'){
													$option_data = array();
													$option_data['question_id'] = $insert_id;
													if($post['q1_option_1']!=''){
														$option_data['option'] = $post['q1_option_1'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
													if($post['q1_option_2']!=''){
														$option_data['option'] = $post['q1_option_2'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
													if($post['q1_option_3']!=''){
														$option_data['option'] = $post['q1_option_3'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
												}
												$error = '';
												$error_count=0;												 												
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}
										}
										if($question2!=''){
											$quesdtion_data['question'] = $question2;
											$quesdtion_data['answer_type'] = $post['q2_answer_type'];
											$insert_id = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($quesdtion_data);
											if($insert_id){
												if($post['q2_answer_type']=='radio'||$post['q2_answer_type']=='checkbox'){
													$option_data = array();
													$option_data['question_id'] = $insert_id;
													if($post['q2_option_1']!=''){
														$option_data['option'] = $post['q2_option_1'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
													if($post['q2_option_2']!=''){
														$option_data['option'] = $post['q2_option_2'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
													if($post['q2_option_3']!=''){
														$option_data['option'] = $post['q2_option_3'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
												}
												$error = '';
												$error_count=0;												 												
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}
										}
										if($question3!=''){
											$quesdtion_data['question'] = $question3;
											$quesdtion_data['answer_type'] = $post['q3_answer_type'];
											$insert_id = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($quesdtion_data);
											if($insert_id){
												if($post['q3_answer_type']=='radio'||$post['q3_answer_type']=='checkbox'){
													$option_data = array();
													$option_data['question_id'] = $insert_id;
													if($post['q3_option_1']!=''){
														$option_data['option'] = $post['q3_option_1'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
													if($post['q3_option_2']!=''){
														$option_data['option'] = $post['q3_option_2'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
													if($post['q3_option_3']!=''){
														$option_data['option'] = $post['q3_option_3'];
														$this->getGroupQuestionnaireOptionTable()->AddOptions($option_data);
													}
												}
												$error = '';
												$error_count=0;												 												
											}else{
												$error = 'Some error occured. Please try again';
												$error_count++;
											}
										}										
									}
								}else{ 
									$error = 'Question field couldnot be empty';
									$error_count++;
								}
							}else{
								$error = 'Invalid access';
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
					$error = "The groups you are requested is not existing in this system";
					$error_count++;
				}
			}else{
				$error = "The groups you are requested is not existing in this system";
				$error_count++;
			}
		}else{
			$error = 'Your session has to be expired';
			$error_count++;
		}
		if($error_count==0){
			$questions = $this->getGroupJoiningQuestionnaireTable()->getQuestionnaire($planet_id);
			$i=0;
			$question_list = '';
			foreach($questions as $list){$i++;
				$question_list.= '<div class="settings-field-outer">
									<div class="settings-label2">Question  '.$i.' </div>
									<div class="settings-input-small" id="question_'.$list->questionnaire_id.'">'.$list->question.'</div>
									<div class="field-edit" id="question_button_'.$list->questionnaire_id.'"><a href="javascript:void(0)" id="edit_question_'.$list->questionnaire_id.'" class="edit_question">Edit</a></div>
									<div class="clear"></div>
								</div>';
			}
			$return_array['msg'] = $question_list;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function questionnaireAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = '';
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$error_count = 0;
		$is_question = 0;
		$questionForm='';
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {
						$subGroupAlreadyRegisterInfo =$this->getUserGroupTable()->getUserGroup($identity->user_id,  $planet_id);
						if(isset($subGroupAlreadyRegisterInfo->user_group_id) && !empty($subGroupAlreadyRegisterInfo->user_group_id)){	 
							$error = 'You are already member of this group';
							$error_count++;	
						}else if($this->getUserGroupJoiningRequestTable()->checkIfrequestExist($identity->user_id,$planet_id)){
							$error = 'You are already requested in this group';
							$error_count++;	
						}else{
							$group_settings = $this->getGroupSettingsTable()->loadGroupSettings($planet_id);
							if($group_settings->group_member_join_type == 'Any' || $group_settings->group_member_join_type == 'AdminApproval'){
								$post = $request->getPost();
								$offset = 0;
								$question = $post['question'];
								if($question == '' ||$question==1) {
									$offset = 0;
								}
								else{
									$offset = $question-1;
								}
								$answer = $post['answer'];
								$question_id = $post['question_id'];
								if($question>1){
									if($answer!=''){
										$question_old = $this->getGroupJoiningQuestionnaireTable()->getQuestionFromQuestionIdAndGroupId($question_id,$planet_id);										
										if($question_old->questionnaire_id){
											$answerIfExist = $this->getGroupQuestionnaireAnswersTable()->getAnswerOfOneQuestion($planet_id,$question_id,$identity->user_id);
											if(!empty($answerIfExist)&&$answerIfExist->answer_id){
												if($this->getGroupQuestionnaireAnswersTable()->UpdateAnswer($answerIfExist->answer_id,$answer)){
													$error = '';
													$error_count=0;
												}else{
													$error = 'Some error occured.Please try again.';
													$error_count++;	
												}
											}else{
												$answer_data['group_id'] = $planet_id;
												$answer_data['question_id'] = $question_id;
												if($question_old->answer_type == 'Textarea'){
													$answer_data['answer'] = $answer;
												}else{
													$answer_data['selected_options'] = $answer;
												}
												$answer_data['added_user_id'] = $identity->user_id;
												$objUser = new User();												
												$answer_data['added_ip'] = $objUser->getUserIp();
												if($this->getGroupQuestionnaireAnswersTable()->AddAnswer($answer_data)){
													$error = '';
													$error_count=0;
												}else{
													$error = 'Some error occured.Please try again.';
													$error_count++;	
												}
											}
										}
									}else{
										$error = 'Answer this question.';
										$error_count++;	
									}
								}
								if($error_count==0){
									$total_questions = $this->getGroupJoiningQuestionnaireTable()->getQuestionnaireCount($planet_id);
									if($question <= $total_questions->question_count){
										$question = $this->getGroupJoiningQuestionnaireTable()->getQuestionnaireWithPagination($planet_id,$offset,1);	
										foreach($question as $list){
											if($list->answer_type == 'radio' || $list->answer_type == 'checkbox' ){
												$options = $this->getGroupQuestionnaireOptionTable()->getoptionOfOneQuestion($list->questionnaire_id);
											}											 
											$questionForm .= '
												<div class="settings-field-outer">
													<div class="settings-label2">Question  '. ($offset+1) .' </div>
													<div class="settings-input-small" >'.$list->question.'<input type="hidden" id="answer_type" value="'.$list->answer_type.'" /></div>';
											if($list->answer_type == 'radio' ){
												$i=0;
												foreach($options as $option){ $i++;
												$questionForm .= '	<div class="field-edit" > <input type="radio" id="answer_'.$i.'_'.$list->questionnaire_id.'" name="answer_'.$list->questionnaire_id.'" value="'.$option->option_id.'" />'.$option->option.'</div>';
												}
											}else if($list->answer_type == 'checkbox' ){ 
												$i=0;
												foreach($options as $option){ $i++;
												$questionForm .= '	<div class="field-edit" > <input type="checkbox" id="answer_'.$i.'_'.$list->questionnaire_id.'" name="answer_'.$list->questionnaire_id.'" value="'.$option->option_id.'" />'.$option->option.'</div>';
												}
											}else{
													
												$questionForm .= '	<div class="field-edit" > <input type="text" id="answer_'.$list->questionnaire_id.'" /></div>';
											}
													$questionForm .= '<div class="field-edit" > <input type="button" class="blue-butn" id="btn_answer_'.$list->questionnaire_id.'" value="Answer" /></div>
													<div class="clear"></div>
												</div>
												';
												$is_question++;
										}
										$error_count = 0;
									}else{
										if($group_settings->group_member_join_type == 'Any'){
										$userGroupData = array();
										$userGroupData['user_group_user_id'] = $identity->user_id;
										$userGroupData['user_group_group_id'] = $planet_id;	
										$objUser = new User();
										$userGroupData['user_group_added_ip_address'] = $objUser->getUserIp();
										$userGroupData['user_group_status'] = "1";
										$userGroupData['user_group_is_owner'] = "0";
										$userGroup = new UserGroup();
										$userGroup->exchangeArray($userGroupData);
										$insertedUserGroupId ="";
										$insertedUserGroupId = $this->getUserGroupTable()->saveUserGroup($userGroup);
										if(isset($insertedUserGroupId) && !empty($insertedUserGroupId)){
											$error_count = 0;
											$admin_users = $this->getUserGroupTable()->getAllAdminUsersWithGroupSettings($planet_id);
											$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
											foreach($admin_users as $users){
												$permission = 1;
												if((isset($users->member)&&$users->member!='no')){
													$permission =0;
												}
												$config = $this->getServiceLocator()->get('Config');
												$base_url = $config['pathInfo']['base_url'];
												$msg = $identity->user_given_name." joined in <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."' />".$subGroupData->group_title."</a>";
												$subject = 'Group join request';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($users->user_group_user_id,$msg,2,$subject,$from);
											}									  
										}else{
											$error = 'Some error occured. Please try again';
											$error_count++;
										}
									}elseif($group_settings->group_member_join_type == 'AdminApproval'){								 
										$userJoiningRequest = array();
										$userJoiningRequest['user_group_joining_request_user_id'] = $identity->user_id;
										$userJoiningRequest['user_group_joining_request_group_id'] = $planet_id;
										$objUser = new User();
										$userJoiningRequest['user_group_joining_request_added_ip_address'] = $objUser->getUserIp();
										$UserGroupJoiningRequestObject = new UserGroupJoiningRequest();
										$UserGroupJoiningRequestObject->exchangeArray($userJoiningRequest);
										if($insertedUserGroupJoiningRequest = $this->getUserGroupJoiningRequestTable()->saveUserGroupJoiningRequest($UserGroupJoiningRequestObject)){
											$error_count = 0;
											$admin_users = $this->getUserGroupTable()->getAllAdminUsers($planet_id);									 
											$admin_users = $this->getUserGroupTable()->getAllAdminUsersWithGroupSettings($planet_id);
											$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
											foreach($admin_users as $users){
												$permission = 1;
												if((isset($users->member)&&$users->member!='no')){
													$permission =0;
												}
											$config = $this->getServiceLocator()->get('Config');
											$base_url = $config['pathInfo']['base_url'];
											$msg = $identity->user_given_name." requested to join  <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."' />".$subGroupData->group_title."</a>";
											$subject = 'Group join request';
											$from = 'admin@jeera.com';
											$this->UpdateNotifications($users->user_group_user_id,$msg,2,$subject,$from);
											}
										}else{
											$error = 'Some error occured. Please try again';
											$error_count++;
										}							 
									}
									}
								}
							}else{
								$error = 'This is a closed group.';
								$error_count++;					
							}
						}
					}else{
						$error = 'Invalid request';
						$error_count++;
					}
				}else{
					$error = 'Invalid request';
					$error_count++;
				}
			}else{
				$error = 'Invalid request';
				$error_count++;
			}
		}else{
			$error = 'Your session has to be expired';
			$error_count++;
		}
		if($error_count==0){	
			if($is_question==0){
				$return_array['msg'] = '';
				$return_array['success'] = 1;
				$return_array['question'] = 0;
			}else{
				$return_array['msg'] = $questionForm;
				$return_array['success'] = 1;
				$return_array['question'] = 1;
			}
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
			$return_array['question'] = 0;
		 }
		 echo json_encode($return_array);die();
	}		
	public function getGroupQuestionnaireAnswersTable(){
		if (!$this->groupQuestionnaireAnswersTable) {
            $sm = $this->getServiceLocator();
			$this->groupQuestionnaireAnswersTable = $sm->get('Groups\Model\GroupQuestionnaireAnswersTable');
        }
        return $this->groupQuestionnaireAnswersTable;
	}
	public function getGroupQuestionnaireOptionTable(){
		if (!$this->groupQuestionnaireOptionsTable) {
            $sm = $this->getServiceLocator();
			$this->groupQuestionnaireOptionsTable = $sm->get('Groups\Model\GroupQuestionnaireOptionsTable');
        }
        return $this->groupQuestionnaireOptionsTable;
	}
	public function editQuestionAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {						 
						$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
						if($admin_status->is_admin){
							$post = $request->getPost();
							if(!empty($post)){
								$question_id = $post['question_id'];								 
								if($question_id!=''){
									$question = $this->getGroupJoiningQuestionnaireTable()->getQuestionFromQuestionId($question_id);
									$viewModel->setVariable('question', $question);
									if(!empty($question)&&($question->answer_type=='radio'||$question->answer_type=='checkbox')){	
										$question_options = $this->getGroupQuestionnaireOptionTable()->getoptionOfOneQuestion($question_id);
										$viewModel->setVariable('options', $question_options);
									}
								}else{ 
									$error[] = 'Question field couldnot be empty';								 
								}
							}else{
								$error[] = 'Invalid access';								 
							}
						}else{
							$error[] = 'You don\'t have the permission to do this';							 
						}
					}else{
						$error[] = 'Invalid access';						 
					}
				}else{
					$error[] = "The groups you are requested is not existing in this system";					
				}
			}else{
				$error[] = "The groups you are requested is not existing in this system";				 
			}
		}else{
			$error[] = 'Your session has to be expired';			 
		}
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function getUserQuestionnaireAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			$group_seo = $this->params('group_id'); 				
			$planet_seo = $this->params('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$request = $this->getRequest();
					if ($request->isPost()) {						 
						$post = $request->getPost();
						if(!empty($post)){
							$user_id = $post['user_id'];								 
							if($user_id!=''){
								$questions = $this->getGroupJoiningQuestionnaireTable()->getQuestionnaire($planet_id);
								$arr_questionnaire = array();
								foreach($questions as $row){
									$answer = '';
									$Objanswers = $this->getGroupQuestionnaireAnswersTable()->getAnswerOfOneQuestion($planet_id,$row->questionnaire_id,$user_id);
									if($row->answer_type == 'Textarea'){										 
										if(!empty($Objanswers)&&$Objanswers->answer!=''){$answer = $Objanswers->answer;}
									}else{										 
										if(!empty($Objanswers)&&$Objanswers->selected_options!=''){ $selected_options = $Objanswers->selected_options;
											$arr_options = explode(",",$selected_options);
											array_filter($arr_options);
											$i=0;
											foreach($arr_options as $option_id){ 
												$Oboptions = $this->getGroupQuestionnaireOptionTable()->getSelectedOptionValue($option_id);
												if(!empty($Oboptions)&&$Oboptions->option!=''){$i++;
													$answer .=$i.'. '.$Oboptions->option.' ';
												}
											}
										}
									}
									$arr_questionnaire[] = array('questions'=>$row->question,
															'answer'=>$answer);
								}
								$viewModel->setVariable('questionnaire', $arr_questionnaire);
							}else{ 
								$error[] = 'Question field couldnot be empty';								 
							}
						}else{
							$error[] = 'Invalid access';								 
						}						 
					}else{
						$error[] = 'Invalid access';						 
					}
				}else{
					$error[] = "The groups you are requested is not existing in this system";					
				}
			}else{
				$error[] = "The groups you are requested is not existing in this system";				 
			}
		}else{
			$error[] = 'Your session has to be expired';			 
		}
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function ajaxGetPlanetFromGalaxySeoTitleAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();	
		$post = $request->getPost();		
		$group_seo = $post['group_seo'];  
		if($group_seo!=''){
			$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
			$group_id = $groupdetails->group_id;			 
			if($group_id){					 
				$planets = $this->getGroupTable()->getPlanets($group_id);
				$viewModel->setVariable('planets', $planets);				 
			}else{
				$error[] = "The groups you are requested is not existing in this system";
			}
		}else{
			$error[] = "The groups you are requested is not existing in this system";
		}		 
		$viewModel->setVariable('error', $error);	
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
		die();
	}
}