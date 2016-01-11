<?php
require_once 'EasyORM.php';
class jjh_ordersDAO extends EasyORM{
	 const tableName = 'jjh_orders';
	 const tableField ='jjh_id,jjh_order_id,jjh_rdero_datetime,jjh_cate_id,jjh_pm,jjh_activities,jjh_donors_alumn,jjh_order_statue';
	 public $jjh_id,$jjh_order_id,$jjh_rdero_datetime,$jjh_cate_id,$jjh_pm,$jjh_activities,$jjh_donors_alumn,$jjh_order_statue;
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