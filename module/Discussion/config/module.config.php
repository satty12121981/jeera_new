<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Discussion\Controller\Discussion' => 'Discussion\Controller\DiscussionController',
        ),
    ),

    // The following section routes the discussion controller
    'router' => array(
        'routes' => array(
            'discussion' => array(
					'type' => 'Literal',
					'priority' => 1000,
					'options' => array(
						'route'    => '/discussion',
						'defaults' => array(
							'__NAMESPACE__' => 'Discussion\Controller',
							'controller' => 'discussion',
							'action'     => 'index',
						),
					),
			'may_terminate' => true,
                'child_routes' => array(
					'loadmore' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/loadmore/[:planet_id]',
                            'constraints' => array(
								'planet_id' => '[a-zA-Z0-9_-]*',											 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Discussion\Controller',
								'controller' => 'discussion',
								'action'     => 'loadmore',
							),
                        ),					 
                    ),	
					'ajaxAddDiscussion' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:group_id]/[:planet_id]/ajaxAddDiscussion',
                            'constraints' => array(
								'group_id' => '[a-zA-Z0-9_-]*',
								'planet_id' => '[a-zA-Z0-9_-]*',										 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Discussion\Controller',
								'controller' => 'discussion',
								'action'     => 'ajaxAddDiscussion',
							),
                        ),					 
                    ),
					'ajaxEditDiscussion' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:group_id]/[:planet_id]/ajaxEditDiscussion',
                            'constraints' => array(
								'group_id' => '[a-zA-Z0-9_-]*',
								'planet_id' => '[a-zA-Z0-9_-]*',										 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Discussion\Controller',
								'controller' => 'discussion',
								'action'     => 'ajaxEditDiscussion',
							),
                        ),					 
                    ),
					'ajaxDeleteDiscussion' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:group_id]/[:planet_id]/ajaxDeleteDiscussion',
                            'constraints' => array(
								'group_id' => '[a-zA-Z0-9_-]*',
								'planet_id' => '[a-zA-Z0-9_-]*',										 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Discussion\Controller',
								'controller' => 'discussion',
								'action'     => 'ajaxDeleteDiscussion',
							),
                        ),					 
                    ),
					'loadmoreDiscussion' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/loadmoreDiscussion',
                            
							'defaults' => array(
								'__NAMESPACE__' => 'Discussion\Controller',
								'controller' => 'discussion',
								'action'     => 'loadmoreDiscussion',
							),
                        ),					 
                    ),
					),
                ),
            ),
        ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'discussion' => __DIR__ . '/../view',
        ),
    ),
);

