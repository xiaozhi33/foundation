<?php
	require_once("BaseController.php");
	class Management_ConfigController extends BaseController {
		private $dbhelper;
		
		public function indexAction(){
			$configDAO = new woow_configDAO();
			$configDAO->selectLimit = "order by id asc";
			$config = $configDAO->get($this->dbhelper);
			
			$this->view->assign('config',$config);
			$this->view->display("config/index.html");
		}
		
		public function modifyAction(){
			if($_POST){				
				if(!empty($_POST)){
					foreach ($_POST as $key=>$val){
						$configDAO = new woow_configDAO($key);
						$configDAO->value = $val;						
						$configDAO->save($this->dbhelper);
					}
				}
				$this->_redirect("/admin/config");
			}else{
				$this->_redirect("/admin/config");
			}
		}
		
		//日志审核
		public function rizhiAction(){
			$woow_blogDAO = new woow_blogDAO();
			/**
			 * 搜索功能
			 */
			$state = HttpUtil::getString("state");
			$blog_title = HttpUtil::getString("blog_title");
			if($state != ""){
				$woow_blogDAO ->blog_state = $state;
			}
			if($blog_title != ""){
				$woow_blogDAO ->blog_title = $blog_title;
			}
			$woow_blogDAO ->selectLimit = " order by dateline desc";
			$woow_blogDAO = $woow_blogDAO ->get($this->dbhelper);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($woow_blogDAO,null,"rizhi",null,'get',20,20);
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);
			
			$this->view->assign('rizhilist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);
			$this->view->display("config/rizhi.html");
		}
		
		//日志详细
		public function viewrizhiAction(){
			$id = HttpUtil::getString("id");
			$woow_blogDAO = new woow_blogDAO();
			$woow_blogDAO ->blogid = $id;
			$woow_blogDAO = $woow_blogDAO ->get($this->dbhelper);
			
			$this->view->assign("type_options",array(0=>'未审核',1=>'已审核'));
			$this->view->assign("info",$woow_blogDAO);
			$this->view->display("config/viewrizhi.html");
		}
		
		//修改日志状态
		public function editrizhiAction(){
			$id = HttpUtil::postString("id");
			$woow_blogDAO = new woow_blogDAO($id);
			$woow_blogDAO ->blog_state = 1;
			$woow_blogDAO = $woow_blogDAO ->save($this->dbhelper);
			alert_back("修改成功");
		}
		
		//删除日志
		public function delrizhiAction(){
			$id = HttpUtil::getString("id");
			$woow_blogDAO = new woow_blogDAO($id);
			$woow_blogDAO ->del($this->dbhelper);
			alert_go("删除成功","/admin/config/rizhi");
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
		}

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'index',
                'modify',
                'viewrizhi',
                'editrizhi',
                'delrizhi',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}
?>