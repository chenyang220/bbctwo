<?php 
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
    <title><?= __('积分列表'); ?></title>
    <link rel="stylesheet" type="text/css" href="../css/base.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_products_list.css">
</head>
<body>
    <style type="text/css">
        .nctouch-main-layout-a {
            display: block;
            top: -0.1rem;
            right: 0;
            left: 0;
            bottom: 0;
            /* overflow: hidden; */
            z-index: 1;
            position: absolute;
            bottom: 0;
            -webkit-overflow-scrolling: touch;
        }
    </style>
<!-- <header id="header" class="nctouch-product-header fixed"> -->
    <!-- <div class="header-wrap"> -->
        <!-- <div class="header-l"> <a href="javascript:history.go(-1)"> <i class="back"></i> </a> </div> -->
        <!-- <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup style="display: inline;"></sup></a> </div> -->
    <!-- </div> -->
    <!-- <div class="nctouch-nav-layout"> -->
<!--         <div class="nctouch-nav-menu"> <span class="arrow"></span>
            <ul>
                <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                    <li><a href="../tmpl/store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                   
                <?php }else{ ?>
                    <li><a href="../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                   
                <?php }?>
                <li><a href="../tmpl/cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup style="display: inline;"></sup></a></li>
                <li><a href="../tmpl/member/member.html"><i class="member"></i><?= __('我的商城'); ?></a></li>
                <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
            </ul>
        </div>
    </div> -->
<!-- </header> -->
<div class="goods-search-list-nav">
    <ul id="nav_ul">
        <li><a href="javascript:void(0);" class="current" id="sort_default"><?= __('默认排序'); ?><i></i></a></li>
        <li><a href="javascript:void(0);" class="" id="grade_default"><?= __('会员等级'); ?><i></i></a></li>
        <li><a href="javascript:void(0);" id="search_adv"><?= __('筛选'); ?><i></i></a></li>
    </ul>
    <div class="browse-mode"><a href="javascript:void(0);" id="show_style"><span class="browse-list"></span></a></div>
</div>
<div id="sort_inner" class="goods-sort-inner hide">
    <span><a href="javascript:void(0);" onclick="init_get_list('order', 'default')"><?= __('默认排序'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('order', 'stimedesc')"><?= __('最近上架'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('order', 'pointsasc')"><?= __('积分值从低到高'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('order', 'pointsdesc')"><?= __('积分值从高到低'); ?><i></i></a></span>
</div>
<div id="grade_inner" class="goods-sort-inner hide">
    <span><a href="javascript:void(0);" onclick="init_get_list('grade', 0)"><?= __('会员等级'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('grade', 1)"><?= __('注册会员'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('grade', 2)"><?= __('铜牌会员'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('grade', 3)"><?= __('银牌会员'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('grade', 4)"><?= __('金牌会员'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('grade', 5)"><?= __('钻石会员'); ?><i></i></a></span>
</div>
<!--<?= __('筛选部分'); ?>-->
<div class="nctouch-full-mask hide">
    <div class="nctouch-full-mask-bg"></div>
    <div class="nctouch-full-mask-block">
<!--         <div class="header" style="display: block;">
            <div class="header-wrap"> -->
     <!--            <div class="-headerl"> <a href="javascript:void(0);"><i class="back"></i></a></div>
                <div class="header-title">
                    <h1><?= __('积分商品筛选'); ?></h1>
                </div> -->
<!--                 <div class="header-r"><a href="javascript:void(0);" id="reset" class="text"><?= __('重置'); ?></a> </div>
        </div> -->
        <!-- </div> -->
        <div class="nctouch-main-layout-a secreen-layout" id="list-items-scroll" ><div></div></div>
    </div>
</div>
<div class="nctouch-main-layout mt40 mb20">
    <div id="product_list" class="list">
        <ul class="goods-secrch-list">
        </ul>
    </div>
</div>
<div class="fix-block-r">
    <a href="javascript:void(0);" class="gotop-btn gotop" id="goTopBtn"><i></i></a>
</div>
<script type="text/html" id="integral_product_list">
    <% if (points_goods) { %>
    <% var items = points_goods.items %>
    <% for ( var i in items ) { %>
    <li class="goods-item integral-list">
				<span class="goods-pic">
					<a href="integral_product_detail.html?id=<%= items[i].id %>">
		                <img src="<%= items[i].points_goods_image %>">
		            </a>
				</span>
        <dl class="goods-info">
            <dt class="goods-name">
                <a href="integral_product_detail.html?id=<%= items[i].id %>">
                    <h4><%= items[i].points_goods_name %></h4>
                </a>
            </dt>
            <dd class="goods-sale">
                <span class="goods-price"><?= __('所需积分：'); ?><em><%= items[i].points_goods_points %></em></span>

            </dd>
            <dd class="goods-assist">
                <span><?= __('参考价格：￥'); ?><%= items[i].points_goods_price %></span>
            </dd>
        </dl>
    </li>
    <% } %>
    <% } %>
</script>

<script type="text/html" id="search_items">
    <div>
        <dl>
            <dt  id="p-headerl"  style="color: #FE244B"><i class="back"></i><?= __('< 返回'); ?></dt>
        </dl>
        <dl>
            <dt><?= __('所需积分'); ?></dt>
            <dd>
                <span class="inp-balck"><input type="text" id="points_min" nctype="points" pattern="[0-9]*" class="inp" placeholder="<?= __('最低积分'); ?>"/></span>
                <span class="line"></span>
                <span class="inp-balck"><input type="text" id="points_max" nctype="points" pattern="[0-9]*" class="inp" placeholder="<?= __('最高积分'); ?>"/></span>
            </dd>
        </dl>
<!--        <dl>-->
<!--            <dt><?= __('兑换范围'); ?></dt>-->
<!--            <dd>-->
<!--                <a href="javascript:void(0);" nctype="items" id="is_self" class=""><?= __('只看我能兑换'); ?></a>-->
<!--            </dd>-->
<!--        </dl>-->
        <div class="bottom">
            <a href="javascript:void(0);" class="btn-l" id="search_submit"><?= __('筛选积分商品'); ?></a>
        </div>
    </div>
</script>


<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/tmpl/integral_product_list.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
</body>
</html>
<?php 
include __DIR__.'/../includes/footer.php';
?>