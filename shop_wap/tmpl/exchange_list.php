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
        <title><?= __('商城触屏版'); ?></title>
        <link rel="stylesheet" type="text/css" href="../css/base.css?v=1111">
        <link rel="stylesheet" type="text/css" href="../css/index.css">
        <link rel="apple-touch-icon" href="images/touch-icon-iphone.png"/>
        <link rel="stylesheet" type="text/css" href="../css/nctouch_integral.css?v=9"/>
        <link rel="stylesheet" href="../css/iconfont.css">
    </head>
    <body>
		<header id="" class="fixed borb0">
			<div class="header-wrap">
				<div class="header-l">
					<!-- <a href="javascript:history.go(-1)"> <i class="back back2"></i> </a> -->
				</div>
				<div class="header-title posr">
					<h1 class="drap-h1-after" id="z-tab-order" data-order_state="all">兑换记录</h1>
				</div>
			</div>
		</header>
		<div class="nctouch-main-layout">
			<div class="nctouch-single-nav">
				<ul class="w20h">
					<li class="selected status_0"><a href="javascript:void(0);" onclick="express_status(0)">全部</a></li>
					<li class="status_1"><a href="javascript:void(0);" onclick="express_status(1)">待发货</a></li>
					<li class="status_2"><a href="javascript:void(0);" onclick="express_status(2)">待收货</a></li>
					<li class="status_3"><a href="javascript:void(0);" onclick="express_status(3)">已完成</a></li>
				</ul>
			</div>
			<div id="exchange_list"></div>
		</div>

		<script type="text/javascript" src="../js/zepto.min.js"></script>
		<script type="text/javascript" src="../js/simple-plugin.js"></script>
		<script type="text/javascript" src="../js/template.js"></script>
		<script type="text/javascript" src="../js/common.js"></script>
		<script type="text/javascript" src="../js/exchange_list.js"></script>
		<script id="exchange_list_template" type="text/html">
			<ul class="exchange-orders-list">
  				<% for (var i=0; i < items.length; i++) { %>
					<li>
						<div class="list-head clearfix">
							<span class="fl">订单编号：<%= items[i].points_order_rid%></span>
							<div class="fr fz0">
								<b class="exchange-orders-status active">
								<%if (items[i].points_orderstate == 1) {%>
								   待发货		
								<%}%>	
								<%if (items[i].points_orderstate == 2) {%>
								   待收货	
								<%}%>
								<%if (items[i].points_orderstate == 3) {%>
								   完成	
								<%}%>
								<%if (items[i].points_orderstate == 4) {%>
								   交易取消	
								<%}%>
								</b>
								<%if (items[i].points_orderstate == 3) {%>
									<b class="exchange-orders-status" onclick='del("<%= items[i].points_order_id%>")'>删除</b>	
								<%}%>	
							</div>
						</div>


						<% for (var j=0; j < items[i].points_ordergoods_list.length; j++) { %>
							<a class="list-center flex wp100">
								<em class="img-box"><img src="<%= items[i].points_ordergoods_list[j].points_goodsimage%>" alt="" class="cter"></em>
								<span class="flex1 one-overflow exchange-goods-name"><%= items[i].points_ordergoods_list[j].points_goodsname%></span>
								<b><%= items[i].points_ordergoods_list[j].points_goodspoints%>积分</b>
								<i>x<%= items[i].points_ordergoods_list[j].points_goodsnum%></i>
							</a>
						<% } %>
						<p class="tr exchange-points-total"><span>合计：<%= items[i].points_allpoints%>积分</span></p>
						<div class="list-bottom clearfix">
							<time class="fl"><%= items[i].points_addtime%></time>
							<div class="fr list-bottom-btn fz0">
								<%
								   if (items[i].points_orderstate != 1 && items[i].points_orderstate != 4) {
								%>
								<a onclick='express("<%=items[i].points_order_id%>")' class="style1 express_<%=items[i].points_order_id%>" href="javascript:;"   
								data-order_id ="<%=items[i].points_order_rid%>" 
								data-express_name="<%=items[i].points_logistics%>" 
								data-shiping_code ="<%=items[i].points_shippingcode%>" 
								data-shiping_express ="<%=items[i].points_express_id%>"
									>查看物流</a>
									<% if (items[i].points_orderstate != 3) {%>
										<a onclick='test("<%= items[i].points_order_id%>")' class="style2" href="javascript:;">确认收货</a>
									<% } %>
								<%
								   }
								%>
							</div>
						</div>
					</li>
				<% } %>
			</ul>
    </script>
    </body>
    </html>
<?php
include __DIR__ . '/../includes/footer.php';
?>