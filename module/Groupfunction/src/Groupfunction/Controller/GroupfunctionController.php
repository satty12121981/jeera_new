<?php
namespace Groupfunction\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/ 
#Country classs
use Zend\Crypt\BlockCipher;	#for encryption
use Groupfunction\Model\Groupfunction;  
class GroupfunctionController extends AbstractActionController
{
    protected $cityTable;		#variable to hold the country model confirgration 
	protected $countryTable;
 	#it is not using right now   
    public function indexAction()
    {
		
		//echo "<pre>";print_r($shail);exit;
					
		$auth = new AuthenticationService();	
		$identity = null;        
		if ($auth->hasIdentity()) {
            // Identity exists; get it
           	$identity = $auth->getIdentity();
        } 
		$this->layout()->identity = $identity;	//assign Identity to layout    
        return array('identity' => $identity,);	 
    }
	public function grouprolesAction(){
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
	#accessing the country table module
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