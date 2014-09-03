<?php
####################Like Controller #################################
#namespace for module like
namespace Like\Controller;
#library uses
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\View\Model\JsonModel;
use Zend\Session\Container;		// We need this when using sessions     
use Zend\Authentication\AuthenticationService;		//Needed for checking User session
use Zend\Authentication\Adapter\DbTable as AuthAdapter;		//Db adapter
use Zend\Crypt\BlockCipher;		# For encryption

/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/
 
#Model uses
use User\Model\User; 
use User\Model\UserTable; 
use Groups\Model\Groups;  
use Groups\Model\GroupsTable; 
use Discussion\Model\Discussion;  
use Discussion\Model\DiscussionTable; 
use Album\Model\Album;  
use Album\Model\AlbumTable; 
use Activity\Model\Activity;  
use Activity\Model\ActivityTable; 
use Comment\Model\Comment; 
use Comment\Model\CommentTable; 
use Like\Model\Like;  
use Like\Model\LikeTable; 
use Notification\Model\UserNotification; 
use \Exception;		#Exception class for handling exception
#Group Module like uses
use Like\Form\LikeForm;
use Like\Form\LikeFilter;

use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
class LikeController extends AbstractActionController
{ 	
	protected $userTable;
	protected $groupTable;
	protected $userGroupTable;
	protected $photoTable = ""; 
	protected $activityTable = ""; 
	protected $discussionTable = "";
	protected $commentTable = ""; 
	protected $albumTable = ""; 
	protected $LikeTable = "";
	protected $remoteAddr;
	protected $albumDataTable;	
	protected $activityRsvpTable;	
	protected $userNotificationTable;
	public function __construct(){
		return $this;
	}	
	#this function will load the css and javascript need for perticular action
	protected function getViewHelper($helperName){
    	return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}    	
	public function indexAction(){
		return $this;
	}	
	#This will load all Subgroups Of Group   
	public function LikesAction() {		
		$auth = new AuthenticationService();
		$error = array();
		if ($auth->hasIdentity()) {
			$request   = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$identity = $auth->getIdentity();
				$SystemType = $post['type'];
				$identity = $auth->getIdentity();
				$sm = $this->getServiceLocator();	  
				$basePath = $sm->get('Request')->getBasePath();				
				$this->groupTable = $sm->get('Groups\Model\GroupsTable');
				$SystemTypeData = $this->groupTable->fetchSystemType($SystemType);
				if(!empty($SystemTypeData)){
					switch($SystemType){
						case 'Discussion':
							$group_id = $post['group_id'];
							$planet_id = $post['planet'];
							if($group_id!=''&&$planet_id!=''){
								$SubGroupData = $this->groupTable->getSubGroupForSEO($planet_id);
								if(!empty($SubGroupData)){
									$discussion_id = $post['content_id'];
									if($discussion_id!=''){
										$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
										$discussion_data = $this->discussionTable->getDiscussion($discussion_id);
										if(!empty($discussion_data)){
											if($this->addLIke($identity->user_id,$SystemTypeData->system_type_id,$discussion_id)){
												$success[] = 'Likes saved successfully';
												$joinedMembers =$this->discussionTable->getDiscussionMembersWithGroupsettings($discussion_id,$SubGroupData->group_id,$SystemTypeData->system_type_id);
												$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($SubGroupData->group_id);
												foreach($joinedMembers as $members){
													$permission = 1;
													if((isset($members->discussion)&&$members->discussion=='no')){
														$permission =0;
													}
													if($members->comment_by_user_id!=$identity->user_id&&$members->comment_by_user_id!=$discussion_data->group_discussion_owner_user_id &&$permission){
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$msg = $identity->user_given_name." commented the discussion -  ".$discussion_data->group_discussion_content." - under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/discussion'>".$subGroupData->group_title."</a>";
														$subject = 'Likes on discussion';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($members->comment_by_user_id,$msg,2,$subject,$from);
													}
												}
												if($discussion_data->group_discussion_owner_user_id!=$identity->user_id){ 
													$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
													$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($discussion_data->group_discussion_owner_user_id,$discussion_data->group_discussion_group_id);
													$permission = 1;
													if((isset($user_group_settings->discussion)&&$user_group_settings->discussion=='no')){
														$permission =0;
													}
													if($permission){
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$msg = $identity->user_given_name." commented the discussion -  ".$discussion_data->group_discussion_content." - under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/discussion'>".$subGroupData->group_title."</a>";
														$subject = 'Likes on discussion';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($discussion_data->group_discussion_owner_user_id,$msg,2,$subject,$from);
													}
												}
												$this->likeTable = $sm->get('Like\Model\LikeTable');
												$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$discussion_id,$identity->user_id);	
												//$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$discussion_id,$identity->user_id);
												if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
													if ( $ModuleLikesData->is_liked && $ModuleLikesData->likes_counts > 0 ) {
														$data['view'] = "<a href=\"#\" class=\"discussion-unlikes\" id=\"$discussion_id\"><img src=\"".$basePath."/public/images/likes-icon.png\" /></a>".($ModuleLikesData->likes_counts);
													} else{
														$data['view'] =	"";
													}												
												}
												else{											
													$data['view'] = '';
												}
												$data['error'] = 0;
												$data['msg'] = 0;
												echo json_encode($data);die();
																					 
											}else{
												 $error[] = 'Oops an error is occured while saving Likes';
											}
										}
										else{
											$error[] = 'Content is not existing';
										}
									}else{
										$error[] = 'Content is not existing';
									}									
								}
								else{
									$error[] = 'Unautherized access';	
								}
							}
							else{
								$error[] = 'Unautherized access';
							}
						break;
						case 'Activity':
							$group_id = $post['group_id'];
							$planet_id = $post['planet'];
							if($group_id!=''&&$planet_id!=''){
								$SubGroupData = $this->groupTable->getSubGroupForSEO($planet_id);
								if(!empty($SubGroupData)){
									$activity_id = $post['content_id'];
									if($activity_id!=''){
										$this->activityTable = $sm->get('Activity\Model\ActivityTable');
										$activity_data = $this->activityTable->getActivity($activity_id);
										if(!empty($activity_data)){
											if($this->addLIke($identity->user_id,$SystemTypeData->system_type_id,$activity_id)){
												$success[] = 'Likes saved successfully';												 
												$joinedMembers = $this->getActivityRsvpTable()->getAllJoinedMembers($activity_data->group_activity_id,$activity_data->group_activity_group_id);
												$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($activity_data->group_activity_group_id);
												foreach($joinedMembers as $members){
													$permission = 1;
													if((isset($members->activity)&&$members->activity!='no')){
														$permission =0;
													}
													if($activity_data->group_activity_owner_user_id!=$members->user_id&&$members->user_id!=$identity->user_id&&$permission){
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$msg = $identity->user_given_name." like the activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activity_data->group_activity_id."'>".$activity_data->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
														$subject = 'Likes On activity';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($members->user_id,$msg,2,$subject,$from);
													}
												}
												if($activity_data->group_activity_owner_user_id!=$identity->user_id){ 
													$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
													$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($activity_data->group_activity_owner_user_id,$activity_data->group_activity_group_id);
													$permission = 1;
													if((isset($user_group_settings->activity)&&$user_group_settings->activity!='no')){
														$permission =0;
													}
													if($permission){
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$msg = $identity->user_given_name." like the activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activity_data->group_activity_id."'>".$activity_data->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
														$subject = 'Likes On activity';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($activity_data->group_activity_owner_user_id,$msg,2,$subject,$from );												 
													}
												}
												$this->likeTable = $sm->get('Like\Model\LikeTable');
												$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity_id,$identity->user_id);	
												//$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$activity_id,$identity->user_id);
												if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
													if ( $ModuleLikesData->is_liked && $ModuleLikesData->likes_counts > 0 ) {
														$data['view'] = "<a href=\"javascript:void(0)\" class=\"activity-unlikes\" id=\"$activity_id\"><img src=\"".$basePath."/public/images/likes-icon.png\" /></a>".($ModuleLikesData->likes_counts);
													} else{
														$data['view'] =	"";
													}												
												}
												else{											
													$data['view'] = '';
												}
												$data['error'] = 0;
												$data['msg'] = 0;
												echo json_encode($data);die();
																					 
											}else{
												 $error[] = 'Oops an error is occured while saving Likes';
											}
										}else{
											$error[] = 'Content is not existing';
										}
									}else{
										$error[] = 'Content is not existing';
									}
								}else{	
									$error[] = 'Unautherized access';
								}
							}else{								 
								$error[] = 'Unautherized access';						 
							}
						break;
						case 'Media':
							$group_id = $post['group_id'];
							$planet_id = $post['planet'];
							if($group_id!=''&&$planet_id!=''){
								$SubGroupData = $this->groupTable->getSubGroupForSEO($planet_id);
								if(!empty($SubGroupData)){
									$data_id = $post['content_id'];
									if($data_id!=''){
										$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
										$album_data = $this->albumDataTable->getalbumdata($data_id);
										if(!empty($album_data)){
											if($this->addLIke($identity->user_id,$SystemTypeData->system_type_id,$data_id)){
												$success[] = 'Likes saved successfully';
												$album_details = $this->albumDataTable->getAlbumDetailsFromData($album_data->parent_album_id);
												if($album_details->album_user_id!=$identity->user_id){
													$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
													$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($album_details->album_user_id,$album_details->album_group_id);
													$permission = 1;													 
													if((isset($user_group_settings->media)&&$user_group_settings->media=='no')){
														$permission =0;
													}
													if($permission){
														$page = $post['page'];
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$userData = $this->getUserTable()->getUser($tag_id); 
														$msg = '<a href="'.$base_url.'album/photo/'.$data_id.'">'.$identity->user_given_name." liked in one image</a>";
														$subject = 'Likes on images';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($album_details->album_user_id,$msg,2,$subject,$from);
													}
												}
												if($album_data->added_user_id!=$identity->user_id&&$album_data->added_user_id!=$album_details->album_user_id){
													$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
													$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($album_data->added_user_id,$album_details->album_group_id);
													$permission = 1;													
													if((isset($user_group_settings->media)&&$user_group_settings->media=='no')){
														$permission =0;
													}
													if($permission){
														$page = $post['page'];
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$userData = $this->getUserTable()->getUser($tag_id); 
														$msg = '<a href="'.$base_url.'album/photo/'.$data_id.'">'.$identity->user_given_name." likes in one image</a>";
														$subject = 'Likes on images';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($album_data->added_user_id,$msg,2,$subject,$from);
													}
												}
												$tagged_users = $this->albumDataTable->getTaggedUsersWithGroupSettings($data_id,$album_details->album_group_id);												 
												foreach($tagged_users as $members){
													$permission = 1;
													if((isset($members->media)&&$members->media=='no')){
														$permission =0;
													}
													if($members->album_tag_user_id!=$album_data->added_user_id&&$members->album_tag_user_id!=$album_details->album_user_id &&$permission){
														$page = $post['page'];
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$msg = '<a href="'.$base_url.'album/photo/'.$data_id.'">'.$identity->user_given_name." liked in one image</a>";
														$subject = 'Likes on images';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($members->album_tag_user_id,$msg,2,$subject,$from);
													}
												}
												$this->likeTable = $sm->get('Like\Model\LikeTable');
												$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$data_id,$identity->user_id);	
												//$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$activity_id,$identity->user_id);
												if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
													if ( $ModuleLikesData->is_liked && $ModuleLikesData->likes_counts > 0 ) {
														$data['view'] = "<a href=\"javascript:void(0)\" class=\"album-file-unlikes\" id=\"$data_id\"><img src=\"".$basePath."/public/images/likes-icon.png\" /></a>".($ModuleLikesData->likes_counts);
													} else{
														$data['view'] =	"";
													}												
												}
												else{											
													$data['view'] = '';
												}
												$data['error'] = 0;
												$data['msg'] = 0;
												echo json_encode($data);die();
																					 
											}else{
												 $error[] = 'Oops an error is occured while saving Likes';
											}
										}else{
											$error[] = 'Content is not existing';
										}
									}else{
										$error[] = 'Content is not existing';
									}
								}else{	
									$error[] = 'Unautherized access';
								}
							}else{								 
								$error[] = 'Unautherized access';						 
							}
						break;
						case 'Userfiles':					 
							$data_id = $post['content_id'];
							if($data_id!=''){
								$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
								$album_data = $this->albumDataTable->getalbumdata($data_id);
								if(!empty($album_data)){
									if($this->addLIke($identity->user_id,$SystemTypeData->system_type_id,$data_id)){
										$success[] = 'Likes saved successfully';	
										$album_details = $this->albumDataTable->getAlbumDetailsFromData($data_id);												 
										$this->albumTagTable = $sm->get('Album\Model\AlbumTagTable');
										$tagged_users = $this->albumTagTable->getAllTaggedUsers($data_id);
										$all_notification_users = array();										 
										$all_notification_users[] = $album_details->album_user_id;
										$all_notification_users[] = $album_data->added_user_id;
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
													$msg = '<a href="'.$base_url.'album/photo/'.$data_id.'">'.$identity->user_given_name." likes on your image</a>";
													$subject = 'New Likes in your image';
													$from = 'admin@jeera.com';
													$this->UpdateNotifications($users,$msg,2,$subject,$from);
												}
											}
										}
										$this->likeTable = $sm->get('Like\Model\LikeTable');
										$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$data_id,$identity->user_id);	
										//$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$activity_id,$identity->user_id);
										if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
											if ( $ModuleLikesData->is_liked && $ModuleLikesData->likes_counts > 0 ) {
												$data['view'] = "<a href=\"javascript:void(0)\" class=\"album-file-unlikes\" id=\"$data_id\"><img src=\"".$basePath."/public/images/likes-icon.png\" /></a>".($ModuleLikesData->likes_counts);
											} else{
												$data['view'] =	"";
											}												
										}
										else{											
											$data['view'] = '';
										}
										$data['error'] = 0;
										$data['msg'] = 0;
										echo json_encode($data);die();																					 
									}else{
										 $error[] = 'Oops an error is occured while saving Likes';
									}
								}else{
									$error[] = 'Content is not existing';
								}
							}else{
								$error[] = 'Content is not existing';
							}						 
						break;
						case 'Comment':
							$comment_id = $post['content_id'];
							if($comment_id!=''){
								$this->commentTable = $sm->get('Comment\Model\CommentTable');
								$Comment_data = $this->commentTable->getComment($comment_id);
								if(!empty($Comment_data)){
									if($this->addLIke($identity->user_id,$SystemTypeData->system_type_id,$comment_id)){
										$success[] = 'Likes saved successfully';
										$comment_type = $this->likeTable->getCommentSystemType($Comment_data->comment_system_type_id);
										switch($comment_type->system_type_title){
										case 'Activity':
											$this->activityTable = $sm->get('Activity\Model\ActivityTable');
											$activity_data = $this->activityTable->getActivity($Comment_data->comment_refer_id);
											$joinedMembers = $this->getActivityRsvpTable()->getAllJoinedMembers($activity_data->group_activity_id,$activity_data->group_activity_group_id);
											$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($activity_data->group_activity_group_id);
											foreach($joinedMembers as $members){
												$permission = 1;
												if((isset($members->activity)&&$members->activity!='no')){
													$permission =0;
												}
												if($activity_data->group_activity_owner_user_id!=$members->user_id&&$members->user_id!=$identity->user_id&&$permission){
													$config = $this->getServiceLocator()->get('Config');
													$base_url = $config['pathInfo']['base_url'];
													$msg = $identity->user_given_name." like the comments of activity  <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activity_data->group_activity_id."'>".$activity_data->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
													$subject = 'Likes On activity comment';
													$from = 'admin@jeera.com';
													$this->UpdateNotifications($members->user_id,$msg,2,$subject,$from);
												}
											}
											if($activity_data->group_activity_owner_user_id!=$identity->user_id){ 
												$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
												$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($activity_data->group_activity_owner_user_id,$activity_data->group_activity_group_id);
												$permission = 1;
												if((isset($user_group_settings->activity)&&$user_group_settings->activity!='no')){
													$permission =0;
												}
												if($permission){
													$config = $this->getServiceLocator()->get('Config');
													$base_url = $config['pathInfo']['base_url'];
													$msg = $identity->user_given_name." like the comments of activity <a href='".$base_url."activity/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/".$activity_data->group_activity_id."'>".$activity_data->group_activity_title."</a> under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
													$subject = 'Likes On activity';
													$from = 'admin@jeera.com';
													$this->UpdateNotifications($activity_data->group_activity_owner_user_id,$msg,2,$subject,$from );												 
												}
											}
											break;
											case 'Discussion':
												$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
												$discussion_data = $this->discussionTable->getDiscussion($Comment_data->comment_refer_id);
												$joinedMembers =$this->discussionTable->getDiscussionMembersWithGroupsettings($Comment_data->comment_refer_id,$discussion_data->group_discussion_group_id,$comment_type->system_type_id);
												$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($discussion_data->group_discussion_group_id);
												foreach($joinedMembers as $members){
													$permission = 1;
													if((isset($members->discussion)&&$members->discussion=='no')){
														$permission =0;
													}
													if($members->comment_by_user_id!=$identity->user_id&&$members->comment_by_user_id!=$discussion_data->group_discussion_owner_user_id &&$permission){
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$msg = $identity->user_given_name." commented the discussion -  ".$discussion_data->group_discussion_content." - under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/discussion'>".$subGroupData->group_title."</a>";
														$subject = 'Like on discussion comment';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($members->comment_by_user_id,$msg,2,$subject,$from);
													}	
												}
												if($discussion_data->group_discussion_owner_user_id!=$identity->user_id){ 
													$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
													$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($discussion_data->group_discussion_owner_user_id,$discussion_data->group_discussion_group_id);
													$permission = 1;
													if((isset($user_group_settings->discussion)&&$user_group_settings->discussion=='no')){
														$permission =0;
													}
													if($permission){
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$msg = $identity->user_given_name." commented the discussion -  ".$discussion_data->group_discussion_content." - under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/discussion'>".$subGroupData->group_title."</a>";
														$subject = 'Comments on discussion';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($discussion_data->group_discussion_owner_user_id,$msg,2,$subject,$from);
													}
												}
											break;
											case 'Media':
												$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
												$album_details = $this->albumdataTable->getAlbumDetailsFromData($Comment_data->comment_refer_id);
												$album_data_details = $this->albumdataTable->getalbumdata($Comment_data->comment_refer_id);
												if($album_details->album_user_id!=$identity->user_id){
													$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
													$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($album_details->album_user_id,$album_details->album_group_id);
													$permission = 1;													 
													if((isset($user_group_settings->media)&&$user_group_settings->media=='no')){
														$permission =0;
													}
													if($permission){
														$page = $post['page'];
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$userData = $this->getUserTable()->getUser($tag_id); 
														$msg = '<a href="'.$base_url.'album/photo/'.$Comment_data->comment_refer_id.'">'.$identity->user_given_name."like comments in one image</a>";
														$subject = 'Comment likes on images';
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
														$page = $post['page'];
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$userData = $this->getUserTable()->getUser($tag_id); 
														$msg = '<a href="'.$base_url.'album/photo/'.$Comment_data->comment_refer_id.'">'.$identity->user_given_name."like comments in one image</a>";
														$subject = 'Comment like on images';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($album_data_details->added_user_id,$msg,2,$subject,$from);
													}
												}
												$tagged_users = $this->albumdataTable->getTaggedUsersWithGroupSettings($Comment_data->comment_refer_id,$album_details->album_group_id);												 
												foreach($tagged_users as $members){
													$permission = 1;
													if((isset($members->media)&&$members->media=='no')){
														$permission =0;
													}
													if($members->album_tag_user_id!=$album_data_details->added_user_id&&$members->album_tag_user_id!=$album_details->album_user_id &&$permission){
														$page = $post['page'];
														$config = $this->getServiceLocator()->get('Config');
														$base_url = $config['pathInfo']['base_url'];
														$msg = '<a href="'.$base_url.'album/photo/'.$Comment_data->comment_refer_id.'">'.$identity->user_given_name." like comments in one image</a>";
														$subject = 'Comment like on images';
														$from = 'admin@jeera.com';
														$this->UpdateNotifications($members->album_tag_user_id,$msg,2,$subject,$from);
													}
												}
											break;
											case 'Userfiles':
												$this->albumdataTable = $sm->get('Album\Model\AlbumDataTable');
												$album_details = $this->albumdataTable->getAlbumDetailsFromData($Comment_data->comment_refer_id);
												$album_data_details = $this->albumdataTable->getalbumdata($Comment_data->comment_refer_id);
												$this->albumTagTable = $sm->get('Album\Model\AlbumTagTable');
												$tagged_users = $this->albumTagTable->getAllTaggedUsers($Comment_data->comment_refer_id);
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
															$msg = '<a href="'.$base_url.'album/photo/'.$Comment_data->comment_refer_id.'">'.$identity->user_given_name." likes on your image</a>";
															$subject = 'New Likes in your image';
															$from = 'admin@jeera.com';
															$this->UpdateNotifications($users,$msg,2,$subject,$from);
														}
													}
												}
											break;
										}
										$this->likeTable = $sm->get('Like\Model\LikeTable');
										$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$comment_id,$identity->user_id);	
										//$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$comment_id,$identity->user_id);
										if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
											if ( $ModuleLikesData->is_liked && $ModuleLikesData->likes_counts > 0 ) {
												$data['view'] = "<a href=\"javascript:void(0)\" class=\"comments-unlikes\" id=\"$comment_id\"><img src=\"".$basePath."/public/images/likes-icon.png\" /></a>".($ModuleLikesData->likes_counts);
											} else{
												$data['view'] =	"";
											}												
										}
										else{											
											$data['view'] = '';
										}
										$data['error'] = 0;
										$data['msg'] = 0;
										echo json_encode($data);die();
									}else{
										 $error[] = 'Oops an error is occured while saving Likes';
									}									
								}else{
									$error[] = 'Content is not existing';
								}
							}
							else{
								$error[] = 'Content is not existing';
							}
						break;
						default:
						$error[] = 'You don\'t habe the permissions to do this';						
					}
				}
				else{
					$error[] = 'Likes for this section currently unavailable';
				}
			}
			else{
				$error[] = 'Unautherized access';
			}
		}
		else{	
			$error[] = 'Your session has been expired';
		}
		$data['error'] = 1;
		$data['view']	= '';
		$data['msg'] = $error[0];
		echo json_encode($data);die();	
	}
	public function addLIke($user_id,$type,$content_id){
		$sm = $this->getServiceLocator();
		$this->remoteAddr = $sm->get('ControllerPluginManager')->get('GenericPlugin')->getRemoteAddress();
		$Like = new Like();
		$this->likeTable = $sm->get('Like\Model\LikeTable');
		$likeData = $this->likeTable->LikeExistsCheck($type,$content_id,$user_id);
		$LikesData = array();
		$LikesData['like_system_type_id'] = $type;
		$LikesData['like_by_user_id'] = $user_id;
		$LikesData['like_refer_id'] = $content_id;
		$LikesData['like_status'] = 1;		
		$LikesData['like_added_ip_address'] =  $this->remoteAddr;
		$Like->exchangeArray($LikesData);
		$insertedLikesId = $this->likeTable->saveLike($Like);
		if($insertedLikesId)
		return true;
		else
		return false;
	}	
	#This will load all Subgroups Of Group   
	public function UnLikesAction() {
		$auth = new AuthenticationService();
		$error = array();
		if ($auth->hasIdentity()) {
			$request   = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$identity = $auth->getIdentity();
				$SystemType = $post['type'];
				$identity = $auth->getIdentity();
				$sm = $this->getServiceLocator();	
				$basePath = $sm->get('Request')->getBasePath();								
				$this->groupTable = $sm->get('Groups\Model\GroupsTable');
				$SystemTypeData = $this->groupTable->fetchSystemType($SystemType);
				if(!empty($SystemTypeData)){
					switch($SystemType){
						case 'Discussion':
							$group_id = $post['group_id'];
							$planet_id = $post['planet'];
							if($group_id!=''&&$planet_id!=''){
								$SubGroupData = $this->groupTable->getSubGroupForSEO($planet_id);
								if(!empty($SubGroupData)){
									$discussion_id = $post['content_id'];
									if($discussion_id!=''){
										$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
										$discussion_data = $this->discussionTable->getDiscussion($discussion_id);
										if(!empty($discussion_data)){
											$this->likeTable = $sm->get('Like\Model\LikeTable');
											$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$discussion_id,$identity->user_id);
											if ( !empty( $likeData->like_id ) ) {
												if( $this->likeTable->deleteLikeByReference($SystemTypeData->system_type_id,$likeData->like_by_user_id,$discussion_id)){
													$success[] = 'Unliked successfully';
													$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$discussion_id,$identity->user_id);	
													//$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$discussion_id,$identity->user_id);
													if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
													$data['view'] = "<a id=\"$discussion_id\" class=\"discussion-likes\" href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>".($ModuleLikesData->likes_counts);									 
													}
													else{											
													$data['view'] =	"<a id=\"$discussion_id\" class=\"discussion-likes\"  href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>".($ModuleLikesData->likes_counts);					
													}
													$data['error'] = 0;
													$data['msg'] = 0;
													echo json_encode($data);die();													
												}else{
													$error[] = 'Oops an error is occured while saving Likes';
												}
											}
											else{
												$error[] = 'Content is not existing';
											}
												
										}
										else{
											$error[] = 'Content is not existing';
										}
									}else{
										$error[] = 'Content is not existing';
									}									
								}
								else{
									$error[] = 'Unautherized access';	
								}
							}
							else{
								$error[] = 'Unautherized access';
							}
						break;
						case 'Activity':
							$group_id = $post['group_id'];
							$planet_id = $post['planet'];
							if($group_id!=''&&$planet_id!=''){
								$SubGroupData = $this->groupTable->getSubGroupForSEO($planet_id);
								if(!empty($SubGroupData)){
									$activity_id = $post['content_id'];
									if($activity_id!=''){
										$this->activityTable = $sm->get('Activity\Model\ActivityTable');
										$activity_data = $this->activityTable->getActivity($activity_id);
										if(!empty($activity_data)){
											$this->likeTable = $sm->get('Like\Model\LikeTable');
											$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$activity_id,$identity->user_id);
											if ( !empty( $likeData->like_id ) ) {
												if( $this->likeTable->deleteLikeByReference($SystemTypeData->system_type_id,$likeData->like_by_user_id,$activity_id)){
													$success[] = 'Unliked successfully';
													$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity_id,$identity->user_id);	
													$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$activity_id,$identity->user_id);
													if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
													$data['view'] = "<a id=\"$activity_id\" class=\"activity-likes\" href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>".($ModuleLikesData->likes_counts);											 
													}
													else{											
													$data['view'] =	"<a id=\"$activity_id\" class=\"activity-likes\"  href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>";
													}
													$data['error'] = 0;
													$data['msg'] = 0;
													echo json_encode($data);die();
													
												}else{
													$error[] = 'Oops an error is occured while saving Likes';
												}
											}
											else{
												$error[] = 'Content is not existing';
											}												
										}
										else{
											$error[] = 'Content is not existing';
										}
									}else{
										$error[] = 'Content is not existing';
									}									
								}
								else{
									$error[] = 'Unautherized access';	
								}
							}
							else{
								$error[] = 'Unautherized access';
							}
						break;
						case 'Media':
							$group_id = $post['group_id'];
							$planet_id = $post['planet'];
							if($group_id!=''&&$planet_id!=''){
								$SubGroupData = $this->groupTable->getSubGroupForSEO($planet_id);
								if(!empty($SubGroupData)){
									$data_id = $post['content_id'];
									if($data_id!=''){
										$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
										$album_data = $this->albumDataTable->getalbumdata($data_id);
										if(!empty($album_data)){
											$this->likeTable = $sm->get('Like\Model\LikeTable');
											$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$data_id,$identity->user_id);
											if ( !empty( $likeData->like_id ) ) {
												if( $this->likeTable->deleteLikeByReference($SystemTypeData->system_type_id,$likeData->like_by_user_id,$data_id)){
													$success[] = 'Unliked successfully';
													$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$data_id,$identity->user_id);	
													$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$data_id,$identity->user_id);
													if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
													$data['view'] = "<a id=\"$data_id\" class=\"album-file-likes\" href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>".($ModuleLikesData->likes_counts);											 
													}
													else{											
													$data['view'] =	"<a id=\"$data_id\" class=\"album-file-likes\"  href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>";
													}
													$data['error'] = 0;
													$data['msg'] = 0;
													echo json_encode($data);die();
													
												}else{
													$error[] = 'Oops an error is occured while saving Likes';
												}
											}
											else{
												$error[] = 'Content is not existing';
											}												
										}
										else{
											$error[] = 'Content is not existing';
										}
									}else{
										$error[] = 'Content is not existing';
									}									
								}
								else{
									$error[] = 'Unautherized access';	
								}
							}
							else{
								$error[] = 'Unautherized access';
							}
						break;
						case 'Userfiles':							 
							$data_id = $post['content_id'];
							if($data_id!=''){
								$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
								$album_data = $this->albumDataTable->getalbumdata($data_id);
								if(!empty($album_data)){
									$this->likeTable = $sm->get('Like\Model\LikeTable');
									$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$data_id,$identity->user_id);
									if ( !empty( $likeData->like_id ) ) {
										if( $this->likeTable->deleteLikeByReference($SystemTypeData->system_type_id,$likeData->like_by_user_id,$data_id)){
											$success[] = 'Unliked successfully';
											$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$data_id,$identity->user_id);	
											$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$data_id,$identity->user_id);
											if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
											$data['view'] = "<a id=\"$data_id\" class=\"album-file-likes\" href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>".($ModuleLikesData->likes_counts);											 
											}
											else{											
											$data['view'] =	"<a id=\"$data_id\" class=\"album-file-likes\"  href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>";
											}
											$data['error'] = 0;
											$data['msg'] = 0;
											echo json_encode($data);die();
											
										}else{
											$error[] = 'Oops an error is occured while saving Likes';
										}
									}
									else{
										$error[] = 'Content is not existing';
									}												
								}
								else{
									$error[] = 'Content is not existing';
								}
							}else{
								$error[] = 'Content is not existing';
							}									
								 
						break;
						case 'Comment':
							$comment_id = $post['content_id'];
							if($comment_id!=''){
								$this->commentTable = $sm->get('Comment\Model\CommentTable');
								$Comment_data = $this->commentTable->getComment($comment_id);
								if(!empty($Comment_data)){
									$this->likeTable = $sm->get('Like\Model\LikeTable');
									$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$comment_id,$identity->user_id);
									if ( !empty( $likeData->like_id ) ) {
										if( $this->likeTable->deleteLikeByReference($SystemTypeData->system_type_id,$likeData->like_by_user_id,$comment_id)){
											$success[] = 'Unliked successfully';											
											$ModuleLikesData = $this->likeTable->fetchLikesCountByReference($SystemTypeData->system_type_id,$comment_id,$identity->user_id);	
											$likeData = $this->likeTable->LikeExistsCheck($SystemTypeData->system_type_id,$comment_id,$identity->user_id);
											if (isset($ModuleLikesData) && !empty($ModuleLikesData)) {
											$data['view'] = "<a id=\"$comment_id\" class=\"comments-likes\" href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>".($ModuleLikesData->likes_counts);											 
											}
											else{											
											$data['view'] =	"<a id=\"$comment_id\" class=\"comments-likes\"  href=\"javascript:void(0)\"><img src=\"".$basePath."/public/images/nolike-icon.png\" /></a>";
											}
											$data['error'] = 0;
											$data['msg'] = 0;
											echo json_encode($data);die();
										}else{
											$error[] = 'Oops an error is occured while saving Likes';
										}										
									}
									else{
										$error[] = 'Content is not existing';
									}
								}else{									
								}
							}
							else{
								$error[] = 'Content is not existing';
							}
						break;
						default:
						$error[] = 'You don\'t habe the permissions to do this';
						
					}
				}else{
					$error[] = 'Likes for this section currently unavailable';
				}
			}
			else{
				$error[] = 'Unautherized access';
			}
		}
		else{	
			$error[] = 'Your session has been expired';
		}
		$data['error'] = 1;
		$data['view']	= '';
		$data['msg'] = $error[0];
		echo json_encode($data);die();		
	}	
	#This will load all Subgroups Of Group   
	public function LikesUsersListAction() {	
		$error = array();	#Error variable
		$success = array();	#success message variable
		$GroupId = "";
		$SubGroupId = "";	//This will hold the galaxy id
		$GroupReferId = ""; 
		$SystemTypeId = ""; 				
		$userData = array();	//this will hold data from y2m_user table
		$groupData = array();//this will hold the Galaxy data
		$SubGroupData = array();//this will hold the Planet data
		$LikesUsersListData = array();//this will hold the Planet data		
		$GroupId = $this->params('group_id'); 				
		$SubGroupId = $this->params('sub_group_id'); 
		$GroupReferId = $this->params('group_refer_id');
		$SystemTypeId = $this->params('system_type_id'); 	
		#db connectivity
		$sm = $this->getServiceLocator();
		$this->remoteAddr = $sm->get('ControllerPluginManager')->get('GenericPlugin')->getRemoteAddress();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');		
		try {
			$request   = $this->getRequest();
			$auth = new AuthenticationService();	
			$identity = null; 			
			if ($auth->hasIdentity()) {				
				// Identity exists; get it
				$identity = $auth->getIdentity();				
				#fetch the user Galaxy
				$this->userTable = $sm->get('User\Model\UserTable');				
				#check the identity against the DB
				$userData = $this->userTable->getUser($identity->user_id);			
				if(isset($userData->user_id) && !empty($userData->user_id) && isset($GroupId) && !empty($GroupId) && isset($SubGroupId) && !empty($SubGroupId)) {							
					$this->groupTable = $sm->get('Group\Model\GroupTable');					
					#get Group Info
					$SubGroupData = $this->groupTable->getSubGroupForSEO($SubGroupId);					
					#fetch the Galaxy Info
					$groupData = $this->groupTable->getGroup($SubGroupData->group_parent_group_id);	
					$SystemTypeData = $this->groupTable->fetchSystemType($SystemTypeId);					
					$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');					
					#fetch the Discussion planet details
					$GroupModuleData = $this->discussionTable->getDiscussion($GroupReferId);					
					#add discussion code
					if(isset($SubGroupData->group_id) && !empty($SubGroupData->group_id) && isset($SubGroupData->group_parent_group_id) && !empty($SubGroupData->group_parent_group_id) && isset($GroupModuleData->group_discussion_group_id) && !empty($GroupModuleData->group_discussion_group_id) ) {					
						$this->likeTable = $sm->get('Like\Model\LikeTable');	
						#fetch the Discussion Like of planet details
						$LikesUsersListData = $this->likeTable->fetchLikesUsersByReference($SystemTypeData->system_type_id,$GroupReferId);						
					}
				}
			
			} //if ($auth->hasIdentity()) 			
		} catch (\Exception $e) {
			echo "Caught exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";			 
		}
		//echo $ModuleLikesData->likes_counts;		
		$viewModel = new ViewModel(array('userData' => $userData,'groupData' => $groupData,'SubGroupData' => $SubGroupData, 'LikesUsersListData' => $LikesUsersListData, 'Group_Refer_Id' => $GroupReferId,'System_Type_Id' => $SystemTypeId, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;	
	}	
	#This will load all Subgroups Of Group   
	public function CommentsLikesUsersListAction() {	
		$error = array();	#Error variable
		$success = array();	#success message variable
		$GroupId = "";
		$SubGroupId = "";	//This will hold the galaxy id
		$GroupReferId = ""; 
		$SystemTypeId = ""; 				
		$userData = array();	//this will hold data from y2m_user table
		$groupData = array();//this will hold the Galaxy data
		$SubGroupData = array();//this will hold the Planet data
		$LikesUsersListData = array();//this will hold the Planet data		
		$GroupId = $this->params('group_id'); 				
		$SubGroupId = $this->params('sub_group_id'); 
		$GroupReferId = $this->params('group_refer_id');
		$SystemTypeId = $this->params('system_type_id'); 	
		$SubSystemTypeId = $this->params('sub_system_type_id'); 	
		#db connectivity
		$sm = $this->getServiceLocator();
		$this->remoteAddr = $sm->get('ControllerPluginManager')->get('GenericPlugin')->getRemoteAddress();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');		
		try {	
			$request   = $this->getRequest();
			$auth = new AuthenticationService();	
			$identity = null;   			
			if ($auth->hasIdentity()) {				
				// Identity exists; get it
				$identity = $auth->getIdentity();				
				#fetch the user Galaxy
				$this->userTable = $sm->get('User\Model\UserTable');				
				#check the identity against the DB
				$userData = $this->userTable->getUser($identity->user_id);		
				if(isset($userData->user_id) && !empty($userData->user_id) && isset($GroupId) && !empty($GroupId) && isset($SubGroupId) && !empty($SubGroupId)) {				
					$this->groupTable = $sm->get('Group\Model\GroupTable');					
					#get Group Info
					$SubGroupData = $this->groupTable->getSubGroupForSEO($SubGroupId);
					#fetch the Galaxy Info
					$groupData = $this->groupTable->getGroup($SubGroupData->group_parent_group_id);						
					$this->commentTable = $sm->get('Comment\Model\CommentTable');
					$SystemTypeData = $this->groupTable->fetchSystemType($SubSystemTypeId);					
					$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');					
					#fetch the Discussion planet details
					$GroupDiscussionCommentsData = $this->commentTable->getComment($GroupReferId);					
					#add discussion code
					if(isset($SubGroupData->group_id) && !empty($SubGroupData->group_id) && isset($SubGroupData->group_parent_group_id) && !empty($SubGroupData->group_parent_group_id) && isset($GroupDiscussionCommentsData->comment_id) && !empty($GroupDiscussionCommentsData->comment_id) ) {						
						$this->likeTable = $sm->get('Like\Model\LikeTable');	
						#fetch the Discussion Like of planet details
						$LikesUsersListData = $this->likeTable->fetchLikesUsersByReference($SystemTypeData->system_type_id,$GroupReferId);	
					}
				}			
			} //if ($auth->hasIdentity()) 			
		} catch (\Exception $e) {
			echo "Caught exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";			 
		}		
		
		$viewModel = new ViewModel(array('userData' => $userData,'groupData' => $groupData,'SubGroupData' => $SubGroupData, 'LikesUsersListData' => $LikesUsersListData, 'Group_Refer_Id' => $GroupReferId,'System_Type_Id' => $SystemTypeId, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;	
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
	public function getActivityRsvpTable(){
        if (!$this->activityRsvpTable) {
            $sm = $this->getServiceLocator();
            $this->activityRsvpTable = $sm->get('Activity\Model\ActivityRsvpTable');
        }
        return $this->activityRsvpTable;
    }
	public function getGroupTable(){
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
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
