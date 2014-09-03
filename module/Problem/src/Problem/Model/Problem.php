<?php
namespace Problem\Model;
class Problem 
{  	
	const File_Delimiter          = '-';
	const File_Seperator          = '/';
	public $problem_id;
    public $problem_sytem_type_id;
    public $problem_problem_id;
	public $problem_reason_title;
	public $problem_reason_discription;
	public $problem_added_timestamp;
	public $problem_added_ip_address;
	public $problem_added_user_id;

	
    public function exchangeArray($data)
    {
        $this->problem_id     = (isset($data['problem_id'])) ? $data['problem_id'] : null;
        $this->problem_system_type_id = (isset($data['problem_system_type_id'])) ? $data['problem_system_type_id'] : null;
        $this->problem_reason_title  = (isset($data['problem_reason_title'])) ? $data['problem_reason_title'] : null;
		$this->problem_reason_discription  = (isset($data['problem_reason_discription'])) ? $data['problem_reason_discription'] : null;
		$this->problem_added_timestamp  = (isset($data['problem_added_timestamp'])) ? $data['problem_added_timestamp'] : null;
		$this->problem_added_ip_address  = (isset($data['problem_added_ip_address'])) ? $data['problem_added_ip_address'] : null;
		$this->problem_added_user_id  = (isset($data['problem_added_user_id'])) ? $data['problem_added_user_id'] : null;

    }
	
	#Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}