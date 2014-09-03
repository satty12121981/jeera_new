<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminPlanetEditFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter, $id) {  	
		
	 
	$this->add(array(
    	'name' => 'group_title',
     	'required' => true,
     	'filters' => array(
  			array('name' => 'StripTags'),
			array('name' => 'StringTrim'),
			array('name' => 'HtmlEntities'),				 
     	),
		'validators' => array(
        	array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),
			array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_group','field' => 'group_title', 'exclude' => array ('field' => 'group_id', 'value' => $id),  'adapter' => $dbAdapter),),          
		),
		));	
		
		 
	$this->add(array(
    	'name' => 'group_seo_title',
     	'required' => true,
     	'filters' => array(
  			array('name' => 'StripTags'),
			array('name' => 'StringTrim'),
			array('name' => 'HtmlEntities'),				 
     	),
		'validators' => array(
        	array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),
			array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_group','field' => 'group_seo_title', 'exclude' => array ('field' => 'group_id', 'value' => $id), 'adapter' => $dbAdapter),),                
		),
		));
		
		$this->add(array(
    	'name' => 'group_location',
     	'required' => false,
     	'filters' => array(
  			array('name' => 'StripTags'),
			array('name' => 'StringTrim'),
			array('name' => 'HtmlEntities'),				 
     	),
		'validators' => array(
        	array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),
		),
		));	
				
		$this->add(array(
    	'name' => 'group_discription',
     	'required' => true,
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