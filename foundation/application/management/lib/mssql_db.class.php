<?php
    require_once 'configs_db_mssql.php';
    class mssql_db_lib
    {
        private  $conn;
        private  $DB_helper;
        private function __contruct()
        {
            $this->conn = mssql_connect(__HOST__,__ROOT__,__PASSWD__) or die ("connect failed");
            $this->DB_helper = mssql_select_db(__DBNAME__, $this->conn);
        }

        public function selectDB($sql = '',$type = 'all')
        {
            if(!empty($sql))
            {
                if($type == "all"){
                    mssql_query($sql);
                    $row = mssql_query($sql);
                    return mssql_fetch_array($row);
                }
            }else {
                return array();
            }
        }
        public function saveDB()
        {

        }
    }
?>