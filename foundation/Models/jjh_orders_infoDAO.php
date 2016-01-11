<?php
require_once 'EasyORM.php';
class jjh_orders_infoDAO extends EasyORM{
	 const tableName = 'jjh_orders_info';
	 const tableField ='jjh_order_id,jjh_price_cate,jjh_pm,jjh_money,jjh_content,jjh_activities,jjh_is_zhengshu,jjh_is_shouju,jjh_donors_name,jjh_donors_cname,jjh_donors_alumni,jjh_donors_phone,jjh_donors_mobile,jjh_donors_zip,jjh_donors_company_position,jjh_donors_is_anonymous,jjh_donors_email';
	 public $jjh_order_id,$jjh_price_cate,$jjh_pm,$jjh_money,$jjh_content,$jjh_activities,$jjh_is_zhengshu,$jjh_is_shouju,$jjh_donors_name,$jjh_donors_cname,$jjh_donors_alumni,$jjh_donors_phone,$jjh_donors_mobile,$jjh_donors_zip,$jjh_donors_company_position,$jjh_donors_is_anonymous,$jjh_donors_email;
	 public function __construct($jjh_order_id=null){
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
    			