<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Activity\Controller\Activity' => 'Activity\Controller\ActivityController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'activity' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/activity',
                    'defaults' => array(
						'__NAMESPACE__' => 'Activity\Controller',
                        'controller' => 'activity',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'activity-group' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/group/[:group_id]',
                            'constraints' => array(
								'group_id' => '[a-zA-Z0-9_-]*',											 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'index',
							),
                        ),					 
                    ),
					'activity-rsvp' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/rsvp',
                            'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'activityrsvp',
							),
                        ),					 
                    ),
					'quit-rsvp' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/quitrsvp',
                            'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'quitrsvp',
							),
                        ),					 
                    ),
					
					'activity-view' => array(
                        'type' => 'segment',
						'options' => array(
                           	'route'    => '/[:group_id]/[:planet_id][/:id]',
							'constraints' => array(
								'group_id' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'planet_id' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[0-9]+',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
                                'controller' => 'activity',
                                'action'     => 'view',
                            ),
                        ),
                      					 
                    ),
					'activity-calendar' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/calendar',
                            'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'calendar',
							),
                        ),					 
                    ),
					'join' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/join/[:planet_id]',
                            'constraints' => array(
								'planet_id' => '[a-zA-Z0-9_-]*',											 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'join',
							),
                        ),					 
                    ),
					'loadmore' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/loadpastevents/[:planet_id]',
                            'constraints' => array(
								'planet_id' => '[a-zA-Z0-9_-]*',											 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'loadpastevents',
							),
                        ),					 
                    ),
					'loadmoreActivity' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/loadmoreActivity',
                            'constraints' => array(
								 							 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'loadmoreActivity',
							),
                        ),					 
                    ),
					'memberslist' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/memberslist',
                            'constraints' => array(
								 							 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'memberslist',
							),
                        ),					 
                    ),
					'popular' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/popular',
                            'constraints' => array(
								 									 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'popular',
							),
                        ),					 
                    ),
					'ajaxLoadActivity' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/ajaxLoadActivity',
                            'constraints' => array(
								 									 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'ajaxLoadActivity',
							),
                        ),					 
                    ),
					'OneDayOnly' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/OneDayOnly',
                            'constraints' => array(
								 									 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Activity\Controller',
								'controller' => 'activity',
								'action'     => 'OneDayOnly',
							),
                        ),					 
                    ),
					'ajaxCreateActivity' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxCreateActivity',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Activity\Controller',
											'controller' => 'activity',
											'action'     => 'ajaxCreateActivity',
										),
									),
								),
					'ajaxLoadMoreMembers' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/ajaxLoadMoreMembers',										
											'constraints' => array(
												 							 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Activity\Controller',
											'controller' => 'activity',
											'action'     => 'ajaxLoadMoreMembers',
										),
									),
								),
					'rsvpRemove' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/rsvpRemove',										
											'constraints' => array(
												 							 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Activity\Controller',
											'controller' => 'activity',
											'action'     => 'rsvpRemove',
										),
									),
								),
					'ajaxEditActivity' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxEditActivity',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Activity\Controller',
											'controller' => 'activity',
											'action'     => 'ajaxEditActivity',
										),
									),
								),
					'ajaxDeleteActivity' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxDeleteActivity',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Activity\Controller',
											'controller' => 'activity',
											'action'     => 'ajaxDeleteActivity',
										),
									),
								),
					'approveActivity' => array(
					 'type' => 'segment',
					 'options' => array(
					  'route' => '/[:group_id]/[:sub_group_id]/approveActivity',          
					   'constraints' => array(
						'group_id' => '[a-zA-Z0-9_-]*', 
						'sub_group_id' => '[a-zA-Z0-9_-]*',           
					   ),
					  'defaults' => array(
					   '__NAMESPACE__' => 'activity\Controller',
					   'controller' => 'activity',
					   'action'     => 'approveActivity',
					  ),
					 ),
					),
					'IgnoreActivity' => array(
					 'type' => 'segment',
					 'options' => array(
					  'route' => '/[:group_id]/[:sub_group_id]/IgnoreActivity',          
					   'constraints' => array(
						'group_id' => '[a-zA-Z0-9_-]*', 
						'sub_group_id' => '[a-zA-Z0-9_-]*',           
					   ),
					  'defaults' => array(
					   '__NAMESPACE__' => 'activity\Controller',
					   'controller' => 'activity',
					   'action'     => 'IgnoreActivity',
					  ),
					 ),
					),
					'removeActivity' => array(
					 'type' => 'segment',
					 'options' => array(
					  'route' => '/[:group_id]/[:sub_group_id]/removeActivity',          
					   'constraints' => array(
						'group_id' => '[a-zA-Z0-9_-]*', 
						'sub_group_id' => '[a-zA-Z0-9_-]*',           
					   ),
					  'defaults' => array(
					   '__NAMESPACE__' => 'activity\Controller',
					   'controller' => 'activity',
					   'action'     => 'removeActivity',
					  ),
					 ),
					),			
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'activity' => __DIR__ . '/../view',
        ),
    ),
);
