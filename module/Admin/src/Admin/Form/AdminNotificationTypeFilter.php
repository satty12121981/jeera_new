<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminNotificationTypeFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter) {  	
		
	 
	$this->add(array(
    	'name' => 'notification_type_title',
     	'required' => true,
     	'filters' => array(
  			array('name' => 'StripTags'),
			array('name' => 'StringTrim'),
			array('name' => 'HtmlEntities'),				 
     	),
		'validators' => array(
        	array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),
			array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_notification_type','field' => 'notification_type_title',  'adapter' => $dbAdapter),),                
		),
		));	
						
		$this->add(array(
    	'name' => 'notification_type_discription',
     	'required' => false,
     	'filters' => array(
  			array('name' => 'StripTags'),
			array('name' => 'StringTrim'),
			array('name' => 'HtmlEntities'),				 
     	),
		'validators' => array(
        	array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 500,),),			              
		),
		));	
 
    }
}
