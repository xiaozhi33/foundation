<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/sessionutil.php");

	error_reporting(E_ERROR);
	
	class newsController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			try{
				$cid = HttpUtil::getString("cid");
				$information = new my_informationDAO(null,$cid);
				$information ->selectLimit = " and my_infor_isdisplay = 1 and my_infor_state = 1 order by my_infor_datetime desc";
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
			}catch(Exception $e){
				throw $e;
			}
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
		
		public function indexinfoAction(){

			// 防止post xss跨站注入
			if(!empty($_POST)){
				@header("http/1.1 404 not found");
				@header("status: 404 not found");
				include("http://www.phpernote.com/404.html");//跳转到某一个页面，推荐使用这种方法
				exit();
			}

			if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
				@header("http/1.1 404 not found");
				@header("status: 404 not found");
				include("http://www.phpernote.com/404.html");//跳转到某一个页面，推荐使用这种方法
				exit();
			}
			// 防止post xss跨站注入

			$indexinfo = new pm_mg_infoDAO();
			$c_name = HttpUtil::getString("c_name");
			$pname = HttpUtil::getString("pname");

			if($c_name =="zijin"){
				if($pname != ""){
					$indexinfo ->selectLimit .= " and pm_pp like '%". $pname ."%'";
				}
				
				$indexinfo ->selectLimit .= " and is_renling=1 and zijin_daozheng_jiner >= 0 order by zijin_daozhang_datetime desc";
			}elseif($c_name=="shiyong"){
				$indexinfo ->selectLimit = " and is_renling=1 and shiyong_zhichu_jiner >= 0 order by shiyong_zhichu_datetime desc";
			}
			//$indexinfo ->debugSql = true;
			$indexinfo = $indexinfo ->get($this->dbhelper);
			
			$total = count($indexinfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($indexinfo,null,"indexinfo",null,'get',14,14);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('informationlist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);		
			echo $this->view->render("news/indexinfo.phtml");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
		
	}
?>