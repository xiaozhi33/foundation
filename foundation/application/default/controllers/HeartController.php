<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/functions.php");
	require_once("../util/sessionutil.php");
	
	class heartController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$my_informationDAO = new my_informationDAO();
			$my_informationDAO ->my_infor_cateid = "shouyi";
			$my_informationDAO ->selectLimit = " and my_infor_isdisplay = 1 and my_infor_state = 1 order by my_infor_datetime desc limit 0,6 ";
			$my_informationDAO = $my_informationDAO->get($this->dbhelper);
			
			
			$RENwu = new my_informationDAO();
			$RENwu ->my_infor_cateid = "renwu";
			$RENwu ->selectLimit = " and my_infor_isdisplay = 1 and my_infor_state = 1 order by my_infor_datetime desc limit 0,6 ";
			$RENwu = $RENwu->get($this->dbhelper);
				
			$this->view->assign("renwu",$RENwu);
			$this->view->assign("info",$my_informationDAO);
			echo $this->view->render("heart/index.phtml");
		}
		
		public function renwuAction(){
			$my_informationDAO = new my_informationDAO();
			$my_informationDAO ->my_infor_cateid = "renwu";
			$my_informationDAO->selectLimit = " and my_infor_isdisplay = 1 and my_infor_state = 1 order by my_infor_datetime desc";
			$information = $my_informationDAO->get($this->dbhelper);
			
			$total = count($information);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($information,null,"renwu",null,'get',12,12);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('shouyilist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("heart/renwu.phtml");
		}
		
		public function renwuinfoAction(){
			$id = HttpUtil::getString("id");
			if($id != ""){
				$my_informationDAO = new my_informationDAO();
				$my_informationDAO ->my_infor_id = $id;
				$info = $my_informationDAO->get($this->dbhelper);
				$this->view->assign("info",$info);
				echo $this->view->render("heart/renwuinfo.phtml");
			}else {
				alert_back("您查看的捐赠故事不存在");
			}
			
		}
		
		public function shouyiAction(){
			$my_informationDAO = new my_informationDAO();
			$my_informationDAO ->my_infor_cateid = "shouyi";
			$my_informationDAO->selectLimit = " and my_infor_isdisplay = 1 and my_infor_state = 1 order by my_infor_datetime desc";
			$information = $my_informationDAO->get($this->dbhelper);
			
			$total = count($information);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($information,null,"shouyi",null,'get',12,12);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('shouyilist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("heart/shouyi.phtml");
		}
		
		public function shouyiinfoAction(){
			$id = HttpUtil::getString("id");
			if($id != ""){
				$my_informationDAO = new my_informationDAO();
				$my_informationDAO ->my_infor_id = $id;
				$info = $my_informationDAO->get($this->dbhelper);
				$this->view->assign("info",$info);
				echo $this->view->render("heart/shouyiinfo.phtml");
			}else {
				alert_back("您查看的捐赠故事不存在");
			}
			
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>