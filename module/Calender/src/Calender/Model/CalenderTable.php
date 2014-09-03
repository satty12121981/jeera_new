<?php 
 
namespace Calender\Model;

/*use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;*/

class CalenderTable 
{
     

    public function __construct()
    {
         
    }

    public function getCalender()
    {
       $monthNames = Array("January", "February", "March", "April", "May", "June", "July","August", "September", "October", "November", "December");
	   if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("n");
		if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");
    }

   

    

}
