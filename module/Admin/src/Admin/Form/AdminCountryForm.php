<?php
namespace Admin\Form;
use Zend\Form\Form;

class AdminCountryForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('admincountry');

        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'country_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		 
        $this->add(array(
            'name' => 'country_status',
            'attributes' => array(
                'type'  => 'hidden',
				'value'  => '1',
				
            ),
        ));

        $this->add(array(
            'name' => 'country_title',
            'attributes' => array(
                'type'  => 'text',
            ),
            
        ));

        $this->add(array(
            'name' => 'country_code',
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
				'class'=> 'alt_btn',
            ),
        ));

    }
}
