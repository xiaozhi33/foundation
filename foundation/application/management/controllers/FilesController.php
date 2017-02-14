<?php
	require_once("BaseController.php");
	class Management_filesController extends BaseController
    {
        public $type_arrays = array(
                                    '查询协议' => '查询协议',
                                    '工作报告' => '工作报告',
                                    '策划书' => '策划书',
                                    '印刷文档' => '印刷文档',
                                    '会议资料' => '会议资料',
                                    '业务资料' => '业务资料',
                                 );
		public function indexAction(){
            $filesDAO = $this->orm->createDAO('jjh_mg_files')->order('id DESC');
            if(!empty($_REQUEST['name'])){
                $filesDAO->findName($_REQUEST['name']);
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
            $admininfo = SessionUtil::getAdmininfo();
            $uploader = $admininfo['admin_name'];

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
                        $filesDAO->name = $_FILES['meeting_files']['temp_name'];
                    }
                }
            }

            if($name == "" || $type == ""){
                alert_back('您输入的信息不完整，请查正后继续添加！！！！！');
            }

            try{
                if(!empty($id)){
                    $filesDAO ->findId($id);
                }
                $filesDAO ->name = $name;
                $filesDAO ->type = $type;
                $filesDAO ->upload_datetime = $upload_datetime;
                $filesDAO ->uploader = $uploader;
                $filesDAO ->save();
            }catch (Exception $e){
                alert_back('保存失败！！！！！');
            }
            alert_go('保存成功', "/management/files/index");
        }
		
		public function editAction(){
			$id = HttpUtil::getString("id");
            $filesDAO = $this->orm->createDAO('jjh_mg_files');
            $filesDAO ->findId($id);
            $filesDAO = $filesDAO ->get();
			
			if($filesDAO != "")
			{
				$this->view->assign("filesDAO", $filesDAO);
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

         public function _init(){
            $this ->dbhelper = new DBHelper();
            $this ->dbhelper ->connect();
            $this->view->assign("type_arrays", $this->type_arrays);
            SessionUtil::sessionStart();
            SessionUtil::checkmanagement();
        }
	}