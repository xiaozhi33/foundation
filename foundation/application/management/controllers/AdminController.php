<?php
	require_once("BaseController.php");
	class Management_adminController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$adminlist =  $this->orm->createDAO("my_admin");
			$adminlist = $adminlist ->get($this->dbhelper);
			$this->view->assign("adminlist",$adminlist);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('admin/index.phtml');
			echo $this->view->render("index/footer.phtml");
		}

		public function addAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('admin/add.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrsAction(){
			$isadmin = $this->isadminAction($_REQUEST['name']);
			if(count($isadmin) != 0){
				alert_back("该用户名已经存在。");
			}
			
			if($_REQUEST['name'] != "" && $_REQUEST['pwd'] != "" && $_REQUEST['gid'] != ""){
				$adminlist = $this->orm->createDAO("my_admin");
				$adminlist ->admin_name = $_REQUEST['name'];
				$adminlist ->admin_pwd = substr(md5(serialize($_REQUEST['pwd'])), 0, 32);
				$adminlist ->gid = $_REQUEST['gid'];
				$adminlist ->save($this->dbhelper);
				alert_go("添加成功。","/management/admin");
			}else{
				alert_back("请输入详细信息。");
			}
		}
		
		//判断该用户是否存在
		public function isadminAction($name){
			$adminlist = $this->orm->createDAO("my_admin");
			$adminlist ->findAdmin_name($name);
			$isadmin = $adminlist->get($this->dbhelper);
			return $isadmin;
		}
		
		public function delAction(){
			if($_REQUEST['id'] != ""){
				$adminlist = new my_adminDAO($_REQUEST['id']);
				$adminlist->del($this->dbhelper);
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('删除成功');");
                echo("location.href='/management/admin';");
                echo('</script>');
                exit;
			}else{
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('删除失败');");
                echo("location.href='/management/admin';");
                echo('</script>');
                exit;
			}
		}
		
		public function editAction(){
			if($_REQUEST['id'] != ""){
				$adminlist =  $this->orm->createDAO("my_admin")->findId($_REQUEST['id']);
				$adminlist = $adminlist->get($this->dbhelper);
				$this->view->assign("adminlist",$adminlist);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render('admin/edit.phtml');
				echo $this->view->render("index/footer.phtml");
			}else{
				alert_back("错误操作");
			}
		}
		
		public function editrsAction(){
			if($_REQUEST['id'] != ""){
				$adminlist =  $this->orm->createDAO("my_admin")->findId($_REQUEST['id']);
				$adminlist ->admin_name = $_REQUEST['name'];
				$adminlist ->gid = $_REQUEST['gid'];
				$adminlist->save($this->dbhelper);
				alert_go("修改成功。","/management/admin");
			}else{
				alert_back("错误操作");
			}
		}

		//所属部门管理
		public function adddepartmentAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/adddepartment.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrsdepartmentAction(){
            try{
                if($_REQUEST['name'] != "" ){
                    // 同步财务系统部门信息
                    $deparmentlDAO = new CW_API();
                    $rs1 = $deparmentlDAO ->get_max_departmentID();
                    $bmbh = (int)$rs1[0]['bmbh'] + 1;
                    $zwbmzdlDAO = new CW_API();
                    $rs = $zwbmzdlDAO ->sync_department($bmbh, $_REQUEST['name']);
					$rs = true;
                    if($rs){
                        $departmentinfo = new jjh_mg_departmentDAO();
                        $departmentinfo ->pname = $_REQUEST['name'];
                        $pid = $departmentinfo->save($this->dbhelper);

                        // 写入对照表
                        $zw_department_relatedDAO = $this->orm->createDAO("zw_department_related");
                        $zw_department_relatedDAO ->pm_pid = $pid;
                        $zw_department_relatedDAO ->pm_pname = $_REQUEST['name'];
                        $zw_department_relatedDAO ->zw_bmbh = $bmbh;
                        $zw_department_relatedDAO ->zw_bmmc = $_REQUEST['name'];
                        $zw_department_relatedDAO ->save();

                        alert_go("添加成功！", "/management/admin/department");
                    }else {
                        alert_back("添加同步财务系统失败！");
                    }
                }else {
                    alert_back("添加失败");
                }
            }catch (Exception $e){
                throw $e;
            }
		}
		
		public function editdepartmentAction(){
			if($_REQUEST['id'] != ""){
				$departmentinfo = new jjh_mg_departmentDAO($_REQUEST['id']);
				$departmentinfo = $departmentinfo->get($this->dbhelper);
				$this->view->assign("departmentinfo",$departmentinfo);
				
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("admin/editdepartment.phtml");
				echo $this->view->render("index/footer.phtml");
			}else {
				alert_back("操作失败");
			}
		}
		
		public function editrsdepartmentAction(){
			if($_REQUEST['name'] != "" && $_REQUEST['id']){
				$departmentinfo = new jjh_mg_departmentDAO($_REQUEST['id']);
				$departmentinfo ->pname = $_REQUEST['name'];
				$departmentinfo->save($this->dbhelper);
				alert_go("部门编辑成功。","/management/admin/department");
			}else {
				alert_back("添加失败");
			}
		}
		
		public function departmentAction(){
			$departmentinfo = new jjh_mg_departmentDAO();
			$departmentinfo = $departmentinfo->get($this->dbhelper);
			
			$total = count($departmentinfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($departmentinfo,null,"department",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('departmentlist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/department.phtml");
			echo $this->view->render("index/footer.phtml");
		}

		/*public function deldepartmentAction(){
			(int)$id = HttpUtil::getString("id");
			if(!empty($id)){
				$jjh_mg_departmentDAO = $this->orm->createDAO("jjh_mg_department");
				$jjh_mg_departmentDAO ->findId($id);
				$jjh_mg_departmentDAO ->delete();

				$zw_department_relatedDAO = $this->orm->createDAO("zw_department_related");
				$zw_department_relatedDAO ->findId($id);
				$zw_department_relatedDAO ->delete();

				echo "<script>alert('删除成功！');";
				echo "window.location.href='/management/admin/department'";
				echo "</script>";
				exit();
			}
		}*/
		
		//项目分类管理
		public function addcateAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/addcate.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrscateAction(){
			if($_REQUEST['name'] != ""){
				$cateinfo = new jjh_mg_cateDAO();
				$cateinfo ->catename = $_REQUEST['name'];
				$cateinfo->save($this->dbhelper);
				alert_go("项目分类添加成功。","/management/admin/cate");
			}else {
				alert_back("添加失败");
			}
		}
		
		public function editcateAction(){
			if($_REQUEST['id'] != ""){
				$departmentinfo = new jjh_mg_departmentDAO($_REQUEST['id']);
				$departmentinfo = $departmentinfo->get($this->dbhelper);
				$this->view->assign("departmentinfo",$departmentinfo);
				
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("admin/editcate.phtml");
				echo $this->view->render("index/footer.phtml");
			}else {
				alert_back("操作失败");
			}
		}
		
		public function editrscateAction(){
			if($_REQUEST['name'] != "" && $_REQUEST['id']){
				$cateinfo = new jjh_mg_cateDAO($_REQUEST['id']);
				$cateinfo ->catename = $_REQUEST['name'];
				$cateinfo->save($this->dbhelper);
				alert_go("部门编辑成功。","/management/admin/cate");
			}else {
				alert_back("添加失败");
			}
		}
		
		public function cateAction(){
			$cateinfo = new jjh_mg_cateDAO();
			$cateinfo = $cateinfo->get($this->dbhelper);
			
			$total = count($cateinfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($cateinfo,null,"cate",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('catelist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/cate.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function editpwdAction(){
			$my_admin = new my_adminDAO();
			$my_admin = $my_admin ->get($this->dbhelper);
			
			$this->view->assign("adminlist",$my_admin);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/editpwd.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function editrspwdAction(){
			$name = $_REQUEST['admin_id'];
			$pwd = $_REQUEST['pwd'];
			
			if($name !="" && $pwd != ""){
				$my_admin = new my_adminDAO($name);
				$my_admin ->admin_pwd = substr(md5(serialize($pwd)), 0, 32);
				$my_admin ->save($this->dbhelper);
				alert_go("密码修改成功","/management/admin/editpwd");
			}else{
				alert_back("请输入管理员名称或密码");
			}
		}

		////////////////////////////////////////////////////////////////
		/**
		 * 捐赠方参加学校活动列表
		 */
		public function pphuodongAction()
		{
			$pp_id = $_REQUEST['pp_id'];
			$pp_id = (int)$pp_id;
			if(!empty($pp_id))
			{
				$pm_mg_info_activeDAO = $this->orm->createDAO("pm_mg_info_active");
				$pm_mg_info_activeDAO ->findPp_id($pp_id);
				$pm_mg_info_activeDAO = $pm_mg_info_activeDAO ->get();

				$ppinfo = $this->getppbyppid($pp_id);

				if(!empty($ppinfo)){
					$this->view->assign("pp_info",$ppinfo[0]);
				}
				$this->view->assign("huodong_list",$pm_mg_info_activeDAO);
				$this->view->assign("pp_id",$pp_id);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("admin/pphuodong.phtml");
				echo $this->view->render("index/footer.phtml");
			}
		}

		/**
		 * 添加捐赠方参加学校活动情况
		 */
		public function addpphuodongAction()
		{
			$pp_id = $_REQUEST['pp_id'];
			$pp_id = (int)$pp_id;
			if(!empty($pp_id)) {
				$pm_mg_info_activeDAO = $this->orm->createDAO("pm_mg_info_active");
				$pm_mg_info_activeDAO->findPp_id($pp_id);
				$pm_mg_info_activeDAO = $pm_mg_info_activeDAO->get();
				$this->view->assign("pphuodong", $pm_mg_info_activeDAO);
			}
			$this->view->assign("pp_id", $pp_id);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/addpphuodong.phtml");
			echo $this->view->render("index/footer.phtml");
		}

		/**
		 * 编辑捐赠方参加学校活动
		 */
		public function editpphuodongAction()
		{
			$id = (int)$_REQUEST['id'];
			if(!empty($id)) {
				$pm_mg_info_activeDAO = $this->orm->createDAO("pm_mg_info_active");
				$pm_mg_info_activeDAO->findId($id);
				$pm_mg_info_activeDAO = $pm_mg_info_activeDAO->get();
				$this->view->assign("pphuodong", $pm_mg_info_activeDAO);
			}
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/ppedithuodong.phtml");
			echo $this->view->render("index/footer.phtml");
		}

		public function savepphuodongAction()
		{
			$id = (int)$_REQUEST['id'];
			$pp_id = $_REQUEST['pp_id'];
			$pp_id = (int)$pp_id;
			$pm_mg_info_activeDAO = $this->orm->createDAO("pm_mg_info_active");

			$a_date_time = HttpUtil::postString("a_date_time");
			$a_content = HttpUtil::postString("a_content");
			$a_contact_person = HttpUtil::postString("a_contact_person");
			$a_gift = HttpUtil::postString("a_gift");
			if($a_date_time == "" || $a_content== "" || $a_contact_person == "" || $a_gift == "")
			{
				alert_back("信息不全，请查看信息的完整性，并重新提交。");
			}

			if(!empty($id)) {
				$pm_mg_info_activeDAO->findid($id);
			}
			$pm_mg_info_activeDAO ->pp_id = $pp_id;
			$pm_mg_info_activeDAO ->a_date_time = $a_date_time;
			$pm_mg_info_activeDAO ->a_content = $a_content;
			$pm_mg_info_activeDAO ->a_contact_person = $a_contact_person;
			$pm_mg_info_activeDAO ->a_gift = $a_gift;

			$pm_mg_info_activeDAO ->save();
			alert_go("活动信息添加成功。","pphuodong?pp_id=".$pp_id);
		}

		public function delpphuodongAction()
		{
			$id = (int)$_REQUEST['id'];
			$pp_id = (int)$_REQUEST['pp_id'];
			if(empty($id)) {
				alert_back("操作失败。");
				$this->_redirect("/management/admin/pphuodong?pp_id=".$pp_id);
			}
			$pm_mg_info_activeDAO = $this->orm->createDAO("pm_mg_info_active");
			$pm_mg_info_activeDAO->findid($id);
			$pm_mg_info_activeDAO->delete();

			$this->_redirect("/management/admin/pphuodong?pp_id=".$pp_id);
		}

		////////////////////////////////////////////////////////////////
		/**
		 * 捐赠方拜访情况
		 */
		public function ppvisitAction()
		{
			$pp_id = $_REQUEST['pp_id'];
			$pp_id = (int)$pp_id;
			if(!empty($pp_id))
			{
				$pm_mg_visit_donorDAO = $this->orm->createDAO("pm_mg_visit_donor");
				$pm_mg_visit_donorDAO ->findPp_id($pp_id);
				$pm_mg_visit_donorDAO = $pm_mg_visit_donorDAO ->get();

				$ppinfo = $this->getppbyppid($pp_id);

				if(!empty($ppinfo)){
					$this->view->assign("pp_info",$ppinfo[0]);
				}
				$this->view->assign("visit_list",$pm_mg_visit_donorDAO);
				$this->view->assign("pp_id",$pp_id);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("admin/ppvisit.phtml");
				echo $this->view->render("index/footer.phtml");
			}
		}

		/**
		 * 添加捐赠方拜访情况
		 */
		public function addppvisitAction()
		{
			$pp_id = $_REQUEST['pp_id'];
			$pp_id = (int)$pp_id;
			if(!empty($pp_id)) {
				$pm_mg_visit_donorDAO = $this->orm->createDAO("pm_mg_visit_donor");
				$pm_mg_visit_donorDAO->findPp_id($pp_id);
				$pm_mg_visit_donorDAO = $pm_mg_visit_donorDAO->get();
				$this->view->assign("pphuodong", $pm_mg_visit_donorDAO);
			}
			$this->view->assign("pp_id", $pp_id);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/addppvisit.phtml");
			echo $this->view->render("index/footer.phtml");
		}

		/**
		 * 编辑捐赠方参加学校活动
		 */
		public function editppvisitAction()
		{
			$id = (int)$_REQUEST['id'];
			if(!empty($id)) {
				$pm_mg_visit_donorDAO = $this->orm->createDAO("pm_mg_visit_donor");
				$pm_mg_visit_donorDAO->findId($id);
				$pm_mg_visit_donorDAO = $pm_mg_visit_donorDAO->get();
				$this->view->assign("ppvisit", $pm_mg_visit_donorDAO);
			}
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/ppeditvisit.phtml");
			echo $this->view->render("index/footer.phtml");
		}

		public function saveppvisitAction()
		{
			$id = (int)$_REQUEST['id'];
			$pp_id = $_REQUEST['pp_id'];
			$pp_id = (int)$pp_id;
			$pm_mg_visit_donorDAO = $this->orm->createDAO("pm_mg_visit_donor");

			$visit_date_time = HttpUtil::postString("visit_date_time");
			$visit_addr = HttpUtil::postString("visit_addr");
			$visit_s_person = HttpUtil::postString("visit_s_person");
			$visit_orther_person = HttpUtil::postString("visit_orther_person");
			$visit_gift = HttpUtil::postString("visit_gift");
			if($visit_date_time == "" || $visit_addr== "" || $visit_s_person == "" || $visit_orther_person == "")
			{
				alert_back("信息不全，请查看信息的完整性，并重新提交。");
			}

			if(!empty($id)) {
				$pm_mg_visit_donorDAO->findid($id);
			}
			$pm_mg_visit_donorDAO ->pp_id = $pp_id;
			$pm_mg_visit_donorDAO ->visit_date_time = $visit_date_time;
			$pm_mg_visit_donorDAO ->visit_addr = $visit_addr;
			$pm_mg_visit_donorDAO ->visit_s_person = $visit_s_person;
			$pm_mg_visit_donorDAO ->visit_orther_person = $visit_orther_person;
			$pm_mg_visit_donorDAO ->visit_gift = $visit_gift;

			$pm_mg_visit_donorDAO ->save();
			alert_go("拜访信息添加成功。","ppvisit?pp_id=".$pp_id);
		}

		public function delppvisitAction()
		{
			$id = (int)$_REQUEST['id'];
			$pp_id = (int)$_REQUEST['pp_id'];
			if(empty($id)) {
				alert_back("操作失败。");
				$this->_redirect("/management/admin/ppvisit?pp_id=".$pp_id);
			}
			$pm_mg_visit_donorDAO = $this->orm->createDAO("pm_mg_visit_donor");
			$pm_mg_visit_donorDAO->findid($id);
			$pm_mg_visit_donorDAO->delete();

			$this->_redirect("/management/admin/ppvisit?pp_id=".$pp_id);
		}

        public function getppbyppid($pp_id)
        {
            if(!empty($pp_id)){
                $jjh_mg_ppDAO = $this->orm->createDAO("jjh_mg_pp");
                $jjh_mg_ppDAO->findPid($pp_id);
                $jjh_mg_ppDAO = $jjh_mg_ppDAO->get();
                return $jjh_mg_ppDAO;
            }else {
                return false;
            }
        }

        /**
         * 人立方 -
         */
        public function relationAction(){
            if($_REQUEST['id'] != ""){
                $ppinfo = new jjh_mg_ppDAO($_REQUEST['id']);
                $ppinfo = $ppinfo->get($this->dbhelper);

                $meeting_pp_companyDAO = $this->orm->createDAO('jjh_mg_pp_company');
                $meeting_pp_companyDAO ->findPp_id($ppinfo[0]['pid']);
                $meeting_pp_companyDAO = $meeting_pp_companyDAO->get();

                $pm_mg_visit_donorDAO = $this->orm->createDAO('pm_mg_visit_donor');
                $pm_mg_visit_donorDAO ->findPp_id($_REQUEST['id']);
                $pm_mg_visit_donorDAO = $pm_mg_visit_donorDAO->get();
                $this->view->assign("pp_visit_donor",$pm_mg_visit_donorDAO);

                $this->view->assign("pp_company_list",$meeting_pp_companyDAO);
                $this->view->assign("ppinfo",$ppinfo);

                // 项目
                $pm_mg_infoDAO = $this->orm->createDAO('pm_mg_info');
                $pm_mg_infoDAO ->findPm_name($ppinfo[0]['pp_pm_id']);
                $pm_mg_infoDAO ->selectLimit .=  " AND zijin_daozheng_jiner!='' AND pm_pp != ''";
                $pm_mg_infoDAO ->selectLimit .=  " limit 0,40";
                $pm_mg_infoDAO = $pm_mg_infoDAO ->get();
                $this->view->assign("pm_mg_infoDAO",$pm_mg_infoDAO);

                // 活动
                $pm_mg_info_activeDAO = $this->orm->createDAO('pm_mg_info_active');
                $pm_mg_info_activeDAO ->findPp_id($_REQUEST['id']);
                $pm_mg_info_activeDAO ->selectLimit .=  " order by id desc limit 0,10";
                $pm_mg_info_activeDAO = $pm_mg_info_activeDAO ->get();
                $this->view->assign("pm_mg_info_activeDAO",$pm_mg_info_activeDAO);

                // 同部门 同分类的其他项目
                $pm_mg_info_1DAO = $this->orm->createDAO('pm_mg_info');
                $pm_mg_info_1DAO ->findCate($pm_mg_infoDAO[0]['cate']);
                $pm_mg_info_1DAO ->findDepartment($pm_mg_infoDAO[0]['dpartment']);
                $pm_mg_info_1DAO ->selectLimit .=  " order by id desc limit 0,10";
                $pm_mg_info_1DAO = $pm_mg_info_1DAO ->get();
                $this->view->assign("pm_mg_info_1DAO",$pm_mg_info_1DAO);

                $juanzengfang = array();
                if(count($pm_mg_infoDAO) > 1){
                    foreach($pm_mg_infoDAO as $key => $value){
                        $juanzengfang[] = $value['pm_pp'];
                    }
                }

                $juanzengfang = array_unique($juanzengfang);
                $this->view->assign("juanzengfang",$juanzengfang);

                echo $this->view->render("index/header.phtml");
                echo $this->view->render("admin/relation.phtml");
                echo $this->view->render("index/footer.phtml");
            }else {
                alert_back("操作失败");
            }
        }

        public function syncjzfAction(){
            // 读取项目info中所有实际捐赠方信息
            $pmDAO = $this->orm->createDAO("pm_mg_info");
            /**
             * pp_jzf_cate 基金会/企业/校友/社会人士
             * pp_jzf_attr1 0 1 是否校友
             * pp_jzf_attr2 境外 境内
            */
            $pmDAO ->select("pm_pp,
                                pm_pp_cate AS pp_jzf_cate,
                                pm_is_school AS pp_jzf_attr1,
                                zijin_laiyuan_qudao AS pp_jzf_attr2,
                                pm_name");
            $pmDAO ->selectLimit .= " AND pm_pp != '' AND pm_pp != '11' AND pm_pp != '123123' AND pm_pp != '33' AND pm_pp != '77' AND pm_pp != '85' AND pm_name != ''GROUP BY pm_pp ";
            //$pmDAO ->selectLimit .= " AND pm_pp != '' AND pm_pp != '11' AND pm_pp != '123123' AND pm_pp != '33' AND pm_pp != '77' AND pm_pp != '85' AND pm_name != '' ";
            $pmDAO = $pmDAO->get();

            if(!empty($pmDAO)){
                foreach($pmDAO as $key => $value){
                    /**
                     * 此操作将过滤捐赠方名称和项目名称相同并已存在的捐赠人信息。
                     * 如果同一捐赠人对应不同项目，则添加多条项目捐赠人信息到人员管理表中。
                     */
                    if($this->isnot_pp($value['pm_pp'], $value['pm_name'])){
                        $ppDAO =  $this->orm->createDAO("jjh_mg_pp");
                        $ppDAO ->pp_cate = '实际捐赠方';
                        $ppDAO ->ppname = $value['pm_pp'];
                        $ppDAO ->pp_pm_id = $value['pm_name'];
                        $ppDAO ->pp_jzf_cate = $value['pp_jzf_cate'];
                        if($value['pp_jzf_attr1'] == 0){
                            $ppDAO ->pp_jzf_attr1 = '非校友';
                        }else{
                            $ppDAO ->pp_jzf_attr1 = '校友';
                        }
                        if($value['pp_jzf_attr2'] == '境内' || $value['pp_jzf_attr2'] == ''){
                            $ppDAO ->pp_jzf_attr2 = '海内';
                        }else{
                            $ppDAO ->pp_jzf_attr2 = '海外';
                        }
                        $ppDAO ->save();
                        unset($ppDAO);
                    }
                }
            }

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('同步已完成');");
            echo("location.href='/management/admin/pp?pp_cate=捐赠方';");
            echo('</script>');
            exit;
        }

        public function isnot_pp($ppname, $pname){
            $ppDAO =  $this->orm->createDAO("jjh_mg_pp");
            $ppDAO ->findPp_pm_id($pname);
            $rs = $ppDAO ->findPpname($ppname)->get();
            if(empty($rs)){
                return true;
            }else {
                return false;
            }
        }

        public function ajaxaddppAction(){

            $pp = $this->orm->createDAO('jjh_mg_pp')->findPpname($_REQUEST['ppname'])->get();
            if(!empty($pp)){
                echo json_encode(array('status'=>'error','message'=>'姓名／名称已经添加，请查证后再添加！'));
                exit();
            }

            $ppinfo = $this->orm->createDAO('jjh_mg_pp');
            $ppinfo ->ppname = $_REQUEST['ppname'];
            $ppinfo ->pp_address = $_REQUEST['pp_address'];
            $ppinfo ->pp_beizhu = $_REQUEST['pp_beizhu'];
            $ppinfo ->pp_cate = $_REQUEST['pp_cate'];
            $ppinfo ->pp_msn = $_REQUEST['pp_msn'];
            $ppinfo ->pp_pm_id = $_REQUEST['pp_pm_id'];

            $ppinfo ->pp_jzf_cate = $_REQUEST['pp_jzf_cate'];
            $ppinfo ->pp_jzf_attr1 = $_REQUEST['pp_jzf_attr1'];
            $ppinfo ->pp_jzf_attr2 = $_REQUEST['pp_jzf_attr2'];
            $ppinfo ->pp_syf_cate = $_REQUEST['pp_syf_cate'];
            $ppinfo ->pp_yuf_cate = $_REQUEST['pp_yuf_cate'];


            $ppinfo ->pp_qq = $_REQUEST['pp_qq'];
            $ppinfo ->ppemail = $_REQUEST['ppemail'];
            $ppinfo ->ppmobile = $_REQUEST['ppmobile'];
            $ppinfo ->ppphone = $_REQUEST['ppphone'];

            if(empty($_REQUEST['ppname'])){
                echo json_encode(array('status'=>'error','message'=>'姓名／名称不能为空'));
                exit();
            }
            $pid = $ppinfo ->save();
            echo json_encode(array('status'=>'success','message'=>'添加成功','pid'=>$pid,'ppname'=>$ppinfo['ppname']));
            exit();
        }

        // 管理组 角色
        public function admingroupAction(){
            $admingrpouplist = $this->orm->createDAO("admingroup")->get();
            $total = count($admingrpouplist);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO ->pageHelper($admingrpouplist,null,"admingrouplist",null,'get',20,20);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php","",$pages);
            $this->view->assign('admingrpouplist',$pageDAO['pageData']);
            $this->view->assign('page',$pages);
            $this->view->assign('total',$total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render('admin/admingroup.phtml');
            echo $this->view->render("index/footer.phtml");
        }

        public function addadmingroupAction(){
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("admin/addadmingroup.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function toAddadmingroupAction(){
            $gid = $_REQUEST['gid'];
            $admingroupDAO = $this->orm->createDAO('admingroup');
            $gname = $_REQUEST['gname'];

            if($gname == ''){
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }

            $admingroupDAO ->gname = $gname;
            $admingroupDAO ->rank = 1;


            if(!empty($gid))  //修改流程
            {
                $admingroupDAO ->findGid($gid);
            }
            try{
                $admingroupDAO ->save();
            }catch (Exception $e){
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('保存失败！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }

            if(empty($id)){
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('保存成功');");
                echo("location.href='/management/admin/admingroup';");
                echo('</script>');
                exit;
            }else {
                echo json_encode(array('msg'=>'保存成功！','return_url'=>'/management/admin/admingroup'));
                exit;
            }
        }

        public function deladmingroupAction(){
            $gid = (int)HttpUtil::getString("gid");
            $admingroupDAO = $this->orm->createDAO('admingroup');
            $admingroupDAO ->findGid($gid);
            $admingroupDAO ->delete();

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/management/admin/admingroup';");
            echo('</script>');
            exit;
        }

        public function editadmingroupAction(){
            if($_REQUEST['gid'] != ""){
                $admingroupinfo = $this->orm->createDAO("admingroup")->findGid($_REQUEST['gid']);
                $admingroupinfo = $admingroupinfo->get($this->dbhelper);
                $this->view->assign("admingroupinfo",$admingroupinfo);

                echo $this->view->render("index/header.phtml");
                echo $this->view->render("admin/editadmingroup.phtml");
                echo $this->view->render("index/footer.phtml");
            }else {
                alert_back("操作失败");
            }
        }
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkmanagement();

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
			$pm_chouzi ->selectLimit .= " AND is_del=0";
			$pm_chouzi ->selectLimit .= " order by id desc";
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);

            //管理员角色类型
            $admingroupDAO = $admingroupDAO = $this->orm->createDAO("admingroup")->get();
            $this->view->assign("admingrouplist",$admingroupDAO);
		}

        //权限
        public function acl()
        {
            //return;
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'isadmin',
                'addrs',
                'editrs',
                'addrsdepartment',
                'editrsdepartment',
                'addrscate',
                'editrscate',
                'editrspwd',
                'savepphuodong',
                'saveppvisit',
                'relation',
                'syncjzf',
                'isnot_pp',
                'ajaxaddpp',
                'to-addadmingroup',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
}
?>
