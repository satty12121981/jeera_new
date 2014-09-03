<?php
namespace City\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/ 
#Country classs
use Zend\Crypt\BlockCipher;	#for encryption
use City\Model\City;  
class CityController extends AbstractActionController
{
    protected $cityTable;	 
	protected $countryTable;  
    public function indexAction()
    {				
		$auth = new AuthenticationService();	
		$identity = null;        
		if ($auth->hasIdentity()) {            
           	$identity = $auth->getIdentity();
        } 
		$this->layout()->identity = $identity; 
        return array('identity' => $identity,);	 
    }
	public function citiesAction(){
		$error = array();
		$request   = $this->getRequest();
		if ($request->isPost()){
			$post = $request->getPost();
			$country_id = $post->get('country_id');
			$blockCipher = BlockCipher::factory('mcrypt', array('algorithm' => 'aes')); 
			$blockCipher->setKey('*&hhjj()_$#(&&^$%^$%^KMNVHDrt#$$$%#@@');
			$decryptCountryId = $blockCipher->decrypt($country_id); 	
			$first_country_id = (int) $decryptCountryId;  
			$selectCity = $this->getCityTable()->selectFormatAllCity($first_country_id);
			$city_selectbox = '<select name="user_profile_city" id="user_profile_city" class="styled">';
			foreach($selectCity as $key => $city){
				$city_selectbox.= '<option value="'.$key.'">'.$city.'</option>';
			}
			$city_selectbox.= '</select>';
			echo $city_selectbox;die();
		}else{
			echo $error = 'Invalid access';die();
		}		
	}
	public function ajaxCitiesAction(){
		$error = array();
		$request   = $this->getRequest();
		if ($request->isPost()){
			$post = $request->getPost();
			$country_id = $post->get('country_id');			  
			
			$city_selectbox = '<select name="user_profile_city" id="user_profile_city" class="styled">';
			if($country_id !=''){
				$selectCity = $this->getCityTable()->selectFormatAllCity($country_id);
				foreach($selectCity as $key => $city){
					$city_selectbox.= '<option value="'.$key.'">'.$city.'</option>';
				}
			}
			$city_selectbox.= '</select>';
			echo $city_selectbox;die();
		}else{
			echo $error = 'Invalid access';die();
		}		
	}
	public function ajaxCitySelectAction(){
		$error = array();
		$request   = $this->getRequest();
		if ($request->isPost()){
			$post = $request->getPost();
			$country = $post->get('country');
			if($country==''){
				$country = 'United Arab Emirates';
			}
			$country_details = $this->getCountryTable()->getCountryIdfromName($country);
			if(!empty($country_details)&&$country_details->country_id){
			$selectCity = $this->getCityTable()->selectFormatAllCity($country_details->country_id);
			}else{
				$selectCity = $this->getCityTable()->selectFormatAllCity(2);
			}
			$city_selectbox = '<select name="user_profile_city" id="user_profile_city" class="styled">';
			foreach($selectCity as $key => $city){
				$city_selectbox.= '<option value="'.$key.'">'.$city.'</option>';
			}
			$city_selectbox.= '</select>';
			echo $city_selectbox;die();
		}else{
			echo $error = 'Invalid access';die();
		}
	}
	public function ajaxCitiesFromGeocodeAction(){
		$error = array();
		$request   = $this->getRequest();
		$cities = array();
		if ($request->isPost()){
			$post = $request->getPost();
			$country = $post->get('country_id');
			$country_id = $this->getCountryTable()->getCountryIdFromGeoCode($country);
			$cities = $this->getCityTable()->selectAllCity($country_id->country_id);
		}
		$viewModel = new ViewModel(array( 'cities' => $cities));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;		
	}
	public function ajaxCitiesForAdminPlanetAction(){
		$error = array();
		$request   = $this->getRequest();
		$cities = array();
		if ($request->isPost()){
			$post = $request->getPost();
			$country = $post->get('country_id');			 
			$cities = $this->getCityTable()->selectAllCity($country);
		}
		$viewModel = new ViewModel(array( 'cities' => $cities));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function getCityTable()
    {
        if (!$this->cityTable) {
            $sm = $this->getServiceLocator();
            $this->cityTable = $sm->get('City\Model\CityTable');
        }
        return $this->cityTable;
    }
	public function getCountryTable()
    {
        if (!$this->countryTable) {
            $sm = $this->getServiceLocator();
            $this->countryTable = $sm->get('Country\Model\CountryTable');
        }
        return $this->countryTable;
    } 
}