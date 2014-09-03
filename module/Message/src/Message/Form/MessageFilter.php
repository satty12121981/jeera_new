<?php
namespace Message\Form;
use Zend\InputFilter\InputFilter;

class MessageFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter) {  	
 
	$this->add(array(
    	'name' => 'user_message_content',
     	'required' => true,
     	'filters' => array(
  			array('name' => 'StripTags'),
			array('name' => 'StringTrim'),
			array('name' => 'StripNewLines'),
			array('name' => 'HtmlEntities'),			
     	),
		'validators' => array(
        	array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),
			array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_user_Message','field' => 'user_message_content', 'adapter' => $dbAdapter),),                
		),
		));	
	 
    }
}
