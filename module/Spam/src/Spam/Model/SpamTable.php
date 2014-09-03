<?php
namespace Spam\Model;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
class SpamTable extends AbstractTableGateway
{ 
    protected $table = 'y2m_spam'; 
	
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Spam());
        $this->initialize();
    }

	// this function will check against user of the system Spam exists already
	public function SpamExistsCheck($SpamTypeId,$ProblemId,$ReferenceId,$UserId) {
	
        $SpamTypeId  = (int) $SpamTypeId;
		$ProblemId  = (int) $ProblemId;
		$ReferenceId  = (int) $ReferenceId;
		$UserId  = (int) $UserId;
        $rowset = $this->select(array('spam_system_type_id' => $SpamTypeId,'spam_problem_id' => $ProblemId,'spam_refer_id'=>$ReferenceId,'spam_report_user_id'=>$UserId));
        $row = $rowset->current();
        return $row;
	}
		
	// this will fetch single Spam from table
    public function getSpam($spam_id)
    {
        $spam_id  = (int) $spam_id;
        $rowset = $this->select(array('spam_id' => $spam_id));
        $row = $rowset->current();
        return $row;
    }
	
	// this will save Spam in a table
    public function saveSpam(Spam $Spam)
    {  
       $data = array(
            'spam_system_type_id' => $Spam->spam_system_type_id,
			'spam_problem_id'  => $Spam->spam_problem_id,
			'spam_other_content'  => $Spam->spam_other_content,
			'spam_added_timestamp'  => $Spam->spam_added_timestamp,
			'spam_added_ip_address'  => new \Zend\Db\Sql\Expression("INET_ATON('" . $Spam->spam_added_ip_address . "')"),
			'spam_report_user_id'  => $Spam->spam_report_user_id,
			'spam_target_user_id'  => $Spam->spam_target_user_id,
			'spam_refer_id'  => $Spam->spam_refer_id
        );

        $spam_id = (int)$Spam->spam_id;
        if ($spam_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getSpam($spam_id)) {
                $this->update($data, array('spam_id' => $spam_id));
            } else {
                throw new \Exception('Spam id does not exist');
            }
        }
    }
	
	// it will delete any Spam
    public function deleteSpam($spam_id)
    {
        $this->delete(array('spam_id' => $spam_id));
    }
	
	// it will delete any Spam
    public function deleteSpamByReference($spam_system_type_id,$spam_report_user_id,$spam_refer_id)
    {
        $this->delete(array('spam_system_type_id' => $spam_system_type_id,'spam_report_user_id' => $spam_report_user_id,'spam_refer_id' => $spam_refer_id));
    }
	
}