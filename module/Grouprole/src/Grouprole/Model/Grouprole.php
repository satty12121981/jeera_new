<?php 

namespace Grouprole\Model;
class Grouprole
{  
    public $group_roles_id;
    public $group_roles_name;
   
	 

    #This function will be used to assign variables in class. Please do not change the name of this function
	#It is required for zend
	public function exchangeArray($data)
    {
        $this->group_roles_id     = (isset($data['group_roles_id'])) ? $data['group_roles_id'] : null;
        $this->group_roles_name = (isset($data['group_roles_name'])) ? $data['group_roles_name'] : null;        		
    }
	
	 // Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

  
	
}