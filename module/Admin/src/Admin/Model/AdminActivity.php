<?php 

namespace Admin\Model;

use Zend\InputFilter\InputFilter;

class AdminActivity
{
    public $group_activity_id;
    public $group_activity_content;
    public $group_activity_owner_user_id;
	public $group_activity_group_id;
    public $group_activity_status;
	public $group_activity_added_timestamp;
    public $group_activity_added_ip_address;
	public $group_activity_start_timestamp;
    public $group_activity_title;
	public $group_activity_location;
    public $group_activity_modifed_timestamp;
	public $group_activity_modified_ip_address;
	public $user_given_name;
   
	protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->group_activity_id     = (isset($data['group_activity_id'])) ? $data['group_activity_id'] : null;
        $this->group_activity_content = (isset($data['group_activity_content'])) ? $data['group_activity_content'] : null;
        $this->group_activity_owner_user_id  = (isset($data['group_activity_owner_user_id'])) ? $data['group_activity_owner_user_id'] : null;
		$this->group_activity_group_id     = (isset($data['group_activity_group_id'])) ? $data['group_activity_group_id'] : null;
        $this->group_activity_status = (isset($data['group_activity_status'])) ? $data['group_activity_status'] : null;
        $this->group_activity_added_timestamp  = (isset($data['group_activity_added_timestamp'])) ? $data['group_activity_added_timestamp'] : null;
		$this->group_activity_added_ip_address     = (isset($data['group_activity_added_ip_address'])) ? $data['group_activity_added_ip_address'] : null;
        $this->group_activity_title = (isset($data['group_activity_title'])) ? $data['group_activity_title'] : null;
        $this->group_activity_location  = (isset($data['group_activity_location'])) ? $data['group_activity_location'] : null;
		$this->group_activity_modifed_timestamp     = (isset($data['group_activity_modifed_timestamp'])) ? $data['group_activity_modifed_timestamp'] : null;
        $this->group_activity_modified_ip_address = (isset($data['group_activity_modified_ip_address'])) ? $data['group_activity_modified_ip_address'] : null;
		$this->user_given_name = (isset($data['user_given_name'])) ? $data['user_given_name'] : null;
    }
	    // Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}