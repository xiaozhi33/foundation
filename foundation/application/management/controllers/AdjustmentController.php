<?php
require_once("BaseController.php");
class Management_adjustmentController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $aaDAO = $this->orm->createDAO('pm_mg_amount_adjustment');
        $in_pm_name = HttpUtil::getString("in_pm_name");
        $out_pm_name = HttpUtil::getString("out_pm_name");
        $starttime = HttpUtil::getString("starttime");
        $endtime = HttpUtil::getString("endtime");

        if(!empty($in_pm_name)){
            $aaDAO->findIn_pm_name($in_pm_name);
        }
        if(!empty($out_pm_name)){
            $aaDAO->findOut_pm_name($out_pm_name);
        }

        if(!empty($starttime)){
            $aaDAO ->selectLimt .= " AND datetimes >= '".$starttime."'";
        }

        if(!empty($endtime)){
            $aaDAO ->selectLimt .= " AND datetimes <= '".$endtime."'";
        }

        $aaDAO = $aaDAO->order('id DESC');
        $aaDAO->getPager(array('path'=>'/management/adjustment/index'))->assignTo($this->view);


        echo $this->view->render("index/header.phtml");
        echo $this->view->render("adjustment/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addaaAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("adjustment/addaa.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddaaAction(){
        $id = $_REQUEST['id'];
        $aaDAO = $this->orm->createDAO('pm_mg_amount_adjustment');

        $in_pm_id = HttpUtil::postString("in_pm_id");
        $in_pm_name = HttpUtil::postString("in_pm_name");
        $out_pm_id = HttpUtil::postString("out_pm_id");
        $out_pm_name = HttpUtil::postString("out_pm_name");
        $datetimes = HttpUtil::postString("datetimes");
        $je = HttpUtil::postString("je");
        $beizhu = HttpUtil::postString("beizhu");

        if($in_pm_name == ''|| $out_pm_name == ''|| $datetimes == ''|| $je == ''){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $aaDAO ->in_pm_id = $in_pm_id;
        $aaDAO ->in_pm_name = $in_pm_name;
        $aaDAO ->out_pm_id = $out_pm_id;
        $aaDAO ->out_pm_name = $out_pm_name;
        $aaDAO ->datetimes = $datetimes;
        $aaDAO ->je = $je;
        $aaDAO ->beizhu = $beizhu;


        if(!empty($id))  //修改流程
        {
            $aaDAO ->findId($id);
        }
        try{
            $aaDAO ->save();
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
        echo("location.href='/management/adjustment/index';");
        echo('</script>');
        exit;
    }

    public function editaaAction(){
        $id = HttpUtil::getString("id");
        $aaDAO = $this->orm->createDAO('pm_mg_amount_adjustment');
        $aaDAO ->findId($id);
        $aaDAO = $aaDAO ->get();

        if($aaDAO != "")
        {
            $this->view->assign("aa_info", $aaDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("adjustment/editaa.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }else {
            $this->alert_back("您的操作有误！");
        }
    }

    public function delaaAction(){
        $id = HttpUtil::getString("id");
        $aaDAO = $this->orm->createDAO('pm_mg_amount_adjustment');
        $aaDAO ->findId($id);
        $aaDAO = $aaDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/adjustment/index';");
        echo('</script>');
        exit;
    }

    //权限
    public function acl()
    {
        $action = $this->getRequest()->getActionName();
        $except_actions = array(
            'index',
            'addaa',
            'to-addaa',
            'editaa',
            'delaa',
        );
        if (in_array($action, $except_actions)) {
            return;
        }
        parent::acl();
    }


    public function _init(){
        //error_reporting(0);
        $carList = $this->orm->createDAO('material_mg_cars_main')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        //项目名称列表
        $pm_chouzi = $this->orm->createDAO("pm_mg_chouzi");
        $pm_chouzi = $pm_chouzi ->get();
        $this->view->assign("pmlist",$pm_chouzi);

        $this->view->assign(array(
            'carList' => $carList
        ));
    }
}