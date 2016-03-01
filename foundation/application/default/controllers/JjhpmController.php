<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/sessionutil.php");
	
	class jjhpmController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$cid = HttpUtil::getString("cid");
			if($cid == null){
				$cid = 1;
			}
			
			$pm_info = new my_categoryDAO($cid);
			$pm_info = $pm_info ->get($this->dbhelper);
			
			$jjh_info = new my_categoryDAO();
			$jjh_info ->selectLimit = " and c_parent_id = '$cid'";
			$jjh_info = $jjh_info ->get($this->dbhelper);

			$this->view->assign("pm_info",$pm_info);
			$this->view->assign("jjh_info",$jjh_info);
			
			echo $this->view->render("jjhpm/index.phtml");
		}
		
		public function jinchengAction(){
			$cid = HttpUtil::getString("cid");
			if($cid == null){
				alert_back("请选择项目！谢谢");
			}
			$pm_info = new my_categoryDAO($cid);
			$pm_info = $pm_info ->get($this->dbhelper);
			$this->view->assign("pm_info",$pm_info);
			echo $this->view->render("jjhpm/jincheng.phtml");
		}
		
		public function tuijianAction(){
			echo $this->view->render("jjhpm/tuijian.phtml");
		}
		
		public function tuijiancateAction(){
			$id = HttpUtil::getString("id");
			$url_info = "jjhpm/tuijian".$id.".phtml";
			echo $this->view->render($url_info);
		}
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
			
			$my_jjh_pm = new my_categoryDAO();
			$my_jjh_pm ->selectLimit = " and c_parent_id = 0";
			$my_jjh_pm = $my_jjh_pm ->get($this->dbhelper);
			$this->view->assign("my_jjh_pm",$my_jjh_pm);
		}
		
	}
?>