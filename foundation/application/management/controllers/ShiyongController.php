<?php
	require_once("BaseController.php");
	class Management_shiyongController extends BaseController {
		private $dbhelper;
		public function indexAction(){
            //$type = $_REQUEST["type"];
			$shiyong_type = $_REQUEST["shiyong_type"];
			$pname = $_REQUEST["pname"];
			$zhichudate = $_REQUEST["zhichudate"];
			$pminfo = new pm_mg_infoDAO();

			if($pname != ""){
                $pminfo ->selectLimit .= " and pm_name like '%".$pname."%'";
			}

			if($shiyong_type != ""){
				$pminfo ->selectLimit .= " and shiyong_type=".$shiyong_type;
			}
			
			if($zhichudate != ""){
				$pminfo ->shiyong_zhichu_datetime = $zhichudate;
			}
            //$pminfo ->shiyong_type = $type;
			
			$pminfo ->selectLimit .= " and cate_id = 1 and is_renling = 1 order by lastmodify DESC,shiyong_zhichu_datetime desc";
			//$pminfo ->debugSql = true;
			
			$pminfo = $pminfo->get($this->dbhelper);

			// 获取项目类型
			if(!empty($pminfo)){
				foreach($pminfo as $keys => $values){
					$pminfo[$keys]['catename'] = $this->gettypebypname($values['pm_name']);
				}
			}

			$total = count($pminfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($pminfo,null,"/management/shiyong/index",null,'get',20,8);
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('shiyonglist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
            //$this->view->assign('type',$type);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render("shiyong/index.phtml");
			echo $this->view->render("index/footer.phtml");
		}

		public function gettypebypname($pname){
			if(!empty($pname)){
				$pmDAO = $this->orm->createDAO("pm_mg_chouzi");
				$pmDAO ->withJjh_mg_cate(array("cate"=>"id"));
				$pmDAO ->select(" pm_mg_chouzi.*, jjh_mg_cate.catename");
				$pmDAO ->findPname($pname);
				$pmDAO = $pmDAO->get();
				return $pmDAO[0]['catename'];
			}

		}
		
		public function addshiyongAction(){
            $type = $_REQUEST["type"];
            $this->view->assign('type',$type);
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("shiyong/addshiyong.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrsshiyongAction(){
			try{
				//error_reporting(E_ALL);
				//ini_set( 'display_errors', 'On' );

				$shiyong_type = HttpUtil::postString("shiyong_type");
				$pname = HttpUtil::postString("pname");  //项目编号
				$shiyong_zhichu_datetime = HttpUtil::postString("shiyong_zhichu_datetime");		 //项目支出日期
				$shiyong_zhichu_jiner = HttpUtil::postString("shiyong_zhichu_jiner");  //项目支出金额
				$fanwei = HttpUtil::postString("fanwei");  //捐赠范围
				$jiangli_renshu = HttpUtil::postString("jiangli_renshu");  //奖励人数
				$beizhu = HttpUtil::postString("beizhu");  //备注

				if($pname == "" || $shiyong_zhichu_datetime == "" || $shiyong_zhichu_jiner == ""){
					$this->alert_back("您输入的信息不完整，请查正后继续添加");
				}

				if($jiangli_renshu == ''){
					$jiangli_renshu = 0;
				}

				$pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
				$pm_mg_infoDAO ->beizhu = $beizhu;
				$pm_mg_infoDAO ->jiangli_renshu = $jiangli_renshu;
				$pm_mg_infoDAO ->jiangli_fanwei = $fanwei;
				$pm_mg_infoDAO ->pm_name = $pname;
				$pm_mg_infoDAO ->shiyong_zhichu_datetime = $shiyong_zhichu_datetime;
				$pm_mg_infoDAO ->shiyong_zhichu_jiner = $shiyong_zhichu_jiner;
				$pm_mg_infoDAO ->pm_juanzeng_cate = HttpUtil::postString("pm_cate");
				$pm_mg_infoDAO ->shiyong_type = $shiyong_type;

				$pm_mg_infoDAO ->cate_id = 1;
				$pm_mg_infoDAO ->is_renling = 1; // 后台添加默认为已认领

				$pm_mg_infoDAO ->lastmodify = time();

				$pm_mg_infoDAO ->sign_id = HttpUtil::postString("pm_sign_id");

				if($_FILES['pm_files']['name']!=""){
					if($_FILES['pm_files']['error'] != 4){
						if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
							mkdir(__UPLOADPICPATH__ ."jjh_download/");
						}
						$uploadpic = new uploadPic($_FILES['pm_files']['name'],$_FILES['pm_files']['error'],$_FILES['pm_files']['size'],$_FILES['pm_files']['tmp_name'],$_FILES['pm_files']['type'],2);
						$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
						$result = $uploadpic->uploadPic();
						if($result['error']!=0){
							$this->alert_back($result['msg']);
						}else{
							$pm_mg_infoDAO->pm_file =  __GETPICPATH__."jjh_download/".$result['picname'];
						}
					}
				}
				$p_idinfo = $pm_mg_infoDAO ->save($this->dbhelper);

				$is_zhixing = HttpUtil::postString("is_zhixing");
				if($is_zhixing == '4'){
					$this->changerate("",'add',4,$p_idinfo);
				}else {
					$this->changerate("",'add',4,$p_idinfo);
				}

				$this->alert_go("添加成功","/management/shiyong");
			}catch(Exception $e){
				throw $e;
			}
		}
		
		public function editshiyongAction(){
			if($_REQUEST['id'] != ""){
				$pm_mg_infoDAO = new pm_mg_infoDAO($_REQUEST['id']);
				$pm_mg_infoDAO = $pm_mg_infoDAO ->get($this->dbhelper);
				$this->view->assign("pm_mg_info",$pm_mg_infoDAO);


				$pm_mg_rateDAO = $this->orm->createDAO('pm_mg_rate');
				$pid = $this->getpmidbetinfoid($_REQUEST['id']);
				$pm_mg_rateDAO ->findPm_id($pid);
				$pm_mg_rateDAO = $pm_mg_rateDAO ->get();
				$this->view->assign("rate_list_new", $pm_mg_rateDAO);

				echo $this->view->render("index/header.phtml");
				echo $this->view->render("shiyong/editshiyong.phtml");
				echo $this->view->render("index/footer.phtml");
			}else{
				alert_back("操作失败");
			}
			
		}
		
		public function editrsshiyongAction(){
			if($_REQUEST['id'] != ""){
				$pname = HttpUtil::postString("pname");  //项目编号
				$shiyong_zhichu_datetime = HttpUtil::postString("shiyong_zhichu_datetime");		 //项目支出日期
				$shiyong_zhichu_jiner = HttpUtil::postString("shiyong_zhichu_jiner");  //项目支出金额
				$fanwei = HttpUtil::postString("fanwei");  //捐赠范围
				$jiangli_renshu = HttpUtil::postString("jiangli_renshu");  //奖励人数
				$beizhu = HttpUtil::postString("beizhu");  //备注

                $shiyong_type = HttpUtil::postString("shiyong_type");
					
				if($pname == "" || $shiyong_zhichu_datetime == "" || $shiyong_zhichu_jiner == ""){
					$this->alert_back("您输入的信息不完整，请查正后继续添加");
				}
				
				$pm_mg_infoDAO = $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info")->findId($_REQUEST['id']);
				$pm_mg_infoDAO ->beizhu = $beizhu;
				$pm_mg_infoDAO ->jiangli_renshu = $jiangli_renshu;
				$pm_mg_infoDAO ->jiangli_fanwei = $fanwei;
				$pm_mg_infoDAO ->pm_name = $pname;
                $pm_mg_infoDAO ->shiyong_type = $shiyong_type;
				$pm_mg_infoDAO ->shiyong_zhichu_datetime = $shiyong_zhichu_datetime;
				$pm_mg_infoDAO ->shiyong_zhichu_jiner = $shiyong_zhichu_jiner;
				$pm_mg_infoDAO ->pm_juanzeng_cate = HttpUtil::postString("pm_cate");

				$pm_mg_infoDAO ->lastmodify = time();

				if($_FILES['pm_files']['name']!=""){
					if($_FILES['pm_files']['error'] != 4){
						if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
						     mkdir(__UPLOADPICPATH__ ."jjh_download/");
						}
						$uploadpic = new uploadPic($_FILES['pm_files']['name'],$_FILES['pm_files']['error'],$_FILES['pm_files']['size'],$_FILES['pm_files']['tmp_name'],$_FILES['pm_files']['type'],2);
						$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
						$result = $uploadpic->uploadPic();
						if($result['error']!=0){
							$this->alert_back($result['msg']);
						}else{				             
						    $pm_mg_infoDAO->pm_file =  __GETPICPATH__."jjh_download/".$result['picname'];
						}		            	    
					}
				}
				
				$logName = SessionUtil::getAdmininfo();
				addlog("修改使用信息-".$pname,$logName['admin_name'],$_SERVER['REMOTE_ADDR'],date("Y-m-d H:i:s",time()),json_encode($pm_mg_infoDAO));
			
				$pm_mg_infoDAO ->save($this->dbhelper);

				$is_zhixing = HttpUtil::postString("is_zhixing");
				if($is_zhixing == '4'){
					$this->changerate("",'add',4,$_REQUEST['id']);
				}else {
					$this->changerate("",'del',4,$_REQUEST['id']);
				}

				$this->alert_go("编辑成功","/management/shiyong");
			}else{
				$this->alert_back("操作失败");
			}
		}

		/**
		 * 未认领列表
		 */
		public function claimlistAction()
		{
			// 同步财务支出（使用）信息
			$zwpzflDAO = new CW_API();
			$zwpzfl_list = $zwpzflDAO ->getzwpzfl();

			// 同步bmbh
			/*$xmlist = $zwpzflDAO ->getxmlist();
			foreach ($xmlist as $k => $v){
				$zw_pm_relatedDAO = $this->orm->createDAO("zw_pm_related");
				$zw_pm_relatedDAO ->findZw_xmmc($v['xmmc']);
				$zw_pm_relatedDAO ->findZw_xmbh($v['xmbh']);
				$zw_pm_relatedDAO ->zw_bmbh = $v['bmbh'];
				$zw_pm_relatedDAO ->save();
			}
			exit();*/

			// 遍历循环插入zw_mg_pzfl_log表中
			foreach($zwpzfl_list as $k => $v){
				$pzfl = $this->ispzfl($v['pzrq'],$v['xmbh'],$v['jje'],$v['flbh']);  // 判断是否重复添加
				$xminfo = $zwpzflDAO ->getxminfo($v['bmbh'],$v['xmbh']);  // 获取项目的详细信息  部门编号+项目编号

				if(empty($pzfl)){
					$zw_mg_pzfl_logDAO = $this->orm->createDAO("zw_mg_pzfl_log");
					$zw_mg_pzfl_logDAO ->pzrq = $v['pzrq'];
					$zw_mg_pzfl_logDAO ->pznm = $v['pznm'];
					$zw_mg_pzfl_logDAO ->flbh = $v['flbh'];
					$zw_mg_pzfl_logDAO ->jje = $v['jje'];
					$zw_mg_pzfl_logDAO ->zy = $v['zy'];    // 用途
					$zw_mg_pzfl_logDAO ->status = 0;  // 状态为0 未同步  1已同步  2忽略
					$zw_mg_pzfl_logDAO ->last_modify = time();
					$zw_mg_pzfl_logDAO ->kmbh = $v['kmbh'];
					$zw_mg_pzfl_logDAO ->bmbh = $v['bmbh'];
					$zw_mg_pzfl_logDAO ->xmbh = $v['xmbh'];
					$zw_mg_pzfl_logDAO ->xmmc = $xminfo[0]['xmmc'];
					$zw_mg_pzfl_logDAO ->save();
				}else {
					continue;
				}
			}

			$this->syncpzfl();  // 同步财务系统支出数据

			$keywords = HttpUtil::getString("pm_name");
			$is_renling = HttpUtil::getString("is_renling");
			$this->renling_weirenling_list = $this->orm->createDAO("pm_mg_info");
			$like_sql = "";
			if($keywords != ""){
				$like_sql .= " AND pm_name like '%".$keywords."%'";
			}
			if($is_renling != ""){
				$like_sql .= " AND is_renling=".$is_renling;
			}else {
				$this->renling_weirenling_list->findIs_renling("0");
			}
			$like_sql .= "  ORDER BY `shiyong_zhichu_datetime` DESC";
			$this->renling_weirenling_list->findCate_id("1");
			$this->renling_weirenling_list->selectLimit = $like_sql;
			$this->renling_weirenling_list = $this->renling_weirenling_list->get();

			$total = count($this->renling_weirenling_list);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO->pageHelper($this->renling_weirenling_list, null, "/management/shiyong/claimlist", null, 'get', 25, 8);
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php", "", $pages);
			$this->view->assign('claimlist', $pageDAO['pageData']);
			$this->view->assign('page', $pages);
			$this->view->assign('total', $total);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render("shiyong/claimlist.phtml");
			echo $this->view->render("index/footer.phtml");
		}


		/**
		 * 删除认领log记录
		 */
		public function delClaimAction()
		{
			$lsh = $_REQUEST['lsh'];
			$pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
			$pm_mg_infoDAO ->findId($lsh);
			$pm_mg_infoDAO ->is_renling = 2; // 逻辑删除认领数据
			$pm_mg_infoDAO ->save();

			echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
			echo('<script language="JavaScript">');
			echo("alert('删除成功');");
			echo("location.href='/management/shiyong/claimlist';");
			echo('</script>');
			exit;
		}

        /**
         */
        public function syncpzfl(){
            $zw_mg_pzfl_logDAO = $this->orm->createDAO("zw_mg_pzfl_log");
			$zw_mg_pzfl_logDAO ->selectLimit .= " and status=0";
            $zw_pzfl_list = $zw_mg_pzfl_logDAO->get();

            if(!empty($zw_pzfl_list)){
                foreach($zw_pzfl_list as $key => $value){  // 批量添加财务来款到项目info中
					// 获取项目名称
					$zw_pm_relatedDAO = $this->orm->createDAO("zw_pm_related");
					$zw_pm_relatedDAO ->findZw_xmbh($value['xmbh']);
					$zw_pm_relatedDAO ->findZw_bmbh($value['bmbh']);
					$zw_pm_relatedDAO = $zw_pm_relatedDAO->get();

					if(!empty($zw_pm_relatedDAO[0]['pm_name'])){
						$islog = $this->getisuselog($value['pzrq'],$zw_pm_relatedDAO[0]['pm_name'],$value['jje'],$value['flbh']);
						if($islog){   // 判断是否已经存在同步记录
							$pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
							$pm_mg_infoDAO ->cate_id = 1;
							$pm_mg_infoDAO ->pm_name = $zw_pm_relatedDAO[0]['pm_name'];
							$pm_mg_infoDAO ->shiyong_zhichu_datetime = date("Y-m-d H:i:s",strtotime($value['pzrq']));
							$pm_mg_infoDAO ->shiyong_zhichu_jiner = $value['jje'];
							$pm_mg_infoDAO ->beizhu = $value['zy'];
							$pm_mg_infoDAO ->is_renling = 0;
							$pm_mg_infoDAO ->save();

							$zw_mg_pzfl_log1DAO = $this->orm->createDAO("zw_mg_pzfl_log");
							$zw_mg_pzfl_log1DAO ->findPzrq($value['pzrq']);
							$zw_mg_pzfl_log1DAO ->findXmbh($value['xmbh']);
							$zw_mg_pzfl_log1DAO ->findJje($value['jje']);
							$zw_mg_pzfl_log1DAO ->status = 1;
							$zw_mg_pzfl_log1DAO ->save();
						}
					}
                }
            }
        }

		/**
		 * 绑定认领
		 */
		public function savebindingclaimAction(){
			try{
				(int)$sign_id = $_REQUEST['sign_id'];
				(int)$pm_id = $_REQUEST['pm_id'];

				if(empty($sign_id)) {
					$this->alert_back("请选择支出对应协议！");
				}

				if(empty($_REQUEST["fanwei"])) {
					$this->alert_back("范围不能为空！");
				}

				// 更新项目来款表
				$pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
				$pm_mg_infoDAO ->findId($_REQUEST["pm_id"]);
				$pm_mg_infoDAO ->pm_pp_cate = $_REQUEST["pm_pp_cate"];                	// 支出类型
				$pm_mg_infoDAO ->jiangli_fanwei = $_REQUEST["fanwei"];              				// 范围
				$pm_mg_infoDAO ->jiangli_renshu = (int)$_REQUEST["jiangli_renshu"];  		// 奖励人数

				/*$is_zhixing = $_REQUEST["is_zhixing"];    				// 是否执行
				if($is_zhixing == '4'){
					$this->changerate("",'add',4,$p_idinfo);
				}else {
					$this->changerate("",'add',4,$p_idinfo);
				}*/

				$pm_mg_infoDAO ->pm_name = $_REQUEST["pname"];
				$pm_mg_infoDAO ->sign_id = $_REQUEST["pm_sign_id"];

				$pm_mg_infoDAO ->beizhu = $_REQUEST["beizhu"];
				$pm_mg_infoDAO ->is_renling = 1;                            				// 是否认领flag 已认领

				if($_FILES['pm_files']['name']!=""){
					if($_FILES['pm_files']['error'] != 4){
						if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
							mkdir(__UPLOADPICPATH__ ."jjh_download/");
						}
						$uploadpic = new uploadPic($_FILES['pm_files']['name'],$_FILES['pm_files']['error'],$_FILES['pm_files']['size'],$_FILES['pm_files']['tmp_name'],$_FILES['pm_files']['type'],2);
						$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
						$result = $uploadpic->uploadPic();
						if($result['error']!=0){
							$this->alert_back($result['msg']);
						}else{
							$pm_mg_infoDAO->pm_file =  __GETPICPATH__."jjh_download/".$result['picname'];
						}
					}
				}

				$admininfo = SessionUtil::getAdmininfo();

				$pm_mg_infoDAO ->renling_name = $admininfo['admin_name'];               // 认领人
				$pm_mg_infoDAO ->claim = $admininfo['admin_id'];                         // 认领人id
				$pm_mg_infoDAO ->claim_time = time();                                     // 认领时间
				$pm_mg_infoDAO ->lastmodify = time();

				$pm_mg_infoDAO ->save();
				$this->alert_go("认领成功！", "/management/shiyong");

			}catch(Exception $e){
				throw $e;
				$this->alert_back("认领失败！请联系管理员");
			}
		}


		/**
		 * 绑定认领页面
		 */
		public function bindingClaimAction(){
			// 获取部门信息
			$jjh_mg_departmentDAO = $this->orm->createDAO("jjh_mg_department");
			$department_list = $jjh_mg_departmentDAO ->get();
			$this->view->assign('department_list', $department_list);

			// 项目相关信息取得
			(int)$pid = $_REQUEST['pm_id'];
			$pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
			$pm_mg_infoDAO ->findId($pid);
			$pm_mg_infoDAO = $pm_mg_infoDAO ->get();
			$this->view->assign('pm_mg_info', $pm_mg_infoDAO);

			// 查看该笔支出是否还有效
			/*$pzflDAO = new CW_API();
			$rs = $pzflDAO ->getlpzfl(date('Ymd',strtotime($pm_mg_infoDAO[0]['shiyong_zhichu_datetime'])), $pm_mg_infoDAO[0]['shiyong_zhichu_jiner'], $pm_mg_infoDAO[0]['beizhu']);*/

			//echo date('Ymd',strtotime($pm_mg_infoDAO[0]['shiyong_zhichu_datetime'])).$pm_mg_infoDAO[0]['shiyong_zhichu_jiner']. $pm_mg_infoDAO[0]['beizhu'];
			//var_dump($rs);exit();

			if(empty($rs)){
				//$this->alert_back("该笔支出出现异常，请核对财务系统后再试！");
			}
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("shiyong/claim.phtml");
			echo $this->view->render("index/footer.phtml");
		}

		public function ispzfl($pzrq,$xmbh,$jje,$flbh){
			$zw_mg_pzfl_logDAO = $this->orm->createDAO("zw_mg_pzfl_log");
			$zw_mg_pzfl_logDAO ->findPzrq($pzrq);
			$zw_mg_pzfl_logDAO ->findXmbh($xmbh);
			$zw_mg_pzfl_logDAO ->findJje($jje);
			$zw_mg_pzfl_logDAO ->findFlbh($flbh);
			return $zw_mg_pzfl_logDAO->get();
		}

		public function getisuselog($pzrq,$xmmc,$jje,$flbh){
			if(!empty($jje) && !empty($pzrq) && !empty($xmmc)){
				$pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
				$pm_mg_infoDAO ->findShiyong_zhichu_jiner($jje);
				$pm_mg_infoDAO ->findPm_name($xmmc);
				$pm_mg_infoDAO ->findFlbh($flbh);
				$pm_mg_infoDAO ->findShiyong_zhichu_datetime(date("Y-m-d H:i:s",strtotime($pzrq)));
				$pm_mg_infoDAO = $pm_mg_infoDAO->get();
				if(empty($pm_mg_infoDAO)){
					return true;
				}else {
					return false;
				}
			}else {
				return false;
			}
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkmanagement();

			// 获取所有协议列表
			$pm_mg_signDAO = $this->orm->createDAO("pm_mg_sign");
			$pm_mg_signDAO ->select .=" pm_mg_sign.id, pm_mg_sign.pm_id, pm_mg_sign.sign_name, pm_mg_sign.xyje, pm_mg_sign.type ";
			$pm_mg_signDAO ->selectLimit .= " ORDER BY pm_mg_sign.sign_time DESC";
			$pm_mg_signDAO = $pm_mg_signDAO ->get();


			//项目名称列表
			$pm_chouzi = new pm_mg_chouziDAO();
			$pm_chouzi ->selectLimit .= " AND is_del=0";
			$pm_chouzi ->selectLimit .= " order by id desc";
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);

			if(!empty($pm_chouzi)){
				$pm_chouzi_array = array();
				foreach($pm_chouzi as $k => $v){
					$pm_chouzi_array[$v['id']] = $v['pname'];
				}
			}

			if(!empty($pm_mg_signDAO)){
				$pm_mg_signDAO_array = array();
				foreach($pm_mg_signDAO as $key => $value){
					$pm_mg_signDAO_array[$value['id']] = $value;
					$pm_mg_signDAO_array[$value['id']]['pname'] = $pm_chouzi_array[$value['pm_id']];
				}
			}

			$this->view->assign('pm_mg_signDAO', $pm_mg_signDAO_array);
			
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
			$pm_chouzi ->selectLimit .= " AND is_del=0";
			$pm_chouzi ->selectLimit .= " order by id desc";
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);
		}

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'addrsshiyong',
                'editrsshiyong',
                'claimlist',
                'savebindingclaim',
				'del-claim',
                //'binding-claim',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}
?>