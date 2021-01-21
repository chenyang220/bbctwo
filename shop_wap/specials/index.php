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
	<link rel="stylesheet" href="../css/customize.css?v=666">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_1369976_bi587t54c07.css">
	<link rel="stylesheet" href="../css/iconfont.css">
	<link rel="stylesheet" type="text/css" href="../css/swiper.min.css">
    <script src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/swiper.min.4.4.1.js"></script>
	
</head>
<body>
	
    <div class="customize-feature-page-head clearfix">
    	<div class="iblock city">
    		<em>乌鲁木齐</em><i class="zk-drap"></i>
    	</div>
    	<div class="customize-feature-page-input">
				<i class="iconfont icon-search"></i>
				<div class="flex1">
					<input class="placeholder-c1 wp100" type="text" placeholder="搜你想搜的">
				</div>
				<label for="search" id="search">搜索</label>
    	</div>
    </div>
   <div class="customize-feature-page-contents">
   		<div id="ts_logo"></div>
		<div class="pl15 pr30 bgf">
			<div class="pl15">
				<div class="swiper-container swiper-custom-index">
					<ul class="swiper-wrapper" id="adv_list"></ul>
					<div class="swiper-pagination swiper-paginations" id="pagination"></div>
				</div>
				<h5 class="custom-module-tit"><span>推荐</span></h5>
			</div>
			<ul class="custom-spec-lists masonry" id="goods_list"></ul>
		</div>
    </div>


    <script type="text/html" id="ts_logo_template">
	  	<ul class="customize-feature-page-navs clearfix bgf" style="margin-top:15px">
	  		<% if (data.label_tag_sort) {  %>
	  			<% for (var i in data.label_tag_sort) { %>
		    		<li class="swiper-slide">
		    			<a href="lists.html?label_id=<%=data.label_tag_sort[i].id%>">
		    				<img src="<%=data.label_tag_sort[i].label_logo%>" alt="nav">
		    				<span><%=data.label_tag_sort[i].label_name%></span>
		    			</a>
		    		</li>
		    	<% } %>
    		<% } %>
    	</u>
	</script>

	<script type="text/html" id="adv_list_template">
  		<% if (data.layout_list) { var mb_tpl_layout_data = data.layout_list.adv_list.mb_tpl_layout_data; %>
  			<% for (var i in mb_tpl_layout_data) { %>
	    		<li class="swiper-slide">
	    			<a href="">
	    				<img class="wp100" src="<%=mb_tpl_layout_data[i].image%>">
	    			</a>
	    		</li>
	    	<% } %>
		<% } %>
	</script>


	<script type="text/html" id="goods_list_template">
  		<% if (goods) { %>
  			<% for (var i in goods) { %>
	    		<li class="item">
					<a class="pad" href="details1.html?goods_id=<%=goods[i].goods_id%>">
						<em class="img-box"><img src="<%=goods[i].goods_image%>" alt="goods">
							<!-- <b>景点·3.2km</b> -->
						</em>
						<div class="cont">
							<!-- <h6>坎儿井是一特殊灌溉系统，与万里长城和京杭大运河为古代三大工程</h6> -->
							<% if (goods[i].label_name != '') { %>
								<p>
	  								<% for (var j in goods[i].label_name) { %>
										<label><%=goods[i].label_name[j]%></label>
									<% } %>
								</p>
							<% } %>

							<div class="rel">
								<b class="rmb">￥</b>
								<strong><%=goods[i].goods_price%></strong>
								<em><%=goods[i].goods_salenum%>人付款</em>
							</div>
						</div>
					</a>
				</li>
	    	<% } %>
		<% } %>
	</script>
	<script src="../js/special-common.js?v=8"></script>
	<script src="../js/waterfall.js?v=8"></script>
	<script type="text/javascript" src="../js/cookie.js"></script>
	<script type="text/javascript" src="../js/zepto.min.js" ></script>
    <script type="text/javascript" src="../js/animation.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/specials/index.js"></script>
	<script type="text/javascript" src="../js/tmpl/footer.js"></script>
	<script>
		
		   // html为测试数据
        // html='<li class="item"><a class="pad" href="details1.html"><em class="img-box"><img src="../images/spec/demo1.png" alt="goods"><b>景点·3.2km</b></em><div class="cont"><h6>吐鲁番火焰山 —— 位于新建吐鲁番市东北区是全国最热的地方</h6><p><label>炎热</label><label>孙悟空</label></p>'+
        // '<div class="rel"><b class="rmb">￥</b><strong>159.00</strong><em>330人付款</em></div></div></a></li><li class="item"><a class="pad"><em class="img-box"><img src="../images/spec/demo2.png" alt="goods"><b>景点·3.2km</b></em><div class="cont"><h6>吐鲁番火焰山 —— 位于新建吐鲁番市东北区是全国最热的地方</h6><p><label>炎热</label><label>孙悟空</label></p>'+
        // '<div class="rel"><b class="rmb">￥</b><strong>159.00</strong><em>330人付款</em></div></div></a></li>';             
       
		$('#search').click(function(){
			window.location.href="./lists.php";
		})
	</script>
</body>
</html>

<?php
include '../includes/footer.php';
?>