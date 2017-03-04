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
        $name = $_REQUEST['name'];
        $director_id = $_REQUEST['director_id'];
        if(!empty($name)){
            $organizationDAO->findName($name);
        }
        if(!empty($this->org_type_status)){
            $organizationDAO->findOrganization_type($this->org_type_status);
        }
        if(!empty($director_id)){
            $organizationDAO->findDirector($director_id);
        }
        $organizationDAO = $organizationDAO->order('id DESC');
        $organizationDAO->getPager(array('path'=>'/management/organization/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("organization/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addorganizationmainAction(){
        $director_id = $_REQUEST['director_id'];
        $this->view->assign("director_id", $director_id);
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("organization/addorganization.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddorganizationmainAction(){
        $id = $_REQUEST['id'];
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization');
        $name = HttpUtil::postString("name");
        $sex = HttpUtil::postString("sex");
        $jjh_post = HttpUtil::postString("jjh_post");
        $org_post = HttpUtil::postString("org_post");
        $department = HttpUtil::postString("department");
        $serving_time = HttpUtil::postString("serving_time");
        $tel = HttpUtil::postString("tel");
        $director = $_REQUEST['director'];
        $birthday = HttpUtil::postString("birthday");
        $id_card = HttpUtil::postString("id_card");
        $organization_type = HttpUtil::postString("organization_type");


        if($_FILES['resume']['name']!=""){
            if($_FILES['resume']['error'] != 4){
                if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
                    mkdir(__UPLOADPICPATH__ ."jjh_download/");
                }
                $uploadpic = new uploadPic($_FILES['resume']['name'],$_FILES['resume']['error'],$_FILES['resume']['size'],$_FILES['resume']['tmp_name'],$_FILES['resume']['type'],2);
                $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
                $result = $uploadpic->uploadPic();
                if($result['error']!=0){
                    alert_back_old($result['msg']);
                }else{
                    $organizationDAO->resume =  __GETPICPATH__."jjh_download/".$result['picname'];
                }
            }
        }

        if($_FILES['examination_approval']['name']!=""){
            if($_FILES['examination_approval']['error'] != 4){
                if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
                    mkdir(__UPLOADPICPATH__ ."jjh_download/");
                }
                $uploadpic = new uploadPic($_FILES['examination_approval']['name'],$_FILES['examination_approval']['error'],$_FILES['examination_approval']['size'],$_FILES['examination_approval']['tmp_name'],$_FILES['examination_approval']['type'],2);
                $uploadpic->FILE_PATH = __UPLOADPICPATH__."jjh_download/" ;
                $result = $uploadpic->uploadPic();
                if($result['error']!=0){
                    alert_back_old($result['msg']);
                }else{
                    $organizationDAO->examination_approval =  __GETPICPATH__."jjh_download/".$result['picname'];
                }
            }
        }

        if($name == ''){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $organizationDAO ->name = $name;
        $organizationDAO ->sex = $sex;
        $organizationDAO ->jjh_post = $jjh_post;
        $organizationDAO ->org_post = $org_post;
        $organizationDAO ->department = $department;
        $organizationDAO ->serving_time = $serving_time;
        $organizationDAO ->tel = $tel;
        $organizationDAO ->director = $director;
        $organizationDAO ->birthday = $birthday;
        $organizationDAO ->id_card = $id_card;
        $organizationDAO ->organization_type = $organization_type;

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
            echo("location.href='/management/organization/index?director_id=".$director."';");
            echo('</script>');
            exit;
        }else {
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存成功');");
            echo("location.href='/management/organization/index?director_id=".$director."';");
            echo('</script>');
            exit;
            //echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/organization/?'.$this->org_type_status));
            //exit;
        }
    }

    public function editorganizationAction(){
        $id = HttpUtil::getString("id");
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization');
        $organizationDAO ->findId($id);
        $organizationDAO = $organizationDAO ->get();

        if($organizationDAO != "")
        {
            $this->view->assign("organization_info", $organizationDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("organization/editorganization.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $organizationDAO = $this->orm->createDAO('jjh_mg_organization')->order('id DESC');

        $this->view->assign("organization_info", $organizationDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("organization/editorganization.phtml?".$this->org_type_status);
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function delorganizationAction(){
        $id = HttpUtil::getString("id");
        $organization1DAO = $this->orm->createDAO('jjh_mg_organization');
        $organization1DAO = $organization1DAO ->findId($id)->get();

        $organizationDAO = $this->orm->createDAO('jjh_mg_organization');
        $organizationDAO ->findId($id);
        $organizationDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        if($organization1DAO[0]['director'] ){
            echo("location.href='/management/organization/index?director_id=".$organization1DAO[0]['director']."'");
        }else{
            echo("location.href='/management/organization'");
        }
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
        //error_reporting(E_ALL);
        $orgList = $this->orm->createDAO('jjh_mg_organization')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();

        $jjh_mg_director_list = $this->orm->createDAO('jjh_mg_director')->get();
        if(!empty($jjh_mg_director_list)){
            foreach($jjh_mg_director_list as $key => $value){
                $_jjh_mg_directior_list[$value['id']] = $value['director'];
            }
        }

        $this->org_type_status =  HttpUtil::getString("type");
        $this->view->assign(array(
            'orgList' => $orgList,
            'org_type_status' => $this->org_type_status,
            'org_type_array' => $this->org_type_array,
            'jjh_mg_director_list' => $_jjh_mg_directior_list
        ));
    }
}