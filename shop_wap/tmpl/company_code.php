<?php
include __DIR__ . '/../includes/header.php';
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
    <title><?= __(' 企业资质'); ?></title>
    <link rel="stylesheet" type="text/css" href="../css/base.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_store.css">
    <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_pwu4kym4n3o9a4i.css">
</head>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script>
<body>
	<header id="header">
	    <div class="header-wrap">
	        <div class="header-l">
	            <!-- <a href="javascript:history.go(-1);"> <i class="back"></i> </a> -->
	        </div>
	        <div class="header-title">
	            <h1><?= __('企业资质'); ?></h1>
	        </div>
	        <!-- <div class="header-r"> <a href="javascript:void(0);" id="header-nav"><i class="more"></i><sup></sup></a> </div> -->
	    </div>
	    <div class="nctouch-nav-layout">
	        <div class="nctouch-nav-menu"> <span class="arrow"></span>
	            <ul>
	                <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
	                    <li><a href="../tmpl/store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
	                    <li><a href="../tmpl/store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
	                <?php }else{ ?>
	                    <li><a href="../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
	                    <li><a href="../tmpl/search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
	                <?php }?>
	                <li><a href="../tmpl/cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup></sup></a></li>
	                <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
	            </ul>
	        </div>
	    </div>
	</header>
	<div class="nctouch-main-layout">
		<span class="block zizhi-tit"><?= __('输入验证码查看企业资质'); ?></span>
		<div class="tc">
			<input class="zizhi-code-input" type="text" name="code" id="code">
			<img class="zizhi-code-img" src="" id="yzm" onClick="get_randfunc(this);">
		</div>
		<div class="form-btn form-btn-color mt-100"><a class="btn-l" href="javascript:;" id="sure">确定</a></div>
	</div>
	
	


<script type="text/javascript" src="../js/zepto.min.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/swipe.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/zepto.waypoints.js"></script>
<script type="text/javascript" src="../js/ncscroll-load.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
<script type="text/javascript">
    var shop_id = getQueryString('shop_id');
    var src = WapSiteUrl + "/includes/rand_func.php";
    $("#yzm").attr('src', src);

    $("#sure").click(function () {
        var code = $("#code").val();
        $.ajax({
            url: WapSiteUrl + "/check.php",
            type: "post",
            data: {code: code},
            dataType: "json",
            success: function (res) {
                if (res.code == 1) {
                    window.location.href = WapSiteUrl + "/tmpl/company_info.html?shop_id=" + shop_id
                } else {
                    $.sDialog({
                        skin: "red",
                        content: "验证码错误",
                        okBtn: false,
                        cancelBtn: false
                    });
                }

            }
        });
    })

    function get_randfunc(obj) {
        var sj = new Date();
        url = obj.src;
        obj.src = url + '?' + sj;
    }
</script>
</body>

</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>
