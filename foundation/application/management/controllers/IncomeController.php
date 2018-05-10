<?php
    /**
     * Created by PhpStorm.
     * User: wangnan - work
     * Date: 2018/5/9
     * Time: 16:37
     */

    require_once("BaseController.php");
    class Management_incomeController extends BaseController
    {
        private $dbhelper;
        public $jjh_mg_pp_list;

        public function indexAction()
        {
            $incomeinfo = $this->orm->createDAO("pm_mg_income");
            $pname = HttpUtil::getString("pname");

            if ($pname != "") {
                $incomeinfo->pname = $pname;
            }

            if(HttpUtil::postString("starttime")!="" && HttpUtil::postString("endtime") != ""){
                $starttime = strtotime(HttpUtil::postString("starttime"));
                $endtime = strtotime(HttpUtil::postString("endtime"));
                $incomeinfo->selectLimit = " and 	income_datetime >= '$starttime' and income_datetime <= '$endtime'";
            }

            $this->view->assign("pname", $pname);

            // 按照星级倒序，之后按照创建id倒序
            $incomeinfo ->selectLimit .= " order by star desc, id desc";

            //$incomeinfo ->debugSql =true;
            $incomeinfo = $incomeinfo->get($this->dbhelper);
            $total = count($incomeinfo);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($incomeinfo, null, "/management/income/index", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);
            $this->view->assign('incomelist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("income/index.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function addAction()
        {
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("income/addincome.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
         * 筹资立项 - 同步财务系统中间库，写入对照关系表
         * @throws Exception
         */
        public function addrsAction()
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

                $pm_fzr = implode(",",$_REQUEST['pm_fzr']);               //项目负责人
                //$pm_fzr_email = HttpUtil::postString("pm_fzr_email");
                //$pm_fzr_tel = HttpUtil::postString("pm_fzr_tel");
                $pm_llr = implode(",",$_REQUEST['pm_llr']);               //联络人
                //$pm_llr_email = HttpUtil::postString("pm_llr_email");
                //$pm_llr_tel = HttpUtil::postString("pm_llr_tel");

                $execute_fzr = implode(",",$_REQUEST['execute_fzr']);
                $execute_llr = implode(",",$_REQUEST['execute_llr']);

                $pm_ckfzr = implode(",",$_REQUEST['pm_ckfzr']);           //筹款负责人
                //$pm_ckfzr_email = HttpUtil::postString("pm_ckfzr_email");
                //$pm_ckfzr_tel = HttpUtil::postString("pm_ckfzr_tel");
                $pm_jzf = implode(",",$_REQUEST['pm_jzf']);               //捐赠方
                //$pm_jzf_email = HttpUtil::postString("pm_jzf_email");
                //$pm_jzf_tel = HttpUtil::postString("pm_jzf_tel");
                $pm_jzfllr = implode(",",$_REQUEST['pm_jzfllr']);         //捐赠方联络人
                //$pm_jzfllr_email = HttpUtil::postString("pm_jzfllr_email");
                //$pm_jzfllr_tel = HttpUtil::postString("pm_jzfllr_tel");
                $pm_sjjzf = implode(",",$_REQUEST['pm_sjjzf']);           //实际捐赠方
                //$pm_sjjzf_email = HttpUtil::postString("pm_sjjzf_email");
                //$pm_sjjzf_tel = HttpUtil::postString("pm_sjjzf_tel");
                $pm_sjjzfllr = implode(",",$_REQUEST['pm_sjjzfllr']);     //捐赠方联络人
                //$pm_sjjzfllr_email = HttpUtil::postString("pm_sjjzfllr_email");
                //$pm_sjjzfllr_tel = HttpUtil::postString("pm_sjjzfllr_tel");

                // $pm_fzr_mc = HttpUtil::postString("fzr");   //项目负责人

                if ($pname == "" || $department == "" || $pm_cate == "" || $qishi == "" || $jiner == "") {
                    alert_back("您输入的信息不完整，请查正后继续添加");
                }

                $pm_incomeDAO = $this->orm->createDAO("pm_mg_income");
                $pm_incomeDAO->beizhu = $beizhu;
                $pm_incomeDAO->cate = $pm_cate;
                $pm_incomeDAO->department = $department;
                $pm_incomeDAO->pid = $bianhao;
                $pm_incomeDAO->pm_fankui_datetime = $fankui;
                $pm_incomeDAO->pm_fuhuaqi = $fuhuaqi;
                $pm_incomeDAO->pm_qishi_datetime = $qishi;
                $pm_incomeDAO->pm_jiezhi_datetime = $jiezhi;
                $pm_incomeDAO->pm_liuben = $liuben;
                $pm_incomeDAO->pm_qianyue_datetime = $qianyuedate;
                $pm_incomeDAO->pm_qixian = $xianqi;
                $pm_incomeDAO->pm_tuidongqi = $tuidongqi;
                $pm_incomeDAO->pm_xieyi_juanzeng_jiner = $jiner;
                $pm_incomeDAO->pm_yishi = $yishi;
                // $pm_incomeDAO->pm_fzr_mc = $pm_fzr_mc;
                $pm_incomeDAO->pname = $pname;

                $pm_incomeDAO->pm_fzr = $pm_fzr;
                $pm_incomeDAO->pm_fzr_email = $pm_fzr_email;
                $pm_incomeDAO->pm_fzr_tel = $pm_fzr_tel;

                $pm_incomeDAO->pm_llr = $pm_llr;
                $pm_incomeDAO->pm_llr_email = $pm_llr_email;
                $pm_incomeDAO->pm_llr_tel = $pm_llr_tel;

                $pm_incomeDAO->pm_ckfzr = $pm_ckfzr;
                $pm_incomeDAO->pm_ckfzr_email = $pm_ckfzr_email;
                $pm_incomeDAO->pm_ckfzr_tel = $pm_ckfzr_tel;

                $pm_incomeDAO->pm_jzf = $pm_jzf;
                $pm_incomeDAO->pm_jzf_email = $pm_jzf_email;
                $pm_incomeDAO->pm_jzf_tel = $pm_jzf_tel;

                $pm_incomeDAO->pm_jzfllr = $pm_jzfllr;
                $pm_incomeDAO->pm_jzfllr_email = $pm_jzfllr_email;;
                $pm_incomeDAO->pm_jzfllr_tel = $pm_jzfllr_tel;

                $pm_incomeDAO->pm_sjjzf = $pm_sjjzf;
                $pm_incomeDAO->pm_sjjzf_email = $pm_sjjzf_email;
                $pm_incomeDAO->pm_sjjzf_tel = $pm_sjjzf_tel;

                $pm_incomeDAO->pm_sjjzfllr = $pm_sjjzfllr;
                $pm_incomeDAO->pm_sjjzfllr_email = $pm_sjjzfllr_email;
                $pm_incomeDAO->pm_sjjzfllr_tel = $pm_sjjzfllr_tel;

                $pm_incomeDAO->execute_fzr = $execute_fzr;
                $pm_incomeDAO->execute_llr = $execute_llr;

                // pm_id为所属父项目id 如果不为空，则新建子项目
                $pid = HttpUtil::postString("pm_id"); // $pid 父项目id
                if(!empty($pid)){
                    $parent_pm_info = $this->orm->createDAO("pm_mg_income")->findId($pid)->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
                    $parent_pm_id = $parent_pm_info[0]['id'];  //直属关系项目id
                    $parent_pm_id_path = $parent_pm_info[0]['parent_pm_id_path'];   //id_path

                    $pm_incomeDAO->parent_pm_id = $parent_pm_id;              //直属关系项目id
                    if(!empty($parent_pm_id_path)){
                        $pm_incomeDAO->parent_pm_id_path = 0;
                    }else {
                        $pm_incomeDAO->parent_pm_id_path = $parent_pm_id.",".$_REQUEST['id'];    //id_path
                    }
                }else {
                    $pm_incomeDAO->parent_pm_id = 0;
                    $pm_incomeDAO->parent_pm_id_path = 0;
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
                            $pm_incomeDAO->pm_xieyii_dianziban = __GETPICPATH__ . "jjh_download/" . $result['picname'];
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
                        alert_back("没有找到对应的财务部门信息，请联系管理员！或添加对应关系！");
                    }

                    $zwxmzdDAO = new CW_API();
                    $rs = $zwxmzdDAO ->sync_pm('000'.$xmnm, $xmbh, $pname, $zw_department_related[0]['zw_bmbh']);
                }

                $_pid = $pm_incomeDAO->save();   // $_pid 项目系统pm_id
                if($_pid) {
                    // 同步财务后写入对照表
                    $zw_pm_relatedDAO = $this->orm->createDAO("zw_pm_related");
                    $zw_pm_relatedDAO ->pm_id = $_pid;
                    $zw_pm_relatedDAO ->pm_name = $pname;
                    $zw_pm_relatedDAO ->zw_xmbh = $xmbh;
                    $zw_pm_relatedDAO ->zw_xmmc = $pname;
                    $zw_pm_relatedDAO ->zw_bmbh = $zw_department_related[0]['zw_bmbh'];
                    $zw_pm_relatedDAO ->save();

                    // 更新项目进度
                    $is_lixiang = HttpUtil::postString("is_lixiang");
                    if($is_lixiang == '1'){
                        $this->changerate($_pid,'add',1);
                    }else {
                        $this->changerate($_pid,'del',1);
                    }
                    alert_go("添加成功", "/management/income");
                }else {
                    alert_back("同步财务系统项目表失败，请联系管理员！");
                }

            }catch (Exception $e){
                throw $e;
            }
        }

        public function editAction()
        {
            if ($_REQUEST['id'] != "") {
                $pm_incomeDAO = new pm_mg_incomeDAO($_REQUEST['id']);
                $pm_incomeDAO = $pm_incomeDAO->get($this->dbhelper);

                $pm_mg_rateDAO = $this->orm->createDAO('pm_mg_rate');
                $pm_mg_rateDAO ->findPm_id($_REQUEST['id']);
                $pm_mg_rateDAO = $pm_mg_rateDAO ->get();
                $this->view->assign("rate_list_new", $pm_mg_rateDAO);

                $this->view->assign("income", $pm_incomeDAO);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("income/editincome.phtml");
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
            $pm_incomeDAO = $this->orm->createDAO("pm_mg_income")->findPname($pname)->get();
            if(!empty($pm_incomeDAO)){
                return true;
            }else {
                return false;
            }
        }

        public function editrsAction()
        {
            if ($_REQUEST['id'] != "") {
                // 获取老数据
                $_pm_incomeDAO = $this->orm->createDAO('pm_mg_income')->findId($_REQUEST['id']);
                $_pm_incomeDAO = $_pm_incomeDAO->get();

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

                $execute_fzr = implode(",",$_REQUEST['execute_fzr']);
                $execute_llr = implode(",",$_REQUEST['execute_llr']);

                $pm_fzr = implode(",",$_REQUEST['pm_fzr']);               //项目负责人
                //$pm_fzr_email = HttpUtil::postString("pm_fzr_email");
                //$pm_fzr_tel = HttpUtil::postString("pm_fzr_tel");
                $pm_llr = implode(",",$_REQUEST['pm_llr']);               //联络人
                //$pm_llr_email = HttpUtil::postString("pm_llr_email");
                //$pm_llr_tel = HttpUtil::postString("pm_llr_tel");
                $pm_ckfzr = implode(",",$_REQUEST['pm_ckfzr']);           //筹款负责人
                //$pm_ckfzr_email = HttpUtil::postString("pm_ckfzr_email");
                //$pm_ckfzr_tel = HttpUtil::postString("pm_ckfzr_tel");
                $pm_jzf = implode(",",$_REQUEST['pm_jzf']);               //捐赠方
                //$pm_jzf_email = HttpUtil::postString("pm_jzf_email");
                //$pm_jzf_tel = HttpUtil::postString("pm_jzf_tel");
                $pm_jzfllr = implode(",",$_REQUEST['pm_jzfllr']);         //捐赠方联络人
                //$pm_jzfllr_email = HttpUtil::postString("pm_jzfllr_email");
                //$pm_jzfllr_tel = HttpUtil::postString("pm_jzfllr_tel");
                $pm_sjjzf = implode(",",$_REQUEST['pm_sjjzf']);           //实际捐赠方
                //$pm_sjjzf_email = HttpUtil::postString("pm_sjjzf_email");
                //$pm_sjjzf_tel = HttpUtil::postString("pm_sjjzf_tel");
                $pm_sjjzfllr = implode(",",$_REQUEST['pm_sjjzfllr']);     //捐赠方联络人
                //$pm_sjjzfllr_email = HttpUtil::postString("pm_sjjzfllr_email");
                //$pm_sjjzfllr_tel = HttpUtil::postString("pm_sjjzfllr_tel");

                if ($pname == "" || $department == "" || $pm_cate == "" || $qishi == "" || $jiner == "") {
                    alert_back("您输入的信息不完整，请查正后继续添加");
                }

                $pm_incomeDAO = $this->orm->createDAO('pm_mg_income')->findId($_REQUEST['id']);
                $pm_incomeDAO->beizhu = $beizhu;
                $pm_incomeDAO->cate = $pm_cate;
                $pm_incomeDAO->department = $department;
                $pm_incomeDAO->pid = $bianhao;
                $pm_incomeDAO->pm_fankui_datetime = $fankui;
                $pm_incomeDAO->pm_fuhuaqi = $fuhuaqi;
                $pm_incomeDAO->pm_jiezhi_datetime = $jiezhi;
                $pm_incomeDAO->pm_liuben = $liuben;
                //$pm_incomeDAO->pm_qianyue_datetime = $qianyuedate;
                $pm_incomeDAO->pm_qishi_datetime = $qishi;
                $pm_incomeDAO->pm_qixian = $xianqi;
                $pm_incomeDAO->pm_tuidongqi = $tuidongqi;
                $pm_incomeDAO->pm_xieyi_juanzeng_jiner = $jiner;
                $pm_incomeDAO->pm_yishi = $yishi;
                // $pm_incomeDAO->pm_fzr_mc = $pm_fzr_mc;
                $pm_incomeDAO->pname = $pname;

                $pm_incomeDAO->pm_fzr = $pm_fzr;
                //$pm_incomeDAO->pm_fzr_email = $pm_fzr_email;
                //$pm_incomeDAO->pm_fzr_tel = $pm_fzr_tel;

                $pm_incomeDAO->pm_llr = $pm_llr;
                //$pm_incomeDAO->pm_llr_email = $pm_llr_email;
                //$pm_incomeDAO->pm_llr_tel = $pm_llr_tel;

                $pm_incomeDAO->pm_ckfzr = $pm_ckfzr;
                //$pm_incomeDAO->pm_ckfzr_email = $pm_ckfzr_email;
                //$pm_incomeDAO->pm_ckfzr_tel = $pm_ckfzr_tel;

                $pm_incomeDAO->pm_jzf = $pm_jzf;
                //$pm_incomeDAO->pm_jzf_email = $pm_jzf_email;
                //$pm_incomeDAO->pm_jzf_tel = $pm_jzf_tel;

                $pm_incomeDAO->pm_jzfllr = $pm_jzfllr;
                //$pm_incomeDAO->pm_jzfllr_email = $pm_jzfllr_email;;
                //$pm_incomeDAO->pm_jzfllr_tel = $pm_jzfllr_tel;

                $pm_incomeDAO->pm_sjjzf = $pm_sjjzf;
                //$pm_incomeDAO->pm_sjjzf_email = $pm_sjjzf_email;
                //$pm_incomeDAO->pm_sjjzf_tel = $pm_sjjzf_tel;

                $pm_incomeDAO->pm_sjjzfllr = $pm_sjjzfllr;
                //$pm_incomeDAO->pm_sjjzfllr_email = $pm_sjjzfllr_email;
                //$pm_incomeDAO->pm_sjjzfllr_tel = $pm_sjjzfllr_tel;

                $pm_incomeDAO->execute_fzr = $execute_fzr;
                $pm_incomeDAO->execute_llr = $execute_llr;

                $pid = HttpUtil::postString("pm_id");
                if(!empty($pid)){
                    $parent_pm_info = $this->orm->createDAO("pm_mg_income")->findId($pid)->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
                    $parent_pm_id = $parent_pm_info[0]['id'];  //直属关系项目id
                    $parent_pm_id_path = $parent_pm_info[0]['parent_pm_id_path'];   //id_path

                    if($_REQUEST['id'] == $pid){
                        alert_back("不能已自己作为父类，请重新选择父类项目");
                    }

                    $pm_incomeDAO->parent_pm_id = $parent_pm_id;              //直属关系项目id
                    if(!empty($parent_pm_id_path)){
                        $pm_incomeDAO->parent_pm_id_path = 0;
                    }else {
                        $pm_incomeDAO->parent_pm_id_path = $parent_pm_id.",".$_REQUEST['id'];    //id_path
                    }
                }else {
                    $pm_incomeDAO->parent_pm_id = 0;
                    $pm_incomeDAO->parent_pm_id_path = 0;
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
                            $pm_incomeDAO->pm_xieyii_dianziban = __GETPICPATH__ . "jjh_download/" . $result['picname'];
                        }
                    }
                }

                $logName = SessionUtil::getAdmininfo();
                addlog("修改筹资信息-" . $pname, $logName['admin_name'], $_SERVER['REMOTE_ADDR'], date("Y-m-d H:i:s", time()), json_encode($pm_incomeDAO));

                $pm_incomeDAO->history_pm_fzr = $_REQUEST['history_pm_fzr'];

                if($_pm_incomeDAO[0]['pm_fzr'] != $pm_fzr){
                    $pm_fzr_array = explode(',',$pm_fzr);
                    // 当项目负责人发生变化时，记录到历史负责人中。（ 变化时间，和变化到人员信息）
                    $pm_incomeDAO ->history_pm_fzr = $_REQUEST['history_pm_fzr'].'\n项目负责人于：'.date('Y-m-d',time()).'发生变更；'.'变更负责人为：'.$this->jjh_mg_pp_list[$pm_fzr_array[0]].' '.$this->jjh_mg_pp_list[$pm_fzr_array[1]].' '.$this->jjh_mg_pp_list[$pm_fzr_array[2]].' '.$this->jjh_mg_pp_list[$pm_fzr_array[3]].' ';
                }

                $pm_incomeDAO->save($this->dbhelper);

                $is_lixiang = HttpUtil::postString("is_lixiang");
                if($is_lixiang == '1'){
                    $this->changerate($_REQUEST['id'],'add',1);
                }else {
                    $this->changerate($_REQUEST['id'],'del',1);
                }
                alert_go("编辑成功", "/management/income");
            } else {
                alert_back("操作失败");
            }
        }

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

            // pplist
            $jjh_mg_ppDAO = $this->orm->createDAO('jjh_mg_pp')->get();
            if(!empty($jjh_mg_ppDAO)){
                foreach($jjh_mg_ppDAO as $k => $v){
                    $temp_array[$v['pid']] = $v['ppname'];
                }
            }
            if(!empty($jjh_mg_ppDAO)){
                foreach($jjh_mg_ppDAO as $k => $v){
                    $_temp_array[$v['pid']]['ppname'] = $v['ppname'];
                    $_temp_array[$v['pid']]['ppemail'] = $v['ppemail'];
                    $_temp_array[$v['pid']]['ppmobile'] = $v['ppmobile'];
                }
            }
            $this->view->assign("jjh_mg_pp_list_info", $_temp_array);
            $this->view->assign("jjh_mg_pp_list", $temp_array);
            $this->jjh_mg_pp_list = $temp_array;

            //项目名称列表
            $pm_chouzi = $this->orm->createDAO("pm_mg_chouzi");
            $pm_chouzi ->selectLimit .= " order by id desc";
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);

            // 项目进度
            $this->view->assign("rate_config",$this->rate_config);
        }

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'addrs',
                'editrs',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
    }