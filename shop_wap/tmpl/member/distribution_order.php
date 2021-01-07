<?php 
include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title>今日推广订单</title>

<!--公共集合样式-->
<link rel="stylesheet" type="text/css" href="../../css/base.css">
<link rel="stylesheet" href="../../css/iconfont.css">
<link rel="stylesheet" href="../../css/style.css" />
 <style>
        .container{
            width:100%;
			margin-top: 2.11rem;
			/* background: rgba(255,255,255,1); */
        }
		.container div{
			 background: rgba(255,255,255,1);
		}
		.top{
			border:1px solid rgba(241,241,241,1);
		}
		.to-u{
			padding-top: 0.45rem;
			padding-bottom: 0.45rem;
		}
        #a{
            width:100%;
			margin: auto;
            display: flex;
        }
        #a div{
			padding-top: 0.363636rem;
			padding-bottom: 0.363636rem;
			font-size:0.636363rem;
			font-family:PingFangSC-Regular,PingFang SC;
			font-weight:400;
			color:rgba(74,74,74,1);
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
          
        }
        .content{
            width:100%;
            background: rgba(255,255,255,1);
            display: none;
			margin-top: 0.45rem;
        }
        .container .active{
            display: block;
        }
		.search{
			margin-top: 0.363636rem;
			margin-bottom: 0.363636rem;
			margin: auto;
			width:12.409090rem;
			height:1.5rem;
			background:rgba(241,241,241,1) !important;
			border-radius:0.81818181rem;
		}
		.search input{
			padding-left: 0.678rem;
			    border: none;
			    width: 80%;
			    height: 1.5rem;
			    background: rgba(241,241,241,1);
			    border-radius: 0.81818181rem;
		}
		.search img{
			margin-top: 0.2rem;
			width: 1.1rem;
			height: 1.1rem;
		}
		.coumn{
			padding-left: 0.5454rem;
			padding-right: 0.5454rem;
			padding-top: 0.409rem;
			
		}
		.su_com{
			padding-bottom: 0.579rem;
		}
		.of{
			font-size:0.545454rem;
			font-weight:400;
			color:rgba(155,155,155,1);
		}
		.red{
			color: #FF001D;
		}
		.yellow{
			color: #F5A623;
		}
		.of_imge{
			display: flex;
			justify-content: flex-start;
			border:1px solid rgba(241,241,241,1);
			padding-left: 1.1363636rem !important;
			
		}
		.imge{
			width: 4.2272rem;
			height: 3.3636rem;
		}
		.imge img{
			width: 100%;
			height: 100%;
		}
		
		.written{
			width: 70%;
			padding-left: 0.4545rem;
		}
    </style>
</head>
<body>
	<header id="header" class="fixed bgf">
	    <div class="header-wrap">
            <!-- <div class="header-l"><a href="javascript:history.go(-1)"><i class="back"></i></a></div> -->
	        <div class="header-title posr">
	            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">今日推广订单</h1>
	        </div>
	    </div>
	</header>
	<div class="container">
		<div class="to-u">
		<div class="search">
			<input placeholder="请输入订单号进行搜索" name="orderkey" class="orderkey">
			<img src="../../images/sousuo.png" class="search_list">
		</div>
		</div>
		<div class="top">
	    <div id="a">
	        <div class="all">全部</div>
	        <div class="wait">待付款</div>
			<div class="already">已付款</div>
			<div class="finish">已完成</div>
			<div class="cancel">已取消</div>	      
	    </div>
		</div>
	    <div class="content1 active">
			
		</div>
		<div class="content2 active">

		</div>
		   
	</div>
	<script type="text/javascript" src="../../js/zepto.min.js"></script>
	<script type="text/javascript" src="../../js/template.js"></script>
	<script type="text/javascript" src="../../js/common.js"></script>
	<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    <script>
        var a = document.getElementById('a');
        var section = getQueryString('section');
        var divObj = a.children
        for(var i = 0; i < divObj.length; i++){
            divObj[0].style.color = 'red'
            divObj[i].setAttribute('index',i) //设置自定义属性
            divObj[i].onclick = function(){
                for(var j = 0; j < divObj.length; j++){
                    divObj[j].style.color = 'rgba(74,74,74,1)'
                }
                this.style.color = "red"
            }
        }
        var orderkey = '';
        var status='all';
        get_list();
        $('.all').click(function(){
        	status='all';
        	get_list();
        });
        $('.wait').click(function(){
        	status='wait';
        	get_list();
        });
        $('.already').click(function(){
        	status='already';
        	get_list();
        });
        $('.finish').click(function(){
        	status='finish';
        	get_list();
        });
        $('.cancel').click(function(){
        	status='cancel';
        	get_list();
        });
        if (section == 2) {
			$("#z-tab-order").html("今日预估收益");
        } else if (section == 0) {
			$("#z-tab-order").html("累计推广订单");
        }
        function get_list(){
        	$.ajax({
	            type: "post",
	            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Distribution&met=directsellerOrder&typ=json",
	            data: {k: getCookie('key'),u: getCookie('id'),status:status,orderkey:orderkey,section:section},
	            dataType: "json",
	            success: function (r) {
	 				var e = template.render("order-direct-list", r.data.direct);
            		$(".content1").html(e);
            		var a = template.render("order-indirect-list", r.data.indirect);
            		$(".content2").html(a);  
	            }
	        });
        }
        $('.search_list').click(function(){
        	orderkey=$('.orderkey').val();
        	status='all';
        	get_list();
        })
    </script>
    <script type="text/html" id="order-direct-list">
    	<% var order = items; %>
		<%if(order.length >0){%>
			<%for(j=0;j < order.length;j++){%>
				<% var list = order[j];var order_goods = list.goods_list;%>
				<div class="count_main">
				<div class="of coumn su_com">
					<span>订单号：</span>
					<span><%=list.order_id%></span>
				</div>
				<%for(a=0;a < order_goods.length;a++){%>
					<% var goods = order_goods[a];%>
					<div class="of_imge coumn su_com">
						<div class="imge">
							<img src="<%=goods.goods_image%>" />
						</div>
						<div class="written">
							<span class="block tit fz-28 col3"><%=goods.goods_name%></span>
							<div class="clearfix mt-10 mb-10">
								<span class="fl price-color fz-28">￥<%=goods.order_goods_payment_amount%></span>
								<% if(goods.goods_return_status > 0) { %>
								    <span class="col-red fr fz-28"><%=goods.goods_return_status_con%></span>
								<% } else if (goods.goods_refund_status > 0) { %>
								    <span class="col-red fr fz-28"><%=goods.goods_refund_status_con%></span>
								<% } else { %>
								  <span class="col-red fr fz-28"><%=list.order_state_con%></span>
								<% } %>
							</div>
							<div class="of clearfix">
								<span class="fz-24">来源：<%=list.buyer_user_name%></span>
								<div class="fr fz-24">
									<span>佣金：</span><span class="yellow">￥<%=goods.directseller_commission_0%></span>
								</div>
							</div>
						</div>	
					</div>
				<%}%>
				<div class="of coumn">
					<span>下单时间：</span>
					<span><%=list.order_create_time%></span>
				</div>
				<div class="of cops coumn su_com pt-10 clearfix">
					<div class="of_money fl">
						<span>付款金额：</span>
						<span class="col-red">￥<%=list.order_payment_amount%></span>
						
					</div>
					<div class="of_money fr">
						<span>预估佣金：</span>
						<span class="yellow">￥<%=list.directseller_commission_0%></span>
					</div>
				</div>
			</div>
			<%}%>
		<%}%>
    </script>
    <script type="text/html" id="order-indirect-list">
    	<% var order = items; %>
		<%if(order.length >0){%>
			<%for(j=0;j < order.length;j++){%>
				<% var list = order[j];var order_goods = list.goods_list;%>
				<div class="count_main">
				<div class="of coumn su_com">
					<span>订单号：</span>
					<span><%=list.order_id%></span>
				</div>
				<%for(a=0;a < order_goods.length;a++){%>
					<% var goods = order_goods[a];%>
					<div class="of_imge coumn su_com">
						<div class="imge">
							<img src="<%=goods.goods_image%>" />
						</div>
						<div class="written">
							<span class="block tit fz-28 col3"><%=goods.goods_name%></span>
							<div class="clearfix mt-10 mb-10">
								<span class="fl price-color fz-28">￥<%=goods.order_goods_payment_amount%></span>
								<% if(goods.goods_return_status > 0) { %>
								    <span class="col-red fr fz-28"><%=goods.goods_return_status_con%></span>
								<% } else if (goods.goods_refund_status > 0) { %>
								    <span class="col-red fr fz-28"><%=goods.goods_refund_status_con%></span>
								<% } else { %>
								  <span class="col-red fr fz-28"><%=list.order_state_con%></span>
								<% } %>
							</div>
							<div class="of clearfix">
								<span class="fz-24">来源：<%=list.buyer_user_name%></span>
								<div class="fr fz-24">
									<span>佣金：</span><span class="yellow">￥<%=goods.directseller_commission_1%></span>
								</div>
							</div>
						</div>	
					</div>
				<%}%>
				<div class="of coumn">
					<span>下单时间：</span>
					<span><%=list.order_create_time%></span>
				</div>
				<div class="of cops coumn su_com pt-10 clearfix">
					<div class="of_money fl">
						<span>付款金额：</span>
						<span class="col-red">￥<%=list.order_payment_amount%></span>
						
					</div>
					<div class="of_money fr">
						<span>预估佣金：</span>
						<span class="yellow">￥<%=list.directseller_commission_1%></span>
					</div>
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