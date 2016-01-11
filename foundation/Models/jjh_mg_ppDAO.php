<?php
require_once 'EasyORM.php';
class jjh_mg_ppDAO extends EasyORM{
	 const tableName = 'jjh_mg_pp';
	 const tableField ='pid,ppname,ppemail,ppmobile,ppphone,pp_pm_id,pp_address,pp_beizhu,pp_qq,pp_msn,pp_cate';
	 public $pid,$ppname,$ppemail,$ppmobile,$ppphone,$pp_pm_id,$pp_address,$pp_beizhu,$pp_qq,$pp_msn,$pp_cate;
	 public function __construct($pid=null){
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