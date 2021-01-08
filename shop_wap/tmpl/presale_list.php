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
    <title><?= __('商品列表'); ?></title>
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
        .make_money{
            display: block;
    background: #FF3333;
   /* padding: 0.3rem 1rem;*/
   padding-left: 0.8rem;
    /* float: right; */
    position: absolute;
    top: 65%;
    right: 0;
    font-size: 0.5rem;
    text-align: right;
    padding-right: 0.5rem;
    color: #fff;
    border-top-left-radius: 1rem;
    border-bottom-left-radius: 1rem;
        }
    </style>
</head>
<body>
<header id="header" class="nctouch-product-header fixed">
    <div class="header-wrap">
        <!--<?= __('返回上一级页面'); ?>---<?= __('首页'); ?>-->
        <div class="header-l">
            <!-- <a href="javascript:history.back(-1)"> <i class="back"></i> </a> -->
        </div>
        <!--<?= __('点击进入搜索页面'); ?>-->
        <div class="header-inp clearfix">
            <i class="icon"></i> <span class="search-input" id="keyword"><?= __('请输入关键词'); ?></span>
        </div>
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
        <li><a href="javascript:void(0);" class="" onclick="init_get_list('sale', 'DESC')"><?= __('销量优先'); ?></a></li>
        <li class="browse-mode">
            <a href="javascript:void(0);" id="show_style"> <span class="browse-grid"></span> </a>
        </li>
    </ul>
    <div class="ser-adv"><a href="javascript:void(0);" id="search_adv"><?= __('筛选'); ?><i></i></a></div>
</div>

<div id="sort_inner" class="goods-sort-inner hide">
    <span><a href="javascript:void(0);" class="cur" onclick="init_get_list('', '')"><?= __('综合排序'); ?><i></i></a></span> <span><a href="javascript:void(0);" onclick="init_get_list('evaluate', 'DESC')"><?= __('评价排序'); ?><i></i></a></span> <span><a href="javascript:void(0);" onclick="init_get_list('price', 'DESC')"><?= __('价格从高到低'); ?><i></i></a></span> <span><a href="javascript:void(0);" onclick="init_get_list('price', 'ASC')"><?= __('价格从低到高'); ?><i></i></a></span>
</div>

<div class="nctouch-main-layout module-top-space">
    <div id="product_list" class="list">
        <ul class="goods-secrch-list"></ul>
    </div>
</div>
<!--<?= __('筛选部分'); ?>-->
<div class="nctouch-full-mask hide JS-search">
    <div class="nctouch-full-mask-bg"></div>
    <div class="nctouch-full-mask-block">
        <div class="header" style="display: none;">
            <div class="header-wrap">
                <div class="header-l"><a href="javascript:void(0);"><i class="back"></i></a></div>
                <div class="header-title">
                    <h1><?= __('商品筛选'); ?></h1>
                </div>
                <div class="header-r"><a href="javascript:void(0);" id="reset" class="text reset"><?= __('重置'); ?></a></div>
            </div>
        </div>
        <div class="nctouch-main-layout-a secreen-layout" id="list-items-scroll">
            <div></div>
        </div>
    </div>
</div>

<div class="fix-block-r">
    <!--<a href="member/views_list.html" class="browse-btn"><i></i></a>-->
    <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
</div>

<footer id="footer" class="bottom"></footer>

<script type="text/html" id="search_items">
    <div>
        <dl>
            <dt><?= __('店铺类型'); ?></dt>
            <dd>
                <a href="javascript:void(0);" nctype="items" id="own_shop" class=""><?= __('平台自营'); ?></a> <a href="javascript:void(0);" nctype="items" id="other_shop" class=""><?= __('入驻店铺'); ?></a>
            </dd>
        </dl>
        <dl>
            <dt><?= __('商品类型'); ?></dt>
            <dd>
                <a href="javascript:void(0);" nctype="items" id="actgoods"><?= __('促销'); ?></a> <a href="javascript:void(0);" nctype="items" id="virtual"><?= __('虚拟'); ?></a>
            </dd>
        </dl>
        <dl>
            <dt><?= __('价格区间'); ?></dt>
            <dd>
                    <span class="inp-balck">
                        <input type="text" id="price_from" nctype="price" pattern="[0-9]*"
                               class="inp" placeholder="<?= __('最低价'); ?>"/></span> <span class="line"></span> <span class="inp-balck"><input nctype="price" type="text" id="price_to" pattern="[0-9]*" class="inp"
                                                                                                                                placeholder="<?= __('最高价'); ?>"/></span>
            </dd>
        </dl>
        <div class="bottom">
            <a href="javascript:void(0);" class="btn-l reset" id="reset"><?= __('重置'); ?></a>
        </div>
        <div class="bottom">
            <a href="javascript:void(0);" class="btn-l" id="search_submit"><?= __('筛选'); ?></a>
        </div>
    </div>
</script>
<!--<?= __('筛选部分'); ?>-->
</body>
<script type="text/html" id="home_body">
    <% var common_list = data.items; %><% if(common_list.length >0){%><%for(j=0;j < common_list.length;j++){%><%  var goods_list = common_list[j].good; var pos = (data.page-1)*pagesize+1+j;%><% if(typeof(goods_list)!=='undefined' && goods_list.length >0){%><%for(i=0;i<1;i++){%>
    <li class="goods-item" goods_id="<%=goods_list[i].goods_id;%>" id="goods_pos_<%=pos;%>">
				<span class="goods-pic">
					<a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
                        <img src="<%= image_cdn(goods_list[i].goods_image); %>"/>
                        <% if(common_list[j].promotion_type=='presale'){%>
                        <p style="background:red;" class="old-Failed  old-Failed-wh"><?= __('预售'); ?></p>
                        <% } %>
                    </a>
				</span>
        <dl class="goods-info" style="position: relative;">
            <dt class="goods-name">
                <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
                    <h4><%=goods_list[i].goods_name;%></h4>
                    <h6><%=goods_list[i].goods_jingle;%></h6>
                </a>
            </dt>
            <dd class="goods-sale">
                <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
							<span class="goods-price"><?= __('￥'); ?><em><%=goods_list[i].goods_price;%></em>
								<%
									if (goods_list[i].sole_flag) {
								%>
									<span class="phone-sale"><i></i><?= __('手机专享'); ?></span>
								<%
									}
								%>
							</span>
                    
                    <% if (goods_list[i].is_virtual == '1') { %> <span class="sale-type"><?= __('虚拟'); ?></span> <% } else { %> <% if (goods_list[i].is_presell == '1') { %> <span class="sale-type"><?= __('预'); ?></span> <% } %> <% if (goods_list[i].is_fcode == '1') { %> <span class="sale-type">F</span> <% } %> <% } %>
                    
                    <% if(goods_list[i].group_flag || goods_list[i].xianshi_flag){ %> <span class="sale-type"><?= __('降'); ?></span> <% } %> <% if(goods_list[i].have_gift == '1'){ %> <span class="sale-type"><?= __('赠'); ?></span> <% } %> </a>
                    <!-- 3.6.7-plus会员价 -->
                    <%if(common_list[j].plus_status){%>
                     <em class="plus-pri"><?= __('￥'); ?><%=common_list[j].plus_price;%></em><b class="plus-logo"></b>
                    <%}%>
            </dd>
            <dd class="goods-assist">
                <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
                    <span class="goods-sold"><?= __('销量'); ?>
                        <em><%=common_list[j].common_salenum;%></em>
                    </span>
                    <span class="goods-sold"><?= __('评论'); ?>
                        <em><%=common_list[j].common_evaluate;%></em>
                    </span>
                </a>
                <!--<div class="goods-store">
                    <% if (goods_list[i].is_own_shop == '1') { %> <span class="mall"><?= __('自营'); ?></span> <% } else { %>
                    <a href="javascript:void(0);" data-id='<%=goods_list[i].shop_id;%>'><%=goods_list[i].store_name;%><i></i></a> <% } %>
                    <div class="sotre-creidt-layout" style="display: none;"></div>
                </div>-->
            </dd>
           <% if (common_list[j].common_is_directseller == '1' && data.distributor_open==1) { %>
             <span class="make_money">
                <li>分享赚</li>
                <% if (data.distributor_type==1) { %>
                    <li>￥<%=common_list[j].common_a_first%></li>
                <% }else{ %>
                    <li>￥<%=common_list[j].common_c_first%></li>
                <% } %>
            </span>
            <% } %>
        </dl>
       
    </li><%}%><%}%><% } %>
    <li class="loading">
        <div class="spinner"><i></i></div>
        <?= __('商品数据读取中'); ?>...
    </li><%}else {%>
    <div class="nctouch-norecord search">
        <div class="norecord-ico"><i></i></div>
        <dl>
            <dt><?= __('没有找到任何相关信息'); ?></dt>
            <dd><?= __('选择或搜索其它商品分类'); ?>/<?= __('名称'); ?>...</dd>
        </dl>
        <a href="javascript:history.go(-1)" class="btn"><?= __('重新选择'); ?></a>
    </div><%}%>
</script>

<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/product_list.js"></script>
<!--<script type="text/javascript" src="../js/footer.js"></script>-->
</html>
<?php
    include __DIR__ . '/../includes/footer.php';
?>
