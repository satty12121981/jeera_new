<?php
namespace User\Form;
use Zend\InputFilter\InputFilter;
class RegisterStepTwoFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter) {         
		$this->add(array(
            'name' => 'user_profile_country_id',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
				 
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 500,),
                ),
            ),
        ));	
		
		$this->add(array(
            'name' => 'user_profile_city',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
				 
            ),
			'validators' => array(      
							array('name' => 'Alpha',),  					 
							array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 300,),							
                ),
			),
        ));

         
		$this->add(array(
            'name' => 'user_profile_dob_dd',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 50,),						
						/*array('name'    => 'Between', 'options' => array('min' => '1901-01-01', 'max' => date('Y-m-d')),),	*/					
                ),
            ),
        ));
		$this->add(array(
            'name' => 'user_profile_dob_mm',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 50,),						
						/*array('name'    => 'Between', 'options' => array('min' => '1901-01-01', 'max' => date('Y-m-d')),),	*/					
                ),
            ),
        ));
		$this->add(array(
            'name' => 'user_profile_dob_yy',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 50,),						
						/*array('name'    => 'Between', 'options' => array('min' => '1901-01-01', 'max' => date('Y-m-d')),),	*/					
                ),
            ),
        ));
		 
		
		$this->add(array(
            'name' => 'user_profile_profession',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 200,),),
						 
						
           		 ),
        ));
		
		$this->add(array(
            'name' => 'user_profile_profession_at',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 200,),),
						 
						
           		 ),
        ));
		
		
		
		
		
    }
}
