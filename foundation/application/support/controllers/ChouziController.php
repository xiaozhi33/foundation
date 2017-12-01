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

            // 按照星级倒序，之后按照创建id倒序
            $chouziinfo ->selectLimit .= " order by star desc, id desc";

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
            //echo $this->view->render("index/footer.phtml");
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
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);

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

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'ajaxaddstar',
                'application-entry',
                'savestep1',
                'savestep3',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}
?>