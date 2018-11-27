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
            $like_sql  .= " AND pm_mg_chouzi.is_del != 1";
            $like_sql .= " order by id desc";
            $chouziDAO->selectLimit = $like_sql;
            $chouziDAO = $chouziDAO ->get();

            $total = count($chouziDAO);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($chouziDAO, null, "/management/index/index", null, 'get', 7, 5);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);

            // 今年新建立项目
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
            $chouziDAO ->selectLimit .= " AND create_time >= '".date("Y",time())."-01-01'";
            $chouziDAO ->selectLimit .= " AND is_del=0";
            $chouziDAO = $chouziDAO ->get();

            $create_p_count = count($chouziDAO);

            // todolist  待办事宜
            /*$pm_mg_todolistDAO = $this->orm->createDAO("pm_mg_todolist");
            $pm_mg_todolistDAO = $pm_mg_todolistDAO ->get();
            $this->view->assign('todolist', $pm_mg_todolistDAO);*/

            // 待办事宜提醒
            /*$pm_mg_todolistDAO = $this->orm->createDAO("pm_mg_todolist");
            $pm_mg_todolistDAO ->selectLimit .= " and date_sub(curdate(), INTERVAL 30 DAY) <= date(`end_time`)";
            $pm_mg_todolistDAO = $pm_mg_todolistDAO ->get();
            $this->view->assign('tixing', $pm_mg_todolistDAO);*/

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /*$year_month_array = array(0,0,0,0,0,0,0,0,0,0,0,0);  //12个月初始化

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
                            $_month = (int)$v1['month'] - 1;
                            $year_month_array[$_month] = $v1['jiner'];
                            $_rs_year[$k]['json'] = implode(',',$year_month_array);
                        }
                        $year_month_array = array(0,0,0,0,0,0,0,0,0,0,0,0);
                    }
                }
                $this->view->assign('rs_year', $_rs_year);

                // 前3年的筹资平均值统计
                if(!empty($_rs_year)){
                    $ii = 1;
                    $_rs_sum = array();
                    foreach($_rs_year as $k => $v){
                        if($ii<4){
                            $v['json'] = explode(',',$v['json']);
                            foreach($v['json'] as $k1 => $v1){
                                $_rs_sum[$k1+1] += $v1;
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
                            $_month = (int)$v1['month'] - 1;
                            $year_month_array[$_month] = $v1['jiner'];
                            $_rs_year[$k]['json'] = implode(',',$year_month_array);
                        }
                        $year_month_array = array(0,0,0,0,0,0,0,0,0,0,0,0);
                    }
                }
                $this->view->assign('rs_year', $_rs_year);
                //var_dump($_rs_year);exit;

                // 前3年的筹资平均值统计
                if(!empty($_rs_year)){
                    $ii = 1;
                    $_rs_sum = array();
                    foreach($_rs_year as $k => $v){
                        if($ii<4){
                            $v['json'] = explode(',',$v['json']);
                            foreach($v['json'] as $k1 => $v1){
                                $_rs_sum[$k1+1] += $v1;
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
            //var_dump($_rs_year);exit();
            $this->view->assign("tongji_type",$_REQUEST['tongji_type']);
            $this->view->assign('rs_avg', $_rs_avg);*/
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //项目年度统计笔数和项目数

            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            /**
             * 年度捐赠收入/公益支出情况
             * @params $year 统计年度
             */
            if(!empty($_REQUEST['year'])){$years = $_REQUEST['year'];}else{$years = date("Y",time());}; //默认为当年统计

            $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
            $pm_mg_infoDAO ->withPm_mg_chouzi(array("pm_name"=>"pname"));
            $pm_mg_infoDAO ->select("pm_mg_info.*, pm_mg_chouzi.cate, pm_mg_chouzi.department");
            $pm_mg_infoDAO ->selectLimit .= " AND pm_mg_chouzi.is_del = 0";         // 未删除项目
            $pm_mg_infoDAO ->selectLimit .= " AND pm_mg_info.is_refund = 0";        // 不是退款的来款
            $pm_mg_infoDAO ->selectLimit .= " AND pm_mg_info.is_renling = 1";       // 已经认领的
            $pm_mg_infoDAO ->selectLimit .= " AND ((pm_mg_info.zijin_daozhang_datetime >= '".$years."-01-01'"." AND pm_mg_info.zijin_daozhang_datetime <= '".$years."-12-31'".") OR (pm_mg_info.shiyong_zhichu_datetime > '".$years."-01-01'"." AND pm_mg_info.shiyong_zhichu_datetime <  '".$years."-12-31'"."))";       // 时间段筛选
            $pm_mg_infoDAO = $pm_mg_infoDAO->get();

            $jjh_mg_cateDAO = $this->orm->createDAO("jjh_mg_cate")->get();

            //捐赠类型分析 - 初始化
            foreach($jjh_mg_cateDAO as $k => $v){
                $income_cate_array[$k+1]['id'] = $v['id'];
                $income_cate_array[$k+1]['name'] = $v['catename'];
                $income_cate_array[$k+1]['counts'] = 0;
                $income_cate_array[$k+1]['income'] = 0;
            }

            //捐赠来源分析
            $income_sour_array = array();
            $income_sour_incomes = 0;

            //捐赠者类型分析
            $income_ppcate_array = array();

            //校友个人及企业捐赠情况
            $income_pp_array = array();

            //各学院、部处捐赠情况
            $income_department_array = array();
            $income_department_incomes = 0;
            $income_department_pcounts = 0;

            //公益支出情况
            $pay_array = array();
            foreach($jjh_mg_cateDAO as $k => $v){
                $pay_array[$k+1]['id'] = $v['id'];
                $pay_array[$k+1]['name'] = $v['catename'];
                $pay_array[$k+1]['pcounts'] = 0;
                $pay_array[$k+1]['counts'] = 0;
                $pay_array[$k+1]['income'] = 0;
            }

            if(!empty($pm_mg_infoDAO)){
                foreach($pm_mg_infoDAO as $key => $value){
                    if($value['cate_id'] == 0){
                        //捐赠类型分析
                        if(!in_array($value["pm_name"], $income_cate_array[$value['cate']]['pname'])){
                            $income_cate_array[$value['cate']]['pname'][] = $value["pm_name"];
                            $income_cate_array[$value['cate']]['counts'] += 1;
                        }
                        $income_cate_array[$value['cate']]['income'] += $value['zijin_daozheng_jiner'];
                        $income_cate_array['incomes'] += $value['zijin_daozheng_jiner'];

                        //捐赠来源分析
                        $income_sour_array[$value["pm_pp_cate"]]['counts'] += 1;
                        $income_sour_array[$value["pm_pp_cate"]]['income'] += $value['zijin_daozheng_jiner'];
                        $income_sour_incomes += $value['zijin_daozheng_jiner'];

                        //校友个人及企业捐赠情况
                        $income_pp_array[$value['pm_is_school']]['counts'] += 1;      // 捐赠项目笔数
                        $income_pp_array[$value['pm_is_school']]['income'] += $value['zijin_daozheng_jiner'];
                        $income_pp_array['incomes'] += $value['zijin_daozheng_jiner'];

                        //各学院、部处捐赠情况
                        if(!in_array($value["pm_name"], $income_department_array[$value['department']]['pname'])){
                            $income_department_array[$value['department']]['pname'][] = $value["pm_name"];
                            $income_department_array[$value['department']]['counts'] += 1;
                        }
                        $income_department_array[$value['department']]['pcounts'] += 1;
                        $income_department_array[$value['department']]['income'] += $value['zijin_daozheng_jiner'];
                        //超过10万金额、超过10笔数
                        if($value['zijin_daozheng_jiner'] >= 100000){
                            $income_department_array[$value['department']]['max_income'] += $value['zijin_daozheng_jiner'];
                            $income_department_array[$value['department']]['max_income_count'] += 1;
                        }
                        $income_department_pcounts += 1;
                        $income_department_incomes += $value['zijin_daozheng_jiner'];
                    }elseif($value['cate_id'] == 1){
                        //公益支出情况
                        if(!in_array($value["pm_name"], $pay_array[$value['cate']]['pname'])){
                            $pay_array[$value['cate']]['pname'][] = $value["pm_name"];
                            $pay_array[$value['cate']]['counts'] += 1;
                        }
                        $pay_array[$value['cate']]['pay'] += $value['shiyong_zhichu_jiner'];
                        $pay_array[$value['cate']]['jiangli_renshu'] += $value['jiangli_renshu'];
                        $pay_array['pays'] += $value['shiyong_zhichu_jiner'];
                    }
                }
            }

            //当月来款总数
            $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
            $pm_mg_infoDAO ->selectLimit .= " AND cate_id=0 AND is_renling=1";
            $pm_mg_infoDAO ->selectLimit .= " AND (zijin_daozhang_datetime >= '".date('Y-m-01', strtotime('0 month'))."' AND zijin_daozhang_datetime<='".date('Y-m-t', strtotime('0 month'))."')";
            $pm_mg_infoDAO ->selectLimit;
            $pm_mg_infoDAO = $pm_mg_infoDAO ->get();
            $month_income_count = count($pm_mg_infoDAO);

            //配比项目总数
            $pm_mg_peibiDAO = $this->orm->createDAO("pm_mg_peibi");
            $pm_mg_peibiDAO ->select(" id, pm_name");
            $pm_mg_peibiDAO ->selectLimit .= " group by pm_name";
            $pm_mg_peibiDAO = $pm_mg_peibiDAO->get();

            //申请金额
            $_pm_mg_peibiDAO = $this->orm->createDAO("pm_mg_peibi");
            $_pm_mg_peibiDAO ->withPm_mg_info(array("lk_main_id" => "id"));
            $_pm_mg_peibiDAO ->select(" sum(je) as sum_je");
            $_pm_mg_peibiDAO ->selectLimit .= " AND pm_mg_peibi.is_pass=1 AND pm_mg_info.cate_id=0 AND pm_mg_info.is_renling=1 ";
            $_pm_mg_peibiDAO = $_pm_mg_peibiDAO->get();
            $sum_je = $_pm_mg_peibiDAO[0]["sum_je"];
            $this->view->assign('sum_je', $sum_je);

            //审批金额
            $_pm_mg_peibiDAO = $this->orm->createDAO("pm_mg_peibi");
            $_pm_mg_peibiDAO ->withPm_mg_info(array("lk_main_id" => "id"));
            $_pm_mg_peibiDAO ->select(" distinct lk_main_id, pm_mg_info.zijin_daozheng_jiner");
            $_pm_mg_peibiDAO ->selectLimit .= " AND pm_mg_peibi.is_pass=1 AND pm_mg_info.cate_id=0 AND pm_mg_info.is_renling=1 ";
            $_pm_mg_peibiDAO = $_pm_mg_peibiDAO->get();

            $sq_je =0;
            foreach ($_pm_mg_peibiDAO as $key => $value){
                $sq_je += $value['zijin_daozheng_jiner'];
            }
            $this->view->assign('sq_je', $sq_je);

            $this->view->assign(array(
                "income_cate_array" => $income_cate_array,                  //捐赠类型分析
                "income_sour_array" => $income_sour_array,                  //捐赠来源分析
                "income_sour_incomes" => $income_sour_incomes,
                "income_ppcate_array" => $income_ppcate_array,              //捐赠者类型分析
                "income_pp_array" => $income_pp_array,                      //校友个人及企业捐赠情况
                "income_department_array" => $income_department_array,      //各学院、部处捐赠情况
                "income_department_incomes" => $income_department_incomes,
                "income_department_pcounts" => $income_department_pcounts,
                "pay_array" => $pay_array,                                  //公益支出情况
                "years"     => $years,
                "create_p_count" => $create_p_count,                         //今年新增项目数
                "month_income_count" => $month_income_count,
                "pm_mg_peibiDAO" => $pm_mg_peibiDAO
            ));

            //所属部门
            $departmentlist = new jjh_mg_departmentDAO();
            $departmentlist = $departmentlist->get($this->dbhelper);
            $this->view->assign("departmentlist",$departmentlist);

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
				SessionUtil::initSession($passwordpost, true);

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
                $_SESSION = array();
	           	SessionUtil::sessionEnd();
	            header("location:".__BASEURL__."/management/index/loginview");
	        }catch (Exception $e){
	            echo $e->getMessage();
	            exit;
	        }
   	    }

		//getpasswordpost方法判定用户名密码的正确性
		public function getpasswordpostAction($username,$password){
			$my_adminDAO = $this->orm->createDAO('my_admin');
			$my_adminDAO ->findAdmin_name($username);
			$my_adminDAO ->findAdmin_pwd(substr(md5(serialize($password)), 0, 32));
			$admininfo = $my_adminDAO->get();
						
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