<?php 
namespace User\Form;
use Zend\Form\Form;
class ForgotPassword extends Form
{
    public function __construct($name = null)
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
            'name' => 'user_email',
            'type' => 'email',
            'options' => array(
                
				
            ),
			'attributes' => array(
                'placeholder' => 'mail@yourdomain', //set selecarray()ted to '1'
				'id' => 'user_email',
				'size'  => '100',
				
            ) 
        ));
          $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Send',
                'id' => 'submitbutton',
				'class' => 'next_button blue-butn',
            ),
        ));
    }
}