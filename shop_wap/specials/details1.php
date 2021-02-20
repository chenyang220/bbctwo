<?php
include   '../includes/header.php';
?>
<!DOCTYPE html>
    <html>
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
        <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit:cover;">
        <meta name="sharecontent" data-msg-img="https://ss0.baidu.com/6ONWsjip0QIZ8tyhnq/it/u=2927678406,1546747626&fm=58"/>
        <title><?= __('商品详情'); ?></title>
        <link rel="stylesheet" type="text/css" href="../css/base.css?v=811">
		<link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
		<link rel="stylesheet" type="text/css" href="../css/nctouch_products_detail.css?v=311">
        <link rel="stylesheet" href="../css/iconfont.css">
        <link rel="stylesheet" href="../css/swiper.min.css">
        <link rel="stylesheet" href="../css/new-style.css">
        <link rel="stylesheet" href="../css/ve_poster.css?v=88">
        <link rel="stylesheet" href="//at.alicdn.com/t/font_562768_qwz6qicku8.css">
		<link rel="stylesheet" href="../css/customize.css">
    </head>
    <style>
        .s-dialog-btn-cancel {
            border-left-width: 0px !important;
        }
    </style>
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script>
    <script type="text/javascript">
        var url = '../product_detail/product_detail?goods_id='+<?php echo $_GET['goods_id']?>; 
        wx.miniProgram.redirectTo({url:url})
    </script>
    <script type="text/javascript" src="../js/NativeShare.js"></script>
    <script type="text/javascript" src="../js/soshm.min.js"></script>
    <body>
	
	<ul class="header-nav customize-detail-head-nav">
		<li class="cur"><a href="javascript:void(0);"><?= __('商品'); ?></a></li>
		<li><a href="javascript:void(0);"><?= __('评价'); ?></a></li>
		<li><a href="javascript:void(0);"><?= __('详情'); ?></a></li>
		<li><a href="javascript:void(0);"><?= __('推荐'); ?></a></li>
	</ul>
    <div class="contentA customize-detail-main">
        <div id="product_detail_html" class="posr zIndex1"></div>
    </div>
    <div class="contentC">
		<div class="special-goods-des">
			<p class="special-goods-des-tit tc"><span>详情</span></p>
		</div>
        <div class="nctouch-main-layout mt0" id="fixed-tab-pannel">
            <div class="fixed-tab-pannel mb-20"></div>
        </div>
    </div>
    <div class="contentD">
        <div id="recommendation">
        </div>
    </div>
    <div style="height:60px;">
        
    </div>
    <div id="product_detail_spec_html" class="nctouch-bottom-mask down"></div>
    <!-- <?= __('新增促销'); ?> 2017.7.17 -->
    <div class="nctouch-bottom-mask down" id="sale-activity-html"></div>
    <!-- <?= __('代金券'); ?> -->
    <div id="voucher_html" class="nctouch-bottom-mask down"></div>
    <!-- 海报样式 -->
    <div class="clearfix" id="poster_pic">
        <!-- 顶部 -->
    </div>
     <div class="mask_div">
        <!-- 遮罩层 -->
        <div class="vemask" style=""></div>
        <!-- 显示截图位置 -->
        <div class="clearfix" id="proPic1">
            <!-- 点击关闭按钮 -->
            <img class="close_poster"  src="../images/new/icon-wrong.png">
            <img src="" id="proPic" />
            <input type="hidden" name="showPic" class="showPic">
            <div class="down_poster">长按图片可以保存到手机</div>
        </div>
        <div class="main_b">
            <div class="box" >
                <img src="../../img/wei.png" class="share_app">
                <a class="" style="font-size:10px;">发送好友</a>
            </div>
            <div class="box"  >
                <img src="../../img/xia.png" class="aaaa" id="bao_app">
                <a  style="font-size:10px;">保存本地</a>
            </div>
        </div>
    </div>
    <script src="../js/swiper.min.js"></script>

    <script type="text/javascript" src="../js/html2canvas.js"></script>
    <script type="text/html" id="draw_report">
		<% if(poster_head) {%>
        <img class="wp100" src="<%=poster_head%>" alt="">
        <% } %>
        <div class="goods_paper">
            <div class="clearfix goods_div">
                <!-- 头像 -->
                <div class="poster_head">
                    <p class="poster_head_p">
                        <img class="poster_head_img" src="<%=head_img%>">
                    </p>
                </div>
                <p class="poster_title">
                    <span class="poster_title_name"><%=user_name%></span>
                    <span class="poster_title_desc">推荐给你一个宝贝</span>
                </p>
            </div>
        </div>
        <!-- 商品图片 -->
        <div class="poster_goods_img">
            <img class="wp100" id="goods_img_bill" src="<%=goods_img%>">
        </div>
        <!-- 商品详情 -->
        <div  class="clearfix poster_goods_bottom">
            <!-- 商品详情 -->
            <div class="poster_goods_detail">
                <p>
                    <span class="poster_goods_detail_spana">￥<%=goods_price%></span> 
                    <span class="poster_goods_detail_spanb">￥<%=goods_price_more%></span>
                </p>
                <!-- <p class="goods_pro_name"><%=goods_pro_name%></p> -->
				<p class="goods_pro_name more-overflow"><%=goods_name%></p>
            </div>
            <!-- 二维码 -->
            <div class="poster_qrcode">
                <div class="poster_qrcode_img">
                    <em>
                    <img class="wp100 hp100" src="<%=er_code%>">
                    </em>
                    <p class="saoyisao">扫一扫立即购买</p>
                </div>
            </div>
        </div>
    </script>
    <script type="text/html" id="productRecommendation">
        <div class="goods-detail-recom grid">
            <h4 class="tc"><?= __('店铺推荐'); ?></h4>
            <ul class="new-goods bg-f5">
                <% if(goods_commend_list){ %>
                    <% for (var i = 0; i<goods_commend_list.length ;i++){ %>
                    <li>
                        <a href="product_detail.html?goods_id=<%=goods_commend_list[i].goods_id%>">
                            <div class="overhide">
                                <div class="table">
                                    <span class="img-area">
                                        <img src="<%=goods_commend_list[i].common_image%>">
                                    </span>
                                </div>
                            </div>
                            <h5 class="fz-28 col2 more-overflow pt-20"><%=goods_commend_list[i].common_name%></h5>
                            <b class="fz-30 pl-10 pr-10 mb-20 mt-20"><?= __('￥'); ?><%=goods_commend_list[i].common_price%></b>
                        </a>
                        
                        
                    </li>
                    <% } %>
                <% } %>
            </ul>
        </div>
    </script>
    <script type="text/html" id="product_detail">
        <div class="goods-detail-top">
            <div class="goods-detail-pic swiper-container custom-product-det-swiper" id="mySwipe">
                <ul class="swiper-wrapper">
                    <% if(goods_info['common_video'] != ""){ %>
                    <li class="swiper-slide">
                        <video id="common_video" poster="<%= goods_info['common_image']%>" src="<%=goods_info['common_video']%>" controls="controls"></video>
                    </li>
                    <% } %>
                    <% for (var i =0;i < goods_image.length; i++){ %>
                    <li class="swiper-slide"><img src="<%=goods_image[i]%>"/></li>
                    <% } %>
                </ul>
                <div class="swiper-pagination swiper-pagination-banner">
                </div>
            </div>
        </div>
        <!-- <?= __('点击'); ?>banner<?= __('放大查看图片或视频'); ?> -->
        <div class="banner-enlarge">
            <span class="btn-close fz-24 col9"><?= __('关闭'); ?></span>
            <div class="swiper-pagination swiper-pagination-enlarge"></div>
            <div class="swiper-container banner-enlarge-swiper">

                <ul class="swiper-wrapper">
                     <% if(goods_info['common_video'] != ""){ %>
                    <li class="swiper-slide">
                        <video id="common_video2" src="<%=goods_info['common_video']%>" controls="controls"></video>
                    </li>
                    <% } %>
                    <% for (var i =0;i < goods_image.length; i++){ %>
                    <li class="swiper-slide"><img src="<%=goods_image[i]%>"/></li>
                    <% } %>
                </ul>

            </div>
        </div>
        <div class="goods-detail-cnt">
			
            <% if(goods_info.promotion_type == 'groupbuy'){ %>
            <!--<?= __('团购样式'); ?> start-->
           <div class="goods-detail-price sale-pri">
			   <h5 class="sale-tip"><span><?= __('团购'); ?></span></h5>
			<div class="flex1 goods-detail-pri-content">
				<div class="iblock align-top">
					<div>
						<span class="w40 pri"><b><?= __('￥'); ?></b><em><%=goods_info.promotion_price%></em></span>
						<% if (goods_info.distributor_open==1 && goods_info.common_is_directseller==1) { %>
						<span class="top-zhuan">
							<b>$</b>
							<% if (goods_info.distributor_type==1) { %>
								<em>赚<%=goods_info.common_a_first%></em>
							
							<% }else{ %>
								<em>赚<%=goods_info.common_c_first%></em>
							<% } %>
						</span>	
						<% } %>
							
						
					</div>
					<p><?= __('商品原价：'); ?><?= __('￥'); ?><%=goods_info.goods_price%></p>
				</div>
				<div class="iblock align-top tc fr">
					<% if(goods_info.promotion_is_start == 0){ %> <span class="sold"><?= __('距离开始'); ?></span>
					<div class="time fnTimeCountDown" data-end="<%=goods_info.groupbuy_starttime%>">
						<% }else{ %> <span class="sold"><?= __('距离结束'); ?></span>
						<div class="time fnTimeCountDown" data-end="<%=goods_info.groupbuy_endtime%>">
							<% } %> <span>
										<span class="day">00</span><strong><?= __('天'); ?></strong>
										<span class="hour">00</span><strong><?= __('小时'); ?></strong>
										<span class="mini">00</span><strong><?= __('分'); ?></strong>
										<span class="sec">00</span><strong><?= __('秒'); ?></strong>
									</span>
						</div>
					</div>
				</div>
		   </div>
            <!--<?= __('团购样式'); ?> end-->
			<% }else if(goods_info.promotion_type == 'xianshi' ){ %>
            <!--<?= __('限时折扣'); ?> start-->
            <div class="goods-detail-price sale-pri">
				<h5 class="sale-tip"><span><?= __('限时 折扣'); ?></span></h5>
				<div class="flex1 goods-detail-pri-content">
					<div class="iblock align-top">
						<div>
							<span class="w40 pri"><b><?= __('￥'); ?></b><em><%=goods_info.promotion_price%></em></span>
							<% if (goods_info.distributor_open==1 && goods_info.common_is_directseller==1) { %>
							<span class="top-zhuan">
								<b>$</b>
								<% if (goods_info.distributor_type==1) { %>
									<em>赚<%=goods_info.common_a_first%></em>
								
								<% }else{ %>
									<em>赚<%=goods_info.common_c_first%></em>
								<% } %>
							</span>	
							<% } %>
						</div>
						<p><?= __('商品原价：'); ?><?= __('￥'); ?><%=goods_info.goods_price%></p>
					</div>
					<div class="iblock align-top tc fr">
						<% if(goods_info.promotion_is_start == 0){ %>
						<span class="sold"><?= __('距离开始'); ?></span>
						<div class="time fnTimeCountDown" data-end="<%=goods_info.groupbuy_starttime%>">
						<% }else{ %>
						<span class="sold"><?= __('距离结束'); ?></span>
						<div class="time fnTimeCountDown" data-end="<%=goods_info.groupbuy_endtime%>">
						<% } %> 
							<span>
								<span class="day">00</span><strong><?= __('天'); ?></strong>
								<span class="hour">00</span><strong><?= __('小时'); ?></strong>
								<span class="mini">00</span><strong><?= __('分'); ?></strong>
								<span class="sec">00</span><strong><?= __('秒'); ?></strong>
							</span>
							</div>
						</div>
					</div>
				</div>
			</div>
            <!--限时折扣-->
            <% }else if(goods_info.promotion_type == 'seckill' ){ %>
            <!--<?= __('秒杀'); ?> start-->
            <div class="goods-detail-price sale-pri">
				<h5 class="sale-tip"><span><?= __('秒杀'); ?></span></h5>
				<div class="flex1 goods-detail-pri-content">
					<div class="iblock align-top">
						<div><span class="w40 pri colf"><?= __('￥'); ?><em><%=goods_info.promotion_price%></em></span></div>
						<p><?= __('商品原价：'); ?><?= __('￥'); ?><%=goods_info.common_market_price%></p>
					</div>
					<div class="iblock align-top tc fr">
					<% if(goods_info.promotion_is_start == 0){ %> <span class="sold"><?= __('距离开始'); ?></span>
					<div class="time fnTimeCountDown" data-end="<%=goods_info.groupbuy_starttime%>">
						<% }else{ %> <span class="sold"><?= __('距离结束'); ?></span>
						<div class="time fnTimeCountDown" data-end="<%=goods_info.groupbuy_endtime%>">
							<% } %> <span>
										<span class="day">00</span><strong><?= __('天'); ?></strong>
										<span class="hour">00</span><strong><?= __('小时'); ?></strong>
										<span class="mini">00</span><strong><?= __('分'); ?></strong>
										<span class="sec">00</span><strong><?= __('秒'); ?></strong>
									</span>
						</div>
					</div>
				</div>
			</div>
            <!--秒杀--><% }else if(goods_info.promotion_type == 'presale'&&goods_info.promotion_is_start == 1){%>
                <!-- 预售begin -->
                <div class="presale-module fz0">
                        <div class="presale-top clearfix">
                                <div class="fl">
                                        <em class="block">预售价</em>
                                        <p><b>￥</b><strong><%=goods_info.presale_price%></strong><i class="ml-10">原价:￥<%=goods_info.goods_price%></i></p>
                                </div>
                                <div class="fr">
                                        <span id="bill" class="hide btn-build-poster cccc"><a class="fz-28 col-red">生成海报</a></span>
                                </div>
                        </div>
                        <div class="presale-center bgf">
                                <div class="goods-detail-pre-name">
                                        <span><%=goods_info.goods_name%></span>
                                </div>
                               <p class="presale-p"><em class="pre-saled">销量：<%=goods_info.common_salenum%>件</em><b class="presale-logs">限购<%=goods_info.lower_limit%>件</b></p>
                                <p class="presale-p">
                                        <span>支付尾款时间：</span>
                                        <time><%=goods_info.presale_final_time%>-<%=goods_info.presale_final_time_end%></time>
                                </p>
                                <p class="presale-p presale-flow"><span class="presale-flow-tit">流程</span><em>1.付定金-2.付尾款-3.发货</em></p>
                        </div>
                </div>
                
                <!-- 预售end -->

            <%}else{ %> <% if (goods_info.promotion_type&&goods_info.promotion_type!='presale' ) { %>
            <div class="goods-detail-price iblock">
              
                <dl class="clearfix">
                    <dt class="fz-30 col-red fl"><?= __('￥'); ?><em><%=goods_info.promotion_price%></em>
                    </dt>
                    <dd class="fl"><em class="through"><?= __('￥'); ?><%=goods_info.goods_price%></em></dd>
                    <dd class="fl"><span class="sold"><?= __('销量：'); ?><%=goods_info.common_salenum%><?= __('件'); ?></span></dd>
                </dl>


            </div>
            <% } %>
            <% } %>
            <% if(goods_info.promotion_type!='presale'){%>
				
					<div class="relative custom-det-module wp100">
						<div class="special-goods-top">
							<% if (!goods_info.promotion_type || goods_info.promotion_is_start != 1) { %>
							<div class="clearfix fz-0">
		<!-- 						<p class="customize-detail-tip">优惠促销</p> -->
								<div class="goods-detail-price iblock">
									<dl class="clearfix fz-0">
									   <dt class="price-color fl"><b class="rmb"><?= __('￥'); ?></b><em class="money"><%=goods_info.goods_price%></em>
									   <% if (!goods_info.plus_status) { %>
									   <em class="origin-pri"><?= __('价格:￥'); ?><b class="through"><%= goods_info.goods_market_price %></b></em>
									   <%}%>
										<!-- 佣金 -->
													
										<% if (goods_info.distributor_open==1 && goods_info.common_is_directseller==1) { %>
											<% if (goods_info.distributor_type==1) { %>
												<span class="b-zhuan"><i>$</i><em>赚<%=goods_info.common_a_first%></em></span>
											
											<% }else{ %>
												<span class="b-zhuan"><i>$</i><em>赚<%=goods_info.common_c_first%></em></span>
										   
											<% } %>
											
										<% } %>
										
										</dt>
									   <!--plus -->
										<% if (goods_info.plus_status) { %>
										 <dd><em class="plus-pri">￥<%=goods_info.plus_price%></em><b class="plus-logo"></b></dd>
										<%}%>
								   </dl>
								</div>
								<% if(!goods_info.promotion_type){ %>
									<div class="bill-poster-link fr normal-poster" id="bill">
									   <a class="fz-28" href="javascript:;">生成海报</a>
									</div>
								<% }%>
							</div>
							<% } %>
							<p class="label-p">
								<% if(goods_info.label_name_arr){ %>
									<% for(label_id in goods_info.label_name_arr){ %>
										<label class="label-item"><%=goods_info.label_name_arr[label_id]%></label>
									<% }%>
								<% }%>
							</p>
							<div class="goods-detail-name bg-ff clearfix pl-0 pt-0 pb-0">
								<dl>
									<dt class="fz-28 z-dhwz">
										<i class="customize-goods-attr">新品</i>
										<%if(goods_info.common_is_virtual == '1'){%>
										<span><?= __('虚拟'); ?></span>
										<%}%>
										<%if(goods_info.common_is_directseller==1){%>
											<b class="b-log">分佣</b>
										<%}%>
										<% if (goods_info.is_presell == '1') { %><span><?= __('预售'); ?></span><% } %><% if (goods_info.is_fcode == '1') { %><span>F<?= __('码'); ?></span><% } %>
										<h4 class="iblock"><%=goods_info.goods_name%></h4>  
									</dt>
								</dl>
								<% if(goods_info.promotion_type){ %>
									<div class="bill-poster-link activity-bill fr" id="bill">
									   <a class="fz-28" href="javascript:;">生成海报</a>
									</div>
								<% }%>
								<!-- <a class="share" href=""><i></i><em>分享</em></a> -->
                                <!-- <?php if ($_COOKIE['is_app_guest']) { ?>
                                    <li>
                                        <a href="" id='shareit' class="share"> <i></i><em>分享</em><sup></sup></a>
                                    </li>
                                <?php }elseif (substr_count($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == 0){ ?>
                                    <?php if(substr_count($_SERVER['HTTP_USER_AGENT'],'UCBrowser') >= 1 || substr_count($_SERVER['HTTP_USER_AGENT'],'UCWEB') >= 1 || substr_count($_SERVER['HTTP_USER_AGENT'],'MQQBrowser') >= 1){ ?>
                                        <li>
                                            <a  class="share" id='share_wap'> <i></i><em>分享</em><sup></sup></a>
                                        </li>
                                    <?php }?>
                                <?php }?> -->
							</div>
						</div>
					</div>				
                        
					   <% if (promotion_info.jia_jia_gou || promotion_info.man_song) { %>
						   <% if (promotion_info) { %>
								   <div class="goods-detail-item  bort1 fz-0" id="for-sale">
									   <div class="itme-name tit-sale "><?= __('促销'); ?></div>
									   <div class="item-con">
											<p class="fz6 lh100 ml-20"><% if (goods_info.promotion_type == 'xianshi') { %><?= __('限时折扣'); ?><% } %> <% if(promotion_info.jia_jia_gou) { %><?= __('加价购'); ?><% } if(promotion_info.jia_jia_gou && promotion_info.man_song){%><?= __('、'); ?><%} if (promotion_info.man_song) { %><?= __('满即送'); ?><% } %></p>
										   <div class="item-more"></div>
									   </div>
								   </div>
						   <% } %>
					   <% } %>
					
            <% } %>
            <!--plus -->
            <% if (goods_info.plus_status) { %>
                <% if (!goods_info.plus_user) { %>
                <div class="plus-open-module goods-detail-item">
                    <a class="block wp100" href="member/plus_open.html">
                    <b></b><span class="lh100"><?= __('开通PLUS会员，立享特惠'); ?></span><div class="item-more"></div></a>
                </div>
                <% }%>
                <div class="goods-detail-item">
                    <div class="itme-name fz-30 col6"><?= __('促销'); ?></div>
                    <p class="iblock"><em class="plus-power-limit"><?= __('PLUS限制'); ?></em><span class="plus-power-limit-text"><?= __('PlUS价格不与套装优惠同时享受'); ?></span></p>
                </div>
             <% }%>
			 <div class="custom-det-module relative wp100">
			 	<div class="special-goods-rel">
			 		<p id="goods_spec_selected">已选 颜色：
			 			<% if (!isEmpty(goods_info.common_spec_name)) { %> <% if(goods_map_spec.length>0){%> <% for(var i =0;i
			 			<goods_map_spec.length
			 				;i++){%>
			 			<%=goods_map_spec[i].goods_spec_name%>
			 			<%for(var j = 0;j<goods_map_spec[i].goods_spec_value.length;j++){%>
			 				<%if (goods_info.goods_spec[goods_map_spec[i].goods_spec_value[j].specs_value_id]){%>
			 					<%=goods_map_spec[i].goods_spec_value[j].specs_value_name%>
			 				<%}%>
			 			<%}%>
			 			<%}%> <%}} else { %><?= __('默认'); ?> <% } %>
			 		</p>
			 		<p>
			 			<span>发货：<%=goods_info.shop_company_address%></span>
			 		</p>
			 		<% if (!isEmpty(rgl_str)) { %>
			 			<p>服务：<%= rgl_str %></p>
			 		 <% } %>
			 		<em><%=goods_info.common_salenum%>人付款</em>
			 	</div>
			 </div>
			 <!-- 新版代金券 -->
			<div class="custom-det-module" id="getVoucher">
				<a class="special-goods-li" href="">
					<span>
						<i class="zk-voucher"></i><em id='voucher_list_text'>领取代金券</em>
					</span>
					<b class="zk-right-black"></b>
				</a>
			</div>
            <div class="goods-detail-item">
                <div class="itme-name fz-30" ><?= __('送至'); ?></div>

<!--    有问题-->
                <div class="item-con wp80">
                    <a href="javascript:void(0);" id="get_area_selected" data-common_id=<%=goods_info.common_id%> data-transport_type_id=<%=goods_info.transport_type_id%> >
                        <dl class="goods-detail-freight wp100">
                            <dt><span id="get_area_selected_name" data-id=""><%=goods_hair_info.area_name%></span> <strong id="get_area_selected_whether"><%=goods_hair_info.if_store_cn%></strong>&nbsp;
                                <span id="get_area_selected_content"  style="display: none;">
                                            <%if(typeof(goods_hair_info.if_store)!='undefined' && typeof(goods_hair_info.transport_data)!='undefined' && typeof(goods_hair_info.transport_data.result)!='undefined') {%>
                                        <%= goods_hair_info.transport_data.transport_str; %>
                                        <% } %>
                                        </span>
                            </dt>
                        </dl>
                    </a>
                </div>
                <div class="item-more location"></div>
                <div class="aging-list" style="font-size: 0.681rem;line-height: 1.3rem;color: #ED5564;"></div>
            </div>
                <div id="services-det-con" class="nctouch-bottom-mask goods-seravices down">
                    <div class="nctouch-bottom-mask-bg"></div>
                    <div class="nctouch-bottom-mask-block">
                        <div class="nctouch-bottom-mask-top">
                            <h5 class="tc">服务</h5>
                            <i class="iconfont icon-close JS_close"></i>
                        </div>
                        <div class="nctouch-bottom-mask-rolling overflow-auto">
                            <strong>服务说明</strong>
                            <% if(contract_type_id.length>0){%>
                            <%for(i=0;i<contract_type_id.length;i++){%>
                            <dl>
                                <dt><%= contract_type_id[i].contract_type_name %></dt>
                                <dd><%= contract_type_id[i].contract_type_desc %></dd>
                            </dl>
                            <%}%>
                            <%}%>
                            <button class="btn-goods-services JS_close">确定</button>
                        </div>
                    </div>
                </div>
       
			<% if(contract_type_id.length>0){%>
			<ul class="goods-promises clearfix js-services-det">
					<%for(i=0;i<contract_type_id.length;i++){%>
					<li><i class="iconfont icon-yes1" ></i><em><%= contract_type_id[i].contract_type_name %></em></li>
					<%}%>
			   
			</ul>
			<%}%>
           <!-- <div  class="contentB bg-ff mt-20"><section id="s-rate" data-spm=""></section></div> -->
			<!-- 新版评价2021 -->
			<div class="custom-det-module">
				<div class="special-goods-evaluate" id="s-rate">
					
				</div>
			</div>
            <% if (store_info.shop_self_support != "true") {%>
            <div class="goods-detail-store">
                <a href="../tmpl/store<%= store_info.shop_wap_index == 1 ? '' :store_info.shop_wap_index %>.html?shop_id=<%= store_info.store_id %>">
                    <div class="store-name flex">
                    	<% if(goods_info.wap_shop_logo){ %>
                    	<!-- <i class="icon-store"></i> -->
							<img src="<%= goods_info.wap_shop_logo%>" style="width: 36px;height: 36px;">
						<% } else {%>
							<i class="icon-store"></i>
						<% }%>
						<div class="flex1">
							<p class="top"><span class="name"><%= store_info.store_name %></span>
								<!-- <b class="fans">粉丝数 <%= store_info.shop_wap_index%></b> -->
							</p>
							<p><em>综合评分</em>
							<span class="star-span">
								<%for(i=0;i< store_info.store_credit.store_desccredit.credit;i++){%>
									<i class="star"></i>
								<%}%>
							</span>
							<% if(goods_info.shop_label_name_arr){ %>
                                <% for(label_id in goods_info.shop_label_name_arr){ %>
                                    <label class="label-item"><%=goods_info.shop_label_name_arr[label_id]%></label>
                                <% }%>
                            <% }%>
							</p>
						</div>
						<button>进店逛逛</button>
					</div>
                    <div class="store-rate">
                <span class="<%= store_info.store_credit.store_desccredit.percent_class %>">
                    <b class="icon1"></b>
                    <strong><?= __('描述相符'); ?></strong>
                    <em><%= store_info.store_credit.store_desccredit.credit %></em>
                    <i><%= store_info.store_credit.store_desccredit.percent_text %></i>
                </span> <span class="<%= store_info.store_credit.store_servicecredit.percent_class %>">
                    <b class="icon2"></b>
                    <strong><?= __('服务态度'); ?></strong>
                    <em><%= store_info.store_credit.store_servicecredit.credit %></em>
                    <i><%= store_info.store_credit.store_servicecredit.percent_text %></i>
                </span> <span class="<%= store_info.store_credit.store_deliverycredit.percent_class %>">
                    <b class="icon3"></b>
                    <strong><?= __('发货速度'); ?></strong>
                    <em><%= store_info.store_credit.store_deliverycredit.credit %></em>
                    <i><%= store_info.store_credit.store_deliverycredit.percent_text %></i>
                </span>
                    </div>
                </a>
            </div>
            <% } %>
            <div class="flex special-bottom-box <% if(goods_info.promotion_type=='presale'){%> presale-bottom-btn <% }%>"   > <!-- !!!!!!!预售状态下此div添加class:presale-bottom-btn -->
                <div class="otreh-handle special-bottom-oper-module">
					<a class="operate btn-store" href="../tmpl/store<%= store_info.shop_wap_index == 1 ? '' :store_info.shop_wap_index %>.html?shop_id=<%= store_info.store_id %>">
						<i></i><span class="block">店铺</span>
					</a>
                    <!--YF_IM <?= __('联系客服'); ?> kefu START -->
                    <span onclick="openChat();"  class="operate btn-customer"><i></i><p><?= __('客服'); ?></p></span>
                    <a href="javascript:void(0);" class="operate btn-save collect pd-collect <% if (is_favorate) { %>favorate<% } %>"><i></i><p><?= __('收藏'); ?></p></a>
                </div>
                <div class="fz0 special-bottom-btn-module <%if(!goods_hair_info.if_store || goods_info.goods_storage == 0){%>no-buy<%}%>">
                    <% if (goods_info.cart == '1'&&goods_info.promotion_type!='seckill'&&goods_info.promotion_type!='presale') { %>
                        <a href="javascript:void(0);" class="btn-cart <%if(goods_hair_info.if_store){%>animation-up<%}%> add-cart"><?= __('加入购物车'); ?></a>
                    <% }else if(goods_info.promotion_type=='presale'&&goods_info.promotion_is_start==1){ %>
                        <a class="presale-btn btn1" href="javascript:void(0);"><%=goods_info.end_date%><br><%=goods_info.end_h%>结束</a>
                    <% } %>
                    
                    <% if (goods_info.seckill_info.seckill_stock == 0 &&goods_info.promotion_type=='seckill') { %>
                    <a href="javascript:void(0);" class="" style="color:#000;background-color: #a7a7a7;"><?= __('抢完啦'); ?></a>
                    <% }else if(goods_info.promotion_is_start == 0 &&goods_info.promotion_type=='seckill'){ %>
                      <a href="javascript:void(0);" class="" style="color:#000;"><?= __('即将开始'); ?></a>
                    <% } else if(goods_info.promotion_type=='presale'&&goods_info.promotion_is_start==1){%>
                        <a class="presale-btn btn2 buy-now animation-up" href="javascript:void(0);">立即付定金<br>￥<span><%=goods_info.presale_deposit%></span></a>
                    <% }else{ %>
                        <a href="javascript:void(0);" class="btn-go <%if(goods_hair_info.if_store){%>animation-up<%}%> buy-now <%if(goods_info.cart != '1'){%>wp100<%}%>"><?= __('立即购买'); ?></a>
                    <% } %>
                </div>
				
				
            </div>
    </script>
    <script type="text/html" id="product_detail_sepc">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block">
            <div class="nctouch-bottom-mask-top goods-options-info">
                <div class="goods-pic">
                    <img src="<%=goods_image[0]%>"/>
                </div>
                <dl>
                    <dt class="fz-28 col2"><%= goods_info.goods_name; %></dt>
                    <dd class="goods-price price-color fz-28">
                        <% if (goods_info.promotion_type && goods_info.promotion_is_start == 1 ) { var promo; switch (goods_info.promotion_type) { case 'groupbuy': promo = '<?= __('团购'); ?>'; break; case 'xianshi': promo = '<?= __('限时折扣'); ?>'; break; case 'seckill': promo = '<?= __('秒杀'); ?>';  case 'presale': promo = '<?= __('预售'); ?>'; break; } %> <?= __('￥'); ?><em><%=goods_info.promotion_price%></em> <span class="activity">
                        <% if (promo) { %>
                            <%= promo %>
                        <% } %>
                        </span> <% } else if(goods_info.plus_status && goods_info.plus_user){ %> <?= __('￥'); ?><em><%=goods_info.plus_price%></em><b class="plus-logo"></b><% } else{%><?= __('￥'); ?><em><%=goods_info.goods_price%></em> <% }%>
                    </dd>
                    <% if(goods_info.promotion_type=='seckill' && goods_info.promotion_is_start == 1){ %>
                    <span class="goods-storage"><?= __('库存：'); ?><%=goods_info.promotion_stock%><?= __('件'); ?></span>
                    <% } else{ %>
                    <span class="goods-storage"><?= __('库存：'); ?><%=goods_info.goods_stock%><?= __('件'); ?></span>
                    <% } %>
                </dl>
                <a href="javascript:void(0);" class="nctouch-bottom-mask-close"><i></i></a>
            </div>
            <div class="nctouch-bottom-mask-rolling go overflow-auto" id="product_roll">
                <div class="goods-options-stock bort1">
                    <% if(goods_map_spec.length>0){%> <% for(var i =0;i < goods_map_spec.length; i++){%>
                    <dl class="spec JS-goods-specs">
                        <dt spec_id="<%=goods_map_spec[i].id%>">
                            <%=goods_map_spec[i].goods_spec_name%><?= __('：'); ?>
                        </dt>
                        <dd>
                            <%for(var j = 0;j < goods_map_spec[i].goods_spec_value.length;j++){%> <a href="javascript:void(0);" <% if(goods_info.goods_spec[goods_map_spec[i].goods_spec_value[j].specs_value_id]){ %> class="current" <% } %> specs_value_id = "<%=goods_map_spec[i].goods_spec_value[j].specs_value_id%>"> <%=goods_map_spec[i].goods_spec_value[j].specs_value_name%></a><%}%>
                        </dd>
                    </dl>
                    <%}%> <%}%> <% if (goods_info.is_virtual == '1') { %>
                    <dl class="spec-promotion">
                        <dt><?= __('提货方式：'); ?></dt>
                        <dd><a href="javascript:void(0);" class="current"><?= __('电子兑换券'); ?></a></dd>
                    </dl>
                    <dl class="spec-promotion">
                        <dt><?= __('有效期：'); ?></dt>
                        <dd>
                            <a href="javascript:void(0);" class="current"> <?= __('即日起'); ?> <?= __('到'); ?> <%= goods_info.virtual_indate_str %> </a> <% if (goods_info.buyLimitation && goods_info.buyLimitation > 0) { %> <?= __('（每人次限购'); ?> <%= goods_info.buyLimitation %> <?= __('件）'); ?> <% } %>
                        </dd>
                    </dl>
                    <% } else { %> <% if (goods_info.is_presell == '1') { %>
                    <dl class="spec-promotion">
                        <dt><?= __('预售：'); ?></dt>
                        <dd>
                            <a href="javascript:void(0);" class="current"> <%= goods_info.presell_deliverdate_str %><?= __('日发货'); ?> </a>
                        </dd>
                    </dl>
                    <% } %> <% if (goods_info.is_fcode == '1') { %>
                    <dl class="spec-promotion">
                        <dt><?= __('购买类型：'); ?></dt>
                        <dd>
                            <a href="javascript:void(0);" class="current">F<?= __('码优先购买'); ?></a> <?= __('（每个'); ?>F<?= __('码优先购买一件商品）'); ?>
                        </dd>
                    </dl>
                    <% } %> <% } %>
                </div>
            </div>
            <div class="goods-option-value clearfix fz-28 colbc">
                <?= __('购买数量'); ?>
                <div class="value-box">
                    <span class="minus"><a href="javascript:void(0);">&nbsp;</a></span>
                    <span>
                         <% if(buyer_limit != 0) {
                                    if(buyer_limit >= goods_info.goods_stock){
                                        data_max = goods_info.goods_stock;
                                    }else if (buyer_limit>=remain_limit){
                                        data_max = remain_limit;
                                    }else{
                                        data_max = buyer_limit;
                                    }
                        } else {
                            data_max = goods_info.goods_stock;
                        }
                        if(goods_info.lower_limit > 1 && goods_info.promotion_is_start == 1&&goods_info.promotion_type!='presale' )
                        {
                            data_min = goods_info.lower_limit;
                            promotion = 1;
                        }
                        else
                        {
                            data_min = 1;
                            promotion = 0;
                        }
                        %>
                        <input type="text" pattern="[0-9]*" class="buy-num" promotion="<%=promotion%>" data-max="<%=data_max%>"
                               data-min="<%=data_min%>" id="buynum" value="<%=data_min%>"/>
                    </span>
                    <span class="add"><a href="javascript:void(0);">&nbsp;</a></span>
                    <% if(buyer_limit != 0) { %>
                    <div style="font-size: 0.5rem;text-align: center;"><?= __('限购'); ?><%= buyer_limit; %><?= __('件'); ?></div>
                    <% } %>
                </div>
            </div>
            <div class="goods-option-foot">
                <div class="only-two-handle buy-handle <%if(!goods_hair_info.if_store || goods_info.goods_storage == 0){%>no-buy<%}%>">
                    <% if (goods_info.cart == '1') { %>
                    <a href="javascript:void(0);" class="add-cart" id="add-cart"><?= __('加入购物车'); ?></a>
                    <% } %> <a href="javascript:void(0);" class=" fl buy-now <%if(goods_info.cart != '1'){%>wp100<%}%>" id="buy-now"><?= __('确定'); ?></a>
                </div>
            </div>
    </script>
    <!-- <?= __('新增促销'); ?> -->                                                                                                                       
    <script type="text/html" id="sale-activity">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block">
            <% if (promotion_info.jia_jia_gou || promotion_info.man_song) { %>
            <div class="goods-detail-item drap-ar">
                <!-- <?= __('加价购'); ?> --><% if (promotion_info.jia_jia_gou) { %>
                <div class="item-con">
                    <div class="tit-sale mb-20"><?= __('加价购'); ?></div>
                    <dl class="goods-detail-sale v-top wp100">
                        <% for(var i = 0; i < promotion_info.jia_jia_gou.rule.length; i++) { %> <% var rule_price = promotion_info.jia_jia_gou.rule[i].rule_price; var rule_goods_limit = promotion_info.jia_jia_gou.rule[i].rule_goods_limit; var redemption_goods = promotion_info.jia_jia_gou.rule[i].redemption_goods; %>
                        <dt><?= __('购物满'); ?><b class="col-red"><?= __('￥'); ?><%= rule_price; %></b><?= __('即可加价换购最多'); ?><%= rule_goods_limit; %><?= __('样以下商品'); ?></dt>
                        <dd>
                            <% for (var m = 0; m < redemption_goods.length; m++) { %>
                            <div>
                                <a href="<%= WapSiteUrl; %>/tmpl/product_detail.html?goods_id=<%= redemption_goods[m].goods_id; %>"><%= redemption_goods[m].goods_name %></a></div>
                            <% } %>
                        <dd>
                            <% } %>
                    </dl>
                </div>
                <% } %>
                <!-- <?= __('加价购'); ?> -->

                <!-- <?= __('满送'); ?> --><% if (promotion_info.man_song) { %>
                <div class="item-con">
                    <div class="tit-sale mb-20"><?= __('满即送'); ?></div>
                    <dl class="goods-detail-sale v-top wp100">
                        <% for(var i = 0; i < promotion_info.man_song.rule.length; i++) { %> <% var rule_price = promotion_info.man_song.rule[i].rule_price; var rule_discount = promotion_info.man_song.rule[i].rule_discount; %>
                        <dt><?= __('购物满'); ?><b class="col-red"><?= __('￥'); ?><%= rule_price; %></b><?= __('，'); ?> <?= __('即享'); ?><%= rule_discount; %><?= __('元优惠'); ?></dt>
                        <% } %>
                    </dl>
                </div>
                <% } %>
            </div>
            <% } %>
            <p class="new-btn close-btn absolute"><a href="javascript:" class="btns"><?= __('关闭'); ?></a></p>
        </div>
    </script>
    <!-- <?= __('代金券'); ?> -->
    <script type="text/html" id="voucher_script">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block vou-area">
            <h3 class="tc"><?= __('代金券'); ?></h3>
            <ul class="vou-lists">
                <% if(voucher_list.length > 0){ for(var i=0; i < voucher_list.length; i ++){ %>

                <li>
                    <div class="left tc hp100">
                        <div class="flex-middle hp100">
                            <div class="fz0">
                                <p>
                                    <i><?= __('￥'); ?></i> <span><%=voucher_list[i].voucher_t_price%></span>
                                </p>
                                <%if(voucher_list[i].voucher_t_points > 0){%> <em><?= __('需花费'); ?><%=voucher_list[i].voucher_t_points%><?= __('积分'); ?></em> <% } %>
                            </div>
                        </div>
                    </div>
                    <div class="right">
                        <div class="rgl">
                            <h4 class="one-overflow"><?= __('店铺优惠券'); ?></h4>
                            <span><?= __('购满'); ?><%=voucher_list[i].voucher_t_limit%><?= __('元使用'); ?></span>
                            <time><%=voucher_list[i].voucher_t_end_date_day%><?= __('前有效'); ?></time>
                        </div>
                        <div class="rgr">
                            <% if(voucher_list[i].is_get == 1){%>
                            <a href="javascript:" class="had"><?= __('已经'); ?><br><?= __('领取'); ?></a>
                            <%}else{%>
                            <a onclick="confrimVoucher(<%= voucher_list[i].voucher_t_id %>, <%= voucher_list[i].voucher_t_points %>, <%= voucher_list[i].voucher_t_price %>)">
                                <?= __('立即'); ?>
                                <br>
                                <?= __('领取'); ?>
                            </a>
                            <%}%>
                        </div>
                    </div>
                </li>
                <%}}%>

            </ul>
            <p class="new-btn close-btn absolute"><a href="javascript:" class="btns"><?= __('关闭'); ?></a></p>
        </div>

    </script>
    <script type="text/html" id="list-address-script">
        <% for (var i=0;i
        <addr_list.length;i++) {%>
        <li>
            <dl>
                <a href="javascript:void(0)" index_id="<%=i%>">
                    <dt><%=addr_list[i].name_info%><span><i></i><?= __('查看地图'); ?></span></dt>
                    <dd><%=addr_list[i].address_info%></dd>
                </a>
            </dl>
            <span class="tel"><a href="tel:<%=addr_list[i].phone_info%>"></a></span>
        </li>
        <% } %>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/swipe.js"></script>
    <script type="text/javascript" src="../js/common.js?v=9"></script>
    <script type="text/javascript" src="../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/iscroll.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/fly/requestAnimationFrame.js"></script>
    <script type="text/javascript" src="../js/fly/zepto.fly.min.js"></script>
    <script type="text/javascript" src="../js/product_detail.js"></script>
    <script type="text/javascript" src="../js/ve_poster1.js"></script>
    <script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>
    <script type="text/javascript" src="../js/ucsdk.min.js"></script>
    <div class="soshm-pop soshm-pop-hide" style="display: none;"><div class="soshm-pop-sites"><div class="soshm-group group1"><div class="soshm-item weixin" data-site="weixin"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAOVBMVEVHcEz///////////////////////////////////////////////////////////////////////99PJZNAAAAEnRSTlMAMOCwQGDQ8IAQkFAgn79wwKDPjjl1AAABuUlEQVR4Xu3VwXKFIAwF0IsASVBR8/8f27IBnSeitatOz455w30kSsTf9G8amW1KMy9O8JifSXdCNHhgiqQfBi+4R6KeowV3jKRNwaAr6qXeIcRqx4xLSfVVwqw3RDR5rQbNTtcjGoT2vZJNi+AAUyJIcK7uYGT1Tw0AjGW54dSkhUO2lgMgEy0mnGEtPLJQ1gIArtfHQQtyABYtrACStKBWBVWag+5QmgfdMfg0ah/ZxaGFtSd4gYyc0qCUEo/yLGDwEB90z/oHAVHAdJJ6N8DDDZ0Rwdf7uTsi3OX++cYF1ybO+/sJVhsCvF7zyLw2uIm0w5TLcGKt95w8xlAKE1MOna5e5nGqR92NnfVw4127C4R4nBOhXun6k0UmZ0XYXWl21yqSwxAUZIb0A09aJa6npDiTFg6tBOf0DkYrwS2PAiBBjxw/CShdrpx/GkA/6oFvzkYWvWM6fp+IR15JM4ugfeFQAbEgE/fNYHlWQdleCGnPgCKy/GDiO1zqdiGiw5BeWYFXCUHwKmEV3GFafdhwF3ceYN+0UeMdvk28rRlhi3mx4CHj+NviBPlMqitemVYVvOMMfsu/L0Ath0CgH1P9AAAAAElFTkSuQmCC" alt="weixin"></span><span class="soshm-item-text"><?= __('微信好友'); ?></span></div><div class="soshm-item weixintimeline" data-site="weixintimeline"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAOVBMVEVHcEz///////////////////////////////////////////////////////////////////////99PJZNAAAAEnRSTlMA8DB/z6DQPxCAwGAgcLBQ4JAZyUNJAAAB60lEQVR4Xs3X246rMAyF4QRI43Bmvf/DbomhLNUmznRfjS+r/p8gCqfwh0dyn8ZzUp/l27o7dnzMnrpv+hV2Ssjzr2I5JMjrCShIvyCWiIGCAhD7Rj6PACgYABjcg5gioAQNIE71vgegBAsAqdYnwBEIILm9FQhQ8HsKFQCL7SfAETSATfdzhCNYIIoCBsARLID1sy+AIxConYRENIXCZTInQdsRSsj4mMkcgC8QsHthzs7M4RQI2L498roB9t1tzP3DbFq4APYR72U8YOclQQkXwJ7LODg9he0HYM/N5PecrHrs/N3rCdy9XAf987+l0RPQPTK3od8TYM/bwtroCege5fx91L3Mz8Cme6QLUP2Qn4Gie4wXoHrUAN1jvQDV14COvVoD1deAqzdAUT0BtycwqZ6A2/OPWfUE3J5bObAn3O55MYUX+zewSbvn5by8ewJlbfd8Qs53T0DfNJfyMPeWT3dPQAv+sCdwCl12RmxPgLeu6uyBs8AAbSGrh6sFfGH8XGQD+ILdbaMFfOFwXnEIUGjfuScN+ELHkoICPGFyXhQNgFEosHcEDWBQwhIqUwjAEZzX9S0SqAmxC87IqAAjrNL85CFghbiF5kiJBJRQxCtJCIH/fbHr0q627jJ//+lbynjOMeXwh+cfhtCFZYWp8dQAAAAASUVORK5CYII=" alt="weixintimeline"></span><span class="soshm-item-text"><?= __('朋友圈'); ?></span></div><div class="soshm-item weibo" data-site="weibo"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAPFBMVEVHcEz///////////////////////////////////////////////////////////////////////////+PybD1AAAAE3RSTlMAoGAw0PCAEEDAIHDfsJBQ4I+vnt3bXwAAAkdJREFUeF7tVtuCqyAMLAghAfHG///rqW0xwWjd9nHPzgsWyGQgF3r7v/CHzrhSiieau+8I+rKhn8MXBEXCp88JqDRwn4tIkBAtZM8MXwKJGb7E+FBBb3YEa4jI4GlMHwzpVKMpL5ggrdzqdo4bgz8+RFwKYzjIhEcWwKkEKA2EEy9DGKY1o3hRyGyBvDaUCnP/ZdcPldXWlx1Aslu0ptLGdbQ7+7kooPJRJbjnKPFkP7wDvEPs8vcxcypoexkFhOV1/8vjyOP6eR9hT7AUDZ/a2ZUBFQH7v0Sul/AyoM/sW899k2e2/JgAn+GLTSp2P7R3j4iArXcZa/H1aicNAIbIq6BIn3QcgGxFhsbRNFFggHf1J0rXNujmMNV2jFcdm3jHSY24FN8U8HmzjlylHnYaQ2V3Hc+NAKlVY05belLTtrqTTTHUSVVDr4Wp2qOM6SxOcVLknZzTOUmHzS7rE9QUGVVBH0nwgiA3jSN6kYzPkcM2iZpgUKNpkCphJ4EOm7VreicLgHqcngneKBjFjU4WMZe+q8mjCNo4ZhkD3OKZxYXplE8qCpYJUv1ibypA8dbkBzf4sImJ+mkx8mXan6HnY0KbNT5w0YsplYqw7fLYyE26a41szLH3kXU6ckpt0FMMJ4vRtIl8ac+rLqoX2mE9Zq/tNYPfCjKvHG7AugzquVcMz7yh7mgNWNKEF//cymJDaz0aL9zz4htHNFvE+86IaabC8EO8vUdIU9GQ78U1OqCiMRnLzq+BFjIR+TIRkYGE4fbb8Yd/KxZhLtPWraEAAAAASUVORK5CYII=" alt="weibo"></span><span class="soshm-item-text"><?= __('微博'); ?></span></div></div><div class="soshm-group group2"><div class="soshm-item yixin" data-site="yixin"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAP1BMVEVHcEz///////////////////////////////////////////////////////////////////////////////9KjZoYAAAAFHRSTlMAMMAgQGCgEPCAcNBQsOCQ37+Pr/HIP5wAAAI0SURBVHhetVfdeuwgCFyNfxhNTJb3f9Zz03aqYvS0X+du10AGGIS8/hjeGLP9zHSz7gr8gcvZ7f+s484tyr3sw7xZxpVWzNXFYxQzzdrNz3g/B6IKzxCe4rC8gnto73gNb79mv2eiSET64PbEz+2z9ThScZ97IAbYdbk2uvLgOvvEgBZLZapIqBVvwFkcaaQKspEU5BfUWpmDl05gP/dwvgAfevu5h02qwLTfTqkSPoDWFHtPAbyORh8qEbVXmuopwKmtqkblQ3Z2oNjwqYEvAlW6oAwuSC0exwujRKC+WML3o4yGaf7weChyA9WpHjHAH3TPQC+82i3SGntly92jkYSKkRFKBQRJd1T9FOUGJCEJeeBAswASMqQhjIpjYQFOdoDX7XDAErTgoMBBE8KcAVjnRQdxFAKhOzsdylq0AwfQgeceh1RlXVX1fBxyUYqQqogKHvHHQy/41qt0S6oHArbNiwYhQIV6GMozBL0BmmIigxJbPYMuKIgNZSQCuMK+MhY2eYrZwRT20E0b6i3zxwxBBPgTWrA7A9kP5gobabmI8b4CAzoN1xg92A6ihnxdM2uHGwLVkjGRCENtsGHk6uxY2OIU4kfFROkWO5ns0MBoFBUrsUjgyW62ZgYn7Ro2PyyKOzcIwneKj7Bv4eEBKG9Kn+VShq7HTcpnXsPuf7eua8kea8kU9HrCpifmhxnaothjBHotwB5Dc/9aQ3JCLjLEuwJzagb0mfwPP70p4fP7T/APapCRU7q26PsAAAAASUVORK5CYII=" alt="yixin"></span><span class="soshm-item-text"><?= __('易信'); ?></span></div><div class="soshm-item qzone" data-site="qzone"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAP1BMVEVHcEz///////////////////////////////////////////////////////////////////////////////9KjZoYAAAAFHRSTlMAYECgEPAwwH8/4NAgUJCwcM+fr0RbN4IAAAGVSURBVHhe7ZbdkoMgDIUVE2ARf7o97/+s22k7pZmAUJnZq36XmBwPJgGHf+CLQ+zKN8DUJbABXRYi0GdhArosRNy5dhoAzMl8whN3UsADXRaI0WfBA30WGOiyMOKd8XOBIATCGQNdFoghCXSiBAJ/YgoEXJgp+5PjCs1Vh9H9yOjA3gRoVcuzK5KtjHVqu27db/Y0vyLKvtZ35AjOjz+itE74tEPCMBJa5hFKF5EvHdoAiX5XFPkTDRKaUWK7x1oW+YOCJuRZB5lfHoysAo9tsy2idKlGtZZnST7Fpxa9Fgr5aafqU/0qzTI0q62SyHc0HJOajU2mfeCHVoE55vKx1QSC7DTLkFxqAtJp1DPSdJ/yksZs34I6Qw4waXgStHjHjZecKVbKjuvcUIYdYF/GVf+YPGq42k9NDa71URX6SIB3MaH1MkAykb44xnaB8HyZnZvHySLBb5E7N5bBvMVF0Yxb2zgtL/eL0g540tJHng4e2qqAK4TER5GXShvwQZ2WUCmDA9bDTqP1eJz8ZoYKVoX08uUPi2Bapj4cFIsAAAAASUVORK5CYII=" alt="qzone"></span><span class="soshm-item-text">QQ<?= __('空间'); ?></span></div><div class="soshm-item tqq" data-site="tqq"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAOVBMVEVHcEz///////////////////////////////////////////////////////////////////////99PJZNAAAAEnRSTlMAoLAwwECAEPAg0HDgYFCQv48OkrAuAAAB90lEQVR4Xu2UXZOjIBBFG+RTQJPz/3/sbqKxnZCpcczD1lblPNGl3Kb70sj/xochXG2Y5CzTBUiQgpyiUEu86TjGKL8nYB/b2k8KUXoGrAaN+butzVaAZFuWL9i0lzVotCMbFMxeIi85r3CN97BIT0gA1RnjRgCusuHx9z7ColRtf3oL1LKmzaUCl6gtvC0dQL3FznX7R0ghilIS2u3AvREA42sBC2l4avwIoyxM9xKGBEy3OD2XMEONr041r8tlMczYxdT21H/NvycnGNRGNdA+22igyAsaXL5epIwR8YTuAHUXeV277QgNE5dcXvLczYnWNFXAxEcE83aadDM5phSlt4C8+XUnPWI1QrwDavT0o6R/eVYuWoNs5BLml8MIVru5krcTdfZ0CgMETbjiNwHfm5PStI/9cQG9pDbvQi0h8CA+CfSje9EP4PTyLgQ1SF6S56SXb2T1dttvZCVR5RtiizpKtMdI5eBG27Q6jPzMsPg+9SNlYJIDjCASUzdSHqocYXDlns31D8IkR/HLBCgqeYwLT1MeDaQoB+nLHUbt6REMNFEGw4H9/aMUjQlTC9cK4KIcpywWFjZqk98wQl7fYYBkJvkVGey68n/J8lsKNDmNPqvnqVR5hwxGTqOv4nkKeHmH8K6AA/kIfAQKSd6jePl3fPgDOtEg/yPlfh8AAAAASUVORK5CYII=" alt="tqq"></span><span class="soshm-item-text"><?= __('腾讯微博'); ?></span></div></div><div class="soshm-group group3"><div class="soshm-item tieba" data-site="tieba"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAOVBMVEVHcEz///////////////////////////////////////////////////////////////////////99PJZNAAAAEnRSTlMAMPCggL8QwD9/4NCQsHDPYFAr85NjAAABBklEQVR4Xu3V3Y6DIBCG4QFxoP5u5/4vdrM4+2kTaoD+eMJ7QtvUJxHHQF+sFbboabdkhGTrOSDJyoEGWI9sFeAJ+csAjzUJhD2uAY6FK4AG1M9BAxrQawr02nQOdDsg6fw54K4E1lcBeRHgNDA4NJwD97guByBxYJwBXVxXpsn/lQsgthKbSSsFFtFcHcCD/PdTBThB1lQARg4NXArgBnQj+2JgltjIKnSFQIcT546NLAE62Udg0s8Go3xDQxrgVbYsH3eDc18msz4emQH/ywTswwTiJkaTCyyCjdfG+DwoF6BZr0dGv+UCbPV65BYqASjg/UGFk+gMVQLoM0CIGUIGP7yr1i+rL2X5ejXICAAAAABJRU5ErkJggg==" alt="tieba"></span><span class="soshm-item-text"><?= __('百度贴吧'); ?></span></div><div class="soshm-item douban" data-site="douban"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAANlBMVEVHcEz///////////////////////////////////////////////////////////////////+GUsxbAAAAEXRSTlMAIPDgv0BegH/AoDCPzxBvsNFNiOYAAAC3SURBVHhe7dbNCoQwDATgabttV+tf3v9lF+rJRJr2sojkO0oYBg1BPIhx3zEJTKQxE04WIJnBLZSoz/T6AD81KAH6kv4twAIswH8a1AC7B7lPwvulzwJp80tBh7J4IooQdiIKu4PmPLwrhND5X1CoSmASVRGqXAcDmKOzALCFuwqOFWiJdxVm/lB/j0kpoFZYZYEDDXK8iAIOTXx+5om5ub0Xnh1HqtbLTGEff1h8W0DMwxwew/wAniVLWfHYFrQAAAAASUVORK5CYII=" alt="douban"></span><span class="soshm-item-text"><?= __('豆瓣'); ?></span></div><div class="soshm-item qq" data-site="qq"><span class="soshm-item-icon"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAMAAACdt4HsAAAAP1BMVEVHcEz///////////////////////////////////////////////////////////////////////////////9KjZoYAAAAFHRSTlMAIGAwgKDwQBDA0FDfsHDgkD+PT2O/b44AAAGsSURBVHhe1dfddqsgEAXgzf8AYmw67/+sPUe7zHLhONjctN9dFHaMCVuCX859/GPwM+5Z+Ft+fuIuO/FB9IQb6sSd6DHsyaeKwxAqLIhpcL7MQdf4QtQTZr5UoDCRr3lcC6yIhCuGVV65ANWEK5F1CbLKAxbIPA/IkGUeARkPMe8GJEjS3w+o7wY0HrJAYGzmEQECT/Rgna9OCgBcGXl/j1M095Xa8xADqtMXVF7HzIQzCzamidMTVqniRHpVtguRO2U2+DZPhA5NzPb1yi6Fd4+8VDq0xiJ0STskm7RyOKB8uqRpu+hYobDbwCaWWU64kLLUKtNI4dgs/p6dXnmfz4lfOIp1nLFJ8769+rA+x+tFfXJt4dYjbupP5FuLmu8HZCnA/ihgLZLHSMA+MPRtaLYTakBO/U206xHL/02WtvsqqWjdho0iFwCFNzkEeX5eh2cc2UAA3Oiz1bUkbfJU9s1dWoAiaPNV5Y2N6l6nshiM8kdBN0PkeYiFgHhMu7NRLlH5DMpXGGD7TIJojn1t1QcfNMIFsm3PKDNhVcOe8VgMVJRW3bGaEuEX+gJWbpMHBa1eygAAAABJRU5ErkJggg==" alt="qq"></span><span class="soshm-item-text">QQ<?= __('好友'); ?></span></div></div></div></div>
    <!--o2o<?= __('分店地址'); ?>Begin-->
    <div id="list-address-wrapper" class="nctouch-full-mask hide">
        <div class="nctouch-full-mask-bg"></div>
        <div class="nctouch-full-mask-block ">
            <div class="header">
                <div class="header-wrap">
                    <div class="header-l"><a href="javascript:void(0);"> <i class="mine-back"></i> </a></div>
                    <div class="header-title">
                        <h1><?= __('商家信息'); ?></h1>
                    </div>
                </div>
            </div>
            <div class="nctouch-main-layt">
                <div class="nctouch-o2o-tip"><a href="javascript:void(0);"
                                                id="map_all"><i></i><?= __('全部实体分店共'); ?><em></em><?= __('家'); ?><span></span></a></div>
                <div class="nctouch-main-layout-a" id="list-address-scroll">
                    <ul class="nctouch-o2o-list" id="list-address-ul">
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--o2o<?= __('分店地址'); ?>End-->
    <!--o2o<?= __('分店地图'); ?>Begin-->
    <div id="map-wrappers" class="nctouch-full-mask hide">
        <div class="nctouch-full-mask-bg"></div>
        <div class="nctouch-full-mask-block ">
            <div class="header transparent">
                <div class="header-wrap">
                    <div class="header-l"><a href="javascript:void(0);"> <i class="mine-back"></i> </a></div>
                </div>
            </div>
            <div class="nctouch-map-layout">
                <div id="baidu_map" class="nctouch-map"></div>
            </div>
        </div>
    </div>
    <p style="display: none" id="goods_one_img"><%= goods_one_img%></p>
    <p style="display: none" id ='share_goods_name'><%=goods_info.goods_promotion_tips.substring(0,50)%></p>
    <p style="display: none"  id ="share_like" ><%= share%></p>
	<!-- 新版2021评价 -->
    <script type="text/html" id="goodsReview">
		
		<% if (evalcount > 0) { %>
			<a href="javascript:void(0);" id="reviewLink" class="flex evaluate-tit">
				<h5>宝贝评价（<%= evalcount %>）</h5>
				<span><em>查看全部</em><i class="zk-right-red"></i></span>
			</a>            
        <% } else { %>
			<a href="javascript:void(0);" class="flex evaluate-tit pb-0">
				<h5>宝贝评价（0）</h5>
			</a>  
        <% } %>       
		<% if (goods_review_rows.length > 0) { %> 
			<p class="special-goods-evaluate-label"><label class="label-item">版型很好看</label><label class="label-item">穿上效果很好</label></p> 
			<ul class="specical-goods-views">
				<% for(var i = 0; i < goods_review_rows.length; i++) { %>
				<li>
					<h6 class="flex">
						<em class="img-box" style="background:url('+<%= goods_review_rows[i].user_logo %>+') no-repeat center;background-size:cover;"></em>
						<p class="fz0"><span class="block"><%= goods_review_rows[i].user_name %></span><b><%= goods_review_rows[i].create_time %> I 尺寸:S 颜色分类:粉色  </b></p>
					</h6>
					<p class="specical-evaluate-text"><%= goods_review_rows[i].content %></p>
					<% if(goods_review_rows[i].image_row.length>0){ %> <% var image_row=goods_review_rows[i].image_row %>
					<div class="goods_geval">
					    <% for(var j=0;j < image_row.length; j++ ){ %>
					        <a href="javascript:void(0);" data-start="<%=j%>">
					            <img class="goods_geval-img2" src="<%= image_row[j] %>"/>
					        </a>
					    <% } %>
					    <div class="nctouch-bigimg-layout hide">
					        <div class="close" style="margin-top:50px"></div>
					        <div class="pic-box">
					            <ul>
					                <% for(var j=0; j < image_row.length; j++ ){ %>
					                <li style="background-image: url(<%= image_row[j] %>)"></li>
					                <% } %>
					            </ul>
					        </div>
					        <div class="nctouch-bigimg-turn">
					            <ul>
					                <% for(var j=0;j < image_row.length; j++ ){ %>
					                <li class="<% if(j == 0) { %> cur <% } %>"></li>
					                <% } %>
					            </ul>
					        </div>
					    </div>
					</div>
					<% } %>
					<div class="mui-tagscloud-date"><%= goods_review_rows[i].goods_spec_str; %></div>
				</li>
				 <% } %>
			</ul>
		<% } %>
        
    </script>
    <!--o2o<?= __('分店地图'); ?>End-->

    <script>
        $.ajax({
            url:ApiUrl + '/index.php?ctl=GroupBuy&met=groupBuyViews&typ=json',
            type:'GET',
            dataType: "json",
            data:{gid:<?php echo $_GET['goods_id']?>},
            success: function (data) {
                console.log(data);
            }
        })


        var shop_u_id = '';
        $.ajax({
            url: ApiUrl + "/index.php?ctl=Goods_Goods&met=getShopUid&typ=json",
            type: "POST",
            data: {k: getCookie("key"), u: getCookie("id"), goods_id: goods_id},
            dataType: "json",
            async: false,
            success: function (result) {
                if (result.status == 200) {
                    shop_u_id = result.data[0].u_id;
                     token = result.data[0].token;
                }
            }
        });


        $(function () {
             //联系客服
            UC.config = {
                // 开启调试模式
                debug: false,
                // 当前登录用户TOKEN，不传会默认从地址栏获取
                token: getCookie("token"),
                // 当前企业ID，不传会默认从地址栏获取
                enterId: getCookie("enterId"),
                // 必填,从开放平台处获取
                appId: '172d1c69e247207db52b54081255e450',
                // 必填,从开放平台处获取
                appSecurity: '896f886913826082f52a49a63aef4cff',
                // 鉴权后跳转地址（当前页面地址）, 注意域名必须和创建APP时候设置的域名保持一致,从开放平台处获取
                redirectUri: WapSiteUrl + "/tmpl/product_detail.html?goods_id=7"
            };
        });
          // 用于处理接口异常、接口不存在
        UC.error = function (data) {
                console.error(data);
        };
        UC.ready = function () {
                //在这调用接口方法
                // openChat();
                // getUserInfo();
                // hideNavigationBarLeft();  
                // setNavigationBarRight();
        };
        function openChat() {
            var apiName = 'openChat';
            var param = {
              'sessionId': shop_u_id,
              'sessionType': 1,
              'content': ''
            };
            UC.call(apiName, param, function (data) {
              console.log('=========' + apiName + '=========');
              console.log(JSON.stringify(data));
            }, function (data) {
              console.log(data, apiName);
            });
        }


        function hideNavigationBarLeft () {
            alert(1);
            var apiName = 'hideNavigationBarLeft';
            var param = '';
            UC.call(apiName, param, function (data) {
                alert(3);
           console.log('=========' + apiName + '=========');
           console.log(JSON.stringify(data));
            }, function (data) {
                alert(4);
            console.log(data, apiName);
            });
          };
        function setNavigationBarRight () {
            alert(2);
            var apiName = 'setNavigationBarRight';
            var param = {
              'text': ''
            };
            UC.call(apiName, param, function (data) {
                alert(5);
              console.log('=========' + apiName + '=========');
              console.log(JSON.stringify(data));
            }, function (data) {
                alert(6);
              console.log(data, apiName);
            });
          };
            //商品属性面板折叠'); ?>
            function isHidden(oDiv) {
                var vDiv = document.getElementById(oDiv);
                var vBtn = document.getElementById("btn_attr");
                if (vDiv.style.display == "none") {
                    vBtn.innerHTML = "<?= __('商品属性'); ?>&nbsp;<?= __('︽'); ?>";
                } else {
                    vBtn.innerHTML = "<?= __('商品属性'); ?>&nbsp;<?= __('︾'); ?>";
                }
                vDiv.style.display = (vDiv.style.display == "none") ? "block" : "none";
            }



    </script>
    <script>
//    判断内容加载完成后执行
    var contentList={};
    function integrity(){
//        页面需要加载的部分，都为true时
        // if(contentList.details&&contentList.recommend){
           console.log(contentList);
            setTimeout(function(){
                ScrollArea();
            },500)
            
            
        // }
    }
        function ScrollArea() {
        // 分类数组
            var ModuleArray = ["contentA", "contentB", "contentC","contentD"];
            var ModuleDistance = [];
            $(".contentA").scrollTop();
            $(window).scroll(function () {//获得滚动条距离顶部的高度);
                var height = $(window).scrollTop();
                currentRegion();
            })
//    获取div距离顶部的距离，最大距离
            var a = ModuleArray.length;
            for (var i = 0; i < ModuleArray.length; i++) {
       console.log(i)
                var name = ModuleArray[i];
                var padding = -45;
                var altitude = $("." + name + "").offset().top + padding;
                ModuleDistance.push(altitude);
//        最大时
                if (i == ModuleArray.length - 1) {
                    var ContentHigh = $("." + name + "").height();
                    var maxAltitude = $("." + name + "").offset().top + padding + ContentHigh;
                    ModuleDistance.push(maxAltitude);
                }
            }
//    判断显示区域
            function currentRegion() {
                for (var i = 0; i <= ModuleDistance.length; i++) {
                    var minNumber = ModuleDistance[i];//内容顶部的距离
                    var maxNumber = ModuleDistance[i];//内容底部的距离
                    var y = $(window).scrollTop();//垂直滚动条的距离
                    if (y < minNumber) {
                        console.log("当前在" + i + "区域");
                        $(".header-nav").children().removeClass("cur");
                        $(".header-nav").children().eq(i - 1).addClass("cur");
                        return false;
                    }
                }
            }
//跳转显示区域
            $(".header-nav li").click(function () {
                var x = $(this).index();
                skipModule(x);
            });
//锚点跳转
            function skipModule(a) {
                var number = ModuleDistance[a];
                $("html,body").scrollTop(number + 1);
            }
            currentRegion();
        };


</script>
    <?php
    include '../includes/footer.php';
    ?>

    </body>
    </html>
