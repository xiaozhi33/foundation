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
			$id = HttpUtil::postString("id");
            $meeting_name = HttpUtil::postString("meeting_name");
            $meeting_cate = HttpUtil::postString("meeting_cate");
            $meeting_joiner = HttpUtil::postString("meeting_joiner");
            $meeting_content = HttpUtil::postString("meeting_content");
			$meeting_start_time = HttpUtil::postString("meeting_start_time");
			$meeting_end_time = HttpUtil::postString("meeting_end_time");
			$meeting_address = HttpUtil::postString("meeting_address");

            if($meeting_name == "" || $meeting_cate == "" || $meeting_joiner == "" || $meeting_content == ""){
                alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            }

            $meetingDAO = $this->orm->createDAO('jjh_meeting');
            if(!empty($id))  //修改流程
            {
                $meetingDAO ->findId($id);
            }
            $meetingDAO ->meeting_name = $meeting_name;
            $meetingDAO ->meeting_cate = $meeting_cate;
            $meetingDAO ->meeting_joiner = $meeting_joiner;
            $meetingDAO ->meeting_content = $meeting_content;
            $meetingDAO ->meeting_start_time = $meeting_start_time;
            $meetingDAO ->meeting_end_time = $meeting_end_time;
            $meetingDAO ->meeting_address = $meeting_address;
            $rs = $meetingDAO ->save();

            if($rs){
                echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/meeting/'));
                exit;
            }else {
                alert_back("保存失败，请联系系统管理员");
            }


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
				echo $this->view->render("meeting/edit.phtml");
				echo $this->view->render("index/footer.phtml");
			}
			
            $id = $_GET['id'];
            $meetingDAO = $this->orm->createDAO('jjh_meeting')->order('id DESC');

            $this->view->assign("meeting_info", $meetingDAO);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("meeting/addmeeting.phtml");
            echo $this->view->render("index/footer.phtml");
		}
		
		public function delAction(){
			$id = HttpUtil::getString("id");
            $meetingDAO = $this->orm->createDAO('jjh_meeting');
			$meetingDAO ->findId($id);
			$meetingDAO = $meetingDAO ->delete();
		}
		
		public function addConnectorAction(){
            $meetingDAO = $this->orm->createDAO('jjh_meeting')->order('id DESC');
		}

        public function _init(){
            error_reporting(0);
            $meetingCateList = $this->orm->createDAO('jjh_meeting_cate')->get();

            $this->view->assign(array(
                'meetingCateList' => $meetingCateList
            ));
        }
	}