<?php
	require_once("BaseController.php");
	class Admin_juanzengController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$juanzenglist = new jjh_juanzeng_infoDAO();
			if(HttpUtil::postString("keyword")!=""){
				$keyword = HttpUtil::postString("keyword");
				$juanzenglist->selectLimit = " and jjh_juanzeng_danwei like '%$keyword%'";
			}
			$juanzenglist->selectLimit = " order by jjh_juanzeng_datetime desc";
			$juanzenglist = $juanzenglist->get($this->dbhelper);
			
			$total = count($juanzenglist);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($juanzenglist,null,"index",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('juanzenglist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('juanzeng/index.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addjuanzengAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('juanzeng/addjuanzeng.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrsAction(){
			$name = HttpUtil::postInsString("name");
			$jiner = HttpUtil::postInsString("jiner");
			$pm = HttpUtil::postInsString("pm");
			$riqi = HttpUtil::postInsString("riqi");
			
			if($name == "" || $jiner == "" || $pm == "" || $riqi == ""){
				alert_back("您输入的资料不全。");
			}
			
			$jjh_juanzeng = new jjh_juanzeng_infoDAO();
			$jjh_juanzeng ->jjh_juanzeng_danwei = $name;
			$jjh_juanzeng ->jjh_juanzeng_datetime = $riqi;
			$jjh_juanzeng ->jjh_juanzeng_pm = $pm;
			$jjh_juanzeng ->jjh_juanzeng_jiner = $jiner;
			$jjh_juanzeng ->save($this->dbhelper);
			alert_go("添加成功","/admin/juanzeng/index");
		}
		
		public function editjuanzengAction(){
			$id = HttpUtil::getString("id");
			if( $id == ""){
				alert_back("操作失败");
			}

			$jjh_juanzeng_info = new jjh_juanzeng_infoDAO($id);
			$jjh_juanzeng_info = $jjh_juanzeng_info ->get($this->dbhelper);
			
			$this->view->assign('jjh_juanzeng_info',$jjh_juanzeng_info);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('juanzeng/editjuanzeng.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function editrsAction(){
			$id = HttpUtil::postInsString("id");
			if( $id == ""){
				alert_back("操作失败");
			}
			$name = HttpUtil::postInsString("name");
			$jiner = HttpUtil::postInsString("jiner");
			$pm = HttpUtil::postInsString("pm");
			$riqi = HttpUtil::postInsString("riqi");
			
			if($name == "" || $jiner == "" || $pm == "" || $riqi == ""){
				alert_back("您输入的资料不全。");
			}
			
			$jjh_juanzeng = new jjh_juanzeng_infoDAO($id);
			$jjh_juanzeng ->jjh_juanzeng_danwei = $name;
			$jjh_juanzeng ->jjh_juanzeng_datetime = $riqi;
			$jjh_juanzeng ->jjh_juanzeng_pm = $pm;
			$jjh_juanzeng ->jjh_juanzeng_jiner = $jiner;
			$jjh_juanzeng ->save($this->dbhelper);
			
			alert_go("修改成功","/admin/juanzeng/index");
		}
		
		
		public function deljuanzengAction(){
			$id = HttpUtil::getString("id");
			if( $id == ""){
				alert_back("操作失败");
			}
			$jjh_juanzeng = new jjh_juanzeng_infoDAO($id);
			$jjh_juanzeng ->del($this->dbhelper);
			alert_go("删除成功","/admin/juanzeng/index");
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkadmin();
		}
	}
?>