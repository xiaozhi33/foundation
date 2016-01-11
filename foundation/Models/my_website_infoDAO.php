<?php
require_once 'EasyORM.php';
class my_website_infoDAO extends EasyORM{
	 const tableName = 'my_website_info';
	 const tableField ='web_name,web_copyright,web_logo,web_url,web_weibo,web_tel,web_fax,web_email,web_address,web_recall';
	 public $web_name,$web_copyright,$web_logo,$web_url,$web_weibo,$web_tel,$web_fax,$web_email,$web_address,$web_recall;
	 public function __construct(){
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