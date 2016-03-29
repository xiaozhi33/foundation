<?php
require_once("BaseController.php");
require_once("./../../configs_db_mssql.php");
class Management_linkController extends BaseController
{
    private $dbhelper;
    public function indexAction()
    {

    }
    public function _init(){
        error_reporting("E_ALL");
    }
}