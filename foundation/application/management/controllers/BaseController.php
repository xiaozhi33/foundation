<?php
    session_start();
	require_once("sessionutil.php");
	require_once("httputil.php");
	require_once("functions.php");
	require_once 'resizepic.php'; //创建缩略图
	require_once 'uploadpic.php';

    // mssql 数据库操作类
    require_once("cw_api.class.php");
    require_once("mssql_db.class.php");
	
	$uploadpicpath = __UPLOADPICPATH__;//上传图片路径

	class BaseController extends Zend_Controller_Action
	{
        protected $orm;
        public $mssql_class;
        public $admininfo = '';
        public $renling_weirenling_list = "";
        public $shiyong_weirenling_list = "";
        public $task_init_array = "";

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
            $this->admininfo = $admininfo['admin_info'];

            //捐赠项目金额
            $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info")->findCate_id(0)->select(" sum(zijin_daozheng_jiner) as allsum")->get();

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

            $admin_list = $this->orm->createDAO("my_admin")->get();
            foreach($admin_list as $k => $v){
                $_admin_list[$v['id']] = $v['admin_name'];
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
                'admininfo' => $admininfo,
                'task_init_array' => $this->task_init_array,
                'admin_list_info' => $_admin_list,
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

        public function byte_format($size,$dec=2)
        {
            $a = array("B", "KB", "MB", "GB", "TB", "PB","EB","ZB","YB");
            $pos = 0;
            while ($size >= 1024)
            {
                $size /= 1024;
                $pos++;
            }
            return round($size,$dec)." ".$a[$pos];
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
	}