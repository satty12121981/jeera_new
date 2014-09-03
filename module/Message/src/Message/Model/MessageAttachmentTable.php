<?php
namespace Message\Model;

use Zend\Db\Sql\Select, \Zend\Db\Sql\Where;
use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
 
class MessageAttachmentTable extends AbstractTableGateway
{
    protected $table = 'y2m_message_attachment'; 

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new MessageAttachment());
        $this->initialize();
    }

    public function fetchAll()
    {
       $resultSet = $this->select();
       return $resultSet;
    }
	public function saveAttachment(MessageAttachment $MessageAttachment){
		$data = array(
            'message_id' => $MessageAttachment->message_id,
            'attachment_type'  => $MessageAttachment->attachment_type,
			'attachment_element'  => $MessageAttachment->attachment_element,
			  	
        );
		 $this->insert($data);
		 return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();		 
	}
	public function getAttachments($message_id){
		$select = new Select;
		$select->from('y2m_message_attachment')
			   ->where(array("message_id"=>$message_id));
			   $statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);	
		$resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet->toArray();
	}
}