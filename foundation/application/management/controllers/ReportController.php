<?php
	require_once("BaseController.php");
	class Management_reportController extends BaseController {
		private $dbhelper;
		public $departmentlist;
		public function indexAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("report/index.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		//筹资
		public function chouziAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("report/chouzi.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		//筹资统计toExcel
		public function chouzitoexcelAction(){
			$pname = HttpUtil::postString("pname");
			$department = HttpUtil::postString("department");
			$cate = HttpUtil::postString("cate");
			$chouziinfo = new pm_mg_chouziDAO();
			
			if($pname != ""){
				$chouziinfo ->pname = $pname;
			}
			
			if($department != ""){
				$chouziinfo ->department = $department;
			}
			
			if($cate != ""){
				$chouziinfo ->cate = $cate;
			}

			if(HttpUtil::postString("starttime")!="" && HttpUtil::postString("endtime") != ""){
				$starttime = HttpUtil::postString("starttime");
				$endtime = HttpUtil::postString("endtime");
				$chouziinfo->selectLimit = " and pm_qishi_datetime >= '$starttime' and pm_jiezhi_datetime <= '$endtime'";
			}

			$chouziinfo = $chouziinfo->get($this->dbhelper);
			if (count($chouziinfo) == 0){
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('查无结果，请重新查询');");
                echo('history.back();');
                echo('</script>');
                exit;
			}
			
			//导出excel
			require_once 'phpexcel/Classes/PHPExcel.php';
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("TJ BYJJH")
										 ->setLastModifiedBy("TJ BYJJH")
										 ->setTitle("Office 2007 XLSX  Document")
										 ->setSubject("Office 2007 XLSX  Document")
										 ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("rescues");


			// Add some data
			$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A1', '编号')
			            ->setCellValue('B1', '项目名称')
			            ->setCellValue('C1', '所属部门')
			            ->setCellValue('D1', '项目分类')
			            ->setCellValue('E1', '宣传推动期')
			            ->setCellValue('F1', '项目孵化期')
			            ->setCellValue('G1', '项目签约日期')
			            ->setCellValue('H1', '项目起始日期')
			            ->setCellValue('I1', '项目截止日期')
			            ->setCellValue('J1', '协议捐赠金额')
			            ->setCellValue('K1', '留本基金')
			            ->setCellValue('L1', '捐赠仪式')
			            ->setCellValue('M1', '备注');

			$i = 2;
			foreach($chouziinfo as $v){
				$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A'.$i, $v['pid'])
			            ->setCellValue('B'.$i, $v['pname'])
			            ->setCellValue('C'.$i, $this->getdepartmentAction($this->departmentlist,$v['department']))
			            ->setCellValue('D'.$i, $this->getcateAction($this->pcatelist,$v['cate']))
			            ->setCellValue('E'.$i, $v['pm_tuidongqi'])
			            ->setCellValue('F'.$i, $v['pm_fuhuaqi'])
			            ->setCellValue('G'.$i, $v['pm_qianyue_datetime'])
			            ->setCellValue('H'.$i, $v['pm_qishi_datetime'])
			            ->setCellValue('I'.$i, $v['pm_jiezhi_datetime'])
			            ->setCellValue('J'.$i, $v['pm_xieyi_juanzeng_jiner'])
			            ->setCellValue('K'.$i, $v['pm_liuben'])
			            ->setCellValue('L'.$i, $v['pm_yishi'])
			            ->setCellValue('M'.$i, $v['beizhu']);
			            
			            $xieyijuanzeng += $v['pm_xieyi_juanzeng_jiner'];
			    $i++;
			}
			
			$hejiqq = count($chouziinfo) + 2;
			$heji = "合计";
			
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('J'.$hejiqq,$heji.$xieyijuanzeng);
			
			$i = "";
			
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('统计');
			
			
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="筹资统计报表.xls"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
		}
		
		//资金
		public function zijinAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("report/zijin_new.phtml");
			echo $this->view->render("index/footer.phtml");
		}

        //收入
        public function zijinnewtoexcelAction(){
            try{
                $type = HttpUtil::postString("type");
                $pname = HttpUtil::postString("pname");
                $cate = HttpUtil::postString("cate");
                $department = HttpUtil::postString("department");
                $zijin_daozhang_datetime =  HttpUtil::postString("zijin_daozhang_datetime");
                $zijin_daozhang_datetime1 =  HttpUtil::postString("zijin_daozhang_datetime1");

                if($pname == ''){   // 以父类项目进行统计  -- all
                    $zijininfo = new pm_mg_infoDAO();
                    $zijininfo ->joinTable(" left join pm_mg_chouzi as c on pm_mg_info.pm_name=c.pname");
                    //$zijininfo ->selectField(" concat(parent_pm_id,'-',c.id) as bpath, c.parent_pm_id, c.parent_pm_id_path, pm_mg_info.id, pm_mg_info.pm_name, pm_mg_info.zijin_daozhang_datetime, pm_mg_info.zijin_daozheng_jiner, pm_mg_info.pm_pp_cate, pm_mg_info.pm_juanzeng_cate, pm_mg_info.zijin_laiyuan_qudao, pm_mg_info.shiyong_type, pm_mg_info.piaoju, pm_mg_info.piaoju_fph, pm_mg_info.piaoju_fkfs, pm_mg_info.piaoju_kddh, pm_mg_info.renling_name");

                    $zijininfo ->selectField("
                    IF(
                        parent_pm_id = '',
                        concat(parent_pm_id, '-', c.id),
                        concat('0-', parent_pm_id, '-', c.id)
                    )AS bpath,
                     c.parent_pm_id,
                     c.parent_pm_id_path,
                     pm_mg_info.pm_name,
                     pm_mg_info.zijin_daozhang_datetime,
                     pm_mg_info.zijin_daozheng_jiner,
                     pm_mg_info.pm_pp_cate,
                     pm_mg_info.pm_juanzeng_cate,
                     pm_mg_info.zijin_laiyuan_qudao,
                     pm_mg_info.shiyong_type,
                     pm_mg_info.piaoju,
                     pm_mg_info.piaoju_fph,
                     pm_mg_info.piaoju_fkfs,
                     pm_mg_info.piaoju_kddh,
                     pm_mg_info.renling_name ");

                    if($pname != ""){
                        $zijininfo ->pm_name = $pname;
                    }
                    if($department != ""){
                        $zijininfo ->department = $department;
                    }

                    if($zijin_daozhang_datetime != "" && $zijin_daozhang_datetime1 != ""){
                        $zijininfo ->selectLimit .= " and zijin_daozhang_datetime between '$zijin_daozhang_datetime' and '$zijin_daozhang_datetime1'";
                    }

                    $zijininfo ->selectLimit .= " and cate_id=0 order by bpath";
                    $zijininfo ->debugSql =true;
                    $zijininfo = $zijininfo->get($this->dbhelper);
                    var_dump($zijininfo);exit();

                    if (count($zijininfo) == 0){
                        alert_back("查无结果，请重新查询");
                    }

                    require_once 'phpexcel/Classes/PHPExcel.php';
                    // Create new PHPExcel object
                    $zijintj = new PHPExcel();

                    // Set properties
                    $zijintj->getProperties()->setCreator("TJ BYJJH")
                        ->setLastModifiedBy("TJ BYJJH")
                        ->setTitle("Office 2007 XLSX  Document")
                        ->setSubject("Office 2007 XLSX  Document")
                        ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
                        ->setKeywords("office 2007 openxml php")
                        ->setCategory("rescues");
                    // Add some data
                    $zijintj->setActiveSheetIndex(0)
                        ->setCellValue('A1', '序号')
                        ->setCellValue('B1', '项目名称')
                        ->setCellValue('C1', '项目捐赠者')
                        ->setCellValue('D1', '捐赠者类型')
                        ->setCellValue('E1', '捐赠级别')
                        ->setCellValue('F1', '项目捐赠类型')
                        ->setCellValue('G1', '捐赠用途')
                        ->setCellValue('H1', '捐赠到账日期')
                        ->setCellValue('I1', '捐赠到账金额')
                        ->setCellValue('J1', '资金来源渠道')
                        ->setCellValue('K1', '是否是校友')
                        ->setCellValue('L1', '配比状态')
                        ->setCellValue('M1', '票据状态')
                        ->setCellValue('N1', '证书状态')
                        ->setCellValue('O1', '捐赠单位介绍')
                        ->setCellValue('P1', '备注');

                    $ii = 2;
                    foreach($zijininfo as $v){
                        $zijintj->setActiveSheetIndex(0)
                            ->setCellValue('A'.$ii, $v['id'])
                            ->setCellValue('B'.$ii, $v['pm_name'])
                            ->setCellValue('C'.$ii, $v['pm_pp'])
                            ->setCellValue('D'.$ii, $v['pm_pp_cate'])
                            ->setCellValue('E'.$ii, $v['pm_juanzeng_jibie'])
                            ->setCellValue('F'.$ii, $this->getcateAction($this->pcatelist,$v['pm_juanzeng_cate']))
                            ->setCellValue('G'.$ii, $v['pm_juanzeng_yongtu'])
                            ->setCellValue('H'.$ii, $v['zijin_daozhang_datetime'])
                            ->setCellValue('I'.$ii, $v['zijin_daozheng_jiner'])
                            ->setCellValue('J'.$ii, $v['zijin_laiyuan_qudao'])
                            ->setCellValue('K'.$ii, $v['pm_is_school'])
                            ->setCellValue('L'.$ii, $v['peibi'])
                            ->setCellValue('M'.$ii, $v['piaoju'])
                            ->setCellValue('N'.$ii, $v['zhengshu'])
                            ->setCellValue('O'.$ii, $v['pm_pp_company'])
                            ->setCellValue('P'.$ii, $v['beizhu']);
                        $ii++;

                        $shouru += $v['zijin_daozheng_jiner'];
                    }

                    $hejixx = count($zijininfo) + 2;
                    $heji = "合计";

                    $zijintj->setActiveSheetIndex(0)->setCellValue('I'.$hejixx,$heji.$shouru);

                    $ii = "";

                    $zijintj->getActiveSheet()->setTitle('zijintongji');
                    $zijintj->setActiveSheetIndex(0);

                    ob_end_clean();
                    ob_start();

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="资金统计报表.xls"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($zijintj, 'Excel5');
                    $objWriter->save('php://output');
                    exit;


                }else {       // 按照项目统计 - 单个项目收支统计


                    $zijininfo = new pm_mg_infoDAO();
                    $zijininfo ->joinTable(" left join pm_mg_chouzi as c on pm_mg_info.pm_name=c.pname");
                    $zijininfo ->selectField(" concat(parent_pm_id,'-',c.id) as bpath, c.parent_pm_id, c.parent_pm_id_path, pm_mg_info.id, pm_mg_info.pm_name, pm_mg_info.zijin_daozhang_datetime, pm_mg_info.zijin_daozheng_jiner, pm_mg_info.pm_pp_cate, pm_mg_info.zijin_laiyuan_qudao, pm_mg_info.shiyong_type, pm_mg_info.piaoju, pm_mg_info.piaoju_fph, pm_mg_info.piaoju_fkfs, pm_mg_info.piaoju_kddh, pm_mg_info.renling_name");

                    if($pname != ""){
                        $zijininfo ->pm_name = $pname;
                    }

                    if($department != ""){
                        $zijininfo ->department = $department;
                    }

                    if($zijin_daozhang_datetime != "" && $zijin_daozhang_datetime1 != ""){
                        $zijininfo ->selectLimit .= " and zijin_daozhang_datetime between '$zijin_daozhang_datetime' and '$zijin_daozhang_datetime1'";
                    }

                    $zijininfo ->selectLimit .= " and cate_id=0 order by concat(parent_pm_id,'-',c.id)";
                    //$zijininfo ->debugSql =true;
                    $zijininfo = $zijininfo->get($this->dbhelper);
                    var_dump($zijininfo);exit();

                    if (count($zijininfo) == 0){
                        alert_back("查无结果，请重新查询");
                    }

                    require_once 'phpexcel/Classes/PHPExcel.php';
                    // Create new PHPExcel object
                    $zijintj = new PHPExcel();

                    // Set properties
                    $zijintj->getProperties()->setCreator("TJ BYJJH")
                        ->setLastModifiedBy("TJ BYJJH")
                        ->setTitle("Office 2007 XLSX  Document")
                        ->setSubject("Office 2007 XLSX  Document")
                        ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
                        ->setKeywords("office 2007 openxml php")
                        ->setCategory("rescues");
                    // Add some data
                    $zijintj->setActiveSheetIndex(0)
                        ->setCellValue('A1', '序号')
                        ->setCellValue('B1', '项目名称')
                        ->setCellValue('C1', '项目捐赠者')
                        ->setCellValue('D1', '捐赠者类型')
                        ->setCellValue('E1', '捐赠级别')
                        ->setCellValue('F1', '项目捐赠类型')
                        ->setCellValue('G1', '捐赠用途')
                        ->setCellValue('H1', '捐赠到账日期')
                        ->setCellValue('I1', '捐赠到账金额')
                        ->setCellValue('J1', '资金来源渠道')
                        ->setCellValue('K1', '是否是校友')
                        ->setCellValue('L1', '配比状态')
                        ->setCellValue('M1', '票据状态')
                        ->setCellValue('N1', '证书状态')
                        ->setCellValue('O1', '捐赠单位介绍')
                        ->setCellValue('P1', '备注');

                    $ii = 2;
                    foreach($zijininfo as $v){
                        $zijintj->setActiveSheetIndex(0)
                            ->setCellValue('A'.$ii, $v['id'])
                            ->setCellValue('B'.$ii, $v['pm_name'])
                            ->setCellValue('C'.$ii, $v['pm_pp'])
                            ->setCellValue('D'.$ii, $v['pm_pp_cate'])
                            ->setCellValue('E'.$ii, $v['pm_juanzeng_jibie'])
                            ->setCellValue('F'.$ii, $this->getcateAction($this->pcatelist,$v['pm_juanzeng_cate']))
                            ->setCellValue('G'.$ii, $v['pm_juanzeng_yongtu'])
                            ->setCellValue('H'.$ii, $v['zijin_daozhang_datetime'])
                            ->setCellValue('I'.$ii, $v['zijin_daozheng_jiner'])
                            ->setCellValue('J'.$ii, $v['zijin_laiyuan_qudao'])
                            ->setCellValue('K'.$ii, $v['pm_is_school'])
                            ->setCellValue('L'.$ii, $v['peibi'])
                            ->setCellValue('M'.$ii, $v['piaoju'])
                            ->setCellValue('N'.$ii, $v['zhengshu'])
                            ->setCellValue('O'.$ii, $v['pm_pp_company'])
                            ->setCellValue('P'.$ii, $v['beizhu']);
                        $ii++;

                        $shouru += $v['zijin_daozheng_jiner'];
                    }

                    $hejixx = count($zijininfo) + 2;
                    $heji = "合计";

                    $zijintj->setActiveSheetIndex(0)->setCellValue('I'.$hejixx,$heji.$shouru);

                    $ii = "";

                    $zijintj->getActiveSheet()->setTitle('zijintongji');
                    $zijintj->setActiveSheetIndex(0);

                    ob_end_clean();
                    ob_start();

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="资金统计报表.xls"');
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($zijintj, 'Excel5');
                    $objWriter->save('php://output');
                    exit;
                }
            }catch (Exception $e){
                throw $e;
            }
        }
		
		//资金统计toExcel
		public function zijintoexcelAction(){
            try{
                $pname = HttpUtil::postString("pname");
                $department = HttpUtil::postString("department");
                $pm_juanzeng_jibie = HttpUtil::postString("pm_juanzeng_jibie");
                $pm_juanzeng_yongtu = HttpUtil::postString("pm_juanzeng_yongtu");
                $pm_pp = HttpUtil::postString("pm_pp");
                $zijin_daozhang_datetime =  HttpUtil::postString("zijin_daozhang_datetime");
                $zijin_daozhang_datetime1 =  HttpUtil::postString("zijin_daozhang_datetime1");
                $zijin_daozheng_jiner =  HttpUtil::postString("zijin_daozheng_jiner");

                $peibi = HttpUtil::postString("peibi");
                $pm_is_school = HttpUtil::postString("pm_is_school");

                $zijininfo = new pm_mg_infoDAO();

                if($pname != ""){
                    $zijininfo ->pm_name = $pname;
                }

                if($peibi != ""){
                    $zijininfo ->peibi = $peibi;
                }

                if($pm_is_school != ""){
                    $zijininfo ->pm_is_school = $pm_is_school;
                }

                if($department != ""){
                    $zijininfo ->department = $department;
                }

                if($pm_juanzeng_jibie != ""){
                    $zijininfo ->pm_juanzeng_jibie = $pm_juanzeng_jibie;
                }

                if($pm_juanzeng_yongtu != ""){
                    $zijininfo ->pm_juanzeng_yongtu = $pm_juanzeng_yongtu;
                }

                if($pm_pp != ""){
                    $zijininfo ->pm_pp = $pm_pp;
                }

                if($zijin_daozheng_jiner != ""){
                    $zijininfo ->zijin_daozheng_jiner = $zijin_daozheng_jiner;
                }

                if($zijin_daozhang_datetime != "" && $zijin_daozhang_datetime1 != ""){
                    $zijininfo ->selectLimit .= " and zijin_daozhang_datetime between '$zijin_daozhang_datetime' and '$zijin_daozhang_datetime1'";
                }

                $zijininfo ->selectLimit .= " and cate_id=0 order by id desc";
                //$zijininfo ->debugSql =true;
                $zijininfo = $zijininfo->get($this->dbhelper);

                if (count($zijininfo) == 0){
                    alert_back("查无结果，请重新查询");
                }

                require_once 'phpexcel/Classes/PHPExcel.php';
                // Create new PHPExcel object
                $zijintj = new PHPExcel();

                // Set properties
                $zijintj->getProperties()->setCreator("TJ BYJJH")
                    ->setLastModifiedBy("TJ BYJJH")
                    ->setTitle("Office 2007 XLSX  Document")
                    ->setSubject("Office 2007 XLSX  Document")
                    ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("rescues");
                // Add some data
                $zijintj->setActiveSheetIndex(0)
                    ->setCellValue('A1', '序号')
                    ->setCellValue('B1', '项目名称')
                    ->setCellValue('C1', '项目捐赠者')
                    ->setCellValue('D1', '捐赠者类型')
                    ->setCellValue('E1', '捐赠级别')
                    ->setCellValue('F1', '项目捐赠类型')
                    ->setCellValue('G1', '捐赠用途')
                    ->setCellValue('H1', '捐赠到账日期')
                    ->setCellValue('I1', '捐赠到账金额')
                    ->setCellValue('J1', '资金来源渠道')
                    ->setCellValue('K1', '是否是校友')
                    ->setCellValue('L1', '配比状态')
                    ->setCellValue('M1', '票据状态')
                    ->setCellValue('N1', '证书状态')
                    ->setCellValue('O1', '捐赠单位介绍')
                    ->setCellValue('P1', '备注');

                $ii = 2;
                foreach($zijininfo as $v){
                    $zijintj->setActiveSheetIndex(0)
                        ->setCellValue('A'.$ii, $v['id'])
                        ->setCellValue('B'.$ii, $v['pm_name'])
                        ->setCellValue('C'.$ii, $v['pm_pp'])
                        ->setCellValue('D'.$ii, $v['pm_pp_cate'])
                        ->setCellValue('E'.$ii, $v['pm_juanzeng_jibie'])
                        ->setCellValue('F'.$ii, $this->getcateAction($this->pcatelist,$v['pm_juanzeng_cate']))
                        ->setCellValue('G'.$ii, $v['pm_juanzeng_yongtu'])
                        ->setCellValue('H'.$ii, $v['zijin_daozhang_datetime'])
                        ->setCellValue('I'.$ii, $v['zijin_daozheng_jiner'])
                        ->setCellValue('J'.$ii, $v['zijin_laiyuan_qudao'])
                        ->setCellValue('K'.$ii, $v['pm_is_school'])
                        ->setCellValue('L'.$ii, $v['peibi'])
                        ->setCellValue('M'.$ii, $v['piaoju'])
                        ->setCellValue('N'.$ii, $v['zhengshu'])
                        ->setCellValue('O'.$ii, $v['pm_pp_company'])
                        ->setCellValue('P'.$ii, $v['beizhu']);
                    $ii++;

                    $shouru += $v['zijin_daozheng_jiner'];
                }

                $hejixx = count($zijininfo) + 2;
                $heji = "合计";

                $zijintj->setActiveSheetIndex(0)->setCellValue('I'.$hejixx,$heji.$shouru);

                $ii = "";

                $zijintj->getActiveSheet()->setTitle('zijintongji');
                $zijintj->setActiveSheetIndex(0);

                ob_end_clean();
                ob_start();

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="资金统计报表.xls"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($zijintj, 'Excel5');
                $objWriter->save('php://output');
                exit;
            }catch (Exception $e){
                throw $e;
            }
		}
		
		//使用
		public function shiyongAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("report/shiyong.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		//使用统计toExcel
		public function shiyongtoexcelAction(){
			$pname = HttpUtil::postString("pname");
			$shiyong_zhichu_datetime = HttpUtil::postString("shiyong_zhichu_datetime");
			$shiyong_zhichu_datetime1 = HttpUtil::postString("shiyong_zhichu_datetime1");
			$shiyong_zhichu_jiner = HttpUtil::postString("shiyong_zhichu_jiner");
			$pminfo = new pm_mg_infoDAO();
			
			if($pname != ""){
				$pminfo ->pm_name = $pname;
			}
			
			if($shiyong_zhichu_jiner != ""){
				$pminfo ->shiyong_zhichu_jiner = $shiyong_zhichu_jiner;
			}
			
			if($shiyong_zhichu_datetime != "" && $shiyong_zhichu_datetime1 != ""){
				$pminfo ->selectLimit .= " and shiyong_zhichu_datetime between '$shiyong_zhichu_datetime' and '$shiyong_zhichu_datetime1' ";
			}
			$pminfo ->selectLimit .= " and cate_id = 1 order by id desc";
			//$pminfo ->debugSql =true;
			$pminfo = $pminfo->get($this->dbhelper);
			
			if (count($pminfo) == 0){
				alert_back("查无结果，请重新查询");
			}
			
			
			require_once 'phpexcel/Classes/PHPExcel.php';
			// Create new PHPExcel object
			$objPHPExcel2 = new PHPExcel();
			
			// Set properties
			$objPHPExcel2->getProperties()->setCreator("TJ BYJJH")
										 ->setLastModifiedBy("TJ BYJJH")
										 ->setTitle("Office 2007 XLSX  Document")
										 ->setSubject("Office 2007 XLSX  Document")
										 ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("rescues");


			// Add some data
			$objPHPExcel2->setActiveSheetIndex(0)
						->setCellValue('A1', '项目名称')
			            ->setCellValue('B1', '项目捐赠类型')
			            ->setCellValue('C1', '项目支出日期')
			            ->setCellValue('D1', '项目支出金额')
			            ->setCellValue('E1', '捐赠使用范围')
			            ->setCellValue('F1', '奖励人数')
			            ->setCellValue('G1', '备注');

			$iii = 2;
			
			foreach($pminfo as $v){
				$pm_cate = $this->getcateAction($this->pcatelist,$v['pm_juanzeng_cate']);
				$pm_cate = iconv("utf-8","gb2312",$pm_cate);
				
				
				$cid = $v['pm_juanzeng_cate'];
				$cateinfo = new jjh_mg_cateDAO();
				$cateinfo ->id = $cid;
				$cateinfo = $cateinfo ->get($this->dbhelper);

				$objPHPExcel2->setActiveSheetIndex(0)
						->setCellValue('A'.$iii, $v['pm_name'])
			            ->setCellValue('B'.$iii, $cateinfo[0]['catename'])
			            ->setCellValue('C'.$iii, $v['shiyong_zhichu_datetime'])
			            ->setCellValue('D'.$iii, $v['shiyong_zhichu_jiner'])
			            ->setCellValue('E'.$iii, $v['jiangli_fanwei'])
			            ->setCellValue('F'.$iii, $v['jiangli_renshu'])
			            ->setCellValue('G'.$iii, $v['beizhu']);
			            
			            $zhichushiyong += $v['shiyong_zhichu_jiner'];
			            $renshutongji += $v['jiangli_renshu'];
			    $iii++;
			}
			
			$heji123 = count($pminfo) + 2;
			$heji = "合计";
			
			$objPHPExcel2->setActiveSheetIndex(0)
						->setCellValue('D'.$heji123,$heji.$zhichushiyong)
						->setCellValue('F'.$heji123,$heji.$renshutongji);
			
			$iii = "";
			
			// Rename sheet
			$objPHPExcel2->getActiveSheet()->setTitle('shiyong');
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel2->setActiveSheetIndex(0);			
			// Redirect output to a client’s web browser (Excel5)
			
			//重要
			ob_end_clean();
			ob_start();
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="shiyongtongji.xls"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel2, 'Excel5');
			$objWriter->save('php://output');
			exit;
		}
		
		
		//项目收支统计
		public function pmshouzhiAction(){
			echo $this->view->render("index/header.phtml");
			echo $this->view->render("report/pmshouzhi.phtml");
			echo $this->view->render("index/footer.phtml");
		}
		
		//项目收支统计toExcel
		public function pmshouzhitoexcelAction(){
			$pname = HttpUtil::postString("pname");
			//$shiyong_zhichu_datetime = HttpUtil::postString("shiyong_zhichu_datetime");
			//$shiyong_zhichu_jiner = HttpUtil::postString("shiyong_zhichu_jiner");
		
			$pminfo = new pm_mg_infoDAO();
			
			if($pname != ""){
				$pminfo ->pm_name = $pname;
			}else{
				alert_back("请输入项目名称");
			}
			//$pminfo ->selectLimit = " and cate_id = 1 order by id desc";
			//$pminfo ->debugSql =true;

            $pminfo ->selectLimit .= " order by id";
			$pminfo = $pminfo->get($this->dbhelper);
			//var_dump($pminfo);exit;
			if (count($pminfo) == 0){
				alert_back("查无结果，请重新查询");
			}
			
			require_once 'phpexcel/Classes/PHPExcel.php';
			// Create new PHPExcel object
			$objPHPExcelx = new PHPExcel();
			
			// Set properties
			$objPHPExcelx->getProperties()->setCreator("TJ BYJJH")
										 ->setLastModifiedBy("TJ BYJJH")
										 ->setTitle("Office 2007 XLSX  Document")
										 ->setSubject("Office 2007 XLSX  Document")
										 ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("rescues");


			// Add some data
			$objPHPExcelx->setActiveSheetIndex(0)
						->setCellValue('A1', '项目名称')
			            ->setCellValue('B1', '项目进款日期')
			            ->setCellValue('C1', '项目进款金额')
			            ->setCellValue('D1', '项目支出日期')
			            ->setCellValue('E1', '项目支出金额')
			            ->setCellValue('F1', '奖励人数');

			$n = 2;
			foreach($pminfo as $v){			
				$objPHPExcelx->setActiveSheetIndex(0)
						->setCellValue('A'.$n, $v['pm_name'])
			            ->setCellValue('B'.$n, $v['zijin_daozhang_datetime'])
			            ->setCellValue('C'.$n, $v['zijin_daozheng_jiner'])
			            ->setCellValue('D'.$n, $v['shiyong_zhichu_datetime'])
			            ->setCellValue('E'.$n, $v['shiyong_zhichu_jiner'])
			            ->setCellValue('F'.$n, $v['jiangli_renshu']);
			    $n++;
			    
			    $shouru += $v['zijin_daozheng_jiner'];
			    $zhichu += $v['shiyong_zhichu_jiner'];
			    $renshu += $v['jiangli_renshu'];
			    $yuer = round(($shouru - $zhichu), 2);
			}
			
			$xx = count($pminfo) + 2;
			$heji = "合计";
			
			$objPHPExcelx->setActiveSheetIndex(0)
						->setCellValue('C'.$xx,"收入小计：".$shouru)
						->setCellValue('E'.$xx,"支出小计：".$zhichu)
						->setCellValue('F'.$xx,"奖励人数：".$renshu)
						->setCellValue('G'.$xx,"余额：".$yuer);			
			$n = "";
			
			// Rename sheet
			$objPHPExcelx->getActiveSheet()->setTitle('shouzhi');
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcelx->setActiveSheetIndex(0);			
			// Redirect output to a client’s web browser (Excel5)
			
			//重要
			ob_end_clean();
			ob_start();
			
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="pmshouzhi.xls"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcelx, 'Excel5');
			$objWriter->save('php://output');
			exit;
		}
		
		
		public function reportinfoAction(){
			//根据时间统计项目收支明细
			if($_REQUEST['style'] == "style1"){
				$start = $_REQUEST['start_datetime'];
				$end = $_REQUEST['end_datetime'];
				$pname = $_REQUEST['pname'];
				if($start != "" && $end != "" || $pname != ""){
					$selectSQL .= "select sum(i.zijin_daozheng_jiner) as daozhang,sum(i.shiyong_zhichu_jiner) as shiyong,i.pm_name,c.catename,sum(i.zijin_daozheng_jiner) as daozhang,sum(i.jiangli_renshu) as renshu  ";
					$selectSQL .= " from pm_mg_info as i ";
					$selectSQL .= " left join jjh_mg_cate as c on i.pm_juanzeng_cate = c.id where";
					
					if ($start != "" && $end != ""){
						$selectSQL .= " i.zijin_daozhang_datetime between '$start' and '$end' or i.shiyong_zhichu_datetime between '$start' and '$end' ";
					}

					if($start != "" && $end != "" && $pname != ""){
						$selectSQL .= " and i.pm_name = '$pname'";
					}
					
					if($start == "" && $end == "" && $pname != ""){
						$selectSQL .= " i.pm_name = '$pname'";
					}
					
					$selectSQL .= ' group by i.pm_name';
					
					$rss = $this->dbhelper->fetchAllData($selectSQL);
										
					$this->view->assign("reportinfo",$rss);
					$this->view->assign("start",$start);
					$this->view->assign("end",$end);
				
					//资金统计
					/*$zijin = new pm_mg_infoDAO();
					if($start != "" && $end != ""  || $pname != ""){
						$zijin ->selectLimit .= " and zijin_daozhang_datetime between '$start' and '$end' ";
					}
					$zijin ->selectLimit .= " and cate_id = 0 order by id desc";
					$zijin ->selectField("pm_name,zijin_daozheng_jiner");
					if($pname != ""){
						$zijin ->pm_name = $pname;
					}
					$zijin = $zijin->get($this->dbhelper);
					$this->view->assign("reportinfo",$zijin);
					
					var_dump($zijin);exit;*/

					
					//使用统计
					/*$pminfo = new pm_mg_infoDAO();
					if($start != "" && $end != ""){
						$pminfo ->selectLimit .= " and shiyong_zhichu_datetime between '$start' and '$end' ";
					}
					$pminfo ->selectLimit .= " and cate_id = 1 order by id desc";
					$pminfo ->selectField("pm_name,shiyong_zhichu_jiner,jiangli_renshu");
					$pminfo = $pminfo->get($this->dbhelper);
					
					$this->view->assign("shiyongqingkuang",$pminfo);*/					
					echo $this->view->render("report/report_form.phtml");
				}else {
                    echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                    echo('<script language="JavaScript">');
                    echo("alert('请输入查询条件');");
                    echo('history.back();');
                    echo('</script>');
                    exit;
				}
			}
		}
		
		//获取部门名称
		public function getdepartmentAction($departmentlist,$id){
			if($departmentlist != ""){
				foreach ($departmentlist as $v){
					if($v['id'] == $id){
						$department = $v['pname'];
					}
				}
			}
			return $department;
		}
		
		//获取项目分类名称
		public function getcateAction($catelist,$id){
			if($catelist != ""){
				foreach ($catelist as $v){
					if($v['id'] == $id){
						$cate = $v['catename'];
					}
				}
			}
			return $cate;
		}

        //回馈toExcel
        public function feedbacktoexcelAction(){
            $pname = $_REQUEST["pname"];
            $pm_mg_feedback = $this->orm->createDAO("pm_mg_feedback");
            if($pname != ""){
                $pm_mg_feedback ->findPm_name($pname);
            }
            $pm_mg_feedback = $pm_mg_feedback->get();

            if (count($pm_mg_feedback) == 0){
                alert_back("查无结果，请重新查询");
            }

            require_once 'phpexcel/Classes/PHPExcel.php';
            // Create new PHPExcel object
            $zijintj = new PHPExcel();

            // Set properties
            $zijintj->getProperties()->setCreator("TJ BYJJH")
                ->setLastModifiedBy("TJ BYJJH")
                ->setTitle("Office 2007 XLSX  Document")
                ->setSubject("Office 2007 XLSX  Document")
                ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("rescues");
            // Add some data
            $zijintj->setActiveSheetIndex(0)
                ->setCellValue('A1', '序号')
                ->setCellValue('B1', '项目id')
                ->setCellValue('C1', '项目名称 ')
                ->setCellValue('D1', '回馈时间')
                ->setCellValue('E1', '回馈方式')
                ->setCellValue('F1', '进度')
                ->setCellValue('G1', '回馈人')
                ->setCellValue('H1', '经办人');

            $ii = 2;
            foreach($pm_mg_feedback as $v){
                if($v['jindu'] == 1){
                    $v['jindu'] = "已回馈";
                }
                $zijintj->setActiveSheetIndex(0)
                    ->setCellValue('A'.$ii, $v['id'])
                    ->setCellValue('B'.$ii, $v['pm_id'])
                    ->setCellValue('C'.$ii, $v['pm_name'])
                    ->setCellValue('D'.$ii, $v['feedback_datetime'])
                    ->setCellValue('E'.$ii, $v['feedback_type'])
                    ->setCellValue('F'.$ii, $v['jindu'])
                    ->setCellValue('G'.$ii, $v['feedbacker'])
                    ->setCellValue('H'.$ii, $v['jbr']);
                $ii++;
            }
            $ii = "";

            $zijintj->getActiveSheet()->setTitle('huikui');
            $zijintj->setActiveSheetIndex(0);

            ob_end_clean();
            ob_start();

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="回馈统计.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($zijintj, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        //配比toExcel
        public function peibitoexcelAction(){
            $pname = $_REQUEST["pname"];
            $pm_mg_peibi = $this->orm->createDAO("pm_mg_peibi");
            if($pname != ""){
                $pm_mg_peibi ->findPm_name($pname);
            }
            $pm_mg_peibi = $pm_mg_peibi->get();

            if (count($pm_mg_peibi) == 0){
                alert_back("查无结果，请重新查询");
            }

            require_once 'phpexcel/Classes/PHPExcel.php';
            // Create new PHPExcel object
            $zijintj = new PHPExcel();

            // Set properties
            $zijintj->getProperties()->setCreator("TJ BYJJH")
                ->setLastModifiedBy("TJ BYJJH")
                ->setTitle("Office 2007 XLSX  Document")
                ->setSubject("Office 2007 XLSX  Document")
                ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("rescues");
            // Add some data
            $zijintj->setActiveSheetIndex(0)
                ->setCellValue('A1', '序号')
                ->setCellValue('B1', '项目id')
                ->setCellValue('C1', '项目名称 ')
                ->setCellValue('D1', '是否配比')
                ->setCellValue('E1', '是否通过配比')
                ->setCellValue('F1', '拒批原因')
                ->setCellValue('G1', '配比金额')
                ->setCellValue('H1', '配比下发时间')
                ->setCellValue('I1', '划拨部门')
                ->setCellValue('J1', '卡号')
                ->setCellValue('K1', '经费负责人')
                ->setCellValue('L1', '配比审批人');

            $ii = 2;
            foreach($pm_mg_peibi as $v){
                if($v['is_peibi'] == 1){
                    $v['is_peibi'] = "已配比";
                }else{
                    $v['is_peibi'] = "未配比";
                }
                if($v['is_pass'] == 1){
                    $v['is_pass'] = "通过";
                }else{
                    $v['is_pass'] = "未通过";
                }
                $zijintj->setActiveSheetIndex(0)
                    ->setCellValue('A'.$ii, $v['id'])
                    ->setCellValue('B'.$ii, $v['pm_id'])
                    ->setCellValue('C'.$ii, $v['pm_name'])
                    ->setCellValue('D'.$ii, $v['is_peibi'])
                    ->setCellValue('E'.$ii, $v['is_pass'])
                    ->setCellValue('F'.$ii, $v['jpyy'])
                    ->setCellValue('G'.$ii, $v['je'])
                    ->setCellValue('H'.$ii, $v['peibi_datetime'])
                    ->setCellValue('I'.$ii, $v['huabo_department'])
                    ->setCellValue('J'.$ii, $v['card'])
                    ->setCellValue('K'.$ii, $v['jffzr'])
                    ->setCellValue('L'.$ii, $v['peibi_spr']);
                $ii++;
            }
            $ii = "";

            $zijintj->getActiveSheet()->setTitle('huikui');
            $zijintj->setActiveSheetIndex(0);

            ob_end_clean();
            ob_start();

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="配比统计.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($zijintj, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        /**
         * @throws Exception 新的筹资统计
         */
        public function newchouzitoexcelAction(){
            $pname = $_REQUEST["pname"];
            $search_cate = $_REQUEST["cate"];
            $search_department_id = $_REQUEST["department_id"];
            $pm_mg_chouzi = $this->orm->createDAO("pm_mg_chouzi");
            if($pname != ""){
                $pm_mg_chouzi ->findPname($pname);
            }
            if($search_cate != ""){
                $pm_mg_chouzi ->findCate($search_cate);
            }
            if($search_department_id != ""){
                $pm_mg_chouzi ->findDepartment($search_department_id);
            }

            $pm_mg_chouzi ->withJjh_mg_cate(array("cate" => "id"));
            $pm_mg_chouzi ->withJjh_mg_department(array("department" => "id"));
            $pm_mg_chouzi ->select(" pm_mg_chouzi.*, jjh_mg_cate.catename, jjh_mg_department.pname as department_name");
            $pm_mg_chouzi = $pm_mg_chouzi->get();

            if (count($pm_mg_chouzi) == 0){
                // alert_back("查无结果，请重新查询");
                echo('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />');
                echo('<script language="JavaScript">');
                echo("alert('查无结果，请重新查询');");
                echo('history.back();');
                echo('</script>');
                exit;
            }

            require_once 'phpexcel/Classes/PHPExcel.php';
            // Create new PHPExcel object
            $zijintj = new PHPExcel();

            // Set properties
            $zijintj->getProperties()->setCreator("TJ BYJJH")
                ->setLastModifiedBy("TJ BYJJH")
                ->setTitle("Office 2007 XLSX  Document")
                ->setSubject("Office 2007 XLSX  Document")
                ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("rescues");
            // Add some data
            $zijintj->setActiveSheetIndex(0)
                ->setCellValue('A1', '父项目名称')
                ->setCellValue('B1', '项目名称')
                ->setCellValue('C1', '项目类型 ')
                ->setCellValue('D1', '所属部门')
                ->setCellValue('E1', '项目负责人')
				->setCellValue('F1', '电邮')
				->setCellValue('G1', '电话')
                ->setCellValue('H1', '项目联络人')
				->setCellValue('I1', '电邮')
				->setCellValue('J1', '电话')
                ->setCellValue('K1', '筹款负责人')
				->setCellValue('L1', '电邮')
				->setCellValue('M1', '电话')
                ->setCellValue('N1', '捐赠方')
				->setCellValue('O1', '电邮')
				->setCellValue('P1', '电话')
                ->setCellValue('Q1', '捐赠方联络人')
				->setCellValue('R1', '电邮')
				->setCellValue('S1', '电话')
                ->setCellValue('T1', '实际捐赠方')
				->setCellValue('U1', '电邮')
				->setCellValue('V1', '电话')
                ->setCellValue('W1', '实际捐赠方联络人')
				->setCellValue('X1', '电邮')
				->setCellValue('Y1', '电话')
                ->setCellValue('Z1', '项目起始年份')
                ->setCellValue('AA1', '项目截止年份')
                ->setCellValue('AB1', '协议捐赠金额')
                ->setCellValue('AC1', '是否留本');
                //->setCellValue('P1', '捐赠类别');

            $ii = 2;
            foreach($pm_mg_chouzi as $v){
                if($v['pm_liuben'] == 1){
                    $v['pm_liuben'] = "是";
                }else{
                    $v['pm_liuben'] = "否";
                }

                $p_pname = $this->findparentname($v['parent_pm_id']);
                $zijintj->setActiveSheetIndex(0)
                    ->setCellValue('A'.$ii, $p_pname)
                    ->setCellValue('B'.$ii, $v['pname'])
                    ->setCellValue('C'.$ii, $v['catename'])
                    ->setCellValue('D'.$ii, $v['department_name'])
                    ->setCellValue('E'.$ii, $v['pm_fzr'])
					->setCellValue('F'.$ii, $v['pm_fzr_email'])
					->setCellValue('G'.$ii, $v['pm_fzr_tel'])
                    ->setCellValue('H'.$ii, $v['pm_llr'])
					->setCellValue('I'.$ii, $v['pm_llr_email'])
					->setCellValue('J'.$ii, $v['pm_llr_tel'])
                    ->setCellValue('K'.$ii, $v['pm_ckfzr'])
					->setCellValue('L'.$ii, $v['pm_ckfzr_email'])
					->setCellValue('M'.$ii, $v['pm_ckfzr_tel'])
                    ->setCellValue('N'.$ii, $v['pm_jzf'])
					->setCellValue('O'.$ii, $v['pm_jzf_email'])
					->setCellValue('P'.$ii, $v['pm_jzf_tel'])
                    ->setCellValue('Q'.$ii, $v['pm_jzfllr'])
					->setCellValue('R'.$ii, $v['pm_jzfllr_email'])
					->setCellValue('S'.$ii, $v['pm_jzfllr_tel'])
                    ->setCellValue('T'.$ii, $v['pm_sjjzf'])
					->setCellValue('U'.$ii, $v['pm_sjjzf_email'])
					->setCellValue('V'.$ii, $v['pm_sjjzf_tel'])
                    ->setCellValue('W'.$ii, $v['pm_sjjzfllr'])
					->setCellValue('X'.$ii, $v['pm_sjjzfllr_email'])
					->setCellValue('Y'.$ii, $v['pm_sjjzfllr_tel'])
                    ->setCellValue('Z'.$ii, $v['pm_qishi_datetime'])
                    ->setCellValue('AA'.$ii, $v['pm_jiezhi_datetime'])
                    ->setCellValue('AB'.$ii, $v['pm_xieyi_juanzeng_jiner'])
                    ->setCellValue('AC'.$ii, $v['pm_liuben']);
                    //->setCellValue('P'.$ii, $v['peibi_spr']);
                $ii++;
            }
            $ii = "";

            $zijintj->getActiveSheet()->setTitle('pmListInfo');
            $zijintj->setActiveSheetIndex(0);

            ob_end_clean();
            ob_start();

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="项目基本信息.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($zijintj, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }

        public function findparentname($pid){
            if((int)$pid){
                $pm_mg_chouziDAO = $this->orm->createDAO("pm_mg_chouzi");
                $pm_mg_chouziDAO ->findId((int)$pid);
                $pm_mg_chouziDAO = $pm_mg_chouziDAO->get();
                return $pm_mg_chouziDAO[0]['pname'];
            }else {
                return "";
            }
        }

		
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkmanagement();
			
			//项目分类
			$pcatelist = new jjh_mg_cateDAO();
			$pcatelist =  $pcatelist ->get($this->dbhelper);
			$this->pcatelist = $pcatelist;
			$this->view->assign("pcatelist",$pcatelist);
			
			//所属部门
			$departmentlist = new jjh_mg_departmentDAO();
			$departmentlist = $departmentlist->get($this->dbhelper);
			$this->departmentlist = $departmentlist;
			$this->view->assign("departmentlist",$departmentlist);
			
			//项目名称列表
			$pm_chouzi = new pm_mg_chouziDAO();
            $pm_chouzi ->selectLimit .= " order by id desc";
			$pm_chouzi = $pm_chouzi ->get($this->dbhelper);
			$this->view->assign("pmlist",$pm_chouzi);
		}
	}
?>