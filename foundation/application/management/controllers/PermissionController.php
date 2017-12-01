<?php

require_once 'BaseController.php';

class Management_PermissionController extends BaseController {
	
	//后台全局权限设置
	public function backgroundPermissionAction() {
		try {
			if($_POST) {
				$permission = array();
				
				$cur_controller = explode('/',$_POST['m_name'][0]);
				$cur_controller = $cur_controller[0];
				$allow_status = 'all';
				$i = 0;
				foreach($_POST['m_name'] as $mnk=>$mnv) {
					$temp = explode('/',$mnv);
					if($cur_controller != $temp[0]) {
						$permission['controllers'][$cur_controller] = array(
							'name'		=> $_POST['c_desc'][$i]?$_POST['c_desc'][$i]:$_POST['c_name'][$i],
							'allow'		=> $allow_status
						);
						
						$cur_controller = $temp[0];
						$i++;
						$allow_status = 'all';
					}
					
					$permission['methods'][$cur_controller][$mnv] = array(
						'name'		=> $_POST['m_desc'][$mnk]?$_POST['m_desc'][$mnk]:$mnv,
						'allow'		=> $_POST['allow'][$mnk]
					);
					
					if($allow_status == 'all') {
						if($_POST['allow'][$mnk] != 'all') {
							$allow_status = $_POST['allow'][$mnk];
						}
					}
				}
				$permission['controllers'][$cur_controller] = array(
					'name'		=> $_POST['c_desc'][$i]?$_POST['c_desc'][$i]:$_POST['c_name'][$i],
					'allow'		=> $allow_status
				);
				
				$permission = var_export($permission,true);
				
				file_put_contents(__SITEPATH__ . '/application/configs/permissionConfig.php','<?php return ' . $permission . ' ?>');
				
				$this->alert_go('保存成功！','/management/permission/background-permission');
			}
			$controllers = array();
		
			if ($dp = opendir(__SITEPATH__ . "/application/admin/controllers")) {
				while (($file=readdir($dp)) != false) {
					if ($file != '.' && $file != '..' && $file!='.svn' && $file != 'BaseController.php') {
						$controllers[strtolower(str_replace('Controller.php','',$file))]['path'] = __SITEPATH__ . '/application/management/controllers/' . $file;
					}
				}
			}
			closedir($dp);
			
			$find_arr = range('A','Z');
			$replace_arr = array();
			foreach($find_arr as $k=>$v) {
				$replace_arr[$k] = '-' . strtolower($v);
			}
			
			foreach($controllers as $k=>$v) {
				require_once $v['path'];
				$methods = get_class_methods('Admin_' . $k . 'Controller');
				
				foreach($methods as $m) {
					if(substr($m,-6) === 'Action') {
						$controllers[strtolower($k)]['methods'][] = str_replace($find_arr,$replace_arr,substr($m,0,-6));
					}
				}
			}
			
			$this->view->assign(array(
				'controllers'	=> $controllers,
				'config'		=> $this->permissionConfig
			));
			
			echo $this->view->render('permission/background-permission.htm');
			exit;
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//积分规则
	public function integralAction() {
		try{
			if($_POST) {
				//print_r($_POST['rule']);
				foreach ($_POST['rule'] as $key => $value){
					if($value['rid']){
						$ruleDAO = $this->orm->createDAO('_integral_rule')->findRid($value['rid']);
						$ruleDAO->integral = $value['integral'];
						$ruleDAO->reputation = $value['reputation'];
						$ruleDAO->save();
					}
				}
				$this->alert_go('保存成功！','/admin/permission/integral');
			} else {
				$ruleDAO = $this->orm->createDAO('_integral_rule')->findAction($action)->get();
				//print_r($ruleDAO);
				$this->view->assign(array(
						'rule_list'		=> $ruleDAO
				));
				
				echo $this->view->render('permission/integral.htm');
				exit;
			}
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//添加积分规则
	public function addintegralAction() {
		try {
			if($_POST) {
				$rulename = HttpUtil::postString('rulename');
				$ruleaction = HttpUtil::postString('ruleaction');
				$integral = HttpUtil::postString('integral');
				$reputation = HttpUtil::postString('reputation');
				
				if($rulename != "" && $ruleaction != "" && $integral != "" && $reputation != ""){
					
					$rulecheckDAO = $this->orm->createDAO('_integral_rule')->findRulename($rulename);
					$rulecheckDAO->selectLimit .= " OR action = '$ruleaction' ";
					$rulecheck = $rulecheckDAO->get();
					if(!$rulecheck){
						$ruleDAO = $this->orm->createDAO('_integral_rule');
						$ruleDAO->rulename = $rulename;
						$ruleDAO->action = $ruleaction;
						$ruleDAO->integral = $integral;
						$ruleDAO->reputation = $reputation;
							
						$rid = $ruleDAO->save();
						if($rid > 0) {
							//$this->alert_go('添加失败！','/admin/permission/integral');
							echo '<script type="text/javascript">alert("添加成功");parent.location.href = "/admin/permission/integral";</script>';
							exit;
						}else {
							$this->alert_back("添加失败，请重新提交！");
						}
					} else {
						$this->alert_back("规则名或规则动作不能重复！");
					}
				} else {
					$this->alert_back("不能有空值！");
				}
			}
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//删除积分规则
	public function delintegralAction(){
		try {
			$rid = $this->_getParam("rid");
			if($rid != ""){
				$ruleDAO = $this->orm->createDAO("_integral_rule")->findRid($rid);;
				$ruleDAO ->delete();
				$this->alert_go("删除成功", "/admin/permission/integral");
			}else {
				$this->alert_back("您的操作有误");
			}
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	public function integralLimitAction(){
		try{
			$wenda_sysetm = $this->orm->createDAO('_wenda_sysetm')->findWd_appid(1);
			if($_POST){
				$wenda_sysetm->wd_integral_limit = $_POST['integral_limit'];
				$wenda_sysetm->wd_reputation_limit = $_POST['reputation_limit'];
				$wenda_sysetm->save();
				$this->alert_go('修改成功！','/admin/permission/integral-limit');
			}
			$this->view->assign(array(
					'system'	=> $wenda_sysetm->get(),
			));
			echo $this->view->render("permission/integral_limit.htm");
			exit;
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	public function ipLimitAction(){
		try{
			$wenda_sysetm = $this->orm->createDAO('_wenda_sysetm')->findWd_appid(1);
			if($_POST){
				$ips = serialize(explode("|", str_replace("\r","|", str_replace("\n","", trim($_POST['ip_limit'])))));
				$wenda_sysetm->wd_ip_limit = $ips;
				$wenda_sysetm->save();
				$this->alert_go('修改成功！','/admin/permission/ip-limit');
			}
			$this->view->assign(array(
					'system'	=> $wenda_sysetm->get(),
			));
			echo $this->view->render("permission/ip_limit.htm");
			exit;
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//站点信息
	public function siteAction() {
		try{
			if($_POST) {
				$setting = $_POST;
				if($this->setSystemSeting($setting)){
					$this->_redirect('/admin/permission/site');
				} else {
					$this->alert_go('保存失败！','/admin/permission/site');
				}
			} else {
				$this->view->assign(array(
						'system'		=> $this->systemSetting
				));
	
				echo $this->view->render('permission/site.htm');
				exit;
			}
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//注册与访问
	public function visitAction() {
		try{
			if($_POST) {
				$setting = $_POST;
				if($this->setSystemSeting($setting)){
					$this->_redirect('/admin/permission/visit');
				} else {
					$this->alert_go('保存失败！','/admin/permission/visit');
				}
			} else {
				$this->view->assign(array(
						'system'		=> $this->systemSetting
				));
	
				echo $this->view->render('permission/visit.htm');
				exit;
			}
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//邮箱设置
	public function emailAction() {
		try{
			if($_POST) {
				$setting = $_POST;
				if($this->setSystemSeting($setting)){
					$this->_redirect('/management/permission/email');
				} else {
					$this->alert_go('保存失败！','/management/permission/email');
				}
			} else {
				$this->view->assign(array(
						'system'		=> $this->systemSetting
				));

				echo $this->view->render("index/header.phtml");
				echo $this->view->render("permission/email.phtml");
				echo $this->view->render("index/footer.phtml");
				exit;
			}
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	//开放平台
	public function openAction() {
		try{
			if($_POST) {
				$setting = $_POST;
				if($this->setSystemSeting($setting)){
					$this->_redirect('/admin/permission/open');
				} else {
					$this->alert_go('保存失败！','/admin/permission/open');
				}
			} else {
				$this->view->assign(array(
						'system'		=> $this->systemSetting
				));
	
				echo $this->view->render('permission/open.htm');
				exit;
			}
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}
	
	public function testEmailSettingAction(){
		try{
			if($_POST){
				$email = HttpUtil::postString("test_email");
				$subject = $this->systemSetting['site_name'].' - 邮箱服务器配置测试';
				$body = '这是一封测试邮件，收到邮件表明网站邮箱服务器配置成功！';
				$mail_result = $this->sendEmail($subject, $body, $email, $email);
				if($mail_result) {
					$this->alert_back('发送成功，请前往邮箱查收！<br><b>提醒</b>：测试邮件可能会被放入垃圾邮件中！');
				}
			}
			$this->alert_back('发送失败！');
		}catch(Exception $e) {
			$this->toErrorLogs($e);
			$this->alert_back(addslashes($e->getMessage()));
		}
	}

	//权限
	public function acl()
	{
		$action = $this->getRequest()->getActionName();
		$except_actions = array(
			'ip-limit',
			'email',
			'open',
			'test-email-setting',
		);
		if (in_array($action, $except_actions)) {
			return;
		}
		parent::acl();
	}
}