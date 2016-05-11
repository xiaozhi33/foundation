<?php
	require_once("BaseController.php");
	class Management_peibiController extends BaseController
    {

		public function indexAction(){
            $peibikDAO = $this->orm->createDAO('pm_mg_peibi')->order('id DESC');
            $peibikDAO->getPager(array('path'=>'/management/peibi/index'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("peibi/index.phtml");
            echo $this->view->render("index/footer.phtml");
		}
        /*
         *  add feedback
         */
		public function addAction(){
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("peibi/addpeibi.phtml");
            echo $this->view->render("index/footer.phtml");
		}

        /*
         *  toSave feedback information
         */
        public function toAddAction(){
            (int)$id = $_REQUEST['id'];
            $pm_id = HttpUtil::postString("pm_id");
            $pm_name = HttpUtil::postString("pm_name");
            $is_peibi = HttpUtil::postString("is_peibi");
            $is_pass = HttpUtil::postString("is_pass");
            $jpyy = HttpUtil::postString("jpyy");
            $je = HttpUtil::postString("je");
            $peibi_datetime = HttpUtil::postString("peibi_datetime");
            $card = HttpUtil::postString("card");
            $jffzr = HttpUtil::postString("jffzr");
            $peibi_spr = HttpUtil::postString("peibi_spr");

            $peibiDAO = $this->orm->createDAO('pm_mg_peibi');

            if($pm_id == "" || $peibi_datetime == ""){
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }

            try{
                if(!empty($id)){
                    $peibiDAO ->findId($id);
                }
                $peibiDAO ->pm_id = $pm_id;
                $peibiDAO ->pm_name = $pm_name;
                $peibiDAO ->is_peibi = $is_peibi; //
                $peibiDAO ->is_pass = $is_pass; //
                $peibiDAO ->jpyy = $jpyy; //
                $peibiDAO ->je = $je;  //
                $peibiDAO ->peibi_datetime = $peibi_datetime;   //
                $peibiDAO ->card = $card;   //
                $peibiDAO ->jffzr = $jffzr;   //
                $peibiDAO ->peibi_spr = $peibi_spr;   //
                $peibiDAO ->save();
            }catch (Exception $e){

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
            echo("location.href='/management/feedback';");
            echo('</script>');
            exit;
        }
		
		public function editAction(){
			$id = HttpUtil::getString("id");
            $peibiDAO = $this->orm->createDAO('pm_mg_peibi');
            $peibiDAO ->findId($id);
            $peibiDAO = $peibiDAO ->get();
			
			if($peibiDAO != "")
			{
				$this->view->assign("peibiDAO", $peibiDAO);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("peibi/editpeibi.phtml");
				echo $this->view->render("index/footer.phtml");
                exit();
			}
		}
		
		public function delAction(){
			$id = HttpUtil::getString("id");
            $peibiDAO = $this->orm->createDAO('pm_mg_peibi');
            $peibiDAO ->findId($id);
            $peibiDAO = $peibiDAO ->delete();

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/management/feedback';");
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