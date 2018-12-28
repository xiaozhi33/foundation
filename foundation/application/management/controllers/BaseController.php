<?php
    session_start();
	require_once("sessionutil.php");
	require_once("httputil.php");
	require_once("functions.php");
	require_once 'resizepic.php';           //创建缩略图
	require_once 'uploadpic.php';

    // mssql 数据库操作类
    require_once("cw_api.class.php");
    require_once("mssql_db.class.php");
	
	$uploadpicpath = __UPLOADPICPATH__;       //上传图片路径

	class BaseController extends Zend_Controller_Action
	{
        protected $orm;
        public $mssql_class;
        public $admininfo = '';
        public $renling_weirenling_list = "";
        public $shiyong_weirenling_list = "";
        public $task_init_array = "";
        public $is_star = 0;                 // 是否开启标星

        public $pp_config = array(
            'pp_jzf_cate' => array('个人'=>'个人','企业'=>'企业','公益组织'=>'公益组织','其他'=>'其他'),
            'pp_jzf_attr1' => array('校友'=>'校友','校友联系'=>'校友联系','非校友'=>'非校友','其他'=>'其他'),
            'pp_jzf_attr2' => array('海内'=>'海内','海外'=>'海外'),
            'pp_syf_cate' => array('学校'=>'学校','机关'=>'机关','学院'=>'学院','直属单位'=>'直属单位','校外'=>'校外','其他'=>'其他'),
            'pp_yuf_cate' => array('登记'=>'登记','业务主管'=>'业务主管','银行'=>'银行','财税'=>'财税','高校基金会'=>'高校基金会','其他'=>'其他'),
        );

        public $rate_config = array(
            '1' => '已立项',
            '2' => '已签约',
            '3' => '已到账',
            '4' => '已执行',
            '5' => '已回馈',
            '6' => '已完结',
            '7' => '待催款',
            '8' => '正常执行',
            '9' => '待结项',
        );

        public $type_arrays = array(
            '查询协议' => '查询协议',
            '工作报告' => '工作报告',
            '策划书' => '策划书',
            '印刷文档' => '印刷文档',
            '会议资料' => '会议资料',
            '业务资料' => '业务资料',
        );

        public $project_status = array(
            //'0' => '未提交',
            '1' => '电子版待审核',
            '2' => '电子版审核未通过',
            '3' => '电子版审核通过',
            '4' => '签字盖章pdf文件待审核',
            '5' => '签字盖章pdf文件审核未通过',
            '6' => '签字盖章pdf文件审核通过',
            '7' => '等待领导审核并签字',
            '8' => '立项成功',
        );

        public $expenditure_status = array(
            //'0' => '未提交',
            '1' => '电子版待审核',
            '2' => '电子版审核未通过',
            '3' => '电子版审核通过',
            '4' => '签字盖章pdf文件待审核',
            '5' => '签字盖章pdf文件审核未通过',
            '6' => '签字盖章pdf文件审核通过',
            '7' => '等待领导审核并签字',
            '8' => '资金使用申请成功',
        );

		public function init()
	    {
	    	$request_mod = $this->getRequest()->getParams();
			$this->view = new Zend_View();
			$this->view ->addScriptPath('application/management/views/scripts');
            $this->orm = ORM::getInstance();
            $this->mssql_class = new msSQL();  // mssql操作类
            //$this->WhiteIP();  //设置白名单

            //获取认领信息
            $this->pm_count = $this->orm->createDAO("pm_mg_info")->get();
            $this->renling_weirenling_list = $renling_weirenling_list = $this->orm->createDAO("pm_mg_info")->findCate_id("0")->findIs_renling("0")->get();
            $this->shiyong_weirenling_list = $shiyong_weirenling_list = $this->orm->createDAO("pm_mg_info")->findCate_id("1")->findIs_renling("0")->get();

            $admininfo = SessionUtil::getAdmininfo();
            //$this->admininfo = $admininfo['admin_info'];
            $my_adminDAO = $this->orm->createDAO('my_admin');
            $my_adminDAO ->findId($admininfo['admin_info']['id']);
            $my_adminDAO = $my_adminDAO->get();
            $this->admininfo = $my_adminDAO[0];

            //捐赠项目金额
            $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info")->findCate_id(0)->select(" sum(zijin_daozheng_jiner) as allsum");
            $pm_mg_infoDAO ->selectLimit .= " AND is_renling=1";
            $pm_mg_infoDAO = $pm_mg_infoDAO->get();

            //会议活动
            $meetingDAO = $this->orm->createDAO("jjh_meeting")->get();

            $this->task_init_array = array(
                'status' => array(  //任务状态
                    '1'  => '新建',
                    '2'  => '进行中',
                    '3'  => '测试ok',
                    '4'  => '反馈',
                    '5'  => '已解决',
                    '6'  => '已关闭',
                ),
                'priority' => array(  //任务优先级
                    '1'  => '低',
                    '2'  => '普通',
                    '3'  => '高',
                    '4'  => '紧急',
                    '5'  => '立刻',
                ),
                'type' => array(   //任务类型
                    'pm' => '项目管理',
                    'active' => '活动任务',
                    'custom' => '客户拜访',
                    'other' => '其他',
                ),
                'schedule' => array(
                    '0' => '0%',
                    '10' => '10%',
                    '20' => '20%',
                    '30' => '30%',
                    '40' => '40%',
                    '50' => '50%',
                    '60' => '60%',
                    '70' => '70%',
                    '80' => '80%',
                    '90' => '90%',
                    '100' => '100%',
                )
            );

            $types_array = array('1' => '新','2' => '补','3' => '续','4' => '改');
            $this->view->assign("types_array", $types_array);

            $admin_list = $this->orm->createDAO("my_admin")->get();
            foreach($admin_list as $k => $v){
                $_admin_list[$v['id']] = $v['admin_name'];
            }

            $admin_groupDAO  = $this->orm->createDAO("admingroup")->get();
            foreach($admin_groupDAO as $k => $v){
                $_g_list[$v['gid']] = $v['gname'];
            }

            $departmentDAO = $this->orm->createDAO("jjh_mg_department")->get();
            $_departmentDAO = array();
            if(!empty($departmentDAO)){
                foreach ($departmentDAO as $key => $value) {
                    $_departmentDAO[$value['id']] = $value['pname'];
                }
            }

            $cateDAO = $this->orm->createDAO("jjh_mg_cate")->get();
            $_cateDAO = array();
            if(!empty($cateDAO)){
                foreach ($cateDAO as $key => $value) {
                    $_cateDAO[$value['id']] = $value['catename'];
                }
            }

            $this->view->assign(array(
				"module" => $request_mod['module'],
				"controller" => $request_mod['controller'],
				"action" => $request_mod['action'],
                'renling_weirenling_list' => $renling_weirenling_list,
                'shiyong_weirenling_list' => $shiyong_weirenling_list,
                "pm_count" => count($this->pm_count),
                "allsum" => (int)$pm_mg_infoDAO[0]['allsum'],
                "meeting_count" => count($meetingDAO),
                'admininfo' =>  $this->admininfo,
                'task_init_array' => $this->task_init_array,
                'admin_list_info' => $_admin_list,
                'group_list' => $_g_list,
                'project_status' => $this->project_status,
                'expenditure_status' => $this->expenditure_status,
                'department_list' => $_departmentDAO,
                'cate_list' => $_cateDAO,
			));

            //config
            // 人员类型
            $jjh_mg_pp_catelist = $this->orm->createDAO("jjh_mg_pp_cate")->get();
            if(!empty($jjh_mg_pp_catelist)){
                foreach($jjh_mg_pp_catelist as $key => $value){
                    $this->pp_config['pp_cate'][$value['pp_cate_name']] = $value['pp_cate_name'];
                }
            }
            $this->view->assign("jjh_mg_pp_catelist",$jjh_mg_pp_catelist);
            $this->view->assign("pp_config",$this->pp_config);

            // 标签
            $pm_mg_tagDAO = $this->orm->createDAO("pm_mg_tag")->get();
            $this->view->assign("taglist",$pm_mg_tagDAO);


            // 立项申请审核
            $_support_project_list = $this->orm->createDAO('_support_project');
            $_support_project_list ->selectLimit .= ' AND status!=8';
            $_support_project_list ->order(' lastmodify DESC ');
            $_support_project_list = $_support_project_list->get();

            $this->view->assign("support_project_list",$_support_project_list);
            // 项目支出申请审核
            $_support_expenditure_list = $this->orm->createDAO('_support_expenditure');
            $_support_expenditure_list ->selectLimit .= ' AND status!=8';
            $_support_expenditure_list ->order(' lastmodify DESC ');
            $_support_expenditure_list = $_support_expenditure_list->get();

            $this->view->assign("support_expenditure_list",$_support_expenditure_list);

            // 操作类型
            $active_array = array(
                'tjdzsq' => '提交电子版申请',
                'shtg' => '电子版申请审核通过',
                'shsb' => '电子版申请审核失败',
                'tjpdf' => '签字盖章pdf文件待审核',
                'pdfshtg' => '签字盖章pdf文件审核通过',
                'pdfshsb' => '签字盖章pdf文件审核失败',
                'lxcg'  => '立项成功',
            );
            $this->view->assign("active_array",$active_array);

            // 操作类型
            $active_expenditure_array = array(
                'tjsysq' => '提交资金使用申请',
                'shtg' => '电子版申请审核通过',
                'shsb' => '电子版申请审核失败',
                'tjpdf' => '签字盖章pdf文件待审核',
                'pdfshtg' => '签字盖章pdf文件审核通过',
                'pdfshsb' => '签字盖章pdf文件审核失败',
                'sqcg'  => '资金使用申请成功',
            );
            $this->view->assign("active_expenditure_array",$active_expenditure_array);

			//网站配置
			$this->systemSetting = $this->getSystemSetting();

            // 待办任务列表list
            $task_list_infoDAO = $this->orm->createDAO('jjh_mg_task');
            $task_list_infoDAO ->selectLimit .= ' AND (FIND_IN_SET('.$this->admininfo['id'].',sponsor) OR FIND_IN_SET('.$this->admininfo['id'].',executor) OR FIND_IN_SET('.$this->admininfo['id'].',helper))';
            $task_list_infoDAO ->selectLimit .= ' AND schedule!=100 ';
            $task_list_infoDAO = $task_list_infoDAO->order(' schedule ASC, priority DESC, id DESC ')->get();
            $this->view->assign("task_list_info",$task_list_infoDAO);

            $this->acl();
            $this->_init();
	    }

        //判断权限
        public function acl() {
            //判断是否需要权限限制
            $isacllist = $this->IsAclList();
            if($isacllist === false) {
                if(HttpUtil::isJsonRequest()) {
                    $this->alert_back('您无权访问此页面');
                }else {
                    $this->alert_back('您无权访问此页面');
                }
            }
        }

        public function IsAclList($action = null,$controller = null){
            if($controller == null) {
                $controller = $this->getRequest()->getControllerName();
            }
            if($action == null) {
                $action = $this->getRequest()->getActionName();
            }

            $acl_admin_info = $this->orm->createDAO('acl_admin_group')->findGid($this->admininfo['gid'])->get();
            $acl_admin_info = unserialize($acl_admin_info[0]['acl_admin_info']);
            if(empty($acl_admin_info)) {
                return false;
            }

            $AclDAO = $this->orm->createDAO("acl_info");
            $AclDAO ->findController($controller);
            $AclDAO ->findAction($action);
            $AclDAO ->findId($acl_admin_info);
            $rs = $AclDAO ->get();
            if(!empty($rs)){
                return TRUE;
            }else {
                return FALSE;
            }
        }

        //JS返回信息提示
        public function alert_back($msg){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('$msg');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        //JS返回信息提示并跳转
        public function alert_go($msg,$url){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('$msg');");
            echo("location.href='$url';");
            echo('</script>');
            exit;
        }

        public function byte_format($size,$dec=2,$is_ex='true')
        {
            $a = array("B", "KB", "MB", "GB", "TB", "PB","EB","ZB","YB");
            $pos = 0;
            while ($size >= 1024)
            {
                $size /= 1024;
                $pos++;
            }
            if($is_ex){
                return round($size,$dec)." ".$a[$pos];
            }else {
                return round($size,$dec);
            }
        }
	    
	    public function _init(){

	    }

        /**
         * 白名单功能
         */
        protected function WhiteIP(){
            $curr_ip=$this->GetIP();
            if($curr_ip){
                $sysetm = $this->orm->createDAO('_system_setting')->findVarname('ip_limit')->get();

                $white_list = unserialize($sysetm[0]['value']); //白名单列表
                //print_r($sysetm);exit;

                $ip_check=false;
                foreach($white_list as $iprule){
                    if($this->CheckIP($curr_ip,trim($iprule))){
                        $ip_check=true;
                        break;
                    }
                }
                if(!$ip_check) $this->alert_back('您的IP'.$curr_ip.'受限，请联系管理员！');
            } else {
                if(!$ip_check) $this->alert_back('您的IP无效，请联系管理员！');
            }
        }
        /*
         * 得到IP
         */
        protected function GetIP(){
            if(!empty($_SERVER["HTTP_CLIENT_IP"]))
                $cip = $_SERVER["HTTP_CLIENT_IP"];
            else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
                $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            else if(!empty($_SERVER["REMOTE_ADDR"]))
                $cip = $_SERVER["REMOTE_ADDR"];
            else
                $cip = false;
            return $cip;
        }

        /*
         * IP规则验证
         */
        protected function CheckIP($ip,$iprule){
            $ipruleregexp=str_replace('.*','ph',$iprule);
            $ipruleregexp=preg_quote($ipruleregexp,'/');
            $ipruleregexp=str_replace('ph','\.[0-9]{1,3}',$ipruleregexp);
            if(preg_match('/^'.$ipruleregexp.'$/',$ip)) return true;
            else return false;
        }

        /**
         * @param $pm_id 项目id
         * @param string $type   类型默认 add为添加，del为删除
         * @param $value 进度值  1已立项 2已签约 3已到账 4已执行 5已回馈
         */
        public function changerate($pm_id, $type='add',$value,$pm_info_id='')
        {
            try{
                $pm_rateDAO = $this->orm->createDAO("pm_mg_rate");
                $pm_rateDAO ->findPm_id($pm_id);
                $rate_list = $pm_rateDAO ->get();

                // 如果没有设置进度，默认为0
                if(empty($rate_list)){
                    $pm_rateDAO = $this->orm->createDAO("pm_mg_rate");
                    $pm_rateDAO ->pm_id = $pm_id;
                    $pm_rateDAO ->pm_rate = 0;
                    $pm_rateDAO ->last_modify = time();
                    $pm_rateDAO ->save();
                }

                if($pm_info_id != ''){
                    $pm_id = $this->getpmidbetinfoid($pm_info_id);
                }
                if($type == 'add'){
                    $insertsql = "UPDATE `pm_mg_rate` `pm_mg_rate`
                                SET `pm_mg_rate`.`pm_rate` = CONCAT(pm_rate,',".$value."')
                                WHERE
                                    1 = 1
                                AND `pm_mg_rate`.`pm_id` = '".$pm_id."'";
                    $this->orm ->exec($insertsql);
                }else {
                    $updatesql = "UPDATE `pm_mg_rate` `pm_mg_rate`
                                SET `pm_mg_rate`.`pm_rate` = replace(pm_rate,'".$value."','')
                                WHERE
                                    1 = 1
                                AND `pm_mg_rate`.`pm_id` = '".$pm_id."'";

                    $this->orm ->exec($updatesql);

                    $updatesql = "UPDATE `pm_mg_rate` `pm_mg_rate`
                                SET `pm_mg_rate`.`pm_rate` = replace(pm_rate,',,','')
                                WHERE
                                    1 = 1
                                AND `pm_mg_rate`.`pm_id` = '".$pm_id."'";

                    $this->orm ->exec($updatesql);
                }

            }catch (Exception $e){
                throw $e;
            }
        }

        public function getpmidbetinfoid($info_id){
            if($info_id != ""){
                $infoDAO = $this->orm->createDAO("pm_mg_info");
                $infoDAO ->findId($info_id);
                $infoDAO = $infoDAO->get();

                if(!empty($infoDAO)){
                    $chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
                    $chouziDAO ->findPname($infoDAO[0]['pm_name']);
                    $chouziDAO = $chouziDAO->get();
                    return $chouziDAO[0]['id'];
                }
            }
        }

        public function getppinfobyids($str_ids){
            if(!empty($str_ids)){
                $ppDAO = $this->orm->createDAO('jjh_mg_pp');
                $ppDAO->selectLimt .= ' AND pid in('.$str_ids.')';
                return $ppDAO->get();
            }else {
                return array();
            }
        }

        public function getppmeetingbypid($pp_ids = ''){
            if(!empty($pp_ids)){
                $meetingDAO = $this->orm->createDAO('jjh_meeting');
                $meetingDAO->selectLimt .= ' AND meeting_joiner find_in_set('.$pp_ids.')';
                return $meetingDAO->get();
            }else {
                return array();
            }
        }

        public function getppfeedbackbypid($pp_ids = ''){
            if(!empty($pp_ids)){
                $feedbackDAO = $this->orm->createDAO('jjh_mg_feedback');
                $feedbackDAO->selectLimt .= ' AND find_in_set('.$pp_ids.')';
                return $feedbackDAO->get();
            }else {
                return array();
            }
        }

        /**
         * 获取部门信息
         */
        public function getdepartmentbyid($pid){
            if(!empty($pid)){
                $departmentinfo = $this->orm->createDAO("jjh_mg_department")->findId($pid);
                $departmentinfo = $departmentinfo->get();
                return $departmentinfo[0];
            }else {
                return false;
            }
        }

        public function getpmbyid($pid){
            if(!empty($pid)){
                $chouzi = $this->orm->createDAO("pm_mg_chouzi")->findId($pid);
                $chouzi = $chouzi->get();
                return $chouzi[0];
            }else {
                return false;
            }
        }



		/**
		 * 发送消息
		 * @param int $author_id 发送人ID
		 * @param int $user_id 接收人ID
		 * @param string $title 消息标题
		 * @param string $message 消息内容
		 * @param int $is_system 是否为系统该消息
		 * @param int $app_id 应用ID
		 * @return string
		 */
		protected function savemessage($author_id, $user_id, $title, $message, $is_system=0, $app_id=1){
			try {
				$user_infoDAO = $this->orm->createDAO('_users_info')->findUser_id($user_id);
				$user_infoDAO->notice += 1;
				$user_infoDAO->save();

				$user_pmDAO = $this->orm->createDAO('_users_pm');
				$user_pmDAO->author_id = $author_id;
				$user_pmDAO->user_id = $user_id;
				$user_pmDAO->title = $title;
				$user_pmDAO->message = str_replace("'", '"', $message);
				$user_pmDAO->is_system = $is_system;
				$user_pmDAO->is_new = 1;
				$user_pmDAO->app_id = $app_id;
				$user_pmDAO->datetime = time();
				return $user_pmDAO->save();

			}catch(Exception $e) {
				//$this->toErrorLogs($e);
				$this->alert_back(addslashes($e->getMessage()));
			}
		}

		/**
		 * 发送邮件
		 * @param string $subject
		 * @param string $body
		 * @param string $address
		 * @param string $user user_name
		 * @return boolean
		 */
		protected function SendEmail($subject, $body, $address, $user)
		{
			require_once 'phpmailer/class.phpmailer.php';
			$mail = new PHPMailer();
			$mail->IsSMTP(); 																				// telling the class to use SMTP
			$mail->Host = $this->systemSetting['smtp_server']; 								// SMTP server
			//$mail->SMTPDebug = 2;                     												// enables SMTP debug information (for testing)
			$mail->SMTPAuth = true;                 		 											// enable SMTP authentication
			$mail->SMTPSecure = $this->systemSetting['smtp_ssl']=='Y'?'ssl':'';		// sets the prefix to the servier
			$mail->Port = $this->systemSetting['smtp_port']?$this->systemSetting['smtp_port']:($this->systemSetting['smtp_ssl']=='Y'?'465':'25');		// set the SMTP port for the server
			$mail->Username = $this->systemSetting['smtp_username']; 				// SMTP account username
			$mail->Password = $this->systemSetting['smtp_password'];        			// SMTP account password
			$mail->SetFrom($this->systemSetting['from_email'], $this->systemSetting['site_name']);
			$mail->AddReplyTo($this->systemSetting['from_email'], $this->systemSetting['site_name']);
			$mail->Subject =$subject;
			$mail->CharSet = 'utf-8';
			$mail->MsgHTML($body);
			$mail->AddAddress($address, $user);
			return $mail->Send();
		}

		/**
		 * 获取用户信息
		 * @param string $uid
		 * @return $userinfo
		 */

		public function getuserinfoByidAction($uid){
			try {
				if(!empty($uid)){
					$user_infoDAO = $this->orm->createDAO("_users_info")->alias('u');
					$user_groupDAO = $user_infoDAO->with_users_group(array('group_id'=>'group_id'))->alias('g');
					$user_infoDAO ->findUser_id($uid);
					$userinfo = $user_infoDAO->get();
					return $userinfo;
				}
			}catch(Exception $e) {
				$this->toErrorLogs($e);
				$this->alert_back(addslashes($e->getMessage()));
			}
		}

        /**
         * 获取用户信息
         * @param string $uid
         * @return $userinfo
         */

        public function getadmininfoByidAction($uid){
            try {
                if(!empty($uid)){
                    $admininfoDAO = $this->orm->createDAO("my_admin");
                    $admininfoDAO ->findId($uid);
                    $admininfoDAO = $admininfoDAO->get();
                    return $admininfoDAO;
                }
            }catch(Exception $e) {
                $this->toErrorLogs($e);
                $this->alert_back(addslashes($e->getMessage()));
            }
        }

		/**
		 * 读取基本配置信息
		 * @param string $varname
		 */
		public function getSystemSetting(){
			try{
				$system = $this->orm->createDAO('_system_setting')->get();
				foreach ($system as $key => $value){
					$setting[$value['varname']] = unserialize($value['value']);
				}
				return $setting;
			}catch(Exception $e) {
				$this->toErrorLogs($e);
				$this->alert_back(addslashes($e->getMessage()));
			}
		}

		/**
		 * 设置系统配置
		 * @param array $setting
		 */
		public function setSystemSeting($setting){
			try{
				if($setting){
					foreach ($setting as $key => $value){
						$systemDAO = $this->orm->createDAO('_system_setting')->findVarname($key);
						$systemDAO->value = serialize($value);
						$systemDAO->save();
					}
					return true;
				} else {
					return false;
				}
			}catch(Exception $e) {
				$this->toErrorLogs($e);
				$this->alert_back(addslashes($e->getMessage()));
			}
		}

        public function toErrorLogs(){

        }
	}