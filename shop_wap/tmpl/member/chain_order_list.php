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
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="wap-font-scale" content="no">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('自提订单'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_cart.css">
    <link rel="stylesheet" type="text/css" href="../../css/private-store.css"/>
    <link rel="stylesheet" href="../../css/iconfont.css">
</head>
<body>
   		<header id="header" class="fixed">
	        <div class="header-wrap">
	            <div class="header-l">
	                <!-- <a href="javascript:history.go(-1)"> <i class="back"></i> </a> -->
	            </div>
	            <div class="header-title posr">
                    <h1 class="drap-h1-after" id="z-tab-order" data-order_state="all">自提订单</h1>
                </div>
	        </div>
	    </header>
         <!--选项卡-->
        <div class="nctouch-bottom-mask-bg order-mask-bg" id="z-tab-box">
            <ul class="clearfix z-tab bort1" id="z-tab-box-ul">
                <a href="./order_list.html"><li class="fl">实物订单</li></a>
                <a href="./vr_order_list.html"><li class="fl">虚拟订单</li></a>
                <a href="./chain_order_list.html"><li class="fl">自提订单</li></a>
            </ul>
        </div>
    	<div class="nctouch-main-layout">
        	<div class="nctouch-order-search">
           		<form>
               		<span class="ser-area ">
               			<i class="icon-ser"></i>
               			<input type="text" autocomplete="on" maxlength="50" placeholder="<?= __('输入商品标题或订单号进行搜索'); ?>" name="order_key" id="order_key" oninput="writeClear($(this));">
     					<span class="input-del"></span>
               		</span>
               		<input type="button" id="search_btn" value="<?= __('搜索'); ?>">
            	</form>
       		</div>
        </div>
        <div id="fixed_nav" class="nctouch-single-nav">
            <ul id="filtrate_ul" class="w20h">
                <li class="selected"><a href="javascript:;" data-state=""><?= __('全部'); ?></a></li>
                <li><a href="javascript:;" data-state="chain_finish"><?= __('已完成'); ?></a></li>
                <li><a href="javascript:;" data-state="received"><?= __('待评价'); ?></a></li>
                <li><a href="javascript:;" data-state="order_chain"><?= __('待自提'); ?></a></li>
                <li><a href="javascript:;" data-state="wait_pay"><?= __('待付款'); ?></a></li>
            </ul>
        </div>
        <!--<?= __('暂无订单'); ?>-->
         <!--<div class="norecord tc">
            <div class="ziti-store">
                <i></i>
            </div>
            <p class="fz-30 col9"><?= __('暂无任何订单'); ?></p >
        </div>-->
        <div class="nctouch-order-list bgf8" id="chain-order-list">

        </div>
    <div class="fix-block-r">
        <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
    </div>
    <footer id="footer" class="bottom"></footer>
<script type="text/html" id="chain-order-list-tmp">
    <% if (chainOrderList.length > 0) { %>
        <ul id="order-list">
        <% for (var i = 0; i < chainOrderList.length; i++) {  
            var goods_list = chainOrderList[i].goods_list
        %>
            <li class="green-order-skin ">
                <div class="nctouch-order-item">
                    <div class="nctouch-order-item-head">
                        <a href="<%=WapSiteUrl%>/tmpl/store.html?shop_id=<%=chainOrderList[i].shop_id%>" class="store"><i class="iconfont icon-stores align-middle fz-30 mr-10"></i><span class="store-tit-text align-middle"><%=chainOrderList[i].shop_name%></span><i class="iconfont icon-arrow-right align-middle ml-10 fz-26 col9"></i></a>
                        <span class="state">
                        <% if(chainOrderList[i].order_status == 6 && chainOrderList[i].order_buyer_evaluation_status == 0 && chainOrderList[i].order_nums*1 != chainOrderList[i].order_refund_nums*1){ %>
                            <span class="ot-nofinish"><?= __('待评价'); ?></span>
                        <% }else{ %>
                            <span class="ot-nofinish"><%=chainOrderList[i].order_state_con%></span>
                        <% } %>
                        </span>
                    </div>
                    <div class="nctouch-order-item-con">
                    <% for (var j = 0; j < goods_list.length; j++) { %>
                        <div class="goods-block z-ztbgc">
                            <a href="<%=WapSiteUrl%>/tmpl/member/order_detail.html?from=chain&order_id=<%=chainOrderList[i].id%>" class="clearfix wp100">
                                <div class=""> 
                                    <div class="goods-pic">
                                        <img src="<%=goods_list[j]['goods_image']%>">
                                    </div>
                                    <dl class="goods-info">
                                        <dt class="goods-name"><%=goods_list[j]['goods_name']%></dt>
                                        <% if(goods_list[j]['title_order_spec_info']){ %>
                                        <dd class="goods-type one-overflow"><%=goods_list[j]['title_order_spec_info']%></dd>
                                        <%}%>
                                    </dl>
                                </div>
                                <div class="goods-subtotal">
                                    <span class="goods-price"><?= __('￥'); ?><em><%=goods_list[j]['goods_price']%></em></span>
                                    <span class="goods-num goods-num-top">x<%=goods_list[j]['order_goods_num']%></span>
                                    <div class="fz0">
                                    </div>
                                </div>
                            </a>
                        </div>
                    <% } %>
                    </div>
                    <div class="nctouch-order-item-footer">
                        <div class="store-totle">
                            <span><?= __('共'); ?><em><%=chainOrderList[i]['order_nums']%></em><?= __('件商品，合计'); ?></span><span class="sum"><?= __('￥'); ?><em><%=chainOrderList[i]['order_payment_amount']%></em></span>
                        </div>
                        <% if(chainOrderList[i].order_status == 6 || chainOrderList[i].order_status == 1 || chainOrderList[i].order_status == 7){ %>
                        <div class="handle">
                        <%if(chainOrderList[i].order_status == 7 || chainOrderList[i].order_status == 6){%>
                        <a href="javascript:void(0)" order_id="<%=chainOrderList[i].order_id%>" class="del delete-order btn"><?= __('删除'); ?></a>
                        <%}%>

                        <!-- <% if(chainOrderList[i].order_status == 6 && chainOrderList[i].order_buyer_evaluation_status == 1){ %>
                            <a href="javascript:void(0)" order_id="<%=chainOrderList[i].id%>" class="del btn view-evaluation"><?= __('查看评价'); ?></a>
                        <% } %> -->

                        <% if(chainOrderList[i].order_status == 6 && chainOrderList[i].goods_list[0]['evaluation_count'] == 2){ %>
                            <a href="javascript:void(0)" order_id="<%=chainOrderList[i].id%>" class="del btn view-evaluation"><?= __('查看评价'); ?></a>
                        <% } %>

                        <% if(chainOrderList[i].order_status == 6 && chainOrderList[i].goods_list[0]['evaluation_count'] == 1){ %>
                            <a href="javascript:void(0)" order_id="<%=chainOrderList[i].id%>" class="del btn view-evaluation"><?= __('追加评价'); ?></a>
                        <% } %>

                        <% if(chainOrderList[i].order_status == 6 && chainOrderList[i].order_buyer_evaluation_status == 0 && chainOrderList[i].order_nums*1 != chainOrderList[i].order_refund_nums*1){ %>
                            <a href="javascript:void(0)" order_id="<%=chainOrderList[i].id%>" class="del btn evaluation-order"><?= __('立即评价'); ?></a>
                        <% } %> 
 
                        <% if(chainOrderList[i].order_status == 1){ %>
                            <a href="javascript:void(0)" order_id="<%=chainOrderList[i].id%>" class="del btn check-payment" onclick="payOrder('<%= chainOrderList[i].payment_number %>','<%= chainOrderList[i].id %>')" data-paySn="<%= chainOrderList[i].id %>"><?= __('立即付款'); ?></a>
                            <a href="javascript:void(0)" order_id="<%=chainOrderList[i].order_id%>" class="btn cancel-order"><?= __('取消订单'); ?></a>
                        <% } %>
 
                        </div>
                        <% } %>
                    </div>
                </div>
            </li>
        <% } %>
        </ul>
    <% }else{ %>
    <!--<?= __('暂无订单'); ?>-->
        <div class="norecord tc">
            <div class="ziti-store">
                <i></i>
            </div>
            <p class="fz-30 col9"><?= __('暂无任何订单'); ?></p >
        </div>
    <% } %>
</script>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript" src="../../js/zepto.waypoints.js"></script>
<script type="text/javascript" src="../../js/tmpl/chain_order_list.js"></script>
<script>
        $(document).ready(function(){
                $("#z-tab-order").click(function(){
                    $("#z-tab-box").toggle();
                    $(".drap-h1-after").toggleClass("active");
                    $('.header-r').click(function(){
                        $("#z-tab-box").hide()
                        $(".drap-h1-after").removeClass("active");
                    })
                });
                $("#z-tab-box-ul li").click(function(){
                    var e=$(this).text()
                    $("#z-tab-order").text(e);
                    $("#z-tab-box").toggle();
                })
                
        });
    </script>
</body>
</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>