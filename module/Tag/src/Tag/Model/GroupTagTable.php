<?php
namespace Tag\Model;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select; 
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Expression as predicate;
class GroupTagTable extends AbstractTableGateway
{
    protected $table = 'y2m_group_tag';
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new GroupTag());
        $this->initialize();
    }	
	#this will fetch the single array based on group tag Id. primary key
	public function getGroupTag($group_tag_id)
    {
        $select = new Select;
		$select->from('y2m_group_tag')
    		->join('y2m_tag', 'y2m_tag.tag_id = y2m_group_tag.group_tag_tag_id', array('tag_title'))
			->join('y2m_group', 'y2m_group.group_id = y2m_group_tag.group_tag_group_id', array('group_title'))
			->where(array('y2m_group_tag.group_tag_id' => $group_tag_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());		 
		$data = array();
		if($resultSet->count()){					  
			foreach($resultSet as $row){
				//echo "hahah<pre>";print_r($row);exit;
				$data =$row;
			}		  
		} 	
		return $data;	 
    }	
	#this will fetch the single array based on user tag Id. primary key
	public function checkGroupTag($group_id, $tag_id)
    {
        $group_id  = (int) $group_id;
		$tag_id  = (int) $tag_id;
        $rowset = $this->select(array('group_tag_group_id' => $group_id, 'group_tag_tag_id' => $tag_id));
        $row = $rowset->current();        
        return $row;
    }		
	#This function will be used in Edit tag. It will check for the given User ID, the same tag id won't exist(exluding row the group_tag_id)
	public function checkGroupTagForEdit($group_id, $tag_id, $group_tag_id)
    {
        $group_id  = (int) $group_id;
		$tag_id  = (int) $tag_id;
		$group_tag_id  = (int) $group_tag_id;	
        $rowset = $this->select(array('group_tag_group_id' => $group_id, 'group_tag_tag_id' => $tag_id));
        $row = $rowset->current();  
		#if combination exist, check it should not be the current one. Exclude for the current group_tag_id
		if(isset($row->group_tag_id) && !empty($row->group_tag_id) && trim($row->group_tag_id!=$group_tag_id)){
					 return $row;
		}else{ //if(isset($row->group_tag_id) && !empty($row->group_tag_id) && trim($row->group_tag_id!=$group_tag_id))
					 return array();
		} //else of //if(isset($row->group_tag_id) && !empty($row->group_tag_id) && trim($row->group_tag_id!=$group_tag_id))      
    }		
    public function fetchAll()
    {
		$select = new Select;
		$select->from('y2m_group_tag')
    		->join('y2m_tag', 'y2m_tag.tag_id = y2m_group_tag.group_tag_tag_id', array('tag_title'))
			->join('y2m_group', 'y2m_group.group_id = y2m_group_tag.group_tag_group_id', array('group_title'))
			->order('y2m_group_tag.group_tag_id ASC'); 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;
    }	
	#This function will fetch all the groups register for that Tag
	public function fetchAllGroupsOfTag($tag_id)
    {
      	$tag_id  = (int) $tag_id;
	  	$resultSet = $this->select(array('group_tag_tag_id' => $tag_id));
        return $resultSet;
    }	
	#This function will fetch all the tags register for that grpup
	public function fetchAllTagsOfGroup($group_id,$limit = '',$offset='',$tag_string='')
    {      	 
		$select = new Select;
		$select->from('y2m_group_tag')
    		->join('y2m_tag', 'y2m_tag.tag_id = y2m_group_tag.group_tag_tag_id', array('tag_title','tag_id'))
			->join('y2m_group', 'y2m_group.group_id = y2m_group_tag.group_tag_group_id', array('group_title'))
			->where(array('y2m_group_tag.group_tag_group_id' => $group_id));
		if($tag_string!=''){
			$select->where->like('tag_title','%'.$tag_string.'%');
		}
		$statement = $this->adapter->createStatement();
		if($limit!=''){
			$select->limit($limit);
			$select->offset($offset);
		}		
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();//exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());		 
		return $resultSet;	
    }	
    public function saveGroupTag(GroupTag $tag){
       $data = array(
            'group_tag_group_id' => $tag->group_tag_group_id,
            'group_tag_added_timestamp'  => $tag->group_tag_added_timestamp,
			'group_tag_added_ip_address'  => $tag->group_tag_added_ip_address,
			'group_tag_tag_id'  => $tag->group_tag_tag_id				
        );
        $group_tag_id = (int)$tag->group_tag_id;
        if ($group_tag_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getGroupTag($group_tag_id)) {
                $this->update($data, array('group_tag_id' => $group_tag_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    public function deleteGroupTag($group_tag_id)
    {
        $this->delete(array('group_tag_id' => $group_tag_id));
    }
	public function saveTags($planet_id,$tag_id){
		$select = new Select;
		$select->from('y2m_group_tag')
			   ->columns(array("group_tag_id"))
			   ->where(array('y2m_group_tag.group_tag_group_id' => $planet_id,"group_tag_tag_id"=>$tag_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());		 
		$row = $resultSet->current();
		if(!empty($row)&&$row->group_tag_id){	 
			;
		}else{
			$data['group_tag_group_id'] = $planet_id;
			$data['group_tag_tag_id'] = $tag_id;
			$this->insert($data);
			$inserted_id = $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
		}
		return $tag_id;
	}
	public function RemoveGroupUnusedTags($planet_id,$tag_id){
		 $sql = "SELECT group_tag_tag_id FROM y2m_group_tag WHERE group_tag_tag_id NOT IN ( ".implode(',',$tag_id).") AND group_tag_group_id =  $planet_id"; 
		 $statement = $this->adapter-> query($sql);  		
		 $resultSet = new ResultSet();
		 $tags_array = array();
		 $resultSet->initialize($statement->execute());
		 foreach($resultSet as $result){
			if($result->group_tag_tag_id){
				$select = new Select;
				$select->from('y2m_activity_tag')
			   ->columns(array("group_tag_id"))
			   ->join("y2m_group_activity","y2m_group_activity.group_activity_id = y2m_activity_tag.activity_id",array())
			   ->where(array('y2m_group_activity.group_activity_group_id' => $planet_id))
			   ->where(array('y2m_activity_tag.group_tag_id' => $result->group_tag_tag_id))
			   ->where->greaterThan("group_activity_start_timestamp",new Expression('now()'));
				$statement = $this->adapter->createStatement();
				$select->prepareStatement($this->adapter, $statement);
				$resultSet = new ResultSet();
				//echo $select->getSqlString();//exit;
				$resultSet->initialize($statement->execute());		 
				$row = $resultSet->current();
				if(!empty($row)&&$row->group_tag_id){  
					$tags_array[] =  $row->group_tag_id;
				}else{ 
					$this->delete(array('group_tag_tag_id' => $result->group_tag_tag_id,'group_tag_group_id'=>$planet_id));
				}
			}
		 }
		  
		 return $tags_array;
	}
	public function AddGroupTags($data){
		$this->insert($data);
		return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
	 }
	 public function getCountOfAllGroupTags($search=''){
		$select = new Select;
		$select->from('y2m_group')
			->columns(array(new Expression('COUNT(distinct(y2m_group.group_id)) as tag_count')))
			->join('y2m_group_tag', 'y2m_group.group_id = y2m_group_tag.group_tag_group_id', array(),'left')
			->join('y2m_tag', 'y2m_tag.tag_id = y2m_group_tag.group_tag_tag_id', array('tags'=>new Expression('GROUP_CONCAT(y2m_tag.tag_title)')),'left') ;
		$select->where(array("y2m_group.group_parent_group_id !=0 "));
		if($search!=''){
			$select->where->like('y2m_group.group_title',$search.'%')->or->like('y2m_tag.tag_title',$search.'%');		
		}
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return  $resultSet->current()->tag_count;
	 }
	public function getAllGroupTags($limit,$offset,$field='group_title',$order = 'ASC',$search=''){		 
       	$select = new Select;
		$select->from('y2m_group')
			->columns(array('group_title','group_id'))
    		->join('y2m_group_tag', 'y2m_group.group_id = y2m_group_tag.group_tag_group_id', array(),'left')
			->join('y2m_tag', 'y2m_tag.tag_id = y2m_group_tag.group_tag_tag_id', array('tags'=>new Expression('GROUP_CONCAT(y2m_tag.tag_title)')),'left')		 
			->group('y2m_group.group_id');
		$select->where(array("y2m_group.group_parent_group_id !=0 "));
		if($search!=''){
			$select->where->like('y2m_group.group_title',$search.'%')->or->like('y2m_tag.tag_title',$search.'%');		
		}
		$select->limit($limit);
		$select->offset($offset);
		$select->order($field.' '.$order);
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;	   
	}
	public function fetchAllTagsOfPlanet($planet_id){
		$select = new Select;
		$select->from('y2m_group_tag')
    		->join('y2m_tag', 'y2m_tag.tag_id = y2m_group_tag.group_tag_tag_id', array('tag_title','tag_id'))			 
			->where(array('y2m_group_tag.group_tag_group_id' => $planet_id))
			->order(array('y2m_group_tag.group_tag_tag_id ASC'));		 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->buffer();
	}
	public function removeTagFromGroup($planet_id,$tag_id){ 
		return $this->delete(array('group_tag_group_id' => $planet_id,'group_tag_tag_id' =>$tag_id )); 
	}
	public function fetchAllTagsExceptGroup($group_id,$limit,$offset){
		$subselect = new Select;
		$subselect->from('y2m_group_tag')
			->columns(array(new Expression('distinct(y2m_group_tag.group_tag_tag_id) as tag_id')))    					 
			;
		$subselect->where(array('y2m_group_tag.group_tag_group_id' => $group_id));
		$select = new Select;
		$select->from('y2m_tag')
				->columns(array('tag_id','tag_title'))
				->where->addPredicate(new predicate('y2m_tag.tag_id NOT IN(?)',array($subselect)));
		$select->limit($limit);
		$select->offset($offset);
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;
	}
	public function RemoveAllGroupTags($group_id){
		$sql = "DELETE FROM y2m_group_tag WHERE y2m_group_tag. 	group_tag_group_id = ".$group_id;	
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		return true;
	}
}