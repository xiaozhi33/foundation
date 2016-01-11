<?php
require_once 'EasyORM.php';
class my_informationDAO extends EasyORM{
	 const tableName = 'my_information';
	 const tableField ='my_infor_id,my_infor_cateid,my_infor_title,my_infor_ctitle,my_infor_sumary,my_infor_content,my_infor_datetime,my_infor_images,my_infor_isdisplay,my_infor_state';
	 public $my_infor_id,$my_infor_cateid,$my_infor_title,$my_infor_ctitle,$my_infor_sumary,$my_infor_content,$my_infor_datetime,$my_infor_images,$my_infor_isdisplay,$my_infor_state;
	 public function __construct($my_infor_id=null,$my_infor_cateid=null){
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