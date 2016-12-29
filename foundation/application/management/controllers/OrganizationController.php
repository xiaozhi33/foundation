<?php
require_once("BaseController.php");
class Management_organizationController extends BaseController
{
    private $dbhelper;
    public $org_type_array = array(
        1 => '理事会',
        2 => '校董会',
        3 => '秘书处',
        4 => '公益社区'
    );
    public $org_type_status;

    public function indexAction()
    {
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization');
        $name = HttpUtil::postString("name");
        if(!empty($name)){
            $organizationDAO->findName($name);
        }
        if(!empty($this->org_type_status)){
            $organizationDAO->findOrganization_type($this->org_type_status);
        }
        $organizationDAO = $organizationDAO->order('id DESC');
        $organizationDAO->getPager(array('path'=>'/management/organization/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("organization/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addorganizationmainAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("organization/addorganization.phtml?".$this->org_type_status);
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddorganizationAction(){
        $id = $_REQUEST['id'];
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization');
        $name = HttpUtil::postString("name");

        if($name == ''|| $name == ''){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $organizationDAO ->name = $name;

        if(!empty($id))  //修改流程
        {
            $organizationDAO ->findId($id);
        }
        try{
            $organizationDAO ->save();
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
            echo("location.href='/management/organization?".$this->org_type_status."';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/organization/?'.$this->org_type_status));
            exit;
        }
    }

    public function editorganizationAction(){
        $id = HttpUtil::getString("id");
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization');
        $organizationDAO ->findId($id);
        $organizationDAO = $organizationDAO ->get();

        if($organizationDAO != "")
        {
            $this->view->assign("gift_info", $organizationDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("gift/editorganization.phtml?".$this->org_type_status);
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization')->order('id DESC');

        $this->view->assign("gift_info", $organizationDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("gift/editorganization.phtml?".$this->org_type_status);
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delgiftmainAction(){
        $id = HttpUtil::getString("id");
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization');
        $organizationDAO ->findId($id);
        $organizationDAO = $organizationDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/organization?".$this->org_type_status."';");
        echo('</script>');
        exit;

    }

    /**
     * check是否已经存在
     */
    public function has_is($name){
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization');
        $organizationDAO ->findName($name);
        $organizationDAO = $organizationDAO->get();
        if(!empty($organizationDAO)){
            return true;
        }else {
            return false;
        }
    }

    public function _init(){
        error_reporting(0);
        $orgList = $this->orm->createDAO('jjh_mg_organization')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $this->org_type_status =  HttpUtil::getString("type");
        $this->view->assign(array(
            'orgList' => $orgList,
            'org_type_status' => $this->org_type_status,
            'org_type_array' => $this->org_type_array
        ));
    }
}