<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Groups\Controller\Groups' => 'Groups\Controller\GroupsController',
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
            'groups' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/groups',
                   'defaults' => array(
						'__NAMESPACE__' => 'Groups\Controller',
                        'controller' => 'groups',
                        'action'     => 'index',
                    ),
                ),
				'may_terminate' => true,
				'child_routes' => array(
					'planet' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'planet',
										),
									),
								),
					'planethome' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:planet_id]',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',
												'planet_id' => '[a-zA-Z0-9_-]*',												
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'planethome',
										),
									),
								),
					'planetsettings' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:planet_id]/ajaxSettings',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',
												'planet_id' => '[a-zA-Z0-9_-]*',												
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxSettings',
										),
									),
								),
					'join' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:planet_id]/join',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',
												'planet_id' => '[a-zA-Z0-9_-]*',												
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'join',
										),
									),
								),
					'group-discussion' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/discussion',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Discussion\Controller',
											'controller' => 'discussion',
											'action'     => 'index',
										),
									),
								),
					'group-members' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:group_id]/[:sub_group_id]/members',
                            'constraints' => array(
                                'group_id' => '[a-zA-Z0-9_-]*',
                                'sub_group_id' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Groups\Controller',
                                'controller' => 'groups',
                                'action'     => 'loadPlanetMembers',
                            ),
                        ),
                    ),'ajaxLoadMoreMembers' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/[:group_id]/[:sub_group_id]/ajaxLoadMoreMembers',
                            'constraints' => array(
                                'group_id' => '[a-zA-Z0-9_-]*',
                                'sub_group_id' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Groups\Controller',
                                'controller' => 'groups',
                                'action'     => 'ajaxLoadMoreMembers',
                            ),
                        ),
                    ),
					'ajaxLoadGalaxy' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ajaxLoadGalaxy',
                            'defaults' => array(
								'__NAMESPACE__' => 'Groups\Controller',
								'controller' => 'groups',
								'action'     => 'ajaxLoadGalaxy',
							),
                        ),					 
                    ),
				'ajaxPlanetSearch' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ajaxPlanetSearch',
                            'defaults' => array(
								'__NAMESPACE__' => 'Groups\Controller',
								'controller' => 'groups',
								'action'     => 'ajaxPlanetSearch',
							),
                        ),					 
                    ),
				'ajaxPlanetSort' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ajaxPlanetSort',
                            'defaults' => array(
								'__NAMESPACE__' => 'Groups\Controller',
								'controller' => 'groups',
								'action'     => 'ajaxPlanetSort',
							),
                        ),					 
                    ),
				'ajaxLoadPlanet' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ajaxLoadPlanet',
                            'defaults' => array(
								'__NAMESPACE__' => 'Groups\Controller',
								'controller' => 'groups',
								'action'     => 'ajaxLoadPlanet',
							),
                        ),					 
                    ),
					'ajaxMembersList' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxMembersList',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxMembersList',
										),
									),
								),
					'ajaxAddRoles' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxAddRoles',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxAddRoles',
										),
									),
								),
					'ajaxPermissions' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxPermissions',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxPermissions',
										),
									),
								),
					'ajaxSavePermissions' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxSavePermissions',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxSavePermissions',
										),
									),
								),
					'ajaxDiscussionSettings' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxDiscussionSettings',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxDiscussionSettings',
										),
									),
								),
					'ajaxActivitySettings' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxActivitySettings',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxActivitySettings',
										),
									),
								),
					'ajaxMemberSettings' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxMemberSettings',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxMemberSettings',
										),
									),
								),
					'ajaxPrivacySettings' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxPrivacySettings',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxPrivacySettings',
										),
									),
								),
					'ajaxSaveGroupTags' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxSaveGroupTags',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxSaveGroupTags',
										),
									),
								),
						'ajaxVisibilitySettings' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxVisibilitySettings',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxVisibilitySettings',
										),
									),
								),
						'ajaxGroupTags' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxGroupTags',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxGroupTags',
										),
									),
								),
							'ajaxGroupTagSearch' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxGroupTagSearch',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxGroupTagSearch',
										),
									),
								),
							'ajaxAllMembersExepctLoggedOne' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:group_id]/[:sub_group_id]/ajaxAllMembersExepctLoggedOne',										
											'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',	
												'sub_group_id' => '[a-zA-Z0-9_-]*',										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'ajaxAllMembersExepctLoggedOne',
										),
									),
								),
								'createPlanet' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/createPlanet',										
											'constraints' => array(
												 										 
											),
										'defaults' => array(
											'__NAMESPACE__' => 'Groups\Controller',
											'controller' => 'groups',
											'action'     => 'createPlanet',
										),
									),
								),
								
						'group-media' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/media',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Album\Controller',
           'controller' => 'album',
           'action'     => 'index',
          ),
         ),
        ),
		'group-album' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/media/[:id]',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',
			'id'     => '[a-zA-Z0-9_-]*',			
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Album\Controller',
           'controller' => 'album',
           'action'     => 'view',
          ),
         ),
        ),
		'group-album-data' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/media/[:id]/[:count]',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',
			'id'     => '[a-zA-Z0-9_-]*',	
			'count'     => '[0-9]+',				
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Album\Controller',
           'controller' => 'album',
           'action'     => 'file',
          ),
         ),
        ),
		'groupTop' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/groupTop',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'groupTop',
          ),
         ),
        ),	
					'quitGroup' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/quitGroup',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'quitGroup',
          ),
         ),
        ),
				'ajaxRemoveUser' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/ajaxRemoveUser',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'ajaxRemoveUser',
          ),
         ),
        ),
			'ajaxSuspendUser' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/ajaxSuspendUser',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'ajaxSuspendUser',
          ),
         ),
        ),
		'ajaxRemoveSuspension' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/ajaxRemoveSuspension',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'ajaxRemoveSuspension',
          ),
         ),
        ),
		'approveMembers' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/approveMembers',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'approveMembers',
          ),
         ),
        ),
		'IgnoreMembers' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/IgnoreMembers',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'IgnoreMembers',
          ),
         ),
        ),
		'RemoveMemberRequest' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/RemoveMemberRequest',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'RemoveMemberRequest',
          ),
         ),
        ),
		'ajaxmemberSearch'=> array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/ajaxmemberSearch',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'ajaxmemberSearch',
          ),
         ),
        ),
		'checkPlanetExist'=> array(
         'type' => 'segment',
         'options' => array(
          'route' => '/checkPlanetExist',          
           'constraints' => array(
                   
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'checkPlanetExist',
          ),
         ),
        ),
		'updateQuestion' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/updateQuestion',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'updateQuestion',
          ),
         ),
        ),
		'AddQuestion' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/AddQuestion',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'AddQuestion',
          ),
         ),
        ),		
		'editQuestion' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/editQuestion',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'editQuestion',
          ),
         ),
        ),
		'questionnaire' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/questionnaire',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'questionnaire',
          ),
         ),
        ),
		'gotoNextQuestion' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/gotoNextQuestion',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'gotoNextQuestion',
          ),
         ),
        ),	
'getUserQuestionnaire' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/[:group_id]/[:sub_group_id]/getUserQuestionnaire',          
           'constraints' => array(
            'group_id' => '[a-zA-Z0-9_-]*', 
            'sub_group_id' => '[a-zA-Z0-9_-]*',           
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'getUserQuestionnaire',
          ),
         ),
        ),			
		'planetSuggestions' => array(
         'type' => 'segment',
         'options' => array(
          'route' => '/planetSuggestions',          
           'constraints' => array(
            
           ),
          'defaults' => array(
           '__NAMESPACE__' => 'Groups\Controller',
           'controller' => 'groups',
           'action'     => 'planetSuggestions',
          ),
         ),
        ),
		'ajaxGetPlanetFromGalaxySeoTitle' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ajaxGetPlanetFromGalaxySeoTitle',
                            'defaults' => array(
								'__NAMESPACE__' => 'Groups\Controller',
								'controller' => 'groups',
								'action'     => 'ajaxGetPlanetFromGalaxySeoTitle',
							),
                        ),					 
                    ),
		
				),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'groups' => __DIR__ . '/../view',
        ),
    ),
);