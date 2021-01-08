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
	<title>店铺介绍</title>
	<link rel="stylesheet" type="text/css" href="../../css/base.css">
	<link rel="stylesheet" href="../../css/iconfont.css">
		<style>
		     .brand{
				 margin-top: 2rem;
				  background:rgba(255,255,255,1);
				   
				   
			 }
			.brand_one{
				padding: 0.5rem;
			}
			.brand_one span{
				font-size:0.72727rem;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(74,74,74,1);
				line-height: 2.125rem;
			}
			.brand_one img{
				width: 55px;
				height: 55px;

			}
			.brand_two img{
				display: block;
				margin: auto;
				margin-top: 2rem;
			}
			.brand_ser{
				text-align: center;
				font-size:16px;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(155,155,155,1);
				margin-top: 1.5rem;
				padding-bottom:5rem;
			}
			.text{
				display: block;
				height: 5.13636rem;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(155,155,155,1);
				line-height:0.9727rem;
			}
			.su{
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(74,74,74,1);
			}
		</style>
	</head>
	<body>
		<header id="header" class="fixed bgf">
		    <div class="header-wrap">
		        <!-- <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col9b fz-40"></b></a></div> -->
		        <div class="header-title posr">
		            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">店铺介绍</h1>
		        </div>
		    </div>
		</header>
		<div class="brand">
			
			
		</div>
	</body>
</html>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<script type="text/javascript">
	var distribution_shop_id = getQueryString("sid");
	$.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getDistributionShopDetail&typ=json",
            data: {k: getCookie('key'),u: getCookie('id'),distribution_shop_id:distribution_shop_id},
            dataType: "json",
            success: function (r) {
            	if(r.status==200){
            		var e = template.render("distributed-introduction", r.data);
                    $(".brand").html(e);
            	}   
            }
        });
</script>
<script type="text/html" id="distributed-introduction">
	<div class="brand_one borb1 pad30">
		<img src="<%=distribution_logo%>">
		<span><%=distribution_name%></span>
	</div>
	<div class="brand_two borb1">
		<span class="pad30 fz-28 pb-20 su">店铺介绍：</span>
		<span class="text pad30 fz4 pb-20"><%=distribution_desc%></span>
		
	</div>
	<div class="brand_two borb1 flex-lr">
		<span class="pad30 fz-28 pb-20 su">联系方式</span>
		<span class="pad30 fz-28 pb-20 su"><%=distribution_phone%></span>
	</div>
</script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>