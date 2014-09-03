<?php
namespace Admin\Form;
use Zend\Form\Form;

class AdminPlanetForm extends Form
{
    public function __construct( $selectAllGroup,$country,$city,$selected_group='',$selected_country='',$selected_city='', $group_owner='',$name = null)
    {
        // we want to ignore the name passed
        parent::__construct('adminplanet');

        $this->setAttribute('method', 'post');
		$this->setAttribute('enctype', 'multipart/form-data');
        $this->add(array(
            'name' => 'group_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		$this->add(array(     
			'type' => 'Zend\Form\Element\Select',       
			'name' => 'group_parent_group_id',  				  
			'options' => array(							 
						 'value_options' => $selectAllGroup,
						
				),
			'attributes' => array(
				'id' => 'group_parent_group_id',
				'value'=>$selected_group,
				)   
		));
		
		$this->add(array(
            'name' => 'group_status',
            'attributes' => array(
                'type'  => 'hidden',
				'value'  => '1',
            ),
        ));
		
		$this->add(array(
            'name' => 'y2m_group_location_lat',
            'attributes' => array(
                'type'  => 'hidden',
				'value'  => '',
				'id' => 'y2m_group_location_lat',
            ),
        ));
		$this->add(array(
            'name' => 'y2m_group_location_lng',
            'attributes' => array(
                'type'  => 'hidden',
				'value'  => '',
				'id' => 'y2m_group_location_lng',
            ),
        ));
		$this->add(array(
            'name' => 'group_photo_id',
            'attributes' => array(
                'type'  => 'hidden'
            ),
        ));

        $this->add(array(
            'name' => 'group_title',
            'attributes' => array(
                'type'  => 'text',
				'id' => 'group_title',
            ),
           
        ));
		
		$this->add(array(
            'name' => 'group_seo_title',
            'attributes' => array(
                'type'  => 'text',
				'id' => 'group_seo_title',
            ),
          
        ));
		$this->add(array(     
			'type' => 'Zend\Form\Element\Select',       
			'name' => 'group_country_id',  				  
			'options' => array(							 
						 'value_options' => $country,
						
				),
			'attributes' => array(
				'id' => 'group_country_id',
				'value'=>$selected_country,
				)   
		));
		$this->add(array(     
			'type' => 'Zend\Form\Element\Select',       
			'name' => 'group_city_id',  				  
			'options' => array(							 
						 'value_options' => $city,
					'disable_inarray_validator' => true,	
				),
			'attributes' => array(
				'id' => 'group_city_id',
				'value'=>$selected_city,
				)   
		));
		
		$this->add(array(
            'name' => 'group_location',
            'attributes' => array(
                'type'  => 'text',
				'id' => 'group_location',
            ),
          
        ));
		 
		$this->add(array(
            'name' => 'group_web_address',
            'attributes' => array(
                'type'  => 'text',
				'id' => 'group_web_address',
            ),
          
        ));
		$this->add(array(
            'name' => 'group_discription',
            'attributes'=>array(
                'type'=>'textarea' 
            ),
           
        ));
		 $this->add(array(
            'name' => 'group_welcome_message_members',
            'attributes'=>array(
                'type'=>'textarea' 
            ),
           
        )); 
        $this->add(array(
            'name' => 'group_image',
            'attributes' => array(
                'type'  => 'file',
            ),
           
        )); 
		$this->add(array(
            'name' => 'group_owner',
            'attributes'=>array(
                'type'=>'text' ,
				'id' => 'group_owner',
				'value'=>$group_owner,
            ),
           
        )); 
		$this->add(array(
            'name' => 'group_owner_id',
            'attributes'=>array(
                'type'=>'hidden' ,
				'id' => 'group_owner_id',
            ),
           
        )); 
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'alt_btn',
            ),
        ));

    }
}
