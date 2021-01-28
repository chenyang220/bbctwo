<?php
include __DIR__ . '/../includes/header.php';
?>
    <!DOCTYPE html>
    <html>
    <head>
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
        <title><?= __('购物车'); ?></title>
        <link rel="stylesheet" type="text/css" href="../css/base.css">
        <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
        <link rel="stylesheet" type="text/css" href="../css/nctouch_cart.css?v=81">
        <link rel="stylesheet" type="text/css" href="../css/iconfont.css">
    </head>
    <body>
    <header id="header" class="fixed borb0">
        <div class="header-wrap">
            <div class="header-l">
                <a href="javascript:history.go(-1)"> <i class="back"></i> </a>
            </div>
            <div class="header-title">
                <h1><?= __('购物车'); ?></h1>
            </div>
            <div class="JS-header-edit fz-26 col6">
                <?= __('管理'); ?>
            </div>
        </div>
    </header>
	
    <div class="" style="margin-top:45px;">
		<div class="zk-cart-orders bgf">
			<div class="zk-cart-orders-content">
				<p><span>我的订单</span><a class="fr" href="/tmpl/member/order_list.html">查看全部</a></p>
				<ul class="clearfix">
					<li><a href="/tmpl/member/order_list.html?data-state=wait_pay"><em class="img-box"><img src="../images/zk/c-dfk.png" alt="icon"></em><span>待付款</span></a></li>
					<li><a href="/tmpl/member/order_list.html?data-state=order_payed"><em class="img-box"><img src="../images/zk/c-dfh.png" alt="icon"></em><span>待发货</span></a></li>
					<li><a href="/tmpl/member/order_list.html?data-state=wait_confirm_goods"><em class="img-box"><img src="../images/zk/c-dsh.png" alt="icon"></em><span>待收货</span></a></li>
					<li><a href="/tmpl/member/order_list.html?data-state=finish"><em class="img-box"><img src="../images/zk/c-dpj.png" alt="icon"></em><span>待评价</span></a></li>
					<li><a href="/tmpl/member/member_refund.html"><em class="img-box"><img src="../images/zk/c-dsh.png" alt="icon"></em><span>退款售后</span></a></li>
				</ul>
			</div>
		</div>
        <div id="cart-list-wp" class="mb50 zk-cart-content"></div>
    </div>
    <footer id="footer" class="bottom"></footer>
   <!--  <div class="pre-loading hide">
        <div class="pre-block">
            <div class="spinner"><i></i></div>
            <?= __('购物车数据读取中'); ?>...
        </div>
    </div> -->
    <script id="cart-list" type="text/html">
        <% if(cart_list.length >0){%>
        <% for (var i = 0;i
        <cart_list.length;i++){%>
        <div class="nctouch-cart-container">
            <dl class="nctouch-cart-store bgf">
                <dt><span class="store-check">
							<input class="store_checkbox" type="checkbox" checked>
						</span>
                    <strong class="iblock align-middle ml-10"><%=
                        cart_list[i].shop_name%></strong>
                    <% if (cart_list[i].shop_self_support == 'true') { %>
						<span>
                            <?= __('自营店铺'); ?>
						</span> <% } %>
                    <a href="<%=WapSiteUrl%>/tmpl/store.html?shop_id=<%=cart_list[i].shop_id%>">
                   </a>

                    <!-- <span class="JS-edit fr"><?= __('编辑'); ?></span> -->
                </dt>
            </dl>
            <ul class="nctouch-cart-item nctouch-cart-item-new">
                <% if (cart_list[i].goods) { %> <% for (var j=0; j < cart_list[i].goods.length; j++) {var goods =
                cart_list[i].goods[j];%>
                <li cart_id="<%=goods.cart_id%>" class="cart-litemw-cnt">
                    <div class="buy-li clearfix borb1">
                        <div class="goods-check "> 
                            <input type="checkbox" data-num="<%= goods.goods_num %>" checked name="cart_id"
                                   value="<%=goods.cart_id%>" <% if(goods.IsHaveBuy){ %> disabled="" title="<?= __('您已达限购数量'); ?>" <%
                            } %> />
                        </div>
                        <div class="goods-pic posr">
                            <% if(goods.goods_base.is_del == 2){ %>
                        	    <p class="old-Failed"><?= __('该商品'); ?><br/><?= __('已失效'); ?></p>
                            <% } %>
                            <a href="<%=WapSiteUrl%>/tmpl/product_detail.html?goods_id=<%=goods.goods_id%>">
                                <img src="<%=goods.common_base.common_image%>"/> </a>
                        </div>
                        <dl class="goods-info fl ml-20">
                            <dt class="goods-name  word-wrap">
                                <a class="z-dhwz iblock" href="<%=WapSiteUrl%>/tmpl/product_detail.html?goods_id=<%=goods.goods_id%>">
                                    <%=goods.common_base.common_name%> </a>
                            </dt>
							<%if(goods.goods_base.spec_val_str){%>
                            <dd class="goods-type">
								<span class="zk-cart-type-sel"><em><%=goods.goods_base.spec_val_str%></em><i class="iconfont icon-xiala align-top"></i></span>
							</dd>
							<%}%>
                            <dd class="iblock">
								<span class="goods-price"><b class="rmb"><?= __('￥'); ?></b>
									<%if(goods.isPlus){%>
									<em><%=goods.plus_price%></em><b class="plus-logo"></b>
									<%}else{%>
									<em><%=goods.now_price%></em>
									<%}%>
								</span>
								<!-- <span class="fr nums">x<%=goods.goods_num%></span> -->
								<div class="value-box cart">
								    <span class="minus">
								        <a href="javascript:void(0);">&nbsp;</a>
								    </span>
                                      <!-- s <?= __('获取并设置限购数量'); ?> 2017.5.2 -->
                                    <span>
                                        <% if(goods.buy_limit > 0 && !goodsIsHaveBuy)
                                        {
                                            data_max = goods.buy_limit;
                                        }
                                        else
                                        {
                                            data_max = goods.goods_base.goods_stock;
                                        }
                                        if(goods.goods_base.lower_limit)
                                        {
                                            data_min = goods.goods_base.lower_limit;
                                            promotion = 1;
                                        }
                                        else
                                        {
                                            data_min = 1;
                                            promotion = 0;
                                        }
                                        %>
                                        <input type="number" min="122" class="buy-num buynum" promotion="<%=promotion%>"
                                               data_max="<%=data_max%>" data_min="<%=data_min%>"
                                               value="<%=goods.goods_num%>"/>
                                        <!-- e <?= __('获取并设置限购数量'); ?> 2017.5.2 -->
                                    </span>
								    <span class="add">
								        <a href="javascript:void(0);">&nbsp;</a>
								    </span>
								</div>
							</dd>  
                                <% if(goods.goods_base.is_del ==2){ %>
								<span class="old-price">
									<p style="color: red"><?= __('商品已被商家删除'); ?></p>
								</span>
                                <% } %>
							<% if (!isEmpty(goods.groupbuy_info) || !isEmpty(goods.xianshi_info) || !isEmpty(goods.sole_info)){ %>
                            <span class="goods-sale">
                            <% if (!isEmpty(goods.groupbuy_info))
                                {%><em><?= __('团购'); ?></em><% }
                            else if (!isEmpty(goods.xianshi_info))
                                { %><em><?= __('限时折扣'); ?></em><% }
                            else if (!isEmpty(goods.sole_info))
                                { %><em><i></i><?= __('手机专享'); ?></em><% } %>
                            </span>
							<% } %>
                        </dl>
                        <div class="edit-area">
                            <div class="goods-del" cart_id="<%=goods.cart_id%>">
                                <?= __('删除'); ?>
                            </div>
                            <div class="goods-subtotal">

                                <div class="value-box">
                                    <span class="minus">
                                        <a href="javascript:void(0);">-</a>
                                    </span>
                                    <!-- s <?= __('获取并设置限购数量'); ?> 2017.5.2 -->
                                    <span>
                                        <% if(goods.buy_limit > 0 && !goodsIsHaveBuy)
                                        {
                                            data_max = goods.buy_limit;
                                        }
                                        else
                                        {
                                            data_max = goods.goods_base.goods_stock;
                                        }
                                        if(goods.goods_base.lower_limit)
                                        {
                                            data_min = goods.goods_base.lower_limit;
                                            promotion = 1;
                                        }
                                        else
                                        {
                                            data_min = 1;
                                            promotion = 0;
                                        }
                                        %>
                                        <input type="number" min="122" class="buy-num buynum" promotion="<%=promotion%>"
                                               data_max="<%=data_max%>" data_min="<%=data_min%>"
                                               value="<%=goods.goods_num%>"/>
                                        <!-- e <?= __('获取并设置限购数量'); ?> 2017.5.2 -->
                                    </span>
                                    <span class="add">
                                        <a href="javascript:void(0);">&nbsp;</a>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </li>
                <% } %> <% } %>
            </ul>
            <dl class="nctouch-cart-store bgf">
                <% if (cart_list[i].free_freight) { %>
                <dd class="store-activity">
                    <em><?= __('免运费'); ?></em> <span><%=cart_list[i].free_freight%></span>
                </dd>
                <% } %> <% if (cart_list[i].mansong_info && !isEmpty(cart_list[i].mansong_info)) { %>
                <dd class="store-activity">
                    <em><?= __('满即送'); ?></em> <%var mansong = cart_list[i].mansong_info%> <span class="fz-28 col2"><%if(mansong.rule_discount){%><?= __('店铺优惠'); ?><%=mansong.rule_discount%><?= __('。'); ?><%}%> <%if(mansong.goods_name){%><?= __('赠品：'); ?><%=mansong.goods_name%><%}%></span>
                    <i class="arrow-down"></i>
                </dd>
                <% } %>
            </dl>


            <% if (cart_list[i].shop_voucher) { %>
            <div class="nctouch-bottom-mask down nctouch-bottom-mask<%=i%>">
                <div class="nctouch-bottom-mask-bg"></div>
                <div class="nctouch-bottom-mask-block">
                    <div class="nctouch-bottom-mask-tip"><i></i><?= __('点击此处返回'); ?></div>
                    <div class="nctouch-bottom-mask-top store-voucher">
                        <i class="iconfont icon-stores"></i> <%=cart_list[i].shop_name%>&nbsp;&nbsp;<?= __('领取店铺代金券'); ?>
                        <a href="javascript:void(0);" class="nctouch-bottom-mask-close"><i></i></a>
                    </div>
                    <div class="nctouch-bottom-mask-rolling nctouch-bottom-mask-rolling<%=i%>">
                        <div class="nctouch-bottom-mask-con">
                            <ul class="nctouch-voucher-list">
                                <% for (var j=0; j < cart_list[i].shop_voucher.length; j++) { var voucher =
                                cart_list[i].shop_voucher[j];%>
                                <li>
                                    <dl>
                                        <dt class="money"><?= __('面额'); ?><em><%=voucher.voucher_t_price%></em><?= __('元'); ?></dt>
                                        <dd class="need"><?= __('需消费'); ?><%=voucher.voucher_t_limit%><?= __('使用'); ?></dd>
                                        <dd class="time"><?= __('至'); ?><%=voucher.voucher_t_end_date%><?= __('前使用'); ?></dd>
                                    </dl>
                                    <a href="javascript:void(0);" class="btn" data-tid=<%=voucher.voucher_t_id%>><?= __('领取'); ?></a>
                                </li>
                                <% } %>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <% } %>
        </div><%}%>
        <% if (check_out === true) {%>
        <div class="nctouch-cart-bottom bot-2 clearfix">
            <div class="all-check fl pl-20">
                <input class="all_checkbox" type="checkbox" checked> <span class="selected-all fz-30 col6"><?= __('全选'); ?></span>
            </div>
			<div class="fr">
				<div class="total">
				    <dl class="total-money">
				        <dt class="fz-28 col3"><?= __('合计：'); ?></dt>
				        <dd><?= __('￥'); ?><em><%=sum%></em></dd>
				    </dl>
				</div>
				<div id="batchRemove"><?= __('删除'); ?></div>
				<div class="check-out ok">
				    <a href="javascript:void(0)" id="productNumber"><?= __('结算'); ?></a>
				</div>
			</div>
            
        </div><% } else { %>
        <div class="nctouch-cart-bottom no-login">
            <div class="cart-nologin-tip"><?= __('为了您的购物有更好的体验请优先登录'); ?></div>
            <div class="cart-nologin-btn"><a href="../tmpl/member/login.html" class="btn"><?= __('登录'); ?></a>
                <!-- <a href="../tmpl/member/register.html" class="btn"><?= __('注册'); ?></a> -->
            </div>
        </div><% } %><%}else{%>
        <div class="nctouch-norecord cart">
            <div class="norecord-ico"><i></i></div>
            <dl>
                <dt class="colbc"><?= __('购物车空空如也'); ?></dt>

            </dl>

        </div><%}%>
    </script>


    <script id="cart-list1" type="text/html">
        <% if(cart_list.length >0){ %>
           <ul class="nctouch-cart-item nctouch-cart-item-new">
            <% for (var i = 0;i< cart_list.length;i++){ %>
                <div class="nctouch-cart-container">
                    <dl class="nctouch-cart-store bgf borb1">
                        <dt>
                            <span class="store-check">
        					    <input class="store_checkbox" type="checkbox" checked>
        					</span>
                            <i class="iconfont icon-stores"></i> <%=cart_list[i].store_name%>
                           <!-- <span class="JS-edit fr"><?= __('编辑'); ?></span> -->
                        </dt>
                    </dl>
                </div>
                <% if (cart_list[i].goods) { %> 
                    <% for (var j=0; j< cart_list[i].goods.length; j++) {var goods=cart_list[i].goods[j];%>
                        <li cart_id="<%=goods.cart_id%>" class="cart-litemw-cnt borb1">
                            <div class="buy-li">
                                <div class="goods-check">
                                    <input type="checkbox" checked name="cart_id" data-num="<%= goods.goods_num %>"
                                           value="<%=goods.cart_id%>"/>
                                </div>
                                <div class="goods-pic">
                                    <a href="<%=WapSiteUrl%>/tmpl/product_detail.html?goods_id=<%=goods.goods_id%>">
                                        <img src="<%=goods.goods_image_url%>"/> </a>
                                </div>
                                <dl class="goods-info fr">
                                    <dt class="goods-name">
                                        <a class="z-dhwz iblock" href="<%=WapSiteUrl%>/tmpl/product_detail.html?goods_id=<%=goods.goods_id%>">
                                            <%=goods.goods_name%> </a>
                                    </dt>
                                   <!-- <dd class="goods-type"><%=goods.goods_spec%></dd> -->
                                    <%if(goods.goods_spec){%>
                                    <dd class="goods-type">
                                        <span class="zk-cart-type-sel"><em><%=goods.goods_spec%></em><i class="iconfont icon-xiala align-top"></i></span>
                                    </dd>
                                    <%}%>
                                    <dd class="iblock">
                                        <span class="goods-price"><b class="rmb"><?= __('￥'); ?></b><em><%=goods.goods_price%></em></span>
                                        <!-- <span class="fr nums">x<%=goods.goods_num%></span> -->
                                        <div class="value-box">
                                            <span class="minus">
                                                <a href="javascript:void(0);">-</a>
                                            </span>

                                            <!-- s <?= __('获取并设置限购数量'); ?> 2017.5.2 -->
                                            <span>
                                            <input type="number" min="1" class="buy-num buynum"
                                                   value="<%=goods.goods_num%>"/>
                                        </span>
                                            <span class="add">
                                                <a href="javascript:void(0);">&nbsp;</a>
                                            </span>
                                        </div>
                                    </dd>
                                </dl>
                                <div class="edit-area">
                                    <div class="goods-del" cart_id="<%=goods.cart_id%>">
                                        <?= __('删除'); ?>
                                    </div>
                                    <div class="goods-subtotal">
                                        <div class="value-box">
                                            <span class="minus">
                                                <a href="javascript:void(0);">&nbsp;</a>
                                            </span>
                                            <span>
                                                <input type="number" min="1" class="buy-num buynum"
                                                       value="<%=goods.goods_num%>"/>
                                            </span>
                                            <span class="add">
                                                <a href="javascript:void(0);">&nbsp;</a>
                                            </span>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </li>
                    <% } %> 
                <% } %>

            <%}%>
            </ul>




            <% if (check_out === true) {%>
            <div class="nctouch-cart-bottom">
                <div class="all-check fl pl-20">
                    <input class="all_checkbox" type="checkbox" checked> <span class="selected-all fz-30 col2"><?= __('全选'); ?></span>
                </div>
                <div class="total">
                    <dl class="total-money">
                        <dt class="fz-28 col3"><?= __('合计：'); ?></dt>
                        <dd><?= __('￥'); ?><em><%=sum%></em></dd>
                    </dl>
                </div>
                <div class="check-out ok">
                    <a href="javascript:void(0)"><?= __('结算'); ?></a>
                </div>
            </div>
            <% } else { %>
            <div class="nctouch-cart-bottom no-login">
                <div class="cart-nologin-tip clearfix"><span class="fl"><?= __('为了您的购物有更好的体验请优先登录'); ?></span><a
                            href="../tmpl/member/login.html" class="fz-26 fr col2"><b class="align-middle"><?= __('去登录'); ?></b><i
                                class="iconfont icon-arrow-right align-middle colbc ml-10"></i></a></div>
            </div>
            <% } %>
        <%}else{%>
            <div class="nctouch-norecord cart">
                <div class="norecord-ico"><i></i></div>
                <dl>
                    <dt class="colbc"><?= __('购物车空空如也'); ?></dt>

                </dl>

            </div>
        <%}%>
    </script>

    <!-- <?= __('底部'); ?> -->
    <?php
    // include __DIR__ . '/../includes/footer_menu.php';
    ?>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/cart-list.js"></script>

    </body>
    </html>
<?php
include __DIR__ . '/../includes/footer.php';
?>