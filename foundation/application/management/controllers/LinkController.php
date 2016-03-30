<?php
require_once("BaseController.php");
//require_once("./../../configs_db_mssql.php");
//require_once("../lib/mssql_db.class.php");
class Management_linkController extends BaseController
{
    private $dbhelper;
    public function indexAction()
    {
        try{
            /*header("Content-type: text/html; charset=utf-8");
            $mssql_localhost = 'egServer70';
            $mssql_rootname = 'tc_byjjh_zjk';
            $mssql_passwd = 'byjjh_zjk';
            $mssql_dbname = 'byjjh_zjk';
            $mssql_port = '1433';

            define("__MSSQL_HOST__", $mssql_localhost);
            define("__MSSQL_PORT__", $mssql_port);
            define("__MSSQL_ROOT__", $mssql_rootname);
            define("__MSSQL_PASSWD__", $mssql_passwd);
            define("__MSSQL_DBNAME__", $mssql_dbname);

            $conn = mssql_connect(__MSSQL_HOST__,__MSSQL_ROOT__,__MSSQL_PASSWD__) or die ("connect failed");
            mssql_select_db(__MSSQL_DBNAME__, $conn);
            //mssql_query('SET NAMES \'UTF8\'');

            $query = "select * from zw_lkgl";
            $row = mssql_query($query);

            while($list=mssql_fetch_array($row))
            {
                print_r($list);
                echo "<br>";
            }*/


            /////////////////////////////////
            /*$msdb=mssql_connect("219.243.39.69:1433","tc_byjjh_zjk","byjjh_zjk");
            if (!$msdb) {
                echo "connect sqlserver error";
                exit;
            }
            mssql_select_db("byjjh_zjk",$msdb);
            $result = mssql_query("select * from zw_lkgl", $msdb);
            while($row = mssql_fetch_array($result)) {
                print_r($row);
            }*/

            //////////////////////
            try {
                $hostname = "219.243.39.69";
                $port = 1433;
                $dbname = "byjjh_zjk";
                $username = "tc_byjjh_zjk";
                $pw = "byjjh_zjk";
                $dbh = new PDO ("dblib:host=$hostname:$port;dbname=$dbname","$username","$pw");
            } catch (PDOException $e) {
                echo "Failed to get DB handle: " . $e->getMessage() . "\n";
                exit;
            }

            $stmt = $dbh->prepare("select * from zw_lkgl");
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                print_r($row);
            }
            unset($dbh); unset($stmt);


            @mssql_free_result($result);
            @mssql_close();

            echo "ceshi";exit();
        }catch (Exception $e){
            throw $e;
        }
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
        $this ->dbhelper = new DBHelper();
        $this ->dbhelper ->connect();
        error_reporting("E_ALL");
    }
}