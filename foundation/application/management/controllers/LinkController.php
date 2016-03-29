<?php
require_once("BaseController.php");
//require_once("./../../configs_db_mssql.php");
//require_once("../lib/mssql_db.class.php");
class Management_linkController extends BaseController
{
    private $dbhelper;
    public function indexAction()
    {
        echo "ceshi";exit();
    }

    // 项目来款-推送到未认领
    public function laikuanAction(){}

    // 项目支出-保存到中间库中
    public function zhichuAction(){}

    // 新项目
    public function new_mgAction(){}

    // 部门同步
    public function department_syncAction(){}

    public function _init()
    {
        //SessionUtil::sessionStart();  // 登录后台校验
        //SessionUtil::checkmanagement();
        //$this->dbhelper = new mssql_db_lib();
        error_reporting("E_ALL");
    }
}