<?php
include   '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en" class="bgf">
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
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit:cover">
	<title>特色商城</title>
	<link rel="stylesheet" href="../css/base.css">
	<link rel="stylesheet" href="../css/customize.css">
	<link rel="stylesheet" href="../css/swiper.min.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_1369976_bi587t54c07.css">
	<link rel="stylesheet" href="../css/iconfont.css">

	<link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
	<link rel="stylesheet" type="text/css" href="../css/nctouch_products_detail.css?v=311">
</head>
<body class="bgf">
	<!-- <header id="header" class="fixed customize-index-header"> -->
        <!-- <div class="header-wrap"> -->
    <!--        <div class="left">
			   <a class="iblock" href="javascript:history.go(-1)"><i class="zk-head-back"></i></a>
		   </div> -->
<!--             <div class="header-title">
                <h1>特色商城</h1>
            </div> -->
  <!--          <div class="right fz0">
               <a class="iblock" href="javascript:void(0);"><i class="zk-head-more"></i></a>
           </div> -->
        <!-- </div> -->
    <!-- </header> -->
    <div id="product_detail_html" class="posr zIndex1"></div>
    <div class="nctouch-main-layout" >
	    <div class="swiper-container custom-product-det-swiper" id="goods_image"></div>
	    <div class="custom-product-det-content">
	    	<div class="custom-product-det-texts">
	    		<div class="custom-product-det-texts-head clearfix">
	    			<em class="img-box fl" id="shop_img" style=""></em>
	    			<span class="fl"><em class="block" id="shop_name">剪纸艺人</em>
	    				<!-- <time class="block">2019-05-28 16:10</time> -->
	    			</span>
	    			<button class="fr custom-product-det-follow pd-collect <% if (is_favorate) { %>favorate<% } %>">收藏</button>
	    		</div>
	    <!-- 		<h4 class="clearfix tc fz0">
	    			<span>剪纸66</span>
	    		</h4> -->
	    		<div class="contentC">
			    <!-- <div class="goods-detail-bottom mt-20"><a href="javascript:void(0);"><?= __('商品详情'); ?></a></div> -->
					<div class="special-goods-des">
						<p class="special-goods-des-tit tc"><span>详情</span></p>
					</div>
			        <div class="nctouch-main-layout mt0" id="fixed-tab-pannel">
			            <div class="fixed-tab-pannel mb-20" style="margin-left: -30px;"></div>
			        </div>
			    </div>

	    		<!-- <div id="content"></div> -->
	    	</div>
	    	<div class="custom-product-det-btn fz0">
	    		 <span class="btn-zan" style="width:100%;"><i class="custom-zan"></i><em id="zan_sum">0</em></span>
	    	</div>
	    </div>
    </div>
</body>

<script type="text/javascript" src="../js/zepto.min.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/swipe.js"></script>
<script type="text/javascript" src="../js/common.js?v=9"></script>
<script type="text/javascript" src="../js/zepto.cookie.js"></script>
<script type="text/javascript" src="../js/iscroll.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
<script type="text/javascript" src="../js/fly/requestAnimationFrame.js"></script>
<script type="text/javascript" src="../js/details2.js"></script>
<script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>
<script type="text/html" id="product_detail">
	 <ul class="swiper-wrapper">
		<% if(goods_image) {%>
			<% for(i in goods_image) { %>
			    <li class="swiper-slide">
					<img class="wp100" src="<%=goods_image[i]%>" alt="">
				</li>
			<% } %>
		<% } %>
		</ul>
	    <div class="swiper-pagination"></div>
</script>
<script src="../js/swiper.min.js"></script>
<script>
	$(function(){
		var windowWidth=$(window).width();
        $(".custom-product-det-swiper").css("height",windowWidth);
        
	})
</script>

<?php
include '../includes/footer.php';
?>
</html>
