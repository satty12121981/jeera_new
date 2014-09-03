<?php
namespace Message\Model;

class Message
{  
    public $user_message_id;
	public $user_message_receiver_id;
	public $user_message_sender_id;
    public $user_message_content;
    public $user_message_added_ip_address;
	public $user_message_status;
	public $user_message_type;

    const MESSAGE_FILE_PATH = 'message/';
    const SENDER_USER_NAME = '[[SenderUserName]]';
    const SENDER_USER_PHOTO = '[[SenderUserPhoto]]';
    const SENDER_USER_MESSAGE = '[[SenderUserMessage]]';

    public function exchangeArray($data)
    {
        $this->user_message_id     = (isset($data['user_message_id'])) ? $data['user_message_id'] : null;
        $this->user_message_receiver_id = (isset($data['user_message_receiver_id'])) ? $data['user_message_receiver_id'] : null;
		$this->user_message_sender_id = (isset($data['user_message_sender_id'])) ? $data['user_message_sender_id'] : null;
        $this->user_message_content  = (isset($data['user_message_content'])) ? $data['user_message_content'] : null;
		$this->user_message_added_ip_address  = (isset($data['user_message_added_ip_address'])) ? $data['user_message_added_ip_address'] : null;
		$this->user_message_status  = (isset($data['user_message_status'])) ? $data['user_message_status'] : null;
		$this->user_message_type  = (isset($data['user_message_type'])) ? $data['user_message_type'] : null;

        $this->user_message_sender_deleted  = (isset($data['user_message_sender_deleted'])) ? $data['user_message_sender_deleted'] : null;
        $this->user_message_receiver_deleted  = (isset($data['user_message_receiver_deleted'])) ? $data['user_message_receiver_deleted'] : null;
        $this->user_message_sender_viewed  = (isset($data['user_message_sender_viewed'])) ? $data['user_message_sender_viewed'] : null;
        $this->user_message_receiver_viewed  = (isset($data['user_message_receiver_viewed'])) ? $data['user_message_receiver_viewed'] : null;
        $this->user_message_sender_deleted_date  = (isset($data['user_message_sender_deleted_date'])) ? $data['user_message_sender_deleted_date'] : null;
        $this->user_message_receiver_deleted_date  = (isset($data['user_message_receiver_deleted_date'])) ? $data['user_message_receiver_deleted_date'] : null;
        $this->user_message_sender_viewed_date  = (isset($data['user_message_sender_viewed_date'])) ? $data['user_message_sender_viewed_date'] : null;
        $this->user_message_receiver_viewed_date  = (isset($data['user_message_receiver_viewed_date'])) ? $data['user_message_receiver_viewed_date'] : null;
    }
	
	// Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
		
}