<?php
####################Spam Controller #################################

namespace Spam\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;	//Needed for checking User session
use Zend\Authentication\Adapter\DbTable as AuthAdapter;	//Db adapter
use Zend\Crypt\BlockCipher;		# For encryption
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/
 
#Group classs
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
use Spam\Model\Spam;  
use Spam\Model\SpamTable; 
use \Exception;		#Exception class for handling exception
#Group Suggestion add form
use Spam\Form\SpamForm;
use Notification\Model\UserNotification;
use Notification\Model\UserNotificationTable;

class SpamController extends AbstractActionController
{     
	protected $userTable;
	protected $groupTable;
	protected $userGroupTable;

	protected $photoTable = ""; 
	protected $activityTable = ""; 
	protected $discussionTable = "";
	protected $commentTable = ""; 
	protected $albumTable = ""; 
	protected $SpamTable ="";
	
	public function __construct(){
		return $this;
	}
	
	#this function will load the css and javascript need for perticular action
	protected function getViewHelper($helperName)
	{
    	return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}
    	
	public function indexAction(){
		return $this;
	}
	
	#This will load all Subgroups Of Group   
	function SpamsAction() {	
		$error = array();	#Error variable
		$success = array();	#success message variable
		$GroupId = "";
		$SubGroupId = "";	//This will hold the galaxy id
		$GroupReferId = ""; 
		$SystemTypeId = ""; 				
		$userData = array();	//this will hold data from y2m_user table
		$groupData = array();//this will hold the Galaxy data
		$SubGroupData = array();//this will hold the Planet data
		$SpamData = array();
		$spam_problem_id =1;
		
		$GroupId = $this->params('group_id'); 				
		$SubGroupId = $this->params('sub_group_id'); 
		$GroupReferId = $this->params('group_refer_id');
		$SystemTypeId = $this->params('system_type_id'); 		
	
		#db connectivity
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		
		try {				
			
			$request   = $this->getRequest();
			
			if ($request->isPost()) {
			
				$auth = new AuthenticationService();	
				$identity = null;
				
				// Identity exists; get it
				$identity = $auth->getIdentity();
				
				#fetch the user Galaxy
				$this->userTable = $sm->get('User\Model\UserTable');
				
				#check the identity against the DB
				$userData = $this->userTable->getUser($identity->user_id);	
							
				if ($auth->hasIdentity()) {
		
					if(isset($userData->user_id) && !empty($userData->user_id) && isset($GroupId) && !empty($GroupId) && isset($SubGroupId) && !empty($SubGroupId)) {				
					
						$servParam = $request->getServer();
						
						$remoteAddr = $servParam->get('REMOTE_ADDR');
						
						$this->groupTable = $sm->get('Groups\Model\GroupsTable');
					
						#get Group Info
						$SubGroupData = $this->groupTable->getSubGroupForName($SubGroupId);

						#fetch the Galaxy Info
						if(isset($SubGroupData->group_parent_group_id)) $groupData = $this->groupTable->getGroup($SubGroupData->group_parent_group_id);	
						
						$SystemTypeData = $this->groupTable->fetchSystemType($SystemTypeId);
						
						$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
						
						#fetch the Discussion planet details
						$GroupDiscussionData = $this->discussionTable->getDiscussion($GroupReferId);
					
						if(isset($SubGroupData->group_id) && !empty($SubGroupData->group_id) && isset($SubGroupData->group_parent_group_id) && !empty($SubGroupData->group_parent_group_id) && isset($GroupDiscussionData->group_discussion_group_id) && !empty($GroupDiscussionData->group_discussion_group_id) ) {						
								
							$Spam = new Spam();
							
							$form = new SpamForm($SystemTypeData->system_type_id,1,$GroupReferId);
							
							$form->setData($request->getPost());
								
							 if ($form->isValid()) {
							 
								$this->SpamTable = $sm->get('Spam\Model\SpamTable');
								
								$insertedSpamsId = "";
																
								$SpamData = $this->SpamTable->SpamExistsCheck($SystemTypeData->system_type_id,$spam_problem_id,$GroupReferId,$userData->user_id);

								if ( empty( $SpamData->spam_id ) ) {
								
									#add the Spam
									$post = $request->getPost();						
									#get the Spams content
									$spam_report_user_id = $userData->user_id;
																					
									#save the Spams
									$SpamsData = array();
									$SpamsData['spam_system_type_id'] = $SystemTypeData->system_type_id;
									$SpamsData['spam_problem_id'] = $spam_problem_id;
									$SpamsData['spam_other_content'] = $post->get('spam_other_content');
									$SpamsData['spam_added_ip_address'] = $remoteAddr;
									$SpamsData['spam_report_user_id'] = $spam_report_user_id;
									$SpamsData['spam_target_user_id'] = $GroupDiscussionData->group_discussion_owner_user_id;
									$SpamsData['spam_refer_id'] = $GroupReferId;
									
									#lets Save the Spam
									$Spam->exchangeArray($SpamsData);
									
									$insertedSpamsId = $this->SpamTable->saveSpam($Spam); 
									
									#send the Notification to User
									$UserGroupNotificationData = array();						
									$UserGroupNotificationData['user_notification_user_id'] = $GroupDiscussionData->group_discussion_owner_user_id;								
									$UserGroupNotificationData['user_notification_content'] = "Your group discussion is marked as spam <b>".$GroupDiscussionData->group_discussion_content."</b>";						
									$UserGroupNotificationData['user_notification_notification_type_id'] = "7";
									$UserGroupNotificationData['user_notification_status'] = 1;															 
																	
									#lets Save the User Notification
									$UserGroupNotificationSaveObject = new UserNotification();
									$this->UserNotificationTable = $sm->get('Notification\Model\UserNotificationTable');
									$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);							
									$insertedUserGroupNotificationId = "";	#this will hold the latest inserted id value
									$insertedUserGroupNotificationId = $this->UserNotificationTable->saveUserNotification($UserGroupNotificationSaveObject);

								}
								if(isset($insertedSpamsId) && !empty($insertedSpamsId)) {
									 $success[] = 'Reported Spam';
								}
								else {
									 $success[] = 'Oops an error has occurred while reporting spam';	
								}
								
								$SpamData = $this->SpamTable->SpamExistsCheck($SystemTypeData->system_type_id,$spam_problem_id,$GroupReferId,$userData->user_id);
							
								$viewModel = new ViewModel(array('form' => $form,'userData' => $userData,'groupData' => $groupData,'SubGroupData' => $SubGroupData,'Spams_exist_check_for_user'=>$SpamData,'Group_Refer_Id' => $GroupReferId,'System_Type_Id' => $SystemTypeId, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()));
								$viewModel->setTerminal($request->isXmlHttpRequest());
								return $viewModel;
							
							} 
								
						} 	
					}
				}
			
			} 	
		} catch (\Exception $e) {
			echo "Caught exception: " . get_class($e) . "\n";
			echo "Message: " . $e->getMessage() . "\n";
			return $e;
		}
		
	}
	
}
