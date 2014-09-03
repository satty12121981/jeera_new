<?php
namespace Photo\Model;

class Album
{  
    public $album_id;
    public $album_added_timestamp;
    public $album_added_ip_address;
	public $album_status;
	public $album_title;
	public $album_discription;
	public $album_user_id;
	public $album_cover_photo_id;
	public $album_location;
	public $album_view_counter;
	public $album_modified_timestamp;
	public $album_modified_ip_address;
	
	 
	/**
     * Used by ResultSet to pass each database row to the entity
     */

    public function exchangeArray($data)
    {
        $this->album_id     = (isset($data['album_id'])) ? $data['photo_id'] : null;
        $this->album_added_timestamp = (isset($data['album_added_timestamp'])) ? $data['album_added_timestamp'] : null;
        $this->album_added_ip_address  = (isset($data['album_added_ip_address'])) ? $data['album_added_ip_address'] : null;
		$this->album_status  = (isset($data['album_status'])) ? $data['album_status'] : null;
		$this->album_title  = (isset($data['album_title'])) ? $data['album_title'] : null;
		$this->album_discription  = (isset($data['album_discription'])) ? $data['album_discription'] : null;
		$this->album_user_id  = (isset($data['album_user_id'])) ? $data['album_user_id'] : null;
		$this->album_cover_photo_id  = (isset($data['album_cover_photo_id'])) ? $data['album_cover_photo_id'] : null;		
		$this->album_location  = (isset($data['album_location'])) ? $data['album_location'] : null;		
		$this->album_view_counter  = (isset($data['album_view_counter'])) ? $data['album_view_counter'] : null;		
		$this->album_modified_timestamp  = (isset($data['album_modified_timestamp'])) ? $data['album_modified_timestamp'] : null;		
		$this->album_modified_ip_address  = (isset($data['album_modified_ip_address'])) ? $data['album_modified_ip_address'] : null;		
    }
	
	// Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
 
	
}