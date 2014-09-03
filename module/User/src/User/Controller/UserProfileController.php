<?php 
#namespance Define
namespace User\Controller;
use Zend\View\Helper\HeadScript;
use \Exception;
#zend Library
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;	//using set template
#session
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter; 
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
#Custom classes
use Calender\Controller\Calender;
use Calender\Model\CalenderTable; #calender
use User\Model\User;
use User\Model\UserProfile;
use Tag\Model\UserTagTable; #calender
use Group\Model\UserGroup;
use Activity\Model\Activity; 
use Tag\Model\UserTag;
use Album\Form\AlbumForm;
use Notification\Model\UserNotification; 
class UserProfileController extends AbstractActionController
{
    protected $userTable;
	protected $userProfileTable;
	protected $groupTable;
	protected $userGroupTable;
	protected $userTagTable;
	protected $countryTable;
	protected $activityTable;	
	protected $photoTable;	
	protected $headScript;	#javascript addition object
	protected $userFriendTable;
	protected $userFriendRequestTable;
	protected $tagTable;
	protected $albumTable;
	protected $userNotificationTable;
	protected $likeTable;
	protected $commentTable;
	protected $activityRsvpTable;
	protected $discussionTable;
	protected $userFrontTable;
	
	#routing contact
	const PROFILE_PATH     = 'datagd/profile';  
	const TIMELINE_PATH     = 'datagd/timeline';  
    //<?php echo Application\Model\Application::TIMELINE_PATH; 
	#this function will load the css and javascript need for perticular action
	protected function getViewHelper($helperName){
    	return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}	 
    public function indexAction(){			
		#loading the configration
		$sm = $this->getServiceLocator(); 
		$basePath = $sm->get('Request')->getBasePath();	#get the base path		
		$this->getViewHelper('HeadScript')->appendFile($basePath.'/js/jquery.min.js','text/javascript');
		$this->getViewHelper('HeadScript')->appendFile($basePath.'/js/1625.js','text/javascript');		
		$auth = new AuthenticationService();
		$userGroupWidget =array();	
		$identity = null;        
		if ($auth->hasIdentity()) {
            // Identity exists; get it
           	$identity = $auth->getIdentity();			
			$userData =array();	///this will hold data from y2m_user table
			$userProfileData =array();//this will hold data of profile table
			$userTimelinePhotoData =array();//this will hold the timeline photo
			$userProfilePhotoData =array();//this will hold the profile photo
			#check the identity againts the DB
			$userData = $this->getUserTable()->getUser($identity->user_id);	
			$userAge =0;
			$userTag =array();	
			$userGroups =array();	
			//echo "<pre>";print_r($userData);exit;	
			if(isset($userData->user_id) && !empty($userData->user_id)){				
				#fetch the user pic
				if(isset($userData->user_id) && !empty($userData->user_id) && trim($userData->user_id)!='0'){
					#fetch user profile data
					$userProfileData = $this->getUserProfileTable()->getUserProfileForUserId($userData->user_id);
					$userObj =new User();
					$userAge =$userObj->calculateUserAge($userProfileData->user_profile_dob);	
					 
					#fetch user timeline photo
					if(isset($userData->user_timeline_photo_id) && !empty($userData->user_timeline_photo_id) && trim($userData->user_timeline_photo_id)!='0'){
						$userTimelinePhotoData = $this->getPhotoTable()->getPhoto($userData->user_timeline_photo_id);						
					} //if(isset($userData->user_timeline_photo_id) && !empty($userData->user_timeline_photo_id) && trim($userData->user_timeline_photo_id)!='0')
					
					#fetch user profile photo
					if(isset($userData->user_profile_photo_id) && !empty($userData->user_profile_photo_id) && trim($userData->user_profile_photo_id)!='0'){
						$userProfilePhotoData = $this->getPhotoTable()->getPhoto($userData->user_profile_photo_id);	
					} //if(isset($userData->user_timeline_photo_id) && !empty($userData->user_timeline_photo_id) && trim($userData->user_timeline_photo_id)!='0')
					
					#fetch all user tags
					$userTag =	$this->getUserTagTable()->fetchAllTagsOfUser($userData->user_id);	
					
					#call the user group Widget
					$userGroupWidget = $this->forward()->dispatch('Group\Controller\UserGroup', array('action' => 'index'));					
				} //if(isset($userData->user_id) && !empty($userData->user_id) && trim($userData->user_id)!='0')				
				//echo "<pre>";print_r($userProfileData);exit;			
			}//if($userData->user_id)	 
			
        } 
		$this->layout()->identity = $identity;	//assign Identity to layout 	 
		$viewModel = new ViewModel(array('users' => $userData,'userData' => $userData,'userProfileData' => $userProfileData,'userTimelinePhotoData' => $userTimelinePhotoData,'userProfilePhotoData' => $userProfilePhotoData,'userAge' => $userAge, 'userTag' => $userTag, 'userGroups' => $userGroups));
    	$viewModel->addChild($userGroupWidget, 'userGroupWidget');		 
    	return $viewModel;	
	}	
	#this function will load subgroups for the user as suggestion on his home page
	public function loadAction(){		
		$suggestSubGroup =array();
		try {	
			$auth = new AuthenticationService();	
			$identity = null;        
			if ($auth->hasIdentity()) {
            	// Identity exists; get it
           		$identity = $auth->getIdentity();			
				 #check the identity againts the DB
				$userData = $this->getUserTable()->getUser($identity->user_id);					
				if(isset($userData->user_id) && !empty($userData->user_id)){					 
					#fetch the user Galaxy
					$suggestSubGroup = $this->getUserGroupTable()->fetchAllSubGroupUserNotRegisterInGroup($userData->user_id);				 
				} //if(isset($userData->user_id) && !empty($userData->user_id))   
	    	} //if ($auth->hasIdentity()) 		
		} catch (\Exception $e) {
         	#DOO  OUR LOGGED ERROR FUNCTION
			 
   		}	
		$viewModel = new ViewModel(array('suggestSubGroup' => $suggestSubGroup));
    	$viewModel->setTerminal(true);
    	return $viewModel;	
    
			
	}	
	public function userAction(){
		$auth = new AuthenticationService();	
		$identity = null;        
		if ($auth->hasIdentity()) {
            // Identity exists; get it
           	$identity = $auth->getIdentity();
			
			$userData =array();	///this will hold data from y2m_user table
			$userProfileData =array();//this will hold data of profile table
			$userTimelinePhotoData =array();//this will hold the timeline photo
			$userProfilePhotoData =array();//this will hold the profile photo
			#check the identity againts the DB
			$userData = $this->getUserTable()->getUser($identity->user_id);	
			$userAge =0;
			$userTag =array();	
			$userGroups =array();	
			//echo "<pre>";print_r($userData);exit;	
			if(isset($userData->user_id) && !empty($userData->user_id)){				
				#fetch the user pic
				if(isset($userData->user_id) && !empty($userData->user_id) && trim($userData->user_id)!='0'){
					#fetch user profile data
					$userProfileData = $this->getUserProfileTable()->getUserProfileForUserId($userData->user_id);
					$userObj =new User();
					$userAge =$userObj->calculateUserAge($userProfileData->user_profile_dob);	
					 
					#fetch user timeline photo
					if(isset($userData->user_timeline_photo_id) && !empty($userData->user_timeline_photo_id) && trim($userData->user_timeline_photo_id)!='0'){
						$userTimelinePhotoData = $this->getPhotoTable()->getPhoto($userData->user_timeline_photo_id);	
						//echo "<pre>";print_r($userTimelinePhotoData); 
					} //if(isset($userData->user_timeline_photo_id) && !empty($userData->user_timeline_photo_id) && trim($userData->user_timeline_photo_id)!='0')
					
					#fetch user profile photo
					if(isset($userData->user_profile_photo_id) && !empty($userData->user_profile_photo_id) && trim($userData->user_profile_photo_id)!='0'){
						$userProfilePhotoData = $this->getPhotoTable()->getPhoto($userData->user_profile_photo_id);	
						//echo "<pre>";print_r($userProfilePhotoData); 
					} //if(isset($userData->user_timeline_photo_id) && !empty($userData->user_timeline_photo_id) && trim($userData->user_timeline_photo_id)!='0')
					
					#fetch all user tags
					$userTag =	$this->getUserTagTable()->fetchAllTagsOfUser($userData->user_id);	
					
					#fetch all groups of users
					$userGroups = $this->getUserGroupTable()->fetchAllUserGroup($userData->user_id);					
					 
										
				
				} //if(isset($userData->user_id) && !empty($userData->user_id) && trim($userData->user_id)!='0')				
				//echo "<pre>";print_r($userProfileData);exit;			
			}//if($userData->user_id)	 
			
        } 
		$this->layout()->identity = $identity;	//assign Identity to layout   		  
		
		 
		
		return new ViewModel(array(
            'users' => $userData,'userData' => $userData,'userProfileData' => $userProfileData,'userTimelinePhotoData' => $userTimelinePhotoData,'userProfilePhotoData' => $userProfilePhotoData,'userAge' => $userAge, 'userTag' => $userTag, 'userGroups' => $userGroups, 
        ));
    }	 
	public function calAction(){ 		 
		 $month_id= $this->params('month_id');
		 $month_id= $this->params('year_id');
		 
		 #fetch the user Galaxy
			$suggestSubGroup =array();		
			$suggestSubGroup = $this->getUserGroupTable()->fetchAllSubGroupUserNotRegisterInGroup();
			
			#call the calender module in zend
			//$calender = new CalenderTable();
			$monthNames = Array("January", "February", "March", "April", "May", "June", "July","August", "September", "October", "November", "December");
			if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("n");
			if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");
			
			$cMonth = $_REQUEST["month"];
			$cYear = $_REQUEST["year"];
			
			$prev_year = $cYear;
			$next_year = $cYear;
			$prev_month = $cMonth-1;
			$next_month = $cMonth+1;
			 
			if ($prev_month == 0 ) {
				$prev_month = 12;
				$prev_year = $cYear - 1;
			}
			if ($next_month == 13 ) {
				$next_month = 1;
				$next_year = $cYear + 1;
			}
			
			
			$allUpcommingActivity =array();		
			$allUpcommingActivity = $this->getActivityTable()->getUserJoinedActivityForCalender($cYear, $cMonth,28);
			
			$result = new ViewModel();
			$result->setTerminal(true);
			$result->setVariables(array('suggestSubGroup' => $suggestSubGroup, 'allUpcommingActivity' => $allUpcommingActivity, 'cMonth' => $cMonth, 'cYear' => $cYear,'prev_year' => $prev_year, 'next_year' => $next_year,'prev_month' => $prev_month, 'next_month' => $next_month, 'monthNames' => $monthNames));
			return $result;
		 
		 
		 
 	
	}
	public function friendRequestAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$mainViewModel = new ViewModel();
		$offset = 0;
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;	
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$requested_id =  $post['user'];
				if($requested_id!=''){
					 if($this->getUserFriendTable()->isFriend($user_id,$requested_id)){
						$msg = 'You are already friends';
						$error =1;
					 }else if($this->getUserFriendTable()->isRequested($user_id,$requested_id)){
						$msg = 'You are already Requested';
						$error =1;
					 }else if($this->getUserFriendTable()->isPending($user_id,$requested_id)){
						$msg = 'Person is waiting for your response';
						$error =1;
					 }else{
						$data = array(
							'user_friend_request_sender_user_id'=>$user_id,
							'user_friend_request_friend_user_id'=>$requested_id,
							'user_friend_request_status' =>0
							);
						if($this->getUserFriendRequestTable()->sendFriendRequest($data)){
							$config = $this->getServiceLocator()->get('Config');
							$base_url = $config['pathInfo']['base_url'];
							$userData = $this->getUserTable()->getUser($user_id); 
							$msg = '<a href="'.$base_url.$userData->user_profile_name.'">'.$identity->user_given_name." Sent you a friend request</a>";
							$subject = 'Friend request';
							$from = 'admin@jeera.com';
							$this->UpdateNotifications($requested_id,$msg,2,$subject,$from);
							$msg = 'Your request has been sent to this user';
							$error =0;
						}else{
							$msg = 'Some error occured. Pleaes try again';
							$error =1;
						}
					 }
				}else{
					$msg = 'Select one user';
					$error =1;
				}
			}else{
				$msg = 'Invalid access';
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
	public function acceptFriendRequestAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$mainViewModel = new ViewModel();
		$offset = 0;
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;	
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$requested_id =  $post['user'];
				if($requested_id!=''){
					if($this->getUserFriendTable()->isFriend($user_id,$requested_id)){
						$msg = 'You are already friends';
						$error =1;
						$this->getUserFriendRequestTable()->makeRequestTOProcessed($user_id,$requested_id);
					 }
					else if($this->getUserFriendTable()->isPending($user_id,$requested_id)){
						 if($this->getUserFriendTable()->AcceptFriendRequest($user_id,$requested_id)){
							$this->getUserFriendRequestTable()->makeRequestTOProcessed($user_id,$requested_id);
							$config = $this->getServiceLocator()->get('Config');
							$base_url = $config['pathInfo']['base_url'];
							$userData = $this->getUserTable()->getUser($user_id); 
							$msg = '<a href="'.$base_url.$userData->user_profile_name.'">'.$identity->user_given_name." accept your friend request</a>";
							$subject = 'Friend request process';
							$from = 'admin@jeera.com';
							$this->UpdateNotifications($requested_id,$msg,2,$subject,$from);
							$msg = '<span>Friends</span>';
							$error =0;
						 }else{
							$msg = 'Some error occured.Please try again.';
							$error =1;
						 }
					 }else{
						$msg = 'No request is made on this profile';
						$error =1;
					 }
				}else{
					$msg = 'Select one user';
					$error =1;
				}
			}else{
				$msg = 'Invalid access';
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
    public function declineFriendRequestAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null;
		$this->layout('layout/planet_home');
		$mainViewModel = new ViewModel();
		$offset = 0;
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;	
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				$requested_id =  $post['user'];
				if($requested_id!=''){
					if($this->getUserFriendTable()->isFriend($user_id,$requested_id)){
						$msg = 'You are already friends';
						$error =1;
						$this->getUserFriendRequestTable()->makeRequestTOProcessed($user_id,$requested_id);
					 }
					else if($this->getUserFriendTable()->isPending($user_id,$requested_id)){
						 if($this->getUserFriendRequestTable()->DeclineFriendRequest($user_id,$requested_id)){						 				
							$msg = '';
							$error =0;
						 }else{
							$msg = 'Some error occured.Please try again.';
							$error =1;
						 }
					 }else{
						$msg = 'No request is made on this profile';
						$error =1;
					 }
				}else{
					$msg = 'Select one user';
					$error =1;
				}
			}else{
				$msg = 'Invalid access';
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
	#access User Module 
    public function getUserTable(){
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
	#access User Profile Module
    public function getUserProfileTable(){
        if (!$this->userProfileTable) {
            $sm = $this->getServiceLocator();
            $this->userProfileTable = $sm->get('User\Model\UserProfileTable');
        }
        return $this->userProfileTable;
    }	
	#access Galaxy/Planet Table Module
	public function getGroupTable(){
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    }	
	#access User Galaxy/Planet Module
	public function getUserGroupTable(){
        if (!$this->userGroupTable) {
            $sm = $this->getServiceLocator();
			$this->userGroupTable = $sm->get('Group\Model\UserGroupTable');
        }
        return $this->userGroupTable;
    } 
	#accessing the country table module
	public function getCountryTable(){
        if (!$this->countryTable) {
            $sm = $this->getServiceLocator();
            $this->countryTable = $sm->get('Country\Model\CountryTable');
        }
        return $this->countryTable;
    } 	
	#access Activity Table Module
	public function getActivityTable(){
        if (!$this->activityTable) {
            $sm = $this->getServiceLocator();
            $this->activityTable = $sm->get('Activity\Model\ActivityTable');
        }
        return $this->activityTable;
    }
	#access Photo Module Model
	public function getPhotoTable(){
        if (!$this->photoTable) {
            $sm = $this->getServiceLocator();
            $this->photoTable = $sm->get('Photo\Model\PhotoTable');
        }
        return $this->photoTable;
    }	
	#access User Tag Module Model
	public function getUserTagTable(){
        if (!$this->userTagTable) {
            $sm = $this->getServiceLocator();
            $this->userTagTable = $sm->get('Tag\Model\UserTagTable');
        }
        return $this->userTagTable;
    } 
	public function getUserFriendTable(){
		 if (!$this->userFriendTable) {
            $sm = $this->getServiceLocator();
            $this->userFriendTable = $sm->get('User\Model\UserFriendTable');
        }
        return $this->userFriendTable;
	}
	public function getUserFriendRequestTable(){
		 if (!$this->userFriendRequestTable) {
            $sm = $this->getServiceLocator();
            $this->userFriendRequestTable = $sm->get('User\Model\UserFriendRequestTable');
        }
        return $this->userFriendRequestTable;
	}
	public function ajaxGetConnectionCountAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$friendRequests_count = 0;	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			if($identity->user_id){
				$friendRequests_count = $this->getUserFriendRequestTable()->getAllReuqestsCount($identity->user_id);
			}			
		}else{
			$error[] = "Your session has to be expired";
		}		 
		echo $friendRequests_count;die();
	}
	public function ajaxNewConnectionRequestsAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$error = array();
		$identity = null;
		$viewModel = new ViewModel();	
		$request   = $this->getRequest();
		$friendRequests = array();	
		$viewModel = new ViewModel();	
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();		 
			if($identity->user_id){
				$friendRequests = $this->getUserFriendRequestTable()->getAllReuqests($identity->user_id);
			}			
		}else{
			$error[] = "Your session has to be expired";
		}		 
		$viewModel->setVariable('error', $error);	
		$viewModel->setVariable('friendRequests', $friendRequests);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function profileTopAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = array();
		$myprofile = 0 ;
		$is_friend = false;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			if($profilename!=''){
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if(!empty($userinfo)&&$userinfo->user_id){
					$userData = $this->userTable->getProfileDetails($userinfo->user_id);
					if($userData->user_id == $identity->user_id){
						$myprofile = 1 ;
						$planetSugessions = $this->forward()->dispatch('Groups\Controller\Groups', array(
									'action' => 'planetSuggestions',								 
								));
						$viewModel->addChild($planetSugessions, 'planetSugessions');						
					}
					if(!$myprofile){
						$is_friend = $this->getUserFriendTable()->isFriend($userData->user_id,$identity->user_id);
					}
					$viewModel->setVariable('userData', $userData);
					$total_progression = 0;
                    if ($userData->user_email) {
                        $total_progression = $total_progression + 10;
                    }
                    if ($userData->user_first_name) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_middle_name) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_last_name) {
                        $total_progression = $total_progression + 5;
                    }
					if ($userData->user_given_name) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_gender) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_profile_phone) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_profile_dob) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_profile_about_me) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_profile_profession) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_profile_profession_at) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_profile_city_id) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_profile_country_id) {
                        $total_progression = $total_progression + 5;
                    }
                    if ($userData->user_profile_current_location) {
                        $total_progression = $total_progression + 5;
					}
					if ($userData->profile_photo) {
						$total_progression = $total_progression + 10;
					}
					if ($userData->timeline_photo) {
						$total_progression = $total_progression + 5;
					}
					if ($userData->is_taged == 1) {
						$total_progression = $total_progression + 5;
					}
					if ($userData->is_member == 1) {
						$total_progression = $total_progression + 5;
					}
					$viewModel->setVariable('total_progression', $total_progression);
					$this->userTagTable = $sm->get('Tag\Model\UserTagTable');
					$user_tags = $this->userTagTable->fetchAllTagsOfUser($userData->user_id);
					$viewModel->setVariable('user_tags', $user_tags);					  
                    $usergroupsettings = $this->getUserGroupTable()->fetchAllUserPlanetsWithUserSettings($userData->user_id,0,10);
					$viewModel->setVariable('usergroupsettings', $usergroupsettings);
					$this->userGeneralsettingsTable = $sm->get('User\Model\UserGeneralSettingsTable');
                    $usergeneralsettings = $this->userGeneralsettingsTable->getUserGeneralsettings($userData->user_id);
					$viewModel->setVariable('usergeneralsettings', $usergeneralsettings);
					$this->userProfilesettingsTable = $sm->get('User\Model\UserProfileSettingsTable');
					$userprofilesettings = array();
					$userprofilesettings = $this->userProfilesettingsTable->getUserProfilesettings($userData->user_id);
					$viewModel->setVariable('userprofilesettings', $userprofilesettings);
				}else{
					$error[] = "Sorry this page is not available";
				}
			}else{
				return $this->redirect()->toRoute('home', array('action' => 'index'));
			}
		}else{
			$error[] = "Your session has to be expired";
		}
		$viewModel->setVariable('myprofile', $myprofile);
		$viewModel->setVariable('is_friend', $is_friend);
		$viewModel->setVariable('error', $error);
		return $viewModel;
	}
	public function memberprofileAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = array();
		$this->layout('layout/planet_home');
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$this->layout()->identity = $identity;
			if($profilename!=''){
				$this->layout('layout/planet_home');
				$profilename = $this->params('member_profile');
				$viewModel->setVariable('profilename', $profilename);
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if(!empty($userinfo)&&$userinfo->user_id){
					$profileTopWidget = $this->forward()->dispatch('User\Controller\UserProfile', array(
											'action' => 'profileTop',
											'member_profile'     => $profilename,							 
										));
					$viewModel->addChild($profileTopWidget, 'profileTopWidget');
					$this->userTable = $sm->get('User\Model\UserFriendTable');
					$friends = $this->userTable->getAllFriends($userinfo->user_id,$identity->user_id,0,25);
					$viewModel->setVariable('friends', $friends);
					$viewModel->setVariable('logged_id', $identity->user_id);
					
				}else{
					$error[] = "Sorry this page is not available";
				}
			}else{
				return $this->redirect()->toRoute('home', array('action' => 'index'));
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		return $viewModel;
	}
    public function settingsAction() { 
        $sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
        $identity = null;
        $user_id = null;
		$error = array();
		$error_count  = 0;
		$email_varification = 0;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request = $this->getRequest();
            $user_id = $identity->user_id;
			if ($request->isPost()) {
                $post = $request->getPost();
				$type = $post['type'];
                switch ($type) {
					case "profile":
						if(isset($post['field_firstname'])&&$post['field_firstname']==''){
							$error_count++;
							$error = 'First name is required';
						}
						if(isset($post['field_givenname'])&&$post['field_givenname']==''){
							$error_count++;
							$error = 'Display name is required';
						}
						if(isset($post['field_email'])&&$post['field_email']==''){
							$error_count++;
							$error = 'Email is required';
						}
						if(isset($post['field_email'])&&$this->getUserTable()->UserEmailExists($post['field_email'],$user_id)){
							$error_count++;
							$error = 'Email already exist';
						}	
						$usertableData = $this->getUserTable()->getUser($user_id);	
						if($error_count==0&&isset($post['field_email'])&&$post['field_email'] != $usertableData->user_email){
							if(isset($post['email_validation_code'])&&$post['email_validation_code']==''){
								$error_count++;
								$error = 'Check your inbox and enter validation code that sent to you in this email';
								$email_varification = 1;
							}else if(isset($post['email_validation_code'])&&$post['email_validation_code']!=''){
								if(!$this->getUserTable()->checkEmailVarification($post['email_validation_code'],$user_id)){
									$error_count++;
									$error = 'Varification Code that you are entered is wrong.Please check your mail inbox and enter correct code';
									$email_varification = 1;
								}								
							}else{
								$user_verification_key = md5('enckey'.rand().time());								 
								if($this->getUserTable()->updateVarificationCode($user_verification_key,$user_id)){
									$this->sendEmailVarificationCodeForEdit($user_verification_key,$user_id,$post['field_email']);
									$error_count++;
									$error = 'Email varification code sent to this email. Please check your inbox and enter code in the given field';		$email_varification = 1;																
								}else{
									$error_count++;
									$error = 'Email properties are failed in this email id. Please try agian with some other email address';
								}
							}
						}
						if($error_count==0){
							if(isset($post['field_givenname'])){
								$userdata['user_given_name'] = $post['field_givenname'];
							}
							if(isset($post['field_firstname'])){
								$userdata['user_first_name'] = $post['field_firstname'];
							}
							if(isset($post['field_middlename'])){
								$userdata['user_middle_name'] = $post['field_middlename'];
							}
							if(isset($post['field_lastname'])){
								$userdata['user_last_name'] = $post['field_lastname'];
							}
							if(isset($post['field_gender'])){
								$userdata['user_gender'] = $post['field_gender'];
							}
							$this->userTable = $sm->get('User\Model\UserTable');
							$usertableData = $this->userTable->getUser($user_id);							 
							if(isset($post['field_email'])&&$post['field_email'] != $usertableData->user_email){
								$userdata['user_email'] = $post['field_email'];
							} 
							if(isset($post['field_dob'])){
								$userprofiledata['user_profile_dob'] =  $post['field_dob'];
							}
							if(isset($post['field_profession'])){
								$userprofiledata['user_profile_profession'] =  $post['field_profession'];
							}
							if(isset($post['field_professionat'])){
								$userprofiledata['user_profile_profession_at'] =  $post['field_professionat'];
							}
							if(isset($post['user_profile_city'])){
								$userprofiledata['user_profile_city_id'] =  $post['user_profile_city'];
							}
							if(isset($post['country_list'])){
								$userprofiledata['user_profile_country_id'] =  $post['country_list'];
							}
							if(isset($post['field_location'])){
								$userprofiledata['user_profile_current_location'] =  $post['field_location'];
							}
							if(isset($post['field_mobile'])){
								$userprofiledata['user_profile_phone'] =  $post['field_mobile'];
							}
							if(!empty($userdata)){
								$this->getUserTable()->updateUser($userdata,$user_id);
							}
							if(!empty($userprofiledata)){
								$user_profile_data = $this->getUserProfileTable()->getUserProfileForUserId($user_id);
								if($user_profile_data->user_profile_id){  
									$this->getUserProfileTable()->updateUserProfile($userprofiledata, $user_profile_data->user_profile_id); 
								}else{
									$userprofiledata['user_profile_user_id'] = $user_id;
									$userProfile = new UserProfile();
									$userProfile->exchangeArray($userprofiledata);
									$insertedUserProfileId = $this->getUserProfileTable()->saveUserProfile($userProfile);
								}
							}
							$error_count=0;
							$error = '';
						}
					break;
					case 'group':					
						$group_id			= $post['group_id'];
						if($group_id){
							if(isset($post['activity'])&&$post['activity']=='yes'){
								$activity 			= $post['activity'];
							}else{
								$activity 			= "no";
							}
							if(isset($post['member'])&&$post['member']=='yes'){
								$member 			= $post['member'];
							}else{
								$member 			= "no";
							}
							if(isset($post['media'])&&$post['media']=='yes'){
								$media 			= $post['media'];
							}else{
								$media 			= "no";
							}
							if(isset($post['announcement'])&&$post['announcement']=='yes'){
								$group_announcement 	= $post['announcement'];
							}else{
								$group_announcement 	= "no";
							}
							if(isset($post['discussion'])&&$post['discussion']=='yes'){
								$discussion 			= $post['discussion'];
							}else{
								$discussion 			= "no";
							}							                  
							$data['user_id'] 	= $user_id;
							$data['group_id'] 	= $group_id;
							$data['activity'] 	= $activity;
							$data['discussion'] = $discussion;
							$data['media']		= $media;
							$data['member'] 	= $member;
							$data['group_announcement'] = $group_announcement;                        
							$this->userGroupsettingsTable = $sm->get('User\Model\UserGroupSettingsTable');
							$old_settings = $this->userGroupsettingsTable->getUserGroupSettingsOfSelectedGroup($user_id,$group_id);
							if(!empty($old_settings)&&$old_settings->id!=''){
								$data['id'] = $old_settings->id;
							}else{
								$data['id'] = 0;
							}
							if( $savegroupsettings = $this->userGroupsettingsTable->saveUserGroupsettings($data)){
								$error_count=0;
								$error = '';
							}else{
								$error_count++;
								$error = 'Some error occured. Please try again';
							}
						}else{
							$error_count++;
							$error = 'Unable to process';
						}
					break;
					case 'general':                         
                        $event_alert = "no";
                        $survey_alert = "no";
                        $new_feature = "no";
                        $friend_request = "all";
                        $message = "all";                         
                        if ($post['alerts']) {
                            foreach ($post['alerts'] as $alerts) {

                                if ($alerts == "event") {
                                    $event_alert = "yes";
                                }
                                if ($alerts == "survay") {
                                    $survey_alert = "yes";
                                }
                                if ($alerts == "feature") {
                                    $new_feature = "yes";
                                }
                            }
                        }

                        if ($post['connection']) {
                            $friend_request = $post['connection'];
                        }
                        if ($post['message']) {
                            $message = $post['message'];
                        }						
                        $data['user_id'] = $user_id;
                        $data['event_alert'] = $event_alert;
                        $data['survey_alert'] = $survey_alert;
                        $data['new_feature'] = $new_feature;
                        $data['friend_request'] = $friend_request;
                        $data['message'] = $message;
                        $this->userGeneralsettingsTable = $sm->get('User\Model\UserGeneralSettingsTable');
						$usergeneralsettings = $this->userGeneralsettingsTable->getUserGeneralsettings($user_id);
						if($usergeneralsettings&&$usergeneralsettings->id)
						$data['id'] = $usergeneralsettings->id;
						else
						$data['id'] = 0;
                        if($this->userGeneralsettingsTable->saveUserGeneralsettings($data)){
							$error_count=0;
							$error = '';
						}else{
							$error_count++;
							$error = 'Some error occured. Please try again';
						}                       
                        break;
					default:
						$error = 'empty process';
						$error_count++;
					break;
				}
            }else{
				$error = 'Invalide accesss';
				$error_count++;
			}
		}else{
			$error = 'Your session expired';
			$error_count++;
		}
		 if($error_count==0){
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
			$return_array['email_varification'] = $email_varification;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
			$return_array['email_varification'] =$email_varification;
		 }
		 echo json_encode($return_array);die();
    }
	public function settingsGroupLoadmoreAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
        $identity = null;
        $user_id = null;
		$error = array();
		$error_count  = 0;
		$request = $this->getRequest();
		$viewModel = new ViewModel();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			
            $user_id = $identity->user_id;
			if ($request->isPost()) {
                $post = $request->getPost();
				$page = $post['page'];
				$offset = 0;
				if($page>0){
					$offset = $page * 10;
				}
				$limit = 10;
				$usergroupsettings = $this->getUserGroupTable()->fetchAllUserPlanetsWithUserSettings($user_id,$offset,$limit);
				$viewModel->setVariable('usergroupsettings', $usergroupsettings);
			}
			else{
				$error[] = "Invalid access";	
			}
		}else{
			$error[] = "Your session expired..";
		}
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function updatebioAction() {
        $sm = $this->getServiceLocator();
        $auth = new AuthenticationService();
        $identity = null;
        $user_id = null;
		$error = '';
		$error_count = 0;
        if ($auth->hasIdentity()) {
            $request = $this->getRequest();
            $identity = $auth->getIdentity();
            $user_id = $identity->user_id;
            if ($request->isPost()) {
                $post = $request->getPost();
                $bio = $post['bio'];
                $data['user_profile_user_id'] = $user_id;
                $data['user_profile_about_me'] = $bio;
                $this->userProfileTable = $sm->get('User\Model\UserProfileTable');
                if($this->userProfileTable->updateuserAboutme($data)){
					$error = '';
					$error_count = 0;
				}else{
					$error = 'Some error occured. Please try again';
					$error_count++;
				}
                 
            }
			else{
				$error = 'Invalid access';
				$error_count++;
			}
        }else{
			$error = 'Your session expired';
			$error_count++;
		}
		if($error_count==0){
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
    }
	public function addTagAction(){
		$sm = $this->getServiceLocator();
        $auth = new AuthenticationService();
        $identity = null;
        $user_id = null;
		$error = '';
		$error_count = 0;
        if ($auth->hasIdentity()) {
            $request = $this->getRequest();
            $identity = $auth->getIdentity();
            $user_id = $identity->user_id;
            if ($request->isPost()) {
                $post = $request->getPost();
                $tag_id = $post['tag_id'];
				if($tag_id>0){
					$data['user_tag_user_id'] = $user_id;
					$data['user_tag_tag_id'] = $tag_id;
					$this->userTagTable = $sm->get('Tag\Model\UserTagTable');
					$UserTag = new UserTag();
					$UserTag->exchangeArray($data);
					$inserted_id = $this->userTagTable->saveUserTag($UserTag);
					if($inserted_id){
						$this->TagTable = $sm->get('Tag\Model\TagTable');
						$tag_data = $this->TagTable->getTag($tag_id);
						$config = $this->getServiceLocator()->get('Config');					  
						$base_url = $config['pathInfo']['base_url'];
						$error = '<span id="user_tag_added_'.$tag_data->tag_id.'">'.$tag_data->tag_title.'<a href="javascript:void(0)" id="tag_remove_'.$tag_data->tag_id.'" class="remove_tag"><img src="'.$base_url.'/public/images/remove-icon.png" alt="" /></a></span>';
						$error_count = 0;
					}else{
						$error = 'Some error occured. Please try again';
						$error_count++;
					}
				}else{
					$error = 'Invalid tag';
					$error_count++;
				}                
            }
			else{
				$error = 'Invalid access';
				$error_count++;
			}
        }else{
			$error = 'Your session expired';
			$error_count++;
		}
		if($error_count==0){
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function removeTagAction(){
		$sm = $this->getServiceLocator();
        $auth = new AuthenticationService();
        $identity = null;
        $user_id = null;
		$error = '';
		$error_count = 0;
        if ($auth->hasIdentity()) {
            $request = $this->getRequest();
            $identity = $auth->getIdentity();
            $user_id = $identity->user_id;
            if ($request->isPost()) {
                $post = $request->getPost();
                $tag_id = $post['tag_id'];
				if($tag_id>0){					 
					$this->userTagTable = $sm->get('Tag\Model\UserTagTable');				 
					if($this->userTagTable->removeUserTag($tag_id,$user_id)){						 
						$error = '';
						$error_count = 0;
					}else{
						$error = 'Some error occured. Please try again';
						$error_count++;
					}
				}else{
					$error = 'Invalid tag';
					$error_count++;
				}                
            }
			else{
				$error = 'Invalid access';
				$error_count++;
			}
        }else{
			$error = 'Your session expired';
			$error_count++;
		}
		if($error_count==0){
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function ajaxLoadMoreFriendsAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = array();
		$this->layout('layout/planet_home');
		$request = $this->getRequest();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');			 
			if($profilename!=''){				 
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if(!empty($userinfo)&&$userinfo->user_id){
					$post = $request->getPost();	
					$page = 0;
					if ($request->isPost()){
						$page =$post->get('page');
						if(!$page)
						$page = 0;						 
					}
					$offset = $page*25;
					$this->userTable = $sm->get('User\Model\UserFriendTable');
					$friends = $this->userTable->getAllFriends($userinfo->user_id,$identity->user_id,$offset,25);
					$viewModel->setVariable('friends', $friends);
					$viewModel->setVariable('logged_id', $identity->user_id);
					
				}else{
					$error[] = "Sorry this page is not available";
				}
			}else{
				$error[] = "Sorry this page is not available";
			}
		}else{
			$error[] = "Your session expired";
		}
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function planetsAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = array();
		$this->layout('layout/planet_home');
		$myprofile = 0 ;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$this->layout()->identity = $identity;
			if($profilename!=''){
				$this->layout('layout/planet_home');
				$profilename = $this->params('member_profile');
				$viewModel->setVariable('profilename', $profilename);
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
					$viewModel->addChild($profileTopWidget, 'profileTopWidget');
					$this->usergroupTable = $sm->get('Groups\Model\UserGroupTable');
					$planets = $this->usergroupTable->fetchAllUserPlanets($userinfo->user_id,0,10);
					$viewModel->setVariable('planets', $planets);
					$countries = $this->getCountryTable()->fetchAll();
					$viewModel->setVariable('countries', $countries);		
					$viewModel->setVariable('logged_id', $identity->user_id);
					$galexies =  $this->getGroupTable()->fetchAllGroups();
					$viewModel->setVariable('galexies', $galexies);
					$selectAllTags = $this->getTagTable()->getPopularUserTags(0,12);
					$viewModel->setVariable('popularTags', $selectAllTags);
				}else{
					$error[] = "Sorry this page is not available";
				}
			}else{
				return $this->redirect()->toRoute('home', array('action' => 'index'));
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		$viewModel->setVariable('myprofile', $myprofile);
		return $viewModel;
	}
	public function ajaxLoadMorePlanetsAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = array();
		$this->layout('layout/planet_home');
		$request = $this->getRequest();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');			 
			if($profilename!=''){				 
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if(!empty($userinfo)&&$userinfo->user_id){
					$post = $request->getPost();	
					$page = 0;
					if ($request->isPost()){
						$page =$post->get('page');
						if(!$page)
						$page = 0;						 
					}
					$offset = $page*10;
					$this->usergroupTable = $sm->get('Groups\Model\UserGroupTable');
					$planets = $this->usergroupTable->fetchAllUserPlanets($userinfo->user_id,$offset,10);
					$viewModel->setVariable('planets', $planets);	
								 			
					$viewModel->setVariable('logged_id', $identity->user_id);
					
				}else{
					$error[] = "Sorry this page is not available";
				}
			}else{
				$error[] = "Sorry this page is not available";
			}
		}else{
			$error[] = "Your session expired";
		}
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function getTagTable(){
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Tag\Model\TagTable');
        }
        return $this->tagTable;
    }
	public function photosAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = array();
		$myprofile = 0;
		$this->layout('layout/planet_home');
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$this->layout()->identity = $identity;
			if($profilename!=''){
				$this->layout('layout/planet_home');
				$profilename = $this->params('member_profile');
				$viewModel->setVariable('profilename', $profilename);
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
					$viewModel->addChild($profileTopWidget, 'profileTopWidget');					 		
					$viewModel->setVariable('logged_id', $identity->user_id);
					$albums = $this->getAlbumTable()->fetchAllUserAlbum($userinfo->user_id);
					$viewModel->setVariable('albums', $albums);
					$tagged_album  = $this->getAlbumTable()->fetchTaggedUserAlbum($userinfo->user_id);
					$viewModel->setVariable('tagged_album', $tagged_album);
					$form = new AlbumForm();
					$viewModel->setVariable('form', $form);
				}else{
					$error[] = "Sorry this page is not available";
				}
			}else{
				return $this->redirect()->toRoute('home', array('action' => 'index'));
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		$viewModel->setVariable('myprofile', $myprofile);
		return $viewModel;
	}
	public function getAlbumTable(){
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
	public function saveProfilePicAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = '';
		$error_count = 0;
		$this->layout('layout/planet_home');
		$request = $this->getRequest();
		$config = $this->getServiceLocator()->get('Config');
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');			 
			if($profilename!=''){			
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				if(!empty($userinfo)&&$userinfo->user_id){  
					$post = $request->getPost();				
					if ($request->isPost()){
						$Xvalue=$post['Xvalue'];
						$Yvalue=$post['Yvalue'];
						$Wvalue=$post['Wvalue'];
						$Hvalue=$post['Hvalue'];
						$data_id=$post['data_id'];
						$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
						$album_details = $this->albumDataTable->getAlbumDetailsFromData($data_id); 
						$target = $config['pathInfo']['UploadPath'].'profile/';
						if(!is_dir($target)) {
							@mkdir($target);	
						}
						$target.=$identity->user_id.'/';
						if(!is_dir($target)) {
							@mkdir($target);	
						}
						$filename = $identity->user_given_name.'_'.time().'.jpg';
						if($album_details->album_group_id){
							$file_path = $config['pathInfo']['AlbumUploadPath'].$album_details->album_group_id.'/'.$album_details->album_id.'/'.$album_details->data_content;
						}else{
							$file_path = $config['pathInfo']['AlbumUploadPath'].'profile'.'/'.$album_details->album_user_id.'/'.$album_details->album_id.'/'.$album_details->data_content;
						}
						if($Xvalue&&$Yvalue&&$Wvalue&&$Hvalue){
							$targ_w =218; $targ_h = 192;	
							$newtarget ='';					
							$newtarget = $target.'218x192/';
							if(!is_dir($newtarget)) {
								@mkdir($newtarget);	
							}					 
							if($this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename)){
								$this->userProfilePhotoTable = $sm->get('User\Model\UserProfilePhotoTable');
								$profilePicData = $this->userProfilePhotoTable->checkUserProfilePicExist($identity->user_id);
								if(!empty($profilePicData)){
									@unlink($target.$profilePicData->profile_photo);
								}
								$data['profile_user_id'] = $identity->user_id;
								$data['profile_photo'] = $filename;
								$data['profile_user_id'] = $identity->user_id;
								$profilePicData = $this->userProfilePhotoTable->addUserProfilePic($data);
								$user_data['user_profile_photo_id'] = $profilePicData;
								$this->userTable = $sm->get('User\Model\UserTable');
								$this->userTable->updateUser($user_data,$identity->user_id);
								$targ_w =66; $targ_h = 66;	
								$newtarget ='';					
								$newtarget = $target.'66x66/';
								if(!is_dir($newtarget)) {
									@mkdir($newtarget);	
								}	
								$this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename);
								if(!empty($profilePicData)){
									@unlink($newtarget.$profilePicData->profile_photo);
								}
								$targ_w =56; $targ_h = 56;	
								$newtarget ='';					
								$newtarget = $target.'56x56/';
								if(!is_dir($newtarget)) {
									@mkdir($newtarget);	
								}
								$this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename);
								if(!empty($profilePicData)){
									@unlink($newtarget.$profilePicData->profile_photo);
								}
								$targ_w =45; $targ_h = 45;	
								$newtarget ='';					
								$newtarget = $target.'45x45/';
								if(!is_dir($newtarget)) {
									@mkdir($newtarget);	
								}
								$this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename);
								if(!empty($profilePicData)){
									@unlink($newtarget.$profilePicData->profile_photo);
								}
								$targ_w =36; $targ_h = 36;	
								$newtarget ='';					
								$newtarget = $target.'36x36/';
								if(!is_dir($newtarget)) {
									@mkdir($newtarget);	
								}
								$this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename);
								if(!empty($profilePicData)){
									@unlink($newtarget.$profilePicData->profile_photo);
								}
							}else{
								$error = 'Some error occured. please try again';
								$error_count++;
							}
						}else{
							$targ_w =218; $targ_h = 192;
							$newtarget ='';
							$newtarget = $target.'218x192/';
							if(!is_dir($newtarget)) {
								@mkdir($newtarget);	
							}
							if($this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename)){
								$this->userProfilePhotoTable = $sm->get('User\Model\UserProfilePhotoTable');
								$profilePicData = $this->userProfilePhotoTable->checkUserProfilePicExist($identity->user_id);
								if(!empty($profilePicData)){
									@unlink($target.$profilePicData->profile_photo);
								}
								$data['profile_user_id'] = $identity->user_id;
								$data['profile_photo'] = $filename;
								$data['profile_user_id'] = $identity->user_id;
								$profilePicData = $this->userProfilePhotoTable->addUserProfilePic($data);
								$user_data['user_profile_photo_id'] = $profilePicData;
								$this->userTable = $sm->get('User\Model\UserTable');
								$this->userTable->updateUser($user_data,$identity->user_id);
								$targ_w =66; $targ_h = 66;	
								$newtarget ='';					
								$newtarget = $target.'66x66/';
								if(!is_dir($newtarget)) {
									@mkdir($newtarget);	
								}	
								$this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename);
								if(!empty($profilePicData)){
									@unlink($newtarget.$profilePicData->profile_photo);
								}
								$targ_w =56; $targ_h = 56;	
								$newtarget ='';					
								$newtarget = $target.'56x56/';
								if(!is_dir($newtarget)) {
									@mkdir($newtarget);	
								}
								$this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename);
								if(!empty($profilePicData)){
									@unlink($newtarget.$profilePicData->profile_photo);
								}
								$targ_w =45; $targ_h = 45;	
								$newtarget ='';					
								$newtarget = $target.'45x45/';
								if(!is_dir($newtarget)) {
									@mkdir($newtarget);	
								}
									
								$this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename);
								if(!empty($profilePicData)){
									@unlink($newtarget.$profilePicData->profile_photo);
								}
								$targ_w =36; $targ_h = 36;	
								$newtarget ='';					
								$newtarget = $target.'36x36/';
								if(!is_dir($newtarget)) {
									@mkdir($newtarget);	
								}
								$this->createImageWithDifferentDimensions($file_path,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename);
								if(!empty($profilePicData)){
									@unlink($newtarget.$profilePicData->profile_photo);
								}
							}else{
								$error = 'Some error occured. please try again';
								$error_count++;
							}
						}
						 
						
						
 	 
					}else{	
						$error = 'Invalid access';
						$error_count++;
					}
				}
				else{
					$error = 'Invalid access';
					$error_count++;
				}
					
			}else{
				$error = "Sorry this page is not available";
				$error_count++;
			}
			 
		}else{
			$error =  "Your session expired";
			$error_count++;
		}
		 if($error_count==0){
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function addProfilePicAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = '';
		$error_count = 0;
		$this->layout('layout/planet_home');
		$request = $this->getRequest();
		$config = $this->getServiceLocator()->get('Config');
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$Xvalue=0;
			$Yvalue=0;
			$Wvalue=0;
			$Hvalue=0;
			 
				if ($request->isPost()){
					$Xvalue=$post['Xvalue'];
					$Yvalue=$post['Yvalue'];
					$Wvalue=$post['Wvalue'];
					$Hvalue=$post['Hvalue'];
				}
				$imagefiles  = $_FILES; 			
				$target = $config['pathInfo']['UploadPath'].'profile/';
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$target.=$identity->user_id.'/';
				if(!is_dir($target)) {
					mkdir($target);	
				}
				 
				/*$fn = $imagefiles[0]['tmp_name'];
				$size = getimagesize($fn);
				$ratio = $size[0]/$size[1]; // width/height
				if( $ratio > 1) {
					$width = 400;
					$height = 400/$ratio;
				}
				else {
					$width = 400*$ratio;
					$height = 400;
				}
				$jpeg_quality = 90;
				//$filename = $identity->user_given_name.'_'.time().'.jpg';
				
				$src = imagecreatefromstring(file_get_contents($fn));
				$dst = imagecreatetruecolor($width,$height);
				imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
				imagedestroy($src);
				imagejpeg($dst,$target.$filename,$jpeg_quality); // adjust format as needed
				imagedestroy($dst);*/
				$filename = $post['filename'];
				if($filename){
				if($Xvalue&&$Yvalue&&$Wvalue&&$Hvalue){
					$targ_w =218; $targ_h = 192;	
					$newtarget ='';					
					$newtarget = $target.'218x192/';
					if(!is_dir($newtarget)) {
						mkdir($newtarget);	
					}					 
					if($this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename)){
						$this->userProfilePhotoTable = $sm->get('User\Model\UserProfilePhotoTable');
						$profilePicData = $this->userProfilePhotoTable->checkUserProfilePicExist($identity->user_id);
						if(!empty($profilePicData)){
							@unlink($target.$profilePicData->profile_photo);
						}
						$data['profile_user_id'] = $identity->user_id;
						$data['profile_photo'] = $filename;
						$data['profile_user_id'] = $identity->user_id;
						$profilePicData = $this->userProfilePhotoTable->addUserProfilePic($data);
						$user_data['user_profile_photo_id'] = $profilePicData;
						$this->userTable = $sm->get('User\Model\UserTable');
						$this->userTable->updateUser($user_data,$identity->user_id);
						$targ_w =66; $targ_h = 66;	
						$newtarget ='';					
						$newtarget = $target.'66x66/';
						if(!is_dir($newtarget)) {
							@mkdir($newtarget);	
						}	
						$this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename);
						if(!empty($profilePicData)){
							@unlink($newtarget.$profilePicData->profile_photo);
						}
						$targ_w =56; $targ_h = 56;	
						$newtarget ='';					
						$newtarget = $target.'56x56/';
						if(!is_dir($newtarget)) {
							@mkdir($newtarget);	
						}
						$this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename);
						if(!empty($profilePicData)){
							@unlink($newtarget.$profilePicData->profile_photo);
						}
						$targ_w =45; $targ_h = 45;	
						$newtarget ='';					
						$newtarget = $target.'45x45/';
						if(!is_dir($newtarget)) {
							@mkdir($newtarget);	
						}
						$this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename);
						if(!empty($profilePicData)){
							@unlink($newtarget.$profilePicData->profile_photo);
						}
						$targ_w =36; $targ_h = 36;	
						$newtarget ='';					
						$newtarget = $target.'36x36/';
						if(!is_dir($newtarget)) {
							@mkdir($newtarget);	
						}
						$this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$newtarget.$filename);
						if(!empty($profilePicData)){
							@unlink($newtarget.$profilePicData->profile_photo);
						}
						@unlink($target.'temp/'.$filename);
					}else{
						$error = 'Some error occured. please try again';
						$error_count++;
					}
				}else{
					$targ_w =218; $targ_h = 192;
					$newtarget ='';
					$newtarget = $target.'218x192/';
					if(!is_dir($newtarget)) {
						mkdir($newtarget);	
					}
					$fn =  $target.$filename;
					$size = getimagesize($target.$filename);
					$width = $size[0];
					$height = $size[1];
					if($this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename)){
						$this->userProfilePhotoTable = $sm->get('User\Model\UserProfilePhotoTable');
						$profilePicData = $this->userProfilePhotoTable->checkUserProfilePicExist($identity->user_id);
						if(!empty($profilePicData)){
							@unlink($target.$profilePicData->profile_photo);
						}
						$data['profile_user_id'] = $identity->user_id;
						$data['profile_photo'] = $filename;
						$data['profile_user_id'] = $identity->user_id;
						$profilePicData = $this->userProfilePhotoTable->addUserProfilePic($data);
						$user_data['user_profile_photo_id'] = $profilePicData;
						$this->userTable = $sm->get('User\Model\UserTable');
						$this->userTable->updateUser($user_data,$identity->user_id);
						$targ_w =66; $targ_h = 66;	
						$newtarget ='';					
						$newtarget = $target.'66x66/';
						if(!is_dir($newtarget)) {
							@mkdir($newtarget);	
						}	
						$this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename);
						if(!empty($profilePicData)){
							@unlink($newtarget.$profilePicData->profile_photo);
						}
						$targ_w =56; $targ_h = 56;	
						$newtarget ='';					
						$newtarget = $target.'56x56/';
						if(!is_dir($newtarget)) {
							@mkdir($newtarget);	
						}
						$this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename);
						if(!empty($profilePicData)){
							@unlink($newtarget.$profilePicData->profile_photo);
						}
						$targ_w =45; $targ_h = 45;	
						$newtarget ='';					
						$newtarget = $target.'45x45/';
						if(!is_dir($newtarget)) {
							@mkdir($newtarget);	
						}
						$this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename);
						if(!empty($profilePicData)){
							@unlink($newtarget.$profilePicData->profile_photo);
						}
						$targ_w =36; $targ_h = 36;	
						$newtarget ='';					
						$newtarget = $target.'36x36/';
						if(!is_dir($newtarget)) {
							@mkdir($newtarget);	
						}
						$this->createImageWithDifferentDimensions($target.$filename,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$width,$height,$newtarget.$filename);
						if(!empty($profilePicData)){
							@unlink($newtarget.$profilePicData->profile_photo);
						}
						@unlink($target.'temp/'.$filename);
					}else{
						$error = 'Some error occured. please try again';
						$error_count++;
					}
				}
			}else{
				$error =  "File is not existing in the system";
				$error_count++;
			}
			 	 
		}else{	
			$error =  "Your session expired";
			$error_count++;
		}
		if($error_count==0){
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function createImageWithDifferentDimensions($image,$targ_w,$targ_h,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue,$target_image){
		$jpeg_quality = 90;
		$img_r = imagecreatefromjpeg($image);
		$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
		imagecopyresampled($dst_r,$img_r,0,0,$Xvalue,$Yvalue,$targ_w,$targ_h,$Wvalue,$Hvalue);
	
		if(imagejpeg($dst_r,$target_image,$jpeg_quality)){
			return true;
		}else{
			return false;
		}
	}
	public function addProfileCoverAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = '';
		$error_count = 0;
		$this->layout('layout/planet_home');
		$request = $this->getRequest();
		$config = $this->getServiceLocator()->get('Config');
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();
			$leftvalue=0;
			$topvalue=0;
			 
			if(!empty($_FILES)){
				if ($request->isPost()){
					$leftvalue=$post['leftvalue'];
					$topvalue=$post['topvalue'];					 
				}
				$imagefiles  = $_FILES; 			
				$filename = '';
				$filename = time().$imagefiles[0]['name'];
				$target = $config['pathInfo']['UploadPath'].'profile/';
				if(!is_dir($target)) {
					@mkdir($target);	
				}
				$target =$target.$identity->user_id.'/';
				if(!is_dir($target)) {
					@mkdir($target);	
				}
				$target =$target.'cover/';
				if(!is_dir($target)) {
					@mkdir($target);	
				}
				if(move_uploaded_file($imagefiles[0]["tmp_name"],$target. $filename)){
					$this->userCoverPhotoTable = $sm->get('User\Model\UserCoverPhotoTable');
					$profileCoverData = $this->userCoverPhotoTable->checkUserCoverPicExist($identity->user_id);
					if(!empty($profileCoverData)){
						@unlink($target.$profileCoverData->cover_photo);
					}
					$data['cover_user_id'] = $identity->user_id;
					$data['cover_photo'] = $filename;
					$data['cover_photo_left'] = $leftvalue;
					$data['cover_photo_top'] = $topvalue;
					$profilePicData = $this->userCoverPhotoTable->addUserCoverPic($data);
					$user_data['user_timeline_photo_id'] = $profilePicData;
					$this->userTable = $sm->get('User\Model\UserTable');
					$this->userTable->updateUser($user_data,$identity->user_id);
				}else{
					$error = 'Some error occured. please try again';
					$error_count++;
				}				 
			}			 	 
		}else{	
			$error =  "Your session expired";
			$error_count++;
		}
		if($error_count==0){
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function addProfilePicToTempAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = '';
		$error_count = 0;		 
		$request = $this->getRequest();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();			 
			if(!empty($_FILES)){				 
				$imagefiles  = $_FILES; 			
				$target = $config['pathInfo']['UploadPath'].'profile/';
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$target.=$identity->user_id.'/';
				$target_root = $target;
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$target.='/temp/';
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$filename = $identity->user_given_name.'_'.time().$imagefiles[0]['name'];
				if(move_uploaded_file($imagefiles[0]['tmp_name'],$target.$filename)){
					$fn =  $target.$filename;
					$size = getimagesize($target.$filename);
					$ratio = $size[1]/$size[0]; // width/height
					if( $ratio > 1) {
						$height = 400;
						$width = 400/$ratio;
					}
					else {
						$height = 400*$ratio;
						$width = 400;
					}
					$jpeg_quality = 90;
					 
					$src = imagecreatefromstring(file_get_contents($fn));
					$dst = imagecreatetruecolor($width,$height);
					imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
					imagedestroy($src);
					imagejpeg($dst,$target_root.$filename,$jpeg_quality); // adjust format as needed
					imagedestroy($dst); 
					$base_url = $config['pathInfo']['base_url'];
					$image_url = $base_url."public/datagd/profile/".$identity->user_id."/".$filename;					
					$viewModel->setVariable('filename', $filename);	
					$viewModel->setVariable('image', $image_url);	
				}else{
					$error[] = 'Some error occured. please try again';					
				}
				 
			}else{
				$error[] =  "Upload one new image";			
			}		 
		}else{	
			$error[]=  "Your session expired";
			
		}	 
		$viewModel->setVariable('error', $error);	
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function addAlbumPicToTempAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = '';
		$error_count = 0;		 
		$request = $this->getRequest();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel =  new ViewModel();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();			 
			if(!empty($post)){				 
				$data_id=$post['data_id'];
				$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
				$album_details = $this->albumDataTable->getAlbumDetailsFromData($data_id); 
				$filename = $identity->user_given_name.'_'.time().'.jpg';
				if($album_details->album_group_id){
					$file_path = $config['pathInfo']['AlbumUploadPath'].$album_details->album_group_id.'/'.$album_details->album_id.'/'.$album_details->data_content;
				}else{
					$file_path = $config['pathInfo']['AlbumUploadPath'].'profile'.'/'.$album_details->album_user_id.'/'.$album_details->album_id.'/'.$album_details->data_content;
				}
				$target = $config['pathInfo']['UploadPath'].'profile/';
				if(!is_dir($target)) {
					mkdir($target);	
				}
				$target.=$identity->user_id.'/';
				$target_root = $target;
				if(!is_dir($target)) {
					mkdir($target);	
				}
				 				 
				$fn =  $file_path;
				$size = getimagesize($file_path);
				$ratio = $size[1]/$size[0]; // width/height
				if( $ratio > 1) {
					$height = 400;
					$width = 400/$ratio;
				}
				else {
					$height = 400*$ratio;
					$width = 400;
				}
				$jpeg_quality = 90;
				 
				$src = imagecreatefromstring(file_get_contents($fn));
				$dst = imagecreatetruecolor($width,$height);
				imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
				imagedestroy($src);
				imagejpeg($dst,$target_root.$filename,$jpeg_quality); // adjust format as needed
				imagedestroy($dst); 
				$base_url = $config['pathInfo']['base_url'];
				$image_url = $base_url."public/datagd/profile/".$identity->user_id."/".$filename;					
				$viewModel->setVariable('filename', $filename);	
				$viewModel->setTemplate('user/user-profile/add-profile-pic-to-temp');
				$viewModel->setVariable('image', $image_url);	
				 
				 
			}else{
				$error[] =  "Upload one new image";			
			}		 
		}else{	
			$error[]=  "Your session expired";
			
		}	 
		$viewModel->setVariable('error', $error);	
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function inviteFriendsAction(){
		$error = array();
		$auth = new AuthenticationService();	
		$identity = null;    
		$user_id  = null; 
		$mainViewModel = new ViewModel();
		$error =0;
		$msg = '';
		if ($auth->hasIdentity()) { 
			$identity = $auth->getIdentity();
			$user_id = $identity->user_id;	
			$request = $this->getRequest();
			if ($request->isPost()) {
				$post = $request->getPost();
				if($post['firstname_invite']==''){
					$msg = 'Enter friend name.';
					$error =1;
				}
				if($post['email_invite']==''){
					$msg = 'Enter friend Email.';
					$error =1;
				}
				if(!filter_var($post['email_invite'], FILTER_VALIDATE_EMAIL))
				{
					$msg = 'Enter a valid Email.';
					$error =1;
				}
				if($error==0){
					if($this->sendInvitationEmail($user_id,$post['firstname_invite'],$post['email_invite']) ){
						$msg = 'Your invitation has to be send.';
						$error =0;
					}
				} 
			}else{
				$msg = 'Invalid access';
				$error =1;
			}
		}else{
			$msg = 'Your session is expired. Please try again after login';
			$error =1;
		}
		$return_array = array();

		if($error != 0) {
			$return_array = array('error' =>1,'msg'=>$msg);
		}else{	
			$return_array = array('error' =>0,'msg'=>$msg);
		}
		echo json_encode($return_array);die();
	}
	public function sendInvitationEmail($user_id,$firstname,$emailId){
		$sm = $this->getServiceLocator();
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');
		$this->userTable = $sm->get('User\Model\UserTable');	
		$userData = array();
		$userData = $this->userTable->getUser($user_id);
		$body = $this->renderer->render('user/email/emailInvitation.phtml', array('userdata'=>$userData,'firstname'=>$firstname,));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";

		$textPart = new MimePart($body);
		$textPart->type = "text/plain";

		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));

		$message = new Mail\Message();
		$message->setFrom($userData->user_email);
		$message->addTo($emailId);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject("Jeera invitation");
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');

		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
	}
	public function savePrivacySettingsAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = '';
		$error_count = 0;		 
		$request = $this->getRequest();		 
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$post = $request->getPost();			 
			if(!empty($post)){
				$field =  $post['field'];
				$value =  $post['value'];
				$this->userProfilesettingsTable = $sm->get('User\Model\UserProfileSettingsTable');
				$userprofilesettings = $this->userProfilesettingsTable->getUserProfilesettings($identity->user_id); 	
				$settings = array();
				$settings['user_id'] = $identity->user_id;
				if($userprofilesettings){
					 $settings['id'] = $userprofilesettings->id;
				}else{
					$settings['id'] = 0; 
				}
				if($field!=''&&$value!=''){
					$settings['field'] = $field; 
					$settings['option'] = $value;
					if($this->userProfilesettingsTable->saveUserProfilesettings($settings)){
						$error =  "Successfully changed..";
						$error_count = 0 ;	
					}else{
						$error =  "Some error occured. Please try again";
						$error_count++;
					}
				}else{
					$error =  "Values are not specified";
					$error_count++;
				}
				
			}else{
				$error =  "Unable to process";
				$error_count++;
			}			
		}else{	
			$error =  "Your session expired";
			$error_count++;
		}
		if($error_count==0){
			$return_array['msg'] = $error;
			$return_array['success'] = 1;
		 }
		 else{
			$return_array['msg'] = $error;
			$return_array['success'] = 0;
		 }
		 echo json_encode($return_array);die();
	}
	public function sendEmailVarificationCodeForEdit($varification_code,$user_id,$email){
		$sm = $this->getServiceLocator();
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');
		$this->userTable = $sm->get('User\Model\UserTable');	
		$userData = array();
		$userData = $this->userTable->getUser($user_id);
		$body = $this->renderer->render('user/email/emailVarificationEdit.phtml', array('userdata'=>$userData,'varification_code'=>$varification_code,));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";

		$textPart = new MimePart($body);
		$textPart->type = "text/plain";

		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));

		$message = new Mail\Message();
		$message->setFrom('admin@jeera.com');
		$message->addTo($email);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject("Jeera Email Varification");
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');

		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
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
	public function feedsAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();
		$error = array();		
		$is_admin = 0;		
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $identity->user_profile_name;
			$identity->profile_pic = '';
			$user_id = $identity->user_id;
			$profilepic = $this->getUserTable()->getUserProfilePic($user_id);
			$activity = $this->getActivityTable()->getEventCalendarJason($user_id); 
			foreach($profilepic as $pic){$identity->profile_pic = $pic->biopic;}
			$identity->activity = $activity;			 
			$this->layout()->identity = $identity;
			$this->layout()->page = 'Feeds';
			if($profilename!=''){
				$viewModel->setVariable('profilename', $profilename);
				$this->userTable = $sm->get('User\Model\UserTable');
				$userinfo = $this->userTable->getUserByProfilename($profilename);
				$feedSummery = $this->userTable->getNewsSummery($identity->user_id,0,10);
				$array_news = array();
				foreach($feedSummery as $news){
					switch($news['type']){
						case "New Activity":
							$event_id = $news['event_id'];
							$activity = $this->getActivityTable()->getOneActivityWithMembercountWithoutPlanetid($identity->user_id,$event_id);
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
							$admin_status = $this->getGroupTable()->getAdminStatus($activity->group_activity_group_id,$identity->user_id);
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($activity->group_activity_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}	 
							if(!empty($activity)){
								$activity_details = array(
													"group_activity_title" => $activity->group_activity_title,
													"group_activity_location" => $activity->group_activity_location,
													"group_activity_content" => $activity->group_activity_content,
													"group_activity_start_timestamp" => $activity->group_activity_start_timestamp,
													"group_activity_owner_user_id" => $activity->group_activity_owner_user_id,
													"group_activity_id" => $activity->group_activity_id,
													"group_activity_type" => $activity->group_activity_type,
													"group_activity_status" => $activity->group_activity_status,
													"is_member" => $activity->is_member,
													"user_given_name" => $activity->user_given_name,
													"user_id" => $activity->user_id,
													"user_profile_name" => $activity->user_profile_name,
													"user_register_type" => $activity->user_register_type,
													"user_fbid" => $activity->user_fbid,
													"profile_photo" => $activity->profile_photo,
													"member_count" => $activity->member_count,
													"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id),
													"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activity->group_activity_id)->comment_count,
													'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id,2,0),
													'members' =>$this->getActivityRsvpTable()->getJoinMembersWithFriendshipStatus($activity->group_activity_id,$identity->user_id,25,0),
													"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($activity->group_activity_group_id,$identity->user_id),
													'is_admin'=>$is_admin,
													);
								$array_news[] = array('content' => $activity_details,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
												); 
							}
						break;
						case "New Discussion":
							$event_id = $news['event_id'];
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Discussion');
							$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
							$discussion = $this->discussionTable->getOneDiscussionWithOwnerInfo($event_id);
							$admin_status = $this->getGroupTable()->getAdminStatus($discussion->group_discussion_group_id,$identity->user_id);
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($discussion->group_discussion_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}	
							if(!empty($discussion)){
								$arr_descussion = array();							 
								$arr_descussion = array(
												'group_discussion_content' =>$discussion->group_discussion_content,
												'group_discussion_id' =>$discussion->group_discussion_id,
												'group_discussion_owner_user_id'=>$discussion->group_discussion_id,
												'user_given_name' =>$discussion->user_given_name,
												'user_id' =>$discussion->user_id,
												'user_profile_name' =>$discussion->user_profile_name,
												'user_register_type' =>$discussion->user_register_type,
												'user_fbid' =>$discussion->user_fbid,
												'profile_photo' =>$discussion->profile_photo,
												"descussion_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id),
												"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$discussion->group_discussion_id)->comment_count,
												'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id,2,0),
												"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($discussion->group_discussion_group_id,$identity->user_id),
												'is_admin'=>$is_admin,
												);								
								$array_news[] = array('content' => $arr_descussion,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
												); 
							}
						break;
						case "New Activity Member":
							$event_id = $news['event_id'];
							$this->activityRsvpTable = $sm->get('Activity\Model\ActivityRsvpTable');
							$rsvp_details = $this->activityRsvpTable->GetActivityRsvpWithActivityDetails($event_id);
							$arr_rsvp = array();
							$arr_rsvp = array(
										'activity_id' =>$rsvp_details->group_activity_id,
										'group_activity_title'=>$rsvp_details->group_activity_title,
										'user_id'=>$rsvp_details->user_id,
										'user_given_name'=>$rsvp_details->user_given_name,
										'user_profile_name'=>$rsvp_details->user_profile_name,
										'profile_photo'=>$rsvp_details->profile_photo,
										'user_register_type' =>$rsvp_details->user_register_type,
										'user_fbid' =>$rsvp_details->user_fbid,
										'activity_group'=>$this->getGroupTable()->getSubgroupWithParentSeo($rsvp_details->group_activity_group_id),
										);
							$array_news[] = array('content' => $arr_rsvp,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
											);
						break;
						case "Activity Like":
							$event_id = $news['event_id'];
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
							$like_details = $this->getLikeTable()->getLike($event_id);
							$activity = $this->getActivityTable()->getOneActivityWithMembercountWithoutPlanetid($identity->user_id,$like_details->like_refer_id);
							$admin_status = $this->getGroupTable()->getAdminStatus($activity->group_activity_group_id,$identity->user_id);
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($activity->group_activity_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}
							$user_info = $this->getUserTable()->getUserWithProfilePic($like_details->like_by_user_id);							
							if(!empty($activity)){
								$activity_details = array(
													"group_activity_title" => $activity->group_activity_title,
													"group_activity_location" => $activity->group_activity_location,
													"group_activity_content" => $activity->group_activity_content,
													"group_activity_start_timestamp" => $activity->group_activity_start_timestamp,
													"group_activity_owner_user_id" => $activity->group_activity_owner_user_id,
													"group_activity_id" => $activity->group_activity_id,
													"group_activity_type" => $activity->group_activity_type,
													"group_activity_status" => $activity->group_activity_status,
													"is_member" => $activity->is_member,
													"user_given_name" => $user_info->user_given_name,
													"user_id" => $user_info->user_id,
													"user_profile_name" => $user_info->user_profile_name,
													"user_register_type" => $user_info->user_register_type,
													"user_fbid" => $user_info->user_fbid,
													"profile_photo" => $user_info->profile_photo,
													"member_count" => $activity->member_count,
													"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id),
													"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activity->group_activity_id)->comment_count,
													'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id,2,0),
													'members' =>$this->getActivityRsvpTable()->getJoinMembersWithFriendshipStatus($activity->group_activity_id,$identity->user_id,25,0),
													"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($activity->group_activity_group_id,$identity->user_id),
													'is_admin'=>$is_admin,
													);
								$array_news[] = array('content' => $activity_details,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
												); 
							}
						break;
						case "Activity Comment":
							$event_id = $news['event_id'];
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
							$comment_details = $this->getCommentTable()->getComment($event_id);
							$activity = $this->getActivityTable()->getOneActivityWithMembercountWithoutPlanetid($identity->user_id,$comment_details->comment_refer_id);
							$admin_status = $this->getGroupTable()->getAdminStatus($activity->group_activity_group_id,$identity->user_id);
							$is_admin = 0;
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($activity->group_activity_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}
							$user_info = $this->getUserTable()->getUserWithProfilePic($comment_details->comment_by_user_id);							
							if(!empty($activity)){
								$activity_details = array(
													"group_activity_title" => $activity->group_activity_title,
													"group_activity_location" => $activity->group_activity_location,
													"group_activity_content" => $activity->group_activity_content,
													"group_activity_start_timestamp" => $activity->group_activity_start_timestamp,
													"group_activity_owner_user_id" => $activity->group_activity_owner_user_id,
													"group_activity_id" => $activity->group_activity_id,
													"group_activity_type" => $activity->group_activity_type,
													"group_activity_status" => $activity->group_activity_status,
													"is_member" => $activity->is_member,
													"user_given_name" => $user_info->user_given_name,
													"user_id" => $user_info->user_id,
													"user_profile_name" => $user_info->user_profile_name,
													"user_register_type" => $user_info->user_register_type,
													"user_fbid" => $user_info->user_fbid,
													"profile_photo" => $user_info->profile_photo,
													"member_count" => $activity->member_count,
													"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id),
													"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activity->group_activity_id)->comment_count,
													'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id,2,0),
													'members' =>$this->getActivityRsvpTable()->getJoinMembersWithFriendshipStatus($activity->group_activity_id,$identity->user_id,25,0),
													"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($activity->group_activity_group_id,$identity->user_id),
													'is_admin'=>$is_admin,
													);
								$array_news[] = array('content' => $activity_details,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
												); 
							}
						break;
						case "Discussion Like":
							$event_id = $news['event_id'];
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Discussion');
							$like_details = $this->getLikeTable()->getLike($event_id);
							$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
							$discussion = $this->discussionTable->getOneDiscussionWithOwnerInfo($like_details->like_refer_id);
							$admin_status = $this->getGroupTable()->getAdminStatus($discussion->group_discussion_group_id,$identity->user_id);
							$is_admin = 0;
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($discussion->group_discussion_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}
							$user_info = $this->getUserTable()->getUserWithProfilePic($like_details->like_by_user_id);							
							if(!empty($discussion)){
								$arr_descussion = array();							 
								$arr_descussion = array(
												'group_discussion_content' =>$discussion->group_discussion_content,
												'group_discussion_id' =>$discussion->group_discussion_id,
												'group_discussion_owner_user_id'=>$discussion->group_discussion_id,
												'user_given_name' =>$discussion->user_given_name,
												'user_id' =>$discussion->user_id,
												'user_profile_name' =>$discussion->user_profile_name,
												'user_register_type' =>$discussion->user_register_type,
												'user_fbid' =>$discussion->user_fbid,
												'profile_photo' =>$discussion->profile_photo,
												"descussion_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id),
												"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$discussion->group_discussion_id)->comment_count,
												'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id,2,0),
												"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($discussion->group_discussion_group_id,$identity->user_id),
												'is_admin'=>$is_admin,
												);								
								$array_news[] = array('content' => $arr_descussion,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
												); 
							}
						break;
						case "Discussion Comment":
							$event_id = $news['event_id'];
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Discussion');
							$comment_details = $this->getCommentTable()->getComment($event_id);
							$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
							$discussion = $this->discussionTable->getOneDiscussionWithOwnerInfo($comment_details->comment_refer_id);
							$admin_status = $this->getGroupTable()->getAdminStatus($discussion->group_discussion_group_id,$identity->user_id);
							$is_admin = 0;
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($discussion->group_discussion_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}
							$user_info = $this->getUserTable()->getUserWithProfilePic($comment_details->comment_by_user_id);							
							if(!empty($discussion)){
								$arr_descussion = array();							 
								$arr_descussion = array(
												'group_discussion_content' =>$discussion->group_discussion_content,
												'group_discussion_id' =>$discussion->group_discussion_id,
												'group_discussion_owner_user_id'=>$discussion->group_discussion_id,
												'user_given_name' =>$discussion->user_given_name,
												'user_id' =>$discussion->user_id,
												'user_profile_name' =>$discussion->user_profile_name,
												'user_register_type' =>$discussion->user_register_type,
												'user_fbid' =>$discussion->user_fbid,
												'profile_photo' =>$discussion->profile_photo,
												"descussion_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id),
												"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$discussion->group_discussion_id)->comment_count,
												'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id,2,0),
												"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($discussion->group_discussion_group_id,$identity->user_id),
												'is_admin'=>$is_admin,
												);								
								$array_news[] = array('content' => $arr_descussion,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
												); 
							}
						break;
						case "New Group Members":
							$event_id = $news['event_id'];
							$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
							$userGroup_details = $this->userGroupTable->GetUserGroupWithGroupDetails($event_id);
							$arr_rsvp = array();
							$arr_rsvp = array(
										'group_id' =>$userGroup_details->group_id,
										'group_title'=>$userGroup_details->group_title,
										'group_seo_title'=>$userGroup_details->group_seo_title,
										'parent_seo_title'=>$userGroup_details->parent_seo_title,
										'user_id'=>$userGroup_details->user_id,
										'user_given_name'=>$userGroup_details->user_given_name,
										'user_profile_name'=>$userGroup_details->user_profile_name,
										'profile_photo'=>$userGroup_details->profile_photo,
										'user_register_type' =>$userGroup_details->user_register_type,
										'user_fbid' =>$userGroup_details->user_fbid,
										 
										);
							$array_news[] = array('content' => $arr_rsvp,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
											);
						break;
						case "New Group Albums":
							$event_id = $news['event_id'];
							$album_details = $this->getAlbumTable()->getalbumrow($event_id);
							$user_info = $this->getUserTable()->getUserWithProfilePic($album_details->album_user_id);
							$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
							$arr_rsvp = array(
										'group_id' =>$group_info->group_id,
										'group_title'=>$group_info->group_title,
										'group_seo_title'=>$group_info->group_seo_title,
										'parent_seo_title'=>$group_info->parent_seo_title,
										'user_id'=>$user_info->user_id,
										'user_given_name'=>$user_info->user_given_name,
										'user_profile_name'=>$user_info->user_profile_name,
										'profile_photo'=>$user_info->profile_photo,
										'user_register_type' =>$user_info->user_register_type,
										'user_fbid' =>$user_info->user_fbid,
										'album_id' =>$album_details->album_id,
										'album_title' =>$album_details->album_title,
										'album_seotitle' =>$album_details->album_seotitle,
										);
							$array_news[] = array('content' => $arr_rsvp,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
											);							
						break;
						case "New Group Album Pictures":
							$event_id = $news['event_id'];
							$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
							$album_data_details = $this->albumDataTable->getalbumdata($event_id);
							$album_details = $this->getAlbumTable()->getalbumrow($album_data_details->parent_album_id);
							$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
							$user_info = $this->getUserTable()->getUserWithProfilePic($album_data_details->added_user_id);
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
							$is_admin = 0;
							$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}
							$arr_photo = array(
										'group_id' =>$group_info->group_id,
										'group_title'=>$group_info->group_title,
										'group_seo_title'=>$group_info->group_seo_title,
										'parent_seo_title'=>$group_info->parent_seo_title,
										'user_id'=>$user_info->user_id,
										'user_given_name'=>$user_info->user_given_name,
										'user_profile_name'=>$user_info->user_profile_name,
										'profile_photo'=>$user_info->profile_photo,
										'user_register_type' =>$user_info->user_register_type,
										'user_fbid' =>$user_info->user_fbid,
										'album_id' =>$album_details->album_id,
										'album_title' =>$album_details->album_title,
										'album_seotitle' =>$album_details->album_seotitle,
										'album_user_id'=>$album_details->album_user_id,
										'data_id'=>$album_data_details->data_id,
										'parent_album_id'=>$album_data_details->parent_album_id,
										'added_user_id'=>$album_data_details->added_user_id,
										'data_type'=>$album_data_details->data_type,
										'data_content'=>$album_data_details->data_content,
										'album_location'=>$album_details->album_location,
										'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
										"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
										'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
										'is_admin'=>$is_admin,
										);
							$array_news[] = array('content' => $arr_photo,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
											);							
						break;
						case "All Tagged Pictures":
							$event_id = $news['event_id'];
							$this->albumTagTable = $sm->get('Album\Model\AlbumTagTable');
							$album_tag_details = $this->albumTagTable->getTagDetails($event_id);
							$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
							$album_data_details = $this->albumDataTable->getalbumdata($album_tag_details->album_tag_data_id);
							$album_details = $this->getAlbumTable()->getalbumrow($album_data_details->parent_album_id);
							$user_info = $this->getUserTable()->getUserWithProfilePic($album_tag_details->album_tag_added_user);
							$group_info = array();
							$album_type = '';
							$is_admin = 0;
							if($album_details->album_group_id){
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
								$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
								$album_type = 'Media';
								
								$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
								if($admin_status->is_admin){
									$is_admin = 1;
								}
								$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
								if(!empty($user_role)){
									$is_admin = 1;
								}
							}else{
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');
								$album_type = 'Userfiles';
							}
							$arr_photo = array(
										'group_info' =>$group_info,
										'album_type' => $album_type,
										'user_id'=>$user_info->user_id,
										'user_given_name'=>$user_info->user_given_name,
										'user_profile_name'=>$user_info->user_profile_name,
										'profile_photo'=>$user_info->profile_photo,
										'user_register_type' =>$user_info->user_register_type,
										'user_fbid' =>$user_info->user_fbid,
										'album_id' =>$album_details->album_id,
										'album_title' =>$album_details->album_title,
										'album_seotitle' =>$album_details->album_seotitle,
										'album_user_id'=>$album_details->album_user_id,
										'data_id'=>$album_data_details->data_id,
										'parent_album_id'=>$album_data_details->parent_album_id,
										'added_user_id'=>$album_data_details->added_user_id,
										'data_type'=>$album_data_details->data_type,
										'data_content'=>$album_data_details->data_content,
										'album_location'=>$album_details->album_location,
										'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
										"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
										'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
										'is_admin'=>$is_admin,
										);
							$array_news[] = array('content' => $arr_photo,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
											);
						break;
						case "All Picture Like":
							$event_id = $news['event_id'];							
							$like_details = $this->getLikeTable()->getLike($event_id);
							$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
							$album_data_details = $this->albumDataTable->getalbumdata($like_details->like_refer_id);
							$album_details = $this->getAlbumTable()->getalbumrow($album_data_details->parent_album_id);
							$user_info = $this->getUserTable()->getUserWithProfilePic($like_details->like_by_user_id);
							$group_info = array();
							$album_type = '';
							$is_admin = 0;
							if($album_details->album_group_id){
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
								$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
								$album_type = 'Media';
								$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
								
								if($admin_status->is_admin){
									$is_admin = 1;
								}
								$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
								if(!empty($user_role)){
									$is_admin = 1;
								}
							}else{
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');
								$album_type = 'Userfiles';
							}
							$arr_photo = array(
										'group_info' =>$group_info,
										'album_type' => $album_type,
										'user_id'=>$user_info->user_id,
										'user_given_name'=>$user_info->user_given_name,
										'user_profile_name'=>$user_info->user_profile_name,
										'profile_photo'=>$user_info->profile_photo,
										'user_register_type' =>$user_info->user_register_type,
										'user_fbid' =>$user_info->user_fbid,
										'album_id' =>$album_details->album_id,
										'album_title' =>$album_details->album_title,
										'album_seotitle' =>$album_details->album_seotitle,
										'album_user_id'=>$album_details->album_user_id,
										'data_id'=>$album_data_details->data_id,
										'parent_album_id'=>$album_data_details->parent_album_id,
										'added_user_id'=>$album_data_details->added_user_id,
										'data_type'=>$album_data_details->data_type,
										'data_content'=>$album_data_details->data_content,
										'album_location'=>$album_details->album_location,
										'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
										"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
										'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
										'is_admin'=>$is_admin,
										);
							$array_news[] = array('content' => $arr_photo,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
											);
						break;
						case "All Picture Comments":
							$event_id = $news['event_id'];							
							$comment_details = $this->getCommentTable()->getComment($event_id);
							$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
							$album_data_details = $this->albumDataTable->getalbumdata($comment_details->comment_refer_id);
							$album_details = $this->getAlbumTable()->getalbumrow($album_data_details->parent_album_id);
							$user_info = $this->getUserTable()->getUserWithProfilePic($comment_details->comment_by_user_id);
							$group_info = array();
							$album_type = '';
							$is_admin = 0;
							if($album_details->album_group_id){
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
								$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
								$album_type = 'Media';
								$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
								if($admin_status->is_admin){
									$is_admin = 1;
								}
								$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
								if(!empty($user_role)){
									$is_admin = 1;
								}
							}else{
								$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');
								$album_type = 'Userfiles';
							}
							$arr_photo = array(
										'group_info' =>$group_info,
										'album_type' => $album_type,
										'user_id'=>$user_info->user_id,
										'user_given_name'=>$user_info->user_given_name,
										'user_profile_name'=>$user_info->user_profile_name,
										'profile_photo'=>$user_info->profile_photo,
										'user_register_type' =>$user_info->user_register_type,
										'user_fbid' =>$user_info->user_fbid,
										'album_id' =>$album_details->album_id,
										'album_title' =>$album_details->album_title,
										'album_seotitle' =>$album_details->album_seotitle,
										'album_user_id'=>$album_details->album_user_id,
										'data_id'=>$album_data_details->data_id,
										'parent_album_id'=>$album_data_details->parent_album_id,
										'added_user_id'=>$album_data_details->added_user_id,
										'data_type'=>$album_data_details->data_type,
										'data_content'=>$album_data_details->data_content,
										'album_location'=>$album_details->album_location,
										'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
										"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
										'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
										'is_admin'=>$is_admin,
										);
							$array_news[] = array('content' => $arr_photo,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
											);
						break;
						case "New Friendship":
							$event_id = $news['event_id'];	
							$this->userFrontTable = $sm->get('User\Model\UserFriendTable');
							$userFrontDetails = $this->userFrontTable->getUserFriend($event_id);
							$friend = ($userFrontDetails->user_friend_sender_user_id == $identity->user_id)?$userFrontDetails->user_friend_friend_user_id:$userFrontDetails->user_friend_sender_user_id;
							$user_info = $this->getUserTable()->getUserWithProfilePic($friend);
							$arr_friend = array(										 
										'user_id'=>$user_info->user_id,
										'user_given_name'=>$user_info->user_given_name,
										'user_profile_name'=>$user_info->user_profile_name,
										'profile_photo'=>$user_info->profile_photo,
										'user_register_type' =>$user_info->user_register_type,
										'user_fbid' =>$user_info->user_fbid,
										 
										);
							$array_news[] = array('content' => $arr_friend,
													'type'=>$news['type'],
													'time'=>$news['update_time'],
											);
						break;
					}
				}
				$viewModel->setVariable('feedSummery', $array_news);
			}else{
				return $this->redirect()->toRoute('home', array('action' => 'index'));
			}
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
		return $viewModel;
	}
	public function morefeedsAction(){
		$sm = $this->getServiceLocator();
		$auth = new AuthenticationService();
		$identity = null;
		$viewModel = new ViewModel();	 
		$error = array();		 
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			 
			$this->layout()->identity = $identity;
			$request = $this->getRequest();
			$page = 0;
			if ($request->isPost()) {
				$post = $request->getPost();
				$page = $post['page'];
			}
			$viewModel->setVariable('page', $page);
			$offset = $page*10;
			$this->userTable = $sm->get('User\Model\UserTable');
			$feedSummery = $this->userTable->getNewsSummery($identity->user_id,$offset,10);
			$array_news = array();
			foreach($feedSummery as $news){
				switch($news['type']){
					case "New Activity":
						$event_id = $news['event_id'];
						$activity = $this->getActivityTable()->getOneActivityWithMembercountWithoutPlanetid($identity->user_id,$event_id);
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
						$admin_status = $this->getGroupTable()->getAdminStatus($activity->group_activity_group_id,$identity->user_id);
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($activity->group_activity_group_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}	 
						if(!empty($activity)){
							$activity_details = array(
												"group_activity_title" => $activity->group_activity_title,
												"group_activity_location" => $activity->group_activity_location,
												"group_activity_content" => $activity->group_activity_content,
												"group_activity_start_timestamp" => $activity->group_activity_start_timestamp,
												"group_activity_owner_user_id" => $activity->group_activity_owner_user_id,
												"group_activity_id" => $activity->group_activity_id,
												"group_activity_type" => $activity->group_activity_type,
												"group_activity_status" => $activity->group_activity_status,
												"is_member" => $activity->is_member,
												"user_given_name" => $activity->user_given_name,
												"user_id" => $activity->user_id,
												"user_profile_name" => $activity->user_profile_name,
												"user_register_type" => $activity->user_register_type,
												"user_fbid" => $activity->user_fbid,
												"profile_photo" => $activity->profile_photo,
												"member_count" => $activity->member_count,
												"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id),
												"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activity->group_activity_id)->comment_count,
												'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id,2,0),
												'members' =>$this->getActivityRsvpTable()->getJoinMembersWithFriendshipStatus($activity->group_activity_id,$identity->user_id,25,0),
												"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($activity->group_activity_group_id,$identity->user_id),
												'is_admin'=>$is_admin,
												);
							$array_news[] = array('content' => $activity_details,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
											); 
						}
					break;
					case "New Discussion":
						$event_id = $news['event_id'];
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Discussion');
						$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
						$discussion = $this->discussionTable->getOneDiscussionWithOwnerInfo($event_id);
						$admin_status = $this->getGroupTable()->getAdminStatus($discussion->group_discussion_group_id,$identity->user_id);
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($discussion->group_discussion_group_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}	
						if(!empty($discussion)){
							$arr_descussion = array();							 
							$arr_descussion = array(
											'group_discussion_content' =>$discussion->group_discussion_content,
											'group_discussion_id' =>$discussion->group_discussion_id,
											'group_discussion_owner_user_id'=>$discussion->group_discussion_id,
											'user_given_name' =>$discussion->user_given_name,
											'user_id' =>$discussion->user_id,
											'user_profile_name' =>$discussion->user_profile_name,
											'user_register_type' =>$discussion->user_register_type,
											'user_fbid' =>$discussion->user_fbid,
											'profile_photo' =>$discussion->profile_photo,
											"descussion_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id),
											"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$discussion->group_discussion_id)->comment_count,
											'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id,2,0),
											"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($discussion->group_discussion_group_id,$identity->user_id),
											'is_admin'=>$is_admin,
											);								
							$array_news[] = array('content' => $arr_descussion,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
											); 
						}
					break;
					case "New Activity Member":
						$event_id = $news['event_id'];
						$this->activityRsvpTable = $sm->get('Activity\Model\ActivityRsvpTable');
						$rsvp_details = $this->activityRsvpTable->GetActivityRsvpWithActivityDetails($event_id);
						$arr_rsvp = array();
						$arr_rsvp = array(
									'activity_id' =>$rsvp_details->group_activity_id,
									'group_activity_title'=>$rsvp_details->group_activity_title,
									'user_id'=>$rsvp_details->user_id,
									'user_given_name'=>$rsvp_details->user_given_name,
									'user_profile_name'=>$rsvp_details->user_profile_name,
									'profile_photo'=>$rsvp_details->profile_photo,
									'user_register_type' =>$rsvp_details->user_register_type,
									'user_fbid' =>$rsvp_details->user_fbid,
									'activity_group'=>$this->getGroupTable()->getSubgroupWithParentSeo($rsvp_details->group_activity_group_id),
									);
						$array_news[] = array('content' => $arr_rsvp,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
										);
					break;
					case "Activity Like":
						$event_id = $news['event_id'];
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
						$like_details = $this->getLikeTable()->getLike($event_id);
						$activity = $this->getActivityTable()->getOneActivityWithMembercountWithoutPlanetid($identity->user_id,$like_details->like_refer_id);
						$admin_status = $this->getGroupTable()->getAdminStatus($activity->group_activity_group_id,$identity->user_id);
						$is_admin = 0;
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($activity->group_activity_group_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}
						$user_info = $this->getUserTable()->getUserWithProfilePic($like_details->like_by_user_id);							
						if(!empty($activity)){
							$activity_details = array(
												"group_activity_title" => $activity->group_activity_title,
												"group_activity_location" => $activity->group_activity_location,
												"group_activity_content" => $activity->group_activity_content,
												"group_activity_start_timestamp" => $activity->group_activity_start_timestamp,
												"group_activity_owner_user_id" => $activity->group_activity_owner_user_id,
												"group_activity_id" => $activity->group_activity_id,
												"group_activity_type" => $activity->group_activity_type,
												"group_activity_status" => $activity->group_activity_status,
												"is_member" => $activity->is_member,
												"user_given_name" => $user_info->user_given_name,
												"user_id" => $user_info->user_id,
												"user_profile_name" => $user_info->user_profile_name,
												"user_register_type" => $user_info->user_register_type,
												"user_fbid" => $user_info->user_fbid,
												"profile_photo" => $user_info->profile_photo,
												"member_count" => $activity->member_count,
												"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id),
												"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activity->group_activity_id)->comment_count,
												'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id,2,0),
												'members' =>$this->getActivityRsvpTable()->getJoinMembersWithFriendshipStatus($activity->group_activity_id,$identity->user_id,25,0),
												"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($activity->group_activity_group_id,$identity->user_id),
												'is_admin'=>$is_admin,
												);
							$array_news[] = array('content' => $activity_details,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
											); 
						}
					break;
					case "Activity Comment":
						$event_id = $news['event_id'];
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Activity');
						$comment_details = $this->getCommentTable()->getComment($event_id);
						$activity = $this->getActivityTable()->getOneActivityWithMembercountWithoutPlanetid($identity->user_id,$comment_details->comment_refer_id);
						$admin_status = $this->getGroupTable()->getAdminStatus($activity->group_activity_group_id,$identity->user_id);
						$is_admin = 0;
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($activity->group_activity_group_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}
						$user_info = $this->getUserTable()->getUserWithProfilePic($comment_details->comment_by_user_id);							
						if(!empty($activity)){
							$activity_details = array(
												"group_activity_title" => $activity->group_activity_title,
												"group_activity_location" => $activity->group_activity_location,
												"group_activity_content" => $activity->group_activity_content,
												"group_activity_start_timestamp" => $activity->group_activity_start_timestamp,
												"group_activity_owner_user_id" => $activity->group_activity_owner_user_id,
												"group_activity_id" => $activity->group_activity_id,
												"group_activity_type" => $activity->group_activity_type,
												"group_activity_status" => $activity->group_activity_status,
												"is_member" => $activity->is_member,
												"user_given_name" => $user_info->user_given_name,
												"user_id" => $user_info->user_id,
												"user_profile_name" => $user_info->user_profile_name,
												"user_register_type" => $user_info->user_register_type,
												"user_fbid" => $user_info->user_fbid,
												"profile_photo" => $user_info->profile_photo,
												"member_count" => $activity->member_count,
												"activity_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id),
												"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$activity->group_activity_id)->comment_count,
												'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$activity->group_activity_id,$identity->user_id,2,0),
												'members' =>$this->getActivityRsvpTable()->getJoinMembersWithFriendshipStatus($activity->group_activity_id,$identity->user_id,25,0),
												"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($activity->group_activity_group_id,$identity->user_id),
												'is_admin'=>$is_admin,
												);
							$array_news[] = array('content' => $activity_details,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
											); 
						}
					break;
					case "Discussion Like":
						$event_id = $news['event_id'];
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Discussion');
						$like_details = $this->getLikeTable()->getLike($event_id);
						$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
						$discussion = $this->discussionTable->getOneDiscussionWithOwnerInfo($like_details->like_refer_id);
						$admin_status = $this->getGroupTable()->getAdminStatus($discussion->group_discussion_group_id,$identity->user_id);
						$is_admin = 0;
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($discussion->group_discussion_group_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}
						$user_info = $this->getUserTable()->getUserWithProfilePic($like_details->like_by_user_id);							
						if(!empty($discussion)){
							$arr_descussion = array();							 
							$arr_descussion = array(
											'group_discussion_content' =>$discussion->group_discussion_content,
											'group_discussion_id' =>$discussion->group_discussion_id,
											'group_discussion_owner_user_id'=>$discussion->group_discussion_id,
											'user_given_name' =>$discussion->user_given_name,
											'user_id' =>$discussion->user_id,
											'user_profile_name' =>$discussion->user_profile_name,
											'user_register_type' =>$discussion->user_register_type,
											'user_fbid' =>$discussion->user_fbid,
											'profile_photo' =>$discussion->profile_photo,
											"descussion_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id),
											"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$discussion->group_discussion_id)->comment_count,
											'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id,2,0),
											"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($discussion->group_discussion_group_id,$identity->user_id),
											'is_admin'=>$is_admin,
											);								
							$array_news[] = array('content' => $arr_descussion,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
											); 
						}
					break;
					case "Discussion Comment":
						$event_id = $news['event_id'];
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Discussion');
						$comment_details = $this->getCommentTable()->getComment($event_id);
						$this->discussionTable = $sm->get('Discussion\Model\DiscussionTable');
						$discussion = $this->discussionTable->getOneDiscussionWithOwnerInfo($comment_details->comment_refer_id);
						$admin_status = $this->getGroupTable()->getAdminStatus($discussion->group_discussion_group_id,$identity->user_id);
						$is_admin = 0;
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($discussion->group_discussion_group_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}
						$user_info = $this->getUserTable()->getUserWithProfilePic($comment_details->comment_by_user_id);							
						if(!empty($discussion)){
							$arr_descussion = array();							 
							$arr_descussion = array(
											'group_discussion_content' =>$discussion->group_discussion_content,
											'group_discussion_id' =>$discussion->group_discussion_id,
											'group_discussion_owner_user_id'=>$discussion->group_discussion_id,
											'user_given_name' =>$discussion->user_given_name,
											'user_id' =>$discussion->user_id,
											'user_profile_name' =>$discussion->user_profile_name,
											'user_register_type' =>$discussion->user_register_type,
											'user_fbid' =>$discussion->user_fbid,
											'profile_photo' =>$discussion->profile_photo,
											"descussion_like" => $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id),
											"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$discussion->group_discussion_id)->comment_count,
											'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$discussion->group_discussion_id,$identity->user_id,2,0),
											"group_info" => $this->getGroupTable()->getPlanetDetailsForPalnetView($discussion->group_discussion_group_id,$identity->user_id),
											'is_admin'=>$is_admin,
											);								
							$array_news[] = array('content' => $arr_descussion,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
											); 
						}
					break;
					case "New Group Members":
						$event_id = $news['event_id'];
						$this->userGroupTable = $sm->get('Groups\Model\UserGroupTable');
						$userGroup_details = $this->userGroupTable->GetUserGroupWithGroupDetails($event_id);
						$arr_rsvp = array();
						$arr_rsvp = array(
									'group_id' =>$userGroup_details->group_id,
									'group_title'=>$userGroup_details->group_title,
									'group_seo_title'=>$userGroup_details->group_seo_title,
									'parent_seo_title'=>$userGroup_details->parent_seo_title,
									'user_id'=>$userGroup_details->user_id,
									'user_given_name'=>$userGroup_details->user_given_name,
									'user_profile_name'=>$userGroup_details->user_profile_name,
									'profile_photo'=>$userGroup_details->profile_photo,
									'user_register_type' =>$userGroup_details->user_register_type,
									'user_fbid' =>$userGroup_details->user_fbid,
									 
									);
						$array_news[] = array('content' => $arr_rsvp,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
										);
					break;
					case "New Group Albums":
						$event_id = $news['event_id'];
						$album_details = $this->getAlbumTable()->getalbumrow($event_id);
						$user_info = $this->getUserTable()->getUserWithProfilePic($album_details->album_user_id);
						$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
						$arr_rsvp = array(
									'group_id' =>$group_info->group_id,
									'group_title'=>$group_info->group_title,
									'group_seo_title'=>$group_info->group_seo_title,
									'parent_seo_title'=>$group_info->parent_seo_title,
									'user_id'=>$user_info->user_id,
									'user_given_name'=>$user_info->user_given_name,
									'user_profile_name'=>$user_info->user_profile_name,
									'profile_photo'=>$user_info->profile_photo,
									'user_register_type' =>$user_info->user_register_type,
									'user_fbid' =>$user_info->user_fbid,
									'album_id' =>$album_details->album_id,
									'album_title' =>$album_details->album_title,
									'album_seotitle' =>$album_details->album_seotitle,
									);
						$array_news[] = array('content' => $arr_rsvp,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
										);							
					break;
					case "New Group Album Pictures":
						$event_id = $news['event_id'];
						$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
						$album_data_details = $this->albumDataTable->getalbumdata($event_id);
						$album_details = $this->getAlbumTable()->getalbumrow($album_data_details->parent_album_id);
						$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
						$user_info = $this->getUserTable()->getUserWithProfilePic($album_data_details->added_user_id);
						$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
						$is_admin = 0;
						$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
						if($admin_status->is_admin){
							$is_admin = 1;
						}
						$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
						if(!empty($user_role)){
							$is_admin = 1;
						}
						$arr_photo = array(
									'group_id' =>$group_info->group_id,
									'group_title'=>$group_info->group_title,
									'group_seo_title'=>$group_info->group_seo_title,
									'parent_seo_title'=>$group_info->parent_seo_title,
									'user_id'=>$user_info->user_id,
									'user_given_name'=>$user_info->user_given_name,
									'user_profile_name'=>$user_info->user_profile_name,
									'profile_photo'=>$user_info->profile_photo,
									'user_register_type' =>$user_info->user_register_type,
									'user_fbid' =>$user_info->user_fbid,
									'album_id' =>$album_details->album_id,
									'album_title' =>$album_details->album_title,
									'album_seotitle' =>$album_details->album_seotitle,
									'album_user_id'=>$album_details->album_user_id,
									'data_id'=>$album_data_details->data_id,
									'parent_album_id'=>$album_data_details->parent_album_id,
									'added_user_id'=>$album_data_details->added_user_id,
									'data_type'=>$album_data_details->data_type,
									'data_content'=>$album_data_details->data_content,
									'album_location'=>$album_details->album_location,
									'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
									"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
									'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
									'is_admin'=>$is_admin,
									);
						$array_news[] = array('content' => $arr_photo,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
										);							
					break;
					case "All Tagged Pictures":
						$event_id = $news['event_id'];
						$this->albumTagTable = $sm->get('Album\Model\AlbumTagTable');
						$album_tag_details = $this->albumTagTable->getTagDetails($event_id);
						$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
						$album_data_details = $this->albumDataTable->getalbumdata($album_tag_details->album_tag_data_id);
						$album_details = $this->getAlbumTable()->getalbumrow($album_data_details->parent_album_id);
						$user_info = $this->getUserTable()->getUserWithProfilePic($album_tag_details->album_tag_added_user);
						$group_info = array();
						$album_type = '';
						$is_admin = 0;
						if($album_details->album_group_id){
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
							$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
							$album_type = 'Media';
							
							$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}
						}else{
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');
							$album_type = 'Userfiles';
						}
						$arr_photo = array(
									'group_info' =>$group_info,
									'album_type' => $album_type,
									'user_id'=>$user_info->user_id,
									'user_given_name'=>$user_info->user_given_name,
									'user_profile_name'=>$user_info->user_profile_name,
									'profile_photo'=>$user_info->profile_photo,
									'user_register_type' =>$user_info->user_register_type,
									'user_fbid' =>$user_info->user_fbid,
									'album_id' =>$album_details->album_id,
									'album_title' =>$album_details->album_title,
									'album_seotitle' =>$album_details->album_seotitle,
									'album_user_id'=>$album_details->album_user_id,
									'data_id'=>$album_data_details->data_id,
									'parent_album_id'=>$album_data_details->parent_album_id,
									'added_user_id'=>$album_data_details->added_user_id,
									'data_type'=>$album_data_details->data_type,
									'data_content'=>$album_data_details->data_content,
									'album_location'=>$album_details->album_location,
									'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
									"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
									'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
									'is_admin'=>$is_admin,
									);
						$array_news[] = array('content' => $arr_photo,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
										);
					break;
					case "All Picture Like":
						$event_id = $news['event_id'];							
						$like_details = $this->getLikeTable()->getLike($event_id);
						$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
						$album_data_details = $this->albumDataTable->getalbumdata($like_details->like_refer_id);
						$album_details = $this->getAlbumTable()->getalbumrow($album_data_details->parent_album_id);
						$user_info = $this->getUserTable()->getUserWithProfilePic($like_details->like_by_user_id);
						$group_info = array();
						$album_type = '';
						$is_admin = 0;
						if($album_details->album_group_id){
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
							$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
							$album_type = 'Media';
							$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
							
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}
						}else{
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');
							$album_type = 'Userfiles';
						}
						$arr_photo = array(
									'group_info' =>$group_info,
									'album_type' => $album_type,
									'user_id'=>$user_info->user_id,
									'user_given_name'=>$user_info->user_given_name,
									'user_profile_name'=>$user_info->user_profile_name,
									'profile_photo'=>$user_info->profile_photo,
									'user_register_type' =>$user_info->user_register_type,
									'user_fbid' =>$user_info->user_fbid,
									'album_id' =>$album_details->album_id,
									'album_title' =>$album_details->album_title,
									'album_seotitle' =>$album_details->album_seotitle,
									'album_user_id'=>$album_details->album_user_id,
									'data_id'=>$album_data_details->data_id,
									'parent_album_id'=>$album_data_details->parent_album_id,
									'added_user_id'=>$album_data_details->added_user_id,
									'data_type'=>$album_data_details->data_type,
									'data_content'=>$album_data_details->data_content,
									'album_location'=>$album_details->album_location,
									'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
									"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
									'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
									'is_admin'=>$is_admin,
									);
						$array_news[] = array('content' => $arr_photo,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
										);
					break;
					case "All Picture Comments":
						$event_id = $news['event_id'];							
						$comment_details = $this->getCommentTable()->getComment($event_id);
						$this->albumDataTable = $sm->get('Album\Model\AlbumDataTable');
						$album_data_details = $this->albumDataTable->getalbumdata($comment_details->comment_refer_id);
						$album_details = $this->getAlbumTable()->getalbumrow($album_data_details->parent_album_id);
						$user_info = $this->getUserTable()->getUserWithProfilePic($comment_details->comment_by_user_id);
						$group_info = array();
						$album_type = '';
						$is_admin = 0;
						if($album_details->album_group_id){
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Media');
							$group_info = $this->getGroupTable()->getSubgroupWithParentSeo($album_details->album_group_id);
							$album_type = 'Media';
							$admin_status = $this->getGroupTable()->getAdminStatus($album_details->album_group_id,$identity->user_id);
							if($admin_status->is_admin){
								$is_admin = 1;
							}
							$user_role = $this->getUserGroupTable()->getUserRole($album_details->album_group_id,$identity->user_id);
							if(!empty($user_role)){
								$is_admin = 1;
							}
						}else{
							$SystemTypeData = $this->getGroupTable()->fetchSystemType('Userfiles');
							$album_type = 'Userfiles';
						}
						$arr_photo = array(
									'group_info' =>$group_info,
									'album_type' => $album_type,
									'user_id'=>$user_info->user_id,
									'user_given_name'=>$user_info->user_given_name,
									'user_profile_name'=>$user_info->user_profile_name,
									'profile_photo'=>$user_info->profile_photo,
									'user_register_type' =>$user_info->user_register_type,
									'user_fbid' =>$user_info->user_fbid,
									'album_id' =>$album_details->album_id,
									'album_title' =>$album_details->album_title,
									'album_seotitle' =>$album_details->album_seotitle,
									'album_user_id'=>$album_details->album_user_id,
									'data_id'=>$album_data_details->data_id,
									'parent_album_id'=>$album_data_details->parent_album_id,
									'added_user_id'=>$album_data_details->added_user_id,
									'data_type'=>$album_data_details->data_type,
									'data_content'=>$album_data_details->data_content,
									'album_location'=>$album_details->album_location,
									'file_like'=>$this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id),
									"comment_count" =>$this->getCommentTable()->getCommentCount($SystemTypeData->system_type_id,$album_data_details->data_id)->comment_count,
									'comments' =>$this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$album_data_details->data_id,$identity->user_id,2,0),
									'is_admin'=>$is_admin,
									);
						$array_news[] = array('content' => $arr_photo,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
										);
					break;
					case "New Friendship":
						$event_id = $news['event_id'];	
						$this->userFrontTable = $sm->get('User\Model\UserFriendTable');
						$userFrontDetails = $this->userFrontTable->getUserFriend($event_id);
						$friend = ($userFrontDetails->user_friend_sender_user_id == $identity->user_id)?$userFrontDetails->user_friend_friend_user_id:$userFrontDetails->user_friend_sender_user_id;
						$user_info = $this->getUserTable()->getUserWithProfilePic($friend);
						$arr_friend = array(										 
									'user_id'=>$user_info->user_id,
									'user_given_name'=>$user_info->user_given_name,
									'user_profile_name'=>$user_info->user_profile_name,
									'profile_photo'=>$user_info->profile_photo,
									'user_register_type' =>$user_info->user_register_type,
									'user_fbid' =>$user_info->user_fbid,
									 
									);
						$array_news[] = array('content' => $arr_friend,
												'type'=>$news['type'],
												'time'=>$news['update_time'],
										);
					break;
				}
			}
			$viewModel->setVariable('feedSummery', $array_news);			 
		}else{
			$error[] = "Your session expired. Please logged in and try";
		}
		$viewModel->setVariable('error', $error);		
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
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
	public function getActivityRsvpTable(){
        if (!$this->activityRsvpTable) {
            $sm = $this->getServiceLocator();
            $this->activityRsvpTable = $sm->get('Activity\Model\ActivityRsvpTable');
        }
        return $this->activityRsvpTable;
    }
}
