<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminPlanetFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter) {  	
		
	 
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
			array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_group','field' => 'group_title',  'adapter' => $dbAdapter),),                
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
			array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_group','field' => 'group_seo_title',  'adapter' => $dbAdapter),),                
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
    	'name' => 'group_country_id',
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
		$this->add(array(
    	'name' => 'group_city_id',
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
		$this->add(array(
    	'name' => 'group_web_address',
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
		$this->add(array(
    	'name' => 'group_welcome_message_members',
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
		$this->add(array(
    	'name' => 'group_discription',
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
