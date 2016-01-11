<?php
	require_once("BaseController.php");
	class Admin_adController extends BaseController {
		private $dbhelper;
		public function indexAction(){

			$ad_list = new my_ad_infoDAO();
			$ad_list = $ad_list->get($this->dbhelper);
			
			$total = count($ad_list);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($ad_list,null,"index",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('ad_list',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('ad/index.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addadAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render('ad/addad.phtml');
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addrsAction(){
			if(HttpUtil::postString("name") == ""){
				alert_back("请填写广告标题！");
			}
			if(HttpUtil::postString("content") == ""){
				alert_back("请填写广告内容！");
			}
			if(HttpUtil::postString("link") == ""){
				alert_back("请填写广告链接！");
			}
			
			$ad_info = new my_ad_infoDAO();
			
			if($_FILES['pic']['name']!=""){
				if($_FILES['pic']['error'] != 4){
				    if(!is_dir(__UPLOADPICPATH__ ."jjh_image/")){
				       mkdir(__UPLOADPICPATH__ ."jjh_image/");
				    }					
					$uploadpic = new uploadPic($_FILES['pic']['name'],$_FILES['pic']['error'],$_FILES['pic']['size'],$_FILES['pic']['tmp_name'],$_FILES['pic']['type'],2);
					$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_image/" ;
					$result = $uploadpic->uploadPic();
					if($result['error']!=0){					    	
					   	alert_back($result['msg']);
					}else{				             
				        $ad_info->ad_image =  __GETPICPATH__."jjh_image/".$result['picname'];
				    }		            	    
			    }else{
			       	alert_back('上传文件错误，请重试！');
			    }
			}
			
			$ad_info ->ad_content = HttpUtil::postString("name");
			$ad_info ->ad_link = HttpUtil::postString("link");
			$ad_info ->ad_name = HttpUtil::postString("name");
			$ad_info ->save($this->dbhelper);
			alert_go('添加成功！',"/admin/ad");
		}
		
		public function editadAction(){
			$id = $_REQUEST['id'];
			if($id != ""){
				$ad_info = new my_ad_infoDAO($id);
				$ad_info = $ad_info->get($this->dbhelper);
				
				$this->view->assign("ad_info",$ad_info);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render('ad/editad.phtml');
				echo $this->view->render("index/footer.phtml");
			}else {
				alert_back("您的操作失败。");
			}
		}
		
		public function editrsAction(){
			$id = $_REQUEST['id'];
			if($id != ""){
				$ad_info = new my_ad_infoDAO($id);
				if(HttpUtil::postString("name") == ""){
					alert_back("请填写广告标题！");
				}
				if(HttpUtil::postString("content") == ""){
					alert_back("请填写广告内容！");
				}
				if(HttpUtil::postString("link") == ""){
					alert_back("请填写广告链接！");
				}
				
				if($_FILES['pic']['name']!=""){
					if($_FILES['pic']['error'] != 4){
					    if(!is_dir(__UPLOADPICPATH__ ."jjh_image/")){
					       mkdir(__UPLOADPICPATH__ ."jjh_image/");
					    }					
						$uploadpic = new uploadPic($_FILES['pic']['name'],$_FILES['pic']['error'],$_FILES['pic']['size'],$_FILES['pic']['tmp_name'],$_FILES['pic']['type'],2);
						$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_image/" ;
						$result = $uploadpic->uploadPic();
						if($result['error']!=0){					    	
						   	alert_back($result['msg']);
						}else{				             
					        $ad_info->ad_image =  __GETPICPATH__."jjh_image/".$result['picname'];
					    }		            	    
				    }else{
				       	alert_back('上传文件错误，请重试！');
				    }
				}else{
					$ad_info->ad_image =  HttpUtil::postString("oldpic");
				}
				$ad_info ->ad_content = HttpUtil::postString("name");
				$ad_info ->ad_link = HttpUtil::postString("link");
				$ad_info ->ad_name = HttpUtil::postString("name");
				$ad_info ->save($this->dbhelper);
				alert_back("广告修改成功。");
			}else {
				alert_back("您的操作失败。");
			}
		}
		
		public function deladAction(){
			$id = $_REQUEST['id'];
			if($id != ""){
				$ad_info = new my_ad_infoDAO($id);
				$ad_info ->del($this->dbhelper);
				alert_back("广告删除成功。");
			}else {
				alert_back("您的操作失败。");
			}
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkadmin();
		}
	}
?>