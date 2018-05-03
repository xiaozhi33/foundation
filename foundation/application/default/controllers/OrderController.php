<?php
	require_once("BaseController.php");
	require_once("../util/httputil.php");
	require_once("../util/sessionutil.php");
	
	class orderController extends BaseController {
		private $dbhelper;
		public function indexAction(){
			echo $this->view->render("order/index.phtml");
		}
		
		public function addorderAction(){
			$cate_1 = HttpUtil::postInsString("cate_1");
			$cate_2 = HttpUtil::postInsString("cate_2");
			$cate_3 = HttpUtil::postInsString("cate_3");
			
/* 			//旧版
			if ($cate_1 == ""){
				alert_back("请选择要捐赠项目的名称！");
			}
			if($cate_3 != ""){
				$jjh_pm2 = $this ->getcnameAction($cate_2);
				$jjh_pm3 = $this ->getcnameAction($cate_3);
				$jjh_pm = $jjh_pm2[0]['c_name']."--".$jjh_pm3[0]['c_name'];
				$jjh_cid = $cate_3;
			}elseif ($cate_2 != "" && $cate_3 == ""){
				$jjh_pm2 = $this ->getcnameAction($cate_2);
				$jjh_pm = $jjh_pm2[0]['c_name'];
				$jjh_cid = $cate_2;
			}elseif ($cate_2 == "" && $cate_3 == ""){
				$jjh_pm1 = $this ->getcnameAction($cate_1);
				$jjh_pm = $jjh_pm1[0]['c_name'];
				$jjh_cid = $cate_1;
			}
 */			
			//新版2.0更新
			$cate_id_info = HttpUtil::postInsString("cate_id_info");
            $cate_id_info = (int)$cate_id_info;
			if($cate_id_info == ""){
				alert_back("请选择要捐赠项目的名称！");
			}

			$jjh_pm_info = $this ->getcnameAction($cate_id_info);
			$jjh_pm = $jjh_pm_info[0]['c_name'];

			$Amount = HttpUtil::postInsString("Amount");
			if ($Amount == "" || $Amount < 10){
				alert_back("请添加要捐赠项目的捐赠总额！并金额不少于10元");
			}
			if (is_int((int)$Amount) == false){
				alert_back("金额必须是正整数。");
			}
			$Amount = (int)$Amount;
			$SpecialRequest = HttpUtil::postInsString("SpecialRequest"); //捐赠说明
			$EventName = HttpUtil::postInsString("EventName"); 			 //相关活动
			$zs = HttpUtil::postInsString("zs");						 //是否需要证书
			$sj = HttpUtil::postInsString("sj");						 //是否要收据
			$UserName = HttpUtil::postInsString("UserName");			 //捐赠人名称
			$Gender = HttpUtil::postInsString("Gender");				 //捐赠人称谓
			$AlumniInfo = HttpUtil::postInsString("AlumniInfo");		 //是否校友
			$Email = HttpUtil::postInsString("Email");					 //邮箱
			$Phone = HttpUtil::postInsString("Phone");					 //联系电话
			$Mobile = HttpUtil::postInsString("Mobile");				 //移动电话
			$Address = HttpUtil::postInsString("Address");				 //联系地址
			$Postcode = HttpUtil::postInsString("Postcode");			 //邮编
			$CareerInfo = HttpUtil::postInsString("CareerInfo");		 //工作单位
			$IsAnonymous = HttpUtil::postInsString("IsAnonymous");		 //是否匿名捐赠
			$PayType = HttpUtil::postInsString("PayType");		 		 //捐赠方式
			
			if($PayType == 2){
				$this->_redirect("/donate/fangshi");exit;
			}

			if ( $Amount > 100000){
				alert_go("感谢您的捐赠，由于捐款数额过大，为了保证安全，请以电汇,转账等方式捐赠。或者咨询校方，电话：0086－22－27403247。","/donate/fangshi");
			}
			
			if ($UserName == ""){
				alert_back("请填写捐赠人的姓名！");
			}
			if ($Phone == ""){
				alert_back("请填写捐赠人的联系电话！");
			}
			if ($Email == ""){
				alert_back("请填写捐赠人的联系电话！");
			}
			if ($Mobile == ""){
				alert_back("请填写捐赠人的移动电话！");
			}

			SessionUtil::sessionSet(array(
					'orderinfo' => array(
								'jjh_pm' => $jjh_pm,
								'jjh_cid' => $jjh_cid,
								'Amount' => $Amount,
								'SpecialRequest' => $SpecialRequest,
								'EventName' => $EventName,
								'zs' => $zs,
								'sj' => $sj,
								'UserName' => $UserName,
								'Gender' => $Gender,
								'AlumniInfo' => $AlumniInfo,
								'Email' => $Email,
								'Phone' => $Phone,
								'Mobile' => $Mobile,
								'Address' => $Address,
								'Postcode' => $Postcode,
								'CareerInfo' => $CareerInfo,
								'IsAnonymous' => $IsAnonymous,
								'PayType' => $PayType
					)));
					
			//$var = sprintf("%06d", 12);
			$orderid = rand(100,99999);
			$jjh_order_id = date("Ymd",time())."-5653-".sprintf("%06d", $orderid)."-".date("hms",time());
			$jjh_orders = new jjh_ordersDAO();
			$jjh_orders ->jjh_activities = $SpecialRequest;
			$jjh_orders ->jjh_cate_id = $jjh_cid;
			$jjh_orders ->jjh_donors_alumn = $AlumniInfo;
			$jjh_orders ->jjh_order_id = $jjh_order_id;  //订单生成日期(yyyymmdd)-商户编号-报名号-6位时间（hhmmss）
			$jjh_orders ->jjh_pm = $jjh_pm;
			$jjh_orders ->jjh_rdero_datetime = date("Y-m-d h:m:s",time());
			$jjh_orders ->save($this->dbhelper);
			
			$jjh_orders_info = new jjh_orders_infoDAO();
			$jjh_orders_info ->jjh_activities = $EventName;
			$jjh_orders_info ->jjh_content = $SpecialRequest;
			$jjh_orders_info ->jjh_donors_alumni = $AlumniInfo;
			$jjh_orders_info ->jjh_donors_cname = $jjh_pm;
			$jjh_orders_info ->jjh_donors_company_position = $CareerInfo;
			$jjh_orders_info ->jjh_donors_mobile = $Mobile;
			$jjh_orders_info ->jjh_donors_name = $UserName;
			$jjh_orders_info ->jjh_donors_phone = $Phone;
			$jjh_orders_info ->jjh_donors_zip = $Postcode;
			$jjh_orders_info ->jjh_donors_email = $Email;
			$jjh_orders_info ->jjh_is_shouju = $sj;
			$jjh_orders_info ->jjh_is_zhengshu = $zs;
			$jjh_orders_info ->jjh_money = $Amount;
			$jjh_orders_info ->jjh_order_id = $jjh_order_id;
			$jjh_orders_info ->jjh_pm = $jjh_pm;
			$jjh_orders_info ->jjh_price_cate = $PayType;
			$jjh_orders_info ->save($this->dbhelper);
			
			$jjh_datetime = date("Ymd",time());   //当前日期
			//生成数字签名；
			//$sourcedata = v_moneytype v_ymd v_amount v_rcvname v_oid v_mid v_url
			//$sourcedata="0".$jjh_datetime.$Amount.$UserName.$jjh_order_id."5653".__BASEURL__."/order/urlreturn";
			
			
			$Amount = iconv("UTF-8","gb2312",$Amount);
			$UserName = iconv("UTF-8","gb2312",$UserName);
			$jjh_order_id = iconv("UTF-8","gb2312",$jjh_order_id);

			$sourcedata="0".$jjh_datetime.$Amount.$UserName.$jjh_order_id."5653"."http://pyedf.tju.edu.cn/order/urlreturn";
		    $MD5Key="zwnf88cyf88yy888";   //发邮件huangyi@payeasenet.com 公司名、商户号、联系人、密钥
			//exec("./forlinux $sourcedata $MD5Key",$result,$res);
			//变量$result中即为MD5签名结果
			//var_dump($sourcedata);
		
			$MD5Key = iconv("UTF-8","gb2312",$MD5Key);
			$result = $this->hmac($MD5Key,$sourcedata);
			
			//var_dump($result);
			//exit;
			
			$this->view->assign("result",$result);
			$orderinfo = SessionUtil::sessionGet("orderinfo");
			$this->view->assign("orderinfo",$orderinfo);
			$this->view->assign("jjh_order_id",$jjh_order_id);
			$this->view->assign("jjh_datetime",$jjh_datetime);
            $this->view->assign("PayType",$PayType);
			echo $this->view->render("donate/message.phtml");
		}
		
		public function getcnameAction($c_id){
			$my_cate = new my_categoryDAO($c_id);
			$my_cate ->selectField(" c_name");
			$my_cate_cname = $my_cate ->get($this->dbhelper);
			return $my_cate_cname;
		}
		
		public function  hmac ($key, $data){
			// 创建 md5的HMAC

			$b = 64; // md5加密字节长度
			if (strlen($key) > $b) {
			$key = pack("H*",md5($key));
			}
			$key  = str_pad($key, $b, chr(0x00));
			$ipad = str_pad('', $b, chr(0x36));
			$opad = str_pad('', $b, chr(0x5c));
			$k_ipad = $key ^ $ipad;
			$k_opad = $key ^ $opad;

			return md5($k_opad  . pack("H*",md5($k_ipad . $data)));
		}
		
		public function urlreturnAction(){
			//在每次发送时，我们将以七个参数
			//（v_count、v_oid、v_pmode、v_pstatus、v_pstring、v_amount、v_moneytype）
			//表示订单相关内容，另外附加三个数字指纹字段（v_mac、v_md5money、v_sign）用于以上订单信息的校验。
			
			$request_info = new jjh_request_infoDAO();
			$request_info ->request_json = json_encode($_REQUEST);
			$request_info ->request_datetime = date("Y-m-d h:i:s",time());
			$request_info ->save($this->dbhelper);
			
			$v_count = $_REQUEST['v_count'];         //本次发送的订单个数；（最少为1，最大为4）
			$v_oid = $_REQUEST['v_oid'];             //定义同商户提交待付款订单接口中的订单编号定义；
			$v_pmode = $_REQUEST['v_pmode'];         //支付方式中文说明，如“中行长城信用卡”。
			$v_pstatus = $_REQUEST['v_pstatus'];     //支付结果，0→待处理（支付结果未确定）； 1支付完成；3支付被拒绝； 
			$v_pstring  = $_REQUEST['v_pstring'];    //对支付结果的说明，成功时（v_pstatus=1）为“支付成功”，支付被拒绝时（v_pstatus=3）为失败原因。
			$v_amount = $_REQUEST['v_amount'];       //订单实际支付金额
			$v_moneytype = $_REQUEST['v_moneytype'];    //订单实际支付币种
			
			$v_sign = $_REQUEST['v_sign'];  //商城数据签名，参与签名的数据（v_oid+v_pstatus+v_amount+v_moneytype+v_count）
			
			$my_orderinfo = new jjh_ordersDAO($v_oid);
			$my_orderinfo = $my_orderinfo ->get($this->dbhelper);
			
			if($my_orderinfo != ""){
				$orderinfo = new jjh_ordersDAO($v_oid);
				$orderinfo ->jjh_order_statue = $v_pstatus;
				$orderinfo ->save($this->dbhelper);
				
				$order_info = new jjh_orders_infoDAO($v_oid);
				$order_info ->jjh_money = $v_amount;
				$order_info ->save($this->dbhelper);
				echo "捐赠已成功！感谢您对天津大学的支持，5个工作日内会有工作人员与您联系！";
			}else{
				echo "error";
			}
			
			//$sourcedata=$PayType.$jjh_datetime.$Amount.$UserName.$jjh_order_id."5653".__BASEURL__."/order/urlreturn";
			//$MD5Key="zwnf88cyf88yy888";   
			//exec("./MD5Win32 $sourcedata $MD5Key",$result,$res);
			//$this->view->assign("result",$result);
		}
		
		public function _init(){
			$this->dbhelper = new DBHelper();
			$this->dbhelper ->connect();
			$lifeTime = 1 * 3600;
			session_set_cookie_params($lifeTime);   //设置session过期时间
			SessionUtil::sessionStart();
		}
	}
?>
