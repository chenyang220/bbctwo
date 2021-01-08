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
    <title><?= __('我的财产'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
</head>

<body>
    <header id="header">
        <div class="header-wrap">
        <!--     <div class="header-l">
                <a href="member.html"> <i class="back"></i> </a>
            </div> -->
            <div class="header-title">
                <h1><?= __('我的财产'); ?></h1>
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
        <ul class="nctouch-default-list">
            <li>
                <a href="predepositlog_list.html">
                    <h4><i class="cc-06"></i><?= __('账户余额'); ?></h4>
                    <h6><?= __('预存款账户余额、充值及提现明细'); ?></h6>
                    <span class="tip" id="predepoit"></span> <span class="arrow-r"></span></a>
            </li>
            <li>
                <a href="rechargecardlog_list.html">
                    <h4><i class="cc-07"></i><?= __('充值卡余额'); ?></h4>
                    <h6><?= __('充值卡账户余额以及卡密充值操作'); ?></h6>
                    <span class="tip" id="rcb"></span> <span class="arrow-r"></span></a>
            </li>
            <li>
                <a href="voucher_list.html">
                    <h4><i class="cc-08"></i><?= __('店铺代金券'); ?></h4>
                    <h6><?= __('店铺代金券使用情况以及卡密兑换代金券操作'); ?></h6>
                    <span class="tip" id="voucher"></span><span class="arrow-r"></span></a>
            </li>
            <li>
                <a href="redpacket_list.html">
                    <h4><i class="cc-09"></i><?= __('平台红包'); ?></h4>
                    <h6><?= __('平台红包使用情况以及卡密领取红包操作'); ?></h6>
                    <span class="tip" id="redpacket"></span><span class="arrow-r"></span></a>
            </li>
            <li>
                <a href="pointslog_list.html">
                    <h4><i class="cc-10"></i><?= __('会员积分'); ?></h4>
                    <h6><?= __('会员积分获取及消费日志'); ?></h6>
                    <span class="tip" id="point"></span><span class="arrow-r"></span></a>
            </li>
        </ul>
    </div>
    <footer id="footer" class="posa"></footer>
    
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../../js/tmpl/member_asset.js"></script>
</body>

</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>