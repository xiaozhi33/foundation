<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/functions.php");
	require_once("../util/sessionutil.php");
	
	class contactController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			echo $this->view->render("contact/index.phtml");
		}
		
		public function frilinkAction(){
			echo $this->view->render("contact/frilink.phtml");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>