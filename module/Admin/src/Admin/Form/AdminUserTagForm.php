<?php
namespace Admin\Form;
use Zend\Form\Form;

class AdminUserTagForm extends Form
{
    public function __construct($allUserData, $allTagData)
    {
        // we want to ignore the name passed
        parent::__construct('adminusertag');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'user_tag_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		
		$this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'user_tag_tag_id',  				  
				'options' => array(							 
		                	'value_options' => $allTagData,
							
						),
				'attributes' => array(
					'id' => 'user_tag_tag_id',
					'value' => '2' //set selecarray()ted to '1'
					)   
				)); 
				
				
		$this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'user_tag_user_id',  				  
				'options' => array(							 
		                	'value_options' => $allUserData,
							 
						),
				'attributes' => array(
					'id' => 'user_tag_user_id',
					'value' => '2', //set selecarray()ted to '1'
					
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
