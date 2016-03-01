<?php
	require_once("BaseController.php");
	class Admin_donateController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			//导出excel文件
			if(HttpUtil::getString("excel")=="yes"){
				$jjh_order = new jjh_ordersDAO();
				$jjh_order ->joinTable(" left join jjh_orders_info on jjh_orders_info.jjh_order_id = jjh_orders.jjh_order_id");
				$jjh_order ->selectField(" jjh_orders.*,jjh_orders.jjh_order_id as oid,jjh_orders_info.*");
				$jjh_order->selectLimit .= " order by oid desc";
				$jjh_order = $jjh_order ->get($this->dbhelper);
				$this->ordertoexcelAction($jjh_order);
				exit;
			}

			if(HttpUtil::postString("orderid")!=""){
				$jjh_order = new jjh_ordersDAO(HttpUtil::postString("orderid"));
			}else {
				$jjh_order = new jjh_ordersDAO();
			}

			if(HttpUtil::postString("uname")!=""){
				$uname = HttpUtil::postString("uname");
				$jjh_order->selectLimit = " and jjh_orders_info.jjh_donors_name like '%$uname%'";
			}
			$jjh_order ->joinTable(" left join jjh_orders_info on jjh_orders_info.jjh_order_id = jjh_orders.jjh_order_id");
			$jjh_order ->selectField(" jjh_orders.*,jjh_orders.jjh_order_id as oid,jjh_orders_info.*");
			$jjh_order->selectLimit .= " order by oid desc";
			//$jjh_order->debugSql = true;
			$jjh_order = $jjh_order->get($this->dbhelper);

			$total = count($jjh_order);
			$pageDAO = new pageDAO();
			$pageDAO = $pageDAO ->pageHelper($jjh_order,null,"index",null,'get',30,30);						
			$pages = $pageDAO['pageLink']['all'];
			$pages = str_replace("/index.php","",$pages);	
			$this->view->assign('jjh_order',$pageDAO['pageData']);
			$this->view->assign('page',$pages);	
			$this->view->assign('total',$total);
			
			echo $this->view->render('donate/index.phtml');
		}
		
		//toExcel
		public function ordertoexcelAction($array=""){
			//导出excel
			require_once 'phpexcel/Classes/PHPExcel.php';
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			$objPHPExcel->getProperties()->setCreator("TJDX BYJJH")
										 ->setLastModifiedBy("TJDX BYJJH")
										 ->setTitle("Office 2007 XLSX  Document")
										 ->setSubject("Office 2007 XLSX  Document")
										 ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("rescues");


			// Add some data
			$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A1', '订单号')
			            ->setCellValue('B1', '捐赠项目名称')
			            ->setCellValue('C1', '捐赠金额')
			            ->setCellValue('D1', '捐赠人')
			            ->setCellValue('E1', '捐赠时间')
			            ->setCellValue('F1', '付款状态')
			            ->setCellValue('G1', '捐赠人电话')
			            ->setCellValue('H1', '捐赠人手机')
			            ->setCellValue('I1', '捐赠人电子邮箱')
			            ->setCellValue('J1', '校友信息')
			            ->setCellValue('K1', '是否需要证书')
			            ->setCellValue('L1', '是否匿名捐赠')
			            ->setCellValue('M1', '是否需要收据');

			$i = 2;
			foreach($array as $v){
				if ($v['jjh_order_statue']==0){$__statue="未付款";}elseif ($v['jjh_order_statue']!=0 && $v['jjh_order_statue']!="-1" ){$__statue="已付款";}
				elseif ($v['jjh_order_statue']=="-1"){$__statue="已退款";}
				$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A'.$i, $v['jjh_order_id'])
			            ->setCellValue('B'.$i, $v['jjh_pm'])
			            ->setCellValue('C'.$i, $v['jjh_money'])
			            ->setCellValue('D'.$i, $v['jjh_donors_name'])
			            ->setCellValue('E'.$i, $v['jjh_rdero_datetime'])
			            ->setCellValue('F'.$i, $__statue)
			            ->setCellValue('G'.$i, $v['jjh_donors_phone'])
			            ->setCellValue('H'.$i, $v['jjh_donors_mobile'])
			            ->setCellValue('I'.$i, $v['jjh_donors_email'])
			            ->setCellValue('J'.$i, $v['jjh_donors_alumn'])
			            ->setCellValue('K'.$i, $v['jjh_is_zhengshu'])
			            ->setCellValue('L'.$i, $v['jjh_donors_is_anonymous'])
			            ->setCellValue('M'.$i, $v['jjh_is_shouju']);

			    $i++;
			}
			$i = "";
			
			//var_dump($array);exit;
			/* var_dump($this->departmentlist);
			var_dump($this->pcatelist);
			exit;*/
			
			// Rename sheet
			$objPHPExcel->getActiveSheet()->setTitle('统计');

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="在线捐赠订单报表.xls"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
		}
		
		public function editdonateAction(){
			if(HttpUtil::getString("id") != ""){
				$jjh_ordersDAO = new jjh_ordersDAO(HttpUtil::getString("id"));
				$jjh_ordersDAO ->joinTable(" left join jjh_orders_info on jjh_orders_info.jjh_order_id = jjh_orders.jjh_order_id");
				$jjh_ordersDAO ->selectField(" jjh_orders.*,jjh_orders.jjh_order_id as oid,jjh_orders_info.*");
				$jjh_ordersDAO ->selectLimit .= " order by oid desc";
				$jjh_ordersDAO = $jjh_ordersDAO->get($this->dbhelper);
				$this->view->assign('jjh_order',$jjh_ordersDAO);
				//var_dump($jjh_ordersDAO);exit();
				echo $this->view->render('donate/editdonate.phtml');
			}else {
				alert_back("无此信息");
			}
		}
		
		public function editdonatersAction(){
			if (HttpUtil::postString("id")!=""){
				$jjh_ordersDAO = new jjh_ordersDAO(HttpUtil::postString("id"));
				$jjh_ordersDAO ->jjh_order_statue = HttpUtil::postString("statue");
				$jjh_ordersDAO ->save($this->dbhelper);
				alert_go("订单".HttpUtil::postString("id")."状态修改成功","/admin/donate");
			}else {
				alert_back("您的操作有误！");
			}
		}
	
		public function _init(){
			$this ->dbhelper = new DBHelper();
			$this ->dbhelper ->connect();
			SessionUtil::sessionStart();
			SessionUtil::checkadmin();
		}
	}
?>