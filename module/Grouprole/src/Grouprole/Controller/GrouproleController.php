<?php
namespace Grouprole\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/ 
#Country classs
use Zend\Crypt\BlockCipher;	#for encryption
use Grouprole\Model\Grouprole;  
class GrouproleController extends AbstractActionController
{
    protected $grouprolesTable;		#variable to hold the country model confirgration 
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
        return array('identity' => $identity);	 
    }
	public function rolelistAction(){
		$error = array();
		$vm = new ViewModel();	
		$request   = $this->getRequest();
		$roles = $this->grouprolesTable()->getRoles();
		$vm->setVariable('roles', $roles);
		$vm->setTerminal($request->isXmlHttpRequest());
		return $vm;
	}
	public function grouprolesTable(){
		
		if (!$this->grouprolesTable) {
            $sm = $this->getServiceLocator();
			$this->grouprolesTable = $sm->get('Grouprole\Model\GrouproleTable');
        }
        return $this->grouprolesTable;
	}	
}