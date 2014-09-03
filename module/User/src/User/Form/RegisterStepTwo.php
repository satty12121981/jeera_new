<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Element\Captcha as Captcha;

class RegisterStepTwo extends Form
{ 
     protected $captchaElement= null;
	
	public function __construct($selectAllCountry,$selectCity)
    {
        // we want to ignore the name passed
        parent::__construct('user');
        $this->setAttribute('method', 'post');   
		
		$this->add(array(
     'type' => 'Zend\Form\Element\Csrf',
     'name' => 'csrf',
     'options' => array(
             'csrf_options' => array(
                     'timeout' => 600,
					 'salt' => 'unique'
             )
     )
 ));     
       
	  $this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'user_profile_country_id',  
				  
				'options' => array(
							/*'label' => 'user_profile_country_id',*/
							'disable_inarray_validator' => true,
		                	'value_options' => $selectAllCountry,
						),
				'attributes' => array(
					'id' => 'user_profile_country_id',
					'value' => '2', //set selecarray()ted to '1',
					'class' => 'styled',
					)   
				));   
			$this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'user_profile_city',  
				  
				'options' => array(
							/*'label' => 'user_profile_country_id',*/
							'disable_inarray_validator' => true,
		                	'value_options' => $selectCity,
						),
				'attributes' => array(
					'id' => 'user_profile_city',
					'value' => '2', //set selecarray()ted to '1',
					'class' => 'styled',
					)   
				));   				
			for($i=1;$i<=31;$i++){
				$date_array[$i] = $i;
			}
			$this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'user_profile_dob_dd',  
				  
				'options' => array(
							/*'label' => 'user_profile_country_id',*/
							'disable_inarray_validator' => true,
		                	'value_options' => $date_array,
						),
				'attributes' => array(
					'id' => 'user_profile_dob_dd',
					'value' => '1', //set selecarray()ted to '1',
					'class' => 'styled',
					)   
				));
				for($i=1;$i<=12;$i++){
				$month_array[$i] = $i;
				}
				$this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'user_profile_dob_mm',  
				  
				'options' => array(
							/*'label' => 'user_profile_country_id',*/
							'disable_inarray_validator' => true,
		                	'value_options' => $month_array,
						),
				'attributes' => array(
					'id' => 'user_profile_dob_mm',
					'value' => '1', //set selecarray()ted to '1',
					'class' => 'styled',
					)   
				));
			$year=date("Y");
			for($i=1950;$i<=$year;$i++){
				$year_array[$i] = $i;
			}
			$this->add(array(     
				'type' => 'Zend\Form\Element\Select',       
				'name' => 'user_profile_dob_yy',  
				  
				'options' => array(
							/*'label' => 'user_profile_country_id',*/
							'disable_inarray_validator' => true,
		                	'value_options' => $year_array,
						),
				'attributes' => array(
					'id' => 'user_profile_dob_yy',
					'value' => '1', //set selecarray()ted to '1',
					'class' => 'styled',
					)   
				));
		  
				$this->add(array(
					 'type' => 'Zend\Form\Element\Radio',
					 'name' => 'user_gender',
					 'options' => array(							  
							 'value_options' => array(
									 'F' => array(
										'value'=>'F',
										'attributes'=>array(
											'id' => 'r1'
										),
										'label'=>'F',
										'label_attributes' => array('for'=>'r1'),
									 ),
									 'M' => array(
										'value'=>'M',
										'attributes'=>array(
											'id' => 'r2'
										),
										'label'=>'M',
										'label_attributes' => array('for'=>'r2'),
									 ),
									
							 ),
					 )
				));
		 
		
		 

		$this->add(array(
					'name' => 'user_profile_profession',
					'type' => 'Text',
					 'options' => array(
						 
					),
					'attributes' => array(
						'id' => 'user_profile_profession',
						'placeholder' => 'Profession:',
					) 
					
				));
		
		 $this->add(array(
            'name' => 'user_profile_profession_at',
            'type' => 'Text',
			 'options' => array(
                
            ),
			'attributes' => array(
						'id' => 'user_profile_profession_at',	
						'placeholder' => 'University/Company:',						
					)
            
        )); 
       
	 
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Next',
                'id' => 'submitbutton',
				'class' => 'next_button blue-butn',
            ),
        ));
		
		$this->add(array(
            'name' => 'reset',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Skip',
                'id' => 'resetbutton',
				'class' => 'next_button blue-butn',
            ),
        ));
    }
}