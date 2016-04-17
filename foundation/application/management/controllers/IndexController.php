<?php
	require_once("BaseController.php");
	class Management_indexController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			SessionUtil::checkmanagement();

            // 系统信息 - 磁盘占用空间数
            $free_df = disk_free_space("/");
            $total_df = disk_total_space("/");
            $free_df = $this->byte_format($free_df);
            $total_df = $this->byte_format($total_df);

            $this->view->assign("free_df",disk_free_space("/"));
            $this->view->assign("free",$free_df);
            $this->view->assign("total_df",disk_total_space("/"));
            $this->view->assign("total",$total_df);

            // 项目进度
            $name = HttpUtil::postString("pname");
            $rate = HttpUtil::postString("pm_rate");

            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
            $chouziDAO ->withPm_mg_rate(array("id" => "pm_id"));
            $like_sql = "";
            if($name != "")
            {
                $like_sql .= " AND pm_mg_chouzi.pname like '%".$name."%'";
            }
            if($rate != "")
            {
                $like_sql .= " AND pm_mg_rate.pm_rate like '%".$rate."%'";
            }
            $like_sql .= " order by id desc";
            $chouziDAO->selectLimit = $like_sql;
            $chouziDAO = $chouziDAO ->get();

            $total = count($chouziDAO);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($chouziDAO, null, "/management/index/index", null, 'get', 7, 5);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);
            $this->view->assign('chouzilist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render('index/index.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function loginviewAction(){
			$returnURL = HttpUtil::getString('returnURL');
			$this->view->assign("returnURL",$returnURL);
			echo $this->view->render('index/loginview.phtml');	
		}
		
		public function loginAction(){
			$username = HttpUtil::postString('user_name');
			$password = HttpUtil::postString('user_password');
			
			//判定用户名密码的正确性			
			if(!$passwordpost = $this->getpasswordpostAction($username,$password)){
				//alert_go('您输入的密码有误！','/management/index/loginview');
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('您输入的密码有误');");
                echo("location.href='/management/index/loginview';");
                echo('</script>');
                exit;
			}else{
				SessionUtil::initSession($passwordpost);

				if ($_REQUEST['returnURL']!=''){
                	$returnURL = HttpUtil::valueString($_REQUEST['returnURL']);
                	$returnURL = base64_decode($returnURL);
            		header("location:" .$returnURL);
            		exit();
            	}
            	
            	if(HttpUtil::getString("returnURL")){
            		$returnURL = base64_decode(HttpUtil::postString("returnURL"));
            		header('$returnURL');
            	}
            	
            	//跳转到个人主页（管理）
            	header("location:".__BASEURL__."/management/index");
			}
		}
		
		public function logoutAction(){
	        try{
	           	SessionUtil::sessionEnd();
	            header("location:".__BASEURL__."/management/index/loginview");
	        }catch (Exception $e){
	            echo $e->getMessage();
	            exit;
	        }
   	    }

		//getpasswordpost方法判定用户名密码的正确性
		public function getpasswordpostAction($username,$password){
			$my_adminDAO = new my_adminDAO();
			$my_adminDAO ->admin_name = $username;
			$my_adminDAO ->admin_pwd = substr(md5(serialize($password)), 0, 32);
			//$my_adminDAO ->admin_password = substr(md5(md5($password)."wangnan-mycms-ok100"),0,12);
			$admininfo = $my_adminDAO->get($this->dbhelper);
						
			if($admininfo){
				return $admininfo;
			}else{
				return false;
			}
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
		}
	}
?>