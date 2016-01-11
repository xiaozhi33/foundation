<?php
require_once 'EasyORM.php';
class my_ad_infoDAO extends EasyORM{
	 const tableName = 'my_ad_info';
	 const tableField ='ad_id,ad_name,ad_image,ad_content,ad_link,ad_type';
	 public $ad_id,$ad_name,$ad_image,$ad_content,$ad_link,$ad_type;
	 public function __construct($ad_id=null){
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