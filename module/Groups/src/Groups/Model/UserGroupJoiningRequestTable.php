<?php 
namespace Groups\Model;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
class UserGroupJoiningRequestTable extends AbstractTableGateway
{
    protected $table = 'y2m_user_group_joining_request';  
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new UserGroupJoiningRequest());
        $this->initialize();
    }
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }
	  public function saveUserGroupJoiningRequest(UserGroupJoiningRequest $UserGroupJoiningRequest)
    {
       $data = array(
            'user_group_joining_request_user_id' => $UserGroupJoiningRequest->user_group_joining_request_user_id,
            'user_group_joining_request_group_id'  => $UserGroupJoiningRequest->user_group_joining_request_group_id,
			'user_group_joining_request_added_ip_address'  => $UserGroupJoiningRequest->user_group_joining_request_added_ip_address,			 
		);

        $user_group_joining_request_id = (int)$UserGroupJoiningRequest->user_group_joining_request_id;
        if ($user_group_joining_request_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } 
		return true;		
    }
	 public function checkIfrequestExist($user_id,$group_id){
		$select = new Select;
		$select->from('y2m_user_group_joining_request')    		 
			->where(array('y2m_user_group_joining_request.user_group_joining_request_group_id' => "$group_id"))
			->where(array('y2m_user_group_joining_request.user_group_joining_request_user_id' => "$user_id")); 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
	  	return $resultSet->current();	
	 }
	 public function RemoveRequest($user_id,$planet_id){
		  return $this->delete(array('user_group_joining_request_user_id' => $user_id,'user_group_joining_request_group_id'=>$planet_id));
	 }
	 public function getUserRequests($group_id){
		$select = new Select;
		$select->from('y2m_user_group_joining_request')
			->join("y2m_user","y2m_user.user_id = y2m_user_group_joining_request.user_group_joining_request_user_id",array("user_id"=>"user_id","user_given_name"=>"user_given_name",'user_profile_name'=>'user_profile_name','user_register_type'=>'user_register_type','user_fbid'=>'user_fbid'))
			 ->join('y2m_user_profile_photo','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array('profile_photo'),'left')
			->where(array('y2m_user_group_joining_request.user_group_joining_request_group_id' => "$group_id","y2m_user_group_joining_request.user_group_joining_request_status"=>0));
			 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
	  	return $resultSet->buffer();
	 }
	 public function checkRequestExist($planet_id,$user){
		$select = new Select;
		$select->from('y2m_user_group_joining_request')
			->join("y2m_user","y2m_user.user_id = y2m_user_group_joining_request.user_group_joining_request_user_id",array("user_id"=>"user_id","user_given_name"=>"user_given_name"))			 
			->where(array('y2m_user_group_joining_request.user_group_joining_request_group_id' => "$planet_id"));
			$select->where(array("y2m_user_group_joining_request.user_group_joining_request_user_id"=>$user));
			$select->where(array("y2m_user_group_joining_request.user_group_joining_request_status"=>0));
			 
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
	  	return $resultSet->current();
	 }
	 public function ChangeStatusTOProcessed($planet_id,$user){
		$data['user_group_joining_request_status'] = 1;
		return $this->update($data, array('user_group_joining_request_group_id' => $planet_id,'user_group_joining_request_user_id'=>$user));
	 }
	public function ChangeStatusTOIgnored($planet_id,$user){
		$data['user_group_joining_request_status'] = 2;
		return $this->update($data, array('user_group_joining_request_group_id' => $planet_id,'user_group_joining_request_user_id'=>$user));
	 }
	 public function ChangeStatusTORemoved($planet_id,$user){
		return $this->delete(array('user_group_joining_request_user_id' => $user,'user_group_joining_request_group_id'=>$planet_id));
	 }
}