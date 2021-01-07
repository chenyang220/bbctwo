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
    <title><?= __('领取红包'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
</head>

<body>
    <header id="header">
        <div class="header-wrap">
            <!-- <div class="header-l"><a href="member.html"><i class="back"></i></a></div> -->
            <span class="header-tab"> <a href="redpacket_list.html"><?= __('我的红包'); ?></a> <a href="javascript:void(0);" class="cur"><?= __('领取红包'); ?></a> </span>
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
                    <li><a href="member.html"><i class="member"></i><?= __('我的商城'); ?></a><sup></sup></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <div class="nctouch-asset-info">
            <div class="container packet"> <i class="icon"></i>
                <dl class="rule">
                    <dd><?= __('请输入已知平台红包卡密号码'); ?></dd>
                    <dd><?= __('确认生效后可在购物车使用抵扣订单金额'); ?></dd>
                </dl>
            </div>
        </div>
        <div class="nctouch-inp-con">
            <form action="" method="">
                <ul class="form-box">
                    <li class="form-item">
                        <h4><?= __('红包卡密'); ?></h4>
                        <div class="input-box">
                            <input type="text" id="pwd_code" class="inp" name="pwd_code" maxlength="20" placeholder="<?= __('请输入平台红包卡密号'); ?>" pattern="[0-9]*" oninput="writeClear($(this));" onfocus="writeClear($(this));" />
                            <span class="input-del"></span> </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('验'); ?>&nbsp;<?= __('证'); ?>&nbsp;<?= __('码'); ?></h4>
                        <div class="input-box">
                            <input type="text" id="captcha" name="captcha" maxlength="4" size="10" class="inp" autocomplete="off" placeholder="<?= __('输入'); ?>4<?= __('位验证码'); ?>" oninput="writeClear($(this));" />
                            <span class="input-del code"></span>
                            <a href="javascript:void(0)" id="refreshcode" class="code-img"><img border="0" id="codeimage" name="codeimage"></a>
                            <input type="hidden" id="codekey" name="codekey" value="">
                        </div>
                    </li>
                </ul>
                <div class="error-tips"></div>
                <div class="form-btn"><a href="javascript:void(0);" class="btn" id="saveform"><?= __('确认提交'); ?></a></div>
            </form>
        </div>
        <footer id="footer" class="bottom"></footer>
        
        <script type="text/javascript" src="../../js/zepto.min.js"></script>
        <script type="text/javascript" src="../../js/template.js"></script>
        <script type="text/javascript" src="../../js/common.js"></script>
        <script type="text/javascript" src="../../js/simple-plugin.js"></script>
        <script type="text/javascript" src="../../js/tmpl/redpacket_pwex.js"></script>
        <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</body>

</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>