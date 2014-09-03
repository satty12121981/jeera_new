<?php  
namespace User\Model;
use Zend\Db\Sql\Select ;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
class UserTable extends AbstractTableGateway
{
    protected $table = 'y2m_user';
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new User());
        $this->initialize();
    }
    public function fetchAll(){
        $resultSet = $this->select();
        return $resultSet;
    }
    public function getUser($user_id){
        $user_id  = (int) $user_id;
        $rowset = $this->select(array('user_id' => $user_id));
        $row = $rowset->current();         
        return $row;
    }
    public function saveUser(User $user){
       $data = array(
            'user_given_name' => $user->user_given_name,
            'user_first_name'  => $user->user_first_name,
			'user_middle_name'  => $user->user_middle_name,
			'user_last_name'  => $user->user_last_name,
			'user_profile_name' =>$user->user_profile_name,
			'user_status'  => $user->user_status,
			'user_added_ip_address'  => $user->user_added_ip_address,
			'user_email'  => $user->user_email,
			'user_password'  => $user->user_password,
			'user_gender'  => $user->user_gender,
			'user_timeline_photo_id'  => $user->user_timeline_photo_id,
			'user_language_id'  => $user->user_language_id,
			'user_user_type_id'  => $user->user_user_type_id,
			'user_profile_photo_id'  => $user->user_profile_photo_id,
			'user_friend_request_reject_count'  => $user->user_friend_request_reject_count,
			'user_mobile'  => $user->user_mobile,
			'user_verification_key'  => $user->user_verification_key,
			'user_added_timestamp'  => $user->user_added_timestamp,
			'user_modified_timestamp'  => $user->user_modified_timestamp,
			'user_modified_ip_address'  => $user->user_modified_ip_address,	
			'user_register_type'  => $user->user_register_type,
			'user_fbid'			=> $user->user_fbid,
        );
		 $user_id = (int)$user->user_id;
        if ($user_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
						 		 
        } else {
            if ($this->getUser($user_id)) {
                $this->update($data, array('user_id' => $user_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
	public function updateUser($data,$user_id){
		if ($this->getUser($user_id)) {
			$this->update($data, array('user_id' => $user_id));
			return true;
		} else {
			throw new \Exception('Form id does not exist');
		}
		
	}
    public function deleteUser($user_id){
        $this->delete(array('user_id' => $user_id));
    }
	public function checkUserVarification($code,$user){ 
		$rowset = $this->select(array('user_verification_key'=>$code));
        $row = $rowset->current();
		if($row){
			if($row->user_status==0&&md5(md5('userId~'.$row->user_id))==$user)
			return $row->user_id;
			else
			return false;
		}
		else{	
			return false;
		}
	}
	public function getUserFromEmail($email){
       
        $rowset = $this->select(array('user_email' => $email));
        $row = $rowset->current();
         
        return $row;
    }
	public function getUserProfilePic($user_id){
		$select = new Select;
		$select->from('y2m_user_profile_photo')
			   ->columns(array('biopic'=>'profile_photo'))
			   ->join('y2m_user','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array())
			   ->where(array('y2m_user.user_id = '.$user_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet;		
	}
	public function seasrchUser($search_string,$user,$offset,$limit){
		$select = new Select;
		$select->from('y2m_user')
			   ->columns(array('is_friend'=>new Expression('IF(EXISTS(SELECT * FROM y2m_user_friend WHERE  (y2m_user_friend.user_friend_sender_user_id = y2m_user.user_id AND y2m_user_friend.user_friend_friend_user_id = '.$user.')OR(y2m_user_friend.user_friend_friend_user_id = y2m_user.user_id AND y2m_user_friend.user_friend_sender_user_id = '.$user.')),1,0)'),
				'is_requested'=>new Expression('IF(EXISTS(SELECT * FROM   y2m_user_friend_request WHERE  ( y2m_user_friend_request.user_friend_request_friend_user_id = y2m_user.user_id AND y2m_user_friend_request.user_friend_request_sender_user_id = '.$user.' AND y2m_user_friend_request.user_friend_request_status = 0) ),1,0)'),
				'get_request'=>new Expression('IF(EXISTS(SELECT * FROM   y2m_user_friend_request WHERE  ( y2m_user_friend_request.user_friend_request_sender_user_id = y2m_user.user_id AND y2m_user_friend_request.user_friend_request_friend_user_id = '.$user.' AND y2m_user_friend_request.user_friend_request_status = 0) ),1,0)'),'user_given_name','user_id','user_profile_name','user_register_type','user_fbid'))
			   ->join('y2m_user_profile_photo','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array('profile_photo'),'left')
			   ;
		$select->where->like('user_given_name','%'.$search_string.'%')->or->like('user_email','%'.$search_string.'%');
		$select->limit($limit);
		$select->offset($offset);	 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->buffer();	
	}
	public function checkProfileNameExist($string){
		$select = new Select;
		$select->from('y2m_user')
			   ->columns(array('user_id'))
			   ->where(array('user_profile_name'=>$string));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$row =  $resultSet->current();	
		if(!empty($row)&&$row->user_id!=''){
			return true;
		}
		else{
			return false;
		}
	}
	public function getUserByProfilename($profile_name){
        $profile_name  = (string) $profile_name;
        $rowset = $this->select(array('user_profile_name' => $profile_name));
        $row = $rowset->current();        
        return $row;
    }
	public function  getProfileDetails($user_id){
		$select = new select();
		$select->from('y2m_user')
			   ->columns(array("user_id"=>"user_id","user_given_name"=>"user_given_name","user_first_name"=>"user_first_name","user_middle_name"=>"user_middle_name","user_last_name"=>"user_last_name","user_profile_name"=>"user_profile_name","user_email"=>"user_email","user_gender"=>"user_gender","user_mobile"=>"user_mobile","user_register_type"=>"user_register_type","user_fbid"=>"user_fbid",'is_member'=>new Expression('IF(EXISTS(SELECT * FROM y2m_user_group WHERE user_group_user_id = '.$user_id.'),1,0)'),'is_taged'=>new Expression('IF(EXISTS(SELECT * FROM  y2m_user_tag WHERE user_tag_user_id = '.$user_id.'),1,0)')))
			   ->join("y2m_user_profile","y2m_user_profile.user_profile_user_id = y2m_user.user_id",array("user_profile_dob","user_profile_about_me","user_profile_profession","user_profile_profession_at","user_profile_city_id","user_profile_state_id","user_profile_country_id","user_address","user_profile_current_location","user_profile_phone"),"left")
			   ->join("y2m_country","y2m_country.country_id = y2m_user_profile.user_profile_country_id",array("country_title"),"left")
			   ->join("y2m_city","y2m_city.city_id = y2m_user_profile.user_profile_city_id",array("name"),"left")
			   ->join(array("profile_photo"=>"y2m_user_profile_photo"),"profile_photo.profile_photo_id = y2m_user.user_profile_photo_id",array("profile_photo"=>"profile_photo"),"left")
			   ->join(array("timeline_photo"=>"y2m_user_cover_photo"),"timeline_photo.cover_photo_id = y2m_user.user_timeline_photo_id",array("timeline_photo"=>"cover_photo","cover_photo_left","cover_photo_top"),"left")
			   ->where(array("y2m_user.user_id"=>$user_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$row =  $resultSet->current();	
		return $row;
	}	
	public function UserEmailExists($email,$id){
		$select = new select();
        $rowset = $select->from('y2m_user')
		->where(array('user_email' => $email))->where->notEqualTo('user_id',$id)	;	
         $resultSet = $this->selectWith($select);
        $row =  $resultSet->current();
         
        return $row;
    }
	public function changeUserpassword($pass,$id){ 
		$userdata = array(
            'user_password'  => $pass 
        );
		return $this->update($userdata, array('user_id' => $id)); 
	}
	public function getAllUserFriendsForTag($user_id,$term){
		$subselect = new Select;
		$subselect->from('y2m_user_friend')
				  ->columns(array('friend_user'=>new Expression('IF(user_friend_sender_user_id='.$user_id.',user_friend_friend_user_id,user_friend_sender_user_id)')))
				  ->where->equalTo('user_friend_sender_user_id',$user_id)->OR->equalTo('user_friend_friend_user_id',$user_id)
				 ;		 
		$select = new Select;
		$select->from(array("temp"=>$subselect))
			   ->columns(array())
			   ->join("y2m_user",'y2m_user.user_id = temp.friend_user', array('user_given_name','user_id'))
			   ->join("y2m_album_data",'y2m_user.user_profile_photo_id = y2m_album_data.data_id', array('data_content'),'left')
			   ;
		$select->where->like('user_given_name','%'.$term.'%')->or->like('user_email','%'.$term.'%');
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		// echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->buffer(); 
	}
	public function getUserByEmail($email){
		$select = new Select;
		$select->from("y2m_user")
			->columns(array('*'))
			->where(array("user_email"=>$email));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		// echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->current(); 
	}
	public function getUserByFbid($fbid){
		$select = new Select;
		$select->from("y2m_user")
			->columns(array('*'))
			->where(array("user_fbid"=>$fbid));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		// echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->current(); 
	}
	public function checkEmailVarification($varification_code,$user_id){
		$select = new Select;
		$select->from("y2m_user")
			->columns(array('user_id'))
			->where(array("user_id"=>$user_id,"user_verification_key"=>$varification_code));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		// echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		$row =  $resultSet->current(); 
		if(isset($row)&&!empty($row)&&$row->user_id!=''){
			return true;
		}else{
			return false;
		}
	}
	public function updateVarificationCode($varification_code,$user_id){
		$data['user_verification_key'] = $varification_code;
		return $this->update($data, array('user_id' => $user_id));
	}
	public function getNewsSummery($user_id,$offset,$limit){
		$result = new ResultSet();		 
		 $sql = 'SELECT group_activity_id as event_id,group_activity_added_timestamp as update_time,if(group_activity_id,"New Activity","") as type FROM  y2m_group_activity WHERE group_activity_group_id IN (SELECT user_group_group_id FROM y2m_user_group WHERE user_group_user_id = '.$user_id.') AND group_activity_status = 1
UNION
SELECT group_discussion_id as event_id,group_discussion_added_timestamp as update_time,if(group_discussion_id,"New Discussion","") as type FROM  y2m_group_discussion WHERE group_discussion_group_id IN (SELECT user_group_group_id FROM y2m_user_group WHERE user_group_user_id = '.$user_id.' ) AND group_discussion_status = 1
UNION
SELECT group_activity_rsvp_id as event_id,group_activity_rsvp_added_timestamp as update_time,if(group_activity_rsvp_id,"New Activity Member","") as type  FROM  y2m_group_activity_rsvp WHERE group_activity_rsvp_group_id IN (SELECT group_activity_rsvp_group_id FROM y2m_group_activity_rsvp WHERE group_activity_rsvp_user_id = '.$user_id.') OR group_activity_rsvp_group_id IN(SELECT user_group_group_id FROM y2m_user_group WHERE user_group_user_id = '.$user_id.' AND (user_group_is_owner =1 OR user_group_role!=0) OR group_activity_rsvp_activity_id IN (SELECT group_activity_id FROM  y2m_group_activity WHERE group_activity_owner_user_id = '.$user_id.') )
UNION
SELECT like_id as event_id,like_added_timestamp as update_time,if(like_id,"Activity Like","") as type  FROM y2m_like WHERE like_system_type_id = 1 AND (like_refer_id IN (SELECT group_activity_id FROM  y2m_group_activity INNER JOIN y2m_user_group ON y2m_group_activity.group_activity_group_id = y2m_user_group.user_group_group_id WHERE user_group_user_id = '.$user_id.' AND (user_group_is_owner = 1 OR user_group_role>0)) OR like_refer_id IN (SELECT group_activity_rsvp_activity_id FROM y2m_group_activity_rsvp WHERE group_activity_rsvp_user_id = '.$user_id.') OR like_refer_id IN (SELECT group_activity_id FROM  y2m_group_activity WHERE group_activity_owner_user_id = '.$user_id.'))
UNION 
SELECT comment_id as event_id,comment_added_timestamp as update_time,if(comment_id,"Activity Comment","") as type  FROM y2m_comment WHERE comment_system_type_id = 1 AND (comment_refer_id IN (SELECT group_activity_id FROM  y2m_group_activity INNER JOIN y2m_user_group ON y2m_group_activity.group_activity_group_id = y2m_user_group.user_group_group_id WHERE user_group_user_id = '.$user_id.' AND (user_group_is_owner = 1 OR user_group_role>0)) OR comment_refer_id IN (SELECT group_activity_rsvp_activity_id FROM y2m_group_activity_rsvp WHERE group_activity_rsvp_user_id = '.$user_id.')OR comment_refer_id IN (SELECT group_activity_id FROM  y2m_group_activity WHERE group_activity_owner_user_id = '.$user_id.'))
UNION
SELECT like_id as event_id,like_added_timestamp as update_time,if(like_id,"Discussion Like","") as type  FROM y2m_like WHERE like_system_type_id = 2 AND (like_refer_id IN (SELECT group_discussion_id FROM  y2m_group_discussion INNER JOIN y2m_user_group ON y2m_group_discussion.group_discussion_group_id = y2m_user_group.user_group_group_id WHERE user_group_user_id = '.$user_id.' AND (user_group_is_owner = 1 OR user_group_role>0)) OR like_refer_id IN (SELECT  comment_refer_id FROM y2m_comment WHERE comment_by_user_id = '.$user_id.' AND comment_system_type_id = 2))
UNION
SELECT comment_id as event_id,comment_added_timestamp as update_time,if(comment_id,"Discussion Comment","") as type  FROM y2m_comment WHERE comment_system_type_id = 2 AND (comment_refer_id IN (SELECT group_discussion_id FROM  y2m_group_discussion INNER JOIN y2m_user_group ON y2m_group_discussion.group_discussion_group_id = y2m_user_group.user_group_group_id WHERE user_group_user_id = '.$user_id.' AND (user_group_is_owner = 1 OR user_group_role>0)) OR comment_refer_id IN (SELECT  comment_refer_id FROM y2m_comment WHERE comment_by_user_id = '.$user_id.' AND comment_system_type_id = 2))
UNION 
SELECT user_group_id as event_id,user_group_added_timestamp as update_time,if(user_group_id,"New Group Members","") as type FROM  y2m_user_group WHERE user_group_group_id IN (SELECT user_group_group_id FROM y2m_user_group WHERE user_group_user_id = '.$user_id.' )
UNION
SELECT album_id as event_id,album_added_timestamp as update_time,if(album_id,"New Group Albums","") as type FROM  y2m_album WHERE album_group_id IN (SELECT user_group_group_id FROM y2m_user_group WHERE user_group_user_id = '.$user_id.')
UNION
SELECT data_id as event_id,data_added_date as update_time,if(data_id,"New Group Album Pictures","") as type FROM  y2m_album_data WHERE parent_album_id IN (SELECT album_id FROM y2m_album INNER JOIN  y2m_user_group ON y2m_album.album_group_id = y2m_user_group.user_group_group_id WHERE y2m_user_group.user_group_user_id = '.$user_id.')
UNION
SELECT album_tag_id as event_id,album_tag_added_date as update_time,if(album_tag_id,"All Tagged Pictures","") as type FROM  y2m_album_tags WHERE album_tag_user_id = '.$user_id.'
UNION
SELECT like_id as event_id,like_added_timestamp as update_time,if(like_id,"All Picture Like","") as type  FROM y2m_like WHERE (like_system_type_id = 3 OR like_system_type_id =4) AND (like_refer_id IN (SELECT data_id FROM y2m_album_data WHERE parent_album_id IN (SELECT album_id FROM y2m_album INNER JOIN  y2m_user_group ON y2m_album.album_group_id = y2m_user_group.user_group_group_id WHERE y2m_user_group.user_group_user_id = '.$user_id.') OR parent_album_id IN (SELECT album_id FROM y2m_album  WHERE y2m_album.album_user_id = '.$user_id.')) OR like_refer_id IN (SELECT data_id FROM y2m_album_data WHERE added_user_id = '.$user_id.') OR like_refer_id IN (SELECT album_tag_data_id FROM  y2m_album_tags WHERE album_tag_user_id = '.$user_id.'))
UNION
SELECT comment_id as event_id,comment_added_timestamp as update_time,if(comment_id,"All Picture Comments","") as type  FROM y2m_comment WHERE (comment_system_type_id = 3 OR comment_system_type_id =4) AND (comment_refer_id IN (SELECT data_id FROM y2m_album_data WHERE parent_album_id IN (SELECT album_id FROM y2m_album INNER JOIN  y2m_user_group ON y2m_album.album_group_id = y2m_user_group.user_group_group_id WHERE y2m_user_group.user_group_user_id = '.$user_id.') OR parent_album_id IN (SELECT album_id FROM y2m_album  WHERE y2m_album.album_user_id = '.$user_id.')) OR comment_refer_id IN (SELECT data_id FROM y2m_album_data WHERE added_user_id = '.$user_id.') OR comment_refer_id IN (SELECT album_tag_data_id FROM  y2m_album_tags WHERE album_tag_user_id = '.$user_id.'))
UNION
SELECT  user_friend_id as event_id,user_friend_added_timestamp as update_time,if(user_friend_id,"New Friendship","") as type  FROM y2m_user_friend WHERE user_friend_sender_user_id = '.$user_id.' OR user_friend_friend_user_id = '.$user_id.' ORDER BY update_time DESC LIMIT '.$offset.','.$limit;   
		 $statement = $this->adapter-> query($sql); 
		 $results = $statement -> execute();	
		 return $results;
	}
	public function getUserWithProfilePic($user_id){
         $select = new select();
		$select->from('y2m_user')
			   ->columns(array("user_id"=>"user_id","user_given_name"=>"user_given_name","user_first_name"=>"user_first_name","user_middle_name"=>"user_middle_name","user_last_name"=>"user_last_name","user_profile_name"=>"user_profile_name","user_email"=>"user_email","user_gender"=>"user_gender","user_mobile"=>"user_mobile","user_register_type"=>"user_register_type","user_fbid"=>"user_fbid"))			    
			   ->join(array("profile_photo"=>"y2m_user_profile_photo"),"profile_photo.profile_photo_id = y2m_user.user_profile_photo_id",array("profile_photo"=>"profile_photo"),"left")			    
			   ->where(array("y2m_user.user_id"=>$user_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$row =  $resultSet->current();	
		return $row;
    }
	public function getAllUsers($offset,$limit,$search=''){
		 $select = new select();
		 $select->from('y2m_user')
		 ->columns(array("user_id","user_first_name","user_last_name","user_given_name"));
		 if($search!=''){
			$select->where->like('user_given_name','%'.$search.'%')->or->like('user_first_name','%'.$search.'%')->or->like('user_last_name','%'.$search.'%');
		 }
		 $select->limit($limit);
		 $select->offset($offset);	 
		 $statement = $this->adapter->createStatement();
		 $select->prepareStatement($this->adapter, $statement);
		 //echo $select->getSqlString();exit;
		 $resultSet = new ResultSet();
		 $resultSet->initialize($statement->execute());
		 return  $resultSet->buffer();	
	}
}
