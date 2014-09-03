<?php

namespace Country\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

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
    public function fetchAll()
    {
       $resultSet = $this->select();
        return $resultSet;
    }

   public function getCountry($country_id)
    {
        $id  = (int) $country_id;
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
	
	
	
	public function selectFormatAllCountry($data){
		//This function will return the format of  '0' => 'Apple', '1' => 'Mango' of countrys		
				 
		$selectObject =array();

		foreach($data as $country){
			$selectObject[$country->country_id] = $country->country_title;
			//print_r($row);
		}		
		return $selectObject;	//return blank array
	}
	
	
 
}