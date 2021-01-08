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
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('设置'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
</head>

<body>
    <header id="header">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="member.html"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1><?= __('设置'); ?></h1>
            </div>
        </div>
        <!-- <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a> </div> -->
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
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <ul class="nctouch-default-list mt5">
            <!--
            <li>
                <a href="member_password_step1.html">
                    <h4><?= __('登录密码'); ?></h4>
                    <h6><?= __('建议您定期更改密码以保护账户安全'); ?></h6>
                    <span class="arrow-r"></span></a>
            </li>
            <li>
                <a href="member_paypwd_step1.html">
                    <h4><?= __('支付密码'); ?></h4>
                    <h6><?= __('建议您设置复杂的支付密码保护账户金额安全'); ?></h6>
                    <span class="tip" id="paypwd_tips"></span> <span class="arrow-r"></span> </a>
            </li>-->

            <li id="member_mobile_bind">
                <a href="member_mobile_bind.html" id="mobile_link">
                    <h4><?= __('手机验证'); ?></h4>
                    <h6><?= __('若您的手机已丢失或停用，请立即修改更换'); ?></h6>
                    <span class="tip" id="mobile_value"></span> <span class="arrow-r"></span></a>
            </li>
             <li>
                <a href="member_feedback.html">
                    <h4><?= __('用户反馈'); ?></h4>
                    <h6><?= __('您在使用中遇到的问题与建议可向我们反馈'); ?></h6>
                    <span class="arrow-r"></span></a>
            </li>
        </ul>
       
        <ul class="nctouch-default-list mt5">
            <li>
                <a href="member_version.php">
                    <h4><?= __('关于我们'); ?></h4>
                    <span class="arrow-r"></span></a>
            </li>
        </ul>
        <ul class="nctouch-default-list mt5" id="logoutbtn_ul">
            <li>
                <a id="logoutbtn" href="javascript:void(0);">
                    <h4><?= __('安全退出'); ?></h4>
                </a>
            </li>
        </ul>
    </div>
    <footer id="footer"></footer>
    
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/tmpl/member_account.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</body>

</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>