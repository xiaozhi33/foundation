<?php
	require_once("BaseController.php");
	class Management_feedbackController extends BaseController
    {
		public function indexAction(){
            $feedbackDAO = $this->orm->createDAO('pm_mg_feedback')->order('id DESC');
            if(!empty($_REQUEST['pm_name'])){
                $feedbackDAO->findPm_name($_REQUEST['pm_name']);
                $this->view->assign("pname", $_REQUEST['pm_name']);
            }
            $feedbackDAO->getPager(array('path'=>'/management/feedback/index'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("feedback/index.phtml");
            echo $this->view->render("index/footer.phtml");
		}
        /*
         *  add feedback
         */
		public function addAction(){
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("feedback/addfeedback.phtml");
            echo $this->view->render("index/footer.phtml");
		}

        /*
         *  toSave feedback information
         */
        public function toAddAction(){
            (int)$id = $_REQUEST['id'];
            $pm_id = HttpUtil::postString("pm_id");
            $pm_name = HttpUtil::postString("pm_name");
            $feedback_datetime = HttpUtil::postString("feedback_datetime");
            $feedback_type = HttpUtil::postString("feedback_type");
            $jindu = HttpUtil::postString("jindu");
            $feedbacker = HttpUtil::postString("feedbacker");
            $jbr = HttpUtil::postString("jbr");

            $pm_mg_feedbackDAO = $this->orm->createDAO('pm_mg_feedback');

            if($pm_id == "" || $feedback_datetime == "" || $feedback_type == "" || $jindu == ""){
                alert_back('您输入的信息不完整，请查正后继续添加！！！！！');
            }

            try{
                if(!empty($id)){
                    $pm_mg_feedbackDAO ->findId($id);
                }
                $pm_mg_feedbackDAO ->pm_id = $pm_id;
                $pm_mg_feedbackDAO ->pm_name = $pm_name;
                $pm_mg_feedbackDAO ->feedback_datetime = $feedback_datetime; // 回馈时间
                $pm_mg_feedbackDAO ->feedback_type = $feedback_type; // 回馈方式 致电、致函、当面拜访、赠送纪念品、其他
                $pm_mg_feedbackDAO ->jindu = $jindu; // 进度
                $pm_mg_feedbackDAO ->feedbacker = $feedbacker;  // 回馈人
                $pm_mg_feedbackDAO ->jbr = $jbr;   // 经办人
                $pm_mg_feedbackDAO ->save();
            }catch (Exception $e){
                alert_back('保存失败！！！！！');
            }

            alert_go('保存成功', "/management/feedback/index");
        }
		
		public function editAction(){
			$id = HttpUtil::getString("id");
            $pm_mg_feedbackDAO = $this->orm->createDAO('pm_mg_feedback');
            $pm_mg_feedbackDAO ->findId($id);
            $pm_mg_feedbackDAO = $pm_mg_feedbackDAO ->get();
			
			if($pm_mg_feedbackDAO != "")
			{
				$this->view->assign("pm_mg_feedbackDAO", $pm_mg_feedbackDAO);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("feedback/editfeedback.phtml");
				echo $this->view->render("index/footer.phtml");
                exit();
			}
		}
		
		public function delAction(){
			$id = HttpUtil::getString("id");
            $pm_mg_feedbackDAO = $this->orm->createDAO('pm_mg_feedbackDAO');
            $pm_mg_feedbackDAO ->findId($id);
            $pm_mg_feedbackDAO = $pm_mg_feedbackDAO ->delete();

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/management/feedback';");
            echo('</script>');
            exit;

        }

         public function _init(){
            $this ->dbhelper = new DBHelper();
            $this ->dbhelper ->connect();
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