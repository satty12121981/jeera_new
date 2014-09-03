<?php 
namespace User\Model;

use Zend\InputFilter\InputFilter;

class UserFriendRequest  
{
    public $user_friend_request_id;
    public $user_friend_request_sender_user_id;
    public $user_friend_request_friend_user_id;
	public $user_friend_request_status;
	public $user_friend_request_added_timestamp;

    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */ 
    public function exchangeArray($data)
    {
        $this->user_friend_request_id     = (isset($data['user_friend_request_id'])) ? $data['user_friend_request_id'] : null;
        $this->user_friend_request_sender_user_id = (isset($data['user_friend_request_sender_user_id'])) ? $data['user_friend_request_sender_user_id'] : null;
        $this->user_friend_request_friend_user_id  = (isset($data['user_friend_request_friend_user_id'])) ? $data['user_friend_request_friend_user_id'] : null;
		$this->user_friend_request_status 	  = (isset($data['user_friend_request_status'])) ? $data['user_friend_request_status'] : null;
		$this->user_friend_request_added_timestamp  = (isset($data['user_friend_request_added_timestamp'])) ? $data['user_friend_request_added_timestamp'] : null;
    }

    // Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
	  
}
