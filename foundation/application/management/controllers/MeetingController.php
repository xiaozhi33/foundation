<?php
	require_once("BaseController.php");
	class Management_MeetingController extends BaseController
    {

		public function indexAction(){
            //$this->_redirect('/admin/editor/spec-list');
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
            $meeting_joiner = implode(',',$_REQUEST['meeting_joiner']);
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
                        echo "window.location.href='/management/meeting';";
                        echo "</script>";
                        exit();
                    }else{
                        $meetingDAO->meeting_files =  __GETPICPATH__."jjh_download/".$result['picname'];
                        $meetingDAO->meeting_files_name = $_FILES['meeting_files']['name'];
                    }
                }
            }

            for($i=1; $i<=5; $i++){
                if($_FILES['meeting_files'.$i]['name']!=""){
                    if($_FILES['meeting_files'.$i]['error'] != 4){
                        if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
                            mkdir(__UPLOADPICPATH__ ."jjh_download/");
                        }
                        $uploadpic = new uploadPic($_FILES['meeting_files'.$i]['name'],$_FILES['meeting_files'.$i]['error'],$_FILES['meeting_files'.$i]['size'],$_FILES['meeting_files'.$i]['tmp_name'],$_FILES['meeting_files'.$i]['type'],2);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
                        $result = $uploadpic->uploadPic();
                        if($result['error']!=0){
                            echo "<script>alert('".$result['msg']."');";
                            echo "window.location.href='/management/meeting';";
                            echo "</script>";
                            exit();
                        }else{
                            $string = 'meeting_files'.$i;
                            $string1 = 'meeting_files_name'.$i;
                            $meetingDAO->$string =  __GETPICPATH__."jjh_download/".$result['picname'];
                            $meetingDAO->$string1 = $_FILES['meeting_files'.$i]['name'];
                        }
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

        // 文件下载
        public function downloadAction(){
            if($_GET){
                (int)$id = HttpUtil::getString('id');
                (int)$fid = HttpUtil::getString('fid');
                $jjh_meetingDAO = $this->orm->createDAO("jjh_meeting");
                $jjh_meetingDAO->findId($id);
                $jjh_meetingDAO = $jjh_meetingDAO->get();
                if(!empty($jjh_meetingDAO)){
                    if($fid != ''){
                        $jjh_meetingDAO[0]['meeting_files'.$fid] = str_replace("/include/upload_file/", "",$jjh_meetingDAO[0]['meeting_files'.$fid]);
                        $file =__REPICPATH__.$jjh_meetingDAO[0]['meeting_files'.$fid];
                    }else {
                        $jjh_meetingDAO[0]['meeting_files'] = str_replace("/include/upload_file/", "",$jjh_meetingDAO[0]['meeting_files']);
                        $file =__REPICPATH__.$jjh_meetingDAO[0]['meeting_files'];
                    }

                    if(file_exists($file)){
                        ob_end_clean();
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: attachment; filename=" .basename($file)); //以真实文件名提供给浏览器下载

                        readfile($file);    // 打开文件，并输出
                    }else{
                        echo "<script>alert('文件不存在！');";
                        echo "window.location.href='/management/meeting'; ";
                        echo "</script>";
                        exit();
                    }
                }else{
                    echo "<script>alert('下载文件出错！');";
                    echo "window.location.href='/management/meeting'; ";
                    echo "</script>";
                    exit();
                }
            }
        }
		
		public function addConnectorAction(){
            $meetingDAO = $this->orm->createDAO('jjh_meeting')->order('id DESC');
		}

        public function _init(){
            error_reporting(0);
            $jjh_mg_ppDAO = $this->orm->createDAO('jjh_mg_pp')->get();
            $this->view->assign("jjh_mg_pp_list", $jjh_mg_ppDAO);

            if(!empty($jjh_mg_ppDAO)){
                foreach($jjh_mg_ppDAO as $k => $v){
                    $temp_array[$v['pid']] = $v['ppname'];
                }
            }
            $this->view->assign("jjh_mg_pp_tlist", $temp_array);

            $meetingCateList = $this->orm->createDAO('jjh_meeting_cate')->get();
            SessionUtil::sessionStart();
            SessionUtil::checkmanagement();

            $this->view->assign(array(
                'meetingCateList' => $meetingCateList
            ));
        }

        // 查看会议详细
        public function infoAction(){
            (int)$id = $_REQUEST['id'];
            if(!empty($id)){
                $jjh_meetingDAO = $this->orm->createDAO('jjh_meeting');
                $jjh_meetingDAO = $jjh_meetingDAO ->findId($id);
                $jjh_meetingDAO = $jjh_meetingDAO ->get();

                if(!empty($jjh_meetingDAO)){
                    $this->view->assign('jjh_meeting_info', $jjh_meetingDAO);
                    echo $this->view->render("index/header.phtml");
                    echo $this->view->render("meeting/meetinginfo.phtml");
                    echo $this->view->render("index/footer.phtml");
                    exit();
                }else {
                    echo "<script>alert('未检索到该会议详细信息，请查正后再试！');";
                    echo "window.location.href='/management/meeting'; ";
                    echo "</script>";
                    exit();
                }
            }else {
                echo "<script>alert('操作失败！');";
                echo "window.location.href='/management/meeting'; ";
                echo "</script>";
                exit();
            }
        }

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'to-add',
                'download',
                'add-connector',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}