<?php
namespace Tag\Model;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Expression as predicate;
class UserTagTable extends AbstractTableGateway
{ 
    protected $table = 'y2m_user_tag';
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new UserTag());
        $this->initialize();
    }	
	#this will fetch the single array based on user tag Id. primary key
	public function getUserTag($user_tag_id)
    {
        $select = new Select;
		$select->from('y2m_user_tag')
    		->join('y2m_tag', 'y2m_tag.tag_id = y2m_user_tag.user_tag_tag_id', array('tag_title'))
			->join('y2m_user', 'y2m_user.user_id = y2m_user_tag.user_tag_user_id', array('user_first_name', 'user_last_name'))
			->where(array('y2m_user_tag.user_tag_id' => $user_tag_id));
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
	public function checkUserTag($user_id, $tag_id)
    {
        $user_id  = (int) $user_id;
		$tag_id  = (int) $tag_id;
        $rowset = $this->select(array('user_tag_user_id' => $user_id, 'user_tag_tag_id' => $tag_id));
        $row = $rowset->current();        
        return $row;
    }	
	#This function will be used in Edit tag. It will check for the given User ID, the same tag id won't exist(exluding row the user_tag_id)
	public function checkUserTagForEdit($user_id, $tag_id, $user_tag_id)
    {
        $user_id  = (int) $user_id;
		$tag_id  = (int) $tag_id;
		$user_tag_id  = (int) $user_tag_id;	
        $rowset = $this->select(array('user_tag_user_id' => $user_id, 'user_tag_tag_id' => $tag_id));
        $row = $rowset->current();  
		#if combination exist, check it should not be the current one. Exclude for the current user_tag_id
		if(isset($row->user_tag_id) && !empty($row->user_tag_id) && trim($row->user_tag_id!=$user_tag_id)){
					 return $row;
		}else{ //if(isset($row->user_tag_id) && !empty($row->user_tag_id) && trim($row->user_tag_id!=$user_tag_id))
					 return array();
		} //else of //if(isset($row->user_tag_id) && !empty($row->user_tag_id) && trim($row->user_tag_id!=$user_tag_id))      
    }	
	#this will fetch all user tags
    public function fetchAll()
    {
       	$select = new Select;
		$select->from('y2m_user_tag')
    		->join('y2m_tag', 'y2m_tag.tag_id = y2m_user_tag.user_tag_tag_id', array('tag_title'))
			->join('y2m_user', 'y2m_user.user_id = y2m_user_tag.user_tag_user_id', array('user_first_name', 'user_last_name'))
			->order(array('y2m_user_tag.user_tag_id ASC'));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;		
    }	
	#This function will fetch all the Users register for that Tag
	public function fetchAllUsersOfTag($tag_id)
    {
      	$select = new Select;
		$select->from('y2m_user_tag')
    		->join('y2m_tag', 'y2m_tag.tag_id = y2m_user_tag.user_tag_tag_id', array('tag_title'))
			->join('y2m_user', 'y2m_user.user_id = y2m_user_tag.user_tag_user_id', array('user_first_name', 'user_last_name'))
			->where(array('y2m_user_tag.user_tag_tag_id' => $tag_id))
			->order(array('y2m_user_tag.user_tag_id ASC'));		 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;
    }	
	#This function will fetch all the tags of a user
	public function fetchAllTagsOfUser($user_id)
    {
      	$select = new Select;
		$select->from('y2m_user_tag')
    		->join('y2m_tag', 'y2m_tag.tag_id = y2m_user_tag.user_tag_tag_id', array('tag_title','tag_id'))			 
			->where(array('y2m_user_tag.user_tag_user_id' => $user_id))
			->order(array('y2m_user_tag.user_tag_id ASC'));		 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->buffer();
    }
    public function saveUserTag(UserTag $tag){
       $data = array(
            'user_tag_user_id' => $tag->user_tag_user_id,
            'user_tag_tag_id'  => $tag->user_tag_tag_id,
			'user_tag_added_timestamp'  => $tag->user_tag_added_timestamp,
			'user_tag_added_ip_address'  => $tag->user_tag_added_ip_address				
        );
        $user_tag_id = (int)$tag->user_tag_id;
        if ($user_tag_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getUserTag($user_tag_id)) {
                $this->update($data, array('user_tag_id' => $user_tag_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    public function deleteUserTag($user_tag_id)
    {
        $this->delete(array('user_tag_id' => $user_tag_id));
    }
	 public function removeUserTag($tag_id,$user_id)
    {
       return $this->delete(array('user_tag_user_id' => $user_id,'user_tag_tag_id' =>$tag_id ));
    }
	public function getAllUserTags($limit,$offset,$field='user_given_name',$order = 'ASC',$search=''){		 
       	$select = new Select;
		$select->from('y2m_user')
			->columns(array('user_first_name', 'user_last_name','user_given_name','user_id'))
    		->join('y2m_user_tag', 'y2m_user.user_id = y2m_user_tag.user_tag_user_id', array(),'left')
			->join('y2m_tag', 'y2m_tag.tag_id = y2m_user_tag.user_tag_tag_id', array('tags'=>new Expression('GROUP_CONCAT(y2m_tag.tag_title)')),'left')			 
			->group('y2m_user.user_id');
		if($search!=''){
			$select->where->like('y2m_user.user_given_name',$search.'%')->or->like('y2m_tag.tag_title',$search.'%');		
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
	public function getCountOfAllUserTags($search=''){
		$select = new Select;
		$select->from('y2m_user')
			->columns(array(new Expression('COUNT(distinct(y2m_user.user_id)) as tag_count')))
    		->join('y2m_user_tag', 'y2m_user.user_id = y2m_user_tag.user_tag_user_id', array(),'left')
			->join('y2m_tag', 'y2m_tag.tag_id = y2m_user_tag.user_tag_tag_id', array('tags'=>new Expression('GROUP_CONCAT(y2m_tag.tag_title)')),'left') ;
		if($search!=''){
			$select->where->like('y2m_user.user_given_name',$search.'%')->or->like('y2m_tag.tag_title',$search.'%');		
		}
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return  $resultSet->current()->tag_count;
	}
	public function fetchAllTagsExceptUser($user_id,$limit,$offset){
		$subselect = new Select;
		$subselect->from('y2m_user_tag')
			->columns(array(new Expression('distinct(y2m_user_tag.user_tag_tag_id) as tag_id')))    					 
			;
		$subselect->where(array('y2m_user_tag.user_tag_user_id' => $user_id));
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
}