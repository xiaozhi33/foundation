<?php
require_once 'EasyORM.php';
class jjh_request_infoDAO extends EasyORM{
	 const tableName = 'jjh_request_info';
	 const tableField ='id,request_json,request_datetime';
	 public $id,$request_json,$request_datetime;
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
?>	 
    			