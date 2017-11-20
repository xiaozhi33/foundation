<?php
require_once("BaseController.php");
class Management_investmentproductController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $productDAO = $this->orm->createDAO('pm_mg_investment_product');
        $product_name = HttpUtil::getString("product_name");
        if(!empty($product_name)){
            $productDAO->selectLimit .= " AND product_name like '%".$product_name."%'";
        }
        $investment_account_id = HttpUtil::postString("id");
        if(!empty($investment_account_id)){
            $productDAO->findInvestment_account_id($investment_account_id);
        }

        $productDAO = $productDAO->order('id DESC');
        $productDAO->getPager(array('path'=>'/management/investmentproduct/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investmentproduct/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addproductAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investmentproduct/addproduct.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddproductAction(){
        $id = $_REQUEST['id'];
        $productDAO = $this->orm->createDAO('pm_mg_investment_product');
        $product_name = HttpUtil::postString("product_name");
        $product_number = HttpUtil::postString("product_number");

        $investment_id = HttpUtil::postString("iid");
        $productDAO ->investment_account_id = $investment_id;

        if($product_name == '' || $investment_id == ''){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $account1DAO = $this->orm->createDAO('pm_mg_investment_product');
        $account1DAO = $account1DAO ->findId($id)->get();

        if($account1DAO[0]['product_name'] == $product_name){
            $hasAccount = false;
        }else {
            $hasAccount = true;
        }

        if(empty($id)) {
            if ($hasproduct) {
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('该投资账户已添加，请核对后重新添加！！！！！');");
                echo('history.back();');
                echo('</script>');
                exit;
            }
        }else {
            if ($hasproduct) {
                echo json_encode(array('msg' => "该投资账户已添加，请核对后重新添加！！！！！！", 'return_url' => '/management/investmentproduct/'));
                exit;
            }
        }

        $productDAO ->product_name = $product_name;
        $productDAO ->product_number = $product_number;

        if(!empty($id))  //修改流程
        {
            $productDAO ->findId($id);
        }
        try{
            $productDAO ->save();
        }catch (Exception $e){
            //echo $e;exit;
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
            echo("location.href='/management/investmentproduct?id=".$investment_id."';");
            echo('</script>');
            exit;
        }else {
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存成功！！！！！');");
            echo("location.href='/management/investmentproduct?id=".$investment_id."'");
            echo('</script>');
            exit;
            //echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/investmentproduct?id=".$investment_id."'));
            //exit;
        }
    }

    public function editproductAction(){
        $id = HttpUtil::getString("id");
        $productDAO = $this->orm->createDAO('pm_mg_investment_product');
        $productDAO ->findId($id);
        $productDAO = $productDAO ->get();

        if($productDAO != "")
        {
            $this->view->assign("product_info", $productDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("investmentproduct/editproduct.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $productDAO = $this->orm->createDAO('pm_mg_investment_product')->order('id DESC');

        $this->view->assign("product_info", $productDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investmentproduct/editproduct.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delproductAction(){
        $id = HttpUtil::getString("id");
        $productDAO = $this->orm->createDAO('pm_mg_investment_product');
        $productDAO ->findId($id);
        $productDAO = $productDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/investmentproduct';");
        echo('</script>');
        exit;
    }

    /**
     * check是否已经存在投资账户信息
     */
    public function hasproduct($product_name){
        $productDAO = $this->orm->createDAO('pm_mg_investment_product');
        $productDAO ->findproduct_name($product_name);
        $productDAO = $productDAO->get();
        if(!empty($productDAO)){
            return true;
        }else {
            return false;
        }
    }

    // ================================历史投资明细=======================================================

    public function logAction()
    {
        $logDAO = $this->orm->createDAO('pm_mg_investment_product_logs');
        $investment_product_id = HttpUtil::getString("id");
        if(!empty($investment_product_id)){
            $logDAO->findInvestment_product_id($investment_product_id);
        }
        $logDAO = $logDAO->order('id DESC');
        $logDAO->getPager(array('path'=>'/management/investmentproduct/log'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investmentproduct/log.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addlogAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investmentproduct/addlog.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddlogAction(){
        $id = $_REQUEST['id'];
        $logDAO = $this->orm->createDAO('pm_mg_investment_product_logs');

        $investment_product_id = HttpUtil::postString("investment_product_id");         // 投资账户id
        $account_re_datetime = HttpUtil::postString("account_re_datetime");             // 收支操作时间
        $account_receipts = HttpUtil::postString("account_receipts");                   // 收入
        $account_expenses = HttpUtil::postString("account_expenses");                   // 支出
        $end_datetime = HttpUtil::postString("end_datetime");                           // 到期时间 （投资）
        $rate_of_return = HttpUtil::postString("rate_of_return");                       // 收益率
        $account_corpus = HttpUtil::postString("account_corpus");   // 本金-可以先不填
        $remark = HttpUtil::postString("remark");

        if($investment_product_id == ''){
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

        $logDAO ->investment_product_id = $investment_product_id;
        $logDAO ->account_re_datetime = $account_re_datetime;
        $logDAO ->end_datetime = $end_datetime;
        $logDAO ->account_receipts = $account_receipts;
        $logDAO ->account_expenses = $account_expenses;
        $logDAO ->account_corpus = $account_corpus;
        $logDAO ->rate_of_return = $rate_of_return;
        $logDAO ->end_datetime = $end_datetime;
        $logDAO ->remark = $remark;
        $logDAO ->type =  HttpUtil::postString("type");
        if(!empty($id))  //修改流程
        {
            $logDAO ->findId($id);
        }
        try{
            $logDAO ->save();
        }catch (Exception $e){
            //echo $e;exit();
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
            echo("location.href='/management/investmentproduct/log';");
            echo('</script>');
            exit;
        }else {
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存成功');");
            echo("location.href='/management/investmentproduct/log';");
            echo('</script>');
            exit;
            //echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/investmentproduct/log'));
            //exit;
        }
    }

    public function editlogAction(){
        $id = HttpUtil::getString("id");
        $logDAO = $this->orm->createDAO('pm_mg_investment_product_logs');
        $logDAO ->findId($id);
        $logDAO = $logDAO ->get();

        if($logDAO != "")
        {
            $this->view->assign("log_info", $logDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("investmentproduct/editlog.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $logDAO = $this->orm->createDAO('pm_mg_investment_product_logs')->order('id DESC');

        $this->view->assign("log_info", $logDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("investmentproduct/editlog.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function dellogAction(){
        $id = HttpUtil::getString("id");
        $logDAO = $this->orm->createDAO('pm_mg_investment_product_logs');
        $logDAO ->findId($id);
        $logDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/investmentproduct/log';");
        echo('</script>');
        exit;
    }

    public function _init(){
        error_reporting(0);
        $productList = $this->orm->createDAO('pm_mg_investment_product')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        // 账户
        $pm_mg_investment_account_list = $this->orm->createDAO("pm_mg_investment_account")->get();
        $this->view->assign("pm_mg_investment_account_list", $pm_mg_investment_account_list);

        // 账户
        $pm_mg_investment_product_list = $this->orm->createDAO("pm_mg_investment_product")->get();
        $this->view->assign("pm_mg_investment_product_list", $pm_mg_investment_product_list);

        $this->view->assign(array(
            'productList' => $productList
        ));
    }

    //权限
    public function acl()
    {
        $action = $this->getRequest()->getActionName();
        $except_actions = array(
            'to-addproduct',
            'hasproduct',
            'to-addlog',
        );
        if (in_array($action, $except_actions)) {
            return;
        }
        parent::acl();
    }
}