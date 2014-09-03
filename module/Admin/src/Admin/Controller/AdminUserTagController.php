<?php
namespace Admin\Controller; 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select; 
use Zend\Authentication\Storage\Session as SessionStorage;

use Tag\Model\Tag;
use Tag\Model\UserTag;
use User\Model\User;
use Admin\Form\AdminUserTagForm;
use Admin\Form\AdminUserTagFilter;   
use Admin\Form\AdminUserTagEditFilter;   
class AdminUserTagController extends AbstractActionController
{   
	protected $userTable;			#variable to hold the User model configration 
	protected $userProfileTable;	#variable to hold the User Profile model configration 
	protected $tagTable;			#variable to hold the Tag configration 
	protected $userTagTable;		#variable to hold the User Tag model configration 
	protected $groupTable;			#variable to hold the User Group model configration    
    public function indexAction()
    {		 
		$error =array();
		$success =array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null;
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;
			$page = $this->getEvent()->getRouteMatch()->getParam('page');
			$offset = 0 ;
			if($page){
				$offset = ($page-1)*20;
			}else{
				$page = 1;
				$offset = 0 ;
			}
			$sort = $this->getEvent()->getRouteMatch()->getParam('sort');
			$order = $this->getEvent()->getRouteMatch()->getParam('order');
			$field = '';
			switch($sort){
				case 'user':
					$field = 'user_given_name';
				break;
				case 'tags':
					$field = 'tags';
				break;
				default:
					$field = 'user_given_name';
			}
			if($order == 'desc'){
				$order = 'DESC';
			}else{
				$order = 'ASC';
			}
			if($sort==''){
				$sort = 'user';
			}			 
			$search = '';
			$request = $this->getRequest();
			if ($request->isPost()) {						 
				$post = $request->getPost();
				if(isset($post['tag_search'])&&$post['tag_search']!=''){
					$search = $post['tag_search'];
				}
			}else{
				$search =  $this->getEvent()->getRouteMatch()->getParam('search');
			}
			$total_tags = $this->getUserTagTable()->getCountOfAllUserTags($search); 
			$total_pages = ceil($total_tags/20);
			$allUserTagData = array();	
			$allUserTagData = $this->getUserTagTable()->getAllUserTags(20,$offset,$field,$order,$search);			  
			return array('allUserTagData' => $allUserTagData,'total_pages'=>$total_pages,'page'=> $page,'search'=>$search, 'field'=>$sort,'order'=>$order, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());	
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));
		}
    }
	public function viewAction(){
		$error =array();
		$success =array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null;
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;
			$user_id = $this->getEvent()->getRouteMatch()->getParam('id');
			if($user_id!=''){
				$userData = $this->getUserTable()->getUser($user_id);
				if(!empty($userData)&&$userData->user_id!=''){
					$usertags = $this->getUserTagTable()->fetchAllTagsOfUser($user_id);
					return array('usertags' => $usertags,'userData'=>$userData, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());
				}else{
					$error[] = 'Given user is not existing in this system';
				}
			}else{
				return $this->redirect()->toRoute('jadmin/admin-user-tags', array('action' => 'index'));
			}				
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));
		}
	}
	public function deleteAction(){
		$error ='';
		$error_count =0;
		$return_array = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		if ($auth->hasIdentity()) {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				if(isset($post['tag_id'])&&$post['tag_id']!=''&&isset($post['user_id'])&&$post['user_id']!=''){
					if($this->getUserTagTable()->removeUserTag($post['tag_id'],$post['user_id'])){
						$error = 'Successfully removed';
						$error_count==0;
					}else{
						$error = 'Some error occured.Please try again';
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
	public function getTagListAction(){
		$error =array();
		$success =array();
		$viewModel = new ViewModel();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null;
		$limit = 50;
		$offset = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();	
			if(!empty($post)){
				$user_id = $post['user_id'];
				$page = $post['page'];
				if($page){
					$offset = $page*$limit;
				}
				if($user_id!=''){
					$tags = $this->getUserTagTable()->fetchAllTagsExceptUser($user_id,$limit,$offset);
					$viewModel->setVariable('tags', $tags);	
				}else{
					$error[] = 'Invalid access method';
				}
			}else{
				$error[] = 'Invalid access method';
			}			 			
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));
		}
		$viewModel->setVariable('error', $error);	
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;		
	}
	public function addUserTagAction(){
		$error ='';
		$error_count =0;
		$return_array = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		if ($auth->hasIdentity()) {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				if(isset($post['tag_id'])&&$post['tag_id']!=''&&isset($post['user_id'])&&$post['user_id']!=''){
					$userData = $this->getUserTable()->getUser($post['user_id']);
					$tagData = $this->getTagTable()->getTag($post['tag_id']);
					if(!empty($userData)&&!empty($tagData)){
						$tag_data['user_tag_user_id'] = $post['user_id'];
						$tag_data['user_tag_tag_id'] = $post['tag_id'];
						$objuser = new User();					
						$tag_data['user_tag_added_ip_address'] = $objuser->getUserIp();
						$userTag = new UserTag();
						$userTag->exchangeArray($tag_data);		               	 
						if($this->getUserTagTable()->saveUserTag($userTag)){
							$error = 'Successfully removed';
							$error_count==0;
						}else{
							$error = 'Some error occured.Please try again';
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
	public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }	 	
	#Accessing the tag table
	public function getTagTable()
    {
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Tag\Model\TagTable');
        }
        return $this->tagTable;
    }	
	#Accessing the Group tag table
	public function getUserTagTable()
    {
        if (!$this->userTagTable) {
            $sm = $this->getServiceLocator();
            $this->userTagTable = $sm->get('Tag\Model\UserTagTable');
        }
        return $this->userTagTable;
    }
}