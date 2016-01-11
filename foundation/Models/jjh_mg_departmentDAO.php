<?php
require_once 'EasyORM.php';
class jjh_mg_departmentDAO extends EasyORM{
	 const tableName = 'jjh_mg_department';
	 const tableField ='id,pname';
	 public $id,$pname;
	 public function __construct($id=null){
		$this->_init(get_defined_vars());
	}
	 public function get($db){
		return $this->selectTable($db);
	}
	 public function save($db){
		return $this->writeTable($db,array());
	}
	 public function del($db){
		return $this->deleteTable($db);
	}
}
