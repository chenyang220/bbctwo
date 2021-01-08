<?php
    include __DIR__.'/../../includes/header.php';

    $data_state = $_GET['data-state'] ? $_GET['data-state'] : "";
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
        <title><?= __('实物订单'); ?></title>
        <link rel="stylesheet" type="text/css" href="../../css/base.css">
        <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
        <link rel="stylesheet" type="text/css" href="../../css/nctouch_common.css">
        <link rel="stylesheet" type="text/css" href="../../css/nctouch_cart.css">
        <link rel="stylesheet" href="../../css/iconfont.css">
    </head>
    <script type="text/javascript">
        //用app电话号码登录
        var u_id = '<?php echo $u_id;?>';
        if (u_id) {
           window.location.href = UCenterApiUrl + '/?ctl=Login&met=oauth&typ=e&u_id=' + u_id + "&return_url=" + WapSiteUrl + "/tmpl/member/order_list.html?data-state=<?=$data_state?>";
        }
    </script>
    <body>   
    <header id="header" class="fixed">
        <div class="header-wrap">
            <!-- <div class="header-l"><a href="member.html"><i class="back"></i></a></div> -->
            <div class="header-title posr">
                <h1 class="drap-h1-after" id="z-tab-order" data-order_state="all">实物订单</h1>
            </div>
            <!-- <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a> </div> -->
        </div>
        <div class="nctouch-nav-layout">
            <div class="nctouch-nav-menu"> <span class="arrow"></span>
                <ul>
                    <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                        <li><a href="../store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }else{ ?>
                        <li><a href="../../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }?>
                    <li><a href="../cart_list.html"><i class="cart"></i><?= __('购物车'); ?></a><sup></sup></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
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
                <span class="ser-area "><i class="icon-ser"></i><input type="text" autocomplete="on" maxlength="50" placeholder="<?= __('输入商品标题或订单号进行搜索'); ?>" name="order_key" id="order_key" oninput="writeClear($(this));" >
      <span class="input-del"></span></span>
                <input type="button" id="search_btn" value="<?= __('搜索'); ?>">
            </form>
        </div>
        <div id="fixed_nav" class="nctouch-single-nav borb1">
            <ul id="filtrate_ul" class="w20h">
                <li class="selected"><a href="javascript:void(0);" data-state=""><?= __('全部'); ?></a></li>
                <li><a href="javascript:void(0);" data-state="wait_pay"><?= __('待付款'); ?></a></li>
                <li><a href="javascript:void(0);" data-state="order_payed"><?= __('待发货'); ?></a></li>
                <li><a href="javascript:void(0);" data-state="wait_confirm_goods"><?= __('待收货'); ?></a></li>
                <li><a href="javascript:void(0);" data-state="finish"><?= __('待评价'); ?></a></li>
            </ul>
        </div>
        <div class="nctouch-order-list">
            <ul id="order-list" class="mt-20">
            </ul>
        </div>
        <!--<?= __('底部总金额固定层'); ?>End-->
        <div class="nctouch-bottom-mask down">
            <div class="nctouch-bottom-mask-bg"></div>
            <div class="nctouch-bottom-mask-block">
                <div class="nctouch-bottom-mask-tip"><i></i><?= __('点击此处返回'); ?></div>
                <div class="nctouch-bottom-mask-top">
                    <p class="nctouch-cart-num"><?= __('本次交易需在线支付'); ?><em id="onlineTotal">0.00</em><?= __('元'); ?></p>
                    <p style="display:none" id="isPayed"></p>
                    <a href="javascript:void(0);" class="nctouch-bottom-mask-close"><i></i></a> </div>
                <div class="nctouch-inp-con nctouch-inp-cart">
                    <ul class="form-box" id="internalPay">
                        <p class="rpt_error_tip" style="display:none;color:red;"></p>
                        <li class="form-item" id="wrapperUseRCBpay">
                            <div class="input-box pl5">
                                <label>
                                    <input type="checkbox" class="checkbox" id="useRCBpay" autocomplete="off" /> <?= __('使用充值卡支付'); ?> <span class="power"><i></i></span> </label>
                                <p><?= __('可用充值卡余额'); ?> <?= __('￥'); ?><em id="availableRcBalance"></em></p>
                            </div>
                        </li>
                        <li class="form-item" id="wrapperUsePDpy">
                            <div class="input-box pl5">
                                <label>
                                    <input type="checkbox" class="checkbox" id="usePDpy" autocomplete="off" /> <?= __('使用预存款支付'); ?> <span class="power"><i></i></span> </label>
                                <p><?= __('可用预存款余额'); ?> <?= __('￥'); ?><em id="availablePredeposit"></em></p>
                            </div>
                        </li>
                        <li class="form-item" id="wrapperPaymentPassword" style="display:none">
                            <div class="input-box"> <span class="txt"><?= __('输入支付密码'); ?></span>
                                <input type="password" class="inp" id="paymentPassword" autocomplete="off" />
                                <span class="input-del"></span></div>
                            <a href="../member/member_paypwd_step1.html" class="input-box-help" style="display:none"><i>i</i><?= __('尚未设置'); ?></a> </li>
                    </ul>
                    <div class="nctouch-pay">
                        <div class="spacing-div"><span><?= __('在线支付方式'); ?></span></div>
                        <div class="pay-sel">
                            <label style="display:none">
                                <input type="radio" name="payment_code" class="checkbox" id="alipay" autocomplete="off" />
                                <span class="alipay"><?= __('支付宝'); ?></span></label>
                            <label style="display:none">
                                <input type="radio" name="payment_code" class="checkbox" id="wxpay_jsapi" autocomplete="off" />
                                <span class="wxpay"><?= __('微信'); ?></span></label>
                        </div>
                    </div>
                    <div class="pay-btn"> <a href="javascript:void(0);" id="toPay" class="btn-l"><?= __('确认支付'); ?></a> </div>
                </div>
            </div>
        </div>
    </div>
    <div class="fix-block-r">
        <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
    </div>
    <footer id="footer" class="bottom"></footer>
    <script type="text/html" id="order-list-tmpl">
        <% var orderlist = data.items; %>
        <% if (orderlist.length > 0){%>
        <% for(var i = 0;i<orderlist.length;i++){
        var orderinfo = orderlist[i];
        var goodslist = orderinfo.goods_list;
        var pintuan_person_num = orderinfo.pintuan_person_num;
        var pintuan_type = orderinfo.pintuan_type;
        var pintuan_temp_order = orderinfo.pintuan_temp_order;
        %>
        <li class="<%if(orderinfo.order_payment_amount){%>green-order-skin<%}else{%>gray-order-skin<%}%> <%if(i>0){%>mt-20<%}%>">
            
            <div class="nctouch-order-item">
                <div class="nctouch-order-item-head">
                    <%if (orderinfo.shop_self_support){%>
                    <a class="store one-overflow"><i class="iconfont icon-stores mr-10 iblock align-middle"></i><?= __('自营店铺'); ?></a>
                    <%}else{%>
                    <a href="<%=WapSiteUrl%>/tmpl/store.html?shop_id=<%=orderinfo.shop_id%>" class="store"><i class="iconfont icon-stores mr-10 iblock align-middle fz-30"></i><strong class="iblock align-middle one-overflow mwp50 wauto"><%= orderinfo.shop_name %></strong><i class="iconfont icon-arrow-right ml-10 iblock align-middle fz-26 col9"></i> </a>
                    <%}%>
                    <span class="state">
                                                    <%
                                                    if(pintuan_temp_order == 1){
                                                        if(pintuan_type == 1)
                                                        {
                                                    %>
                                                        1<?= __('人团'); ?>
                                                    <%
                                                        }
                                                        else
                                                        {
                                                            if(pintuan_person_num){
                                                    %>
                                                        <%=pintuan_person_num%><?= __('人团'); ?>
                                                    <% } } }%>

                        							<%
                        								var stateClass ="ot-finish";
                        								var orderstate = orderinfo.order_status;
                        								if(orderstate == 2 || orderstate == 3 || orderstate == 4 || orderstate == 5){
                        									stateClass = stateClass;
                        								}else if(orderstate == 7) {
                        									stateClass = "ot-cancel";
                        								}else {
                        									stateClass = "ot-nofinish";
                        								}
                        							%>
                        							<span class="<%=stateClass%>"><%=orderinfo.order_state_con%></span>
                                                </span>
                </div>
                <div class="nctouch-order-item-con nctouch-order-item-con-cart">
                    <%
                    if(goodslist){
                    for(var j = 0;j<goodslist.length;j++){
                    var order_goods = goodslist[j];
                    %>
                    <div class="goods-block">
                        <%   if (orderinfo.order_detail_url){%>
                            <a href="<%=orderinfo.order_detail_url%>" class="clearfix wp100">
                        <% } else {%>
                            <a href="<%=WapSiteUrl%>/tmpl/member/order_detail.html?order_id=<%=orderinfo.order_id%>" class="clearfix wp100">
                        <% } %>
                            <div class="">
                                <div class="goods-pic">
                                    <% if(order_goods.is_del == 2){ %>
                                	<p class="old-Failed"><?= __('此商品'); ?><br/><?= __('已失效'); ?></p>
                                    <% } %>
                                    <img src="<%=order_goods.goods_image%>" />
                                    <!-- 1113<?= __('拼团图标'); ?> -->
                                    <!-- <b class="icon-tg"></b> -->
                                </div>
                                <dl class="goods-info">
                                    <dt class="goods-name"><%=order_goods.goods_name%></dt>
                                    <% if(order_goods.title_order_spec_info){ %>
                                    <dd class="goods-type one-overflow">
                                        <%=order_goods.title_order_spec_info%>
                                    </dd>
                                    <% } %>
                                </dl>
                            </div>
                            
                            <div class="goods-subtotal">
                                <%
                                if(pintuan_temp_order == 1){
                                %>
                                <span class="goods-price"><?= __('￥'); ?><em><%=order_goods.order_goods_payment_amount%></em></span>
                                <span class="old-goods-price"><?= __('￥'); ?><em><%=order_goods.old_price%></em></span>
                                <%}else{%>
                                <span class="goods-price"><?= __('￥'); ?><em><%=order_goods.goods_price%></em></span>
                                <%}%>
                                <!-- 3.6.7-砍价 -->
                                <% if(orderinfo.order_is_bargain == 1) {%>
                                    <em class="bargain-order-tips">砍价成功</em>
                                    <span class="goods-num fz-24 colbc">x<%=order_goods.order_goods_num%></span>
                                <% } %>
                            </div>
                        </a>
                        <div class="return-tips tr">
                            <!-- <?= __('退款状态'); ?> -->
                            <div class="fz-24">
                                <% if(order_goods.goods_return_status > 0) {%>
                                <a href="<%=WapSiteUrl%>/tmpl/member/member_refund_info.html?refund_id=<%=order_goods.order_return_id%>" class=''><span class="default-color"><%=order_goods.goods_return_status_con%></span></a>
                                <% } %>
                                <% if(order_goods.goods_refund_status > 0) {%>
                                <a href="<%=WapSiteUrl%>/tmpl/member/member_return_info.html?refund_id=<%=order_goods.order_refund_id%>" class=''><span class="default-color"><%=order_goods.goods_refund_status_con%></span></a>
                                <% } %>
                            </div>
                        </div>
                    </div>
                    <%}%>
                </div>
                <div class="nctouch-order-item-footer">
                    <div class="store-totle">
                        <span><?= __('共'); ?><em><%=orderinfo.order_nums%></em><?= __('件商品，合计'); ?></span><span class="sum"><?= __('￥'); ?><em><%= p2f(orderinfo.order_payment_amount) %></em></span><span class="freight">(<?= __('含运费￥'); ?><%=orderinfo.order_shipping_fee%>)</span>
                    
                    </div>
                    <%if(orderinfo.order_status == 1 || orderinfo.order_status == 3 || orderinfo.order_status == 4 || orderinfo.order_status == 6 || orderinfo.order_status == 7 ||orderinfo.order_status==20){%>
                    <div class="handle">
                        <%if(orderinfo.order_status == 7 || orderinfo.order_status == 6){%>
                        <a href="javascript:void(0)" order_id="<%=orderinfo.order_id%>" class="del delete-order btn"><?= __('删除'); ?></a>
                        <%}%>
                        <% if(orderinfo.order_is_bargain != 1){ %>
                        <%if(orderinfo.order_status == 1 || (orderinfo.order_status == 3 && orderinfo.payment_id == 2)||orderinfo.order_status==20){%>
                            <a href="javascript:void(0)" status="<%=orderinfo.order_status%>" order_id="<%=orderinfo.order_id%>" class="btn cancel-order"  order_from="<%=orderinfo.order_from%>"><?= __('取消订单'); ?></a>
                        <%}%>
                        <%}%>
                        <%if(orderinfo.order_status == 4){%>
                        <a href="javascript:void(0)" order_id="<%=orderinfo.order_id%>" express_id="<%=orderinfo.order_shipping_express_id%>" express_name="<%=orderinfo.express_name%>" shipping_code="<%=orderinfo.order_shipping_code%>" class="btn viewdelivery-order"><?= __('查看物流'); ?></a>
                        <%}%>
                        <%if(orderinfo.order_status == 4){%>
                        <a href="javascript:void(0)" order_id="<%=orderinfo.order_id%>" class="btn key sure-order"><?= __('确认收货'); ?></a>
                        <%}%>
                        <%if(orderinfo.order_status == 6){%>
                        <% if(order_goods.goods_return_status <= 0) {%>
                        <%if(orderinfo.evala_status == 1){%>
                        <a href="javascript:void(0)" order_id="<%=orderinfo.order_id%>" express_id="<%=orderinfo.order_shipping_express_id%>" express_name="<%=orderinfo.express_name%>" shipping_code="<%=orderinfo.order_shipping_code%>" class="btn viewdelivery-order"><?= __('查看物流'); ?></a>
                        <a href="javascript:void(0)" order_id="<%=orderinfo.order_id%>" class="btn key evaluation-order"><?= __('评价'); ?></a>
                        <%}else if(orderinfo.evala_status == 2){%>
                        <a href="javascript:void(0)" order_id="<%=orderinfo.order_id%>" class="btn evaluation-again-order"><?= __('追加评价'); ?></a>
                        <%}else if(orderinfo.evala_status == 3){%>
                        <a href="javascript:void(0)" order_id="<%=orderinfo.order_id%>" class="btn evaluation-again-order"><?= __('查看评价'); ?></a>
                        <%}%>
                        <%}%>
                        <%}%>
                        <%if(orderinfo.order_status == 1 && orderinfo.order_payment_amount>0){%>
                        <a href="javascript:;" onclick="payOrder('<%= orderinfo.payment_number %>','<%= orderinfo.order_id %>')" data-paySn="<%= orderinfo.order_id %>" class="btn key check-payment"><?= __('订单支付'); ?></a>
                        <%}else if(orderinfo.order_status == 20 && orderinfo.order_payment_amount>0&&orderinfo.is_final_start==1){%>
                        
                        <a href="javascript:;" onclick="payOrder('<%= orderinfo.payment_other_number %>','<%= orderinfo.order_id %>')" data-paySn="<%= orderinfo.order_id %>" class="btn key check-payment"><?= __('支付尾款'); ?></a>
                        <%}%>
                    </div>
                    <%}%>
                </div>
            </div>
        
        
        </li>
        <%}}%>
        <% if (hasmore) {%>
        <li class="loading">
            <div class="spinner"><i></i></div><?= __('订单数据读取中'); ?>...</li>
        <% } %>
        <%}else {%>
        <div class="nctouch-norecord order">
            <div class="norecord-ico"><i></i></div>
            <dl>
                <dt><?= __('您还没有相关的订单'); ?></dt>
                <dd><?= __('可以去看看哪些想要买的'); ?></dd>
            </dl>
            <a href="<%=WapSiteUrl%>" class="btn"><?= __('随便逛逛'); ?></a>
        </div>
        <%}%>
    </script>
	<iframe style='width:1px;height:1px;' src="<?php echo $PayCenterWapUrl.'?ctl=Index&met=iframe';?>"></iframe>
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/template.js"></script>
    
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/zepto.waypoints.js"></script>
    <script type="text/javascript" src="../../js/tmpl/order_payment_common.js"></script>
    <script type="text/javascript" src="../../js/tmpl/order_list.js"></script>
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
