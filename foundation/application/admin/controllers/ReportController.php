<?php
	require_once("BaseController.php");
	class Admin_reportController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("report/index.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function reportinfoAction(){
			//根据时间统计项目收支明细
			if($_REQUEST['style'] == "style1"){
				$start = $_REQUEST['start_datetime'];
				$end = $_REQUEST['end_datetime'];
				if($start != "" && $end != ""){
					$selectSQL = "select sum(zijin_daozheng_jiner) as daozhang,sum(shiyong_zhichu_jiner) as shiyong,pm_name from pm_mg_info where pm_name = pm_name and zijin_daozhang_datetime>='$start' or shiyong_zhichu_datetime<='$end' group by pm_name";
					$rss = $this->dbhelper->fetchAllData($selectSQL);
					
					//var_dump($rss);exit;
					$this->view->assign("reportinfo",$rss);
					$this->view->assign("start",$start);
					$this->view->assign("end",$end);
					echo $this->view->render("report/report_form.phtml");
				}
			}
		}
		

		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkadmin();
			
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