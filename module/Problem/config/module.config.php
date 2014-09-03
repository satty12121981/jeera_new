<?php
return array(
    'controllers' => array(
        'invokables' => array(
            //'Discussion\Controller\Discussion' => 'Discussion\Controller\DiscussionController',
			//'Activity\Controller\Activity' => 'Activity\Controller\ActivityController',
			//'Spam\Controller\Spam' => 'Spam\Controller\SpamController',
			'Problem\Controller\Problem' => 'Problem\Controller\ProblemController',
			//'Group\Controller\Group' => 'Group\Controller\GroupController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'spam' => array(
					'type' => 'Literal',
					'priority' => 1000,
					'options' => array(
						'route'    => '/group',
						'defaults' => array(
							'__NAMESPACE__' => 'Spam\Controller',
							'controller' => 'Spam',
							'action'     => 'index',
						),
					),
					'may_terminate' => true,
					'child_routes' => array(
						'spam' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/[:group_id]/[:sub_group_id]/[:system_type_id]/spams/[:group_refer_id]',
									'constraints' => array(
										'group_id' => '[a-zA-Z0-9_-]*',
										'sub_group_id' => '[a-zA-Z0-9_-]*',
										'system_type_id' => '[a-zA-Z0-9_-]*',
										'group_refer_id' => '[a-zA-Z0-9_-]*',											
									),
								'defaults' => array(
									'__NAMESPACE__' => 'Spam\Controller',
									'controller' => 'Spam',
									'action'     => 'Spams',
								),
							),
						),
					),

                ),
            ),
        ),

    'view_manager' => array(
        'template_path_stack' => array(
            'Spam' => __DIR__ . '/../view',
        ),
    ),
);

