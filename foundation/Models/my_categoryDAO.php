<?php
require_once 'EasyORM.php';
class my_categoryDAO extends EasyORM{
	 const tableName = 'my_category';
	 const tableField ='c_id,c_name,c_parent_id,c_path,c_describe,c_content,c_images,c_online';
	 public $c_id,$c_name,$c_parent_id,$c_path,$c_describe,$c_content,$c_images,$c_online;
	 public function __construct($c_id=null){
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
    			