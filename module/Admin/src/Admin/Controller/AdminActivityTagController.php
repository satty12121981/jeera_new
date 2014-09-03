<?php 
namespace Admin\Controller; 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Db\Sql\Select; 
use Tag\Model\Tag;
use Tag\Model\GroupTag;
use Group\Model\Group;
use Admin\Form\AdminPlanetTagForm;
use Admin\Form\AdminPlanetTagFilter;   
use Admin\Form\AdminPlanetTagEditFilter;
use User\Model\User;   
class AdminActivityTagController extends AbstractActionController
{   
	protected $userTable;	 
	protected $userProfileTable;	 
	protected $tagTable; 
	protected $groupTagTable; 
	protected $groupTable; 
	protected $activityTagTable;
	protected $activityTable;
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
				case 'activity':
					$field = 'group_activity_title';
				break;
				case 'tags':
					$field = 'tags';
				break;
				default:
					$field = 'group_activity_title';
			}
			if($order == 'desc'){
				$order = 'DESC';
			}else{
				$order = 'ASC';
			}
			if($sort==''){
				$sort = 'activity';
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
			$total_tags = $this->getActivityTagTable()->getCountOfAllActivityTags($search); 
			$total_pages = ceil($total_tags/20);
			$allActivityTagData = array();	
			$allActivityTagData = $this->getActivityTagTable()->getAllActivityTags(20,$offset,$field,$order,$search);			 
			return array('allActivityTagData' => $allActivityTagData,'total_pages'=>$total_pages,'page'=> $page,'search'=>$search, 'field'=>$sort,'order'=>$order,  'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());	 
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
			$activity_id = $this->getEvent()->getRouteMatch()->getParam('id');
			if($activity_id!=''){
				$activityData = $this->getActivityTable()->getActivity($activity_id);
				if(!empty($activityData)&&$activityData->group_activity_id!=''){
					$activitytags = $this->getActivityTagTable()->fetchAllTagsOfActivity($activity_id);
					return array('activitytags' => $activitytags,'activityData'=>$activityData, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());
				}else{
					$error[] = 'Given group is not existing in this system';
				}
			}else{
				return $this->redirect()->toRoute('jadmin/admin-planet-tags', array('action' => 'index'));
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
				if(isset($post['tag_id'])&&$post['tag_id']!=''&&isset($post['activity_id'])&&$post['activity_id']!=''){ 				 
					if($this->getActivityTagTable()->RemoveActivityTags($post['tag_id'],$post['activity_id'])){
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
				$activity_id = $post['activity_id'];
				$page = $post['page'];
				if($page){
					$offset = $page*$limit;
				}
				if($activity_id!=''){
					$activityData = $this->getActivityTable()->getActivity($activity_id);
					if(!empty($activityData)&&$activityData->group_activity_id!=''){
						$tags = $this->getActivityTagTable()->fetchAllTagsExceptActivity($activity_id,$activityData->group_activity_group_id,$limit,$offset);
						$viewModel->setVariable('tags', $tags);	
					}else{
						$error[] = 'Given activity is not existing in this system';
					}
					
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
	public function addActivityTagAction(){ 
		$error ='';
		$error_count =0;
		$return_array = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		if ($auth->hasIdentity()) {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				if(isset($post['tag_id'])&&$post['tag_id']!=''&&isset($post['activity_id'])&&$post['activity_id']!=''){
					$activityData = $this->getActivityTable()->getActivity($post['activity_id']);
					$tagData = $this->getTagTable()->getTag($post['tag_id']);
					if(!empty($activityData)&&!empty($tagData)){
						$tag_data['activity_id'] = $post['activity_id'];
						$tag_data['group_id'] = $activityData->group_activity_group_id;
						$tag_data['group_tag_id'] = $post['tag_id'];						 
						if($this->getActivityTagTable()->saveActivityTags($tag_data)){
							$error = 'Successfully added';
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
	public function getGroupTable()
    {
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
            $this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    } 
	public function getActivityTable()
    {
        if (!$this->activityTable) {
            $sm = $this->getServiceLocator();
            $this->activityTable = $sm->get('Activity\Model\ActivityTable');
        }
        return $this->activityTable;
    } 
	public function getTagTable()
    {
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Tag\Model\TagTable');
        }
        return $this->tagTable;
    } 
	public function getGroupTagTable()
    {
        if (!$this->groupTagTable) {
            $sm = $this->getServiceLocator();
            $this->groupTagTable = $sm->get('Tag\Model\GroupTagTable');
        }
        return $this->groupTagTable;
    }
	public function getActivityTagTable()
    {
        if (!$this->activityTagTable) {
            $sm = $this->getServiceLocator();
            $this->activityTagTable = $sm->get('Tag\Model\ActivityTagTable');
        }
        return $this->activityTagTable;
    }
	
}