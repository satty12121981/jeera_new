<?php
namespace Activity\Form;
use Zend\Form\Form;
class ActivityAddForm extends Form
{ 
	protected $captchaElement= null;
	public function __construct($userData = null)
    {
        // we want to ignore the name passed
        parent::__construct('activityAdd');
        $this->setAttribute('method', 'post');
		
		//$this->add(array('hash','csrf_token',array('salt'=>get_class($this).'s3cr3t%Ek@on9!'));	
		
		/*$this->add(array(
     'type' => 'Zend\Form\Element\Csrf',
     'name' => 'csrf',
     'options' => array(
             'csrf_options' => array(
                     'timeout' => 600,
					 'salt' => 'unique'
             )
     )
 ));*/
 
 		//Activity date
		$this->add(array(
            'name' => 'group_activity_start_timestamp',
            'type' => 'Text',
			'options' => array(
                'label' => 'Activity Date :',				 	 
            ),
			'attributes' => array(
				'id' => 'group_activity_start_timestamp',
                'placeholder' => 'YY-MM-DD' //set selecarray()ted to '1'
				
            ) 
             
        ));
		
		$this->add(array(
            'name' => 'group_activity_location',
            'type' => 'Text',
            'options' => array(
                'label' => 'Location :',
            ),
			'attributes' => array(
                'id' => 'group_activity_location',								
            ) 
        )); 
	$this->add(array(
            'name' => 'group_activity_title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title :',
            ),
			'attributes' => array(
                'id' => 'group_activity_title',								
            ) 
        )); 
	$this->add(array(
            'name' => 'group_activity_type',
            'type' => 'Zend\Form\Element\Radio',
             'options' => array(
                     'value_options' => array(
                             'public' => 'Public',
                             'private' => 'Invited Only',
							 
                     ),	 
             ),
			 'attributes' => array(
                'id' => 'group_activity_type',	
				'value' => 'public'
				) 
        ));		
       
	   $this->add(array(
            'name' => 'group_activity_content',
			'options' => array(
                'label' => 'Description :'
            ),
            'attributes'=>array(
                'type'=>'textarea',
				 'id' => 'group_activity_content',		
            ),
            
        ));	   
		 
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Create Activity',
                'id' => 'activity_submit',
				'class' => 'add_btn',
            ),
        ));
		
		
    }
}