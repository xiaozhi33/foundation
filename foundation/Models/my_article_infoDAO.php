<?php
require_once 'EasyORM.php';
class my_article_infoDAO extends EasyORM{
	 const tableName = 'my_article_info';
	 const tableField ='a_id,a_cate_id,a_name,a_cname,a_description,a_content,a_author,a_source,a_datetime,a_is_push,a_is_index,a_is_display,a_cover_image';
	 public $a_id,$a_cate_id,$a_name,$a_cname,$a_description,$a_content,$a_author,$a_source,$a_datetime,$a_is_push,$a_is_index,$a_is_display,$a_cover_image;
	 public function __construct($a_id=null){
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
    			