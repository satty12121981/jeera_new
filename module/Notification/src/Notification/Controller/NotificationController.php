<?php
namespace Notification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/ 
//Login Form
#Notification class
use Notification\Model\Notification;  
use Notification\Model\NotificationTable; 
class NotificationController extends AbstractActionController
{
    protected $NotificationTable;		
  	protected $userNotificationTable;
	protected $userTable;
	protected $activityTable;
    public function indexAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$notification_list = array();	
		$viewModel = new ViewModel();	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;
			$profilepic = $this->getUserTable()->getUserProfilePic($user_id);
			$activity = $this->getActivityTable()->getEventCalendarJason($user_id); 
			foreach($profilepic as $pic){$identity->profile_pic = $pic->biopic;}
			$identity->activity = $activity;
			$this->layout()->identity = $identity;
			$planetSugessions = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'planetSuggestions',								 
								));
			$viewModel->addChild($planetSugessions, 'planetSugessions');
			if($identity->user_id){
				$notification_list = $this->getUserNotificationTable()->getAllUserNotificationWithAllStatus($identity->user_id,0,20);
			}			
		}else{
			 return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}		 
		$viewModel->setVariable('error', $error);	
		$viewModel->setVariable('notification_list', $notification_list);	 
		return $viewModel;
    }
	public function ajaxGetNotificationCountAction(){		 
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$notification_count = 0;	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			if($identity->user_id){
				$notification_count = $this->getUserNotificationTable()->getUserNotificationCountForUserUnread($identity->user_id); 
			}			
		}else{
			$error[] = "Your session has to be expired";
		}		 
		echo $notification_count;die();	 
	}	
	public function getUserNotificationTable(){
		 if (!$this->userNotificationTable) {
            $sm = $this->getServiceLocator();
            $this->userNotificationTable = $sm->get('Notification\Model\UserNotificationTable');
        }
        return $this->userNotificationTable;
	}
	public function ajaxGetUserNotificationListAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$notification_list = array();	
		$viewModel = new ViewModel();	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			if($identity->user_id){
				$notification_list = $this->getUserNotificationTable()->getAllUnreadNotification($identity->user_id);
				$this->getUserNotificationTable()->makeNotificationsReaded($identity->user_id);
			}			
		}else{
			$error[] = "Your session has to be expired";
		}		 
		$viewModel->setVariable('error', $error);	
		$viewModel->setVariable('notification_list', $notification_list);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}	
	public function getUserTable(){
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
	public function ajaxLoadMoreAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$notification_list = array();	
		$viewModel = new ViewModel();
		$page = 0;			
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;
			$post = $request->getPost();		
			if ($request->isPost()){
				$page =$post->get('page');
				if(!$page)
				$page = 0;				 
			}
			$offset = $page*25;
			if($identity->user_id){
				$notification_list = $this->getUserNotificationTable()->getAllUserNotificationWithAllStatus($identity->user_id,$offset,20);
			}else{
				$error[] = "invalid request";
			}				
		}else{
			$error[] = "Your session has to be expired";
		}		 
		$viewModel->setVariable('error', $error);	
		$viewModel->setVariable('notification_list', $notification_list);	 
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
		
	}
}