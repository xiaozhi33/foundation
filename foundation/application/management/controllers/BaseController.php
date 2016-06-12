<?php
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
            $admininfo = SessionUtil::getAdmininfo();
            $this->admininfo = SessionUtil::getAdmininfo();

            //捐赠项目金额
            $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info")->findCate_id(0)->select(" sum(zijin_daozheng_jiner) as allsum")->get();

            //会议活动
            $meetingDAO = $this->orm->createDAO("jjh_meeting")->get();

			$this->view->assign(array(
				"module" => $request_mod['module'],
				"controller" => $request_mod['controller'],
				"action" => $request_mod['action'],
                'renling_weirenling_list' => $renling_weirenling_list,
                "pm_count" => count($this->pm_count),
                "allsum" => (int)$pm_mg_infoDAO[0]['allsum'],
                "meeting_count" => count($meetingDAO),
                'admininfo' => $admininfo,
			));
			
		    $this->_init();
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
        public function changerate($pm_id, $type='add',$value)
        {
            try{
                if($type == 'add'){
                    $insertsql = "UPDATE `pm_mg_rate` `pm_mg_rate`
                                SET `pm_mg_rate`.`pm_rate` = CONCAT(pm_rate,',".$value."')
                                WHERE
                                    1 = 1
                                AND `pm_mg_rate`.`pm_id` = '".$pm_id."'";
ECHO $insertsql;EXIT;
                    $this->orm ->exec($insertsql);
                }else {
                    $updatesql = "UPDATE `pm_mg_rate` `pm_mg_rate`
                                SET `pm_mg_rate`.`pm_rate` = replace(pm_rate,'".$value."','')
                                WHERE
                                    1 = 1
                                AND `pm_mg_rate`.`pm_id` = '".$pm_id."'";

                    $this->orm ->exec($updatesql);
                }

            }catch (Exception $e){
                throw $e;
            }
        }
	}