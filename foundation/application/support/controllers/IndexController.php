<?php
	require_once("BaseController.php");
	class Support_indexController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			SessionUtil::checkSupport();

			// 有待完成的立项申请
			$_support_projectDAO = $this->orm->createDAO('_support_project');
			$_support_projectDAO ->findUid($this->admininfo['admin_info']['id'])->order(' lastmodify DESC ');
			$_support_projectDAO ->selectLimit .= ' AND status!=8';
			$_support_projectDAO = $_support_projectDAO ->get();
			$this->view->assign("project_list",$_support_projectDAO);

			// 有待完成的资金使用申请
			$_support_expenditureDAO = $this->orm->createDAO('_support_expenditure');
			$_support_expenditureDAO ->findUid($this->admininfo['admin_info']['id'])->order(' lastmodify DESC ');
			$_support_expenditureDAO ->selectLimit .= ' AND status!=8';
			$_support_expenditureDAO = $_support_expenditureDAO ->get();
			$this->view->assign("expenditure_list",$_support_expenditureDAO);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render('index/index.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function loginviewAction(){
            if(!empty(SessionUtil::getAdmininfo())){
                header("location:".__BASEURL__."/support/index");
            }
			$returnURL = HttpUtil::getString('returnURL');
			$this->view->assign("returnURL",$returnURL);
			echo $this->view->render('index/loginview.phtml');	
		}
		
		public function loginAction(){
			$username = HttpUtil::postString('user_name');
			$password = HttpUtil::postString('user_password');

            if($_POST['code'] != $_SESSION['code']){
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('您输入的验证码有误');");
                echo("location.href='/support/index/loginview';");
                echo('</script>');
                exit;
            }
			
			//判定用户名密码的正确性			
			if(!$passwordpost = $this->getpasswordpostAction($username,$password)){
				//alert_go('您输入的密码有误！','/management/index/loginview');
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('您的账户名或密码有误');");
                echo("location.href='/support/index/loginview';");
                echo('</script>');
                exit;
			}else{
				SessionUtil::initSupportSession($passwordpost, true);

                // 记录登录logo
                $loginDAO = $this->orm->createDAO('_support_college_user_login_log');
                $admininfo_array = SessionUtil::getAdmininfo();
                $loginDAO ->uid = $admininfo_array['admin_info']['id'];
                $loginDAO ->name = $admininfo_array['admin_info']['username'];
                $loginDAO ->loginip = $this->GetIP();
                $loginDAO ->logintime = date('Y-m-d H:i:s',time());
                $loginDAO ->save();

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
            	header("location:".__BASEURL__."/support/index");
			}
		}
		
		public function logoutAction(){
	        try{
                $_SESSION = array();
	           	SessionUtil::sessionEnd();
	            header("location:".__BASEURL__."/support/index/loginview");
	        }catch (Exception $e){
	            echo $e->getMessage();
	            exit;
	        }
   	    }

		//getpasswordpost方法判定用户名密码的正确性
		public function getpasswordpostAction($username,$password)
		{
			$_support_college_userDAO = $this->orm->createDAO('_support_college_user');
			$_support_college_userDAO ->findUsername($username);
			$_support_college_userDAO ->findPassword(substr(md5(serialize($password)), 0, 32));
			$admininfo = $_support_college_userDAO->get();

			if($admininfo){
				return $admininfo;
			}else{
				return false;
			}
		}

        //权限
        public function acl()
        {
            // 不需要权限检查直接返回
            return;
        }

		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
		}
	}
?>