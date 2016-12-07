<?php
require_once("BaseController.php");
class Management_carController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $meetingDAO = $this->orm->createDAO('material_mg_cars_main');
        $car_number = HttpUtil::postString("car_number");
        if(!empty($car_number)){
            $meetingDAO->findCar_number($car_number);
        }
        $meetingDAO = $meetingDAO->order('id DESC');
        $meetingDAO->getPager(array('path'=>'/management/car/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addcarmainAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/addcarmain.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddcarmainAction(){
        $id = $_REQUEST['id'];
        $carDAO = $this->orm->createDAO('material_mg_cars_main');
        $name = HttpUtil::postString("name");
        $car_number = HttpUtil::postString("car_number");
        $description = HttpUtil::postString("description");

        if($name == ''|| $car_number == ''){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $hasCarNumber = $this->hasCarNumber($car_number);
        if($hasCarNumber){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('该车辆信息已添加，请核对后重新添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $carDAO ->name = $name;
        $carDAO ->car_number = $car_number;
        $carDAO ->description = $description;


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
        echo("location.href='/management/car';");
        echo('</script>');
        exit;

        /*echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/meeting/'));
        exit;*/
    }

    public function editcarmainAction(){
        $id = HttpUtil::getString("id");
        $carDAO = $this->orm->createDAO('material_mg_cars_main');
        $carDAO ->findId($id);
        $carDAO = $carDAO ->get();

        if($carDAO != "")
        {
            $this->view->assign("car_info", $carDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("car/editcarmain.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $carDAO = $this->orm->createDAO('material_mg_cars_main')->order('id DESC');

        $this->view->assign("car_info", $carDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/editcarmain.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delcarmainAction(){
        $id = HttpUtil::getString("id");
        $carDAO = $this->orm->createDAO('material_mg_cars_main');
        $carDAO ->findId($id);
        $carDAO = $carDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/car';");
        echo('</script>');
        exit;

    }

    public function addusecarAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/addusecar.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddusecarAction(){
        $id = $_REQUEST['id'];
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $carDAO ->car_number = HttpUtil::postString("car_number");
        $carDAO ->destination_use = HttpUtil::postString("destination_use");
        $carDAO ->kilometers = HttpUtil::postString("kilometers");
        $carDAO ->use_starttime = HttpUtil::postString("use_starttime");
        $carDAO ->use_endtime = HttpUtil::postString("use_endtime");
        $carDAO ->cost_oil_counts = HttpUtil::postString("cost_oil_counts");
        $carDAO ->cost_price = HttpUtil::postString("cost_price");
        $carDAO ->cost_road_toll = HttpUtil::postString("cost_road_toll");
        $carDAO ->user = HttpUtil::postString("user");
        $carDAO ->driver = HttpUtil::postString("driver");
        $carDAO ->description = HttpUtil::postString("description");

        if($carDAO ->car_number == ''|| $carDAO ->destination_use == ''|| $carDAO ->use_starttime == ''|| $carDAO ->use_endtime == ''){
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

    public function hasUserCarAction($star_time,$end_time){
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $carDAO ->selectLimit .= " use_starttime < ".$star_time." OR use_endtime >".$end_time;
    }

    /**
     * check是否已经存在车辆信息
     */
    public function hasCarNumber($car_number){
        $carDAO = $this->orm->createDAO('material_mg_cars_main');
        $carDAO ->findCar_number($car_number);
        $carDAO = $carDAO->get();
        if(!empty($carDAO)){
            return true;
        }else {
            return false;
        }
    }

    public function _init(){
        error_reporting(0);
        $carList = $this->orm->createDAO('material_mg_cars_main')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $this->view->assign(array(
            'carList' => $carList
        ));
    }
}