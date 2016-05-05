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

			$zijininfo ->selectLimit = " and cate_id=0 order by id desc";
            //$zijininfo ->selectLimit = " and is_renling=1"; // 显示已认领的项目
			//$chouziinfo ->debugSql =true;
			$zijininfo = $zijininfo->get($this->dbhelper);
			$total = count($zijininfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($zijininfo,null,"index",null,'get',20,8);
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
                $pm_zijinDAO ->delete($this->dbhelper);

                echo "<script>alert('删除成功！');";
                echo "window.location.href='/management/zijin/index'";
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
			$pm_pp = HttpUtil::postString("pm_pp");      //项目捐赠人名称
			$pm_cate = HttpUtil::postString("pm_cate");  //项目分类
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
			
			if($pname == "" || $pm_pp == "" || $pm_cate == "" || $zijin_daozhang_datetime == "" || $zijin_daozheng_jiner == ""){
				alert_back("您输入的信息不完整，请查正后继续添加");
			}
			
			$pm_zijinDAO = new pm_mg_infoDAO();
			$pm_zijinDAO ->pm_name = $pname;
			$pm_zijinDAO ->pm_pp = $pm_pp;
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
			
			$pm_zijinDAO ->yishi = $yishi;
			$pm_zijinDAO ->jinianpin = $jinianpin;
			
			$pm_zijinDAO ->cate_id = 0;

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
				$pm_cate = HttpUtil::postString("pm_cate");  //项目分类
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
				
				if($pname == "" || $pm_pp == "" || $pm_cate == "" || $zijin_daozhang_datetime == "" || $zijin_daozheng_jiner == ""){
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
			
				$pm_zijinDAO ->piaoju = $piaoju;
				$pm_zijinDAO ->zhengshu = $zhengshu;
				$pm_zijinDAO ->pm_pp_company = $pm_pp_company;
				$pm_zijinDAO ->beizhu = $beizhu;
				$pm_zijinDAO ->zijin_laiyuan_qudao = $zijin_laiyuan_qudao;
				$pm_zijinDAO ->pm_juanzeng_cate = $pm_cate;
				$pm_zijinDAO ->pm_pp_cate = $pm_pp_cate;

                $pm_zijinDAO ->yishi = $yishi;
                $pm_zijinDAO ->jinianpin = $jinianpin;
	
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
                        $pm_mg_infoDAO ->pm_pp_company = $value["lrrq"];              // 付款单位
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
            $zw_lkglDAO = new CW_API();
            $lkgl_list = $zw_lkglDAO ->getlkgl();

            // 遍历循环插入lkrl_log表中
            foreach($lkgl_list as $k => $v){
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

            $keywords = HttpUtil::postString("pm_name");
            $is_renling = HttpUtil::postString("is_renling");
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
         * 绑定认领
         */
        public function savebindingClaimAction(){
            (int)$pid = $_REQUEST['pm_id'];
            (int)$department_id = $_REQUEST['department_id'];
            if(empty($pid) || empty($department_id)){
                alert_back("请选择认领项目和部门！");
            }

            // 1, 查看项目财务对照表－取得财务对应项目名称和编号
            $pm_relateDAO = $this->orm->createDAO("zw_pm_related");
            $pm_relateDAO ->findPm_id($pid);
            $pm_relateDAO = $pm_relateDAO->get();

            if(empyt($pm_relateDAO[0]['zw_xmbh']) || empyt($pm_relateDAO[0]['zw_xmmc'])){
                alert_back("该项目没有绑定财务系统，请联系管理员");
            }

            // 2, 部门信息同步
            $department_info = $this->orm->createDAO("zw_department_related");
            $department_info ->findPm_pid($department_id);
            $department_info = $department_info->get();

            if(empyt($department_info[0]['zw_bmbh']) || empyt($department_info[0]['zw_bmmc'])){
                alert_back("该部门没有绑定财务部门，请联系管理员");
            }

            // 3, 负者人信息同步

            // 4, 同步更新财务系统lkrl表
            $zw_lkrlDAO = new CW_API();
            $rs = $zw_lkrlDAO ->addlkrl($lsh, $rlxh, $rlrq, $rlr, $rlrbh, $bmbh, $xmbh, $rlje, $lspz, $rlpznm, $czy);
            if($rs){
                // 更新项目系统认领log表
                $zw_lkrl_logsDAO = $this->orm->createDAO("zw_lkrl_logs");
                $zw_lkrl_logsDAO ->findLsh($rs);
                $rs1 = $zw_lkrl_logsDAO ->save();
                if($rs1){
                    alert_go("认领成功！", "/management/zijin/claimlist");
                }else {
                    alert_back("认领失败！");
                }
            }
        }


        /**
         * 绑定认领页面
         */
        public function bindingClaimAction(){
            // 财务系统相关 - 读取财务项目信息
            /*$zwxmzdDAO = array();
            $select_zw_xm = "SELECT xmnm,bmbh,xmbh,xmmc,fzr,fzrbh FROM zwxmzd";
            $this->mssql_class->connect();
            $zwxmzd_list = $this->mssql_class->query($select_zw_xm);
            while($row = $this->mssql_class->fetch_array($zwxmzd_list)){
                $zwxmzdDAO[$row['xmnm']] = $row;
            }
            $this->mssql_class->free();*/
            //$this->view->assign('zwxmzd_list', $zwxmzdDAO);

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
         */
        public function ajaxgetzwxmAction(){
            (int)$pm_id = $_REQUEST["pm_id"];
            $zw_pm_relatedDAO = $this->orm->createDAO("zw_pm_related");
            $zw_pm_relatedDAO ->findPm_id($pm_id);
            $zw_pm_relatedDAO = $zw_pm_relatedDAO->get();

            if($zw_pm_relatedDAO != ""){
                echo json_encode($zw_pm_relatedDAO[0]);
            }else {
                echo json_encode(array());
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
         * 项目进度管理
         */
        public function rateAction()
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

                    // 如果没有设置进度，默认为洽谈中
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
                alert_go("编辑成功","/management/zijin/rate");
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
            $pm_signDAO = $this->orm->createDAO("pm_mg_chouzi");
            $pm_signDAO ->findId($id);
            $pm_signDAO = $pm_signDAO ->get();
            $this->view->assign("signifo", $pm_signDAO);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("zijin/addsign.phtml");
            echo $this->view->render("index/footer.phtml");
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

            //ini_set("display_errors", "On");
            //error_reporting(E_ERROR);
		}
	}
?>