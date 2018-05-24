<?php
require_once("BaseController.php");
class Management_taskController extends BaseController
{
    private $dbhelper;

    public function indexAction()
    {
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        // 标题的模糊匹配
        $title = HttpUtil::getString("title");
        if(!empty($title)){
            $taskDAO->selectLimit .= " AND title like '%".$title."%'";
        }

        $taskDAO ->selectLimit .= ' AND (FIND_IN_SET('.$this->admininfo['id'].',sponsor) OR FIND_IN_SET('.$this->admininfo['id'].',executor) OR FIND_IN_SET('.$this->admininfo['id'].',helper))';


        $taskDAO = $taskDAO->order(' schedule ASC, priority DESC, id DESC');
        $taskDAO->getPager(array('path'=>'/management/task/index'))->assignTo($this->view);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("task/index.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function addtaskmainAction(){
        echo $this->view->render("index/header.phtml");
        echo $this->view->render("task/addtask.phtml");
        echo $this->view->render("index/footer.phtml");
    }

    public function toAddtaskmainAction(){
        $id = $_REQUEST['id'];
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $title = HttpUtil::postString("title");
        $type = HttpUtil::postString("type");
        $sponsor = $this->admininfo['id'];  //发起人
        $executor = HttpUtil::postString("executor");  //执行者 （指派给）
        $helper = implode(',',$_REQUEST["helper"]);   //协助者
        $star_time = HttpUtil::postString("star_time");
        $end_time = HttpUtil::postString("end_time");
        $plan_time = HttpUtil::postString("plan_time");
        $status = HttpUtil::postString("status");
        $priority = HttpUtil::postString("priority");  //优先级
        $schedule = HttpUtil::postString("schedule");  //进度表
        $description = HttpUtil::postString("description");
        //$tixing = HttpUtil::postString("description"); //如果到预定完成时间没有完成，提前多少天提醒。

        if($title == ''|| $executor == ''|| $star_time == ''){
            //alert_back("您输入的信息不完整，请查正后继续添加！！！！！");
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('您输入的信息不完整，请查正后继续添加！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        $taskDAO ->title = $title;
        $taskDAO ->type = $type;
        $taskDAO ->sponsor = $sponsor;
        $taskDAO ->executor = $executor;
        $taskDAO ->helper = $helper;
        $taskDAO ->star_time = $star_time;
        $taskDAO ->end_time = $end_time;
        $taskDAO ->plan_time = $plan_time;
        $taskDAO ->status = $status;
        $taskDAO ->priority = $priority;
        $taskDAO ->schedule = $schedule;
        $taskDAO ->description = $description;

        if(!empty($id))  //修改流程
        {
            $taskDAO ->findId($id);
        }
        try{
            $re_id = $taskDAO ->save();
        }catch (Exception $e){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存失败！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }

        if(empty($id) && $re_id != '')  // 新增流程，添加到log表中
        {
            $task_array = $this->orm->createDAO('jjh_mg_task')->findId($re_id)->get();

            $jjh_mg_task_logDAO = $this->orm->createDAO('jjh_mg_task_log');
            $jjh_mg_task_logDAO ->task_id = $re_id;
            $jjh_mg_task_logDAO ->lastmodify = time();
            $jjh_mg_task_logDAO ->new_info = serialize($task_array);
            $jjh_mg_task_logDAO ->uid = $this->admininfo['id'];
            $jjh_mg_task_logDAO ->save();
        }
        if(empty($id)){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('保存成功');");
            echo("location.href='/management/task';");
            echo('</script>');
            exit;
        }else {
            echo json_encode(array('msg'=>"保存成功！",'return_url'=>'/management/task/'));
            exit;
        }
    }

    public function edittaskmainAction(){
        $id = HttpUtil::getString("id");
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $taskDAO ->findId($id);
        $taskDAO = $taskDAO ->get();

        if($taskDAO != "")
        {
            $this->view->assign("task_info", $taskDAO);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("task/edittask.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }
        $taskDAO = $this->orm->createDAO('jjh_mg_task')->order('id DESC')->get();


        $this->view->assign("task_info", $taskDAO);

        echo $this->view->render("index/header.phtml");
        echo $this->view->render("task/edittask.phtml");
        echo $this->view->render("index/footer.phtml");
        exit();
    }

    public function deltaskmainAction(){
        $id = HttpUtil::getString("id");
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $taskDAO ->findId($id);
        $taskDAO ->delete();

        echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
        echo('<script language="JavaScript">');
        echo("alert('删除成功');");
        echo("location.href='/management/task';");
        echo('</script>');
        exit;

    }

    /**
     * check是否已经存在
     */
    public function has_is($name){
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $taskDAO ->findName($name);
        $taskDAO = $taskDAO->get();
        if(!empty($taskDAO)){
            return true;
        }else {
            return false;
        }
    }

    public function taskinfoAction()
    {
        $id = HttpUtil::getString("id");
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $taskDAO ->findId($id);
        $taskDAO = $taskDAO ->get();

        $task_logDAO = $this->orm->createDAO('jjh_mg_task_log')->findTask_id($id);
        $task_logDAO ->selectLimit .= ' order by lastmodify DESC';
        $task_logDAO = $task_logDAO->get();

        $task_log_array = array();
        foreach($task_logDAO as $k => $v){
            $task_log_array[$k] = $v;
            if($v['old_info'] != ''){
                $task_log_array[$k]['old_info'] = unserialize($v['old_info']); // 旧数据
            }
            if($v['new_info'] != ''){
                $task_log_array[$k]['new_info'] = unserialize($v['new_info']); // 新数据
            }
            $task_log_array[$k]['user_info'] = $this->getadmininfoByidAction($v['uid']);
        }

        if($taskDAO != "")
        {
            $this->view->assign("task_info", $taskDAO);
            $this->view->assign("task_log_array", $task_log_array);
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("task/taskinfo.phtml");
            echo $this->view->render("index/footer.phtml");
            exit();
        }else {
            $this->alert_back('您查询的任务不存在！');
        }
    }

    ////////////////////安全校验///////////////////////////////////
    /**
     * 检查用户是否有权限查看，编辑，删除task
     */
    public function checktask($tid)
    {
        $taskDAO = $this->orm->createDAO('jjh_mg_task');
        $taskDAO ->findId($tid);
        $taskDAO ->selectLimit .= ' AND (FIND_IN_SET('.$this->admininfo['id'].',sponsor) OR FIND_IN_SET('.$this->admininfo['id'].',executor) OR FIND_IN_SET('.$this->admininfo['id'].',helper)';
        $taskDAO = $taskDAO ->get();
        if($taskDAO[0]['id'] != ''){
            return true;
        }else {
            return false;
        }
    }

    public function addtasklogAction()
    {
        $id = $_REQUEST['id'];
        // 旧值
        $task_old_array = $this->orm->createDAO('jjh_mg_task')->findId($id)->get();
        $taskDAO = $this->orm->createDAO('jjh_mg_task')->findId($id);

        $executor = HttpUtil::postString("executor");  //执行者 （指派给）
        $helper = implode(',',$_REQUEST["helper"]);   //协助者
        /*$star_time = HttpUtil::postString("star_time");
        $end_time = HttpUtil::postString("end_time");
        $plan_time = HttpUtil::postString("plan_time");
        $status = HttpUtil::postString("status");*/
        $priority = HttpUtil::postString("priority");  //优先级
        $schedule = HttpUtil::postString("schedule");  //进度表
        $description = HttpUtil::postString("description");

        if(empty($description)){
            $this->alert_back('任务变更描述不能为空，请填写描述后再试！');exit();
        }

        $taskDAO ->executor = $executor;
        $taskDAO ->helper = $helper;
        $taskDAO ->priority = $priority;
        $taskDAO ->schedule = $schedule;
        $taskDAO ->description = $description;
        $taskDAO ->save();
        // 新值
        $task_array = $this->orm->createDAO('jjh_mg_task')->findId($id)->get();

        $jjh_mg_task_logDAO = $this->orm->createDAO('jjh_mg_task_log');
        $jjh_mg_task_logDAO ->task_id = $id;
        $jjh_mg_task_logDAO ->lastmodify = time();
        $jjh_mg_task_logDAO ->old_info = serialize($task_old_array);
        $jjh_mg_task_logDAO ->new_info = serialize($task_array);
        $jjh_mg_task_logDAO ->uid = $this->admininfo['id'];

        try{
            $jjh_mg_task_logDAO ->save();
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('更新成功');");
            echo("location.href='/management/task/taskinfo?id=".$id."';");
            echo('</script>');
            exit;
        }catch (Exception $e){
            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('更新失败！！！！！');");
            echo('history.back();');
            echo('</script>');
            exit;
        }
    }

    public function _init(){
        //error_reporting(0);
        $orgList = $this->orm->createDAO('jjh_mg_task')->get();
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();
        $admin_list = $this->orm->createDAO("my_admin")->get();

        $this->view->assign(array(
            'orgList' => $orgList,
            'admin_list' => $admin_list,
        ));
    }

    //权限
    public function acl()
    {
        $action = $this->getRequest()->getActionName();
        $except_actions = array(
            'to-addtaskmain',
            'taskinfo',
            'addtasklog'
        );
        if (in_array($action, $except_actions)) {
            return;
        }
        parent::acl();
    }
}