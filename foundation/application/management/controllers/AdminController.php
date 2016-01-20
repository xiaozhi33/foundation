<?php
	require_once("BaseController.php");
	class Management_adminController extends BaseController {
		private $dbhelper;
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
				$adminlist ->admin_pwd = $_REQUEST['pwd'];
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
		
		public function nodelAction(){
			if($_REQUEST['id'] != ""){
				$adminlist = new my_adminDAO($_REQUEST['id']);
				$adminlist->del($this->dbhelper);
				alert_go("删除成功。","/management/admin");
			}else{
				alert_back("删除失败");
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
			if($_REQUEST['name'] != ""){
				$departmentinfo = new jjh_mg_departmentDAO();
				$departmentinfo ->pname = $_REQUEST['name'];
				$departmentinfo->save($this->dbhelper);
				alert_go("部门添加成功。","/management/admin/department");
			}else {
				alert_back("添加失败");
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
				$ppinfo = new jjh_mg_ppDAO();
				$ppinfo ->ppname = $_REQUEST['ppname'];
				$ppinfo ->pp_address = $_REQUEST['pp_address'];
				$ppinfo ->pp_beizhu = $_REQUEST['pp_beizhu'];
				$ppinfo ->pp_cate = $_REQUEST['pp_cate'];
				$ppinfo ->pp_msn = $_REQUEST['pp_msn'];
				$ppinfo ->pp_pm_id = $_REQUEST['pp_pm_id'];
				$ppinfo ->pp_qq = $_REQUEST['pp_qq'];
				$ppinfo ->ppemail = $_REQUEST['ppemail'];
				$ppinfo ->ppmobile = $_REQUEST['ppmobile'];
				$ppinfo ->ppphone = $_REQUEST['ppphone'];
				
				$ppinfo->save($this->dbhelper);
				alert_go("联系人添加成功。","/management/admin/pp");
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
				$ppinfo = new jjh_mg_ppDAO($_REQUEST['pid']);
				$ppinfo ->ppname = $_REQUEST['ppname'];
				$ppinfo ->pp_address = $_REQUEST['pp_address'];
				$ppinfo ->pp_beizhu = $_REQUEST['pp_beizhu'];
				$ppinfo ->pp_cate = $_REQUEST['pp_cate'];
				$ppinfo ->pp_msn = $_REQUEST['pp_msn'];
				$ppinfo ->pp_pm_id = $_REQUEST['pp_pm_id'];
				$ppinfo ->pp_qq = $_REQUEST['pp_qq'];
				$ppinfo ->ppemail = $_REQUEST['ppemail'];
				$ppinfo ->ppmobile = $_REQUEST['ppmobile'];
				$ppinfo ->ppphone = $_REQUEST['ppphone'];
				
				$ppinfo ->save($this->dbhelper);
				alert_go("编辑成功。","/management/admin/pp");
			}else {
				alert_back("添加失败");
			}
		}
		
		public function ppAction(){
			$ppinfo = new jjh_mg_ppDAO();
			if($_REQUEST['ppname'] != ""){
				$ppinfo->selectLimit = " and ppname='$_REQUEST[ppname]' and pp_pm_id='$_REQUEST[pp_pm_id]'";
			}
			$ppinfo = $ppinfo->get($this->dbhelper);
			
			$total = count($ppinfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($ppinfo,null,"pp",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('pplist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("admin/pp.phtml");
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
				$my_admin ->admin_pwd = $pwd;
				$my_admin ->save($this->dbhelper);
				alert_go("密码修改成功","/management/admin/editpwd");
			}else{
				alert_back("请输入管理员名称或密码");
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
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);
		}
	}
?>
