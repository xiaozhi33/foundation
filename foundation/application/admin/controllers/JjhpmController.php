<?php
	require_once("BaseController.php");
	class Admin_JjhpmController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$my_categoryDAO = new my_categoryDAO();
			$my_categoryDAO ->selectField("c_id,c_name,c_parent_id,c_path,concat(c_path,',',c_id) as new_path");
			$my_categoryDAO ->selectLimit =" order by new_path";
			$my_categoryDAO = $my_categoryDAO ->get($this->dbhelper);
			
			$this->view->assign("catelist",$my_categoryDAO);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("jjh_pm/index.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addsmallcateAction(){
			$c_name = HttpUtil::postInsString("c_name");
			$p_id = HttpUtil::postInsString("p_id");
			$path = HttpUtil::postInsString("new_p");
			if($c_name == ""){
				alert_back("请添加分类名称！");
			}
			
			$my_categoryDAO = new my_categoryDAO();
			$my_categoryDAO ->c_name = $c_name;
			$my_categoryDAO ->c_parent_id = $p_id;
			$my_categoryDAO ->c_path = $path;
			$my_categoryDAO ->save($this->dbhelper);
			
			alert_go("添加成功！","/admin/jjhpm");
		}
		
		public function removenameAction(){
			$c_name = HttpUtil::postInsString("c_name");
			$id = HttpUtil::postInsString("id");
			if($c_name == ""){
				alert_back("请填写分类名称！");
			}
			
			$my_categoryDAO = new my_categoryDAO($id);
			$my_categoryDAO ->c_name = $c_name;
			$my_categoryDAO ->save($this->dbhelper);
			
			alert_go("修改成功！","/admin/jjhpm");
		}
		
		public function editcateAction(){
			$cid = HttpUtil::getString("cid");
			if($cid == ""){
				alert_back("没有这个分类。");
			}
			$my_jjh = new my_categoryDAO($cid);
			$my_jjh = $my_jjh ->get($this->dbhelper);
			$this->view->assign("my_jjh",$my_jjh);
			echo $this->view->render("jjh_pm/editcate.phtml");
		}
		
		public function savecateAction(){
			$cid = HttpUtil::postInsString("cid");
			if($cid == ""){
				alert_back("没有这个分类。");
			}
			$title = HttpUtil::postInsString("title");
			if($title == ""){
				alert_back("请添加分类名称。");
			}
			$describe = HttpUtil::postInsString("describe");
			$content = HttpUtil::postInsString("content");
			$c_online = HttpUtil::postInsString("c_online");
			
			$my_jjh_pm = new my_categoryDAO($cid);
			$my_jjh_pm ->c_name = $title;
			$my_jjh_pm ->c_describe = $describe;
			$my_jjh_pm ->c_content = $content;
			$my_jjh_pm ->c_online = $c_online;
			$my_jjh_pm ->save($this->dbhelper);
			alert_go("修改成功！","/admin/jjhpm");
		}
		
		public function delcateAction(){
			$id = HttpUtil::getString("sid");
			$my_categoryDAO = new my_categoryDAO($id);
			$my_categoryDAO ->del($this->dbhelper);
			alert_go("删除成功！","/admin/jjhpm");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper->connect();
			SessionUtil::sessionStart();
	    	SessionUtil::checkadmin();
		}
	}
?>