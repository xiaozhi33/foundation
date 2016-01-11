<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/functions.php");
	require_once("../util/sessionutil.php");
	
	class newsController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$cid = HttpUtil::getString("cid");
			$information = new my_informationDAO(null,$cid);
			$information ->selectLimit = " and my_infor_isdisplay = 1 and my_infor_state = 1";
			$information = $information ->get($this->dbhelper);
			
			$total = count($information);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($information,null,"index",null,'get',14,14);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('informationlist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			if($cid == "gonggao" || $cid == ""){
				$c_name = "基金会公告";
			}elseif ($cid == "zizhuxinxi"){
				$c_name = "资助信息";
			}elseif ($cid == "xinwen"){
				$c_name = "基金会新闻";
			}elseif ($cid == "gongshi"){
				$c_name = "捐赠公示";
			}		
			$this->view->assign('c_name',$c_name);
			echo $this->view->render("news/index.phtml");
		}
		
		public function newsinfoAction(){
			$id = HttpUtil::getString("id");
			if($id == ""){
				alert_back("没有这篇资讯。");
			}
			
			$newsinfo = new my_informationDAO($id);
			$newsinfo = $newsinfo ->get($this->dbhelper);
			$this->view->assign("newsinfo",$newsinfo);
			//echo $newsinfo[0]['my_infor_cateid'];
			if($newsinfo[0]['my_infor_cateid'] == "gonggao"){
				$c_name = "基金会公告";
			}elseif ($newsinfo[0]['my_infor_cateid'] == "zizhuxinxi"){
				$c_name = "资助信息";
			}elseif ($newsinfo[0]['my_infor_cateid'] == "xinwen"){
				$c_name = "基金会新闻";
			}elseif ($newsinfo[0]['my_infor_cateid'] == "gongshi"){
				$c_name = "捐赠公示";
			}		

			$this->view->assign('c_name',$c_name);
			echo $this->view->render("news/newsinfo.phtml");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
		
	}
?>