<?php
namespace Country\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Zend\View\Model\ViewModel;	//Return model 
 



use Zend\Session\Container; // We need this when using sessions
     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/
 
//Login Form
use Country\Form\Login;       // <-- Add this import
use Country\Form\LoginFilter;   

#Country classs
use Country\Model\Country;  
use Country\Model\CountryTable; 


class CountryController extends AbstractActionController
{
    protected $countryTable;		#variable to hold the country model confirgration
	
	#routing contact
	const ROUTE_CHANGEPASSWD = 'country/changepassword';
    const ROUTE_LOGIN        = 'country/login';
    const ROUTE_REGISTER     = 'country/register';
    const ROUTE_CHANGEEMAIL  = 'country/changeemail';
   
   
 	#message contact	    
    public function indexAction()
    {
		$allCountry =array();
		$allCountry = array('y2m_country' => $this->getAlbumTable()->fetchAll());
		
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
	
	#accessing the country table module
	public function getCountryTable()
    {
        if (!$this->countryTable) {
            $sm = $this->getServiceLocator();
            $this->countryTable = $sm->get('Country\Model\CountryTable');
        }
        return $this->countryTable;
    }
	 
}