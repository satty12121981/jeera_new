<?php

namespace Photo\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class GroupPhotoTable extends AbstractTableGateway
{
    protected $table = 'y2m_group_photo'; 

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new GroupPhoto());

        $this->initialize();
    }
    public function fetchAll()
    {
       $resultSet = $this->select();
        return $resultSet;
    }

   public function getGroupPhoto($group_photo_id)
    {
        $group_photo_id  = (int) $group_photo_id;
        $rowset = $this->select(array('group_photo_id' => $group_photo_id));
        $row = $rowset->current();
        
        return $row;
    }
	
	

    public function saveGroupPhoto(GroupPhoto $group_photo)
    {
        $data = array(
            'group_photo_photo_id' => $photo->group_photo_photo_id,
            'group_photo_group_id'  => $photo->group_photo_group_id,
			'group_photo_album_id'  => $photo->group_photo_album_id,
			'group_cover_photo_id'  => $photo->group_cover_photo_id,
        );

        $group_photo_id = (int)$group_photo->group_photo_id;
		
        if ($group_photo_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getPhoto($group_photo_id)) {
                $this->update($data, array('group_photo_id' => $group_photo_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteGroupPhoto($group_photo_id)
    {
        $this->delete(array('group_photo_id' => $group_photo_id));
    }
	
	
	
	public function selectFormatAllPhoto($data){
		//This function will return the format of  '0' => 'Apple', '1' => 'Mango' of photos		
				 
				$selectObject =array();
				
				foreach($data as $group_photo){
					$selectObject[$group_photo->group_photo_id] = $group_photo->group_photo_name;
					//print_r($row);
				}		
			return $selectObject;	//return blank array
	} 
}