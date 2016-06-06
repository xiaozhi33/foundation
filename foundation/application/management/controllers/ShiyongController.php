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
				$pmDAO ->joinTable (" left join jjh_mg_cate as r on r.id = pm_mg_chouzi.cate");
				$pmDAO ->selectField(" pm_mg_chouzi.*, r.catename");
				$pmDAO ->findPname($pname);
				$pmDAO = $pmDAO->get();
				return $pmDAO[0]['catename'];
				var_dump($pmDAO);exit();
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
				$pm_mg_infoDAO ->save($this->dbhelper);
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
					
				if($pname == "" || $shiyong_zhichu_datetime == "" || $shiyong_zhichu_jiner == ""){
					alert_back("您输入的信息不完整，请查正后继续添加");
				}
				
				$pm_mg_infoDAO = new pm_mg_infoDAO($_REQUEST['id']);
				$pm_mg_infoDAO ->beizhu = $beizhu;
				$pm_mg_infoDAO ->jiangli_renshu = $jiangli_renshu;
				$pm_mg_infoDAO ->jiangli_fanwei = $fanwei;
				$pm_mg_infoDAO ->pm_name = $pname;
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
				alert_go("编辑成功","/management/shiyong");
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
            $pm_chouzi ->selectLimit .= " order by id desc";
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);
		}
	}
?>