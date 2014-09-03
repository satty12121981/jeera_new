<?php
return array(
    'router' => array(
        'routes' => array(
            'galaxy' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'priority' => 1000,
                'options' => array(
                    'route'    => '/galaxy',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
			'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
				'priority' => 1000,
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'User\Controller\UserProfile',
                        'action'     => 'feeds',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
					'quicksearch' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/quicksearch',
                            'defaults' => array(
								'__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Index',
                                'action'     => 'quicksearch',
                            ),
                        ),
                    ),
					'ajaxLoadMore' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ajaxLoadMore',
                            'defaults' => array(
								'__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Index',
                                'action'     => 'ajaxLoadMore',
                            ),
                        ),
                    ),
					'search' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/search/[:search_str]',
							 'constraints' => array(
                                'search_str' => '[a-zA-Z][a-zA-Z0-9_-]*',                                 
                            ),
                            'defaults' => array(
								'__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Index',
                                'action'     => 'search',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            //'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            //'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
			'Application\Controller\GenericPlugin' => 'Application\Controller\GenericPlugin',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
