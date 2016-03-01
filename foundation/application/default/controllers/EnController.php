<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/sessionutil.php");
	
	class enController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			echo $this->view->render("en/index.phtml");
		}
		
		public function aboutAction(){
			echo $this->view->render("en/about.phtml");
		}
		
		public function contactAction(){
			echo $this->view->render("en/contact.phtml");
		}
		
		public function donateAction(){
			echo $this->view->render("en/donate.phtml");
		}
		
		public function projectsAction(){
			echo $this->view->render("en/projects.phtml");
		}
		
		public function taxAction(){
			echo $this->view->render("en/tax.phtml");
		}
		
		public function recognitionAction(){
			echo $this->view->render("en/recognition.phtml");	
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>