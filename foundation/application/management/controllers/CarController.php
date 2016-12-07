<?php
require_once("BaseController.php");
class Management_carController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $meetingDAO = $this->orm->createDAO('material_mg_cars')->order('id DESC');
        $meetingDAO->getPager(array('path'=>'/management/car/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/addcar.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddAction(){
        $id = $_REQUEST['id'];
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $carDAO ->car_number = HttpUtil::postString("car_number");

        if($carDAO ->car_number){
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
            $carDAO ->findId($id);
        }
        try{
            $carDAO ->save();
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
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $carDAO ->findId($id);
        $carDAO = $carDAO ->get();

        if($carDAO != "")
        {
            $this->view->assign("car_info", $carDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("car/editcar.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $carDAO = $this->orm->createDAO('material_mg_cars')->order('id DESC');

        $this->view->assign("car_info", $carDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/editcar.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delAction(){
        $id = HttpUtil::getString("id");
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $carDAO ->findId($id);
        $carDAO = $carDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/car';");
        echo('</script>');
        exit;

    }

    public function _init(){
        error_reporting(0);
        $carList = $this->orm->createDAO('material_mg_cars')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $this->view->assign(array(
            'carList' => $carList
        ));
    }
}