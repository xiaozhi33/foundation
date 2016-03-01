<?php
	require_once("BaseController.php");
	class Management_zijinController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$pname = HttpUtil::getString("pname");
			$department = HttpUtil::getString("department");
			$zijininfo = new pm_mg_infoDAO();
			
			if($pname != ""){
				$zijininfo ->pm_name = $pname;
			}
			
			if($department != ""){
				$zijininfo ->department = $department;
			}

			$zijininfo ->selectLimit = " and cate_id=0 order by id desc";
			//$chouziinfo ->debugSql =true;
			$zijininfo = $zijininfo->get($this->dbhelper);
			$total = count($zijininfo);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($zijininfo,null,"index",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('zijinlist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);

			echo $this->view->render("index/header.phtml");
			echo $this->view->render("zijin/index.phtml");
			echo $this->view->render("index/footer.phtml");
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
				$pm_zijinDAO ->piaoju = $piaoju;
				$pm_zijinDAO ->zhengshu = $zhengshu;
				$pm_zijinDAO ->pm_pp_company = $pm_pp_company;
				$pm_zijinDAO ->beizhu = $beizhu;
				$pm_zijinDAO ->zijin_laiyuan_qudao = $zijin_laiyuan_qudao;
				$pm_zijinDAO ->pm_juanzeng_cate = $pm_cate;
				$pm_zijinDAO ->pm_pp_cate = $pm_pp_cate;
	
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
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);
		}
	}
?>