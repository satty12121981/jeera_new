<?php
namespace Groups\Model;
 
use Zend\Db\Sql\Select ;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
class GroupsTable extends AbstractTableGateway
{ 
    protected $table = 'y2m_group';  
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Groups());
        $this->initialize();
    }
	//this function will fetch all groups
    public function fetchAllGroups(){ 
		$select = new Select;
		$select->from('y2m_group')    			
			->where(array('y2m_group.group_parent_group_id' => "0"))
			->order(array('y2m_group.group_title ASC'));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet;	     
    }
	public function getPlanets($group_id=null){
		$group_id  = (int) $group_id;
		$predicate = new  \Zend\Db\Sql\Where();
		$select = new Select;
		$select->from('y2m_group')				 
			 ->where(array($predicate->greaterThan('y2m_group.group_parent_group_id' , "0"), 'y2m_group.group_parent_group_id' => $group_id))
			 ->order(array('y2m_group.group_id ASC'));		 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
	//	echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->buffer();
	}
	public function getPlanetinfo($group_id){
		$select = new Select;
		$predicate = new  \Zend\Db\Sql\Where();
		$select->from('y2m_group')
		 ->where(array('y2m_group.group_id' => $group_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->current();
	}	 
    public function fetchAllSubGroups($group_id=null,$limit,$offset,$field="group_id",$order='ASC',$search=''){ 	 		
			$predicate = new  \Zend\Db\Sql\Where();
			$sub_select = new Select;
			$sub_select->from('y2m_group')
				   ->columns(array(new Expression('COUNT(y2m_group.group_id) as member_count'),"group_id"))
				   ->join(array('y2m_user_group'=>'y2m_user_group'),'y2m_group.group_id = y2m_user_group.user_group_group_id',array());
			$sub_select->group('y2m_group.group_id');			 
			$sub_select2 = new Select;
			$sub_select2->from('y2m_group')
				   ->columns(array(new Expression('COUNT(y2m_group.group_id) as activity_count'),"group_id"))
				   ->join(array('y2m_group_activity'=>'y2m_group_activity'),'y2m_group.group_id = y2m_group_activity.group_activity_group_id',array());
				  
			$sub_select2->group('y2m_group.group_id');		
			$select = new Select;
			$select->from(array('c' => 'y2m_group'))
				 ->join(array('p' => 'y2m_group'), 'c.group_id = p.group_parent_group_id', array('*'))	
				 ->join(array('temp_member' => $sub_select), 'temp_member.group_id = p.group_id',array('member_count'),'left')
				 ->join(array('temp_activity' => $sub_select2), 'temp_activity.group_id = p.group_id',array('activity_count'),'left')
				 ->where($predicate->greaterThan('p.group_parent_group_id' , "0"))
				 ;
			$select->columns(array('parent_title' => 'group_title'));
			if($group_id!=null){	
				$select->where(array( 'p.group_parent_group_id' => $group_id));
			}
			$select->limit($limit);
			$select->offset($offset);
			$select->order($field.' '.$order);
			if($search!=''){
				$select->where->like('c.group_title',$search.'%')->or->like('p.group_title',$search.'%');				 	
			}			
			$statement = $this->adapter->createStatement();
			$select->prepareStatement($this->adapter, $statement);
			$resultSet = new ResultSet();
			$resultSet->initialize($statement->execute());
			return $resultSet;		 
    }
	public function fetchAllUnapprovedSubGroups($group_id=null,$limit,$offset,$field="group_id",$order='ASC',$search=''){ 	 		
		$predicate = new  \Zend\Db\Sql\Where();
		$select = new Select;
		$select->from(array('c' => 'y2m_group'))
			 ->join(array('p' => 'y2m_group'), 'c.group_id = p.group_parent_group_id', array('*'))				 
			 ->where($predicate->greaterThan('p.group_parent_group_id' , "0"))
			 ->where( array('p.group_status'=>0))
			 ;
		$select->columns(array('parent_title' => 'group_title'));
		if($group_id!=null){	
			$select->where(array( 'p.group_parent_group_id' => $group_id));
		}
		$select->limit($limit);
		$select->offset($offset);
		$select->order($field.' '.$order);
		if($search!=''){
			$select->where->like('c.group_title',$search.'%')->or->like('p.group_title',$search.'%');				 	
		}			
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet;		 
    }
	//this function will fetch all groups
    public function fetchAllPlanets(){      	
		$predicate = new  \Zend\Db\Sql\Where();
		$select = new Select;
		$select->from(array('c' => 'y2m_group'))
				->join(array('p' => 'y2m_group'), 'c.group_id = p.group_parent_group_id', array('*'))
				->where($predicate->greaterThan('p.group_parent_group_id' , "0"))
				->order(array('c.group_id ASC'));
		$select->columns(array('child_title' => 'group_title', 'parent_title' => 'group_title'));
   		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet;
    }		
	//this will fetch single Galaxy from table
    public function getGroup($group_id){
        $group_id  = (int) $group_id;
        $rowset = $this->select(array('group_id' => $group_id,'group_parent_group_id' => '0'));
        $row = $rowset->current();
        return $row;
    }	
	//this will fetch single Galaxy from table
    public function getGroupForName($group_title){
        $rowset = $this->select(array('group_title' => $group_title,'group_parent_group_id' => '0'));
        $row = $rowset->current();
        return $row;
    }	
	#check Planet based based on name
	public function getSubGroupForName($group_title){
        $predicate = new  \Zend\Db\Sql\Where();
        $rowset = $this->select(array('group_title' => $group_title, $predicate->greaterThan('group_parent_group_id' , "0")));
        $row = $rowset->current();
		return $row;
    }	
	//this will fetch single Planet from table
	public function getSubGroup($group_id){
        $group_id  = (int) $group_id;
		$predicate = new  \Zend\Db\Sql\Where();
        $rowset = $this->select(array('group_id' => $group_id, $predicate->greaterThan('group_parent_group_id' , "0")));
		$row = $rowset->current();
        return $row;
    }	
	//this will fetch Group Based on SEO name
	public function getGroupIdFromSEO($group_seo_title){
      	$rowset = $this->select(array('group_seo_title' => $group_seo_title));
		$row = $rowset->current(); 
        return $row;
    }
	public function getGroupForSEO($group_seo_title){
      	$rowset = $this->select(array('group_seo_title' => $group_seo_title, 'group_parent_group_id' => '0'));
		$row = $rowset->current();
        return $row;
    }	
	//this will fetch single Planet Based on SEO name
	public function getSubGroupForSEO($group_seo_title){
        $predicate = new  \Zend\Db\Sql\Where();
        $rowset = $this->select(array('group_seo_title' => $group_seo_title, $predicate->greaterThan('group_parent_group_id' , "0")));
		$row = $rowset->current();
        return $row;
    }	
	//this will save group in a table
    public function saveGroup(Groups $group){
       $data = array(
            'group_title' => $group->group_title,
            'group_seo_title'  => $group->group_seo_title,
			'group_status'  => $group->group_status,
			'group_discription'  => $group->group_discription,
			'group_added_timestamp'  => $group->group_added_timestamp,
			'group_added_ip_address'  => $group->group_added_ip_address,
			'group_parent_group_id'  => 0,
			'group_location'  => $group->group_location,
			'group_city_id'  => $group->group_city_id,
			'group_country_id'  => $group->group_country_id,
			'y2m_group_location_lat'  => $group->y2m_group_location_lat,
			'y2m_group_location_lng'  => $group->y2m_group_location_lng,
			'group_web_address'  => $group->group_web_address,
			'group_welcome_message_members'  => $group->group_welcome_message_members,
			'group_photo_id'  => $group->group_photo_id,
			'group_modified_timestamp'  => $group->group_modified_timestamp,
			'group_modified_ip_address'  => $group->group_modified_ip_address,
			'group_view_counter'  => $group->group_view_counter,		
        );
        $group_id = (int)$group->group_id;
        if ($group_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getGroup($group_id)) {
                $this->update($data, array('group_id' => $group_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
	//this will save sub-group in a table
	public function saveSubGroup(Groups $group){
       $data = array(
            'group_title' => $group->group_title,
            'group_seo_title'  => $group->group_seo_title,
			'group_status'  => $group->group_status,
			'group_discription'  => $group->group_discription,
			'group_added_timestamp'  => $group->group_added_timestamp,
			'group_added_ip_address'  => $group->group_added_ip_address,
			'group_parent_group_id'  => $group->group_parent_group_id,
			'group_location'  => $group->group_location,
			'group_photo_id'  => $group->group_photo_id,
			'group_city_id'  => $group->group_city_id,
			'group_country_id'  => $group->group_country_id,
			'y2m_group_location_lat'  => $group->y2m_group_location_lat,
			'y2m_group_location_lng'  => $group->y2m_group_location_lng,
			'group_web_address'  => $group->group_web_address,
			'group_welcome_message_members'  => $group->group_welcome_message_members,
			'group_modified_timestamp'  => $group->group_modified_timestamp,
			'group_modified_ip_address'  => $group->group_modified_ip_address,
			'group_view_counter'  => $group->group_view_counter,		
        );
        $group_id = (int)$group->group_id;
        if ($group_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getSubGroup($group_id)) {
                $this->update($data, array('group_id' => $group_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
	//it will delete any group
    public function deleteGroup($group_id){
        $this->delete(array('group_id' => $group_id, 'y2m_group.group_parent_group_id' => "0"));
    }	
	public function deleteSubGroup($group_id){
        $predicate = new  \Zend\Db\Sql\Where();
		return $this->delete(array('group_id' => $group_id, $predicate->greaterThan('group_parent_group_id' , "0")));
    }	
	public function fetchSystemType($SystemTypeTitle){
		$SystemTypeTitle  = (string) $SystemTypeTitle;
		$table = new TableGateway('y2m_system_type', $this->adapter, new RowGatewayFeature('system_type_title'));
		$results = $table->select(array('system_type_title' => $SystemTypeTitle));
		$Row = $results->current();
		return $Row;
    }
	public function getSeotitle($group_id){
		$rowset = $this->select(array('group_id' => $group_id));
		$row = $rowset->current();
        return $row;
	}	
    public function getGalexyWithUsers($offset,$limit){
		$select = new Select;
		$select->from(array('A' => 'y2m_group'))
			   ->columns(array(new Expression('COUNT(A.group_id) as member_count'),'group_id'=>'group_id','group_title'=>'group_title','group_seo_title'=>'group_seo_title',))
			   ->join(array("B"=>'y2m_group'),'B.group_parent_group_id = A.group_id',array(),'left')
			   ->join(array("C"=>'y2m_user_group'),'C.user_group_group_id = B.group_id',array(),'left')
			   ->join(array("D"=>'y2m_group_photo'),'D.group_photo_group_id = A.group_id',array('group_photo_photo'),'left')
			   ->where(array("A.group_parent_group_id = 0 AND A.group_status = 1"));
			    
		$select->group('A.group_id');
		$select->order(array('member_count DESC'));
		$select->limit($limit);
		$select->offset($offset);		 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;	
	}
	public function getAllPlanetsWithUsers($user_id,$group_id,$offset,$limit,$string_search,$sort_string){
		$select = new Select;		
		$sub_select = new Select;
		$sub_select->from('y2m_group')
				   ->columns(array(new Expression('COUNT(y2m_group.group_id) as member_count'),"group_id"))
				   ->join(array('y2m_user_group'=>'y2m_user_group'),'y2m_group.group_id = y2m_user_group.user_group_group_id',array());
		$sub_select->group('y2m_group.group_id');
	
		$select->from('y2m_group')
			   ->columns(array('group_id'=>'group_id','group_seo_title'=>'group_seo_title','group_title'=>'group_title','group_discription'=>'group_discription','is_member'=>new Expression('IF(EXISTS(SELECT * FROM y2m_user_group WHERE y2m_user_group.user_group_user_id = '.$user_id.' AND y2m_user_group.user_group_group_id = y2m_group.group_id),1,0)')))
			   ->join(array("B"=>'y2m_group'),'y2m_group.group_parent_group_id = B.group_id',array('parent_seo_title'=>'group_seo_title'),'left')
			   ->join(array('temp_member' => $sub_select), 'temp_member.group_id = y2m_group.group_id',array('member_count'),'left')
			   ->join(array('y2m_album'=>'y2m_album'),'y2m_group.group_id = y2m_album.album_group_id',array(),'left')
			  ->join("y2m_album_data","y2m_group.group_photo_id = y2m_album_data.data_id",array("data_content"),"left");
		if($group_id!=0){
			$select->where(array("y2m_group.group_parent_group_id = $group_id AND y2m_group.group_status = 1"));
		}
		else{
			$select->where(array("y2m_group.group_parent_group_id != 0 AND y2m_group.group_status = 1"));
		}
		if($string_search!=''){			 
			$select->where->like('y2m_group.group_title','%'.$string_search.'%');		 
		}
		if($sort_string!=''){			 
			$select->where->like('y2m_group.group_title',$sort_string.'%');		 
		}
		$select->group('y2m_group.group_id');
		$select->order(array('member_count DESC'));
		$select->limit($limit);
		$select->offset($offset);
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;	
	}
	public function getPlanetDetailsForPalnetView($planet_id,$user_id){
		$sub_select = new Select;
		$sub_select->from('y2m_group')
				   ->columns(array(new Expression('COUNT(y2m_group.group_id) as member_count'),"group_id"))
				   ->join(array('y2m_user_group'=>'y2m_user_group'),'y2m_group.group_id = y2m_user_group.user_group_group_id',array());
		$sub_select->group('y2m_group.group_id');
		$select = new Select;
		$select->from('y2m_group')
			   ->columns(array("group_id"=>"group_id","group_status"=>"group_status","group_title"=>"group_title","group_seo_title"=>"group_seo_title","group_discription"=>"group_discription","group_location"=>"group_location","group_web_address"=>"group_web_address","group_added_timestamp"=>"group_added_timestamp","group_welcome_message_members"=>"group_welcome_message_members","y2m_group_location_lat"=>"y2m_group_location_lat","y2m_group_location_lng"=>"y2m_group_location_lng",'is_member'=>new Expression('IF(EXISTS(SELECT * FROM y2m_user_group WHERE y2m_user_group.user_group_user_id = '.$user_id.' AND y2m_user_group.user_group_group_id = y2m_group.group_id AND y2m_user_group.user_group_status=1),1,0)'),'is_admin'=>new Expression('IF(EXISTS(SELECT * FROM y2m_user_group WHERE y2m_user_group.user_group_user_id = '.$user_id.' AND y2m_user_group.user_group_group_id = y2m_group.group_id AND y2m_user_group.user_group_is_owner = 1),1,0)')))
			   ->join(array("galexy"=>"y2m_group"),"y2m_group.group_parent_group_id = galexy.group_id",array("galexy_title"=>"group_title","galexy_seo_title"=>"group_seo_title"))
			   ->join("y2m_album_data","y2m_group.group_photo_id = y2m_album_data.data_id",array("data_content"),"left")
			   ->join(array('temp_member' => $sub_select), 'temp_member.group_id = y2m_group.group_id',array('member_count'),'left')
			   ->join("y2m_country","y2m_country.country_id = y2m_group.group_country_id",array("country_code_googlemap","country_title"),'left')
			   ->join("y2m_city","y2m_city.city_id = y2m_group.group_city_id",array("city"=>"name"),'left')
			   ->where(array("y2m_group.group_id"=>$planet_id));
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->current();	
	}
	public function getAdminStatus($group_id,$user_id){
		$select = new Select;
		$select->from('y2m_group')
				->columns(array('is_admin'=>new Expression('IF(EXISTS(SELECT * FROM y2m_user_group WHERE y2m_user_group.user_group_user_id = '.$user_id.' AND y2m_user_group.user_group_group_id = y2m_group.group_id AND y2m_user_group.user_group_is_owner = 1),1,0)')))
				->where(array("y2m_group.group_id"=>$group_id));
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->current();			 
	}
	public function updateGroup($data,$group_id){
		return $this->update($data, array('group_id' => $group_id));
	}
	public function getPlanetRoleDetails($planet_id){
		$sql = "SELECT y2m_group_roles.group_roles_id,y2m_group_roles.group_roles_name,y2m_user.user_given_name,y2m_user.user_register_type,y2m_user.user_profile_name,y2m_user.user_id,y2m_user.user_fbid,y2m_user_profile_photo.profile_photo FROM y2m_group_roles LEFT JOIN  y2m_user_group ON y2m_group_roles.group_roles_id = y2m_user_group.user_group_role AND y2m_user_group.user_group_group_id = $planet_id AND y2m_user_group.user_group_role!=0 LEFT JOIN y2m_user ON y2m_user_group.user_group_user_id = y2m_user.user_id LEFT JOIN y2m_user_profile_photo ON y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id";
			    
		$statement = $this->adapter-> query($sql);  		
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
	}
	public function getAllGroupMembersWithoutAdmin($planet_id,$user_id){
		$select = new Select;
		$select->from('y2m_user_group')
			   ->join("y2m_user","y2m_user_group.user_group_user_id = y2m_user.user_id",array("user_id","user_given_name","user_register_type","user_fbid","user_profile_name"))
			   ->join("y2m_user_profile_photo","y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id",array("profile_photo"),"left")
			   ->where(array("y2m_user_group.user_group_group_id"=>$planet_id))
			   ->where->notEqualTo("y2m_user_group.user_group_user_id",$user_id);
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;	
	}
	public function getAllExistingRolesWithoutAdmin($planet_id,$role,$user_id){
		$select = new Select;
		$select->from('y2m_user_group')
			   ->join("y2m_user","y2m_user_group.user_group_user_id = y2m_user.user_id",array("user_id","user_given_name","user_register_type","user_fbid","user_profile_name"))
			   ->join("y2m_user_profile_photo","y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id",array("profile_photo"),"left")
			   ->where(array("y2m_user_group.user_group_group_id"=>$planet_id))
			   ->where(array("y2m_user_group.user_group_role"=>$role))
			   ->where->notEqualTo("y2m_user_group.user_group_user_id",$user_id);
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;
	}
	public function is_member($planet_id,$user_id){
		$select = new Select;
		$select->from('y2m_user_group')
			   ->columns(array("user_group_id"))
			   ->where(array("y2m_user_group.user_group_group_id"=>$planet_id))
			   ->where(array("y2m_user_group.user_group_user_id"=>$user_id))
			   ->where(array("y2m_user_group.user_group_status"=>1));
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		$row  = $resultSet->current(); 
		if(isset($row->user_group_id)&&$row->user_group_id){
			return true;
		}
		else{	
			return false;
		}
	}
	public function AddRoles($planet_id,$user_id,$role){ 
		$select = new Select;
		$select->from('y2m_user_group')
			   ->columns(array("user_group_id"))
			   ->where(array("y2m_user_group.user_group_group_id"=>$planet_id))
			   ->where(array("y2m_user_group.user_group_role"=>$role))
			   ->where(array("y2m_user_group.user_group_user_id"=>$user_id));
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		$row  = $resultSet->current(); 
		if($row && $row->user_group_id){
			return $user_id;
		}
		else{ 
			$sql = "UPDATE y2m_user_group  SET y2m_user_group.user_group_role = ".$role." WHERE  y2m_user_group.user_group_group_id = ".$planet_id ." AND y2m_user_group.user_group_user_id = ".$user_id;		 	    
			$statement = $this->adapter-> query($sql);  		
			$resultSet = new ResultSet();
			$resultSet->initialize($statement->execute());
			 
			 return $user_id;
		} 
	}
	public function removeOldRoles($planet_id,$user_ids,$role){
		$sql = "UPDATE y2m_user_group  SET y2m_user_group.user_group_role = 0 WHERE  y2m_user_group.user_group_group_id = ".$planet_id ." AND y2m_user_group.user_group_user_id NOT IN (".implode(',',$user_ids).') AND y2m_user_group.user_group_role = '.$role;
	 //echo $sql;die();
		$statement = $this->adapter-> query($sql);  		
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
	}
	public function getAllRolesWithPermissions($planet_id){
		$sql = "SELECT y2m_group_roles.*,GROUP_CONCAT(function_id,',')as functions FROM  y2m_group_roles LEFT JOIN y2m_user_group_permissions ON y2m_group_roles.group_roles_id = y2m_user_group_permissions.role_id AND  y2m_user_group_permissions.group_id = $planet_id GROUP BY (y2m_group_roles.group_roles_id)";
	 //echo $sql;die();
		$statement = $this->adapter-> query($sql);  		
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
	}
	public function getAllPermissionsOfRoles($planet_id,$role){
		$sql = "SELECT y2m_group_roles.*,GROUP_CONCAT(function_id,',')as functions FROM  y2m_group_roles LEFT JOIN y2m_user_group_permissions ON y2m_group_roles.group_roles_id = y2m_user_group_permissions.role_id AND  y2m_user_group_permissions.group_id = $planet_id WHERE y2m_group_roles.group_roles_id = $role GROUP BY (y2m_group_roles.group_roles_id)";
		 
		$statement = $this->adapter-> query($sql);  		
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
	}
	public function changeGroupsStatus($group,$status){
		$sql = "UPDATE y2m_group  SET group_status = ".$status." WHERE   group_id = ".$group;
	 //echo $sql;die();
		$statement = $this->adapter-> query($sql);  		
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
 
	}
	public function getPlanetSuggestions($user_id,$limit,$offset){
		
		$select = new Select;		
		$sub_select = new Select;		 
		$sub_select->from('y2m_group')
				   ->columns(array(new Expression('COUNT(y2m_group.group_id) as member_count'),"group_id"))
				   ->join(array('y2m_user_group'=>'y2m_user_group'),'y2m_group.group_id = y2m_user_group.user_group_group_id',array());
		$sub_select->group('y2m_group.group_id');
		 
		$select->from('y2m_group')
			   ->columns(array('group_id'=>'group_id','group_seo_title'=>'group_seo_title','group_title'=>'group_title','group_discription'=>'group_discription'))
			   ->join('y2m_group_tag','y2m_group.group_id = y2m_group_tag.group_tag_group_id',array())
			   ->join('y2m_user_tag',new Expression("y2m_user_tag.user_tag_tag_id = y2m_group_tag.group_tag_tag_id AND y2m_user_tag.user_tag_user_id =". $user_id),array())
			   ->join('y2m_tag','y2m_tag.tag_id = y2m_user_tag.user_tag_tag_id',array())
			   ->join(array("B"=>'y2m_group'),'y2m_group.group_parent_group_id = B.group_id',array('parent_seo_title'=>'group_seo_title'),'left')
			   ->join(array('temp_member' => $sub_select), 'temp_member.group_id = y2m_group.group_id',array('member_count'),'left') 
			 
			   ->join('y2m_user_profile',new Expression("y2m_user_profile.user_profile_city_id = y2m_group.group_city_id AND y2m_user_profile.user_profile_user_id =". $user_id),array('user_profile_city_id'),'left')
			 
			   ->join(array('y2m_album_data'=>'y2m_album_data'),'y2m_group.group_photo_id = y2m_album_data.data_id',array('data_content'=>'data_content'),'left');
			   $select->where(array("y2m_group.group_id NOT IN(SELECT user_group_group_id FROM y2m_user_group WHERE user_group_user_id = $user_id)"));
			   $select->where(array("y2m_group.group_id NOT IN(SELECT user_group_joining_request_group_id FROM y2m_user_group_joining_request WHERE user_group_joining_request_user_id = $user_id)"));
			   $select->where(array("y2m_group.group_parent_group_id !=0 "));
			 //  $select->where(array("y2m_user_profile.user_profile_user_id =  $user_id"));
		$select->limit($limit);
		$select->offset($offset);
		$select->group('y2m_group.group_id');
		$select->order(array('user_profile_city_id DESC'));
		$select->order(array('member_count DESC'));
	 
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		 
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->buffer();	
	}
	public function searchGroup($search_string,$user_id,$offset,$limit){
		$select = new Select;
		$select->from('y2m_group')
				->columns(array('is_member'=>new Expression('IF(EXISTS(SELECT * FROM y2m_user_group WHERE y2m_user_group.user_group_user_id = '.$user_id.' AND y2m_user_group.user_group_group_id = y2m_group.group_id),1,0)'),'is_requested'=>new Expression('IF(EXISTS(SELECT * FROM  y2m_user_group_joining_request WHERE  y2m_user_group_joining_request.user_group_joining_request_user_id = '.$user_id.' AND y2m_user_group_joining_request.user_group_joining_request_group_id = y2m_group.group_id),1,0)'),'group_title','group_seo_title'))
				->join(array("group_parent"=>"y2m_group"),"group_parent.group_id = y2m_group.group_parent_group_id",array("parent_seo_title"=>'group_seo_title'),"left")
				->join('y2m_group_photo','y2m_group_photo.group_photo_group_id = y2m_group.group_id',array('group_photo_photo'),'left')
				->join(array('y2m_album_data'=>'y2m_album_data'),'y2m_group.group_photo_id = y2m_album_data.data_id',array('data_content'=>'data_content'),'left');
				$select->where->like('y2m_group.group_title','%'.$search_string.'%');
				$select->limit($limit);
				$select->offset($offset); 

		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		 
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->buffer();					
	}
	public function checkPlanetExist($planet_name){
		$select = new Select;
		$select->from('y2m_group')
			   ->columns(array('group_id'))
			   ->where(array("group_title"=>$planet_name));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);	
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		$row = $resultSet->current();
		if(!empty($row)&&$row->group_id!=''){
			return true;
		}else{
			return false;
		}
	}
	public function checkSeotitleExist($seotitle){
		$select = new Select;
		$select->from('y2m_group')
			   ->columns(array('group_id'))
			   ->where(array("group_seo_title"=>$seotitle));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);	
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		$row = $resultSet->current();
		if(!empty($row)&&$row->group_id!=''){
			return true;
		}else{
			return false;
		}
	}
	public function createPlanet($data)	{		
		$this->insert($data);
		return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
	}
	public function getSubgroupWithParentSeo($group_id){  
		$select = new Select;
		$select->from('y2m_group')				 
				->join(array("group_parent"=>"y2m_group"),"group_parent.group_id = y2m_group.group_parent_group_id",array("parent_seo_title"=>'group_seo_title'),"left");				 
		$select->where(array('y2m_group.group_id'=>$group_id));		
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		 
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->current();		
	}
	public function getSubgroupWithParentTitle($group_id){  
		$select = new Select;
		$select->from('y2m_group')				 
				->join(array("group_parent"=>"y2m_group"),"group_parent.group_id = y2m_group.group_parent_group_id",array("parent_title"=>'group_title'),"left");				 
		$select->where(array('y2m_group.group_id'=>$group_id));		
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		 
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->current();		
	}
	public function getSystemInfo($system_id){
		$select = new Select;
		$select->from('y2m_system_type')				 
				;				 
		$select->where(array('system_type_id'=>$system_id));		
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		 
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->current();	
	}
	public function fetchAllGalaxy($limit,$offset,$field="group_id",$order='ASC',$search=''){ 
		$inner_select = new Select;
		$predicate = new  \Zend\Db\Sql\Where();
		$inner_select->from('y2m_group')
					 ->columns(array(new Expression('COUNT(y2m_group.group_id) as group_count'),'group_parent_group_id'=>'group_parent_group_id'))
					 ->where($predicate->greaterThan('y2m_group.group_parent_group_id' , "0"));
		$inner_select->group('y2m_group.group_parent_group_id');	
		$select = new Select;
		$select->from('y2m_group')    		
			->join(array('temp' => $inner_select), 'y2m_group.group_id = temp.group_parent_group_id',array('group_count'),'left')
			->where(array('y2m_group.group_parent_group_id' => "0"))
			;
		$select->limit($limit);
		$select->offset($offset);
		$select->order($field.' '.$order);
		if($search!=''){
			$select->where->like('y2m_group.group_title',$search.'%');		
		}
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet;	     
    }
	public function getCountOfAllGalaxy($search=''){
		$select = new Select;
		$select->from('y2m_group')		
			   ->columns(array(new Expression('COUNT(y2m_group.group_id) as group_count')))
			   ->where(array('y2m_group.group_parent_group_id' => "0"));
		if($search!=''){
			$select->where->like('y2m_group.group_title',$search.'%');		
		}
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return  $resultSet->current()->group_count;
	}
	public function getPlanetsCountUnderThisGroup($group_id){
		$select = new Select;
		$select->from('y2m_group')		
			   ->columns(array(new Expression('COUNT(y2m_group.group_id) as group_count')))
			   ->where(array('y2m_group.group_parent_group_id' => $group_id));
		 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return  $resultSet->current()->group_count;
	}
	public function getCountOfAllPlanet($group_id=null,$search=''){
		$predicate = new  \Zend\Db\Sql\Where();
		$select = new Select;
		$sub_select = new Select;
		$sub_select->from('y2m_group')
			   ->columns(array(new Expression('COUNT(y2m_group.group_id) as member_count'),"group_id"))
			   ->join(array('y2m_user_group'=>'y2m_user_group'),'y2m_group.group_id = y2m_user_group.user_group_group_id',array());
		$sub_select->group('y2m_group.group_id');
		$sub_select2 = new Select;
		$sub_select2->from('y2m_group')
			   ->columns(array(new Expression('COUNT(y2m_group.group_id) as activity_count'),"group_id"))
			   ->join(array('y2m_group_activity'=>'y2m_group_activity'),'y2m_group.group_id = y2m_group_activity.group_activity_group_id',array());
		$sub_select2->group('y2m_group.group_id');	
		$select->from(array('c' => 'y2m_group'))
			->columns(array(new Expression('COUNT(c.group_id) as group_count')))
			 ->join(array('p' => 'y2m_group'), 'c.group_id = p.group_parent_group_id',array())	
			 ->join(array('temp_member' => $sub_select), 'temp_member.group_id = c.group_id',array('member_count'),'left')
			 ->join(array('temp_activity' => $sub_select2), 'temp_activity.group_id = p.group_id',array('activity_count'),'left')
			 ->where($predicate->greaterThan('p.group_parent_group_id' , "0"))
			 ->order(array('c.group_id ASC'));
		//$select->columns(array('parent_title' => 'group_title'));
		if($group_id){	
			$select->where(array( 'p.group_parent_group_id' => $group_id));	
		}	
		if($search!=''){
			$select->where->like('c.group_title',$search.'%')->or->like('p.group_title',$search.'%');	
		}		
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return  $resultSet->current()->group_count;
		;	
	}
	public function getCountOfAllUnapprovedPlanet($group_id=null,$search=''){
		$predicate = new  \Zend\Db\Sql\Where();
		$select = new Select;
		$select->from(array('c' => 'y2m_group'))
			->columns(array(new Expression('COUNT(c.group_id) as group_count')))
			 ->join(array('p' => 'y2m_group'), 'c.group_id = p.group_parent_group_id',array())				 
			 ->where($predicate->greaterThan('p.group_parent_group_id' , "0"))
			 ->where( array('p.group_status'=>0))
			 ->order(array('c.group_id ASC'));
		//$select->columns(array('parent_title' => 'group_title'));
		if($group_id){	
			$select->where(array( 'p.group_parent_group_id' => $group_id));	
		}	
		if($search!=''){
			$select->where->like('c.group_title',$search.'%')->or->like('p.group_title',$search.'%');	
		}		
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return  $resultSet->current()->group_count;
		;	
	}
	public function RemoveAllGroupDiscussionDetails($group_id){
		$sql = "DELETE FROM  y2m_like WHERE like_system_type_id = 3 AND like_refer_id IN (SELECT  comment_id FROM  y2m_comment INNER JOIN  y2m_group_discussion ON y2m_group_discussion.group_discussion_id = y2m_comment.comment_refer_id AND y2m_comment.comment_system_type_id = 2 WHERE y2m_group_discussion.group_discussion_group_id = ".$group_id.")";	
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE FROM y2m_like WHERE like_system_type_id = 2 AND like_refer_id IN (SELECT  group_discussion_id FROM  y2m_group_discussion WHERE y2m_group_discussion.group_discussion_group_id = ".$group_id.")";	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE FROM y2m_comment WHERE comment_system_type_id = 2 AND comment_refer_id IN (SELECT  group_discussion_id FROM  y2m_group_discussion WHERE y2m_group_discussion.group_discussion_group_id = ".$group_id.")";	
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE  FROM y2m_group_discussion WHERE y2m_group_discussion.group_discussion_group_id = ".$group_id;	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		return true;
	}
	public function RemoveAllGroupSettigns($group_id){
		$sql = "DELETE FROM  y2m_group_setting WHERE y2m_group_setting.group_setting_group_id = ".$group_id;	
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE FROM y2m_user_group_settings WHERE y2m_user_group_settings.group_id = ".$group_id;	 
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();
		$sql = "DELETE FROM y2m_user_group_permissions WHERE y2m_user_group_permissions.group_id = ".$group_id;	
		$statement = $this->adapter-> query($sql); 
		$statement -> execute();		 
		return true;
	}
}