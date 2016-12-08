<?php
require_once("BaseController.php");
class Management_taskController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $name = HttpUtil::postString("name");
        if(!empty($name)){
            $taskDAO->findName($name);
        }
        $taskDAO = $taskDAO->order('id DESC');
        $taskDAO->getPager(array('path'=>'/management/task/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("task/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addgiftmainAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("task/addtask.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddtaskAction(){
        $id = $_REQUEST['id'];
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $name = HttpUtil::postString("name");

        if($name == ''|| $name == ''){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $taskDAO ->name = $name;

        if(!empty($id))  //修改流程
        {
            $taskDAO ->findId($id);
        }
        try{
            $taskDAO ->save();
        }catch (Exception $e){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存失败！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        if(empty($id)){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存成功');");
            echo("location.href='/management/task';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/task/'));
            exit;
        }
    }

    public function edittaskAction(){
        $id = HttpUtil::getString("id");
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $taskDAO ->findId($id);
        $taskDAO = $taskDAO ->get();

        if($taskDAO != "")
        {
            $this->view->assign("gift_info", $taskDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("gift/edittask.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $taskDAO = $this->orm->createDAO('jjh_mg_task')->order('id DESC');

        $this->view->assign("gift_info", $taskDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/edittask.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delgiftmainAction(){
        $id = HttpUtil::getString("id");
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $taskDAO ->findId($id);
        $taskDAO = $taskDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/task';");
        echo('</script>');
        exit;

    }

    /**
     * check是否已经存在
     */
    public function has_is($name){
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $taskDAO ->findName($name);
        $taskDAO = $taskDAO->get();
        if(!empty($taskDAO)){
            return true;
        }else {
            return false;
        }
    }

    public function _init(){
        error_reporting(0);
        $orgList = $this->orm->createDAO('jjh_mg_task')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $this->view->assign(array(
            'orgList' => $orgList
        ));
    }
}