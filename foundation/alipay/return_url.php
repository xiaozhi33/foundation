<?php
/* *
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */

require_once("config.php");
require_once 'pagepay/service/AlipayTradeService.php';


$arr=$_GET;
$alipaySevice = new AlipayTradeService($config);
$result = $alipaySevice->check($arr);

// 记录return_url详情
/*ini_set("display_errors", "On");
error_reporting(E_ALL);*/

set_include_path('.' .PATH_SEPARATOR .'../../library');
require_once '../configs.php';
$ORM = ORM::getInstance();

// 根据订单号查询项目详情
$orderDAO = $ORM->createDAO("jjh_orders_info")->findJjh_order_id(htmlspecialchars($_GET['out_trade_no']))->get();
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>天津大学北洋教育发展基金会</title>
    <meta name="description" content="天津大学北洋教育发展基金会" />
    <meta name="keywords" content="天津大学，天津大学北洋教育发展基金会，基金会" />

    <link href="/include/default/css/style.css" rel="stylesheet"  type="text/css"/>
    <script src="/include/default/js/silder.js" type="text/javascript" language="javascript"></script>
    <script src="/include/default/js/tab.js" type="text/javascript"></script>

</head>
<body>

<?php
//$front = Zend_Controller_Front::getInstance();
//$controller_name = $front->getRequest()->getControllerName();
//var_dump($controller_name);
?>

<div class="jjh_container">
    <div class="jjh_header">
        <div class="jjh_topadv"><img src="/include/default/images/topadv.jpg" /></div>
        <div class="jjh_nav">
            <ul>
                <li><a <?php if($controller_name == "index"){?>id="linkon"<?php }?> href="/">首页</a></li>
                <li><a <?php if($controller_name == "about" && $_GET['true'] != 1){?>id="linkon"<?php }?> href="/about">基金会概况</a></li>
                <li><a <?php if($controller_name == "news"){?>id="linkon"<?php }?> href="/news/index?cid=xinwen">新闻中心</a></li>
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

    <div class="jjh_content_sub" style="height:500px;">
        <div class="jjh_content_sub_left">
            <div class="jjh_sub_topimg"><img src="<?php echo __DEFAULTINCLUDEPATH__;?>images/donate_top_img.jpg" /></div>
            <div class="jjh_sub_nav_tit"><span>在线捐赠</span></div>
            <div class="jjh_sub_nav_con">
                <ul>
                    <li><a href="/donate/fangshi">捐赠方式</a></li>
                    <li><a href="/donate/zhanghu"><span>基金会账户</span></a></li>
                    <li><a href="/donate/mianshui">免税政策</a></li>
                    <li><a href="/donate/shuoming"><span>捐赠说明</span></a></li>
                    <li><a href="/donate">我要捐赠</a></li>
                    <!--<li><a href="/donate/query">捐款到账查询</a></li>
                    --><li><a href="/donate/wuxie">捐赠鸣谢</a></li>
                </ul>
            </div>
            <div><img src="<?php echo __DEFAULTINCLUDEPATH__;?>images/serline.jpg" /></div>
        </div>
        <div class="jjh_content_sub_right">
            <div class="jjh_sub_con_tit"><span>当前位置：<a href="/donate">在线捐赠</a> </span><em>在线捐赠</em></div>
            <div class="cle"></div>
            <div class="jjh_sub_con_content" style="height:320px;">

                <?php
                if($result) {//验证成功
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //请在这里加上商户的业务逻辑程序代码

                    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
                    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

                    //商户订单号
                    $out_trade_no = htmlspecialchars($_GET['out_trade_no']);

                    //付款总金额
                    $total_amount = htmlspecialchars($_GET['total_amount']);

                    //支付宝交易号
                    $trade_no = htmlspecialchars($_GET['trade_no']);

                    //交易状态
                    $trade_status = $_GET['trade_status'];


                    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                        //判断该笔订单是否在商户网站中已经做过处理
                        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                        //如果有做过处理，不执行商户的业务程序

                        // 纪录订单交易状态 0 交易失败 -1 退款
                        $orders = $ORM->createDAO("jjh_orders");
                        $orders ->findJjh_order_id($out_trade_no);
                        $orders ->jjh_order_statue = 1;
                        $orders ->save();
                    }
                    else {
                        //echo "trade_status=".$_GET['trade_status'];
                    }

                    //echo "验证成功<br />";
                    echo '<p class="jjh_font4" style="padding:10px 0 0px 0;">捐赠项目名称：<strong>'.$orderDAO[0]['jjh_donors_cname'].'</strong></p>';
                    echo '<p class="jjh_font4">订单号：'.$orderDAO[0]['jjh_order_id'].'</p>';
                    echo '<p class="jjh_font4">订单金额：'.$orderDAO[0]['jjh_money'].' 元</p>';
                    echo '<p class="jjh_font3" style="padding:10px 0 10px 0;font-size: 16px;">支付完成！感谢您的捐赠。</p>';
                    //mecho '<script type="text/javascript">window.location.href="/";</script>';


                    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                }
                else {

                    // 纪录订单交易状态 0 交易失败 -1 退款
                    $orders = $ORM->createDAO("jjh_orders");
                    $orders ->findJjh_order_id($out_trade_no);
                    $orders ->jjh_order_statue = 1;
                    $orders ->save();

                    //验证失败
                    //如要调试，请看alipay_notify.php页面的verifyReturn函数
                    //echo "验证失败";
                    echo '<p class="jjh_font4" style="padding:10px 0 10px 0;">订单:'.$out_trade_no.'支付失败，订单状态异常。</p>';
                    //echo '<script type="text/javascript">window.location.href="/";</script>';
                }
                ?>



            </div>
        </div>
    </div>

    <div class="jjh_footer">
        版权所有 &copy 天津大学<?php echo $this->websiteinfo[0]['web_copyright'];?> 北洋教育发展基金会   津ICP备05004358号    津教备0316号
    </div>
</div>

</body>
</html>