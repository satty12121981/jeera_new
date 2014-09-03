<?php  
namespace Admin\Controller; 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel; 
use Zend\Session\Container;    
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select; 
use Zend\Validator\File\Size;
use Zend\Authentication\Storage\Session as SessionStorage; 
use User\Model\User;
use Groups\Model\GroupPhoto;
use Groups\Model\Groups;
use Admin\Form\AdminGalaxyForm;
use Admin\Form\AdminGalaxyEditForm;
use Admin\Form\AdminGalaxyFilter;   
use Admin\Form\AdminGalaxyEditFilter;    
class AdminGalaxyController extends AbstractActionController
{   
	 
	protected $groupTable;			#variable to hold the  Groups model configuration 
	protected $userGroupTable;		#variable to hold the User Groups model configuration 
	protected $photoTable;			#variable to hold the Photo table
	protected $activityTable;		#variable to hold the Activity table
	protected $groupThumb ="";			//Image path of Group Standard Time line
	protected $Group_Timeline_Path="";	//Image path of Group Time line
	protected $Group_Thumb_Smaller ="";	//Image path of Group small Thumb
	protected $Group_Minumum_Bytes ="";	//Image path of Group small Thumb	
	public function __construct()
    { 
        $this->groupThumb = Groups::Group_Thumb_Path;  
		$this->Group_Timeline_Path = Groups::Group_Timeline_Path;  
		$this->Group_Thumb_Smaller = Groups::Group_Thumb_Smaller;  		
		$this->Group_Minumum_Bytes = Groups::Group_Minumum_Bytes;  		  
    } 
    public function indexAction()
    {		 
		$error = array();	 
		$success = array();	 
		$allGroupData = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;			
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
					$field = 'group_id';
				break;
				case 'title':
					$field = 'group_title';
				break;
				case 'seotitle':
					$field = 'group_seo_title';
				break;
				default:
					$field = 'group_id';
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
				if(isset($post['galexy_search'])&&$post['galexy_search']!=''){
					$search = $post['galexy_search'];
				}
			}else{
				$search =  $this->getEvent()->getRouteMatch()->getParam('search');
			}
			if($sort==''){
				$sort = 'id';
			}
			$total_tags = $this->getGroupTable()->getCountOfAllGalaxy($search); 
			$total_pages = ceil($total_tags/20);
			$allGroupData = $this->getGroupTable()->fetchAllGalaxy(20,$offset,$field,$order,$search);			 
			return array('allGroupData' => $allGroupData,'field'=>$sort,'order'=>$order,'search'=>$search,'total_pages'=>$total_pages,'page'=> $page, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());		   
			 
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    }	
	public function addAction()
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
			$sm = $this->getServiceLocator();   	
			$config = $this->getServiceLocator()->get('Config'); 
			$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');		
			$form = new AdminGalaxyForm();
			$form->get('submit')->setAttribute('value', 'Add');
			$request = $this->getRequest();
			if ($request->isPost()) {
				$group = new Groups();
				$form->setInputFilter(new AdminGalaxyFilter($dbAdapter));					 
				$form->setData($request->getPost());
				$nonFile = $request->getPost()->toArray();
				$File = $this->params()->fromFiles('galaxy_image');
				$data = array_merge(
					$nonFile,
					 array('fileupload'=> $File['name']) 
				 );				
				if ($form->isValid()) {                 
					$size = new Size(array('min'=>$this->Group_Minumum_Bytes)); 
					$adapter = new \Zend\File\Transfer\Adapter\Http();					 
					$adapter->setValidators(array($size), $File['name']);					 
					$adapter->addValidator('Extension', false, 'jpg,png,gif');				 
					if (!$adapter->isValid()){
						$dataError = $adapter->getMessages();
						foreach($dataError as $key=>$row)
						{
							$error[] = $row;
						} 				 
						$form->setMessages(array('fileupload'=>$error ));
					} else {				 
						$fileName ="";
						$string = '';
						$length = 5;
						$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
						$string = strtolower($string).'_'.$randomString;
						$fileName = time().$string.$File['name'];
						move_uploaded_file($File['tmp_name'],$config['pathInfo']['UploadPath'].'galexy/'.$fileName);
						//$fileName = Groups::uploadGroupImage('Galaxy',$File, $config['pathInfo']['UploadPath'].'galexy/', $adapter, $this->params()->fromPost('group_title'));					 
						$groupData = array();				 
						$groupData['group_title'] = $this->params()->fromPost('group_title');
						$groupData['group_seo_title'] = $this->params()->fromPost('group_seo_title');
						$groupData['group_status'] = "1";
						$groupData['group_discription'] = $this->params()->fromPost('group_discription');
						$objuser = new User();
						$groupData['group_added_ip_address'] = $objuser->getUserIp();					 
						$groupData['group_view_counter'] = "0";					 
						$group->exchangeArray($groupData);	
						$insert_group_id = $this->getGroupTable()->saveGroup($group); 
						if(!empty($insert_group_id)){
							$photoData = array();
							$photoData['group_photo_photo'] = $fileName;
							$photoData['group_photo_group_id'] = $insert_group_id;	
							$photo = new GroupPhoto();
							$photo->exchangeArray($photoData);	
							$insertedPhotoId = $this->getGroupPhotoTable()->savePhoto($photo);	 						
						}
						return $this->redirect()->toRoute('jadmin/admin-galaxy');				 
					} 
				} 
			}  
			return array('form' => $form, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    }
	public function getSeoTitleAction(){
		$error ='';
		$error_count =0;
		$return_array = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$string = '';
		if ($auth->hasIdentity()) {
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$string = trim($post['group_title']);		
				$string = preg_replace('/(\W\B)/', '',  $string);		
				$string = preg_replace('/[\W]+/',  '_', $string);		
				$string = str_replace('-', '_', $string);
				if($this->checkSeotitleExist($string)){							
					$length = 5;
					$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
					$string = strtolower($string).'_'.$randomString;
					if(!$this->checkSeotitleExist($string)){
						$string = strtolower($string).'_'.time(); 
					} 
				}
			}else{
				$error = 'Invalid access';
				$error_count++;
			}
		}else{
			$error = 'Your session expired';
			$error_count++;
		}
		$return_array['seotitle'] = $string;		  
		echo json_encode($return_array);die();
	}
	public function checkSeotitleExist($seo_title){		 
		if($this->getGroupTable()->checkSeotitleExist($seo_title)){
			return true;				
		}
		else{
			return false;
		}
	}
    public function editAction()
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
			$sm = $this->getServiceLocator();
			$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
			$config = $this->getServiceLocator()->get('Config');		
			$id = (int)$this->params('id');
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-galaxy');
			}        
			$groupData =array();
			$groupData = $this->getGroupTable()->getGroup($id);	
			if(!isset($groupData->group_id) || empty($groupData->group_id)){
				return $this->redirect()->toRoute('jadmin/admin-galaxy', array('action'=>'index'));
			}	
			$groupPhoto =array();
			$groupPhoto = $this->getGroupPhotoTable()->getGalexyPhoto($groupData->group_id);
			$form = new AdminGalaxyEditForm();
			$form->bind($groupData);
			$form->get('submit')->setAttribute('value', 'Edit');        
			$request = $this->getRequest();
			if ($request->isPost()) {
				$group = new Groups();
				$form->setInputFilter(new AdminGalaxyEditFilter($dbAdapter, $id));					 
				$form->setData($request->getPost());
				$nonFile = $request->getPost()->toArray();
				$File = $this->params()->fromFiles('galaxy_image');
				$data = array_merge(
					$nonFile, 
					 array('fileupload'=> $File['name']) 
				 );   		 
				 $addGroup = false; 	
				 $group_photo_id ="";	 
				 if ($form->isValid()) { 	
					 if(isset($File['name']) && !empty($File['name'])){				 	
						####################################### IF user has upload New File###########################################
						$size = new Size(array('min'=>$this->Group_Minumum_Bytes));
						$adapter = new \Zend\File\Transfer\Adapter\Http();					
						$adapter->setValidators(array($size), $File['name']);
						$adapter->addValidator('Extension', false, 'jpg,png,gif');
						if (!$adapter->isValid()){							
							$dataError = $adapter->getMessages();							 
							foreach($dataError as $key=>$row)
							{
								$error[] = $row;
							}
							$addGroup = false;
							$form->setMessages(array('fileupload'=>$error ));
						}
					}
					if(empty($error)){
						if(isset($File['name']) && !empty($File['name'])){ 
							$fileName ="";
							$string = '';
							$length = 5;
							$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
							$string = strtolower($string).'_'.$randomString;
							$fileName = time().$string.$File['name'];
							move_uploaded_file($File['tmp_name'],$config['pathInfo']['UploadPath'].'galexy/'.$fileName);
							@unlink($config['pathInfo']['UploadPath'].'galexy/'.$groupPhoto->group_photo_photo);
							$photoData = array();							 
							$photoData['group_photo_photo'] = $fileName;
							$photoData['group_photo_group_id'] = $id;
							$photoData['group_photo_id'] = $groupPhoto->group_photo_id;	
							$photo = new GroupPhoto();
							$photo->exchangeArray($photoData);	
							$this->getGroupPhotoTable()->savePhoto($photo);
						}
						$arrgroupData = array();	
						$arrgroupData['group_id'] = $id;	
						$arrgroupData['group_title'] = $this->params()->fromPost('group_title');
						$arrgroupData['group_seo_title'] = $this->params()->fromPost('group_seo_title');							 
						$arrgroupData['group_discription'] = $this->params()->fromPost('group_discription');						 				 
						$group->exchangeArray($arrgroupData);	
						$this->getGroupTable()->saveGroup($group); 							
					 }				  
				}	 				   			 
			}
			$groupPhoto =array();
			
			$groupData = $this->getGroupTable()->getGroup($id);	
			$groupPhoto = $this->getGroupPhotoTable()->getGalexyPhoto($groupData->group_id);
			return array(
				'id' => $id,
				'form' => $form,
				'group' => $groupData,
				'groupPhoto' => $groupPhoto,
				'groupThumb' => $this->groupThumb,			
				'error' => $error, 
				'success' => $success,
				'flashMessages' => $this->flashMessenger()->getMessages(),		
			);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    }
    public function deleteAction()
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
			$id = (int)$this->params('id');
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-galaxy');
			}
			$group =array();
			$group = $this->getGroupTable()->getGroup($id); #Get Group Details			
			if(!isset($group->group_id) || empty($group->group_id)){
				return $this->redirect()->toRoute('jadmin/admin-galaxy', array('action'=>'index'));
			}
			$request = $this->getRequest();
			if ($request->isPost()) {
				$del = $request->getPost()->get('del', 'No');
				if ($del == 'Yes') {
					$id = (int)$request->getPost()->get('id');
					$this->getGroupTable()->deleteGroup($id);
				}				
				return $this->redirect()->toRoute('jadmin/admin-galaxy');
			}	 
			return array(
				'id' => $id,
				'group' => $group,
				'planets_count' => $this->getGroupTable()->getPlanetsCountUnderThisGroup($group->group_id),			 
				'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()
			);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    } 
	public function getGroupTable()
    {
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
            $this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    }	 
	public function getGroupPhotoTable()
    {       
		if (!$this->photoTable) {
            $sm = $this->getServiceLocator();
            $this->photoTable = $sm->get('Groups\Model\GroupPhotoTable');
        }
        return $this->photoTable;
    } 	 
}