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

        public function get_max_xmnmID(){
            $selectSQL = 'select top 1 xmnm from zwxmzd where 1=1 order by xmnm DESC';
            $this->connect();
            $query = $this->query($selectSQL);
            while($row=mssql_fetch_array($query))
            {
                $rs[] = $row;
            }

            $this->free();
            return $rs;
        }

        public function get_max_xmbhID(){
            $selectSQL = 'select top 1 xmbh from zwxmzd where 1=1 order by xmbh DESC';
            $this->connect();
            $query = $this->query($selectSQL);
            while($row=mssql_fetch_array($query))
            {
                $rs[] = $row;
            }

            $this->free();
            return $rs;
        }

        public function sync_pm($xmnm, $xmbh, $xmmc, $bmbh)
        {
            $insert_SQL = "INSERT INTO [zwxmzd_copy] ([xmnm], [bmbh], [xmbh], [xmmc], [lbbh], [jc], [mx], [kgrq], [wgrq], [wgf], [fzr], [czf], [czje], [madd], [tcode], [qyf], [fkf], [ic_id], [fkr], [xmmm], [srkm], [zckm], [bz], [zzlx], [isfb], [fzrbh], [xmlx], [flsx1], [flsx2], [flsx3], [czy], [czrq], [gkxxm], [xmjc], [CCLASS], [EDBMBH], [EDXMBH], [ISGK], [KYXMBH], [ZJE], [XMQC], [ZXBH], [XMLYM], [MJM], [CJXS], [GCDM], [ZTZJE], [TZGCDM], [TZLYDM], [JZMJ], [DWBH], [YSNDXX], [XMYSSX], [CX1], [CX2], [CX3], [CX4], [BZLX], [isfnd], [iszdzx], [isedkz], [jjflzckm], [czzckm], [YSZCZJLYBH], [YSLXBH], [YSXMLBBH], [BMYSXMBM], [YSSRZJLYBH], [ISZFCG], [ZFCGLBBH], [EDFLBH], [isdx], [gkbmbh], [jkcs], [iszxm], [zbmbh], [zxmbh], [jtbh], [jtrq], [isjt], [djje], [gkxmdm], [xmfl], [yszclxbh], [xmsx], [jtmbbh], [nosrkm], [nozckm], [nojjflkm], [isczzc], [yslx], [lkx]) VALUES ('$xmnm', '$bmbh', '$xmbh', '$xmmc', '', '1', '1', NULL, NULL, '0', '项目管理系统', '0', '.0000', NULL, NULL, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 'ST', '', '', '', '', '', '', '', '', '99', '$bmbh', '$xmbh', '0', '', '.00', '$xmmc', '', '', '1', '', '', '.00', '', '', 0, '', '', '', '', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '1', '$bmbh', 3, '1', '$bmbh', '200002', '', '', '0', '.00', '', '', '', '1', '', '', '', '', '0', '', '')";
            $this->connect();
            $rs = $this->query($insert_SQL);
            $this ->free();
            return $rs;
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
            $insert_SQL = "INSERT INTO zwbmzd_copy (bmbh, bmmc, bmxz, jc, mx, zgrs, madd, tcode, qyf) VALUES('$bmbh','$bmmc','',1 ,1,'','','',1)";
            $this->connect();
            $rs = $this->query($insert_SQL);
            $this ->free();
            return $rs;
        }
        public function sync_zrl(){

        }


    }