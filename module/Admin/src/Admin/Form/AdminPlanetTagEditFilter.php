<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminPlanetTagEditFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter)	
	{ 	
		 
		
		#validation for Planet. Required
		$this->add(array(
			'name' => 'group_tag_group_id',
			'required' => true,
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
			),
			'validators' => array(
				array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),				 
			),
		));	
		
		#validation for Tag. Required
		$this->add(array(
			'name' => 'group_tag_tag_id',
			'required' => true,
			'filters' => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
			),
			'validators' => array(array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),				 	
			),
		));		
    } //public function __construct($dbAdapter, $id)
}
