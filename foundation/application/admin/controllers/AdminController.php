<?php
	require_once("BaseController.php");
	class Admin_adminController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$adminlist = new my_adminDAO();
			$adminlist = $adminlist ->get($this->dbhelper);
			$this->view->assign("adminlist",$adminlist);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('admin/index.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		//判断该用户是否存在
		public function isadminAction($name){
			$adminlist = new my_adminDAO();
			$adminlist ->admin_name = $name;
			$isadmin = $adminlist->get($this->dbhelper);
			return $isadmin;
		}
		
		public function nodelAction(){
			if($_REQUEST['id'] != ""){
				$adminlist = new my_adminDAO($_REQUEST['id']);
				$adminlist->del($this->dbhelper);
				alert_go("删除成功。","/admin/admin");
			}else{
				alert_back("删除失败");
			}
		}
		
		//联系我们
		public function websitAction(){
			$my_websit_info = new my_website_infoDAO();
			$my_websit_info = $my_websit_info ->get($this->dbhelper);
			$this->view->assign("info",$my_websit_info);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('admin/websit.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		
		public function editrslianxifangshiAction(){
			$my_websit_info = new my_website_infoDAO($_REQUEST['web_name']);
			$my_websit_info ->web_address = $_REQUEST['web_address'];
			$my_websit_info ->web_email = $_REQUEST['web_email'];
			$my_websit_info ->web_fax = $_REQUEST['web_fax'];
			$my_websit_info ->web_recall = $_REQUEST['web_recall'];
			$my_websit_info ->web_tel = $_REQUEST['web_tel'];
			$my_websit_info ->web_url = $_REQUEST['web_url'];
			$my_websit_info ->web_weibo = $_REQUEST['web_weibo'];
			
			$my_websit_info ->web_copyright = $_REQUEST['web_copyright'];
			$my_websit_info ->web_name = $_REQUEST['web_name'];
			$my_websit_info ->save($this->dbhelper);
			alert_back("修改成功");
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkadmin();
		}
	}
?>
