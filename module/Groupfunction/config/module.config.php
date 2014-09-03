<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Groupfunction\Controller\Groupfunction' => 'Groupfunction\Controller\GroupfunctionController',
        ),
    ),
    // The following section is new and should be added to your file
       'router' => array(
        'routes' => array(
            'groupfunction' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/grouprole',
                    'defaults' => array(
						'__NAMESPACE__' => 'Groupfunction\Controller',
                        'controller' => 'groupfunction',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'groupfunctions' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/groupfunctions',
                             
							'defaults' => array(
								'__NAMESPACE__' => 'Groupfunction\Controller',
								'controller' => 'groupfunction',
								'action'     => 'groupfunctions',
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

