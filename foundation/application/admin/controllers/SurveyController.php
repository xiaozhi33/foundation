<?php
	require_once("BaseController.php");
	class Admin_surveyController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			if(HttpUtil::getString("name") != ""){
				$name = HttpUtil::getString("name");
				$rs = $this->selectinfoByName($name);
				$name1 = HttpUtil::getString("name1");
				$name2 = HttpUtil::getString("name2");
				
				$this->view->assign("name1",$name1);
				$this->view->assign("name2",$name2);
				$this->view->assign("survey",$rs);

				echo $this->view->render("index/header.phtml");
				echo $this->view->render('survey/index.phtml');
				echo $this->view->render("index/footer.phtml");
			}else {
				alert_back("操作失败");	
			}
		}
		
		public function editsurveyAction(){
			if($_REQUEST['id'] != ""){
				$content = $_REQUEST['content'];
				$id = $_REQUEST['id'];
				$jjh_surveyDAO = new jjh_surveyDAO($id);
				$jjh_surveyDAO ->content = $content;
				$jjh_surveyDAO ->save($this->dbhelper);
				alert_back("信息修改成功");
			}else {
				alert_back("修改信息失败，请选择要修改内容");
			}
		}
		
		//根据名称取信息全部
		public function selectinfoByName($name){
			$jjh_surveyDAO = new jjh_surveyDAO();
			$jjh_surveyDAO ->name = $name;
			$jjh_surveyDAO = $jjh_surveyDAO->get($this->dbhelper);
			
			if (count($jjh_surveyDAO) != ""){
				return $jjh_surveyDAO;
			}else {
				return false;
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