<?php 
// module/Album/config/module.config.php:
return array(
    'controllers' => array(
        'invokables' => array(
            'Album\Controller\Album' => 'Album\Controller\AlbumController',
			  
			
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
            'album' => array(
                   'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/album',
                    'defaults' => array(
						'__NAMESPACE__' => 'Album\Controller',
                        'controller' => 'album',
                        'action'     => 'index',
                    ),
                ),
				
				  'may_terminate' => true,
                'child_routes' => array(
                    'album-add' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/add/[:group_id]',
                            'constraints' => array(
								'group_id' => '[a-zA-Z0-9_-]*',											 
							),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'add',
							),
                        ),					 
                    ),
					 'album-create' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/add',
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'add',
							),
                        ),					 
                    ),
					'album-view' => array(
                        'type' => 'segment',
                        'options' => array(
                             'route'    => '[/:id]',
                    'constraints' => array(                       
                        'id'     => '[a-zA-Z0-9_-]*',
						
                    ),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'view',
							),
                        ),					 
                    ),
					'photo' => array(
                        'type' => 'segment',
                        'options' => array(
                             'route'    => '/photo[/:id]',
                    'constraints' => array(                       
                        'id'     => '[a-zA-Z0-9_-]*',
						
                    ),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'photo',
							),
                        ),					 
                    ),
					'album-single' => array(
                        'type' => 'segment',
                        'options' => array(
                             'route'    => '/single[/:id][/:panel]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
						'panel'     => '[0-9]+',
						
                    ),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'single',
							),
                        ),					 
                    ),
					'album-upload' => array(
                        'type' => 'segment',
                        'options' => array(
                             'route'    => '/upload[/:id]',
                   'constraints' => array(
                            'id'     => '[0-9]+',
					     ),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'upload',
							),
                        ),					 
                    ),
						'album-cover' => array(
                        'type' => 'segment',
                        'options' => array(
                             'route'    => '/coverpic[/:data_id][/:album_id]',
                   'constraints' => array(
                            'data_id'     => '[0-9]+',
							'album_id'     => '[0-9]+',
					     ),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'coverpic',
							),
                        ),					 
                    ),
					'album-more' => array(
                        'type' => 'segment',
                        'options' => array(
                             'route'    => '/addmore',
                  
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'addmore',
							),
                        ),					 
                    ),
						'album-edit' => array(
                        'type' => 'segment',
                        'options' => array(
                             'route'    => '/edit',
                  
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'edit',
							),
                        ),					 
                    ),
					'album-delete' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/delete[/:album_id]',
						'constraints' => array(                             
							'album_id' => '[a-zA-Z0-9_-]*',		
					     ),
                  
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'delete',
							),
                        ),					 
                    ),
					'album-deletedata' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/deletedata[/:data_id]',
						'constraints' => array(
                            'data_id'     => '[0-9]+',
					     ),
                  
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'deletedata',
							),
                        ),					 
                    ),
					'album-tagquery' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/usertagquery[/:group_id]',
						'constraints' => array(
                            'group_id'     => '[a-zA-Z0-9_-]*',
					     ),
                  
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'usertagquery',
							),
                        ),					 
                    ),
					'album-addtag' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/addTag[/:data_id][/:tag_id][/:x_axis][/:y_axis]',
						'constraints' => array(
                            'data_id'     => '[0-9]+',
							'tag_id'      => '[0-9]+',
					     ),
                  
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'addTag',
							),
                        ),					 
                    ),
					'album-deletetag' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/deleteTag[/:tag_id]',
						'constraints' => array(
							'tag_id'      => '[0-9]+',
					     ),
                  
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'deleteTag',
							),
                        ),					 
                    ),
					'ajaxAddAlbum' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '[/:group_id][/:planet_id]/ajaxAddAlbum',
						'constraints' => array(
												'group_id' => '[a-zA-Z0-9_-]*',
												'planet_id' => '[a-zA-Z0-9_-]*',												
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxAddAlbum',
							),
                        ),					 
                    ),
					'ajaxAddmore' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/[:album_id]/ajaxAddmore',
						'constraints' => array(												 
												'album_id' => '[a-zA-Z0-9_-]*',													
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxAddmore',
							),
                        ),					 
                    ),
					'ajaxAddUserAlbum' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/ajaxAddUserAlbum',
						 
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxAddUserAlbum',
							),
                        ),					 
                    ),
					
					'ajaxEditAlbum' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/[:album_id]/ajaxEditAlbum',
						'constraints' => array(												 
												'album_id' => '[a-zA-Z0-9_-]*',													
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxEditAlbum',
							),
                        ),					 
                    ),
					'ajaxAlbumCover' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/[:album_id]/ajaxAlbumCover[/:id]',
						'constraints' => array(												 
												'album_id' => '[a-zA-Z0-9_-]*',
												'id'      => '[0-9]+',												
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxAlbumCover',
							),
                        ),					 
                    ),
					'ajaxDeleteData' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/[:album_id]/ajaxDeleteData[/:id]',
						'constraints' => array(												 
												'album_id' => '[a-zA-Z0-9_-]*',
												'id'      => '[0-9]+',												
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxDeleteData',
							),
                        ),					 
                    ),
					'ajaxDeleteUserData' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/[:album_id]/ajaxDeleteUserData[/:id]',
						'constraints' => array(												 
												'album_id' => '[a-zA-Z0-9_-]*',
												'id'      => '[0-9]+',												
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxDeleteUserData',
							),
                        ),					 
                    ),
					'ajaxLoadUserData' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/[:album_id]/ajaxLoadUserData',
						'constraints' => array(												 
												'album_id' => '[a-zA-Z0-9_-]*',
												 										
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxLoadUserData',
							),
                        ),					 
                    ),
					'ajaxLoadUserPhoto' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/[:member_profile]/ajaxLoadUserPhoto',
						'constraints' => array(												 
												'member_profile' => '[a-zA-Z0-9_-]*',
												 										
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxLoadUserPhoto',
							),
                        ),					 
                    ),
					'users' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '/users/[:profile_name]',
						'constraints' => array(												 
												'profile_name' => '[a-zA-Z0-9_-]*',
												 										
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'users',
							),
                        ),					 
                    ),
					
					'ajaxLoadData' => array(
                        'type' => 'segment',
                        'options' => array(
							  'route'    => '[/:group_id][/:planet_id]/[:album_id]/ajaxLoadData',
						'constraints' => array(												 
												'album_id' => '[a-zA-Z0-9_-]*',
												'group_id' => '[a-zA-Z0-9_-]*',
												'planet_id' => '[a-zA-Z0-9_-]*',								
											),
							'defaults' => array(
								'__NAMESPACE__' => 'Album\Controller',
								'controller' => 'album',
								'action'     => 'ajaxLoadData',
							),
                        ),					 
                    ),
                ),
				
				
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);