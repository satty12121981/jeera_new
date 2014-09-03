<?php
namespace Admin\Form;
use Zend\InputFilter\InputFilter;
class AdminCountryEditFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter, $id) {  	
		
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
							array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_country','field' => 'country_title', 'exclude' => array ('field' => 'country_id', 'value' => $id),  'adapter' => $dbAdapter),),	
									                
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
							array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_country','field' => 'country_code','exclude' => array ('field' => 'country_id', 'value' => $id),  'adapter' => $dbAdapter),),                
			),
        )); 		
		
    }
}
