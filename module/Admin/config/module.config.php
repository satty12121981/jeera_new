<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Admin' => 'Admin\Controller\AdminController',
			'Admin\Controller\AdminTag' => 'Admin\Controller\AdminTagController',
			'Admin\Controller\AdminUserTag' => 'Admin\Controller\AdminUserTagController',
			'Admin\Controller\AdminPlanetTag' => 'Admin\Controller\AdminPlanetTagController',
			'Admin\Controller\AdminActivityTag' => 'Admin\Controller\AdminActivityTagController',
			'Admin\Controller\AdminGalaxy' => 'Admin\Controller\AdminGalaxyController',
			'Admin\Controller\AdminPlanet' => 'Admin\Controller\AdminPlanetController',
			'Admin\Controller\AdminActivity' => 'Admin\Controller\AdminActivityController',
        ),
    ),	 
	'controller_plugins' => array(
        'invokables' => array(
             
     'ResizePlugin' => 'Album\Controller\Plugin\ResizePlugin',
   
        ),
    ),
	 // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'jadmin' => array(
                'type'    => 'Literal',
				 'priority' => 1000,
                'options' => array(
                    'route'    => '/jadmin',
                   'defaults' => array(
						'__NAMESPACE__' => 'Admin\Controller',
                        'controller' => 'admin',
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
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admin',
                                'action'     => 'login',
                            ),
                        ),
                    ), 
					 'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admin',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
					 'admin-tags' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/tags[/:page][/:sort][/:order][/:search]',
							'constraints' => array(
								'page' => '[0-9]+',
								'sort'     => '[a-zA-Z0-9_-]*',
								'order'     => '[a-zA-Z0-9_-]*',
								'search' => '[a-zA-Z0-9_-]*',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admintag',
                                'action'     => 'index',
                            ),
                        ),
                    ),
					'admin-tags-add' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/tags/add',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admintag',
                                'action'     => 'add',
                            ),
                        ),
                    ),
					'admin-tags-edit' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/tags/edit/[:id]',
							'constraints' => array(
								 'id'=>'[0-9]+'
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admintag',
                                'action'     => 'edit',
                            ),
                        ),
                    ),
					'admin-tags-delete' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/tags/delete/[:id]',
							'constraints' => array(
								 'id'=>'[0-9]+'
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admintag',
                                'action'     => 'delete',
                            ),
                        ),
                    ),
					'admin-user-tags' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/usertags[/:page][/:sort][/:order][/:search]',
							'constraints' => array(
								'page' => '[0-9]+',
								'sort'     => '[a-zA-Z0-9_-]*',
								'order'     => '[a-zA-Z0-9_-]*',
								'search' => '[a-zA-Z0-9_-]*',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminusertag',
                                'action'     => 'index',
                            ),
                        ),
                    ),
					'admin-user-tags-view' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/usertags/view/[:id]',
							'constraints' => array(
								 'id'=>'[0-9]+'
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminusertag',
                                'action'     => 'view',
                            ),
                        ),
                    ),
					'admin-user-tags-delete' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/usertags/delete',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminusertag',
                                'action'     => 'delete',
                            ),
                        ),
                    ),
					'admin-user-tags-getTagList' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/usertags/getTagList',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminusertag',
                                'action'     => 'getTagList',
                            ),
                        ),
                    ),
					'admin-user-tags-addUserTag' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/usertags/addUserTag',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminusertag',
                                'action'     => 'addUserTag',
                            ),
                        ),
                    ),
					'admin-planet-tags' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planettags[/:page][/:sort][/:order][/:search]',
							'constraints' => array(
								'page' => '[0-9]+',
								'sort'     => '[a-zA-Z0-9_-]*',
								'order'     => '[a-zA-Z0-9_-]*',
								'search' => '[a-zA-Z0-9_-]*',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanettag',
                                'action'     => 'index',
                            ),
                        ),
                    ),					
					'admin-palnet-tags-view' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planettags/view/[:id]',
							'constraints' => array(
								 'id'=>'[0-9]+'
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanettag',
                                'action'     => 'view',
                            ),
                        ),
                    ),
					'admin-palnet-tags-delete' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planettags/delete',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanettag',
                                'action'     => 'delete',
                            ),
                        ),
                    ),
					'admin-palnet-tags-getTagList' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planettags/getTagList',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanettag',
                                'action'     => 'getTagList',
                            ),
                        ),
                    ),
					'admin-palnet-tags-addGroupTag' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planettags/addGroupTag',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanettag',
                                'action'     => 'addGroupTag',
                            ),
                        ),
                    ),
					'admin-activity-tags' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activitytags[/:page][/:sort][/:order][/:search]',
							'constraints' => array(
								'page' => '[0-9]+',
								'sort'     => '[a-zA-Z0-9_-]*',
								'order'     => '[a-zA-Z0-9_-]*',
								'search' => '[a-zA-Z0-9_-]*',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivitytag',
                                'action'     => 'index',
                            ),
                        ),
                    ),
					'admin-activity-tags-view' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activitytags/view/[:id]',
							'constraints' => array(
								 'id'=>'[0-9]+'
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivitytag',
                                'action'     => 'view',
                            ),
                        ),
                    ),
					'admin-activity-tags-getTagList' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activitytags/getTagList',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivitytag',
                                'action'     => 'getTagList',
                            ),
                        ),
                    ),
					'admin-activity-tags-delete' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activitytags/delete',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivitytag',
                                'action'     => 'delete',
                            ),
                        ),
                    ),
					'admin-activity-tags-addActivityTag' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activitytags/addActivityTag',
							'constraints' => array(								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivitytag',
                                'action'     => 'addActivityTag',
                            ),
                        ),
                    ),
					'admin-galaxy' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/galaxy[/:page][/:sort][/:order][/:search]',
							'constraints' => array(
								'page' => '[0-9]+',
								'sort'     => '[a-zA-Z0-9_-]*',
								'order'     => '[a-zA-Z0-9_-]*',
								'search' => '[a-zA-Z0-9_-]*',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admingalaxy',
                                'action'     => 'index',
                            ),
                        ),
                    ),
					'admin-galaxy-add' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/galaxy/add',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admingalaxy',
                                'action'     => 'add',
                            ),
                        ),
                    ),
					'admin-galaxy-edit' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/galaxy/edit[/:id]',
							'constraints' => array(
								'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admingalaxy',
                                'action'     => 'edit',
                            ),
                        ),
                    ),
					'admin-galaxy-getSeoTitle' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/galaxy/getSeoTitle',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admingalaxy',
                                'action'     => 'getSeoTitle',
                            ),
                        ),
                    ),
					'admin-galaxy-delete' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/galaxy/delete[/:id]',
							'constraints' => array(
								 'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'admingalaxy',
                                'action'     => 'delete',
                            ),
                        ),
                    ),
					'admin-planet' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet[/:galaxy][/:page][/:sort][/:order][/:search]',
							'constraints' => array(
								'galaxy' => '[a-zA-Z0-9_-]*',
								'page' => '[0-9]+',
								'sort'     => '[a-zA-Z0-9_-]*',
								'order'     => '[a-zA-Z0-9_-]*',
								'search' => '[a-zA-Z0-9_-]*',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'index',
                            ),
                        ),
                    ),
					'admin-planet-add' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/add',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'add',
                            ),
                        ),
                    ),
					'admin-planet-edit' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/edit[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'edit',
                            ),
                        ),
                    ),
					'admin-planet-view' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/view[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'view',
                            ),
                        ),
                    ),
					'admin-planet-add-questions' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/addQuestions[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'addQuestions',
                            ),
                        ),
                    ),	
					'admin-planet-edit-questions' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/editQuestions[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'editQuestions',
                            ),
                        ),
                    ),
					'admin-planet-status-questions' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/statusQuestions[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'statusQuestions',
                            ),
                        ),
                    ),
					'admin-planet-delete-questions' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/deleteQuestions[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'deleteQuestions',
                            ),
                        ),
                    ),
					'admin-planet-owners' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/ajaxOwnersList',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'ajaxOwnersList',
                            ),
                        ),
                    ),
					'admin-planet-delete' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/delete[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'delete',
                            ),
                        ),
                    ),
					'removeAlbumDetails' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/removeAlbumDetails',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'removeAlbumDetails',
                            ),
                        ),
                    ),
					'removeDiscussionDetails' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/removeDiscussionDetails',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'removeDiscussionDetails',
                            ),
                        ),
                    ),
					'removeActivities' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/removeActivities',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'removeActivities',
                            ),
                        ),
                    ),					
					'removeMembers' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/removeMembers',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'removeMembers',
                            ),
                        ),
                    ),
					'removeQuestionnaire' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/removeQuestionnaire',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'removeQuestionnaire',
                            ),
                        ),
                    ),
					'removeTags' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/removeTags',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'removeTags',
                            ),
                        ),
                    ),
					'removeSettings' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/removeSettings',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'removeSettings',
                            ),
                        ),
                    ),
					'removeGroup' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/removeGroup',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'removeGroup',
                            ),
                        ),
                    ),
					'admin-planet-approvelist'=>array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/approvelist[/:galaxy][/:page][/:sort][/:order][/:search]',
							'constraints' => array(
								'galaxy' => '[a-zA-Z0-9_-]*',
								'page' => '[0-9]+',
								'sort'     => '[a-zA-Z0-9_-]*',
								'order'     => '[a-zA-Z0-9_-]*',
								'search' => '[a-zA-Z0-9_-]*',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'approvelist',
                            ),
                        ),
                    ),
					'admin-planet-approve'=>array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/planet/approve[/:planet_id]',
							'constraints' => array(
								'planet_id' => '[0-9]+',								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminplanet',
                                'action'     => 'approve',
                            ),
                        ),
                    ),
					'admin-activity' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity[/:galaxy][/:planet][/:page][/:sort][/:order][/:search]',
							'constraints' => array(
								'galaxy' => '[a-zA-Z0-9_-]*',
								'page' => '[0-9]+',
								'sort'     => '[a-zA-Z0-9_-]*',
								'order'     => '[a-zA-Z0-9_-]*',
								'search' => '[a-zA-Z0-9_-]*',
								'planet' => '[a-zA-Z0-9_-]*',
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'index',
                            ),
                        ),
                    ),
					'admin-activity-view' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/view[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'view',
                            ),
                        ),
                    ),
					'admin-activity-view' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/view[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'view',
                            ),
                        ),
                    ),
					'getActivityMembers' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/getActivityMembers',
							'constraints' => array(
								 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'getActivityMembers',
                            ),
                        ),
                    ),
					'admin-activity-block' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/block[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'block',
                            ),
                        ),
                    ),
					'admin-activity-delete' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/delete[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'delete',
                            ),
                        ),
                    ),
					'removeActivityLikesAndComments' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/removeActivityLikesAndComments[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'removeActivityLikesAndComments',
                            ),
                        ),
                    ),
					'removeActivityTags' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/removeActivityTags[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'removeActivityTags',
                            ),
                        ),
                    ),
					'removeActivityMembers' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/removeActivityMembers[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'removeActivityMembers',
                            ),
                        ),
                    ),
					'removeActivity' => array(
                        'type' => 'segment',
                        'options' => array(
                           	'route'    => '/activity/removeActivity[/:id]',
							'constraints' => array(
								  'id'=>'[0-9]+' 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Admin\Controller',
                                'controller' => 'adminactivity',
                                'action'     => 'removeActivity',
                            ),
                        ),
                    ),
					
					
				),
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'admin/layout'           => __DIR__ . '/../view/layout/admin_layout.phtml',        
            
        ),

        'template_path_stack' => array(
            'admin' => __DIR__ . '/../view',
        ),
    ),
);