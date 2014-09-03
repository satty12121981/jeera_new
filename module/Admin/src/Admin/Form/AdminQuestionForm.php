<?php
namespace Admin\Form;
use Zend\Form\Form;

class AdminQuestionForm extends Form
{
    public function __construct($question = null,$answer_type = 'Textarea' )
    {
        // we want to ignore the name passed
        parent::__construct('groupquestion');
		
        $this->setAttribute('method', 'post'); 

        $this->add(array(
            'name' => 'question',
            'attributes' => array(
                'type'  => 'text',
				'value' => $question,
            ),
        ));

        $this->add(array(
			'name' => 'answer_type',
			'type' => 'radio',
			'options' => array(
				'value_options' => array(
				'Textarea' => 'Textarea',
				'radio' => 'Radio buttons',
				'checkbox' => 'Checkboxes',
				),
			),
			'attributes' => array(
				'value' => $answer_type,
			),
		));
	 $this->add(array(
            'name' => 'option[]',
            'attributes' => array(
                'type'  => 'text',
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
