<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminCountryFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter) {  	
		
		#validation for country title. It needs to be UNIQUE
		$this->add(array(
            'name' => 'country_title',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
				 
            ),
			'validators' => array(
        					array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),
							array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_country','field' => 'country_title',  'adapter' => $dbAdapter),),                
			),
        ));
		
		#validation for country code. It needs to be UNIQUE
		$this->add(array(
            'name' => 'country_code',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
				 
            ),
			'validators' => array(
        					array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 50,),),
							array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_country','field' => 'country_code',  'adapter' => $dbAdapter),),                
			),
        )); 		
		
    }
}
