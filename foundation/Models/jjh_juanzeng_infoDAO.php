<?php
require_once 'EasyORM.php';
class jjh_juanzeng_infoDAO extends EasyORM{
	 const tableName = 'jjh_juanzeng_info';
	 const tableField ='id,jjh_juanzeng_danwei,jjh_juanzeng_pm,jjh_juanzeng_datetime,jjh_juanzeng_jiner';
	 public $id,$jjh_juanzeng_danwei,$jjh_juanzeng_pm,$jjh_juanzeng_datetime,$jjh_juanzeng_jiner;
	 public function __construct($id = null){
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
    			