<?php 
include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
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
		<meta name="wap-font-scale" content="no">
		<meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
		<title>分销小店</title>
		<link rel="stylesheet" type="text/css" href="../../css/base.css">
		<link rel="stylesheet" href="../../css/iconfont.css">
		<style>
			.main_heard {
				width: 100%;
				height: 8.1854545454rem;
			}

			.lode {
				width: 16.181818rem;
				height: 6.95454545rem;
				margin: auto;
				position: relative;
				z-index: 10;
				top: -3.86363636rem;



			}

			.lode img {
				width: 100%;
				height: 100%;
			}

			.main {
				background: rgba(255, 255, 255, 1);
				border-radius: 9px;
				position: relative;
				top: -3.409090rem;
			}

			.fl {
				font-size: 14px;
				padding: 0.568181rem;
				font-family: PingFangSC-Regular, PingFang SC;
				font-weight: 400;
				color: rgba(0, 0, 0, 1);
				display: block;
				position: relative;
				top: 50%;
				/*偏移*/
				margin-top: 0.2rem;
			}

			.fr {
				padding: 0.568181rem;
			}
			.relative{
				margin: 0.19rem;
			}

			.relative img {
				width: 4.718181rem;
				height: 4.718181rem;
			}

			.relatives {
				background: rgba(255, 255, 255, 1);
				border-radius: 4px;
				margin-top: 0.45454545rem;
			}
			.tit{
				margin: .26rem auto 0;
    width: 4.5rem;
    height: 0.7rem;
    line-height: .68rem;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    white-space: normal;
			}

			.relatives img {
				width: 7.609090rem;
				height: 7.609090rem;
				border-top-right-radius: 4px;
				border-top-left-radius: 4px;
			}

			.block {
				font-size: 0.636363rem;
				font-family: PingFangSC-Medium, PingFang SC;
				font-weight: 500;
				color: rgba(74, 74, 74, 1)
			}
             .tits{
             	width: 6.9rem;
                margin: .26rem auto 0;
                height: 1.32rem;
               /* font-size: .52rem; */
                line-height: .68rem;
               /* font-weight: bold; */
                overflow: hidden;
                text-overflow: ellipsis;
                 display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                white-space: normal;
             }
			.red {
				color: rgba(238, 46, 35, 1);
				/*margin-bottom: 0.6818181rem;*/
			}

			.order-status {
				margin-top: 0.454545rem;
			}

			.fle {
				display: flex;
				flex-flow: row wrap;
				justify-content:flex-start;
			}

			.main_one {
				position: relative;
				top: -5.409090rem;
			}

			.gen {
				font-size: 0.545454rem;
				font-family: PingFangSC-Regular, PingFang SC;
				font-weight: 400;
				color: rgba(155, 155, 155, 1);
			}

			.account-bg {
				margin-right: 30rpx;
				width: 2.27272727rem;
				height: 2.27272727rem;
				border-radius: 100%;
				border: 1px solid rgba(255, 255, 255, 0.2);

			}

			.head {
				padding-top: 0.68181rem;
				margin-left: 0.68181rem;
				display: flex;
				justify-content: start;
			}

				.heaer_name {
				width: 6rem;
				padding-left:0.4rem; 
				display: flex;
				justify-content: center;
				flex-direction: column;
				font-size: 0.727272rem;
				font-family: PingFangSC-Regular, PingFang SC;
				font-weight: 400;
				color: rgba(255, 255, 255, 1);
			}

			.header_in{
				width: 60%;
			}


			.header_in a {
				margin-left: 0.727272rem;
				padding: 0.181818rem;
				padding-left: 0.45454545rem;
				padding-right: 0.45454545rem;
				border-radius: 0.59090909rem;
				border: 1px solid rgba(255, 255, 255, 1);
				font-size: 0.56818181rem;
				font-family: PingFangSC-Regular, PingFang SC;
				font-weight: 400;
				color: rgba(255, 255, 255, 1);
			}

			.header_in .nu {
				margin-left: 2.727272rem;
			}

			.deng {
				float: right;
				padding-right: 0.681818rem;
				font-size: 0.636363rem;
				font-family: PingFangSC-Regular, PingFang SC;
				font-weight: 400;
				color: rgba(255, 255, 255, 1);
				position: relative;
				top: -0.227272rem;
			}
			.fleu{
				display: flex;
                flex-flow: wrap;
                justify-content: space-between;
			}
			.order-status{
		       margin-top: 0.454545rem;
               width: 95%;
                margin: auto;
                padding: 0.3rem;
			}
			.cumb{
				margin-bottom: 0.6818181rem;
			}

			.clearfix li {
				width: 25%;
				float: left;
				font-size: 0.545454rem;
				font-family: PingFangSC-Regular, PingFang SC;
				font-weight: 400;
				color: rgba(136, 136, 136, 1);
				text-align: center;
			}
			#footer{
				z-index: 999;
				    font-size: 0;
				    background: #fff;
				    position: fixed;
				    left: 0;
				    bottom: 0;
				    width: 100%;
				    margin-top: 0.83333rem;
				    min-height: 2.454545rem;
				    box-shadow: 0 0.83333rem 1.66667rem #000;
			}
			/*底部*/
			#footer ul li h3{font-size:0.5rem;color:#888;line-height:1.2rem;}
			#footer ul li i.icon{width:1rem;height:1rem;display:inline-block;margin-top:0.25rem;}
			#footer ul li i.icon.footer-first {
			  background: url(../../images/店铺1.png) no-repeat center;
			  background-size: contain;
			}
			
			#footer ul li i.icon.footer-classify {
			  background: url(../../images/红包.png) no-repeat center;
			  background-size: contain;
			}
			
			#footer ul li i.icon.footer-find {
			  background: url(../../images/联系人群组.png) no-repeat center;
			  background-size: 96%;
			}
			
			#footer ul li i.icon.footer-cart {
			  background: url(../../images/个人.png) no-repeat center;
			  background-size: contain;
			}
			
			#footer ul li i.icon.footer-myMan {
			  background: url(../../images/new/icon-mine.png) no-repeat center;
			  background-size: contain;
			}			
		</style>
	</head>
	<body>

			<div class="main_heard" style="background-image:url('../../images/group.png');background-size: 100%;">
			  <div class="head">
			  	<img class="account-bg shop_logo" src=""></img>
			  	<div class="heaer_name">新型小店</div>
			  </div>
			  <text class="deng logbtn">登录</text>
			</div>
			<div class="lode"><img src="../../images/Bitmap.png"></img></div>
			<div class="main">
				<div class="clearfix wp100 member-nav-header borb1">
			      <text class="fl">店长推荐</text>
			      <a class="fr recommend-more"><icon class="iconfont icon-arrow-right c-ccc"></icon></a>
			  	</div>
			  	<div class="order-status fle plr30 tc mb20 recommend_list">
		    	</div>
		  	</div>
		    <div class="main_one">
		  	<div class="clearfix wp100 member-nav-header">
			    <text class="fl">热销商品</text>
			    <a class="fr hot-more"><text class="gen">更多</text><icon class="iconfont icon-arrow-right c-ccc"></icon></a>
		    </div>
		    <div class="order-status fleu plr20 tc mb20 hot_list">
	    	</div>
		<footer id="footer">
			<ul class="clearfix">
				<li>
					<a href="#" id="distribution_introduction">
						<i class="icon footer-first"></i>
						<h3>店铺介绍</h3>
					</a>
					<b class="active"></b>
				</li>
				<li>
					<a href="../redpacket_plat.html">
						<i class="icon footer-classify"></i>
						<h3>领红包</h3>
					</a>
					<b class="active"></b>
				</li>
				<li>
					<a href="#" id="tel-phone">
						<i class="icon footer-find"></i>
						<h3>联系我们</h3>
					</a>
					<b class="active"></b>
				</li>
				<li >
					<a href="member.html">
						<i class="icon footer-cart"></i>
						<h3>我的</h3>
					</a>
					<b class="active"></b>
				</li>
			</ul>
		</footer>	  
	</body>
</html>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/directseller_shop_detail.js"></script>
<script type="text/javascript">
	if(getCookie('key')){
		$(".deng").hide();
	}
	var distribution_shop_id = getQueryString("sid");
	$.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getDistributionShopDetail&typ=json",
            data: {distribution_shop_id:distribution_shop_id},
            dataType: "json",
            success: function (r) {
            	if(r.status==200){
            		$(".shop_logo").attr("src",r.data.distribution_logo);
            		$(".heaer_name").html(r.data.distribution_name);
            		$("#tel-phone").attr("href","tel:"+r.data.distribution_phone);
            		$("#distribution_introduction").attr("href","distribution_introduction.html?sid="+r.data.distribution_shop_id);
            		$(".recommend-more").attr("href","more_list.html?status=recommend&sid="+r.data.distribution_shop_id);
            		$(".hot-more").attr("href","more_list.html?status=hot&sid="+r.data.distribution_shop_id);
            	}   
            }
        });
</script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>

<script type="text/html" id="distributed-recommend-goods">
	<% var recommend_goods = data.recommend; %>
	<% var user_id = data.userId; %>
	<%if(recommend_goods.length >0){%>
		<%for(j=0;j < recommend_goods.length;j++){%>
			<% var goods_list = recommend_goods[j]%>
			<a class="relative" href="../product_detail.html?goods_id=<%=goods_list.goods_id;%>&rec=u<%=user_id%>s<%=goods_list.shop_id%>c<%=goods_list.common_id%>">
			    <img src="<%=goods_list.common_image%>"></img>
			    <text class="block tit"><%=goods_list.common_name%></text>
			    <text class="block red">￥<%=goods_list.common_price%></text>
			</a>
		<%}%>
	<%}%>	
</script>
<script type="text/html" id="distributed-hot-goods">
	<% var hot_goods = data.hot; %>
	<% var user_id = data.userId; %>
	<%if(hot_goods.length >0){%>
		<%for(j=0;j < hot_goods.length;j++){%>
			<% var goods_list = hot_goods[j]%>
			<a class="relatives" href="../product_detail.html?goods_id=<%=goods_list.goods_id;%>&rec=u<%=user_id%>s<%=goods_list.shop_id%>c<%=goods_list.common_id%>">
			    <img src="<%=goods_list.common_image%>"></img>
			    <text class="block tits"><%=goods_list.common_name%></text>
			    <text class="block red cumb">￥<%=goods_list.common_price%></text>
			</a>
		<%}%>
	<%}%>
</script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>