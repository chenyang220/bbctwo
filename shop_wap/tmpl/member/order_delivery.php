<?php 
include __DIR__.'/../../includes/header.php';
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('物流信息'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
</head>
<body>
<header id="header">
    <div class="header-wrap">
        <!-- <div class="header-l"> <a href="javascript:history.go(-1)"> <i class="back"></i> </a> </div> -->
        <div class="header-title">
            <h1><?= __('物流信息'); ?></h1>
        </div>
        <div class="header-r"><a href="javascript:void(0)" onClick="location.reload();"><i class="refresh"></i></a></div>
    </div>
</header>
<div class="nctouch-main-layout" id="order-delivery"><div class="loading"><div class="spinner"><i></i></div><?= __('物流信息读取中'); ?>...</div></div>
<div class="nctouch-delivery-tip wp80"><?= __('以上部分信息来自于第三方，仅供参考'); ?><br/>
    <?= __('如需准确信息可联系卖家或物流公司'); ?></div>
<footer id="footer"></footer>
<script type="text/html" id="order-delivery-tmpl">
    <% if (err) { %>
    <div class="no-record m10 tc mt60 fz8"><%= err %></div>
    <% } else { %>
    <div class="nctouch-order-deivery-info">
        <i class="icon"></i>
        <dl>
            <dt><?= __('物流公司：'); ?><%= express_name %></dt>
            <dd><?= __('运单号码：'); ?><%= shipping_code %></dd>
        </dl>
    </div>
    <div class="nctouch-order-deivery-con">
        <ul>
            <% for (var i in deliver_info) { %>
            <li><span><i></i></span><%= deliver_info[i].time %>&nbsp;&nbsp;<%= deliver_info[i].context %></li>
            <% } %>
        </ul>
    </div>
    <% } %>
</script>
<script type="text/html" id="delivery-tmpl">
    <div class="nctouch-order-deivery-info">
        <i class="icon"></i>
        <dl style="height: 100%!important;">
            <dt>商家自主配送，商品已发出</dt>
            <dt><?= __('联系人：'); ?><%= deliver_info.contact_name %></dt>
            <dd><?= __('联系方式：'); ?><%= deliver_info.contact_mobile %></dd>
            <dd><?= __('备注：'); ?><%= deliver_info.contact_remarks %></dd>
        </dl>
    </div>
</script>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>

<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<script type="text/javascript" src="../../js/tmpl/order_delivery.js"></script>
</body>
</html>

<?php 
include __DIR__.'/../../includes/footer.php';
?>