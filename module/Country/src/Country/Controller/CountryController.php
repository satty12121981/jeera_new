<?php
namespace Country\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/ 
#Country classs
use Country\Model\Country;  
class CountryController extends AbstractActionController
{
    protected $countryTable;		#variable to hold the country model confirgration 
   
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
	public function ajaxCountryListAction(){
		$request = $this->getRequest();
		$countries  = $this->getCountryTable()->fetchAll();
		$viewModel = new ViewModel(array( 'countries' => $countries));
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function ajaxCountrySelectAction(){
		$request = $this->getRequest();
		$post = $request->getPost();	
		$country ='';
		$country = $post->get('country');
		$countries  = $this->getCountryTable()->fetchAll();
		$viewModel = new ViewModel(array( 'countries' => $countries));
		$viewModel->setVariable("country",$country);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
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