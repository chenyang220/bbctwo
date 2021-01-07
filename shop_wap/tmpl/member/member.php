<?php
    include __DIR__ . '/../../includes/header.php';
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
        <title><?= __('我的商城'); ?></title>
        <link rel="stylesheet" type="text/css" href="../../css/base.css">
        <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
        <link rel="stylesheet" href="//at.alicdn.com/t/font_562768_qjg7nufa5w.css">
        <link rel="stylesheet" type="text/css" href="../../css/iconfont.css">
    </head>
    <body>
    <header id="header" class="transparent">
        <div class="header-wrap">
            <!-- <div class="header-l"> <a href="member_account.html"> <i class="set"></i> </a> </div> -->
            <div class="header-l">
            <!--     <a href="javascript:history.go(-1)">
                    <i class="mine-back"></i>
                </a> -->
            </div>
            <div class="header-title">
                <h1><?= __('我的商城'); ?></h1>
            </div>
            <!-- <div class="header-r"><a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a></div> -->
        </div>
        <div class="nctouch-nav-layout">
            <div class="nctouch-nav-menu"><span class="arrow"></span>
                <ul>
                    <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                        <li><a href="../store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }else{ ?>
                        <li><a href="../../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }?>
                    <li><a href="../cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup style="display: inline;"></sup></a></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="scroller-body mrb300">
        <div class="scroller-box">
            <div class="member-top member-top1"></div>
            <div class="member-collect"></div>
            <div class="member-center mt5 ">
                <dl>
                    <dt><a href="order_list.html">
                            <h3><i class="i-myorder"></i><em class="align-middle"><?= __('我的订单'); ?></em></h3>
                            <h5><em class="iblock align-middle"><?= __('查看全部订单'); ?></em><i class="arrow-r"></i></h5>
                        </a></dt>
                    <dd>
                        <ul id="order_ul">
                        </ul>
                    </dd>
                </dl>
                <!-- <?= __('门店自提订单'); ?> -->
                <!-- <dl class="bort1">
                    <dt><a href="chain_order_list.html">
                            <h3><?= __('门店自提订单'); ?></h3>
                            <h5><i class="arrow-r"></i></h5>
                        </a></dt>
                </dl> -->
                 <!-- <?= __('二维码'); ?>+<?= __('分享'); ?> -->

                <ul class="member-interaction clearfix bgf mt-20 mb-20">
                    
                </ul>

                <!-- <dl class="mt5 bort1">
                    <dt><a id="paycenter">
                            <h3><i class="mc-02"></i><?= __('我的财产'); ?></h3>
                            <h5><em class="iblock align-middle"><?= __('查看全部财产'); ?></em><i class="arrow-r"></i></h5>
                        </a></dt>
                    <dd>
                        <ul  class="property-overview">
                            <li class="paycenter">
                                <h3><i></i><span><?= __('余额'); ?></span></h3>
                                <strong id="user_money"><?= __('￥'); ?>0</strong>
                            </li>
                            <li>
                                <a href="pointslog_list.html">
                                    <h3><i></i><span><?= __('积分'); ?></span></h3>
                                    <strong id="user_points">0</strong>
                                </a>
                            </li>
                        </ul>
                    </dd>
                </dl> -->
                <ul class="member-nav-items clearfix bgf mb-20">
                    <li>
                        <a href="member_voucher.html"><i class="i-kaquan"></i><span class="block">红包卡券</span></a>
                    </li>
                    <li>
                        <a href="pointslog_list.html"><i class="i-jifen"></i><span class="block">我的积分</span></a>
                    </li>
                    <li>
                        <a href="address_list.html"><i class="i-addr"></i><span class="block">地址管理</span></a>
                    </li>
                    <li class="member-nav-property">
                        <a id="paycenter"><i class="i-caichan"></i><span class="block">我的财产</span></a>
                    </li>
                    <li class="member-nav-wei">
                        <a id="my_code"><i class="i-erweima"></i><span class="block">我的二维码</span></a>
                    </li>
                    <li class="member-nav-setting">
                        <a href="member_account.html"><i class="i-shezhi"></i><span class="block">设置</span></a>
                    </li>
                </ul>
            </div>
        </div>
        <footer id="footer"></footer>
        <!-- <?= __('底部'); ?> -->
        <?php
            include __DIR__ . '/../../includes/footer_menu.php';
        ?>
    </div>
    
    <script type="text/javascript" src="../../js/zepto.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/template.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/zepto.cookie.js"></script>

    <!-- <?php include __DIR__ . '/../../includes/translatejs.php'; ?> -->

    <script type="text/javascript" src="../../js/iscroll.js"></script>
    <script type="text/javascript" src="../..//js/fly/requestAnimationFrame.js"></script>
    <!-- <script type="text/javascript" src="../../js/fly/zepto.fly.min.js"></script> -->
    <script type="text/javascript" src="../../js/NativeShare.js"></script>
    <script type="text/javascript" src="../../js/tmpl/member.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../../js/soshm.min.js"></script>
    </body>
    </html>
<?php
    include __DIR__ . '/../../includes/footer.php';
?>