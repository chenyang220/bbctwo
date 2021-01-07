<?php
    include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="msapplication-tap-highlight" content="no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="Author" contect="U2FsdGVkX1+liZRYkVWAWC6HsmKNJKZKIr5plAJdZUSg1A==">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit:cover;">
    <title><?= __('显示折扣列表'); ?></title>
    <link rel="stylesheet" type="text/css" href="../css/base.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_products_list.css">
    <style type="text/css">
        .nctouch-full-mask.left {
            left: 25%;
        }
        
        .nctouch-main-layout-a {
            top: 0;
        }
        
        .secreen-layout .bottom {
            padding: 0.5rem 0;
        }
        
        #reset {
            background: #70696a;
        }
    </style>
</head>
<body>
<header id="header" class="nctouch-product-header fixed">
    <div class="header-wrap">
        <!--<?= __('返回上一级页面'); ?>---<?= __('首页'); ?>-->
       <!--  <div class="header-l">
            <a href="javascript:history.back(-1)"> <i class="back"></i> </a>
        </div> -->
        <!--<?= __('点击进入搜索页面'); ?>-->
<!--        <div class="header-inp clearfix">-->
<!--            <i class="icon"></i> <span class="search-input" id="keyword"><?= __('请输入关键词'); ?></span>-->
<!--        </div>-->
        <div class="header-title posr" id="discount_title"></div>
        <!--<?= __('快捷入口'); ?>-->
        <div class="header-r">
            <a href="../tmpl/product_first_categroy.html" class="categroy"><i></i> </a> 
            <!-- <a id="header-nav" href="javascript:void(0);"> <i class="more"></i> <sup></sup> </a> -->
        </div>
    
    </div>
    <!--<?= __('展示入口列表'); ?>-->
    <div class="nctouch-nav-layout">
        <div class="nctouch-nav-menu"><span class="arrow"></span>
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
<!--<?= __('排序条件'); ?>-->
<div class="goods-search-list-nav">
    <ul id="nav_ul">
        <li><a href="javascript:void(0);" class="current" id="sort_default"><?= __('综合排序'); ?><i></i></a></li>
        <li class="browse-mode">
            <a href="javascript:void(0);" id="show_style"> <span class="browse-grid"></span> </a>
        </li>
    </ul>
</div>

<div id="sort_inner" class="goods-sort-inner hide">
    <span><a href="javascript:void(0);" class="cur" onclick="init_get_list('', '')"><?= __('综合排序'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('uptime', 'DESC')"><?= __('上架时间'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" onclick="init_get_list('price', 'DESC')"><?= __('折扣'); ?><i></i></a></span>
</div>

<div class="nctouch-main-layout module-top-space">
    <div id="product_list" class="list">
        <ul class="goods-secrch-list"></ul>
    </div>
</div>


<div class="fix-block-r">
    <!--<a href="member/views_list.html" class="browse-btn"><i></i></a>-->
    <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
</div>

<footer id="footer" class="bottom"></footer>

<!--<?= __('筛选部分'); ?>-->
</body>
<script type="text/html" id="home_body">
    <% var goods_list = data.items; %>
    <% if(goods_list.length >0){%>
        <%for(i=0;i < goods_list.length;i++){%>
        <li class="goods-item" goods_id="<%=goods_list[i].goods_id;%>">
                    <span class="goods-pic">
                        <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>">
                            <img src="<%=goods_list[i].goods_image;%>"/>
                        </a>
                    </span>



            <% if(goods_list[i].goods_stock<=0){  %>
            <div style="position:absolute;left:0px;top:0px;background:#666666;width:100%;height:100%;filter:alpha(opacity=60); opacity:0.6; z-Index:5;"></div>
            <div id="modal" style=" position:absolute;width:100%;height:100%;opacity:0.6;cursor:pointer;z-Index:6;top:0;font-size: 35px;border:1px solid #fff"><i style="font-family: 宋体;font-weight: 900;background: #ffffff;display: block; color: red;width: 30%;margin: auto;margin-top: 2rem;margin-right:2rem;-webkit-transform: rotate(25deg);-moz-transform: rotate(25deg);filter: progid:DXImageTransform.Microsoft.BasicImage(Rotation=0.45);">已售罄</i></div>
            <dl class="goods-info">
                <dt class="goods-name">
                    <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>">
                        <% if(goods_list[i].goods_name){ var name=goods_list[i].goods_name }else{ var name=goods_list[i].discount_name } %>
                        <h4><%=name;%></h4>
                    </a>
                </dt>
                <dd class="goods-sale">
                    <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>">
                        <span class="goods-price"><?= __('￥'); ?><em><%=goods_list[i].discount_price;%></em></span>
                </dd>
                <dd class="goods-assist">
                    <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
                        <span class="goods-sold" style="width:70%"><?= __('开始时间'); ?>
                            <em><%=goods_list[i].goods_start_time;%></em>
                    </span>
                    </a>
                </dd>
            </dl>
            <% }else{ %>
            <dl class="goods-info">
                <dt class="goods-name">
                    <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>">
                        <% if(goods_list[i].goods_name){ var name=goods_list[i].goods_name }else{ var name=goods_list[i].discount_name } %>
                        <h4><%=name;%></h4>
                    </a>
                </dt>
                <dd class="goods-sale">
                    <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>">
                        <span class="goods-price"><?= __('￥'); ?><em><%=goods_list[i].discount_price;%></em></span>
                </dd>
                <dd class="goods-assist">
                    <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
                        <span class="goods-sold" style="width:70%"><?= __('开始时间'); ?>
                            <em><%=goods_list[i].goods_start_time;%></em>
                    </span>
                    </a>
                </dd>
            </dl>

            <% } %>







        </li>
        <%}%>
    <li class="loading">
        <div class="spinner"><i></i></div>
        <?= __('商品数据读取中'); ?>...
    </li>
    <%}else {%>
    <div class="nctouch-norecord search">
        <div class="norecord-ico"><i></i></div>
        <dl>
            <dt><?= __('没有找到任何相关信息'); ?></dt>
            <dd><?= __('选择或搜索其它商品分类'); ?>/<?= __('名称'); ?>...</dd>
        </dl>
        <a href="javascript:history.go(-1)" class="btn"><?= __('重新选择'); ?></a>
    </div>
    <%}%>
</script>

<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/discount_list.js"></script>
<!--<script type="text/javascript" src="../js/footer.js"></script>-->
</html>
<?php
    include __DIR__ . '/../includes/footer.php';
?>
