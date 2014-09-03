<?php
namespace Spam\Model;
class Spam 
{  	
	const File_Delimiter          = '-';
	const File_Seperator          = '/';
	public $spam_id;
    public $spam_sytem_type_id;
    public $spam_problem_id;
	public $spam_content;
	public $spam_added_timestamp;
	public $spam_added_ip_address;
	public $spam_report_user_id;
	public $spam_target_user_id;
	public $spam_refer_id;
	
    public function exchangeArray($data)
    {
        $this->spam_id     = (isset($data['spam_id'])) ? $data['spam_id'] : null;
        $this->spam_system_type_id = (isset($data['spam_system_type_id'])) ? $data['spam_system_type_id'] : null;
        $this->spam_problem_id	  = (isset($data['spam_problem_id'])) ? $data['spam_problem_id'] : null;
		$this->spam_other_content  = (isset($data['spam_other_content'])) ? $data['spam_other_content'] : null;
		$this->spam_added_timestamp  = (isset($data['spam_added_timestamp'])) ? $data['spam_added_timestamp'] : null;
		$this->spam_added_ip_address  = (isset($data['spam_added_ip_address'])) ? $data['spam_added_ip_address'] : null;
		$this->spam_report_user_id  = (isset($data['spam_report_user_id'])) ? $data['spam_report_user_id'] : null;
		$this->spam_target_user_id  = (isset($data['spam_target_user_id'])) ? $data['spam_target_user_id'] : null;
		$this->spam_refer_id  = (isset($data['spam_refer_id'])) ? $data['spam_refer_id'] : null;
    }
	
	#Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}