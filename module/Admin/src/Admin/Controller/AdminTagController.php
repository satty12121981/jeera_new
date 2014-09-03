<?php
namespace Admin\Controller; 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel; 
use Zend\Session\Container;     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select;
use Zend\Authentication\Storage\Session as SessionStorage;
 
use Tag\Model\Tag;
use Admin\Form\AdminTagForm;
use Admin\Form\AdminTagFilter;   
use Admin\Form\AdminTagEditFilter;   
class AdminTagController extends AbstractActionController
{    
	protected $tagTable;	 
	protected $userTagTable;
	protected $groupTagTable; 
    public function indexAction()
    {	
		$error = array(); 
		$success = array();	 	
		$vm = new ViewModel(); 
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;			 
			$allTagData = array();
			$page = $this->getEvent()->getRouteMatch()->getParam('page');
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
				case 'id':
					$field = 'tag_id';
				break;
				case 'title':
					$field = 'tag_title';
				break;
				default:
					$field = 'tag_id';
			}
			if($order == 'desc'){
				$order = 'DESC';
			}else{
				$order = 'ASC';
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
			if($sort==''){
				$sort = 'id';
			}
			$total_tags = $this->getTagTable()->getCountOfAllTags($search); 
			$total_pages = ceil($total_tags/20);
			$allTagData = $this->getTagTable()->getAllTags(20,$offset,$field,$order,$search);			 
			return array('allTagData' => $allTagData,'field'=>$sort,'order'=>$order,'search'=>$search,'total_pages'=>$total_pages,'page'=> $page, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());	 	
        }else{			
			 return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));		
		}	
		 
    }
	
	public function addAction()
    {        
	    $error = array(); 
		$success = array();		 
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;			
			$form = new AdminTagForm();
			$form->get('submit')->setAttribute('value', 'Add');
			$request = $this->getRequest();
			$this->layout('layout/admin_page');	
			if ($request->isPost()) {
				$tag = new Tag();
				$sm = $this->getServiceLocator();
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$form->setInputFilter(new AdminTagFilter($dbAdapter));					 
				$form->setData($request->getPost());
				if ($form->isValid()) {
					$tag->exchangeArray($form->getData());
					$this->getTagTable()->saveTag($tag);                
					return $this->redirect()->toRoute('jadmin/admin-tags');
				} 
			}
			return array('form' => $form, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());
		}else{			
			 return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));		
		}
    }

    public function editAction()
    {
        $error = array(); 
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;		
			$sm = $this->getServiceLocator();
			$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');		
			$id = (int)$this->params('id');
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-tags-add', array('action'=>'add'));
			}
			$tag = $this->getTagTable()->getTag($id); 
			if(!isset($tag->tag_id) || empty($tag->tag_id)){
				return $this->redirect()->toRoute('jadmin/admin-tags', array('action'=>'index'));
			}
			$form = new AdminTagForm();
			$form->bind($tag);
			$form->get('submit')->setAttribute('value', 'Edit');        
			$request = $this->getRequest();
			if ($request->isPost()) {
				$form->setInputFilter(new AdminTagEditFilter($dbAdapter, $id));		
				$form->setData($request->getPost());
				if ($form->isValid()) {
					$this->getTagTable()->saveTag($tag);                // Redirect to list of tags
					return $this->redirect()->toRoute('jadmin/admin-tags');
				} 
			}
			return array(
				'id' => $id,
				'form' => $form,
				'error' => $error, 
				'success' => $success, 
				'flashMessages' => $this->flashMessenger()->getMessages()
			);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    }

    public function deleteAction()
    {
		$error = array();
		$success = array();		
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;	
			$id = (int)$this->params('id');
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-tags');
			}
			$tag = $this->getTagTable()->getTag($id); 
			if(!isset($tag->tag_id) || empty($tag->tag_id)){
				return $this->redirect()->toRoute('jadmin/admin-tags', array('action'=>'index'));
			}
			$request = $this->getRequest();
			if ($request->isPost()) {
				$del = $request->getPost()->get('del', 'No');
				if ($del == 'Yes') {					 
					$this->getTagTable()->deleteTag($id);
					$this->getUserTagTable()->deleteUserTag($id);
					$this->getGroupTagTable()->deleteGroupTag($id);
				}            
				return $this->redirect()->toRoute('jadmin/admin-tags');
			}	 
			return array(
				'id' => $id,
				'tag' => $this->getTagTable()->getTag($id),
				'usersList' => $this->getUserTagTable()->fetchAllUsersOfTag($id),
				'groupList' => $this->getGroupTagTable()->fetchAllGroupsOfTag($id),
				'error' => $error, 
				'success' => $success, 
				'flashMessages' => $this->flashMessenger()->getMessages()
			);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    } 
	public function getTagTable()
    {
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Tag\Model\TagTable');
        }
        return $this->tagTable;
    }  
	public function getUserTagTable()
    {
        if (!$this->userTagTable) {
            $sm = $this->getServiceLocator();
            $this->userTagTable = $sm->get('Tag\Model\UserTagTable');
        }
        return $this->userTagTable;
    }
	public function getGroupTagTable()
    {
        if (!$this->groupTagTable) {
            $sm = $this->getServiceLocator();
            $this->groupTagTable = $sm->get('Tag\Model\GroupTagTable');
        }
        return $this->groupTagTable;
    }
}