<?php
namespace Country\Model;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Crypt\BlockCipher;
use Zend\Db\Sql\Select;
class CountryTable extends AbstractTableGateway
{
    protected $table = 'y2m_country'; 
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Country());

        $this->initialize();
    }	 
    public function fetchAll(Select $select = null)
    {
      if (null === $select)
        $select = new Select();
        $select->from($this->table);      
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);        
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;      
    } 
	public function getCountry($country_id)
    {
        $country_id  = (int) $country_id;
        $rowset = $this->select(array('country_id' => $country_id));
        $row = $rowset->current();
        return $row;
    }	 
    public function saveCountry(Country $country)
    {
       $data = array(
            'country_title' => $country->country_title,
            'country_code'  => $country->country_code,
			'country_added_ip_address'  => $country->country_added_ip_address,
			'country_added_timestamp'  => $country->country_added_timestamp,
			'country_status'  => $country->country_status,
			'country_modified_timestamp'  => $country->country_modified_timestamp,
			'country_modified_ip_address'  => $country->country_modified_ip_address		
        );
        $country_id = (int)$country->country_id;
        if ($country_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getCountry($country_id)) {
                $this->update($data, array('country_id' => $country_id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }	 
    public function deleteCountry($country_id)
    {
        $this->delete(array('country_id' => $country_id));
    }	 
	public function selectFormatAllCountry(){
		$data = $this->fetchAll();		 		 
		$blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
		$blockCipher->setKey('*&hhjj()_$#(&&^$%^$%^KMNVHDrt#$$$%#@@'); 		
		$selectObject =array();
		foreach($data as $country){
			$selectObject[$blockCipher->encrypt($country->country_id)] = $country->country_title;			 
		}		
		return $selectObject;
	}
	public function getCountryIdFromGeoCode($geocode){
		$select = new Select();
        $select->from($this->table)
			   ->columns(array("country_id"))
			   ->where(array("country_code_googlemap"=>$geocode));
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);       
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet->current();
	}
	public function getCountryIdfromName($country){
		$select = new Select();
        $select->from($this->table)
			   ->columns(array("country_id"))
			   ->where(array("country_title"=>$country));
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);        
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet->current();
	}
	public function selectAllCountryWithoutEncrypt(){
		$data = $this->fetchAll();		 		 	  		
		$selectObject =array();
		foreach($data as $country){
			$selectObject[$country->country_id] = $country->country_title;			 
		}		
		return $selectObject;
	}
}