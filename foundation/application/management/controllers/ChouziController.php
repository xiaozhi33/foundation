<?php
	require_once("BaseController.php");
	class Management_chouziController extends BaseController
    {
        private $dbhelper;

        public function indexAction()
        {
            $pname = HttpUtil::postString("pname");
            $department = HttpUtil::postString("department");
            $cate = HttpUtil::postString("cate");

            $this->view->assign("pname", $pname);
            $this->view->assign("cate", $cate);
            $this->view->assign("department", $department);
            $chouziinfo = new pm_mg_chouziDAO();

            $chouziinfo ->joinTable (" left join pm_mg_rate as r on r.pm_id = id");
            $chouziinfo ->selectField(" *");

            if ($pname != "") {
                $chouziinfo->pname = $pname;
            }

            if ($department != "") {
                $chouziinfo->department = $department;
            }

            if ($cate != "") {
                $chouziinfo->cate = $cate;
            }

            /*if (HttpUtil::postString("starttime") != "" && HttpUtil::postString("endtime") != "") {
                $starttime = HttpUtil::postString("starttime");
                $endtime = HttpUtil::postString("endtime");
                $chouziinfo->selectLimit = " and pm_qishi_datetime<'$starttime' and pm_jiezhi_datetime>'$endtime'";
            }*/

            $chouziinfo ->selectLimit .= " order by id desc";

            //$chouziinfo ->debugSql =true;
            $chouziinfo = $chouziinfo->get($this->dbhelper);
            $total = count($chouziinfo);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($chouziinfo, null, "/management/chouzi/index", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);
            $this->view->assign('chouzilist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/index.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function addchouziAction()
        {
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/addchouzi.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function addrschouziAction()
        {
            try{
                $pname = HttpUtil::postString("pname");      //项目名称
                //check项目是否已经建立
                $is_pname = $this->checkPname($pname);
                if($is_pname === true){
                    alert_back("该项目已经被添加，请查正后重新添加！");
                }

                $bianhao = "jjh" . date("Yhdhis");  //项目编号 自动编号 编号内容为年月日时分秒

                $department = HttpUtil::postString("department");   //相关部门
                $pm_cate = HttpUtil::postString("pm_cate");  //项目分类
                $tuidongqi = HttpUtil::postString("tuidongqi");     //项目推动期
                $fuhuaqi = HttpUtil::postString("fuhuaqi");  //项目孵化期
                $liuben = HttpUtil::postString("liuben");  //项目孵化期
                $qianyuedate = HttpUtil::postString("qianyuedate"); //项目签约日期
                $fankui = HttpUtil::postString("fankui");    //项目反馈日期
                $qishi = HttpUtil::postString("qishi");         //项目起始日期
                $xianqi = HttpUtil::postString("xianqi");         //项目限期
                $jiezhi = HttpUtil::postString("jiezhi");         //项目截止日期
                $jiner = HttpUtil::postString("jiner");         //协议捐赠金额
                $yishi = HttpUtil::postString("yishi");         //项目仪式
                $beizhu = HttpUtil::postString("beizhu");         //备注

                $pm_fzr = HttpUtil::postString("pm_fzr");               //项目负责人
                $pm_llr = HttpUtil::postString("pm_llr");               //联络人
                $pm_ckfzr = HttpUtil::postString("pm_ckfzr");           //筹款负责人
                $pm_jzf = HttpUtil::postString("pm_jzf");               //捐赠方
                $pm_jzfllr = HttpUtil::postString("pm_jzfllr");         //捐赠方联络人
                $pm_sjjzf = HttpUtil::postString("pm_sjjzf");           //实际捐赠方
                $pm_sjjzfllr = HttpUtil::postString("pm_sjjzfllr");     //捐赠方联络人

                // $pm_fzr_mc = HttpUtil::postString("fzr");   //项目负责人

                if ($pname == "" || $department == "" || $pm_cate == "" || $qishi == "" || $jiner == "") {
                    alert_back("您输入的信息不完整，请查正后继续添加");
                }

                $pm_chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
                $pm_chouziDAO->beizhu = $beizhu;
                $pm_chouziDAO->cate = $pm_cate;
                $pm_chouziDAO->department = $department;
                $pm_chouziDAO->pid = $bianhao;
                $pm_chouziDAO->pm_fankui_datetime = $fankui;
                $pm_chouziDAO->pm_fuhuaqi = $fuhuaqi;
                $pm_chouziDAO->pm_qishi_datetime = $qishi;
                $pm_chouziDAO->pm_jiezhi_datetime = $jiezhi;
                $pm_chouziDAO->pm_liuben = $liuben;
                $pm_chouziDAO->pm_qianyue_datetime = $qianyuedate;
                $pm_chouziDAO->pm_qixian = $xianqi;
                $pm_chouziDAO->pm_tuidongqi = $tuidongqi;
                $pm_chouziDAO->pm_xieyi_juanzeng_jiner = $jiner;
                $pm_chouziDAO->pm_yishi = $yishi;
                // $pm_chouziDAO->pm_fzr_mc = $pm_fzr_mc;
                $pm_chouziDAO->pname = $pname;

                $pm_chouziDAO->pm_fzr = $pm_fzr;
                $pm_chouziDAO->pm_llr = $pm_llr;
                $pm_chouziDAO->pm_ckfzr = $pm_ckfzr;
                $pm_chouziDAO->pm_jzf = $pm_jzf;
                $pm_chouziDAO->pm_jzfllr = $pm_jzfllr;
                $pm_chouziDAO->pm_sjjzf = $pm_sjjzf;
                $pm_chouziDAO->pm_sjjzfllr = $pm_sjjzfllr;

                $pid = HttpUtil::postString("pm_id");
                if(!empty($pid)){
                    $parent_pm_info = $this->orm->createDAO("pm_mg_chouzi")->findId($pid)->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
                    $parent_pm_id = $parent_pm_info[0]['id'];  //直属关系项目id
                    $parent_pm_id_path = $parent_pm_info[0]['parent_pm_id_path'];   //id_path

                    $pm_chouziDAO->parent_pm_id = $parent_pm_id;              //直属关系项目id
                    if(!empty($parent_pm_id_path)){
                        $pm_chouziDAO->parent_pm_id_path = 0;
                    }else {
                        $pm_chouziDAO->parent_pm_id_path = $parent_pm_id.",".$_REQUEST['id'];    //id_path
                    }
                }else {
                    $pm_chouziDAO->parent_pm_id = 0;
                    $pm_chouziDAO->parent_pm_id_path = 0;
                }

                if ($_FILES['xieyidianzi']['name'] != "") {
                    if ($_FILES['xieyidianzi']['error'] != 4) {
                        if (!is_dir(__UPLOADPICPATH__ . "jjh_download/")) {
                            mkdir(__UPLOADPICPATH__ . "jjh_download/");
                        }
                        $uploadpic = new uploadPic($_FILES['xieyidianzi']['name'], $_FILES['xieyidianzi']['error'], $_FILES['xieyidianzi']['size'], $_FILES['xieyidianzi']['tmp_name'], $_FILES['xieyidianzi']['type'], 2);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__ . "jjh_download/";
                        $result = $uploadpic->uploadPic();
                        if ($result['error'] != 0) {
                            alert_back($result['msg']);
                        } else {
                            $pm_chouziDAO->pm_xieyii_dianziban = __GETPICPATH__ . "jjh_download/" . $result['picname'];
                        }
                    }
                }

                // 同步财务系统项目信息
                $pmDAO = new CW_API();
                $rs1 = $pmDAO ->get_max_xmnmID();
                $xmnm = (int)$rs1[0]['xmnm'] + 1;
                $rs2 = $pmDAO ->get_max_xmbhID();
                $xmbh = (int)$rs2[0]['xmbh'] + 1;

                // 获取对应部门信息
                $zw_department_related = $this->orm->createDAO("zw_department_related");
                $zw_department_related ->findPm_pid($department);
                $zw_department_related = $zw_department_related ->get();

                if(empty($zw_department_related[0]['zw_bmbh'])){
                    alert("没有找到对应的财务部门信息，请联系管理员！或添加对应关系！");
                }

                $zwxmzdDAO = new CW_API();
                $rs = $zwxmzdDAO ->sync_pm('000'.$xmnm, $xmbh, $pname, $zw_department_related[0]['zw_bmbh']);

                $pid = $pm_chouziDAO->save();

                if($rs) {
                    // 同步对照表
                    $zw_pm_relatedDAO = $this->orm->createDAO("zw_pm_related");
                    $zw_pm_relatedDAO ->pm_id = $pid;
                    $zw_pm_relatedDAO ->pm_name = $pname;
                    $zw_pm_relatedDAO ->zw_xmbh = $xmbh;
                    $zw_pm_relatedDAO ->zw_xmmc = $pname;
                    $zw_pm_relatedDAO ->save();

                    alert_go("添加成功", "/management/chouzi");
                }else {
                    alert_back("同步财务系统项目表失败，请联系管理员！");
                }

            }catch (Exception $e){
                throw $e;
            }
        }

        public function editchouziAction()
        {
            if ($_REQUEST['id'] != "") {
                $pm_chouziDAO = new pm_mg_chouziDAO($_REQUEST['id']);
                $pm_chouziDAO = $pm_chouziDAO->get($this->dbhelper);
                $this->view->assign("chouzi", $pm_chouziDAO);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("chouzi/editchouzi.phtml");
                echo $this->view->render("index/footer.phtml");
            } else {
                alert_back("操作失败");
            }
        }

        /**
         * check项目是否已经建立
         * @param $pname  项目名称
         * @return bool
         */
        public function checkPname($pname){
            $pm_chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->findPname($pname)->get();
            if(!empty($pm_chouziDAO)){
                return true;
            }else {
                return false;
            }
        }

        public function editrschouziAction()
        {
            if ($_REQUEST['id'] != "") {
                $bianhao = HttpUtil::postString("bianhao");  //项目编号
                $pname = HttpUtil::postString("pname");      //项目名称
                $department = HttpUtil::postString("department");   //相关部门
                $pm_cate = HttpUtil::postString("pm_cate");  //项目分类
                $tuidongqi = HttpUtil::postString("tuidongqi");     //项目推动期
                $fuhuaqi = HttpUtil::postString("fuhuaqi");  //项目孵化期
                $liuben = HttpUtil::postString("liuben");  //项目孵化期
                //$qianyuedate = HttpUtil::postString("qianyuedate"); //项目签约日期
                $fankui = HttpUtil::postString("fankui");    //项目反馈日期
                $qishi = HttpUtil::postString("qishi");         //项目起始日期
                $xianqi = HttpUtil::postString("xianqi");         //项目限期
                $jiezhi = HttpUtil::postString("jiezhi");         //项目截止日期
                $jiner = HttpUtil::postString("jiner");         //协议捐赠金额
                $yishi = HttpUtil::postString("yishi");         //项目仪式
                $beizhu = HttpUtil::postString("beizhu");         //备注
                // $pm_fzr_mc = HttpUtil::postString("fzr");   //项目负责人

                $pm_fzr = HttpUtil::postString("pm_fzr");               //项目负责人
                $pm_llr = HttpUtil::postString("pm_llr");               //联络人
                $pm_ckfzr = HttpUtil::postString("pm_ckfzr");           //筹款负责人
                $pm_jzf = HttpUtil::postString("pm_jzf");               //捐赠方
                $pm_jzfllr = HttpUtil::postString("pm_jzfllr");         //捐赠方联络人
                $pm_sjjzf = HttpUtil::postString("pm_sjjzf");           //实际捐赠方
                $pm_sjjzfllr = HttpUtil::postString("pm_sjjzfllr");     //捐赠方联络人

                if ($pname == "" || $department == "" || $pm_cate == "" || $qishi == "" || $jiner == "") {
                    alert_back("您输入的信息不完整，请查正后继续添加");
                }

                $pm_chouziDAO = new pm_mg_chouziDAO($_REQUEST['id']);
                $pm_chouziDAO->beizhu = $beizhu;
                $pm_chouziDAO->cate = $pm_cate;
                $pm_chouziDAO->department = $department;
                $pm_chouziDAO->pid = $bianhao;
                $pm_chouziDAO->pm_fankui_datetime = $fankui;
                $pm_chouziDAO->pm_fuhuaqi = $fuhuaqi;
                $pm_chouziDAO->pm_jiezhi_datetime = $jiezhi;
                $pm_chouziDAO->pm_liuben = $liuben;
                //$pm_chouziDAO->pm_qianyue_datetime = $qianyuedate;
                $pm_chouziDAO->pm_qishi_datetime = $qishi;
                $pm_chouziDAO->pm_qixian = $xianqi;
                $pm_chouziDAO->pm_tuidongqi = $tuidongqi;
                $pm_chouziDAO->pm_xieyi_juanzeng_jiner = $jiner;
                $pm_chouziDAO->pm_yishi = $yishi;
                // $pm_chouziDAO->pm_fzr_mc = $pm_fzr_mc;
                $pm_chouziDAO->pname = $pname;

                $pm_chouziDAO->pm_fzr = $pm_fzr;
                $pm_chouziDAO->pm_llr = $pm_llr;
                $pm_chouziDAO->pm_ckfzr = $pm_ckfzr;
                $pm_chouziDAO->pm_jzf = $pm_jzf;
                $pm_chouziDAO->pm_jzfllr = $pm_jzfllr;
                $pm_chouziDAO->pm_sjjzf = $pm_sjjzf;
                $pm_chouziDAO->pm_sjjzfllr = $pm_sjjzfllr;

                $pid = HttpUtil::postString("pm_id");
                if(!empty($pid)){
                    $parent_pm_info = $this->orm->createDAO("pm_mg_chouzi")->findId($pid)->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
                    $parent_pm_id = $parent_pm_info[0]['id'];  //直属关系项目id
                    $parent_pm_id_path = $parent_pm_info[0]['parent_pm_id_path'];   //id_path

                    if($_REQUEST['id'] == $pid){
                        alert_back("不能已自己作为父类，请重新选择父类项目");
                    }

                    $pm_chouziDAO->parent_pm_id = $parent_pm_id;              //直属关系项目id
                    if(!empty($parent_pm_id_path)){
                        $pm_chouziDAO->parent_pm_id_path = 0;
                    }else {
                        $pm_chouziDAO->parent_pm_id_path = $parent_pm_id.",".$_REQUEST['id'];    //id_path
                    }
                }else {
                    $pm_chouziDAO->parent_pm_id = 0;
                    $pm_chouziDAO->parent_pm_id_path = 0;
                }

                if ($_FILES['xieyidianzi']['name'] != "") {
                    if ($_FILES['xieyidianzi']['error'] != 4) {
                        if (!is_dir(__UPLOADPICPATH__ . "jjh_download/")) {
                            mkdir(__UPLOADPICPATH__ . "jjh_download/");
                        }
                        $uploadpic = new uploadPic($_FILES['xieyidianzi']['name'], $_FILES['xieyidianzi']['error'], $_FILES['xieyidianzi']['size'], $_FILES['xieyidianzi']['tmp_name'], $_FILES['xieyidianzi']['type'], 2);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__ . "jjh_download/";
                        $result = $uploadpic->uploadPic();
                        if ($result['error'] != 0) {
                            alert_back($result['msg']);
                        } else {
                            $pm_chouziDAO->pm_xieyii_dianziban = __GETPICPATH__ . "jjh_download/" . $result['picname'];
                        }
                    }
                }

                $logName = SessionUtil::getAdmininfo();
                addlog("修改筹资信息-" . $pname, $logName['admin_name'], $_SERVER['REMOTE_ADDR'], date("Y-m-d H:i:s", time()), json_encode($pm_chouziDAO));

                $pm_chouziDAO->save($this->dbhelper);
                alert_go("编辑成功", "/management/chouzi");
            } else {
                alert_back("操作失败");
            }
        }
        //=========================================================================================

        /**
         * 获取项目协议
         * @param $pm_id
         * @return array
         */
        public function getsignbypmidAction($pm_id){
            if(!empty($pm_id)){
                $signDAO = $this->orm->createDAO('pm_mg_sing')->findPm_id($pm_id);
                return($signDAO);
            }else {
                return array();
            }
        }

        /**
         * 保存项目协议
         * @param $pm_id
         * @param bool|string $sign_time
         * @param $sign_files
         * @return bool
         */
        public function toaddsignAction($pm_id, $sign_time, $sign_files){
            if(!empty($pm_id)){
                $signDAO = $this->orm->createDAO('pm_mg_sing');
                if(!$sign_time){$sign_time=date("Y-m-d H:i:s", time());}  //取当前时间
                $signDAO->pm_id = $pm_id;
                $signDAO->sign_time = $sign_time;
                $signDAO->sign_files($sign_files);
                $signDAO = $signDAO->save();
                return $signDAO;
            }else {
                return false;
            }
        }

        /**
         * 删除项目协议 -- 此功能可暂时不开放
         * @param $id
         * @return mixed
         */
        public function delsignAction($id){
            $signDAO = $this->orm->createDAO('pm_mg_sing')->findId($id)->delete();
            return $signDAO;
        }

        // =========================================================================================
        /**
         * pm_mg_pp List
         * 联系人管理
         */
        public function pplistAction()
        {
            $meetingDAO = $this->orm->createDAO('jjh_mg_pp')->order('pid DESC');
            $meetingDAO->getPager(array('path' => '/management/chouzi/pplist/'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/pplist.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
         * pm_mg_pp addppAction
         * 添加项目联系人
         */
        public function addppAction()
        {
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/addpp.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
		 * pm_mg_pp toaddppAction
		 * 添加联系人
		 */
		public function toaddppAction()
        {
			echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/addpp.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
		 * pm_mg_pp editppAction
		 * 编辑联系人
		 */
		public function editppAction()
        {
			$meetingDAO = $this->orm->createDAO('jjh_mg_pp')->order('pid DESC');
			$meetingDAO->getPager(array('path'=>'/management/chouzi/pplist/'))->assignTo($this->view);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render("chouzi/pplist.phtml");
			echo $this->view->render("index/footer.phtml");
        }

        /**
		 * pm_mg_pp toeditppAction
		 * 编辑联系人
		 */
		public function toeditppAction()
        {
            $meetingDAO = $this->orm->createDAO('jjh_mg_pp')->order('pid DESC');
            $meetingDAO->getPager(array('path' => '/management/chouzi/pplist/'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/pplist.phtml");
            echo $this->view->render("index/footer.phtml");
        }
		
		//=======================================================================

        /**
         * 添加子公司
         */
		public function addppcompanyAction()
		{
			$company_name = HttpUtil::postString("company_name");
            $company_contector = HttpUtil::postString("company_contector");
            $company_cont_style = HttpUtil::postString("company_cont_style");
			
			$jjh_mg_pp_companyDAO = $this->orm->createDAO('jjh_mg_pp_company');
			$jjh_mg_pp_companyDAO ->company_name = $company_name;
			$jjh_mg_pp_companyDAO ->company_contector = $company_contector;
			$jjh_mg_pp_companyDAO ->company_cont_style = $company_cont_style;
	
			$jjh_mg_pp_companyDAO ->save();
		}

        /**
         * 编辑子公司
         */
		public function edigppompanyAction()
		{
            (int)$id = HttpUtil::postString("id");
			$company_name = HttpUtil::postString("company_name");
            $company_contector = HttpUtil::postString("company_contector");
            $company_cont_style = HttpUtil::postString("company_cont_style");
			
			$jjh_mg_pp_companyDAO = $this->orm->createDAO('jjh_mg_pp_company');
			$jjh_mg_pp_companyDAO ->findId($id);
			$jjh_mg_pp_companyDAO ->company_name = $company_name;
			$jjh_mg_pp_companyDAO ->company_contector = $company_contector;
			$jjh_mg_pp_companyDAO ->company_cont_style = $company_cont_style;
	
			$jjh_mg_pp_companyDAO ->save();
		}
		
		public function delppcompanyAction()
		{
			$id = HttpUtil::postString("id");
			$jjh_mg_pp_companyDAO = $this->orm->createDAO('jjh_mg_pp_company');
			$jjh_mg_pp_companyDAO ->findId($id);
			$jjh_mg_pp_companyDAO ->delete();
		}

        /**
         * 项目详情
         */
        public function pminfoAction(){
            (int)$pid = HttpUtil::getString("id");
            if(!empty($pid)){
                $pm_mg_chouziDAO = $this->orm->createDAO('pm_mg_chouzi');
                $pm_mg_chouziDAO ->findId($pid);
                $pm_mg_chouziDAO = $pm_mg_chouziDAO ->get();

                //生产二维码
                if(!file_exists(__UPLOADPICPATH__ . '/pmqrcode/')) {
                    mkdir(__UPLOADPICPATH__ . '/pmqrcode/' ,0777);
                }
                if(!file_exists(__BASEURL__ ."/include/upload_file/pmqrcode/".$pid.".png")){
                    require_once 'phpqrcode/qrlib.php';
                    QRcode::png(__BASEURL__ ."/management/chouzi/pminfo?id=".$pid , __UPLOADPICPATH__ . '/pmqrcode/' . $pid .".png", 'H', 5, 2);
                }

                $this->view->assign("chouzi", $pm_mg_chouziDAO);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("chouzi/pminfo.phtml");
                echo $this->view->render("index/footer.phtml");
            }else {
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('该项目不存在！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }
        }

        //==============================================================================

		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkmanagement();
            $this->admininfo = SessionUtil::getAdmininfo();
			
			//项目分类
			$pcatelist = new jjh_mg_cateDAO();
			$pcatelist =  $pcatelist ->get($this->dbhelper);
			$this->view->assign("pcatelist",$pcatelist);
			
			//所属部门
			$departmentlist = new jjh_mg_departmentDAO();
			$departmentlist = $departmentlist->get($this->dbhelper);
			$this->view->assign("departmentlist",$departmentlist);

            //项目名称列表
            $pm_chouzi = new pm_mg_chouziDAO();
            $pm_chouzi ->selectLimit .= " order by id desc";
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);


            //获取筹资项目list
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
            $this->view->assign("chouzi_lists",$chouziDAO);
		}
	}
?>