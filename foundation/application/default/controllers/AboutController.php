<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/sessionutil.php");
	
	class AboutController extends BaseController{
		private $dbhelper;
		public function indexAction(){
			$survey_info = new jjh_surveyDAO(1);
			$survey_info = $survey_info ->get($this->dbhelper);
			
			if($_GET['true'] == 1){
				$this->view->assign("true",1);
				$this->_redirect("/about/baogao?true=1");
				exit;
			}
			
			$this->view->assign("info",$survey_info);
			echo $this->view->render("about/index.phtml");
		}
		
		public function lingdaoticiAction(){
			$survey_info = new jjh_surveyDAO(2);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("about/lingdaotici.phtml");
		}
		
		public function zhangchengAction(){
			$survey_info = new jjh_surveyDAO(3);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("about/zhangcheng.phtml");
		}
		
		public function zuzhiAction(){
			$survey_info = new jjh_surveyDAO(4);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("about/zuzhi.phtml");
		}
		
		public function baogaoAction(){
			if($_GET['true'] == 1){
				$this->view->assign("true",1);
			}
			$information_list = new my_informationDAO(null,"baogao");
			$information_list ->selectLimit = " and my_infor_isdisplay=1 and my_infor_state=1 order by my_infor_id desc";
			$information_list = $information_list->get($this->dbhelper);
			$this->view->assign("informationlist",$information_list);
			echo $this->view->render("about/baogao.phtml");
		}
		
		public function caiwushenjiAction(){
			if($_GET['true'] == 1){
				$this->view->assign("true",1);
			}
			$information_list = new my_informationDAO(null,"caiwushenji");
			$information_list ->selectLimit = " and my_infor_isdisplay=1 and my_infor_state=1 order by my_infor_id desc";
			$information_list = $information_list->get($this->dbhelper);
			$this->view->assign("informationlist",$information_list);
			echo $this->view->render("about/caiwushenji.phtml");
		}
		
		public function guanlizhiduAction(){
			if($_GET['true'] == 1){
				$this->view->assign("true",1);
			}
			$information_list = new my_informationDAO(null,"guanlizhidu");
			$information_list ->selectLimit = " and my_infor_isdisplay=1 and my_infor_state=1 order by my_infor_id desc";
			$information_list = $information_list->get($this->dbhelper);
			$this->view->assign("informationlist",$information_list);
			echo $this->view->render("about/guanlizhidu.phtml");
		}

		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>