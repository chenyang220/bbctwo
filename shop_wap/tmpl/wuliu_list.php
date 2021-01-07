<?php 
include __DIR__.'/../includes/header.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>店铺列表</title>
    <link rel="stylesheet" href="../css/base.css" />
    <link rel="stylesheet" href="../css/index.css" />
    <link rel="stylesheet" href="../css/Group.css" />
    <link rel="stylesheet" href="../css/swiper.min.css" />
    <link rel="stylesheet" href="../css/nctouch_products_list.css" />
    <link rel="stylesheet" href="../css/nctouch_common.css" />
    <!-- <link rel="stylesheet" href="../css/iconfont.css"> -->
    <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_pwu4kym4n3o9a4i.css">
    <script type="text/javascript" src="../js/swipe.js"></script>

<style>
    #search-btn{
        position: absolute;
        right: 2rem;
        top: 0;
        font-size: 0.7rem;
        color: #666;
        display: inline-block;
        line-height: 1.8rem;
    }
    .header-r a i.more{
        background-size: 40%;
    }
    .nctouch-norecord .norecord-ico{
        margin: 10rem 0 0 0;
    }
    
    .goods_wuliu::-webkit-scrollbar {
        width: 0px;
        height: 0px;
    }
</style>

 
</head>
<body>
	<header id="header" class="nctouch-product-header fixed">
	    <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:history.go(-1)"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1>查看物流</h1>
            </div>
        </div>
	    <div class="nctouch-nav-layout">
	        <div class="nctouch-nav-menu"> <span class="arrow"></span>
	            <ul>
	                <li><a href="../index.html"><i class="home"></i>首页</a></li>
	                <li><a href="../tmpl/cart_list.html"><i class="cart"></i>购物车<sup style="display: inline;"></sup></a></li>
	                <li><a href="../tmpl/member/member.html"><i class="member"></i>我的商城</a></li>
	                <li><a href="javascript:void(0);"><i class="message"></i>消息<sup></sup></a></li>
	            </ul>
	        </div>
	    </div>
	</header>
	<div class="store-lists-area" style="margin-top: 2rem;"></div>
</body>
<script type="text/html" id="store-lists-area">
    <% if(data.length > 1){ %>
        <span style="font-size: 17px;margin-left: 25%;">-------已被拆成<%=data.length%>个包裹-------</span>
    <% }%>
    <ul>
        <% if(data.length > 0){ %>
        <% for( var i in data ){ %>
        <li class="store-list clearfix" data-order_id ="<%=data[i].order_id%>" data-express_name="<%=data[i].express_name%>" data-shiping_code ="<%=data[i].shiping_code%>" data-shiping_express ="<%=data[i].shiping_express%>">
            <div class="store-item-name">
                <div class="store-info clearfix">
                    <div class="store-info-o">
                        <p>
                            <a class="m-r-5 one-overflow wp60" style="max-width: 165px;height: 20px;font-size: 12px;font-weight: 600;color: #333;line-height: 20px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;" href="javascript:void(0);">物流单号：
                                <%= data[i].shiping_code%>
                            </a>
                            <a href="javascript:;" data-nc-im="" data-im-seller-id="6" data-im-common-id="0"><i class="im_common offline"></i></a>

                        </p>
                    </div>
                </div>
            </div>

            <div class="store-item-goods item-goods fl">
                <ul class="goods_wuliu clearfix pl-20 pr-20 pb-30" style="    max-height: 124px;white-space: nowrap;overflow: auto;width: 100vw;word-wrap: break-word;white-space: nowrap;">
                    <% var goods_data = data[i].image %>
                    <% for( var j in goods_data ){ %>
                    <li style="display: inline-block;width: 5rem;position: relative;margin-right: 0.568rem;">
                        <a href="javascript:void(0);">
                            <div class="goods-pic"><img style="width: 100%;height: 5rem;" src="<%=goods_data[j]%>" alt=""></div>
                        </a>
                    </li>
                    <% } %>
                </ul>
            </div>
        </li>
        <% }}else { %>
        <div class="nctouch-norecord search">
            <div class="norecord-ico"><i></i></div>
            <dl>
                <dt>没有找到任何相关信息</dt>
            </dl>
        </div>
        <%
        }
        %>

    </ul>
</script>

<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/tmpl/wuliu_list.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
</html>
<?php 
include __DIR__.'/../includes/footer.php';
?>
<script type="text/javascript">
    $(document).on("click",".store-list",function(){
        var order_id = $(this).attr("data-order_id");
        var shiping_code = $(this).attr("data-shiping_code");
        var shiping_express = $(this).attr("data-shiping_express");
        var express_name = $(this).attr("data-express_name");
        window.location.href='./member/order_delivery.html?order_id='+order_id+'&shipping_code='+shiping_code+'&express_id='+shiping_express+'&express_name='+express_name;
    })
</script>