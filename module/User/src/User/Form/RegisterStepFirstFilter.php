<?php
namespace User\Form;
use Zend\InputFilter\InputFilter;
class RegisterStepFirstFilter extends InputFilter
{
    private $dbAdapter;
	public function __construct($dbAdapter) {         
		$this->add(array(
            'name' => 'user_given_name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(					 
                array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 1,'max' => 50,),),
            ),
        ));	
		
		$this->add(array(
            'name' => 'user_email',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
				 
            ),
			'validators' => array(
        					array('name' => 'EmailAddress'),
							array('name' => 'StringLength', 'options' => array('encoding' => 'UTF-8', 'min' => 1, 'max' => 200,),),
							array('name' => 'Db\NoRecordExists', 'options' => array('table' => 'y2m_user','field' => 'user_email',  'adapter' => $dbAdapter),),
                
			),
        ));

        $this->add(array(
            'name' => 'user_password',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 3,'max' => 60,),
                ),
            ),
        ));
		
		$this->add(array(
            'name' => 'user_retype_password',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
				array('name' => 'HtmlEntities'),
            ),
            'validators' => array(
                		array('name' => 'StringLength','options' => array('encoding' => 'UTF-8', 'min' => 3,'max' => 60,),),
						array('name' => 'identical','options' => array('token' => 'user_password'),)	
						
           		 ),
        ));
		
		
		
    }
}
