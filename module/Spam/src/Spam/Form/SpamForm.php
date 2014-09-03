<?php
namespace Spam\Form;
use Zend\Form\Form;

class SpamForm extends Form
{
    public function __construct($spam_system_type_id,$spam_problem_id,$spam_refer_id)
    {
        // we want to ignore the name passed
        parent::__construct('spam');		 
       
	    $this->add(array(
            'name' => 'spam_system_type_id',
            'attributes' => array(
                'type'  => 'hidden',
				'value'  => $spam_system_type_id,
            ),
        ));
		
		$this->add(array(
            'name' => 'spam_problem_id',
            'attributes' => array(
                'type'  => 'hidden',
				'value'  => $spam_problem_id,
            ),
        ));
		
		$this->add(array(
            'name' => 'spam_other_content',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		$this->add(array(
            'name' => 'spam_refer_id',
            'attributes' => array(
                'type'  => 'hidden',
				'value'  => $spam_refer_id,
            ),
        ));
		
    }
}
