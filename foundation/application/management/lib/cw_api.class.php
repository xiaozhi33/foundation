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
         * @param $lsh      流水号
         * @param $rlxh     认领序号  项目编号id
         * @param $rlrq     认领日期
         * @param $rlr      认领人
         * @param $rlrbh    认领人编号
         * @param $bmbh     部门编号
         * @param $xmbh     项目编号
         * @param $rlje     认领金额
         * @param $ispz     是否制单
         * @param $rlpznm   认领凭证内码
         * @param $czy      操作员
         * @return mixed
         * @throws Exception
         */
        public function addlkrl($lsh, $rlxh, $rlrq, $rlr, $rlrbh, $bmbh, $xmbh, $rlje, $ispz, $rlpznm, $czy)
        {
            try{
                $insert_SQL = "INSERT INTO zw_lkrl (lsh, rlxh, rlrq, rlr, rlrbh, bmbh, xmbh, rlje, ispz, rlpznm, czy, sm, rlflbh, istl,tlpznm,tlflbh,czrq,kyxmbh,lslsh) VALUES('$lsh', '$rlxh', '$rlrq', '$rlr', '$rlrbh', '$bmbh', '$xmbh', '$rlje', '$ispz', '$rlpznm', '$czy' ,'','','','','','','','')";
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

        public function get_max_departmentID(){
            $selectSQL = 'select top 1 bmbh from zwbmzd where 1=1 order by bmbh DESC';
            $this->connect();
            $query = $this->query($selectSQL);
            while($row=mssql_fetch_array($query))
            {
                $rs[] = $row;
            }

            $this->free();
            return $rs;
        }

        public function sync_department($bmbh, $bmmc)
        {
            $insert_SQL = "INSERT INTO zwbmzd (bmbh, bmmc, bmxz, jc, mx, zgrs, madd, tcode, qyf) VALUES('$bmbh','$bmmc','',1 ,1,'','','',1)";
            $this->connect();
            alert_back($insert_SQL);exit();
            $rs = $this->query($insert_SQL);
            $this ->free();
            return $rs;
        }
        public function sync_zrl(){

        }


    }