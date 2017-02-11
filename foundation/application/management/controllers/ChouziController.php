<?php
	require_once("BaseController.php");
	class Management_chouziController extends BaseController
    {
        private $dbhelper;

        public function indexAction()
        {
            $pname = HttpUtil::getString("pname");
            $department = HttpUtil::getString("department");
            $cate = HttpUtil::getString("cate");

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

        /**
         * 筹资立项 - 同步财务系统中间库，写入对照关系表
         * @throws Exception
         */
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
                $pm_fzr_email = HttpUtil::postString("pm_fzr_email");
                $pm_fzr_tel = HttpUtil::postString("pm_fzr_tel");
                $pm_llr = HttpUtil::postString("pm_llr");               //联络人
                $pm_llr_email = HttpUtil::postString("pm_llr_email");
                $pm_llr_tel = HttpUtil::postString("pm_llr_tel");
                $pm_ckfzr = HttpUtil::postString("pm_ckfzr");           //筹款负责人
                $pm_ckfzr_email = HttpUtil::postString("pm_ckfzr_email");
                $pm_ckfzr_tel = HttpUtil::postString("pm_ckfzr_tel");
                $pm_jzf = HttpUtil::postString("pm_jzf");               //捐赠方
                $pm_jzf_email = HttpUtil::postString("pm_jzf_email");
                $pm_jzf_tel = HttpUtil::postString("pm_jzf_tel");
                $pm_jzfllr = HttpUtil::postString("pm_jzfllr");         //捐赠方联络人
                $pm_jzfllr_email = HttpUtil::postString("pm_jzfllr_email");
                $pm_jzfllr_tel = HttpUtil::postString("pm_jzfllr_tel");
                $pm_sjjzf = HttpUtil::postString("pm_sjjzf");           //实际捐赠方
                $pm_sjjzf_email = HttpUtil::postString("pm_sjjzf_email");
                $pm_sjjzf_tel = HttpUtil::postString("pm_sjjzf_tel");
                $pm_sjjzfllr = HttpUtil::postString("pm_sjjzfllr");     //捐赠方联络人
                $pm_sjjzfllr_email = HttpUtil::postString("pm_sjjzfllr_email");
                $pm_sjjzfllr_tel = HttpUtil::postString("pm_sjjzfllr_tel");

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
                $pm_chouziDAO->pm_fzr_email = $pm_fzr_email;
                $pm_chouziDAO->pm_fzr_tel = $pm_fzr_tel;

                $pm_chouziDAO->pm_llr = $pm_llr;
                $pm_chouziDAO->pm_llr_email = $pm_llr_email;
                $pm_chouziDAO->pm_llr_tel = $pm_llr_tel;

                $pm_chouziDAO->pm_ckfzr = $pm_ckfzr;
                $pm_chouziDAO->pm_ckfzr_email = $pm_ckfzr_email;
                $pm_chouziDAO->pm_ckfzr_tel = $pm_ckfzr_tel;

                $pm_chouziDAO->pm_jzf = $pm_jzf;
                $pm_chouziDAO->pm_jzf_email = $pm_jzf_email;
                $pm_chouziDAO->pm_jzf_tel = $pm_jzf_tel;

                $pm_chouziDAO->pm_jzfllr = $pm_jzfllr;
                $pm_chouziDAO->pm_jzfllr_email = $pm_jzfllr_email;;
                $pm_chouziDAO->pm_jzfllr_tel = $pm_jzfllr_tel;

                $pm_chouziDAO->pm_sjjzf = $pm_sjjzf;
                $pm_chouziDAO->pm_sjjzf_email = $pm_sjjzf_email;
                $pm_chouziDAO->pm_sjjzf_tel = $pm_sjjzf_tel;

                $pm_chouziDAO->pm_sjjzfllr = $pm_sjjzfllr;
                $pm_chouziDAO->pm_sjjzfllr_email = $pm_sjjzfllr_email;
                $pm_chouziDAO->pm_sjjzfllr_tel = $pm_sjjzfllr_tel;

                // pm_id为所属父项目id 如果不为空，则新建子项目
                $pid = HttpUtil::postString("pm_id"); // $pid 父项目id
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
                $rs_1 = $pmDAO ->get_max_xmnm_copyID();
                $xmnm = (int)$rs1[0]['xmnm'] + 1;
                $xmnm_copy = (int)$rs_1[0]['xmnm'] + 1;
                if($xmnm_copy > $xmnm) $xmnm = $xmnm_copy;  // 如果临时表的最大值大，取临时表

                $rs2 = $pmDAO ->get_max_xmbhID();
                $xmbh = (int)$rs2[0]['xmbh'] + 1;

                if(empty($pid) || (int)$pid == 0){   // 只有父类项目同步到财务系统
                    // 获取对应部门信息
                    $zw_department_related = $this->orm->createDAO("zw_department_related");
                    $zw_department_related ->findPm_pid($department);
                    $zw_department_related = $zw_department_related ->get();

                    if(empty($zw_department_related[0]['zw_bmbh'])){
                        alert("没有找到对应的财务部门信息，请联系管理员！或添加对应关系！");
                    }

                    $zwxmzdDAO = new CW_API();
                    $rs = $zwxmzdDAO ->sync_pm('000'.$xmnm, $xmbh, $pname, $zw_department_related[0]['zw_bmbh']);
                }

                $_pid = $pm_chouziDAO->save();   // $_pid 项目系统pm_id
                if($_pid) {
                    // 同步财务后写入对照表
                    $zw_pm_relatedDAO = $this->orm->createDAO("zw_pm_related");
                    $zw_pm_relatedDAO ->pm_id = $_pid;
                    $zw_pm_relatedDAO ->pm_name = $pname;
                    $zw_pm_relatedDAO ->zw_xmbh = $xmbh;
                    $zw_pm_relatedDAO ->zw_xmmc = $pname;
                    $zw_pm_relatedDAO ->save();

                    // 更新项目进度
                    $is_lixiang = HttpUtil::postString("is_lixiang");
                    if($is_lixiang == '1'){
                        $this->changerate($_pid,'add',1);
                    }else {
                        $this->changerate($_pid,'del',1);
                    }
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

                $pm_mg_rateDAO = $this->orm->createDAO('pm_mg_rate');
                $pm_mg_rateDAO ->findPm_id($_REQUEST['id']);
                $pm_mg_rateDAO = $pm_mg_rateDAO ->get();
                $this->view->assign("rate_list_new", $pm_mg_rateDAO);

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
                $pm_fzr_email = HttpUtil::postString("pm_fzr_email");
                $pm_fzr_tel = HttpUtil::postString("pm_fzr_tel");
                $pm_llr = HttpUtil::postString("pm_llr");               //联络人
                $pm_llr_email = HttpUtil::postString("pm_llr_email");
                $pm_llr_tel = HttpUtil::postString("pm_llr_tel");
                $pm_ckfzr = HttpUtil::postString("pm_ckfzr");           //筹款负责人
                $pm_ckfzr_email = HttpUtil::postString("pm_ckfzr_email");
                $pm_ckfzr_tel = HttpUtil::postString("pm_ckfzr_tel");
                $pm_jzf = HttpUtil::postString("pm_jzf");               //捐赠方
                $pm_jzf_email = HttpUtil::postString("pm_jzf_email");
                $pm_jzf_tel = HttpUtil::postString("pm_jzf_tel");
                $pm_jzfllr = HttpUtil::postString("pm_jzfllr");         //捐赠方联络人
                $pm_jzfllr_email = HttpUtil::postString("pm_jzfllr_email");
                $pm_jzfllr_tel = HttpUtil::postString("pm_jzfllr_tel");
                $pm_sjjzf = HttpUtil::postString("pm_sjjzf");           //实际捐赠方
                $pm_sjjzf_email = HttpUtil::postString("pm_sjjzf_email");
                $pm_sjjzf_tel = HttpUtil::postString("pm_sjjzf_tel");
                $pm_sjjzfllr = HttpUtil::postString("pm_sjjzfllr");     //捐赠方联络人
                $pm_sjjzfllr_email = HttpUtil::postString("pm_sjjzfllr_email");
                $pm_sjjzfllr_tel = HttpUtil::postString("pm_sjjzfllr_tel");

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
                $pm_chouziDAO->pm_fzr_email = $pm_fzr_email;
                $pm_chouziDAO->pm_fzr_tel = $pm_fzr_tel;

                $pm_chouziDAO->pm_llr = $pm_llr;
                $pm_chouziDAO->pm_llr_email = $pm_llr_email;
                $pm_chouziDAO->pm_llr_tel = $pm_llr_tel;

                $pm_chouziDAO->pm_ckfzr = $pm_ckfzr;
                $pm_chouziDAO->pm_ckfzr_email = $pm_ckfzr_email;
                $pm_chouziDAO->pm_ckfzr_tel = $pm_ckfzr_tel;

                $pm_chouziDAO->pm_jzf = $pm_jzf;
                $pm_chouziDAO->pm_jzf_email = $pm_jzf_email;
                $pm_chouziDAO->pm_jzf_tel = $pm_jzf_tel;

                $pm_chouziDAO->pm_jzfllr = $pm_jzfllr;
                $pm_chouziDAO->pm_jzfllr_email = $pm_jzfllr_email;;
                $pm_chouziDAO->pm_jzfllr_tel = $pm_jzfllr_tel;

                $pm_chouziDAO->pm_sjjzf = $pm_sjjzf;
                $pm_chouziDAO->pm_sjjzf_email = $pm_sjjzf_email;
                $pm_chouziDAO->pm_sjjzf_tel = $pm_sjjzf_tel;

                $pm_chouziDAO->pm_sjjzfllr = $pm_sjjzfllr;
                $pm_chouziDAO->pm_sjjzfllr_email = $pm_sjjzfllr_email;
                $pm_chouziDAO->pm_sjjzfllr_tel = $pm_sjjzfllr_tel;

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

                $is_lixiang = HttpUtil::postString("is_lixiang");
                if($is_lixiang == '1'){
                    $this->changerate($_REQUEST['id'],'add',1);
                }else {
                    $this->changerate($_REQUEST['id'],'del',1);
                }
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

                /////////////////////////////////////////////////////////////////////////////////////////////////
                // 收支统计信息
                $zhichuinfo = new pm_mg_infoDAO();
                $zhichuinfo->joinTable(" left join pm_mg_chouzi as c on pm_mg_info.pm_name=c.pname");
                $zhichuinfo->selectField("
                    IF(
                        parent_pm_id = '',
                        concat(parent_pm_id, '-', c.id),
                        concat('0-', parent_pm_id, '-', c.id)
                    )AS bpath,
                     c.id as main_id,
                     c.parent_pm_id,
                     c.parent_pm_id_path,
                     pm_mg_info.pm_name,
                     pm_mg_info.shiyong_zhichu_datetime,
                     pm_mg_info.shiyong_zhichu_jiner,
                     pm_mg_info.zijin_daozhang_datetime,
                     pm_mg_info.zijin_daozheng_jiner,
                     pm_mg_info.pm_juanzeng_cate,
                     c.department,
                     c.pm_fzr_mc,
                     pm_mg_info.pm_pp");
                $zhichuinfo->selectLimit .= " and pm_mg_info.pm_name='".$pm_mg_chouziDAO[0]['pname']."' ";
                $zhichuinfo->selectLimit .= " and c.id!='' ";

                $zhichuinfo->selectLimit .= " order by bpath";
                $zhichuinfo = $zhichuinfo->get($this->dbhelper);

                $zhichu = '';
                $shouru = '';
                $xiangmushuliang = array(); // 项目数量 只统计父类id
                foreach ($zhichuinfo as $key => $v) {
                    $zhichuinfo[$key]['parent_pm_name'] = $this->pm[$v[parent_pm_id]];
                    $zhichuinfo[$key]['leixing'] = $this->getcateAction($this->pcatelist,$v['pm_juanzeng_cate']);
                    $zhichuinfo[$key]['deparment'] = $this->getdepartmentAction($this->departmentlist,$v['department']);
                    $zhichu += $v['shiyong_zhichu_jiner'];
                    $shouru += $v['zijin_daozheng_jiner'];
                }

                $this->view->assign("zhichu", round($zhichu,2));
                $this->view->assign("shouru", round($shouru,2));
                $this->view->assign("yuer", round(($shouru - $zhichu),2));
                $this->view->assign("zhichuinfo", $zhichuinfo);

                /////////////////////////////////////////////////////////////////////////////////////////////////
                // 签约信息
                $signDAO = $this->orm->createDAO("pm_mg_sign");
                $signDAO ->withPm_mg_chouzi(array("pm_id" => "id"));
                $like_sql = "";
                if($pm_mg_chouziDAO[0]['pname'] != "")
                {
                    $like_sql .= " AND pm_mg_chouzi.pname like '%".$pm_mg_chouziDAO[0]['pname']."%'";
                }
                $like_sql .= " order by id desc";
                $signDAO->select(" pm_mg_sign.*,pm_mg_chouzi.pname");
                $signDAO->selectLimit = $like_sql;
                $signDAO = $signDAO ->get();
                $this->view->assign("signDAO", $signDAO);
                //////////////////////////////////////////////////////////////////////////////////////////////


                /////////////////////////////////////////////////////////////////////////////////////////////////
                // 回馈信息
                $feedbackDAO = $this->orm->createDAO('pm_mg_feedback')->order('id DESC');
                $feedbackDAO->findPm_name($pm_mg_chouziDAO[0]['pname']);
                $feedbackDAO = $feedbackDAO ->get();
                $this->view->assign("feedbackDAO", $feedbackDAO);
                //////////////////////////////////////////////////////////////////////////////////////////////

                /////////////////////////////////////////////////////////////////////////////////////////////////
                // 配比信息
                $peibikDAO = $this->orm->createDAO('pm_mg_peibi')->order('id DESC');
                $peibikDAO->findPm_name($pm_mg_chouziDAO[0]['pname']);
                $peibikDAO = $peibikDAO ->get();
                $this->view->assign("peibikDAO", $peibikDAO);
                //////////////////////////////////////////////////////////////////////////////////////////////

                $this->view->assign("chouzi", $pm_mg_chouziDAO);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("chouzi/pminfo.phtml");
                //echo $this->view->render("index/footer.phtml");
            }else {
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('该项目不存在！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }
        }

        //获取部门名称
        public function getdepartmentAction($departmentlist,$id){
            if($departmentlist != ""){
                foreach ($departmentlist as $v){
                    if($v['id'] == $id){
                        $department = $v['pname'];
                    }
                }
            }
            return $department;
        }

        //获取项目分类名称
        public function getcateAction($catelist,$id){
            if($catelist != ""){
                foreach ($catelist as $v){
                    if($v['id'] == $id){
                        $cate = $v['catename'];
                    }
                }
            }
            return $cate;
        }

        //==============================================================================

		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkmanagement();
            $this->admininfo = SessionUtil::getAdmininfo();
            $this->view->assign("admininfo",$this->admininfo);

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