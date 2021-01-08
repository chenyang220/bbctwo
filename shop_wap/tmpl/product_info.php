<?php 
include __DIR__.'/../includes/header.php';
?>
<!doctype html>
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
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('商品详情'); ?></title>
    <link rel="stylesheet" type="text/css" href="../css/base.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_products_detail.css">
    <link rel="stylesheet" href="../css/iconfont.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
    <link rel="stylesheet" href="//at.alicdn.com/t/font_562768_5xeq0a3i6ha.css">
    <script type="text/javascript" src="../js/NativeShare.js"></script>
</head>

<body>
    <header id="header" class="posf">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:history.go(-1)"> <i class="back"></i> </a> -->
            </div>
            <ul class="header-nav">
                <li><a href="javascript:void(0);" id="goodsDetail"><?= __('商品'); ?></a></li>
                <li class="cur"><a href="javascript:void(0);" id="goodsBody"><?= __('详情'); ?></a></li>
                <li><a href="javascript:void(0);" id="goodsEvaluation"><?= __('评价'); ?></a></li>
                <li><a href="javascript:void(0);" id="goodsRecommendation"><?= __('推荐'); ?></a></li>
            </ul>
            <!-- <div class="header-r"><a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a> </div> -->
        </div>
        <div class="nctouch-nav-layout">
            <div class="nctouch-nav-menu"> <span class="arrow"></span>
                <ul>
                    <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                        <li><a href="../tmpl/store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../tmpl/store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }else{ ?>
                        <li><a href="../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../tmpl/search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }?>
                    <li><a href="../tmpl/cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup></sup></a></li>
                    <li><a href="../tmpl/member/member.html"><i class="member"></i><?= __('我的商城'); ?></a></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="nctouch-main-layout" id="fixed-tab-pannel">
        <div class="fixed-tab-pannel"></div>
    </div>
    <!--新增-->
    <div id="product_detail_html" class="posr zIndex1"></div>
    <div id="product_detail_spec_html" class="nctouch-bottom-mask down"></div>
    <!-- <?= __('新增促销'); ?> 2017.7.17 -->
    <div class="nctouch-bottom-mask down" id="sale-activity-html"></div>
    <!-- <?= __('代金券'); ?> -->
    <div id="voucher_html" class="nctouch-bottom-mask down"></div>
    <script src="../js/swiper.min.js"></script>
    <div id="product_detail_spec_html" class="nctouch-bottom-mask down"></div>
    <script type="text/html" id="product_detail">
        <!--<div class="goods-detail-bottom"><a href="javascript:void(0);" id="goodsBody1"><?= __('点击查看商品详情'); ?></a></div>-->
        <div class="goods-detail-foot">
            <div class="otreh-handle bgf">
                <!--YF_IM <?= __('联系客服'); ?> kefu START -->
                <a style="display: none;" href="javascript:void(0);" class="kefu wp30" id="customer"><i></i><p><?= __('客服'); ?></p></a>
                <!--YF_IM <?= __('联系客服'); ?> END -->
                <a href="javascript:void(0);" class="borl1 wp30 collect pd-collect <% if (is_favorate) { %>favorate<% } %>"><i></i><p><?= __('收藏'); ?></p></a>
                <a href="../tmpl/cart_list.html" class="cart"><i></i><p><?= __('购物车'); ?></p><span id="cart_count"></span></a>
            </div>
            <div class="buy-handle <%if(!goods_hair_info.if_store || goods_info.goods_storage == 0){%>no-buy<%}%>">
                <% if (goods_info.cart == '1') { %>
                <a href="javascript:void(0);" class="<%if(goods_hair_info.if_store){%>animation-up<%}%> add-cart"><?= __('加入购物车'); ?></a>
                <% } %> <a href="javascript:void(0);" class="<%if(goods_hair_info.if_store){%>animation-up<%}%> buy-now <%if(goods_info.cart != '1'){%>wp100<%}%>"><?= __('立即购买'); ?></a>
            </div>
        </div>
    </script>
    <script type="text/html" id="product_detail_sepc">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block">
            <!--<div class="nctouch-bottom-mask-tip"><i></i><?= __('点击此处返回'); ?></div>-->
            <div class="nctouch-bottom-mask-top goods-options-info">
                <div class="goods-pic">
                    <img src="<%=goods_image[0]%>"/>
                </div>
                <dl>
                    <dt class="fz-28 col2"><%= goods_info.goods_name; %></dt>
                    <dd class="goods-price price-color fz-28">
                        <% if (goods_info.promotion_type && goods_info.promotion_is_start == 1 ) { var promo; switch (goods_info.promotion_type) { case 'groupbuy': promo = '<?= __('团购'); ?>'; break; case 'xianshi': promo = '<?= __('限时折扣'); ?>'; break; } %> <?= __('￥'); ?><em><%=goods_info.promotion_price%></em> <span class="activity">
                        <% if (promo) { %>
                            <%= promo %>
                        <% } %>
                        </span> <% } else if(goods_info.plus_status && goods_info.plus_user){ %> <?= __('￥'); ?><em><%=goods_info.plus_price%></em><b class="plus-logo"></b><% } else{%><?= __('￥'); ?><em><%=goods_info.goods_price%></em> <% }%>
                    </dd>
                    <span class="goods-storage"><?= __('库存：'); ?><%=goods_info.goods_stock%><?= __('件'); ?></span>
                </dl>
                <a href="javascript:void(0);" class="nctouch-bottom-mask-close"><i></i></a>
            </div>
            <div class="nctouch-bottom-mask-rolling go overflow-auto" id="product_roll">
                <div class="goods-options-stock bort1">
                    <% if(goods_map_spec.length>0){%> <% for(var i =0;i < goods_map_spec.length; i++){%>
                    <dl class="spec JS-goods-specs">
                        <dt spec_id="<%=goods_map_spec[i].id%>">
                            <%=goods_map_spec[i].goods_spec_name%><?= __('：'); ?>
                        </dt>
                        <dd>
                            <%for(var j = 0;j < goods_map_spec[i].goods_spec_value.length;j++){%> <a href="javascript:void(0);" <% if(goods_info.goods_spec[goods_map_spec[i].goods_spec_value[j].specs_value_id]){ %> class="current" <% } %> specs_value_id = "<%=goods_map_spec[i].goods_spec_value[j].specs_value_id%>"> <%=goods_map_spec[i].goods_spec_value[j].specs_value_name%></a><%}%>
                        </dd>
                    </dl>
                    <%}%> <%}%> <% if (goods_info.is_virtual == '1') { %>
                    <dl class="spec-promotion">
                        <dt><?= __('提货方式：'); ?></dt>
                        <dd><a href="javascript:void(0);" class="current"><?= __('电子兑换券'); ?></a></dd>
                    </dl>
                    <dl class="spec-promotion">
                        <dt><?= __('有效期：'); ?></dt>
                        <dd>
                            <a href="javascript:void(0);" class="current"> <?= __('即日起'); ?> <?= __('到'); ?> <%= goods_info.virtual_indate_str %> </a> <% if (goods_info.buyLimitation && goods_info.buyLimitation > 0) { %> <?= __('（每人次限购'); ?> <%= goods_info.buyLimitation %> <?= __('件）'); ?> <% } %>
                        </dd>
                    </dl>
                    <% } else { %> <% if (goods_info.is_presell == '1') { %>
                    <dl class="spec-promotion">
                        <dt><?= __('预售：'); ?></dt>
                        <dd>
                            <a href="javascript:void(0);" class="current"> <%= goods_info.presell_deliverdate_str %><?= __('日发货'); ?> </a>
                        </dd>
                    </dl>
                    <% } %> <% if (goods_info.is_fcode == '1') { %>
                    <dl class="spec-promotion">
                        <dt><?= __('购买类型：'); ?></dt>
                        <dd>
                            <a href="javascript:void(0);" class="current">F<?= __('码优先购买'); ?></a> <?= __('（每个'); ?>F<?= __('码优先购买一件商品）'); ?>
                        </dd>
                    </dl>
                    <% } %> <% } %>
                </div>
            </div>
            <div class="goods-option-value clearfix fz-28 colbc">
                <?= __('购买数量'); ?>
                <div class="value-box">
                    <span class="minus">
                        <a href="javascript:void(0);">&nbsp;</a>
                    </span> <span>
                         <% if(buyer_limit != 0) {
                                    if(buyer_limit >= goods_info.goods_stock){
                                        data_max = goods_info.goods_stock;
                                    }else{
                                        data_max = buyer_limit;
                                    }
                        } else {
                            data_max = goods_info.goods_stock;
                        }
                        if(goods_info.lower_limit > 1 && goods_info.promotion_is_start == 1 )
                        {
                            data_min = goods_info.lower_limit;
                            promotion = 1;
                        }
                        else
                        {
                            data_min = 1;
                            promotion = 0;
                        }
                        %>
            <input type="text" pattern="[0-9]*" class="buy-num" promotion="<%=promotion%>" data-max="<%=data_max%>"
                   data-min="<%=data_min%>" id="buynum" value="<%=data_min%>"/>
        </span> <span class="add">
            <a href="javascript:void(0);">&nbsp;</a>
        </span> <% if(buyer_limit != 0) { %>
                    <div style="font-size: 0.5rem;text-align: center;"><?= __('限购'); ?><%= buyer_limit; %><?= __('件'); ?></div>
                    <% } %>
                </div>
            </div>
            <div class="goods-option-foot">
                <!--<div class="otreh-handle">
                    <a href="javascript:void(0);" class="kefu">
                        <i></i>
                        <p><?= __('客服'); ?></p>
                    </a>
                    <a href="../tmpl/cart_list.html" class="cart">
                        <i></i>
                        <p><?= __('购物车'); ?></p>
                        <span id="cart_count1"></span>
                    </a>
                </div>-->
                <div class="only-two-handle buy-handle <%if(!goods_hair_info.if_store || goods_info.goods_storage == 0){%>no-buy<%}%>">
                    <% if (goods_info.cart == '1') { %> <a href="javascript:void(0);" class="add-cart" id="add-cart"><?= __('加入购物车'); ?></a> <% } %> <a href="javascript:void(0);"
                                                                                                                                                        class=" fl buy-now <%if(goods_info.cart != '1'){%>wp100<%}%>" id="buy-now"><?= __('确定'); ?></a>
                </div>
            </div>
    </script>

    <script type="text/html" id="goodsReview">
        <p class="evals bgf mt5 bort1 borb1"><span><?= __('商品评价'); ?><% if (evalcount > 0) { %>
                    (<%= evalcount %>)
                    <% } else { %>
                (0)
                <% } %></span></p>
        <% if (evalcount > 0) { %>
        <div id="mui-tagscloud-i" class="mui-tagscloud borb1 pb-0">
            <div class="mui-tagscloud-main">
                <% if (goods_review_rows.length > 0) { %> <% for(var i = 0; i < goods_review_rows.length; i++) { %>
                <div class="mui-tagscloud-comments mar-0 pb-30">
                    <div class="mui-tagscloud-user clearfix">
                        <img class="mui-tagscloud-img fl" src="<%= goods_review_rows[i].user_logo %>">
                        <div class="fl">
                            <span class="mui-tagscloud-name"><%= goods_review_rows[i].user_name %></span>
                            <p class="levels">
                                <% for(var j = 0; j < goods_review_rows[i].scores; j++) { %> <i class="icon-star"></i> <% } %>
                            </p>
                        </div>
                        <li class="fr mt5 product-li-font"><%= goods_review_rows[i].create_time %></li>
                    </div>
                    <div class="mui-tagscloud-content"><%= goods_review_rows[i].content %></div>
                    <% if(goods_review_rows[i].image_row.length>0){ %> <% var image_row=goods_review_rows[i].image_row %>
                    <div class="goods_geval">
                        <% for(var j=0;j < image_row.length; j++ ){ %>
                        <a href="javascript:void(0);" data-start="<%=j%>">
                            <img class="goods_geval-img2" src="<%= image_row[j] %>"/>
                        </a>
                        <% } %>
                        <div class="nctouch-bigimg-layout hide">
                            <div class="close" style="margin-top:50px"></div>
                            <div class="pic-box">
                                <ul>
                                    <% for(var j=0; j < image_row.length; j++ ){ %>
                                    <li style="background-image: url(<%= image_row[j] %>)"></li>
                                    <% } %>
                                </ul>
                            </div>
                            <div class="nctouch-bigimg-turn">
                                <ul>
                                    <% for(var j=0;j < image_row.length; j++ ){ %>
                                    <li class="<% if(j == 0) { %> cur <% } %>"></li>
                                    <% } %>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <% } %>
                    <div class="mui-tagscloud-date"><%= goods_review_rows[i].goods_spec_str; %></div>
                </div>
                <% } %> <% } %>
            </div>
            <div class="mui-tagscloud-more">
                <% if (goods_review_rows.length > 0) { %>
                <button id="reviewLink"><?= __('查看全部评价'); ?></button>
                <% } %>
            </div>
        </div>
        <% } %>
    </script>
    <script type="text/html" id="sale-activity">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block">
            <% if (promotion_info.jia_jia_gou || promotion_info.man_song) { %>
            <div class="goods-detail-item drap-ar">
                <!-- <?= __('加价购'); ?> --><% if (promotion_info.jia_jia_gou) { %>
                <div class="item-con">
                    <div class="tit-sale mb-20"><?= __('加价购'); ?></div>
                    <dl class="goods-detail-sale v-top wp100">
                        <% for(var i = 0; i < promotion_info.jia_jia_gou.rule.length; i++) { %> <% var rule_price = promotion_info.jia_jia_gou.rule[i].rule_price; var rule_goods_limit = promotion_info.jia_jia_gou.rule[i].rule_goods_limit; var redemption_goods = promotion_info.jia_jia_gou.rule[i].redemption_goods; %>
                        <dt><?= __('购物满'); ?><b class="col-red"><?= __('￥'); ?><%= rule_price; %></b><?= __('即可加价换购最多'); ?><%= rule_goods_limit; %><?= __('样以下商品'); ?></dt>
                        <dd>
                            <% for (var m = 0; m < redemption_goods.length; m++) { %>
                            <div>
                                <a href="<%= WapSiteUrl; %>/tmpl/product_detail.html?goods_id=<%= redemption_goods[m].goods_id; %>"><%= redemption_goods[m].goods_name %></a></div>
                            <% } %>
                        <dd>
                            <% } %>
                    </dl>
                </div>
                <% } %>
                <!-- <?= __('加价购'); ?> -->

                <!-- <?= __('满送'); ?> --><% if (promotion_info.man_song) { %>
                <div class="item-con">
                    <div class="tit-sale mb-20"><?= __('满即送'); ?></div>
                    <dl class="goods-detail-sale v-top wp100">
                        <% for(var i = 0; i < promotion_info.man_song.rule.length; i++) { %> <% var rule_price = promotion_info.man_song.rule[i].rule_price; var rule_discount = promotion_info.man_song.rule[i].rule_discount; %>
                        <dt><?= __('购物满'); ?><b class="col-red"><?= __('￥'); ?><%= rule_price; %></b><?= __('，'); ?> <?= __('即享'); ?><%= rule_discount; %><?= __('元优惠'); ?></dt>
                        <% } %>
                    </dl>
                </div>
                <% } %>
                <!-- <?= __('满送'); ?> -->
                <!-- <?= __('限时折扣'); ?> -->
                <!--                    <% if (promotion_info.xian_shi) { %>
                                        <div class="item-con">
                                                <div><?= __('限时折扣'); ?></div>
                                                <dl class="goods-detail-sale">
                                                    <dt><?= __('直降'); ?><%= promotion_info.xian_shi.goods_price - promotion_info.xian_shi.discount_price %><?= __('，最低购'); ?><%= promotion_info.xian_shi.goods_lower_limit %><?= __('件'); ?></dt>
                                                </dl>
                                        </div>
                                    <% } %>-->
                <!-- <?= __('限时折扣'); ?> -->

            </div>
            <% } %>
            <p class="new-btn close-btn absolute"><a href="javascript:" class="btns"><?= __('关闭'); ?></a></p>
        </div>
    </script>
    <footer id="footer"></footer>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/swipe.js"></script>
    <script type="text/javascript" src="../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/iscroll.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/fly/requestAnimationFrame.js"></script>
    <script type="text/javascript" src="../js/fly/zepto.fly.min.js"></script>

    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/tmpl/product_info.js"></script>
    <script type="text/javascript" src="../js/product_info_cartl.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>
    <script>
        $(function () {
            //商品属性面板折叠'); ?>
            function isHidden(oDiv) {
                var vDiv = document.getElementById(oDiv);
                var vBtn = document.getElementById("btn_attr");
                if (vDiv.style.display == "none") {
                    vBtn.innerHTML = "<?= __('商品属性'); ?>&nbsp;<?= __('︽'); ?>";
                } else {
                    vBtn.innerHTML = "<?= __('商品属性'); ?>&nbsp;<?= __('︾'); ?>";
                }
                vDiv.style.display = (vDiv.style.display == "none") ? "block" : "none";
            }
        });
    </script>

</body>

</html>
<?php 
include __DIR__.'/../includes/footer.php';
?>