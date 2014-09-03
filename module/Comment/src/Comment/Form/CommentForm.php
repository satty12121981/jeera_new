<?php
namespace Comment\Form;
use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('comment');		 
        
	    $this->add(array(
            'name' => 'comment_content',
            'attributes' => array(
                'type'  => 'textarea',
				'label' => '',
				'placeholder'  => 'Post a Comment',
				'id' => 'comment_content',
            ),
            
        ));
		$this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Comment',
                'class' => 'commentButton',
            ),
        ));

    }
}
