<?php
ini_set('date.timezone','Asia/Shanghai');
//ini_set("display_errors", "On");
//error_reporting(E_ALL);

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
    $notify = new NativePay();
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

    $ORM = ORM::getInstance();
    $order_id = $_REQUEST['WIDout_trade_no'];
    // 判断total_amount是否为订单到实际金额
    $ordersinfoDAO = $ORM->createDAO("jjh_orders_info");
    $ordersinfoDAO->findJjh_order_id($order_id);
    $ordersinfo = $ordersinfoDAO->get();

    $input = new WxPayUnifiedOrder();
    $input->SetBody($ordersinfo[0]['jjh_donors_cname']);
    $input->SetAttach($ordersinfo[0]['jjh_donors_alumni']);
    $input->SetOut_trade_no(WxPayConfig::MCHID . date("YmdHis"));
    $input->SetTotal_fee($ordersinfo[0]['jjh_money']*100);
    $input->SetTime_start(date("YmdHis"));
    $input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag($ordersinfo[0]['jjh_order_id']);
    $input->SetNotify_url("http://pyedf.tju.edu.cn/wxpay/main/notify.php");
    $input->SetTrade_type("NATIVE");
    $input->SetProduct_id($ordersinfo[0]['jjh_order_id']);
    $result = $notify->GetPayUrl($input);
    $url2 = $result["code_url"];

    //var_dump($result);exit;

}catch(Exception $e){
    echo $e->getMessage();
    exit();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>天津大学北洋教育发展基金会-微信支付</title>
    <meta name="description" content="天津大学北洋教育发展基金会" />
    <meta name="keywords" content="天津大学，天津大学北洋教育发展基金会，基金会" />
    <link href="<?php echo __DEFAULTINCLUDEPATH__;?>css/style.css" rel="stylesheet"  type="text/css"/>
    <script src="<?php echo __DEFAULTINCLUDEPATH__;?>js/silder.js" type="text/javascript" language="javascript"></script>
    <script src="<?php echo __DEFAULTINCLUDEPATH__;?>js/tab.js" type="text/javascript"></script>
</head>
<body>
<?php
$controller_name = "donate";
?>
<div class="jjh_container">
    <div class="jjh_header">
        <div class="jjh_topadv"><img src="<?php echo __DEFAULTINCLUDEPATH__;?>images/topadv.jpg" /></div>
        <div class="jjh_nav">
            <ul>
                <li><a <?php if($controller_name == "index"){?>id="linkon"<?php }?> href="/">首页</a></li>
                <li><a <?php if($controller_name == "about" && $_GET['true'] != 1){?>id="linkon"<?php }?> href="/about">基金会概况</a></li>
                <li><a <?php if($controller_name == "news"){?>id="linkon"<?php }?> href="/news/index?cid=gonggao">新闻中心</a></li>
                <li><a <?php if($controller_name == "jjhpm"){?>id="linkon"<?php }?> href="/jjhpm">筹资项目</a></li>
                <li><a <?php if($controller_name == "pmuse"){?>id="linkon"<?php }?> href="/pmuse">捐赠使用</a></li>
                <li><a <?php if($controller_name == "about"  && $_GET['true'] == 1){?>id="linkon"<?php }?> href="/about?true=1">信息披露</a></li>
                <li><a <?php if($controller_name == "heart"){?>id="linkon"<?php }?> href="/heart">捐赠故事</a></li>
                <li><a <?php if($controller_name == "donate"){?>id="linkon"<?php }?> href="/donate">在线捐赠</a></li>
                <li><a <?php if($controller_name == "contact"){?>id="linkon"<?php }?> href="/contact">联系我们</a></li>
                <li style="background:none;"><a <?php if($controller_name == "en"){?>id="linkon"<?php }?> href="/en">English</a></li>
            </ul>
        </div>
    </div>
<!--<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式一</div><br/>
	<img alt="模式一扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($url1);?>" style="width:150px;height:150px;"/>
	<br/><br/><br/>-->

<div style="background-color: #FFFFFF;
    padding: 50px 0px;
    border-top: 4px solid #02c801;
    border-bottom: 4px solid #02c801;" class="center-box">

    <div class="center-box-inner" style="
    width: 760px;
    height: 422px;
    margin: 0 auto;
    background: url(http://pyedf.tju.edu.cn/include/default/images/pay_weixin_img01.jpg) right center no-repeat;">

        <div class="left-box" style="width: 300px;">
            <div class="tit-box">微信支付</div>
            <div class="code-box" style="border: 1px solid #02c801;">
                <div class="code-img" id="code_weixin" style="
                width: 255px;
                height: 255px;
                margin: 15px auto;">
                    <img alt="微信扫码支付" src="http://pyedf.tju.edu.cn/wxpay/main/qrcode.php?data=<?php echo urlencode($url2);?>" style="width:150px;height:150px;"/>
                </div>
                <div class="btm-box" style="    background-color: #02c801;
    color: #FFFFFF;
    text-align: center;
    padding: 10px 0px;">请使用微信扫一扫<br>扫描二维码支付</div>
            </div>
        </div>
    </div>

</div>

    <div class="jjh_footer">
        版权所有 &copy 天津大学<?php echo $this->websiteinfo[0]['web_copyright'];?> 北洋教育发展基金会   津ICP备05004358号    津教备0316号
    </div>

</div>

</body>
</html>