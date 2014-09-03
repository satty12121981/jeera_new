<?php
namespace City\Model;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Crypt\BlockCipher;
use Zend\Db\Sql\Select;
class CityTable extends AbstractTableGateway
{
    protected $table = 'y2m_city'; 
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new City());
        $this->initialize();
    }
 
    public function fetchAll(Select $select = null)
    {
      if (null === $select)
        $select = new Select();
        $select->from($this->table);
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
	public function getCountry($city_id)
    {
        $city_id  = (int) $city_id;
        $rowset = $this->select(array('city_id' => $city_id));
        $row = $rowset->current();
        return $row;
    }	 
    public function saveCountry(City $city)
    {
       $data = array(
            'country_id' => $city->country_id,
            'name'  => $city->name,			 
        );
        $city_id = (int)$city->city_id;
        if ($city_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getCity($city_id)) {
                $this->update($data, array('city_id' => $city_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }	 
    public function deleteCity($city_id)
    {
        $this->delete(array('city_id' => $city_id));
    }
	public function selectFormatAllCity($country_id){
		$data =  $select = new Select();
        $select->from($this->table);
		$select->where(array('country_id = '.$country_id));		
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        $data = $resultSet;;		
		$selectObject =array();
		foreach($data as $city){
			$selectObject[$city->city_id] = $city->name;			
		}		
		return $selectObject;
	} 
	public function selectAllCity($country_id){
		$data =  $select = new Select();
        $select->from($this->table);
		$select->where(array('country_id = '.$country_id));
		//echo $select->getSqlString();die();
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();       
		return $resultSet; 		
		 
	} 
}