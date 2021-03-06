<?php
    include __DIR__ . '/../includes/header.php';
?>
    <!doctype html>
    <html class="bgf">
    
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-touch-fullscreen" content="yes"/>
        <meta name="format-detection" content="telephone=no"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
        <meta name="format-detection" content="telephone=no"/>
        <meta name="msapplication-tap-highlight" content="no"/>
        <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1"/>
        <title><?= __('店铺首页'); ?></title>
        <link rel="stylesheet" type="text/css" href="../css/base.css">
        <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
        <link rel="stylesheet" type="text/css" href="../css/nctouch_store.css?v=91">
		<link rel="stylesheet" href="../css/nctouch_products_list.css?v=911">
		<link rel="stylesheet" href="../css/nctouch_products_detail.css?v=911">
		<link rel="stylesheet" href="../css/swiper.min.css">
        <link rel="stylesheet" href="../css/iconfont.css">
		<link rel="stylesheet" href="../css/customize.css?v=11399">
    </head>
    <!-- <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script> -->
    <script type="text/javascript">
        var url = '../store/store?shop_id='+<?php echo $_GET['shop_id']?>; 
        wx.miniProgram.redirectTo({url:url})
    </script>
    <body>
	
	<div class="customize-feature-page-head clearfix lists">
		<a href="/tmpl/search.html?mb=shop" class="customize-feature-page-input to-search">
			<i class="iconfont icon-search"></i>
			<div class="flex1">
				<input class="placeholder-c1" type="text" placeholder="搜你想搜的" readonly>
			</div>
		</a>
		<!-- <a href="./shop_goods_cat.html?shop_id=<?php echo $_GET['shop_id']?>" class="zk-menu"></a> -->
		<b class="zk-menu active" id="menuChange"></b>
	</div>
	<div class="custom-store-top bgf">
		<div class="custom-store-info goods-detail-store">
			<div class="store-name flex">
			    <i class="icon-store  shop_image" style="background-image:url(https://shops.look56.com/image.php/shop/data/upload/media/b54a7238a221685616d4fe794fa43b0b/10002/1/image/20200928/160126733562579976301346610359.png!64x64.png);"></i>
				<div class="flex1">
					<p class="top"><span class="name shop_name">订来订趣 新疆礼物</span>
					<!-- <b class="fans">粉丝数 56435</b> -->
					</p>
					<p>
						<em>综合评分</em>
						<span id="label_name"></span>
					</p>
				</div>
				<div class="store-head-oper">
					<!-- <button class="operate btn-save collect pd-collect" onclick="collectShop(<?php echo $_GET['shop_id']?>)">收藏</button> -->
					<!-- <button>签到</button> -->
					<?php if(!$_COOKIE['SHOP_ID_WAP']){?>
			        <div class="store-favorate">
			            <a href="javascript:void(0);" id="store_collected" class="added fz-28" > 
			                <i class="iconfont icon-like default-color align-middle mr-10"></i><b class="default-color"><?= __('已收藏'); ?></b> 
			            </a> 
			            <a href="javascript:void(0);" id="store_notcollect" class="fz-28"  style="color: #222;" hidden="true"> 
			                <i class="iconfont icon-like align-middle mr-10"></i><b><?= __('收藏'); ?></b> 
			            </a> 
			        </div>
			        <?php } ?>
				</div>
			</div>
			<ul class="custom-store-nav flex" id="nav_tab">
				<!-- <li class="selected"><a href="javascript: void(0);" data-type="storeindex" id="storeindex" onclick="nav_clicks('storeindex')"><?= __('店铺首页'); ?></a></li> -->
				<li class="selected"><a href="javascript: void(0);" data-type="storeindex1" id="storeindex1" onclick="nav_click('storeindex1')"><?= __('店铺首页'); ?></a></li>
				<li><a href="javascript: void(0);" data-type="allgoods" id="allgoods" onclick="nav_click('allgoods')"><?= __('全部商品'); ?></a></li>
				<li><a href="javascript: void(0);" data-type="newgoods" id="newgoods" onclick="nav_click('newgoods')"><?= __('商品上新'); ?></a></li>
				<li><a href="javascript: void(0);" data-type="storeactivity" id="storeactivity" onclick="nav_click('storeactivity')"><?= __('店铺活动'); ?></a></li>
			</ul>
		</div>
		<!-- 首页 -->
		<div id="storeindex_con"></div>
		<!-- 所有商品 -->
		<div id="allgoods_con"></div>
		<!-- 商品上新 -->
		<div id="newgoods_con" class="nctouch-store-goods-list customize-goods-lists"></div>
		<!-- 店铺活动 -->
		<div id="storeactivity_con"></div>
	</div>
    <div class="fix-block-r">
        <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
    </div>
    
    <div id="store_voucher_con"></div>

    <div id="shop_footer_div"></div>
    
	<!-- 首页 -->
	<script type="text/html" id="store_index_tpl">
	    <!-- 首页-图片 -->
	    <a class="iblock wp100 custom-img-module" href="javascript: void(0);" onclick="dajinquan()"><img class="wp100" src="https://www.yuanfengtest.com/image.php/shop/data/upload/media/plantform/6fc0625bf097e245fa1c1007bf528b2d/image/20200813/159730607675136588586586241244.png" alt=""></a>
	    <!-- 首页-轮播图 -->
	    <div id="store_sliders" class="swiper-container nctouch-store-wapper pl-30 pr-30"></div>
		<div class="customize-goods-lists">
	    	<div class="grid pt-0 style-change">
	    		<h3 class="custom-store-module-tit tc"><span>热销爆款</span></h3>
	    		<ul class="fz0 masonry">
	    			<% if (common_salenum_list) { %>
	    			<% for (var j in common_salenum_list) { var s = common_salenum_list[j]; %>
	    			<li class="item">
	    				<div class="pad">
	    					<span class="goods-pic iblock">
	    						<% if (s.third_url) { %>
									<a class="iblock wp100" href="<%= s.third_url %>">
								<% } else {%>
									<a class="iblock wp100" href="product_detail.html?goods_id=<%= s.goods_id %>&amp;pos=1&amp;pos_page=product_list">
								<% } %>
	    							<img src="<%=s.common_image %>">
	    						</a>
	    					</span>
	    					<dl class="goods-info relative">
	    						<dt class="goods-name">
	    							<a href="product_detail.html?goods_id=7&amp;pos=1&amp;pos_page=product_list">
	    								<h4 class="more-overflow"><%= s.common_name %></h4>
	    							</a>
	    						</dt>
	    						<dd class="goods-sale">
	    							<% if (s.third_url) { %>
										<a href="<%= s.third_url %>">
									<% } else {%>
										<a href="product_detail.html?goods_id=<%= s.goods_id %>&amp;pos=1&amp;pos_page=product_list">
									<% } %>
	    								<p class="label">
	    										<% if (s.label_name_arr) { %>
													<% for (var l in s.label_name_arr) { %>
			    											<label class="label-item"><%= s.label_name_arr[l]%></label>
													<% } %>
	    										<% } %>
	    								</p>
	    								<p>
	    									<span class="goods-price"><b>￥</b><em><%= s.common_price %></em>
	    									</span>
	    									<b class="had-sale"><%= s.common_salenum %>人付款</b>
	    								</p>
	    							</a>
	    						</dd>
	    						
	    					</dl>
	    			   </div>
	    			</li>
	    			<% } %>
	    			<% } %>
	    		</ul>
	    		<h3 class="custom-store-module-tit tc"><span>新品主推</span></h3>
	    			<ul class="fz0 masonry">
						<% if (rec_goods_list) { %>
	    				<% for (var i in rec_goods_list) { var g = rec_goods_list[i]; %>
		    				<li class="item">
		    					<div class="pad">
		    						<span class="goods-pic">
		    								<% if (g.third_url) { %>
		    									<a href="<%= g.third_url %>">
		    								<% } else {%>
		    									<a href="product_detail.html?goods_id=<%= g.goods_id %>&amp;pos=1&amp;pos_page=product_list">
		    								<% } %>
		    								<img src="<%= g.common_image %>">
		    							</a>
		    						</span>
		    						<dl class="goods-info relative">
		    							<dt class="goods-name">
											<% if (g.third_url) { %>
		    									<a href="<%= g.third_url %>">
		    								<% } else {%>
		    									<a href="product_detail.html?goods_id=<%= g.goods_id %>&amp;pos=1&amp;pos_page=product_list">
		    								<% } %>
		    									<h4 class="more-overflow"><%= g.common_name %></h4>
		    								</a>
		    							</dt>
		    							<dd class="goods-sale">
		    								<% if (g.third_url) { %>
		    									<a href="<%= g.third_url %>">
		    								<% } else {%>
		    									<a href="product_detail.html?goods_id=<%= g.goods_id %>&amp;pos=1&amp;pos_page=product_list">
		    								<% } %>
		    									<p class="label">
												<% if (g.label_name_arr) { %>
													<% for (var j in g.label_name_arr) { %>
			    											<label class="label-item"><%= g.label_name_arr[j]%></label>
													<% } %>
	    										<% } %>
		    									</p>
		    									<p>
		    										<span class="goods-price"><b>￥</b><em><%= g.common_price %></em>
		    										</span>
		    										<b class="had-sale"><%= g.common_salenum %>人付款</b>
		    									</p>
		    								</a>
		    							</dd>	
		    						</dl>
		    				   </div>
		    				</li>
	    				<% } %>
	    				<% } %>
	    			</ul>
	    	</div>
	    </div>
	</script>
    <!-- banner tpl -->
    <script type="text/html" id="store_banner_tpl">
        <div class="store-top-bg"><span class="img" nc_type="store_banner_img"><img src="" alt="store-bg"></span></div>
        <div class="store-avatar"><img src="<%= store_info.store_avatar %>"/></div>
        <div class="iblock store-name-text">
            <a href="./store_intro.php?shop_id=<%= store_info.shop_id %>" class="block store-name"><em><%= store_info.store_name %></em><i class="iconfont icon-arrow-right fz-24"></i></a>
			<span class="fans-num">
				<input type="hidden" id="store_favornum_hide" value="<%= store_info.store_collect %>"/>
				<em id="store_favornum"><%= store_info.store_collect %>粉丝</em>
			</span>
        </div>
        <?php if(!$_COOKIE['SHOP_ID_WAP']){?>
        <div class="store-favorate">
            <a href="javascript:void(0);" id="store_collected" class="added fz-28"> 
                <i class="iconfont icon-like default-color align-middle mr-10"></i><b class="default-color"><?= __('已收藏'); ?></b> 
            </a> 
            <a href="javascript:void(0);" id="store_notcollect" class="fz-28"> 
                <i class="iconfont icon-like align-middle mr-10"></i><b><?= __('收藏'); ?></b> 
            </a> 
        </div>
        <?php } ?>
        
    </script>
    <script type="text/html" id="store_sliders_tpl">
        <ul class="swiper-wrapper">
            <% for (var i in store_info.mb_sliders) { var s = store_info.mb_sliders[i]; %> <% if(s.imgUrl){ %>
            <li class="swiper-slide">
                <% if (s.link) { %> <a href="<%= s.link %>"><img class="wp100" alt="" src="<%= s.imgUrl %>"/></a> <% } else { %> <a href="javascript:void(0);"><img class="wp100" alt="" src="<%= s.imgUrl %>"/></a> <% } %>
            </li>
            <% } %> <% } %>
        </ul>
		<div class="swiper-pagination"></div>
    </script>
    <script type="text/html" id="voucher_list_tpl">
        <ul class="inline">
            <% for (var i in voucher_list) { %>
            <li>
                <a href="store_voucher_list.html?shop_id=<%=voucher_list[i].shop_id%>">
                    <div class="left"><span><i><?= __('￥'); ?></i><strong><%=voucher_list[i].voucher_t_price%></strong></span></div>
                    <div class="right"><span><?= __('满'); ?><%=voucher_list[i].voucher_t_limit%><?= __('元使用'); ?></span></div>
                </a>
            </li>
            <% } %>
        </ul>
        <p class="fr"><a href="store_voucher_list.html?shop_id=<%=voucher_list[i].shop_id%>"><span><?= __('更多'); ?></span><i class="icon-arrow"></i></a></p>
    </script>
    <script type="text/html" id="goodsrank_collect_tpl">
		<div class="swiper-container store-goods-rank-swiper">
			<ul class="swiper-wrapper">
				<% var goods_list = items; %>
				<% for (var i in goods_list) { var v = goods_list[i]; %>
				<li class="swiper-slide">
					<a class="iblock wp100 hp100" href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
						<em class="iblock img-box" style="background:url(<%= v.common_image %>) no-repeat center;background-size:cover;"></em>
						<p class="clearfix"><span class="save fl"><?= __('收藏'); ?><em><%= v.common_collect %></em></span><span class="pri fr"><?= __('￥'); ?><em><%= v.common_price %></em></span></p>
					</a>
				</li>
				<% } %>
			</ul>
		</div>
		
       <!-- <% var goods_list = items; %>
        <% for (var i in goods_list) { var v = goods_list[i]; %>
        <dl class="goods-item">
            <a href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
                <dt><img alt="<%= v.common_name %>" src="<%= v.common_image %>"/></dt>
                <dd><span><?= __('收藏'); ?><em><%= v.common_collect %></em></span><span><?= __('￥'); ?><em><%= v.common_price %></em></span></dd>
            </a>
        </dl>
        <% } %> -->
    </script>
    <script type="text/html" id="label_name_tmpl">

    	<% if(store_info.shop_desc_scores){%>
			<%for(i=0;i< store_info.shop_desc_scores;i++){%>
                <i class="star"></i>
            <%}%>
		<% } %>
		<% if(store_info.shop_label_name){%>
			<% for (var i in store_info.shop_label_name) {  %>
				<label class="label-item"><%= store_info.shop_label_name[i] %><%=store_info.shop_label_name.length %></label>
			<% } %>
		<% } %>
    </script>
    <script type="text/html" id="goodsrank_salenum_tpl">
		<div class="swiper-container store-sale-rank-swiper">
			<ul class="swiper-wrapper">
			<% var goods_list = items; %>
			<% for (var i in goods_list) { var v = goods_list[i]; %>
			<li class="swiper-slide">
				<a class="iblock wp100 hp100" href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
					<em class="iblock img-box" style="background:url(<%= v.common_image %>) no-repeat center;background-size:cover;"></em>
					<p class="clearfix"><span class="save fl"><?= __('已售'); ?><em><%= v.common_salenum %></em></span><span class="pri fr"><?= __('￥'); ?><em><%= v.common_price %></em></span></p>
				</a>
			</li>
			<% } %>
			</ul>
		</div>
    </script>
    <script type="text/html" id="goods_recommend_tpl">
        <ul>
            <% for (var i in rec_goods_list) { var g = rec_goods_list[i]; %>
            <li class="goods-item">
                <a href="product_detail.html?goods_id=<%= g.goods_id %>">
                    <div class="goods-item-pic">
                        <img alt="" src="<%= g.common_image %>"/>
                    </div>
                    <div class="goods-item-name one-overflow">
                        <%= g.common_name %>
                    </div>
                    <div class="goods-item-price"><?= __('￥'); ?><em><%= g.common_price %></em></div>
                </a>
            </li>
            <% } %>
        </ul>
    </script>
    <script type="text/html" id="newgoods_tpl">
		<div id="newgoods_next" class="grid pt-0 style-change">
			<% var goods_list = items; %>
			<% if(goods_list.length >0){%>
			<% for (var i in goods_list) {
				var v = goods_list[i];
			%>
				<% if(v.goods_addtime_text){ %>
					<p class="addtime" addtimetext='<%=v.common_add_time %>'>
						<span><time><%= v.goods_addtime_text %></time></span>
					</p>
					<ul class="custom-store-goods-list fz0 masonry">
						<%var y = v.good;%>
						<% if(y.length >0){%>
						<% for (var w in y) {%>
							<li class="item">
								<a class="pad iblock" href="product_detail.html?goods_id=<%= v.goods_id[0].goods_id %>">
									<div class="goods-pic">
										<img alt="" src="<%= v.common_image %>"/>
									</div>
									<dl class="goods-info relative">
										<dt class="goods-name">
												<h4> <%= v.common_name %></h4>
										</dt>
										<dd class="goods-sale">
											<p class="label">
												<% if (v.label_name_arr) { %>
				                                    <% for (var l in v.label_name_arr) { %>
				                                            <label class="label-item"><%= v.label_name_arr[l]%></label>
				                                    <% } %>
				                                <% } %>
											</p>
											<p>
												<span class="goods-price"><b>￥</b><em><%= v.common_price %></em>
												</span>
												<b class="had-sale">0人付款</b>
											</p>
										</dd>		   
									</dl>
								</a>
							</li>
						<% }} %>
					</ul>
				<% } %>
			<% } %>
		   <!-- <li class="loading">
				<div class="spinner"><i></i></div>
				<?= __('商品数据读取中'); ?>...
			</li> -->
			
			<% } else { %>
			<div class="nctouch-norecord search">
				<div class="norecord-ico"><i></i></div>
				<dl>
					<dt><?= __('商铺最近没有新品上架'); ?></dt>
					<dd><?= __('收藏店铺经常来逛一逛'); ?></dd>
				</dl>
			</div>
			<% } %>
		</div>
    </script>
    <script type="text/html" id="storeactivity_tpl">
			<% if(promotion.count){ %>
				<div class="customize-goods-lists">
				
					<div class="list style-change pt-0">
						<ul class="fz0 custom-store-goods-special masonry custom-store-activity-ul">
						<% if(promotion.mansong){ %>
							<%for(var k=0; k < promotion.mansong.length; k++){ var mansong = promotion.mansong[k];if(mansong.shop_id != 0){ %>
								<li class="item">
									<div class="pad">
										<span class="goods-pic">
											<a href="store_goods.html?shop_id=<%=shop_id %>">
												<img src="https://shops.look56.com/image.php/shop/data/upload/media/b54a7238a221685616d4fe794fa43b0b/10002/1/image/20200925/160100996855688270210436410088.jpg">
											</a>
										</span>
										<dl class="goods-info relative">
											<dt class="goods-name">
												<a href="store_goods.html?shop_id=<%=shop_id %>">
													<h4 class="one-overflow">
													<div>活动名称</div>
													<span>满即送	
													</span>
												</h4>
												</a>
											</dt>
											<dd class="goods-sale">
												<a href="store_goods.html?shop_id=<%=shop_id %>">
													<p class="custom-store-activity-time"><em class="time">活动时间： <%=mansong.start_time_text%>-<%=mansong.mansong_end_time%></em></p>
												</a>
											</dd>
										</dl>
								   </div>
								</li>
							<% }} %>
						<% } %>
						<% if(promotion.kanjia){ %>
							<%for(var k=0; k < promotion.kanjia.length; k++){var kanjia = promotion.kanjia[k]; if(kanjia.shop_id != 0){%>
								<li class="item">
									<div class="pad">
										<span class="goods-pic">
											<a href="store_goods.html?shop_id=<%=shop_id %>">
												<img src="<%=kanjia.goods_image%>">
											</a>
										</span>
										<dl class="goods-info relative">
											<dt class="goods-name">
												<a href="store_goods.html?shop_id=<%=shop_id %>">
													<h4 class="one-overflow">
														<div>活动名称</div>
														<span style="    display: inline-block;background-color: #ED5564;color: #FFF;">砍价砍价
														</span>
													</h4>
												</a>
											</dt>
											<dd class="goods-sale">
												<a href="store_goods.html?shop_id=<%=shop_id %>">
													<p class="custom-store-activity-time"><em class="time">活动时间：<%=kanjia.start_date%>-<%=kanjia.end_date%></em></p>
												</a>
											</dd>
										</dl>
								   </div>
								</li>	
							 <% }} %>
						 <% } %>
						 <% if(promotion.xianshi){%>
							 <% for(var k=0; k < promotion.xianshi.length; k++){var xianshi = promotion.xianshi[k]; if(xianshi.shop_id != 0){%>
								<li class="item">
									<div class="pad">
										<span class="goods-pic">
											<a href="store_goods.html?shop_id=<%=shop_id %>">
												<img src="<%=xianshi.goods_image%>">
											</a>
										</span>
										<dl class="goods-info relative">
											<dt class="goods-name">
												<a href="store_goods.html?shop_id=<%=shop_id %>&common_is_xian=1">
							 
													<h4 class="one-overflow">
														<div>活动名称</div>
														<span style="    display: inline-block;background-color: #ED5564;color: #FFF;">限时折扣	
														</span>
													</h4>
												</a>
											</dt>
											<dd class="goods-sale">
												<a href="store_goods.html?shop_id=<%=shop_id %>">
													<p class="custom-store-activity-time"><em class="time">活动时间：<%=xianshi.discount_start_time%>—<%=xianshi.discount_end_time%></em></p>
												</a>
											</dd>
										</dl>
								   </div>
								</li>	
							 <% }} %>
						<% } %>
        			</ul>
					</div>
        		
			    </div>

        <% }else{ %>
	       	<div class="nctouch-norecord search">
	            <div class="norecord-ico"><i></i></div>
	            <dl>
	                <dt><?= __('商铺最近没有促销活动'); ?></dt>
	                <dd><?= __('收藏店铺经常来逛一逛'); ?></dd>
	            </dl>
	        </div>
        <% } %>
    </script>
    <script type="text/html" id="storeactivity_tpl44">
		<div class="tc"><a class="custom-btn-store-activity" href="javascript:;">活动最低价</a></div>
		<div class="customize-goods-lists">
			<div class="list style-change">
				<ul class="fz0 custom-store-goods-special masonry">
					<li class="item">
						<div class="pad">
							<span class="goods-pic">
								<a href="product_detail.html?goods_id=7&amp;pos=1&amp;pos_page=product_list">
									<img src="https://shops.look56.com/image.php/shop/data/upload/media/b54a7238a221685616d4fe794fa43b0b/10002/1/image/20200925/160100996855688270210436410088.jpg">
								</a>
							</span>
							<dl class="goods-info relative">
								<dt class="goods-name">
									<a href="product_detail.html?goods_id=7&amp;pos=1&amp;pos_page=product_list">
										<h4 class="one-overflow">高昌贡2020新疆无核葡萄干500g吐鲁番特产玫瑰红香妃王</h4>
									</a>
								</dt>
								<dd class="goods-sale">
									<a href="product_detail.html?goods_id=7&amp;pos=1&amp;pos_page=product_list">
										<p class="label">
												<label class="label-item">舒适</label>
												<label class="label-item">干练</label>
										</p>
										<p class="custom-store-activity-pri">
											<span class="goods-price"><b>折扣价￥</b><em>34.80</em>
											</span>
											<b class="had-sale">原价￥<i class="through">168</i></b>
										</p>
										<p class="custom-store-activity-time"><em class="time">活动时间：9月1号—9月8号</em></p>
										<button class="custom-store-button">立即抢购</button>
									</a>
								</dd>
							</dl>
					   </div>
					</li>
				</ul>
			</div>
		</div>
        <% if(promotion.count){ %>
        <% if(promotion.mansong){ for(var k=0; k < promotion.mansong.length; k++){ var mansong = promotion.mansong[k];if(mansong.shop_id != 0){ %>
        <div class="store-sale-block">
            <a href="store_goods.html?shop_id=<%=shop_id %>">
                <div class="store-sale-tit">
                    <h3><%=mansong.mansong_start_time %></h3>
                    <time><?= __('活动时间：'); ?> <%=mansong.start_time_text%> <?= __('至'); ?> <%=mansong.mansong_end_time%>
                    </time>
                </div>
                <div class="sotre-sale-con">
                    <ul class="mjs">
                        <% for (var i in mansong.rule) { var rules = mansong.rule[i]; %>
                        <li><?= __('单笔订单消费满'); ?><em><?= __('¥'); ?><%=rules.rule_price %></em> <% if(rules.rule_discount) { %><?= __('，立减现金'); ?><em><?= __('¥'); ?><%=rules.rule_discount %></em> <% } %> <% if(rules.goods_id > 0) { %><?= __('，'); ?> <?= __('还可获赠品'); ?><img src="<%=rules.goods_image %>" alt="<%=rules.goods_name %>">&nbsp;<?= __('。'); ?> <% } %>
                        </li>
                        <% } %>
                    </ul>
                    <% if(mansong.mansong_remark){ %>
                    <p class="note"><?= __('活动说明：'); ?> <%=mansong.mansong_remark %>
                    </p>
                    <% } %>
                </div>
            </a>
        </div>
        <% }}} %>
        
        <% if(promotion.xianshi){ for(var k=0; k < promotion.xianshi.length; k++){var xianshi = promotion.xianshi[k]; if(xianshi.shop_id != 0){%>
	        <div class="store-sale-block">
	            <a href="store_goods.html?shop_id=<%=shop_id %>">
	                <div class="store-sale-tit">
	                    <h3><%=xianshi.discount_name %></h3>
	                    <time><?= __('活动时间：'); ?> <%=xianshi.discount_start_time%> <?= __('至'); ?> <%=xianshi.discount_end_time%>
	                    </time>
	                </div>
	                <div class="sotre-sale-con">
	                    <ul class="xs">
	                        <li><?= __('单件活动商品满'); ?><em><%=xianshi.discount_lower_limit %></em><?= __('件即可享受折扣价。'); ?></li>
	                    </ul>
	                    <% if(xianshi.discount_explain){ %>
	                    <p class="note"><?= __('活动说明：'); ?> <%=xianshi.discount_explain %>
	                    </p>
	                    <% } %>
	            </a>
	        </div>
        <% }}} %>
        
        <% }else{ %>
       <!-- <div class="nctouch-norecord search">
            <div class="norecord-ico"><i></i></div>
            <dl>
                <dt><?= __('商铺最近没有促销活动'); ?></dt>
                <dd><?= __('收藏店铺经常来逛一逛'); ?></dd>
            </dl>
        </div> -->
        <% } %>
    </script>
    <script type="text/html" id="store_voucher_con_tpl">
        <div class="nctouch-bottom-mask">
            <div class="nctouch-bottom-mask-bg"></div>
            <div class="nctouch-bottom-mask-block">
                <div class="nctouch-bottom-mask-tip"><i></i><?= __('点击此处返回'); ?></div>
                <div class="nctouch-bottom-mask-top store-voucher">
                    <i class="icon-store"></i><?= __('领取店铺代金券'); ?><a href="javascript:void(0);" class="nctouch-bottom-mask-close"><i></i></a>
                </div>
                <div class="nctouch-bottom-mask-rolling">
                    <div class="nctouch-bottom-mask-con">
                        <ul class="nctouch-voucher-list">
                            <% var voucher_list = voucher.items %> <% if(voucher_list.length > 0){ %> <% for (var i=0; i < voucher_list.length; i++) { var v = voucher_list[i]; %>
                            <li>
                                <dl>
                                    <dt class="money"><?= __('面额'); ?><em><%=v.voucher_t_price %></em><?= __('元'); ?></dt>
                                    <dd class="need"><?= __('需消费'); ?> <%=v.voucher_t_limit %><?= __('元使用'); ?></dd>
                                    <dd class="time"><?= __('至'); ?> <%=v.voucher_t_end_date %><?= __('前使用'); ?></dd>
                                </dl>
                                <a href="javascript:void(0);" nc_type="getvoucher" class="btn" data-tid="<%=v.voucher_t_id%>"><?= __('领取'); ?></a>
                            </li>
                            <% } %> <% }else{ %>
                            <div class="nctouch-norecord voucher" style="position: relative; margin: 3rem auto; top: auto; left: auto; text-align: center;">
                                <div class="norecord-ico"><i></i></div>
                                <dl style="margin: 1rem 0 0;">
                                    <dt style="color: #333;"><?= __('暂无代金券可以领取'); ?></dt>
                                    <dd><?= __('店铺代金券可享受商品折扣'); ?></dd>
                                </dl>
                            </div>
                            <% } %>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/common1.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/zepto.waypoints.js"></script>
    <script type="text/javascript" src="../js/ncscroll-load.js"></script>
	<script type="text/javascript" src="../js/swiper.min.js"></script>
	<script src="../js/waterfall-store.js?v=8121412"></script>
	<script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/store.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript">
        var level = getQueryString('level');
        var shop_id = getQueryString('shop_id');
            addCookie('shop_search',shop_id);
        if(level){
            addCookie('SHOP_ID_WAP',shop_id);
        }
    </script>

<script type="text/html" id="shop_footer">
    <div class="footer bort1 " id="footer-template-bort1">
    <ul class="clearfix">
        <li class="active">
            <a href="store.html?shop_id=<%=data %>">
                <i class="iconfont icon-home-active"></i>
                <h3>首页</h3>
            </a>
        </li>
        <li>
            <a href="shop_goods_cat.html?shop_id=<%=data %>&mb=shop&style=">
                <i class="iconfont icon-class1"></i>
                <h3>分类</h3>
            </a>
        </li>
        <li>
            <a id="store_voucher" href="javascript:void(0)">
                <i class="iconfont icon-voucher"></i>
                <h3>领券</h3>
            </a>
        </li>
        <li>
            <a href="javascript: void(0);"  id="store_kefu">
                <i class="iconfont icon-kefu1" ></i>
                <h3>联系客服</h3>
            </a>
        </li>
        <li>
            <a href="member/member.html">
                <i class="iconfont icon-mine"></i>
                <h3>个人中心</h3>
            </a>
        </li>
    </ul>
    </div>
</script>

    </body>
    </html>
<?php
    include __DIR__ . '/../includes/footer.php';
?>



