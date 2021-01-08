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
    <link rel="stylesheet" type="text/css" href="../css/style2.css?v=3">
    <link rel="stylesheet" href="../css/iconfont.css">
<script src="../js/jquery.js"></script>
<script src="../js/jquery.lazy.js"></script>
</head>
<body>
<header id="header">
    <div class="header-wrap">
        <!-- <div class="header-l"> <a href="javascript:history.back()"> <i class="back"></i> </a> </div> -->
        <div class="header-inp clearfix"> <i class="icon"></i> <span class="search-input" id="keyword"><?= __('请输入关键字'); ?></span> </div>
        <!-- <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a> </div> -->
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
    <div class="categroy-cnt" id="categroy-cnt"></div>
    <div class="categroy-rgt" id="categroy-rgt"></div>
    <div class="pre-loading categroy-loading">
        <div class="pre-block">
            <div class="spinner"><i></i></div>
            <?= __('分类数据读取中'); ?>... </div>
    </div>
</div>


</body>
<script type="text/html" id="category-one">
    <ul class="categroy-list">
        <?php if(!$_COOKIE['SHOP_ID_WAP']){?>
        <li class="category-item">
            <a href="javascript:void(0);" class="category-item-a brand">
                <div class="ci-fcategory-name"><?= __('品牌推荐'); ?></div>
            </a>
        </li>
        <?php }?>
        <% for(var i = 0;i<items.length;i++){ %>
        <li class="category-item">
            <a href="javascript:void(0);" class="category-item-a category" date-id="<%= items[i].cat_id %>">
                <div class="ci-fcategory-name one-overflow"><%= items[i].cat_name %></div>
            </a>
        </li>
        <% } %>
    </ul>
</script>
<script type="text/html" id="category-two">
    <dl class="categroy-child-list">
        <% for(var i=0; i<items.length; i++) { var col = i % 10;%>
        <dt><a href="<%= WapSiteUrl %>/tmpl/product_list.html?cat_id=<%= items[i].cat_id %>"><span><%= items[i].cat_name %></span></a></dt>
        <dd>
            <% for(var j=0; j<items[i].child.length; j++) { %>
            <a href="<%= WapSiteUrl %>/tmpl/product_list.html?cat_id=<%= items[i].child[j].cat_id %>">
                    <% if(!items[i].child[j].cat_image){
                        items[i].child[j].cat_image = '../images/icons/category.png';
                    } %>
                    <em class="img-box"><image src="<%= items[i].child[j].cat_image %>"/></em>
                    <p><%= items[i].child[j].cat_name %></p>
                </a>
           
                
            <% } %>
        </dd>
        <% } %>
    </dl>
</script>
<script type="text/html" id="brand-one">
    <dl class="categroy-child-list">
        <dd>
        <% for(var i = 0;i<items.length;i++){ %>
        
            <a href="<%= WapSiteUrl %>/tmpl/product_list.html?brand_id=<%= items[i].brand_id %>">
                <% if(items[i].brand_pic.length>0){ %>
                <!-- <em class="img-box"><img class="lazy" data-original="<%= items[i].brand_pic %>"></em> -->
                <em class="img-box"><img class="lazy" src="<%= items[i].brand_pic %>"></em>
                <% }else{ %>
               <!--  <em class="img-box"><img class="lazy" data-original="<%= WapSiteUrl %>/images/new/brand.png"></em> -->
                <em class="img-box"><img class="lazy" src="<%= WapSiteUrl %>/images/new/brand.png"></em>
                <%}%>
                <p><%= items[i].brand_name %></p>

            </a>
        
        <% } %>
        </dd>
    </dl>
</script>
<!-- <?= __('底部'); ?> -->
<?php 
        include __DIR__.'/../includes/footer_menu.php';
?>
<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/iscroll.js"></script>
<script type="text/javascript" src="../js/categroy-frist-list.js"></script>

</body></html>
<?php 
include __DIR__.'/../includes/footer.php';
?>