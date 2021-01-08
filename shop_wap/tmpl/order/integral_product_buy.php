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
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('确认订单'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_cart.css">
    <link rel="stylesheet" type="text/css" href="../../css/intlTelInput.css">
    <link rel="stylesheet" type="text/css" href="../../css/private-store.css">
    <style>
        .jia-shop .fr a.min {
            background: #d5d5d5;
        }
        .jia-shop .fr a.min.disabled, .jia-shop .fr a.max.disabled{
            background: #eeeeee;
        }
        .s-dialog-btn-ok{
            border: none !important;
        }
    </style>
</head>
<body>
<header id="header" class="fixed">
    <div class="header-wrap">
        <!-- <div class="header-l"> <a href="javascript:history.go(-1)"> <i class="back"></i> </a> </div> -->
        <div class="header-title">
            <h1><?= __('确认订单'); ?></h1>
        </div>
        <!-- <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a> </div> -->
    </div>
    <div class="nctouch-nav-layout">
        <div class="nctouch-nav-menu"> <span class="arrow"></span>
            <ul>
                <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                    <li><a href="../../tmpl/store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                    <li><a href="../../tmpl/store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                <?php }else{ ?>
                    <li><a href="../../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                    <li><a href="../../tmpl/search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                <?php }?>
                <li><a href="../../tmpl/member/member.html"><i class="member"></i><?= __('我的商城'); ?></a></li>
                <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
            </ul>
        </div>
    </div>
</header>
<div id="container-fcode" class="hide">
    <div class="fcode-bg">
        <div class="con">
            <h3><?= __('您正在购买“'); ?>F<?= __('码”商品'); ?></h3>
            <h5><?= __('请输入所知的'); ?>F<?= __('码序列号并提交验证'); ?><br/>
                <?= __('系统效验后可继续完成下单'); ?></h5>
            <input type="text" name="fcode" id="fcode" placeholder="" />
            <p class="fcode_error_tip" style="display:none;color:red;"></p>
            <a href="javascript:void(0);" class="submit"><?= __('提交验证'); ?></a> </div>
    </div>
</div>
<div class="nctouch-main-layout mb20">
    <div class="nctouch-cart-block">
        <!--<?= __('正在使用的默认地址'); ?>Begin-->
        <div class="nctouch-cart-add-default borb1">
            <div class="address1 packets-type">
                <a href="javascript:void(0);" id="list-address-valve"><i class="icon-add"></i>
                <dl>
                    <input type="hidden" class="inp" name="address_id" id="address_id"/>
                    <input type="hidden" id="area_code" name="area_code">
                    <dt><?= __('收货人：'); ?><span id="true_name"></span><span id="mob_phone"></span></dt>
                    <dd><span id="address"></span></dd>
                </dl>
                <i class="icon-arrow"></i></a>
            </div>
            <!--<?= __('添加收货地址'); ?>-->
            <div class="address2 z-box2  new-address-valve">
                <div class="z-box2-tj js-add-address">
                    <img src="../../images/SHIZI.png"/>
                    <div class="z-box2-a"><?= __('添加收货人信息'); ?></div>
                </div>
            </div>
        </div>
        <!--<?= __('正在使用的默认地址'); ?>End-->
    </div>
    <!--<?= __('选择收货地址'); ?>Begin-->
    <div id="list-address-wrapper" class="nctouch-full-mask hide">
        <div class="nctouch-full-mask-bg"></div>
        <div class="nctouch-full-mask-block">
            <div class="header absolute">
                <div class="header-wrap">
                    <div class="header-l"> <a href="javascript:void(0);"> <i class="back"></i> </a> </div>
                    <div class="header-title">
                        <h1><?= __('收货地址管理'); ?></h1>
                    </div>
                </div>
            </div>
            <div class="nctouch-main-layout" style="display: block; position: absolute; top: 0; right: 0; left: 0; bottom:2rem; overflow: hidden; z-index: 1;" id="list-address-scroll">
                <ul class="nctouch-cart-add-list" id="list-address-add-list-ul">
                    
                </ul>
            </div>
            <div id="addresslist" class="mt10" style="position: absolute; right: 0; left: 0; bottom: 0; z-index: 1;"> <a href="javascript:void(0);" class="btn-l" id="new-address-valve"><?= __('新增收货地址'); ?></a> </div>
        </div>
    </div>
    <!--选择收货地址End-->
    <!--新增收货地址Begin-->
    <div id="new-address-wrapper" class="nctouch-full-mask hide">
        <div class="nctouch-full-mask-bg"></div>
        <div class="nctouch-full-mask-block">
            <div class="header">
                <div class="header-wrap">
                    <div class="header-l"> <a href="javascript:void(0);"> <i class="back"></i> </a> </div>
                    <div class="header-title">
                        <h1><?= __('新增收货地址'); ?></h1>
                    </div>
                </div>
            </div>
            <div class="nctouch-main-layout" id="new-address-scroll">
                <div class="nctouch-inp-con">
                    <form id="add_address_form">
                        <ul class="form-box">
                            <li class="form-item">
                                <h4><?= __('收货人姓名'); ?></h4>
                                <div class="input-box">
                                    <input type="text" class="inp" name="true_name" id="vtrue_name" autocomplete="off" oninput="writeClear($(this));"/>
                                    <span class="input-del"></span> </div>
                            </li>
                            <li class="form-item">
                                <h4><?= __('联系手机'); ?></h4>
                                <div class="input-box">
                                    <input type="tel" class="inp" name="mob_phone" id="re_user_mobile" autocomplete="off" oninput="writeClear($(this));"/>
                                    <input type="hidden" id="area_code" name="area_code" class="no-follow">
                                    <span class="input-del"></span> </div>
                            </li>
                            <li class="form-item">
                                <h4><?= __('地区选择'); ?></h4>
                                <div class="input-box">
                                    <input name="area_info" type="text" class="inp" id="varea_info" unselectable="on" onfocus="this.blur()" autocomplete="off" onchange="btn_check($('form'));" readonly/>
                                </div>
                            </li>
                            <li class="form-item">
                                <h4><?= __('详细地址'); ?></h4>
                                <div class="input-box">
                                    <input type="text" class="inp" name="vaddress" id="vaddress" autocomplete="off" oninput="writeClear($(this));"/>
                                    <span class="input-del"></span> </div>
                            </li>
                        </ul>
                        <div class="error-tips"></div>
                        <div class="form-btn"><a href="javascript:void(0);" class="btn"><?= __('保存地址'); ?></a></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--<?= __('新增收货地址'); ?>End-->



    <!--<?= __('商品列表'); ?>Begin-->
    <div id="goodslist_before" class="mt5">
        <div id="deposit"> 
            <div class="nctouch-cart-container">
                <ul class="nctouch-cart-item bgf bort1 borb1" id="point_goods_list">
                    
                </ul>
                <div class="nctouch-cart-subtotal">
                    <dl class="message bgf borb1">
                        <dt class=""><label for="remark"><?= __('买家留言：'); ?></label></dt>
                        <dd>
                            <input type="text" name="remark" placeholder="<?= __('店铺订单留言'); ?>" rel="" id="remark" maxlength="45">
                            <img src="../images/close_window.png" class="clearBuyerMessage hide">
                        </dd>
                    </dl>

                </div>
            </div>
        </div>
    </div>
    <!--<?= __('商品列表'); ?>End-->

    <!--<?= __('合计支付金额'); ?>Begin-->
    <div id="rptVessel" class="nctouch-cart-block mt5">
        <div class="current-con">
            <dl class="total-money">
            <?= __('合计：'); ?><span class="col4 fz8"><?= __('￥'); ?><em id="totalPrice">0.00</em></span>
            </dl>
            <dl class="total-money rate-money" style="display: none;">
                <?= __('会员折扣：'); ?><span class="col-red"><?= __('￥'); ?><em id="ratePrice">0.00</em></span>
            </dl>
        </div>
    </div>
    <!--<?= __('合计支付金额'); ?>End-->


    <!--<?= __('底部总金额固定层'); ?>Begin-->
    <div class="nctouch-cart-bottom">
        <div class="total"><span id="online-total-wrapper"></span>
            <dl class="total-money">

            </dl>
        </div>
        <div class="check-out"><a href="javascript:void(0);" id="ToBuyStep2"><?= __('提交订单'); ?></a></div>
    </div>
    
</div>
<script type="text/html" id="list-address-add-list-script">

    <% if(address_list[0].user_address_id != 0 ){ %>
    <% for (var i=0; i<address_list.length; i++) { %>
    <li <% if ( (!isEmpty(address_id) && address_list[i].user_address_id == address_id) || (isEmpty(address_id) && address_list[i].user_address_default == 1) ) { %>class="selected"<% } %> data-param="{user_address_id:'<%=address_list[i].user_address_id%>',user_address_contact:'<%=address_list[i].user_address_contact%>',user_address_phone:'<%=address_list[i].user_address_phone%>',area_code:'<%=address_list[i].area_code%>',user_address_area:'<%=address_list[i].user_address_area%>',user_address_area:'<%=address_list[i].user_address_area%>',user_address_area_id:'<%=address_list[i].user_address_area_id%>',user_address_city_id:'<%=address_list[i].user_address_city_id%>',user_address_address:'<%=address_list[i].user_address_address%>'}"> <i></i>
    <dl>
        <dt><?= __('收货人：'); ?><span id=""><%=address_list[i].user_address_contact%></span><span id=""><%=address_list[i].user_address_phone%></span><% if (address_list[i].user_address_default == 1) { %><sub><?= __('默认'); ?></sub><% } %></dt>
        <dd><span id=""><%=address_list[i].user_address_area %>&nbsp;<%=address_list[i].user_address_address %></span></dd>
    </dl>
    </li>
    <% }} %>
</script>



<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/iscroll.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript" src="../../js/fly/requestAnimationFrame.js"></script>
<script type="text/javascript" src="../../js/fly/zepto.fly.min.js"></script>
<script type="text/javascript" src="../../js/tmpl/order_payment_common.js"></script>
<script type="text/javascript" src="../../js/tmpl/integral_product_buy.js"></script>
<script type="text/javascript" src="../../js/intlTelInput.js"></script>

</body>
</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>