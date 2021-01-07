<?php
include __DIR__.'/../../includes/header.php';
$act = $_GET['act'] ? $_GET['act'] : 0;
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
        <title><?= __('我的红包'); ?></title>
        <link rel="stylesheet" type="text/css" href="../../css/base.css">
        <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    </head>

    <body>
    <header id="header">
        <div class="header-wrap">
            <!-- <div class="header-l"><a href="member.html"><i class="back"></i></a></div> -->
            <span class="header-tab"><a href="member_voucher.html">代金券</a><a class="cur" href="redpacket_list.html">我的红包</a></span>
            <a  class="delredpacket" href="javascript:void(0)"><?= __('清空'); ?></a>
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
                    <li><a href="../../tmpl/cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup></sup></a></li>
                    <li><a href="../../tmpl/member/member.html"><i class="member"></i><?= __('我的商城'); ?></a></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    </header>
    <ul class="v-list-tab bgf borb1">
        <li class="redpacket_status_li <?php if($act == 0){echo 'active';}?>"><a href="redpacket_list.html?act=0"><?= __('全部'); ?></a></li>
        <li class="redpacket_status_li <?php if($act == 1){echo 'active';}?>"><a href="redpacket_list.html?act=1"><?= __('未使用'); ?></a></li>
        <li class="redpacket_status_li <?php if($act == 3){echo 'active';}?>"><a href="redpacket_list.html?act=3"><?= __('已失效'); ?></a></li>
    </ul>

    <div class="v-list" id="v_list">

    </div>
    <script type="text/html" id="redpacket_list">
        <%if(items.length>0){%>
        <ul>
            <% for (var i in items) {%>
            <a href="../../index.html">
                <%if(items[i].redpacket_state == 1){%>
                <li class="clearfix yes">
                    <%}else if(items[i].redpacket_state == 2){%>
                <li class="clearfix no">
                    <%}else {%>
                <li class="clearfix pass">
                    <% } %>

                    <div class="fl">
                        <div>
                            <p class="tc fz-56 colf"><b class="fz-28 iblock align-top"><?= __('￥'); ?></b><span><%=items[i].redpacket_price%></span></p>
                            <div class="tc fz-24"><span><?= __('满'); ?><%=items[i].redpacket_t_orderlimit%><?= __('元使用'); ?></span></div>
                        </div>

                    </div>
                    <div class="fr pt-20 pb-20 pr-30">
                        <p class="fz-24 colbc mt-20"><%=items[i].redpacket_end_date%><?= __('前有效'); ?></p>
                        <p class="fz-24 colbc mt-20"><?= __('状态'); ?>:<%=items[i].redpacket_state_label%></p>
                    </div>
                    <%if(items[i].redpacket_state ==1){%>
                    <span class="btn-voucher-use fz-24 default-color"><?= __('立即使用'); ?></span>
                    <% } %>
                    <!--  <i class="icon-pase"></i> -->
                </li>
            </a>
            <%}%>
        </ul>
        <%}else{%>
            <div class="no-data tc">
                <div class="table wp100 hp100">
                    <div class="table-cell">
                        <img src="../../images/new/icon-tips.png" alt="img">
                        <span id="end" class="block fz-30 col6"><?= __('您还没有优惠券哦'); ?></span>
                    </div>
                </div>
                
            </div>
        <%}%>
    </script>

    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/template.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/ncscroll-load.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/tmpl/redpacket_list.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    </body>

    </html>
<?php
include __DIR__.'/../../includes/footer.php';
?>