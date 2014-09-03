<?php
namespace Tag\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Zend\View\Model\ViewModel;	//Return model 
 



use Zend\Session\Container; // We need this when using sessions
     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/
 
//Login Form
use Tag\Form\Login;       // <-- Add this import
use Tag\Form\LoginFilter;   

#Tag classs
use Tag\Model\Tag;  
use Tag\Model\TagTable; 


class TagController extends AbstractActionController
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
		$allTag =array();
		$allTag = array('y2m_country' => $this->getAlbumTable()->fetchAll());
		
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
	
	
	public function getTagTable()
    {
        if (!$this->countryTable) {
            $sm = $this->getServiceLocator();
            $this->countryTable = $sm->get('Tag\Model\TagTable');
        }
        return $this->countryTable;
    }
	public function listExceptSelectedAction(){
		$auth = new AuthenticationService();	
		$identity = null;
		$error = array();
		$viewModel = new ViewModel();
		$request = $this->getRequest();
		if ($auth->hasIdentity()) {             
           	$identity = $auth->getIdentity();
			$user_id = $identity->user_id;
			$tags = $this->getTagTable()->listExceptSelected($user_id);
			$viewModel->setVariable('tags', $tags);
        }else{
			$error[] = "Your session expired";
		}
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		return $viewModel;
	}
	public function  searchTagsAction(){
		$auth = new AuthenticationService();	
		$identity = null;
		$error = array();
		$viewModel = new ViewModel();
		$request = $this->getRequest();
		$search_str = '';
		if ($auth->hasIdentity()) {             
           	$identity = $auth->getIdentity();
			$user_id = $identity->user_id;
			if ($request->isPost()) {
				$search_str = $request->getPost('search_str');
			}
			$tags = $this->getTagTable()->listExceptSelected($user_id,$search_str);
			$viewModel->setVariable('tags', $tags);
        }else{
			$error[] = "Your session expired";
		}
		$viewModel->setVariable('error', $error);
		$viewModel->setTerminal($request->isXmlHttpRequest());
		$viewModel->setTemplate('tag/tag/list-except-selected');
		return $viewModel;
	}
	 
}