<?php
session_cache_limiter("private, must-revalidate");
session_start();
include __DIR__.'/../includes/header.php';
?>
<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="msapplication-tap-highlight" content="no">
<meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit:cover;">
<title><?= __('商品分类'); ?></title>
<link rel="stylesheet" href="../css/footer.css">
<link rel="stylesheet" type="text/css" href="../css/base.css">
<link rel="stylesheet" type="text/css" href="../css/nctouch_categroy.css">
<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_y8zleljop7c.css">
<link rel="stylesheet" type="text/css" href="../css/style2.css?v=3">
<link rel="stylesheet" href="../css/iconfont.css">
<link rel="stylesheet" href="../css/swiper.min.css">
</head>
<body style="background:#fff">

    <div class="nctouch-main-layout" style="margin-top: 2px;">
        <div class="category-lists pb-4"></div>
        
        </div>
    </div>



<script type="text/html" id="category-list">
    <% if(data.length > 0) {%>
    <div class="zk-hy-module bgf">
        <% for(var i = 0;i<data.length;i++){ %>
            <dl class="zk-hy-store-dl">
                <dt> 
                    <a href="<%= WapSiteUrl %>/tmpl/product_list.html?cat_id=<%= data[i].cat_id%>" class="clearfix">
                        <span style="color: #333;"><strong style="font-weight: 700;"><%= data[i].cat_name%></strong></span>
                    </a>
                </dt>
                <dd>
                    <ul class="clearfix">
                        <% var son =  data[i].son; for(var j=0; j<son.length; j++) { %>
                            <li>
                                <a href="<%= WapSiteUrl %>/tmpl/product_list.html?cat_id=<%= son[j].cat_id%>"><em class="img-box"><img class="cter" src="<%= son[j].cat_pic%>" alt=""></em>
                                <span class="block"><%= son[j].cat_name%></span></a>
                            </li>
                        <% } %>
                    </ul>
                </dd>
            </dl>
        <% } %>
    </div>
    <% } else { %>
     <div class="nctouch-norecord order">
            <div class="norecord-ico"><i></i></div>
            <dl>
                <dt><?= __('您还没有相关的订单'); ?></dt>
                <dd><?= __('可以去看看哪些想要买的'); ?></dd>
            </dl>
            <a href="<%=WapSiteUrl%>" class="btn"><?= __('随便逛逛'); ?></a>
        </div>
    <% } %>
</script>
<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/iscroll.js"></script>
<script type="text/javascript" src="../js/swiper.min.js"></script>
<script type="text/javascript" src="../js/categroy-frist-list.js?v=91"></script>


</body></html>
<?php 
include __DIR__.'/../includes/footer.php';
?>