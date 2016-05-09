<?php
	require_once("BaseController.php");
	class Management_feedbackController extends BaseController
    {

		public function indexAction(){
            $meetingDAO = $this->orm->createDAO('jjh_meeting')->order('id DESC');
            $meetingDAO->getPager(array('path'=>'/management/meeeting/index'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("meeting/index.phtml");
            echo $this->view->render("index/footer.phtml");
		}
        /*
         *  add meeting
         */
		public function addAction(){
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("meeting/addmeeting.phtml");
            echo $this->view->render("index/footer.phtml");
		}

        /*
         *  toSave meeting information
         */
        public function toAddAction(){
			$id = $_REQUEST['id'];
            $meeting_name = HttpUtil::postString("meeting_name");
            $meeting_cate = HttpUtil::postString("meeting_cate");
            $meeting_joiner = HttpUtil::postString("meeting_joiner");
            $meeting_content = HttpUtil::postString("meeting_content");
			$meeting_start_time = HttpUtil::postString("meeting_start_time");
			$meeting_end_time = HttpUtil::postString("meeting_end_time");
			$meeting_address = HttpUtil::postString("meeting_address");

            $meetingDAO = $this->orm->createDAO('jjh_meeting');

            if($meeting_name == "" || $meeting_cate == "" || $meeting_joiner == "" || $meeting_content == ""){
                //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }

            if($_FILES['meeting_files']['name']!=""){
                if($_FILES['meeting_files']['error'] != 4){
                    if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
                        mkdir(__UPLOADPICPATH__ ."jjh_download/");
                    }
                    $uploadpic = new uploadPic($_FILES['meeting_files']['name'],$_FILES['meeting_files']['error'],$_FILES['meeting_files']['size'],$_FILES['meeting_files']['tmp_name'],$_FILES['meeting_files']['type'],2);
                    $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
                    $result = $uploadpic->uploadPic();
                    if($result['error']!=0){
                        echo "<script>alert('".$result['msg']."');";
                        echo "window.location.href='/management/meeting";
                        echo "</script>";
                        exit();
                    }else{
                        $meetingDAO->meeting_files =  __GETPICPATH__."jjh_download/".$result['picname'];
                        $meetingDAO->meeting_files_name = $_FILES['meeting_files']['name'];
                    }
                }
            }
            if(!empty($id))  //修改流程
            {
                $meetingDAO ->findId($id);
            }
            try{
                $meetingDAO ->meeting_name = $meeting_name;
                $meetingDAO ->meeting_cate = $meeting_cate;
                $meetingDAO ->meeting_joiner = $meeting_joiner;
                $meetingDAO ->meeting_content = $meeting_content;
                $meetingDAO ->meeting_start_time = $meeting_start_time;
                $meetingDAO ->meeting_end_time = $meeting_end_time;
                $meetingDAO ->meeting_address = $meeting_address;
                $meetingDAO ->save();
            }catch (Exception $e){
                /*alert_back("保存失败！");
                exit;*/
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('保存失败！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存成功');");
            echo("location.href='/management/meeting';");
            echo('</script>');
            exit;

            /*echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/meeting/'));
            exit;*/
        }
		
		public function editAction(){
			$id = HttpUtil::getString("id");
            $meetingDAO = $this->orm->createDAO('jjh_meeting');
			$meetingDAO ->findId($id);
			$meetingDAO = $meetingDAO ->get();
			
			if($meetingDAO != "")
			{
				$this->view->assign("meeting_info", $meetingDAO);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("meeting/editmeeting.phtml");
				echo $this->view->render("index/footer.phtml");
                exit();
			}
            $meetingDAO = $this->orm->createDAO('jjh_meeting')->order('id DESC');

            $this->view->assign("meeting_info", $meetingDAO);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("meeting/editmeeting.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
		}
		
		public function delAction(){
			$id = HttpUtil::getString("id");
            $meetingDAO = $this->orm->createDAO('jjh_meeting');
			$meetingDAO ->findId($id);
			$meetingDAO = $meetingDAO ->delete();

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/management/meeting';");
            echo('</script>');
            exit;

        }

         public function _init(){
            error_reporting(0);
            SessionUtil::sessionStart();
            SessionUtil::checkmanagement();

            //项目名称列表
            $pm_chouzi = new pm_mg_chouziDAO();
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);

            //获取筹资项目list
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
            $this->view->assign("chouzi_lists",$chouziDAO);
        }
	}