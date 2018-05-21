<?php
require_once("BaseController.php");
class Management_ppController extends BaseController {
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
                alert_go("添加成功！", "/management/pp/ppcate");
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
            alert_go("人员类型编辑成功。","/management/pp/ppcate");
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
            echo("location.href='/management/pp/ppcate';");
            echo('</script>');
            exit;
        }else {
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('操作失败');");
            echo("location.href='/management/pp/ppcate';");
            echo('</script>');
            exit;
        }
    }

    public function _init(){
        $this ->dbhelper = new DBHelper();
        $this ->dbhelper ->connect();
        SessionUtil::sessionStart();
        //SessionUtil::checkadmin();
    }


    ////////////////////////////////////////////////////////////////
    /**
     * 联系人子公司列表
     */
    public function ppcompanyAction()
    {
        $pp_id = $_REQUEST['pp_id'];
        $pp_id = (int)$pp_id;
        if(!empty($pp_id))
        {
            $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");
            $jjh_mg_pp_companyDAO ->findPp_id($pp_id);
            $jjh_mg_pp_companyDAO = $jjh_mg_pp_companyDAO ->get();

            $ppinfo = $this->getppbyppid($pp_id);

            if(!empty($ppinfo)){
                $this->view->assign("pp_info",$ppinfo[0]);
            }
            $this->view->assign("company_list",$jjh_mg_pp_companyDAO);
            $this->view->assign("pp_id",$pp_id);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("admin/ppcompany.phtml");
            echo $this->view->render("index/footer.phtml");
        }
    }

    /**
     * 添加联系人子公司信息
     */
    public function addppcompanyAction()
    {
        $pp_id = $_REQUEST['pp_id'];
        $pp_id = (int)$pp_id;
        if(!empty($pp_id)) {
            $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");
            $jjh_mg_pp_companyDAO->findPp_id($pp_id);
            $jjh_mg_pp_companyDAO = $jjh_mg_pp_companyDAO->get();
            $this->view->assign("ppcompany", $jjh_mg_pp_companyDAO);
        }
        $this->view->assign("pp_id", $pp_id);
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("admin/addppcompany.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    /**
     * 编辑联系人子公司信息
     */
    public function editppcompanyAction()
    {
        $id = (int)$_REQUEST['id'];
        if(!empty($id)) {
            $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");
            $jjh_mg_pp_companyDAO->findId($id);
            $jjh_mg_pp_companyDAO = $jjh_mg_pp_companyDAO->get();
            $this->view->assign("ppcompany", $jjh_mg_pp_companyDAO);
        }
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("admin/editppcompany.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function saveppcompanyAction()
    {
        $id = (int)$_REQUEST['id'];
        $pp_id = $_REQUEST['pp_id'];
        $pp_id = (int)$pp_id;
        $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");

        $company_name = HttpUtil::postString("company_name");
        $company_contector = HttpUtil::postString("company_contector");
        $company_cont_style = HttpUtil::postString("company_cont_style");
        if($company_name == "" || $company_contector== "" || $company_cont_style == "")
        {
            alert_back("信息不全，请查看信息的完整性，并重新提交。");
        }

        if(!empty($id)) {
            $jjh_mg_pp_companyDAO->findid($id);
        }
        $jjh_mg_pp_companyDAO ->pp_id = $pp_id;
        $jjh_mg_pp_companyDAO ->company_name = $company_name;
        $jjh_mg_pp_companyDAO ->company_contector = $company_contector;
        $jjh_mg_pp_companyDAO ->company_cont_style = $company_cont_style;

        $jjh_mg_pp_companyDAO ->save();
        alert_go("子公司信息添加成功。","ppcompany?pp_id=".$pp_id);
    }

    public function delppcompanyAction()
    {
        $id = (int)$_REQUEST['id'];
        $pp_id = (int)$_REQUEST['pp_id'];
        if(empty($id)) {
            alert_back("操作失败。");
            $this->_redirect("/management/admin/ppcompany?pp_id=".$pp_id);
        }
        $jjh_mg_pp_companyDAO = $this->orm->createDAO("jjh_mg_pp_company");
        $jjh_mg_pp_companyDAO->findid($id);
        $jjh_mg_pp_companyDAO->delete();

        $this->_redirect("/management/pp/ppcompany?pp_id=".$pp_id);
    }

    //////////////////////////////////////////////////////////////////////////
    //联系人管理
    public function addppAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("admin/addpp.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addrsppAction(){
        if($_REQUEST['ppname'] != ""){
            $ppinfo = $this->orm->createDAO("jjh_mg_pp");
            $ppinfo ->ppname = $_REQUEST['ppname'];
            $ppinfo ->pp_address = $_REQUEST['pp_address'];
            $ppinfo ->pp_beizhu = $_REQUEST['pp_beizhu'];
            $ppinfo ->pp_cate = $_REQUEST['pp_cate'];
            $ppinfo ->pp_msn = $_REQUEST['pp_msn'];
            $ppinfo ->pp_pm_id = $_REQUEST['pp_pm_id'];

            $ppinfo ->pp_jzf_cate = $_REQUEST['pp_jzf_cate'];
            $ppinfo ->pp_jzf_attr1 = $_REQUEST['pp_jzf_attr1'];
            $ppinfo ->pp_jzf_attr2 = $_REQUEST['pp_jzf_attr2'];
            $ppinfo ->pp_syf_cate = $_REQUEST['pp_syf_cate'];
            $ppinfo ->pp_yuf_cate = $_REQUEST['pp_yuf_cate'];

            for($i=1; $i<=11; $i++){
                if($_FILES['pp_card'.$i]['name']!=""){
                    if($_FILES['pp_card'.$i]['error'] != 4){
                        if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
                            mkdir(__UPLOADPICPATH__ ."jjh_download/");
                        }
                        $rename = date("YmdHis".time()).rand('9999','100000000');
                        $uploadpic = new uploadPic($_FILES['pp_card'.$i]['name'],$_FILES['pp_card'.$i]['error'],$_FILES['pp_card'.$i]['size'],$_FILES['pp_card'.$i]['tmp_name'],$_FILES['pp_card'.$i]['type'],2,$rename);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__."card/" ;
                        $result = $uploadpic->uploadPic();
                        if($result['error']!=0){
                            echo "<script>alert('".$result['msg']."');";
                            echo "window.location.href='/management/pp/pp';";
                            echo "</script>";
                            exit();
                        }else{
                            $string = 'pp_card'.$i;
                            //$string1 = 'meeting_files_name'.$i;
                            $ppinfo->$string =  __GETPICPATH__."card/".$result['picname'];
                            //$ppinfo->$string1 = $_FILES['card'.$i]['name'];
                        }
                    }
                }
            }

            $ppinfo ->pp_qq = $_REQUEST['pp_qq'];
            $ppinfo ->ppemail = $_REQUEST['ppemail'];
            $ppinfo ->ppmobile = $_REQUEST['ppmobile'];
            $ppinfo ->ppphone = $_REQUEST['ppphone'];

            $ppinfo->save($this->dbhelper);
            // alert_go("联系人添加成功。","/management/admin/pp?pp_cate=".$_REQUEST['pp_cate']);
            $this->alert_go("联系人添加成功。","/management/pp/pp");
        }else {
            $this->alert_back("添加失败");
        }
    }

    public function editppAction(){
        if($_REQUEST['id'] != ""){
            $ppinfo = new jjh_mg_ppDAO($_REQUEST['id']);
            $ppinfo = $ppinfo->get($this->dbhelper);

            $meeting_pp_companyDAO = $this->orm->createDAO('jjh_mg_pp_company');
            $meeting_pp_companyDAO ->findPp_id($ppinfo[0]['pid']);
            $meeting_pp_companyDAO = $meeting_pp_companyDAO->get();

            $this->view->assign("pp_company_list",$meeting_pp_companyDAO);
            $this->view->assign("ppinfo",$ppinfo);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("admin/editpp.phtml");
            echo $this->view->render("index/footer.phtml");
        }else {
            alert_back("操作失败");
        }
    }

    public function editrsppAction(){
        if($_REQUEST['ppname'] != "" && $_REQUEST['pid']){
            $ppinfo = $this->orm->createDAO("jjh_mg_pp");
            $ppinfo ->findPid($_REQUEST['pid']);
            $ppinfo ->ppname = $_REQUEST['ppname'];
            $ppinfo ->pp_address = $_REQUEST['pp_address'];
            $ppinfo ->pp_beizhu = $_REQUEST['pp_beizhu'];
            $ppinfo ->pp_cate = $_REQUEST['pp_cate'];

            $ppinfo ->pp_jzf_cate = $_REQUEST['pp_jzf_cate'];
            $ppinfo ->pp_jzf_attr1 = $_REQUEST['pp_jzf_attr1'];
            $ppinfo ->pp_jzf_attr2 = $_REQUEST['pp_jzf_attr2'];
            $ppinfo ->pp_syf_cate = $_REQUEST['pp_syf_cate'];
            $ppinfo ->pp_yuf_cate = $_REQUEST['pp_yuf_cate'];

            $ppinfo ->pp_msn = $_REQUEST['pp_msn'];
            $ppinfo ->pp_pm_id = $_REQUEST['pp_pm_id'];
            $ppinfo ->pp_qq = $_REQUEST['pp_qq'];
            $ppinfo ->ppemail = $_REQUEST['ppemail'];
            $ppinfo ->ppmobile = $_REQUEST['ppmobile'];
            $ppinfo ->ppphone = $_REQUEST['ppphone'];

            for($i=1; $i<=11; $i++){
                if($_FILES['pp_card'.$i]['name']!=""){
                    if($_FILES['pp_card'.$i]['error'] != 4){
                        if(!is_dir(__UPLOADPICPATH__ ."jjh_download/")){
                            mkdir(__UPLOADPICPATH__ ."jjh_download/");
                        }
                        $rename = date("YmdHis".time()).rand('1','100000');
                        $uploadpic = new uploadPic($_FILES['pp_card'.$i]['name'],$_FILES['pp_card'.$i]['error'],$_FILES['pp_card'.$i]['size'],$_FILES['pp_card'.$i]['tmp_name'],$_FILES['pp_card'.$i]['type'],2,$rename);
                        $uploadpic->FILE_PATH = __UPLOADPICPATH__."card/" ;
                        $result = $uploadpic->uploadPic();
                        if($result['error']!=0){
                            echo "<script>alert('".$result['msg']."');";
                            echo "window.location.href='/management/pp/pp';";
                            echo "</script>";
                            exit();
                        }else{
                            $string = 'pp_card'.$i;
                            //$string1 = 'meeting_files_name'.$i;
                            $ppinfo->$string =  __GETPICPATH__."card/".$result['picname'];
                            //$ppinfo->$string1 = $_FILES['card'.$i]['name'];
                        }
                    }
                }
            }

            $ppinfo ->save($this->dbhelper);
            // alert_go("编辑成功。","/management/admin/pp?pp_cate=".$_REQUEST['pp_cate']);
            $this->alert_go("编辑成功。","/management/pp/pp");
        }else {
            $this->alert_back("添加失败");
        }
    }

    /**
     * 捐赠人列表
     */
    public function ppAction(){
        $ppinfo = $this->orm->createDAO("jjh_mg_pp");
        if($_REQUEST['ppname'] != ""){
            $ppinfo->selectLimit .= " and ppname like '%".$_REQUEST['ppname']."%'";
        }
        if($_REQUEST['pp_address'] != ""){
            $ppinfo->selectLimit .= " and pp_address like '%".$_REQUEST['pp_address']."%'";
        }
        if($_REQUEST['pname'] != ""){
            $ppinfo->selectLimit .= " and pp_pm_id = '".$_REQUEST['pname']."'";
        }
        if($_REQUEST['pp_cate'] != ""){
            $ppinfo->selectLimit .= " and pp_cate like '%".$_REQUEST['pp_cate']."%'";
        }

        $ppinfo->selectLimit .= " order by pid DESC";
        $ppinfo = $ppinfo->get($this->dbhelper);

        $total = count($ppinfo);
        $pageDAO = new pageDAO();
        $pageDAO = $pageDAO ->pageHelper($ppinfo,null,"pp",null,'get',20,8);
        $pages = $pageDAO['pageLink']['all'];
        $pages = str_replace("/index.php","",$pages);
        $this->view->assign('pplist',$pageDAO['pageData']);
        $this->view->assign('page',$pages);
        $this->view->assign('total',$total);
        $this->view->assign('pname',$_REQUEST['pname']);
        $this->view->assign('ppname',$_REQUEST['ppname']);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("admin/pp.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function ppinfoAction(){
        if($_REQUEST['id'] != ""){
            $ppinfo = $this->orm->createDAO("jjh_mg_pp");
            $ppinfo ->findPid($_REQUEST['id']);
            $ppinfo = $ppinfo->get($this->dbhelper);

            $meeting_pp_companyDAO = $this->orm->createDAO('jjh_mg_pp_company');
            $meeting_pp_companyDAO ->findPp_id($ppinfo[0]['pid']);
            $meeting_pp_companyDAO = $meeting_pp_companyDAO->get();

            $this->view->assign("pp_company_list",$meeting_pp_companyDAO);
            $this->view->assign("ppinfo",$ppinfo);

            //项目捐赠方
            $pm_ppDAO = $this ->orm->createDAO("pm_mg_info");
            $pm_ppDAO ->joinTable(" left join pm_mg_chouzi as c on pm_mg_info.pm_name=c.pname");
            $pm_ppDAO ->selectField(" c.id, distinct c.panme");
            $pm_ppDAO ->selectLimit .= ' AND pm_mg_info.pm_pp ='.$ppinfo['ppname'];
            $pm_ppDAO = $pm_ppDAO->get();
            $this->view->assign("pm_list",$pm_ppDAO);

            //参加学校活动
            $meetingDAO = $this->orm->createDAO('jjh_meeting');
            $meetingDAO ->selectLimit .= ' AND find_in_set('.$_REQUEST['id'].',meeting_joiner)';
            $meetingDAO = $meetingDAO->get();
            $this->view->assign("meeting_list",$meetingDAO);

            $_meeting_cate = array();
            $_meeting_cate = array('理事会会议','理事长会议','工作推动会','工作例会','捐赠仪式','其他单位来访交流','捐赠人交流会','学习交流会');
            $this->view->assign('meeting_cate',$_meeting_cate);


            //拜访捐赠人 － 回馈
            $feedbackDAO = $this->orm->createDAO('pm_mg_feedback');
            $feedbackDAO ->selectLimit .= ' AND (FIND_IN_SET('.$_REQUEST['id'].',feedbacker) OR FIND_IN_SET('.$_REQUEST['id'].',jbr))';
            //$feedbackDAO ->selectLimit .= ' OR FIND_IN_SET('.$_REQUEST['id'].',jbr)';
            $feedbackDAO = $feedbackDAO->get();
            $this->view->assign("feedback_list",$feedbackDAO);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("admin/ppinfo.phtml");
            echo $this->view->render("index/footer.phtml");
        }else {
            alert_back("操作失败");
        }
    }

    public function getppbyppid($pp_id)
    {
        if(!empty($pp_id)){
            $jjh_mg_ppDAO = $this->orm->createDAO("jjh_mg_pp");
            $jjh_mg_ppDAO->findPid($pp_id);
            $jjh_mg_ppDAO = $jjh_mg_ppDAO->get();
            return $jjh_mg_ppDAO;
        }else {
            return false;
        }
    }

    //权限
    public function acl()
    {
        $action = $this->getRequest()->getActionName();
        $except_actions = array(
            'addrsppcate',
            'editrsppcate',
            'saveppcompany',
            'addrspp',
            'editrspp',
            'ppinfo',
        );
        if (in_array($action, $except_actions)) {
            return;
        }
        parent::acl();
    }
}