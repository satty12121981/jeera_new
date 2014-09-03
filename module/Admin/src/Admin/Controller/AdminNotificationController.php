<?php
namespace Admin\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\Session\Container; // We need this when using sessions     
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select;
use Zend\Validator\File\Size;

#Tag classs
use Notification\Model\NotificationType;
use Notification\Model\NotificationTypeTable;

use Admin\Form\AdminNotificationTypeForm; 
use Admin\Form\AdminNotificationTypeFilter;
use Admin\Form\AdminNotificationTypeEditForm; 
use Admin\Form\AdminNotificationTypeEditFilter;   
    
class AdminNotificationController extends AbstractActionController
{   
    protected $adminNotificationTable;
	
	public function __construct()
    {
	   // do some stuff!
    }
	//===================== Fetch all notification types======================================================================
    public function indexAction()
    { 
		$title = 'Notification Types';
		$sm = $this->getServiceLocator();
		$adminNotificationTable = $sm->get('Notification\Model\NotificationTypeTable');
		$all_notification_types = array();	
		$all_notification_types = $adminNotificationTable->fetchAll();
        return array('all_notification_types' => $all_notification_types, 
						'title' => $title, 
						'flashMessages' => $this->flashMessenger()->getMessages(),
					);	 
    }
	//======================= Fetch single notification type ==================================================================
	public function viewAction()
	{   $title = 'View Notification Type';
	    $sm = $this->getServiceLocator();
		$adminNotificationTable = $sm->get('Notification\Model\NotificationTypeTable');
		
	    $id = (int)$this->params('id');
		 if (!$id) {
            return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
        }
		
		$view_notification_type = array();	
		$view_notification_type = $adminNotificationTable->get_notification_type($id);
		return array('view_notification_type' => $view_notification_type , 'title' => $title );
	}
	//============================= Add notification type ======================================================================
	public function addAction()
	{ 
	   $title = 'Add Notification type'; 
	   $form = new AdminNotificationTypeForm();
       $form->get('submit')->setAttribute('value', 'Add');
	   $request = $this->getRequest();
	   
	   	    if ($request->isPost()) {
		    $sm = $this->getServiceLocator();
	        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
			$notification_type = new NotificationType();
            $vaildate = new AdminNotificationTypeFilter($dbAdapter);
            $form->setInputFilter($vaildate);
            $form->setData($request->getPost());
 
            if ($form->isValid()) {
                $notification_type->exchangeArray($form->getData());
                if($this->getnotificationtypeTable()->savenotificationType($notification_type)=="success"){
				$alert_content = 'Notification Type successfully added....';
		         $msg = array('type'=>'success',
		                      'message'=>$alert_content,
		                      );
		         $this->flashMessenger()->addMessage($msg);
                return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
				}else{
				 $msg = array('type'=>'error',
							  'message'=>'Sorry some error occured !',
							 );
				 $this->flashMessenger()->addMessage($msg);
				 return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
				}
            }
        }  
	   return array('title' => $title,'form'=>$form );
	}
	//============================== Edit Notification Types =======================================================================
	public function editAction(){
	    $title = "Edit Notification Type";
	    $form = new AdminNotificationTypeEditForm();
        $form->get('submit')->setAttribute('value', 'Edit');
	    $request = $this->getRequest();
		$id = (int)$this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
        }
		
		$types =array();
		$types = $this->getnotificationtypeTable()->get_notification_type($id); #Get Group Details			
		if(isset($types->notification_type_id) && !empty($types->notification_type_id)){
			
		}else{
			return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
		}
		$form->bind($types);
		$request = $this->getRequest();
        if($request->isPost()) {
		    $sm = $this->getServiceLocator();
	        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		    $notification_type = new NotificationType();
            $vaildate = new AdminNotificationTypeEditFilter($dbAdapter,$id);
            $form->setInputFilter($vaildate);
            $form->setData($request->getPost());
           
            if ($form->isValid()) { 
			//$this->getAlbumTable()->saveAlbum($form->getData());
                if($this->getnotificationtypeTable()->savenotificationType($form->getData())=="success"){
				$alert_content = 'Notification '.$id.' successfully Updated....';
		         $msg = array('type'=>'success',
		                      'message'=>$alert_content,
		                      );
		         $this->flashMessenger()->addMessage($msg);
                return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
				}else{
				 $msg = array('type'=>'error',
							  'message'=>'Sorry some error occured !',
							 );
				 $this->flashMessenger()->addMessage($msg);
				 return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
				}
            }
        }

		return array('title' => $title,'form'=>$form ,'id' => $id); 
	   
	}
	//=========================================Enable Notification Type=============================================================
    public function enableAction()
	{
		
	    $id = (int)$this->params('id'); 
	
		 if (!$id) {
            return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
        }
		
		if($this->getnotificationtypeTable()->Admin_enable_notificationtype($id) == 'success')
		{  
		   $alert_content = 'Activity '.$id.' hasbeen enabled !';
		   $msg = array('type'=>'success',
		                'message'=>$alert_content,
		               );
		   $this->flashMessenger()->addMessage($msg);
		  
		   return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
		}else{
		   $msg = array('type'=>'error',
		                'message'=>'Sorry some error occured !',
		               );
		   $this->flashMessenger()->addMessage($msg);
		   return $this->redirect()->toRoute('admin/admin-activity', array('action'=>'index'));
		}
	}
    //========================================Disable Notification Type=============================================================
     public function disableAction()
	{
	    $id = (int)$this->params('id'); 
	
		 if (!$id) {
            return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
        }
		
		if($this->getnotificationtypeTable()->Admin_disable_notificationtype($id) == 'success')
		{  
		   $alert_content = 'Activity '.$id.' hasbeen disabled !';
		   $msg = array('type'=>'success',
		                'message'=>$alert_content,
		               );
		   $this->flashMessenger()->addMessage($msg);
		  
		   return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
		}else{
		   $msg = array('type'=>'error',
		                'message'=>'Sorry some error occured !',
		               );
		   $this->flashMessenger()->addMessage($msg);
		   return $this->redirect()->toRoute('admin/admin-activity', array('action'=>'index'));
		}
	}
    //========================================Delete notification type==========================================================
    public function deleteAction(){
	$id = (int)$this->params('id'); 
	
		 if (!$id) {
            return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
        }
		if($this->getnotificationtypeTable()->deleteNotificationType($id) == 'success')
		{  
		   $alert_content = 'Activity '.$id.' hasbeen Deleted !';
		   $msg = array('type'=>'success',
		                'message'=>$alert_content,
		               );
		   $this->flashMessenger()->addMessage($msg);
		  
		   return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
		}else{
		   $msg = array('type'=>'error',
		                'message'=>'Sorry some error occured !',
		               );
		   $this->flashMessenger()->addMessage($msg);
		   return $this->redirect()->toRoute('admin/admin-notification', array('action'=>'index'));
		}

	}
    //===============================================================================================================================
   
    public function getnotificationtypeTable()
    { 
        if (!$this->adminNotificationTable) {
		    
            $sm = $this->getServiceLocator();
            $this->adminNotificationTable = $sm->get('Notification\Model\NotificationTypeTable');
        }
		
        return $this->adminNotificationTable;
    }	
	
	
}