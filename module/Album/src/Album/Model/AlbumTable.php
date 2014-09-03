<?php
// module/Album/src/Album/Model/AlbumTable.php:
namespace Album\Model;

use Zend\Db\Sql\Select , \Zend\Db\Sql\Where;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Predicate\Expression;

class AlbumTable extends AbstractTableGateway
{
    protected $table ='y2m_album';
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Album());
        $this->initialize();
    }
    public function fetchAll($id){
		 $result = new ResultSet();		 
		 $sql = "SELECT a.*, b.*, CASE WHEN a.album_cover_photo_id IS NOT NULL THEN (SELECT DISTINCT data_content FROM y2m_album_data WHERE data_id = a.album_cover_photo_id GROUP BY a.album_id) ELSE b.data_content END AS cover_photo FROM y2m_album a LEFT JOIN y2m_album_data b ON a.album_id = b.parent_album_id WHERE a.album_group_id =".$id." GROUP BY a.album_id"; 	 
		 $statement = $this->adapter-> query($sql); 
		 $results = $statement -> execute();	
		 return $results;
    }
    public function getalbumrow($id){
        $id  = (int) $id;
        $rowset = $this->select(array(
            'album_id' => $id,
        ));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function saveAlbum(Album $album){
        $data = array(
            'album_title' => $album->album_title,
			'album_seotitle' => $album->album_seotitle,
            'album_group_id'  => $album->album_group_id,
			'album_user_id'  => $album->album_user_id,
			'album_location'  => $album->album_location,
			'album_added_ip_address'  => $album->album_added_ip_address,
			'album_status'  => $album->album_status,
        );
        $id = (int) $album->album_id;
        if ($id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } elseif ( $id ) {
			$data_update = array('album_title' => $album->album_title,
			'album_location' => $album->album_location);
            $this->update(			
                $data_update,
                array(
                    'album_id' => $id,
                )
            );
			return $id;
        } else {
            throw new \Exception('Form id does not exist');
        }
    }
	public function addcoverpic($data_id,$album_id){
				$data = array(
				'album_cover_photo_id' => $data_id,
				
				);
					$this->update(
							$data,
							array(
								'album_id' => $album_id,
							)
						);
						return "success";
			}
	public function updateAlbum(Album $album){
		$data = array(
		'album_title' => $album->album_title,
		'album_location'  => $album->album_location,
			);
		$this->update(
				$data,
				array(
					'album_id' => $album->album_id,
				)
			);
			return "success";
	}	
	public function updatecoverAlbum($data_id){
		$data = array(
		'album_cover_photo_id' => "",
		
		);
			$this->update(
					$data,
					array(
						'album_cover_photo_id' => $data_id,
					)
				);
				return "success";
	}
    public function deleteAlbum($group_id,$album_id){
	    $sql = "DELETE FROM y2m_album, y2m_album_data USING y2m_album LEFT JOIN y2m_album_data ON y2m_album.album_id = y2m_album_data.parent_album_id WHERE y2m_album.album_id=".$album_id."";
	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		return "success";
    }
	public function getalbumFromSeotitle($seotitle){
		$select = new Select;
			$select->from('y2m_album')    			 
				->where(array('y2m_album.album_seotitle' => $seotitle));
				 
	   		$statement = $this->adapter->createStatement();
			$select->prepareStatement($this->adapter, $statement);
			$resultSet = new ResultSet();
			$resultSet->initialize($statement->execute());	  
			return $resultSet->current();
	}
	public function fetchAllUserAlbum($user_id){
		 $result = new ResultSet();
		 
		 $sql = "SELECT a.*, b.*, CASE WHEN a.album_cover_photo_id IS NOT NULL THEN (SELECT DISTINCT data_content FROM y2m_album_data WHERE data_id = a.album_cover_photo_id GROUP BY a.album_id) ELSE b.data_content END AS cover_photo FROM y2m_album a LEFT JOIN y2m_album_data b ON a.album_id = b.parent_album_id WHERE a.album_group_id =0 AND album_user_id = ".$user_id." GROUP BY a.album_id"; 
	 
		 $statement = $this->adapter-> query($sql); 
		 $results = $statement -> execute();
	
		 return $results;
    }
	public function fetchTaggedUserAlbum($user_id){
		$select = new Select;
		$select->from('y2m_album_data')
			   ->columns(array("data_content"))
			   ->join("y2m_album_tags","y2m_album_tags.album_tag_data_id = y2m_album_data.data_id",array())
			   ->join("y2m_album","y2m_album_data.parent_album_id = y2m_album.album_id",array("album_group_id","album_user_id","album_id"))
			->where(array('y2m_album_tags.album_tag_user_id' => $user_id));
			 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->current();
	}
	public function fetchTaggedUserAlbumData($user_id,$offset,$limit){
		$select = new Select;
		$select->from('y2m_album_data')
			   ->columns(array("data_content","data_id","data_type","parent_album_id","added_user_id"))
			   ->join("y2m_album_tags","y2m_album_tags.album_tag_data_id = y2m_album_data.data_id",array())
			   ->join("y2m_album","y2m_album_data.parent_album_id = y2m_album.album_id",array("album_group_id","album_user_id","album_id","album_location"))
			->where(array('y2m_album_tags.album_tag_user_id' => $user_id));
			 $select->limit($limit); 
		  $select->offset($offset);  
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString(); die();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->buffer();
	}
	public function fetchTaggedUserAlbumDataCount($user_id){
		$select = new Select;
		$select->from('y2m_album_data')
			   ->columns(array(new Expression('COUNT(y2m_album_data.data_id) as total_data')))
			   ->join("y2m_album_tags","y2m_album_tags.album_tag_data_id = y2m_album_data.data_id",array())
			   ->join("y2m_album","y2m_album_data.parent_album_id = y2m_album.album_id",array())
			->where(array('y2m_album_tags.album_tag_user_id' => $user_id));
			   
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString(); die();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->current();
	}
	public function getAllGroupAlbumDetails($group_id){
		$select = new Select;
		$select->from('y2m_album_data')
			->columns(array('data_id','data_content','data_type'))
			->join("y2m_album","y2m_album_data.parent_album_id = y2m_album.album_id",array('album_id','album_title',))
			->where(array('y2m_album.album_group_id' => $group_id));
			$statement = $this->adapter->createStatement();
		//echo $select->getSqlString(); die();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->buffer();
	}
	public function RemoveAllGroupAlbumDetails($group_id){
		$sql = "DELETE FROM  y2m_like WHERE like_system_type_id = 3 AND like_refer_id IN (SELECT  comment_id FROM  y2m_comment INNER JOIN  y2m_album_data ON y2m_album_data.data_id = y2m_comment.comment_refer_id AND y2m_comment.comment_system_type_id = 4 INNER JOIN  y2m_album ON y2m_album.album_id = y2m_album_data.parent_album_id  WHERE y2m_album.album_group_id = ".$group_id.")";	
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE FROM y2m_like WHERE like_system_type_id = 4 AND like_refer_id IN (SELECT  data_id FROM  y2m_album_data  INNER JOIN  y2m_album ON y2m_album.album_id = y2m_album_data.parent_album_id  WHERE y2m_album.album_group_id = ".$group_id.")";	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE FROM y2m_comment WHERE comment_system_type_id = 4 AND comment_refer_id IN (SELECT  data_id FROM  y2m_album_data  INNER JOIN  y2m_album ON y2m_album.album_id = y2m_album_data.parent_album_id  WHERE y2m_album.album_group_id = ".$group_id.")";	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE FROM  y2m_album_tags WHERE album_tag_data_id  IN (SELECT  data_id FROM  y2m_album_data  INNER JOIN  y2m_album ON y2m_album.album_id = y2m_album_data.parent_album_id  WHERE y2m_album.album_group_id = ".$group_id.")";	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE y2m_album_data FROM y2m_album_data INNER JOIN y2m_album ON y2m_album.album_id = y2m_album_data.parent_album_id  WHERE y2m_album.album_group_id = ".$group_id;	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE  FROM y2m_album WHERE y2m_album.album_group_id = ".$group_id;	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		return true;
	}
}