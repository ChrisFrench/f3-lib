<?php 
namespace Dsc\Models\Db;

class Mongo extends \Dsc\Models\Db\Base 
{
    protected $collection = null;
    protected $crud_item_key = "_id";
    
    protected function createDb()
    {
        $db_name = \Base::instance()->get('db.mongo.name');
        $this->db = new \DB\Mongo('mongodb://localhost:27017', $db_name);
        
        return $this;
    }
    
    public function getMapper()
    {
        $mapper = null;
        if ($this->collection) {
            $mapper = new \Dsc\Mongo\Mapper( $this->getDb(), $this->getCollectionName() );
        }
        return $mapper;
    }
    
    public function getCollection() 
    {
        return $this->getDb()->{$this->collection};
    }
    
    public function getCollectionName()
    {
        return $this->collection;
    }

    protected function buildOrderClause()
    {
        $order = null;
    
        if ($this->getState('order_clause')) {
            return $this->getState('order_clause');
        }
        
        if (is_null($this->getState('list.direction')))
        {
            $this->setState('list.direction', $this->default_ordering_direction);
        }
        
        if (is_null($this->getState('list.order'))) {
            $this->setState('list.order', $this->default_ordering_field);
        }
    
        if ($this->getState('list.order') && in_array($this->getState('list.order'), $this->filter_fields)) {
    
            $direction = '1';
            if ($this->getState('list.direction') && in_array($this->getState('list.direction'), $this->order_directions)) {
                $direction = (int) $this->getState('list.direction');
            }
    
            $order = array( $this->getState('list.order') => $direction);
        }
    
        return $order;
    }
}
?>