<?php
require_once("BaseController.php");
class Management_refundController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $refundDAO = $this->orm->createDAO('pm_mg_refund');
        $pm_name = HttpUtil::getString("pm_name");
        $starttime = HttpUtil::getString("starttime");
        $endtime = HttpUtil::getString("endtime");

        $refundDAO ->withPm_mg_info(array("pm_info_id"=>"id"));
        $refundDAO ->select("pm_mg_refund.*, pm_mg_info.*, pm_mg_info.beizhu as p_beizhu,pm_mg_refund.beizhu as r_beizhu, pm_mg_refund.id as r_id");

        if(!empty($in_pm_name)){
            $refundDAO->findPm_name($pm_name);
        }

        if(!empty($starttime)){
            $refundDAO ->selectLimit .= " AND datetimes >= ".strtotime($starttime);
        }

        if(!empty($endtime)){
            $refundDAO ->selectLimit .= " AND datetimes <= ".strtotime($endtime);
        }
        $refundDAO->getPager(array('path'=>'/management/refund/index'))->assignTo($this->view);


        echo $this->view->render("index/header.phtml");
        echo $this->view->render("refund/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addAction(){
        if(empty($_REQUEST['pm_name'])){
            $this->alert_go("请选择要退款的项目！", "/management/refund?pm_name=".$_REQUEST['pm_name']);
        }
        // 所有来款信息
        $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
        $pm_mg_infoDAO ->findPm_name($_REQUEST['pm_name']);
        $pm_mg_infoDAO ->findCate_id(0);
        $pm_mg_infoDAO ->findIs_renling(1);
        $pm_mg_infoDAO = $pm_mg_infoDAO ->get();

        $this->view->assign("pm_mg_infoDAO", $pm_mg_infoDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("refund/add.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddAction(){
        $id = $_REQUEST['id'];
        $refundDAO = $this->orm->createDAO('pm_mg_refund');

        $pm_name = HttpUtil::postString("pm_name");
        $je = HttpUtil::postString("je");
        $pm_info_id = HttpUtil::postString("pm_info_id");
        $datetimes = HttpUtil::postString("datetimes");
        $jbr = $_REQUEST['jbr'];
        $beizhu = HttpUtil::postString("beizhu");

        if($pm_name == ''|| $je == ''|| $pm_info_id == ''|| $datetimes == ''){
            $this->alert_go("您输入的信息不完整，请查正后继续添加！", "/management/refund/add?pm_name=".$_REQUEST['pm_name']);
        }

        // 退款金额不能大于来款金额
        $pm_infoDAO = $this->orm->createDAO("pm_mg_info")->findId($pm_info_id)->get();
        if($je > $pm_infoDAO[0]['zijin_daozheng_jiner']) {
            $this->alert_go("退款金额不能大于来款金额！", "/management/refund/add?pm_name=".$_REQUEST['pm_name']);
        }

        $refundDAO ->pm_name = $pm_name;
        $refundDAO ->je = $je;
        $refundDAO ->pm_info_id = $pm_info_id;
        $refundDAO ->datetimes = strtotime($datetimes);
        $refundDAO ->jbr = implode(",",$jbr);
        $refundDAO ->beizhu = $beizhu;

        if(!empty($id))  //修改流程
        {
            $refundDAO ->findId($id);

            $rinfo =  $this->orm->createDAO("pm_mg_refund")->findId($id)->get();
            // 金额的调整
            $pm_info_DAO = $this->orm->createDAO("pm_mg_info")->findId($rinfo[0]['refund_pm_info_id']);
            $pm_info_DAO ->zijin_daozhang_datetime = date("Y-m-d H:i:s", strtotime($datetimes));
            $pm_info_DAO ->pm_pp =  $pm_infoDAO[0]['pm_pp'];
            $pm_info_DAO ->beizhu = $beizhu;
            $pm_info_DAO ->zijin_daozheng_jiner = '-'.$je;
            $pm_info_DAO ->save();

        }else {
            // 同时加入一条退款为负值的来款
            $pm_info_DAO = $this->orm->createDAO("pm_mg_info");
            $pm_info_DAO ->cate_id = 0;
            $pm_info_DAO ->pm_name = $pm_name;
            $pm_info_DAO ->pm_pp =  $pm_infoDAO[0]['pm_pp'];
            $pm_info_DAO ->pm_juanzeng_cate =  $pm_infoDAO[0]['pm_juanzeng_cate'];
            $pm_info_DAO ->zijin_daozhang_datetime = date("Y-m-d H:i:s", strtotime($datetimes));
            $pm_info_DAO ->zijin_daozheng_jiner = "-".$je;
            $pm_info_DAO ->beizhu = $beizhu;
            $pm_info_DAO ->is_renling = 1;
            $pm_info_DAO ->is_refund = 1;
            $pm_info_DAO ->is_web_show = 0;
            $refund_id = $pm_info_DAO ->save();

            if(!empty($refund_id)){
                $refundDAO ->refund_pm_info_id = $refund_id;
            }
        }
        try{
            $refundDAO ->save();
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
        echo("location.href='/management/refund/index';");
        echo('</script>');
        exit;
    }

    public function editAction(){
        $id = HttpUtil::getString("id");
        $refundDAO = $this->orm->createDAO('pm_mg_refund');
        $refundDAO ->findId($id);
        $refundDAO = $refundDAO ->get();

        // 所有来款信息
        $pm_mg_infoDAO = $this->orm->createDAO("pm_mg_info");
        $pm_mg_infoDAO ->findPm_name($refundDAO[0]['pm_name']);
        $pm_mg_infoDAO ->findCate_id(0);
        $pm_mg_infoDAO ->findIs_renling(1);
        $pm_mg_infoDAO = $pm_mg_infoDAO ->get();

        $this->view->assign("pm_mg_infoDAO", $pm_mg_infoDAO);

        if($refundDAO != "")
        {
            $this->view->assign("refund_info", $refundDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("refund/edit.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }else {
            $this->alert_back("您的操作有误！");
        }
    }

    public function delAction(){
        $id = HttpUtil::getString("id");
        $rinfo =  $this->orm->createDAO("pm_mg_refund")->findId($id)->get();
        $pm_info_DAO = $this->orm->createDAO("pm_mg_info")->findId($rinfo[0]['refund_pm_info_id']);
        $pm_info_DAO ->is_renling = 3;
        $pm_info_DAO ->save();

        $refundDAO = $this->orm->createDAO('pm_mg_refund');
        $refundDAO ->findId($id);
        $refundDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/refund/index';");
        echo('</script>');
        exit;
    }

    //权限
    public function acl()
    {
        $action = $this->getRequest()->getActionName();
        $except_actions = array(
            'index',
            'add',
            'to-add',
            'edit',
            'del',
        );
        if (in_array($action, $except_actions)) {
            return;
        }
        parent::acl();
    }


    public function _init(){
        //error_reporting(0);
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        // pplist
        $jjh_mg_ppDAO = $this->orm->createDAO('jjh_mg_pp')->get();
        if(!empty($jjh_mg_ppDAO)){
            foreach($jjh_mg_ppDAO as $k => $v){
                $temp_array[$v['pid']] = $v['ppname'];
            }
        }
        if(!empty($jjh_mg_ppDAO)){
            foreach($jjh_mg_ppDAO as $k => $v){
                $_temp_array[$v['pid']]['ppname'] = $v['ppname'];
                $_temp_array[$v['pid']]['ppemail'] = $v['ppemail'];
                $_temp_array[$v['pid']]['ppmobile'] = $v['ppmobile'];
            }
        }
        $this->view->assign("jjh_mg_pp_list_info", $_temp_array);
        $this->view->assign("jjh_mg_pp_list", $temp_array);
        $this->jjh_mg_pp_list = $temp_array;

        //项目名称列表
        $pm_chouzi = $this->orm->createDAO("pm_mg_chouzi");
        $pm_chouzi = $pm_chouzi ->get();
        $this->view->assign("pmlist",$pm_chouzi);
    }
}