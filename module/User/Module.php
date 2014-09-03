<?php
namespace User;
use User\Model\UserTable;
use User\Model\UserProfileTable;
use User\Model\RecoveryemailsTable;
use Country\Model\CountryTable;
use Groups\Model\GroupsTable;
use Groups\Model\UserGroupTable;
use Activity\Model\ActivityTable;
use Photo\Model\PhotoTable;
use Tag\Model\TagTable;
use Tag\Model\UserTagTable;
use User\Model\UserFriendTable;
use User\Model\UserFriendRequestTable;
use User\Model\UserGroupSettingsTable;
use User\Model\UserGeneralSettingsTable;
use User\Model\UserProfilePhotoTable;
use User\Model\UserCoverPhotoTable;
use User\Model\UserProfileSettingsTable;

class Module
{
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
				'User\Model\RecoveryemailsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new RecoveryemailsTable($dbAdapter);
                    return $table;
                },
				'Country\Model\CountryTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new CountryTable($dbAdapter);
                    return $table;
                },
				'Planet\Model\PlanetTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new PlanetTable($dbAdapter);
					return $table;
                },
				'Group\Model\UserGroupTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserGroupTable($dbAdapter);
					return $table;
                },
				'Activity\Model\ActivityTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ActivityTable($dbAdapter);
					return $table;
                },
				'Photo\Model\PhotoTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new PhotoTable($dbAdapter);
					return $table;
                },
				'Tag\Model\TagTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new TagTable($dbAdapter);
					return $table;
                },
				'Tag\Model\UserTagTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserTagTable($dbAdapter);
					return $table;
                },
				'User\Model\UserFriendTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserFriendTable($dbAdapter);
					return $table;
                },
				'User\Model\UserFriendRequestTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserFriendRequestTable($dbAdapter);
					return $table;
                },
				'User\Model\UserGroupSettingsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserGroupSettingsTable($dbAdapter);
					return $table;
                },
				'User\Model\UserGeneralSettingsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserGeneralSettingsTable($dbAdapter);
					return $table;
                },
				'User\Model\UserProfilePhotoTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserProfilePhotoTable($dbAdapter);
					return $table;
                },
				'User\Model\UserCoverPhotoTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserCoverPhotoTable($dbAdapter);
					return $table;
                },
				'User\Model\UserProfileSettingsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserProfileSettingsTable($dbAdapter);
					return $table;
                },
			),
        );
    }    

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}