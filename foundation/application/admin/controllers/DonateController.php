<?php
	require_once("BaseController.php");
	class Admin_donateController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			if(HttpUtil::postString("orderid")!=""){
				$jjh_order = new jjh_ordersDAO(HttpUtil::postString("orderid"));
			}else {
				$jjh_order = new jjh_ordersDAO();
			}

			if(HttpUtil::postString("uname")!=""){
				$uname = HttpUtil::postString("uname");
				$jjh_order->selectLimit = " and jjh_orders_info.jjh_donors_name like '%$uname%'";
			}
			$jjh_order ->joinTable(" left join jjh_orders_info on jjh_orders_info.jjh_order_id = jjh_orders.jjh_order_id");
			$jjh_order ->selectField(" jjh_orders.*,jjh_orders.jjh_order_id as oid,jjh_orders_info.*");
			$jjh_order->selectLimit .= " order by oid desc";
			//$jjh_order->debugSql = true;
			$jjh_order = $jjh_order->get($this->dbhelper);

			$total = count($jjh_order);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($jjh_order,null,"index",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('jjh_order',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render('donate/index.phtml');
		}
		
		public function editdonateAction(){
			if(HttpUtil::getString("id") != ""){
				$jjh_ordersDAO = new jjh_ordersDAO(HttpUtil::getString("id"));
				$jjh_ordersDAO ->joinTable(" left join jjh_orders_info on jjh_orders_info.jjh_order_id = jjh_orders.jjh_order_id");
				$jjh_ordersDAO ->selectField(" jjh_orders.*,jjh_orders.jjh_order_id as oid,jjh_orders_info.*");
				$jjh_ordersDAO ->selectLimit .= " order by oid desc";
				$jjh_ordersDAO = $jjh_ordersDAO->get($this->dbhelper);
				$this->view->assign('jjh_order',$jjh_ordersDAO);
				//var_dump($jjh_ordersDAO);exit();
				echo $this->view->render('donate/editdonate.phtml');
			}else {
				alert_back("无此信息");
			}
		}
	
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkadmin();
		}
	}
?>