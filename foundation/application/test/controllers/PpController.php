<?php
require_once("BaseController.php");
class Test_ppController extends BaseController {
    private $dbhelper;
    public function ppcateAction(){
        $ppcatelist = $this->orm->createDAO("jjh_mg_pp_cate")->get();
        //$this->view->assign("ppcatelist",$ppcatelist);
        $total = count($ppcatelist);
        $pageDAO = new pageDAO();
        $pageDAO = $pageDAO ->pageHelper($ppcatelist,null,"ppcate",null,'get',20,20);
        $pages = $pageDAO['pageLink']['all'];
        $pages = str_replace("/index.php","",$pages);
        $this->view->assign('ppcatelist',$pageDAO['pageData']);
        $this->view->assign('page',$pages);
        $this->view->assign('total',$total);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render('pp/ppcate.phtml');
        echo $this->view->render("index/footer.phtml");
    }

    public function addppcateAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("pp/addppcate.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addrsppcateAction(){
        try{
            if($_REQUEST['pp_cate_name'] != "" ){
                $ppcateinfo = $this->orm->createDAO("jjh_mg_pp_cate");
                $ppcateinfo ->pp_cate_name = $_REQUEST['pp_cate_name'];
                $pid = $ppcateinfo->save($this->dbhelper);
                alert_go("添加成功！", "/test/pp/ppcate");
            }else {
                alert_back("添加失败！");
            }
        }catch (Exception $e){
            throw $e;
        }
    }

    public function editppcateAction(){
        if($_REQUEST['id'] != ""){
            $ppcateinfo = $this->orm->createDAO("jjh_mg_pp_cate")->findId($_REQUEST['id']);
            $ppcateinfo = $ppcateinfo->get($this->dbhelper);
            $this->view->assign("ppcateinfo",$ppcateinfo);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("pp/editppcate.phtml");
            echo $this->view->render("index/footer.phtml");
        }else {
            alert_back("操作失败");
        }
    }

    public function editrsppcateAction(){
        if($_REQUEST['pp_cate_name'] != "" && $_REQUEST['id']){
            $ppcateinfo = $this->orm->createDAO("jjh_mg_pp_cate")->findId($_REQUEST['id']);
            $ppcateinfo ->pp_cate_name = $_REQUEST['pp_cate_name'];
            $ppcateinfo->save();
            alert_go("人员类型编辑成功。","/test/pp/ppcate");
        }else {
            alert_back("添加失败");
        }
    }

    public function delppcateAction(){
        if($_REQUEST['id'] != ""){
            $this->orm->createDAO("jjh_mg_pp_cate")->findId($_REQUEST['id'])->delete();
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/test/pp/ppcate';");
            echo('</script>');
            exit;
        }else {
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('操作失败');");
            echo("location.href='/test/pp/ppcate';");
            echo('</script>');
            exit;
        }
    }

    public function _init(){
        $this ->dbhelper = new DBHelper();
        $this ->dbhelper ->connect('test');
        SessionUtil::sessionStart();
        SessionUtil::checkadmin();
    }
}