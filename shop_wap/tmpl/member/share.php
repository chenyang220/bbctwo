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
        <link rel="stylesheet" href="//at.alicdn.com/t/font_562768_me20q48nlt9.css">
    </head>
    <body>
    <header id="header" class="transparent">
        <div class="header-wrap">
            <!-- <div class="header-l"> <a href="member_account.html"> <i class="set"></i> </a> </div> -->
            <div class="header-l">
       <!--          <a href="javascript:history.go(-1)">
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
                    <?php if ($_COOKIE['is_app_guest']) { ?>
                        <li>
                            <a href="javascript:;" id='shareit'> <i class="share"></i><?= __('分享'); ?><sup></sup> </a>
                        </li>
                    <?php } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== true) { ?>
                        <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'UCWEB') !== false) { ?>
                            <li>
                                <a href="javascript:;" class="share_wap" id='share_wap'> <i class="share"></i><?= __('分享'); ?><sup></sup> </a>
                            </li>
                        <?php } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false) { ?>
                            <li>
                                <a href="javascript:;" class="share_wap" id='share_wap'> <i class="share"></i><?= __('分享'); ?><sup></sup> </a>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </header>
    <!--<div class="scroller-body scroller-body-share">
        <div class="scroller-box wp100 hp100">
            <div class="member-collect borb1"></div>
            <div class="member-center tc">
                <span class="fz-30 colf block mb-80 tc"><?= __('我的二维码'); ?></span>
                <div id="shareCode" class="share-code tc">
                    <img src="" alt="">
                </div>-->

                <!--                --><?php //if ($_COOKIE['is_app_guest']) { ?>
                <!--                    <span class="block fz-30 colf share-to-tit">--><? //= __('分享我的二维码至'); ?><!--</span>-->
                <!--                    <ul class="share-items clearfix">-->
                <!--                        <li class="share-wechat share_app" data-share_type="0"><a href="javascript:;"><i class="iconfont fz-40 icon-wechat"></i><span class="fz-26 col3 block mt-10">--><? //= __('微信'); ?><!--</span></a></li>-->
                <!--                        <li class="share-circle share_app" data-share_type="1"><a href="javascript:;"><i class="iconfont fz-40 icon-circle"></i><span class="fz-26 col3 block mt-10">--><? //= __('朋友圈'); ?><!--</span></a></li>-->
                <!--                        <li class="share-qq share_app" data-share_type="2"><a href="javascript:;"><i class="iconfont fz-40 icon-QQ"></i><span class="fz-26 col3 block mt-10">QQ--><? //= __('好友'); ?><!--</span></a></li>-->
                <!--                        <li class="share-qq-zone share_app" data-share_type="3"><a href="javascript:;"><i class="iconfont fz-40 icon-qq-zone"></i><span class="fz-26 col3 block mt-10">QQ--><? //= __('空间'); ?><!--</span></a></li>-->
                <!--                     </ul>-->
                <!--                --><?php //}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false){ ?>
                <!--                    --><?php //if(strpos($_SERVER['HTTP_USER_AGENT'],'UCBrowser')!==false||strpos($_SERVER['HTTP_USER_AGENT'],'UCWEB')!==false){ ?>
                <!--                        <span class="block fz-30 colf share-to-tit">--><? //= __('分享我的二维码至'); ?><!--</span>-->
                <!--                        <div id="nativeShare"></div>-->
                <!--                    --><?php //}elseif (strpos($_SERVER['HTTP_USER_AGENT'],'MQQBrowser')!==false){ ?>
                <!--                        <span class="block fz-30 colf share-to-tit">--><? //= __('分享我的二维码至'); ?><!--</span>-->
                <!--                        <div id="nativeShare"></div>-->
                <!--                    --><?php //}?>
                <!--                --><?php //}?>
           <!-- </div>
        </div>
    </div>-->
    <div id="share_bgimg_div_idshare" class="scroller-body scroller-body-share" style="background: none">
        <div class="scroller-box wp100" id="show_set_msg_div">
            <!-- <div class="member-collect borb1"></div> -->
            <div class="member-center tc member-share-style">
                <em class="img-box relative "><img id="my_code_wx_logo" src="" alt="" class="cter"></em>
                <div class="member-share-text">
                    <p id="my_code_wx_name"></p>
                    <p id="my_code_txt"></p>
                </div>
                <div id="shareCode" class="member-share-code">
                    <img src="" alt="">
                </div>


            </div>
        </div>
    </div>
    <script type="text/javascript" src="../../js/zepto.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/NativeShare.js"></script>
    <script type="text/javascript" src="../../js/tmpl/share.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>


    </body>
    </html>
<?php
include __DIR__ . '/../../includes/footer.php';
?>