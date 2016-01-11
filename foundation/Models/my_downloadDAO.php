<?php
require_once 'EasyORM.php';
class my_downloadDAO extends EasyORM{
	 const tableName = 'my_download';
	 const tableField ='did,download_cate,download_title,download_content,download_file';
	 public $did,$download_cate,$download_title,$download_content,$download_file;
	 public function __construct($did=null){
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
