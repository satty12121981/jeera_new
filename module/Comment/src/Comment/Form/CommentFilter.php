<?php
namespace Comment\Form;
use Zend\InputFilter\InputFilter;
class CommentFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct() {  	
		
		#validation for comment title. It needs to be UNIQUE
		$this->add(array(
            'name' => 'comment_content',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),				 
            ),
			 
        ));	
		
    }
}
