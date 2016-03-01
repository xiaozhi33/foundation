<?php
	require_once("BaseController.php");
	require_once("./util/httputil.php");
	require_once("./util/sessionutil.php");
	require_once("./util/isonline.php");
	
	class DownloadController extends BaseController {
		private $dbhelper;
		
		public function indexAction(){
			//图片表情
			$smileDAO = new woow_smileDAO();
			$smileDAO->type = 0;
			$pic = $smileDAO->get($this->dbhelper);
			
			//flash
			$smileDAO = new woow_smileDAO();
			$smileDAO->type = 1;
			$flash = $smileDAO->get($this->dbhelper);
			
			//压缩包
			$smileDAO = new woow_smileDAO();
			$smileDAO->type = 2;
			$rar = $smileDAO->get($this->dbhelper);
			
			$this->view->assign("pic",$pic);
			$this->view->assign("flash",$flash);
			$this->view->assign("rar",$rar);
			$this->view->display("download/index.html");
		}
		
		public function doAction(){
			if($_GET){			
				$id = HttpUtil::getString('id');
				$smileDAO = new woow_smileDAO();
				$smileDAO->id = $id;
				$smile = $smileDAO->get($this->dbhelper);
				if(!empty($smile)){
					$file =__REPICPATH__.$smile[0]['path'];
					//$file =$smile[0]['path'];	
					//var_dump($file);exit;		
					if(file_exists($file)){		
						ob_end_clean();						  	
						  header("Content-type: application/octet-stream");					  					  
						  header("Content-Disposition: attachment; filename=" .basename($file)); //以真实文件名提供给浏览器下载  
						 					  			 			 				 
						readfile($file);    // 打开文件，并输出					
					}else{
						alert_back('文件不存在');
					}
				}else{
					alert_back('下载文件出错');
				}
			}
			
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
		}
	}