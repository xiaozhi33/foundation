<?php
require_once("BaseController.php");
class Management_giftController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $giftDAO = $this->orm->createDAO('material_mg_gift_main');
        $name = HttpUtil::postString("name");
        if(!empty($name)){
            $giftDAO->findName($name);
        }
        $giftDAO = $giftDAO->order('id DESC');
        $giftDAO->getPager(array('path'=>'/management/gift/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addgiftmainAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/addgiftmain.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddgiftmainAction(){
        $id = $_REQUEST['id'];
        $giftDAO = $this->orm->createDAO('material_mg_gift_main');
        $name = HttpUtil::postString("name");
        $store = HttpUtil::postString("store");

        if($name == ''|| $store == ''){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $hasName = $this->hasGiftName($name);
        if(empty($id)) {
            if ($hasName) {
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('该车辆信息已添加，请核对后重新添加！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }
        }else {
            if ($hasName) {
                echo json_encode(array('msg' => "该车辆信息已添加，请核对后重新添加！！！！！！", 'return_url' => '/management/gift/'));
                exit;
            }
        }

        $giftDAO ->name = $name;
        $giftDAO ->store = $store;

        if(!empty($id))  //修改流程
        {
            $giftDAO ->findId($id);
        }
        try{
            $giftDAO ->save();
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
            echo("location.href='/management/gift';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/gift/'));
            exit;
        }
    }

    public function editgiftmainAction(){
        $id = HttpUtil::getString("id");
        $giftDAO = $this->orm->createDAO('material_mg_gift_main');
        $giftDAO ->findId($id);
        $giftDAO = $giftDAO ->get();

        if($giftDAO != "")
        {
            $this->view->assign("gift_info", $giftDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("gift/editgiftmain.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $giftDAO = $this->orm->createDAO('material_mg_gift_main')->order('id DESC');

        $this->view->assign("gift_info", $giftDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/editgiftmain.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delgiftmainAction(){
        $id = HttpUtil::getString("id");
        $giftDAO = $this->orm->createDAO('material_mg_gift_main');
        $giftDAO ->findId($id);
        $giftDAO = $giftDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/gift';");
        echo('</script>');
        exit;

    }

    /**
     * check是否已经存在礼品信息
     */
    public function hasGiftName($name){
        $giftDAO = $this->orm->createDAO('material_mg_gift_main');
        $giftDAO ->findName($name);
        $giftDAO = $giftDAO->get();
        if(!empty($giftDAO)){
            return true;
        }else {
            return false;
        }
    }

    // ================================使用礼品相关=======================================================

    public function usegiftAction()
    {
        $giftDAO = $this->orm->createDAO('material_mg_gift_info');
        $gift_name = HttpUtil::postString("gift_name");
        if(!empty($gift_name)){
            $giftDAO->findGift_name($gift_name);
        }
        $giftDAO = $giftDAO->order('id DESC');
        $giftDAO->getPager(array('path'=>'/management/gift/usegift'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/usegift.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addusegiftAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/addusegift.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddusegiftAction(){
        $id = $_REQUEST['id'];
        $giftDAO = $this->orm->createDAO('material_mg_gift_info');

        $gift_id = HttpUtil::postString("gift_name");
        $gift_datetime = HttpUtil::postString("gift_datetime");
        $brokerage = HttpUtil::postString("brokerage");
        $use = HttpUtil::postString("use");
        $customer_name = HttpUtil::postString("customer_name");
        $customer_tel = HttpUtil::postString("customer_tel");
        $customer_address = HttpUtil::postString("customer_address");
        $gift_count = HttpUtil::postString("gift_count");

        if(is_int($gift_count)){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('赠送数量必须为整数');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $gift_remarks = HttpUtil::postString("gift_remarks");

        if($gift_datetime == ''|| $use == ''|| $brokerage == ''|| $customer_name == ''|| $gift_count == ''|| $customer_tel == ''){
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

        // 礼品库存处理
        $gift_count = (int)$gift_count;
        $gift_main = $this->orm->createDAO("material_mg_gift_main")->findId($gift_id)->get();
        if($gift_count > $gift_main[0]['store']){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您所赠送的礼品数量不足，请下补货再进行操作。');");
            echo('history.back();');
            echo('</script>');
            exit;
        }
        // 减库存
        $gift_main = $this->orm->createDAO("material_mg_gift_main")->findId($gift_id);
        if((int)$gift_main[0]['store'] - (int)$gift_count < 0){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您所赠送的礼品数量不足，请下补货再进行操作。');");
            echo('history.back();');
            echo('</script>');
            exit;
        }
        $gift_main ->store = (int)$gift_main[0]['store'] - (int)$gift_count;
        $gift_main ->save();

        $giftDAO ->gift_id = $gift_id;
        $giftList = $this->orm->createDAO('material_mg_gift_main')->get();
        foreach ($giftList as $k => $v){
            if($v['id'] == $gift_id){
                $gift_name = $v['name'];
            }
        }
        $giftDAO ->gift_name = $gift_name;
        $giftDAO ->gift_datetime = $gift_datetime;
        $giftDAO ->brokerage = $brokerage;
        $giftDAO ->use = $use;
        $giftDAO ->customer_name = $customer_name;
        $giftDAO ->customer_tel = $customer_tel;
        $giftDAO ->customer_address = $customer_address;
        $giftDAO ->gift_count = $gift_count;
        $giftDAO ->gift_remarks = $gift_remarks;

        if(!empty($id))  //修改流程
        {
            $giftDAO ->findId($id);
        }
        try{
            $giftDAO ->save();
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
            echo("location.href='/management/gift/usegift';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/gift/usegift'));
            exit;
        }
    }

    public function editusegiftAction(){
        $id = HttpUtil::getString("id");
        $giftDAO = $this->orm->createDAO('material_mg_gift_info');
        $giftDAO ->findId($id);
        $giftDAO = $giftDAO ->get();

        if($giftDAO != "")
        {
            $this->view->assign("gift_info", $giftDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("gift/editusegift.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $giftDAO = $this->orm->createDAO('material_mg_gift')->order('id DESC');

        $this->view->assign("gift_info", $giftDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/editusegift.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delusegiftAction(){
        $id = HttpUtil::getString("id");
        $giftDAO = $this->orm->createDAO('material_mg_gift_info');
        $giftDAO ->findId($id);
        $giftDAO = $giftDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/gift/usegift';");
        echo('</script>');
        exit;

    }

    public function _init(){
        error_reporting(0);
        $giftList = $this->orm->createDAO('material_mg_gift_main')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $admin_list = $this->orm->createDAO("my_admin")->get();

        $this->view->assign(array(
            'giftList' => $giftList,
            'admin_list' => $admin_list
        ));
    }
}