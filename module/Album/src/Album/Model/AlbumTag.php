<?php

// module/Album/src/Album/Model/Album.php:
namespace Album\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AlbumTag implements InputFilterAwareInterface
{
    public $album_tag_id;
    public $album_tag_data_id;
    public $album_tag_user_id;
	public $album_tag_added_user;
	public $album_tag_xaxis;
    public $album_tag_yaxis;
	public $album_tag_added_date;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->album_tag_id     = (isset($data['album_tag_id'])) ? $data['album_tag_id'] : null;
        $this->album_tag_data_id = (isset($data['album_tag_data_id'])) ? $data['album_tag_data_id'] : null;
        $this->album_tag_user_id  = (isset($data['album_tag_user_id'])) ? $data['album_tag_user_id'] : null;
		$this->album_tag_added_user = (isset($data['album_tag_added_user'])) ? $data['album_tag_added_user'] : null;
		$this->album_tag_xaxis = (isset($data['album_tag_xaxis'])) ? $data['album_tag_xaxis'] : null;
        $this->album_tag_yaxis  = (isset($data['album_tag_yaxis'])) ? $data['album_tag_yaxis'] : null;
		$this->album_tag_added_date = (isset($data['album_tag_added_date'])) ? $data['album_tag_added_date'] : null;    
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