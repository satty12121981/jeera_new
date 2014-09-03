<?php
namespace Problem\Model;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
class ProblemTable extends AbstractTableGateway
{ 
    protected $table = 'y2m_Problem'; 
	
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Problem());
        $this->initialize();
    }
		
	// this will fetch single Problem from table
    public function getProblem($problem_id)
    {
        $problem_id  = (int) $problem_id;
        $rowset = $this->select(array('Problem_id' => $problem_id));
        $row = $rowset->current();
        return $row;
    }
		//this function will fetch all groups
    public function fetchAllProblems()
    { 
		$select = new Select;
		$select->from('y2m_problem');
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet;
    }
	
	// this will save Problem in a table
    public function saveProblem(Problem $problem)
    {
       $data = array(
			'problem_reason_title'  => $problem->problem_reason_title,
			'problem_reason_discription'  => $problem->problem_reason_discription,
			'Problem_added_ip_address'  => new \Zend\Db\Sql\Expression("INET_ATON('" . $problem->problem_added_ip_address . "')"),
			'problem_added_user_id'  => $problem->problem_added_user_id,
			'problem_system_type_id'  => $problem->problem_system_type_id
        );

        $problem_id = (int)$problem->Problem_id;
        if ($problem_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getProblem($problem_id)) {
                $this->update($data, array('Problem_id' => $problem_id));
            } else {
                throw new \Exception('Problem id does not exist');
            }
        }
    }
	
	// it will delete any Problem
    public function deleteProblem($problem_id)
    {
        $this->delete(array('Problem_id' => $problem_id));
    }

	
}