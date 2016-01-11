<?php
class ORM_DAO_Join extends ORM_DAO {
	private $_onField = array();
	private $_joinMap = array();
	private $_joinType = 'left';
	private $_targetDAO;
	
	public function __construct(ORM $ORM,$tableName,$targetDAO){
		$this->_targetDAO = $targetDAO;
		parent::__construct($ORM, $tableName);
    }
    
    public function setJoinType($type){
    	$this->_joinType = $type;
    }
    
    public function getJoinType(){
    	return $this->_joinType;
    }
	
	public function getOnField(){
		return $this->_onField;
	}
	
	public function getJoinMap(){
		return $this->_joinMap;
	}
	
	public function setJoinMap($map){
		$this->_joinMap = $map;
	}
	
	public function getTarget(){
		return $this->_targetDAO;
	}
	
	public function select($selectFields){
		$this->_targetDAO->select($selectFields);
		return $this;
	}
	
	public function limit($limit, $counts = null){
		$this->_targetDAO->limit($limit, $counts);
		return $this;
	}
	
	public function order($orders){
		$this->_targetDAO->order($orders);
		return $this;
	}
	
	public function getPager($option = array()){
		return $this->_targetDAO->getPager($option);
	}
	
	public function getForm($options=null,Zend_Translate $translator = null){
		return $this->_targetDAO->getForm($options,$translator);
	}
	
	public function __call($fun, $args){
		if(substr($fun,0,2) === 'on'){
            $filter = array();
            $filter['field'] = substr($fun,2);
            $filter['field'][0] = strtolower($filter['field'][0]);
            $filter['value'] = $args[0];
            $filter['condition'] = isset($args[1])?$args[1]:'=';
            if(isset($args[0])){
            	$this->_onField[] = $filter;
            }
            return $this;
        }else{
        	return parent::__call($fun, $args);
        }
	}
}