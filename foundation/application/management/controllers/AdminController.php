<?php
	require_once("BaseController.php");
	class Management_adminController extends BaseController {
		private $dbhelper;
		public $pp_config = array(
			'pp_cate' => array('捐赠方'=>'捐赠方','实际捐赠方'=>'实际捐赠方','使用方'=>'使用方','业务方'=>'业务方'),
			'pp_jzf_cate' => array('个人'=>'个人','企业'=>'企业','公益组织'=>'公益组织','其他'=>'其他'),
			'pp_jzf_attr1' => array('校友'=>'校友','校友联系'=>'校友联系','非校友'=>'非校友','其他'=>'其他'),
			'pp_jzf_attr2' => array('海内'=>'海内','海外'=>'海外'),
			'pp_syf_cate' => array('学校'=>'学校','机关'=>'机关','学院'=>'学院','直属单位'=>'直属单位','校外'=>'校外','其他'=>'其他'),
			'pp_yuf_cate' => array('登记'=>'登记','业务主管'=>'业务主管','银行'=>'银行','财税'=>'财税','高校基金会'=>'高校基金会','其他'=>'其他'),
		);
		public function indexAction(){
			$adminlist = new my_adminDAO();
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
			
			if($_REQUEST['name'] != "" && $_REQUEST['pwd'] != "" && $_REQUEST['type'] != ""){
				$adminlist = new my_adminDAO();
				$adminlist ->admin_name = $_REQUEST['name'];
				$adminlist ->admin_pwd = substr(md5(serialize($_REQUEST['pwd'])), 0, 32);
				$adminlist ->admin_type = $_REQUEST['type'];
				$adminlist ->save($this->dbhelper);
				alert_go("添加成功。","/management/admin");
			}else{
				alert_back("请输入详细信息。");
			}
		}
		
		//判断该用户是否存在
		public function isadminAction($name){
			$adminlist = new my_adminDAO();
			$adminlist ->admin_name = $name;
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
				$adminlist = new my_adminDAO($_REQUEST['id']);
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
				$adminlist = new my_adminDAO($_REQUEST['id']);
				$adminlist ->admin_name = $_REQUEST['name'];
				$adminlist ->admin_type = $_REQUEST['type'];
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
		
		//联系人管理科
		public function addppAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/addpp.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrsppAction(){
			if($_REQUEST['ppname'] != ""){
				$ppinfo = $this->orm->createDAO("jjh_mg_pp");
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
				
				$ppinfo->save($this->dbhelper);
				alert_go("联系人添加成功。","/management/admin/pp?pp_cate=".$_REQUEST['pp_cate']);
			}else {
				alert_back("添加失败");
			}
		}
		
		public function editppAction(){
			if($_REQUEST['id'] != ""){
				$ppinfo = new jjh_mg_ppDAO($_REQUEST['id']);
				$ppinfo = $ppinfo->get($this->dbhelper);

                $meeting_pp_companyDAO = $this->orm->createDAO('jjh_mg_pp_company');
                $meeting_pp_companyDAO ->findPp_id($ppinfo[0]['pid']);
                $meeting_pp_companyDAO = $meeting_pp_companyDAO->get();

                $this->view->assign("pp_company_list",$meeting_pp_companyDAO);
				$this->view->assign("ppinfo",$ppinfo);
				
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("admin/editpp.phtml");
				echo $this->view->render("index/footer.phtml");
			}else {
				alert_back("操作失败");
			}
		}
		
		public function editrsppAction(){
			if($_REQUEST['ppname'] != "" && $_REQUEST['pid']){
				$ppinfo = $this->orm->createDAO("jjh_mg_pp");
                $ppinfo ->findPid($_REQUEST['pid']);
				$ppinfo ->ppname = $_REQUEST['ppname'];
				$ppinfo ->pp_address = $_REQUEST['pp_address'];
				$ppinfo ->pp_beizhu = $_REQUEST['pp_beizhu'];
				$ppinfo ->pp_cate = $_REQUEST['pp_cate'];

				$ppinfo ->pp_jzf_cate = $_REQUEST['pp_jzf_cate'];
				$ppinfo ->pp_jzf_attr1 = $_REQUEST['pp_jzf_attr1'];
				$ppinfo ->pp_jzf_attr2 = $_REQUEST['pp_jzf_attr2'];
				$ppinfo ->pp_syf_cate = $_REQUEST['pp_syf_cate'];
				$ppinfo ->pp_yuf_cate = $_REQUEST['pp_yuf_cate'];

				$ppinfo ->pp_msn = $_REQUEST['pp_msn'];
				$ppinfo ->pp_pm_id = $_REQUEST['pp_pm_id'];
				$ppinfo ->pp_qq = $_REQUEST['pp_qq'];
				$ppinfo ->ppemail = $_REQUEST['ppemail'];
				$ppinfo ->ppmobile = $_REQUEST['ppmobile'];
				$ppinfo ->ppphone = $_REQUEST['ppphone'];
				
				$ppinfo ->save($this->dbhelper);
				alert_go("编辑成功。","/management/admin/pp?pp_cate=".$_REQUEST['pp_cate']);
			}else {
				alert_back("添加失败");
			}
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
         * 联系人子公司列表
         */
        public function ppcompanyAction()
        {
            $pp_id = $_REQUEST['pp_id'];
            $pp_id = (int)$pp_id;
            if(!empty($pp_id))
            {
                $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");
                $jjh_mg_pp_companyDAO ->findPp_id($pp_id);
                $jjh_mg_pp_companyDAO = $jjh_mg_pp_companyDAO ->get();

                $ppinfo = $this->getppbyppid($pp_id);

                if(!empty($ppinfo)){
                    $this->view->assign("pp_info",$ppinfo[0]);
                }
                $this->view->assign("company_list",$jjh_mg_pp_companyDAO);
                $this->view->assign("pp_id",$pp_id);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("admin/ppcompany.phtml");
                echo $this->view->render("index/footer.phtml");
            }
        }

        /**
         * 添加联系人子公司信息
         */
        public function addppcompanyAction()
        {
            $pp_id = $_REQUEST['pp_id'];
            $pp_id = (int)$pp_id;
            if(!empty($pp_id)) {
                $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");
                $jjh_mg_pp_companyDAO->findPp_id($pp_id);
                $jjh_mg_pp_companyDAO = $jjh_mg_pp_companyDAO->get();
                $this->view->assign("ppcompany", $jjh_mg_pp_companyDAO);
            }
            $this->view->assign("pp_id", $pp_id);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("admin/addppcompany.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
         * 编辑联系人子公司信息
         */
        public function editppcompanyAction()
        {
            $id = (int)$_REQUEST['id'];
            if(!empty($id)) {
                $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");
                $jjh_mg_pp_companyDAO->findId($id);
                $jjh_mg_pp_companyDAO = $jjh_mg_pp_companyDAO->get();
                $this->view->assign("ppcompany", $jjh_mg_pp_companyDAO);
            }
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("admin/editppcompany.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function saveppcompanyAction()
        {
            $id = (int)$_REQUEST['id'];
            $pp_id = $_REQUEST['pp_id'];
            $pp_id = (int)$pp_id;
            $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");

            $company_name = HttpUtil::postString("company_name");
            $company_contector = HttpUtil::postString("company_contector");
            $company_cont_style = HttpUtil::postString("company_cont_style");
            if($company_name == "" || $company_contector== "" || $company_cont_style == "")
            {
                alert_back("信息不全，请查看信息的完整性，并重新提交。");
            }

            if(!empty($id)) {
                $jjh_mg_pp_companyDAO->findid($id);
            }
            $jjh_mg_pp_companyDAO ->pp_id = $pp_id;
            $jjh_mg_pp_companyDAO ->company_name = $company_name;
            $jjh_mg_pp_companyDAO ->company_contector = $company_contector;
            $jjh_mg_pp_companyDAO ->company_cont_style = $company_cont_style;

            $jjh_mg_pp_companyDAO ->save();
            alert_go("子公司信息添加成功。","ppcompany?pp_id=".$pp_id);
        }

        public function delppcompanyAction()
        {
            $id = (int)$_REQUEST['id'];
            $pp_id = (int)$_REQUEST['pp_id'];
            if(empty($id)) {
                alert_back("操作失败。");
                $this->_redirect("/management/admin/ppcompany?pp_id=".$pp_id);
            }
            $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");
            $jjh_mg_pp_companyDAO->findid($id);
            $jjh_mg_pp_companyDAO->delete();

            $this->_redirect("/management/admin/ppcompany?pp_id=".$pp_id);
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
         * 捐赠人列表
         */
        public function ppAction(){
            $ppinfo = $this->orm->createDAO("jjh_mg_pp");
            if($_REQUEST['ppname'] != ""){
                $ppinfo->selectLimit .= " and ppname like '%".$_REQUEST['ppname']."%'";
            }
            if($_REQUEST['pname'] != ""){
                $ppinfo->selectLimit .= " and pp_pm_id = '".$_REQUEST['pname']."'";
            }
            if($_REQUEST['pp_cate'] != ""){
                $ppinfo->selectLimit .= " and pp_cate = '".$_REQUEST['pp_cate']."'";
            }

            $ppinfo->selectLimit .= " order by pid DESC";
            $ppinfo = $ppinfo->get($this->dbhelper);

            $total = count($ppinfo);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO ->pageHelper($ppinfo,null,"pp",null,'get',20,8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php","",$pages);
            $this->view->assign('pplist',$pageDAO['pageData']);
            $this->view->assign('page',$pages);
            $this->view->assign('total',$total);
            $this->view->assign('pname',$_REQUEST['pname']);
            $this->view->assign('ppname',$_REQUEST['ppname']);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("admin/pp.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function ppinfoAction(){
            if($_REQUEST['id'] != ""){
                $ppinfo = $this->orm->createDAO("jjh_mg_pp");
                $ppinfo ->findPid($_REQUEST['id']);
                $ppinfo = $ppinfo->get($this->dbhelper);

                $meeting_pp_companyDAO = $this->orm->createDAO('jjh_mg_pp_company');
                $meeting_pp_companyDAO ->findPp_id($ppinfo[0]['pid']);
                $meeting_pp_companyDAO = $meeting_pp_companyDAO->get();

                $this->view->assign("pp_company_list",$meeting_pp_companyDAO);
                $this->view->assign("ppinfo",$ppinfo);

                echo $this->view->render("index/header.phtml");
                echo $this->view->render("admin/ppinfo.phtml");
                echo $this->view->render("index/footer.phtml");
            }else {
                alert_back("操作失败");
            }
        }

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
                $pm_mg_infoDAO = $pm_mg_infoDAO ->get();
                $this->view->assign("pm_mg_infoDAO",$pm_mg_infoDAO);

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
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkmanagement();

			//config
			$this->view->assign("pp_config",$this->pp_config);
			
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
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);
		}
	}
?>
