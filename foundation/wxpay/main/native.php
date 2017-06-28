<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_All);

try {

    require_once "../lib/WxPay.Api.php";
    require_once "WxPay.NativePay.php";
    require_once 'log.php';

//模式一
    /**
     * 流程：
     * 1、组装包含支付信息的url，生成二维码
     * 2、用户扫描二维码，进行支付
     * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
     * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
     * 5、支付完成之后，微信服务器会通知支付成功
     * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
     */
//$notify = new NativePay();
//$url1 = $notify->GetPrePayUrl("123456789");

//模式二
    /**
     * 流程：
     * 1、调用统一下单，取得code_url，生成二维码
     * 2、用户扫描二维码，进行支付
     * 3、支付完成之后，微信服务器会通知支付成功
     * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
     */

    /**
     * 订单支付金额从db中获取
     */
    set_include_path('.' . PATH_SEPARATOR . '../../../library');
    require_once '../../configs.php';

    echo __BASEURL__; exit();

    $ORM = ORM::getInstance();
    $order_id = $_REQUEST['order_id'];
// 判断total_amount是否为订单到实际金额
    $ordersinfoDAO = $ORM->createDAO("jjh_orders_info");
    $ordersinfoDAO->findJjh_order_id($order_id);
    $ordersinfo = $ordersinfoDAO->get();

    if (emtpy($ordersinfo[0])) {
        echo '查无此订单，本次操作失败！';
        exit;
    }

    $input = new WxPayUnifiedOrder();
    $input->SetBody($_POST['WIDsubject']);
    $input->SetAttach($_POST['WIDsubject']);
    $input->SetOut_trade_no(WxPayConfig::MCHID . date("YmdHis"));
    $input->SetTotal_fee($ordersinfo[0]['WIDtotal_amount']);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag($_POST['WIDout_trade_no']);
    $input->SetNotify_url("http://202.113.6.233/main/notify.php");
    $input->SetTrade_type("NATIVE");
    $input->SetProduct_id($_POST['WIDout_trade_no']);
    $result = $notify->GetPayUrl($input);
    $url2 = $result["code_url"];

}catch(Exception $e){
    echo $e->getMessage();
    exit();
}
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例</title>
</head>
<body>
	<!--<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式一</div><br/>
	<img alt="模式一扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($url1);?>" style="width:150px;height:150px;"/>
	<br/><br/><br/>-->
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式二</div><br/>
	<img alt="模式二扫码支付" src="http://202.113.6.233/main/qrcode.php?data=<?php echo urlencode($url2);?>" style="width:150px;height:150px;"/>
	
</body>
</html>