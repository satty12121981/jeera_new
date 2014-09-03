<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminUserTagFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter)	
	{	
		#validation for Tag. Required
		$this->add(array(
			'name' => 'user_tag_tag_id',
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
		
		#validation for User. Required
		$this->add(array(
			'name' => 'user_tag_user_id',
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
    }
}
