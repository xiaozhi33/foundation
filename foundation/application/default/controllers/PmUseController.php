<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/functions.php");
	require_once("../util/sessionutil.php");
	
	class pmuseController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$survey_info = new jjh_surveyDAO(5);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("pmuse/index.phtml");
		}
		
		public function usage1Action(){
			$survey_info = new jjh_surveyDAO(6);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("pmuse/usage1.phtml");
		}
		
		public function usage2Action(){
			$survey_info = new jjh_surveyDAO(7);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("pmuse/usage2.phtml");
		}
		
		public function usage3Action(){
			$survey_info = new jjh_surveyDAO(8);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("pmuse/usage3.phtml");
		}
		
		public function usage4Action(){
			$survey_info = new jjh_surveyDAO(9);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("pmuse/usage4.phtml");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>