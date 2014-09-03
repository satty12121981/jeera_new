<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminNotificationTypeEditFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter, $id) {  	
		
	 
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
			array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_notification_type','field' => 'notification_type_title','exclude' => array ('field' => 'notification_type_id', 'value' => $id),  'adapter' => $dbAdapter),),                
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