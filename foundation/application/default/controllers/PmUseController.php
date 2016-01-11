<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/functions.php");
	require_once("../util/sessionutil.php");
	
	class pmuseController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			echo $this->view->render("pmuse/index.phtml");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>