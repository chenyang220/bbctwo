<?php
    include __DIR__ . '/../../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title>签到.优惠</title>
	<link rel="stylesheet" href="/../../css/base.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_y8zleljop7c.css">
	<link rel="stylesheet" type="text/css" href="/../../css/nctouch_categroy.css">
</head>
<style type="text/css">
	

	.gq{
		color: #bcbcbc;
	}
</style>
<body>
	<header id="header" class="posf borb1">
	    <div class="header-wrap">
	        <div class="header-l"> <a href="javascript:history.go(-1)"> <i class="back"></i> </a> </div>
			<div class="header-title">
				<h1>签到.优惠</h1>
			</div>
	    </div>
	</header>
	<div class="nctouch-main-layout">
		<div class="zk-sign-head">
			<div class="clearfix">
				<div class="fl">
					<p>
						<span><em>积分</em><i class="zk-jf"></i><b>:</b></span>
						<strong id="pointnum">1200</strong>
					</p>
<!-- 					<p>
						<span><em>金币</em><i class="zk-jb"></i><b>:</b></span>
						<strong>2088</strong>
					</p> -->
				</div>
				<span class="fr zk-level-sign">积分规则</span>
				<!-- <span class="fr zk-level-sign active">黑卡会员</span> -->
			</div>
			<p class="zk-sign-days">已连签<b id="continuation_sign">0</b>天</p>
			<div id="sign_heard"></div>
			<!-- <ul class="zk-sign-det-ul clearfix fz-0 tc">
				<li>
					<span>+1</span>
					<em class="block">04-06</em>
					<b></b>
				</li>
				<li>
					<span>+1</span>
					<em class="block">04-07</em>
					<b></b>
				</li>
				<li>
					<span>+1</span>
					<em class="block">04-08</em>
					<b></b>
				</li>
				<li class="active signed">
					<span>+1</span>
					<em class="block">今日</em>
					<b></b>
				</li>
				<li>
					<span class="color">+2</span>
					<em class="block">04-10</em>
					<b></b>
				</li>
				<li>
					<span class="color">+3</span>
					<em class="block">04-11</em>
					<b></b>
				</li>
				<li>
					<span class="color">+4</span>
					<em class="block">04-12</em>
					<b></b>
				</li>
			</ul> -->
			<div class="tc"><button class="btn-sign-get">签到领取金币</button></div>
		</div>
		<div class="zk-module-tit clearfix">
			<span>代金券</span><a class="fr" href=""><em>查看更多</em><i class="iconfont icon-btnrightarrow"></i></a>
		</div>
		<ul class="zk-voucher-items clearfix">
			<li>
				<a href="">
				<span class="fl">
					<em><b>满</b><strong>199-100</strong></em>
					<br/>
					<b>优惠券</b>
				</span>
				<span class="fr">立即<br/>领取</span>
				</a>
			</li>
			<li>
				<a href="">
				<span class="fl">
					<em><b>满</b><strong>199-100</strong></em>
					<br/>
					<b>优惠券</b>
				</span>
				<span class="fr">立即<br/>领取</span>
				</a>
			</li>
			<li>
				<a href="">
				<span class="fl">
					<em><b>满</b><strong>199-100</strong></em>
					<br/>
					<b>优惠券</b>
				</span>
				<span class="fr">立即<br/>领取</span>
				</a>
			</li>
			<li>
				<a href="">
				<span class="fl">
					<em><b>满</b><strong>199-100</strong></em>
					<br/>
					<b>优惠券</b>
				</span>
				<span class="fr">立即<br/>领取</span>
				</a>
			</li>
		</ul>
		<div>
			<div class="zk-module-tit clearfix borb1 bgf">
				<span>专属代金券</span><a class="fr" href=""><i class="iconfont icon-btnrightarrow"></i></a>
			</div>
			<ul class="sign-jb-exchange-items">
				<li>
					<a href="javascript:;" class="img-box"><img class="cter" src="https://www.yuanfengtest.com/image.php/shop/data/upload/media/6fc0625bf097e245fa1c1007bf528b2d/10011/9/image/20181126/1543234155824096.jpg!360x64.jpg" alt=""></a>
					<div class="sign-jb-exchange-content">
						<h4><a href="javascript:;">韩式部队火锅套餐</a></h4>
						<p class="sign-goods-ty"><a class="block one-overflow" href="">套餐：辛拉面、鱼饼、芝士年糕、时令蔬菜、菌菇类...</a></p>
						<p class="tr"><a class="btn-zk-exchange" href="javascript:;">兑换</a></p>
					</div>
				</li>
				<li>
					<a href="javascript:;" class="img-box"><img class="cter" src="https://www.yuanfengtest.com/image.php/shop/data/upload/media/6fc0625bf097e245fa1c1007bf528b2d/10011/9/image/20181126/1543234155824096.jpg!360x64.jpg" alt=""></a>
					<div class="sign-jb-exchange-content">
						<h4><a href="javascript:;">韩式部队火锅套餐</a></h4>
						<p class="sign-goods-ty"><a class="block one-overflow" href="">套餐：辛拉面、鱼饼、芝士年糕、时令蔬菜、菌菇类...</a></p>
						<p class="tr"><a class="btn-zk-exchange" href="javascript:;">兑换</a></p>
					</div>
				</li>
				
			</ul>
		</div>
	</div>
<script type="text/html" id="sign_heard_template">
    <% if (curdate) { %>
    	
		<ul class="zk-sign-det-ul clearfix fz-0 tc">
			<% for (var i in curdate) { %>
			<li  class="<%= curdate[i].time == '今日' ? 'active signed' : '' %>   <%= curdate[i].type == 0 ? 'gq' : '' %> ">
				<span class="<%= curdate[i].time == '今日' ? 'block' : '' %>  <%= curdate[i].type == 1 && curdate[i].time != '今日'? 'color' : '' %>" >+<%= curdate[i].grade %></span>
				<em class="block"><%= curdate[i].time %></em>
				<b></b>
			</li>
			<% } %>
		</ul>
	<% } %>
    <!-- <div>
        <% if (title) { %>
        <div class="common-tit tc">
            <h4 class='fz-32'><%= title %></h4>
        </div>
        <% } %>
        <div class="layout2 pl-20 pr-20">
            <ul class="clearfix fz0">
                <% for (var i in item) { %>
                <li><a href="<%= item[i].url %>"><img src="<%= item[i].image %>" alt=""></a></li>
                <% } %>
            </ul>
        </div>
    </div> -->
</script>

	<script type="text/javascript" src="/../../js/zepto.js"></script>
    <script type="text/javascript" src="/../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="/../../js/template.js"></script>
    <script type="text/javascript" src="/../../js/common.js"></script>
    <script type="text/javascript" src="/../../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="/../../js/sign.js"></script>
</body>
<?php
    include __DIR__ . '/../../includes/footer.php';
?>
</html>
