<?php
require_once 'EasyORM.php';
class jjh_mg_ipDAO extends EasyORM{
	 const tableName = 'jjh_mg_ip';
	 const tableField ='id,ip_info,ip_status';
	 public $id,$ip_info,$ip_status;
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