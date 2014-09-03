<?php
namespace User\Model;
use Zend\InputFilter\InputFilter;
 
class UserCoverPhoto
{
    public $cover_photo_id;
    public $cover_user_id;
    public $cover_photo;
	public $cover_photo_left;
	public $cover_photo_top;
    public $cover_photo_added_Timestamp;
    public $cover_photo_added_ip;
 	 	
    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */ 
    public function exchangeArray($data)
    {
        $this->cover_photo_id     = (isset($data['cover_photo_id'])) ? $data['cover_photo_id'] : null;
        $this->cover_user_id = (isset($data['cover_user_id'])) ? $data['cover_user_id'] : null;
        $this->cover_photo  = (isset($data['cover_photo'])) ? $data['cover_photo'] : null;
		$this->cover_photo_left  = (isset($data['cover_photo_left'])) ? $data['cover_photo_left'] : null;
		$this->cover_photo_top  = (isset($data['cover_photo_top'])) ? $data['cover_photo_top'] : null;
		$this->cover_photo_added_Timestamp  = (isset($data['cover_photo_added_Timestamp'])) ? $data['cover_photo_added_Timestamp'] : null;
		$this->cover_photo_added_ip  = (isset($data['cover_photo_added_ip'])) ? $data['cover_photo_added_ip'] : null;
		 
		
    }

    // Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }	  
}