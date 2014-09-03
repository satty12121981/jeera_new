<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'City\Controller\City' => 'City\Controller\CityController',
        ),
    ),
    // The following section is new and should be added to your file
       'router' => array(
        'routes' => array(
            'city' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/city',
                    'defaults' => array(
						'__NAMESPACE__' => 'City\Controller',
                        'controller' => 'city',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'cities' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/cities',
                             
							'defaults' => array(
								'__NAMESPACE__' => 'City\Controller',
								'controller' => 'city',
								'action'     => 'cities',
							),
                        ),					 
                    ),
					'ajaxCities' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/ajaxCities',
                             
							'defaults' => array(
								'__NAMESPACE__' => 'City\Controller',
								'controller' => 'city',
								'action'     => 'ajaxCities',
							),
                        ),					 
                    ), 
					'ajaxCitySelect' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/ajaxCitySelect',
                             
							'defaults' => array(
								'__NAMESPACE__' => 'City\Controller',
								'controller' => 'city',
								'action'     => 'ajaxCitySelect',
							),
                        ),					 
                    ),
					
					'ajaxCitiesFromGeocode' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/ajaxCitiesFromGeocode',
                             
							'defaults' => array(
								'__NAMESPACE__' => 'City\Controller',
								'controller' => 'city',
								'action'     => 'ajaxCitiesFromGeocode',
							),
                        ),					 
                    ), 
					'ajaxCitiesForAdminPlanet' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/ajaxCitiesForAdminPlanet',
                             
							'defaults' => array(
								'__NAMESPACE__' => 'City\Controller',
								'controller' => 'city',
								'action'     => 'ajaxCitiesForAdminPlanet',
							),
                        ),					 
                    ), 
                ),
            ),
        ),
    ),


    'view_manager' => array(
        'template_path_stack' => array(
            'city' => __DIR__ . '/../view',
        ),
    ),
);

