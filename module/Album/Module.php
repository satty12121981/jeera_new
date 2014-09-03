<?php
// module/Album/Module.php
namespace Album;
// Add this import statement:
use Album\Model\AlbumTable;
use Album\Model\AlbumDataTable;
use Album\Model\AlbumTagTable;
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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
	    // getAutoloaderConfig() and getConfig() methods here

    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
			 'Album\Model\Album' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new Album($dbAdapter);
                    return $table;
                },
				 'Album\Model\AlbumData' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new AlbumData($dbAdapter);
                    return $table;
                },
				'Album\Model\AlbumTag' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new AlbumTag($dbAdapter);
                    return $table;
                },
                'Album\Model\AlbumTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new AlbumTable($dbAdapter);
                    return $table;
                },
				'Album\Model\AlbumDataTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new AlbumDataTable($dbAdapter);
                    return $table;
                },
				'Album\Model\AlbumTagTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new AlbumTagTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }
}