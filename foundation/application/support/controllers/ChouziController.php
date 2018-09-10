<?php
	require_once("BaseController.php");
	class Support_chouziController extends BaseController
    {
        private $dbhelper;
        public $jjh_mg_pp_list;

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

            //
            $chouziinfo ->department = $this->admininfo['admin_info']['department_id'];

            if ($pname != "") {
                $chouziinfo->pname = $pname;
            }

            /*if ($department != "") {
                $chouziinfo->department = $department;
            }*/
            $chouziinfo->department = $this->admininfo['admin_info']['department_id'];

            if ($cate != "") {
                $chouziinfo->cate = $cate;
            }

            if(!empty($_REQUEST['rate']) && $_REQUEST['rate'][0] != ''){
                foreach($_REQUEST['rate'] as $key => $value){
                    $chouziinfo ->selectLimit .= ' AND find_in_set('.$value.',r.pm_rate)';
                }
            }

            // 按照星级倒序，之后按照创建id倒序
            $chouziinfo ->selectLimit .= " order by star desc, id desc";

            //$chouziinfo ->debugSql =true;
            $chouziinfo = $chouziinfo->get($this->dbhelper);
            $total = count($chouziinfo);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($chouziinfo, null, "/support/chouzi/index", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);
            $this->view->assign('chouzilist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/index.phtml");
            //echo $this->view->render("index/footer.phtml");
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

		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkSupport();
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
            $pm_chouzi ->selectLimit .= " order by id desc";
            $pm_chouzi ->department = $this->admininfo['admin_info']['department_id'];
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);

            // 获取可以申请使用的项目列表，并显示余额和项目信息
            $expenditurelistDAO = new pm_mg_chouziDAO();
            $expenditurelistDAO ->joinTable(" left join pm_mg_info as c on pm_mg_chouzi.pname=c.pm_name");
            $expenditurelistDAO ->selectField("
                     pm_mg_chouzi.*,
                     sum(c.zijin_daozheng_jiner) as shouru,
                     sum(c.shiyong_zhichu_jiner) as shiyong
                      ");

            $expenditurelistDAO ->selectLimit .= " and c.is_renling=1 and pm_mg_chouzi.department=".$this->admininfo['admin_info']['department_id'];
            $expenditurelistDAO = $expenditurelistDAO ->get($this->dbhelper);

            // 查看能申请使用的项目
            if(!empty($expenditurelistDAO)) {
                foreach($expenditurelistDAO as $key => $value){
                    if(!$this->checkfeedback($value['pname'], $value['id'])){
                        unset($expenditurelistDAO[$key]);
                    }
                    if(((float)$value['shouru'] - (float)$value['shiyong']) <= 0){
                        unset($expenditurelistDAO[$key]);
                    }
                }
            }
            //var_dump($expenditurelistDAO);
            $this->view->assign("expenditurelist",$expenditurelistDAO);

            // 项目进度
            $this->view->assign("rate_config",$this->rate_config);

            //获取筹资项目list
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
            $this->view->assign("chouzi_lists",$chouziDAO);
		}

        /**
         * 立项申请入口
         */
        public function applicationEntryAction()
        {
            $id = $_REQUEST['id'];
            if(!empty($id)){
                $_support_projectDAO = $this->orm->createDAO('_support_project');
                $_support_projectDAO ->findId($id);
                $_support_projectDAO = $_support_projectDAO->get();
                // 是否属于该管理员的项目
                if($_support_projectDAO['0']['uid'] != $this->admininfo['admin_info']['id']){
                    echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                    echo('<script language="JavaScript">');
                    echo("alert('您的操作有误!');");
                    echo "window.location.href='/support/index';";
                    echo('</script>');
                    exit;
                }

                $_support_project_logDAO = $this->orm->createDAO('_support_project_log');
                $_support_project_logDAO ->findMain_id($id);
                $_support_project_logDAO ->selectLimit .= ' ORDER BY lastmodify DESC';
                $_support_project_logDAO = $_support_project_logDAO->get();

                $this->view->assign("project_info",$_support_projectDAO);
                $this->view->assign("project_info_log",$_support_project_logDAO);

                // 获取最后一次审核失败的log记录
                $_support_project_log1DAO = $this->orm->createDAO('_support_project_log')->findMain_id($id)->findActive('shsb');
                $_support_project_log1DAO ->selectLimit .= ' ORDER BY lastmodify DESC LIMIT 0,1';
                $_support_project_log1DAO = $_support_project_log1DAO->get();
                $this->view->assign("shsb",$_support_project_log1DAO);

                // pdf审核失败
                $_support_project_log5DAO = $this->orm->createDAO('_support_project_log')->findMain_id($id)->findActive('pdfshsb');
                $_support_project_log5DAO ->selectLimit .= ' ORDER BY lastmodify DESC LIMIT 0,1';
                $_support_project_log5DAO = $_support_project_log5DAO->get();
                $this->view->assign("pdfshsb",$_support_project_log5DAO);
            }

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/application_entry.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function savestep1Action(){
            try{
                $this->orm->beginTran();
                $id = (int)$_REQUEST['id'];
                $pname = HttpUtil::postString("pname");
                $instruction = HttpUtil::postString("instruction");
                $_support_projectDAO = $this->orm->createDAO('_support_project');
                $_support_project_logDAO = $this->orm->createDAO('_support_project_log');

                if(!empty($id)){
                    $_support_projectDAO ->findId($id);
                    //$_support_project_logDAO ->findMain_id($id);
                }

                if($pname == "" || $_FILES['img']['name'] == ""){
                    echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                    echo('<script language="JavaScript">');
                    echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
                    echo('history.back();');
                    echo('</script>');
                    exit;
                }

                // 检查项目是否已经存在 从申请和现有项目两个维度进行校验
                $pm_mg_chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->findPname($pname)->get();
                if(!empty($pm_mg_chouziDAO)){
                    $this->alert_back("该项目已存在，或已在申请中！请核对后重新申请！");
                }
                $spDAO = $this->orm->createDAO("_support_project")->findP_name($pname)->get();
                if(!empty($spDAO) && (int)$spDAO[0]['id'] != $id){
                    $this->alert_back("该项目已存在！请核对后重新申请！");
                }

                // 项目不存在，生产申请项目的唯一id
                $_support_projectDAO ->uid = $this->admininfo['admin_info']['id'];
                $_support_projectDAO ->department_id = $this->admininfo['admin_info']['department_id'];
                $_support_projectDAO ->p_name = $pname;
                $_support_projectDAO ->lastmodify = time();
                $_support_projectDAO ->status = 1;
                $_support_projectDAO ->instruction = $instruction;

                $p_id = $_support_projectDAO ->save();

                if($p_id == "" && $id == ''){
                    echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                    echo('<script language="JavaScript">');
                    echo("alert('操作失败！！！！！');");
                    echo('history.back();');
                    echo('</script>');
                    exit;
                }

                if($id != ''){  // 重新编辑时赋值
                    $p_id = $id;
                }

                // 完善申请log表
                $_support_project_logDAO -> main_id = $p_id;
                $_support_project_logDAO -> lastmodify = time();
                $_support_project_logDAO -> desc = $instruction;
                $_support_project_logDAO -> username = $this->admininfo['admin_info']['username'];
                $_support_project_logDAO -> active = 'tjdzsq';

                if($_FILES['img']['name']!=""){
                    if($_FILES['img']['error'] != 4){
                        if(!is_dir(__UPLOADPICPATH__ ."jjh_project/".$this->admininfo['admin_info']['department_id']."/")){
                            mkdir(__UPLOADPICPATH__ ."jjh_project/".$this->admininfo['admin_info']['department_id']."/");
                        }
                        $uploadpic = new uploadPic($_FILES['img']['name'],$_FILES['img']['error'],$_FILES['img']['size'],$_FILES['img']['tmp_name'],$_FILES['img']['type'],2);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_project/".$this->admininfo['admin_info']['department_id']."/" ;
                        $result = $uploadpic->uploadPic();
                        if($result['error'] != 0){
                            echo "<script>alert('".$result['msg']."');";
                            echo "window.location.href='/support/chouzi/application-entry';";
                            echo "</script>";
                            exit();
                        }else{
                            $_support_project_logDAO->img =  __GETPICPATH__."jjh_project/".$this->admininfo['admin_info']['department_id']."/".$result['picname'];
                            //$_support_projectDAO->meeting_files_name = $_FILES['meeting_files']['name'];
                        }
                    }
                }

                $_support_project_logDAO->save();
                $this->orm->commit();
                echo "<script>";
                echo "window.location.href='/support/chouzi/application-entry?id=".$p_id."&step=2';";
                echo "</script>";
                exit();
            }catch(Exception $e){
                $this->orm->rollback();
                echo $e->getMessage();
                return false;exit();
            }
        }

        public function savestep3Action(){
            try{
                $this->orm->beginTran();
                $id = (int)$_REQUEST['id'];
                $instruction = HttpUtil::postString("instruction");
                $_support_projectDAO = $this->orm->createDAO('_support_project');
                $_support_project_logDAO = $this->orm->createDAO('_support_project_log');

                if(!empty($id)){
                    $_support_projectDAO ->findId($id);
                }else {
                    $this->alert_back('操作失败！');
                }

                if($_FILES['img']['name'] == ""){
                    $this->alert_back('请上传pdf文档！');
                }

                if($_FILES['img']['type'] != "application/pdf"){
                    $this->alert_back('上传文件格式必须为pdf文档！');
                }

                // 项目不存在，生产申请项目的唯一id
                $_support_projectDAO ->lastmodify = time();
                $_support_projectDAO ->status = 4;  // '4' => '签字盖章pdf文件待审核',
                $_support_projectDAO ->instruction = $instruction;

                $p_id = $_support_projectDAO ->save();

                if($p_id == "" && $id == ''){
                    $this->alert_back('操作失败！');
                }

                if($id != ''){  // 重新编辑时赋值
                    $p_id = $id;
                }

                // 完善申请log表
                $_support_project_logDAO -> main_id = $p_id;
                $_support_project_logDAO -> lastmodify = time();
                $_support_project_logDAO -> desc = $instruction;
                $_support_project_logDAO -> username = $this->admininfo['admin_info']['username'];
                $_support_project_logDAO -> active = 'tjpdf';

                if($_FILES['img']['name']!=""){
                    if($_FILES['img']['error'] != 4){
                        if(!is_dir(__UPLOADPICPATH__ ."jjh_project/".$this->admininfo['admin_info']['department_id']."/")){
                            mkdir(__UPLOADPICPATH__ ."jjh_project/".$this->admininfo['admin_info']['department_id']."/");
                        }
                        $uploadpic = new uploadPic($_FILES['img']['name'],$_FILES['img']['error'],$_FILES['img']['size'],$_FILES['img']['tmp_name'],$_FILES['img']['type'],2);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_project/".$this->admininfo['admin_info']['department_id']."/" ;
                        $result = $uploadpic->uploadPic();
                        if($result['error'] != 0){
                            echo "<script>alert('".$result['msg']."');";
                            echo "window.location.href='/support/chouzi/application-entry';";
                            echo "</script>";
                            exit();
                        }else{
                            $_support_project_logDAO->img =  __GETPICPATH__."jjh_project/".$this->admininfo['admin_info']['department_id']."/".$result['picname'];
                            //$_support_projectDAO->meeting_files_name = $_FILES['meeting_files']['name'];
                        }
                    }
                }

                $_support_project_logDAO->save();
                $this->orm->commit();
                $this->alert_go('操作成功！', '/support/chouzi/application-entry?id='.$p_id);
                exit();
            }catch(Exception $e){
                $this->orm->rollback();
                echo $e->getMessage();
                return false;exit();
            }
        }

        /**
         * 使用申请入口
         */
        public function expenditureAction()
        {
            $id = $_REQUEST['id'];
            if(!empty($id)){
                $_support_expenditureDAO = $this->orm->createDAO('_support_expenditure');
                $_support_expenditureDAO ->findId($id);
                $_support_expenditureDAO = $_support_expenditureDAO->get();
                // 是否属于该管理员的项目
                if($_support_expenditureDAO['0']['uid'] != $this->admininfo['admin_info']['id']){
                    echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                    echo('<script language="JavaScript">');
                    echo("alert('您的操作有误!');");
                    echo "window.location.href='/support/index';";
                    echo('</script>');
                    exit;
                }

                $_support_expenditure_logDAO = $this->orm->createDAO('_support_expenditure_log');
                $_support_expenditure_logDAO ->findMain_id($id);
                $_support_expenditure_logDAO ->selectLimit .= ' ORDER BY lastmodify DESC';
                $_support_expenditure_logDAO = $_support_expenditure_logDAO->get();

                $this->view->assign("expenditure_info",$_support_expenditureDAO);
                $this->view->assign("expenditure_info_log",$_support_expenditure_logDAO);

                // 获取最后一次审核失败的log记录
                $_support_project_log1DAO = $this->orm->createDAO('_support_project_log')->findMain_id($id)->findActive('shsb');
                $_support_project_log1DAO ->selectLimit .= ' ORDER BY lastmodify DESC LIMIT 0,1';
                $_support_project_log1DAO = $_support_project_log1DAO->get();
                $this->view->assign("shsb",$_support_project_log1DAO);

                // pdf审核失败
                $_support_project_log5DAO = $this->orm->createDAO('_support_project_log')->findMain_id($id)->findActive('pdfshsb');
                $_support_project_log5DAO ->selectLimit .= ' ORDER BY lastmodify DESC LIMIT 0,1';
                $_support_project_log5DAO = $_support_project_log5DAO->get();
                $this->view->assign("pdfshsb",$_support_project_log5DAO);
            }

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("chouzi/expenditure.phtml");
            //echo $this->view->render("index/footer.phtml");
        }

        public function savesteponeAction(){
            try{
                $this->orm->beginTran();
                $id = (int)$_REQUEST['id'];
                $pm_id = (int)$_REQUEST['pm_id'];

                if(!empty($pm_id)) {
                    $expenditurelistDAO = new pm_mg_chouziDAO();
                    $expenditurelistDAO ->joinTable(" left join pm_mg_info as c on pm_mg_chouzi.pname=c.pm_name");
                    $expenditurelistDAO ->selectField("
                     pm_mg_chouzi.*,
                     sum(c.zijin_daozheng_jiner) as shouru,
                     sum(c.shiyong_zhichu_jiner) as shiyong
                      ");

                    $expenditurelistDAO ->selectLimit .= " and c.is_renling=1 and pm_mg_chouzi.id=".$pm_id;
                    $expenditurelistDAO = $expenditurelistDAO ->get($this->dbhelper);

                    $result_array['rs']['yuer'] = (float)$expenditurelistDAO[0]['shouru']-(float)$expenditurelistDAO[0]['shiyong'];
                    $result_array['rs']['percent'] = $expenditurelistDAO[0]['percent'];
                    $result_array['rs']['shiyong'] = (int)($result_array['rs']['yuer'] * $result_array['rs']['percent'] / 100);
                }
                $pname = $expenditurelistDAO[0]['pname'];

                $jiner = HttpUtil::postString("jiner");
                if(!is_numeric($jiner))
                {
                    $this->alert_back("使用资金金额必须为数字！");
                    exit;
                }
                $instruction = HttpUtil::postString("instruction");
                $_support_expenditureDAO = $this->orm->createDAO('_support_expenditure');
                $_support_expenditure_logDAO = $this->orm->createDAO('_support_expenditure_log');

                if(!empty($id)){
                    $_support_expenditureDAO ->findId($id);
                }

                if($pm_id == "" || $_FILES['img']['name'] == "" || $jiner == "" || $pname == ""){
                    $this->alert_back("您输入的信息不完整，请查正后继续添加！");
                    exit;
                }

                // 检查项目是否有余额，并且已经完成资金使用反馈 todo
                if(($result_array['rs']['shiyong'] - $jiner) < 0){
                    $this->alert_back("申请资金使用金额不能超出限额：".$result_array['rs']['shiyong']."元");
                    exit;
                }

                // 项目不存在，生产申请项目的唯一id
                $_support_expenditureDAO ->uid = $this->admininfo['admin_info']['id'];
                $_support_expenditureDAO ->department_id = $this->admininfo['admin_info']['department_id'];
                $_support_expenditureDAO ->p_id = $pm_id;
                $_support_expenditureDAO ->p_name = $pname;
                $_support_expenditureDAO ->lastmodify = time();
                $_support_expenditureDAO ->status = 1;
                $_support_expenditureDAO ->jiner = $jiner;
                $_support_expenditureDAO ->instruction = $instruction;

                $p_id = $_support_expenditureDAO ->save();

                if($p_id == "" && $id == ''){
                    echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                    echo('<script language="JavaScript">');
                    echo("alert('操作失败！！！！！');");
                    echo('history.back();');
                    echo('</script>');
                    exit;
                }

                if($id   != ''){  // 重新编辑时赋值
                    $p_id = $id;
                }

                // 完善申请log表
                $_support_expenditure_logDAO -> main_id = $p_id;
                $_support_expenditure_logDAO -> lastmodify = time();
                $_support_expenditure_logDAO -> desc = $instruction;
                $_support_expenditure_logDAO -> username = $this->admininfo['admin_info']['username'];
                $_support_expenditure_logDAO -> active = 'tjsysq';

                if($_FILES['img']['name']!=""){
                    if($_FILES['img']['error'] != 4){
                        if(!is_dir(__UPLOADPICPATH__ ."jjh_project/".$this->admininfo['admin_info']['department_id']."/")){
                            mkdir(__UPLOADPICPATH__ ."jjh_project/".$this->admininfo['admin_info']['department_id']."/");
                        }
                        $uploadpic = new uploadPic($_FILES['img']['name'],$_FILES['img']['error'],$_FILES['img']['size'],$_FILES['img']['tmp_name'],$_FILES['img']['type'],2);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_project/".$this->admininfo['admin_info']['department_id']."/" ;
                        $result = $uploadpic->uploadPic();
                        if($result['error'] != 0){
                            echo "<script>alert('".$result['msg']."');";
                            echo "window.location.href='/support/chouzi/expenditure';";
                            echo "</script>";
                            exit();
                        }else{
                            $_support_expenditure_logDAO->img =  __GETPICPATH__."jjh_project/".$this->admininfo['admin_info']['department_id']."/".$result['picname'];
                            //$_support_projectDAO->meeting_files_name = $_FILES['meeting_files']['name'];
                        }
                    }
                }

                $_support_expenditure_logDAO->save();
                $this->orm->commit();
                echo "<script>";
                echo "window.location.href='/support/chouzi/expenditure?id=".$p_id."&step=2';";
                echo "</script>";
                exit();
            }catch(Exception $e){
                $this->orm->rollback();
                echo $e->getMessage();
                return false;exit();
            }
        }

        public function savestepthreeAction(){
            try{
                $this->orm->beginTran();
                $id = (int)$_REQUEST['id'];
                $instruction = HttpUtil::postString("instruction");
                $_support_expenditureDAO = $this->orm->createDAO('_support_expenditure');
                $_support_expenditure_logDAO = $this->orm->createDAO('_support_expenditure_log');

                if(!empty($id)){
                    $_support_expenditureDAO ->findId($id);
                }else {
                    $this->alert_back('操作失败！');
                }

                if($_FILES['img']['name'] == ""){
                    $this->alert_back('请上传pdf文档！');
                }

                if($_FILES['img']['type'] != "application/pdf"){
                    $this->alert_back('上传文件格式必须为pdf文档！');
                }

                // 项目不存在，生产申请项目的唯一id
                $_support_expenditureDAO ->lastmodify = time();
                $_support_expenditureDAO ->status = 4;  // '4' => '签字盖章pdf文件待审核',
                $_support_expenditureDAO ->instruction = $instruction;

                $p_id = $_support_expenditureDAO ->save();

                if($p_id == "" && $id == ''){
                    $this->alert_back('操作失败！');
                }

                if($id != ''){  // 重新编辑时赋值
                    $p_id = $id;
                }

                // 完善申请log表
                $_support_expenditure_logDAO -> main_id = $p_id;
                $_support_expenditure_logDAO -> lastmodify = time();
                $_support_expenditure_logDAO -> desc = $instruction;
                $_support_expenditure_logDAO -> username = $this->admininfo['admin_info']['username'];
                $_support_expenditure_logDAO -> active = 'tjpdf';

                if($_FILES['img']['name']!=""){
                    if($_FILES['img']['error'] != 4){
                        if(!is_dir(__UPLOADPICPATH__ ."jjh_project/".$this->admininfo['admin_info']['department_id']."/")){
                            mkdir(__UPLOADPICPATH__ ."jjh_project/".$this->admininfo['admin_info']['department_id']."/");
                        }
                        $uploadpic = new uploadPic($_FILES['img']['name'],$_FILES['img']['error'],$_FILES['img']['size'],$_FILES['img']['tmp_name'],$_FILES['img']['type'],2);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_project/".$this->admininfo['admin_info']['department_id']."/" ;
                        $result = $uploadpic->uploadPic();
                        if($result['error'] != 0){
                            echo "<script>alert('".$result['msg']."');";
                            echo "window.location.href='/support/chouzi/application-entry';";
                            echo "</script>";
                            exit();
                        }else{
                            $_support_expenditure_logDAO->img =  __GETPICPATH__."jjh_project/".$this->admininfo['admin_info']['department_id']."/".$result['picname'];
                            //$_support_projectDAO->meeting_files_name = $_FILES['meeting_files']['name'];
                        }
                    }
                }

                $_support_expenditure_logDAO->save();
                $this->orm->commit();
                $this->alert_go('操作成功！', '/support/chouzi/expenditure?id='.$p_id);
                exit();
            }catch(Exception $e){
                $this->orm->rollback();
                echo $e->getMessage();
                return false;exit();
            }
        }

        /**
         * @param $pname
         * @param $pid
         * @return bool
         * check是否已经填写反馈情况
         * pm_mg_support_feedback表，support_id,lastmodify,pid,pname,expenditure_id,files_info,text_info,status 0,1
         */
        public function checkfeedback($pname, $pid)
        {
            if(empty($pname) || empty($pid)){
                // $this->alert_back('操作失败！');
                return false;
            }
            $pm_mg_infoDAO = $this->orm->createDAO('pm_mg_info')->findPm_name($pname)->findCate_id(1)->findIs_renling(1)->get();
            $feedbackDAO = $this->orm->createDAO('pm_mg_support_feedback')->findPid($pid)->findStatus(1)->get();

            if((count($pm_mg_infoDAO) - count($feedbackDAO))  <=  1){
                return ture;
            }else {
                return false;
            }
        }

        public function ajaxpinfoAction(){
            if(empty((int)$_REQUEST['pid'])) {
                echo json_encode(array('error'=>'error'));
                exit();
            }
            $id = (int)$_REQUEST['pid'];
            // 获取可以申请使用的项目列表，并显示余额和项目信息
            $expenditurelistDAO = new pm_mg_chouziDAO();
            $expenditurelistDAO ->joinTable(" left join pm_mg_info as c on pm_mg_chouzi.pname=c.pm_name");
            $expenditurelistDAO ->selectField("
                     pm_mg_chouzi.*,
                     sum(c.zijin_daozheng_jiner) as shouru,
                     sum(c.shiyong_zhichu_jiner) as shiyong
                      ");

            $expenditurelistDAO ->selectLimit .= " and c.is_renling=1 and pm_mg_chouzi.id=".$id;
            $expenditurelistDAO = $expenditurelistDAO ->get($this->dbhelper);

            $result_array['rs']['yuer'] = (float)$expenditurelistDAO[0]['shouru']-(float)$expenditurelistDAO[0]['shiyong'];
            $result_array['rs']['percent'] = $expenditurelistDAO[0]['percent'];
            $result_array['rs']['shiyong'] = (int)($result_array['rs']['yuer'] * $result_array['rs']['percent'] / 100);

            echo json_encode($result_array);
            exit();
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

                // 项目意见反馈
                $_support_feedbackDAO = $this->orm->createDAO("_support_feedback")->findPm_id($pm_mg_chouziDAO[0]['id']);
                $_support_feedbackDAO ->selectLimit .= " ORDER BY datetimes desc ";
                $_support_feedbackDAO = $_support_feedbackDAO->get();
                $userlist = $this ->orm->createDAO("_support_college_user")->get();

                $_user_list = array();
                if(!empty($userlist)){
                    foreach($userlist as $key => $value){
                        $_user_list[$value['id']] = $value['username'];
                    }
                }

                $this->view->assign("userlist", $_user_list);
                $this->view->assign("support_feedbackDAO", $_support_feedbackDAO);

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

        public function tosavefeedbackAction()
        {
            if(empty($_REQUEST['contents']) || empty($_REQUEST['id'])){
                $this->alert_back('内容不能为空！');
            }else {
                $content =  htmlspecialchars($_POST['contents']);
                $_support_feedbackDAO = $this->orm->createDAO('_support_feedback');
                $_support_feedbackDAO ->content = $content;
                $_support_feedbackDAO ->pm_id = $_REQUEST['id'];
                $_support_feedbackDAO ->user_id = $this->admininfo['admin_info']['id'];
                $_support_feedbackDAO ->datetimes = time();
                $_support_feedbackDAO ->save();
                $this->alert_go('提交成功！','/support/chouzi/repminfo?id='.$_REQUEST['id']);
            }
        }

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'ajaxaddstar',
                'application-entry',
                'savestep1',
                'savestep3',
                'pminfo',
                'expenditure',
                'savestepone',
                'savestepthree',
                'ajaxpinfo',
                'repminfo',
                'tosavefeedback'
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}
?>