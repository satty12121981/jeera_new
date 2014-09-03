<?php
return array(
    'controllers' => array(
        'invokables' => array(
			'Message\Controller\Message' => 'Message\Controller\MessageController',
        ),
    ),

    // The following section is router to route controller
    'router' => array(
        'routes' => array(
            'messages' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/messages',
                    'defaults' => array(
						'__NAMESPACE__' => 'Message\Controller',
                        'controller' => 'message',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
					'messageshome' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/messageshome',
                            'defaults' => array(
								'__NAMESPACE__' => 'Message\Controller',
                                'controller' => 'message',
                                'action'     => 'messageshome',
                            ),
                        ),
                    ),
                    'messagesend' => array(
                        'type' => 'Segment',
                        'options' => array(
                             'route' => '/messagesend',
                             'constraints' => array(
                                 
                             ),
                             'defaults' => array(
                                 '__NAMESPACE__' => 'Message\Controller',
                                 'controller' => 'message',
                                 'action'     => 'messagesend',
                             ),
                        ),
                     ),
                    'messageprocess' => array(
                         'type' => 'Segment',
                         'options' => array(
                             'route' => '/messageprocess/[:usergivenname]',
                             'constraints' => array(
                                 'usergivenname' => '[a-zA-Z0-9_-]*',
                             ),
                             'defaults' => array(
                                 '__NAMESPACE__' => 'Message\Controller',
                                 'controller' => 'message',
                                 'action'     => 'messageprocess',
                             ),
                         ),
                     ),
                    'messagearea' => array(
                         'type' => 'Segment',
                         'options' => array(
                             'route' => '/messagearea/[:usergivenname]',
                             'constraints' => array(
                                 'usergivenname' => '[a-zA-Z0-9_-]*',
                             ),
                             'defaults' => array(
                                 '__NAMESPACE__' => 'Message\Controller',
                                 'controller' => 'message',
                                 'action'     => 'messagearea',
                             ),
                         ),
                     ),
                    'messageslist' => array(
                         'type' => 'Segment',
                         'options' => array(
                             'route' => '/messageslist',
                             'constraints' => array(
                                  
                             ),
                             'defaults' => array(
                                 '__NAMESPACE__' => 'Message\Controller',
                                 'controller' => 'message',
                                 'action'     => 'messageslist',
                             ),
                         ),
                    ),
					'ajxLoadmoreMessage' => array(
                         'type' => 'Segment',
                         'options' => array(
                             'route' => '/ajxLoadmoreMessage',
                             'constraints' => array(
                                  
                             ),
                             'defaults' => array(
                                 '__NAMESPACE__' => 'Message\Controller',
                                 'controller' => 'message',
                                 'action'     => 'ajxLoadmoreMessage',
                             ),
                         ),
                    ),
                    'messageimagedelete' => array(
                         'type' => 'Segment',
                         'options' => array(
                             'route' => '/messageimagedelete/[:usergivenname]',
                             'constraints' => array(
                                 'usergivenname' => '[a-zA-Z0-9_-]*',
                             ),
                             'defaults' => array(
                                 '__NAMESPACE__' => 'Message\Controller',
                                 'controller' => 'message',
                                 'action'     => 'messageimagedelete',
                             ),
                         ),
                    ),
                    'messagesdelete' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/messagesdelete',
                            'constraints' => array(
                             
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Message\Controller',
                                'controller' => 'message',
                                'action'     => 'messagesdelete',
                            ),
                        ),
                    ),
					'usersearch' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/usersearch',
                            'constraints' => array(
                             
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Message\Controller',
                                'controller' => 'message',
                                'action'     => 'usersearch',
                            ),
                        ),
                    ),
					'ajxLoadmoreUsers' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/ajxLoadmoreUsers',
                            'constraints' => array(
                             
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Message\Controller',
                                'controller' => 'message',
                                'action'     => 'ajxLoadmoreUsers',
                            ),
                        ),
                    ),
					'ajaxMessageCount' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/ajaxMessageCount',
                            'constraints' => array(
								 									 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Message\Controller',
								'controller' => 'message',
								'action'     => 'ajaxMessageCount',
							),
                        ),					 
                    ),
					'ajaxGetMessageNotification' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/ajaxGetMessageNotification',
                            'constraints' => array(
								 									 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Message\Controller',
								'controller' => 'message',
								'action'     => 'ajaxGetMessageNotification',
							),
                        ),					 
                    ),
					'sendMessages' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/sendMessages',
                            'constraints' => array(
								 									 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Message\Controller',
								'controller' => 'message',
								'action'     => 'sendMessages',
							),
                        ),					 
                    ),
                ),
			),
		),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'message' => __DIR__ . '/../view',
        ),
    ),
);

