<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
	<meta name="description" content="<?php if($this->description){?><?=$this->description ?><?php }?>" />
    <meta name="Keywords" content="<?php if($this->keyword){?><?=$this->keyword ?><?php }?>" />
<title><?php if ($this->title) { ?><?= addslashes($this->title) ?><?php } else { ?><?= addslashes(Web_ConfigModel::value('site_name')) ?><?php } ?></title>
    <?php
    $ctl = request_string('ctl');
    $met = request_string('met');
    $request_uri = $ctl . '.' . $met . '.' . request_string('type');
    $in = [
        'Goods_Goods.goods.goods',
        'Goods_Goods.goodslist.',
    ];
    $minify = false;
    if ($ctl && $met && in_array($request_uri, $in)) {
        $minify = true;
    } ?>
    <!--css 替换 -->
    <?php if ($minify) {
        $base_uri = Yf_Registry::get('root_uri');
        $css_url = "";
        $css_url .= $base_uri.'/shop/static/default/css/headfoot.css,';
        $css_url .= $base_uri.'/shop/static/default/css/sidebar.css,';
        $css_url .= $base_uri.'/shop/static/default/css/sass/custom.css,';
        $css_url .= $base_uri.'/shop/static/default/css/index.css,';
        $css_url .= $base_uri.'/shop/static/default/css/nav.css,';
        $css_url .= $base_uri.'/shop/static/default/css/base.css,';
        $css_url .= $base_uri.'/shop/static/default/css/swiper.css,';
        $css_url .= $base_uri.'/shop/static/default/css/iconfont/iconfont.css,';
        $css_url .= $base_uri.'/shop/static/default/css/select2.min.css,';
        $css_url .= $base_uri.'/shop/static/default/css/login.css,';
        $css_url .= $base_uri.'/shop/static/common/css/jquery/plugins/dialog/green.css';
    ?>
    <link rel="stylesheet" type="text/css" href="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f='.$css_url); ?>"/>
    
    <?php
    
    $min = $base_uri."/shop/static/common/js/jquery.js,";
    $min .= $base_uri."/shop/static/default/js/swiper.min.js,";
    $min .= $base_uri."/shop/static/default/js/jquery.SuperSlide.2.1.1.js,";
    //$min .= "/shop/static/default/js/select2.min.js";
    $min .= $base_uri."/shop/static/common/js/plugins/jquery.cookie.js,";
    $min .= $base_uri."/shop/static/common/js/jquery.nicescroll.js,";
    $min = substr($min, 0, -1);
    ?>
        
        <script type="text/javascript" src="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f=' . $min); ?>"></script>
    
    
    <?php
    $min = $base_uri."/shop/static/default/js/common.js,";
    $min .= $base_uri."/shop/static/default/js/index.js,";
    $min .= $base_uri."/shop/static/default/js/nav.js,";
    $min .= $base_uri."/shop/static/default/js/decoration/common.js,";
    $min .= $base_uri."/shop/static/default/js/base.js,";
    $min = substr($min, 0, -1);
    ?>
        <script type="text/javascript" src="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f=' . $min); ?>"></script>
    
    
    <?php }else{ ?>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/headfoot.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/sidebar.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/sass/custom.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/index.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/nav.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/swiper.css"/>
    <link rel="stylesheet" href="<?= $this->view->css ?>/iconfont/iconfont.css"/>
    <link rel="stylesheet" href="<?= $this->view->css ?>/select2.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/login.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css">
        <script type="text/javascript" src="<?= $this->view->js_com ?>/jquery.js"></script>
        <script type="text/javascript" src="<?= $this->view->js ?>/swiper.min.js"></script>
        <script type="text/javascript" src="<?= $this->view->js ?>/jquery.SuperSlide.2.1.1.js"></script>
        <script type="text/javascript" src="<?= $this->view->js ?>/select2.min.js"></script>
        <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.cookie.js"></script>
        <script type="text/javascript" src="<?= $this->view->js_com ?>/jquery.nicescroll.js"></script>
        <script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
        <script type="text/javascript" src="<?= $this->view->js ?>/index.js"></script>
        <script type="text/javascript" src="<?= $this->view->js ?>/nav.js"></script>
        <script type="text/javascript" src="<?= $this->view->js ?>/decoration/common.js"></script>
        <script type="text/javascript" src="<?= $this->view->js ?>/base.js"></script>
    <?php } ?>
    
    
    <script type="text/javascript">
        var IM_URL = "<?=Yf_Registry::get('im_api_url')?>";
        var IM_STATU = "<?=Yf_Registry::get('im_statu')?>";
        var BASE_URL = "<?=Yf_Registry::get('base_url')?>";
        var protocolStr = document.location.protocol;
        var SITE_URL = "<?=Yf_Registry::get('url')?>";
        if (protocolStr === "http:") {
            SITE_URL = SITE_URL.replace(/https:/, "http:");
        } else if (protocolStr === "https:") {
            SITE_URL = SITE_URL.replace(/http:/, "https:");
        } else {
            SITE_URL = SITE_URL.replace(/https:/, "http:");
        }
        var INDEX_PAGE = "<?=Yf_Registry::get('index_page')?>";
        var STATIC_URL = "<?=Yf_Registry::get('static_url')?>";
        var PAYCENTER_URL = "<?=Yf_Registry::get('paycenter_api_url')?>";
        var UCENTER_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";
        var is_open_city = "<?= Web_ConfigModel::value('subsite_is_open');?>";
        var DOMAIN = document.domain;
        var WDURL = "";
        var SCHEME = "default";
        var MASTER_SITE_URL = "<?=Yf_Registry::get('shop_api_url')?>";
    </script>
    
    <script type="text/javascript">
        //分站定位
        if (!getCookie("isset_local_subsite") && !getCookie("sub_site_id")) {
            //没有设置过分站，就调用该方法
            getLocalSubsite();
        }
        
        function getLocalSubsite() {
            $.ajax({
                type: "GET",
                url: SITE_URL + "?ctl=Base_District&met=getLocalSubsite&typ=json",
                data: {},
                dataType: "json",
                success: function (data) {
                    if (data.status == 200) {
                        $.cookie("isset_local_subsite", 1);
                        $.cookie("sub_site_id", data.data.subsite_id);
                        $.cookie("sub_site_name", data.data.sub_site_name);
                        $.cookie("sub_site_logo", data.data.sub_site_logo);
                        $.cookie("sub_site_copyright", data.data.sub_site_copyright);
                        window.location.reload();
                    }
                }
            });
        }
    </script>
    <?php
    include $this->view->getTplPath() . '/' . 'translatejs.php';
    ?>
</head>
<body>
<div class="head">
	<div class="wrapper clearfix">
		<div class="head_left">
			<div id="login_top">
				<dl class="header_select_province">
					<dt><b class="iconfont icon-dingwei2"></b>
						<span id="area"><?= Web_ConfigModel::value('subsite_is_open') ? ($show_area_name = __($_COOKIE['sub_site_name']) ? :__('全部')):__($show_area_name); ?></span>
					</dt>
					<dd>
					</dd>
				</dl>
			</div>

		</div>
		<div class="head_right">
			<dl>
                                <p></p>
				<dt><a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Order&met=physical"><?=__('我的订单')?></a></dt>
				<dd class="rel_nav">
					<a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Order&met=physical"><?=__('实物订单')?></a>
					<a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Order&met=virtual"><?=__('虚拟订单')?></a>
				</dd>
			</dl>

			<dl>
				<p></p>
				<dt><a href="<?= Yf_Registry::get('paycenter_api_url') ?>?ctl=Info&met=recordlist" target="_blank"><span class="iconfont icon-paycenter bbc_color"></span><?=Yf_Registry::get('paycenter_api_name')?></a></dt>
			</dl>
<!--			<dl>-->
<!--				<p></p>-->
<!--				<dt><a href="--><?//= Yf_Registry::get('im_url') ?><!--" target="_blank"><span class="iconfont icon-sns bbc_color"></span>SNS</a></dt>-->
<!--			</dl>-->

			<dl>
                             <p></p>
				<dt><a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Favorites&met=favoritesGoods" target="_blank"><span class="iconfont icon-taoxinshi bbc_color"></span><?=__('我的收藏')?></a></dt>
				<dd class="rel_nav">
					<a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Favorites&met=favoritesShop"><?=__('店铺收藏')?></a>
					<a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Favorites&met=favoritesGoods"><?=__('商品收藏')?></a>
					<a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Favorites&met=footprint"><?=__('我的足迹')?></a>
				</dd>
			</dl>
			<dl>
                             <p></p>
				<dt>
					<a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Cart&met=cart"><span class="iconfont icon-zaiqigoumai bbc_color"></span><?=__('购物车')?></a>
				</dt>
			</dl>
			<dl>
                             <p></p>
				<dt><a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Service_Custom&met=index" target="_blank"><?=__('客服中心')?></a></dt>
				<dd class="rel_nav">
					<a href="<?= Yf_Registry::get('url') ?>?ctl=Article_Base&met=index&article_id=2"><?=__('帮助中心')?></a>
					<a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Service_Return&met=index"><?=__('售后服务')?></a>
			</dl>
			<dl>
				<dt><span class="iconfont icon-shoujibangding bbc_color"></span><a href="#"><?=__('手机版')?></a></dt>
				<dd class="rel_nav  phone-code">
					<img src="<?=Yf_Registry::get('base_url')?>/shop/api/qrcode.php?data=<?=urlencode(Yf_Registry::get('shop_wap_url'))?>" width="150" height="150"/></dd>
			</dl>
<!--			<dl>
				<dt><a href="#">商家支持</a></dt>
				<dd class="rel_nav"><a href="#">代付款订单</a><a href="#">代付款订单</a><a href="#">代付款订单</a></dd>
			</dl>-->
		</div>
	</div>
</div>
