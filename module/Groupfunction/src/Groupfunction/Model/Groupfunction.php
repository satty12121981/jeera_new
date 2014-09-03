<?php 

namespace Groupfunction\Model;
class Groupfunction
{  
    public $group_functions_id;
    public $functions;
   
	 

    #This function will be used to assign variables in class. Please do not change the name of this function
	#It is required for zend
	public function exchangeArray($data)
    {
        $this->group_functions_id     = (isset($data['group_functions_id'])) ? $data['group_functions_id'] : null;
        $this->functions = (isset($data['functions'])) ? $data['functions'] : null;        		
    }
	
	 // Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

  
	
}