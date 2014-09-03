<?php 
#namespance Define
namespace User\Controller; 
#zend Library 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;	//using set template
use \Exception;

use Zend\Crypt\BlockCipher;	#for encryption
use Zend\Crypt\Password\Bcrypt;	#for password Encryption
use User\Auth\BcryptDbAdapter as AuthAdapter;
#session
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
//use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Authentication\Storage\Session;
#Custom classes
use User\Model\User;
use User\Model\UserProfile;
use Tag\Model\UserTag;
use User\Model\Recoveryemails;
  #user Tags/Interest
use Facebook\Controller\Facebook;
use Facebook\Controller\FacebookApiException;
use Zend\Form\Form;
use User\Form\Login;       
use User\Form\LoginFilter; 
use User\Form\RegisterStepFirst;       // <-- Register 1 step form
use User\Form\RegisterStepTwo;       // <-- Register 2 step form
use User\Form\RegisterStepThree;       // <-- Register 3 step form
use User\Form\RegisterStepFour;       // <-- Register 4 step form
use User\Form\ResendVerification;       // <-- Register 4 step form
use User\Form\ForgotPassword;
use User\Form\ResetPassword;

class UserController extends AbstractActionController
{
   	protected $userTable;
	protected $userProfileTable;
	protected $groupTable;
	protected $userGroupTable;
	protected $countryTable;
	protected $activityTable;	
	protected $photoTable;	
	protected $tagTable;	
	protected $userTagTable;	 
	protected $RecoveryemailsTable;	
	protected $cityTable;
	#routing contact
	const ROUTE_CHANGEPASSWD = 'user/changepassword';
    const ROUTE_LOGIN        = 'user/login';
    const ROUTE_REGISTER     = 'user/register';
    const ROUTE_CHANGEEMAIL  = 'user/changeemail';
	public function  __construct() {

        $this->facebook = new Facebook(array(
            'appId'  => '739393236113308',
            'secret' => '9da375419c2da6d66b7237673b285ff0'
        ));

    }
	#this function will load the css and javascript need for perticular action
	protected function getViewHelper($helperName)
	{
    	return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}	

    public function indexAction()
    {	
		$error = array();
		$success = array();
		$auth = new AuthenticationService();	
		$identity = null;        
		if ($auth->hasIdentity()) {            
           	$identity = $auth->getIdentity();	
			$this->layout()->identity = $identity;			
			$viewModel = new ViewModel(array( 'userData' => $identity,'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()));    		 	
			return $viewModel;	 						 		
        }else{			
			 return $this->redirect()->toRoute('user/login', array('action' => 'login'));		
		}		
    }
	public function ajaxloginAction(){
		$msg = '';	#Error variable
		$error = 1;
		$success = array();	#success message variable				 
      	$form = new Login();
		$request = $this->getRequest();
		if ($request->isPost()) {
			$form->setInputFilter(new LoginFilter());			
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $request->getPost();
				$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
				$authAdapter = new AuthAdapter($dbAdapter);
				$authAdapter
					->setTableName('y2m_user')
					->setIdentityColumn('user_email')
					->setCredentialColumn('user_password');					
				$authAdapter
					->setIdentity(addslashes($data['user_email']))
					->setCredential($data['user_password']);				
				$result = $authAdapter->authenticate();
				if (!$result->isValid()) {
					$error = 1;
					$msg = "Invalid Email or Password"; 	
				} else { 
					$auth = new AuthenticationService();
					$storage = $auth->getStorage();
					$storage->write($authAdapter->getResultRowObject(
						null,
						'user_password'
					));
					if($this->params()->fromPost('rememberme')){ 
						  $authNamespace = new Container(Session::NAMESPACE_DEFAULT);
						  $authNamespace->getManager()->rememberMe(200000);
					  }
					  else{
						 $authNamespace = new Container(Session::NAMESPACE_DEFAULT);
						$authNamespace->getManager()->rememberMe(200);
					  }
					$error = 0;
					$msg = '';
					 	
				}
			}else{
				$error = 1;
				$validation_msg = $form->getMessages();
				if(isset($validation_msg['user_email']['isEmpty'])&&$validation_msg['user_email']['isEmpty']!=''){
					$msg = $validation_msg['user_email']['isEmpty'];
				}
				else if(isset($validation_msg['user_password']['isEmpty'])&&$validation_msg['user_password']['isEmpty']!=''){
					$msg = $validation_msg['user_password']['isEmpty'];
				}
				else if(isset($validation_msg['user_email']['emailAddressInvalidHostname'])&&$validation_msg['user_email']['emailAddressInvalidHostname']!=''){
					$msg = $validation_msg['user_email']['emailAddressInvalidHostname'];
				}
				else if(isset($validation_msg['user_email']['hostnameUnknownTld'])&&$validation_msg['user_email']['hostnameUnknownTld']!=''){
					$msg = $validation_msg['user_email']['hostnameUnknownTld'];
				}
				else if(isset($validation_msg['user_email']['hostnameLocalNameNotAllowed'])&&$validation_msg['user_email']['hostnameLocalNameNotAllowed']!=''){
					$msg = $validation_msg['user_email']['hostnameLocalNameNotAllowed'];
				}
				else{
					$msg = "Error occured. PLease try again";
				}
			}
		}
		else{
			$error = 1;
			$msg = 'Invalid access!';
		}
		$return_array['msg'] = $msg;
		$return_array['error'] = $error;
		echo json_encode($return_array);die();
	}
	public function loginAction() {
		$error = array();	#Error variable
		$success = array();	#success message variable				 
      	$form = new Login();
		$request = $this->getRequest();
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) { 
			return $this->redirect()->toRoute('home');	
		}
		if ($request->isPost()) {			
			$form->setInputFilter(new LoginFilter());			
			$form->setData($request->getPost()); 
			if ($form->isValid()) {				 
				$data = $request->getPost();
				$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
				$authAdapter = new AuthAdapter($dbAdapter);
				$authAdapter
					->setTableName('y2m_user')
					->setIdentityColumn('user_email')
					->setCredentialColumn('user_password');					
				$authAdapter
					->setIdentity(addslashes($data['user_email']))
					->setCredential($data['user_password']);				
				$result = $authAdapter->authenticate();
				if (!$result->isValid()) {
					
					$error[] = "Invalid Email or Password"; 	
				} else { 
					$auth = new AuthenticationService();
					$storage = $auth->getStorage();
					$storage->write($authAdapter->getResultRowObject(
						null,
						'user_password'
					));
					if($this->params()->fromPost('rememberme')){ 
						  $authNamespace = new Container(Session::NAMESPACE_DEFAULT);
						  $authNamespace->getManager()->rememberMe(2000);
					  }
					  else{
						 $authNamespace = new Container(Session::NAMESPACE_DEFAULT);
						$authNamespace->getManager()->rememberMe(20);
					  }
					return $this->redirect()->toRoute('application', array('action' => 'index'));		
				}
			}	
		} //if ($request->isPost())		
		return array('form' => $form,'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());
	}
	
	public function registerAction(){	
		$vm = new ViewModel();
		$sm = $this->getServiceLocator();
		$step = 1;
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');				
		$registerSession = new Container('register'); 		
		$request = $this->getRequest();
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) { 
			return $this->redirect()->toRoute('home');	
		}
		$user = new User();
		if ($request->isPost()) {
		 if($this->params()->fromPost('reset')=="Skip"){
			$form = '';
			$registerSession->getManager()->destroy();					 
			$vm->setTemplate('user/user/register-step-final');
		 }		
		} 
		switch($registerSession->step){
			case "step1":
				$form = new RegisterStepFirst();
				if ($request->isPost()) {
					$form->setInputFilter($user->getInputFilter($dbAdapter));
					$form->setData($request->getPost());
					if($form->isValid()){
						$data  = $form->getData();
						$bcrypt = new Bcrypt();
						$data['user_password'] = $bcrypt->create($data['user_password']);
						$user_verification_key = md5('enckey'.rand().time());
						$data['user_verification_key'] = $user_verification_key;
						$data['user_profile_name'] = $this->make_url_friendly($data['user_first_name']."".$data['user_middle_name']."".$data['user_last_name']);
						//$registerSession->form1 = $form->getData();
						$user->exchangeArray($data);
						$insertedUserId = $this->getUserTable()->saveUser($user);
						if($insertedUserId){
							$this->sendVerificationEmail($user_verification_key,$insertedUserId,$data['user_email']);
						}
						$registerSession->insertedUserId = $insertedUserId;
						$registerSession->step ="step2";						
						$selectAllCountry = $this->getCountryTable()->selectFormatAllCountry();
						$first_country =  key($selectAllCountry);
						$blockCipher = BlockCipher::factory('mcrypt', array('algorithm' => 'aes')); 
						$blockCipher->setKey('*&hhjj()_$#(&&^$%^$%^KMNVHDrt#$$$%#@@');
						$decryptCountryId = $blockCipher->decrypt($first_country); 	
						$first_country_id = (int) $decryptCountryId;  
						$selectCity = $this->getCityTable()->selectFormatAllCity($first_country_id); 
						$form = new RegisterStepTwo($selectAllCountry,$selectCity);
						$step = 2;
					}
				}
			break;
			case "step2":
				$step = 2;
				$selectAllCountry = $this->getCountryTable()->selectFormatAllCountry();
				$first_country =  key($selectAllCountry);
				$blockCipher = BlockCipher::factory('mcrypt', array('algorithm' => 'aes')); 
				$blockCipher->setKey('*&hhjj()_$#(&&^$%^$%^KMNVHDrt#$$$%#@@');
				$decryptCountryId = $blockCipher->decrypt($first_country); 	
				$first_country_id = (int) $decryptCountryId;  
				$selectCity = $this->getCityTable()->selectFormatAllCity($first_country_id); 
				$form = new RegisterStepTwo($selectAllCountry,$selectCity);
				if ($request->isPost()) { 
					$form->setInputFilter($user->getInputFilterFrom2());
					$form->setData($request->getPost());
					if($form->isValid()){  
						$data  = $form->getData();
						$encryptedCountryCode = $this->params()->fromPost('user_profile_country_id');
						$blockCipher = BlockCipher::factory('mcrypt', array('algorithm' => 'aes')); 
						$blockCipher->setKey('*&hhjj()_$#(&&^$%^$%^KMNVHDrt#$$$%#@@');
						$decryptCountryId = $blockCipher->decrypt($encryptedCountryCode); 							
						$country_id = (int) $decryptCountryId;
						$profile_data['user_profile_country_id'] = $country_id;
						$profile_data['user_profile_user_id'] = $registerSession->insertedUserId;
						$profile_data['user_profile_city_id'] = $data['user_profile_city'];
						$profile_data['user_profile_profession'] = $data['user_profile_profession'];
						if($data['user_gender']=='F')
							$data['user_gender'] = 'female';
						else
							$data['user_gender'] = 'male';
						$profile_data['user_profile_dob'] = $data['user_profile_dob_yy']."-".$data['user_profile_dob_mm']."-".$data['user_profile_dob_dd'];
						$profile_data['user_profile_profession_at'] = $data['user_profile_profession_at'];						 
						$data['user_id'] = $registerSession->insertedUserId;
						$userdata = array('user_gender' => $data['user_gender']);						
						$this->getUserTable()->updateUser($userdata,$registerSession->insertedUserId);
						$userProfile = new UserProfile();
						$userProfile->exchangeArray($profile_data);
						$insertedUserProfileId = $this->getUserProfileTable()->saveUserProfile($userProfile);
						$registerSession->form2 = $form->getData();
						$registerSession->step ="step3";
						$selectAllTags = $this->getTagTable()->getPopularUserTags(0,12); 
						$vm->setVariable('tags', $selectAllTags);						
						$form = new RegisterStepThree();	
						$step = 3;						
					} 
				}
			break;
			case "step3":
				$step = 3;				 
				$selectAllTags = $this->getTagTable()->getPopularUserTags(0,12); 
				$vm->setVariable('tags', $selectAllTags);
				$form = new RegisterStepThree();
				$userTag = new UserTag();
				if ($request->isPost()) {
					$form->setData($request->getPost());
					$tags = $this->params()->fromPost('tags');
					if($tags!=''){
						$arr_tags = explode('~',$tags);
						$user_tags = array_unique($arr_tags);
						//$userTags =$this->params()->fromPost('user_tag_id'); 
						foreach($user_tags as $row){ 
							//$blockCipher = BlockCipher::factory('mcrypt', array('algorithm' => 'aes')); 
							//$blockCipher->setKey('*&hhjj()_$#(&&^$%gdgfd^&*%fgfg'); 
							//$decryptTagId = $blockCipher->decrypt($row); 							
							$tag_id = (int) $row;						
							$userTagData =array();
							$userTagData['user_tag_user_id'] = $registerSession->insertedUserId;
							$userTagData['user_tag_tag_id'] = $tag_id;
							$userTag->exchangeArray($userTagData);
							$insertedUserTagId = $this->getUserTagTable()->saveUserTag($userTag);	
							$step =4;
						} 
					}
					$form = '';
					$registerSession->getManager()->destroy();					
					$vm->setTemplate('user/user/register-step-final');
					
				}
			break;			 
			default:
			$form = new RegisterStepFirst();		
			$registerSession->step ="step1";
		}
		$vm->setVariable('step', $step);	
		$vm->setVariable('form', $form);
		return $vm;
	}
	public function fbloginAction(){
		 $user = null;
        $user = $this->facebook->getUser();
        $user_profile = null;
        $logoutUrl = null;
        $statusUrl = null;

        $config = $this->getServiceLocator()->get('Config');

        if ($user) {
            $logoutUrl = $this->facebook->getLogoutUrl();
            $this->facebook->setExtendedAccessToken();
            $access_token = $this->facebook->getAccessToken();
            $user_profile = $this->facebook->api('/me?access_token='.$access_token);
        }

         
		return $this->redirect()->toUrl($this->facebook->getLoginUrl(array('redirect_uri' => $config['pathInfo']['fbredirect'], 'scope' => 'public_profile,email,user_friends,offline_access')));
	}
	public function fbredirectAction(){ 
		$user = $this->facebook->getUser();  
		$sm = $this->getServiceLocator();	
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');		
        if ($user) { 
			 try {
            // Proceed knowing you have a logged in user who's authenticated.
                $access_token = $this->facebook->getAccessToken();
                $user_profile = $this->facebook->api('/me?access_token='.$access_token);
				 
                $this->userTable = $sm->get('User\Model\UserTable');
                $this->userProfileTable = $sm->get('User\Model\UserProfileTable');
				if(isset($user_profile['email'])&&$user_profile['email']!=''){
					$checkFbUserData = $this->userTable->getUserByEmail($user_profile['email']);
				}else{
					$checkFbUserData = $this->userTable->getUserByFbid($user_profile['id']);
				}
                if (!empty($checkFbUserData->user_id)) {                     
					$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
					
					 
						$auth = new AuthenticationService();
						$storage = $auth->getStorage();
						$storage->write($checkFbUserData);					 
						$authNamespace = new Container(Session::NAMESPACE_DEFAULT);
						$authNamespace->getManager()->rememberMe(2000);
						return $this->redirect()->toRoute('home');
					
                }else{					 
					$user_data['user_given_name'] =  $user_profile['first_name'].' '.$user_profile['last_name'];
					$user_data['user_first_name'] =  $user_profile['first_name'];
					$user_data['user_last_name'] =  $user_profile['last_name'];
					$user_data['user_profile_name'] = $this->make_url_friendly($user_profile['first_name']."".$user_profile['last_name']);
					$user_data['user_status'] = 1;
					$user_data['user_email'] = $user_profile['email'];
					$user_data['user_gender'] = $user_profile['gender'];
					$user_data['user_register_type'] = 'facebook';
					$user_data['user_fbid'] = $user_profile['id'];
					$user = new User();
					$user->exchangeArray($user_data);
					$insertedUserId = $this->getUserTable()->saveUser($user);
					$checkFbUserData =  $this->getUserTable()->getUser($insertedUserId);
					$auth = new AuthenticationService();
					$storage = $auth->getStorage();
					$storage->write($checkFbUserData);					 
					$authNamespace = new Container(Session::NAMESPACE_DEFAULT);
					$authNamespace->getManager()->rememberMe(2000);
					return $this->redirect()->toRoute('home');
				}
            }
            catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }
		}else{
			return $this->redirect()->toRoute('user/login', array('action' => 'login'));
		}
	}
	public function varifyemailAction(){
		$vm = new ViewModel();  
		$key = $this->getEvent()->getRouteMatch()->getParam('key');  
		$id = $this->getEvent()->getRouteMatch()->getParam('id');
		$error = array();
		 
		if($key!=''&&$id!=''){		
			$user_id = $this->getUserTable()->checkUserVarification($key,$id);
			if($user_id){
				$data = array('user_status'=>1);
				$this->getUserTable()->updateUser($data,$user_id);
				return $this->redirect()->toRoute('user/login', array('action' => 'login'));	
			}
			else{	
				$error[] = "Varification code that you are entered is not valid";
			}
			
		}
		else{
			$error[] = "Unauthorized access";
		}
		$vm->setVariable('error', $error);
		return $vm;
	}
	public function resendverificationAction(){
		$vm = new ViewModel();
		$form = new ResendVerification();
		$request = $this->getRequest();
		$user = new User();
		$error = array();
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) { 
			return $this->redirect()->toRoute('home');	
		}
		$success = array();
		if ($request->isPost()) {
			$form->setInputFilter($user->getResendverificationFilter());
			$form->setData($request->getPost());
			if($form->isValid()){ 
				$user_details = $this->getUserTable()->getUserFromEmail($this->params()->fromPost('user_email'));
				if(!empty($user_details)){
					if($user_details->user_status){
						$error[] = "This profile is already activated";
					}
					else{
						$user_verification_key = md5('enckey'.rand().time());						 
						$data['user_verification_key'] = $user_verification_key;
						$this->getUserTable()->updateUser($data,$user_details->user_id);						
						$this->sendVerificationEmail($user_verification_key,$user_details->user_id,$user_details->user_email);
						$success[] = "Varification code is send to your email. Please check your email";
					}
				}
				else{
					$error[] = "No records exist in this system with the given email id";
				}
			}
		}
		$vm->setVariable('error', $error);
		$vm->setVariable('success', $success);
		$vm->setVariable('form', $form);
		return $vm;
	}
	 public function logoutAction()
    {
		$user = $this->facebook->getUser();
         

      
		$auth = new AuthenticationService();	
	  	$auth->clearIdentity();
		unset($_SESSION);	  
		$this->flashmessenger()->addMessage("You've been logged out");
		if($user){
          $logoutUrl = $this->facebook->getLogoutUrl();
          $this->redirect()->toUrl($logoutUrl);
       }
		return $this->redirect()->toRoute('user/login', array('action' => 'login'));			
    }
	public function forgotPasswordAction(){
		$vm = new ViewModel();
		$error = array();
		$success = array();
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) { 
			return $this->redirect()->toRoute('home');	
		}
		$form = new ForgotPassword();
		$request = $this->getRequest();
		$recoveremails = new Recoveryemails();
		if ($request->isPost()) {
			$form->setInputFilter($recoveremails->getForgotPasswordFilter());
			$form->setData($request->getPost());
			if($form->isValid()){ 
				$user_details = $this->getUserTable()->getUserFromEmail($this->params()->fromPost('user_email'));
				if(!empty($user_details)){
					if($user_details->user_status){
						$data  = $form->getData();
						$data['user_id'] = $user_details->user_id;
						$secret_code = time().rand();
						$data['secret_code'] = $secret_code;
						$data['status'] = 0;
						$recoveremails->exchangeArray($data);
						$this->getRecoveremailsTable()->ResetAllActiveRequests($user_details->user_id);
						$insertedRecoveryId = $this->getRecoveremailsTable()->saveRecovery($recoveremails);
						if($insertedRecoveryId){
							$this->sendPasswordResetMail($secret_code,$insertedRecoveryId,$user_details->user_email);
							$success[] = "Password reset option send to your email. Please check your email and follow the steps";
						}
						
					}
					else{
						$error[] = "No records exist in this system with the given email id";
					}
				}
				else{
					$error[] = "No records exist in this system with the given email id";
				}
			}
		}
		$this->layout('layout/container_top');
		$vm->setVariable('error', $error);
		$vm->setVariable('success', $success);
		$vm->setVariable('form', $form);
		return $vm;
	}
	public function resetpasswordAction(){ 
		$vm = new ViewModel();  
		$key = $this->getEvent()->getRouteMatch()->getParam('key');  
		$id = $this->getEvent()->getRouteMatch()->getParam('id');
		$error = array();	 
		if($key!=''&&$id!=''){	
			$request_record = new Recoveryemails();
			$request_record = $this->getRecoveremailsTable()->checkResetRequest($key,$id);			 
			if($request_record){
				 if($request_record->status){
					$error[] = "This option is expired for you.";
					$this->flashmessenger()->addMessage("This option is expired for you.");
					return $this->redirect()->toRoute('user/login', array('action' => 'login'));	
				 }
				 else if(md5(md5('recoverid~'.$request_record->id))!=$id){
					$error[] = "Varification code that you are entered is not valid";
					$this->flashmessenger()->addMessage("Varification code that you are entered is not valid.");
					return $this->redirect()->toRoute('user/login', array('action' => 'login'));	
				 }
				 else{
					$current_date = strtotime(date("Y-m-d"));
					$expiry_date = strtotime(date("Y-m-d", strtotime($request_record->senddate)) . " +1 day");
					if($current_date>$expiry_date){
						$data['status'] = 1;
						$request_record = $this->getRecoveremailsTable()->updateRecovery($data,$request_record->id);
						$error[] = "This option is expired for you.";
						$this->flashmessenger()->addMessage("This option is expired for you.");
						return $this->redirect()->toRoute('user/login', array('action' => 'login'));	
					}
					else{
						$user_id = $request_record->user_id;
						$form = new ResetPassword();
						$request = $this->getRequest();
						$recoveremails = new Recoveryemails();
						if ($request->isPost()) {
							$form->setInputFilter($recoveremails->getResetPasswordFilter());
							$form->setData($request->getPost());
							if($form->isValid()){
								$userdata  = $form->getData();
								$bcrypt = new Bcrypt();
								$data['user_password'] = $bcrypt->create($userdata['user_password']);
								if($this->getUserTable()->updateUser($data,$user_id)){
									$this->flashmessenger()->addMessage("Successfully reset your password.");
									$data_expry['status'] = 1;
									$request_record = $this->getRecoveremailsTable()->updateRecovery($data_expry,$user_id);
									return $this->redirect()->toRoute('user/login', array('action' => 'login'));	
								}
								else{
									$error[] = "Some error occured. Please try again";
								}
							}							
						}
						$this->layout('layout/container_top');
						$vm->setVariable('key', $key);
						$vm->setVariable('id', $id);
						$vm->setVariable('error', $error);
						$vm->setVariable('form', $form);
						return $vm; 
					}
				 }
			}
			else{	
			$this->flashmessenger()->addMessage("Varification code that you are entered is not valid");
						return $this->redirect()->toRoute('user/login', array('action' => 'login'));	
				$error[] = "Varification code that you are entered is not valid";
			}
			
		}
		else{
			$this->flashmessenger()->addMessage("Unauthorized accessUnauthorized access");
						return $this->redirect()->toRoute('user/login', array('action' => 'login'));	
			$error[] = "Unauthorized access";
		}
		$vm->setVariable('error', $error);
		return $vm; 
	}
	#access User Module 
    public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    } 
	
	#access User Profile Module
    public function getUserProfileTable()
    {
        if (!$this->userProfileTable) {
            $sm = $this->getServiceLocator();
            $this->userProfileTable = $sm->get('User\Model\UserProfileTable');
        }
        return $this->userProfileTable;
    } 
	
	#access Galaxy/Planet Table Module
	 public function getGroupTable()
    {
        if (!$this->groupTable) {
            $sm = $this->getServiceLocator();
			$this->groupTable = $sm->get('Groups\Model\GroupsTable');
        }
        return $this->groupTable;
    } 
	
	#access User Galaxy/Planet Module
	 public function getUserGroupTable()
    {
        if (!$this->userGroupTable) {
            $sm = $this->getServiceLocator();
			$this->userGroupTable = $sm->get('Group\Model\UserGroupTable');
        }
        return $this->userGroupTable;
    } 
	#accessing the country table module
	 public function getCountryTable()
    {
        if (!$this->countryTable) {
            $sm = $this->getServiceLocator();
            $this->countryTable = $sm->get('Country\Model\CountryTable');
        }
        return $this->countryTable;
    }  
	public function getCityTable(){
		if (!$this->cityTable) {
            $sm = $this->getServiceLocator();
            $this->cityTable = $sm->get('City\Model\CityTable');
        }
        return $this->cityTable;
	}
	#access Activity Table Module
	 public function getActivityTable()
    {
        if (!$this->activityTable) {
            $sm = $this->getServiceLocator();
            $this->activityTable = $sm->get('Activity\Model\ActivityTable');
        }
        return $this->activityTable;
    } 
	
	#access Activity Table Module
	 public function getTagTable()
    {
        if (!$this->tagTable) {
            $sm = $this->getServiceLocator();
            $this->tagTable = $sm->get('Tag\Model\TagTable');
        }
        return $this->tagTable;
    } 
	public function getRecoveremailsTable(){
		if (!$this->RecoveryemailsTable) {
            $sm = $this->getServiceLocator();
            $this->RecoveryemailsTable = $sm->get('User\Model\RecoveryemailsTable');
        }
        return $this->RecoveryemailsTable;
	}
	#access Activity Table Module
	 public function getUserTagTable()
    {
        if (!$this->userTagTable) {
            $sm = $this->getServiceLocator();
            $this->userTagTable = $sm->get('Tag\Model\UserTagTable');
        }
        return $this->userTagTable;
    } 
	 
	public function sendVerificationEmail($user_verification_key,$insertedUserId,$emailId){
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');	 
		$user_insertedUserId = md5(md5('userId~'.$insertedUserId));
		$body = $this->renderer->render('user/email/emailVarification.phtml', array('user_verification_key'=>$user_verification_key,'user_insertedUserId'=>$user_insertedUserId));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";

		$textPart = new MimePart($body);
		$textPart->type = "text/plain";

		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));

		$message = new Mail\Message();
		$message->setFrom('admin@jeera.com');
		$message->addTo($emailId);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject("Registration confirmation");
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');

		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
	}
	public function sendPasswordResetMail($user_verification_key,$insertedRecoveryid,$emailId){
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');	 
		$user_recoverId = md5(md5('recoverid~'.$insertedRecoveryid));
		$body = $this->renderer->render('user/email/emailResetPassword.phtml', array('user_verification_key'=>$user_verification_key,'user_recoverId'=>$user_recoverId));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";

		$textPart = new MimePart($body);
		$textPart->type = "text/plain";

		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));

		$message = new Mail\Message();
		$message->setFrom('admin@jeera.com');
		$message->addTo($emailId);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject("Reset password request");
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');

		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
	}
	public function ajaxgettagAction(){
		$request = $this->getRequest();
		$page = $request->getPost('page');
		$tag_string = $request->getPost('search_string');
		$return_string = '';
		if($page>0){
			$page_limit = 12+($page-1);
			$selectAllTags = $this->getTagTable()->getPopularUserTags($page_limit,1,$tag_string); 
			foreach($selectAllTags as $tags){
				$return_string .= '<a href="javascript:void(0)" class="add-option"  id="'.$tags->tag_id.'" >'.$tags->tag_title.'</a>';
			}			
		}
		echo $return_string;die();
	}
	public function tagsearchAction(){
		$return_string = '';
		$request = $this->getRequest();
		$tag_string = $request->getPost('search_string');
		$selectAllTags = $this->getTagTable()->getPopularUserTags(0,12,$tag_string); 
		foreach($selectAllTags as $tags){
			$return_string .= '<a href="javascript:void(0)" class="add-option"  id="'.$tags->tag_id.'" >'.$tags->tag_title.'</a>';
		}
		echo $return_string;die();
	}
	public function make_url_friendly($string)
	{
		$string = trim($string);

		// weird chars to nothing
		$string = preg_replace('/(\W\B)/', '',  $string);

		// whitespaces to underscore
		$string = preg_replace('/[\W]+/',  '_', $string);

		// dash to underscore
		$string = str_replace('-', '_', $string);
		if(!$this->checkProfileNameExist($string)){
			return $string; 
		}
		$length = 5;
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$string = strtolower($string).'_'.$randomString;
		if(!$this->checkProfileNameExist($string)){
			return $string; 
		}
		// make it all lowercase
		$string = strtolower($string).'_'.time();

		return $string; 
	}
	public function checkProfileNameExist($string){
		if($this->getUserTable()->checkProfileNameExist($string)){
			return true;
		}else{
			return false;
		}
	}
	public function changepasswordAction(){	
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
			$identity = $auth->getIdentity();
            $user_id = $identity->user_id;
            if ($request->isPost()) {
				$bcrypt = new Bcrypt();
                $post = $request->getPost();
				$current_password = $post['current_pass'];
				$new_pass = $post['new_pass'];
				$re_pass = $post['re_pass'];
				if($current_password==''){
					$error = 'Enter your current password';
					$error_count++;
				}
				if($new_pass==''){
					$error = 'Enter new password';
					$error_count++;
				}
				if($re_pass==''){
					$error = 'please confirm your password';
					$error_count++;
				}
				if($new_pass!=$re_pass){
					$error = 'New password and confirm password are not identical';
					$error_count++;
				}
				$this->userTable = $sm->get('User\Model\UserTable');
				$userData = $this->userTable->getUser($user_id);
				if(!$bcrypt->verify($_POST['current_pass'], $userData->user_password)){
					$error = 'Current password is wrong';
					$error_count++;
				}
				if($error_count == 0){	
					
					$pass = $bcrypt->create($new_pass);
					if($this->userTable->changeUserpassword($pass,$user_id)){
						$error = '';
						$error_count=0;
					}else{
						$error = 'Current password is wrong';
						$error_count++;
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
}
