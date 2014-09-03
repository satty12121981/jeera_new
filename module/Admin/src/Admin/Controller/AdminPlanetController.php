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

use Groups\Model\Groups;
use Photo\Model\Photo;
use User\Model\User; 
use Groups\Model\UserGroup;
use Groups\Model\UserGroupAddSuggestion;
use Notification\Model\UserNotification;
use Admin\Form\AdminPlanetForm;
use Admin\Form\AdminPlanetFilter;   
use Admin\Form\AdminPlanetEditFilter; 
use Admin\Form\AdminQuestionForm;
use Admin\Form\AdminQuestionFilter; 
class AdminPlanetController extends AbstractActionController
{   
	protected $userTable;		 
	protected $groupTable;		 
	protected $userGroupTable;
	protected $userProfileTable;	 
	protected $photoTable;		  
	protected $userGroupAddSuggestionTable;	
	protected $userNotificationTable; 
	protected $countryTable;
	protected $cityTable; 
	protected $albumDataTable;
	protected $groupTagTable;
	protected $groupQuestionnaireTable;
	protected $groupQuestionnaireOptionsTable;
	protected $activityTable;
	protected $groupQuestionnaireAnswersTable;
	protected $albumTable;
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
			$galaxy = $this->getEvent()->getRouteMatch()->getParam('galaxy');
			$galaxy_id = null;
			if($galaxy!=''&&$galaxy!='All'){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($galaxy);
				if(!empty($groupdetails)&&$groupdetails->group_id!=''){				
					$galaxy_id = $groupdetails->group_id;
				}
			}
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
				case 'galexy':
					$field = 'parent_title';
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
				if(isset($post['planet_search'])&&$post['planet_search']!=''){
					$search = $post['planet_search'];
				}
			}else{
				$search =  $this->getEvent()->getRouteMatch()->getParam('search');
			}
			if($sort==''){
				$sort = 'id';
			}
			$total_tags = $this->getGroupTable()->getCountOfAllPlanet($galaxy_id,$search);
			$total_pages = ceil($total_tags/20);
			$allSubGroupData = array();
			$allSubGroupData = $this->getGroupTable()->fetchAllSubGroups($galaxy_id,20,$offset,$field,$order,$search);			 
			return array('allSubGroupData' => $allSubGroupData,'field'=>$sort,'galaxy'=>$galaxy,'order'=>$order,'search'=>$search,'total_pages'=>$total_pages,'page'=> $page, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());				 
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
			$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');		 		
			$config = $this->getServiceLocator()->get('Config'); 
			$allGroupData = array();	
			$allGroupData = $this->getGroupTable()->fetchAllGroups();
			$selectAllGroup =array();
			$obj_groups = new Groups();
			$selectAllGroup = $obj_groups->selectFormatAllGroup($allGroupData);
			$country = $this->getCountryTable()->selectAllCountryWithoutEncrypt();
			foreach($country as $key=>$value){
				$city = $this->getCityTable()->selectFormatAllCity($key);
				break;
			}
			$form = new AdminPlanetForm($selectAllGroup,$country,$city);
			$form->get('submit')->setAttribute('value', 'Add');
			$request = $this->getRequest();
			if ($request->isPost()) {
				$group = new Groups();				
				$form->setInputFilter(new AdminPlanetFilter($dbAdapter));					 
				$form->setData($request->getPost());
				if ($form->isValid()) {
					$groupData = array();      
					$groupData['group_title'] = $this->params()->fromPost('group_title');
					$groupData['group_seo_title'] = $this->params()->fromPost('group_seo_title');
					$groupData['group_status'] = "1";
					$groupData['group_discription'] = $this->params()->fromPost('group_discription'); 
					$groupData['group_parent_group_id'] = $this->params()->fromPost('group_parent_group_id');					
					$groupData['group_location'] = $this->params()->fromPost('group_location');
					$objUser = new User();
					$groupData['group_added_ip_address'] = $objUser->getUserIp();
					$groupData['group_city_id'] = $this->params()->fromPost('group_city_id');
					$groupData['group_country_id'] = $this->params()->fromPost('group_country_id');					
					$groupData['y2m_group_location_lat'] = $this->params()->fromPost('y2m_group_location_lat');	
					$groupData['y2m_group_location_lng'] = $this->params()->fromPost('y2m_group_location_lng');	
					$groupData['group_web_address'] = $this->params()->fromPost('group_web_address');	
					$groupData['group_welcome_message_members'] = $this->params()->fromPost('group_welcome_message_members');
					$groupData['group_modified_ip_address'] = $objUser->getUserIp();
					$group->exchangeArray($groupData); 
					$insert_id = $this->getGroupTable()->saveSubGroup($group);
					if($this->params()->fromPost('group_owner_id')>0){
						$group_owner_id = $this->params()->fromPost('group_owner_id');
					}else{
						$group_owner_id = 1;
					}
					
					if($insert_id){
						$user_data['user_group_user_id'] = $group_owner_id;
						$user_data['user_group_group_id'] = $insert_id;
						$user_data['user_group_status'] = 1;
						$user_data['user_group_is_owner'] = 1;
						$user_data['user_group_role'] = 1;
						$objUser = new User();
						$user_data['user_group_added_ip_address'] = $objUser->getUserIp();
						$this->getUserGroupTable()->AddMembersTOGroup($user_data);
					}
					if(isset($_FILES)&&!empty($_FILES)){ 
						$album_id =0;											 
						$config = $this->getServiceLocator()->get('Config'); 
						$output_dir = $config['pathInfo']['AlbumUploadPath']; 
						$base_url = $config['pathInfo']['base_url'];
						$imagesizedata = getimagesize($_FILES['group_image']["tmp_name"]); 
						if ($imagesizedata === FALSE)
						{
							$error = 'Image size is not valid';
							$error_count++;
						}else{  
							if ($_FILES['group_image']["error"] > 0){
								$error = $_FILES['group_image']["error"];
								$error_count++;
							}
							else{  
								$newfilename = time().$_FILES['group_image']["name"];
								$target = $output_dir.$insert_id;
								if(!is_dir($target)) {
									mkdir($target);	
								}
								$target = $output_dir.$insert_id ."/main/";
								$target_root = $output_dir.$insert_id;
								if(!is_dir($target)) {
									mkdir($target);	
								}
								$output_dir = $target. $newfilename; 
								move_uploaded_file($_FILES['group_image']["tmp_name"],$output_dir);
								$resizeObj = $this->ResizePlugin();
								$image_path = $base_url.'/public/album/'.$insert_id.'/main/'.$newfilename; 
								$resizeObj->assignImage($image_path);
									//*** 2) Resize image (options: exact, portrait, landscape, auto, crop)
								$resizeObj -> resizeImage(403, 138, 'auto');
								//*** 3) Save image
								$target_small = $target_root."/small/";
								if(!is_dir($target_small)) {
									mkdir($target_small);	
								}
								$resizeObj -> saveImage($target_small.$newfilename, 75);								
								$resizeObj -> resizeImage(403, 138, 'auto');

								//*** 3) Save image
								$target_medium = $target_root."/medium/";
								if(!is_dir($target_medium)) {
									mkdir($target_medium);	
								}
								$resizeObj -> saveImage($target_medium.$newfilename, 75);
								$resizeObj -> resizeImage(1024, 192, 'auto');

								//*** 3) Save image
								$target_medium = $target_root."/cover/";
								if(!is_dir($target_medium)) {
									mkdir($target_medium);	
								}
								$resizeObj -> saveImage($target_medium.$newfilename, 75);
								$photo_data['parent_album_id'] = $album_id;
								$photo_data['added_user_id'] = $group_owner_id;
								$photo_data['data_type'] = 'image';														
								$photo_data['data_content'] = $newfilename;
								$photo_id = $this->getAlbumDataTable()->addToAlbumData($photo_data);
								$planet_data['group_photo_id'] = $photo_id;
								$this->getGroupTable()->updateGroup($planet_data,$insert_id);
								$error_count = 0;
							}
						}
					}				             
					return $this->redirect()->toRoute('jadmin/admin-planet');
				} 
			}
			return array('form' => $form);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    }
	public function ajaxOwnersListAction(){
		$error = array();	 
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$offset = 0 ;
			$limit = 20;
			if($post['page']){
				$offset = ($post['page']-1)*20;
			} 
			$search = '';
			$search = $post['user_name'];
			$users = $this->getUserTable()->getAllUsers($offset,$limit,$search);
			$viewModel->setVariable('users', $users);
		}else{
			$error[] = "Your session expired. Please try again after login";
		}
		$viewModel->setVariable('error', $error);	
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;	
	}
    public function editAction()
    {
		$error = array();	 
		$success = array();
		$error_count = 0;
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
			$group_id = $id;
			if (!$id) {
			return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}		
			$group = $this->getGroupTable()->getSubGroup($id); 
			$group_id  = $group->group_id;
			 
			if(!isset($group->group_id) || empty($group->group_id)){
				return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}		
						 
			$allGroupData = array();	
			$allGroupData = $this->getGroupTable()->fetchAllGroups();
			$selectAllGroup =array();
			$selectAllGroup = Groups::selectFormatAllGroup($allGroupData);
			$country = $this->getCountryTable()->selectAllCountryWithoutEncrypt();
			$groupOwner = $this->getUserGroupTable()->findGroupOwner($group->group_id);
			$group_owner_id = $groupOwner->user_group_user_id;
			if($group->group_country_id!='' && $group->group_country_id!=0){
				$city = $this->getCityTable()->selectFormatAllCity($group->group_country_id);
			}else{
				foreach($country as $key=>$value){
					$city = $this->getCityTable()->selectFormatAllCity($key);
					break;
				}
			}
			$form = new AdminPlanetForm($selectAllGroup,$country,$city,$group->group_parent_group_id,$group->group_country_id,$group->group_city_id,$groupOwner->user_first_name.' '.$groupOwner->user_last_name);
			$form->bind($group);
			$form->get('submit')->setAttribute('value', 'Edit');        
			$request = $this->getRequest();		
			if ($request->isPost()) {		
				 
				$form->setInputFilter(new AdminPlanetEditFilter($dbAdapter, $id));			
				$form->setData($request->getPost());				 		
				if ($form->isValid()) {
					$groupData = array();     
					$groupData['group_id'] = $group_id;
					$groupData['group_title'] = $this->params()->fromPost('group_title');
					$groupData['group_seo_title'] = $this->params()->fromPost('group_seo_title');
					$groupData['group_status'] = "1";
					$groupData['group_discription'] = $this->params()->fromPost('group_discription'); 
					$groupData['group_parent_group_id'] = $this->params()->fromPost('group_parent_group_id');					
					$groupData['group_location'] = $this->params()->fromPost('group_location');
					$objUser = new User();
					$groupData['group_added_ip_address'] = $objUser->getUserIp();
					$groupData['group_city_id'] = $this->params()->fromPost('group_city_id');
					$groupData['group_country_id'] = $this->params()->fromPost('group_country_id');					
					$groupData['y2m_group_location_lat'] = $this->params()->fromPost('y2m_group_location_lat');	
					$groupData['y2m_group_location_lng'] = $this->params()->fromPost('y2m_group_location_lng');	
					$groupData['group_web_address'] = $this->params()->fromPost('group_web_address');	
					$groupData['group_welcome_message_members'] = $this->params()->fromPost('group_welcome_message_members');
					$groupData['group_modified_ip_address'] = $objUser->getUserIp();  
					$groupData['group_modified_timestamp'] = date("Y-m-d H:m:s"); 										
					$this->getGroupTable()->updateGroup($groupData,$group_id);					 
					if($this->params()->fromPost('group_owner_id')>0){
						$group_owner_id = $this->params()->fromPost('group_owner_id');
						$user_data['user_group_user_id'] = $group_owner_id;
						$user_data['user_group_group_id'] = $group_id;
						$user_data['user_group_status'] = 1;
						$user_data['user_group_is_owner'] = 1;
						$user_data['user_group_role'] = 1;
						 
						$objUser = new User();
						$user_data['user_group_added_ip_address'] = $objUser->getUserIp();
						$this->getUserGroupTable()->AddMembersTOGroup($user_data);
					} 
					if(isset($_FILES)&&!empty($_FILES)){ 
						$album_id =0;											 
						$config = $this->getServiceLocator()->get('Config'); 
						$output_dir = $config['pathInfo']['AlbumUploadPath']; 
						$base_url = $config['pathInfo']['base_url'];
						$imagesizedata = getimagesize($_FILES['group_image']["tmp_name"]); 
						if ($imagesizedata === FALSE)
						{
							$error = 'Image size is not valid';
							$error_count++;
						}else{  
							if ($_FILES['group_image']["error"] > 0){
								$error = $_FILES['group_image']["error"];
								$error_count++;
							}
							else{  
								$newfilename = time().$_FILES['group_image']["name"];
								$target = $output_dir.$group_id;
								if(!is_dir($target)) {
									mkdir($target);	
								}
								$target = $output_dir.$group_id ."/main/";
								$target_root = $output_dir.$group_id;
								if(!is_dir($target)) {
									mkdir($target);	
								}
								$output_dir = $target. $newfilename; 
								move_uploaded_file($_FILES['group_image']["tmp_name"],$output_dir);
								$resizeObj = $this->ResizePlugin();
								$image_path = $base_url.'/public/album/'.$group_id.'/main/'.$newfilename; 
								$resizeObj->assignImage($image_path);
									//*** 2) Resize image (options: exact, portrait, landscape, auto, crop)
								$resizeObj -> resizeImage(403, 138, 'auto');
								//*** 3) Save image
								$target_small = $target_root."/small/";
								if(!is_dir($target_small)) {
									mkdir($target_small);	
								}
								$resizeObj -> saveImage($target_small.$newfilename, 75);								
								$resizeObj -> resizeImage(403, 138, 'auto');

								//*** 3) Save image
								$target_medium = $target_root."/medium/";
								if(!is_dir($target_medium)) {
									mkdir($target_medium);	
								}
								$resizeObj -> saveImage($target_medium.$newfilename, 75);
								$resizeObj -> resizeImage(1024, 192, 'auto');

								//*** 3) Save image
								$target_medium = $target_root."/cover/";
								if(!is_dir($target_medium)) {
									mkdir($target_medium);	
								}
								$resizeObj -> saveImage($target_medium.$newfilename, 75);
								$groupPhoto = array(); 
								$group = $this->getGroupTable()->getSubGroup($group_id);
								$groupPhoto = $this->getAlbumDataTable()->getalbumdata($group->group_photo_id);
								$photo_data['parent_album_id'] = $album_id;
								$photo_data['added_user_id'] = $group_owner_id;
								$photo_data['data_type'] = 'image';														
								$photo_data['data_content'] = $newfilename;
								if($group->group_photo_id){
									$photo_data['data_id'] = $group->group_photo_id;
									$this->getAlbumDataTable()->updateAlbumData($photo_data);
								}
								else{
									$photo_id = $this->getAlbumDataTable()->addToAlbumData($photo_data);
									$planet_data['group_photo_id'] = $photo_id;
									$this->getGroupTable()->updateGroup($planet_data,$insert_id);
								}
								@unlink($base_url.'/public/album/'.$group_id.'/main/'.$groupPhoto->data_content);
								@unlink($base_url.'/public/album/'.$group_id.'/small/'.$groupPhoto->data_content);
								@unlink($base_url.'/public/album/'.$group_id.'/medium/'.$groupPhoto->data_content);
								@unlink($base_url.'/public/album/'.$group_id.'/cover/'.$groupPhoto->data_content);
								$error_count = 0;
							}
						}
					} 
					
					// Redirect to list of tags
					return $this->redirect()->toRoute('jadmin/admin-planet');	
            } else {
				echo "Error in Form";
			}
        }
		$groupPhoto = array();
		$groupPhoto = $this->getAlbumDataTable()->getalbumdata($group->group_photo_id);
		
        return array(
            'id' => $id,
            'form' => $form,
			'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages(),
			'group' => $group,
			'groupPhoto' => $groupPhoto,
			'groupOwner'=> $groupOwner,
			//'groupThumb' => $this->Group_Thumb_Path
        );
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    }
	public function viewAction(){
		$error = array();	 
		$success = array();
		$error_count = 0;
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;
			$id = (int)$this->params('id');
			$group_id = $id;
			if (!$id) {
			return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}		
			$group = $this->getGroupTable()->getSubGroup($id); 
			$group_id  = $group->group_id;			
			if(!isset($group->group_id) || empty($group->group_id)){
				return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}
			$groupOwner = $this->getUserGroupTable()->findGroupOwner($group->group_id);
			$grouptags = $this->getGroupTagTable()->fetchAllTagsOfPlanet($group_id);
			$questionnaire = $this->getGroupQuestionnaireTable()->getQuestionnaire($group_id);
			$arr_questionaire = array();
			foreach($questionnaire as $questions){
				$arr_questionaire[] = array('question' => $questions->question,
										'questionnaire_id' => $questions->questionnaire_id,
										'question_status' => $questions->question_status,
										'answer_type' => $questions->answer_type,
										'options'=>$this->getGroupQuestionnaireOptionsTable()->getoptionOfOneQuestion($questions->questionnaire_id),
									);
			}
			return array(
            'id' => $id,
            'group' => $this->getGroupTable()->getPlanetDetailsForPalnetView($id,1),
			'activity_count' => $this->getActivityTable()->getCountOfAllActivities($id),
			'groupOwner'=> $groupOwner,	
			'grouptags'=> $grouptags,
			'questionaire' =>$arr_questionaire,
			'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()
			); 
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
	}
	public function addQuestionsAction(){
		$error = array();	 
		$success = array();
		$error_count = 0;
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;
			$id = (int)$this->params('id');
			$group_id = $id;
			if (!$id) {
			return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}		
			$group = $this->getGroupTable()->getSubGroup($id); 
			$group_id  = $group->group_id;			
			if(!isset($group->group_id) || empty($group->group_id)){
				return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}
			$form = new AdminQuestionForm();
			$form->get('submit')->setAttribute('value', 'Add');
			$request = $this->getRequest();
			if ($request->isPost()) {
				$form->setInputFilter(new AdminQuestionFilter());					 
				$form->setData($request->getPost());
				if ($form->isValid()) { 
					if($this->params()->fromPost('answer_type')=='radio'||$this->params()->fromPost('answer_type')=='checkbox'){
						$options = $this->getRequest()->getPost('option');
						if($options[0]!=''&&$options[1]!=''&&$options[2]!=''){
							$question_data = array();
							$question_data['group_id']=$group_id;
							$question_data['question']=$this->params()->fromPost('question');
							$question_data['question_status']=1;
							$objUser = new User();						 
							$question_data['added_user_id']=1;
							$question_data['added_ip']=$objUser->getUserIp();
							$question_data['answer_type']=$this->params()->fromPost('answer_type');
							$insert_id = $this->getGroupQuestionnaireTable()->AddQuestion($question_data);
							if($insert_id){
								foreach($options as $values){
									$option_data['question_id'] = $insert_id;
									$option_data['option'] = $values;
									$this->getGroupQuestionnaireOptionsTable()->AddOptions($option_data);
								}
							}
							return $this->redirect()->toRoute('jadmin/admin-planet-view', array('id'=>$group_id));
						}else{
							$error[] = 'Add options';
						}
					}else{
						$question_data = array();
						$question_data['group_id']=$group_id;
						$question_data['question']=$this->params()->fromPost('question');
						$question_data['question_status']=1;
						$objUser = new User();						 
						$question_data['added_ip']=$objUser->getUserIp();
						$question_data['answer_type']=$this->params()->fromPost('answer_type');
						$insert_id = $this->getGroupQuestionnaireTable()->AddQuestion($question_data);
						return $this->redirect()->toRoute('jadmin/admin-planet-view', array('id'=>$group_id));
					}
					
				}
			}
			return array(
				'group_id' => $id,
				'form' => $form,
				'success' => $success,
				'flashMessages' => $this->flashMessenger()->getMessages(),
				'error' => $error,
			);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
	}
	public function editQuestionsAction(){
		$error = array();	 
		$success = array();
		$error_count = 0;
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;
			$id = (int)$this->params('id');
			$question_id = $id;
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}
			$question_data = $this->getGroupQuestionnaireTable()->getQuestionFromQuestionId($id);							
			if(!isset($question_data->questionnaire_id) || empty($question_data->questionnaire_id)){
				return $this->redirect()->toRoute('admin/admin-planet', array('action'=>'index'));
			}
			$group = $this->getGroupTable()->getSubGroup($question_data->group_id); 
			$group_id  = $group->group_id;
			$form = new AdminQuestionForm($question_data->question,$question_data->answer_type);
			$form->get('submit')->setAttribute('value', 'Edit');
			$request = $this->getRequest();
			if ($request->isPost()) {
				$form->setInputFilter(new AdminQuestionFilter());					 
				$form->setData($request->getPost());
				if ($form->isValid()) { 
					if($this->params()->fromPost('answer_type')=='radio'||$this->params()->fromPost('answer_type')=='checkbox'){
						$options = $this->getRequest()->getPost('option');
						if($options[0]!=''&&$options[1]!=''&&$options[2]!=''){
							$question_data = array();							 
							$question_data['question']=$this->params()->fromPost('question');
							$question_data['question_status']=1;
							$objUser = new User();						 
							$question_data['modified_ip']=$objUser->getUserIp();
							$question_data['modified_timestamp']=date("Y-m-d H:i:s");
							$question_data['modified_user_id']=1;
							$question_data['answer_type']=$this->params()->fromPost('answer_type');
							$this->getGroupQuestionnaireTable()->updateQuestion($question_data,$question_id);
							$opton_data = $this->getGroupQuestionnaireOptionsTable()->getoptionOfOneQuestion($question_id);
							$i = 0;
							foreach($opton_data as $rows){								 
								$option_data['option'] = $options[$i];
								$this->getGroupQuestionnaireOptionsTable()->UpdateOptions($option_data,$rows->option_id);
								$i++;
							}
							if($i == 0){
								foreach($options as $values){
									$option_data['question_id'] = $question_id;
									$option_data['option'] = $values;
									$this->getGroupQuestionnaireOptionsTable()->AddOptions($option_data);
								}
							}
							return $this->redirect()->toRoute('jadmin/admin-planet-view', array('id'=>$group_id));
						}else{
							$error[] = 'Add options';
						}
					}else{
						$question_data = array();							 
						$question_data['question']=$this->params()->fromPost('question');
						$question_data['question_status']=1;
						$objUser = new User();						 
						$question_data['modified_ip']=$objUser->getUserIp();
						$question_data['modified_timestamp']=date("Y-m-d H:i:s");
						$question_data['modified_user_id']=1;
						$question_data['answer_type']=$this->params()->fromPost('answer_type');
						$this->getGroupQuestionnaireTable()->updateQuestion($question_data);
						return $this->redirect()->toRoute('jadmin/admin-planet-view', array('id'=>$group_id));
					}
					
				}
			}
			return array(
				'question_id' => $question_id,
				'question_data' =>$this->getGroupQuestionnaireTable()->getQuestionFromQuestionId($question_id),
				'option_data' => $this->getGroupQuestionnaireOptionsTable()->getoptionOfOneQuestion($question_id),
				'form' => $form,
				'success' => $success,
				'flashMessages' => $this->flashMessenger()->getMessages(),
				'error' => $error,
			);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
	}
	public function statusQuestionsAction(){
		$error = array();	 
		$success = array();
		$error_count = 0;
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;
			$id = (int)$this->params('id');
			$question_id = $id;
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}
			$question= $this->getGroupQuestionnaireTable()->getQuestionFromQuestionId($id);	 
			if(!isset($question->questionnaire_id) || empty($question->questionnaire_id)){
				return $this->redirect()->toRoute('admin/admin-planet', array('action'=>'index'));
			}
			if($question->question_status){ 
				$question_data['question_status']=0;
				$objUser = new User();						 
				$question_data['modified_ip']=$objUser->getUserIp();
				$question_data['modified_timestamp']=date("Y-m-d H:i:s");
				$question_data['modified_user_id']=1;				 
				$this->getGroupQuestionnaireTable()->updateQuestion($question_data,$question_id);
			}else{ 
				$question_data['question_status']=1;
				$objUser = new User();						 
				$question_data['modified_ip']=$objUser->getUserIp();
				$question_data['modified_timestamp']=date("Y-m-d H:i:s");
				$question_data['modified_user_id']=1;				 
				$this->getGroupQuestionnaireTable()->updateQuestion($question_data,$question_id);
			}
			return $this->redirect()->toRoute('jadmin/admin-planet-view', array('id'=>$question->group_id)); 
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
	}
	public function deleteQuestionsAction(){
		$error = array();	 
		$success = array();
		$error_count = 0;
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;
			$id = (int)$this->params('id');
			$question_id = $id;
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}
			$question= $this->getGroupQuestionnaireTable()->getQuestionFromQuestionId($id);
			$this->getGroupQuestionnaireAnswersTable()->deleteAnswers($question_id);
			$this->getGroupQuestionnaireOptionsTable()->DeleteOptions($question_id);
			$this->getGroupQuestionnaireTable()->DeleteQuestions($question_id);
			if(!isset($question->questionnaire_id) || empty($question->questionnaire_id)){
				return $this->redirect()->toRoute('admin/admin-planet', array('action'=>'index'));
			}
			 
			return $this->redirect()->toRoute('jadmin/admin-planet-view', array('id'=>$question->group_id)); 
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
	}
    public function deleteAction()
    {
		$error = array();	 
		$success = array();
		$error_count = 0;
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$identity = null; 
		$this->layout('layout/admin_page');	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;
			$id = (int)$this->params('id');
			if (!$id) {
				return $this->redirect()->toRoute('jadmin/admin-planet');
			}		
			$group = $this->getGroupTable()->getSubGroup($id); 
			if(!isset($group->group_id) || empty($group->group_id)){
				return $this->redirect()->toRoute('jadmin/admin-planet', array('action'=>'index'));
			}
			$request = $this->getRequest();
			if ($request->isPost()) {			   
				$del = $request->getPost()->get('planet_delete', 'No');
				if ($del == 'Yes') { 
					
				} 
				return $this->redirect()->toRoute('jadmin/admin-planet');
			}	 
			return array(
				'id' => $id,
				'group' => $this->getGroupTable()->getSubGroup($id),
				'activity_count' => $this->getActivityTable()->getCountOfAllActivities($id),
				'group_member_couunt'=>$this->getUserGroupTable()->countGroupMembers($id)->memberCount,
				'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()
			);
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
    }
	public function removeAlbumDetailsAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $post['group_id'];
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){
					$album_details = $this->getAlbumTable()->getAllGroupAlbumDetails($group->group_id);
					$config = $this->getServiceLocator()->get('Config'); 
					$output_dir = $config['pathInfo']['AlbumUploadPath'];
					foreach($album_details as $details){
						if($details->data_type == 'image'){
						 
							@unlink($output_dir.$group_id.'/'.$details->album_id.'/'.$details->data_content);
							@unlink($output_dir.$group_id.'/'.$details->album_id.'/medium/'.$details->data_content);
							@unlink($output_dir.$group_id.'/'.$details->album_id.'/small/'.$details->data_content);
						}
					}
					if($this->getAlbumTable()->RemoveAllGroupAlbumDetails($group->group_id)){
						$error_count = 0;
						$error = "";
					}else{
						$error_count ++;
						$error = "Some error occured. Please try again";
					}
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function removeDiscussionDetailsAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $post['group_id'];
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){					 
					if($this->getGroupTable()->RemoveAllGroupDiscussionDetails($group->group_id)){
						$error_count = 0;
						$error = "";
					}else{
						$error_count ++;
						$error = "Some error occured. Please try again";
					}
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function removeActivitiesAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $post['group_id'];
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){					 
					if($this->getActivityTable()->RemoveAllGroupActivitiesAndRelatedInfos($group->group_id)){
						$error_count = 0;
						$error = "";
					}else{
						$error_count ++;
						$error = "Some error occured. Please try again";
					}
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function removeMembersAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $post['group_id'];
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){					 
					if($this->getUserGroupTable()->RemoveAllGroupMembersWithPermissions($group->group_id)){
						$error_count = 0;
						$error = "";
					}else{
						$error_count ++;
						$error = "Some error occured. Please try again";
					}
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function removeQuestionnaireAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $post['group_id'];
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){					 
					if($this->getGroupQuestionnaireTable()->RemoveAllGroupQuestionnairesAndAnswers($group->group_id)){
						$error_count = 0;
						$error = "";
					}else{
						$error_count ++;
						$error = "Some error occured. Please try again";
					}
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function removeTagsAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $post['group_id'];
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){					 
					if($this->getGroupTagTable()->RemoveAllGroupTags($group->group_id)){
						$error_count = 0;
						$error = "";
					}else{
						$error_count ++;
						$error = "Some error occured. Please try again";
					}
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function removeSettingsAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $post['group_id'];
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){					 
					if($this->getGroupTable()->RemoveAllGroupSettigns($group->group_id)){
						$error_count = 0;
						$error = "";
					}else{
						$error_count ++;
						$error = "Some error occured. Please try again";
					}
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function removeGroupAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $post['group_id'];
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){					 
					if($this->getGroupTable()->deleteSubGroup($group->group_id)){
						$error_count = 0;
						$error = "";
					}else{
						$error_count ++;
						$error = "Some error occured. Please try again";
					}
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function approvelistAction(){ 	
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
			$galaxy = $this->getEvent()->getRouteMatch()->getParam('galaxy');
			$galaxy_id = null;
			if($galaxy!=''&&$galaxy!='All'){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($galaxy);
				if(!empty($groupdetails)&&$groupdetails->group_id!=''){				
					$galaxy_id = $groupdetails->group_id;
				}
			}
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
				case 'galexy':
					$field = 'parent_title';
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
				if(isset($post['planet_search'])&&$post['planet_search']!=''){
					$search = $post['planet_search'];
				}
			}else{
				$search =  $this->getEvent()->getRouteMatch()->getParam('search');
			}
			if($sort==''){
				$sort = 'id';
			}
			$total_tags = $this->getGroupTable()->getCountOfAllUnapprovedPlanet($galaxy_id,$search);
			$total_pages = ceil($total_tags/20);
			$allSubGroupData = array();
			$allSubGroupData = $this->getGroupTable()->fetchAllUnapprovedSubGroups($galaxy_id,20,$offset,$field,$order,$search);			 
			return array('allSubGroupData' => $allSubGroupData,'field'=>$sort,'galaxy'=>$galaxy,'order'=>$order,'search'=>$search,'total_pages'=>$total_pages,'page'=> $page, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());				 
		}else{
			return $this->redirect()->toRoute('jadmin/login', array('action' => 'login'));	
		}
	}	
	public function approveAction(){
		$error = array();	
		$error_count = 0;
		$success = array();
		$auth = new AuthenticationService();
		$auth->setStorage(new SessionStorage('admin'));
		$request = $this->getRequest();
		$identity = null; 		
		$viewModel = new ViewModel();
		$status		=0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$group_id =  $this->getEvent()->getRouteMatch()->getParam('planet_id');
			if ($group_id) {
				$group = $this->getGroupTable()->getSubGroup($group_id); 
				if(isset($group->group_id) && !empty($group->group_id)){					 
					 if($group->group_status){
						if($this->getGroupTable()->changeGroupsStatus($group->group_id,0)){
							$error_count =0;
							$error = "";
							$status		=0;
						}else{
							$error_count ++;
							$error = "Some error occured. Please try again";

						}
					 }else{
						if($this->getGroupTable()->changeGroupsStatus($group->group_id,1)){
							$error_count =0;
							$error = "";
							$status		=1;
						}else{
							$error_count ++;
							$error = "Some error occured. Please try again";
						}
					 }
				}else{	
					$error_count ++;
					$error = "This group is not existing";
				}
			}else{	
				$error_count ++;
				$error = "This group is not existing";
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
	public function getUserProfileTable()
    {
        if (!$this->userProfileTable) {
            $sm = $this->getServiceLocator();
            $this->userProfileTable = $sm->get('User\Model\UserProfileTable');
        }
        return $this->userProfileTable;
    }
	public function getGroupTable()
    {
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
            $this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    }
	public function getUserGroupTable()
    {
        if (!$this->userGroupTable) {
            $sm = $this->getServiceLocator();
			$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
        }
        return $this->userGroupTable;
    }
    public function getUserNotificationTable()
    {
        if (!$this->userNotificationTable) {
            $sm = $this->getServiceLocator();
            $this->userNotificationTable = $sm->get('Notification\Model\UserNotificationTable');
        }
        return $this->userNotificationTable;
    }
	public function getPhotoTable()
    {
        if (!$this->photoTable) {
            $sm = $this->getServiceLocator();
            $this->photoTable = $sm->get('Photo\Model\PhotoTable');
        }
        return $this->photoTable;
    }
    public function getUserGroupAddSuggestionTable()
    {
        if (!$this->userGroupAddSuggestionTable) {
            $sm = $this->getServiceLocator();
            $this->userGroupAddSuggestionTable = $sm->get('Groups\Model\UserGroupAddSuggestionTable');
        }
        return $this->userGroupAddSuggestionTable;
    } 	
	public function getCountryTable()
    {
        if (!$this->countryTable) {
            $sm = $this->getServiceLocator();
            $this->countryTable = $sm->get('Country\Model\CountryTable');
        }
        return $this->countryTable;
    }	
	public function getCityTable()
    {
        if (!$this->cityTable) {
            $sm = $this->getServiceLocator();
            $this->cityTable = $sm->get('City\Model\CityTable');
        }
        return $this->cityTable;
    }
	public function getAlbumTable(){
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
	public function getAlbumDataTable(){
        if (!$this->albumDataTable) {
            $sm = $this->getServiceLocator();
            $this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
        }
        return $this->albumDataTable;
    }
	public function getGroupTagTable()
    {
        if (!$this->groupTagTable) {
            $sm = $this->getServiceLocator();
            $this->groupTagTable = $sm->get('Tag\Model\GroupTagTable');
        }
        return $this->groupTagTable;
    }
	public function getActivityTable()
    {
        if (!$this->activityTable) {
            $sm = $this->getServiceLocator();
            $this->activityTable = $sm->get('Activity\Model\ActivityTable');
        }
        return $this->activityTable;
    }
	public function getGroupQuestionnaireTable(){
		if (!$this->groupQuestionnaireTable) {
            $sm = $this->getServiceLocator();
			$this->groupQuestionnaireTable = $sm->get('Groups\Model\GroupJoiningQuestionnaireTable');
        }
        return $this->groupQuestionnaireTable;
	}
	public function getGroupQuestionnaireOptionsTable(){
		if (!$this->groupQuestionnaireOptionsTable) {
            $sm = $this->getServiceLocator();
			$this->groupQuestionnaireOptionsTable = $sm->get('Groups\Model\GroupQuestionnaireOptionsTable');
        }
        return $this->groupQuestionnaireOptionsTable;
	}
	public function getGroupQuestionnaireAnswersTable(){
		if (!$this->groupQuestionnaireAnswersTable) {
            $sm = $this->getServiceLocator();
			$this->groupQuestionnaireAnswersTable = $sm->get('Groups\Model\GroupQuestionnaireAnswersTable');
        }
        return $this->groupQuestionnaireAnswersTable;
	}
}