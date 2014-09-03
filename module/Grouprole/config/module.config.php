<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Grouprole\Controller\Grouprole' => 'Grouprole\Controller\GrouproleController',
        ),
    ),
    // The following section is new and should be added to your file
       'router' => array(
        'routes' => array(
            'grouprole' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/grouprole',
                    'defaults' => array(
						'__NAMESPACE__' => 'Grouprole\Controller',
                        'controller' => 'grouprole',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'grouproles' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/grouproles',
                             
							'defaults' => array(
								'__NAMESPACE__' => 'Grouprole\Controller',
								'controller' => 'grouprole',
								'action'     => 'grouproles',
							),
                        ),					 
                    ),
					 'rolelist' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/rolelist',
                             
							'defaults' => array(
								'__NAMESPACE__' => 'Grouprole\Controller',
								'controller' => 'grouprole',
								'action'     => 'rolelist',
							),
                        ),					 
                    ),
					
					 
                ),
            ),
        ),
    ),


    'view_manager' => array(
        'template_path_stack' => array(
            'role' => __DIR__ . '/../view',
        ),
    ),
);

