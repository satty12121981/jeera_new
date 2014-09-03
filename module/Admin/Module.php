<?php
namespace Admin;

use Notification\Model\NotificationTypeTable;
use Activity\Model\ActivityTable;
use Country\Model\CountryTable;
use User\Model\UserTable;
use User\Model\UserProfileTable;
use Tag\Model\TagTable;
use Tag\Model\GroupTagTable;
use Tag\Model\UserTagTable;
use Photo\Model\PhotoTable;
use Groups\Model\GroupsTable;
use Groups\Model\UserGroupTable;
use Notification\Model\UserNotificationTable;
use Groups\Model\UserGroupAddSuggestionTable;
use Admin\Model\AdminTable;
use Zend\Db\ResultSet\ResultSet;			 
use Zend\Db\TableGateway\TableGateway;		 
use Zend\Mvc\ModuleRouteListener;			#it is use to listen module
/*************** Session *************/
use Zend\Session\SessionManager;
use Zend\Session\Container;
class Module
{    
	 public function onBootstrap($e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		$this->bootstrapSession($e);
		//For Multiple layout...
		  
        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller      = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\')); 
            $config          = $e->getApplication()->getServiceManager()->get('config');
           // echo '<pre>';print_r($config); die();
            $routeMatch = $e->getRouteMatch();
            $actionName = strtolower($routeMatch->getParam('action', 'not-found')); // get the action name

            if (isset($config['module_layouts'][$moduleNamespace][$actionName])) {
                $layout = $controller->layout($config['module_layouts'][$moduleNamespace][$actionName]); 
            }elseif(isset($config['module_layouts'][$moduleNamespace]['default'])) {
                $layout = $controller->layout($config['module_layouts'][$moduleNamespace]['default']);
            }

        }, 100);
    }
	
	public function bootstrapSession($e){
        $session = $e->getApplication()
                     ->getServiceManager()
                     ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {
             $session->regenerateId(true);
             $container->init = 1;
        }
    }
	
	public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
	// Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'User\Model\UserTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserTable($dbAdapter);
                    return $table;
                },
				'User\Model\UserProfileTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserProfileTable($dbAdapter);
                    return $table;
                },
				'Country\Model\CountryTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new CountryTable($dbAdapter);
                    return $table;
                },
				'Tag\Model\TagTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new tagTable($dbAdapter);
                    return $table;
                },
				'Tag\Model\GroupTagTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new groupTagTable($dbAdapter);
                    return $table;
                },
				'Tag\Model\UserTagTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new userTagTable($dbAdapter);
                    return $table;
                },
				'Groups\Model\GroupsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new GroupsTable($dbAdapter);
                    return $table;
                },
				'Groups\Model\UserGroupTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserGroupTable($dbAdapter);
                    return $table;
                },
				'Notification\Model\UserNotificationTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserNotificationTable($dbAdapter);
                    return $table;
                },
				'Notification\Model\NotificationTypeTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new NotificationTypeTable($dbAdapter);
                    return $table;
                },
				'Photo\Model\PhotoTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new PhotoTable($dbAdapter);
                    return $table;
                },
				'Groups\Model\UserGroupAddSuggestionTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserGroupAddSuggestionTable($dbAdapter);
                    return $table;
                },
               'Admin\Model\AdminTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new AdminTable($dbAdapter);
                    return $table;
                },
				'Activity\Model\ActivityTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new activityTable($dbAdapter);
                    return $table;
                },
               
				//session start
				'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config['session'])) {
                        $session = $config['session'];

                        $sessionConfig = null;
                        if (isset($session['config'])) {
                            $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = null;
                        if (isset($session['storage'])) {
                            $class = $session['storage'];
                            $sessionStorage = new $class();
                        }

                        $sessionSaveHandler = null;
                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);
                        if (isset($session['validator'])) {
                            $chain = $sessionManager->getValidatorChain();
                            foreach ($session['validator'] as $validator) {
                                $validator = new $validator();
                                $chain->attach('session.validate', array($validator, 'isValid'));

                            }
                        }
                    } else {
                        $sessionManager = new SessionManager();
                    }
                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                	},				
				//session ends
            ),
        );
    }
	
}