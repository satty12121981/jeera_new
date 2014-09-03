<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
			'Groups\Controller\Groups' => 'Groups\Controller\GroupsController',
			'Country\Controller\Country' => 'Country\Controller\CountryController',		
			'User\Controller\UserProfile' => 'User\Controller\UserProfileController',	
			'Calender\Controller\Calender' => 'Calender\Controller\CalenderController',	
			'Activity\Controller\Calender' => 'Activity\Controller\CalenderController',	
			'Photo\Controller\Photo' => 'Photo\Controller\PhotoController',		
			
			'Tag\Controller\Tag' => 'Tag\Controller\TagController',	
			'User\Auth\BcryptDbAdapter'=>'User\Auth\BcryptDbAdapter',
			),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
			
            'user' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/user',
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'user',
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
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'user',
                                'action'     => 'login',
                            ),
                        ),
                    ),    
					'ajax_login' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ajaxlogin',
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'user',
                                'action'     => 'ajaxlogin',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
					 'fblogin' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/fblogin',
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'user',
                                'action'     => 'fblogin',
                            ),
                        ),
                    ),
					'fbredirect' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/fbredirect',
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'user',
                                'action'     => 'fbredirect',
                            ),
                        ),
                    ),
					'changepassword' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/changepassword',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'changepassword',
                            ),
                        ),
                    ),
					'forgotPassword' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/forgotPassword',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'forgotPassword',
                            ),
                        ),
                    ),
					'varifyemail' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/varifyemail[/:key][/:id]',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'varifyemail',
                            ),
                        ),
                    ),
					'resetpassword' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/resetpassword[/:key][/:id]',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'resetpassword',
                            ),
                        ),
                    ),
					'resendverification' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/resendverification',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'resendverification',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/register',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'register',
                            ),
                        ),
                    ),
                     
					'ajaxgettag' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/ajaxgettag',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'ajaxgettag',
                            ),
                        ),                        
                    ),
					'tagsearch' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/tagsearch',
                            'defaults' => array(
                                'controller' => 'user',
                                'action'     => 'tagsearch',
                            ),
                        ),                        
                    ),
                    'changeemail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-email',
                            'defaults' => array(
                                'controller' => 'user',
                                'action' => 'changeemail',
                            ),
                        ),                        
                    ),
					'friend-request' => array(
                'type' => 'Literal',                
                'options' => array(
                    'route' => '/friendRequest',
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'userProfile',
                        'action'     => 'friendRequest',
                    ),
                ),
			),
				'friend-accept' => array(
                'type' => 'Literal',                
                'options' => array(
                    'route' => '/acceptFriendRequest',
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'userProfile',
                        'action'     => 'acceptFriendRequest',
                    ),
                ),
			),
			'ajaxGetConnectionCount' => array(
                'type' => 'Literal',                
                'options' => array(
                    'route' => '/ajaxGetConnectionCount',
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'userProfile',
                        'action'     => 'ajaxGetConnectionCount',
                    ),
                ),
			),
			'ajaxNewConnectionRequests' => array(
                'type' => 'Literal',                
                'options' => array(
                    'route' => '/ajaxNewConnectionRequests',
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'userProfile',
                        'action'     => 'ajaxNewConnectionRequests',
                    ),
                ),
			),
			'ajaxApproveRequests' => array(
                'type' => 'Literal',                
                'options' => array(
                    'route' => '/ajaxApproveRequests',
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'userProfile',
                        'action'     => 'ajaxApproveRequests',
                    ),
                ),
			),
			'declineFriendRequest' => array(
                'type' => 'Literal',                
                'options' => array(
                    'route' => '/declineFriendRequest',
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'userProfile',
                        'action'     => 'declineFriendRequest',
                    ),
                ),
			),
			
                ),
            ),
			'memberprofile' => array(
                'type' => 'segment',
				'may_terminate' => true,
                'options' => array(
                   'route' => '[/:member_profile]',
				   'constraints' => array(
						'member_profile' => '[a-zA-Z0-9_-]*',												 
					),
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'userprofile',
                        'action'     => 'memberprofile',
                    ),
                ),
                'child_routes' => array(
                    'memberconnect' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/connect/[:member_profile]',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',												 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'memberconnect',
                            ),
                        ),
                    ),
					'planets' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/planets',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',												 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'planets',
                            ),
                        ),
                    ),
					'photos' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/photos',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',												 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'photos',
                            ),
                        ),
                    ),
					 
					'feeds-loadmore' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/morefeeds',
							'constraints' => array(							 										 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'morefeeds',
                            ),
                        ),
                    ),
					'photos_view' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/photos/[:album_id]',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',	
							'album_id' => '[a-zA-Z0-9_-]*',								
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
                                'controller' => 'album',
                                'action'     => 'userAlbumView',
                            ),
                        ),
                    ),
					
					'user_photos' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/photos/user_photos',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',	
							 				
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
                                'controller' => 'album',
                                'action'     => 'userPhotos',
                            ),
                        ),
                    ),
					
					'file_view' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/photos/[:album_id]/[:num]',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',	
							'num' => '[0-9]+',								
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
                                'controller' => 'album',
                                'action'     => 'userfile',
                            ),
                        ),
                    ),
					'memberapprove' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/approve/[:member_profile]',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',												 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'memberapprove',
                            ),
                        ),
                    ),
					'saveProfilePic' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/saveProfilePic',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',												 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'saveProfilePic',
                            ),
                        ),
                    ),
					'profileTop' => array(
						 'type' => 'segment',
						 'options' => array(
						  'route' => '/profileTop/[:member_profile]',          
						   'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',	         
						   ),
						  'defaults' => array(
						  '__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'profileTop',
						  ),
						 ),
						),	
						'updatebio' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/updatebio',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'updatebio',
								),
							),
						),
						'ajaxLoadMoreFriends' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/ajaxLoadMoreFriends/[:member_profile]',
								'constraints' => array(
								'member_profile' => '[a-zA-Z0-9_-]*',	         
							   ),	 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'ajaxLoadMoreFriends',
								),
							),
						),
						'ajaxLoadMorePlanets' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/ajaxLoadMorePlanets/[:member_profile]',
								'constraints' => array(
								'member_profile' => '[a-zA-Z0-9_-]*',	         
							   ),	 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'ajaxLoadMorePlanets',
								),
							),
						),
					'settingsGroupLoadmore' => array(
						 'type' => 'segment',
						 'options' => array(
						  'route' => '/settingsGroupLoadmore',          
						   'constraints' => array(
							  
						   ),
						  'defaults' => array(
						  '__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'settingsGroupLoadmore',
						  ),
						 ),
						),	
					'memberreject' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/reject/[:member_profile]',
							'constraints' => array(
							'member_profile' => '[a-zA-Z0-9_-]*',												 
							),
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'memberreject',
                            ),
                        ),
                    ),
				),
			),
			//userProfileRoutes
			
            'profile' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/profile',
                    'defaults' => array(
						'__NAMESPACE__' => 'User\Controller',
                        'controller' => 'userProfile',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'load' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/load',
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'load',
                            ),
                        ),
                    ), 
					'settings' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/settings',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'settings',
								),
							),
						),
						'addTag' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/addTag',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'addTag',
								),
							),
						),
						'removeTag' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/removeTag',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'removeTag',
								),
							),
						),
						'addProfilePic' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/addProfilePic',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'addProfilePic',
								),
							),
						),
						'addProfileCover' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/addProfileCover',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'addProfileCover',
								),
							),
						),
						'savePrivacySettings' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/savePrivacySettings',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'savePrivacySettings',
								),
							),
						),
						'addProfilePicToTemp' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/addProfilePicToTemp',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'addProfilePicToTemp',
								),
							),
						),
						'addAlbumPicToTemp' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/addAlbumPicToTemp',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'addAlbumPicToTemp',
								),
							),
						),
						'inviteFriends' => array(
							'type' => 'segment',
							'options' => array(
								'route' => '/inviteFriends',
									 
								'defaults' => array(
									'controller' => 'UserProfile',
									'action'     => 'inviteFriends',
								),
							),
						),
					
					'user' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/user',
                            'defaults' => array(
								'__NAMESPACE__' => 'User\Controller',
                                'controller' => 'userprofile',
                                'action'     => 'user',
                            ),
                        ),
						
						'may_terminate' => true,
							'child_routes' => array(
								'load' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:user_url_identifier]',
											'constraints' => array(
												'user_url_identifier' => '[a-zA-Z0-9_-]*',												 
											),
										'defaults' => array(
											'controller' => 'userprofile',
											'action'     => 'user',
										),
									),
								),
							),
                    ),                    
                    'cal' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/cal',
                            'defaults' => array(
                                'controller' => 'userprofile',
                                'action'     => 'cal',
                            ),
                        ),
						
						'may_terminate' => true,
							'child_routes' => array(
								'load' => array(
									'type' => 'segment',
									'options' => array(
										'route' => '/[:month_id]/[:year_id]',
											'constraints' => array(
												'month_id' => '[0-9]+',
												'year_id' => '[0-9]+'
											),
										'defaults' => array(
											'controller' => 'userprofile',
											'action'     => 'cal',
										),
									),
								),
							),		   
				   		),
					
                    'register' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/register',
                            'defaults' => array(
                                'controller' => 'UserProfile',
                                'action'     => 'register',
                            ),
                        ),
                    ),					 
                    'changepassword' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-password',
                            'defaults' => array(
                                'controller' => 'UserProfile',
                                'action'     => 'changepassword',
                            ),
                        ),                        
                    ),
                    'changeemail' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/change-email',
                            'defaults' => array(
                                'controller' => 'UserProfile',
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
            'user' => __DIR__ . '/../view',
        ),
    ),
);

