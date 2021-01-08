<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0,minimum-scale=1, user-scalable=no"/>
    <title>发布资讯</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/wapMessageList.css">
    <!--<link rel="stylesheet" href="css/swiper.min.css"/>-->
    <!--    <script type="text/javascript" src="../js/jquery.js"></script>-->
    <!--<script src="js/swiper.min.js"></script>-->

</head>
<body>
<!--资讯详情-->
<div class="MeParticularsSh">
    <header id="" class="fixed">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:history.go(-1)"> <i class="back detailsBack"></i> </a> -->
            </div>
            <div class="header-title">
                <h1>资讯详情</h1>
            </div>
        </div>
    </header>
    <div class=" article-contentBox" id="news">
    
    </div>


</div>
<script type="text/html" id="news-tmp">
    <h2 class="title-info"><%=newsdetails['title']%></h2>
    
    <p class="subheadInfo"><%=newsdetails['title']%></p>
    
    <div class="authorBox">
        <P><%=newsdetails['authorname']%></P>
        <p><span><%=newsdetails['number']%></span>条阅读</p>
        <% if(newsdetails['author_type']==2){%> <a class="entranceBn" href="store.html?shop_id=<%=newsdetails['shop_id']%>"><i></i>进入店铺</a> <%}%>
    </div>

        <%=newsdetails['content']%>

    <div class="bannerBox">
        <p><%=newsdetails['content']%></p>
        <div class="BmOther">
            <p><%=newsdetails['create_time']%></p>
            <button class="complainBn" order_id="<%=newsdetails['id']%>">投诉</button>
        </div>
    </div>

</script>

<script type="text/javascript" src="../js/zepto.min.js"></script>
<script type="text/javascript" src="../js/template.js"></script>

<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/zepto.waypoints.js"></script>
<script type="text/javascript" src="../js/tmpl/order_payment_common.js"></script>

<script type="text/javascript" src="../js/information_details.js"></script>

</body>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>

