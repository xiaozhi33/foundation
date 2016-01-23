<?php
	require_once("sessionutil.php");
	require_once("httputil.php");
	require_once("functions.php");
	require_once 'resizepic.php'; //创建缩略图
	require_once 'uploadpic.php';
	
	$uploadpicpath = __UPLOADPICPATH__;//上传图片路径

	class BaseController extends Zend_Controller_Action
	{
        protected $orm;
		public function init()
	    {
	    	$request_mod = $this->getRequest()->getParams();
			$this->view = new Zend_View();
			$this->view ->addScriptPath('application/management/views/scripts');
            $this->orm = ORM::getInstance();
            //$this->WhiteIP();

            //获取认领信息
            $renling_weirenling_list = $this->orm->createDAO("pm_mg_chouzi")->findIs_renling("")->get();
            $renling = $this->orm->createDAO("pm_mg_chouzi")->get();
			
			$this->view->assign(array(
				"module" => $request_mod['module'],
				"controller" => $request_mod['controller'],
				"action" => $request_mod['action'],
                'renling_weirenling_list' => $renling,
			));
			
		    $this->_init();
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
	}