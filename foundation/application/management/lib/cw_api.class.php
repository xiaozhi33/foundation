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
            $this->connect();
            $zwxmzd_list = $this->query($select_zw_xm);
            $this->free();
        }

        public function addddepartment()
        {
            $select_zw_xm = "SELECT xmnm,bmbh,xmbh,xmmc,fzr,fzrbh FROM zwxmzd";
            $this->connect();
            $zwxmzd_list = $this->query($select_zw_xm);
            $this->free();
        }

        /**
         * 获取来款管理最新数据
         */
        public function getlkgl(){
            $select_SQL = "SELECT * FROM zw_lkgl ORDER BY lsh DESC";
            $this->connect();
            $query = $this->query($select_SQL);

            while($row=mssql_fetch_array($query))
            {
                $zw_lkgl[] = $row;
            }

            $this->free();
            return $zw_lkgl;
        }

        /**
         * 添加财务认领
         * @param $lkrl_array
         */
        public function addlkrl($lsh, $rlxh, $rlrq, $rlr, $rlrbh, $bmbh, $xmbh, $rlje, $ispz, $rlpznm, $czy)
        {
            try{
                $insert_SQL = "INSERT INTO zw_lkrl (lsh, rlxh, rlrq, rlr, rlrbh, bmbh, xmbh, rlje, ispz, rlpznm, czy) VALUES('$lsh', '$rlxh', '$rlrq', '$rlr', '$rlrbh', '$bmbh', '$xmbh', '$rlje', '$ispz', '$rlpznm', '$czy')";
                alert_back($insert_SQL);exit();
                $this->connect();
                $rs = $this->query($insert_SQL);
                $this ->free();
                return $rs;
            }catch (Exception $e){
                throw $e;
            }
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