<?php
require_once 'Abstractor.php';
require_once 'Interface.php';
/**
 * @package ORM
 * @category Builder
 *
 */
class ORM_Builder_Mysql extends ORM_Builder_Abstractor implements ORM_Builder_Interface {
    private $_joinTableList = array();
    public function __construct(){}
    
    private function _filterParam($params){
        if(is_string($params)||!isset($params)){
            $params = "'$params'";
        }elseif (is_array($params)) {
            foreach ($params as &$param) {
                $param = $this->_filterParam($param);
            }
        }
        return $params;
    }
    
    /**
     * 
     * @param ORM_DAO $dao
     * @param boolean $join 是否考虑连表
     */
    private function _where(ORM_DAO $dao, $join = true){
    	$DAOArray = ($join === true)?array_merge(array($dao),$this->_joinTableList):array($dao);
    	$sql = "\nwhere 1=1";
    	
    	foreach ($DAOArray as $dao){
	        $filters = $dao->getFilterfield();
	        if (is_array($filters)) {
	            foreach ($filters as $filter) {
	                if (is_array($filter['value'])) {
	                    $sql .= " and `{$dao->getAlias()}`.`{$filter['field']}` in (" . implode(',', $this->_filterParam($filter['value'])) . ')';
	                } else {
	                    $sql .= " and `{$dao->getAlias()}`.`{$filter['field']}` {$filter['condition']} " . $this->_filterParam($filter['value']);
	                }
	            }
	        }
    	}
        
        return $sql;
    }
    
    private function _field(ORM_DAO $dao){
        $fields = $dao->getSelectfield();        
        return isset($fields)?$fields:'*';
    }
    
    private function _with(ORM_DAO $dao)
    {
        $sql = '';
        
        $this->_joinTableList = $joinTableList = $dao->getJoinTable();
        if(isset($joinTableList[0]) && is_a($joinTableList[0], 'ORM_DAO_Join')){
        	while ($joinTable = current($joinTableList)){
        		$sql.= $this->_joinBuilderOne($joinTable);
        		foreach ($joinTable->getJoinTable() as $item){
        			$joinTableList[] = $item;
        		}
        		next($joinTableList);
        	}
        	$this->_joinTableList = $joinTableList;
        }
        
        return $sql;
    }
    
    private function _joinBuilderOne(ORM_DAO_Join $join){
    	$sql = ' ';
    	$map = $join->getJoinMap();
    	$onField = $join->getOnField();
    	$sqlOn = array();
    	$joinType = $join->getJoinType();
    	$joinAlias = $join->getAlias();
    	$targetAlias = $join->getTarget()->getAlias();
    	
    	$sql.= "\n{$joinType} join `{$join}` `{$joinAlias}` ";
    	foreach ($map as $key => $value){
    		$sqlOn[] = "`$targetAlias`.`{$key}` = `$joinAlias`.`{$value}`";
    	}
    	
    	if(!empty($sqlOn)){
    		$sql .= 'on ' . implode(' and ', $sqlOn);
    		foreach ($join->getOnField() as $item){
    			if (is_array($item['value'])) {
                    $sql .= " and `{$joinAlias}`.`{$item['field']}` in (" . implode(',', $this->_filterParam($item['value'])) . ')';
                } else {
                    $sql .= " and `{$joinAlias}`.`{$item['field']}` {$item['condition']} " . $this->_filterParam($item['value']);
                }
    		}
    	}
    	
    	return $sql;
    	
    }
    
    public function _order(ORM_DAO $dao)
    {
        $sql = $dao->getOrder();
        return is_string($sql) ? $sql : '';
        
    }
    
    public function _limit(ORM_DAO $dao)
    {
        $sql = $dao->getLimit();
        return is_string($sql) ? $sql : '';
    }
    
    public function _group(ORM_DAO $dao)
    {
        
    }
    
    public function selectSql(ORM_DAO $dao){
        $fields = $this->_field($dao);
        
        $sql = "select $fields from `$dao` `{$dao->getAlias()}`";
        $sql .= $this->_with($dao);
        $sql .= $this->_where($dao);
        $sql .= $dao->selectLimit;
        $sql .= $this->_order($dao);
        $sql .= $this->_limit($dao);
        
        return $sql;
    }
    
    public function updateSql(ORM_DAO $dao){
        
        $sql = '';
        $joinSql = $this->_with($dao);
        foreach (array_merge(array($dao),$this->_joinTableList) as $joinDAO){
        	$tableName = $joinDAO->getAlias();
	        foreach ($this->_filterParam($joinDAO->getSaveField()) as $field => $value) {
	            $sql .= ', `' . $tableName . '`.`' . $field . '` = ' . $value;
	        }
        }
        if(!empty($sql)){
            $sql = ltrim($sql, ', ');
            $sql .= $this->_where($dao);
            $sql = 'update `' . $dao . '` `' . $dao->getAlias() . '` ' . $joinSql . ' set '.$sql;
        }
        
        return $sql;
    }
    
    public function insertSql(ORM_DAO $dao){
        $saveFields = $dao->getSaveField();
        $fields = array_keys($saveFields);
        $sql = 'insert into `' . $dao . '` (`' . implode('`, `', $fields) . '`) values (' . implode(', ', $this->_filterParam($saveFields)) . ')';
        return $sql;
    }
    
    public function deleteSql(ORM_DAO $dao){
        $sql = 'delete from `' . $dao.'`';
        $sql .= $this->_where($dao,false);
        return $sql;
    }
}