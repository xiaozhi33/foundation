<?php
require_once 'EasyORM.php';
class pm_mg_chouziDAO extends EasyORM{
	 const tableName = 'pm_mg_chouzi';
	 const tableField ='id,pid,pname,department,cate,pm_tuidongqi,pm_fuhuaqi,pm_qianyue_datetime,pm_yishi,pm_xieyii_dianziban,pm_fankui_datetime,pm_qishi_datetime,pm_qixian,pm_jiezhi_datetime,pm_xieyi_juanzeng_jiner,pm_liuben,beizhu,parent_pm_id,parent_pm_id_path,schedule,pm_fzr,pm_llr,pm_ckfzr,pm_jzf,pm_jzfllr,pm_sjjzf,pm_sjjzfllr,pm_fzr_email,pm_fzr_tel,pm_llr_email,pm_llr_tel,pm_ckfzr_email,pm_ckfzr_tel,pm_jzf_email,pm_jzf_tel,pm_jzfllr_email,pm_jzfllr_tel,pm_sjjzf_email,pm_sjjzf_tel,pm_sjjzfllr_email,pm_sjjzfllr_tel,star,is_del';
	 public $id,$pid,$pname,$department,$cate,$pm_tuidongqi,$pm_fuhuaqi,$pm_qianyue_datetime,$pm_yishi,$pm_xieyii_dianziban,$pm_fankui_datetime,$pm_qishi_datetime,$pm_qixian,$pm_jiezhi_datetime,$pm_xieyi_juanzeng_jiner,$pm_liuben,$beizhu,$parent_pm_id,$parent_pm_id_path,$schedule,$pm_fzr,$pm_llr,$pm_ckfzr,$pm_jzf,$pm_jzfllr,$pm_sjjzf,$pm_sjjzfllr,$pm_fzr_email,$pm_fzr_tel,$pm_llr_email,$pm_llr_tel,$pm_ckfzr_email,$pm_ckfzr_tel,$pm_jzf_email,$pm_jzf_tel,$pm_jzfllr_email,$pm_jzfllr_tel,$pm_sjjzf_email,$pm_sjjzf_tel,$pm_sjjzfllr_email,$pm_sjjzfllr_tel,$star,$is_del;
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