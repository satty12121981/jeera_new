<?php
namespace Admin\Form;
use Zend\Form\Form;

class AdminPlanetTagForm extends Form
{
    public function __construct($allSubGroupData, $allTagData)
    {
        // we want to ignore the name passed
        parent::__construct('admingrouptag');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'group_tag_id',
            'attributes' => array(
                'type'  => 'hidden',
				
            ),
        ));	
		
		 
						
		$this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'group_tag_group_id',  				  
				'options' => array(							 
		                	 'value_options' => $allSubGroupData,
							 
						),
				'attributes' => array(
					'id' => 'group_tag_group_id',
					 				
					)   
				));
		
		$this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'group_tag_tag_id',  				  
				'options' => array(							 
		                	'value_options' => $allTagData,
							
						),
				'attributes' => array(
					'id' => 'group_tag_tag_id',
					 
					)   
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
