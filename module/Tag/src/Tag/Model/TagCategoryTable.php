<?php
namespace Tag\Model;
use Zend\Db\Sql\Select, Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Crypt\BlockCipher;	#for encryption
use Zend\Db\Sql\Expression;
class TagCategoryTable extends AbstractTableGateway
{
    protected $table = 'y2m_tag_category'; 

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new TagCategory());

        $this->initialize();
    }
	
    public function fetchAll()
    {
       $resultSet = $this->select();
       return $resultSet;
    }

    public function getTagCategory($tag_category_id)
    {
        $tag_category_id  = (int) $tag_category_id;
        $rowset = $this->select(array('tag_category_id' => $tag_category_id));
        $row = $rowset->current();
        
        return $row;
    }

    public function saveTagCategory(TagCategory $tag_category)
    {
       $data = array(
            'tag_category_title' => $tag_category->tag_category_title,
            'tag_category_desc'  => $tag_category->tag_category_desc,
			'tag_category_status'  => $tag_category->tag_category_status			
        );

        $tag_category_id = (int)$tag_category->tag_category_id;
        if ($tag_category_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getTagCategory($tag_category_id)) {
                $this->update($data, array('tag_category_id' => $tag_category_id));
            } else {
                throw new \Exception('tag category id does not exist');
            }
        }
    }

    public function deleteTagCategory($tag_category_id)
    {
        $this->delete(array('tag_category_id' => $tag_category_id));
    }

    public function selectFormatAllTagCategory($data){
        //This function will return the format of  '0' => 'Apple', '1' => 'Mango' of tags
        $selectObject =array();
        foreach($data as $tag){
            $selectObject[$tag->tag_category_id] = $tag->tag_category_title;
            //print_r($row);
        }
        return $selectObject;	//return blank array
    }

 
}