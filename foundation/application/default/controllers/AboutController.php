<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/functions.php");
	require_once("../util/sessionutil.php");
	
	class AboutController extends BaseController{
		private $dbhelper;
		public function indexAction(){
			echo $this->view->render("about/index.phtml");
		}
		
		public function lingdaoticiAction(){
			echo $this->view->render("about/lingdaotici.phtml");
		}
		
		public function zhangchengAction(){
			echo $this->view->render("about/zhangcheng.phtml");
		}
		
		public function zuzhiAction(){
			echo $this->view->render("about/zuzhi.phtml");
		}
		
		public function baogaoAction(){
			$information_list = new my_informationDAO(null,"baogao");
			$information_list ->selectLimit = " and my_infor_isdisplay=1 and my_infor_state=1 order by my_infor_id desc";
			$information_list = $information_list->get($this->dbhelper);
			$this->view->assign("informationlist",$information_list);
			echo $this->view->render("about/baogao.phtml");
		}

		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>