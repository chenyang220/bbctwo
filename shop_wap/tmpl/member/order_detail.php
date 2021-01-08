<?php
    include __DIR__ . '/../../includes/header.php';
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
    <title><?= __('订单详情'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    <link rel="stylesheet" href="../../css/iconfont.css">
</head>

<body>
<iframe style='width:1px;height:1px;' src="<?php echo $PayCenterWapUrl.'?ctl=Index&met=iframe';?>"></iframe>
<header id="header" class="fixed">
    <div class="header-wrap">
        <!-- <div class="header-l"><a href="javascript:history.go(-1)"> <i class="back"></i> </a></div> -->
        <div class="header-title">
            <h1><?= __('订单详情'); ?></h1>
        </div>
        <div class="header-r">
            <!-- <a id="header-nav" href="javascript:void(0);"> <i class="more"></i> <sup></sup> </a> -->
        </div>
    </div>
    <div class="nctouch-nav-layout">
        <div class="nctouch-nav-menu">
            <span class="arrow"></span>
            <ul>
                <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                    <li><a href="../store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                    <li><a href="../store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                <?php }else{ ?>
                    <li><a href="../../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                    <li><a href="../search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                <?php }?>
                <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
            </ul>
        </div>
    </div>
</header>
<div class="nctouch-main-layout mb20">
    <div class="nctouch-order-list" id="order-info-container">
        <ul></ul>
    </div>
</div>
<footer id="footer"></footer>
<script type="text/html" id="order-info-tmpl">
    <div class="nctouch-oredr-detail-block order-status-bg">
        <h3><?= __('交易状态'); ?></h3>
        <div class="order-state"><%= order_state_con %></div>
        <%if (order_cancel_reason != ''){%>
            <div class="info colf"><i class="iconfont icon-limit"></i><em class="iblock"><%=order_cancel_reason%></em></div>
        <%}%>
        <%if(order_status == 4){%>
        <div class="time fnTimeCountDown colf fz-28" data-end="<%=order_receiver_date%>">
            <i class="icon-time"></i>
            <span class="ts">
                <?= __('剩余'); ?>
                <span class="day">00</span>
                <strong><?= __('天'); ?></strong>
                <span class="hour">00</span>
                <strong><?= __('小时'); ?></strong>
                <span class="mini">00</span>
                <strong><?= __('分'); ?></strong>
                <span class="sec">00</span>
                <strong><?= __('秒'); ?></strong>
                <?= __('自动确认收货'); ?>
            </span>
        </div>
        <% }%>
        <%if(order_status == 1){%>
        <div class="time fnTimeCountDown colf fz-28" data-end="<%=cancel_time%>">
            <i class="icon-time"></i>
            <span class="ts">
                <?= __('剩余'); ?>
                <span class="day">00</span>
                <strong><?= __('天'); ?></strong>
                <span class="hour">00</span>
                <strong><?= __('小时'); ?></strong>
                <span class="mini">00</span>
                <strong><?= __('分'); ?></strong>
                <span class="sec">00</span>
                <strong><?= __('秒'); ?></strong>
                <?= __('自动关闭订单'); ?>
            </span>
        </div>
        <% }%>
    </div>
    <!--   <%if(order_status == 4){%>
      <div class="nctouch-oredr-detail-delivery">
          <a href="<%=WapSiteUrl%>/tmpl/member/order_delivery.html?order_id=<%=order_id%>">
              <span class="time-line">
                  <i></i>
              </span>
              <div class="info">
                  <p id="delivery_content"></p>
                  <time id="delivery_time"></time>
              </div>
              <span class="arrow-r"></span>
          </a>
      </div>
      <%}%> -->
    <div class="goods-detail-module mb-20">
        <div class="nctouch-oredr-detail-block">
            <div class="nctouch-oredr-detail-add">
                <dl class="clearfix pb-20">
                    <% if(chain_id > 0){ %>
                    <dt class="fl"><p class="one-overflow fl w4 col4a"><%=order_receiver_name%></p></dt>
                    <dd class="fr"><p class="addr-detail  z-dhwz col4a"><%=order_receiver_contact%></p></dd>
                    <% }else{ %>
                    <dt class="clearfix mb-20"><p class="one-overflow fl w4 col4a"><%=order_receiver_name%></p>
                        <p class="fr col4a"><%=order_receiver_contact%></p></dt>
                    <dd><p class="addr-detail z-dhwz"><%=order_receiver_address%></p></dd>
                    <% } %>
                </dl>
            </div>
        </div>
        <% if(order_message != ''){ %>
        <!--<div class="nctouch-oredr-detail-block">
            <h3><i class="msg"></i><?= __('买家留言'); ?></h3>
            <div class="info"><%=order_message%></div>
        </div>-->
        <% } %>
        <div class="nctouch-oredr-detail-block borb0 pt-20">
            <%if (order_invoice != ''){%>
            <div class="order-det-overview clearfix mb-14">
                <h3 class="fl mb-0"><?= __('发票信息'); ?></h3>
                <div class="info fr"><%=order_invoice%></div>
            </div>
            <%}%> <%if (payment_name != ''){%>
            <div class="order-det-overview clearfix">
                <h3 class="fl mb-0"><?= __('付款方式'); ?></h3>
                <% if(order_is_bargain == 1 && order_payment_amount <= 0){ %>
                    <div class="info fr"><?= __('砍价免费拿'); ?></div>
                <% }else{ %>
                    <div class="info fr"><%=payment_name%></div>
                <% } %>
            </div>
            <%}%>
        </div>
    </div>
    <!-- 预售begin -->
    <% if(is_presale==1){%>
    <div class="presale-begin bgf">
        <ul>
            <li <% if(order_status==20&&is_presale==1){%> class="active" <%}%>>
                <em>
                    <b></b>
                </em>
                <span>付定金</span>
            </li>
            <li <% if(order_status==2&&is_presale==1){%> class="active" <%}%>>
                <em>
                    <i></i>
                    <b></b>
                </em>
                <span>付尾款</span>
            </li>
            <li <% if(order_status==4&&is_presale==1){%> class="active" <%}%>>
                <em>
                    <i></i>
                    <b></b>
                </em>
                <span>商家发货</span>
            </li>
            <li <% if(order_status==6&&is_presale==1){%> class="active" <%}%>>
                <em>
                    <i></i>
                    <b></b>
                </em>
                <span>交易完成</span>
            </li>
        </ul>
        <p class="presale-rules">注：未在指定时间内支付尾款，则定金不予退还！</p>
    </div>
    <% }%>
    <!-- 预售end -->
    <!-- <?= __('门店自提'); ?> -->
    <% if(chain_id > 0){ %>
    <div class="order-ziti-addr col5 bgf">
        <dl class="pt-30 pl-20 pr-20">
            <dt><?= __('店铺地址：'); ?></dt>
            <dd>
                <%=chain_info.chain_province+chain_info.chain_city+chain_info.chain_county+chain_info.chain_address%>
            </dd>
        </dl>
        <dl class="pt-30 pb-30 pl-20 pr-20 relative wp100 box-size">
            <dt><?= __('联系电话：'); ?></dt>
            <dd><%=chain_info.chain_mobile%></dd>
            <a onclick="dial('<%=chain_info.chain_mobile%>')" href="javascript:void(0)" class="btn-phone"> <i class="icon icon-phone"></i> </a>
        </dl>
        <dl class="pt-30 pb-30 pl-20 pr-20 bort1 borb1">
            <dt><?= __('提货码：'); ?></dt>
            <% if(chain_code.chain_code_id){ %>
            <dd><%=chain_code.chain_code_id%></dd>
            <% }else{ %>
            <dd><?= __('无'); ?></dd>
            <% } %>
        </dl>
    </div>
    <% } %>
    <!--<?= __('店铺信息'); ?>-->
    <div class="nctouch-order-item">
        <div class="nctouch-order-item-head bgf borb1">
            <%if (shop_self_support){%>
                <a href="<%=WapSiteUrl%>/tmpl/store.html?shop_id=<%=shop_id%>" class="store">
                    <i class="iconfont icon-stores mr-10 fz-30 align-middle"></i>
                    <strong class="store-tit-text align-middle one-overflow"><%=shop_name%></strong>
                    <i class="iconfont icon-arrow-right iblock align-middle fz-26 col9 ml-10 "></i>
                </a>
                <% }else{ %>
                <a href="<%=WapSiteUrl%>/tmpl/store.html?shop_id=<%=shop_id%>" class="store">
                    <i class="icon"></i>
                    <%=shop_name%>
                    <i class="arrow-r"></i>
                </a>
            <%}%>
        </div>
        <!--<?= __('商品信息'); ?>-->
        <div class="nctouch-order-item-con">
            <% for(i=0; i < goods_list.length; i++){%>
            <% if (goods_list[i].goods_refund_status == 0 && order_status == 6 && goods_list[i].goods_price !=0 && goods_list[i].order_goods_num > goods_list[i].order_goods_returnnum) { %>
            <div class="goods-block detail borb1 mb-20 return_block_height">
                <% }else{ %>
                <div class="goods-block detail clearfix goods-det-module">
                    <%}%>
                    <a href="<%=WapSiteUrl%>/tmpl/product_detail.html?goods_id=<%=goods_list[i].goods_id%>" class="wp100">
                        <div class="goods-pic">
                            <img src="<%=goods_list[i].goods_image%>">
                        </div>
                        <dl class="goods-info">
                            <dt class="goods-name"><%=goods_list[i].goods_name%></dt>
                            <% var order_spec_info = '';
                                    if(goods_list[i].order_spec_info && goods_list[i].order_spec_info.length > 0){
                                        for(var j in goods_list[i].order_spec_info){
                                            order_spec_info += goods_list[i].order_spec_info[j] + '; ';
                                        }
                            %>
                            <dd class="goods-type one-overflow"><%=order_spec_info%></dd>
                            <% } %>
                        </dl>
                        <div class="goods-subtotal">
                            <% if(goods_list[i].pintuan_temp_order == 1){ %>
                                <span class="goods-price"><?= __('￥'); ?><em><%=goods_list[i].order_goods_amount%></em></span>
                                <span class="old-goods-price"><?= __('￥'); ?><em><%=goods_list[i].goods_price%></em></span>
                            <% }else{ %>
                                <span class="goods-price"><?= __('￥'); ?><em><%=goods_list[i].goods_price%></em></span>
                            <% } %>
                            <span class="goods-num">x<%=goods_list[i].order_goods_num%></span>
                        </div>
                    </a>
                    <div class="return-tips tr fz0">
                        <% if(goods_list[i].goods_return_status == 1 && goods_list[i].return_shop_handle == 1 ) {%>
                            <a class="ml4 cancel-refund" order_return_id="<%=goods_list[i].order_return_id%>"><span><?= __('取消退款'); ?></span></a>
                        <% } %>
                        <div class="fz-24 iblock ">
                            <% if(goods_list[i].goods_return_status > 0) {%>
                            <a href="<%=WapSiteUrl%>/tmpl/member/member_refund_info.html?refund_id=<%=goods_list[i].order_return_id%>" class="ml4">
                                <span class="default-color">
                                    <%=goods_list[i].goods_return_status_con%>
                                </span>
                            </a>
                            <% } %>
                            <% if(goods_list[i].goods_refund_status > 0) {%>
                            <a href="<%=WapSiteUrl%>/tmpl/member/member_return_info.html?refund_id=<%=goods_list[i].order_refund_id%>" class="ml4">
                                <span class="default-color"><%=goods_list[i].goods_refund_status_con%></span>
                            </a>
                            <% } %>
                        </div>
                        <div class="iblock" style="vertical-align: middle;">
                            <% if(goods_list[i].pintuan_temp_order == 1 || order_is_bargain == 1){ %>
                                <a href="javascript:void(0)"></a>
                            <% }else{ %>
                                <% if (goods_list[i].goods_refund_status == 0 && order_status == 6 && goods_list[i].goods_price !=0 && goods_list[i].order_goods_num*1 > goods_list[i].order_goods_returnnum*1 && goods_list[i].rgl_val !=1) {%>
                                    <% if (goods_list[i].rgl_flag){%>
										<a href="javascript:void(0)" order_id="<%=order_id%>" order_goods_id="<%=goods_list[i].order_goods_id%>" class="goods-return fr"><?= __('退货'); ?></a>
									<% } %>
								<% } %>
                                <% if (goods_list[i].goods_return_status == 0 && (order_status == 2 || (order_status == 11 && payment_id == 1)) && goods_list[i].goods_price !=0) { %>
									<% if (goods_list[i].rgl_flag){ %>                                   
										<a href="javascript:void(0)" order_id="<%=order_id%>" order_goods_id="<%=goods_list[i].order_goods_id%>" class="goods-return fr"><?= __('退款'); ?></a>
									<% } %>
								<% } %>
                            <% } %>
                        </div>
                    </div>
                </div>
                <%}%>
                <!-- <?= __('门店自提'); ?> -->
                <% if(chain_id > 0){ %>
                    <div class="order-ziti-addr order-ziti-message col5 bgf">
                        <dl class="pl-20 pr-20 pt-20 pb-20">
                            <dt class=""><?= __('买家留言：'); ?></dt>
                            <% if(order_message){ %>
                                <dd class="one-overflow mt1"><%=order_message%></dd>
                            <% }else{ %>
                                <div class="info col5 fr"><?= __('无'); ?></div>
                            <% } %>
                        </dl>
                    </div>
                    <div class="nctouch-oredr-detail-block bort1 borb0 order-ziti-det">
                        <% if(voucher_price > 0){ %>
                            <div class="order-det-overview clearfix pl-20 pr-20 pt-20 pb-20">
                                <h3 class="fl mb-0"><?= __('店铺代金券'); ?></h3>
                                <div class="info col5 fr"><?= __('减￥'); ?><%=voucher_price%></div>
                            </div>
                        <% } %>
                        <% if(order_rpt_price > 0){ %>
                            <div class="order-det-overview clearfix pl-20 pr-20 pt-20 pb-20">
                                <h3 class="fl mb-0"><?= __('平台红包'); ?></h3>
                                <div class="info col5 fr"><?= __('减￥'); ?><%=order_rpt_price%></div>
                            </div>
                        <% } %>
                        <% if(order_user_discount > 0){ %>
                            <div class="order-det-overview clearfix pl-20 pr-20 pt-20 pb-20">
                                <h3 class="fl mb-0"><?= __('会员折扣'); ?></h3>
                                <div class="info col5 fr"><?= __('减￥'); ?><%=order_user_discount%></div>
                            </div>
                        <% } %>
                        <div class="order-det-overview clearfix pl-20 pr-20 pt-20 pb-20">
                            <div class="info fr default-color"><?= __('合计：￥'); ?><%=order_payment_amount%></div>
                        </div>
                    </div>
                <% } else { %>
                <div class="nctouch-oredr-detail-block pl-20 pr-20 pt-20 pb-20 bort1  mb-20">
                    <div class="order-buyer-message">
                        <dl>
                            <dt class="clearfix hgauto">
                                <h4 class="col3">
                                    <i class="iconfont icon-news mr-20 col9 fz-32 align-middle"></i>
                                    <?= __('买家留言'); ?>
                                </h4>
                                <% if(order_message != ''){ %>
                                <p class="z-dhwz col9 mt-10"><%=order_message%></p>
                                <% } %>
                            </dt>
                        </dl>
                    </div>
                </div>

                <!-- 预售begin -->
                <% if(is_presale==1){ %>
                <div class="goods-subtotle bgf mt-20 mb-20">
                       
                        <dl class="t">
                            <% if(order_status==1&&is_presale==1) {%>
                                <dt>阶段1：定金（待付款）</dt>
                            <% }else if ((order_status==20&&is_presale==1)||(order_status==2&&is_presale==1)) {%>
                                <dt>阶段1：定金（已付）</dt>
                            <% }%>
                            <dd>￥<em><%=presale_deposit%></em></dd>
                        </dl>
                        <dl class="t">
                            <% if((order_status==20&&is_presale==1)||(order_status==1&&is_presale==1)) {%>
                                <dt>阶段2：尾款（待付款）</dt>
                            <% }else if(order_status==2&&is_presale==1) {%>
                                <dt>阶段2：尾款（已付）</dt>
                            <% } %>
                            
                            <dd>￥<em><%=final_price%></em></dd>
                        </dl>
                </div>
                <%}%>
                <!-- 预售end -->

            <% if(order_status!=20){%>
                <div class="goods-subtotle bgf">
                    <dl>
                        <dt><?= __('运费'); ?></dt>
                        <dd class="col8"><?= __('￥'); ?><em><%=order_shipping_fee%></em></dd>
                    </dl>
                    <!-- 12.20<?= __('新增加'); ?>-->
                    <% if(voucher_price > 0){ %>
                        <dl>
                            <dt><?= __('店铺优惠券'); ?></dt>
                            <dd class="col-ed55"><?= __('减￥'); ?><%=voucher_price%></dd>
                        </dl>
                    <% } %>
                    <% if(order_rpt_price > 0){ %>
                        <dl>
                            <dt><?= __('平台红包'); ?></dt>
                            <dd class="col-ed55"><?= __('减￥'); ?><%=order_rpt_price%></dd>
                        </dl>
                    <% } %>
                    <% if(order_user_discount > 0){ %>
                        <dl>
                            <dt><?= __('会员折扣'); ?></dt>
                            <!-- <dd class="col-ed55"><?= __('减￥'); ?><%=order_user_discount%></dd> -->
                            <dd class="col-ed55"><%=new_benefit%></dd>
                        </dl>
                    <% } %>
                    <dl class="t">
                        <dt>
                            <%if(order_status == 1 && order_payment_amount > 0){ %>
                            <?= __('应付款'); ?>
                            <% }else{ %>
                            <?= __('实付款'); ?>
                            <% } %>
                            <em class="col8 fz4"><?= __('（含运费'); ?><?= __('）'); ?></em>
                        </dt>
                        <dd><?= __('￥'); ?><em><%=order_payment_amount%></em></dd>
                    </dl>
                </div>
                <% } %>
                <% } %>
        </div>
        <span class="im-contact" style="display: none">
            <a href="javascript:void(0);" class="kefu" style="display: none"><i class="im"></i><?= __('联系客服'); ?></a>
        </span>
        <% if(shop_phone){ %>
        <span class="to-call">
            <a href="tel:<%=shop_phone%>" tel="<%=shop_phone%>"><i class="tel"></i><?= __('拨打电话'); ?></a>
        </span>
        <% } %>
        <div class="nctouch-oredr-detail-block mt5">
            <ul class="order-log">
                <li><?= __('订单编号：'); ?><%=order_id%></li>
                <li><?= __('创建时间：'); ?><%=order_create_time%></li>
                <% if(payment_time !== '0000-00-00 00:00:00'){%>
                    <li><?= __('付款时间：'); ?><%=payment_time%></li>
                <%}%>
                <% if(order_shipping_time !== '0000-00-00 00:00:00'){%>
                <li><?= __('发货时间：'); ?><%=order_shipping_time%></li>
                <%}%>
                <% if(order_finished_time !== '0000-00-00 00:00:00'){%>
                <li><?= __('完成时间：'); ?><%=order_finished_time%></li>
                <%}%>
                <% if(chain_id > 0){ %>
                    <% if(order_finished_time !== '0000-00-00 00:00:00'){%>
                        <li><?= __('提货时间：'); ?><%=order_finished_time%></li>
                    <%}%>
                <% } %>
                <% if(order_buyer_evaluation_time !== '0000-00-00 00:00:00'){%>
                    <li><?= __('评价时间：'); ?><%=order_buyer_evaluation_time %></li>
                <%}%>
            </ul>
        </div>
        <div class="nctouch-oredr-detail-bottom">
            <!--<?= __('退款'); ?>/<?= __('货状态'); ?>-->
            <% if (order_return_status == 1 || order_refund_status == 1) {%>
                <p><?= __('退款'); ?>/<?= __('退货中'); ?>...</p>
            <% } %>
            <!--<?= __('取消状态'); ?>-->
            <% if (order_is_bargain != 1) {%>
                <% if (order_status == 1 || (order_status == 3 && payment_id == 2)||order_status==20) {%>
                    <a href="javascript:void(0)" order_id="<%=order_id%>" class="btn cancel-order" order_from="<%=order_from%>"><?= __('取消订单'); ?></a>
                <% } %>
            <% } %>
            <!--<?= __('物流信息'); ?>-->
            <% if (order_status == 4) { %>
                <a href="javascript:void(0)" order_id="<%=order_id%>" express_id="<%=order_shipping_express_id%>" shipping_code="<%=shipping_code%>" class="btn viewdelivery-order"><?= __('查看物流'); ?></a>
            <%}%>
            <!--<?= __('确认收货'); ?>-->
            <% if (order_status == 4){ %>
                <a href="javascript:void(0)" order_id="<%=order_id%>" class="btn key sure-order"><?= __('确认收货'); ?></a>
            <% } %>
            <!--<?= __('删除订单'); ?>-->
            <% if (order_status == 6 || order_status == 7) {%>
                <a href="javascript:void(0)" order_id="<%=order_id%>" class="btn delete-order"><?= __('删除订单'); ?></a>
            <% } %>
            <!--<?= __('评价订单'); ?>-->
            <% if (order_status == 6 && order_buyer_evaluation_status == 0) {%>
                <% if (order_refund_status < 1 && (order_nums*1 > order_refund_nums*1)){ %>
                    <a href="javascript:void(0)" order_id="<%=order_id%>" class="btn key evaluation-order"><?= __('评价订单'); ?></a>
                <% } %>
            <% } %>
            <!--<?= __('自提订单'); ?>-->
            <%if(chain_id > 0){ %>
                <!--<?= __('查看评价'); ?>-->
                <% if (order_buyer_evaluation_status == 1 && order_refund_status < 1){ %>
                    <a href="javascript:void(0)" order_id="<%=order_id%>" class="btn evaluation-again-order"> <?= __('查看评价'); ?> </a>
                <% } %>
            
            <% } else { %>
                <!--<?= __('追评'); ?>-->
                <% if (order_buyer_evaluation_status == 1 && order_refund_status < 1){ %>
                    <a href="javascript:void(0)" order_id="<%=order_id%>" class="btn evaluation-again-order"> <?= __('查看评价'); ?> </a>
                <% } %>
            
            <% } %>
            <!--<?= __('订单支付'); ?>-->
            <% if(order_status == 1 && order_payment_amount > 0){ %>
                <a href="javascript:" onclick="payOrder('<%= payment_number %>','<%=order_id %>')" data-paySn="<%=order_id %>" class="btn key check-payment"><?= __('订单支付'); ?></a> 
            <% } %>

            <% if(order_status == 20 && order_payment_amount > 0&&is_final_start){ %>
                <a href="javascript:" onclick="payOrder('<%= payment_other_number %>','<%=order_id %>')" data-paySn="<%=order_id %>" class="btn key check-payment"><?= __('支付尾款'); ?></a> 
            <% } %>
        </div>
    </script>
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/template.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/tmpl/order_detail.js"></script>
    <script type="text/javascript" src="../../js/jquery.timeCountDown.js"></script>
    </body>
    </html>
<?php
    include __DIR__ . '/../../includes/footer.php';
?>
