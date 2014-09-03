<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Country\Controller\Country' => 'Country\Controller\CountryController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'country' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/country',
                    'defaults' => array(
						'__NAMESPACE__' => 'Country\Controller',
                        'controller' => 'country',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
								'__NAMESPACE__' => 'Country\Controller',
                                'controller' => 'country',
                                'action'     => 'login',
                            ),
                        ),
                    ),                    
                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'country',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/register',
                            'defaults' => array(
                                'controller' => 'country',
                                'action'     => 'register',
                            ),
                        ),
                    ),
                    'changepassword' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-password',
                            'defaults' => array(
                                'controller' => 'country',
                                'action'     => 'changepassword',
                            ),
                        ),                        
                    ),
                    'changeemail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-email',
                            'defaults' => array(
                                'controller' => 'country',
                                'action' => 'changeemail',
                            ),
                        ),                        
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'country' => __DIR__ . '/../view',
        ),
    ),
);

