<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="../css/base.css">
	<link rel="stylesheet" href="../css/heart.css">
    <link rel="stylesheet" href="../css/nctouch_common.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_tgl5z4j705l.css">
</head>
<body>
	<header id="header" class="posf">
        <div class="header-wrap">
             <div class="header-l">
                <!-- <a href="javascript:window.history.back();"> <i class="back"></i> </a> -->
            </div>
            <div class="tit"></div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <div class="discover-goods-box">
            <ul class="discover-goods-items mb-120 pl-20 pr-20">

            </ul>
        </div>
    </div>

    <!-- 商品列表 -->
    <script id="goods-list-template" type="text/template">
        <% if(data){ %>
            <% for(i=0;i<data.length;i++){ var item = data[i]; %>
            <li>
                <a href="./product_detail.html?cid=<%=item.common_id%>">
                    <em class="img-box"><img src="<%=item.common_image%>" alt="goods"></em>
                    <div>
                        <h4 class="more-overflow"><%=item.common_name%></h4>
                        <p class="discover-goods-rel2"><strong>￥<%=item.common_price%></strong><em class="through">￥<%=item.common_market_price%> </em></p>
                    </div>
                </a>
            </li>
            <% } %>
        <% } %>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/animation.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/animation.js"></script>
    <script type="text/javascript" src="../js/iscroll.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/explore_goods_list.js"></script>
</body>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>