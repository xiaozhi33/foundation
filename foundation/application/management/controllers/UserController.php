<?php
require_once("BaseController.php");
class Management_userController extends BaseController
{
    /**
     * @用户首页 - 任务一览，消息一览
     */
    public function indexAction(){
        try{
            $uid = $this->admininfo['admin_info']['id'];
            // 发起的任务
            $task_from = $this->orm->createDAO('jjh_mg_task')->findSponsor($uid)->get();

            // 协助的任务
            $task_helper = $this->orm->createDAO('jjh_mg_task');
            $task_helper->selectLimit .= " AND find_in_set('".$uid."',helper)";
            $task_helper = $task_helper->get();

            // 需执行的任务
            $task_to = $this->orm->createDAO('jjh_mg_task')->findExecutor($uid)->get();

            // 和我有关的项目 jjh_mg_chouzi fzr

            // 我上传的文档 jjh_mg_files

            // 我参加的活动 pm_mg_info_active

            // 我的代办事宜  pm_mg_todolist

            // 我的回馈 pm_mg_feedback


            $this->view->assign(array(
                'task_from' => $task_from,
                'task_helper' => $task_helper,
                'task_to' => $task_to,
            ));
        }catch (Exception $e){
            throw $e;
        }
    }
	
	public function editpwdAction()
	{
		echo $this->view->render("index/header.phtml");
		echo $this->view->render("user/editpwd.phtml");
		echo $this->view->render("index/footer.phtml");
	}
	
	
	public function editrspwdAction(){
		$admininfo = SessionUtil::getAdmininfo();
        //$this->admininfo = $admininfo['admin_info'];
			
		$name = $admininfo['admin_id'];
		$pwd = $_REQUEST['pwd'];
		
		if($name !="" && $pwd != ""){
			$my_admin = new my_adminDAO($name);
			$my_admin ->admin_pwd = substr(md5(serialize($pwd)), 0, 32);
			$my_admin ->save($this->dbhelper);
			alert_go_old("密码修改成功","/management/user/editpwd");
		}else{
			alert_back_old("请输入管理员名称或密码");
		}
	}
	
	//权限
	public function acl()
	{
		$action = $this->getRequest()->getActionName();
		$except_actions = array(
			'index',
			'editpwd',
			'editrspwd'
		);
		if (in_array($action, $except_actions)) {
			return;
		}
		parent::acl();
	}
		
    public function _init(){
        //error_reporting(0);
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();
    }
}