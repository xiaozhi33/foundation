<?php
require_once("BaseController.php");
class Management_carController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $carDAO = $this->orm->createDAO('material_mg_cars_main');
        $car_number = HttpUtil::postString("car_number");
        if(!empty($car_number)){
            $carDAO->findCar_number($car_number);
        }
        $carDAO = $carDAO->order('id DESC');
        $carDAO->getPager(array('path'=>'/management/car/index'))->assignTo($this->view);

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

        $hasCarNumber = $this->hasCarNumber($car_number,$id);
        if(empty($id)) {
            if ($hasCarNumber) {
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('该车辆信息已添加，请核对后重新添加！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }
        }else {
            if ($hasCarNumber) {
                echo json_encode(array('msg' => "该车辆信息已添加，请核对后重新添加！！！！！！", 'return_url' => '/management/car/'));
                exit;
            }
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

        if(empty($id)){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存成功');");
            echo("location.href='/management/car';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>'保存成功！','return_url'=>'/management/car/'));
            exit;
        }
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

    /**
     * check是否已经存在车辆信息
     */
    public function hasCarNumber($car_number,$id=''){
        $carDAO = $this->orm->createDAO('material_mg_cars_main');
        $carDAO ->findCar_number($car_number);
        if($id !=''){
            $carDAO ->selectLimit .= ' AND id !='.$id;
        }
        $carDAO = $carDAO->get();
        if(!empty($carDAO)){
            return true;
        }else {
            return false;
        }
    }

    // ================================用车相关=======================================================

    public function usecarAction()
    {
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $car_number = HttpUtil::postString("car_number");
        if(!empty($car_number)){
            $carDAO->findCar_number($car_number);
        }
        $carDAO = $carDAO->order('id DESC');
        $carDAO->getPager(array('path'=>'/management/car/usecar'))->assignTo($this->view);

        $usecar_now = $this->usecar_now();
        $this->view->assign("usecar_now", $usecar_now);

        if(!empty($usecar_now)){
            $car_ids = '';
            foreach($usecar_now as $key => $value){
                $car_ids .= $value['car_id'].',';
            }
            $car_ids = substr($car_ids,0,strlen($car_ids)-1);
        }

        $car_kongxian = $this->orm->createDAO('material_mg_cars_main');
        if($car_ids != ''){
            $car_kongxian ->selectLimit .= " AND id not in(".$car_ids.")";
        }
        $car_kongxian = $car_kongxian ->get();
        $this->view->assign("car_kongxian", $car_kongxian);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/usecar.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function usecar_now(){
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $now = time();
        $carDAO ->selectLimit .= " AND use_starttime < ".$now." AND use_endtime >".$now;
        return $carDAO ->get();
    }

    public function addusecarAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/addusecar.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddusecarAction(){
        $id = $_REQUEST['id'];
        $carDAO = $this->orm->createDAO('material_mg_cars');

        $car_id = HttpUtil::postString("car_number");
        $destination_use = HttpUtil::postString("destination_use");
        $kilometers = HttpUtil::postString("kilometers");
        $use_starttime = HttpUtil::postString("use_starttime");
        $use_endtime = HttpUtil::postString("use_endtime");
        $cost_oil_counts = HttpUtil::postString("cost_oil_counts");
        $cost_price = HttpUtil::postString("cost_price");
        $cost_road_toll = HttpUtil::postString("cost_road_toll");
        $user = HttpUtil::postString("user");
        $driver = HttpUtil::postString("driver");
        $description = HttpUtil::postString("description");

        if($destination_use == ''|| $car_id == ''|| $user == ''|| $driver == ''|| $use_starttime == ''|| $use_endtime == ''){
            if(!empty($id)){
                echo json_encode(array('msg'=>"您输入的信息不完整，请查正后继续添加！！！！！",'return_url'=>''));
                exit;
            }
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        if(empty($id))  //首次判断同一时间一台车辆不能添加多条记录
        {
            $rs = $this->hasUserCarAction($use_starttime,$use_endtime,$car_id);
            if(!empty($rs)){
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('该车辆在".$use_starttime."-".$use_endtime."期间已有使用记录，请查正后继续添加！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }
        }

        $carDAO ->car_id = $car_id;
        $carList = $this->orm->createDAO('material_mg_cars_main')->get();
        foreach ($carList as $k => $v){
            if($v['id'] == $car_id){
                $car_number = $v['car_number'];
            }
        }
        $carDAO ->car_number = $car_number;
        $carDAO ->destination_use = $destination_use;
        $carDAO ->kilometers = $kilometers;
        $carDAO ->use_starttime = strtotime($use_starttime);
        $carDAO ->use_endtime = strtotime($use_endtime);
        $carDAO ->cost_oil_counts = $cost_oil_counts;
        $carDAO ->cost_price = $cost_price;
        $carDAO ->cost_road_toll = $cost_road_toll;
        $carDAO ->user = $user;
        $carDAO ->driver = $driver;
        $carDAO ->description = $description;


        if(!empty($id))  //修改流程
        {
            $carDAO ->findId($id);
        }
        try{
            $carDAO ->save();
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
            echo("location.href='/management/car/usecar';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/car/usecar'));
            exit;
        }
    }

    public function editusecarAction(){
        $id = HttpUtil::getString("id");
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $carDAO ->findId($id);
        $carDAO = $carDAO ->get();

        if($carDAO != "")
        {
            $this->view->assign("car_info", $carDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("car/editusecar.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $carDAO = $this->orm->createDAO('material_mg_cars')->order('id DESC');

        $this->view->assign("car_info", $carDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("car/editusecar.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delusecarAction(){
        $id = HttpUtil::getString("id");
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $carDAO ->findId($id);
        $carDAO = $carDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/car/usecar';");
        echo('</script>');
        exit;

    }

    //权限
    public function acl()
    {
        $action = $this->getRequest()->getActionName();
        $except_actions = array(
            'to-addcarmain',
            'has-car-number',
            'usecar_now',
            'to-addusecar',
            'has-user-car',
        );
        if (in_array($action, $except_actions)) {
            return;
        }
        parent::acl();
    }

    public function hasUserCarAction($star_time,$end_time,$car_number){
        $carDAO = $this->orm->createDAO('material_mg_cars');
        $carDAO ->selectLimit .= " AND (use_starttime < '".$star_time."' OR use_endtime >'".$end_time."')";
        $carDAO ->selectLimit .= " AND car_number=".$car_number;
        return $carDAO ->get();
    }
    public function _init(){
        //error_reporting(0);
        $carList = $this->orm->createDAO('material_mg_cars_main')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $this->view->assign(array(
            'carList' => $carList
        ));
    }
}