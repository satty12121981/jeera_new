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

    public function selectFormatAllTagCategory($data)
    {
        //This function will return the format of  '0' => 'Apple', '1' => 'Mango' of tags
        $selectObject =array();
        foreach($data as $tag){
            $selectObject[$tag->tag_category_id] = $tag->tag_category_title;
            //print_r($row);
        }
        return $selectObject;	//return blank array
    }

    public function getAllTagCategories($limit,$offset,$field="tag_category_id",$order='ASC',$search=''){ 
        $select = new Select;
        $usersubselect = new select;
        $groupsubselect = new select;   
        $tagsubselect = new select;   

        $tagsubselect->from('y2m_tag')
            ->columns(array(new Expression('COUNT(y2m_tag.category_id) as tag_count'),'category_id','tag_id'))
            ->group(array('category_id'))
            ;
        $usersubselect->from('y2m_user_tag')
            ->columns(array(new Expression('COUNT(y2m_user_tag.user_tag_id) as user_count'),'user_tag_tag_id'))
            ->group(array('user_tag_tag_id'))
            ;
        $groupsubselect->from('y2m_group_tag')
            ->columns(array(new Expression('COUNT(y2m_group_tag.group_tag_id) as group_count'),'group_tag_tag_id'))
            ->group(array('group_tag_tag_id'))
            ;

        $select->from('y2m_tag_category')
            ->columns(array('tag_category_id'=>'tag_category_id','tag_category_title'=>'tag_category_title'))
            ->join(array('temp2' => $tagsubselect), 'temp2.category_id = y2m_tag_category.tag_category_id',array('tag_count'),'left')
            ->join(array('temp' => $usersubselect), 'temp.user_tag_tag_id = temp2.tag_id',array('user_count'),'left')
            ->join(array('temp1' => $groupsubselect), 'temp1.group_tag_tag_id = temp2.tag_id',array('group_count'),'left');

        $select->limit($limit);
        $select->offset($offset);
        $select->order($field.' '.$order);
        if($search!=''){
            $select->where->like('y2m_tag_category.tag_category_title',$search.'%');      
        }
        $statement = $this->adapter->createStatement();
        //echo $select->getSqlString();
        $select->prepareStatement($this->adapter, $statement);
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());              
        return  $resultSet->buffer();
    }
    
    public function getCountOfAllTagCategories($search=''){
        $select = new Select;
        $select->from('y2m_tag_category')        
               ->columns(array(new Expression('COUNT(y2m_tag_category.tag_category_id) as tag_category_count')));
        if($search!=''){
            $select->where->like('y2m_tag_category.tag_category_title',$search.'%');      
        }
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return  $resultSet->current()->tag_count;
    }
 
}