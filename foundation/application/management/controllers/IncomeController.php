<?php
    /**
     * Created by PhpStorm.
     * User: wangnan - work
     * Date: 2018/5/9
     * Time: 16:37
     */

    require_once("BaseController.php");
    class Management_incomeController extends BaseController
    {
        private $dbhelper;
        public $jjh_mg_pp_list;

        public function indexAction()
        {
            $incomeinfo = $this->orm->createDAO("pm_mg_income");
            $pname = HttpUtil::getString("pname");
            $department = HttpUtil::getString('department');

            $incomeinfo ->withPm_mg_chouzi(array("pname" => "pname"));
            $incomeinfo ->select(" pm_mg_income.*, pm_mg_chouzi.department ");


            if ($pname != "") {
                $incomeinfo->findPname($pname);
            }

            if(!empty($department)){
                $incomeinfo->selectLimit .= " AND pm_mg_chouzi.department=".$department;
            }

            if(HttpUtil::getString("starttime")!="" && HttpUtil::getString("endtime") != ""){
                $starttime = strtotime(HttpUtil::getString("starttime"));
                $endtime = strtotime(HttpUtil::getString("endtime"));
                $incomeinfo->selectLimit = " and 	income_datetime >= '$starttime' and income_datetime <= '$endtime'";
            }

            $this->view->assign("pname", $pname);
            $this->view->assign("starttime", HttpUtil::getString("starttime"));
            $this->view->assign("endtime", HttpUtil::getString("endtime"));

            $incomeinfo ->selectLimit .= " order by income_datetime desc";
            //$incomeinfo ->debugSql =true;
            $incomeinfo = $incomeinfo->get();

            if(!empty($incomeinfo)){
                $jjehj = '';
                foreach($incomeinfo as $key => $value){
                    $jjehj += $value['income_jje'];
                }
                $this->view->assign("jjehj", $jjehj);
            }

            // 如果是导出excel
            if($_REQUEST['excel'] == 'true'){
                //导出excel
                require_once 'phpexcel/Classes/PHPExcel.php';
                // Create new PHPExcel object
                $objPHPExcel = new PHPExcel();

                // Set properties
                $objPHPExcel->getProperties()->setCreator("JJH")
                    ->setLastModifiedBy("JJH")
                    ->setTitle("Office 2007 XLSX  Document")
                    ->setSubject("Office 2007 XLSX  Document")
                    ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("rescues");


                // Add some data
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', '')
                    ->setCellValue('B1', '项目名称')
                    ->setCellValue('C1', '收益时间')
                    ->setCellValue('D1', '收益金额')
                    ->setCellValue('E1', '备注');

                $i = 2;
                $hj = 0;
                foreach($incomeinfo as $v){
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, '')
                        ->setCellValue('B'.$i, $v['pname'])
                        ->setCellValue('C'.$i, date("Y-m-d",$v['income_datetime']))
                        ->setCellValue('D'.$i, number_format($v['income_jje'],2))
                        ->setCellValue('E'.$i, $v['beizhu']);

                    $hj += $v['income_jje'];
                    $i++;
                }

                $hejiqq = count($incomeinfo) + 2;
                $heji = "合计";

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('J'.$hejiqq,$heji.number_format($hj,2).'元');

                $i = "";

                // Rename sheet
                $objPHPExcel->getActiveSheet()->setTitle('统计');


                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $objPHPExcel->setActiveSheetIndex(0);


                // Redirect output to a client’s web browser (Excel5)
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="项目收益统计报表.xls"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
            }

            $total = count($incomeinfo);
            $pageDAO = new pageDAO();
            $pageDAO = $pageDAO->pageHelper($incomeinfo, null, "/management/income/index", null, 'get', 25, 8);
            $pages = $pageDAO['pageLink']['all'];
            $pages = str_replace("/index.php", "", $pages);
            $this->view->assign('incomelist', $pageDAO['pageData']);
            $this->view->assign('page', $pages);
            $this->view->assign('total', $total);

            echo $this->view->render("index/header.phtml");
            echo $this->view->render("income/index.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        public function addAction()
        {
            echo $this->view->render("index/header.phtml");
            echo $this->view->render("income/addincome.phtml");
            echo $this->view->render("index/footer.phtml");
        }

        /**
         * 筹资立项 - 同步财务系统中间库，写入对照关系表
         * @throws Exception
         */
        public function addrsAction()
        {
            try{
                $pname = HttpUtil::postString("pname");      //项目名称
                $income_datetime = HttpUtil::postString("income_datetime");   //
                $income_jje = HttpUtil::postString("income_jje");  //
                $beizhu = HttpUtil::postString("beizhu");  //

                if ($pname == "" || $income_datetime == "" || $income_jje == "") {
                    $this->alert_back("您输入的信息不完整，请查正后继续添加");
                }

                if(!is_numeric($income_jje) || $income_jje <= 0){
                    $this->alert_back("您输入的收益金额不正确！请重新输入！");
                }

                $pm_incomeDAO = $this->orm->createDAO("pm_mg_income");
                $pm_incomeDAO->pname = $pname;
                $pm_incomeDAO->income_datetime = strtotime($income_datetime);
                $pm_incomeDAO->income_jje = $income_jje;
                $pm_incomeDAO->beizhu = $beizhu;
                $pm_incomeDAO->lastmodify = time();

                $pm_info = $this->orm->createDAO("pm_mg_chouzi")->findPname($pname)->get();
                if(!empty($pm_info)){
                    $pid = $pm_info[0]['id'];
                }else {
                    $this->alert_back("项目不存在，或系统异常请联系系统开发人员！");
                }
                $pm_incomeDAO->pid = $pid;

                $logName = SessionUtil::getAdmininfo();
                addlog("添加收益信息-" . $pname, $logName['admin_name'], $_SERVER['REMOTE_ADDR'], date("Y-m-d H:i:s", time()), json_encode($pm_incomeDAO));

                $pm_incomeDAO ->admin_id = $logName['admin_id'];
                $pm_incomeDAO ->admin_name = $logName['admin_name'];

                $_pid = $pm_incomeDAO->save();   // $_pid 项目系统pm_id
                if($_pid) {
                    $this->alert_go("添加成功！", "/management/income");
                }else {
                    $this->alert_back("添加失败！");
                }

            }catch (Exception $e){
                throw $e;
            }
        }

        public function editAction()
        {
            $id = (int)$_REQUEST['id'];
            if ($_REQUEST['id'] != "") {
                $pm_incomeDAO = $this->orm->createDAO("pm_mg_income")->findId($id);
                $pm_incomeDAO = $pm_incomeDAO->get();

                $this->view->assign("income", $pm_incomeDAO);
                echo $this->view->render("index/header.phtml");
                echo $this->view->render("income/editincome.phtml");
                echo $this->view->render("index/footer.phtml");
            } else {
                alert_back("操作失败");
            }
        }

        /**
         * check项目是否已经建立
         * @param $pname  项目名称
         * @return bool
         */
        public function checkPname($pname){
            $pm_incomeDAO = $this->orm->createDAO("pm_mg_income")->findPname($pname)->get();
            if(!empty($pm_incomeDAO)){
                return true;
            }else {
                return false;
            }
        }

        public function editrsAction()
        {
            if ($_REQUEST['id'] != "") {
                $pname = HttpUtil::postString("pname");      //项目名称
                $income_datetime = HttpUtil::postString("income_datetime");   //
                $income_jje = HttpUtil::postString("income_jje");  //
                $beizhu = HttpUtil::postString("beizhu");  //

                if ($pname == "" || $income_datetime == "" || $income_jje == "") {
                    $this->alert_back("您输入的信息不完整，请查正后继续添加");
                }

                if (!is_numeric($income_jje) || $income_jje <= 0) {
                    $this->alert_back("您输入的收益金额不正确！请重新输入！");
                }

                $pm_incomeDAO = $this->orm->createDAO("pm_mg_income")->findId($_REQUEST['id']);;
                $pm_incomeDAO->pname = $pname;
                $pm_incomeDAO->income_datetime = strtotime($income_datetime);
                $pm_incomeDAO->income_jje = $income_jje;
                $pm_incomeDAO->beizhu = $beizhu;
                $pm_incomeDAO->lastmodify = time();

                $pm_info = $this->orm->createDAO("pm_mg_chouzi")->findPname($pname)->get();
                if (!empty($pm_info)) {
                    $pid = $pm_info[0]['id'];
                } else {
                    $this->alert_back("项目不存在，或系统异常请联系系统开发人员！");
                }
                $pm_incomeDAO->pid = $pid;

                $logName = SessionUtil::getAdmininfo();
                addlog("编辑收益信息-" . $pname, $logName['admin_name'], $_SERVER['REMOTE_ADDR'], date("Y-m-d H:i:s", time()), json_encode($pm_incomeDAO));

                $pm_incomeDAO->admin_id = $logName['admin_id'];
                $pm_incomeDAO->admin_name = $logName['admin_name'];

                $_pid = $pm_incomeDAO->save();   // $_pid 项目系统pm_id
                if ($_pid) {
                    $this->alert_go("编辑成功！", "/management/income");
                } else {
                    $this->alert_back("编辑失败！");
                }
            }else{
                $this->alert_back("编辑失败！");
            }
        }

        public function _init(){
            $this ->dbhelper = new DBHelper();
            $this ->dbhelper ->connect();
            SessionUtil::sessionStart();
            SessionUtil::checkmanagement();
            $this->admininfo = SessionUtil::getAdmininfo();
            $this->view->assign("admininfo",$this->admininfo);

            //项目分类
            $pcatelist = new jjh_mg_cateDAO();
            $pcatelist =  $pcatelist ->get($this->dbhelper);
            $this->view->assign("pcatelist",$pcatelist);

            //所属部门
            $departmentlist = new jjh_mg_departmentDAO();
            $departmentlist = $departmentlist->get($this->dbhelper);
            $this->view->assign("departmentlist",$departmentlist);

            // pplist
            $jjh_mg_ppDAO = $this->orm->createDAO('jjh_mg_pp')->get();
            if(!empty($jjh_mg_ppDAO)){
                foreach($jjh_mg_ppDAO as $k => $v){
                    $temp_array[$v['pid']] = $v['ppname'];
                }
            }
            if(!empty($jjh_mg_ppDAO)){
                foreach($jjh_mg_ppDAO as $k => $v){
                    $_temp_array[$v['pid']]['ppname'] = $v['ppname'];
                    $_temp_array[$v['pid']]['ppemail'] = $v['ppemail'];
                    $_temp_array[$v['pid']]['ppmobile'] = $v['ppmobile'];
                }
            }
            $this->view->assign("jjh_mg_pp_list_info", $_temp_array);
            $this->view->assign("jjh_mg_pp_list", $temp_array);
            $this->jjh_mg_pp_list = $temp_array;

            //项目名称列表
            $pm_chouzi = $this->orm->createDAO("pm_mg_chouzi");
            $pm_chouzi ->selectLimit .= " order by id desc";
            $pm_chouzi = $pm_chouzi ->get($this->dbhelper);
            $this->view->assign("pmlist",$pm_chouzi);

            // 项目进度
            $this->view->assign("rate_config",$this->rate_config);
        }

        //权限
        public function acl()
        {
            $action = $this->getRequest()->getActionName();
            $except_actions = array(
                'addrs',
                'editrs',
            );
            if (in_array($action, $except_actions)) {
                return;
            }
            parent::acl();
        }
    }