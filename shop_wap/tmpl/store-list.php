<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title><?= __('店铺列表'); ?></title>
    <link rel="stylesheet" href="../css/base.css"/>
    <link rel="stylesheet" href="../css/index.css"/>
    <link rel="stylesheet" href="../css/Group.css"/>
    <link rel="stylesheet" href="../css/swiper.min.css"/>
    <link rel="stylesheet" href="../css/nctouch_products_list.css"/>
    <link rel="stylesheet" href="../css/nctouch_common.css"/>
    <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_qwz6qicku8.css">
    <script type="text/javascript" src="../js/swipe.js"></script>


    <script type="text/javascript">
        window.addressStr = '';
        window.coordinate = null;
        function initialize() {
            var geoc = new BMap.Geocoder();
            geolocation.getCurrentPosition(function (r) {
                if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                    var mk = new BMap.Marker(r.point);
                    window.coordinate = {'lng': r.point.lng, lat: r.point.lat};
                    console.info(window.coordinate);
                    geoc.getLocation(r.point, function (rs) {
                        var addComp = rs.addressComponents;
                        window.addressStr = addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber;
                        console.info(window.addressStr);
                    });

                }
                else {
                    alert('failed' + this.getStatus());
                }
            }, {enableHighAccuracy: true})
        }

        function loadScript() {
            var script = document.createElement("script");
            script.src = "http://api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9&callback=initialize";
        }

        window.onload = loadScript;
    </script>
</head>
<body>
<header id="header" class="nctouch-product-header fixed">
    <div class="header-wrap">
        <!-- <div class="header-l"><a href="javascript:history.go(-1)"> <i class="back"></i> </a></div> -->
        <div class="header-inp clearfix">
            <i class="icon"></i>
            <input type="text" class="search-input" name="keyword" value="" oninput="writeClear($(this));" id="keyword"
                   maxlength="50" autocomplete="on" >
            <span class="input-del"></span>
        </div>
		<div class="header-r">
			<a id="search-btn" href="javascript:void(0);" class="search-btn" style="top: 4px;"><?= __('搜索'); ?></a>
		</div>
    </div>
    <div class="nctouch-nav-layout">
        <div class="nctouch-nav-menu"><span class="arrow"></span>
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
    </div>
</header>
<div class="goods-search-list-nav">
    <ul id="nav_ul" style="width:100%;">
        <li style="width:25%;"><a href="javascript:void(0);" onclick="init_get_list('default', '')"class="current"><span><?= __('默认排序'); ?></span></a></li>
        <li style="width:25%;"><a href="javascript:void(0);" onclick="init_get_list('or', 'collect')" class=""><span><?= __('收藏量'); ?></span></a></li>
        <li style="width:25%;"><a href="javascript:void(0);" onclick="init_get_list('plat', 1)"><span><?= __('平台自营'); ?></span></a></li>
        <li style="width:25%;"><a href="javascript:void(0);" onclick="init_get_list('near', 1)"><span><?= __('附近的门店'); ?></span></a></li>
    </ul>
</div>
<!--<?= __('筛选部分'); ?>-->
<div class="nctouch-full-mask hide">
    <div class="nctouch-full-mask-bg"></div>
    <div class="nctouch-full-mask-block">
        <div class="header">
            <div class="header-wrap">
                <div class="header-l"><a href="javascript:void(0);"><i class="back"></i></a></div>
                <div class="header-title">
                    <h1><?= __('地区筛选'); ?></h1>
                </div>
                <div class="header-r"><a href="javascript:void(0);" id="reset" class="text"><?= __('重置'); ?></a></div>
            </div>
        </div>
        <div class="nctouch-main-layout-a secreen-layout" id="list-items-scroll" style="top: 2rem;"></div>
    </div>
</div>
<div class="store-lists-area pl-20 pr-20"></div>
<div class="fix-block-r">
    <a href="javascript:void(0);" class="gotop-btn gotop" id="goTopBtn"><i></i></a>
</div>
</body>
<script type="text/html" id="search_items">
    <div style="transition-timing-function: cubic-bezier(0.1, 0.57, 0.1, 1); transition-duration: 0ms; transform: translate(0px, 0px) translateZ(0px);">
        <dl>
            <dt><?= __('所在地'); ?></dt>
            <dd>
                <a href="javascript:void(0);" nctype="items" onclick="init_rows('district_name','',this)"><?= __('不限'); ?></a>
                <% if(data.items){ %>
                <% var items = data.items %>
                <% for( var i in items ){ %>
                <a href="javascript:void(0);" nctype="items"
                   onclick="init_rows('district_name','<%= items[i].district_name%>',this)">
                    <%= items[i].district_name %></a>
                <% }} %>
            </dd>
        </dl>
        <div class="bottom">
            <a href="javascript:void(0);" class="btn-l" id="search_submit" onclick="search_adv()"><?= __('筛选店铺'); ?></a>
        </div>
    </div>
</script>

<script type="text/html" id="store-lists-area">
    <ul>
        <% if(data.records > 0){ %>
        <% var items = data.items %>
        <% for( var i in items ){ 
            if(items[i].shop_wap_index==1){
                items[i].shop_wap_index = ''
            }
        %>
        <li class="store-list clearfix">
            <div class="store-item-name">
                <div class="store-info clearfix flex">
                    <div class="store-img">
                        <% if(items[i].distance){ %> 
                            <% if(items[i].chain_img){ %>   
                                <a href="javascript:;" title="">
                                    <img src="<%= items[i].chain_img%>" class="img-ber">
                                </a>
                            <% }else{ %>
                                <a href="javascript:;" title="">
                                    <img src="../images/default_store_image.png" class="img-ber">
                                </a>
                            <% } %>
                        <% }else{ %>
                            <% if(items[i].wap_shop_logo){ %>   
                                <a href="store<%= items[i].shop_wap_index%>.html?shop_id=<%= items[i].shop_id%>" title="">
                                    <img src="<%= items[i].wap_shop_logo%>" class="img-ber">
                                </a>
                            <% }else{ %>
                                <a href="store<%= items[i].shop_wap_index%>.html?shop_id=<%= items[i].shop_id%>" title="">
                                    <img src="../images/default_store_image.png" class="img-ber">
                                </a>
                            <% } %>
                        <% } %>
                    </div>
                    <div class="store-info-o flex1">
						<div class="store-info-text hp100">
							<p>
								<% if(items[i].distance){ %>
                                    <a class="store-name m-r-5 one-overflow" href="javascript:;">
                                        <%= items[i].chain_name%>
                                    </a>
								    <span class="fr" title=""><i class="iconfont icon-dizhi align-middle fz-28"></i> <%= items[i].distance%> <?= __('km'); ?></span>
                                <% }else{ %>
                                    <a class="store-name m-r-5 one-overflow" href="store<%= items[i].shop_wap_index%>.html?shop_id=<%= items[i].shop_id%>">
                                        <%= items[i].shop_name%>
                                    </a>
								<% } %>
							</p>
							
							<% if(items[i].distance){ %>
    							<p><span class="mr-40"> <%= items[i].chain_province%> <%= items[i].chain_city%></span><a href="tel:<%= items[i].chain_mobile%>"><i class="i-bg tel mr-10"></i><span><%= items[i].chain_mobile%></span></a></p>
    							<p><span><?= __('详细地址：'); ?> <%= items[i].entity_xxaddr%></span>
                                    <a href="http://api.map.baidu.com/marker?location=<%= items[i].latitude%>,<%= items[i].longitude%>&title=<%= items[i].chain_province%> <%= items[i].chain_city%><%= items[i].chain_county%>&content=<%= items[i].entity_xxaddr%>&output=html">
                                        <i class="i-bg map ml-10"></i>
                                    </a>
                                </p>
							<% } else { %>
    							<p><?= __('共'); ?><%= items[i].goods_num %><?= __('件宝贝'); ?></p>
    							<p><?= __('所在地：'); ?><span><%= items[i].shop_company_address%></span></p>
    								<% if(items[i].is_collect == 1){%>
    								<div class="fav-store">
    									<a href="javascript:;" class="store_save_btn_<%= items[i].shop_id%> active"
    									   nc_type="storeFavoritesBtn" onclick="collectShop('<%= items[i].shop_id%>')">
    										<i class="iconfont icon-star align-middle fz-26 mr-10"></i>
    										<?= __('已收藏'); ?>
    									</a>
    								</div>
    								<% }else{ %>
    								<div class="fav-store">
    									<a href="javascript:;" class="store_save_btn_<%= items[i].shop_id%>" nc_type="storeFavoritesBtn"
    									   onclick="collectShop('<%= items[i].shop_id%>')">
    										<i class="iconfont icon-save align-middle fz-26 mr-10"></i>
    										<?= __('收藏店铺'); ?>
    									</a>
    								</div>
    								<% } %>
							<% } %>
						</div>
                    </div>
                </div>
                
            </div>

            <div class="store-item-goods item-goods fl swiper-container wp100">
                <ul class="store-preview-goods clearfix swiper-wrapper">
                    <% if(items[i].distance){ %>
                        <% var goods_items = items[i].goods_recommended %>
                        <% for( var j in goods_items ){ %>
                        <li class="swiper-slide">
                            <a href="product_detail.html?goods_id=<%= goods_items[j].goods_id%>">
                                <div class="goods-pic"><img src="<%= goods_items[j].goods_image%>" alt=""></div>
                                <p class="goods-price tr fz-24 pl-10 pr-10"><em><?= __('￥'); ?><%= goods_items[j].goods_price%></em></p>
                            </a>
                        </li>
                        <% } %>
                    <% } else { %>
                        <% var goods_items = items[i].goods_recommended.items %>
                        <% for( var j in goods_items ){ %>
                        <li class="swiper-slide">
                            <a href="product_detail.html?goods_id=<%= goods_items[j].goods_id%>">
                                <div class="goods-pic"><img src="<%= goods_items[j].common_image%>" alt=""></div>
                                <p class="goods-price tr fz-24 pl-10 pr-10"><em><?= __('￥'); ?><%= goods_items[j].common_price%></em></p>
                            </a>
                        </li>
                        <% } %>
                    <% } %>
                    
                </ul>
            </div>
        </li>
        <% }}else { %>
        <div class="nctouch-norecord search">
            <div class="norecord-ico"><i></i></div>
            <dl>
                <dt><?= __('没有找到任何相关信息'); ?></dt>
            </dl>
        </div>
        <%
        }
        %>
    </ul>
</script>

<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/swiper.min.js"></script>
<script type="text/javascript" src="../js/tmpl/store_list.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
<script src="http://pv.sohu.com/cityjson?ie=utf-8"></script>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9&callback=baidu_lbs_geo"></script>
</html>

<?php
include __DIR__ . '/../includes/footer.php';
?>
