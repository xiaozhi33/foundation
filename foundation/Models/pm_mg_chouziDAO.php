<?php
require_once 'EasyORM.php';
class pm_mg_chouziDAO extends EasyORM{
	 const tableName = 'pm_mg_chouzi';
	 const tableField ='id,pid,pname,department,cate,pm_tuidongqi,pm_fuhuaqi,pm_qianyue_datetime,pm_yishi,pm_xieyii_dianziban,pm_fankui_datetime,pm_qishi_datetime,pm_qixian,pm_jiezhi_datetime,pm_xieyi_juanzeng_jiner,pm_liuben,beizhu,parent_pm_id,parent_pm_id_path';
	 public $id,$pid,$pname,$department,$cate,$pm_tuidongqi,$pm_fuhuaqi,$pm_qianyue_datetime,$pm_yishi,$pm_xieyii_dianziban,$pm_fankui_datetime,$pm_qishi_datetime,$pm_qixian,$pm_jiezhi_datetime,$pm_xieyi_juanzeng_jiner,$pm_liuben,$beizhu;
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