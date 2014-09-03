<?php
namespace Activity\Form;
use Zend\InputFilter\InputFilter;
class ActivityAddFormFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct() {         
		$this->add(array(
            'name' => 'group_activity_start_timestamp',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(						
				array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 100,),),
            ),
        ));	
		
		$this->add(array(
            'name' => 'group_activity_location',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(						
				array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 100,),),
            ),
        ));	
		
		 
		
		$this->add(array(
            'name' => 'group_activity_content',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(						
				array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 500,),),
            ),
        ));			
    }
}
