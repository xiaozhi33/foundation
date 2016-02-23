<?php
require_once 'EasyORM.php';
class pm_mg_infoDAO extends EasyORM{
	 const tableName = 'pm_mg_info';
	 const tableField ='id,cate_id,pm_name,pm_pp,pm_pp_company,pm_pp_cate,pm_is_school,pm_juanzeng_jibie,pm_juanzeng_cate,pm_juanzeng_yongtu,zijin_daozhang_datetime,zijin_daozheng_jiner,zijin_laiyuan_qudao,peibi,piaoju,zhengshu,shiyong_zhichu_datetime,shiyong_zhichu_jiner,jiangli_fanwei,jiangli_renshu,beizhu,pm_file,peibi_jiner,peibi_department,peibi_card,peibi_pp,peibi_jupi,yishi,jinianpin,shiyong_type';
	 public $id,$cate_id,$pm_name,$pm_pp,$pm_pp_company,$pm_pp_cate,$pm_is_school,$pm_juanzeng_jibie,$pm_juanzeng_cate,$pm_juanzeng_yongtu,$zijin_daozhang_datetime,$zijin_daozheng_jiner,$zijin_laiyuan_qudao,$peibi,$piaoju,$zhengshu,$shiyong_zhichu_datetime,$shiyong_zhichu_jiner,$jiangli_fanwei,$jiangli_renshu,$beizhu,$pm_file,$peibi_jiner,$peibi_department,$peibi_card,$peibi_pp,$peibi_jupi,$yishi,$jinianpin,$shiyong_type;
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
    			