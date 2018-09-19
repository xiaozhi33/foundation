<?php
	require_once("BaseController.php");
	class Management_chouziController extends BaseController
    {
        private $dbhelper;
        public $jjh_mg_pp_list;

        public function indexAction()
        {
            $pname = $_REQUEST['pname'];
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

            if(!empty($_REQUEST['rate']) && $_REQUEST['rate'][0] != ''){
                foreach($_REQUEST['rate'] as $key => $value){
                    $chouziinfo ->selectLimit .= ' AND find_in_set('.$value.',r.pm_rate)';
                }
            }

            /*if (HttpUtil::postString("starttime") != "" && HttpUtil::postString("endtime") != "") {
                $starttime = HttpUtil::postString("starttime");
                $endtime = HttpUtil::postString("endtime");
                $chouziinfo->selectLimit = " and pm_qishi_datetime<'$starttime' and pm_jiezhi_datetime>'$endtime'";
            }*/

            // 过滤逻辑删除的项目
            $chouziinfo ->selectLimit .= ' AND is_del=0';

            // 按照星级倒序，之后按照创建id倒序
            $chouziinfo ->selectLimit .= " order by star desc, id desc";

            //$chouziinfo ->debugSql =true;
            $chouziinfo = $chouziinfo->get($this->dbhelper);
            $total = count($chouziinfo);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($chouziinfo, null, "/management/chouzi/index", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);


            if(!empty($pageDAO['pageData'])){
                foreach($pageDAO['pageData'] as $kkk => $vvv){
                    // 获取 - 总捐赠收入/总协议金额/余额
                    /////////////////////////////////////////////////////////////////////////////////////////////////
                    // 项目收入
                    $sr = '';
                    $sr = $this->orm->createDAO("pm_mg_info");
                    $sr->select(" DATE_FORMAT(zijin_daozhang_datetime,'%Y-%m-%d') AS stime ,pm_mg_info.*");
                    $sr->selectLimit .= " and pm_mg_info.pm_name='".$vvv['pname']."' ";
                    $sr->selectLimit .= " and cate_id=0 and is_renling=1 ";
                    $sr->selectLimit .= " ORDER BY stime ASC ";
                    $sr = $sr->get();

                    $sr1 = '';
                    $sr1 = $this->orm->createDAO("pm_mg_info");
                    $sr1 ->select("sum(zijin_daozheng_jiner) as aaa");
                    $sr1 ->selectLimit .= " and pm_mg_info.pm_name='".$vvv['pname']."' ";
                    $sr1 ->selectLimit .= " and cate_id=0 and is_renling=1 ";
                    $sr1 = $sr1->get();


                    $pageDAO['pageData'][$kkk]['sr'] = $sr;
                    $pageDAO['pageData'][$kkk]['srhj'] = sprintf("%.2f", $sr1[0]['aaa']);
                    /////////////////////////////////////////////////////////////////////////
                    /*if(!empty($sr)){
                        $jzf = array();
                        $sjjzf = '';
                        foreach($sr as $key => $value){
                            if(!in_array($value['pm_pp'], $jzf)){
                                $jzf[] = $value['pm_pp'];
                            }
                        }
                    }

                    if(count($jzf) > 5){
                        $sjjzf = '多人';
                    }else {
                        $sjjzf = implode('，',$jzf);
                    }

                    $this->view->assign("sjjzf", $sjjzf);*/
                    //////////////////////////////////////////////////////////////////////////////////////////////

                    /////////////////////////////////////////////////////////////////////////////////////////////////
                    // 项目增值
                    $zz = '';
                    $zz = $this->orm->createDAO("pm_mg_income");
                    $zz->selectLimit .= " and pid='".$vvv['id']."' ";
                    $zz->selectLimit .= " ORDER BY income_datetime asc ";
                    $zz = $zz->get();

                    $zz1 = '';
                    $zz1 = $this->orm->createDAO("pm_mg_income");
                    $zz1 ->select("sum(income_jje) as aaa");
                    $zz1->selectLimit .= " and pid='".$vvv['id']."' ";
                    $zz1 = $zz1->get();

                    $pageDAO['pageData'][$kkk]['zz'] = $zz;
                    $pageDAO['pageData'][$kkk]['zzhj'] = sprintf("%.2f", $zz1[0]['aaa']);
                    //////////////////////////////////////////////////////////////////////////////////////////////

                    ////////////////////////
                    $_srhj = sprintf("%.2f", $sr1[0]['aaa']) + sprintf("%.2f", $zz1[0]['aaa']);
                    $pageDAO['pageData'][$kkk]['srhjh'] = $_srhj;
                    ///////////////////

                    /////////////////////////////////////////////////////////////////////////////////////////////////
                    // 项目支出
                    $zc = '';
                    $zc = $this->orm->createDAO("pm_mg_info");
                    $zc->selectLimit .= " and pm_mg_info.pm_name='".$vvv['pname']."' ";
                    $zc->selectLimit .= " and cate_id=1 and is_renling=1 and shiyong_zhichu_jiner!=0";
                    $zc->selectLimit .= " ORDER BY shiyong_zhichu_datetime asc ";
                    $zc = $zc->get();

                    $zc1 = '';
                    $zc1 = $this->orm->createDAO("pm_mg_info");
                    $zc1 ->select("sum(shiyong_zhichu_jiner) as aaa, sum(jiangli_renshu) as bbb");
                    $zc1 ->selectLimit .= " and pm_mg_info.pm_name='".$vvv['pname']."' ";
                    $zc1 ->selectLimit .= " and cate_id=1 and is_renling=1 ";
                    $zc1 = $zc1->get();

                    $pageDAO['pageData'][$kkk]['zc'] = $zc;
                    $pageDAO['pageData'][$kkk]['zchj'] = sprintf("%.2f", $zc1[0]['aaa']);
                    $pageDAO['pageData'][$kkk]['rshj'] = $zc1[0]['bbb'];
                    //////////////////////////////////////////////////////////////////////////////////////////////
                    // 项目调账
                    $aaDAO = '';
                    $aaDAO = $this->orm->createDAO("pm_mg_amount_adjustment");
                    $aaDAO ->selectLimit .= " AND ( in_pm_name= '".$vvv['pname']."' or out_pm_name = '".$vvv['pname']."')";
                    $aaDAO ->selectLimit .= " ORDER BY datetimes DESC";
                    $aaDAO = $aaDAO ->get();

                    $tzhj = 0;
                    if(!empty($aaDAO)){
                        $tzhj = 0;
                        foreach($aaDAO as $key => $value){
                            if($pageDAO['pageData'][0]['pname'] == $value['out_pm_name']){
                                $tzhj = ($tzhj + $value['je']);
                            }else {
                                $tzhj = ($tzhj + $value['je']);
                            }
                        }
                    }

                    //////////////////////////////////////////////////////////////////////////////////////////////
                    // 项目余额 = 捐赠收入 + 收益 - 捐赠支出 + 调账
                    $xmye = 0;
                    $xmye = sprintf("%.2f", $sr1[0]['aaa']) + sprintf("%.2f", $zz1[0]['aaa']) - sprintf("%.2f", $zc1[0]['aaa']) + $tzhj;
                    $tzhj = 0;
                    if(number_format($xmye, 2) == 0){
                        $xmye = 0;
                    }
                    $pageDAO['pageData'][$kkk]['xmye'] = $xmye;

                    /////////////////////////////////////////////////////////////////////////////////////////////
                    // 项目最后收支时间和金额
                    $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
                    $pm_mg_infoDAO ->select("zijin_daozhang_datetime, zijin_daozheng_jiner");
                    $pm_mg_infoDAO ->selectLimit.= " AND pm_name='".$vvv['pname']."'";
                    $pm_mg_infoDAO ->selectLimit.= " AND cate_id=0 AND is_renling=1";
                    $pm_mg_infoDAO ->selectLimit.= " ORDER BY zijin_daozhang_datetime DESC LIMIT 0,1";
                    $pm_mg_infoDAO = $pm_mg_infoDAO->get();

                    $pm_mg_info_DAO = $this->orm->createDAO("pm_mg_info");
                    $pm_mg_info_DAO ->select("shiyong_zhichu_datetime, shiyong_zhichu_jiner");
                    $pm_mg_info_DAO ->selectLimit.= " AND pm_name='".$vvv['pname']."'";
                    $pm_mg_info_DAO ->selectLimit.= " AND cate_id=1 AND is_renling=1";
                    $pm_mg_info_DAO ->selectLimit.= " ORDER BY shiyong_zhichu_datetime DESC LIMIT 0,1";
                    $pm_mg_info_DAO = $pm_mg_info_DAO->get();

                    $pageDAO['pageData'][$kkk]['last_zijin'] = array($pm_mg_infoDAO[0]['zijin_daozhang_datetime'],$pm_mg_infoDAO[0]['zijin_daozheng_jiner']);
                    $pageDAO['pageData'][$kkk]['last_shiyong'] = array($pm_mg_info_DAO[0]['shiyong_zhichu_datetime'],$pm_mg_info_DAO[0]['shiyong_zhichu_jiner']);
                }
            }


            $this->view->assign('chouzilist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/index.phtml");
            //echo $this->view->render("index/footer.phtml");
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

                if(!empty($qishi)){
                    $qishi = $qishi."-01-01 00:00:00";
                }
                if(!empty($jiezhi)){
                    $jiezhi = $jiezhi."-12-31 00:00:00";
                }
                $beizhu =  htmlspecialchars($_POST['beizhu']);     //备注
                //$beizhu = HttpUtil::postString("beizhu");         //备注

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

                $pm_chouziDAO->execute_fzr = $execute_fzr;
                $pm_chouziDAO->execute_llr = $execute_llr;

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
                        alert_back("没有找到对应的财务部门信息，请联系管理员！或添加对应关系！");
                    }

                    $zwxmzdDAO = new CW_API();

                    // 同步负责人名称
                    $_fzr = '';
                    if($this->jjh_mg_pp_list != "") {
                        foreach ($this->jjh_mg_pp_list as $k => $v) {
                            if (!empty($pm_fzr)){
                                foreach (explode(',', $pm_fzr) as $key => $value) {
                                    if ($k == $value) {
                                        $_fzr .= $v.' ';
                                    }
                                }
                            }
                        }
                    }
                    $rs = $zwxmzdDAO ->sync_pm('000'.$xmnm, $xmbh, $pname, $zw_department_related[0]['zw_bmbh'],$_fzr);
                    $_fzr = '';
                }

                $_pid = $pm_chouziDAO->save();   // $_pid 项目系统pm_id
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
                // 获取老数据
                $_pm_chouziDAO = $this->orm->createDAO('pm_mg_chouzi')->findId($_REQUEST['id']);
                $_pm_chouziDAO = $_pm_chouziDAO->get();

                $bianhao = HttpUtil::postString("bianhao");  //项目编号
                $pname = HttpUtil::postString("pname");      //项目名称
                $department = HttpUtil::postString("department");   //相关部门
                $pm_cate = HttpUtil::postString("pm_cate");  //项目分类
                $tuidongqi = HttpUtil::postString("tuidongqi");     //项目推动期
                $fuhuaqi = HttpUtil::postString("fuhuaqi");  //项目孵化期
                $liuben = HttpUtil::postString("liuben");  //项目孵化期
                //$qianyuedate = HttpUtil::postString("qianyuedate"); //项目签约日期
                $fankui = HttpUtil::postString("fankui");       //项目反馈日期
                $qishi = HttpUtil::postString("qishi");         //项目起始日期
                $xianqi = HttpUtil::postString("xianqi");       //项目限期
                $jiezhi = HttpUtil::postString("jiezhi");       //项目截止日期
                $jiner = HttpUtil::postString("jiner");         //协议捐赠金额
                $yishi = HttpUtil::postString("yishi");         //项目仪式

                if(!empty($qishi)){
                    $qishi = $qishi."-01-01 00:00:00";
                }
                if(!empty($jiezhi)){
                    $jiezhi = $jiezhi."-12-31 00:00:00";
                }

                $beizhu =  htmlspecialchars($_POST['beizhu']);     //备注
                //$beizhu = HttpUtil::postString("beizhu");         //备注
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

                $pm_chouziDAO = $this->orm->createDAO('pm_mg_chouzi')->findId($_REQUEST['id']);
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
                //$pm_chouziDAO->pm_fzr_email = $pm_fzr_email;
                //$pm_chouziDAO->pm_fzr_tel = $pm_fzr_tel;

                $pm_chouziDAO->pm_llr = $pm_llr;
                //$pm_chouziDAO->pm_llr_email = $pm_llr_email;
                //$pm_chouziDAO->pm_llr_tel = $pm_llr_tel;

                $pm_chouziDAO->pm_ckfzr = $pm_ckfzr;
                //$pm_chouziDAO->pm_ckfzr_email = $pm_ckfzr_email;
                //$pm_chouziDAO->pm_ckfzr_tel = $pm_ckfzr_tel;

                $pm_chouziDAO->pm_jzf = $pm_jzf;
                //$pm_chouziDAO->pm_jzf_email = $pm_jzf_email;
                //$pm_chouziDAO->pm_jzf_tel = $pm_jzf_tel;

                $pm_chouziDAO->pm_jzfllr = $pm_jzfllr;
                //$pm_chouziDAO->pm_jzfllr_email = $pm_jzfllr_email;;
                //$pm_chouziDAO->pm_jzfllr_tel = $pm_jzfllr_tel;

                $pm_chouziDAO->pm_sjjzf = $pm_sjjzf;
                //$pm_chouziDAO->pm_sjjzf_email = $pm_sjjzf_email;
                //$pm_chouziDAO->pm_sjjzf_tel = $pm_sjjzf_tel;

                $pm_chouziDAO->pm_sjjzfllr = $pm_sjjzfllr;
                //$pm_chouziDAO->pm_sjjzfllr_email = $pm_sjjzfllr_email;
                //$pm_chouziDAO->pm_sjjzfllr_tel = $pm_sjjzfllr_tel;

                $pm_chouziDAO->execute_fzr = $execute_fzr;
                $pm_chouziDAO->execute_llr = $execute_llr;

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

                $pm_chouziDAO->history_pm_fzr = $_REQUEST['history_pm_fzr'];

                if($_pm_chouziDAO[0]['pm_fzr'] != $pm_fzr){
                    $pm_fzr_array = explode(',',$pm_fzr);
                    // 当项目负责人发生变化时，记录到历史负责人中。（ 变化时间，和变化到人员信息）
                    $pm_chouziDAO ->history_pm_fzr = $_REQUEST['history_pm_fzr'].'\n项目负责人于：'.date('Y-m-d',time()).'发生变更；'.'变更负责人为：'.$this->jjh_mg_pp_list[$pm_fzr_array[0]].' '.$this->jjh_mg_pp_list[$pm_fzr_array[1]].' '.$this->jjh_mg_pp_list[$pm_fzr_array[2]].' '.$this->jjh_mg_pp_list[$pm_fzr_array[3]].' ';
                }

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
                     pm_mg_info.jiangli_fanwei,
                     pm_mg_info.jiangli_renshu,
                     c.department,
                     c.pm_fzr_mc,
                     pm_mg_info.pm_pp");
                $zhichuinfo->selectLimit .= " and pm_mg_info.pm_name='".$pm_mg_chouziDAO[0]['pname']."' ";
                $zhichuinfo->selectLimit .= " and c.id!='' and is_renling=1 ";

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

        public function repminfoAction(){
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
                     pm_mg_info.jiangli_fanwei,
                     pm_mg_info.jiangli_renshu,
                     c.department,
                     c.pm_fzr_mc,
                     pm_mg_info.pm_pp");
                $zhichuinfo->selectLimit .= " and pm_mg_info.pm_name='".$pm_mg_chouziDAO[0]['pname']."' ";
                $zhichuinfo->selectLimit .= " and c.id!='' and is_renling=1 ";

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
                // 项目收入
                $sr = $this->orm->createDAO("pm_mg_info");
                $sr->select(" DATE_FORMAT(zijin_daozhang_datetime,'%Y-%m-%d') AS stime ,pm_mg_info.*");
                $sr->selectLimit .= " and pm_mg_info.pm_name='".$pm_mg_chouziDAO[0]['pname']."' ";
                $sr->selectLimit .= " and cate_id=0 and is_renling=1 ";
                $sr->selectLimit .= " ORDER BY stime ASC ";
                $sr = $sr->get();

                $sr1 = $this->orm->createDAO("pm_mg_info");
                $sr1 ->select("sum(zijin_daozheng_jiner) as aaa");
                $sr1 ->selectLimit .= " and pm_mg_info.pm_name='".$pm_mg_chouziDAO[0]['pname']."' ";
                $sr1 ->selectLimit .= " and cate_id=0 and is_renling=1 ";
                $sr1 = $sr1->get();

                $this->view->assign("sr", $sr);
                $this->view->assign("srhj", sprintf("%.2f", $sr1[0]['aaa']));
                /////////////////////////////////////////////////////////////////////////
                if(!empty($sr)){
                    $jzf = array();
                    $sjjzf = '';
                    foreach($sr as $key => $value){
                        if(!in_array($value['pm_pp'], $jzf)){
                            $jzf[] = $value['pm_pp'];
                        }
                    }
                }

                if(count($jzf) > 5){
                    $sjjzf = '多人';
                }else {
                    $sjjzf = implode('，',$jzf);
                }

                $this->view->assign("sjjzf", $sjjzf);

                //////////////////////////////////////////////////////////////////////////////////////////////

                /////////////////////////////////////////////////////////////////////////////////////////////////
                // 项目增值
                $zz = $this->orm->createDAO("pm_mg_income");
                $zz->selectLimit .= " and pid='".$pm_mg_chouziDAO[0]['id']."' ";
                $zz->selectLimit .= " ORDER BY income_datetime asc ";
                $zz = $zz->get();

                $zz1 = $this->orm->createDAO("pm_mg_income");
                $zz1 ->select("sum(income_jje) as aaa");
                $zz1->selectLimit .= " and pid='".$pm_mg_chouziDAO[0]['id']."' ";
                $zz1 = $zz1->get();

                $this->view->assign("zz", $zz);
                $this->view->assign("zzhj", sprintf("%.2f", $zz1[0]['aaa']));
                //////////////////////////////////////////////////////////////////////////////////////////////

                ////////////////////////
                $_srhj = sprintf("%.2f", $sr1[0]['aaa']) + sprintf("%.2f", $zz1[0]['aaa']);
                $this->view->assign("srhjh", $_srhj);
                ///////////////////

                /////////////////////////////////////////////////////////////////////////////////////////////////
                // 项目支出
                $zc = $this->orm->createDAO("pm_mg_info");
                $zc->selectLimit .= " and pm_mg_info.pm_name='".$pm_mg_chouziDAO[0]['pname']."' ";
                $zc->selectLimit .= " and cate_id=1 and is_renling=1 and shiyong_zhichu_jiner!=0";
                $zc->selectLimit .= " ORDER BY shiyong_zhichu_datetime asc ";
                $zc = $zc->get();

                $zc1 = $this->orm->createDAO("pm_mg_info");
                $zc1 ->select("sum(shiyong_zhichu_jiner) as aaa, sum(jiangli_renshu) as bbb");
                $zc1 ->selectLimit .= " and pm_mg_info.pm_name='".$pm_mg_chouziDAO[0]['pname']."' ";
                $zc1 ->selectLimit .= " and cate_id=1 and is_renling=1 ";
                $zc1 = $zc1->get();

                $this->view->assign("zc", $zc);
                $this->view->assign("zchj", sprintf("%.2f", $zc1[0]['aaa']));
                $this->view->assign("rshj", $zc1[0]['bbb']);

                //////////////////////////////////////////////////////////////////////////////////////////////
                // 项目调账
                $aaDAO = $this->orm->createDAO("pm_mg_amount_adjustment");
                $aaDAO ->selectLimit .= " AND ( in_pm_name= '".$pm_mg_chouziDAO[0]['pname']."' or out_pm_name = '".$pm_mg_chouziDAO[0]['pname']."')";
                $aaDAO ->selectLimit .= " ORDER BY datetimes DESC";
                $aaDAO = $aaDAO ->get();

                if(!empty($aaDAO)){
                    $tzhj = 0;
                    foreach($aaDAO as $key => $value){
                        if($pm_mg_chouziDAO[0]['pname'] == $value['out_pm_name']){
                            $tzhj = ($tzhj - $value['je']);
                        }else {
                            $tzhj = ($tzhj + $value['je']);
                        }
                    }
                    $this->view->assign("tzhj", $tzhj);
                }
                $this->view->assign("aaDAO", $aaDAO);

                //////////////////////////////////////////////////////////////////////////////////////////////
                // 项目余额 = 捐赠收入 + 收益 - 捐赠支出 + 调账
                $xmye = sprintf("%.2f", $sr1[0]['aaa']) + sprintf("%.2f", $zz1[0]['aaa']) - sprintf("%.2f", $zc1[0]['aaa']) + $tzhj;
                if(number_format($xmye, 2) == 0){
                    $xmye = 0;
                }
                $this->view->assign("xmye", $xmye);

                /////////////////////////////////////////////////////////////////////////////////////////////////
                // 项目
                /*$sr = $this->orm->createDAO("pm_mg_info");
                $sr->selectLimit .= " and pm_mg_info.pm_name='".$pm_mg_chouziDAO[0]['pname']."' ";
                $sr->selectLimit .= " and cate_id=0 and is_renling=1 ";
                $sr->selectLimit .= " ORDER BY zijin_daozhang_datetime DESC ";
                $sr = $sr->get();
                $this->view->assign("sr", $sr);*/
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
                $peibikDAO = $this->orm->createDAO('pm_mg_peibi');
                $peibikDAO->order('peibi_datetime ASC');
                $peibikDAO->findPm_name($pm_mg_chouziDAO[0]['pname']);
                $peibikDAO = $peibikDAO ->get();

                $pbhj = 0;
                if(!empty($peibikDAO)){
                    foreach($peibikDAO as $k => $v){
                        $pbhj += $v['je'];
                    }
                    $this->view->assign("pbhj", $pbhj);
                }
                $this->view->assign("peibikDAO", $peibikDAO);

                // 配比支出
                $peibizcDAO = $this->orm->createDAO('pm_mg_peibi_zc');
                $peibizcDAO->order('peibi_datetime ASC');
                $peibizcDAO->findPm_name($pm_mg_chouziDAO[0]['pname']);
                $peibizcDAO = $peibizcDAO->get();

                $pbzchj = 0;
                if(!empty($peibizcDAO)){
                    foreach($peibizcDAO as $k => $v){
                        $pbzchj += $v['je'];
                    }
                    $this->view->assign("pbzchj", $pbzchj);
                }
                $this->view->assign("peibizcDAO", $peibizcDAO);
                //////////////////////////////////////////////////////////////////////////////////////////////

                // 配比回项目
                $peibik1DAO = $this->orm->createDAO('pm_mg_peibi');
                $peibik1DAO->order('peibi_datetime ASC');
                $peibik1DAO->findPm_name($pm_mg_chouziDAO[0]['pname']);
                $peibik1DAO->findis_income(1);
                $peibik1DAO = $peibik1DAO ->get();

                $pbhj1 = 0;
                if(!empty($peibik1DAO)){
                    foreach($peibik1DAO as $k => $v){
                        $pbhj1 += $v['je'];
                    }
                    $this->view->assign("pbhj1", $pbhj1);
                }
                $this->view->assign("peibik1DAO", $peibik1DAO);

                //////////////////////////////////////////////////////////////////////////////////////////////

                $this->view->assign("chouzi", $pm_mg_chouziDAO);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("chouzi/repminfo.phtml");
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

        // 与财务系统对照
        public function compareAction()
        {
            $pm_name = HttpUtil::getString("pm_name");
            $zw_xmmc = HttpUtil::getString("zw_xmmc");

            $this->view->assign("pm_name", $pm_name);
            $this->view->assign("zw_xmmc", $zw_xmmc);

            $relatedDAO = $this->orm->createDAO("zw_pm_related");

            if(!empty($pm_name)){
                $relatedDAO ->selectLimit .= " AND pm_name like '%".$pm_name."%'";
            }

            if(!empty($zw_xmmc)){
                $relatedDAO ->selectLimit .= " AND zw_xmmc like '%".$zw_xmmc."%'";
            }

            // 按照星级倒序，之后按照创建id倒序
            $relatedDAO ->selectLimit .= " order by id desc";
            $relatedDAO = $relatedDAO->get($this->dbhelper);
            $total = count($relatedDAO);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($relatedDAO, null, "/management/chouzi/compare", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);
            $this->view->assign('chouzilist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/compareindex.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function editcompareAction()
        {
            $id = (int)$_REQUEST['id'];
            if(!empty($id)){
                $relatedDAO = $this->orm->createDAO("zw_pm_related");
                $relatedDAO ->findId($id);
                $relatedDAO = $relatedDAO ->get();
                $this->view->assign('ppinfo', $relatedDAO);

                echo $this->view->render("index/header.phtml");
                echo $this->view->render("chouzi/editcompare.phtml");
                echo $this->view->render("index/footer.phtml");
            }else {
                $this->alert_back("非法操作！");
            }
        }

        public function editrscompareAction()
        {
            if($_REQUEST['qxsqm'] == "" || $_REQUEST['qxsqm'] != "jjh"){
                $this->alert_back("编辑权限授权码输入为空或不正确，请重新输入！");
            }
            // pm_id 项目id	   pm_name 项目名称	zw_xmbh 项目编号	zw_xmmc 项目名称	zw_bmbh
            $id = (int)$_REQUEST['id'];
            if(!empty($id)){
                $relatedDAO = $this->orm->createDAO("zw_pm_related");
                $relatedDAO ->findId($id);
                $relatedDAO ->pm_id = $_REQUEST['pm_id'];
                $relatedDAO ->pm_name = $_REQUEST['pm_name'];
                $relatedDAO ->zw_xmbh = $_REQUEST['zw_xmbh'];
                $relatedDAO ->zw_xmmc = $_REQUEST['zw_xmmc'];
                $relatedDAO ->zw_bmbh = $_REQUEST['zw_bmbh'];
                $relatedDAO ->save();

                $logName = SessionUtil::getAdmininfo();
                addlog("修改项目对照信息-".$_REQUEST['pm_name'].'-'.$_REQUEST['zw_xmmc'],$logName['admin_name'],$_SERVER['REMOTE_ADDR'],date("Y-m-d H:i:s",time()),json_encode($relatedDAO));

                $this->alert_go("编辑成功！", "/management/chouzi/compare");
            }else {
                $this->alert_back("修改项目对照信息失败，请联系管理员！");
            }
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
            $pm_chouzi = new pm_mg_chouziDAO();
            $pm_chouzi ->selectLimit .= " AND is_del=0";
            $pm_chouzi ->selectLimit .= " order by id desc";
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);

            // 项目进度
            $this->view->assign("rate_config",$this->rate_config);

            //获取筹资项目list
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
            $this->view->assign("chouzi_lists",$chouziDAO);
		}

        /**
         * function star
         * action ajax
         */
        public function ajaxaddstarAction()
        {
            $pm_id = $_REQUEST['pm_id'];
            $star = $_REQUEST['star'];

            if(!empty($pm_id)){
                $pm_infoDAO = $this->orm->createDAO("pm_mg_chouzi");
                $pm_infoDAO ->findId($pm_id);
                $pm_infoDAO ->star = $star;
                $pm_infoDAO ->save();

                echo json_encode(array('status'=>'success','message'=>'标星成功'));exit;
            }else {
                echo json_encode(array('status'=>'success','message'=>'标星失败'));exit;
            }
        }

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'addrschouzi',
                'editrschouzi',
                'check-pname',
                'getsignbypmid',
                'toaddsign',
                'toaddpp',
                'toeditpp',
                'pminfo',
                'getdepartment',
                'getcate',
                'ajaxaddstar',
                'compare',
                'editcompare',
                'editrscompare',
                'repminfo'
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}
?>