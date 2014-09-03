<?php
namespace Groups\Model;
class Groups
{  	
    const Group_Thumb_Path        = 'group/thumb/';			#image path of Group in Group page
	const Group_Timeline_Path     = 'group/timeline/';		#image path if Group Time line
	const Group_Thumb_Smaller     = 'group/smallThumb/';		#image path if Group Time line
	const Group_Minumum_Bytes	  = 10;
	const File_Delimiter          = '-';
	const File_Seperator          = '/';
	public $group_id;
    public $group_title;
    public $group_seo_title;
	public $group_status;
	public $group_discription;
	public $group_added_timestamp;
	public $group_added_ip_address;
	public $group_parent_group_id;
	public $group_location;
	public $group_photo_id;
	public $group_modified_timestamp;
	public $group_modified_ip_address; 
	public $group_view_counter;	
	public $group_city_id;
	public $group_country_id;
	public $y2m_group_location_lat;
	public $y2m_group_location_lng;
	public $group_web_address;
	public $group_welcome_message_members;
	 
    public function exchangeArray($data)
    {
        $this->group_id     = (isset($data['group_id'])) ? $data['group_id'] : null;
        $this->group_title = (isset($data['group_title'])) ? $data['group_title'] : null;
        $this->group_seo_title  = (isset($data['group_seo_title'])) ? $data['group_seo_title'] : null;
		$this->group_status  = (isset($data['group_status'])) ? $data['group_status'] : null;
		$this->group_discription  = (isset($data['group_discription'])) ? $data['group_discription'] : null;
		$this->group_added_timestamp  = (isset($data['group_added_timestamp'])) ? $data['group_added_timestamp'] : null;
		$this->group_added_ip_address  = (isset($data['group_added_ip_address'])) ? $data['group_added_ip_address'] : null;
		$this->group_parent_group_id  = (isset($data['group_parent_group_id'])) ? $data['group_parent_group_id'] : null;
		$this->group_location  = (isset($data['group_location'])) ? $data['group_location'] : null;
		$this->group_photo_id  = (isset($data['group_photo_id'])) ? $data['group_photo_id'] : null;
		$this->group_modified_timestamp  = (isset($data['group_modified_timestamp'])) ? $data['group_modified_timestamp'] : null;
		$this->group_modified_ip_address  = (isset($data['group_modified_ip_address'])) ? $data['group_modified_ip_address'] : null;
		$this->y2m_group_location_lat  = (isset($data['y2m_group_location_lat'])) ? $data['y2m_group_location_lat'] : null;
		$this->y2m_group_location_lng  = (isset($data['y2m_group_location_lng'])) ? $data['y2m_group_location_lng'] : null;
		$this->group_view_counter  = (isset($data['group_view_counter'])) ? $data['group_view_counter'] : null;		 
		$this->group_city_id  = (isset($data['group_city_id'])) ? $data['group_city_id'] : null;	
		$this->group_country_id  = (isset($data['group_country_id'])) ? $data['group_country_id'] : null;
		$this->group_web_address  = (isset($data['group_web_address'])) ? $data['group_web_address'] : null;		
		$this->group_welcome_message_members  = (isset($data['group_welcome_message_members'])) ? $data['group_welcome_message_members'] : null;		 
    }
	
	// Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy() {
        return get_object_vars($this);
    }
	
	#This function will be used to pass to send select box input for All Groups/Sub Groups
	public function selectFormatAllGroup($data) {
		 
		$selectObject = array();
		
		foreach($data as $group){			 
			$selectObject[$group->group_id] = $group->group_title;			
		}	
		 	
		return $selectObject;	//return blank array
	} 
	
	#This function will be used only in Admin Planet Tags. 
	public function selectFormatAllGroupForPlanetTags($data) {
		 
		$selectObject = array();
		foreach($data as $group){			 
			$selectObject[$group->group_id] = $group->parent_title." -- ".$group->group_title;			
		}			 	
		return $selectObject;	//return blank array
	} 
	
	public function generateGroupImageName() {
	  $id = uniqid();
      $id = base_convert($id, 16, 2);
      $id = str_pad($id, strlen($id) + (8 - (strlen($id) % 8)), '0', STR_PAD_LEFT);

      $chunks = str_split($id, 8);
      //$mask = (int) base_convert(IDGenerator::BIT_MASK, 2, 10);

      $id = array();
      foreach ($chunks as $key => $chunk) {
         //$chunk = str_pad(base_convert(base_convert($chunk, 2, 10) ^ $mask, 10, 2), 8, '0', STR_PAD_LEFT);
         if ($key & 1) {  // odd
            array_unshift($id, $chunk);
         } else {         // even
            array_push($id, $chunk);
         }
      }

      return base_convert(implode($id), 2, 36);
    }
	
    public function uploadGroupImage($type, $file, $rootPath, $adapter, $name) {
	
		//This function will return the name of file upload. False in ase of error uploading 
		//@type is "Galaxy or Planet"
		//@file is a array file
		//@rootPath will be absolute path to upload directory for example C:/wamp/www/1625/public/datagd/
		//@adapter is the service adapter
		//@name will be any name that you want to add with file
		
		$filename_prefix = $type.self::File_Delimiter;
		
		$adapter->setDestination($rootPath.self::Group_Thumb_Path.$type);
		
		#remove the while space and space froms string
		$name = str_replace(' ', '', $name);
		$name = preg_replace('/\s+/', '', $name);
		
		#create Unique File Name
		$filename = $filename_prefix.$name.self::File_Delimiter.self::generateGroupImageName().".".end(explode(".", $file['name']));
		$adapter->addFilter('File\Rename',array('target' => $adapter->getDestination().self::File_Seperator.$filename,'overwrite' => true));
		
		if($adapter->receive($file['name'])) {
			return $filename;
		} 
		else { 
			return false;
		} 			 
		return false;	
	}
}