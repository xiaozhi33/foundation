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


            // todolist  待办事宜
            $pm_mg_todolistDAO = $this->orm->createDAO("pm_mg_todolist");
            $pm_mg_todolistDAO = $pm_mg_todolistDAO ->get();
            $this->view->assign('todolist', $pm_mg_todolistDAO);


            // 待办事宜提醒
            $pm_mg_todolistDAO = $this->orm->createDAO("pm_mg_todolist");
            $pm_mg_todolistDAO ->selectLimit .= " and date_sub(curdate(), INTERVAL 30 DAY) <= date(`end_time`)";
            $pm_mg_todolistDAO = $pm_mg_todolistDAO ->get();
            $this->view->assign('tixing', $pm_mg_todolistDAO);

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            if($_REQUEST['tongji_type'] == '2'){
                // 资金到账年度统计
                $rs = $this->getChouZiInfo();
                if(!empty($rs)) {
                    $_rs_year = array();
                    foreach($rs as $key => $value){
                        $value['jiner'] = number_format($value['jiner']/10000, 2, '.','');
                        $_rs_year[$value['year']][] = $value;
                    }
                }
                if(!empty($_rs_year)){
                    foreach($_rs_year as $k => $v){
                        foreach($v as $k1 => $v1){
                            if($k1 == count($v) - 1){
                                $_rs_year[$k]['json'] .=  $v1['jiner'];
                            }else {
                                $_rs_year[$k]['json'] .=  $v1['jiner'].",";
                            }
                        }
                    }
                }
                $this->view->assign('rs_year', $_rs_year);

                // 前3年的筹资平均值统计
                if(!empty($_rs_year)){
                    $ii = 1;
                    $_rs_sum = array();
                    foreach($_rs_year as $k => $v){
                        if($ii<4){
                            foreach($v as $k1 => $v1){
                                $_rs_sum[$k1+1] += $v1['jiner'];
                            }
                        }
                        $ii++;
                    }
                }
                if(!empty($_rs_sum)){
                    foreach($_rs_sum as $k => $v){
                        $_rs_avg[$k] = number_format($v/3, 2, '.','');
                    }
                }
            }else {
                // 资金使用年度统计
                $rs = $this->getShiYongInfo();
                if(!empty($rs)) {
                    $_rs_year = array();
                    foreach($rs as $key => $value){
                        $value['jiner'] = number_format($value['jiner']/10000, 2, '.','');
                        $_rs_year[$value['year']][] = $value;
                    }
                }
                if(!empty($_rs_year)){
                    foreach($_rs_year as $k => $v){
                        foreach($v as $k1 => $v1){
                            if($k1 == count($v) - 1){
                                $_rs_year[$k]['json'] .=  $v1['jiner'];
                            }else {
                                $_rs_year[$k]['json'] .=  $v1['jiner'].",";
                            }
                        }
                    }
                }
                $this->view->assign('rs_year', $_rs_year);

                // 前3年的筹资平均值统计
                if(!empty($_rs_year)){
                    $ii = 1;
                    $_rs_sum = array();
                    foreach($_rs_year as $k => $v){
                        if($ii<4){
                            foreach($v as $k1 => $v1){
                                $_rs_sum[$k1+1] += $v1['jiner'];
                            }
                        }
                        $ii++;
                    }
                }
                if(!empty($_rs_sum)){
                    foreach($_rs_sum as $k => $v){
                        $_rs_avg[$k] = number_format($v/3, 2, '.','');
                    }
                }
            }
            $this->view->assign("tongji_type",$_REQUEST['tongji_type']);
            $this->view->assign('rs_avg', $_rs_avg);
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //项目年度统计笔数和项目数

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            $this->view->assign('chouzilist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render('index/index.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function loginviewAction(){
            if(!empty($this->admininfo['admin_info']['id'])){
                header("location:".__BASEURL__."/management/index");
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
                echo("location.href='/management/index/loginview';");
                echo('</script>');
                exit;
            }
			
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

        public function getChouZiInfo(){
            $selectSQL = "SELECT
                            DATE_FORMAT(
                                t.shiyong_zhichu_datetime,
                                '%Y-%m'
                            )yearmonth,
                            SUM(t.shiyong_zhichu_jiner)jiner ,
                            DATE_FORMAT(
                                t.shiyong_zhichu_datetime,
                                '%y'
                            ) year,
                          DATE_FORMAT(
                                t.shiyong_zhichu_datetime,
                                '%m'
                            ) month
                        FROM
                            pm_mg_info t
                        WHERE

                            DATE_FORMAT(
                                t.shiyong_zhichu_datetime,
                                '%Y-%m'
                            )> DATE_FORMAT(
                                date_sub(curdate(), INTERVAL 48 MONTH),
                                '%Y-%m'
                            )
                        GROUP BY
                            yearmonth";
            $rss = $this->dbhelper->fetchAllData($selectSQL);
            return $rss;
        }

        public function getShiYongInfo(){
            $selectSQL = "SELECT
                            DATE_FORMAT(
                                t.zijin_daozhang_datetime,
                                '%Y-%m'
                            )yearmonth,
                            SUM(t.zijin_daozheng_jiner)jiner ,
                            DATE_FORMAT(
                                t.zijin_daozhang_datetime,
                                '%y'
                            ) year,
                          DATE_FORMAT(
                                t.zijin_daozhang_datetime,
                                '%m'
                            ) month
                        FROM
                            pm_mg_info t
                        WHERE

                            DATE_FORMAT(
                                t.zijin_daozhang_datetime,
                                '%Y-%m'
                            )> DATE_FORMAT(
                                date_sub(curdate(), INTERVAL 48 MONTH),
                                '%Y-%m'
                            )
                        GROUP BY
                            yearmonth";
            $rss = $this->dbhelper->fetchAllData($selectSQL);
            return $rss;
        }
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
		}

        /**
         * 修改todolist状态
         */
        public function savetodolistAction(){
            $pm_mg_todolistDAO = $this->orm->createDAO('pm_mg_todolist');
            $pm_mg_todolistDAO ->findId($_REQUEST["id"]);
            $pm_mg_todolistDAO ->status = $_REQUEST["status"]==0?1:0;
            $pm_mg_todolistDAO->save();
            exit();
        }
	}
?>