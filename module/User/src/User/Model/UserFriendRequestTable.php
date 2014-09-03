<?php 
namespace User\Model;
use Zend\Db\Sql\Select , \Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;

class UserFriendRequestTable extends AbstractTableGateway
{
    protected $table = 'y2m_user_friend_request';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new UserFriendRequest());
        $this->initialize();
    }

	#It will fetch all data from table 
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }
	
	 public function sendFriendRequest($data)
    { 
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
						 		 
         
    }
	public function getAllReuqestsCount($user_id){
		$select = new Select;
		$select->from('y2m_user_friend_request')
			   ->columns(array(new Expression('COUNT(y2m_user_friend_request.user_friend_request_id) as request_count')))			   
			   ->where(array('y2m_user_friend_request.user_friend_request_friend_user_id' =>$user_id,'y2m_user_friend_request.user_friend_request_status'=>0));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->current()->request_count;	
	}
	public function getAllReuqests($user_id){
		$select = new Select;
		$select->from('y2m_user_friend_request')			    
			   ->join('y2m_user',"y2m_user.user_id = y2m_user_friend_request.user_friend_request_sender_user_id",array('user_given_name','user_profile_name','user_register_type','user_fbid','user_id'))
			   ->join('y2m_user_profile_photo','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array('profile_photo'),'left')
			   ->where(array('y2m_user_friend_request.user_friend_request_friend_user_id' =>$user_id,'y2m_user_friend_request.user_friend_request_status'=>0));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->buffer();	
	}
	public function makeRequestTOProcessed($user_id,$request_id){
		$data['user_friend_request_status'] = 1;
		$this->update($data, array('user_friend_request_sender_user_id' => $request_id,'user_friend_request_friend_user_id'=>$user_id));
		$this->update($data, array('user_friend_request_friend_user_id' => $request_id,'user_friend_request_sender_user_id'=>$user_id));
		return true;
	}
	public function DeclineFriendRequest($user_id,$request_id){
		$data['user_friend_request_status'] = 2;
		return $this->update($data, array('user_friend_request_sender_user_id' => $request_id,'user_friend_request_friend_user_id'=>$user_id));		 
	}
}
