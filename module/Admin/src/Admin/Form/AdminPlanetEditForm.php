<?php
namespace Admin\Form;
use Zend\Form\Form;

class AdminPlanetForm extends Form
{
    public function __construct( $selectAllGroup, $name = null)
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
            'name' => 'group_photo_id',
            'attributes' => array(
                'type'  => 'hidden'
            ),
        ));

        $this->add(array(
            'name' => 'group_title',
            'attributes' => array(
                'type'  => 'text',
            ),
           
        ));
		
		$this->add(array(
            'name' => 'group_seo_title',
            'attributes' => array(
                'type'  => 'text',
            ),
           
        ));
		
		$this->add(array(
            'name' => 'group_location',
            'attributes' => array(
                'type'  => 'text',
            ),
            
        ));
		
		$this->add(array(
            'name' => 'group_discription',
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
