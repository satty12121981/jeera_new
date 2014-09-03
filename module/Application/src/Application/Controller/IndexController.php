<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Groups\Model\Groups;

class IndexController extends AbstractActionController
{
	public $groupTable;
	public $userTable;
	public $activityTable;
	public function indexAction()
    { 
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);	 
		$auth = new AuthenticationService();	
		$identity = NULL;
		$vm = new ViewModel();	 
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
			$identity->profile_pic = '';
			$user_id = $identity->user_id;
			$profilepic = $this->getUserTable()->getUserProfilePic($user_id);
			$activity = $this->getActivityTable()->getEventCalendarJason($user_id); 
			foreach($profilepic as $pic){$identity->profile_pic = $pic->biopic;}
			$identity->activity = $activity;
			$this->layout()->identity = $identity;
			$this->layout()->page = 'Home';
			$vm->setVariable('userData', $identity);
			$planetSugessions = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'planetSuggestions',								 
								));
			$vm->addChild($planetSugessions, 'planetSugessions');
        }else{
			$this->layout()->identity = $identity;	//assign Identity to layout			
		}
		$galexies = $this->getGroupTable()->getGalexyWithUsers(0,10);		 
		$vm->setVariable('galexies', $galexies);
		return $vm;
    }
	public function getGroupTable()
    {
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
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
	public function quicksearchAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$notification_list = array();	
		$viewModel = new ViewModel();
		$user_id = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			if($identity->user_id){
				$user_id = $identity->user_id;
			}			
		}
		$search_string = '';
		$post = $request->getPost();		
		if ($request->isPost()){
			$search_string =$post->get('search_str');
		}
		$search_result = array();
		$users = $this->getUserTable()->seasrchUser($search_string,$user_id,0,2);
		$groups = $this->getGroupTable()->searchGroup($search_string,$user_id,0,2);
		$search_result['users'] = $users;
		$search_result['groups'] = $groups;
		$viewModel->setVariable('search_result', $search_result);
		$viewModel->setVariable('search_string', $search_string);			
		$viewModel->setVariable('error', $error);	
		$viewModel->setVariable('user_id', $user_id);	
		$viewModel->setVariable('notification_list', $notification_list);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function searchAction(){ 
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$notification_list = array();	
		$viewModel = new ViewModel();
		$user_id = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			if($identity->user_id){
				$user_id = $identity->user_id;
			}
 			
			$identity->profile_pic = '';
			$user_id = $identity->user_id;
			$profilepic = $this->getUserTable()->getUserProfilePic($user_id);
			$activity = $this->getActivityTable()->getEventCalendarJason($user_id); 
			foreach($profilepic as $pic){$identity->profile_pic = $pic->biopic;}
			$identity->activity = $activity;
			$this->layout()->identity = $identity;
			$this->layout()->page = 'Home';
			$viewModel->setVariable('userData', $identity);
			$planetSugessions = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'planetSuggestions',								 
								));
			$viewModel->addChild($planetSugessions, 'planetSugessions');			
		}
		$search_string = '';
		$search_string = $request->getPost();		
		$search_string = $this->params('search_str'); 
		$search_result = array();
		$users = $this->getUserTable()->seasrchUser($search_string,$user_id,0,10);
		$groups = $this->getGroupTable()->searchGroup($search_string,$user_id,0,10);
		$search_result['users'] = $users;
		$search_result['groups'] = $groups;
		$viewModel->setVariable('search_string', $search_string);			
		$viewModel->setVariable('search_result', $search_result);	
		$viewModel->setVariable('error', $error);	
		$viewModel->setVariable('user_id', $user_id);	
		$viewModel->setVariable('notification_list', $notification_list);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
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
		$user_id = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			if($identity->user_id){
				$user_id = $identity->user_id;
			}			
		}
		$page= 0;
		$search_string = '';
		$post = $request->getPost();		
		if ($request->isPost()){
			$search_string =$post->get('search_str');
			$page =$post->get('page');
		}
		$offset =0;
		$offset = $page * 10;
		$search_result = array();
		$users = $this->getUserTable()->seasrchUser($search_string,$user_id,$offset,10);
		$groups = $this->getGroupTable()->searchGroup($search_string,$user_id,$offset,10);
		$search_result['users'] = $users;
		$search_result['groups'] = $groups;
		$viewModel->setVariable('search_result', $search_result);
		$viewModel->setVariable('search_string', $search_string);			
		$viewModel->setVariable('error', $error);
		$viewModel->setVariable('user_id', $user_id);		
		$viewModel->setVariable('notification_list', $notification_list);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	 
}
