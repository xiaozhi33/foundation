<?php
	require_once("BaseController.php");
	class Management_logController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$start = HttpUtil::postString("start");
			$end = HttpUtil::postString("end");
			
			//获取日志
			$loglistinfo = selectlog(null,$start,$end);
			$total = count($loglistinfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($loglistinfo,null,"log",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('loglistinfo',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("log/index.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function slowlogrsAction(){
			$start = HttpUtil::postString("start");
			$end = HttpUtil::postString("end");
			
			//获取日志
			$loglistinfo = selectlog(null,$start,$end);
			$total = count($loglistinfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($loglistinfo,null,"log",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('loglistinfo',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("log/slowlog.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function slowoneAction(){
			$pid = $_REQUEST['id'];
			$loginfo = new my_logDAO($pid);
			$loginfo = $loginfo ->get($this->dbhelper);
			
			$rs = json_decode($loginfo[0]['logMsg'], true);
            $this->view->assign('rs',$rs);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("log/slowone.phtml");
            echo $this->view->render("index/footer.phtml");
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkmanagement();
		}

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'slowlogrs',
                'slowone',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}
?>