<?php
require_once("BaseController.php");
class Test_userController extends BaseController
{
    /**
     * @用户首页 - 任务一览，消息一览
     */
    public function indexAction(){
        try{
            $uid = $this->admininfo['admin_info']['id'];
            // 发起的任务
            $task_from = $this->orm->createDAO('jjh_mg_task')->findSponsor($uid)->get();

            // 协助的任务
            $task_helper = $this->orm->createDAO('jjh_mg_task');
            $task_helper->selectLimit .= " AND find_in_set('".$uid."',helper)";
            $task_helper = $task_helper->get();

            // 需执行的任务
            $task_to = $this->orm->createDAO('jjh_mg_task')->findExecutor($uid)->get();

            // 和我有关的项目 jjh_mg_chouzi fzr

            // 我上传的文档 jjh_mg_files

            // 我参加的活动 pm_mg_info_active

            // 我的代办事宜  pm_mg_todolist

            // 我的回馈 pm_mg_feedback


            $this->view->assign(array(
                'task_from' => $task_from,
                'task_helper' => $task_helper,
                'task_to' => $task_to,
            ));
        }catch (Exception $e){
            throw $e;
        }
    }
    public function _init(){
        //error_reporting(0);
        SessionUtil::sessionStart();
        SessionUtil::checkmanagement();
    }
}