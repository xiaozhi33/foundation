<?php
	require_once("BaseController.php");
	class Management_filesController extends BaseController
    {
		public function indexAction(){
            $filesDAO = $this->orm->createDAO('jjh_mg_files')->order('id DESC');
            if(!empty($_REQUEST['name'])){
                //$filesDAO->findName($_REQUEST['name']);
                $filesDAO->selectLimit .= " AND name like '%".$_REQUEST['name']."%'";
                $this->view->assign("name", $_REQUEST['name']);
            }
            if(!empty($_REQUEST['type'])){
                $filesDAO->findType($_REQUEST['type']);
                $this->view->assign("type", $_REQUEST['type']);
            }
            $filesDAO->getPager(array('path'=>'/management/files/index'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("files/index.phtml");
            echo $this->view->render("index/footer.phtml");
		}
        /*
         *  add feedback
         */
		public function addAction(){
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("files/addfiles.phtml");
            echo $this->view->render("index/footer.phtml");
		}

        /*
         *  toSave feedback information
         */
        public function toAddAction(){
            (int)$id = $_REQUEST['id'];
            $name = HttpUtil::postString("name");
            $type = HttpUtil::postString("type");
            $upload_datetime = date("Y-m-d H:i:s", time());
            $description = HttpUtil::postString("description");
            $admininfo = SessionUtil::getAdmininfo();
            $uploader = $this->admininfo['admin_name'];

            $filesDAO = $this->orm->createDAO('jjh_mg_files');
            if($_FILES['files']['name']!=""){
                if($_FILES['files']['error'] != 4){
                    if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
                        mkdir(__UPLOADPICPATH__ ."jjh_download/");
                    }
                    $uploadpic = new uploadPic($_FILES['files']['name'],$_FILES['files']['error'],$_FILES['files']['size'],$_FILES['files']['tmp_name'],$_FILES['files']['type'],2);
                    $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
                    $result = $uploadpic->uploadPic();
                    if($result['error']!=0){
                        echo "<script>alert('".$result['msg']."');";
                        echo "window.location.href='/management/files';";
                        echo "</script>";
                        exit();
                    }else{
                        $filesDAO->files =  __GETPICPATH__."jjh_download/".$result['picname'];
                        $filesDAO->temp_name = $_FILES['files']['name'];
                    }
                }
            }

            if($name == "" || $type == ""){
                echo "<script>alert('您输入的信息不完整，请查正后继续添加！！！！！');";
                echo "window.location.href='/management/files';";
                echo "</script>";
                exit();
                //alert_back('您输入的信息不完整，请查正后继续添加！！！！！');
            }

            try{
                if(!empty($id)){
                    $filesDAO ->findId($id);
                }
                $filesDAO ->name = $name;
                $filesDAO ->type = $type;
                $filesDAO ->upload_datetime = $upload_datetime;
                $filesDAO ->uploader = $uploader;
                $filesDAO ->description = $description;
                $filesDAO ->save();
            }catch (Exception $e){
                echo "<script>alert('保存失败！！！！！');";
                echo "window.location.href='/management/files';";
                echo "</script>";
                exit();
                //alert_back('保存失败！！！！！');
            }
            echo "<script>alert('保存成功！');";
            echo "window.location.href='/management/files';";
            echo "</script>";
            exit();
            //alert_go('保存成功', "/management/files/index");
        }
		
		public function editAction(){
			$id = HttpUtil::getString("id");
            $filesDAO = $this->orm->createDAO('jjh_mg_files');
            $filesDAO ->findId($id);
            $filesDAO = $filesDAO ->get();
			
			if($filesDAO != "")
			{
				$this->view->assign("files", $filesDAO);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("files/editfiles.phtml");
				echo $this->view->render("index/footer.phtml");
                exit();
			}
		}
		
		public function delAction(){
			$id = HttpUtil::getString("id");
            $filesDAO = $this->orm->createDAO('jjh_mg_files');
            $filesDAO ->findId($id);
            $filesDAO = $filesDAO ->delete();

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/management/files';");
            echo('</script>');
            exit;
        }

        // 文件下载
        public function downloadAction(){
            if($_GET){
                (int)$id = HttpUtil::getString('id');
                $filesDAO = $this->orm->createDAO("jjh_mg_files");
                $filesDAO->findId($id);
                $filesDAO = $filesDAO->get();
                if(!empty($filesDAO)){

                    $filesDAO[0]['files'] = str_replace("/include/upload_file/", "",$filesDAO[0]['files']);
                    $file =__REPICPATH__.$filesDAO[0]['files'];

                    if(file_exists($file)){
                        ob_end_clean();
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=" .basename($file)); //以真实文件名提供给浏览器下载

                        readfile($file);    // 打开文件，并输出
                    }else{
                        echo "<script>alert('文件不存在！');";
                        echo "window.location.href='/management/files'; ";
                        echo "</script>";
                        exit();
                    }
                }else{
                    echo "<script>alert('下载文件出错！');";
                    echo "window.location.href='/management/files'; ";
                    echo "</script>";
                    exit();
                }
            }
        }

         public function _init(){
            $this ->dbhelper = new DBHelper();
            $this ->dbhelper ->connect();
            $this->view->assign("type_arrays", $this->type_arrays);
            SessionUtil::sessionStart();
            SessionUtil::checkmanagement();
        }

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'to-add',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}