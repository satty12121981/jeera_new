<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
//echo "m here"; die();
class AdminActivityTable extends AbstractTableGateway
{
    protected $table = 'y2m_group_activity';

    public function __construct(Adapter $adapter)
    { 
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new AdminActivity());
        $this->initialize();
    }

	#It will fetch all data from table 
    public function fetchAll()
    { 
        $resultSet = $this->select();
        return $resultSet;
    }


   /* public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
	*/

}

