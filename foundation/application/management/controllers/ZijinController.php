<?php
	require_once("BaseController.php");
	class Management_zijinController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$pname = HttpUtil::postString("pname");
			$department = HttpUtil::postString("department");
			$zijininfo = new pm_mg_infoDAO();
			
			if($pname != ""){
				$zijininfo ->pm_name = $pname;
			}
			
			if($department != ""){
				$zijininfo ->department = $department;
			}

			$zijininfo ->selectLimit = " and cate_id=0 order by lastmodify DESC,id desc";
            //$zijininfo ->selectLimit = " and is_renling=1"; // 显示已认领的项目
			//$chouziinfo ->debugSql =true;
			$zijininfo = $zijininfo->get($this->dbhelper);
			$total = count($zijininfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($zijininfo,null,"/management/zijin/index",null,'get',20,8);
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('zijinlist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render("zijin/index.phtml");
			echo $this->view->render("index/footer.phtml");
		}

        public function delzijinAction(){
            (int)$id = HttpUtil::getString("id");
            if(!empty($id)){
                $pm_zijinDAO = $this->orm->createDAO("pm_mg_info");
                $pm_zijinDAO ->findId($id);
                $pm_zijinDAO ->is_renling = '2';
                $pm_zijinDAO ->save($this->dbhelper);

                //$pm_zijinDAO ->delete($this->dbhelper);

                echo "<script>alert('操作成功！');";
                echo "window.location.href='/management/zijin/claimlist'";
                echo "</script>";
                exit();
            }
        }
		
		public function addzijinAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("zijin/addzijin.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrszijinAction(){
			$pname = HttpUtil::postString("pname");      //项目名称
			//$pm_pp = HttpUtil::postString("pm_pp");      //项目捐赠人名称

            // 立项时已经确定项目分类
            // $pm_cate = HttpUtil::postString("pm_cate");  //项目分类

            // 获取该项目的项目分类
            $chouziDAO = $this->orm->createDAO('pm_mg_chouzi');
            $chouziinfo = $chouziDAO ->findPname($pname)->get();

            $pm_cate = $chouziinfo[0]['cate'];

			$pm_pp_cate = HttpUtil::postString("pm_pp_cate");     //捐赠人类型
			$pm_juanzeng_jibie = HttpUtil::postString("pm_juanzeng_jibie");    //捐赠类别
			$pm_juanzeng_yongtu = HttpUtil::postString("pm_juanzeng_yongtu");  //项目用途
			$zijin_daozhang_datetime = HttpUtil::postString("zijin_daozhang_datetime"); //捐赠到账日期
			$zijin_daozheng_jiner = HttpUtil::postString("zijin_daozheng_jiner");       //捐赠到账金额
			$zijin_laiyuan_qudao = HttpUtil::postString("zijin_laiyuan_qudao");		 //捐赠渠道
			$pm_is_school = HttpUtil::postString("pm_is_school");		 //是否校友
			$peibi = HttpUtil::postString("peibi");		 				 //配比状态
			
			$peibi_jiner = HttpUtil::postString("peibi_jiner");
			$peibi_department = HttpUtil::postString("peibi_department");
			$peibi_card = HttpUtil::postString("peibi_card");
			$peibi_pp = HttpUtil::postString("peibi_pp");
			$peibi_jupi = HttpUtil::postString("peibi_jupi");
			
			
			$yishi = HttpUtil::postString("yishi");
			$jinianpin = HttpUtil::postString("jinianpin");
			
			$piaoju = HttpUtil::postString("piaoju");				     //票据
			$zhengshu = HttpUtil::postString("zhengshu");		         //证书
			$pm_pp_company = HttpUtil::postString("pm_pp_company");      //捐赠人公司介绍
			$beizhu = HttpUtil::postString("beizhu");		 //备注

            if($pm_cate == "")
            {
                alert_back("项目类型不能为空，请到对应到项目筹资中添加项目类型！");
            }

			$pm_zijinDAO = new pm_mg_infoDAO();

            // 项目捐赠方
            $pm_pp = implode(",",$_REQUEST['pm_pp']);
            $pm_zijinDAO ->pm_pp = $pm_pp;
            if($pname == "" || $pm_pp == "" || $zijin_daozhang_datetime == "" || $zijin_daozheng_jiner == ""){
                alert_back("您输入的信息不完整，请查正后继续添加");
            }

			$pm_zijinDAO ->pm_name = $pname;
			//$pm_zijinDAO ->pm_pp = $pm_pp;
			$pm_zijinDAO ->pm_juanzeng_jibie = $pm_juanzeng_jibie;
			$pm_zijinDAO ->pm_juanzeng_yongtu = $pm_juanzeng_yongtu;
			$pm_zijinDAO ->zijin_daozhang_datetime = $zijin_daozhang_datetime;
			$pm_zijinDAO ->zijin_daozheng_jiner = $zijin_daozheng_jiner;
			$pm_zijinDAO ->pm_is_school = $pm_is_school;
			$pm_zijinDAO ->peibi =$peibi;
			$pm_zijinDAO ->piaoju = $piaoju;
			$pm_zijinDAO ->zhengshu = $zhengshu;
			$pm_zijinDAO ->pm_pp_company = $pm_pp_company;
			$pm_zijinDAO ->beizhu = $beizhu;
			$pm_zijinDAO ->zijin_laiyuan_qudao = $zijin_laiyuan_qudao;
			$pm_zijinDAO ->pm_pp_cate = $pm_pp_cate;
			$pm_zijinDAO ->pm_juanzeng_cate = $pm_cate;
			
			$pm_zijinDAO ->peibi_jiner = $peibi_jiner;
			$pm_zijinDAO ->peibi_department = $peibi_department;
			$pm_zijinDAO ->peibi_card = $peibi_card;
			$pm_zijinDAO ->peibi_pp = $peibi_pp;
			$pm_zijinDAO ->peibi_jupi = $peibi_jupi;

            $pm_zijinDAO ->piaoju_fph = HttpUtil::postString("piaoju_fph");
            $pm_zijinDAO ->piaoju_fkfs = HttpUtil::postString("piaoju_fkfs");
			$pm_zijinDAO ->piaoju_jbr = HttpUtil::postString("piaoju_jbr");
            $pm_zijinDAO ->piaoju_kddh = HttpUtil::postString("piaoju_kddh");
            $pm_zijinDAO ->jindu = HttpUtil::postString("jindu");

			$pm_zijinDAO ->yishi = $yishi;
			$pm_zijinDAO ->jinianpin = $jinianpin;

            $pm_zijinDAO ->is_renling = 1;
			$pm_zijinDAO ->cate_id = 0;

            $pm_zijinDAO ->lastmodify = time();

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
					    $pm_zijinDAO->pm_file =  __GETPICPATH__."jjh_download/".$result['picname'];
					}		            	    
				}
			}
			$pm_zijinDAO ->save($this->dbhelper);

			alert_go("添加成功","/management/zijin");
		}
		
		public function editzijinAction(){
			if($_REQUEST['id'] != ""){
				$pm_zijininfo = new pm_mg_infoDAO($_REQUEST['id']);
				$pm_zijininfo = $pm_zijininfo ->get($this->dbhelper);
				$this->view->assign("zijin",$pm_zijininfo);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("zijin/editzijin.phtml");
				echo $this->view->render("index/footer.phtml");
			}else{
				alert_back("操作失败");
			}
			
		}
		
		public function editrszijinAction(){
			if($_REQUEST['id'] != ""){
				$pname = HttpUtil::postString("pname");      //项目名称
				$pm_pp = HttpUtil::postString("pm_pp");      //项目捐赠人名称
				//$pm_cate = HttpUtil::postString("pm_cate");  //项目分类
				$pm_pp_cate = HttpUtil::postString("pm_pp_cate");     //捐赠人类型
				$pm_juanzeng_jibie = HttpUtil::postString("pm_juanzeng_jibie");    //捐赠类别
				$pm_juanzeng_yongtu = HttpUtil::postString("pm_juanzeng_yongtu");  //项目用途
				$zijin_daozhang_datetime = HttpUtil::postString("zijin_daozhang_datetime"); //捐赠到账日期
				$zijin_daozheng_jiner = HttpUtil::postString("zijin_daozheng_jiner");       //捐赠到账金额
				$zijin_laiyuan_qudao = HttpUtil::postString("zijin_laiyuan_qudao");		 //捐赠渠道
				$pm_is_school = HttpUtil::postString("pm_is_school");		 //是否校友
				$peibi = HttpUtil::postString("peibi");		 				 //配比状态

                $peibi_jiner = HttpUtil::postString("peibi_jiner");
                $peibi_department = HttpUtil::postString("peibi_department");
                $peibi_card = HttpUtil::postString("peibi_card");
                $peibi_pp = HttpUtil::postString("peibi_pp");
                $peibi_jupi = HttpUtil::postString("peibi_jupi");

                $yishi = HttpUtil::postString("yishi");
                $jinianpin = HttpUtil::postString("jinianpin");

				$piaoju = HttpUtil::postString("piaoju");				     //票据
				$zhengshu = HttpUtil::postString("zhengshu");		         //证书
				$pm_pp_company = HttpUtil::postString("pm_pp_company");      //捐赠人公司介绍
				$beizhu = HttpUtil::postString("beizhu");		 //备注
				
				if($pname == "" || $pm_pp == "" || $zijin_daozhang_datetime == "" || $zijin_daozheng_jiner == ""){
					alert_back("您输入的信息不完整，请查正后继续添加");
				}
				
				$pm_zijinDAO = new pm_mg_infoDAO($_REQUEST['id']);
				$pm_zijinDAO ->pm_name = $pname;
				$pm_zijinDAO ->pm_pp = $pm_pp;
				$pm_zijinDAO ->pm_juanzeng_jibie = $pm_juanzeng_jibie;
				$pm_zijinDAO ->pm_juanzeng_yongtu = $pm_juanzeng_yongtu;
				$pm_zijinDAO ->zijin_daozhang_datetime = $zijin_daozhang_datetime;
				$pm_zijinDAO ->zijin_daozheng_jiner = $zijin_daozheng_jiner;
				$pm_zijinDAO ->pm_is_school = $pm_is_school;
				$pm_zijinDAO ->peibi =$peibi;

                $pm_zijinDAO ->peibi_jiner = $peibi_jiner;
                $pm_zijinDAO ->peibi_department = $peibi_department;
                $pm_zijinDAO ->peibi_card = $peibi_card;
                $pm_zijinDAO ->peibi_pp = $peibi_pp;
                $pm_zijinDAO ->peibi_jupi = $peibi_jupi;

                $pm_zijinDAO ->piaoju_fph = HttpUtil::postString("piaoju_fph");
                $pm_zijinDAO ->piaoju_fkfs = HttpUtil::postString("piaoju_fkfs");
                $pm_zijinDAO ->piaoju_jbr = HttpUtil::postString("piaoju_jbr");
                $pm_zijinDAO ->piaoju_kddh = HttpUtil::postString("piaoju_kddh");
                $pm_zijinDAO ->jindu = HttpUtil::postString("jindu");
			
				$pm_zijinDAO ->piaoju = $piaoju;
				$pm_zijinDAO ->zhengshu = $zhengshu;
				$pm_zijinDAO ->pm_pp_company = $pm_pp_company;
				$pm_zijinDAO ->beizhu = $beizhu;
				$pm_zijinDAO ->zijin_laiyuan_qudao = $zijin_laiyuan_qudao;
				//$pm_zijinDAO ->pm_juanzeng_cate = $pm_cate;
				$pm_zijinDAO ->pm_pp_cate = $pm_pp_cate;

                $pm_zijinDAO ->is_renling = $_REQUEST['is_renling'];

                $pm_zijinDAO ->yishi = $yishi;
                $pm_zijinDAO ->jinianpin = $jinianpin;

                $pm_zijinDAO ->lastmodify = time();
	
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
						    $pm_zijinDAO->pm_file =  __GETPICPATH__."jjh_download/".$result['picname'];
						}		            	    
					}
				}

                // 项目捐赠方
                $pm_pp = implode(",",$_REQUEST['pm_pp']);
                $pm_zijinDAO ->pm_pp = $pm_pp;
				
				//写日志
				$logName = SessionUtil::getAdmininfo();
				addlog("修改资金信息-".$pname,$logName['admin_name'],$_SERVER['REMOTE_ADDR'],date("Y-m-d H:i:s",time()),json_encode($pm_zijinDAO));
			
				$pm_zijinDAO ->save($this->dbhelper);

				alert_go("编辑成功","/management/zijin");
			}else{
				alert_back("操作失败");
			}
		}

        public function islkrl($lsh){
            $zw_lkrl_logsDAO = $this->orm->createDAO("zw_lkrl_logs");
            $zw_lkrl_logsDAO ->findLsh($lsh);
            return $zw_lkrl_logsDAO->get();
        }

        /**
         * 同步来款信息，先同步到项目信息表中，并记录到同步记录表中
         */
        public function synclkrl(){
            $zw_lkrl_logsDAO = $this->orm->createDAO("zw_lkrl_logs");
            $zw_lkrl_logsDAO ->selectLimit .= " and status=0 and is_del=0"; //is_del 是否逻辑删除
            $zw_lkrl_list = $zw_lkrl_logsDAO->get();

            if(!empty($zw_lkrl_list)){
                foreach($zw_lkrl_list as $key => $value){  // 批量添加财务来款到项目info中
                    $islog = $this->getisuselog($value['lsh']);
                    if($islog){   // 判断是否已经存在同步记录
                        $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
                        $pm_mg_infoDAO ->cate_id = 0;
                        $pm_mg_infoDAO ->pm_pp = $value["fkdw"];              // 捐赠者 付款单位
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

        public function getisuselog($lsh){
            if(!empty($lsh)){
                $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
                $pm_mg_infoDAO ->findLsh($lsh);
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

        /**
         * 未认领列表
         */
        public function claimlistAction()
        {
            // 同步财务来款信息
            //$zw_lkglDAO = new CW_API();
            //$lkgl_list = $zw_lkglDAO ->getlkgl();

            // 遍历循环插入lkrl_log表中
            /*foreach($lkgl_list as $k => $v){
                $lk = $this->islkrl($v['lsh']);  // 判断是否重复添加

                if(empty($lk[0]['lsh'])){
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

            $this->synclkrl();  // 同步财务系统来款数据*/

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
            $like_sql .= "  ORDER BY `lastmodify` DESC,`zijin_daozhang_datetime` DESC";
            $this->renling_weirenling_list->findCate_id("0");
            $this->renling_weirenling_list->selectLimit = $like_sql;
            $this->renling_weirenling_list = $this->renling_weirenling_list->get();

            $total = count($this->renling_weirenling_list);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($this->renling_weirenling_list, null, "/management/zijin/claimlist", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);

            // 查看是否有重复的认领数据
            /*$cf_DAO = $this->orm->createDAO('pm_mg_info');
            $cf_DAO ->selectLimit .= ' AND renling_name IN ( SELECT renling_name ss FROM pm_mg_info WHERE is_renling = 0 GROUP BY renling_name HAVING count(*) > 1 ) AND zijin_daozheng_jiner IN ( SELECT zijin_daozheng_jiner ss FROM pm_mg_info WHERE is_renling = 0 GROUP BY zijin_daozheng_jiner HAVING count(*) > 1 ) ';
            $cf_DAO = $cf_DAO->get();

            $this->view->assign('cf_DAO', $cf_DAO);
            var_dump($cf_DAO);exit();*/

            $this->view->assign('is_renling', $is_renling);
            $this->view->assign('claimlist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/claimlist.phtml");
            echo $this->view->render("index/footer.phtml");
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

                $logName = SessionUtil::getAdmininfo();
                $czy = $logName['admin_name'];       // 操作员 ？ 项目负责人 ？

                /*$zw_lkrlDAO = new CW_API();
                $rs = $zw_lkrlDAO ->addlkrl($lsh, $rlxh, $rlrq, $rlr, $rlrbh, $bmbh, $xmbh, $rlje, $ispz, $rlpznm, $czy);*/
                $rs = ture;
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
                    //$pm_mg_infoDAO ->pm_name = $_REQUEST['zw_xmmc'];   // 来款项目名称 以项目管理系统名称为准
                    $pm_mg_infoDAO ->pm_name = $pm_relateDAO[0]['pm_name'];   // 来款项目名称 以项目管理系统名称为准
                    $pm_mg_infoDAO ->pm_pp = HttpUtil::postString("pm_pp");   // 付款单位
                    $pm_mg_infoDAO ->pm_pp_cate = HttpUtil::postString("pm_pp_cate");   // 捐赠者类型 基金会/企业/校友/社会人士
                    $pm_mg_infoDAO ->zijin_laiyuan_qudao = HttpUtil::postString("zijin_laiyuan_qudao");   // 渠道 境内 境外
                    $pm_mg_infoDAO ->pm_is_school = HttpUtil::postString("pm_is_school");   // 是否校友
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
         * 删除认领log记录
         */
        public function delClaimAction()
        {
            $lsh = $_REQUEST['lsh'];
            $zw_lkrl_logsDAO = $this->orm->createDAO("zw_lkrl_logs");
            $zw_lkrl_logsDAO ->findLsh($lsh);
            $zw_lkrl_logs = $zw_lkrl_logsDAO->get();

            $zw_lkrl_logsDAO = $this->orm->createDAO("zw_lkrl_logs");
            $zw_lkrl_logsDAO ->findLsh($lsh);
            $zw_lkrl_logsDAO ->is_del = 1;
            $zw_lkrl_logsDAO ->save();

            $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
            $pm_mg_infoDAO ->findLsh($zw_lkrl_logs[0]['lsh']);
            $pm_mg_infoDAO ->is_renling = 2; // 逻辑删除认领数据
            $pm_mg_infoDAO ->save();

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/management/zijin/claimlist';");
            echo('</script>');
            exit;
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

            // 获取认领详细信息
            $zw_lkrl_logsDAO = $this->orm->createDAO("zw_lkrl_logs");
            $zw_lkrl_logsDAO ->findLsh($pm_mg_infoDAO[0]['lsh']);
            $zw_lkrl_logsDAO = $zw_lkrl_logsDAO ->get();
            $this->view->assign('zw_lkrl_logs', $zw_lkrl_logsDAO);
            
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/claim.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
         * ajax 请求对应的财务项目信息
         * 来款认领可以认领到任意项目下，但同步财务只能同步到其最上级项目（父项目，一级项目）
         */
        public function ajaxgetzwxmAction(){
            (int)$pm_id = $_REQUEST["pm_id"];

            // 取得该项目的父类项目（父项目，一级项目）
            if($pm_id != ''){
                $pm_id = $this->getsubpmidbypmid($pm_id);
            }

            $zw_pm_relatedDAO = $this->orm->createDAO("zw_pm_related");
            $zw_pm_relatedDAO ->findPm_id($pm_id);
            $zw_pm_relatedDAO = $zw_pm_relatedDAO->get();

            // 取得项目的部门信息
            $pm_mg_chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
            $pm_mg_chouziDAO ->findId($pm_id);
            $pm_mg_chouziDAO = $pm_mg_chouziDAO->get();

            $zw_pm_relatedDAO[0]['department'] = $pm_mg_chouziDAO[0]['department'];

            if($zw_pm_relatedDAO != ""){
                echo json_encode($zw_pm_relatedDAO[0]);
            }else {
                echo json_encode(array());
            }
        }

        /**
         * 根据项目id获取其父项目
         */
        public function getsubpmidbypmid($pm_id){
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
            $chouziDAO ->findId($pm_id);
            $chouziDAO = $chouziDAO ->get();

            if($chouziDAO[0]['parent_pm_id'] == 0){
                return $chouziDAO[0]['id'];
            }else {
                if($chouziDAO[0]['parent_pm_id_path'] != 0){
                    $temp_array = explode(',',$chouziDAO[0]['parent_pm_id_path']);
                    return $temp_array[0];
                }
            }
        }

        /**
         * ajax 请求对应的财务部门信息
         */
        public function ajaxgetzwbmAction(){
            (int)$pm_pid = $_REQUEST["pm_pid"];
            $zw_department_related = $this->orm->createDAO("zw_department_related");
            $zw_department_related ->findPm_pid($pm_pid);
            $zw_department_related = $zw_department_related->get();

            if($zw_department_related != ""){
                echo json_encode($zw_department_related[0]);
            }else {
                echo json_encode(array());
            }
        }

        /**
         * 项目立项
         */
        public function rateAction()
        {
            $name = HttpUtil::postString("pname");

            $signDAO = $this->orm->createDAO("pm_mg_sign");
            $signDAO ->withPm_mg_chouzi(array("pm_id" => "id"));
            $like_sql = "";
            if($name != "")
            {
                $like_sql .= " AND pm_mg_chouzi.pname like '%".$name."%'";
            }
            $like_sql .= " order by id desc";
            $signDAO->select(" pm_mg_sign.*,pm_mg_chouzi.pname");
            $signDAO->selectLimit = $like_sql;
            $signDAO = $signDAO ->get();

            $total = count($signDAO);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($signDAO, null, "/management/zijin/rate", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);
            $this->view->assign('signlist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/rate.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function rate_bakAction()
        {
            $name = HttpUtil::postString("pname");
            $rate = HttpUtil::postString("pm_rate");

            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
            $chouziDAO ->withPm_mg_rate(array("id" => "pm_id"));
            $like_sql = "";
            if($name != "")
            {
                $like_sql .= " AND pm_mg_chouzi.pname like '%".$name."%'";
            }
            if($rate != "")
            {
                $like_sql .= " AND pm_mg_rate.pm_rate like '%".$rate."%'";
            }
            $like_sql .= " order by id desc";
            $chouziDAO->selectLimit = $like_sql;
            $chouziDAO = $chouziDAO ->get();

            $total = count($chouziDAO);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($chouziDAO, null, "/management/zijin/rate", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);
            $this->view->assign('chouzilist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/rate.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        // 编辑进度详细
        public function rateinfoAction()
        {
            try{
                (int)$pm_id = HttpUtil::getString("pm_id");
                if(!empty($pm_id)){
                    $rate = HttpUtil::getString("rate");
                    $sign_list = $this->getsign($pm_id);
                    $rate_list = $this->getrateByid($pm_id);

                    // 如果没有设置进度，默认为洽谈中(已立项)
                    if(empty($rate_list)){
                        $pm_rateDAO = $this->orm->createDAO("pm_mg_rate");
                        $pm_rateDAO ->pm_id = $pm_id;
                        $pm_rateDAO ->pm_rate = 1;
                        $pm_rateDAO ->last_modify = time();
                        $pm_rateDAO ->save();
                    }

                    $rate_list_new = $this->getrateByid($pm_id);
                    //var_dump($rate_list_new);exit();

                    $this->view->assign("rate_list_new", $rate_list_new);
                    $this->view->assign("pm_id", $pm_id);

                    echo $this->view->render("index/header.phtml");
                    echo $this->view->render("zijin/editrate.phtml");
                    echo $this->view->render("index/footer.phtml");
                }
            }catch (Exception $e){
                throw $e;
            }
        }

        // 编辑进度
        public function editrsrateAction(){
            (int)$pm_id = HttpUtil::postString("pm_id");
            if(!empty($pm_id)){
                $rate = $_POST["rate"];
                $rate_str = "";
                if(!empty($rate)){
                    foreach($rate as $k => $v){
                        $rate_str .= $v.",";
                    }
                }

                $pm_rateDAO = $this->orm->createDAO("pm_mg_rate");
                $pm_rateDAO ->findPm_id($pm_id);
                $pm_rateDAO ->pm_rate = $rate_str;
                $pm_rateDAO ->last_modify = time();
                $pm_rateDAO ->save();
                //alert_go("编辑成功","/management/zijin/rate");
                alert_go("编辑成功","/management/chouzi/index");
            }else {
                alert_back("编辑失败");
            }
        }

        // 取得进度
        public function getrateByid($pid)
        {
            if(!empty($pid))
            {
                $pm_rateDAO = $this->orm->createDAO("pm_mg_rate");
                $pm_rateDAO ->findPm_id($pid);
                $pm_rateDAO = $pm_rateDAO ->get();

                return $pm_rateDAO;
            }else{
                return array();
            }
        }

        /**
         * 同步旧系统签约内容
         */
        public function syncsignAction()
        {
            $pm_mg_chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
            $pm_mg_chouziDAO ->select("id,pm_qianyue_datetime,pm_xieyii_dianziban");
            $pm_mg_chouziDAO ->selectLimit .= " and pm_qianyue_datetime!='' and pm_xieyii_dianziban !=''";
            $pm_mg_chouziDAO = $pm_mg_chouziDAO ->get();

            if(!empty($pm_mg_chouziDAO)){
                foreach($pm_mg_chouziDAO as $key => $value){
                    $pm_mg_signDAO = $this->orm->createDAO("pm_mg_sign");
                    $pm_mg_signDAO ->pm_id = $value['id'];
                    $pm_mg_signDAO ->sign_time = $value['pm_qianyue_datetime'];
                    $pm_mg_signDAO ->sign_files = $value['pm_xieyii_dianziban'];
                    $pm_mg_signDAO ->save();
                }
            }
        }

        public function signinfoAction(){
            (int)$pm_id = HttpUtil::getString("id");
            $pm_mg_signDAO = $this->getsign($pm_id);
            $this->view->assign("pid", $pm_id);
            $this->view->assign("signifo", $pm_mg_signDAO);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/signinfo.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
         * 添加协议
         */
        public function addsignAction(){
            (int)$id = HttpUtil::getString("id");
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
            $chouziDAO ->findId($id);
            $chouziDAO = $chouziDAO ->get();
            $this->view->assign("chouzilist", $chouziDAO);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/newsigninfo.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function editsignAction(){
            (int)$id = HttpUtil::getString("id");
            $pm_signDAO = $this->orm->createDAO("pm_mg_sign");
            $pm_signDAO ->withPm_mg_chouzi(array("pm_id" => "id"));
            $pm_signDAO ->findId($id);
            $pm_signDAO ->select("pm_mg_sign.*,pm_mg_chouzi.pname");
            $pm_signDAO = $pm_signDAO ->get();
            $this->view->assign("signinfo", $pm_signDAO[0]);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/newsigninfo.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
         * new签约信息
         */
        public function newsigninfoAction(){

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/newsigninfo.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
         * 添加协议
         */
        public function newsavesignAction()
        {
            (int)$pm_id = HttpUtil::postString("pm_id");
            $pm_signDAO = $this->orm->createDAO("pm_mg_sign");
            if(empty($pm_id)){
                echo "<script>alert('非法请求，请查正后再试！');";
                echo "window.location.href='/management/zijin/newsigninfo?id=".$pm_id."'; ";
                echo "</script>";
                exit();
            }

            /*if(HttpUtil::postString("jzys") == 1){
                if($_FILES['sign_files']['name']=="" || HttpUtil::postString("sign_time")=="" ){
                    echo "<script>alert('签约时间和电子协议不能为空！');";
                    echo "javascript:history.go(-1); ";
                    echo "</script>";
                    exit();
                }
            }*/

            /*			if(!empty($id)){
                            $pm_signDAO ->findId($pm_id);
                        }*/
            $pm_signDAO ->pm_id = $pm_id;
            $pm_signDAO ->sign_time = HttpUtil::postString("sign_time");
            $pm_signDAO ->jzys_time = HttpUtil::postString("jzys_time");
            $pm_signDAO ->xydz_time = HttpUtil::postString("xydz_time");

            if($_REQUEST['id']){
                $pm_signDAO ->findId($_REQUEST['id']);
            }

            if($_FILES['sign_files']['name']!=""){
                if($_FILES['sign_files']['error'] != 4){
                    if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
                        mkdir(__UPLOADPICPATH__ ."jjh_download/");
                    }
                    //echo $_FILES['sign_files']['type'];exit();
                    $uploadpic = new uploadPic($_FILES['sign_files']['name'],$_FILES['sign_files']['error'],$_FILES['sign_files']['size'],$_FILES['sign_files']['tmp_name'],$_FILES['sign_files']['type'],2);
                    $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
                    $result = $uploadpic->uploadPic();
                    if($result['error']!=0){
                        echo "<script>alert('".$result['msg']."');";
                        echo "window.location.href='/management/zijin/signinfo?id=".$pm_id."'; ";
                        echo "</script>";
                        exit();
                    }else{
                        $pm_signDAO->sign_files =  __GETPICPATH__."jjh_download/".$result['picname'];
                        $pm_signDAO->sign_files_name = $_FILES['sign_files']['name'];
                    }
                }
            }

            $pm_signDAO ->xyje = HttpUtil::postString("xyje");
            $pm_signDAO ->jzys = HttpUtil::postString("jzys");
            $pm_signDAO ->adress = HttpUtil::postString("adress");

            $pid = $pm_signDAO ->save();

            if($_REQUEST['id']) {
                $is_sign = HttpUtil::postString("is_sign");
                if ($is_sign == '2') {
                    $this->changerate($_REQUEST['id'], 'add', 2);
                } else {
                    $this->changerate($_REQUEST['id'], 'del', 2);
                }
            }

            if($pid){
                $is_sign = HttpUtil::postString("is_sign");
                if ($is_sign == '2') {
                    $this->changerate($pid, 'add', 2);
                } else {
                    $this->changerate($pid, 'del', 2);
                }
            }

            echo "<script>alert('编辑成功！');";
            echo "window.location.href='/management/zijin/rate'; ";
            echo "</script>";
            exit();
        }



		/**
		 * pm_mg_sign 签约
		 */
		public function getsign($pm_id)
		{
			if(!empty($pm_id))
			{
				$pm_signDAO = $this->orm->createDAO("pm_mg_sign");
                $pm_signDAO ->withPm_mg_chouzi(array("pm_id" => "id"));
				$pm_signDAO ->findPm_id($pm_id);
                $pm_signDAO ->select("pm_mg_sign.id,pm_mg_sign.pm_id,pm_mg_chouzi.pname,pm_mg_sign.sign_time,pm_mg_sign.sign_files,pm_mg_sign.sign_files_name");
				$pm_signDAO = $pm_signDAO ->get();
				
				return $pm_signDAO;
			}else{
				return array();
			}	
		}
		
		public function savesignAction()
		{
            (int)$pm_id = HttpUtil::postString("pm_id");
			$pm_signDAO = $this->orm->createDAO("pm_mg_sign");
            if(empty($pm_id)){
                echo "<script>alert('非法请求，请查正后再试！');";
                echo "window.location.href='/management/zijin/signinfo?id=".$pm_id."'; ";
                echo "</script>";
                exit();
            }

            if($_FILES['sign_files']['name']=="" || HttpUtil::postString("sign_time")=="" ){
                echo "<script>alert('签约时间和电子协议不能为空！');";
                echo "window.location.href='/management/zijin/signinfo?id=".$pm_id."'; ";
                echo "</script>";
                exit();
            }
/*			if(!empty($id)){
				$pm_signDAO ->findId($pm_id);
			}*/
			$pm_signDAO ->pm_id = $pm_id;
			$pm_signDAO ->sign_time = HttpUtil::postString("sign_time");

			if($_FILES['sign_files']['name']!=""){
				if($_FILES['sign_files']['error'] != 4){
					if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
						 mkdir(__UPLOADPICPATH__ ."jjh_download/");
					}
					$uploadpic = new uploadPic($_FILES['sign_files']['name'],$_FILES['sign_files']['error'],$_FILES['sign_files']['size'],$_FILES['sign_files']['tmp_name'],$_FILES['sign_files']['type'],2);
					$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
					$result = $uploadpic->uploadPic();
					if($result['error']!=0){
                        echo "<script>alert('".$result['msg']."');";
                        echo "window.location.href='/management/zijin/signinfo?id=".$pm_id."'; ";
                        echo "</script>";
                        exit();
					}else{				             
						$pm_signDAO->sign_files =  __GETPICPATH__."jjh_download/".$result['picname'];
                        $pm_signDAO->sign_files_name = $_FILES['sign_files']['name'];
					}		            	    
				}
			}
			$pm_signDAO ->save();
            echo "<script>alert('添加成功！');";
            echo "window.location.href='/management/zijin/signinfo?id=".$pm_id."'; ";
            echo "</script>";
            exit();
		}

        // 文件下载
        public function downloadAction(){
            if($_GET){
                (int)$id = HttpUtil::getString('id');
                $pm_signDAO = $this->orm->createDAO("pm_mg_sign");
                $pm_signDAO->findId($id);
                $pm_signDAO = $pm_signDAO->get();
                if(!empty($pm_signDAO)){
                    $pm_signDAO[0]['sign_files'] = str_replace("/include/upload_file/", "",$pm_signDAO[0]['sign_files']);
                    $file =__REPICPATH__.$pm_signDAO[0]['sign_files'];

                    if(file_exists($file)){
                        ob_end_clean();
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=" .basename($file)); //以真实文件名提供给浏览器下载

                        readfile($file);    // 打开文件，并输出
                    }else{
                        echo "<script>alert('文件不存在！');";
                        echo "window.location.href='/management/zijin/rate'; ";
                        echo "</script>";
                        exit();
                    }
                }else{
                    echo "<script>alert('下载文件出错！');";
                    echo "window.location.href='/management/zijin/rate'; ";
                    echo "</script>";
                    exit();
                }
            }
        }


		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkmanagement();
            $this->admininfo = SessionUtil::getAdmininfo();
			
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
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);

            //获取筹资项目list
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
            $this->view->assign("chouzi_lists",$chouziDAO);

            // pplist
            $jjh_mg_ppDAO = $this->orm->createDAO('jjh_mg_pp')->get();
            if(!empty($jjh_mg_ppDAO)){
                foreach($jjh_mg_ppDAO as $k => $v){
                    $temp_array[$v['pid']] = $v['ppname'];
                }
            }
            $this->view->assign("jjh_mg_pp_list", $temp_array);

            //ini_set("display_errors", "On");
            //error_reporting(E_ERROR);
		}

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'addrszijin',
                'editrszijin',
                'savebindingclaim',
                'del-claim',
                'ajaxgetzwxm',
                'ajaxgetzwbm',
                'editrsrate',
                'syncsign',
                'signinfo',
                'addsign',
                'editsign',
                'newsigninfo',
                'newsavesign',
                'download',
                'binding-claim',
                'index',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}
?>