<?php
namespace Admin\Form;
use Zend\Form\Form;

class AdminGalaxyForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('admintag');

        $this->setAttribute('method', 'post');
		$this->setAttribute('enctype', 'multipart/form-data');
        $this->add(array(
            'name' => 'group_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		  
		
		$this->add(array(
            'name' => 'group_status',
            'attributes' => array(
                'type'  => 'hidden',
				'value'  => '1',
            ),
        ));
		
		 
      

        $this->add(array(
            'name' => 'group_title',
            'attributes' => array(
                'type'  => 'text',
				'id'	=> 'group_title',
            ),
            
        ));
		
		 $this->add(array(
            'name' => 'group_seo_title',
            'attributes' => array(
                'type'  => 'text',
				'id'	=> 'group_seo_title',
            ),
            /* 'options' => array(
                'label' => 'SEO Name*',
            ),*/
        ));
		/*
		 $this->add(array(
            'name' => 'group_location',
            'attributes' => array(
                'type'  => 'text',
            ),
             'options' => array(
                'label' => 'Galaxy Location',
            ), 
        ));
		*/
		 
		
		$this->add(array(
            'name' => 'group_discription',
            'attributes'=>array(
                'type'=>'textarea' 
            ),
           /* 'options' => array(
                'label' => 'Group Discription*'
            ),*/
        ));
		
		  
        $this->add(array(
            'name' => 'galaxy_image',
            'attributes' => array(
                'type'  => 'file',
            ),
           /* 'options' => array(
                'label' => 'Group*',
            ), */
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
