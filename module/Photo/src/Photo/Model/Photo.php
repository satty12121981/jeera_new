<?php
namespace Photo\Model;

class Photo
{  
    public $photo_id;
    public $photo_name;
    public $photo_added_timestamp;
	public $photo_added_ip_address_address;
	public $photo_status;
	public $photo_caption;
	public $photo_discription;
	public $photo_album_id;
	public $photo_user_id;
	public $photo_location;
	public $photo_view_counter;
	public $photo_visible;
	
	 
	
	/**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->photo_id     = (isset($data['photo_id'])) ? $data['photo_id'] : null;
        $this->photo_name = (isset($data['photo_name'])) ? $data['photo_name'] : null;
        $this->photo_added_timestamp  = (isset($data['photo_added_timestamp'])) ? $data['photo_added_timestamp'] : null;
		$this->photo_added_ip_address  = (isset($data['photo_added_ip_address'])) ? $data['photo_added_ip_address'] : null;
		$this->photo_status  = (isset($data['photo_status'])) ? $data['photo_status'] : null;
		$this->photo_caption  = (isset($data['photo_caption'])) ? $data['photo_caption'] : null;
		$this->photo_discription  = (isset($data['photo_discription'])) ? $data['photo_discription'] : null;
		$this->photo_album_id  = (isset($data['photo_album_id'])) ? $data['photo_album_id'] : null;		
		$this->photo_user_id  = (isset($data['photo_user_id'])) ? $data['photo_user_id'] : null;		
		$this->photo_location  = (isset($data['photo_location'])) ? $data['photo_location'] : null;		
		$this->photo_view_counter  = (isset($data['photo_view_counter'])) ? $data['photo_view_counter'] : null;		
		$this->photo_visible  = (isset($data['photo_visible'])) ? $data['photo_visible'] : null;		
    }
	
	// Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
	
	 
}