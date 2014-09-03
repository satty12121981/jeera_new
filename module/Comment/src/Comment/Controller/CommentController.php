<?php
####################Comment Controller #################################

namespace Comment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;	//Needed for checking User session
use Zend\Authentication\Adapter\DbTable as AuthAdapter;	//Db apapter
use Zend\Crypt\BlockCipher;		# For encryption
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/
 
#Group classs
use Groups\Model\Groups;  
use Groups\Model\GroupsTable; 
use Discussion\Model\Discussion;  
use Discussion\Model\DiscussionTable; 
use Album\Model\Album;  
use Album\Model\AlbumTable; 
use Activity\Model\Activity;  
use Activity\Model\ActivityTable; 
use Activity\Model\ActivityRsvpTable; 

use Comment\Model\Comment;  
use Comment\Model\CommentTable; 
use Notification\Model\UserNotification; 
use \Exception;		#Exception class for handling exception
#Group Suggestion add form
use Comment\Form\CommentForm;
use Comment\Form\CommentFilter; 
use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface;
 
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
class CommentController extends AbstractActionController
{     
	protected $userTable;
	protected $groupTable;
	protected $userGroupTable;
	protected $photoTable = ""; 
	protected $activityTable = ""; 
	protected $discussionTable = ""; 
	protected $albumTable = ""; 
	protected $commentTable ="";
	protected $likeTable;
	protected $albumDataTable;	
	protected $activityRsvpTable;
	protected $userNotificationTable;
	protected $albumTagTable;
	 
	public function __construct(){
		return $this;
	}	
	#this function will load the css and javascript need for perticular action
	protected function getViewHelper($helperName){
    	return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}   
 	#This will load all Groups   
    public function indexAction(){		
		$allComments = array();	//this will keep the all groups of database
		$identity = "";			//This will check user is logged in or not		
		$userGroups = array();	//	This will hold the Data of Register User Groups
		$userGroupsIds = array();//	This will hold the ids of Register User Groups		
		try {
			$request   = $this->getRequest();							
			$auth = new AuthenticationService();	
			#pass the group Image Oath. We need to show thumb image here
			$identity = null;        
			if ($auth->hasIdentity()) {
				// Identity exists; get it
				$identity = $auth->getIdentity();
				$this->layout()->identity = $identity;	//assign Identity to layout 		
				$userData =array();	///this will hold data from y2m_user table				 				
				$userData = $this->getUserTable()->getUser($identity->user_id);	#check the identity againts the DB	
				if(isset($userData->user_id) && !empty($userData->user_id)){				
					#fetch all groups
					$allComments = $this->getCommentTable()->fetchAllComments();	
				} 
			} 
			$this->layout()->identity = $identity;	//assign Identity to layout  	
		} catch (\Exception $e) {
	        echo "Caught exception: " . get_class($e) . "\n";
   			echo "Message: " . $e->getMessage() . "\n";exit;
		}		
		$viewModel = new ViewModel(array('allGroups' => $allGroups, 'groupThumb' => $this->groupThumb));
		$viewModel->setTerminal($request->isXmlHttpRequest());		
    	return $viewModel;          
	}
	public function ajaxAction(){
		echo "here";die();
	}
	#This will load all Subgroups Of Group   
	public function CommentsAction(){		
		$error = array();
		$success = array();		  				
		$auth = new AuthenticationService(); 
		$userData = array();		 
		$ModuleCommentsData = array();
		$request   = $this->getRequest();		
		$post = $request->getPost();		 
		$GroupReferId = $post['content_id']; 			
		$SystemTypeId = $post['type']; 	 
		$sm = $this->getServiceLocator();	
		$auth = new AuthenticationService();	
		$insertedcommentsId = 0;
		$identity = null;   
		if ($auth->hasIdentity()) {			
			$identity = $auth->getIdentity();				
			$this->userTable = $sm->get('User\Model\UserTable');	
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');				
			$userData = $this->userTable->getUser($identity->user_id);			
			$this->commentTable = $sm->get('Comment\Model\CommentTable');					
			$SystemTypeData = $this->groupTable->fetchSystemType($SystemTypeId);
			$LikeTypeData = $this->groupTable->fetchSystemType('comment');
			$module_id = null;			
			switch ($SystemTypeId) {
				case 'Discussion':
					$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
					$GroupModuleData = $this->discussionTable->getDiscussion($GroupReferId);//print_r($GroupModuleData);die();
					$moduleId = $GroupModuleData->group_discussion_group_id;
					break;
				case 'Activity':
					$this->activityTable = $sm->get('Activity\Model\ActivityTable');
					$GroupModuleData = $this->activityTable->getActivity($GroupReferId);
					$moduleId = $GroupModuleData->group_activity_group_id;
					break;
				case 'album':
					$this->albumTable = $sm->get('Album\Model\AlbumTable');
					$GroupModuleData = $this->albumTable->getGroupAlbum($GroupReferId);
					$moduleId = $GroupModuleData->group_album_group_id;
					break;
				case 'Media': 
					$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
					$GroupModuleData = $this->albumdataTable->getGroupAlbumData($GroupReferId);
					$moduleId = $GroupModuleData->album_group_id; 
				 break;
				 case 'Userfiles': 
					$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
					$GroupModuleData = $this->albumdataTable->getGroupAlbumData($GroupReferId);
					$moduleId = $GroupModuleData->album_user_id; 
				 break;
			}
			$form = new CommentForm($SystemTypeData->system_type_id,$GroupReferId);				
			if( isset($moduleId) && !empty($moduleId) ) {				
				if (isset($post['action'])&&$post['action']=='save') {						
						$comment = new Comment();
						$form->setInputFilter(new CommentFilter());
						$form->setData($request->getPost());						
						if ($form->isValid()) {  						
							$comment_by_user_id = $userData->user_id;
							$comment_status = 1;							
							$servParam = $request->getServer();
							$remoteAddr = $servParam->get('REMOTE_ADDR');							
							$commentsData = array();
							$commentsData['comment_system_type_id'] = $SystemTypeData->system_type_id;
							$commentsData['comment_by_user_id'] = $comment_by_user_id;
							$commentsData['comment_refer_id'] = $GroupReferId;
							$commentsData['comment_content'] = $post['comment_content'];
							$commentsData['comment_status'] = $comment_status;							
							$commentsData['comment_added_ip_address'] =  $remoteAddr;
							$commentsData['comment_added_timestamp'] = date("Y-m-d H:i:s");							
							$comment->exchangeArray($commentsData);							
							$insertedcommentsId = "";
							$insertedcommentsId = $this->commentTable->saveComment($comment); 		
							$config = $this->getServiceLocator()->get('Config');
							$base_url = $config['pathInfo']['base_url'];
							if(isset($insertedcommentsId) && !empty($insertedcommentsId)) {
								 $success[] = 'comments saved successfully';
								 switch ($SystemTypeId) {
									case 'Activity':
										$this->activityTable = $sm->get('Activity\Model\ActivityTable');
										$activityData = $this->activityTable->getActivity($GroupReferId);
										$joinedMembers = $this->getActivityRsvpTable()->getAllJoinedMembers($activityData->group_activity_id,$activityData->group_activity_group_id);
										$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($activityData->group_activity_group_id);
										foreach($joinedMembers as $members){
											$permission = 1;
											if((isset($members->activity)&&$members->activity=='no')){
												$permission =0;
											}
											if($activityData->group_activity_owner_user_id!=$members->user_id&&$members->user_id!=$identity->user_id&&$permission){
												$msg = $identity->user_given_name." commented the activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activityData->group_activity_id."'>".$activityData->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
												$subject = 'Comment On activity';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($members->user_id,$msg,2,$subject,$from);
											}
										}
										if($activityData->group_activity_owner_user_id!=$identity->user_id){ 
											$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
											$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($activityData->group_activity_owner_user_id,$activityData->group_activity_group_id);
											$permission = 1;
											if((isset($user_group_settings->activity)&&$user_group_settings->activity=='no')){
												$permission =0;
											}
											if($permission){
												$msg = $identity->user_given_name." commented the activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activityData->group_activity_id."'>".$activityData->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
												$subject = 'Comment On activity';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($activityData->group_activity_owner_user_id,$msg,2,$subject,$from);
											}
										}
									break;
									case 'Discussion':
										$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
										$joinedMembers =$this->discussionTable->getDiscussionMembersWithGroupsettings($GroupReferId,$GroupModuleData->group_discussion_group_id,$SystemTypeData->system_type_id);
										$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($GroupModuleData->group_discussion_group_id);
										foreach($joinedMembers as $members){
											$permission = 1;
											if((isset($members->discussion)&&$members->discussion=='no')){
												$permission =0;
											}
											if($members->comment_by_user_id!=$identity->user_id&&$members->comment_by_user_id!=$GroupModuleData->group_discussion_owner_user_id &&$permission){
												$config = $this->getServiceLocator()->get('Config');
												$base_url = $config['pathInfo']['base_url'];
												$msg = $identity->user_given_name." commented the discussion -  ".$GroupModuleData->group_discussion_content." - under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/discussion'>".$subGroupData->group_title."</a>";
												$subject = 'Comments on discussion';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($members->comment_by_user_id,$msg,2,$subject,$from);
											}
										}
										if($GroupModuleData->group_discussion_owner_user_id!=$identity->user_id){ 
											$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
											$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($GroupModuleData->group_discussion_owner_user_id,$GroupModuleData->group_discussion_group_id);
											$permission = 1;
											if((isset($user_group_settings->discussion)&&$user_group_settings->discussion=='no')){
												$permission =0;
											}
											if($permission){
												$config = $this->getServiceLocator()->get('Config');
												$base_url = $config['pathInfo']['base_url'];
												$msg = $identity->user_given_name." commented the discussion -  ".$GroupModuleData->group_discussion_content." - under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/discussion'>".$subGroupData->group_title."</a>";
												$subject = 'Comments on discussion';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($GroupModuleData->group_discussion_owner_user_id,$msg,2,$subject,$from);
											}
										}
									break;
									case 'Media':
										$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
										$album_details = $this->albumdataTable->getAlbumDetailsFromData($GroupReferId);
										$album_data_details = $this->albumdataTable->getalbumdata($GroupReferId);
										if($album_details->album_user_id!=$identity->user_id){
											$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
											$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($album_details->album_user_id,$album_details->album_group_id);
											$permission = 1;
											$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
											if((isset($user_group_settings->media)&&$user_group_settings->media=='no')){
												$permission =0;
											}
											if($permission){												 
												$config = $this->getServiceLocator()->get('Config');
												$base_url = $config['pathInfo']['base_url'];												 
												$msg = '<a href="'.$base_url.'album/photo/'.$GroupReferId.'">'.$identity->user_given_name." commented in one image</a>";
												$subject = 'Comments on images';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($album_details->album_user_id,$msg,2,$subject,$from);
											}
										}
										if($album_data_details->added_user_id!=$identity->user_id&&$album_data_details->added_user_id!=$album_details->album_user_id){
											$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
											$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($album_data_details->added_user_id,$album_details->album_group_id);
											$permission = 1;											 
											if((isset($user_group_settings->media)&&$user_group_settings->media=='no')){
												$permission =0;
											}
											if($permission){												 
												$config = $this->getServiceLocator()->get('Config');
												$base_url = $config['pathInfo']['base_url'];												 
												$msg = '<a href="'.$base_url.'album/photo/'.$GroupReferId.'">'.$identity->user_given_name." commented in one image</a>";
												$subject = 'Comments on images';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($album_data_details->added_user_id,$msg,2,$subject,$from);
											}
										}
										$tagged_users = $this->albumdataTable->getTaggedUsersWithGroupSettings($GroupReferId,$album_details->album_group_id);										 
										foreach($tagged_users as $members){
											$permission = 1;
											if((isset($members->media)&&$members->media=='no')){
												$permission =0;
											}
											if($members->album_tag_user_id!=$album_data_details->added_user_id&&$members->album_tag_user_id!=$album_details->album_user_id &&$permission){
												$page = $post['page'];
												$config = $this->getServiceLocator()->get('Config');
												$base_url = $config['pathInfo']['base_url'];
												$msg = '<a href="'.$base_url.'album/photo/'.$GroupReferId.'">'.$identity->user_given_name." commented in one image</a>";
												$subject = 'Comments on images';
												$from = 'admin@jeera.com';
												$this->UpdateNotifications($members->album_tag_user_id,$msg,2,$subject,$from);
											}
										}
									break;
									case 'Userfiles':
										$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
										$album_details = $this->albumdataTable->getAlbumDetailsFromData($GroupReferId);
										$album_data_details = $this->albumdataTable->getalbumdata($GroupReferId);
										$this->albumTagTable = $sm->get('Album\Model\AlbumTagTable');
										$tagged_users = $this->albumTagTable->getAllTaggedUsers($GroupReferId);
										$all_notification_users = array();										 
										$all_notification_users[] = $album_details->album_user_id;
										$all_notification_users[] = $album_data_details->added_user_id;
										foreach($tagged_users as $tusers){
											$all_notification_users[] = $tusers->user_id;
										}
										$notification_users = array_unique($all_notification_users);
										foreach($notification_users as $users){
											if($users!=$identity->user_id){
												$permission = 1;
												if($album_details->album_group_id!=0){
													$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
													$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($users,$album_details->album_group_id);
													if((isset($user_group_settings->media)&&$user_group_settings->media=='no')){
														$permission =0;
													}
												}
												if($permission){												 
													$config = $this->getServiceLocator()->get('Config');
													$base_url = $config['pathInfo']['base_url'];
													$msg = '<a href="'.$base_url.'album/photo/'.$GroupReferId.'">'.$identity->user_given_name." commented on one image</a>";
													$subject = 'Commented in one image';
													$from = 'admin@jeera.com';
													$this->UpdateNotifications($users,$msg,2,$subject,$from);
												}
											}
										}										 
									break;
								 }
							}
							else {
								 $success[] = 'Oops an error is occured while saving comments';	
							}
						 }						 
				} 
				$comment_data = array();					
				$comment_data = $this->commentTable->getInsertedCommentWithUserDetails($insertedcommentsId);					
			}else{
				$error[] = "Unable to process";
			}		
		}else{
			$error[] = "Your sesssion has been expired. Please log in and try again.";
		}
		$viewModel = new ViewModel(array('form' => $form,'userData' => $userData,'module_comments' => $comment_data,'Group_Refer_Id' => $GroupReferId,'System_Type_Id' => $SystemTypeId, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;	
	}
	public function loadmoreAction(){
		$auth = new AuthenticationService(); 
		$error = array();
		$identity = array();
		$comment_data = array();
		$planet_member = 0;
		$system = '';
		$is_admin = 0;
		$sm = $this->getServiceLocator();	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$system =  $post['type'];
				$this->groupTable = $sm->get('Groups\Model\GroupsTable');	
				if($system == 'Activity'||$system == 'Discussion'||$system == 'Media'){
					$planet = $post['planet'];
					$planetdetails =  $this->groupTable->getGroupIdFromSEO($planet);
					$planet_id = $planetdetails->group_id; 
					if($this->groupTable->is_member($planet_id,$identity->user_id)){ 
						$planet_member = 1;
					}else{	
						$planet_member = 0;
					}
					$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
					if($admin_status->is_admin){
						$is_admin = 1;
					}
					$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
					if(!empty($user_role)){
						$is_admin = 1;
					}					
					if($system == 'Activity'){
						$referer_id =  $post['content_id'];
						$activity_data = $this->getActivityTable()->getActivity($referer_id);
						if($activity_data->group_activity_owner_user_id == $identity->user_id){
								$is_admin = 1;
						}
					}
					if($system == 'Discussion'){
						$referer_id =  $post['content_id'];
						$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
						$discussion_data = $this->discussionTable->getDiscussion($referer_id);						 
						if($discussion_data->group_discussion_owner_user_id == $identity->user_id){
								$is_admin = 1;
						}
					}
					if($system == 'Media'){
						$referer_id =  $post['content_id'];
						$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
						$GroupalbumData = $this->albumdataTable->getGroupAlbumData($referer_id);			 
						if($GroupalbumData->added_user_id == $identity->user_id){
								$is_admin = 1;
						}
					}
					 
				}
				if($system){					
					$SystemTypeData = $this->groupTable->fetchSystemType($system);
					$referer_id =  $post['content_id'];
					if($referer_id){	
						$page = $request->getPost('page');
						if($page>0){	
							$page = ($page-1)*10+2;
						}else{
							$page = $page+2;
						}
						$this->commentTable = $sm->get('Comment\Model\CommentTable');
						$comment_data = $this->commentTable->getAllCommentsWithLike($SystemTypeData->system_type_id,$referer_id,$identity->user_id,10,$page);
					}else{
						$error[] = "Unautherised access.";
					}
				}else{
					$error[] = "Unautherised access.";
				}
			}else{	
				$error[] = "Unautherised access.";
			}
		}
		else{
			$error[] = "Your session already expired. Please try again after login..";
		}
		$viewModel = new ViewModel(array('error' => $error,'comment_data' => $comment_data,'planet_member'=>$planet_member,'system'=>$system,'user_id'=>$identity->user_id,'is_admin'=>$is_admin));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	 }
	public function editAction(){
		$error = '';
		$error_cnt = 0;
		$success = array();
		$GroupReferId = ""; 
		$SystemTypeId = ""; 				
		$auth = new AuthenticationService(); 
		$userData = array();
		$ModuleCommentsData = array();
		$request   = $this->getRequest();		
		$post = $request->getPost();		
		$GroupReferId = $post['content_id']; 			
		$SystemTypeId = $post['type']; 	 
		$sm = $this->getServiceLocator();	
		$auth = new AuthenticationService();	
		$insertedcommentsId = 0;
		$identity = null;   
		if ($auth->hasIdentity()) {			
			$identity = $auth->getIdentity();			
			$this->userTable = $sm->get('User\Model\UserTable');			
			$userData = $this->userTable->getUser($identity->user_id);			
			if (isset($post['action'])&&$post['action']=='save') {
				$comment_id = $post['content_id'];
				$this->commentTable = $sm->get('Comment\Model\CommentTable');
				$comment_data = $this->commentTable->getInsertedCommentWithUserDetails($comment_id); 
				if($comment_data->user_id == $identity->user_id){
					if($post['comment_content']!=''){
						$data['comment_content'] = $post['comment_content'];
						if($this->commentTable->updateCommentTable($data,$comment_id)){
							
						}else{
							$error = "Some error occured.Please try agaian";
							$error_cnt++; 
						}
					}else{	
						$error = "Comment content required.";
						$error_cnt++; 
					}
				}else{
					$error = "You don't have the permissions to do this.";
					$error_cnt++; 
				}
			}	 	
		}else{
			$error = "Your sesssion has been expired. Please log in and try again.";
			$error_cnt++; 
		}
		if($error_cnt == 0){
			$return_array = array('error' =>0,'msg'=>$error);
		}else{
			$return_array = array('error' =>1,'msg'=>$error);
		}
		echo json_encode($return_array);die();		
	 }
	public function deleteAction(){
		$error = '';
		$error_cnt = 0;
		$success = array();
		$GroupReferId = ""; 
		$SystemTypeId = ""; 				
		$auth = new AuthenticationService(); 
		$userData = array();	 
		$ModuleCommentsData = array();
		$request   = $this->getRequest();		
		$post = $request->getPost();		  
		$GroupReferId = $post['content_id']; 			
		//$SystemTypeId = $post['type']; 	 
		$sm = $this->getServiceLocator();	
		$auth = new AuthenticationService();	
		$insertedcommentsId = 0;
		$identity = null;   
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();		 
			$this->groupTable = $sm->get('Groups\Model\GroupsTable'); 			 
			$this->userTable = $sm->get('User\Model\UserTable');
			$delete_permission = 0; 			 
			$comment_id = $post['content_id'];
			$this->commentTable = $sm->get('Comment\Model\CommentTable');
			$comment_data = $this->commentTable->getInsertedCommentWithUserDetails($comment_id);
			$SystemInfo = $this->groupTable->getSystemInfo($comment_data->comment_system_type_id);
			if($comment_data->user_id == $identity->user_id){
				$delete_permission = 1;
			}else{
				switch ($SystemInfo->system_type_title) {
					case 'Discussion':
						$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
						$DiscussionData = $this->discussionTable->getDiscussion($comment_data->comment_refer_id);//print_r($GroupModuleData);die();
						if($DiscussionData->group_discussion_owner_user_id == $identity->user_id){
							$delete_permission = 1;
						}
						$admin_status = $this->getGroupTable()->getAdminStatus($DiscussionData->group_discussion_group_id,$identity->user_id);
						if($admin_status->is_admin){
							$delete_permission = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($DiscussionData->group_discussion_group_id,$identity->user_id);
						if(!empty($user_role)){
							$delete_permission = 1;
						}
						break;
					case 'Activity':
						$this->activityTable = $sm->get('Activity\Model\ActivityTable');
						$ActivityData = $this->activityTable->getActivity($comment_data->comment_refer_id);
						if($ActivityData->group_activity_owner_user_id!=''){
							if($ActivityData->group_activity_owner_user_id == $identity->user_id){
								$delete_permission = 1;
							}
							$admin_status = $this->getGroupTable()->getAdminStatus($ActivityData->group_activity_group_id,$identity->user_id);
							if($admin_status->is_admin){
								$delete_permission = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($ActivityData->group_activity_group_id,$identity->user_id);
							if(!empty($user_role)){
								$delete_permission = 1;
							}
						}
					case 'album':
						$this->albumTable = $sm->get('Album\Model\AlbumTable');
						$GroupModuleData = $this->albumTable->getGroupAlbum($comment_data->comment_refer_id);
						$moduleId = $GroupModuleData->group_album_group_id;
						break;
					case 'Media': 
						$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
						$GroupModuleData = $this->albumdataTable->getGroupAlbumData($GroupReferId);
						$moduleId = $GroupModuleData->album_group_id; 
					 break;
					 case 'Userfiles': 
						$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
						$GroupModuleData = $this->albumdataTable->getGroupAlbumData($GroupReferId);
						$moduleId = $GroupModuleData->album_user_id;
						$profile_name = $post['profile']; 	 
						if($profile_name!=''){
							$userinfo = $this->userTable->getUserByProfilename($profilename);
							if($userinfo->user_id==$moduleId){
							$delete_permission = 1;
							}
						}
					 break;
				}
			}
			if($delete_permission){
				$SystemTypeData = $this->getGroupTable()->fetchSystemType('Comment');
				$this->getLikeTable()->deleteEventLike($SystemTypeData->system_type_id,$comment_id);
				if($this->commentTable->deleteComment($comment_id)){
					$error = "Successfully removed comments.";
					$error_cnt=0;
				}else{
					$error = "Some error occured. Please try again.";
					$error_cnt++;
				}					
				
			}else{
				$error = "You don't have the permissions to do this.";
				$error_cnt++; 
			}			 	 	
		}else{
			$error = "Your sesssion has been expired. Please log in and try again.";
			$error_cnt++; 
		}
		if($error_cnt == 0){
			$return_array = array('error' =>0,'msg'=>$error);
		}else{
			$return_array = array('error' =>1,'msg'=>$error);
		}
		echo json_encode($return_array);die();		
	}
	public function getLikeTable(){
        if (!$this->likeTable) {
            $sm = $this->getServiceLocator();
            $this->likeTable = $sm->get('Like\Model\LikeTable');
        }
        return $this->likeTable;
    }
	public function getUserGroupTable(){
        if (!$this->userGroupTable) {
            $sm = $this->getServiceLocator();
			$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
        }
        return $this->userGroupTable;
    } 
	public function getGroupTable(){
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    }
	public function getActivityTable(){
        if (!$this->activityTable) {
            $sm = $this->getServiceLocator();
            $this->activityTable = $sm->get('Activity\Model\ActivityTable');
        }
        return $this->activityTable;
    }
	public function getActivityRsvpTable(){
        if (!$this->activityRsvpTable) {
            $sm = $this->getServiceLocator();
            $this->activityRsvpTable = $sm->get('Activity\Model\ActivityRsvpTable');
        }
        return $this->activityRsvpTable;
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
