<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/functions.php");
	require_once("../util/sessionutil.php");
	
	class heartController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			echo $this->view->render("heart/index.phtml");
		}
		
		public function renwuAction(){
			echo $this->view->render("heart/renwu.phtml");
		}
		
		public function shouyiAction(){
			echo $this->view->render("heart/shouyi.phtml");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>