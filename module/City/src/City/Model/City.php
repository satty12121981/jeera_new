<?php 

namespace City\Model;
class City
{  
    public $city_id;
    public $country_id;
    public $name;
	 

    #This function will be used to assign variables in class. Please do not change the name of this function
	#It is required for zend
	public function exchangeArray($data)
    {
        $this->city_id     = (isset($data['city_id'])) ? $data['city_id'] : null;
        $this->country_id = (isset($data['country_id'])) ? $data['country_id'] : null;
        $this->name  = (isset($data['name'])) ? $data['name'] : null;	  		
    }
	
	 // Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

  
	
}