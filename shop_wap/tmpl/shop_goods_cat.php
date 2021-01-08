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
<link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
<link rel="stylesheet" type="text/css" href="../css/nctouch_store.css">
<link rel="stylesheet" href="../css/swiper.min.css">
<link rel="stylesheet" href="../css/iconfont.css">
</head>
<body>
<header id="header">
    <div class="header-wrap">
        <!-- <div class="header-l"> <a href="javascript:history.back()"> <i class="back"></i> </a> </div> -->
        <div class="header-inp clearfix"> <i class="icon"></i> <span class="search-input" id="keyword"><?= __('请输入关键字'); ?></span> </div>
    </div>
    <div class="nctouch-nav-layout">
        <div class="nctouch-nav-menu"> <span class="arrow"></span>
            <ul>
                <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                    <li><a href="../tmpl/store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                    
                <?php }else{ ?>
                    <li><a href="../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                    
                <?php }?>
                <li><a href="../tmpl/cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup></sup></a></li>
                <li><a href="../tmpl/member/member.html"><i class="member"></i><?= __('我的商城'); ?></a></li>
                <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
            </ul>
        </div>
    </div>
</header>
<div class="nctouch-main-layout">
    <div style="position: unset;" class="categroy-rgt" id="categroy-rgt"></div>
    <div class="pre-loading categroy-loading">
        <div class="pre-block">
            <div class="spinner"><i></i></div>
            <?= __('分类数据读取中'); ?>... </div>
    </div>
</div>

</body>
<script type="text/html" id="category-two">
    <dl class="categroy-child-list">
        <% for(var i=0; i<items.length; i++) { var col = i % 10;%>
        <dt><a href="<%= WapSiteUrl %>/tmpl/product_list.html?shop_goods_cat_id=<%= items[i].shop_goods_cat_id %>"><span><%= items[i].shop_goods_cat_name %></span></a></dt>
        <dd>
            <% for(var j=0; j<items[i].child.length; j++) { %>
            <a href="<%= WapSiteUrl %>/tmpl/product_list.html?shop_goods_cat_id=<%= items[i].child[j].shop_goods_cat_id %>">
                    <p><%= items[i].child[j].shop_goods_cat_name %></p>
                </a>
           
                
            <% } %>
        </dd>
        <% } %>
    </dl>
</script>
    <script type="text/html" id="store_voucher_con_tpl">
            <div class="nctouch-bottom-mask-bg"></div>
            <div class="nctouch-bottom-mask-block">
                <div class="nctouch-bottom-mask-tip"><i></i><?= __('点击此处返回'); ?></div>
                <div class="nctouch-bottom-mask-top store-voucher">
                    <i class="icon-store"></i><?= __('领取店铺代金券'); ?><a href="javascript:void(0);" class="nctouch-bottom-mask-close"><i></i></a>
                </div>
                <div class="nctouch-bottom-mask-rolling">
                    <div class="nctouch-bottom-mask-con">
                        <ul class="nctouch-voucher-list">
                            <% var voucher_list = voucher.items %> <% if(voucher_list.length > 0){ %> <% for (var i=0; i < voucher_list.length; i++) { var v = voucher_list[i]; %>
                            <li>
                                <dl>
                                    <dt class="money"><?= __('面额'); ?><em><%=v.voucher_t_price %></em><?= __('元'); ?></dt>
                                    <dd class="need"><?= __('需消费'); ?> <%=v.voucher_t_limit %><?= __('元使用'); ?></dd>
                                    <dd class="time"><?= __('至'); ?> <%=v.voucher_t_end_date %><?= __('前使用'); ?></dd>
                                </dl>
                                <a href="javascript:void(0);" nc_type="getvoucher" class="btn" data-tid="<%=v.voucher_t_id%>"><?= __('领取'); ?></a>
                            </li>
                            <% } %> <% }else{ %>
                            <div class="nctouch-norecord voucher" style="position: relative; margin: 3rem auto; top: auto; left: auto; text-align: center;">
                                <div class="norecord-ico"><i></i></div>
                                <dl style="margin: 1rem 0 0;">
                                    <dt style="color: #333;"><?= __('暂无代金券可以领取'); ?></dt>
                                    <dd><?= __('店铺代金券可享受商品折扣'); ?></dd>
                                </dl>
                            </div>
                            <% } %>
                        </ul>
                    </div>
                </div>
            </div>
	</script>


<?php 
        include __DIR__.'/../includes/footer_menu.php';
?>

<!-- <?= __('底部'); ?> -->
<script type="text/javascript" src="../js/zepto.min.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/swiper.min.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/iscroll.js"></script>
<script type="text/javascript" src="../js/ncscroll-load.js"></script>
<script type="text/javascript" src="../js/shop-goods-cat.js"></script>
<?php 
include __DIR__.'/../includes/footer.php';
?>

</body>
</html>


