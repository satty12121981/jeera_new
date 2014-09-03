<?php
namespace Photo\Model;

class GroupPhoto
{  
    public $group_photo_id;
    public $group_photo_photo_id;
    public $group_photo_group_id;
	public $group_photo_album_id;
	public $group_cover_photo_id;
	
	/**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->group_photo_id     = (isset($data['group_photo_id'])) ? $data['group_photo_id'] : null;
        $this->group_photo_photo_id = (isset($data['group_photo_photo_id'])) ? $data['group_photo_photo_id'] : null;
        $this->group_photo_group_id  = (isset($data['group_photo_group_id'])) ? $data['group_photo_group_id'] : null;
		$this->group_photo_album_id  = (isset($data['group_photo_album_id'])) ? $data['group_photo_album_id'] : null;		
		$this->group_cover_photo_id  = (isset($data['group_cover_photo_id'])) ? $data['group_cover_photo_id'] : null;		
    }
	
	// Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
	
	 
}