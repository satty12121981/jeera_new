<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=y2m_jeera_new;host=localhost',
		'username'       => 'root',
        'password'       => 'chandra',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
	'pathInfo' => array(
         'ROOTPATH'         => "/home/y2m/public_html/development/jeera_new/",	
		'UploadPath'       => "/home/y2m/public_html/development/jeera_new/public/datagd/",
		'AlbumUploadPath'       => "/home/y2m/public_html/development/jeera_new/public/album/",
		'base_url' =>"http://y2m.ae/development/jeera_new/",
		 'fbredirect' =>"http://y2m.ae/development/jeera_new/user/fbredirect",
		 
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
	'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'myapp',
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            array(
                'Zend\Session\Validator\RemoteAddr',
                'Zend\Session\Validator\HttpUserAgent',
            ),
        ),
    ),
);
