<?php 
include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<!-- <link rel="stylesheet" type="text/css" href="static/css/pub.css" /> -->
		<link rel="stylesheet" type="text/css" href="../../css/base.css">
		<link rel="stylesheet" href="../../css/iconfont.css">
		<link rel="stylesheet" href="../../css/style.css" />
		<title>首页</title>
		<style>
			.extension_box{padding-top: 4.045454rem;overflow-y: auto;background:rgba(255,255,255,1);}
			.extension_top{width: 100%;position: fixed;left: 0;top: 0;background: #FFFFFF;z-index: 2;margin-top: 2rem;}
			.extension_sec{width: 100%;position: relative;text-align: center;padding: 0.3181rem 0 0.227272rem;border-bottom: 1px solid rgba(241,241,241,1);;}
			.extension_sec1{width: 60.8%;background:rgba(247,247,247,1);border-radius:17px;border:1px solid rgba(241,241,241,1);height: 1.5rem;padding: 0 1.3636rem 0 0.6818rem;font-size: 0.6363rem;color:#333333;}
			.extension_sec2{width: 0.8181rem;height: 0.8181rem;position: absolute;right:15.5%;top: 0.6818rem;}
			.extension_type{width: 100%;display: flex;border-bottom: 1px solid #CCCCCC;}
			.extension_type1{flex: 1;text-align: center;font-size: 0.6363rem;color:rgba(74,74,74,1);;line-height: 42px;cursor: pointer;}
			.extension_type2{width: 0.4090rem;height: 0.1818rem;}
			.extension_type1.active{color: #FF5B7B;}
			.prolist{padding:  0 4.2%;margin-top: 2rem;}
			.prolist1{display: flex;border-bottom:1px solid #CCCCCC;padding:21px 2.0%;}
			.prolist2{width: 4.4090rem;height: 4.3181rem;flex: 0 0 auto;}
			.prolist3{flex: 1;margin-left: 3.7%;position: relative;}
			.prolist4{width:10.3181rem;height:1.9090rem;font-size:0.6818rem;font-family:PingFangSC-Regular,PingFang SC;font-weight:400;color:rgba(51,51,51,1);line-height:0.954545rem;margin-bottom: 0.3636rem;}
			.prolist5{font-size: 0.5454rem;color:rgba(155,155,155,1);}
			.prolist6{position: absolute;bottom: 0;left: 0;display: flex;justify-content: space-between;width: 100%;align-items: flex-end;}
			.prolist7{font-size: 0.9090rem;font-weight:500;color:rgba(255,59,48,1);}
			.prolist8{font-size: 0.5454rem;color:rgba(155,155,155,1);}
			.prolist8 i{font-size: 0.5454rem;color: #FF8400 ;}
			ovfloew_one{overflow: hidden;white-space: nowrap;text-overflow:ellipsis;}
			/* 一行 */
			.ovfloew_two{overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;}
			.ovfloew_three{overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 3;}
			.ovfloew_four{overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 4;}
			.selected{color: #FF5B7B;}
		</style>
	</head>
	<body>
		<header id="header" class="fixed bgf">
		    <div class="header-wrap">
<!--		        <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col9b fz-40"></b></a></div>-->
                <!-- <div class="header-l"><a href="javascript:history.go(-1)"><i class="back"></i></a></div> -->
		        <div class="header-title posr">
		            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">今日推广商品</h1>
		        </div>
		    </div>
		</header>
		<div class="extension_box">
			<!-- 头部搜索 固定定位-->
			<div class="extension_top">
				<div class="extension_sec">
					<input type="" name="orderkey" id="" value="" placeholder="请输入商品名称进行搜索" class="extension_sec1" />
					<img src="../../images/icon_search@2x.png" class="extension_sec2">
				</div>
				<div class="extension_type">
					<div class="extension_type1 comprehensive selected">综合排序</div>
					<div class="extension_type1 new">最新</div>
					<div class="extension_type1 hot">最热</div>
				</div>
			</div>
			<!-- 列表 -->
			<div class="prolist">

				
			</div>
		</div>
		<script type="text/javascript" src="../../js/zepto.min.js"></script>
		<script type="text/javascript" src="../../js/template.js"></script>
		<script type="text/javascript" src="../../js/common.js"></script>
		<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
		<script type="text/javascript">
			var section = getQueryString('section');
			var status = 'comprehensive';
			var orderkey = '';
			$(".comprehensive").addClass("selected");
	        $(".comprehensive").click(function(){
		        $(".comprehensive").addClass("selected");
		        $(".new").removeClass("selected");
		        $(".hot").removeClass("selected");
		        status='comprehensive';
		        get_list();
		    });
		    if (section == 0) {
				$("#z-tab-order").html("累计推广商品");
        	}
		    $(".new").click(function(){
		        $(".comprehensive").removeClass("selected");
		        $(".new").addClass("selected");
		        $(".hot").removeClass("selected");
		        status='new';
		        get_list();
		    });
		    $(".hot").click(function(){
		        $(".comprehensive").removeClass("selected");
		        $(".new").removeClass("selected");
		        $(".hot").addClass("selected");
		        status='hot';
		        get_list();
		    });
		    get_list();
			function get_list(){
	        	$.ajax({
		            type: "post",
		            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Distribution&met=distributionGoods&typ=json",
		            data: {k: getCookie('key'),u: getCookie('id'),status:status,orderkey:orderkey,section:section},
		            dataType: "json",
		            success: function (r) {
		 				var e = template.render("order-goods-list", r);
	            		$(".prolist").html(e);
		            }
		        });
	        }
	        $('.extension_sec2').click(function(){
		  		orderkey=$.trim($('input[name="orderkey"]').val());
		  		status='comprehensive';
		    	get_list();
		    })
		</script>
		<script type="text/html" id="order-goods-list">
			<% var goods_list = data; %>
			<%if(goods_list.length >0){%>
				<%for(j=0;j < goods_list.length;j++){%>
					<% var list = goods_list[j]%>
					<div class="prolist1">
						<img src="<%=list.goods_image%>" class="prolist2">
						<div class="prolist3">
							<div class="prolist4 ovfloew_two"><%=list.goods_name%></div>
							<div class="prolist5">订单数：<%=list.num%></div>
							<div class="prolist6"><span class="prolist7 fz-26">¥<%=list.goods_price%></span><span class="prolist8">预估佣金：<i>¥<%=list.total%></i></span></div>
						</div>
					</div>
				<%}%>
			<%}%>
		</script>
	</body>
</html>
<?php
include __DIR__ . '/../../includes/footer.php';
?>