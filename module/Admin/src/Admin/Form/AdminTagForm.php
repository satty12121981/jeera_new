<?php
namespace Admin\Form;
use Zend\Form\Form;

class AdminTagForm extends Form
{
    public function __construct($selectAllTagCategory, $selectedTagCategory = null, $name = null)
    {
        // we want to ignore the name passed
        parent::__construct('admintag');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'tag_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'category_id',
            'options' => array(
                'value_options' => $selectAllTagCategory,

            ),
            'attributes' => array(
                'id' => 'category_id',
                'value'=>$selectedTagCategory,
            )
        ));

        $this->add(array(
            'name' => 'tag_title',
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
