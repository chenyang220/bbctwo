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
		<div class="zizhi-detail-text pl-20 pr-20">
			<span class="zizhi-tit block weight"><?= __('网店经营者营业执照信息') ?></span>
			<p><span><?= __('企业名称') ?>：</span><em id="shop_company_name"></em></p>
			<p><span><?= __('营业执照注册号') ?>：</span><em id="business_id"></em></p>
			<p><span><?= __('法定代表人姓名') ?>：</span><em id="legal_person"></em></p>
			<p><span><?= __('营业执照所在地') ?>：</span><em id="business_license_location"></em></p>
			<p><span><?= __('企业注册资金') ?>：</span><em id="company_registered_capital"></em></p>
			<p><span><?= __('营业执照有效期') ?>：</span><em id="business_time"></em></p>
			<p><span><?= __('公司地址') ?>：</span><em id="shop_company_address"></em></p>
			<p><span><?= __('营业执照经营范围') ?>：</span><em id="business_sphere"></em></p>
			<b class="block zizhi-tips-text mt-20 mb-60"><?= __('注') ?>：<?= __('以上营业执照信息，根据国家工商总局《网络交易管理办法》要求对入驻商家营业执照信息进行公示，企业资质信息由卖家自行申报填写。如需进一步核实，请联系当地工商行政管理部门。') ?></b>
			<img class="wp100" id="business_license_electronic" src="">
		</div>
		
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
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Shop&met=getCompany&typ=json",
        type: "post",
        data: {shop_id: shop_id},
        dataType: "json",
        success: function (res) {
            var data = res.data;
            $("#shop_company_name").html(data.shop_company_name);
            $("#business_id").html(data.business_id);
            $("#legal_person").html(data.legal_person);
            $("#business_license_location").html(data.business_license_location);
            $("#company_registered_capital").html(data.company_registered_capital);
            $("#business_time").html(data.business_licence_start + "至" + data.business_licence_end);
            $("#shop_company_address").html(data.shop_company_address + data.company_address_detail);
            $("#business_sphere").html(data.business_sphere);
            $("#business_license_electronic").attr("src", data.business_license_electronic);

        }
    });
</script>
</body>

</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>
