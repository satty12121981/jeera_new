<?php
// module/Album/src/Album/Form/AlbumForm.php:
namespace Album\Form;

use Zend\Form\Form;

class AlbumForm extends Form
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('album');
        $this->setAttribute('method', 'post');
         
        $this->add(array(
            'name' => 'album_title',
            'attributes' => array(
                'type'  => 'text',
				'id' => 'album_title',
            ),
            'options' => array(
                'label' => 'Album Title',
            ),
        ));
        $this->add(array(
            'name' => 'album_location',
			
            'attributes' => array(
                'type'  => 'text',
				'id' => 'album_location',
            ),
            'options' => array(
                'label' => 'Album Location',
				
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'album_save',
				'class'=>'blue-butn'
            ),
        ));
    }
}