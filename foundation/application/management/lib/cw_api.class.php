<?php
    /**
     * Created by PhpStorm.
     * User: zhangwangnan
     * Date: 2016/4/21
     * Time: 15:09
     */
    require_once ("mssql_db.class.php");
    class CW_API extends msSQL{
        public function addpm()
        {
            $select_zw_xm = "INSERT INTO ";
            $this->mssql_class->connect();
            $zwxmzd_list = $this->mssql_class->query($select_zw_xm);
            $this->mssql_class->free();
        }

        public function addddepartment()
        {
            $select_zw_xm = "SELECT xmnm,bmbh,xmbh,xmmc,fzr,fzrbh FROM zwxmzd";
            $this->mssql_class->connect();
            $zwxmzd_list = $this->mssql_class->query($select_zw_xm);
            $this->mssql_class->free();
        }

        public function addzrl()
        {

        }

        public function sync_pm()
        {

        }

        public function sync_department()
        {

        }
        public function sync_zrl(){

        }


    }