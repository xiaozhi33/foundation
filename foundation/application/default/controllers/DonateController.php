<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/sessionutil.php");
	
	class donateController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$my_jjh_pm = new my_categoryDAO();
			$my_jjh_pm ->selectLimit = " and c_parent_id = 0";
			$my_jjh_pm = $my_jjh_pm ->get($this->dbhelper);
			$this->view->assign("my_jjh_pm",$my_jjh_pm);
			
			//我要捐赠功能
			$cid = HttpUtil::getString("cid");
            $cid = (int)$cid;
			$cname = HttpUtil::getString("cname");
			if($cid != "" && $cname != ""){
				$jjh_pm = new my_categoryDAO();
				$jjh_pm ->c_id = $cid;
				$jjh_pm ->c_name = $cname;
				$jjh_pm = $jjh_pm ->get($this->dbhelper);
				if($jjh_pm == ""){
					alert_back("暂无此捐赠项目。");
				}
			}
			
			$c_list = new my_categoryDAO();
			$c_list -> selectLimit = "  and c_online = 1 ";
			$c_list = $c_list ->get($this->dbhelper);
			
			$this->view->assign("c_list",$c_list);
			$this->view->assign("jjh_pm",$jjh_pm);
			echo $this->view->render("donate/index.phtml");
		}
		
		//获取子分类信息ajax
		public function getsmallcateAction(){
			$cate_id = HttpUtil::postInsString("cid");
            $cate_id = (int)$cate_id;
			if($cate_id != 0){
				$my_categoryDAO = new my_categoryDAO();
				$my_categoryDAO ->selectField(" c_id,c_name");
				$my_categoryDAO ->selectLimit = " and c_parent_id = '$cate_id'";
				$my_categoryDAO = $my_categoryDAO ->get($this->dbhelper);
				if(count($my_categoryDAO) >= 1){
					echo json_encode($my_categoryDAO);
				}else{
					echo json_encode(null);
				}
			}else{
				echo json_encode(null);
			}
		}
		
		public function messageAction(){
			echo $this->view->render("donate/message.phtml");
		}
		
		public function fangshiAction(){
			echo $this->view->render("donate/fangshi.phtml");
		}
		
		public function zhanghuAction(){
			echo $this->view->render("donate/zhanghu.phtml");
		}
		
		public function mianshuiAction(){
			echo $this->view->render("donate/mianshui.phtml");
		}
		
		public function shuomingAction(){
			echo $this->view->render("donate/shuoming.phtml");
		}
		
		public function queryAction(){
			echo $this->view->render("donate/query.phtml");
		}
		
		public function wuxieAction(){
			echo $this->view->render("donate/wuxie.phtml");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>