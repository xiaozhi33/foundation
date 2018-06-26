<?php
require_once("BaseController.php");
class Management_supportController extends BaseController {
    private $dbhelper;
    public function indexAction(){
        $userDAO = $this->orm->createDAO("_support_college_user");
        if($_REQUEST['username'] != ""){
            $userDAO->selectLimit .= " and username like '%".$_REQUEST['username']."%'";
        }
        if($_REQUEST['department_id'] != ""){
            $userDAO->selectLimit .= " and department_id =".$_REQUEST['department_id'];
        }
        if($_REQUEST['mobile'] != ""){
            $userDAO->selectLimit .= " and mobile like '%".$_REQUEST['mobile']."%'";
        }
        if($_REQUEST['email'] != ""){
            $userDAO->selectLimit .= " and email like '%".$_REQUEST['email']."%'";
        }
        if($_REQUEST['wechat'] != ""){
            $userDAO->selectLimit .= " and wechat like '%".$_REQUEST['wechat']."%'";
        }
        if($_REQUEST['address'] != ""){
            $userDAO->selectLimit .= " and address like '%".$_REQUEST['address']."%'";
        }

        $userDAO->selectLimit .= " order by id DESC";
        $userDAO = $userDAO->get();

        $total = count($userDAO);
        $pageDAO = new pageDAO();
        $pageDAO = $pageDAO ->pageHelper($userDAO,null,"index",null,'get',20,8);
        $pages = $pageDAO['pageLink']['all'];
        $pages = str_replace("/index.php","",$pages);

        $this->view->assign('userlist',$pageDAO['pageData']);
        $this->view->assign('page',$pages);
        $this->view->assign('total',$total);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("support/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("support/add.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function editAction(){
        if($_REQUEST['id'] != ""){
            $userinfo = $this->orm->createDAO("_support_college_user")->findId($_REQUEST['id']);
            $userinfo = $userinfo->get();
            $this->view->assign('userinfo',$userinfo);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("support/edit.phtml");
            echo $this->view->render("index/footer.phtml");
        }else {
            alert_back("操作失败");
        }
    }

    public function toaddAction(){
        try{
            $userinfo = $this->orm->createDAO("_support_college_user");
            if(!empty($_POST['id'])){
                $userinfo = $userinfo->findId($_REQUEST['id']);
            }else{
                // 用户是否存在
                $_userinfo = $this->orm->createDAO("_support_college_user")->findUsername($_REQUEST['username'])->get();
                if(!empty($_userinfo)){
                    $this->alert_back("该用户已经存在，保存失败！");
                }

                if(empty($_REQUEST['password'])){
                    $this->alert_back("信息不完整，请核对后重新提交！");
                }
            }

            if(empty($_REQUEST['username']) || empty($_REQUEST['department_id'])  || empty($_REQUEST['email'])){
                $this->alert_back("信息不完整，请核对后重新提交！");
            }

            $userinfo -> username = $_REQUEST['username'];
            $userinfo -> password = substr(md5(serialize($_REQUEST['password'])), 0, 32);
            $userinfo -> department_id = $_REQUEST['department_id'];
            $userinfo -> mobile = $_REQUEST['mobile'];
            $userinfo -> email = $_REQUEST['email'];
            $userinfo -> wechat = $_REQUEST['wechat'];
            $userinfo -> address = $_REQUEST['address'];

            $userinfo ->save();
            $this->alert_go("保存成功！",'/management/support/index');
        }catch(Exception $e){
            $this->alert_back("保存失败！");
        }
    }

    public function delAction(){
        try{
            $userinfo = $this->orm->createDAO("_support_college_user");
            $userinfo = $userinfo->findId($_REQUEST['id']);
            $userinfo ->delete();
            $this->alert_go("删除成功！",'/management/support/index');
        }catch(Exception $e){
            $this->alert_back("删除失败！");
        }
    }

    public function _init(){
        $this ->dbhelper = new DBHelper();
        $this ->dbhelper ->connect();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();
        $this->admininfo = SessionUtil::getAdmininfo();
        $this->view->assign("admininfo",$this->admininfo);

        //项目分类
        $pcatelist = new jjh_mg_cateDAO();
        $pcatelist =  $pcatelist ->get($this->dbhelper);
        $this->view->assign("pcatelist",$pcatelist);

        //所属部门
        $departmentlist = new jjh_mg_departmentDAO();
        $departmentlist = $departmentlist->get($this->dbhelper);
        $this->view->assign("departmentlist",$departmentlist);

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
        $pm_chouzi = new pm_mg_chouziDAO();
        $pm_chouzi ->selectLimit .= " order by id desc";
        $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
        $this->view->assign("pmlist",$pm_chouzi);

        // 项目进度
        $this->view->assign("rate_config",$this->rate_config);

        //获取筹资项目list
        $chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
        $this->view->assign("chouzi_lists",$chouziDAO);
    }


    //权限
    public function acl()
    {
        $action = $this->getRequest()->getActionName();
        $except_actions = array(
            'index',
            'add',
            'edit',
            'toadd',
            'del'
        );
        if (in_array($action, $except_actions)) {
            return;
        }
        parent::acl();
    }
}