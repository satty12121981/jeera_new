<?php
namespace User\Form;
use Zend\Form\Form; 

class RegisterStepFirst extends Form
{ 
     protected $captchaElement= null;
	
	public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');
		
		//$this->add(array('hash','csrf_token',array('salt'=>get_class($this).'s3cr3t%Ek@on9!'));	
		
		$this->add(array(
     'type' => 'Zend\Form\Element\Csrf',
     'name' => 'csrf',
     'options' => array(
             'csrf_options' => array(
                     'timeout' => 600,
					 'salt' => 'unique'
             )
     )
 ));
        
       $this->add(array(
            'name' => 'user_given_name',
            'type' => 'Text',
            'options' => array(
                 
            ),
			'attributes' => array(
                'id' => 'user_given_name',
				'placeholder' => 'Display Name:'
            ),
			'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(					 
                array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 50,),),
            ),
        ));
		$this->add(array(
            'name' => 'user_first_name',
            'type' => 'Text',
            'options' => array(
               
            ),
			'attributes' => array(
                'id' => 'user_first_name',	
				'placeholder' => 'First Name:'				
            ),
			'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(					 
                array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 50,),),
            ),
        ));
	   $this->add(array(
            'name' => 'user_middle_name',
            'type' => 'Text',
            'options' => array(
                
            ),
			'attributes' => array(
                'id' => 'user_middle_name',		
				'placeholder' => 'Middle Name:'					
            ),
			'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(					 
                array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 50,),),
            ),
        ));
		$this->add(array(
            'name' => 'user_last_name',
            'type' => 'Text',
            'options' => array(
                
            ),
			'attributes' => array(
                'id' => 'user_last_name',	
				'placeholder' => 'Last Name:'				
            ),
			'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(					 
                array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 50,),),
            ),
        ));
	    $this->add(array(
            'name' => 'user_email',
            'type' => 'Text',
            'options' => array(
               
            ),
			'attributes' => array(
                'placeholder' => 'Email:', //set selecarray()ted to '1'
				'id' => 'user_email',
				'size'  => '100',
				
            ) 
        ));
        $this->add(array(
            'name' => 'user_password',
            'type' => 'Password',
            'options' => array(
                 
            ),
			'attributes' => array(               
				'id' => 'user_password',
				'size'  => '100',				
				'placeholder' => 'Password:',
            ) 
        ));
		
		$this->add(array(
            'name' => 'user_retype_password',
            'type' => 'Password',
            'options' => array(
                
            ),
			'attributes' => array(               
				'id' => 'user_retype_password',
				'size'  => '100',	
				'placeholder' => 'Confirm Password:',				
            ) 
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => 'Next',
                'id' => 'submitbutton',
				'class' => 'next_button blue-butn',
            ),
        ));
		 
		
    }
}