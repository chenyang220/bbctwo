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
	<title>剪纸</title>
	<link rel="stylesheet" href="../css/base.css">
	<link rel="stylesheet" href="../css/customize.css">
	<link rel="stylesheet" href="../css/swiper.min.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_1369976_bi587t54c07.css">
	<link rel="stylesheet" href="../css/iconfont.css">
</head>
<body class="bgf">
	<header id="header" class="fixed customize-index-header">
        <div class="header-wrap">
           <div class="left">
			   <a class="iblock" href="javascript:history.go(-1)"><i class="zk-head-back"></i></a>
		   </div>
            <div class="header-title">
                <h1>剪纸</h1>
            </div>
           <div class="right fz0">
               <a class="iblock" href="javascript:void(0);"><i class="zk-head-more"></i></a>
           </div>
        </div>
        
    </header>
    <div class="nctouch-main-layout">
	    <div class="swiper-container custom-product-det-swiper">
	    	<ul class="swiper-wrapper">
	    		<li class="swiper-slide">
	    			<img class="wp100" src="../images/spec/img5.png" alt="">
	    		</li>
	    		<li class="swiper-slide">
	    			<img class="wp100" src="../images/spec/img5.png" alt="">
	    		</li>
	    	</ul>
	    	<div class="swiper-pagination"></div>
	    </div>
	    <div class="custom-product-det-content">
	    	<div class="custom-product-det-texts">
	    		<div class="custom-product-det-texts-head clearfix">
	    			<em class="img-box fl" style="background:url(../images/spec/img4.png) no-repeat center;background-size:contain"></em>
	    			<span class="fl"><em class="block">剪纸艺人</em><time class="block">2019-05-28 16:10</time></span>
	    			<button class="fr custom-product-det-follow pd-collect <% if (is_favorate) { %>favorate<% } %>">收藏</button>
	    		</div>
	    		<h4 class="clearfix tc fz0">
	    			<span>剪纸</span>
	    		</h4>
	    		<p>关于剪纸的由来，其实很早以前就开始了。那时候的人尝试着用一些图形来记事和内容呈现，但那时纸张还并没有出现，人们只能将一些资料记载在青铜器、竹简、兽皮等载体之上。而随着纸张的出现，这些创造性的图案便开始往纸张上转移。通过这样的前后对比，我们就能够看出，剪纸的由来就是来自于先民的图案记录方式。 
  				</p>
  				<p>关于剪纸的由来，其实很早以前就开始了。那时候的人尝试着用一些图形来记事和内容呈现，但那时纸张还并没有出现，人们只能将一些资料记载在青铜器、竹简、兽皮等载体之上。而随着纸张的出现，这些创造性的图案便开始往纸张上转移。通过这样的前后对比，我们就能够看出，剪纸的由来就是来自于先民的图案记录方式。</p>     
	    	</div>
	    	<div class="custom-product-det-btn fz0">
	    <!-- 		 <span class="btn-save"><i class="custom-like"></i><em>222</em></span> -->
	    		 <span class="btn-zan" style="width:100%;"><i class="custom-zan"></i><em>0</em></span>
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
<script>
	$(function(){
		var windowWidth=$(window).width();
        $(".custom-product-det-swiper").css("height",windowWidth);
        var swiper = new Swiper('.custom-product-det-swiper', {
        	autoplay:3000,
	        pagination: '.swiper-pagination',
	        paginationType: 'fraction'
	    });
	})
</script>

<?php
include '../includes/footer.php';
?>
<!-- <p>       
	<img src="https://shops.look56.com/image.php/shop/data/upload/media/b54a7238a221685616d4fe794fa43b0b/10002/1/image/20200918/160041193324045019836168904353.jpg" alt="5919c3cbaff2bf8bec236c79e4b9ad39.jpg" />
	<img src="https://shops.look56.com/image.php/shop/data/upload/media/b54a7238a221685616d4fe794fa43b0b/10002/1/image/20200918/160041223120727948548836746111.jpeg" alt="9f47b0f2fba713e208e9f478d0bb0e19.jpeg" />
	<span style="color:rgb(34,34,34);font-family:Consolas, 'Lucida Console', 'Courier New', monospace;font-size:12px;white-space:pre-wrap;background-color:rgb(255,255,255);">关于剪纸的由来，其实很早以前就开始了。那时候的人尝试着用一些图形来记事和内容呈现，但那时纸张还并没有出现，人们只能将一些资料记载在青铜器、竹简、兽皮等载体之上。而随着纸张的出现，这些创造性的图案便开始往纸张上转移。通过这样的前后对比，我们就能够看出，剪纸的由来就是来自于先民的图案记录方式。 
  				</span>
</p>

<p>					
</p> -->


</html>
