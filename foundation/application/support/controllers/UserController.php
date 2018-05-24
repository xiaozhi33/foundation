<?php
require_once("BaseController.php");
class Support_userController extends BaseController
{
	public $dbhelper;

	public function editpwdAction()
	{
		echo $this->view->render("index/header.phtml");
		echo $this->view->render("user/editpwd.phtml");
		echo $this->view->render("index/footer.phtml");
	}
	
	
	public function editrspwdAction(){
		$admininfo = SessionUtil::getAdmininfo();
        //$this->admininfo = $admininfo['admin_info'];

		$id = $admininfo['admin_info']['id'];
		$pwd = $_REQUEST['pwd'];
		
		if($id !="" && $pwd != ""){
			$my_admin = $this->orm->createDAO("_support_college_user")->findId($id);
			$my_admin ->password = substr(md5(serialize($pwd)), 0, 32);
			$my_admin ->save($this->dbhelper);
			$this->alert_go("密码修改成功","/support/user/editpwd");
		}else{
			$this->alert_back("请输入管理员名称或密码");
		}
	}

	//权限
	public function acl()
	{
		$action = $this->getRequest()->getActionName();
		$except_actions = array(
			'index',
			'editpwd',
			'editrspwd',
			'editheadimg',  // 头像上传
			'saveheadimg',
			'edituserinfo',
		);
		if (in_array($action, $except_actions)) {
			return;
		}
		parent::acl();
	}
		
    public function _init(){
		$this ->dbhelper = new DBHelper();
		$this ->dbhelper ->connect();
        //error_reporting(0);
        SessionUtil::sessionStart();
        SessionUtil::checkSupport();
    }
}