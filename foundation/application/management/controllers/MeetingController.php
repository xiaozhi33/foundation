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
		public function addAction(){
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("meeting/addmeeting.phtml");
            echo $this->view->render("index/footer.phtml");
		}
        public function toAddAction(){
            $meeting_name = HttpUtil::postString("meeting_name");
            $meeting_cate = HttpUtil::postString("meeting_cate");
            $meeting_joiner = HttpUtil::postString("meeting_joiner");
            $meeting_content = HttpUtil::postString("meeting_content");

            if($meeting_name == "" || $meeting_cate == "" || $meeting_joiner == "" || $meeting_content == ""){
                alert_back("您输入的信息不完整，请查正后继续添加");
            }
            $meetingDAO = $this->orm->createDAO('jjh_meeting');
            $meetingDAO ->meeting_name = $meeting_name;
            $meetingDAO ->meeting_cate = $meeting_cate;
            $meetingDAO ->meeting_joiner = $meeting_joiner;
            $meetingDAO ->meeting_content = $meeting_content;
            $rs = $meetingDAO ->save();
            var_dump($rs);exit();
        }
		public function editAction(){
            $meetingDAO = $this->orm->createDAO('jjh_meeting')->order('id DESC');
		}
        public function saveActiono(){

        }
		public function delAction(){
            $meetingDAO = $this->orm->createDAO('jjh_meeting')->order('id DESC');
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