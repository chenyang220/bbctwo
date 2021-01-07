<?php
    include __DIR__ . '/../includes/header.php';
?>
    <!doctype html>
    <html>
    
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-touch-fullscreen" content="yes"/>
        <meta name="format-detection" content="telephone=no"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
        <meta name="format-detection" content="telephone=no"/>
        <meta name="msapplication-tap-highlight" content="no"/>
        <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1"/>
        <title><?= __('店铺首页'); ?></title>
        <link rel="stylesheet" type="text/css" href="../css/base.css">
        <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
        <link rel="stylesheet" type="text/css" href="../css/nctouch_store.css">
		<link rel="stylesheet" href="../css/swiper.min.css">
        <link rel="stylesheet" href="../css/iconfont.css">
    </head>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script>
    <script type="text/javascript">
        var url = '../store/store?shop_id='+<?php echo $_GET['shop_id']?>; 
        wx.miniProgram.redirectTo({url:url})
    </script>
    <body class="module-industry">
    <header id="header" class="transparent absolute">
        <div class="header-wrap wap-store-index-head">
            <?php if(!$_COOKIE['SHOP_ID_WAP']){ ?>
            <div class="header-l">
            <a href="../index.html"><b class="iconfont icon-arrow-left colf"></b></a></div>
            <?php }?>
            <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
            <a style="font-size: 0.545rem; float: left; color:#999;margin-left: 10px;
    margin-top: 7px;" href="javascript:void(0); " class="logbtn">登录</a>
            <?php }?>    
            <a class="header-inp clearfix" id="goods_search" href="">
                <i class="icon"></i>
                <span class="search-input colbc"><?= __('搜索店铺内商品'); ?></span>
            </a>
            <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                <a style="font-size: 0.545rem; float: right; color:#999;margin-right: 28px;margin-top: -34px;" href="./shop_goods_cat.html">
                    <img style="width: 1.5rem;" src="../images/dpfl.png">
                </a>
            <?php }?>
            <div class="header-r">
                <a id="header-nav" href="javascript:void(0);">
                    <b class="iblock iconfont icon-more fz-50 colf align-middle mt-10"></b>
                    <sup></sup>
                </a>
            </div>
        </div>
        <div class="nctouch-nav-layout">
            <div class="nctouch-nav-menu"><span class="arrow"></span>
                <ul>
                    <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                        <li><a href="../tmpl/store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../tmpl/store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }else{ ?>
                        <li><a href="../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../tmpl/search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }?>
                    <li><a href="../tmpl/cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup></sup></a></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                    <?php if ($_COOKIE['is_app_guest']) { ?>
                        <li><a href="" id="shareit_store"><i class="share"></i><?= __('分享'); ?><sup></sup></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </header>
    
    <div class=" mb25">
        <div id="store-wrapper" class="nctouch-store-con">
			<div class="store-head-bg fz0" style="background:url(https://www.yuanfengtest.com/image.php/shop/data/upload/media/6fc0625bf097e245fa1c1007bf528b2d/10012/10/image/20181126/1543234283168920.jpg) no-repeat center;background-size:cover;">
				<div class="nctouch-store-top iblock fz0" id="store_banner"></div>
				<div id="nav_tab_con" class="nctouch-single-nav nctouch-store-nav">
					<ul id="nav_tab">
						<li class="selected"><a href="javascript: void(0);" data-type="storeindex" id="storeindex" onclick="nav_click('storeindex')"><?= __('店铺首页'); ?></a></li>
						<li><a href="javascript: void(0);" data-type="allgoods" id="allgoods" onclick="nav_click('allgoods')"><?= __('全部商品'); ?></a></li>
						<li><a href="javascript: void(0);" data-type="newgoods" id="newgoods" onclick="nav_click('newgoods')"><?= __('商品上新'); ?></a></li>
						<li><a href="javascript: void(0);" data-type="storeactivity" id="storeactivity" onclick="nav_click('storeactivity')"><?= __('店铺活动'); ?></a></li>
					</ul>
				</div>
			</div>
            <div id="storeindex_con" class="relative wp100 fz0">
				<div class="bgf iblock wp100">
					<div id="store_sliders" class="nctouch-store-wapper nctouch-store-sliders"></div>
					<div class="store-vou bort1 borb1" id="voucher_list_div"></div>
					<div class="nctouch-store-block nctouch-store-ranking pb-40">
						<div class="title"><i class="iconfont icon-paihangbang"></i><span><?= __('商品排行榜'); ?></span></div>
						<div class="nctouch-single-nav">
							<ul id="goods_rank_tab">
								<li><a href="javascript: void(0);" data-type="collect"><?= __('收藏排行'); ?></a></li>
								<li><a href="javascript: void(0);" data-type="salenum"><?= __('销量排行'); ?></a></li>
							</ul>
						</div>
						<div class="top-list mt0" nc_type="goodsranklist" id="goodsrank_collect"></div>
						<div class="top-list" nc_type="goodsranklist" id="goodsrank_salenum"></div>
					</div>
				</div>
                <div style=" text-align: center;margin-top: 5px;"><a href="https://www.look56.com/download.html"><img src="./img/guanggao.png" style="width: 90%;"></a></div>
                <div class="nctouch-store-block shop-owner-recommend">
                    <div class="title">
                        <span><?= __('店主推荐'); ?></span>
                    </div>
                    <div class="nctouch-store-goods-list" id="goods_recommend"></div>
                </div>
            </div>
            <div id="allgoods_con"></div>
            <div id="newgoods_con" class="nctouch-store-goods-list">
                <div id="newgoods_next"></div>
            </div>
            <div id="storeactivity_con"></div>
        </div>
    </div>
    
    <div class="fix-block-r">
        <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
    </div>
    
    <div id="store_voucher_con"></div>
     <div id="shop_footer_div"></div>
    <div class="nctouch-store-bottom fixed-Width">
        <ul>
            <li><a id="store_intro" href="javascript:void(0);"><?= __('店铺介绍'); ?></a></li>
            <li><a id="store_voucher" href="javascript: void(0);"><?= __('免费领券'); ?></a></li>
            <li><a href="member/member.html?shop_id_wap=1"><?= __('个人中心'); ?></a></li>
            <li><a id="store_kefu" class="kefu" href="javascript: void(0);"><?= __('联系客服'); ?></a></li>
        </ul>
    </div>
    
    <!-- banner tpl -->
    <script type="text/html" id="store_banner_tpl">
        <div class="store-top-bg"><span class="img" nc_type="store_banner_img"><img src="" alt="store-bg"></span></div>
        <div class="store-avatar"><img src="<%= store_info.store_avatar %>"/></div>
        <div class="iblock store-name-text">
            <a href="./store_intro.php?shop_id=<%= store_info.shop_id %>" class="block store-name"><em><%= store_info.store_name %></em><i class="iconfont icon-arrow-right fz-24"></i></a>
			<span class="fans-num">
				<input type="hidden" id="store_favornum_hide" value="<%= store_info.store_collect %>"/>
				<em id="store_favornum"><%= store_info.store_collect %>粉丝</em>
			</span>
        </div>
        <?php if(!$_COOKIE['SHOP_ID_WAP']){?>
        <div class="store-favorate">
            <a href="javascript:void(0);" id="store_collected" class="added fz-28"> 
                <i class="iconfont icon-like default-color align-middle mr-10"></i><b class="default-color"><?= __('已收藏'); ?></b> 
            </a> 
            <a href="javascript:void(0);" id="store_notcollect" class="fz-28"> 
                <i class="iconfont icon-like align-middle mr-10"></i><b><?= __('收藏'); ?></b> 
            </a> 
        </div>
        <?php } ?>
    </script>
    <script type="text/html" id="store_sliders_tpl">
        <ul class="swiper-wrapper">
            <% for (var i in store_info.mb_sliders) { var s = store_info.mb_sliders[i]; %> <% if(s.imgUrl){ %>
            <li class="swiper-slide">
                <% if (s.link) { %> <a href="<%= s.link %>"><img class="wp100" alt="" src="<%= s.imgUrl %>"/></a> <% } else { %> <a href="javascript:void(0);"><img class="wp100" alt="" src="<%= s.imgUrl %>"/></a> <% } %>
            </li>
            <% } %> <% } %>
        </ul>
    </script>
    <script type="text/html" id="voucher_list_tpl">
        <ul class="inline">
            <% for (var i in voucher_list) { %>
            <li>
                <a href="store_voucher_list.html?shop_id=<%=voucher_list[i].shop_id%>">
                    <div class="left"><span><i><?= __('￥'); ?></i><strong><%=voucher_list[i].voucher_t_price%></strong></span></div>
                    <div class="right"><span><?= __('满'); ?><%=voucher_list[i].voucher_t_limit%><?= __('元使用'); ?></span></div>
                </a>
            </li>
            <% } %>
        </ul>
        <p class="fr"><a href="store_voucher_list.html?shop_id=<%=voucher_list[i].shop_id%>"><span><?= __('更多'); ?></span><i class="icon-arrow"></i></a></p>
    
    </script>
     <script type="text/html" id="goodsrank_collect_tpl"> 
        <div class="module-industry-goods-rank">
       <% var goods_list = items; %>
        <% for (var i in goods_list) { var v = goods_list[i]; %>
            <div class="industry-goods-rank-li" style="background:url(<%= v.common_image %>) no-repeat center;background-size:cover;">
                <a href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
                    <!-- <dt><img alt="<%= v.common_name %>" src="<%= v.common_image %>"/></dt> -->
                    <p class="clearfix">
                        <% if (i<=1) { %>
                        <span class="fl"><?= __('收藏'); ?><em><%= v.common_collect %></em></span>
                        <span class="fr"><?= __('￥'); ?><em><%= v.common_price %></em></span>
                        <% }else{%>
                        <span class="tc block"><?= __('收藏'); ?><em><%= v.common_collect %></em></span>
                        <% } %>
                    </p>
                </a>
            </div>
        <% } %>
        </ul>
    </script>
    <script type="text/html" id="goodsrank_salenum_tpl"> 
        <div class="module-industry-goods-rank">
       <% var goods_list = items; %>
        <% for (var i in goods_list) { var v = goods_list[i]; %>
            <div class="industry-goods-rank-li" style="background:url(<%= v.common_image %>) no-repeat center;background-size:cover;">
                <a href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
                    <!-- <dt><img alt="<%= v.common_name %>" src="<%= v.common_image %>"/></dt> -->
                    <p class="clearfix">
                        <% if (i<=1) { %>
                        <span class="fl"><?= __('已售'); ?><em><%= v.common_salenum %></em></span>
                        <span class="fr"><?= __('￥'); ?><em><%= v.common_price %></em></span>
                        <% }else{%>
                        <span class="tc block"><?= __('已售'); ?><em><%= v.common_salenum %></em></span>
                        <% } %>
                    </p>
                </a>
            </div>
        <% } %>
        </ul>
    </script>
    
   <!--  <script type="text/html" id="goodsrank_salenum_tpl">
        <div class="swiper-container store-sale-rank-swiper">
            <ul class="swiper-wrapper">
            <% var goods_list = items; %>
            <% for (var i in goods_list) { var v = goods_list[i]; %>
            <li class="swiper-slide">
                <a class="iblock wp100 hp100" href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
                    <em class="iblock img-box" style="background:url(<%= v.common_image %>) no-repeat center;background-size:cover;"></em>
                    <p class="clearfix"><span class="save fl"><?= __('已售'); ?><em><%= v.common_salenum %></em></span><span class="pri fr"><?= __('￥'); ?><em><%= v.common_price %></em></span></p>
                </a>
            </li>
            <% } %>
            </ul>
        </div>
    </script -->>
<!--      <script type="text/html" id="goodsrank_collect_tpl">
        <div class="swiper-container store-goods-rank-swiper">
            <ul class="swiper-wrapper">
                <% var goods_list = items; %>
                <% for (var i in goods_list) { var v = goods_list[i]; %>
                <li class="swiper-slide">
                    <a class="iblock wp100 hp100" href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
                        <em class="iblock img-box" style="background:url(<%= v.common_image %>) no-repeat center;background-size:cover;"></em>
                        <p class="clearfix"><span class="save fl"><?= __('收藏'); ?><em><%= v.common_collect %></em></span><span class="pri fr"><?= __('￥'); ?><em><%= v.common_price %></em></span></p>
                    </a>
                </li>
                <% } %>
            </ul>
        </div>
    </script>

    <script type="text/html" id="goodsrank_salenum_tpl">
        <div class="swiper-container store-sale-rank-swiper">
            <ul class="swiper-wrapper">
            <% var goods_list = items; %>
            <% for (var i in goods_list) { var v = goods_list[i]; %>
            <li class="swiper-slide">
                <a class="iblock wp100 hp100" href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
                    <em class="iblock img-box" style="background:url(<%= v.common_image %>) no-repeat center;background-size:cover;"></em>
                    <p class="clearfix"><span class="save fl"><?= __('已售'); ?><em><%= v.common_salenum %></em></span><span class="pri fr"><?= __('￥'); ?><em><%= v.common_price %></em></span></p>
                </a>
            </li>
            <% } %>
            </ul>
        </div>
    </script> -->
    <script type="text/html" id="goods_recommend_tpl">
        <ul>
            <% for (var i in rec_goods_list) { var g = rec_goods_list[i]; %>
            <li class="goods-item">
                <a href="product_detail.html?goods_id=<%= g.goods_id %>">
                    <div class="goods-item-pic">
                        <img alt="" src="<%= g.common_image %>"/>
                    </div>
                    <div class="goods-item-name one-overflow">
                        <%= g.common_name %>
                    </div>
                    <div class="goods-item-price"><?= __('￥'); ?><em><%= g.common_price %></em></div>
                </a>
            </li>
            <% } %>
        </ul>
    </script>
   <!--  <script type="text/html" id="goodsrank_salenum_tpl">
		<div class="swiper-container store-sale-rank-swiper">
			<ul class="swiper-wrapper">
			<% var goods_list = items; %>
			<% for (var i in goods_list) { var v = goods_list[i]; %>
			<li class="swiper-slide">
				<a class="iblock wp100 hp100" href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
					<em class="iblock img-box" style="background:url(<%= v.common_image %>) no-repeat center;background-size:cover;"></em>
					<p class="clearfix"><span class="save fl"><?= __('已售'); ?><em><%= v.common_salenum %></em></span><span class="pri fr"><?= __('￥'); ?><em><%= v.common_price %></em></span></p>
				</a>
			</li>
			<% } %>
			</ul>
		</div>
    </script>
    <script type="text/html" id="goods_recommend_tpl">
        <ul>
            <% for (var i in rec_goods_list) { var g = rec_goods_list[i]; %>
            <li class="goods-item">
                <a href="product_detail.html?goods_id=<%= g.goods_id %>">
                    <div class="goods-item-pic">
                        <img alt="" src="<%= g.common_image %>"/>
                    </div>
                    <div class="goods-item-name one-overflow">
                        <%= g.common_name %>
                    </div>
                    <div class="goods-item-price"><?= __('￥'); ?><em><%= g.common_price %></em></div>
                </a>
            </li>
            <% } %>
        </ul>
    </script> -->
    <script type="text/html" id="newgoods_tpl">
        <% var goods_list = items; %>
        <% if(goods_list.length >0){%>
        <% for (var i in goods_list) {
                var v = goods_list[i];
        %>
        <% if(v.goods_addtime_text_show){ %>
        </ul><p class="addtime" addtimetext='<%=v.common_add_time %>'>
            <span><i class="iconfont icon-shijian"></i><time><%= v.goods_addtime_text_show %></time></span>
        </p><ul>
        <% } %>
        <li class="goods-item">
            <a href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
                <div class="goods-item-pic">
                    <img alt="" src="<%= v.common_image %>"/>
                </div>
                <div class="goods-item-name one-overflow">
                    <%= v.common_name %>
                </div>
                <div class="goods-item-price"><?= __('￥'); ?><em><%= v.common_price %></em></div>
            </a>
        </li>
        <% } %>
        <li class="loading">
            <div class="spinner"><i></i></div>
            <?= __('商品数据读取中'); ?>...
        </li>
		
        <% } else { %>
        <div class="nctouch-norecord search">
            <div class="norecord-ico"><i></i></div>
            <dl>
                <dt><?= __('商铺最近没有新品上架'); ?></dt>
                <dd><?= __('收藏店铺经常来逛一逛'); ?></dd>
            </dl>
        </div>
        <% } %>
    </script>
    <script type="text/html" id="storeactivity_tpl">
        <% if(promotion.count){ %>
        <% if(promotion.mansong){ for(var k=0; k < promotion.mansong.length; k++){ var mansong = promotion.mansong[k];if(mansong.shop_id != 0){ %>
        <div class="store-sale-block">
            <a href="store_goods.html?shop_id=<%=shop_id %>">
                <div class="store-sale-tit">
                    <h3><%=mansong.mansong_start_time %></h3>
                    <time><?= __('活动时间：'); ?> <%=mansong.start_time_text%> <?= __('至'); ?> <%=mansong.mansong_end_time%>
                    </time>
                </div>
                <div class="sotre-sale-con">
                    <ul class="mjs">
                        <% for (var i in mansong.rule) { var rules = mansong.rule[i]; %>
                        <li><?= __('单笔订单消费满'); ?><em><?= __('¥'); ?><%=rules.rule_price %></em> <% if(rules.rule_discount) { %><?= __('，立减现金'); ?><em><?= __('¥'); ?><%=rules.rule_discount %></em> <% } %> <% if(rules.goods_id > 0) { %><?= __('，'); ?> <?= __('还可获赠品'); ?><img src="<%=rules.goods_image %>" alt="<%=rules.goods_name %>">&nbsp;<?= __('。'); ?> <% } %>
                        </li>
                        <% } %>
                    </ul>
                    <% if(mansong.mansong_remark){ %>
                    <p class="note"><?= __('活动说明：'); ?> <%=mansong.mansong_remark %>
                    </p>
                    <% } %>
                </div>
            </a>
        </div>
        <% }}} %>
        
        <% if(promotion.xianshi){ for(var k=0; k < promotion.xianshi.length; k++){var xianshi = promotion.xianshi[k]; if(xianshi.shop_id != 0){%>
        <div class="store-sale-block">
            <a href="store_goods.html?shop_id=<%=shop_id %>">
                <div class="store-sale-tit">
                    <h3><%=xianshi.discount_name %></h3>
                    <time><?= __('活动时间：'); ?> <%=xianshi.discount_start_time%> <?= __('至'); ?> <%=xianshi.discount_end_time%>
                    </time>
                </div>
                <div class="sotre-sale-con">
                    <ul class="xs">
                        <li><?= __('单件活动商品满'); ?><em><%=xianshi.discount_lower_limit %></em><?= __('件即可享受折扣价。'); ?></li>
                    </ul>
                    <% if(xianshi.discount_explain){ %>
                    <p class="note"><?= __('活动说明：'); ?> <%=xianshi.discount_explain %>
                    </p>
                    <% } %>
            </a>
        </div>
        <% }}} %>
        
        <% }else{ %>
        <div class="nctouch-norecord search">
            <div class="norecord-ico"><i></i></div>
            <dl>
                <dt><?= __('商铺最近没有促销活动'); ?></dt>
                <dd><?= __('收藏店铺经常来逛一逛'); ?></dd>
            </dl>
        </div>
        <% } %>
    </script>
    <script type="text/html" id="store_voucher_con_tpl">
        <div class="nctouch-bottom-mask">
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
        </div>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/zepto.waypoints.js"></script>
    <script type="text/javascript" src="../js/ncscroll-load.js"></script>
	<script type="text/javascript" src="../js/tmpl/swiper.js"></script>
    <script type="text/javascript" src="../js/tmpl/store.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript">
        var level = getQueryString('level');
        var shop_id = getQueryString('shop_id');
        if(level){
            addCookie('SHOP_ID_WAP',shop_id);
        }
    </script>
    <script type="text/html" id="shop_footer">
    <div class="footer bort1 " id="footer-template-bort1">
    <ul class="clearfix">
        <li  class="active shop_footer_dh">
            <a href="store3.html?shop_id=<%=data %>">
                <i class="iconfont icon-home-active"></i>
                <h3>首页</h3>
            </a>
        </li>
        <li class="shop_footer_dh">
            <a href="shop_goods_cat.html?shop_id=<%=data %>&mb=shop&style=3">
                <i class="iconfont icon-class1"></i>
                <h3>分类</h3>
            </a>
        </li>
        <li class="shop_footer_dh" id="store_voucher">
            <a href="javascript:void(0)">
                <i class="iconfont icon-voucher"></i>
                <h3>领券</h3>
            </a>
        </li>
        <li class="shop_footer_dh">
            <a href="javascript: void(0);"  id="store_kefu">
                <i class="iconfont icon-kefu1" ></i>
                <h3>联系客服</h3>
            </a>
        </li>
        <li class="shop_footer_dh">
            <a href="member/member.html">
                <i class="iconfont icon-mine"></i>
                <h3>个人中心</h3>
            </a>
        </li>
    </ul>
    </div>
</script>
    </body>
    
    </html>
<?php
    include __DIR__ . '/../includes/footer.php';
?>
