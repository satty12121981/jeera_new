<?php 

namespace User\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class UserProfileTable extends AbstractTableGateway
{
    protected $table = 'y2m_user_profile';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new UserProfile());
        $this->initialize();
    }

	#It will fetch all data from table 
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

	#it will fetch a user with profile user id
    public function getUserProfile($user_profile_id)
    {
        $user_profile_id  = (int) $user_profile_id;
        $rowset = $this->select(array('user_profile_id' => $user_profile_id));
        $row = $rowset->current();         
        return $row;
    }
	
	#it will a user with user id(column of user table) 
	public function getUserProfileForUserId($user_id)
    {
        $user_id  = (int) $user_id;
        $rowset = $this->select(array('user_profile_user_id' => $user_id));
        $row = $rowset->current();         
        return $row;
    }
	
	#this function will fetch all users profiles for a specific country
	public function getUserProfileOfCountry($country_id)
    {
        $country_id  = (int) $country_id;
        $rowset = $this->select(array('user_profile_country_id' => $country_id));
       	return $rowset;
    }
	
	
	

    public function saveUserProfile(UserProfile $user)
    {
       $data = array(
            'user_profile_dob' => $user->user_profile_dob,
            'user_profile_about_me'  => $user->user_profile_about_me,
			'user_profile_profession'  => $user->user_profile_profession,
			'user_profile_profession_at'  => $user->user_profile_profession_at,
			'user_profile_user_id'  => $user->user_profile_user_id,
			'user_profile_city_id'  => $user->user_profile_city_id,
			'user_profile_state_id'  => $user->user_profile_state_id,
			'user_profile_country_id'  => $user->user_profile_country_id,
			'user_address'  => $user->user_address,
			'user_profile_current_location'  => $user->user_profile_current_location,
			'user_profile_phone'  => $user->user_profile_phone,
			'user_profile_added_timestamp'  => $user->user_profile_added_timestamp,
			'user_profile_added_ip_address'  => $user->user_profile_added_ip_address,
			'user_profile_modified_timestamp'  => $user->user_profile_modified_timestamp,
			'user_profile_modified_ip_address'  => $user->user_profile_modified_ip_address		
        );
		 $user_profile_id = (int)$user->user_profile_id;
        if ($user_profile_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getUserProfile($user_profile_id)) {
                $this->update($data, array('user_profile_id' => $user_profile_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteUserProfile($user_profile_id)
    {
        $this->delete(array('user_profile_id' => $user_profile_id));
    }
	public function updateUserProfile($data,$user_profile_id){
		$this->update($data, array('user_profile_id' => $user_profile_id));
	}
	public function updateuserAboutme($data){
		$userdata = array(
            'user_profile_about_me'  => $data['user_profile_about_me']	
        );
		$this->update($userdata, array('user_profile_user_id' => $data['user_profile_user_id']));
		return "success";
	}
}
