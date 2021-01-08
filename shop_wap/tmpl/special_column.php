<?php
include __DIR__ . '/../includes/header.php';
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
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/index-new.css">
    <link rel="stylesheet" type="text/css" href="../css/swiper.min.css"/>
    <link rel="stylesheet" type="text/css" href="../css/iconfont.css"/>
	<link rel="stylesheet" type="text/css" href="../css/nctouch_store.css">
    <script src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/swiper.min.4.4.1.js"></script>
</head>


<body>
<!-- 搜索 -->
<div class="bgf">
    <div id="main-container2" class="fz0"></div>
</div>
<div id="ss"></div>

<script type="text/javascript" src="../js/zepto.min.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
<script type="text/html" id="spcial_list">
    <div class="relative wp100">
        <div class="swiper-container swiper-container-index">
            <div class="swiper-wrapper">
                <% for (var i in special_column_image) { %>
                <div class="swiper-slide">
                    <a href="<%=special_column_image[i].img_url%>"> <img src="<%=special_column_image[i].img_path%>" class="main-img"> </a>
                </div>
                <% } %>
            </div>
        </div>
</script>
<script type="text/html" id="spcial_goods_list">
    <!-- 展示页 -->
    <% if (special_type == 1) { %>
            <% for (var i in set_image) { %>
            <div class="order-status fle plr30 tc ">
                    <% for (var q in set_image[i].column_set_image) { %>
                        <% if (set_image[i].column_set_type == 1) { %>
                          <a class="relativeone" href="<%=set_image[i].column_set_image[q].set_url %>">
                              <img src="<%=set_image[i].column_set_image[q].set_path %>"></img>
                          </a>
                        <% } else { %>
                          <a class="relative" href="<%=set_image[i].column_set_image[q].set_url %>">
                                <img class="wp100" src="<%=set_image[i].column_set_image[q].set_path %>"></img>
                          </a>
                        <% } %> 
                    <% } %> 
            </div>
            <% } %> 
    <%}%>
    <!-- 商品列表    --> 
    <div>
        <div class="nctouch-store-goods-list mt-30"> 
			<ul>
            <% for (var i in goods_common) { %>
                <li class="goods-item">
					<a href="./product_detail.html?goods_id=<%= goods_common[i].goods_id %>&pos=2&pos_page=product_list">
						<em class="goods-item-pic"><img src="<%= goods_common[i].goods_image %>"></img></em>
						<text class="iblock goods-item-name one-overflow box-size"><%= goods_common[i].goods_name %></text>
						<text class="goods-item-price">￥<%= goods_common[i].goods_price %></text>
					</a>
               </li>  
            <% } %> 
			</ul>
        </div>
    </div> 
</script>
</body>
<script>
  $.ajax({
      type: "get", 
      url: ApiUrl + "/index.php?ctl=Special_Column&met=index&typ=json&ua=wap", 
      data: {}, 
      dataType: "json", 
      success: function (e)
      {
         if (e.status == 200) {
              var r = template.render("spcial_list", e.data);
              $("#main-container2").html(r);
              var r = template.render("spcial_goods_list", e.data);
              $("#ss").html(r);
               var bannerSwipers= new Swiper(".swiper-container-index", {
                  autoplay:true,
                  autoplay:{
                      delay:2000,
                       disableOnInteraction:false, //解决滑动后不能轮播的问题
                   }, 
                  paginationClickable: true

              });

            
         } else {

         }  
      }  
  });
  $(function() {
    $(window).scroll(function(){
      var index_banner_hg=$(".swiper-container-index").height();

      if($(document).scrollTop()>index_banner_hg){
          $(".head-fixed").addClass("scroll-bg");
      }else{
          $(".head-fixed").removeClass("scroll-bg");
      }
  })
});
</script>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>

