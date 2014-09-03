<?php

// module/Album/src/Album/Model/Album.php:
namespace Album\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AlbumData implements InputFilterAwareInterface
{
    public $data_id;
    public $parent_album_id;
    public $data_type;
	public $data_content;
	public $added_user_id;
	public $data_added_date;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->data_id     = (isset($data['data_id']))     ? $data['data_id']     : null;
        $this->parent_album_id = (isset($data['parent_album_id'])) ? $data['parent_album_id'] : null;
        $this->data_type  = (isset($data['data_type']))  ? $data['data_type']  : null;
		$this->data_content     = (isset($data['data_content']))     ? $data['data_content']     : null;
		$this->added_user_id     = (isset($data['added_user_id']))     ? $data['added_user_id']     : null;
		$this->data_added_date     = (isset($data['data_added_date']))     ? $data['data_added_date']     : null;
       
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
	
 // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }	
}