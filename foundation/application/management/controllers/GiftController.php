<?php
require_once("BaseController.php");
class Management_giftController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $meetingDAO = $this->orm->createDAO('material_mg_gift_main')->order('id DESC');
        $meetingDAO->getPager(array('path'=>'/management/gift/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    /*
         *  add meeting
         */
    public function addgiftmainAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/addgiftmain.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    /*
     *  toSave meeting information
     */
    public function toAddAction(){
        $id = $_REQUEST['id'];
        $giftmainDAO = $this->orm->createDAO('material_mg_gift_main');
        $giftmainDAO ->name = HttpUtil::postString("name");
        $giftmainDAO ->store = HttpUtil::postString("store");

        if($giftmainDAO ->name == "" || $giftmainDAO ->store == ""){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }


        if(!empty($id))  //修改流程
        {
            $giftmainDAO ->findId($id);
        }
        try{
            $giftmainDAO ->name = HttpUtil::postString("name");
            $giftmainDAO ->store = HttpUtil::postString("store");
            $giftmainDAO ->save();
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
        echo("location.href='/management/gift';");
        echo('</script>');
        exit;

        /*echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/meeting/'));
        exit;*/
    }

    public function editgiftmainAction(){
        $id = HttpUtil::getString("id");
        $giftmainDAO = $this->orm->createDAO('material_mg_gift_main');
        $giftmainDAO ->findId($id);
        $giftmainDAO = $giftmainDAO ->get();

        if($giftmainDAO != "")
        {
            $this->view->assign("gift_main_info", $giftmainDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("gift/editgiftmain.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $giftmainDAO = $this->orm->createDAO('material_mg_gift_main')->order('id DESC');

        $this->view->assign("gift_main_info", $giftmainDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/editgiftmain.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delAction(){
        $id = HttpUtil::getString("id");
        $giftmainDAO = $this->orm->createDAO('material_mg_gift_main');
        $giftmainDAO ->findId($id);
        $giftmainDAO = $giftmainDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/gift';");
        echo('</script>');
        exit;

    }

    public function _init(){
        error_reporting(0);
        $giftmainList = $this->orm->createDAO('material_mg_gift_main')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $this->view->assign(array(
            'giftmainList' => $giftmainList
        ));
    }
}