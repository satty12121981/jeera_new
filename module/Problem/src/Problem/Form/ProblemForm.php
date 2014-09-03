<?php
namespace Problem\Form;
use Zend\Form\Form;

class ProblemForm extends Form
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('problem');		 
       
	    $this->add(array(
            'name' => 'problem_refer_id',
            'attributes' => array(
                'type'  => 'hidden'
            ),
        ));
		
		$this->add(array(
            'name' => 'submitbutton',
            'attributes' => array(
                'type'  => 'button',
                'value' => 'Continue',
                'id' => 'submitbutton',
            ),
        ));
		
    }
}
