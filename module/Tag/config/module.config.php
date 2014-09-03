<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Tag\Controller\Tag' => 'Tag\Controller\TagController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'tag' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/tag',
                    'defaults' => array(
						'__NAMESPACE__' => 'Tag\Controller',
                        'controller' => 'tag',
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
								'__NAMESPACE__' => 'Tag\Controller',
                                'controller' => 'tag',
                                'action'     => 'login',
                            ),
                        ),
                    ), 
					'listExceptSelected' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/listExceptSelected',
                            'defaults' => array(
                                'controller' => 'tag',
                                'action'     => 'listExceptSelected',
                            ),
                        ),
                    ),
					'searchTags' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/searchTags',
                            'defaults' => array(
                                'controller' => 'tag',
                                'action'     => 'searchTags',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'tag',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/register',
                            'defaults' => array(
                                'controller' => 'tag',
                                'action'     => 'register',
                            ),
                        ),
                    ),
                    'changepassword' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-password',
                            'defaults' => array(
                                'controller' => 'tag',
                                'action'     => 'changepassword',
                            ),
                        ),                        
                    ),
                    'changeemail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-email',
                            'defaults' => array(
                                'controller' => 'tag',
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
            'tag' => __DIR__ . '/../view',
        ),
    ),
);

