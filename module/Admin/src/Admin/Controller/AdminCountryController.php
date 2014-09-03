<?php
namespace Admin\Controller; 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select;
/*use Zend\Authentication\Result as Result;
use Zend\Authentication\Storage;*/ 
#Country classs
use Country\Model\Country;
use Admin\Form\AdminCountryForm;
use Admin\Form\AdminCountryFilter;   
use Admin\Form\AdminCountryEditFilter;   
class AdminCountryController extends AbstractActionController
{
    protected $countryTable;		#variable to hold the country model confirgration 
	protected $userTable;			#variable to hold the country model confirgration 
	protected $userProfileTable;			#variable to hold the country model confirgration 
   
 	#Displaying Country Grid
    public function indexAction()
    {		 
		$error = array();	#Error variable
		$success = array();	#success message variable
				
		$select = new Select();
        $order_by = $this->params()->fromRoute('order_by') ?  $this->params()->fromRoute('order_by') : 'country_id';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
        $select->order($order_by . ' ' . $order);		
		
		#fetch all the country
		$allCountryData = array();	
		$allCountryData = $this->getCountryTable()->fetchAll();	
		  
        return array('allCountryData' => $allCountryData,'order_by' => $order_by,'order' => $order,'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());	 
    }
	
	public function addAction()
    {        
	    $error = array();	#Error variable
		$success = array();	#success message variable
		
		#db connectivity
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		
		$form = new AdminCountryForm();
        $form->get('submit')->setAttribute('value', 'Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
      		$country = new Country();
          	$form->setInputFilter(new AdminCountryFilter($dbAdapter));					 
           	$form->setData($request->getPost());
            if ($form->isValid()) {
                $country->exchangeArray($form->getData());
                $this->getCountryTable()->saveCountry($country);
                // Redirect to list of countrys
                return $this->redirect()->toRoute('admin/admin-country');
            } 
        }

        return array('form' => $form, 'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages());
    }

    public function editAction()
    {
        $error = array();	#Error variable
		$success = array();	#success message variable
		
		#db connectivity
		$sm = $this->getServiceLocator();
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		
		$id = (int)$this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('admin/admin-country', array('action'=>'index'));
        }
        $country = $this->getCountryTable()->getCountry($id);		 
		if(isset($country->country_id) && !empty($country->country_id)){
		
		}else{
			return $this->redirect()->toRoute('admin/admin-country', array('action'=>'index'));
		} 	 

        $form = new AdminCountryForm();
        $form->bind($country);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
			$form->setInputFilter(new AdminCountryEditFilter($dbAdapter, $id));		
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getCountryTable()->saveCountry($country);

                // Redirect to list of countrys
                return $this->redirect()->toRoute('admin/admin-country');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
			'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()	
        );
    }

    public function deleteAction()
    {
    	$error = array();	#Error variable
		$success = array();	#success message variable
		
		// echo "hello";exit;
	    $id = (int)$this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('admin/admin-country');
        }
		
		$country = $this->getCountryTable()->getCountry($id);		 
		if(isset($country->country_id) && !empty($country->country_id)){
			
		}else{
			return $this->redirect()->toRoute('admin/admin-country', array('action'=>'index'));
		} 

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $id = (int)$request->getPost()->get('id');
                $this->getCountryTable()->deleteCountry($id);
            }

            // Redirect to list of countrys
            return $this->redirect()->toRoute('admin/admin-country');
        }

        return array(
            'id' => $id,
            'country' => $this->getCountryTable()->getCountry($id),
			'usersList' => $this->getUserProfileTable()->getUserProfileOfCountry($id),
			'error' => $error, 'success' => $success, 'flashMessages' => $this->flashMessenger()->getMessages()	
        );
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
	
	#accessing the country table module
	public function getUserTable()
    {
        if (!$this->userTable) {
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('User\Model\UserTable');
        }
        return $this->userTable;
    }
	
	#accessing the user profile table module
	public function getUserProfileTable()
    {
        if (!$this->userProfileTable) {
            $sm = $this->getServiceLocator();
            $this->userProfileTable = $sm->get('User\Model\UserProfileTable');
        }
        return $this->userProfileTable;
    }
	 
}