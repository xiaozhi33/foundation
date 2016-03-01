<?php
require_once 'EasyORM.php';
class jjh_surveyDAO extends EasyORM{
	 const tableName = 'jjh_survey';
	 const tableField ='id,name,content';
	 public $id,$name,$content;
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