<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/sessionutil.php");

	class IndexController extends BaseController {
		private $dbhelper;
		public function indexAction(){		
			$my_website_infoDAO = new my_website_infoDAO();
			$my_website_infoDAO = $my_website_infoDAO ->get($this->dbhelper);
			$this->view->assign("websiteinfo",$my_website_infoDAO);
			
			//首页图片滚动4张
			$tuwen_list = new my_informationDAO(null,"tuwen");
			$tuwen_list ->selectLimit = " and my_infor_isdisplay=1 and my_infor_state=1";
			$tuwen_list = $tuwen_list->get($this->dbhelper);
			$this->view->assign("tuwen_list",$tuwen_list);
			
			//首页广告
			$ad_list = new my_ad_infoDAO();
			$ad_list ->selectLimit = " limit 0,4";
			$ad_list = $ad_list ->get($this->dbhelper);
			$this->view->assign("ad_list",$ad_list);
			
			//公告新闻
			$gonggao_list = new my_informationDAO(null,"xinwen");
			$gonggao_list ->selectLimit = " and my_infor_isdisplay=1 and my_infor_state=1 order by my_infor_id desc limit 0,6";
			$gonggao_list = $gonggao_list->get($this->dbhelper);
			$this->view->assign("gonggao_list",$gonggao_list);
			
			//资助信息
			$zizhuxinxi_list = new my_informationDAO(null,"zizhuxinxi");
			$zizhuxinxi_list ->selectLimit = " and my_infor_isdisplay=1 and my_infor_state=1 order by my_infor_id desc limit 0,6";
			$zizhuxinxi_list = $zizhuxinxi_list->get($this->dbhelper);
			$this->view->assign("zizhuxinxi_list",$zizhuxinxi_list);
			
			//最新捐赠
			$juanzeng = new jjh_juanzeng_infoDAO();
			$juanzeng ->selectLimit = " order by jjh_juanzeng_datetime desc limit 0,6";
			$juanzenglist = $juanzeng ->get($this->dbhelper);
			$this->view->assign("juanzenglist",$juanzenglist);
			
			//var_dump($juanzenglist);
			
			/////////////////////////////////////////////////////////////////////////
			
			/*
			 * 2012-10-23 更新 最新捐赠
			 * 只显示订单状态为 已支付
			 */
			//$jjh_order = new jjh_ordersDAO();
			//$jjh_order ->joinTable(" left join jjh_orders_info on jjh_orders_info.jjh_order_id = jjh_orders.jjh_order_id");
			//$jjh_order ->selectField(" jjh_orders_info.jjh_donors_name as pm_pp,jjh_orders_info.jjh_money as zijin_daozheng_jiner,jjh_orders.jjh_rdero_datetime as zijin_daozhang_datetime");
			//$jjh_order ->selectLimit .= " and jjh_orders.jjh_order_statue>0 ";
			//$jjh_order ->selectLimit .= " order by jjh_orders.jjh_id desc limit 0,6";
			//$jjh_order = $jjh_order ->get($this->dbhelper);
			//$this->view->assign("zizhuinfo",$jjh_order);
			
			//new最新捐赠
			//$zizhuinfo = new pm_mg_infoDAO();
			//$zizhuinfo ->selectLimit = " and zijin_daozheng_jiner != '' order by zijin_daozhang_datetime desc limit 0,6";
			//$zizhuinfo = $zizhuinfo ->get($this->dbhelper);
			//$this->view->assign("zizhuinfo",$zizhuinfo);
			
			///////////////////////////////////////////////////////////////////////////
			
			//new最新捐赠
			$zizhuinfo = new pm_mg_infoDAO();
			$zizhuinfo ->selectLimit = " and is_renling=1 and zijin_daozheng_jiner != '' order by zijin_daozhang_datetime desc limit 0,6";
			$zizhuinfo = $zizhuinfo ->get($this->dbhelper);
			$this->view->assign("zizhuinfo",$zizhuinfo);
			
			//new资助信息
			$shiyonginfo = new pm_mg_infoDAO();
			$shiyonginfo ->selectLimit = "and shiyong_zhichu_jiner != '' order by shiyong_zhichu_datetime desc limit 0,6";
			$shiyonginfo = $shiyonginfo ->get($this->dbhelper);
			$this->view->assign("shiyonginfo",$shiyonginfo);
			
			echo $this->view->render("index/index.phtml");
		}

		public function getsocpicAction($sex = null,$uid = null){
			$woow_picDAO = new woow_picDAO();
			$woow_picDAO ->is_score_pic = 1;
			$woow_picDAO ->joinTable .= " left join woow_user on woow_user.user_id = woow_pic.user_id";
			$woow_picDAO ->joinTable .= " left join woow_user_info on woow_user_info.user_id = woow_pic.user_id";
			if($sex != null){
				$woow_picDAO ->whereCondition = " where woow_user.user_sex ='$sex'";
			}
			
			if($uid != null){
				$woow_picDAO ->whereCondition = " where woow_user.user_id ='$uid'";
			}
			$woow_picDAO ->selectField(" *");
			//$woow_picDAO ->debugSql = true;
			$woow_picDAO = $woow_picDAO ->get($this->dbhelper);
			return $woow_picDAO;
		}
		
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper->connect();
			SessionUtil::sessionStart();			
		}
	}
?>