<?php
include __DIR__ . '/../includes/header.php';
?>

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <title><?= __('门店自提订单'); ?></title>
        <link rel="stylesheet" href="../css/base.css">
        <link rel="stylesheet" type="text/css" href="../css/private-store.css"/>
        <link rel="stylesheet" type="text/css" href="../css/nctouch_products_detail.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/nctouch_common.css">
        <link rel="stylesheet" type="text/css" href="../../css/nctouch_cart.css">
        <link rel="stylesheet" type="text/css" href="../../css/voucher.css"/>
        <link rel="stylesheet" type="text/css" href="../../css/intlTelInput.css">
    </head>
    <body>
    <header id="header" class="fixed">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:history.go(-1)"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1 id="title"><?= __('门店自提订单'); ?></h1>
            </div>
        </div>
    </header>
    <ul class="mb50 bgf module-ziti">
        <li class="z-box1 fz-30">
            <?= __('门店：'); ?><span class="js-chain-name"></span>
        </li>
        <li class="z-box2">
            <!--<?= __('添加收货人'); ?>-->
            <div class="z-box2-tj js-add-address">
                <img src="../images/SHIZI.png"/>
               <div class="z-box2-a"><?= __('添加收货人信息'); ?></div>
            </div>
            <div class="js-user-address fz-30 ml065" style="display: none">
            </div>
            <script type="text/html" id="user-address">
                <span class='fl one-overflow w4'><%= user_address_contact %></span>
                <span><%= user_address_phone %></span>
                <input type="hidden" id="receiver_address" value="<%= user_address_area + user_address_address %>">
                <p class="z-dhwz mb063"><%= user_address_area + user_address_address %></p>
            </script>
        </li>
        <li class="z-box3"></li>
        <li class="z-box4 fz-30" id="info">
            <?= __('门店自提商店清单'); ?>
        </li>
        <li class="z-box5 fz-30">
            <img class="js-store-logo">
            <span class="js-store-name"></span>
        </li>
        <li class="z-box6 clearfix">
            <img class="z-box6-z js-goods-img"/>
            <div class="z-box6-a">
                <p class="js-goods-name"</p>
                <p class="f-color js-goods-spec z-ztcd" style="display: none"></p>
            </div>
            <div class="z-box6-b">
                <div><?= __('￥'); ?><em class="js-goods-price"></em></div>
                <input type="button" value="-" class="z-input1 js-reduce"/>
                <input type="number" value="1" id="goodsNumber" min="1"  pattern="[0-9]" class="z-input2"/>
                <input type="button" value="+" class="z-input3 js-add"/>
            </div>
        </li>
        <li class="z-box7 clearfix fz-30 js-voucher js-enabled" id="select-voucher-valve">
            <div class="z-box7-l col5 fl"><?= __('店铺代金券'); ?></div>
            <div class="z-box7-r fz-30 fr">
                <span class="current-con fz-28"></span>
                <img src="../images/jt.png">
            </div>
        </li>
        <li class="z-box8 clearfix fz-30">
            <div>
                <div class="fz-30"><?= __('买家留言：'); ?></div>
                <input type="text" id="buyerMessage" maxlength="45" class="bor0 wt10 fz-28" placeholder="<?= __('店铺订单留言'); ?>" >
                <img src="../images/close_window.png" class="clearBuyerMessage hide">
            </div>
        </li>
        <li class="z-box9"></li>
        <li class="z-box7 clearfix fz-30 js-platform-red-packet js-enabled" id="select-platform-red-packet-valve">
            <div class="z-box7-l col5 fl"><?= __('平台红包'); ?></div>
            <div class="z-box7-r fz-30 fr">
                <span class="current-con fz-28"></span>
                <img src="../images/jt.png">
            </div>
        </li>
        <li class="z-box7 clearfix fz-30 js-member-discount">
            <div class="nctouch-cart-block ratePrice  pb-10 bg-transparent" style="">
                <div>
                    <a href="javascript:void(0);" class="posr">
                        <h3><?= __('会员折扣'); ?></h3>
                        <div class="current-con mr-20 clearfix" style="padding:0">
                            <input type="hidden" name="is_discount" value="1">
                            <div>
                                <em class="align-top iblock lh17 fz-28" id="member-discount"><?= __('此功能不与优惠券共用'); ?></em>
                                <div class="iblock fr btn-switch ml-20 mrt3">
                                    <input id="use-member-discount" type="checkbox"><label></label>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </li>
        <li class="z-box7 clearfix fz-30" id="select-payment-valve">
            <div class="z-box7-l col5 fl"><?= __('支付方式'); ?></div>
            <div class="z-box7-r fz-30 fr" >
                <span class="current-con fz-28"><?= __('在线支付'); ?></span>
                <img src="../images/jt.png"/>
            </div>
        </li>
        <li class="z-box7 clearfix fz-30 hide" id="delivery">
            <div class="z-box7-l col5 fl"><?= __('配送费'); ?></div>
            <div class="z-box7-r fz-30 fr">
                <span class="current-con fz-28" id="delivery_price"></span>
                <img src="../images/jt.png"/>
            </div>
        </li>
        
    </ul>
    <ul class="clearfix nctouch-cart-bottom">
        <li class="z-foot1 fz-32 fl">
            <span><?= __('合计：'); ?></span>
            <span><?= __('￥'); ?><em class="js-store-total"></em></span>
        </li>
        <div class='z-po fr'>
            <!--<?= __('通过删除'); ?>bgc<?= __('控制颜色'); ?>-->
            <li class="z-foot2 js-submit bgc">
                <a href="javascript:;"><?= __('提交订单'); ?></a>
            </li>
        </div>
    </ul>

    <!--<?= __('点击效果'); ?>-->
    <div class="nctouch-bottom-mask down">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block">
            <div class="z-zffs"><?= __('支付方式'); ?></div>
            <div class="z-zffs2 clearfix">
                <p><?= __('在线支付'); ?></p>
                <input type="checkbox" name="" id="" value=""/>
            </div>
            <div class="z-zffs2 clearfix z-mdzf">
                <p><?= __('门店支付'); ?></p>
                <input type="checkbox" name="" id="" value=""/>
            </div>
            <div class="z-zffs3 z-bgc"><?= __('确定'); ?></div>
        </div>
    </div>

    <!--<?= __('选择收货地址'); ?>Begin-->
    <div id="list-address-wrapper" class="nctouch-full-mask hide">
        <div class="nctouch-full-mask-bg"></div>
        <div class="nctouch-full-mask-block">
            <div class="header absolute">
                <div class="header-wrap">
                    <div class="header-l"><a href="javascript:void(0);"> <i class="back"></i> </a></div>
                    <div class="header-title">
                        <h1><?= __('收货地址管理'); ?></h1>
                    </div>
                </div>
            </div>
            <div class="nctouch-main-layout" style="display: block; position: absolute; top: 0; right: 0; left: 0; bottom:2rem; overflow: hidden; z-index: 1;" id="list-address-scroll">
                <ul class="nctouch-cart-add-list" id="list-address-add-list-ul">
                </ul>
            </div>
            <div id="addresslist" class="mt10" style="position: absolute; right: 0; left: 0; bottom: 0; z-index: 1;"><a href="javascript:void(0);" class="btn-l" id="new-address-valve"><?= __('新增收货地址'); ?></a></div>
        </div>
    </div>
    <!--<?= __('选择收货地址'); ?>End-->
    <!--<?= __('新增收货地址'); ?>Begin-->
    <div id="new-address-wrapper" class="nctouch-full-mask hide">
        <div class="nctouch-full-mask-bg"></div>
        <div class="nctouch-full-mask-block">
            <div class="header absolute">
                <div class="header-wrap">
                    <div class="header-l"><a href="javascript:void(0);"> <i class="back"></i> </a></div>
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
                                    <span class="input-del"></span></div>
                            </li>
                            <li class="form-item">
                                <h4><?= __('联系手机'); ?></h4>
                                <div class="input-box">
                                    <input type="tel" class="inp" name="mob_phone" id="re_user_mobile" autocomplete="off" oninput="writeClear($(this));"/>
                                    <input id="area_code" type="hidden">
                                    <span class="input-del"></span>
                                </div>
                            </li>
                            <li class="form-item">
                                <h4><?= __('地区选择'); ?></h4>
                                <div class="input-box">
                                    <input name="area_info" type="text" class="inp" id="varea_info" unselectable="on" onfocus="this.blur()" autocomplete="off"
                                           onchange="btn_check($('form'));" readonly/>
                                </div>
                            </li>
                            <li class="form-item">
                                <h4><?= __('详细地址'); ?></h4>
                                <div class="input-box">
                                    <input type="text" class="inp" name="vaddress" id="vaddress" autocomplete="off"
                                           oninput="writeClear($(this));"/>
                                    <span class="input-del"></span></div>
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

    <!--<?= __('选择付款方式'); ?>Begin-->
    <div class="nctouch-bottom-mask down" style="height: 100%;" id="select-payment-wrapper">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block">
            <div class="z-zffs"><?= __('支付方式'); ?></div>
            <div class="z-zffs2 clearfix">
                <p><?= __('在线支付'); ?></p>
                <input type="radio" name="payment-way" id="payment-online" value="payment-online" checked="checked">
            </div>
            <div class="z-zffs2 clearfix z-mdzf" id="pay_chain">
                <p><?= __('门店支付'); ?></p>
                <input type="radio" name="payment-way" id="payment-offline" value="payment-offline">
            </div>
            <div class="z-zffs3 z-bgc JS_close"><?= __('确定'); ?></div>
        </div>
    </div>
        <!--<div class="header absolute">
            <div class="header-wrap">
                <div class="header-l"><a href="javascript:void(0);"> <i class="back"></i> </a></div>
                <div class="header-title">
                    <h1><?= __('选择支付方式'); ?></h1>
                </div>
            </div>
        </div>
        <div class="nctouch-main-layout">
            <div class="nctouch-sel-box pl-30 pr-30">
                <h4 class="tit"><?= __('支付方式'); ?></h4>
                <div class="sel-con"><a href="javascript:void(0);" class="sel" id="payment-online"><?= __('在线支付'); ?></a>
                    <a href="javascript:void(0);" id="payment-offline"><?= __('门店付款'); ?></a></div>
            </div>
        </div>-->
    <!--<?= __('选择付款方式'); ?>End-->

    <!--<?= __('收货地址列表'); ?>S-->
    <script type="text/html" id="list-address-add-list-script">
        <% for (var i=0; i<addressList.length; i++) { %>
        <% address = addressList[i]; %>
        <li data-address_id="<%= address.id %>" data-area_code="<%= address.area_code %>"><i></i>
            <dl>
                <dt class="clearfix"><p class="fl one-overflow w4"><%= address.user_address_contact %></p><p class="fl ml-20"><%= address.user_address_phone %></p>
                    <% if (address.user_address_default == 1) { %>
                        <sub class="ml-20"><?= __('默认'); ?></sub>
                    <% } %>
                </dt>
                <dd><span class="more-overflow w15"><%= address.user_address_area %>&nbsp;<%= address.user_address_address %></span></dd>
            </dl>
        </li>
        <% } %>
    </script>
    <!--<?= __('收货地址列表'); ?>E-->

    <!--  <?= __('代金券弹出框内容'); ?> -->
    <div id="select-voucher-wrapper" class="nctouch-full-mask hide">
        <div class="nctouch-full-mask-bg"></div>
        <div class="nctouch-full-mask-block">
            <div class="header absolute">
                <div class="header-wrap">
                    <div class="header-l"> <a href="javascript:void(0);"> <i class="back"></i> </a> </div>
                    <div class="header-title">
                        <h1><?= __('选择店铺代金券'); ?></h1>
                    </div>
                </div>
            </div>
            <div id="voucher-html" class="overflow-auto height-set"></div>
        </div>
    </div>

    <script type="text/html" id="voucher-script">
        <% if (voucherList.length > 0) { %>
            <ul class="z-main">
                <% if(enabledVoucher > 0){ %>
                <li class="z-main-li1 bort1 borb1" id="no_voucher">
                    <span><?= __('不使用店铺代金券'); ?></span>
                    <input class="z-main-ipt1 fr" type="radio" name="voucher" value="0" <%= voucherId == 0 ? 'checked' : '' %>>
                </li>
                <% } %>
                <% for (i = 0; i < voucherList.length; i++) { %>
                    <% var flag = voucherList[i].permission ? true : false; %>
                    <li class="z-main-li2">
                        <div class="clearfix <%= flag ? 'active' : '' %>">
                            <div class="fl z-main-li2-div1">
                                <span><?= __('￥'); ?></span>
                                <span><%= voucherList[i].voucher_price %></span>
                                <p><?= __('满'); ?><%= voucherList[i].voucher_limit %><?= __('可用'); ?></p>
                            </div>
                            <div class="fl z-main-li2-div2">
                                <div class="one-overflow"><%= voucherList[i].voucher_title %></div>
                                <div><%= voucherList[i].endDate %><?= __('到期'); ?></div>
                            </div>
                            <% if (flag) { %>
                            <input type="radio" name="voucher" class="z-main-ipt2 fr"
                                   value="<%= voucherList[i].voucher_id %>" <%= voucherId == voucherList[i].voucher_id ? 'checked' : '' %>>
                            <% } %>
                        </div>
                    </li>
                <% } %>
            </ul>
        <% } else { %>
            <div class="norecord tc">
                <div class="ziti-store2">
                    <i></i>
                </div>
                <p class="fz-30 col9"><?= __('暂无可用店铺代金券'); ?></p>
            </div>
        <% } %>
    </script>
    <!--  <?= __('代金券弹出框内容'); ?> -->

    <!-- <?= __('平台红包弹出内容'); ?> -->
    <div id="select-platform-red-packet-wrapper" class="nctouch-full-mask hide">
        <div class="nctouch-full-mask-bg"></div>
        <div class="nctouch-full-mask-block">
            <div class="header absolute">
                <div class="header-wrap">
                    <div class="header-l" id="js-btn-back"> <a href="javascript:void(0);"> <i class="back"></i> </a> </div>
                    <div class="header-title">
                        <h1><?= __('选择平台红包'); ?></h1>
                    </div>
                </div>
            </div>
            <div id="platform-red-packet-html" class="overflow-auto height-set"></div>
        </div>
    </div>
    <script type="text/html" id="platform-red-packet-script">
        <% if (redPacketList.length > 0) { %>
        <ul class="z-main">
            <% if(enabledRedPacket > 0){ %>
            <li class="z-main-li1 bort1 borb1" id="no_redpacket">
                <span><?= __('不使用平台红包'); ?></span>
                <input class="z-main-ipt1 fr" type="radio" name="platform-red-packet" value="0" <%= redPacketId == 0 ? 'checked' : '' %>>
            </li>
            <% } %>
            <% for (i = 0; i < redPacketList.length; i++) { %>
                <% var flag = redPacketList[i].permission ? true : false; %>
                <li class="z-main-li2">
                    <div class="clearfix <%= flag ? 'active' : '' %>">
                        <div class="fl z-main-li2-div1">
                            <span><?= __('￥'); ?></span>
                            <span><%= redPacketList[i].redpacket_price %></span>
                            <p><?= __('满'); ?><%= redPacketList[i].redpacket_t_orderlimit %><?= __('可用'); ?></p>
                        </div>
                        <div class="fl z-main-li2-div2">
                            <div class="one-overflow"><%= redPacketList[i].redpacket_title %></div>
                            <div><%= redPacketList[i].endDate %><?= __('到期'); ?></div>
                        </div>
                        <% if (flag) { %>
                        <input type="radio" name="platform-red-packet" class="z-main-ipt2 fr"
                               value="<%= redPacketList[i].redpacket_id %>" <%= redPacketId == redPacketList[i].redpacket_id ? 'checked' : '' %>>
                        <% } %>
                    </div>
                </li>
            <% } %>
        </ul>
        <% } else { %>
        <div class="norecord tc">
            <div class="ziti-store2">
                <i></i>
            </div>
            <p class="fz-30 col9"><?= __('暂无可用平台红包'); ?></p>
        </div>
        <% } %>
    </script>
    <!-- <?= __('平台红包弹出内容'); ?> -->
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/swipe.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/ziti_confirm.js"></script>
    <script type="text/javascript" src="../js/iscroll.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/fly/requestAnimationFrame.js"></script>
    <script type="text/javascript" src="../js/fly/zepto.fly.min.js"></script>
    <script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>

    <script type="text/javascript" src="../js/intlTelInput.js"></script>
    </body>
    </html>

<?php
include __DIR__ . '/../includes/footer.php';
?>