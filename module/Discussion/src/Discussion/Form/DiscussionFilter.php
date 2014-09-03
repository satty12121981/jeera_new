<?php
namespace Discussion\Form;
use Zend\InputFilter\InputFilter;

class DiscussionFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct() {  	
 
	$this->add(array(
    	'name' => 'group_discussion_content',
     	'required' => true,
     	'filters' => array(  			
			array('name' => 'StringTrim'),			
			array('name' => 'HtmlEntities'),			
     	)
		));	
	
		 
    }
}
