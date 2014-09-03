<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminQuestionFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct() {  	
		
		#validation for country title. It needs to be UNIQUE
		$this->add(array(
            'name' => 'question',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
				 
            ),
			 
        ));
		$this->add(array(
            'name' => 'answer_type',
            'required' => true,
            'filters' => array(
                
				 
            ),
			 
        )); 	
		 
    }
}
