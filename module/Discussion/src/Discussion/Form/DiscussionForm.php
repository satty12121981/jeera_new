<?php
namespace Discussion\Form;
use Zend\Form\Form;

class DiscussionForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('discussion');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'group_discussion_group_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'group_discussion_content',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Post a Discussion',
				'placeholder'  => 'Post a Discussion',
            ),
        ));
		$this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'discussionButton',
            ),
        ));
    }
}