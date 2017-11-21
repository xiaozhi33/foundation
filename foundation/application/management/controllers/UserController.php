<?php
require_once("BaseController.php");
class Management_userController extends BaseController
{
	public $dbhelper;
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

			// 我的上次登录记录
			$loginDAO = $this->orm->createDAO('my_login_log');
			$admininfo_array = SessionUtil::getAdmininfo();
			$loginDAO ->findLogUid($admininfo_array['admin_info']['id']);
			$loginDAO ->selectLimit .= ' order by logTime desc limit 0,1 ';
			$loginDAO = $loginDAO->get();


            $this->view->assign(array(
                'task_from' => $task_from,
                'task_helper' => $task_helper,
                'task_to' => $task_to,
				'admininfo' => $this->admininfo,
				'login_log' => $loginDAO[0]
            ));

			echo $this->view->render("index/header.phtml");
			echo $this->view->render("user/index.phtml");
			echo $this->view->render("index/footer.phtml");
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

		$name = $admininfo['admin_info']['id'];
		$pwd = $_REQUEST['pwd'];
		
		if($name !="" && $pwd != ""){
			$my_admin = new my_adminDAO($name);
			$my_admin ->admin_pwd = substr(md5(serialize($pwd)), 0, 32);
			$my_admin ->save($this->dbhelper);
			$this->alert_go("密码修改成功","/management/user/editpwd");
		}else{
			$this->alert_back("请输入管理员名称或密码");
		}
	}

	///////////////////// 用户头像编辑 ///////////////////////////////////////////////////////////////////

	public function editheadimgAction(){
		// 预加载用户头像
		$admininfo = SessionUtil::getAdmininfo();
		$my_admin = $this->orm->createDAO('my_admin');
		$my_admin ->findId($admininfo['admin_info']['id']);
		$my_admin = $my_admin ->get();
		$this->view->assign("my_admin", $my_admin[0]);

		echo $this->view->render("index/header.phtml");
		echo $this->view->render("user/editheadimg.phtml");
	}

	public function saveheadimgAction(){
		if($_POST['headpic'] == null){
			$this->alert_go("请先上传，并剪裁图片！","/management/user/editheadimg");
		}
		$admininfo = SessionUtil::getAdmininfo();
		$id['id'] = $admininfo['admin_info']['id'];   // 用户id
		$data =array(
			'face' => $_POST['headpic'],
		);
		//处理用户裁剪的图片
		if($data['face']){
			$face = $this->SaveFormUpload($id['id'], $data['face']);
			if($face['error']){
				$this->alert_back($face['msg']);
			}
			$data['face']= $face['url'];
		}else{
			unset($data['face']);
		}

		$my_admin = $this->orm->createDAO('my_admin');
		$my_admin ->findId($admininfo['admin_info']['id']);
		$my_admin ->headimg = $face['url'];
		try{
			$my_admin ->save();
		}catch(Exception $e){
			$this->alert_go("修改失败".'-'.$e,"/management/user/editheadimg");
		}
		$this->alert_go("修改成功","/management/user/index");
	}

	function SaveFormUpload($savepath, $img, $types=array())
	{
		$basedir = '/include/upload_file/headimg/'.$savepath;
		$fullpath = dirname(THINK_PATH).$basedir;
		if(!is_dir($fullpath)){
			mkdir($fullpath,0777,true);
		}
		$types = empty($types)? array('jpg', 'gif', 'png', 'jpeg'):$types;
		$img = str_replace(array('_','-'), array('/','+'), $img);
		$b64img = substr($img, 0,100);
		if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $b64img, $matches)){
			$type = $matches[2];
			if(!in_array($type, $types)){
				return array('error'=>1,'msg'=>'图片格式不正确','url'=>'');
			}
			$img = str_replace($matches[1], '', $img);
			$img = base64_decode($img);
			$photo = '/'.md5(date('YmdHis').rand(1000, 9999)).'.'.$type;
			file_put_contents($fullpath.$photo, $img);
			return array('error'=>0,'msg'=>'保存图片成功','url'=>$basedir.$photo);
		}
		return array('error'=>2,'msg'=>'请选择要上传的图片','url'=>'');
	}

	////////////////////////////////////////////////////////////////////////////////////////////////
	public function edituserinfoAction()
	{
		if(!empty($_POST)){
			// 编辑保存
			$userinfoDAO = $this->orm->createDAO('my_admin');
			$userinfoDAO ->findId($this->admininfo['id']);
			$userinfoDAO ->sex = $_POST['sex'];
			$userinfoDAO ->phone = $_POST['phone'];
			$userinfoDAO ->mobile = $_POST['mobile'];
			$userinfoDAO ->email = $_POST['email'];
			$userinfoDAO ->wechat = $_POST['wechat'];
			$userinfoDAO ->save();
			$this->alert_go("修改成功","/management/user/index");
		}else {
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("user/edituserinfo.phtml");
			echo $this->view->render("index/footer.phtml");
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
        SessionUtil::checkmanagement();
    }
}