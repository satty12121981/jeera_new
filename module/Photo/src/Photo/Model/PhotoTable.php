<?php
namespace Photo\Model;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
class PhotoTable extends AbstractTableGateway
{
    protected $table = 'y2m_photo'; 

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Photo());

        $this->initialize();
    }
    public function fetchAll()
    {
       $resultSet = $this->select();
        return $resultSet;
    }

    public function getPhoto($photo_id)
    {
        $photo_id  = (int) $photo_id;
        $rowset = $this->select(array('photo_id' => $photo_id));
        $row = $rowset->current();
        
        return $row;
    }

    public function savePhoto(Photo $photo)
    {
       $data = array(
            'photo_name' => $photo->photo_name,
            'photo_added_timestamp'  => $photo->photo_added_timestamp,
			'photo_added_ip_address'  => $photo->photo_added_ip_address,
			'photo_status'  => $photo->photo_status,
			'photo_caption'  => $photo->photo_caption,
			'photo_discription'  => $photo->photo_discription,
			'photo_album_id'  => $photo->photo_album_id,	
			'photo_user_id'  => $photo->photo_user_id,	
			'photo_location'  => $photo->photo_location,	
			'photo_view_counter'  => $photo->photo_view_counter,	
			'photo_visible'  => $photo->photo_visible
        );

        $photo_id = (int)$photo->photo_id;
        if ($photo_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getPhoto($photo_id)) {
                $this->update($data, array('photo_id' => $photo_id));
            }
        }
    }

    public function deletePhoto($photo_id)
    {
        $this->delete(array('photo_id' => $photo_id));
    }
	
	public function selectFormatAllPhoto($data){
		//This function will return the format of  '0' => 'Apple', '1' => 'Mango' of photos		
				 
		$selectObject =array();

		foreach($data as $photo){
			$selectObject[$photo->photo_id] = $photo->photo_name;
			//print_r($row);
		}		
		return $selectObject;	//return blank array
	} 
}