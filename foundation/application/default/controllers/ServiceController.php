<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/sessionutil.php");
	
	class serviceController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			$survey_info = new jjh_surveyDAO(10);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("service/index.phtml");
		}
		
		//下载中心
		public function downloadAction(){
			$my_download = new my_downloadDAO();
			$my_download = $my_download->get($this->dbhelper);
			
			$total = count($my_download);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($my_download,null,"index",null,'get',14,14);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('my_downloadlist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			echo $this->view->render("service/download.phtml");
		}
		
		public function downloadoneAction(){
			$id = $_REQUEST['id'];
			$my_download = new my_downloadDAO($id);
			$my_download = $my_download->get($this->dbhelper);
			
			if($my_download != ""){
				$fname = explode("/",$my_download[0]['download_file']);
				$file_dir = "/var/www/html/my_cms/foundation/include/upload_file/jjh_download/";
				$file_name = $fname[4];
				$file = fopen($file_dir.$file_name, "rb");   //打开文件  

				//输入文件标签
				Header("Content-type:application/octet-stream");
				Header("Accept-Ranges:bytes");
				Header("Accept-Length:".@filesize($file_dir.$file_name));
				Header("Content-Disposition:attachment;filename=".$file_name);
				//输出文件内容
				echo fread($file,@filesize($file_dir.$file_name));
				fclose($file);
				exit;
			}else {
				alert_back("对不起没有此下载");
			}
		}
		
		public function guide1Action(){
			$survey_info = new jjh_surveyDAO(11);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("service/guide1.phtml");
		}
		
		public function guide2Action(){
			$survey_info = new jjh_surveyDAO(13);
			$survey_info = $survey_info ->get($this->dbhelper);
			$this->view->assign("info",$survey_info);
			echo $this->view->render("service/guide2.phtml");
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
		}
	}
?>