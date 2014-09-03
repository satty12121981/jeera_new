<?php
// module/Album/src/Album/Model/AlbumTable.php:
namespace Album\Model;
use Zend\Db\Sql\Select , \Zend\Db\Sql\Where;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class AlbumTagTable extends AbstractTableGateway
{
    protected $table ='y2m_album_tags';
    public function __construct(Adapter $adapter){ 
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new AlbumTag());
        $this->initialize();
    } 
    public function getTags($data_id){	
	 $select = new Select;
	 $select->from('y2m_album_tags')
     ->join('y2m_user', 'y2m_album_tags.album_tag_user_id = y2m_user.user_id')    
     ->where(array('y2m_album_tags.album_tag_data_id' =>  $data_id));
     //echo $select->getSqlString(); die();   
	 $statement = $this->adapter->createStatement();
	 $select->prepareStatement($this->adapter, $statement);    
	 $resultSet = new ResultSet();
	 $resultSet->initialize($statement->execute());  
	 return $resultSet->toArray();
    }
    public function saveAlbumTag(AlbumTag $albumtag){ 
        $data = array(
            'album_tag_data_id' => $albumtag->album_tag_data_id,
            'album_tag_user_id'  => $albumtag->album_tag_user_id,
			'album_tag_added_user'  => $albumtag->album_tag_added_user,
			'album_tag_xaxis'  => $albumtag->album_tag_xaxis,
			'album_tag_yaxis'  => $albumtag->album_tag_yaxis,
			);
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
    }
    public function deleteAlbumTag($tag_id){
        $this->delete(array(
            'album_tag_id' => $tag_id,
        ));
		return "success";
    }
	public function getAllTaggedUsers($data_id){
		$select = new Select;
		$select->from('y2m_album_tags')
		->join('y2m_user', 'y2m_album_tags.album_tag_user_id = y2m_user.user_id',array('user_id'))    
		->where(array('y2m_album_tags.album_tag_data_id' =>  $data_id));
		//echo $select->getSqlString(); die();   
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);    
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());  
		return $resultSet->buffer();
	}
	public function getTagDetails($id){
		$id  = (int) $id;
        $rowset = $this->select(array(
            'album_tag_id' => $id,
        ));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
	}
}