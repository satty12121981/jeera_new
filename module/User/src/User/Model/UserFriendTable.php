<?php 
namespace User\Model;
use Zend\Db\Sql\Select , \Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;

class UserFriendTable extends AbstractTableGateway
{
    protected $table = 'y2m_user_friend';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new UserFriend());
        $this->initialize();
    }

	#It will fetch all data from table 
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }
	
	public function fetchAllUserFriend($UserId){
		$UserId = (int) $UserId;
		$subselect = new Select;

        $expression = new Expression(
            "IF (`user_friend_sender_user_id`= $UserId , `user_friend_friend_user_id`, `user_friend_sender_user_id`)"
        );
        $subselect->from($this->table)
            ->columns(array('friend_id'=>$expression))
            ->where->equalTo('user_friend_sender_user_id', $UserId)->OR->equalTo('user_friend_friend_user_id', $UserId)
           ;

        $mainSelect = new Select;
        //main query
        $mainSelect->from(array('temp'=>$subselect))
            ->join(array('temp1'=>'y2m_user'), 'temp1.user_id = temp.friend_id',array('*'))
            ->join('y2m_photo', 'y2m_photo.photo_id = temp1.user_profile_photo_id',array('photo_name','photo_location'),'left')
            ->columns(array('*'));

		$statement = $this->adapter->createStatement();

        $mainSelect->prepareStatement($this->adapter, $statement);
		//echo $mainSelect->getSqlString();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		
		return $resultSet;
	}

	#it will fetch a user with friend user id
    public function getUserFriend($user_friend_id)
    {
        $user_friend_id  = (int) $user_friend_id;
        $rowset = $this->select(array('user_friend_id' => $user_friend_id));
        $row = $rowset->current();         
        return $row;
    }
	
	public function CheckConnectionBetweenMembers($loggedinUserId,$memberUserId) {

        $loggedinUserId  = (int) $loggedinUserId;
		$memberUserId  = (int) $memberUserId;


        $subselect = new Select;
        $subselect->from ('y2m_user_friend_request')
            ->columns(array('user_friend_request_sender_user_id','user_friend_request_friend_user_id'))
            ->where->equalTo('user_friend_request_sender_user_id', $loggedinUserId)->AND->equalTo('user_friend_request_friend_user_id', $memberUserId)
            ->where->OR->equalTo('user_friend_request_sender_user_id', $memberUserId)->AND->equalTo('user_friend_request_friend_user_id', $loggedinUserId)
            ;

        $select = new Select;
        $select->from(array('friend' => $this->table))
            ->join(array('friend_request'=>$subselect), new expression('`friend`.`user_friend_sender_user_id` = `friend_request`.`user_friend_request_sender_user_id` and `friend`.`user_friend_friend_user_id` = `friend_request`.`user_friend_request_friend_user_id`'))
            ->join('y2m_user', 'friend.user_friend_sender_user_id = y2m_user.user_id', array('user_id'))
            ->join(array('user' => 'y2m_user'), 'friend.user_friend_friend_user_id = user.user_id', array('user_id'));

        $statement = $this->adapter->createStatement();

        $select->prepareStatement($this->adapter, $statement);
       
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());

        $row = $resultSet->current();
        return $row;
	}

    public function saveUserFriend(UserFriend $user)
    {
       $data = array(
			'user_friend_id' => $user->user_friend_id,
            'user_friend_sender_user_id' => $user->user_friend_sender_user_id,
            'user_friend_friend_user_id'  => $user->user_friend_friend_user_id,
			'user_friend_added_ip_address'  => $user->user_friend_added_ip_address,
			'user_friend_status'  => $user->user_friend_status,
        );
		 $user_friend_id = (int)$user->user_friend_id;
        if ($user_friend_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getUserfriend($user_friend_id)) {
                $this->update($data, array('user_friend_id' => $user_friend_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteUserfriend($user_friend_id)
    {
        $this->delete(array('user_friend_id' => $user_friend_id));
    }
	public function CheckFriendStatusBetweenMembers($user_id,$friend_id){
		$sql  = 'SELECT * FROM y2m_user_friend a, y2m_user_friend_request b where ((a.user_friend_sender_user_id = '.$user_id.' and a.user_friend_friend_user_id ='.$friend_id.')or(a.user_friend_sender_user_id = '.$friend_id.' and a.user_friend_friend_user_id ='.$user_id.'))AND ((b.user_friend_request_sender_user_id = '.$friend_id.' and b.user_friend_request_friend_user_id ='.$user_id.')or(b.user_friend_request_sender_user_id = '.$user_id.' and b.user_friend_request_friend_user_id ='.$friend_id.'))'; 
		$statement = $this->adapter-> query($sql);  	
		
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        $row = $resultSet->current();
        return $row;
	}
	public function getFriendsForSearch($user_id,$search_string){
		$select = new Select;
		$select->from ('y2m_user')
			   ->columns(array('user_given_name','user_id','user_profile_name','user_register_type','user_fbid'))
			   ->join("y2m_user_friend","y2m_user.user_id = y2m_user_friend.user_friend_sender_user_id OR y2m_user.user_id = y2m_user_friend.user_friend_friend_user_id",array())
			   ->join("y2m_user_friend_request","(y2m_user_friend.user_friend_sender_user_id = y2m_user_friend_request.user_friend_request_sender_user_id AND y2m_user_friend.user_friend_friend_user_id = y2m_user_friend_request.user_friend_request_friend_user_id) OR (y2m_user_friend.user_friend_sender_user_id = y2m_user_friend_request.user_friend_request_friend_user_id AND y2m_user_friend.user_friend_friend_user_id = y2m_user_friend_request.user_friend_request_sender_user_id)",array())
			   ->join('y2m_user_profile_photo','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array('profile_photo'),'left')
			   ->where("(y2m_user_friend.user_friend_sender_user_id = ".$user_id." OR y2m_user_friend.user_friend_friend_user_id = ".$user_id.") AND y2m_user.user_given_name LIKE '%".$search_string."%' and y2m_user.user_id!=".$user_id);
		$select->group("y2m_user.user_id");
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;	
	}
	public function isFriend($user_id,$requested_id){
		$select = new Select;
		$select->from ('y2m_user_friend')
			   ->columns(array('user_friend_id'))
			   ->where(array('(y2m_user_friend.user_friend_sender_user_id = '.$user_id.' AND y2m_user_friend.user_friend_friend_user_id = '.$requested_id.')OR(y2m_user_friend.user_friend_friend_user_id = '.$user_id.' AND y2m_user_friend.user_friend_sender_user_id = '.$requested_id.')'));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		$row = $resultSet->current();
		if($row&&$row->user_friend_id){
			return true;
		}
		else{
			return false;
		}
	}
	public function isRequested($user_id,$requested_id){
		$select = new Select;
		$select->from ('y2m_user_friend_request')
			   ->columns(array('user_friend_request_id'))
			   ->where(array('( y2m_user_friend_request.user_friend_request_friend_user_id = '.$requested_id.' AND y2m_user_friend_request.user_friend_request_sender_user_id = '.$user_id.')'));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		$row = $resultSet->current();
		if($row&&$row->user_friend_request_id){
			return true;
		}
		else{
			return false;
		}
	}
	public function isPending($user_id,$requested_id){
		$select = new Select;
		$select->from ('y2m_user_friend_request')
			   ->columns(array('user_friend_request_id'))
			   ->where(array('( y2m_user_friend_request.user_friend_request_friend_user_id = '.$user_id.' AND y2m_user_friend_request.user_friend_request_sender_user_id = '.$requested_id.' AND y2m_user_friend_request.user_friend_request_status = 0)'));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		$row = $resultSet->current();
		if($row&&$row->user_friend_request_id){
			return true;
		}
		else{
			return false;
		}
	}
	public function AcceptFriendRequest($user_id,$requested_id){
		 $data['user_friend_sender_user_id'] = $requested_id;
		 $data['user_friend_friend_user_id'] = $user_id;
		 $data['user_friend_status'] =1;
		 $this->insert($data);
		 return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
	}
	public function getAllFriends($user_id,$login_user,$offset,$limit){
		$subselect = new Select;
		$subselect->from('y2m_user_friend')
				  ->columns(array('friend_user'=>new Expression('IF(user_friend_sender_user_id='.$user_id.',user_friend_friend_user_id,user_friend_sender_user_id)')))
				  ->where->equalTo('user_friend_sender_user_id',$user_id)->OR->equalTo('user_friend_friend_user_id',$user_id)
				 ;		 
		$select = new Select;
		$select->from(array("temp"=>$subselect))
			   ->columns(array('friend_user','is_friend'=>new Expression('IF(EXISTS(SELECT * FROM y2m_user_friend WHERE  (y2m_user_friend.user_friend_sender_user_id = temp.friend_user AND y2m_user_friend.user_friend_friend_user_id = '.$login_user.')OR(y2m_user_friend.user_friend_friend_user_id = temp.friend_user AND y2m_user_friend.user_friend_sender_user_id = '.$login_user.')),1,0)'),
			   'is_requested'=>new Expression('IF(EXISTS(SELECT * FROM   y2m_user_friend_request WHERE  ( y2m_user_friend_request.user_friend_request_friend_user_id = temp.friend_user AND y2m_user_friend_request.user_friend_request_sender_user_id = '.$login_user.' AND y2m_user_friend_request.user_friend_request_status = 0) ),1,0)'),
				'get_request'=>new Expression('IF(EXISTS(SELECT * FROM   y2m_user_friend_request WHERE  ( y2m_user_friend_request.user_friend_request_sender_user_id = temp.friend_user AND y2m_user_friend_request.user_friend_request_friend_user_id = '.$login_user.' AND y2m_user_friend_request.user_friend_request_status = 0) ),1,0)')
			   ))
			   ->join("y2m_user",'y2m_user.user_id = temp.friend_user', array('user_given_name','user_id','user_profile_name','user_register_type','user_fbid'))
			   ->join("y2m_user_profile_photo",'y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id', array('profile_photo'),'left')
			   ;
			   $select->limit($limit);
			   $select->offset($offset);
			   $statement = $this->adapter->createStatement();
			   $select->prepareStatement($this->adapter, $statement);
		 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		// echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
	  	return $resultSet->buffer(); 
	}

}
