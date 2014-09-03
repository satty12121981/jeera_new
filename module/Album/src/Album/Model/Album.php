<?php

// module/Album/src/Album/Model/Album.php:
namespace Album\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Album implements InputFilterAwareInterface
{
    public $album_id;
    public $album_added_timestamp;
    public $album_added_ip_address;
	
	public $album_status;
    public $album_title;
    public $album_seotitle;
    public $album_group_id;
	
	public $album_user_id;
    public $album_cover_photo_id;
    public $album_location;
	
	
	public $album_view_counter;
    public $album_modified_timestamp;
    public $album_modified_ip_address;
	
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->album_id     = (isset($data['album_id']))     ? $data['album_id']     : null;
        $this->album_added_timestamp = (isset($data['album_added_timestamp'])) ? $data['album_added_timestamp'] : null;
        $this->album_added_ip_address  = (isset($data['album_added_ip_address']))  ? $data['album_added_ip_address']  : null;
		
		$this->album_status     = (isset($data['album_status']))     ? $data['album_status']     : null;
        $this->album_title = (isset($data['album_title'])) ? $data['album_title'] : null;
		$this->album_seotitle = (isset($data['album_seotitle'])) ? $data['album_seotitle'] : null;
        $this->album_group_id  = (isset($data['album_group_id']))  ? $data['album_group_id']  : null;
		
		$this->album_user_id     = (isset($data['album_user_id']))     ? $data['album_user_id']     : null;
        $this->album_cover_photo_id = (isset($data['album_cover_photo_id'])) ? $data['album_cover_photo_id'] : null;
        $this->album_location  = (isset($data['album_location']))  ? $data['album_location']  : null;
		
		$this->album_view_counter     = (isset($data['album_view_counter']))     ? $data['album_view_counter']     : null;
        $this->album_modified_timestamp = (isset($data['album_modified_timestamp'])) ? $data['album_modified_timestamp'] : null;
        $this->album_modified_ip_address  = (isset($data['album_modified_ip_address']))  ? $data['album_modified_ip_address']  : null;
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

            $inputFilter->add($factory->createInput(array(
                'name'     => 'album_group_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
			    $inputFilter->add($factory->createInput(array(
                'name'     => 'album_user_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'album_title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'album_location',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

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