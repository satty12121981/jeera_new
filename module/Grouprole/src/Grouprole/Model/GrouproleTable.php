<?php
####################Country table Model #################################
//Created by Shail
#########################################################################
namespace Grouprole\Model;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Crypt\BlockCipher;	#for encryption
use Zend\Db\Sql\Select;

class GrouproleTable extends AbstractTableGateway
{
    protected $table = 'y2m_group_roles'; 

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Grouprole());

        $this->initialize();
    }
	#It will fetch all countries
    public function fetchAll(Select $select = null)
    {
      if (null === $select)
          $select = new Select();
        $select->from($this->table);
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
	public function getRoles(){
		$select = new Select();
		$select->from($this->table);
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet;	
	}
 
}