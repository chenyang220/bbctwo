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
		<link rel="stylesheet" href="../../css/distribution_shop.css">
	</head>
	<script type="text/javascript">
    //用app电话号码登录
    var u_id = '<?php echo $u_id;?>';
    if (u_id) {
       window.location.href = UCenterApiUrl + '/?ctl=Login&met=oauth&typ=e&u_id=' + u_id + "&return_url=" + WapSiteUrl + "/tmpl/member/distribution_shop.html";
    }
</script>
	<body>

		<div class="main_heard" style="background-image:url('../../images/group.png');background-size: 100%;">
		  	<div class="head">
		  		<img class="account-bg shop_logo" src="../../images/group.png"></img>
		  		<div class="heaer_name">新型小店</div>
		  		<div class="header_in">
		  			<a href="distribution_shop_edit.html">编辑店铺</a>
		  			<a href="directseller_goods.html">商品库</a>
		  		</div>
		  	</div>
		</div>
		<div class="lode"><img src="../../images/Bitmap.png"></img></div>
		<div class="main">
			<div class="clearfix wp100 member-nav-header borb1">
		      <text class="fl">店长推荐</text>
		      <a class="fr recommend-more"><text class="gen align-middle">更多</text><icon class="iconfont icon-arrow-right col9 align-middle fz-26"></icon></a>
	  		</div>
		  	<div class="order-status fle plr30 tc mb20 distribution-recommend-list">
		    </div>
	    </div>
	  	<div class="main_one">
	  	<div class="clearfix wp100 member-nav-header">
		    <text class="fl">热销商品</text>
		    <a class="fr hot-more" ><text class="gen align-middle">更多</text><icon class="iconfont icon-arrow-right col9 align-middle fz-26"></icon></a>
	    </div>
	    <div class="order-status fleu plr20 tc mb20 distribution-hot-list">
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
<script type="text/javascript" src="../../js/tmpl/distribution_shop.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<script type="text/javascript">
	$.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getDistributionShopInfo&typ=json",
            data: {k: getCookie('key'),u: getCookie('id')},
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

<script type="text/html" id="distributed-recommend-goods">
	<% var recommend_goods = data.recommend; %>
	<% var user_id = data.userId; %>
	<%if(recommend_goods.length >0){%>
		<%for(j=0;j < recommend_goods.length;j++){%>
			<% var goods_list = recommend_goods[j]%>
			<a class="relative" href="../product_detail.html?goods_id=<%=goods_list.goods_id;%>&rec=u<%=user_id%>s<%=goods_list.shop_id%>c<%=goods_list.common_id%>">
			    <img src="<%=goods_list.common_image%>"></img>
			    <text class="iblock distribution-tit more-overflow col3"><%=goods_list.common_name%></text>
			    <text class="iblock red distribution-tit">￥<%=goods_list.common_price%></text>
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
			    <text class="iblock distribution-tit more-overflow col3"><%=goods_list.common_name%></text>
			    <text class="block red cumb">￥<%=goods_list.common_price%></text>
			</a>
		<%}%>
	<%}%>
</script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>