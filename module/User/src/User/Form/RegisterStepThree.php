<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element\Captcha as Captcha;

class RegisterStepThree extends Form
{ 
    protected $captchaElement= null;
	
	public function __construct()
    {
        
		// we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');
		
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
			'type' => 'Zend\Form\Element\MultiCheckbox',       
			'name' => 'user_tag_id',
			'attributes' =>  array(
				'options' => array(),
			),
				
		));    

		 
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Next',
				'id' => 'submitbutton',
			),
		));
		$this->add(array(
			'name' => 'reset',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Skip',
				'id' => 'resetbutton',
			),
		));
    }
}