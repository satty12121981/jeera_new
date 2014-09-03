<?php
namespace User\Form;
use Zend\InputFilter\InputFilter;
class RegisterStepThreeFilter extends InputFilter
{
    private $dbAdapter;
	
	public function __construct($dbAdapter) {         
	 
		$this->add(array(
            'name' => 'user_tag_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
				 
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 500,),
                ),
            ),
        ));	
		
		
    }
}
