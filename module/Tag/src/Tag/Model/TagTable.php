<?php
namespace Tag\Model;
use Zend\Db\Sql\Select , \Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Crypt\BlockCipher;	#for encryption
use Zend\Db\Sql\Expression;
class TagTable extends AbstractTableGateway
{
    protected $table = 'y2m_tag'; 

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Tag());

        $this->initialize();
    }
    public function fetchAll()
    {
       $resultSet = $this->select();
       return $resultSet;
    }

   public function getTag($tag_id)
    {
        $tag_id  = (int) $tag_id;
        $rowset = $this->select(array('tag_id' => $tag_id));
        $row = $rowset->current();
        
        return $row;
    }

    public function saveTag(Tag $tag)
    {
       $data = array(
            'tag_title' => $tag->tag_title,
            'tag_added_timestamp'  => $tag->tag_added_timestamp,
			'tag_added_ip_address'  => $tag->tag_added_ip_address			
        );

        $tag_id = (int)$tag->tag_id;
        if ($tag_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getTag($tag_id)) {
                $this->update($data, array('tag_id' => $tag_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteTag($tag_id)
    {
        $this->delete(array('tag_id' => $tag_id));
    }	
	
	//This function will encrypt the tag id for form
	public function encryptTagArray(){
		$data = $this->fetchAll();
		//This function will return the format of  '0' => 'Apple', '1' => 'Mango' of tag but in encrypted format				 
		$blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
		$blockCipher->setKey('*&hhjj()_$#(&&^$%gdgfd^&*%fgfg'); 		
		$selectObject =array();				
		foreach($data as $tag){
			$selectObject[$blockCipher->encrypt($tag->tag_id)] = $tag->tag_title;
			//print_r($row);
		}		
		return $selectObject;	//return blank array
	
	}
	public function getPopularUserTags($page_start,$limit,$tag_string=''){
		$inner_select = new Select;
		$inner_select->from('y2m_user_tag')
					 ->columns(array(new Expression('COUNT(y2m_user_tag.user_tag_tag_id) as tag_count'),'user_tag_tag_id'=>'user_tag_tag_id'));
		$inner_select->group('y2m_user_tag.user_tag_tag_id');			 
		$select = new Select;
		$select->from('y2m_tag')
			   ->columns(array('tag_id'=>'tag_id','tag_title'=>'tag_title'))
			   ->join(array('temp' => $inner_select), 'y2m_tag.tag_id = temp.user_tag_tag_id',array(),'left');
		if($tag_string!=''){
			$select->where->like('tag_title','%'.$tag_string.'%');
		}
		$select->order(array('tag_count DESC'));
		$select->limit($limit);
		$select->offset($page_start);
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;	
	}
	public function getTagDetails($tags){  
		$sql = "SELECT * FROM y2m_tag WHERE  tag_id IN ( ".implode(',',$tags).") "; 
		$statement = $this->adapter-> query($sql);  		
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet;
	}
	public function listExceptSelected($user_id,$search_str=''){
		$sql = "SELECT * FROM y2m_tag WHERE  tag_id NOT IN ( SELECT user_tag_tag_id FROM y2m_user_tag WHERE y2m_user_tag.user_tag_user_id = ".$user_id.") "; 
		if($search_str!=''){
			$sql.=' AND y2m_tag.tag_title LIKE "%'.$search_str.'%" ';
		}
		$statement = $this->adapter-> query($sql);  		
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->buffer(); 
	}
	public function getAllTags($limit,$offset,$field="tag_id",$order='ASC',$search=''){ 
		$select = new Select;
		$usersubselect = new select;
		$groupsubselect = new select;	
		$activitysubselect	= new select;	
		$usersubselect->from('y2m_user_tag')
			->columns(array(new Expression('COUNT(y2m_user_tag.user_tag_id) as user_count'),'user_tag_tag_id'))
			->group(array('user_tag_tag_id'))
			;
		$groupsubselect->from('y2m_group_tag')
			->columns(array(new Expression('COUNT(y2m_group_tag.group_tag_id) as group_count'),'group_tag_tag_id'))
			->group(array('group_tag_tag_id'))
			;
		$activitysubselect->from('y2m_activity_tag')
			->columns(array(new Expression('COUNT(y2m_activity_tag.id) as activity_count'),'group_tag_id'))
			->group(array('group_tag_id'))
			;
		$select->from('y2m_tag')
				->columns(array('tag_id'=>'tag_id','tag_title'=>'tag_title'))
				->join(array('temp' => $usersubselect), 'temp.user_tag_tag_id = y2m_tag.tag_id',array('user_count'),'left')
				->join(array('temp1' => $groupsubselect), 'temp1.group_tag_tag_id = y2m_tag.tag_id',array('group_count'),'left')
				->join(array('temp2' => $activitysubselect), 'temp2.group_tag_id = y2m_tag.tag_id',array('activity_count'),'left');
		$select->limit($limit);
		$select->offset($offset);
		$select->order($field.' '.$order);
		if($search!=''){
			$select->where->like('y2m_tag.tag_title',$search.'%');		
		}
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());			 	
		return  $resultSet->buffer();
	}
	public function getCountOfAllTags($search=''){
		$select = new Select;
		$select->from('y2m_tag')		
			   ->columns(array(new Expression('COUNT(y2m_tag.tag_id) as tag_count')));
		if($search!=''){
			$select->where->like('y2m_tag.tag_title',$search.'%');		
		}
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return  $resultSet->current()->tag_count;
	}
 
}