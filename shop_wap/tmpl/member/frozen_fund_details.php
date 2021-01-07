<?php
include __DIR__ . '/../../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="wap-font-scale" content="no">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('冻结资金明细'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    <link rel="stylesheet" href="../../css/iconfont.css">
</head>
<body>
	<header id="header" class="fixed bgf">
	    <div class="header-wrap">
	        <!-- <div class="header-l absolute"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col3 fz-40"></b></a></div> -->
	        <div class="header-title posr">
	            <h1 id="z-tab-order" data-order_state="all">冻结资金明细</h1>
	        </div>
	    </div>
	</header>
	<div class="nctouch-main-layout bgf pl-20 pr-20 box-size">
		<div class="tc fund-det-top">
			<span class="block">冻结资金（元）</span>
			<strong>30.02</strong>
		</div>
		<ul class="fund-det-ul">
			<li>
				<p><span>订单号：DD-10015-11-102-20200710135741-0001</span></p>
				<p><span>时间：2020-04-01 05:54</span></p>
				<p><span class="wp50">付款金额：<b>￥66.00</b></span><span>结算佣金：<b>￥6.00</b></span></p>
			</li>
			<li>
				<p><span>订单号：DD-10015-11-102-20200710135741-0001</span></p>
				<p><span>时间：2020-04-01 05:54</span></p>
				<p><span class="wp50">付款金额：<b>￥66.00</b></span><span>结算佣金：<b>￥6.00</b></span></p>
			</li>
			<li>
				<p><span>订单号：DD-10015-11-102-20200710135741-0001</span></p>
				<p><span>时间：2020-04-01 05:54</span></p>
				<p><span class="wp50">付款金额：<b>￥66.00</b></span><span>结算佣金：<b>￥6.00</b></span></p>
			</li>
			<li>
				<p><span>订单号：DD-10015-11-102-20200710135741-0001</span></p>
				<p><span>时间：2020-04-01 05:54</span></p>
				<p><span class="wp50">付款金额：<b>￥66.00</b></span><span>结算佣金：<b>￥6.00</b></span></p>
			</li>
		</ul>
		
	</div>
</body>
</html>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>

<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>