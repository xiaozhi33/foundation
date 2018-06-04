<?php
	require_once("BaseController.php");
	class Management_peibiController extends BaseController
    {
		public function indexAction(){
            $pname = $_REQUEST['pname'];
            $is_show = $_REQUEST['is_show'];
            $is_income = $_REQUEST['is_income'];
            $pm_mg_info = $this->orm->createDAO("pm_mg_info");
            $pm_mg_info ->select("
                `pm_mg_info`.id,
                `pm_mg_info`.pm_name,
                `pm_mg_info`.pm_pp,
                `pm_mg_info`.pm_pp_cate,
                `pm_mg_info`.zijin_daozheng_jiner,
                `pm_mg_info`.zijin_daozhang_datetime,
                `pm_mg_info`.zijin_laiyuan_qudao,
                `pm_mg_info`.pm_juanzeng_yongtu,
                `pm_mg_chouzi`.pm_liuben
          ");
            $pm_mg_info ->withPm_mg_chouzi(array("pm_name" => "pname"));
            $pm_mg_info ->selectLimit .= ' AND cast(`pm_mg_info`.zijin_daozheng_jiner as SIGNED INTEGER) >= 100000 ';
            $pm_mg_info ->selectLimit .= ' AND cate_id = 0';
            $pm_mg_info ->selectLimit .= ' AND peibi = 1';
            $pm_mg_info ->selectLimit .= ' AND `pm_mg_info`.is_show_peibi = 0';

            if ($pname != ""){
                $pm_mg_info ->selectLimit .= " and `pm_mg_chouzi`.pname = '$pname'";
            }

            if($is_show != ""){
                $pm_mg_info ->selectLimit .= " and `pm_mg_info`.is_show_peibi = '$is_show'";
            }

            if($is_income != ""){
                $pm_mg_info ->withPm_mg_peibi(array("id"=>"lk_main_id"));
                $pm_mg_info ->selectLimit .= " and `pm_mg_peibi`.is_income = '$is_income'";
            }

            if($_REQUEST['yeartime'] != ''){
                $yeartime = date('Y',strtotime($_REQUEST['yeartime'].'-01-01'));
                $starttime = $yeartime.'-01-01';
                $endtime = $yeartime.'-12-31';
                $pm_mg_info->selectLimit .= " and zijin_daozhang_datetime >= '$starttime' and zijin_daozhang_datetime <= '$endtime'";
            }

            $this->view->assign("yeartime", $_REQUEST['yeartime']);

            $pm_mg_info ->selectLimit .= ' order by zijin_daozhang_datetime desc, `pm_mg_chouzi`.id ';
            $pm_mg_info->getPager(array('path'=>'/management/peibi/index'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("peibi/index.phtml");
            echo $this->view->render("index/footer.phtml");
		}

        public function peibiindexAction(){
            $peibikDAO = $this->orm->createDAO('pm_mg_peibi')->order('id DESC');
            if(!empty($_REQUEST['lk_main_id'])){
                $peibikDAO->findLk_main_id($_REQUEST['lk_main_id']);
                $this->view->assign("lk_main_id", $_REQUEST['lk_main_id']);
            }

            if(!empty($_REQUEST['pm_name'])){
                 $peibikDAO->findPm_name($_REQUEST['pm_name']);
                 $this->view->assign("pname", $_REQUEST['pm_name']);
             }
            $peibikDAO->getPager(array('path'=>'/management/peibi/index'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("peibi/peibiindex.phtml");
            echo $this->view->render("index/footer.phtml");
        }


        /*
         *  add feedback
         */
		public function addAction(){
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("peibi/addpeibi.phtml");
            echo $this->view->render("index/footer.phtml");
		}

        /*
         *  toSave feedback information
         */
        public function toAddAction(){
            (int)$id = $_REQUEST['id'];
           /* $pm_id = HttpUtil::postString("pm_id");
            $pm_name = HttpUtil::postString("pm_name");*/

            //$is_peibi = HttpUtil::postString("is_peibi");
            $is_peibi = '1';

            $is_pass = HttpUtil::postString("is_pass");
            $is_show_peibi = HttpUtil::postString("is_show");
            $jpyy = HttpUtil::postString("jpyy");
            $je = HttpUtil::postString("je");
            $peibi_datetime = HttpUtil::postString("peibi_datetime");
            $card = HttpUtil::postString("card");
            $jffzr = $_REQUEST["jffzr"];
            $peibi_spr = $_REQUEST["peibi_spr"];
            $huabo_department = HttpUtil::postString("huabo_department");
            $lk_main_id = HttpUtil::postString("lk_main_id");
            $is_income = HttpUtil::postString("is_income");

            $jffzr = implode(',',$jffzr);
            $peibi_spr = implode(',',$peibi_spr);

            $peibiDAO = $this->orm->createDAO('pm_mg_peibi');

            if($lk_main_id == ""){
                alert_back('您输入的信息不完整，请查正后继续添加！！！！！');
            }

            $lk_info = $this->orm->createDAO("pm_mg_info")->findId($lk_main_id)->get();
            $lk_info_jr = $lk_info[0]['zijin_daozheng_jiner'];
            $_lk_info_jr = $lk_info_jr * 0.3;

            $lk_peibiDAO = $this->orm->createDAO('pm_mg_peibi');
            $lk_peibiDAO ->findLk_main_id($lk_main_id);
            if(!empty($id)){
                $lk_peibiDAO ->selectLimit .= ' AND id !='.$id;
            }
            $lk_peibiDAO ->select(' sum(je) as djr');
            $lk_peibiDAO = $lk_peibiDAO->get();

            /*if($je > ($_lk_info_jr-$lk_peibiDAO[0]['djr'])){
                alert_back('配比金额不能超过总金额('.$lk_info_jr.')的30%，');
            }*/

            try{
                if(!empty($id)){
                    $peibiDAO ->findId($id);
                }
                //$peibiDAO ->pm_id = $lk_info[0]['id'];
                $peibiDAO ->pm_name = $lk_info[0]['pm_name'];
                $peibiDAO ->is_peibi = $is_peibi; // 是否配比
                $peibiDAO ->is_pass = $is_pass; // 是否通过配比
                $peibiDAO ->jpyy = $jpyy; // 拒批原因
                $peibiDAO ->je = $je;  // 配比金额
                $peibiDAO ->peibi_datetime = $peibi_datetime;   // 配比下发时间
                $peibiDAO ->huabo_department = $huabo_department;   // 配比下发时间
                $peibiDAO ->card = $card;   // 卡号
                $peibiDAO ->jffzr = $jffzr;   // 经费负责人
                $peibiDAO ->peibi_spr = $peibi_spr;   // 配比审批人
                $peibiDAO ->lk_main_id = $lk_main_id;   // 来款id
                $peibiDAO ->is_income = $is_income;   // 来款id
                $peibiDAO ->save();

                // 同步配比是否奖励信息
                $lkDAO = $this->orm->createDAO("pm_mg_info")->findId($lk_main_id);
                $lkDAO ->is_show_peibi = $is_show_peibi;
                $lkDAO ->save();

            }catch (Exception $e){
                //throw $e;
                alert_back('保存失败！！！！！');
            }
            alert_go('保存成功', "/management/peibi/peibiindex?lk_main_id=".$lk_main_id);
        }
		
		public function editAction(){
			$id = HttpUtil::getString("id");
            $peibiDAO = $this->orm->createDAO('pm_mg_peibi');
            $peibiDAO ->findId($id);
            $peibiDAO = $peibiDAO ->get();

            $lk_info = $this->orm->createDAO("pm_mg_info")->findId($peibiDAO[0]['lk_main_id'])->get();
            $this->view->assign("lk_info", $lk_info);
			
			if($peibiDAO != "")
			{
				$this->view->assign("peibiDAO", $peibiDAO);
				echo $this->view->render("index/header.phtml");
				echo $this->view->render("peibi/editpeibi.phtml");
				echo $this->view->render("index/footer.phtml");
                exit();
			}
		}
		
		public function delAction(){
			$id = HttpUtil::getString("id");
            $peibiDAO = $this->orm->createDAO('pm_mg_peibi');
            $peibiDAO ->findId($id);
            $peibiDAO ->delete();

            echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
            echo('<script language="JavaScript">');
            echo("alert('删除成功');");
            echo("location.href='/management/peibi';");
            echo('</script>');
            exit;

        }

        public function getpeibilist($start='',$end='',$department='',$cate='',$pname=''){
            $pm_mg_info = $this->orm->createDAO("pm_mg_info");
            $pm_mg_info ->select("
                `pm_mg_info`.id,
                `pm_mg_info`.pm_name,
                `pm_mg_info`.pm_pp,
                `pm_mg_info`.pm_pp_cate,
                `pm_mg_info`.zijin_daozheng_jiner,
                `pm_mg_info`.zijin_daozhang_datetime,
                `pm_mg_info`.zijin_laiyuan_qudao,
                `pm_mg_info`.pm_juanzeng_yongtu,
                `pm_mg_chouzi`.pm_liuben
          ");
            $pm_mg_info ->withPm_mg_chouzi(array("pm_name" => "pname"));
            $pm_mg_info ->selectLimit .= ' AND cast(`pm_mg_info`.zijin_daozheng_jiner as SIGNED INTEGER) >= 100000 ';
            $pm_mg_info ->selectLimit .= ' AND cate_id = 0';
            $pm_mg_info ->selectLimit .= ' AND peibi = 1';

            if ($start != "" && $end != ""){
                $pm_mg_info ->selectLimit .= " and `pm_mg_info`.zijin_daozhang_datetime between '$start' and '$end' ";
            }
            if ($department != ""){
                $pm_mg_info ->selectLimit .= " and `pm_mg_chouzi`.department = '$department'";
            }
            if ($cate != ""){
                $pm_mg_info ->selectLimit .= " and `pm_mg_chouzi`.cate = '$cate'";
            }
            if ($pname != ""){
                $pm_mg_info ->selectLimit .= " and `pm_mg_chouzi`.pname = '$pname'";
            }
            $pm_mg_info ->selectLimit .= ' order by `pm_mg_chouzi`.id ';
            return $pm_mg_info->get();
        }

         public function _init(){
            $this ->dbhelper = new DBHelper();
            $this ->dbhelper ->connect();
            SessionUtil::sessionStart();
            SessionUtil::checkmanagement();

            //来款列表
            $lk_list = $this->getpeibilist();
            $this->view->assign("lk_list",$lk_list);

            if(!empty($lk_list)){
                foreach($lk_list as $key => $value){
                    $_lk_list[$value['id']] = $value['pm_name']."-".date('Y-m-d',strtotime($value['zijin_daozhang_datetime']))."-".$value['pm_pp']."-金额为：".$value['zijin_daozheng_jiner'];
                }
            }
            $this->view->assign("lk_list",$lk_list);
            $this->view->assign("l_lk_list",$_lk_list);

            //项目名称列表
            $pm_chouzi = new pm_mg_chouziDAO();
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);

             //所属部门
             $departmentlist = new jjh_mg_departmentDAO();
             $departmentlist = $departmentlist->get($this->dbhelper);
             $this->view->assign("departmentlist",$departmentlist);

             // pplist
             $jjh_mg_ppDAO = $this->orm->createDAO('jjh_mg_pp')->select('pid,ppname')->get();
             if(!empty($jjh_mg_ppDAO)){
                 foreach($jjh_mg_ppDAO as $k => $v){
                     $temp_array[$v['pid']] = $v['ppname'];
                 }
             }
             $this->view->assign("jjh_mg_pp_list", $temp_array);

            //获取筹资项目list
            $chouziDAO = $this->orm->createDAO("pm_mg_chouzi")->select("id, pname, parent_pm_id, parent_pm_id_path")->get();
            $this->view->assign("chouzi_lists",$chouziDAO);
        }

        public function peibiinfoAction(){
            $id = HttpUtil::getString("id");
            $peibiDAO = $this->orm->createDAO('pm_mg_peibi');
            $peibiDAO ->findId($id);
            $peibiDAO = $peibiDAO ->get();

            if($peibiDAO != "")
            {
                $this->view->assign("peibiDAO", $peibiDAO);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("peibi/peibiinfo.phtml");
                echo $this->view->render("index/footer.phtml");
                exit();
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////
        /////////////配比支出//////////////////////////////////////////////////////////////////////

        public function zcindexAction(){
            $pname = $_REQUEST['pname'];
            $starttime = $_REQUEST['starttime'];
            $endtime = $_REQUEST['endtime'];
            $card = $_REQUEST['card'];
            $pm_mg_peibi_zcDAO = $this->orm->createDAO("pm_mg_peibi_zc");
            if(!empty($pname)){
                $pm_mg_peibi_zcDAO->findPm_name($pname);
            }
            if(!empty($card)){
                $pm_mg_peibi_zcDAO->findCard($card);
            }
            if(!empty($starttime)){
                $pm_mg_peibi_zcDAO->selectLimit .= " and peibi_datetime >= '$starttime'";
            }
            if(!empty($endtime)){
                $pm_mg_peibi_zcDAO->selectLimit .= " and peibi_datetime <= '$endtime'";
            }

            $hj = $pm_mg_peibi_zcDAO ->get();
            if(!empty($hj)){
                $hjje = 0;
                foreach($hj as $key => $value){
                    $hjje += $value['je'];
                }
                $this->view->assign("hjje", $hjje);
            }
            $pm_mg_peibi_zcDAO->getPager(array('path'=>'/management/peibi/zcindex'))->assignTo($this->view);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("peibi/zcindex.phtml");
            echo $this->view->render("index/footer.phtml");
        }


        /////////////////按项目批量录入配比支出信息//////////////////////////////////////

        public function addzcAction(){
            $pname = $_REQUEST['pname'];
            if(!empty($pname)){
                // 查询该项目是否有配比信息，如果有可以添加配比支出
                $peibiDAO = $this->orm->createDAO("pm_mg_peibi")->findPm_name($pname)->get();
                if(empty($peibiDAO)) {
                    $this->alert_back("该项目目前没有可以支出的配比款项！");
                }
                // 看配比余额是否大于0，如果不大于0，提示余额不足 todolist

                $this->view->assign("peibiinfo", $peibiDAO[0]);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("peibi/addzc.phtml");
                echo $this->view->render("index/footer.phtml");
            }else {
                $this->alert_back("请选择批量添加配比项目");
            }
        }

        /////////////////////////////////////////////////////////////////////////////////
        public function zcaddrsAction(){
            try{
                $pm_mg_peibi_zcDAO = $this->orm->createDAO("pm_mg_peibi_zc");
                $pm_mg_peibi_zcDAO ->beginTran();
                if(empty($_REQUEST['pm_name'])){
                    alert_back('信息不完整，保存失败！！！！！');
                }

                $pm_mg_peibi_zcDAO ->pm_name = $_REQUEST['pm_name'];
                $pm_mg_peibi_zcDAO ->je = $_REQUEST['je'];
                $pm_mg_peibi_zcDAO ->peibi_datetime = $_REQUEST['peibi_datetime'];
                $pm_mg_peibi_zcDAO ->card = $_REQUEST['card'];

                $jffzr = $_REQUEST["jffzr"];
                $jffzr = implode(',',$jffzr);
                $pm_mg_peibi_zcDAO ->jffzr = $jffzr;
                $pm_mg_peibi_zcDAO ->save();

                foreach ($_POST['xnumber'] as $key => $value) {
                    $pm_mg_peibi_zcDAO ->pm_name = $_REQUEST['pm_name'];
                    $pm_mg_peibi_zcDAO ->je = $_REQUEST['je'.$value];
                    $pm_mg_peibi_zcDAO ->peibi_datetime = $_REQUEST['peibi_datetime'.$value];
                    $pm_mg_peibi_zcDAO ->card = $_REQUEST['card'.$value];

                    $jffzr = $_REQUEST["jffzr".$value];
                    $jffzr = implode(',',$jffzr);
                    $pm_mg_peibi_zcDAO ->jffzr = $jffzr;
                    $pm_mg_peibi_zcDAO ->save();
                }

                $pm_mg_peibi_zcDAO ->commit();
            }catch (Exception $e){
                $pm_mg_peibi_zcDAO ->rollback();
                $this->alert_back('保存失败！！！！！');
            }
            $this->alert_go('保存成功', "/management/peibi/zcindex");

        }

        /////////////////////////////////////////////////////////////////////////////////

        public function editzcAction()
        {
            if(!empty($_REQUEST['id'])) {
                $pm_mg_peibi_zcDAO = $this->orm->createDAO("pm_mg_peibi_zc")->findId($_REQUEST['id'])->get();
                $this->view->assign("zcinfo", $pm_mg_peibi_zcDAO[0]);
            }
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("peibi/editzc.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function rszcAction(){
            if(empty($_REQUEST['pm_name']) || empty($_REQUEST['je']) || empty($_REQUEST['peibi_datetime']) || empty($_REQUEST['card'])){
                alert_back('信息不完整，保存失败！！！！！');
            }
            $pm_mg_peibi_zcDAO = $this->orm->createDAO("pm_mg_peibi_zc");
            if(!empty((int)$_REQUEST['id'])) {
                $pm_mg_peibi_zcDAO->findId($_REQUEST['id']);
            }
            $pm_mg_peibi_zcDAO ->pm_name = $_REQUEST['pm_name'];
            $pm_mg_peibi_zcDAO ->je = $_REQUEST['je'];
            $pm_mg_peibi_zcDAO ->peibi_datetime = $_REQUEST['peibi_datetime'];
            $pm_mg_peibi_zcDAO ->card = $_REQUEST['card'];

            $jffzr = $_REQUEST["jffzr"];
            $jffzr = implode(',',$jffzr);
            $pm_mg_peibi_zcDAO ->jffzr = $jffzr;

            try{
                $pm_mg_peibi_zcDAO ->save();
            }catch (Exception $e){
                alert_back('保存失败！！！！！');
            }
            alert_go('保存成功', "/management/peibi/zcindex");
        }

        public function delzcAction(){
            $pm_mg_peibi_zcDAO = $this->orm->createDAO("pm_mg_peibi_zc");
            try{
                if(!empty((int)$_REQUEST['id'])) {
                    $pm_mg_peibi_zcDAO->findId($_REQUEST['id']);
                    $pm_mg_peibi_zcDAO ->delete();
                }else {
                    $this->alert_back('删除失败！！！！！');
                }
            }catch (Exception $e){
                $this->alert_back('删除失败！！！！！');
            }
            $this->alert_go('删除成功', "/management/peibi/zcindex");
        }

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'to-add',
                'getpeibilist',
                'zcindex',
                'addzc',
                'zcaddrs',
                'editzc',
                'rszc',
                'delzc'
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
	}