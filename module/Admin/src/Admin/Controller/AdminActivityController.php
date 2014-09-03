<?php
namespace Admin\Controller; 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select;
use Zend\Validator\File\Size; 
use Zend\Authentication\Storage\Session as SessionStorage;
use Tag\Model\Tag;
use Group\Model\Group;
use Photo\Model\Photo;
use User\Model\User; 
use Activity\Model\Activity;
use Activity\Model\ActivityTable;

  
class AdminActivityController extends AbstractActionController
{   
	
    protected $adminActivityTable;
	protected $userTable;	 
	protected $groupTable;		 
	protected $userProfileTable;	 
	protected $tagTable;			 
	protected $groupTagTable;	 
	protected $userTagTable;	 
	protected $photoTable;		  
	protected $Group_Thumb_Path = "";	 
	protected $Group_Timeline_Path = ""; 
	protected $Group_Thumb_Smaller = "";	 
	protected $Group_Minumum_Bytes = "";
	protected $activityTable;
	protected $activityTagTable;
	protected $activityRsvpTable;
	protected $activityInviteTable;
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
			$galaxy_seo = $this->getEvent()->getRouteMatch()->getParam('galaxy');
			$galaxy_id = null;
			if($galaxy_seo!=''&&$galaxy_seo!='All'){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($galaxy_seo);
				if(!empty($groupdetails)&&$groupdetails->group_id!=''){				
					$galaxy_id = $groupdetails->group_id;
				}
			}	
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('planet');
			$planet_id = null;	
			if($planet_seo!=''&&$planet_seo!='All'){
				$planet_details = $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				if(!empty($planet_details)&&$planet_details->group_id!=''){				
					$planet_id = $planet_details->group_id;
				}
			}
			$galaxy = $this->getGroupTable()->fetchAllGroups();
			 
			if($galaxy_id  == null){ 
				
				foreach($galaxy as $list){
					$galaxy_id = $list->group_id;
					$galaxy_seo = $list->group_seo_title;
					break;
				}
			}			
			$planets = $this->getGroupTable()->getPlanets($galaxy_id);
			if($planet_id==null){
				foreach($planets as $list){
					$planet_id = $list->group_id;
					$planet_seo = $list->group_seo_title;
					break;
				}
			}
			$page = $this->getEvent()->getRouteMatch()->getParam('page');
			if($page){
				$offset = ($page-1)*20;
			}else{
				$page = 1;
				$offset = 0 ;
			}
			$search =  $this->getEvent()->getRouteMatch()->getParam('search');
			$sort = $this->getEvent()->getRouteMatch()->getParam('sort');
			$order = $this->getEvent()->getRouteMatch()->getParam('order');
			$field = '';
			switch($sort){
				case 'id':
					$field = 'group_activity_id';
				break;
				case 'content':
					$field = 'group_activity_content';
				break;
				case 'title':
					$field = 'group_activity_title';
				break;
				case 'group':
					$field = 'group_title';
				break;
				case 'user':
					$field = 'user_first_name';
				break;
				case 'location':
					$field = 'group_activity_location';
				break;
				default:
					$field = 'group_activity_id';
			}
			if($order == 'desc'){
				$order = 'DESC';
			}else{
				$order = 'ASC';
			}
			if($sort==''){
				$sort = 'id';
			}
			$title = 'List Activity';
			$sm = $this->getServiceLocator();
			$adminActivityTable = $sm->get('Activity\Model\ActivityTable');
			$total_activity = $adminActivityTable->getCountOfPlanetActivities($planet_id,$search);
			$total_pages = ceil($total_activity/20);
			$all_activity = array();				
			$all_activity = $adminActivityTable->getAllPlanetActivities($planet_id,20,$offset,$field,$order,$search);
			
			return array('all_activity' => $all_activity, 
						'title' => $title, 
						'flashMessages' => $this->flashMessenger()->getMessages(),
						'total_pages'=>$total_pages,
						'page'=> $page,
						'galaxy' =>$this->getGroupTable()->fetchAllGroups(),
						'planets' =>$this->getGroupTable()->getPlanets($galaxy_id),
						'galaxy_seo' =>$galaxy_seo,
						'planet_seo' => $planet_seo,
						'search' => $search,
						'field'=>$sort,
						'order'=>$order,
					);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));
		}					
    }
	public function viewAction()
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
			$title = 'View Activity';
			$sm = $this->getServiceLocator();
			$adminActivityTable = $sm->get('Activity\Model\ActivityTable');			
			$id = (int)$this->params('id'); 
			 if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-activity', array('action'=>'index'));
			}			
			$view_activity = array();	
			$view_activity = $adminActivityTable->Admin_get_activity($id);
			$activitytags = $this->getActivityTagTable()->fetchAllTagsOfActivity($id);
			$activitymembers = $this->getActivityRsvpTable()->getJoinMembers($id,0,25);
			return array('view_activity' => $view_activity , 'title' => $title,'activitytags'=>$activitytags,'activitymembers'=>$activitymembers );
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));
		}	
	}
	public function getActivityMembersAction(){
		$error =array();
		$success =array();
		$viewModel = new ViewModel();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null;
		$limit = 25;
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
						$members = $this->getActivityRsvpTable()->getJoinMembers($activity_id,$limit,$offset);
						$viewModel->setVariable('members', $members);	
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
	public function blockAction()
	{
		$error = array();	
		$error_count = 0;
		$status		=0;
		$success =array();
		$viewModel = new ViewModel();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null;		 
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
		    $sm = $this->getServiceLocator();
			$adminActivityTable = $sm->get('Activity\Model\ActivityTable');			
			$id = (int)$this->params('id'); 
			if ($id) {					 
				$activityData = $this->getActivityTable()->getActivity($id);	
				if(!empty($activityData)&&$activityData->group_activity_id!=''){
					$status =2;
					if($activityData->group_activity_status == 2)	{
						$status = 1;
					}
					$data['group_activity_status'] = $status;
					$this->getActivityTable()->updateActivity($id,$data);
					$error_count  = 0;
					$error = "";
				}else{
					$error_count ++;
					$error = "Given activity not existed in this system";
				}
			}else{
				$error_count ++;
				$error = "Given activity not existed in this system";
			}			 
		}else{
			$error_count ++;
			$error = "Your session expired. Please try again after login";
		}
		$return_array = array();
		if($error_count==0){
			$return_array['msg'] = " ";
			$return_array['status'] = $status;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['status'] = 0;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	} 	 
    public function deleteAction(){
		$error =array();
		$success =array();	
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null;
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;	
			$id = (int)$this->params('id'); 
			$sm = $this->getServiceLocator();
			$adminActivityTable = $sm->get('Activity\Model\ActivityTable');			
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-activity', array('action'=>'index'));
			}
			$request = $this->getRequest();
			
			$activityData = $this->getActivityTable()->Admin_get_activity($id);	
			if ($request->isPost()) {			   
				$del = $request->getPost()->get('activity_delete', 'No');
				if ($del == 'No') { 
					return $this->redirect()->toRoute('jadmin/admin-activity',array('galaxy'=>$activityData->parent_group_seo_title,'planet'=>$activityData->group_seo_title));
				} 		
			}	
			if(!empty($activityData)&&$activityData->group_activity_id!=''){ 
				return array('activityData' => $activityData  );
			}else{
				return $this->redirect()->toRoute('jadmin/admin-activity', array('action'=>'index'));
			}
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));
		}

	}
	public function removeActivityLikesAndCommentsAction(){
		$error = array();	
		$error_count = 0;
		$status		=0;
		$success =array();
		$viewModel = new ViewModel();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null;		 
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
		    $sm = $this->getServiceLocator();
			$adminActivityTable = $sm->get('Activity\Model\ActivityTable');			
			$id = (int)$this->params('id'); 
			if ($id) {					 
				$activityData = $this->getActivityTable()->getActivity($id);	
				if(!empty($activityData)&&$activityData->group_activity_id!=''){
					if($this->getActivityTable()->removeActivityLikesaAndComments($id) ){
						$error_count = 0 ;
						$error = "";
						
					}else{
						$error_count ++;
						$error = "Some error occured.. Please try again";
					}
				}else{
					$error_count ++;
					$error = "Given activity not existed in this system";
				}
			}else{
				$error_count ++;
				$error = "Given activity not existed in this system";
			}			 
		}else{
			$error_count ++;
			$error = "Your session expired. Please try again after login";
		}
		$return_array = array();
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
	public function removeActivityTagsAction(){ 
		$error = array();	
		$error_count = 0;
		$status		=0;
		$success =array();
		$viewModel = new ViewModel();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null;		 
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
		    $sm = $this->getServiceLocator();
			$adminActivityTable = $sm->get('Activity\Model\ActivityTable');			
			$id = (int)$this->params('id'); 
			if ($id) {					 
				$activityData = $this->getActivityTable()->getActivity($id);	
				if(!empty($activityData)&&$activityData->group_activity_id!=''){
					if($this->getActivityTagTable()->RemoveAllActivityTags($id) ){
						$error_count = 0 ;
						$error = "";
						
					}else{
						$error_count ++;
						$error = "Some error occured.. Please try again";
					}
				}else{
					$error_count ++;
					$error = "Given activity not existed in this system";
				}
			}else{
				$error_count ++;
				$error = "Given activity not existed in this system";
			}			 
		}else{
			$error_count ++;
			$error = "Your session expired. Please try again after login";
		}
		$return_array = array();
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
	public function removeActivityMembersAction(){
		$error = array();	
		$error_count = 0;
		$status		=0;
		$success =array();
		$viewModel = new ViewModel();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null;		 
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
		    $sm = $this->getServiceLocator();
			$adminActivityTable = $sm->get('Activity\Model\ActivityTable');			
			$id = (int)$this->params('id'); 
			if ($id) {					 
				$activityData = $this->getActivityTable()->getActivity($id);	
				if(!empty($activityData)&&$activityData->group_activity_id!=''){
					if($this->getActivityRsvpTable()->deleteAllActivityRsvp($id) ){
						$this->getActivityInviteTable()->deleteAllInviteActivity($id);
						$error_count = 0 ;
						$error = "";
						
					}else{
						$error_count ++;
						$error = "Some error occured.. Please try again";
					}
				}else{
					$error_count ++;
					$error = "Given activity not existed in this system";
				}
			}else{
				$error_count ++;
				$error = "Given activity not existed in this system";
			}			 
		}else{
			$error_count ++;
			$error = "Your session expired. Please try again after login";
		}
		$return_array = array();
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
	public function removeActivityAction(){
		$error = array();	
		$error_count = 0;
		$status		=0;
		$success =array();
		$viewModel = new ViewModel();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null;		 
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
		    $sm = $this->getServiceLocator();
			$adminActivityTable = $sm->get('Activity\Model\ActivityTable');			
			$id = (int)$this->params('id'); 
			if ($id) {					 
				$activityData = $this->getActivityTable()->getActivity($id);	
				if(!empty($activityData)&&$activityData->group_activity_id!=''){
					if($this->getActivityTable()->deleteActivity($id)  == 'success'){
						
						$error_count = 0 ;
						$error = "";
						
					}else{
						$error_count ++;
						$error = "Some error occured.. Please try again";
					}
				}else{
					$error_count ++;
					$error = "Given activity not existed in this system";
				}
			}else{
				$error_count ++;
				$error = "Given activity not existed in this system";
			}			 
		}else{
			$error_count ++;
			$error = "Your session expired. Please try again after login";
		}
		$return_array = array();
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
	public function getActivityTagTable()
    {
        if (!$this->activityTagTable) {
            $sm = $this->getServiceLocator();
            $this->activityTagTable = $sm->get('Tag\Model\ActivityTagTable');
        }
        return $this->activityTagTable;
    }
	public function getActivityRsvpTable()
    {
        if (!$this->activityRsvpTable) {
            $sm = $this->getServiceLocator();
            $this->activityRsvpTable = $sm->get('Activity\Model\ActivityRsvpTable');
        }
        return $this->activityRsvpTable;
    }
	public function getActivityInviteTable()
    {
        if (!$this->activityInviteTable) {
            $sm = $this->getServiceLocator();
            $this->activityInviteTable = $sm->get('Activity\Model\ActivityInviteTable');
        }
        return $this->activityInviteTable;
    }
}