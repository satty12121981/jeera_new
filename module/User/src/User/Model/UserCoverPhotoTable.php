<?php 

namespace User\Model;
use Zend\Db\Sql\Select , \Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class UserCoverPhotoTable extends AbstractTableGateway
{
    protected $table = 'y2m_user_cover_photo';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new UserProfilePhoto());
        $this->initialize();
    }
	public function addUserCoverPic($data){
		$this->insert($data);
		return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
	}
	public function checkUserCoverPicExist($user_id){
		$select = new Select;
		$select->from('y2m_user_cover_photo')  
		->where(array('cover_user_id' =>  $user_id));
		
	    $statement = $this->adapter->createStatement();
	    $select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString(); die();
		
	    $resultSet = new ResultSet();
	    $resultSet->initialize($statement->execute());  
        return $resultSet->current();
	}
 

}
