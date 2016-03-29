<?php
require_once("BaseController.php");
require_once("./../../configs_db_mssql.php");
require_once("../lib/mssql_db.class.php");
class Management_linkController extends BaseController
{
    private $dbhelper;
    public function indexAction(){}
    public function _init()
    {
        $this->dbhelper = new mssql_db_lib();
        error_reporting("E_ALL");
    }
    // 项目来款-推送到未认领
    public function laikuan(){}

    // 项目支出-保存到中间库中
    public function zhichu(){}

    // 新项目
    public function new_mg(){}

    // 部门同步
    public function department_sync(){}
}