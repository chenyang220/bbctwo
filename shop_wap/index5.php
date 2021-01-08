<?php
include __DIR__ . '/includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit:cover">
    <title><?php echo __('首页'); ?></title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/index-new.css?v=881">
    <link rel="stylesheet" type="text/css" href="css/swiper.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/iconfont.css"/>
    <script src="js/jquery.js"></script>
    <script type="text/javascript" src="js/swiper.min.4.4.1.js"></script>
</head>
<script type="text/javascript" src="js/ucsdk.min.js"></script>
<script type="text/javascript" src="js/cookie.js"></script>
<script type="text/javascript">
    if (getCookie("token") == "undefined" || getCookie("token") == null   ||  "<?=$_GET['token']?>" != '') {
        setCookie("token","<?=$_GET['token']?>");
    }

    if (getCookie("enterId")  == "undefined" || getCookie("enterId") == null   ||  "<?=$_GET['enterId']?>" != '') {
       setCookie("enterId","<?=$_GET['enterId']?>"); 
    }

    if (getCookie("access_token")  == "undefined" || getCookie("access_token") == null   ||  "<?=$_GET['access_token']?>" != '') {
       setCookie("access_token","<?=$access_token?>"); 
    }
    //用app电话号码登录
    var u_id = '<?php echo $u_id;?>';
    if (getCookie("u_id")  == "undefined" || getCookie("u_id") == null || (u_id > 0 && getCookie("u_id") != u_id)) {
       setCookie("u_id","<?=$u_id?>"); 
    }

 alert("<?=$_GET['token']?>");
    alert(u_id);
    if (u_id) {
       window.location.href = UCenterApiUrl + '/?ctl=Login&met=oauth&typ=e&u_id=' + u_id + "&token=" + getCookie("token") + "&enterId=" + getCookie("enterId") + "&type=wap";
    }
</script>
<body class="fz-0">

<!-- 搜索 -->
<div class="head-fixed">
    <div class="head-ser">
        <div class="cohesive " id="cohesive_dev"><a href="./tmpl/changecity.html" class="colf"><span class="city-text sub_site_name_span"><?php echo __('全部'); ?></span>
                <i class="icon-drapdown"></i></a></div>
        <a href="tmpl/search.html" class="index-header-inps header-inps <?php if ($_COOKIE['is_app_guest']) { ?> isApp  <?php } ?>"> <b
                    class="iconfont icon-search"></b><span class="search-input" id="keyword"><?php echo __('请输入关键词'); ?></span>
        </a> <?php if ($_COOKIE['is_app_guest']) { ?> <a class="qrcode_open scan ml-20 iblock tc" href="/qrcode_open"><i class="iconfont icon-scan colf"></i><span class="fz-20 colf ml20 block">扫一扫</span></a>

        <?php } ?> <a id="header-nav" class="message colf" href="tmpl/message.html"><i></i><b id="is_look"></b></a>

    </div>
     <div class="clearfix index-head-top-nav">
           <ul class="flex-layout category-head" id="category-head"></ul>
      </div>
      <div class="fen"><a href="<?php echo $WapSiteUrl;?>tmpl/product_first_categroy.html">

        <i></i></a></div>
</div>
<div id="serch_down" class="" style="display: none">

</div>
<p class="index-head-height"></p>
<div class="bgf bgf_bannr">
	
    <div id="main-container1" class="fz0"></div>
</div>
<div class="nctouch-home-layout" id="main-container2"></div>
<div class="guess-like guess-like-module" id="favourite"></div>


<!-- 底部 -->
<?php include __DIR__ . '/includes/footer_menu.php'; ?>
<script type="text/html" id="category-one">
     <li class="flex"><a>首页</a></li>
     <% for(var i = 0;i<items.length;i++){ %>
    <li class="flex"><a href="<%= WapSiteUrl %>/tmpl/product_list.html?cat_id=<%= items[i].cat_id %>"><%= items[i].cat_name %></a></li>
    <% } %>
</script>
<script type="text/html" id="slider_list">
    <div class="relative wp100">
       
        <div class="swiper-container swiper-container-index">
            <div class="swiper-wrapper">
                <% for (var i in item) { %>
                <div class="swiper-slide">
                    <a href="<%= item[i].url %>"> <img src="<%= item[i].image %>" class="main-img"> </a>
                </div>
                <% } %>
            </div>
        </div>
        <% if(item.length>=2){ %>
        <div class="swiper-pagination swiper-paginations" id="pagination"></div>
        <%}%>
    </div>
</script>
<script type="text/html" id="home1">
    <div class="bgf index-model">
        <div class="ad ">
            <a href="<%= url %>" class="tc"><img src="<%= image %>" alt=""><% if (title) { %>
<!--                <div class="class-tit"><span><%= title %></span></div>-->
                <% } %> </a>
        </div>
    </div>
</script>
<script type="text/html" id="home2">
    <div class="">
        <div class="module1">
            <% if (title) { %>
            <div class="common-tit tc">
                <h4 class="fz-32"><%= title %></h4>
            </div>
            <% } %>
            <!-- 布局一（1/3） -->
            <div class="layout1 layout-1 clearfix">
                <div class="big fl"><a href="<%= square_url %>"><img src="<%= square_image %>" alt=""></a></div>
                <div class="small clearfix fr">
                    <a href="<%= rectangle1_url %>" class="mrb22 fr"><img src="<%= rectangle1_image %>" alt=""></a><a
                            href="<%= rectangle2_url %>" class="fr"><img src="<%= rectangle2_image %>" alt=""></a>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="home3">
    <div>
        <% if (title) { %>
        <div class="common-tit tc">
            <h4 class='fz-32'><%= title %></h4>
        </div>
        <% } %>
        <div class="layout2 pl-20 pr-20">
            <ul class="clearfix fz0">
                <% for (var i in item) { %>
                <li><a href="<%= item[i].url %>"><img src="<%= item[i].image %>" alt=""></a></li>
                <% } %>
            </ul>
        </div>
    </div>
</script>
<script type="text/html" id="home4">
    <div>
        <% if (title) { %>
        <div class="common-tit tc">
            <h4 class="fz-32"><%= title %></h4>
        </div>
        <% } %>
        <div class="layout1 layout-2 clearfix">
            <div class="small fl clearfix">
                <a href="<%= rectangle1_url %>" class="mrb22 fl"><img src="<%= rectangle1_image %>" alt=""></a><a
                        href="<%= rectangle2_url %>" class="fl"><img src="<%= rectangle2_image %>" alt=""></a>
            </div>
            <div class="big fr"><a href="<%= square_url %>"><img src="<%= square_image %>" alt=""></a></div>
        </div>
    </div>
</script>
<script type="text/html" id="goods">
    <div class="mr-recommended">
        <% if (title) { %>
        <h3 class="title-bg"><p><%= title %></p></h3>
        <% } %>
        <ul class="clearfix pl-20 pr-20 ">
            <% for (var i in item) { %>
            <li>
                <a href="tmpl/product_detail.html?goods_id=<%= item[i].goods_id %>">
                    <em class="img-box">
                        <img src="<%= item[i].goods_image %>"/>
                    </em>
                    <p class="more-overflow"><%= item[i].goods_name %></p>
					<div class="clearfix pr-10">
						<i class="fl">￥<%= item[i].goods_promotion_price %> </i>
						<i class="colbc fl through pl-0 pr-0">￥<%= item[i].common_market_price %></i>
						<i class="col-made fr">马上抢</i>
					</div>
                    
                </a>
            </li>
            <% } %>
        </ul>
    </div>
</script>
<script type="text/html" id="enterance">
    <ul class="classify clearfix">
        <% for (var i in item) { %>
        <li class="jf">
            <span><a href="<%= item[i].url %>"><img src="<%= item[i].icons %>"/></a></span>
            <p><%= item[i].navName %></p>
        </li>
        <% } %>
    </ul>
</script>
<script type="text/html" id="activityA">
    <% if(item.type == 'discount' && item.content_info) { var content_info = item.content_info %>
    <% if(content_info.length > 0) {  %>
    <div class="xs-discount index-model">
        <div class="title-discount">
            <div>
                <p></p><i class="iconfont icon-shijian"></i><%= item.title %>
                <p></p>
            </div>
            <span><a class="index-more align-middle" href="tmpl/discount_list.html">更多</a><i class="iconfont icon-btnrightarrow"></i></span>
        </div>

        <div class="discount-category">
            <div class="swiper-container swiper-container2">
                <div class="swiper-wrapper">
                    <% for (var i in content_info) { %>
                    <div class="swiper-slide">
                        <a href="tmpl/product_detail.html?goods_id=<%= content_info[i].goods_id %>">
                            <div class="img-box"><img class="wp100" src="<%= content_info[i].goods_image %>" alt="">
                            </div>
                            <h4 class="one-overflow"><%= content_info[i].goods_name %></h4>
                            <p class="clearfix pri">
                                <span>￥<%= content_info[i].discount_price %></span>
                                <span>￥<%= content_info[i].goods_price %></span>
                            </p>
                        </a>
                        <div class="time">
                            <i class="iconfont icon-shijian"></i>
                            <div class="time-item iblock fnTimeCountDown"
                                 data-end="<%= content_info[i].goods_end_time %>">
                                <!--  <p id="minute_show" class="day">00</p>天 --><p id="minute_show" class="hour">00</p>
                                时<p id="second_show" class="mini">00</p>分<p id="second_show" class="sec">00</p>秒
                            </div>
                        </div>
                    </div>
                    <% } %>
                </div>
            </div>
        </div>
    </div>
    <% } %>
    <% } %>
    <!--拼团活动 加长-->
    <% if(item.type == 'pintuan' && item.content_info) {  var content_info = item.content_info %>
    <% if(content_info.length > 0) {  %>
    <div class="pt-discount index-model">
        <div class="title-discount clearfix">
            <div class="fl fz0">
                <i class="iconfont icon-pintu"></i>
                <h3><%= item.title %></h3>
            </div>
            <span class="fr"><a class="index-more align-middle" href="tmpl/pintuan_index.html">更多</a><i class="iconfont icon-btnrightarrow"></i></span>
        </div>
        <div class="swiper-container swiper-container3">
            <div class="swiper-wrapper">
                <% for (var i in content_info) { %>
                <div class="swiper-slide clearfix">
                    <a href="tmpl/pintuan_detail.html?goods_id=<%= content_info[i].goods_id %>&pt_detail_id=<%= content_info[i].detail_id %>">
                        <div class="fl">
                            <h4 class="one-overflow"><%= content_info[i].goods_name %></h4>
                            <!--                        <p><%= content_info[i].goods_name %></p>-->
                            <span><%= content_info[i].person_num %>人团</span><span>￥<%= content_info[i].price %></span>
                            <div>单买价：<%= content_info[i].price_one %><em>已拼1件</em></div>
                            <em class="btn-pintuan">去拼团</em>
                        </div>
                        <img class="fr" src="<%= content_info[i].goods_image %>"/></a>
                </div>
                <% } %>
            </div>
        </div>
    </div>
    <% } %>
    <% } %>

    <!--团购风暴加长-->
    <% if(item.type == 'groupbuy' && item.content_info) {  var content_info = item.content_info %>
    <% if(content_info.length > 0) {  %>
    <div class="tg-discount index-model">
        <div class="swiper-container swiper-container3">
            <div class="swiper-wrapper">
                <% for (var i in content_info) { %>

                <div class="swiper-slide clearfix">
                    <div class="title-discount clearfix">
                        <div class="fl"><%= item.title %></div>
                        <div class="fr">
                            <a class="mt0" href="tmpl/group_buy_index.html"><i class="iconfont icon-btnrightarrow col3 align-top"></i></a>
                        </div>
                        <div class="fr">
                            <div class="time">
                                时间仅剩
                            </div>
                            <div class="time-item fl fnTimeCountDown"
                                 data-end="<%= content_info[i].groupbuy_endtime %>">
                                <p id="minute_show" class="day">00</p> :
                                <p id="minute_show" class="hour">00</p> :
                                <p id="second_show" class="mini">00</p> :
                                <p id="second_show" class="sec">00</p>
                            </div>
                        </div>
                    </div>
                    <a class="block" href="tmpl/product_detail.html?goods_id=<%= content_info[i].goods_id %>">
                        <div class="swiper-text flex">
                            <em class="img-box" style="background:url(<%= content_info[i].groupbuy_image_rec %>) no-repeat center;background-size:cover;">
                            </em>

                            <div class="flex1 tg-discount-texts">
                                <h4 class="one-overflow"><%= content_info[i].goods_name %></h4>
                                <!--                        <p><%= content_info[i].goods_name %></p>-->
                                <span>￥<%= content_info[i].groupbuy_price %></span><span class="through colbc">￥<%= content_info[i].goods_price %></span>
                                <div>已团<%= content_info[i].groupbuy_buyer_count %>件</div>
                                <em>立即去团</em>
                            </div>
                        </div>
                    </a>
                </div>
                <% } %>
            </div>
        </div>
    </div>
    <% } %>
    <% } %>
	
	<% if(item.type == 'presale' && item.content_info){ var content_info = item.content_info; %>
        
            
            <div class="xs-discount index-model">
                <div class="title-discount">
                    <div>
                        <p></p><i class="iconfont icon-shijian"></i>预售活动
                        <p></p>
                    </div>
                    <span><a class="index-more align-middle" href="">更多</a><i class="iconfont icon-btnrightarrow"></i></span>
                </div>
            
                <div class="discount-category">
                    <div class="swiper-container swiper-container2">
                        
                        <div class="swiper-wrapper">
                            <% var k = 0; for (var i in content_info) { k++; if(k <= 2){ %>
                            <div class="swiper-slide">
                                <a href="tmpl/product_detail.html?goods_id=<%= content_info[i].goods_id %>">
                                    <div class="img-box"><img class="wp100" src="<%= content_info[i].goods_image %>" alt="">
                                    </div>
                                    <h4 class="one-overflow presale-tit"><b>预售</b><%= content_info[i].goods_name %></h4>
                                    <p class="clearfix presale-pri">
                                        <span class="presale-pri1">预售价：￥<%= content_info[i].presale_price %></span>
                                        <span class="presale-pri2">原价：￥<%= content_info[i].goods_price %></span>
                                    </p>
                                </a>
                            </div>
                            <% } } %>
                        </div>
                        
                    </div>
                </div>
            </div>
        

        <% } %>       
</script>
<script type="text/html" id="activityB">
    <ul class="clearfix spell-group index-model">
        <%
        if(item[0]) {
        var content_info_left = item[0];
        }
        if(item[1]) {
        var content_info_right = item[1];
        }
        %>
        <% if(content_info_left) { %>
        <% if(content_info_left.type == 'discount' ){ var content_info = content_info_left.content_info; %>
        <li>
            <a href="tmpl/discount_list.html?discount_goods_id=<%= content_info_left.content %>">
                <h3><%= content_info_left.title %></h3>
                <ul class="clearfix pb20">
                    <% var k = 0; for (var i in content_info) { k++; if(k <= 3){ %>
                    <li>
                        <em class="img-box discount-half">
                            <img src="<%= content_info[i].goods_image %>"/>
                        </em>
                        <p class="pt-20 pb-10">￥<%= content_info[i].discount_price %></p>
                        <span>￥<%= content_info[i].goods_price %></span>
                    </li>
                    <% } } %>
                </ul>
            </a>
        </li>

        <% } %>

        <% if(content_info_left.type == 'seckill' ){ var content_info = content_info_left.content_info; %>
        <li>
            <a href="tmpl/seckill_lists.html?seckill_goods_id=<%= content_info_left.content %>">
                <h3><%= content_info_left.title %></h3>
                <ul class="clearfix pb20">
                    <% var k = 0; for (var i in content_info) { k++; if(k <= 3){ %>
                    <li>
                        <em class="img-box discount-half">
                            <img src="<%= content_info[i].goods_image %>"/>
                        </em>
                        <p class="pt-20 pb-10">￥<%= content_info[i].seckill_price %></p>
                        <span>￥<%= content_info[i].goods_price %></span>
                    </li>
                    <% } } %>
                </ul>
            </a>
        </li>

        <% } %>    

        <% if(content_info_left.type == 'pintuan' ){ var content_info = content_info_left.content_info; %>

        <li>
            <a href="tmpl/pintuan_index.html?pintuan_id=<%= content_info_left.content %>">
                <h3><%= content_info_left.title %></h3>
                <div class="clearfix">
                    <% var k = 0; for (var i in content_info) { k++; if(k <= 2){ %>
                    <em class="img-box pintuan-half">
                        <img class="cter" src="<%= content_info[i].goods_image %>"/>
                    </em>
                    <% } } %>
                </div>
            </a>
        </li>

        <% } %>
        <% if(content_info_left.type == 'groupbuy' ){ var content_info = content_info_left.content_info;%>

        <li>
            <a href="tmpl/group_buy_index.html?group_buy_id=<%= content_info_left.content %>">
                <h3><%= content_info_left.title %></h3>
                <% var k = 0; for (var i in content_info) { k++; if(k <= 2){ %>
                <em class="img-box groupbuy-half">
                    <img class="cter" src="<%= content_info[i].groupbuy_image_rec %>"/>
                </em>
                <% } } %>
            </a>
        </li>

        <% } %>
        <% if(content_info_left.type == 'voucher' ){ var content_info = content_info_left.content_info;%>

        <li>
            <a href="tmpl/voucher_list.html">
                <h3><%= content_info_left.title %></h3></a>
            <a href="tmpl/voucher_list.html"> <em class="img-box"><img class="vouchers" src="<%= content_info %>"/></em></a>

        </li>

        <% } %>
        <% if(content_info_left.type == 'redpacket' ){ var content_info = content_info_left.content_info;%>

        <li>
            <a href="tmpl/redpacket_plat.html">
                <h3><%= content_info_left.title %></h3></a>
            <a href="tmpl/redpacket_plat.html"> <em class="img-box"><img class="vouchers2"
                                                                         src="<%= content_info %>"/></em></a>
        </li>

        <% } %>
        <% } %>

        <% if(content_info_right) { %>
        <% if(content_info_right.type == 'discount' ){ var content_info = content_info_right.content_info; %>

        <li><a href="tmpl/discount_list.html?discount_goods_id=<%= content_info_right.content %>">
                <h3><i class="iconfont icon-shijian"></i><%= content_info_right.title %></h3>
                <ul class="clearfix">
                    <% var k = 0; for (var i in content_info) { k++; if(k <= 3){ %>
                    <li>
                        <em class="img-box">
                            <img src="<%= content_info[i].goods_image %>"/></em>
                        <p class="pt10 pb10">￥<%= content_info[i].discount_price %></p>
                        <span>￥<%= content_info[i].goods_price %></span>
                    </li>
                    <% } } %>
                </ul>
            </a>
        </li>

        <% } %>


        <% if(content_info_right.type == 'seckill' ){ var content_info = content_info_right.content_info; %>

        <li><a href="tmpl/seckill_lists.html?seckill_goods_id=<%= content_info_right.content %>">
                <h3><i class="iconfont icon-shijian"></i><%= content_info_right.title %></h3>
                <ul class="clearfix">
                    <% var k = 0; for (var i in content_info) { k++; if(k <= 3){ %>
                    <li>
                        <em class="img-box">
                            <img src="<%= content_info[i].goods_image %>"/></em>
                        <p class="pt10 pb10">￥<%= content_info[i].seckill_price %></p>
                        <span>￥<%= content_info[i].goods_price %></span>
                    </li>
                    <% } } %>
                </ul>
            </a>
        </li>

        <% } %> 

        <% if(content_info_left.type == 'presale' ){ var content_info = content_info_left.content_info; %>
        
            
            <div class="xs-discount index-model">
                <div class="title-discount">
                    <div>
                        <p></p><i class="iconfont icon-shijian"></i>预售活动
                        <p></p>
                    </div>
                    <span><a class="index-more align-middle" href="">更多</a><i class="iconfont icon-btnrightarrow"></i></span>
                </div>
            
                <div class="discount-category">
                    <div class="swiper-container swiper-container2">
                        <% var k = 0; for (var i in content_info) { k++; if(k <= 3){ %>
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <a href="tmpl/product_detail.html?goods_id=<%= content_info[i].goods_id %>">
                                    <div class="img-box"><img class="wp100" src="<%= content_info[i].goods_image %>" alt="">
                                    </div>
                                    <h4 class="one-overflow presale-tit"><b>预售</b><%content_info[i].goods_name %></h4>
                                    <p class="clearfix presale-pri">
                                        <span class="presale-pri1">预售价：￥<%= content_info[i].presale_price %></span>
                                        <span class="presale-pri2">原价：￥<%= content_info[i].goods_price %></span>
                                    </p>
                                </a>
                            </div>
                        </div>
                        <% } } %>
                    </div>
                </div>
            </div>
        

        <% } %>       

        <% if(content_info_right.type == 'pintuan' ){ var content_info = content_info_right.content_info; %>

        <li><a href="tmpl/pintuan_index.html?pintuan_id=<%= content_info_right.content %>">
                <h3><%= content_info_right.title %></h3>
                <div class="clearfix">
                    <% var k = 0; for (var i in content_info) { k++; if(k <= 2){ %>
                    <em class="img-box pintuan-half">
                        <img class="cter" src="<%= content_info[i].goods_image %>"/></em>
                    <% } } %>
                </div>
            </a>
        </li>

        <% } %>
        <% if(content_info_right.type == 'groupbuy' ){ var content_info = content_info_right.content_info;%>

        <li>
            <a href="tmpl/group_buy_index.html?group_buy_id==<%= content_info_left.content %>">
                <h3><%= content_info_right.title %></h3>
                <ul class="clearfix pb20">
                    <% var k = 0; for (var i in content_info) { k++; if(k <= 3){ %>
                    <li>
                        <em class="img-box groupbuy-half">
                            <img src="<%= content_info[i].groupbuy_image_rec %>"/>
                        </em>
                        <p class="pt-20 pb-10">￥<%= content_info[i].groupbuy_price %></p>
                        <span>￥<%= content_info[i].goods_price %></span>
                    </li>
                    <% } } %>
                </ul>
            </a>
        </li>

        <% } %>
        <% if(content_info_right.type == 'voucher' ){ var content_info = content_info_right.content_info;%>

        <li><a href="tmpl/voucher_list.html">
                <h3><%= content_info_right.title %></h3></a>
            <a href="tmpl/voucher_list.html"> <em class="img-box"><img class="vouchers" src="<%= content_info %>"/></em></a>
        </li>

        <% } %>
        <% if(content_info_right.type == 'redpacket' ){ var content_info = content_info_right.content_info;%>

        <li><a href="tmpl/redpacket_plat.html">
                <h3><%= content_info_right.title %></h3></a>
            <a href="tmpl/redpacket_plat.html"> <em class="img-box"><img class="vouchers2"
                                                                         src="<%= content_info %>"/></em></a>
        </li>

        <% } %>
        <% } %>

    </ul>
</script>
<script type="text/html" id="goodsB">
    <div class="guess-like guess-goodsb index-model">
        <% if (title) { %>
        <h3 class="title-bg"><p><%= title %></p></h3>
        <% } %>
        <ul class="clearfix mb-10">
            <% for (var i in item) { %>
            <li><a href="tmpl/product_detail.html?goods_id=<%= item[i].goods_id %>">
                    <em class=img-box>
                        <img src="<%= item[i].goods_image %>"/>
                    </em>
                    <p><%= item[i].goods_name %></p>
                    <span>￥<%= item[i].goods_promotion_price %></span></a>
            </li>
            </a> <% } %>
        </ul>
    </div>
</script>
<script type="text/html" id="advA">
    <div class="shopping fz0 index-model">
        <% if (title) { %>
        <h3 class="title-bg"><p><%= title %></p></h3>
        <% } %>
        <div class="clearfix">
            <div class="fl"><a href="<%= square_data %>"><img src="<%= square_image %>" alt=""></a></div>
            <div class="fr">
                <div><a href="<%= rectangle1_data %>"><img src="<%= rectangle1_image %>" alt=""></a></div>
                <div class="mt6"><a href="<%= rectangle2_data %>"><img src="<%= rectangle2_image %>" alt=""></a></div>
            </div>
        </div>
        <ul class="clearfix">
            <li><a href="<%= rectangle3_data %>"><img src="<%= rectangle3_image %>" alt=""></a></li>
            <li><a href="<%= rectangle4_data %>"><img src="<%= rectangle4_image %>" alt=""></a></li>
            <li><a href="<%= rectangle5_data %>"><img src="<%= rectangle5_image %>" alt=""></a></li>
            <li><a href="<%= rectangle6_data %>"><img src="<%= rectangle6_image %>" alt=""></a></li>
        </ul>
    </div>
</script>

<script type="text/html" id="advB">
    <div class="rq-recommended index-model">
        <% if (title) { %>
        <h3 class="title-bg"><p><%= title %></p></h3>
        <% } %>
        <a class="block" href="<%= rectangle1_data %>"><img src="<%= rectangle1_image %>"/></a>
        <ul class="top clearfix mt-10">
            <li>
                <div><a href="<%= rectangle2_data %>"><img src="<%= rectangle2_image %>" alt=""></a></div>
            </li>
            <li class="fr">
                <div><a href="<%= rectangle3_data %>"><img src="<%= rectangle3_image %>" alt=""></a></div>
            </li>
        </ul>
        <ul class="bottom clearfix mt-10">
            <li>
                <div><a href="<%= rectangle4_data %>"><img src="<%= rectangle4_image %>" alt=""></a></div>
            </li>
            <li class="fr">
                <div><a href="<%= rectangle5_data %>"><img src="<%= rectangle5_image %>" alt=""></a></div>
            </li>
        </ul>
    </div>
</script>
<script type="text/html" id="userFavourite">
    <h3 class="title-bg"><p>猜你喜欢</p></h3>
	
<a style="font-size:16px;width:20px;height:10px;z-index:999999;" href="ceshi.html?token=a0ftbdstu7aekih0&enterId=0">ceshi</a>
    <ul class="clearfix pl-20 pr-20 ">
        <% for (var i in favourite_goods) { %>

        <li><a href="tmpl/product_detail.html?goods_id=<%= favourite_goods[i].goods_id %>">
                <em class=img-box>
                    <img src="<%= favourite_goods[i].goods_image %>"/></em>
                <p><%= favourite_goods[i].goods_name %></p>
                <span>￥<%= favourite_goods[i].goods_price %></span>
            </a>
        </li>

        <% } %>
    </ul>
</script>
<!-- 6.18新增 -->
<script type="text/html" id="goodsC">
    <% if (title) { %>
    <h3 class="title-bg" style="
background: url(../../img/bg4.png) no-repeat;
    background-size: 100%;
" ><p style="  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-image: linear-gradient(to right, #e83f51, #fba001 60%);"><%= title %></p></h3>
    <% } %>
    <ul class="index-goods-list-items">
        <% for (var i in item) { %>
        <li>
            <a href="tmpl/product_detail.html?goods_id=<%=item[i].goods_id %>" class="img-box"><img class="wp100" src="<%= item[i].goods_image %>"/></a>
            <div class="index-goods-list-cont">
                <h4 class="more-overflow"><a href=""><%= item[i].goods_name %></a></h4>
                <div class="clearfix"><strong><i class="fz-24">￥</i><%= item[i].goods_promotion_price %></strong><b>￥<%= item[i].common_market_price %></b><a class="fr btn-goods-get" href="tmpl/product_detail.html?goods_id=<%=item[i].goods_id %>">立即抢</a></div>
            </div>
        </li>
        <% } %>
    </ul>
</script>
<script type="text/javascript" src="js/zepto.min.js"></script>
<script type="text/javascript" src="js/zepto.min.js"></script>
<script type="text/javascript" src="js/template.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<?php include 'includes/translatejs.php'; ?>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript" src="js/tmpl/footer.js"></script>
<script type="text/javascript" src="js/addtohomescreen.js"></script>
<script type="text/javascript" src="js/jquery.timeCountDown.js"></script>
<script>

      var urlstr = location.href;
      var urlstatus=false;
      $("#category-head a").each(function () {
        if ((urlstr + '/').indexOf($(this).attr('href')) > -1&&$(this).attr('href')!='') {
          $(this).addClass('cur'); urlstatus = true;
        } else {
          $(this).removeClass('cur');
        }
      });
      if (!urlstatus) {$("#category-head a").eq(0).addClass('cur'); };

    $.getJSON(ApiUrl + "/index.php?ctl=Goods_Cat&met=cat&typ=json",{cat_is_index:"1"},function (t)
    {
        var r = t.data;
        r.WapSiteUrl = WapSiteUrl;
        var a = template.render("category-one", r);
        $("#category-head").html(a);

    });
    addToHomescreen({
        message: "<?= __('如要把应用程式加至主屏幕,请点击%icon, 然后'); ?><strong><?= __('加至主屏幕'); ?></strong>"
    });
    $(".logbtn").click(function () {
        if (getCookie("id")) {
            $(".logbtn").attr("href", "tmpl/member/signin.html");
        }
    });

    function initialize() {
        // 百度地图API功能
        var geolocation = new BMap.Geolocation();
        var geoc = new BMap.Geocoder();
        geolocation.getCurrentPosition(function (r) {
            if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                var mk = new BMap.Marker(r.point);
                window.coordinate = {"lng": r.point.lng, lat: r.point.lat};
                geoc.getLocation(r.point, function (rs) {
                    var addComp = rs.addressComponents;
                    if (addComp.province != null && addComp.province != "undefined" && addComp.province != "") {
                        //获取分站信息
                        window.addressStr = addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber;
                        $.post(ApiUrl + "/index.php?ctl=Base_District&met=getLocalSubsiteWap&typ=json&ua=wap", {
                            province: addComp.province,
                            city: addComp.city,
                            district: addComp.district,
                            street: addComp.street
                        }, function (result) {
                            if (result.status == 200) {
                                addCookie("sub_site_id", result.data.subsite_id, 0);
                            } else {
                                addCookie("sub_site_id", 0, 0);
                            }
                            /*window.location.reload();*/
                        }, "json");
                    }
                });
            } else {
                alert("failed" + this.getStatus());
            }
        }, {enableHighAccuracy: true});
    }

    function loadScriptSubsite() {
        var script = document.createElement("script");
        script.src = "//api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9&callback=initialize";//此为v2.0版本的引用方式
        document.body.appendChild(script);
    }

    function go_message() {
        if (!getCookie("key")) {
            window.location.href = ShopWapUrl + "/tmpl/member/login.html";
        } else {
            window.location.href = ImApiUrl;
        }
    }
</script>
</body>
<script>
    function A() {
        if ($("#main-container1").children().length == 0) {
            $(".js-nav-class").css("margin-top", "2rem");
        }
    }

    $(function () {
        setTimeout("A()", 1000);
    });

</script>
<script>
    window.onload = function () {

    }

</script>

<script>

</script>

</html>
<?php
include __DIR__ . '/includes/footer.php';
?>
