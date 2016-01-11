<?php
	require_once("BaseController.php");
	class Admin_downloadController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			if(HttpUtil::postString("fname")!=""){
				$my_downloadDAO = new my_downloadDAO(HttpUtil::postString("fname"));
			}else {
				$my_downloadDAO = new my_downloadDAO();
			}

			$my_downloadDAO = $my_downloadDAO->get($this->dbhelper);
			
			$total = count($my_downloadDAO);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($my_downloadDAO,null,"index",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('my_download',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('download/index.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function editdownloadAction(){
			if($_REQUEST['id'] != ""){
				$did = $_REQUEST["id"];
				$my_downloadDAO = new my_downloadDAO($did);
				$my_downloadDAO = $my_downloadDAO ->get($this->dbhelper);
				$this->view->assign("downloadinfo",$my_downloadDAO);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("download/editdownload.phtml");
				echo $this->view->render("index/footer.phtml");
			}else {
				alert_back("您的操作有误，无此下载");
			}
		}
		
		public function editrsAction(){
			if($_REQUEST['id'] != ""){
				$did = $_REQUEST["id"];
				$my_downloadDAO = new my_downloadDAO($did);
				$my_downloadDAO ->download_title = $_REQUEST['title'];
				$my_downloadDAO ->download_content = $_REQUEST['content'];
				if($my_downloadDAO ->download_title == ""){
					alert_back("请添加标题。");
				}
				
				if($_FILES['filesinfo'] != ""){
					if($_FILES['filesinfo']['type'] == "application/octet-stream"){
						$my_downloadDAO ->download_cate = "rar";
					}elseif ($_FILES['filesinfo']['type'] == "application/octet-stream"){
						$my_downloadDAO ->download_cate = "zip";
					}elseif ($_FILES['filesinfo']['type'] == "application/msword"){
						$my_downloadDAO ->download_cate = "doc";
					}
					
					if($_FILES['filesinfo']['name']!=""){
						if($_FILES['filesinfo']['error'] != 4){
						    if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
						       mkdir(__UPLOADPICPATH__ ."jjh_download/");
						    }
							$uploadpic = new uploadPic($_FILES['filesinfo']['name'],$_FILES['filesinfo']['error'],$_FILES['filesinfo']['size'],$_FILES['filesinfo']['tmp_name'],$_FILES['filesinfo']['type'],2);
							$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
							$result = $uploadpic->uploadPic();
							if($result['error']!=0){					    	
							   	alert_back($result['msg']);
							}else{				             
						        $my_downloadDAO->download_file =  __GETPICPATH__."jjh_download/".$result['picname'];
						    }		            	    
					    }else{
					       	alert_back('上传文件错误，请重试！');
					    }
					}
				}else{
					$my_downloadDAO->download_file = $_REQUEST['oldfiles'];
				}

				$my_downloadDAO ->save($this->dbhelper);
				alert_go("编辑成功","/admin/download/index");
			}else {
				alert_back("您的操作有误，无此下载");
			}
		}
		
		public function deldownloadAction(){
			$did = HttpUtil::getString("id");
			if($did != ""){
				$my_downloadDAO = new my_downloadDAO($did);
				$my_downloadDAO ->del($this->dbhelper);
				alert_back("删除成功。");
			}
		}
		
		public function adddownloadAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('download/adddownload.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrsAction(){
			$my_downloadDAO = new my_downloadDAO();
			$my_downloadDAO ->download_title = HttpUtil::postInsString("title");
			$my_downloadDAO ->download_content = HttpUtil::postInsString("content");
			
			if($my_downloadDAO ->download_title == ""){
				alert_back("请添加标题。");
			}
			
			if($_FILES['filesinfo']['type'] == "application/octet-stream"){
				$my_downloadDAO ->download_cate = "rar";
			}elseif ($_FILES['filesinfo']['type'] == "application/octet-stream"){
				$my_downloadDAO ->download_cate = "zip";
			}elseif ($_FILES['filesinfo']['type'] == "application/msword"){
				$my_downloadDAO ->download_cate = "doc";
			}
			
			//var_dump($_FILES['filesinfo']);exit;
			if($_FILES['filesinfo']['name']!=""){
					if($_FILES['filesinfo']['error'] != 4){
					    if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
					       mkdir(__UPLOADPICPATH__ ."jjh_download/");
					    }
						$uploadpic = new uploadPic($_FILES['filesinfo']['name'],$_FILES['filesinfo']['error'],$_FILES['filesinfo']['size'],$_FILES['filesinfo']['tmp_name'],$_FILES['filesinfo']['type'],2);
						$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
						$result = $uploadpic->uploadPic();
						if($result['error']!=0){					    	
						   	alert_back($result['msg']);
						}else{				             
					        $my_downloadDAO->download_file =  __GETPICPATH__."jjh_download/".$result['picname'];
					    }		            	    
				    }else{
				       	alert_back('上传文件错误，请重试！');
				    }
				}
				
			$my_downloadDAO ->save($this->dbhelper);
			alert_go("添加成功！","/admin/download");
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkadmin();
		}
	}
?>