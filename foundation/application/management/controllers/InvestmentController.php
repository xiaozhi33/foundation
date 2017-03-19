<?php
require_once("BaseController.php");
class Management_investmentController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $accountDAO = $this->orm->createDAO('pm_mg_investment_account');
        $account_name = HttpUtil::getString("account_name");
        if(!empty($account_name)){
            $accountDAO->selectLimit .= " AND account_name like '%".$account_name."%'";
        }
        $accountDAO = $accountDAO->order('id DESC');
        $accountDAO->getPager(array('path'=>'/management/investment/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investment/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addaccountAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investment/addaccount.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddaccountAction(){
        $id = $_REQUEST['id'];
        $accountDAO = $this->orm->createDAO('pm_mg_investment_account');
        $account_name = HttpUtil::postString("account_name");
        $account_number = HttpUtil::postString("account_number");

        if($account_name == ''){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $account1DAO = $this->orm->createDAO('pm_mg_investment_account');
        $account1DAO = $account1DAO ->findId($id)->get();

        if($account1DAO[0]['account_name'] != $account_name){
            $hasAccount = $this->hasAccount($account_name);
        }

        if(empty($id)) {
            if ($hasAccount) {
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('该投资账户已添加，请核对后重新添加！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }
        }else {
            if ($hasAccount) {
                echo json_encode(array('msg' => "该投资账户已添加，请核对后重新添加！！！！！！", 'return_url' => '/management/investment/'));
                exit;
            }
        }

        $accountDAO ->account_name = $account_name;
        $accountDAO ->account_number = $account_number;

        if(!empty($id))  //修改流程
        {
            $accountDAO ->findId($id);
        }
        try{
            $accountDAO ->save();
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
            echo("location.href='/management/investment';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/investment/'));
            exit;
        }
    }

    public function editaccountAction(){
        $id = HttpUtil::getString("id");
        $accountDAO = $this->orm->createDAO('pm_mg_investment_account');
        $accountDAO ->findId($id);
        $accountDAO = $accountDAO ->get();

        if($accountDAO != "")
        {
            $this->view->assign("account_info", $accountDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("investment/editaccount.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $accountDAO = $this->orm->createDAO('pm_mg_investment_account')->order('id DESC');

        $this->view->assign("account_info", $accountDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investment/editaccount.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delaccountAction(){
        $id = HttpUtil::getString("id");
        $accountDAO = $this->orm->createDAO('pm_mg_investment_account');
        $accountDAO ->findId($id);
        $accountDAO = $accountDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/investment';");
        echo('</script>');
        exit;
    }

    /**
     * check是否已经存在投资账户信息
     */
    public function hasAccount($account_name){
        $accountDAO = $this->orm->createDAO('pm_mg_investment_account');
        $accountDAO ->findAccount_name($account_name);
        $accountDAO = $accountDAO->get();
        if(!empty($accountDAO)){
            return true;
        }else {
            return false;
        }
    }

    // ================================历史投资明细=======================================================

    public function logAction()
    {
        $logDAO = $this->orm->createDAO('pm_mg_investment_account_logs');
        $investment_account_id = HttpUtil::postString("investment_account_id");
        if(!empty($investment_account_id)){
            $logDAO->findInvestment_account_id($investment_account_id);
        }
        $logDAO = $logDAO->order('id DESC');
        $logDAO->getPager(array('path'=>'/management/investment/log'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investment/log.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addlogAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investment/addlog.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddlogAction(){
        $id = $_REQUEST['id'];
        $logDAO = $this->orm->createDAO('pm_mg_investment_account_logs');

        $investment_account_id = HttpUtil::postString("investment_account_id");         // 投资账户id
        $account_expenses_datetime = HttpUtil::postString("account_expenses_datetime"); // 支出日期
        $account_receipts_datetime = HttpUtil::postString("account_receipts_datetime"); // 收入时间
        $account_receipts = HttpUtil::postString("account_receipts");                   // 收入
        $account_expenses = HttpUtil::postString("account_expenses");                   // 支出
        $account_corpus = HttpUtil::postString("account_corpus");   // 本金-可以先不填
        $remark = HttpUtil::postString("remark");

        if($investment_account_id == ''){
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

        $logDAO ->investment_account_id = $investment_account_id;
        $logDAO ->account_expenses_datetime = $account_expenses_datetime;
        $logDAO ->account_receipts_datetime = $account_receipts_datetime;
        $logDAO ->account_receipts = $account_receipts;
        $logDAO ->account_expenses = $account_expenses;
        $logDAO ->account_corpus = $account_corpus;
        $logDAO ->remark = $remark;
        if(!empty($id))  //修改流程
        {
            $logDAO ->findId($id);
        }
        try{
            $logDAO ->save();
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
            echo("location.href='/management/investment/log';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/investment/log'));
            exit;
        }
    }

    public function editlogAction(){
        $id = HttpUtil::getString("id");
        $logDAO = $this->orm->createDAO('pm_mg_investment_account_logs');
        $logDAO ->findId($id);
        $logDAO = $logDAO ->get();

        if($logDAO != "")
        {
            $this->view->assign("log_info", $logDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("investment/editlog.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $logDAO = $this->orm->createDAO('pm_mg_investment_account_logs')->order('id DESC');

        $this->view->assign("log_info", $logDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investment/editlog.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function dellogAction(){
        $id = HttpUtil::getString("id");
        $logDAO = $this->orm->createDAO('pm_mg_investment_account_logs');
        $logDAO ->findId($id);
        $logDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/investment/log';");
        echo('</script>');
        exit;
    }

    public function _init(){
        error_reporting(0);
        $accountList = $this->orm->createDAO('pm_mg_investment_account')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $this->view->assign(array(
            'accountList' => $accountList
        ));
    }

    //权限
    public function acl()
    {
        $action = $this->getRequest()->getActionName();
        $except_actions = array(
            'to-addaccount',
            'has-addaccount',
            'to-addlog',
        );
        if (in_array($action, $except_actions)) {
            return;
        }
        parent::acl();
    }
}