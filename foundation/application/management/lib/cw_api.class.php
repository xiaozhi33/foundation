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

        /**
         * 获取来款管理最新数据
         */
        public function getlkgl(){
            $select_SQL = "SELECT * FROM zw_lkgl ORDER BY lsh DESC";
            $this->connect();
            $this->query($select_SQL);
            $zw_lkgl = $this->fetch_array();
            $this->free();
            return $zw_lkgl;
        }

        /**
         * 添加财务认领
         * @param $lkrl_array
         */
        public function addlkrl($lsh, $lkrq, $fkdw, $je, $rlje, $lrrq, $lrr, $statusS)
        {
            $insert_SQL = "INSERT INTO lkrl SET()VALUES()";
            $this->mssql_class->connect();
            $rs = $this->mssql_class->query($insert_SQL);
            $this ->mssql_class->free();
            return $rs;
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