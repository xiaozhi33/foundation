<?php
require_once("BaseController.php");
require_once("./../../configs_db_mssql.php");
require_once("../lib/mssql_db.class.php");
class Management_linkController extends BaseController
{
    private $dbhelper;
    public function indexAction()
    {

    }
    public function _init()
    {
        $this->dbhelper = new mssql_db_lib();
        error_reporting("E_ALL");
    }
}