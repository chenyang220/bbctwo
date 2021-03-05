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
<!-- 	<header id="header" class="posf borb1">
	    <div class="header-wrap">
	        <div class="header-l"> <a href="javascript:history.go(-1)"> <i class="back"></i> </a> </div>
			<div class="header-title">
				<h1>签到.优惠</h1>
			</div>
	    </div>
	</header> -->
	<div class="" >
		<div class="zk-sign-head">
			<div class="clearfix">
				<div class="fl">
					<p>
						<span><em>积分</em><i class="zk-jf"></i><b>:</b></span>
						<strong id="pointnum">1200</strong>
					</p>
				</div>
				<span class="fr zk-level-sign">积分规则</span>
			</div>
			<p class="zk-sign-days">已连签<b id="continuation_sign">0</b>天</p>
			<div id="sign_heard"></div>
			<div class="tc"><button class="btn-sign-get"  id="sign_btn">签到领取积分</button></div>
		</div>
		<div class="zk-module-tit clearfix">
			<span>代金券</span><a class="fr" href="/../tmpl/voucher_list.html"><em>查看更多</em><i class="iconfont icon-btnrightarrow"></i></a>
		</div>
		<div id="voucher_list"></div>
		<div>
			<div class="zk-module-tit clearfix borb1 bgf">
				<span>专属代金券</span><a class="fr" href="">
				</a>
			</div>
			<ul class="sign-jb-exchange-items">
				<li>
					<a href="javascript:;" class="img-box"><img class="cter" src="/../../img/旅游.jpg" alt=""></a>
					<div class="sign-jb-exchange-content">
						<h4><a href="javascript:;">旅游</a></h4>
						<p class="sign-goods-ty"><a class="block one-overflow">领取旅游专属代金券</a></p>
						<p class="sign-goods-ty">积分+1</p><!--显示出来-->
						<p class="tr"><a class="btn-zk-exchange" href="javascript:;" date-type="ly" date-src="">去领取</a></p>
					</div>
				</li>
				<li>
					<a href="javascript:;" class="img-box"><img class="cter" src="/../../img/酒店.jpg" alt=""></a>
					<div class="sign-jb-exchange-content">
						<h4><a href="javascript:;">住宿</a></h4>
						<p class="sign-goods-ty"><a class="block one-overflow">领取住宿专属代金券</a></p>
						<p class="sign-goods-ty">积分+1</p><!--显示出来-->
						<p class="tr"><a class="btn-zk-exchange" href="javascript:;"  date-type="zs" date-src="">去领取</a></p>
					</div>
				</li>
				<li>
					<a href="javascript:;" class="img-box"><img class="cter" src="/../../img/美食.jpg" alt=""></a>
					<div class="sign-jb-exchange-content">
						<h4><a href="javascript:;">美食</a></h4>
						<p class="sign-goods-ty"><a class="block one-overflow" >领取美食专属代金券</a></p>
						<p class="sign-goods-ty">积分+1</p><!--显示出来-->
						<p class="tr"><a class="btn-zk-exchange" href="javascript:;" date-type="ms" date-src="">去领取</a></p>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div  class="hide"  id="sign_state">
		<ul>
			<li>积分可用于兑换代金券或在积分商城兑换礼品。</li>
			<li>用户每天总计只能签到一次。</li>
			<li>3天包含3天以内可获得2个积分，4至6天内可获得3个积分，从第7天开始每天可获得5个积分，若任何一天停止签到，则从第一个获取2个积分重新计算，连续"签到"一个月可获取一周的双倍积分奖励。</li>
			<li></li>
		</ul>
		<div class="btn btn_state">知道了</div>
	</div>
<script type="text/html" id="sign_heard_template">
    <% if (curdate) { %>
		<ul class="zk-sign-det-ul clearfix fz-0 tc">
			<% for (var i in curdate) { %>
			<li  class="<%= curdate[i].time == '今日' && sign_satus == 1 ? 'active signed' : '' %> <%= curdate[i].time == '今日'? 'day' : '' %> ">
				<span class="<%= curdate[i].type == 1 ? 'color' : '' %>" >+<%= curdate[i].grade %></span>
				<em class="block"><%= curdate[i].time %></em>
				<b></b>
			</li>
			<% } %>
		</ul>
	<% } %>
</script>
<script type="text/html" id="voucher_list_template">
	<% if (items.length > 0) { %>
		<ul class="zk-voucher-items clearfix">
			<% if (items[0]) { %>
				<li>
					<a href="">
					<span class="fl">
						<em><b>满</b><strong><%=items[0]['voucher_t_limit']%>-<%=items[0]['voucher_t_price']%></strong></em>
						<br/>
						<b>优惠券</b>
					</span>
					<span class="fr">立即<br/>领取</span>
					</a>
				</li>
			<% } %>
			<% if (items[1]) { %>	
				<li>
					<a href="">
					<span class="fl">
						<em><b>满</b><strong><%=items[1]['voucher_t_limit']%>-<%=items[1]['voucher_t_price']%></strong></em>
						<br/>
						<b>优惠券</b>
					</span>
					<span class="fr">立即<br/>领取</span>
					</a>
				</li>
			<% } %>
			<% if (items[2]) { %>
				<li>
					<a href="">
					<span class="fl">
						<em><b>满</b><strong><%=items[2]['voucher_t_limit']%>-<%=items[2]['voucher_t_price']%></strong></em>
						<br/>
						<b>优惠券</b>
					</span>
					<span class="fr">立即<br/>领取</span>
					</a>
				</li>
			<% } %>
			<% if (items[3]) { %>
				<li>
					<a href="">
					<span class="fl">
						<em><b>满</b><strong><%=items[3]['voucher_t_limit']%>-<%=items[3]['voucher_t_price']%></strong></em>
						<br/>
						<b>优惠券</b>
					</span>
					<span class="fr">立即<br/>领取</span>
					</a>
				</li>
			<% } %>
		</ul>
	<% } %>
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
