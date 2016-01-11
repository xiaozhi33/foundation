<?php
abstract class EasyORM{
	protected  $whereCondition=null;
	protected  $selectFiled=null;
	protected  $joinTable=null;
	public     $selectLimit=null;
	public     $selectList=null;
	public 	   $debugSql=false;
	
	protected function _init($arguments){
		$tableName=$this->_getChildConst('tableName');
		foreach ($arguments as $key=>$item) {
			if(isset($item)){
				$this->whereCondition.="and {$tableName}.{$key} = '{$item}' ";
			}
		}
		if(!empty($this->whereCondition))
		$this->whereCondition=' where 1=1 '.$this->whereCondition;
	}
	
	private function _updateSetBuilder($tableName){
		$updateStr="update $tableName ";
		foreach (explode(',',$this->_getChildConst('tableField')) as $item){
			if(isset($this->$item)){
				$setStr.="`{$item}` = '{$this->$item}',";		
			}
		}
		if(!$updateStr)return false;
		
		$updateStr.='set '.str_replace(array("'`","`'"),array('',''),$setStr);
		return substr($updateStr,0,strlen($updateStr)-1);
	}
	
	private function _queryWhereBuilder($tableName){
		$selectStr = null;
		foreach (explode(',',$this->_getChildConst('tableField')) as $item){
			if(isset($this->$item)){
				$selectStr.="and {$tableName}.{$item} = '{$this->$item}' ";		
			}
		}
		return $selectStr;
	}
	
	protected function deleteTable($db,$forceDelete=false){
		if(empty($this->whereCondition)){
			if(!$forceDelete) return;
		}
		$sql='delete from '.$this->_getChildConst('tableName').' '.$this->whereCondition;
		if($this->debugSql)$this->debugSql($sql);
		try{
            return $db->executeOne($sql);
        }catch (Exception $e){
            throw $e;
        }
	}
	
	protected function selectTable($db,$conditionAddOn='',$tableAlias=null){
		try {
			$tableName=$this->_getChildConst('tableName');
			if(!isset($this->whereCondition))$this->whereCondition='where 1=1';
			if(!isset($tableAlias))$tableAlias=$tableName;
			else {
				$this->whereCondition = str_replace("$tableName.","{$tableAlias}.",$this->whereCondition);
				$tableName.=" {$tableAlias}";
			}
			if(isset($this->selectFiled)){
				if(is_array($this->selectFiled)){
					foreach ($this->selectFiled as $tableAlias=>&$table) {
						foreach ($table as $filed => &$alias){
							$alias="{$tableAlias}.{$filed} as {$alias}";
						}
						$table=implode(',',$table);
					}
					$selectFiled=implode(',',$this->selectFiled);
				}else{
					$selectFiled=$this->selectFiled;
				}
			}
			if(empty($selectFiled)) $selectFiled=$tableAlias.'.*';
			$sql=sprintf("select %s from %s %s %s %s %s %s",
					     $selectFiled,//所选字段
					     $tableName,//表名(或别名)
					     $this->joinTable,//连表操作
					     $this->whereCondition,
					     $this->_queryWhereBuilder($tableAlias),//where条件
					     $conditionAddOn,//附加条件,如额外表条件,like,比较,范围操作等
					     $this->selectLimit //查询限制
					 );
			if($this->debugSql)$this->debugSql($sql);
			return $db->fetchAllData($sql);
		}catch (Exception $e){
			throw $e;
		}
	}
	
	protected function insertTable($db,$initValue=array()){
		$insert = null;
		$value = null;
		foreach (explode(',',$this->_getChildConst('tableField')) as $item){
			$insert.=$item.'`,`';
			if(isset($initValue[$item])){
				$value.="'".trim($initValue[$item])."',";
			}else{
				$value.=":$item,";
				$parms[$item]=$this->$item;
			}
		}
		$insert=substr($insert,0,strlen($insert)-2);
		$value=substr($value,0,strlen($value)-1);
		$value=str_replace(array("'`","`'"),array('',''),$value);
		$sql='insert into '.$this->_getChildConst('tableName') ."(`{$insert}) values ({$value})";
	 	if($this->debugSql)$this->debugSql($sql);
		try{
            return $db->executeOne($sql,$parms);
        }catch (Exception $e){
            throw $e;
        }
	}
	
	protected function updateTable($db){
		$updateStr=$this->_updateSetBuilder($this->_getChildConst('tableName'));
		if(!$updateStr) return; 
		$updateStr.=$this->whereCondition;
		if($this->debugSql)$this->debugSql($updateStr);
		try {
			return $db->executeOne($updateStr);
		}catch (Exception $e){
			throw $e;
		}
	}
	
	protected function writeTable($db,$parms=array()){
		if(isset($this->whereCondition)){
			return $this->updateTable($db);
		}else{
			return $this->insertTable($db,$parms);
		}
	}
	
	/**
	 * @return EasyORM
	 */
	public function joinTable($sql){
		$this->joinTable.=" {$sql}\n";
		return $this;
	}
	
	/**
	 * @return EasyORM
	 */
	public function selectField($selectField){
		$this->selectFiled=$selectField;
		return $this;
	}
	
	public function get($db){
		return $this->selectTable($db);
	}
	
	public function save($db){
		return $this->writeTable($db,array());
	}
	
	public function debugSql($sql){
		echo $sql; exit();
	}
	
	public function __set($name,$value){
		throw new Exception("尝试给未定义的类变量{$name}赋值{$value}");
	}
	
	public function __get($name){
		throw new Exception("尝试访问未定义的类变量{$name}");
	}
	
	private function _getChildConst($name){
		return eval('return '.get_class($this).'::'.$name.';');
	}
}
?>