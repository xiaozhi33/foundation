<?php
	require_once("BaseController.php");
	class Admin_informationController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			
			if(HttpUtil::postString("cate")!=""){
				$my_informationDAO = new my_informationDAO(null,HttpUtil::postString("cate"));
			}else {
				$my_informationDAO = new my_informationDAO();
			}

			if(HttpUtil::postString("keyword")!=""){
				$keyword = HttpUtil::postString("keyword");
				$my_informationDAO->selectLimit = " and my_infor_title like '%$keyword%'";
			}

			$my_informationDAO->selectLimit .= " and my_infor_isdisplay = 1 and my_infor_state = 1 order by my_infor_datetime desc";
			$information = $my_informationDAO->get($this->dbhelper);
			
			$total = count($information);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($information,null,"information",null,'get',20,20);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('informationlist',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("information/index.phtml");
			echo $this->view->render("index/footer.phtml");
		}

		public function addinformationAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("information/addinformation.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		public function addresaultAction() {
			if(HttpUtil::postString("title") == ""){
				alert_back("请填写文章的标题！");
			}
			if(HttpUtil::postString("ctitle") == ""){
				alert_back("请填写文章的副标题！");
			}
			if($_REQUEST['content'] == ""){
				alert_back("请填写文章的内容！");
			}
			if(HttpUtil::postString("cate") == ""){
				alert_back("请填写文章的类型！");
			}
			if($_REQUEST['miaoshu'] == ""){
				alert_back("请填写文章的简介！");
			}

			$my_informationDAO = new my_informationDAO();
			
			if($_FILES['pic']['name']!=""){
				if($_FILES['pic']['error'] != 4){
				    if(!is_dir(__UPLOADPICPATH__ ."jjh_image/")){
				       mkdir(__UPLOADPICPATH__ ."jjh_image/");
				    }					
					$uploadpic = new uploadPic($_FILES['pic']['name'],$_FILES['pic']['error'],$_FILES['pic']['size'],$_FILES['pic']['tmp_name'],$_FILES['pic']['type'],2);
					$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_image/" ;
					$result = $uploadpic->uploadPic();
					
					//var_dump($_FILES);EXIT;
					
					if($result['error']!=0){					    	
					   	alert_back($result['msg']);
					}else{				             
				        $my_informationDAO->my_infor_images =  __GETPICPATH__."jjh_image/".$result['picname'];
				    }		            	    
			    }else{
			       	alert_back('上传文件错误，请重试！');
			    }
			}
			
			//附件上传
			if($_FILES['downloadfile']['name']!=""){
					if($_FILES['downloadfile']['error'] != 4){
					    if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
					       mkdir(__UPLOADPICPATH__ ."jjh_download/");
					    }
						$uploadpic = new uploadPic($_FILES['downloadfile']['name'],$_FILES['downloadfile']['error'],$_FILES['downloadfile']['size'],$_FILES['downloadfile']['tmp_name'],$_FILES['downloadfile']['type'],2);
						$uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
						$result = $uploadpic->uploadPic();
						if($result['error']!=0){					    	
						   	alert_back($result['msg']);
						}else{				             
					        $my_informationDAO->my_infor_file =  __GETPICPATH__."jjh_download/".$result['picname'];
					    }		            	    
				    }else{
				       	alert_back('上传文件错误，请重试！');
				    }
			}
			
			$my_informationDAO ->my_infor_content = $_REQUEST['content'];
			$my_informationDAO ->my_infor_title = HttpUtil::postString("title");
			$my_informationDAO ->my_infor_ctitle = HttpUtil::postString("ctitle");
			$my_informationDAO ->my_infor_cateid = HttpUtil::postString("cate");
			$my_informationDAO ->my_infor_sumary = $_REQUEST['miaoshu'];
			if(empty(HttpUtil::postString("my_infor_datetime"))){
				$my_informationDAO ->my_infor_datetime = date("Y-m-d h:i:s",time());
			}else{
				$my_informationDAO ->my_infor_datetime = HttpUtil::postString("my_infor_datetime");
			}
			$my_informationDAO ->my_infor_isdisplay = HttpUtil::postInsString("display");
			$my_informationDAO ->my_infor_state = 1;
			$my_informationDAO ->save($this->dbhelper);
			alert_go('添加成功！',"/admin/information");
		}
		
		public function editinformationAction(){
			try{
				//ini_set("display_errors", "On");
				//error_reporting(E_ERROR);
				$act = HttpUtil::postString("act");
				if($act == "gosave"){
					if(HttpUtil::postString("title") == ""){
						alert_back("请填写文章的标题！");
					}
					if(HttpUtil::postString("ctitle") == ""){
						alert_back("请填写文章的副标题！");
					}
					if($_REQUEST["content"] == ""){
						alert_back("请填写文章的内容！");
					}
					if(HttpUtil::postString("cate") == ""){
						alert_back("请填写文章的类型！");
					}
					if($_REQUEST["miaoshu"] == ""){
						alert_back("请填写文章的简介！");
					}
					$id = HttpUtil::postString('id');
					if($id == ""){
						alert_back("您要编辑的资讯不存在");
					}
					$my_informationDAO = new my_informationDAO($id);
					$my_informationDAO->my_infor_content = $_REQUEST["content"];
					$my_informationDAO->my_infor_title = HttpUtil::postString("title");
					$my_informationDAO->my_infor_ctitle = HttpUtil::postString("ctitle");
					$my_informationDAO->my_infor_cateid = HttpUtil::postString("cate");
					$my_informationDAO->my_infor_sumary = $_REQUEST["miaoshu"];
					$my_informationDAO->my_infor_isdisplay = HttpUtil::postString("display");
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
								$my_informationDAO->my_infor_images =  __GETPICPATH__."jjh_image/".$result['picname'];
							}
						}else{
							alert_back('上传文件错误，请重试！');
						}
					}

					if($_FILES['filesinfo'] != ""){

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
									$my_informationDAO->my_infor_file =  __GETPICPATH__."jjh_download/".$result['picname'];
								}
							}else{
								alert_back('上传文件错误，请重试！');
							}
						}
					}else{
						$my_informationDAO->my_infor_file = $_REQUEST['oldfiles'];
					}

					//$my_informationDAO->debugSql = true;
					$my_informationDAO->save($this->dbhelper);
					alert_go('修改成功！',"/admin/information");
				}else{
					$id = HttpUtil::getString('id');
					if($id == ""){
						alert_back("您要编辑的资讯不存在");
					}

					$my_informationDAO = new my_informationDAO($id);
					$my_informationDAO = $my_informationDAO->get($this->dbhelper);
					$this->view->assign("my_information",$my_informationDAO);
					echo $this->view->render("index/header.phtml");
					echo $this->view->render("information/editinformation.phtml");
					echo $this->view->render("index/footer.phtml");
				}
			}catch(Exception $e){
				throw $e;
			}
		}
		
		public function delinformationAction(){
			$id = HttpUtil::getString('id');
			if($id == ""){
				alert_back("您要删除的信息不存在");
			}
			$my_informationDAO = new my_informationDAO($id);
			$my_informationDAO ->my_infor_isdisplay = 0;
			$my_informationDAO ->save($this->dbhelper);
			//$my_informationDAO ->whereCondition = " where my_infor_id = '$id'";
			//$my_informationDAO ->del($this->dbhelper);
			alert_back("删除成功，已经更改为不显示。");
		}
		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkadmin();
		}
	}
?>
