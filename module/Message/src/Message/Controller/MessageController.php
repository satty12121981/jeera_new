<?php
namespace Message\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model

#session
use Zend\Session\Container; // We need this when using sessions
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Stdlib\Hydrator;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/


#Message class
use User\Model\User;
use Message\Model\Message;
use Message\Form\MessageForm;
use Message\Form\MessageFilter;
use User\Model\UserFriendTable;

use Notification\Model\UserNotification;
use Photo\Model\Photo;
use Message\Model\MessageAttachment;

class MessageController extends AbstractActionController
{

    protected $userTable;
    protected $userFriendTable;
    protected $userNotificationTable;
    protected $notificationTypeTable;
    protected $photoTable;
	protected $activityTable;
	protected $MessageAttachmentTable;

    public function indexAction(){
	
       $auth = new AuthenticationService();
	   $sm = $this->getServiceLocator();	
	   $limit = 10;
	   $offset = 0;
	   $usersWithMessagesData = array();
       $userData = array();
	   $viewModel = new ViewModel();
	   if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$this->layout()->page = 'Messages';
			$user_id = $identity->user_id;
			$profilepic = $this->getUserTable()->getUserProfilePic($user_id);
			$activity = $this->getActivityTable()->getEventCalendarJason($user_id); 
			foreach($profilepic as $pic){$identity->profile_pic = $pic->biopic;}
			$identity->activity = $activity;
			$this->userTable = $sm->get('User\Model\UserTable');
            $userData = $this->userTable->getUser($identity->user_id);
			if( $userData->user_id ) {
				$this->messageTable = $sm->get('Message\Model\MessageTable');
				$usersWithMessagesData  = $this->messageTable->myMessages($userData->user_id,$limit,$offset);
              //  $usersWithMessagesData = $this->messageTable->getMessageOfUserFromUsers($userData->user_id,'sent');
			}
			$planetSugessions = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'planetSuggestions',								 
								));
			$viewModel->addChild($planetSugessions, 'planetSugessions');
			$viewModel->setVariable('userData',$userData);
			$viewModel->setVariable('usersWithMessagesData',$usersWithMessagesData);              
            return $viewModel;
	   }
	   else{	
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));			
	   }
    } 
    protected function messageslistAction()
    {        
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$messages = array();
		$error = array();
		$userMessagesData = array();
		$page_limit = 5;
		$offset = 0;
		$return_array = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			if ($request->isPost()) {
				$this->userTable = $sm->get('User\Model\UserTable');
				$post = $request->getPost();
				$user =$post->get('user');
				if($user){
					$this->userFriendTable = $sm->get('User\Model\UserFriendTable');
					$this->messageTable = $sm->get('Message\Model\MessageTable');
					$this->messageTable->setStatusReadOfSpecic($identity->user_id,$user);
					$this->MessageAttachmentTable =  $sm->get('Message\Model\MessageAttachmentTable');						
					$UserConnectionData = $this->userFriendTable->CheckFriendStatusBetweenMembers($identity->user_id,$user); 
					if(!empty($UserConnectionData)&&$UserConnectionData->user_friend_id){
						$userMessagesData = array_reverse($this->messageTable->fetchAllConversations($identity->user_id,$user,$offset,$page_limit));
						
						foreach($userMessagesData as $messagedata){
							$return_array[] = array('message'=>$messagedata,
											'message_attachments'=>$this->MessageAttachmentTable->getAttachments($messagedata['user_message_id'])
											);
							 
						}
					}
					else{
						$error[] = "This opponent is not existing in your friend list";	
					}
				}else{
					$error[] = "This opponent is not existing in your friend list";					 
				}
			}
			else{
				$error[] = "Invalid access!";
			}
		}else{
			$error[] = "Your session has been expired..! Please try again after login!";
		}
		$config = $this->getServiceLocator()->get('Config');
		$attachment_path =  $config['pathInfo']['UploadPath'].Message::MESSAGE_FILE_PATH;
		$viewModel = new ViewModel(array( 'error' => $error,'userMessagesData' => $return_array,'reciever_id'=>$user,'attachment_path'=>$attachment_path));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
    }    
    protected function messagesendaction(){    
		$sm = $this->getServiceLocator();
        $auth = new AuthenticationService();
		$success = array();
		$error = array();
        $identity = null;
		$return_array = array();
		$insertedmessageId		 = '';
		if ($auth->hasIdentity()) { 
			$config = $this->getServiceLocator()->get('Config');
			$this->remoteAddr = $sm->get('ControllerPluginManager')->get('GenericPlugin')->getRemoteAddress();
			$request = $this->getRequest();
			if($request->isPost()){
				if($request->getPost()->get('messsage')!=''||(isset($_FILES)&&!empty($_FILES))){
					$identity = $auth->getIdentity();
					$reciever =  $request->getPost()->get('reciever_id');
					if($reciever!=''||$reciever!=0){
						$this->userFriendTable = $sm->get('User\Model\UserFriendTable');
						$UserConnectionData = $this->userFriendTable->CheckFriendStatusBetweenMembers($identity->user_id,$reciever); 
						if(!empty($UserConnectionData)&&$UserConnectionData->user_friend_id){ 
							 $message_data = array();
							 $message_data = array(
								'user_message_receiver_id' =>$reciever,
								'user_message_sender_id' =>$identity->user_id,
								'user_message_content'=>$request->getPost()->get('messsage'),
								'user_message_added_ip_address'=>$this->remoteAddr,
								'user_message_status'=>'sent',
								'user_message_sender_deleted' => 0,
								'user_message_receiver_deleted' => 0,
								'user_message_sender_viewed' => 0,
								'user_message_receiver_viewed' => 0,
								'user_message_type'=>'normal'
								);
							$message = new Message();
							$message->exchangeArray($message_data);
							$this->messageTable = $sm->get('Message\Model\MessageTable');
							$insertedmessageId = $this->messageTable->saveMessage($message); 
							$this->MessageAttachmentTable =  $sm->get('Message\Model\MessageAttachmentTable');
							if($insertedmessageId){ 
								
								if(isset($_FILES)&&!empty($_FILES)){ 
									
									$imagefiles  = $_FILES;
									$messageattachment = new MessageAttachment();								  
									 
									foreach($imagefiles as $attachments){
										$filename = '';
										$filename = time().$attachments['name'];
										move_uploaded_file($attachments["tmp_name"], $config['pathInfo']['UploadPath'].Message::MESSAGE_FILE_PATH."/". $filename);
										if((($attachments["type"] == "image/gif") || ($attachments["type"] == "image/jpeg") || ($attachments["type"] == "image/jpg") || ($attachments["type"] == "image/pjpeg") || ($attachments["type"] == "image/x-png") || ($attachments["type"] == "image/png"))){
											$photoData['attachment_type'] = 'image';
										}else{
											$photoData['attachment_type'] = 'doc';
										}
										$photoData['attachment_element'] =$filename;
										$photoData['message_id'] =$insertedmessageId;
										$messageattachment->exchangeArray($photoData); 
										$insertedFileId = $this->MessageAttachmentTable->saveAttachment($messageattachment);
									}
									 
									 
								}
							}else{	
								$error[] = "Unable to process..";	
							}
						}
						else{
							$error[] = "This opponent is not existing in your friend list";	
						}
					}else{
						$error[] = 'Unable to process';
					}
				}
			}else{
				$error[] = 'Unable to process';
			}
		}
		else{
			$error[] = 'Sorry! Your session expired!';
		}
		if($insertedmessageId != ''){
			$return_array['message'] = $this->messageTable->getSingleMessage($insertedmessageId);
			$return_array['message_attachments'] = $this->MessageAttachmentTable->getAttachments($insertedmessageId);
		}
		$viewModel = new ViewModel(array('userMessagesData' => $return_array,'error' => $error)); 
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function ajxLoadmoreMessageAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$messages = array();
		$error = array();
		$userMessagesData = array();
		$page_limit = 5;
		$offset = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			if ($request->isPost()) {
				$this->userTable = $sm->get('User\Model\UserTable');
				$post = $request->getPost();
				$user = $post->get('user');				 
				$page =$post->get('page');
				if(!$page)
					$page = 0;	
				$offset	= $page*5;		 
				if($user){
					$this->userFriendTable = $sm->get('User\Model\UserFriendTable');
					$this->messageTable = $sm->get('Message\Model\MessageTable');		
					$this->MessageAttachmentTable =  $sm->get('Message\Model\MessageAttachmentTable');						
					$UserConnectionData = $this->userFriendTable->CheckFriendStatusBetweenMembers($identity->user_id,$user); 
					if(!empty($UserConnectionData)&&$UserConnectionData->user_friend_id){
						$userMessagesData = array_reverse($this->messageTable->fetchAllConversations($identity->user_id,$user,$offset,$page_limit));
						$return_array = array();
						foreach($userMessagesData as $messagedata){
							$return_array[] = array('message'=>$messagedata,
											'message_attachments'=>$this->MessageAttachmentTable->getAttachments($messagedata['user_message_id'])
											);
							 
						}
					}
					else{
						$error[] = "This opponent is not existing in your friend list";	
					}
				}else{
					$error[] = "This opponent is not existing in your friend list";					 
				}
			}
			else{
				$error[] = "Invalid access!";
			}
		}else{
			$error[] = "Your session has been expired..! Please try again after login!";
		}
		$config = $this->getServiceLocator()->get('Config');
		$attachment_path =  $config['pathInfo']['UploadPath'].Message::MESSAGE_FILE_PATH;
		$viewModel = new ViewModel(array( 'error' => $error,'userMessagesData' => $return_array,'reciever_id'=>$user,'attachment_path'=>$attachment_path));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
    
    protected function messagesdeleteAction() {
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$msg = '';
		$error = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			 if ($request->isPost()) {
				$type =  $this->getRequest()->getPost('type');
				switch($type){
					case "All":
						$user = $this->getRequest()->getPost('user');
						if($user){
							$this->userFriendTable = $sm->get('User\Model\UserFriendTable');
							$this->messageTable = $sm->get('Message\Model\MessageTable');		
							$this->MessageAttachmentTable =  $sm->get('Message\Model\MessageAttachmentTable');						
							$UserConnectionData = $this->userFriendTable->CheckFriendStatusBetweenMembers($identity->user_id,$user); 
							if(!empty($UserConnectionData)&&$UserConnectionData->user_friend_id){
								 $deletedIds = $this->messageTable->updateMessage($identity->user_id,$user);
								  $msg = 'successfully removed the conversations';
							}
							else{
								$msg = "This opponent is not existing in your friend list";	
								$error++;
							}
						}
						else{
							$msg = 'Given usesr is not exist in your friend list';
							$error++;
						}
					break;
					case "Selected":
						$user = $this->getRequest()->getPost('user');
						if($user){
							$mesgid = $this->getRequest()->getPost('messages');
							if(!empty($mesgid)){
								$this->userFriendTable = $sm->get('User\Model\UserFriendTable');
								$this->messageTable = $sm->get('Message\Model\MessageTable');		
								$this->MessageAttachmentTable =  $sm->get('Message\Model\MessageAttachmentTable');						
								$UserConnectionData = $this->userFriendTable->CheckFriendStatusBetweenMembers($identity->user_id,$user); 
								if(!empty($UserConnectionData)&&$UserConnectionData->user_friend_id){
									 $deletedIds = $this->messageTable->updateMessage($identity->user_id,$user,$mesgid);
									 $msg = 'successfully removed the selected conversations';
								}
								else{
									$msg= "This opponent is not existing in your friend list";	
									$error++;
								}
							}
							else{
								$msg = "Nothing selected for delete";	
								$error++;
							}
						}else{
							$msg = 'Given usesr is not exist in your friend list';
							$error++;
						}
					break;
					 
					default:
					$msg = 'Invalid process!';
					$error++;
				}
			 }
			 else{
				$msg = 'Invalid access!';
				$error++;
			 }
		}
		else{
			$msg = 'Your session has been expired..! Please try again after login!';
			$error++;
		}
		$return_array = array();
		if($error==0){
			$return_array['error'] = 0;
			$return_array['msg'] = $msg;
		}
		else{
			$return_array['error'] = 1;
			$return_array['msg'] = $msg;
		}
        echo json_encode($return_array);die();

    }
	public function usersearchAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;		 
		$error = array();
		$friends = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			 if ($request->isPost()) {
				$search_string = $this->getRequest()->getPost('search_string');
				if($search_string!=''){
					$this->userFriendTable = $sm->get('User\Model\UserFriendTable');
					$friends = $this->userFriendTable->getFriendsForSearch($identity->user_id,$search_string);					 
				}
			 }else{
				$error[] = "Invalid access!";
			 }
		}
		else{
			$error[] = "Your session has been expired..! Please try again after login!";
		}
		$viewModel = new ViewModel(array('error'=>$error,'friends'=>$friends));
		$viewModel->setTerminal($request->isXmlHttpRequest());
        return $viewModel;
	}
	public function ajxLoadmoreUsersAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;		 
		$error = array();
		$friends = array();
		$limit = 10;
		$offset = 0;
		$usersWithMessagesData = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
			 if ($request->isPost()) {
				$post = $request->getPost();
				$page =$post->get('page');
				if(!$page)
					$page = 0;	
				$offset	= $page*10;	
				if($identity->user_id){
					$search_string = $this->getRequest()->getPost('search_string');				 
					$this->messageTable = $sm->get('Message\Model\MessageTable');
					$usersWithMessagesData  = $this->messageTable->myMessages($identity->user_id,$limit,$offset);			 
				}else{
					$error[] = "Invalid access!";
				}
			 }else{
				$error[] = "Invalid access!";
			 }
		}
		else{
			$error[] = "Your session has been expired..! Please try again after login!";
		}
		$viewModel = new ViewModel(array('error'=>$error,'usersWithMessagesData'=>$usersWithMessagesData));
		$viewModel->setTerminal($request->isXmlHttpRequest());
        return $viewModel;
	}
	public function ajaxMessageCountAction(){
		$auth = new AuthenticationService();
		$sm = $this->getServiceLocator();
		$msg_count = 0;
		$request = $this->getRequest();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;
			$this->messageTable = $sm->get('Message\Model\MessageTable');
			$messages_count = $this->messageTable->getMessageCount($user_id);
			foreach($messages_count as $result){$msg_count = $result->message_count;}
		}
		$viewModel = new ViewModel(array('msg_count'=>$msg_count));
		$viewModel->setTerminal($request->isXmlHttpRequest());
        return $viewModel;
	}
	public function ajaxGetMessageNotificationAction(){
		$auth = new AuthenticationService();
		$sm = $this->getServiceLocator();		
		$request = $this->getRequest();
		$messages = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;
			$this->messageTable = $sm->get('Message\Model\MessageTable');
			$messages = $this->messageTable->getMessageForNotification($user_id);
			$this->messageTable->setStatusRead($user_id);
		}
		$viewModel = new ViewModel(array('messages'=>$messages));
		$viewModel->setTerminal($request->isXmlHttpRequest());
        return $viewModel;
	}
public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
			$this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
	public function getActivityTable(){
		 if (!$this->activityTable) {
            $sm = $this->getServiceLocator();
			$this->activityTable = $sm->get('Activity\Model\ActivityTable');
        }
        return $this->activityTable;
	}
 
}