<?php
require_once 'EasyORM.php';
class my_adminDAO extends EasyORM{
	 const tableName = 'my_admin';
	 const tableField ='id,admin_name,admin_pwd,admin_type';
	 public $id,$admin_name,$admin_pwd,$admin_type;
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
    			