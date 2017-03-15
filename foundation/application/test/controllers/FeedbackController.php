<?php
	require_once("BaseController.php");
	class Test_feedbackController extends BaseController
    {
		public function indexAction(){
            $feedbackDAO = $this->orm->createDAO('pm_mg_feedback')->order('id DESC');
            if(!empty($_REQUEST['pm_name'])){
                $feedbackDAO->findPm_name($_REQUEST['pm_name']);
                $this->view->assign("pname", $_REQUEST['pm_name']);
            }
            $feedbackDAO->getPager(array('path'=>'/test/feedback/index'))->assignTo($this->view);

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
            $feedbacker = implode(",",$_REQUEST['feedbacker']);
            $jbr = implode(",",$_REQUEST['jbr']);
            $bz = HttpUtil::postString("bz");

            $pm_mg_feedbackDAO = $this->orm->createDAO('pm_mg_feedback');

            if($pm_id == "" || $feedback_datetime == "" || $feedback_type == "" || $jindu == "" || $pm_name == ''){
                alert_back_old('您输入的信息不完整，请查正后继续添加！！！！！');
            }

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
                        echo "window.location.href='/test/feedback';";
                        echo "</script>";
                        exit();
                    }else{
                        $pm_mg_feedbackDAO->files =  __GETPICPATH__."jjh_download/".$result['picname'];
                        $pm_mg_feedbackDAO->files_name = $_FILES['files']['name'];
                    }
                }
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
                $pm_mg_feedbackDAO ->bz = $bz;   // 经办人
                $pm_mg_feedbackDAO ->save();
            }catch (Exception $e){
                alert_back_old('保存失败！！！！！');
            }

            alert_go_old('保存成功', "/test/feedback/index");
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
            $pm_mg_feedbackDAO = $this->orm->createDAO('pm_mg_feedback');
            $pm_mg_feedbackDAO ->findId($id);
            $pm_mg_feedbackDAO ->delete();

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/test/feedback';");
            echo('</script>');
            exit;

        }

         public function _init(){
            $this ->dbhelper = new DBHelper();
             $this ->dbhelper ->connect('test');
            SessionUtil::sessionStart();
            SessionUtil::checkmanagement();

             // 回馈人list
             $jjh_mg_ppDAO = $this->orm->createDAO('jjh_mg_pp')->get();
             if(!empty($jjh_mg_ppDAO)){
                 foreach($jjh_mg_ppDAO as $k => $v){
                     $temp_array[$v['pid']] = $v['ppname'];
                 }
             }
             $this->view->assign("jjh_mg_pp_list", $temp_array);

             // 经办人list


            //项目名称列表
            $pm_chouzi = new pm_mg_chouziDAO();
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);

            //获取筹资项目list
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
            $this->view->assign("chouzi_lists",$chouziDAO);
        }
	}