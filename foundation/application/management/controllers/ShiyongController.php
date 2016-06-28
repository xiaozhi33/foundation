<?php
	require_once("BaseController.php");
	class Management_shiyongController extends BaseController {
		private $dbhelper;
		public function indexAction(){
            $type = $_REQUEST["type"];
			$pname = HttpUtil::postString("pname");
			$zhichudate = HttpUtil::postString("zhichudate");
			$pminfo = new pm_mg_infoDAO();

			if($pname != ""){
                $pminfo ->selectLimit .= " and pm_name like '%".$pname."%'";
			}
			
			if($zhichudate != ""){
				$pminfo ->shiyong_zhichu_datetime = $zhichudate;
			}
            $pminfo ->shiyong_type = $type;
			
			$pminfo ->selectLimit .= " and cate_id = 1 order by shiyong_zhichu_datetime desc";
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
			$pageDAO = $pageDAO ->pageHelper($pminfo,null,"index",null,'get',20,8);
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('shiyonglist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
            $this->view->assign('type',$type);

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
					alert_back("您输入的信息不完整，请查正后继续添加");
				}

				$pm_mg_infoDAO = new pm_mg_infoDAO();
				$pm_mg_infoDAO ->beizhu = $beizhu;
				$pm_mg_infoDAO ->jiangli_renshu = $jiangli_renshu;
				$pm_mg_infoDAO ->jiangli_fanwei = $fanwei;
				$pm_mg_infoDAO ->pm_name = $pname;
				$pm_mg_infoDAO ->shiyong_zhichu_datetime = $shiyong_zhichu_datetime;
				$pm_mg_infoDAO ->shiyong_zhichu_jiner = $shiyong_zhichu_jiner;
				$pm_mg_infoDAO ->pm_juanzeng_cate = HttpUtil::postString("pm_cate");
				$pm_mg_infoDAO ->shiyong_type = $shiyong_type;

				$pm_mg_infoDAO ->cate_id = 1;

				if($_FILES['pm_files']['name']!=""){
					if($_FILES['pm_files']['error'] != 4){
						if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
							mkdir(__UPLOADPICPATH__ ."jjh_download/");
						}
						$uploadpic = new uploadPic($_FILES['pm_files']['name'],$_FILES['pm_files']['error'],$_FILES['pm_files']['size'],$_FILES['pm_files']['tmp_name'],$_FILES['pm_files']['type'],2);
						$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
						$result = $uploadpic->uploadPic();
						if($result['error']!=0){
							alert_back($result['msg']);
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

				alert_go("添加成功","/management/shiyong");
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
					alert_back("您输入的信息不完整，请查正后继续添加");
				}
				
				$pm_mg_infoDAO = new pm_mg_infoDAO($_REQUEST['id']);
				$pm_mg_infoDAO ->beizhu = $beizhu;
				$pm_mg_infoDAO ->jiangli_renshu = $jiangli_renshu;
				$pm_mg_infoDAO ->jiangli_fanwei = $fanwei;
				$pm_mg_infoDAO ->pm_name = $pname;
                $pm_mg_infoDAO ->shiyong_type = $shiyong_type;
				$pm_mg_infoDAO ->shiyong_zhichu_datetime = $shiyong_zhichu_datetime;
				$pm_mg_infoDAO ->shiyong_zhichu_jiner = $shiyong_zhichu_jiner;
				$pm_mg_infoDAO ->pm_juanzeng_cate = HttpUtil::postString("pm_cate");
				
				if($_FILES['pm_files']['name']!=""){
					if($_FILES['pm_files']['error'] != 4){
						if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
						     mkdir(__UPLOADPICPATH__ ."jjh_download/");
						}
						$uploadpic = new uploadPic($_FILES['pm_files']['name'],$_FILES['pm_files']['error'],$_FILES['pm_files']['size'],$_FILES['pm_files']['tmp_name'],$_FILES['pm_files']['type'],2);
						$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
						$result = $uploadpic->uploadPic();
						if($result['error']!=0){					    	
						   	alert_back($result['msg']);
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

				alert_go("编辑成功","/management/shiyong");
			}else{
				alert_back("操作失败");
			}
		}

		/**
		 * 未认领列表
		 */
		public function claimlistAction()
		{
            $time_str = "";
			// 同步财务支出（使用）信息
			$zwpzflDAO = new CW_API();
			$zwpzfl_list = $zwpzflDAO ->getzwpzfl($time_str); // $time_str 上次同步最大时间

			// 遍历循环插入zw_mg_pzfl_log表中
			foreach($zwpzfl_list as $k => $v){
				$lk = $this->islkrl($v['lsh']);  // 判断是否重复添加

				if(empty($lk)){
					$zw_lkrl_logsDAO = $this->orm->createDAO("zw_lkrl_logs");
					$zw_lkrl_logsDAO ->lsh = $v['lsh'];
					$zw_lkrl_logsDAO ->lkrq = $v['lkrq'];
					$zw_lkrl_logsDAO ->fkdw = $v['fkdw'];
					$zw_lkrl_logsDAO ->je = $v['je'];
					$zw_lkrl_logsDAO ->rlje = $v['rlje'];
					$zw_lkrl_logsDAO ->lrrq = $v['lrrq'];
					$zw_lkrl_logsDAO ->lrr = $v['lrr'];
					$zw_lkrl_logsDAO ->save();
				}else {
					continue;
				}
			}

			$this->synclkrl();  // 同步财务系统来款数据

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
			$like_sql .= "  ORDER BY `zijin_daozhang_datetime` DESC";
			$this->renling_weirenling_list->findCate_id("0");
			$this->renling_weirenling_list->selectLimit = $like_sql;
			$this->renling_weirenling_list = $this->renling_weirenling_list->get();

			$total = count($this->renling_weirenling_list);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO->pageHelper($this->renling_weirenling_list, null, "/management/zijin/claimlist", null, 'get', 25, 8);
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php", "", $pages);
			$this->view->assign('claimlist', $pageDAO['pageData']);
			$this->view->assign('page', $pages);
			$this->view->assign('total', $total);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render("zijin/claimlist.phtml");
			echo $this->view->render("index/footer.phtml");
		}

        /**
         * 同步来款信息，新同步到项目信息表中，并记录到同步记录表中
         */
        public function synclkrl(){
            $zw_lkrl_logsDAO = $this->orm->createDAO("zw_lkrl_logs");
            $zw_lkrl_logsDAO ->selectLimit .= " and status=0";
            $zw_lkrl_list = $zw_lkrl_logsDAO->get();

            if(!empty($zw_lkrl_list)){
                foreach($zw_lkrl_list as $key => $value){  // 批量添加财务来款到项目info中
                    $islog = $this->getisuselog($value['lsh']);
                    if($islog){   // 判断是否已经存在同步记录
                        $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
                        $pm_mg_infoDAO ->cate_id = 0;
                        $pm_mg_infoDAO ->pm_pp_company = $value["fkdw"];              // 付款单位
                        $pm_mg_infoDAO ->zijin_daozhang_datetime = $value["lkrq"];  //  来款日期
                        $pm_mg_infoDAO ->zijin_daozheng_jiner = $value["je"];        // 金额
                        //$pm_mg_infoDAO ->pm_pp_company = $value["lrrq"];              // 付款单位
                        $pm_mg_infoDAO ->renling_name = $value["lrr"];                // 付款单位
                        $pm_mg_infoDAO ->lsh = $value["lsh"];                           // 财务流水号
                        $pm_mg_infoDAO ->save();

                        $zw_lkrl_logs1DAO = $this->orm->createDAO("zw_lkrl_logs");
                        $zw_lkrl_logs1DAO ->findLsh($value['lsh']);
                        $zw_lkrl_logs1DAO ->status = 1;
                        $zw_lkrl_logs1DAO ->save();
                    }
                }
            }
        }

		/**
		 * 绑定认领
		 */
		public function savebindingclaimAction(){
			try{
				(int)$pid = $_REQUEST['zw_xmbh'];
				(int)$department_id = $_REQUEST['zw_bmbh'];
				if(empty($pid) || empty($department_id)){
					alert_back("请选择认领项目和部门 或 该部门没有绑定财务部门，请联系管理员");
				}

				// 1, 查看项目财务对照表－取得财务对应项目名称和编号
				$pm_relateDAO = $this->orm->createDAO("zw_pm_related");
				$pm_relateDAO ->findPm_id($_REQUEST['pm_xmbh']);
				$pm_relateDAO = $pm_relateDAO->get();

				if(empty($pm_relateDAO[0]['zw_xmbh']) || empty($pm_relateDAO[0]['zw_xmmc'])){
					alert_back("该项目没有绑定财务系统，请联系管理员");
				}

				// 2, 部门信息同步
				$department_info = $this->orm->createDAO("zw_department_related");
				$department_info ->findPm_pid($_REQUEST['pm_bmbh']);
				$department_info = $department_info->get();

				if(empty($department_info[0]['zw_bmbh']) || empty($department_info[0]['zw_bmmc'])){
					alert_back("该部门没有绑定财务部门，请联系管理员");
				}

				// 3, 负者人信息同步

				// 4, 同步更新财务系统lkrl表
				$lsh = $_REQUEST['lsh'];    // 流水号
				$rlxh = $_REQUEST["pm_id"];   // 认领序号
				$rlrq = date("Ymd" , time());   // 认领日期
				$rlr = $_REQUEST['lrr'];        // 认领人
				$rlrbh = $_REQUEST['lsh'];      // 认领人编号
				$bmbh = $_REQUEST['zw_bmbh'];   // 部门编号
				$xmbh = $_REQUEST['zw_xmbh'];   // 项目编号
				$rlje = $_REQUEST['je'];   // 认领金额
				$ispz = 0;                       // 是否制单
				$rlpznm = "";            // 认领凭证内码
				$czy = "admin";                                     // 操作员

				$zw_lkrlDAO = new CW_API();
				$rs = $zw_lkrlDAO ->addlkrl($lsh, $rlxh, $rlrq, $rlr, $rlrbh, $bmbh, $xmbh, $rlje, $ispz, $rlpznm, $czy);
				if($rs){
					// 更新项目来款表
					$pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
					$pm_mg_infoDAO ->findId($_REQUEST["pm_id"]);
					$pm_mg_infoDAO ->jindu = $_REQUEST["jindu"];                // 来款进度 已到帐 未到帐
					$pm_mg_infoDAO ->piaoju = $_REQUEST["piaoju"];              // 票据
					$pm_mg_infoDAO ->piaoju_kddh = $_REQUEST["piaoju_kddh"];  // 快递单号
					$pm_mg_infoDAO ->piaoju_jbr = $_REQUEST["piaoju_jbr"];    // 经办人
					$pm_mg_infoDAO ->piaoju_fkfs = $_REQUEST["piaoju_fkfs"];  // 反馈方式 领取 寄送 暂存
					$pm_mg_infoDAO ->piaoju_fph = $_REQUEST["piaoju_fph"];    // 发票号

					$pm_mg_infoDAO ->cate_id = 0;   // 类型 0资金 1使用
					$pm_mg_infoDAO ->pm_name = $_REQUEST['zw_xmmc'];   // 类型 0资金 1使用
					$pm_mg_infoDAO ->pm_pp = HttpUtil::postString("pm_pp");   // 付款单位
					$pm_mg_infoDAO ->pm_pp_cate = HttpUtil::postString("pm_pp_cate");   // 捐赠者类型 基金会/企业/校友/社会人士
					$pm_mg_infoDAO ->zijin_laiyuan_qudao = HttpUtil::postString("zijin_laiyuan_qudao");   // 渠道 境内 境外
					$pm_mg_infoDAO ->beizhu = HttpUtil::postString("other");   // 备注
					$pm_mg_infoDAO ->is_renling = 1;                            // 是否认领flag 已认领

					$pm_mg_infoDAO ->save();
					if($rs){
						alert_go("认领成功！", "/management/zijin/claimlist");
					}else {
						alert_back("认领失败！请联系管理员");
					}
				}else {
					alert_back("认领失败！请联系管理员");
				}
			}catch(Exception $e){
				throw $e;
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

			// 获取支出（使用）认领详细信息
			$zw_mg_pzfl_logDAO = $this->orm->createDAO("zw_mg_pzfl_log");
			$zw_mg_pzfl_logDAO ->findLsh($pm_mg_infoDAO[0]['lsh']);
			$zw_mg_pzfl_logDAO = $zw_mg_pzfl_logDAO ->get();
			$this->view->assign('zw_mg_pzfl_log', $zw_mg_pzfl_logDAO);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render("zijin/claim.phtml");
			echo $this->view->render("index/footer.phtml");
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
            $pm_chouzi ->selectLimit .= " order by id desc";
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);
		}
	}
?>