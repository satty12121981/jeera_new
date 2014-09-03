<?php
// module/Album/src/Album/Model/AlbumTable.php:
namespace Album\Model;
use Zend\Db\Sql\Select , \Zend\Db\Sql\Where;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
class AlbumDataTable extends AbstractTableGateway
{
    protected $table ='y2m_album_data';
    public function __construct(Adapter $adapter){ 
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new AlbumData());
        $this->initialize();
    }
    public function getAlbum($id,$limit=0,$offset=0){	
	    $select = new Select;
		$select->from('y2m_album')
			   ->join('y2m_album_data', 'y2m_album.album_id = y2m_album_data.parent_album_id')    
			   ->where(array('y2m_album_data.parent_album_id' =>  $id));  
		if($limit){ $select->limit($limit); }
		if($offset){ $select->offset($offset);  }
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString(); die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());  
		return $resultSet->toArray();
	
    }
	public function getGroupAlbumData($data_id){		
		$select = new Select;
		$select->from('y2m_album_data')
		->join('y2m_album', 'y2m_album_data.parent_album_id = y2m_album.album_id',array("album_group_id","album_user_id"))    
		->where(array('y2m_album_data.data_id' =>  $data_id));   
	   $statement = $this->adapter->createStatement();
	   $select->prepareStatement($this->adapter, $statement);
		// echo $select->getSqlString(); die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());  
		return $resultSet->current();
	}
	public function getalbumdata($id) {
        $id  = (int) $id;
        $rowset = $this->select(array(
            'data_id' => $id,
        ));
        $row = $rowset->current();      
        return $row;
    }
    public function saveAlbumData(AlbumData $albumdata){ 
        $data = array(
            'parent_album_id' => $albumdata->parent_album_id,
            'data_type'  => $albumdata->data_type,
			'data_content'  => $albumdata->data_content,
			'added_user_id' => $albumdata->added_user_id
			);       
		$this->insert($data);
		return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();       
    }
    public function deletedataAlbum($data_id)
    {
        $this->delete(array(
            'data_id' => $data_id,
        ));
		return "success";
    }
	public function getAlbumDataCount($album_id){
		$select = new Select;
		$select->from('y2m_album_data')
			->columns(array(new Expression('COUNT(y2m_album_data.data_id) as total_data')))    
			->where(array('y2m_album_data.parent_album_id' =>  $album_id));   
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);  
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());  
		return $resultSet->current();
	}
	public function addToAlbumData($data){
		$this->insert($data);
		return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
	}
	public function getAlbumDetailsFromData($data_id){
		$select = new Select;
		$select->from('y2m_album_data')
			->columns(array('data_type','data_content','added_user_id'))
			->join("y2m_album","y2m_album.album_id =y2m_album_data.parent_album_id",array('*'))
			->where(array('y2m_album_data.data_id' =>  $data_id));   
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);  
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());  
		return $resultSet->current();
	}
	public function getTaggedUsersWithGroupSettings($data_id,$group_id){
		$select = new Select;
		$select->from('y2m_album_tags')
			->columns(array('album_tag_user_id'))
			->join('y2m_user_group_settings',new Expression('y2m_user_group_settings.user_id = y2m_album_tags.album_tag_user_id AND y2m_user_group_settings.group_id = '.$group_id),array('activity','member','discussion','media','group_announcement'),'left')
			->where(array('y2m_album_tags.album_tag_data_id' =>  $data_id));   
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);  
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());  
		return $resultSet->buffer();
	}
	 public function updateAlbumData($albumdata){ 
        $data = array(
            'parent_album_id' => $albumdata['parent_album_id'],
            'data_type'  => $albumdata['data_type'],
			'data_content'  => $albumdata['data_content'],
			'added_user_id' => $albumdata['added_user_id']
			);       
		return $this->update($data,array('data_id' => $albumdata['data_id']));
		//return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();       
    }
}
