<?php
namespace Message\Form;
use Zend\Form\Form;

class MessageForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('Message');
		
        $this->setAttribute('method', 'post');
		
        $this->add(array(
            'name' => 'user_message_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
        $this->add(array(
            'name' => 'user_message_content',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Post a Message',
				'placeholder'  => 'Post a Message',
            ),
        ));
 
    }
}