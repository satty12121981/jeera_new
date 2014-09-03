<?php
namespace Admin\Form;

use Zend\Form\Form;

class AdminNotificationTypeForm extends Form
{
    public function __construct($name = null)
    {
       
        parent::__construct('adminnotification');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'notification_type_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        $this->add(array(
            'name' => 'notification_type_title',
            'attributes' => array(
                'type'  => 'text',
            ),
            
        ));
        $this->add(array(
            'name' => 'notification_type_discription',
            'attributes' => array(
                'type'  => 'textarea',
            ),
        
        ));
			
		 $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'notification_type_status',
            'options' => array(
                'label' => 'Please choose one',
                'value_options' => array(
                    '1' => 'Enable',
                    '0' => 'Disable',
                ),
            ),
            'attributes' => array(
                'value' => '1' //set checked to '1'
            )
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
