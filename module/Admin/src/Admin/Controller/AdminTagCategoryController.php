<?php
namespace Admin\Controller; 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel; 
use Zend\Session\Container;     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select;
use Zend\Authentication\Storage\Session as SessionStorage;
 
use Tag\Model\TagCategory;
use Admin\Form\AdminTagCategoryForm;
use Admin\Form\AdminTagCategoryFilter;   
use Admin\Form\AdminTagCategoryEditFilter;

class AdminTagCategoryController extends AbstractActionController
{    
	protected $tagTable;
    protected $userTagTable;
    protected $groupTagTable;
    protected $tagCategoryTable;

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
					$field = 'tag_category_id';
				break;
				case 'title':
					$field = 'tag_category_title';
				break;
				default:
					$field = 'tag_category_id';
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
				if(isset($post['tag_category_search'])&&$post['tag_category_search']!=''){
					$search = $post['tag_category_search'];
				}
			}else{
				$search =  $this->getEvent()->getRouteMatch()->getParam('search');
			}
			if($sort==''){
				$sort = 'id';
			}
			$total_tags = $this->getTagCategoryTable()->getCountOfAllTagCategories($search);
			$total_pages = ceil($total_tags/20);
			$allTagCategoriesData = $this->getTagCategoryTable()->getAllTagCategories(20,$offset,$field,$order,$search);			 
			return array('allTagCategoriesData' => $allTagCategoriesData,'field'=>$sort,'order'=>$order,'search'=>$search,'total_pages'=>$total_pages,'page'=> $page, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());	 	
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
            $sm = $this->getServiceLocator();
			$selectAllTagCategory = $this->getTagCategoryTable()->fetchAll();
			$form = new AdminTagCategoryForm($selectAllTagCategory);
			$form->get('submit')->setAttribute('value', 'Add');
			$request = $this->getRequest();
			$this->layout('layout/admin_page');	
			if ($request->isPost()) {
				$tag = new TagCategory();
				$sm = $this->getServiceLocator();
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				$form->setInputFilter(new AdminTagCategoryFilter($dbAdapter));					 
				$form->setData($request->getPost());
				if ($form->isValid()) {
					$tag->exchangeArray($form->getData());
					$this->getTagCategoryTable()->saveTagCategory($tag);                
					return $this->redirect()->toRoute('jadmin/admin-tag-categories');
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
				return $this->redirect()->toRoute('jadmin/admin-tag-categories-add', array('action'=>'add'));
			}
			$tag = $this->getTagCategoryTable()->getTagCategory($id); 
			if(!isset($tag->tag_category_id) || empty($tag->tag_category_id)){
				return $this->redirect()->toRoute('jadmin/admin-tag-categories', array('action'=>'index'));
			}
			$form = new AdminTagCategoryForm();
			$form->bind($tag);
			$form->get('submit')->setAttribute('value', 'Edit');        
			$request = $this->getRequest();
			if ($request->isPost()) {
				$form->setInputFilter(new AdminTagCategoryEditFilter($dbAdapter, $id));		
				$form->setData($request->getPost());
				if ($form->isValid()) {
					$this->getTagCategoryTable()->saveTagCategory($tag);                // Redirect to list of tags
					return $this->redirect()->toRoute('jadmin/admin-tag-categories');
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
				return $this->redirect()->toRoute('jadmin/admin-tag-categories');
			}
			$tag = $this->getTagCategoryTable()->getTagCategory($id);
			if(!isset($tag->tag_category_id) || empty($tag->tag_category_id)){
				return $this->redirect()->toRoute('jadmin/admin-tag-categories', array('action'=>'index'));
			}
			$request = $this->getRequest();
			if ($request->isPost()) {
				$del = $request->getPost()->get('del', 'No');
				if ($del == 'Yes') {
                    $this->getTagCategoryTable()->deleteTagCategory($id);
					$this->getTagTable()->deleteTag($id);
					$this->getUserTagTable()->deleteUserTag($id);
					$this->getGroupTagTable()->deleteGroupTag($id);
				}            
				return $this->redirect()->toRoute('jadmin/admin-tag-categories');
			}	 
			return array(
				'id' => $id,
				'tag' => $this->getTagTable()->getTag($id),
                'tagsList' => $this->getrTagTable()->fetchAllTag($id),
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

    public function getTagCategoryTable()
    {
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Tag\Model\TagCategoryTable');
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