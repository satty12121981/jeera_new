<?php
namespace Message\Model;

use Zend\Db\Sql\Select, \Zend\Db\Sql\Where;
use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
 
class MessageTable extends AbstractTableGateway
{
    protected $table = 'y2m_user_message'; 

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Message());
        $this->initialize();
    }

    public function fetchAll()
    {
       $resultSet = $this->select();
       return $resultSet;
    }

    public function getMessage($user_message_id)
    {
        $user_message_id  = (int) $user_message_id;
        $rowset = $this->select(array('user_message_id' => $user_message_id));
        $row = $rowset->current();
        return $row;
    }

    public function getMessageOfUserFromUsers($UserId,$status)
    {

        $UserId  = (int) $UserId;
        $subselect = new Select;

        $expression = new Expression(
            "IF (`user_message_sender_id`= $UserId , `user_message_receiver_id`, `user_message_sender_id`)"
        );

        $subselect->from($this->table)
            ->columns(array('message_user_id'=>$expression,'message_count'=> new Expression('COUNT(y2m_user_message.user_message_id)'),'user_message_id'=>'user_message_id','user_message_content'=>'user_message_content'))
            ->where(array('user_message_status'=>$status))
            ->order('user_message_added_timestamp ASC')
            ->group('message_user_id')
            ->where->equalTo('user_message_sender_id', $UserId)->AND->equalTo('user_message_sender_deleted', 0)->OR->equalTo('user_message_receiver_id', $UserId)
            ->AND->equalTo('user_message_receiver_deleted', 0);

        //main query
        $mainSelect = new Select;
        $mainSelect->from(array('temp'=>$subselect))
            ->join(array('temp1'=>'y2m_user'), 'temp1.user_id = temp.message_user_id',array('*'))
            ->join('y2m_photo', 'y2m_photo.photo_id = temp1.user_profile_photo_id',array('photo_name','photo_location'),'left')
            ->columns(array('*'));

        $statement = $this->adapter->createStatement();
        $mainSelect->prepareStatement($this->adapter, $statement);
       // echo $mainSelect->getSqlString();die();
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;

    }

    public function getMessageCountOfUser($user_id,$status)
    {
        $user_id  = (int) $user_id;

        $select = new Select;

        $select->from($this->table)
            ->columns(array(new Expression('COUNT(y2m_user_message.user_message_id) as message_count'),'user_message_id'=>'user_message_id'))
            ->join('y2m_user', 'y2m_user.user_id = y2m_user_message.user_message_sender_id')
            ->where(array('user_message_status'=>$status))
            ->group('user_id')
            ->order('user_message_added_timestamp ASC')
            ->where->equalTo('user_message_sender_id', $user_id)
            ->where->OR->equalTo('user_message_receiver_id', $user_id);

        $statement = $this->adapter->createStatement();

        $select->prepareStatement($this->adapter, $statement);
        //echo $select->getSqlString();
        $resultSet = new ResultSet();

        $resultSet->initialize($statement->execute());

        // You can now buffer if you need..
        $resultSet->buffer();
        // Or get an array of all items
        $arrayOfResults = $resultSet->toArray();
        // print_r($arrayOfResults);

        return $arrayOfResults;
        //return $resultSet;
    }

    public function getMessageWithStatus($user_sender_id,$user_receiver_id,$status)
    {
        $user_sender_id  = (int) $user_sender_id;
        $user_receiver_id  = (int) $user_receiver_id;

        $select = new Select;

        $select->from($this->table)
            ->join('y2m_user', 'y2m_user.user_id = y2m_user_message.user_message_sender_id')
            ->join('y2m_photo', 'y2m_photo.photo_id = y2m_user.user_profile_photo_id')
            ->join(array('message_photo' => 'y2m_photo'), 'message_photo.photo_album_id = y2m_user_message.user_message_id',array('mess_photo'=>'photo_name','mess_photo_loc'=>'photo_location'),'left')
            ->where(array('user_message_status'=>$status,'user_message_sender_id' => $user_sender_id,'user_message_receiver_id' => $user_receiver_id))
            ->order('user_message_added_timestamp ASC');

        $statement = $this->adapter->createStatement();

        $select->prepareStatement($this->adapter, $statement);
        //echo $select->getSqlString();
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        $row = $resultSet->current();
        return $row;
    }

    public function fetchMessageBetweenUsers($user_sender_id,$user_receiver_id,$status)
    {
        $user_sender_id  = (int) $user_sender_id;

        $user_receiver_id  = (int) $user_receiver_id;

        $select = new Select;

        $select->from($this->table)
               ->join('y2m_user', 'y2m_user.user_id = y2m_user_message.user_message_sender_id')
               ->join('y2m_photo', 'y2m_photo.photo_id = y2m_user.user_profile_photo_id')
               ->join(array('message_photo' => 'y2m_photo'), 'message_photo.photo_album_id = y2m_user_message.user_message_id',array('mess_photo'=>'photo_name','mess_photo_loc'=>'photo_location'),'left')
               ->where(array('user_message_status'=>$status))
               ->order('user_message_added_timestamp ASC')
               ->where->equalTo('user_message_sender_id', $user_sender_id)->AND->equalTo('user_message_receiver_id', $user_receiver_id)->AND->equalTo('user_message_sender_deleted', 0)
               ->where->OR->equalTo('user_message_sender_id', $user_receiver_id)->AND->equalTo('user_message_receiver_id', $user_sender_id)->AND->equalTo('user_message_receiver_deleted', 0);

        $statement = $this->adapter->createStatement();

        $select->prepareStatement($this->adapter, $statement);
        //echo $select->getSqlString();
        $resultSet = new ResultSet();

        $resultSet->initialize($statement->execute());

        return $resultSet;
    }

    public function saveMessage(Message $Message)
    {
	
	   $data = array(
			'user_message_receiver_id' => $Message->user_message_receiver_id,
            'user_message_sender_id' => $Message->user_message_sender_id,
			'user_message_content' => $Message->user_message_content,
            'user_message_added_ip_address'  => $Message->user_message_added_ip_address,
			'user_message_status'  => $Message->user_message_status, 		
			'user_message_type'  => $Message->user_message_type,
            'user_message_sender_deleted'  => $Message->user_message_sender_deleted,
            'user_message_receiver_deleted'  => $Message->user_message_receiver_deleted,
            'user_message_sender_viewed'  => $Message->user_message_sender_viewed,
            'user_message_receiver_viewed'  => $Message->user_message_receiver_viewed,
            'user_message_sender_deleted_date'  => $Message->user_message_sender_deleted_date,
            'user_message_receiver_deleted_date'  => $Message->user_message_receiver_deleted_date,
            'user_message_sender_viewed_date'  => $Message->user_message_sender_viewed_date,
            'user_message_receiver_viewed_date'  => $Message->user_message_receiver_viewed_date,
        );

       $user_message_id = (int)$Message->user_message_id;
			
       if ($user_message_id == 0) {
            $this->insert($data);
			$lastId = $this->adapter->getDriver()->getLastGeneratedValue();

			return $lastId;
       } else {
            if ($this->getMessage($user_message_id)) {
			    
                $this->update($data, array('user_message_id' => $user_message_id));
            } else {
                throw new \Exception('message id does not exist');
            }
			return;
       }
    }
	
	public function updateMessage($user_sender_id,$user_receiver_id,$user_message_id=null)
	{

        if (is_array($user_message_id))
            $user_message_id = (array) $user_message_id;
        else
            $user_message_id = (int) $user_message_id;

        $expression = new Expression('CASE
                     WHEN user_message_sender_id = '.$user_sender_id.' THEN 1 ElSE user_message_sender_deleted END,
                     user_message_receiver_deleted = CASE WHEN user_message_receiver_id = '.$user_sender_id.' THEN 1 ElSE user_message_receiver_deleted END');

        $update = new Update($this->table);

        if ($user_message_id) {
            $update->set(array('user_message_sender_deleted' => $expression))
                   ->where->in('user_message_id',$user_message_id);
        }
        else{
            $select = new Select;
            $select->from($this->table)
                ->columns(array('user_messageid'=>'user_message_id'))               
                ->where->equalTo('user_message_sender_id', $user_sender_id)->AND->equalTo('user_message_receiver_id', $user_receiver_id)
                ->where->OR->equalTo('user_message_sender_id', $user_receiver_id)->AND->equalTo('user_message_receiver_id', $user_sender_id);

            $subselect = new Select;
            $subselect->from(array('t'=>new \Zend\Db\Sql\TableIdentifier($select)));

            $update->set(array('user_message_sender_deleted' => $expression))
                ->where->in('user_message_id',$subselect);
        }

        $statement = $this->adapter->createStatement();
        $update->prepareStatement($this->adapter, $statement);

        $statement->execute();

		return true;
	}
	public function getMessageCount($user_id){
		$select = new Select;
		$select->from('y2m_user_message')
			   ->columns(array(new Expression('COUNT(y2m_user_message.user_message_id) as message_count')))
			   ->where(array('user_message_receiver_id'=>$user_id));
		$select->where(array('user_message_receiver_viewed'=>0));
		$select->where(array('user_message_receiver_deleted'=>0));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
	}
	public function getMessageForNotification($user_id){
		$select = new Select;
		$select->from('y2m_user_message')
				->join('y2m_user','y2m_user_message.user_message_sender_id = y2m_user.user_id',array('user_given_name','user_id','user_profile_name','user_register_type','user_fbid'))
				->join('y2m_user_profile_photo','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array('profile_photo'),'left')
				->where(array('user_message_receiver_id'=>$user_id))
				->where(array('user_message_receiver_viewed'=>0))
				->where(array('user_message_receiver_deleted'=>0));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
	}
	public function setStatusRead($user_id){
		$update = new Update($this->table);
		$update->set(array('user_message_receiver_viewed'=>1))->where(array("user_message_receiver_id"=>$user_id));
		$statement = $this->adapter->createStatement();
        $update->prepareStatement($this->adapter, $statement);
        $statement->execute();
		return true;
	}
	public function setStatusReadOfSpecic($user_id,$sender){
		$update = new Update($this->table);
		$update->set(array('user_message_receiver_viewed'=>1))->where(array("user_message_receiver_id"=>$user_id,"user_message_sender_id"=>$sender));
		$statement = $this->adapter->createStatement();
        $update->prepareStatement($this->adapter, $statement);
        $statement->execute();
		return true;
	}
	
	public function myMessages($user_id,$limit,$offset){
		$sql  = 'SELECT y2m_user_message.*,y2m_user.*,y2m_user_profile_photo.profile_photo,IF(`user_message_sender_id`='.$user_id.',`user_message_receiver_id`, `user_message_sender_id`) as message_user FROM y2m_user_message
   INNER JOIN y2m_user ON y2m_user.user_id = IF(`user_message_sender_id`='.$user_id.',`user_message_receiver_id`, `user_message_sender_id`) 
   LEFT JOIN y2m_user_profile_photo ON y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id  WHERE (y2m_user_message.user_message_sender_id = '.$user_id.' AND  y2m_user_message.user_message_sender_deleted = 0 ) OR (y2m_user_message.user_message_receiver_id = '.$user_id.' AND y2m_user_message.user_message_receiver_deleted = 0)
   GROUP BY message_user ORDER BY user_message_added_timestamp DESC LIMIT '.$offset.','.$limit;
		$statement = $this->adapter-> query($sql);  		
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
	}
	public function fetchAllConversations($loggedUserId,$PairId,$offset,$limit){
		$select = new Select;
		$select->from('y2m_user_message')
			   ->join('y2m_user','y2m_user.user_id=y2m_user_message.user_message_sender_id',array('user_given_name','user_id','user_profile_name','user_register_type','user_fbid'))
			   ->join('y2m_user_profile_photo','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array('profile_photo'),'left')
			   ->where(array("(y2m_user_message.user_message_receiver_id = $loggedUserId AND y2m_user_message.user_message_sender_id=$PairId AND y2m_user_message.user_message_receiver_deleted = 0 ) OR ( y2m_user_message.user_message_receiver_id = $PairId AND y2m_user_message.user_message_sender_id=$loggedUserId AND y2m_user_message.user_message_sender_deleted = 0 )"));
		$select->order(array('y2m_user_message.user_message_added_timestamp DESC'));
		$select->limit($limit);
		$select->offset($offset);
		//echo $select->getSqlString();die();
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);	
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet->toArray();
	}
	public function getSingleMessage($message_id){
		$select = new Select;
		$select->from('y2m_user_message')
			   ->join('y2m_user','y2m_user.user_id=y2m_user_message.user_message_sender_id',array('user_given_name','user_id','user_profile_name','user_register_type','user_fbid'))
			   ->join('y2m_user_profile_photo','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array('profile_photo'),'left')
			   ->where(array('user_message_id'=>$message_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);	
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet->toArray();
	}
}