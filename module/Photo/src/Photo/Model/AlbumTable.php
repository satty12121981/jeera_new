<?php

namespace Photo\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class AlbumTable extends AbstractTableGateway
{
    protected $table = 'y2m_album'; 

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Album());

        $this->initialize();
    }
    public function fetchAll()
    {
       $resultSet = $this->select();
        return $resultSet;
    }

   public function getAlbum($album_id)
    {
        $album_id  = (int) $album_id;
        $rowset = $this->select(array('album_id' => $album_id));
        $row = $rowset->current();
        
        return $row;
    }

    public function saveAlbum(Album $album)
    {
       $data = array(
            'album_added_timestamp' => $album->album_added_timestamp,
            'album_added_ip_address'  => $album->album_added_ip_address,
			'album_status'  => $album->album_status,
			'album_title'  => $album->album_title,
			'album_discription'  => $album->album_discription,
			'album_user_id'  => $album->album_user_id,
			'album_cover_photo_id'  => $album->album_cover_photo_id,	
			'album_location'  => $album->album_location,	
			'album_view_counter'  => $album->album_view_counter,	
			'album_modified_timestamp'  => $album->album_modified_timestamp,	
			'album_modified_ip_address'  => $album->album_modified_ip_address
        );

        $album_id = (int)$album->album_id;
        if ($album_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getAlbum($album_id)) {
                $this->update($data, array('album_id' => $album_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteAlbum($album_id)
    {
        $this->delete(array('album_id' => $album_id));
    }
	
	
	
	public function selectFormatAllAlbum($data){
		//This function will return the format of  '0' => 'Apple', '1' => 'Mango' of albums		
				 
				$selectObject =array();
				
				foreach($data as $album){
					$selectObject[$album->album_id] = $album->album_added_timestamp;
					//print_r($row);
				}		
			return $selectObject;	//return blank array
	} 
}