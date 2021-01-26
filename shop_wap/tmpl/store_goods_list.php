<script>
    window.load = loadCss(WapSiteUrl+"/css/nctouch_products_list.css?v=911");
    window.load = loadCss(WapSiteUrl+"/css/nctouch_common.css");
</script>
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
</style>
<?php
include __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="../css/customize.css">
<div class="customize-feature-goods-list-nav store-goods-list-nav">
    <ul id="nav_ul">
        <li><a href="javascript:void(0);" class="current" id="sort_default"><?= __('综合'); ?><i></i></a></li>
        <li><a href="javascript:void(0);" id="sort_salesnum"><?= __('销量'); ?></a></li>
		<li><a href="javascript:void(0);" class="">价格</a></li>
    </ul>
    <div class="ser-adv flex1"><a href="javascript:void(0);" id="search_adv">筛选<i></i></a></div>
</div>
<div id="sort_inner" class="goods-sort-inner hide borb1">
    <span><a href="javascript:void(0);" class="cur" id="default"><?= __('综合排序'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" id="pricedown"><?= __('价格从高到低'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" id="priceup"><?= __('价格从低到高'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" id="collect"><?= __('人气排序'); ?><i></i></a></span>
</div>
<div class="customize-goods-lists">
	<div class="grid pt-0 style-change" nc_type="product_content">
	    <ul class="masonry" id="product_list"></ul>
	</div>
</div>
<div class="nctouch-full-mask hide">
    <div class="nctouch-full-mask-bg"></div>
    <div class="nctouch-full-mask-block">
        <div class="header">
            <div class="header-wrap">
                <!-- <div class="header-l"> <a href="javascript:void(0);"><i class="back"></i></a></div> -->
                <div class="header-title">
                    <h1><?= __('商品筛选'); ?></h1>
                </div>
                <div class="header-r"><a href="javascript:void(0);" id="reset" class="text"><?= __('重置'); ?></a> </div>
            </div>
        </div>
        <div class="nctouch-main-layout secreen-layout" id="list-items-scroll">
            <dl>
                <dt><?= __('价格区间'); ?></dt>
                <dd>
                    <span class="inp-balck"><input type="text" id="price_from" nctype="price" pattern="[0-9]*" class="inp" placeholder="<?= __('最低价'); ?>"/></span><span class="line"></span><span class="inp-balck"><input nctype="price" type="text" id="price_to" pattern="[0-9]*" class="inp" placeholder="<?= __('最高价'); ?>"/></span>
                </dd>
            </dl>
            <div class="bottom"> <a href="javascript:void(0);" class="btn-l" id="search_submit"><?= __('筛选商品'); ?></a> </div>
        </div>
    </div>
</div>

<script type="text/html" id="goods_list_tpl">
    <% var goods_list = data.items; %>
    <% if(typeof(goods_list)!=='undefined' && goods_list.length >0){%>
        <% for (var k in goods_list) { var v = goods_list[k];%>
        <li class="item" goods_id="<%=v.goods_id[0].goods_id;%>">
			<div class="pad">
				<span class="goods-pic">
					<a href="product_detail.html?goods_id=<%=v.goods_id[0].goods_id;%>">
						<img src="<%=v.common_image;%>"/>
					</a>
				</span>
				<dl class="goods-info">
					<dt class="goods-name">
						<a href="product_detail.html?goods_id=<%=v.goods_id[0].goods_id;%>">
							<h4><%=v.common_name;%></h4><h6></h6>
						</a>
					</dt>
					<dd class="goods-sale">
						<a href="product_detail.html?goods_id=<%=v.goods_id[0].goods_id;%>">
							<p class="label">
                                <% if (v.label_name_arr) { %>
                                    <% for (var l in v.label_name_arr) { %>
                                            <label class="label-item"><%= v.label_name_arr[l]%></label>
                                    <% } %>
                                <% } %>
                            </p>
							<p>
								<span class="goods-price"><?= __('￥'); ?><em><%=v.common_price;%></em>
									<% if (v.sole_flag) {%>
										<span class="phone-sale"><i></i><?= __('手机专享'); ?></span>
									<% } %>
								</span>
								<b class="had-sale"><%=v.common_salenum;%>人付款</b>
								<% if (v.common_is_virtual == '1') { %>
									<span class="sale-type"><?= __('虚'); ?></span>
								<% } else { %>
									<% if (v.is_presell == '1') { %>
										<span class="sale-type"><?= __('预'); ?></span>
									<% } %>
									<% if (v.is_fcode == '1') { %>
										<span class="sale-type">F</span>
									<% } %>
								<% } %>
								
								<% if(v.group_flag || v.xianshi_flag){ %>
								<span class="sale-type"><?= __('降'); ?></span>
								<% } %>
							</p>
							
						</a>
					</dd>
					
				</dl>
			</div>
        </li>
        <%}%>
    <% }else { %>
    <div class="nctouch-norecord search">
        <div class="norecord-ico"><i></i></div>
        <dl>
            <dt><?= __('没有找到任何相关信息'); ?></dt>
            <dd><?= __('搜索其它商品名称或筛选项'); ?>...</dd>
        </dl>
        <a href="javascript:void(0);" onclick="get_list({'order_val':'<%=order%>','order_key':'<%=key%>'},true)" class="btn"><?= __('查看全部商品'); ?></a>
    </div>
    <% } %>
</script>

<script>
    window.load = loadJs(WapSiteUrl+"/js/tmpl/store_goods_list.js");
</script>