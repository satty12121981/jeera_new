<?php
namespace Message\Model;

class MessageAttachment
{  
    public $attachment_id;
	public $message_id;
	public $attachment_type;
    public $attachment_element;
    public $attachment_date;
    public function exchangeArray($data)
    {
        $this->attachment_id     = (isset($data['attachment_id'])) ? $data['attachment_id'] : null;
        $this->message_id = (isset($data['message_id'])) ? $data['message_id'] : null;
		$this->attachment_type = (isset($data['attachment_type'])) ? $data['attachment_type'] : null;
        $this->attachment_element  = (isset($data['attachment_element'])) ? $data['attachment_element'] : null;
		$this->attachment_date  = (isset($data['attachment_date'])) ? $data['attachment_date'] : null;
		
    }
	
	// Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
		
}