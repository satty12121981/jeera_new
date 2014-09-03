<?php
namespace Album\Controller;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;       
use Album\Model\AlbumData; 
use Album\Model\AlbumDataTable;
use Album\Model\AlbumTag; 
use Album\Model\AlbumTagTable;
use Album\Form\AlbumForm;      
use Groups\Model\GroupsTable;
use Groups\Model\UserGroup;
use Notification\Model\UserNotification;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
class AlbumController extends AbstractActionController
{		
	protected $albumTable;
	protected $albumDataTable;
	protected $albumTagTable;
	protected $groupTable;
	protected $userGroupTable;
	protected $likeTable;
	protected $commentTable;
	protected $userNotificationTable;
	protected $userTable;
	protected function getViewHelper($helperName){
    	return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}
	public function getAlbumTable(){
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
	public function getLikeTable(){
        if (!$this->likeTable) {
            $sm = $this->getServiceLocator();
            $this->likeTable = $sm->get('Like\Model\LikeTable');
        }
        return $this->likeTable;
    }
	public function getCommentTable(){
        if (!$this->commentTable) {
            $sm = $this->getServiceLocator();
            $this->commentTable = $sm->get('Comment\Model\CommentTable');
        }
        return $this->commentTable;
    }
	public function getAlbumDataTable(){
        if (!$this->albumDataTable) {
            $sm = $this->getServiceLocator();
            $this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
        }
        return $this->albumDataTable;
    }
	public function getAlbumTagTable(){
        if (!$this->albumTagTable) {
            $sm = $this->getServiceLocator();
            $this->albumTagTable = $sm->get('Album\Model\AlbumTagTable');
        }
        return $this->albumTagTable;
    }
	public function getGroupTable(){
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    } 
	public function getUserGroupTable(){
        if (!$this->userGroupTable) {
            $sm = $this->getServiceLocator();
			$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
        }
        return $this->userGroupTable;
    } 	
    public function indexAction(){ 
			$error = array(); 
			$this->layout('layout/planet_home');		 
			$auth = new AuthenticationService();	
		    $identity = null;    
			$user_id  = null;
			$mainViewModel = new ViewModel();
			if ($auth->hasIdentity()) {  
				$request   = $this->getRequest();	
				$identity = $auth->getIdentity();
				$user_id = $identity->user_id; 
				$this->layout()->identity = $identity;			
				$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
				$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id');
				if($group_seo!=''&&$planet_seo!=''){
					$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
					$group_id = $groupdetails->group_id;
					$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
					$planet_id = $planetdetails->group_id;
					if($group_id && $planet_id){
						$groupTopWidget = $this->forward()->dispatch('Groups\Controller\Groups', array(
						'action' => 'groupTop',
						'group_id'     => $group_seo,
						'sub_group_id' => $planet_seo,
						));						
						$mainViewModel->addChild($groupTopWidget, 'groupTopWidget');
						$albums =   $this->getAlbumTable()->fetchAll($planet_id);
						$mainViewModel->setVariable('albums', $albums);
						$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
						$mainViewModel->setVariable('planetdetails', $planet_data);
						$form = new AlbumForm();
						$mainViewModel->setVariable('form', $form);
						
					}else{
						$error[] = "Enter a valid planet";
					}						
				}
				else{	
					$error[] = "Specify planet...";
				}				
			}else{
				return $this->redirect()->toRoute('user/login', array('action' => 'login'));
			}
			$mainViewModel->setVariable('error', $error);			
			return $mainViewModel;				
    }  
    public function addAction(){
			$sm = $this->getServiceLocator(); 
			$basePath = $sm->get('Request')->getBasePath();				
			$this->getViewHelper('HeadScript')->appendFile($basePath.'/public/js/jquery.min.js','text/javascript');
			$this->getViewHelper('HeadScript')->appendFile($basePath.'/js/popscript.js','text/javascript');		
			$this->getViewHelper('HeadScript')->appendFile($basePath.'/public/js/album.js','text/javascript');		
			$auth = new AuthenticationService();	
		    $identity = null;    
			$user_id  = null;
			$request   = $this->getRequest();
			$groupId = $this->params('group_id');			
			if ($auth->hasIdentity()) {  
				$identity = $auth->getIdentity();
				$user_id = $identity->user_id; 
				$form = new AlbumForm($user_id,$groupId);
				$form->get('submit')->setValue('Add');	
				if ($request->isPost()) {   
					$album = new Album();			
					$form->setInputFilter($album->getInputFilter());
					$form->setData($request->getPost());
					if ($form->isValid()) {
						$post = $request->getPost();	
						$albumdetails = array();
						$albumdetails['album_group_id'] = $post->get('album_group_id');
						$albumdetails['album_user_id'] = $post->get('album_user_id');
						$albumdetails['album_title'] = $post->get('album_title');
						$albumdetails['album_location'] = $post->get('album_location');
						$albumdetails['album_status'] = 1;				
						$planet_video = $post->get('planet_video');
						$planet_image = $post->get('planet_image');
						$albumObject = new Album();
						$albumObject->exchangeArray($albumdetails);                
						$insert_id = $this->getAlbumTable()->saveAlbum($albumObject);
						if($insert_id){
							if(count($planet_video) || count($planet_image)){
								$albumdataobject = new AlbumData();
								if(count($planet_image > 0)){
									foreach($planet_image as $row){
										if($row != ""){
											$albumdata = array();
											$albumdata['parent_album_id'] = $insert_id;
											$albumdata['data_type'] = "image";
											$albumdata['data_content'] = $row;
											$albumdataobject->exchangeArray($albumdata);
											//$table = new AlbumDataTable();
											$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
										}
									}
								}
								if(count($planet_video > 0)){
									foreach($planet_video as $row1){
										if($row1 != ""){
											$albumdata = array();
											$albumdata['parent_album_id'] = $insert_id;
											$albumdata['data_type'] = "youtube";
											$albumdata['data_content'] = $row1;
											$albumdataobject->exchangeArray($albumdata);
											$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
										}
									}
								}
							}  
				
						}                 
						return $this->redirect()->toRoute('album/album-view', array(
											'controller' => 'album',
											'action' =>  'view',
											'id' => $insert_id
										));
					}
				}	
				return array('form' => $form,);
			}
			else{
			return $this->redirect()->toRoute('application');
			}
    }	 
	public function addmoreAction(){
		$album_group_id = $_POST['album_group_id'];
		$album_id = $_POST['album_id'];
		$planet_video = $_POST['planet_video'];
		$planet_image = $_POST['planet_image'];
		if(count($planet_video) || count($planet_image)){
			$albumdataobject = new AlbumData();
			if(count($planet_image > 0)){
				foreach($planet_image as $row){
					if($row != ""){
						$albumdata = array();
						$albumdata['parent_album_id'] = $album_id;
						$albumdata['data_type'] = "image";
						$albumdata['data_content'] = $row;
						$albumdataobject->exchangeArray($albumdata);								
						$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
					}
				}
			}
			if(count($planet_video > 0)){
				foreach($planet_video as $row1){
					if($row1 != ""){
						$albumdata = array();
						$albumdata['parent_album_id'] = $album_id;
						$albumdata['data_type'] = "youtube";
						$albumdata['data_content'] = $row1;
						$albumdataobject->exchangeArray($albumdata);
						$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
					}
				}
			}					
			echo "success"; exit;
		}	
	}
	public function viewAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$mainViewModel = new ViewModel();
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$user_id = $identity->user_id; 
					$mainViewModel->setVariable('user_id', $user_id); 
					$album_seo = $this->getEvent()->getRouteMatch()->getParam('id'); 
					if($album_seo!='') {
						$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
						if(!empty($album_details)&&$album_details->album_id!=''){
							$is_member = $this->getGroupTable()->is_member($planet_id,$user_id);
							if($is_member){						 
								$groupTopWidget = $this->forward()->dispatch('Groups\Controller\Groups', array(
								'action' => 'groupTop',
								'group_id'     => $group_seo,
								'sub_group_id' => $planet_seo,
								));						
								$mainViewModel->addChild($groupTopWidget, 'groupTopWidget');								 
								$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
								$mainViewModel->setVariable('planetdetails', $planet_data); 
								$album_data = $this->getAlbumDataTable()->getAlbum($album_details->album_id,12,0);
								$mainViewModel->setVariable('album_data', $album_data); 
								$mainViewModel->setVariable('album_details', $album_details); 
							}else{
								$error[] = "You don't have the permission to access this album";
							}						
						}else{
							$error[] = "Sorry we can't find this album..";
						}
					}else{
						$error[] = "Sorry we can't find this album..";
					}
				}else{
						$error[] = "Sorry we can't find this album..";
					}
			}else{
				$error[] = "Sorry we can't find this album..";
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}		
		return $mainViewModel;	 
	} 
	public function singleAction(){			
		$album_id =  (int)$this->params('id');
		$panel_num =  (int)$this->params('panel');
        if (!$album_id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'index'
            ));
        }		
		$album_details = $this->getAlbumTable()->getalbumrow($album_id);
		$album_data = $this->getAlbumDataTable()->getAlbum($album_id);		
		$planetdetails       = $this->getGroupTable()->getSubGroup($album_details->album_group_id);
		$groupseoTitle		 = $this->getGroupTable()->getSeotitle($planetdetails->group_parent_group_id);		
		$album_all_data = array();
		$i = 0;
		foreach($album_data as $datas){	
			$album_all_data[$i]['data_type'] = $datas['data_type'];
			$album_all_data[$i]['data_id'] = $datas['data_id'];
			$album_all_data[$i]['album_group_id'] = $datas['album_group_id'];
			$album_all_data[$i]['data_content'] = $datas['data_content'];
			$album_all_data[$i]['album_tags'] =  $this->getAlbumTagTable()->getTags($datas['data_id']);
			$i++;		
		} 
		return array(
			'album_id' => $album_id,
			'panel_num' => $panel_num,
		    'album_title' => $album_details->album_title,
            'album_data' => $album_all_data,
			'album_group_id' =>  $album_details->album_group_id,
			'group_id' =>$groupseoTitle->group_seo_title,
			'sub_group_id'=>$planetdetails->group_seo_title
        );
	}
	public function coverpicAction(){		
		$data_id =  (int)$this->params('data_id');
		$album_id =  (int)$this->params('album_id');
		if($this->getAlbumTable()->addcoverpic($data_id,$album_id)){
			echo "success";
			exit;
		}		
	}
    public function editAction(){
		$albumdetails['album_id'] = $_POST['album_edit_id'];
		$albumdetails['album_title'] = $_POST['album_title'];
		$albumdetails['album_location'] = $_POST['album_location'];
		$albumObject = new Album();
		$albumObject->exchangeArray($albumdetails);
		$this->getAlbumTable()->updateAlbum($albumObject);
		exit;
    }
    public function deleteAction(){ 
        $error =0;
		$msg = '';
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		
		if ($auth->hasIdentity()) {  
			$request   = $this->getRequest();	
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;				
			$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id');		 
			if($album_seo!=''){
				$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
				$album_id = $album_details->album_id;			 
				if($album_id){					
					if($album_details->album_user_id == $user_id){
						$album_data = $this->getAlbumDataTable()->getAlbum($album_details->album_id);						
						$this->getAlbumTable()->deleteAlbum($album_details->album_group_id,$album_id);
						$config = $this->getServiceLocator()->get('Config');
						$output_dir = $config['pathInfo']['AlbumUploadPath'];
						foreach($album_data as $data){
							unlink($output_dir.$album_details->album_group_id."\\".$album_details->album_id);
						}						
						$msg = "";
						$error =0;													
					}
					else{
						$msg = "You don't have the permissions to do this";
						$error =1;
					}
				}else{
					$msg = "This album is not existing in the system";
					$error =1;
				}
			}else{
				$msg = "This album is not existing in the system";
				$error =1;
			}
		}else{
			$msg = "Your session expired. please try again after logged in";
			$error =1;
		}
		$return_array = array();
		if($error) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
    }
	public function deletedataAction(){ 
         $data_id = (int) $this->params()->fromRoute('data_id', 0);		 
        if (!$data_id) {
            return $this->redirect()->toRoute('album');
        }
		if($this->getAlbumDataTable()->deletedataAlbum($data_id)){
			$this->getAlbumTable()->updatecoverAlbum($data_id);
			return "success";
			exit;
		}    
	}
	public function uploadAction(){	
		$config = $this->getServiceLocator()->get('Config');
		$output_dir = $config['pathInfo']['AlbumUploadPath']; 
		$base_url = $config['pathInfo']['base_url'];
		$planet_id = (int) $this->params()->fromRoute('id', 0);        
		if(isset($_FILES["file"]))
		{
		 $imagesizedata = getimagesize($_FILES["file"]["tmp_name"]);
				if ($imagesizedata === FALSE)
				{
					echo "error";
					//not image
				}
				else{
						//Filter the file types , if you want.
						if ($_FILES["file"]["error"] > 0)
						{
						//echo "Error: " . $_FILES["file"]["error"] . " ";
						echo "error";
						}
						else
						{
						$ran = rand () ;
						$newfilename = rand(1,99999).".".end(explode(".",$_FILES["file"]["name"]));
						$ran2 = $ran.".";
						$ext = $this->findexts($_FILES["file"]["name"]);
						
						 $target = $output_dir.$planet_id."\\";
						 
						if(!is_dir($target)) {
							mkdir($target);	
						}
						
						$output_dir = $target . $newfilename; 
						 move_uploaded_file($_FILES["file"]["tmp_name"],$output_dir);
						//$base_url = $_SERVER['SERVER_NAME']; 
						$resizeObj = $this->ResizePlugin();
						$image_path = $base_url.'/public/album/'.$planet_id.'/'.$newfilename; 
						$resizeObj->assignImage($image_path);

						//*** 2) Resize image (options: exact, portrait, landscape, auto, crop)
						$resizeObj -> resizeImage(276, 100, 'auto');

						//*** 3) Save image
						$target_small = $target."small\\";
						if(!is_dir($target_small)) {
							mkdir($target_small);	
						}
						$resizeObj -> saveImage($target_small."small-".$newfilename, 75);
						
						$resizeObj -> resizeImage(390, 700, 'auto');

						//*** 3) Save image
						$target_medium = $target."medium\\";
						if(!is_dir($target_medium)) {
							mkdir($target_medium);	
						}
						$resizeObj -> saveImage($target_medium."medium-".$newfilename, 75);
						
						echo $newfilename;
						exit;
						}
				}
		}	
	}
	public function findexts($filename){ 
		 $filename = strtolower($filename) ; 
		 $exts = preg_split("[/\\.]", $filename) ; 
		 $n = count($exts)-1; 
		 $exts = $exts[$n]; 
		 return $exts; 
	} 
	public function usertagqueryAction(){
		 $term = $_GET['term'];
		 $planet_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
		 $planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
		 $planet_id = $planetdetails->group_id;
		 $userList =$this->getUserGroupTable()->fetchAllUserListForTag($planet_id,$term);
		 //print_r($userList);
		 foreach($userList as $row) {
		  $return_arr[] = array(
				'id'    => $row->user_group_user_id,
				'value' => $row->user_given_name
			); 
	    }
		 echo json_encode($return_arr);
		 exit;
	}
	public function addTagAction(){
		 $sm = $this->getServiceLocator();
		 $data_id = $this->params('data_id');
		 $tag_id  = $this->params('tag_id');
		 $x_axis  = $this->params('x_axis');
		 $y_axis  = $this->params('y_axis');
		 $request   = $this->getRequest();	
		 $post = $request->getPost();
		 $page = $post['page'];
		 $auth = new AuthenticationService();	
		 $identity = null;    
		 $user_id  = null;		
		 if($auth->hasIdentity()) {  
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id; 			 
			 $albumtags = new AlbumTag();
			 $arr = array();
			 $arr['album_tag_data_id'] = $data_id;
			 $arr['album_tag_user_id'] = $tag_id;
			 $arr['album_tag_added_user'] = $user_id;
			 $arr['album_tag_xaxis'] = $x_axis;
			 $arr['album_tag_yaxis'] = $y_axis;
			 $albumtags->exchangeArray($arr);
			 $result = $this->getAlbumTagTable()->saveAlbumTag($albumtags);
			 if($result){
				$album_details = $this->getAlbumDataTable()->getAlbumDetailsFromData($data_id);
				$album_data_details = $this->getAlbumDataTable()->getalbumdata($data_id);
				$all_notification_users = array();
				$all_notification_users[] = $tag_id;
				$all_notification_users[] = $album_details->album_user_id;
				$all_notification_users[] = $album_data_details->added_user_id;
				$notification_users = array_unique($all_notification_users);
				foreach($notification_users as $users){
					if($users!=$identity->user_id){
						$permission = 1;
						if($album_details->album_group_id!=0){
							$this->userGroupSettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
							$user_group_settings = $this->userGroupSettingsTable->getUserGroupSettingsOfSelectedGroup($users,$album_details->album_group_id);
							if((isset($user_group_settings->media)&&$user_group_settings->media=='no')){
								$permission =0;
							}
						}
						if($permission){
							$config = $this->getServiceLocator()->get('Config');
							$base_url = $config['pathInfo']['base_url'];
							$msg = '<a href="'.$base_url.'album/photo/'.$data_id.'">'.$identity->user_given_name." tagged you in one image</a>";
							$subject = 'Tagged you in one image';
							$from = 'admin@jeera.com';
							$this->UpdateNotifications($users,$msg,2,$subject,$from);
						}
					}
				} 
				 
			 }
		 }else{
			echo "Your sesssion expired. Please try again after logged in";
		 }
		 echo  $result;	
		 exit;
	}
	public function deleteTagAction(){
	    $tag_id =  (int)$this->params('tag_id');
		if($this->getAlbumTagTable()->deleteAlbumTag($tag_id)){
		echo "success";
		exit;
		}		
	}
	public function ajaxAddAlbumAction(){
		$error =0;
		$msg = '';
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		
		if ($auth->hasIdentity()) {  
			$request   = $this->getRequest();	
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;				
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('planet_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$post = $request->getPost();
				    if($post['album_title']!=''){
						$albumdetails = array();
						$albumdetails['album_group_id'] = $planet_id;
						$albumdetails['album_user_id'] = $user_id;
						$albumdetails['album_title'] = $post->get('album_title');
						$albumdetails['album_location'] = $post->get('album_location');
						$albumdetails['album_status'] = 1;
						$album_seotitle = 	$this->make_url_friendly($post->get('album_title'));				
						$albumdetails['album_seotitle'] = $album_seotitle;
						$planet_video = $post->get('planet_video');
						$planet_image = $post->get('planet_image');
						$albumObject = new Album();
						$albumObject->exchangeArray($albumdetails);						
						$insert_id = $this->getAlbumTable()->saveAlbum($albumObject);
						if($insert_id){ 
							$albumdataobject = new AlbumData();
							$videos = explode(',',$post['videos']);
							foreach($videos as $row1){
								 if($row1 != ""){
									$albumdata = array();
									$albumdata['parent_album_id'] = $insert_id;
									$albumdata['data_type'] = "youtube";
									$albumdata['data_content'] = $row1;
									$albumdata['added_user_id'] =  $user_id;
									$albumdataobject->exchangeArray($albumdata);
									$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
								 }
							}
							if(isset($_FILES)&&!empty($_FILES)){
								foreach($_FILES as $file){
									$uploaded_file = $this->uploadFile($file,$planet_id,$insert_id);
									if($uploaded_file){
										$albumdata = array();
										$albumdata['parent_album_id'] = $insert_id;
										$albumdata['data_type'] = "image";
										$albumdata['data_content'] = $uploaded_file;
										$albumdata['added_user_id'] =  $user_id;
										$albumdataobject->exchangeArray($albumdata);										 
										$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
									}
									else{
										$msg = "Failed to uploade one image";
										$error =0;
									}
								}	
							}
							$joinedMembers =  $this->getUserGroupTable()->getPlanetMembersWithGroupSettings($planet_id);
							$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($planet_id);
							foreach($joinedMembers as $members){
								$permission = 1;
								if((isset($members->media)&&$members->media=='no')){
									$permission =0;
								}
								if($members->user_group_user_id!=$identity->user_id&&$permission){
									$config = $this->getServiceLocator()->get('Config');
									$base_url = $config['pathInfo']['base_url'];
									$msgs = $identity->user_given_name." created new album -  <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/media/".$album_seotitle."'>".$post->get('album_title')."</a> - under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
									$subject = 'New Album created';
									$from = 'admin@jeera.com';
									$this->UpdateNotifications($members->user_group_user_id,$msgs,2,$subject,$from);
								}
							}
						}
						else{
							$msg = "Some error occured. Please try again";
							$error =1;
						}
					}else{
						$msg = "Album Title is required";
						$error =1;
					}
				}else{ 
					$msg = "Enter a valid planet";
					$error =1;
				}
					
			}
			else{	
				$msg = "Specify planet...";
				$error =1;
			}
			
		}else{
			$msg = 'Your session is expired. Please try again after login';
			$error =1;
		}
		$return_array = array();
		if($error) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
	}
	public function uploadFile($file,$planet_id,$album_id){	
		$config = $this->getServiceLocator()->get('Config');
		$output_dir = $config['pathInfo']['AlbumUploadPath']; 
		$base_url = $config['pathInfo']['base_url'];		 
        $imagesizedata = getimagesize($file["tmp_name"]);
		if ($imagesizedata === FALSE){		
			return false;
		}
		else{
			 if ($file["error"] > 0){
				return false;
			}
			else{
				$ran = rand () ;
				$arr_ext = explode(".",$file["name"]);
				$newfilename = time().$ran.".".end($arr_ext); 
				$ran2 = $ran.".";
				$ext = $this->findexts($file["name"]);						
				$target = $output_dir.$planet_id;				 
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$target = $output_dir.$planet_id."/".$album_id."/";				 
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$output_dir = $target . $newfilename; 
				if(move_uploaded_file($file["tmp_name"],$output_dir)){
					$resizeObj = $this->ResizePlugin();
					$image_path = $base_url.'/public/album/'.$planet_id.'/'.$album_id.'/'.$newfilename; 
					$resizeObj->assignImage($image_path);
					//*** 2) Resize image (options: exact, portrait, landscape, auto, crop)
					$resizeObj -> resizeImage(276, 100, 'auto');
					//*** 3) Save image
					$target_small = $target."small/";
					if(!is_dir($target_small)) {
						mkdir($target_small);	
					}
					$resizeObj -> saveImage($target_small."small-".$newfilename, 75);
					$resizeObj -> resizeImage(390, 700, 'auto');
					//*** 3) Save image
					$target_medium = $target."medium/";
					if(!is_dir($target_medium)) {
						mkdir($target_medium);	
					}
					$resizeObj -> saveImage($target_medium."medium-".$newfilename, 75);
					return $newfilename;
				}else{
					return false;
				}						
			}
		}
	}
	public function make_url_friendly($string){
		$string = trim($string);
		// weird chars to nothing
		$string = preg_replace('/(\W\B)/', '',  $string);
		// whitespaces to underscore
		$string = preg_replace('/[\W]+/',  '_', $string);
		// dash to underscore
		$string = str_replace('-', '_', $string);
		// make it all lowercase
		$string = strtolower($string).time();
		return $string; 
	}	
	public function ajaxAddmoreAction(){ 
		$error =0;
		$msg = '';
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		
		if ($auth->hasIdentity()) {  
			$request   = $this->getRequest();	
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;				
			$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id'); 			 
			if($album_seo!=''){
				$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo); 
				$album_id = $album_details->album_id;				 
				if($album_id){
					$post = $request->getPost();
				    $albumdataobject = new AlbumData();
					$videos = explode(',',$post['videos']); 
					foreach($videos as $row1){
						 if($row1 != ""){
							$albumdata = array();
							$albumdata['parent_album_id'] = $album_id;
							$albumdata['data_type'] = "youtube";
							$albumdata['data_content'] = $row1;
							$albumdata['added_user_id'] =  $user_id;
							$albumdataobject->exchangeArray($albumdata);
							$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
						 }
					}					 
					if(isset($_FILES)&&!empty($_FILES)){
						foreach($_FILES as $file){ 
							if($album_details->album_group_id)
							$uploaded_file = $this->uploadFile($file,$album_details->album_group_id,$album_id); 
							else
							$uploaded_file = $this->uploadUserFile($file,$user_id,$album_id);
							if($uploaded_file){
								$albumdata = array();
								$albumdata['parent_album_id'] = $album_id;
								$albumdata['data_type'] = "image";
								$albumdata['data_content'] = $uploaded_file;
								$albumdata['added_user_id'] =  $user_id;
								$albumdataobject->exchangeArray($albumdata);										 
								$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
								if($album_details->album_group_id){
									$joinedMembers =  $this->getUserGroupTable()->getPlanetMembersWithGroupSettings($album_details->album_group_id);
									$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
									foreach($joinedMembers as $members){
										$permission = 1;
										if((isset($members->media)&&$members->media=='no')){
										$permission =0;
										}
										if($members->user_group_user_id!=$identity->user_id&&$permission){
											$config = $this->getServiceLocator()->get('Config');
											$base_url = $config['pathInfo']['base_url'];
											$msgs = $identity->user_given_name." added new images to the album -  <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."/media/".$album_details->album_seotitle."'>".$album_details->album_title."</a> - under the planet <a href='".$base_url."groups/".$subGroupData->parent_seo_title."/".$subGroupData->group_seo_title."'>".$subGroupData->group_title."</a>";
											$subject = 'New Images Added';
											$from = 'admin@jeera.com';
											$this->UpdateNotifications($members->user_group_user_id,$msgs,2,$subject,$from);
										}
									}
								}
							}
							else{
								$msg = "Failed to uploade one image";
								$error =0;
							}
						}	 
					}					 
					 
				}else{ 
					$msg = "Enter a valid album";
					$error =1;
				}
					
			}
			else{	
				$msg = "Specify album...";
				$error =1;
			}
			
		}else{
			$msg = 'Your session is expired. Please try again after login';
			$error =1;
		}
		$return_array = array();
		if($error) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
	}
	public function ajaxEditAlbumAction(){
		$error =0;
		$msg = '';
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		
		if ($auth->hasIdentity()) {  
			$request   = $this->getRequest();	
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;				
			$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id');		 
			if($album_seo!=''){
				$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
				$album_id = $album_details->album_id;			 
				if($album_id){
					if($album_details->album_user_id == $user_id){
						$post = $request->getPost();
						if($post['album_title']!=''){
							$albumdetails = array();						 
							$albumdetails['album_title'] = $post->get('album_title');
							$albumdetails['album_location'] = $post->get('album_location');
							$albumdetails['album_id'] =$album_id;							
							$albumObject = new Album();
							$albumObject->exchangeArray($albumdetails);
							
							$insert_id = $this->getAlbumTable()->saveAlbum($albumObject);
							if($insert_id){ 
								$msg = "";
								$error =0;
							}
							else{
								$msg = "Some error occured. Please try again";
								$error =1;
							}
						}else{
							$msg = "Album Title is required";
							$error =1;
						}
					}else{
						$msg = "You don't have the permissions to do this";
						$error =1;
					}
				}else{ 
					$msg = "Enter a valid Album";
					$error =1;
				}					
			}
			else{	
				$msg = "Specify Album...";
				$error =1;
			}			
		}else{
			$msg = 'Your session is expired. Please try again after login';
			$error =1;
		}
		$return_array = array();
		if($error) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
	}
	public function ajaxAlbumCoverAction(){
		$error =0;
		$msg = '';
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		
		if ($auth->hasIdentity()) {  
			$request   = $this->getRequest();	
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;				
			$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id');		 
			if($album_seo!=''){
				$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
				$album_id = $album_details->album_id;			 
				if($album_id){
					if($album_details->album_user_id == $user_id){
						$photo_id = (int)$this->params('id');
						if($photo_id){
							if($this->getAlbumTable()->addcoverpic($photo_id,$album_id)){
								$msg = "Successfully Updated Your Album Cover Picture";
								$error =0;
							}else{
								$msg = "Some error occured. Please try again";
								$error =1;
							}
						}
						else{
							$msg = "Select One Photo Please";
							$error =1;
						}						 
					}else{
						$msg = "You don't have the permissions to do this";
						$error =1;
					}
				}else{ 
					$msg = "Enter a valid Album";
					$error =1;
				}					
			}
			else{	
				$msg = "Specify Album...";
				$error =1;
			}			
		}else{
			$msg = 'Your session is expired. Please try again after login';
			$error =1;
		}
		$return_array = array();
		if($error) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
	}
	public function ajaxDeleteDataAction(){
		$error =0;
		$msg = '';
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		
		if ($auth->hasIdentity()) {  
			$request   = $this->getRequest();	
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;				
			$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id');		 
			if($album_seo!=''){
				$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
				$album_id = $album_details->album_id;			 
				if($album_id){					  
					$photo_id = (int)$this->params('id');
					if($photo_id){	
						$album_data = $this->getAlbumDataTable()->getalbumdata($photo_id);
						if($album_data->added_user_id == $user_id){
							if($this->getAlbumDataTable()->deletedataAlbum($photo_id)){
								$this->getAlbumTable()->updatecoverAlbum($photo_id);	
								if($album_data->data_type=='image'){
									$config = $this->getServiceLocator()->get('Config');
									$output_dir = $config['pathInfo']['AlbumUploadPath'];
									unlink($output_dir.$album_details->album_group_id."\\".$album_details->album_id."\\".$album_data->data_content);
									$msg = "Successfully removed that image";
									$error =0;									
								}								
							}else{
								$msg = "Some error occured. Please try again";
								$error =1;
							}
						}else{
							$msg = "You don't have the permissions to do this.";
							$error =1;
						}						
					}
					else{
						$msg = "Select One Photo Please";
						$error =1;
					}					 
				}else{ 
					$msg = "Enter a valid Album";
					$error =1;
				}					
			}
			else{	
				$msg = "Specify Album...";
				$error =1;
			}			
		}else{
			$msg = 'Your session is expired. Please try again after login';
			$error =1;
		}
		$return_array = array();
		if($error) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
	}
	public function ajaxLoadDataAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		 
		$mainViewModel = new ViewModel();
		$request   = $this->getRequest();	
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('planet_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$user_id = $identity->user_id; 
					$mainViewModel->setVariable('user_id', $user_id); 
					$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id'); 
					if($album_seo!='') {
						$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
						if(!empty($album_details)&&$album_details->album_id!=''){
							$is_member = $this->getGroupTable()->is_member($planet_id,$user_id);
							if($is_member){								 
								$page = 0;
								$request   = $this->getRequest();				 		
								$post = $request->getPost();		
								if ($request->isPost()){
									$page =$post->get('page');
									if(!$page)
									$page = 0;
								}
								$offset = $page*12;
								$mainViewModel->setVariable('page', $page);
								$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
								$mainViewModel->setVariable('planetdetails', $planet_data); 
								$album_data = $this->getAlbumDataTable()->getAlbum($album_details->album_id,12,$offset);
								$mainViewModel->setVariable('album_data', $album_data); 
								$mainViewModel->setVariable('album_details', $album_details); 
							}else{
								$error[] = "You don't have the permission to access this album";
							}						
						}else{
							$error[] = "Sorry we can't find this album..";
						}
					}else{
						$error[] = "Sorry we can't find this album..";
					}
				}else{
						$error[] = "Sorry we can't find this album..";
					}
			}else{
				$error[] = "Sorry we can't find this album..";
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		$mainViewModel->setTerminal($request->isXmlHttpRequest());		
		return $mainViewModel;	
	}
	public function fileAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$mainViewModel = new ViewModel();
		$offset = 0;
		$is_admin = 0;
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$group_seo = $this->getEvent()->getRouteMatch()->getParam('group_id'); 
			$planet_seo = $this->getEvent()->getRouteMatch()->getParam('sub_group_id');
			if($group_seo!=''&&$planet_seo!=''){
				$groupdetails = $this->getGroupTable()->getGroupIdFromSEO($group_seo);
				$group_id = $groupdetails->group_id;
				$planetdetails =  $this->getGroupTable()->getGroupIdFromSEO($planet_seo);
				$planet_id = $planetdetails->group_id;
				if($group_id && $planet_id){
					$admin_status = $this->getGroupTable()->getAdminStatus($planet_id,$identity->user_id);
					if($admin_status->is_admin){
						$is_admin = 1;
					}
					$user_role = $this->getUserGroupTable()->getUserRole($planet_id,$identity->user_id);
					if(!empty($user_role)){
						$is_admin = 1;
					}
					$mainViewModel->setVariable('is_admin', $is_admin);
					$user_id = $identity->user_id; 
					$mainViewModel->setVariable('user_id', $user_id); 
					$album_seo = $this->getEvent()->getRouteMatch()->getParam('id'); 
					if($album_seo!='') {
						$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
						if(!empty($album_details)&&$album_details->album_id!=''){
							$is_member = $this->getGroupTable()->is_member($planet_id,$user_id);
							if($is_member){	
								$count = $this->getEvent()->getRouteMatch()->getParam('count'); 
								if($count>0){
									$offset = $count-1;
								}
								$album_data_count = $this->getAlbumDataTable()->getAlbumDataCount($album_details->album_id);
								$mainViewModel->setVariable('album_data_count', $album_data_count); 
								if($offset>$album_data_count->total_data-1){
									$offset = 0 ;
								}
								$groupTopWidget = $this->forward()->dispatch('Groups\Controller\Groups', array(
								'action' => 'groupTop',
								'group_id'     => $group_seo,
								'sub_group_id' => $planet_seo,
								));						
								$mainViewModel->addChild($groupTopWidget, 'groupTopWidget');								 
								$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($planet_id,$identity->user_id);
								$mainViewModel->setVariable('planetdetails', $planet_data); 
								$album_data = $this->getAlbumDataTable()->getAlbum($album_details->album_id,1,$offset);
								$album_file_data = array();
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
								$album_file_data = array(
														'data_id'=>$album_data[0]['data_id'],
														'parent_album_id'=>$album_data[0]['parent_album_id'],
														'added_user_id'=>$album_data[0]['added_user_id'],
														'data_type'=>$album_data[0]['data_type'],
														'data_content'=>$album_data[0]['data_content'],
														'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data[0]['data_id'],$identity->user_id),
														"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data[0]['data_id'])->comment_count,
														'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data[0]['data_id'],$identity->user_id,2,0),
														'current_file'=>$offset+1,
														'tags' =>$this->getAlbumTagTable()->getTags($album_data[0]['data_id']),
													);
								$mainViewModel->setVariable('album_data', $album_file_data); 
								$mainViewModel->setVariable('album_details', $album_details); 
							}else{
								$error[] = "You don't have the permission to access this album";
							}						
						}else{
							$error[] = "Sorry we can't find this album..";
						}
					}else{
						$error[] = "Sorry we can't find this album..";
					}
				}else{
						$error[] = "Sorry we can't find this album..";
					}
			}else{
				$error[] = "Sorry we can't find this album..";
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}		
		return $mainViewModel;	 
	}
	public function ajaxAddUserAlbumAction(){ 
		$error =0;
		$msg = '';
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		
		if ($auth->hasIdentity()) {  
			$request   = $this->getRequest();	
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;				
			$post = $request->getPost();
			if($post['album_title']!=''){
				$albumdetails = array();
				$albumdetails['album_group_id'] = 0;
				$albumdetails['album_user_id'] = $user_id;
				$albumdetails['album_title'] = $post->get('album_title');
				$albumdetails['album_location'] = $post->get('album_location');
				$albumdetails['album_status'] = 1;		
				$albumdetails['album_seotitle'] = $this->make_url_friendly($post->get('album_title'));
				$planet_video = $post->get('planet_video');
				$planet_image = $post->get('planet_image');
				$albumObject = new Album();
				$albumObject->exchangeArray($albumdetails);
				$insert_id = $this->getAlbumTable()->saveAlbum($albumObject);
				if($insert_id){ 
					$albumdataobject = new AlbumData();
					$videos = explode(',',$post['videos']);
					foreach($videos as $row1){
						 if($row1 != ""){
							$albumdata = array();
							$albumdata['parent_album_id'] = $insert_id;
							$albumdata['data_type'] = "youtube";
							$albumdata['data_content'] = $row1;
							$albumdata['added_user_id'] =  $user_id;
							$albumdataobject->exchangeArray($albumdata);
							$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
						 }
					}
					if(isset($_FILES)&&!empty($_FILES)){
						foreach($_FILES as $file){
							$uploaded_file = $this->uploadUserFile($file,$user_id,$insert_id);
							if($uploaded_file){
								$albumdata = array();
								$albumdata['parent_album_id'] = $insert_id;
								$albumdata['data_type'] = "image";
								$albumdata['data_content'] = $uploaded_file;
								$albumdata['added_user_id'] =  $user_id;
								$albumdataobject->exchangeArray($albumdata);										 
								$this->getAlbumDataTable()->saveAlbumData($albumdataobject);
							}
							else{
								$msg = "Failed to uploade one image";
								$error =0;
							}
						}	
					}
				}else{
					$msg = "Some error occured. Please try again";
					$error =1;
				}
			}else{
				$msg = "Album Title is required";
				$error =1;
			}			
		}else{
			$msg = 'Your session is expired. Please try again after login';
			$error =1;
		}
		$return_array = array();
		if($error) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
	}
	public function uploadUserFile($file,$user_id,$album_id){	
		$config = $this->getServiceLocator()->get('Config');
		$output_dir = $config['pathInfo']['AlbumUploadPath']; 
		$base_url = $config['pathInfo']['base_url'];		 
        $imagesizedata = getimagesize($file["tmp_name"]);
		if ($imagesizedata === FALSE){		
			return false;
		}
		else{
			 if ($file["error"] > 0){
				return false;
			}
			else{
				$ran = rand () ;
				$ext_arr = array();
				$ext_arr = explode(".",$file["name"]);
				$newfilename = time().$ran.".".end($ext_arr); 
				$ran2 = $ran.".";
				$ext = $this->findexts($file["name"]);						
				$target = $output_dir.'profile';				 
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$target = $output_dir.'profile'."/".$user_id;				 
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$target = $output_dir.'profile'."/".$user_id."/".$album_id."/";				 
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$output_dir = $target . $newfilename; 
				if(move_uploaded_file($file["tmp_name"],$output_dir)){
					$resizeObj = $this->ResizePlugin();
					$image_path = $base_url.'/public/album/'.'profile'."/".$user_id.'/'.$album_id.'/'.$newfilename; 
					$resizeObj->assignImage($image_path);
					//*** 2) Resize image (options: exact, portrait, landscape, auto, crop)
					$resizeObj -> resizeImage(276, 100, 'auto');
					//*** 3) Save image
					$target_small = $target."small/";
					if(!is_dir($target_small)) {
						mkdir($target_small);	
					}
					$resizeObj -> saveImage($target_small."small-".$newfilename, 75);
					$resizeObj -> resizeImage(390, 700, 'auto');
					//*** 3) Save image
					$target_medium = $target."medium/";
					if(!is_dir($target_medium)) {
						mkdir($target_medium);	
					}
					$resizeObj -> saveImage($target_medium."medium-".$newfilename, 75);
					return $newfilename;
				}else{
					return false;
				}						
			}
		}	
	}
	public function userAlbumViewAction(){
		$sm = $this->getServiceLocator();
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$mainViewModel = new ViewModel();
		$myprofile = 0 ;
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;					 
			$user_id = $identity->user_id; 
			$profilename = $this->params('member_profile');
			if($profilename!=''){
				$mainViewModel->setVariable('profilename', $profilename);
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if($userinfo->user_id == $identity->user_id){
						$myprofile = 1 ;
				}
				if(!empty($userinfo)&&$userinfo->user_id){
					$profileTopWidget = $this->forward()->dispatch('User\Controller\UserProfile', array(
											'action' => 'profileTop',
											'member_profile'     => $profilename,							 
										));
					$mainViewModel->addChild($profileTopWidget, 'profileTopWidget');					 		
					$mainViewModel->setVariable('logged_id', $identity->user_id);
					$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id'); 
					if($album_seo!='') {						 
						$mainViewModel->setVariable('album_seo',$album_seo);
						$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
						if(!empty($album_details)&&$album_details->album_id!=''){
							$album_data = $this->getAlbumDataTable()->getAlbum($album_details->album_id,12,0);
							$mainViewModel->setVariable('album_data', $album_data); 
							$mainViewModel->setVariable('album_details', $album_details); 
						}						 
					}else{
						$error[] = "Sorry we can't find this album..";
					}
				}
				else{
					$error[] = "Sorry this page is not available";
				}
			}else{
				$error[] = "Sorry we can't find this album..";
			}				 
			 
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}	
		$mainViewModel->setVariable('myprofile', $myprofile);		
		return $mainViewModel;	
	}
	public function ajaxDeleteUserDataAction(){
		$error =0;
		$msg = '';
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		
		if ($auth->hasIdentity()) {  
			$request   = $this->getRequest();	
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;				
			$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id');		 
			if($album_seo!=''){
				$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
				$album_id = $album_details->album_id;			 
				if($album_id){					  
					$photo_id = (int)$this->params('id');
					if($photo_id){	
						$album_data = $this->getAlbumDataTable()->getalbumdata($photo_id);
						if($album_data->added_user_id == $user_id){
							if($this->getAlbumDataTable()->deletedataAlbum($photo_id)){
								$this->getAlbumTable()->updatecoverAlbum($photo_id);	
								if($album_data->data_type=='image'){
									$config = $this->getServiceLocator()->get('Config');
									$output_dir = $config['pathInfo']['AlbumUploadPath'];
									unlink($output_dir."profile/".$album_details->album_user_id."\\".$album_details->album_id."\\".$album_data->data_content);
									$msg = "Successfully removed that image";
									$error =0;									
								}								
							}else{
								$msg = "Some error occured. Please try again";
								$error =1;
							}
						}else{
							$msg = "You don't have the permissions to do this.";
							$error =1;
						}						
					}
					else{
						$msg = "Select One Photo Please";
						$error =1;
					}					 
				}else{ 
					$msg = "Enter a valid Album";
					$error =1;
				}					
			}
			else{	
				$msg = "Specify Album...";
				$error =1;
			}			
		}else{
			$msg = 'Your session is expired. Please try again after login';
			$error =1;
		}
		$return_array = array();
		if($error) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
	}
	public function ajaxLoadUserDataAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		 
		$mainViewModel = new ViewModel();
		$request   = $this->getRequest();	
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$user_id = $identity->user_id; 
			$mainViewModel->setVariable('user_id', $user_id); 
			$mainViewModel->setVariable('profilename', $identity->user_profile_name); 
			$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id'); 
			if($album_seo!='') {				 
				$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
				if(!empty($album_details)&&$album_details->album_id!=''){													 
					$page = 0;
					$request   = $this->getRequest();				 		
					$post = $request->getPost();		
					if ($request->isPost()){
						$page =$post->get('page');
						if(!$page)
						$page = 0;
					}
					$offset = $page*12;
					$album_data = $this->getAlbumDataTable()->getAlbum($album_details->album_id,12,$offset);
					$mainViewModel->setVariable('page', $page);
					$mainViewModel->setVariable('album_data', $album_data); 
					$mainViewModel->setVariable('album_details', $album_details); 													
				}else{
					$error[] = "Sorry we can't find this album..";
				}				 
			}else{
				$error[] = "Sorry we can't find this album..";
			}			 
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		$mainViewModel->setTerminal($request->isXmlHttpRequest());		
		return $mainViewModel;	
	}
	public function userfileAction(){ 
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$mainViewModel = new ViewModel();
		$offset = 0;
		$is_admin = 0;
		$myprofile = 0;
		$sm = $this->getServiceLocator();
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;					 
			$user_id = $identity->user_id; 
			$mainViewModel->setVariable('user_id', $user_id); 
			$album_seo = $this->getEvent()->getRouteMatch()->getParam('album_id');  
			$profilename = $this->params('member_profile');
			if($profilename!=''){ 
				$profilename = $this->params('member_profile');
				$mainViewModel->setVariable('profilename', $profilename);
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if($userinfo->user_id == $identity->user_id){
						$myprofile = 1 ;
				}
				if(!empty($userinfo)&&$userinfo->user_id){
					$profileTopWidget = $this->forward()->dispatch('User\Controller\UserProfile', array(
											'action' => 'profileTop',
											'member_profile'     => $profilename,							 
										));
					$mainViewModel->addChild($profileTopWidget, 'profileTopWidget');
					if($album_seo!='') {
						$mainViewModel->setVariable('album_seo', $album_seo);
						if($album_seo == 'user_photos'){
							$count = $this->getEvent()->getRouteMatch()->getParam('num'); 
							if($count>0){
								$offset = $count-1;
							}
							$album_data_count = $this->getAlbumTable()->fetchTaggedUserAlbumDataCount($userinfo->user_id);
							$mainViewModel->setVariable('album_data_count', $album_data_count); 
							if($offset>$album_data_count->total_data-1){
								$offset = 0 ;
							}
							$album_data = $this->getAlbumTable()->fetchTaggedUserAlbumData($userinfo->user_id,$offset,1);
							$album_file_data = array();
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');	
							foreach($album_data as $data){
								$album_file_data = array(
												'data_id'=>$data->data_id,
												'parent_album_id'=>$data->parent_album_id,
												'added_user_id'=>$data->added_user_id,
												'data_type'=>$data->data_type,
												'data_content'=>$data->data_content,
												'album_group_id'=>$data->album_group_id,
												'album_user_id'=>$data->album_user_id,
												'album_id'=>$data->album_id,
												'album_location'=>$data->album_location,
												'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$data->data_id,$identity->user_id),
												"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$data->data_id)->comment_count,
												'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$data->data_id,$identity->user_id,2,0),
												'current_file'=>$offset+1,
												'tags' =>$this->getAlbumTagTable()->getTags($data->data_id),
												);
							}
							$mainViewModel->setVariable('album_data', $album_file_data); 
						}else{
							$album_details = $this->getAlbumTable()->getalbumFromSeotitle($album_seo);
							if(!empty($album_details)&&$album_details->album_id!=''){
								$count = $this->getEvent()->getRouteMatch()->getParam('num'); 
								if($count>0){
									$offset = $count-1;
								}
								$album_data_count = $this->getAlbumDataTable()->getAlbumDataCount($album_details->album_id);
								$mainViewModel->setVariable('album_data_count', $album_data_count); 
								if($offset>$album_data_count->total_data-1){
									$offset = 0 ;
								}
								$album_data = $this->getAlbumDataTable()->getAlbum($album_details->album_id,1,$offset);
								$album_file_data = array();
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');
								$album_file_data = array(
													'data_id'=>$album_data[0]['data_id'],
													'parent_album_id'=>$album_data[0]['parent_album_id'],
													'added_user_id'=>$album_data[0]['added_user_id'],
													'data_type'=>$album_data[0]['data_type'],
													'data_content'=>$album_data[0]['data_content'],
													'album_location'=>$album_details->album_location,
													'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data[0]['data_id'],$identity->user_id),
													"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data[0]['data_id'])->comment_count,
													'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data[0]['data_id'],$identity->user_id,2,0),
													'current_file'=>$offset+1,
													'tags' =>$this->getAlbumTagTable()->getTags($album_data[0]['data_id']),
													);
								$mainViewModel->setVariable('album_data', $album_file_data); 
								$mainViewModel->setVariable('album_details', $album_details); 
							}
						}
						}else{
							$error[] = "Sorry we can't find this album..";
						}
					}else{
						$error[] = "Sorry we can't find this album..";
					}
				}else{
					$error[] = "Sorry we can't find this album..";
				}
			 
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		$mainViewModel->setVariable('myprofile', $myprofile); 		
		
		return $mainViewModel;	 
	}
	public function usersAction(){
		$sm = $this->getServiceLocator();
		 $term = $_GET['term'];
		 $profile = $this->getEvent()->getRouteMatch()->getParam('profile_name');
		 $return_arr = array();
		 if($profile!=''){ 			 
			$this->userTable = $sm->get('User\Model\UserTable');
			$userinfo = $this->userTable->getUserByProfilename($profile);			 
			if(!empty($userinfo)&&$userinfo->user_id){
				$userList = $this->userTable->getAllUserFriendsForTag($userinfo->user_id,$term);
				foreach($userList as $row) {
				  $return_arr[] = array(
						'id'    => $row->user_id,
						'value' => $row->user_given_name
					); 
				}
			}
		 }		  
		 echo json_encode($return_arr);
		 exit;
	}
	public function userPhotosAction(){
		$sm = $this->getServiceLocator();
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$mainViewModel = new ViewModel();
		$myprofile = 0 ;
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;					 
			$user_id = $identity->user_id; 
			$profilename = $this->params('member_profile');
			if($profilename!=''){
				$mainViewModel->setVariable('profilename', $profilename);
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if($userinfo->user_id == $identity->user_id){
						$myprofile = 1 ;
				}
				if(!empty($userinfo)&&$userinfo->user_id){
					$profileTopWidget = $this->forward()->dispatch('User\Controller\UserProfile', array(
											'action' => 'profileTop',
											'member_profile'     => $profilename,							 
										));
					$mainViewModel->addChild($profileTopWidget, 'profileTopWidget');					 		
					$mainViewModel->setVariable('logged_id', $identity->user_id);					 
					$album_data = $this->getAlbumTable()->fetchTaggedUserAlbumData($userinfo->user_id,0,12);
					$mainViewModel->setVariable('album_data', $album_data); 			 
				}
				else{
					$error[] = "Sorry this page is not available";
				}
			}else{
				$error[] = "Sorry we can't find this album..";
			}				 
			 
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}	
		$mainViewModel->setVariable('myprofile', $myprofile);		
		return $mainViewModel;	
	}
	public function ajaxLoadUserPhotoAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;		 
		$mainViewModel = new ViewModel();
		$request   = $this->getRequest();
		$myprofile = 0 ;
		$sm = $this->getServiceLocator();		
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$user_id = $identity->user_id; 
			$mainViewModel->setVariable('user_id', $user_id); 
			$profilename = $this->params('member_profile');
			if($profilename!=''){
				$mainViewModel->setVariable('profilename', $profilename);
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if($userinfo->user_id == $identity->user_id){
						$myprofile = 1 ;
				}												 
				$page = 0;
				$request   = $this->getRequest();				 		
				$post = $request->getPost();		
				if ($request->isPost()){
					$page =$post->get('page');
					if(!$page)
					$page = 0;
				}
				$offset = $page*12;
				$album_data = $this->getAlbumTable()->fetchTaggedUserAlbumData($userinfo->user_id,$offset,12);
				$mainViewModel->setVariable('album_data', $album_data);
													
				}else{
					$error[] = "Sorry we can't find this album..";
				}				 
			}else{
				$error[] = "Sorry we can't find this album..";
			} 
		$mainViewModel->setVariable('myprofile', $myprofile);		 
		$mainViewModel->setTerminal($request->isXmlHttpRequest());		
		return $mainViewModel;
	}
	public function userPhotoFileAction(){
		echo "here";die();
	}
	public function UpdateNotifications($user_notification_user_id,$msg,$type,$subject,$from){
		$UserGroupNotificationData = array();						
		$UserGroupNotificationData['user_notification_user_id'] =$user_notification_user_id;		 
		$UserGroupNotificationData['user_notification_content']  = $msg;
		$UserGroupNotificationData['user_notification_added_timestamp'] = date('Y-m-d H:i:s');			
		$UserGroupNotificationData['user_notification_notification_type_id'] = $type;
		$UserGroupNotificationData['user_notification_status'] = 0;		
		#lets Save the User Notification
		$UserGroupNotificationSaveObject = new UserNotification();
		$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);	
		$insertedUserGroupNotificationId ="";	#this will hold the latest inserted id value
		$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
		$userData = $this->getUserTable()->getUser($user_notification_user_id); 
		$this->sendNotificationMail($msg,$subject,$userData->user_email,$from);
	}
	public function sendNotificationMail($msg,$subject,$emailId,$from){
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');		
		$body = $this->renderer->render('activity/email/emailinvitation.phtml', array('msg'=>$msg));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";
		$textPart = new MimePart($body);
		$textPart->type = "text/plain";
		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));
		$message = new Mail\Message();
		$message->setFrom($from);
		$message->addTo($emailId);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject($subject);
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');
		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
	}
	public function getUserNotificationTable(){
        if (!$this->userNotificationTable) {
            $sm = $this->getServiceLocator();
            $this->userNotificationTable = $sm->get('Notification\Model\UserNotificationTable');
        }
        return $this->userNotificationTable;
    }
	public function getUserTable(){
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
	public function photoAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$is_admin = 0;
		$mainViewModel = new ViewModel();
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$user_id = $identity->user_id; 
			$photo_id = $this->getEvent()->getRouteMatch()->getParam('id');
			if($photo_id!=''){
				$album_data_details = $this->getAlbumDataTable()->getalbumdata($photo_id); 
				if(!empty($album_data_details)&&isset($album_data_details->data_id)&&$album_data_details->data_id!=''){
					$mainViewModel->setVariable('album_data_details', $album_data_details); 				
					$album_details = $this->getAlbumDataTable()->getAlbumDetailsFromData($photo_id);
					$mainViewModel->setVariable('album_details', $album_details); 
					if($album_details->album_group_id!=0){					
						$subGroupData = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
						$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}
						$mainViewModel->setVariable('is_admin', $is_admin);
						$user_id = $identity->user_id; 
						$mainViewModel->setVariable('user_id', $user_id); 
						$mainViewModel->setVariable('subGroupData', $subGroupData); 	
						$groupTopWidget = $this->forward()->dispatch('Groups\Controller\Groups', array(
						'action' => 'groupTop',
						'group_id'     => $subGroupData->parent_seo_title,
						'sub_group_id' => $subGroupData->group_seo_title,
						));						
						$mainViewModel->addChild($groupTopWidget, 'groupTopWidget');
						$planet_data = $this->getGroupTable()->getPlanetDetailsForPalnetView($album_details->album_group_id,$identity->user_id);
						$mainViewModel->setVariable('planetdetails', $planet_data);
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');	
						$userData = $this->getUserTable()->getUser($album_details->album_user_id); 						
					}else{ 
						$userData = $this->getUserTable()->getUser($album_details->album_user_id); 
						$mainViewModel->setVariable('userData', $userData);
						$profileTopWidget = $this->forward()->dispatch('User\Controller\UserProfile', array(
												'action' => 'profileTop',
												'member_profile'     => $userData->user_profile_name,							 
											));
						$mainViewModel->addChild($profileTopWidget, 'profileTopWidget');
						$mainViewModel->setVariable('profilename', $userData->user_profile_name);
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');
					}
					$album_file_data = array(
										'data_id'=>$album_data_details->data_id,
										'parent_album_id'=>$album_data_details->parent_album_id,
										'added_user_id'=>$album_data_details->added_user_id,
										'data_type'=>$album_data_details->data_type,
										'data_content'=>$album_data_details->data_content,
										'album_location'=>$album_details->album_location,
										'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
										"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
										'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
										'current_file'=>1,
										'tags' =>$this->getAlbumTagTable()->getTags($album_data_details->data_id),
										);
					$myprofile = 0 ;
					$userinfo = $this->getUserTable()->getUserByProfilename($userData->user_profile_name);
					if($userinfo->user_id == $identity->user_id){
							$myprofile = 1 ;
					}
					$mainViewModel->setVariable('myprofile', $myprofile); 
					$mainViewModel->setVariable('album_data', $album_file_data); 
					$mainViewModel->setVariable('album_details', $album_details);
				}else{
					$error[] = "Sorry the picture you are looking is not existing in this system";
				}
			}else{
				$error[] = "Sorry the picture you are looking is not existing in this system";
			}			
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		$mainViewModel->setVariable('error', $error); 	
		return $mainViewModel;	
	}
} 