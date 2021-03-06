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
.reset {
    background: #70696a;
}
    .sj {

    border-color: #AAA transparent transparent transparent;

}
</style>
<?php
include __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="../css/customize.css">
<div class="customize-feature-goods-list-nav store-goods-list-nav">
    <ul id="nav_ul">
        <li><a href="javascript:void(0);" class="current" id="sort_default"><?= __('综合'); ?><i class="sj"></i></a></li>
        <li><a href="javascript:void(0);" id="sort_salesnum"><?= __('销量'); ?></a></li>
		<li><a href="javascript:void(0);" id="sort_prices">价格</a></li>
    </ul>
    <div class="ser-adv flex1"><a href="javascript:void(0);" id="search_adv">筛选<i></i></a></div>
</div>
<div id="sort_inner" class="goods-sort-inner hide borb1">
    <span><a href="javascript:void(0);" class="cur" id="default"><?= __('综合排序'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" id="collect"><?= __('人气排序'); ?><i></i></a></span>
</div>
<div id="sort_price" class="goods-sort-inner hide borb1"  style="position: static;">
<!--     <span><a href="javascript:void(0);" id="pricedown_price"><?= __('价格从高到低'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" id="priceup_price"><?= __('价格从低到高'); ?><i></i></a></span> -->
        <span><a href="javascript:void(0);" id="pricedown"><?= __('价格从高到低'); ?><i></i></a></span>
    <span><a href="javascript:void(0);" id="priceup"><?= __('价格从低到高'); ?><i></i></a></span>
</div>
<div class="customize-goods-lists">
	<div class="grid pt-0 style-change 1111" nc_type="product_content">
	    <ul class="masonry" id="product_list"></ul>
	</div>
</div>
<div class="nctouch-full-mask hide">
    <div class="nctouch-full-mask-bg"></div>
    <div class="nctouch-full-mask-block">
        <div class="header hide">
            <div class="header-wrap">
                <div class="header-l"> <a href="javascript:void(0);" onclick="back()"><i class="back"></i></a></div>
                <div class="header-title">
                    <h1><?= __('商品筛选'); ?></h1>
                </div>
                <div class="header-r"><a href="javascript:void(0);" id="reset" class="text"><?= __('重置'); ?></a> </div>
            </div>
        </div>
        <div class="nctouch-main-layout secreen-layout" id="list-items-scroll" style="    height: 80%;
    overflow-y: auto;">
  
        </div>
    </div>
</div>

<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9&callback=baidu_lbs_geo"></script>
<script type="text/html" id="search_items">
    <div class="search_items">
                  <dl>
                <dt><?= __('店铺类型'); ?></dt>
                <dd>
                    <a href="javascript:void(0);" nctype="items" id="own_shop" class=""><?= __('平台自营'); ?></a>
                    <a href="javascript:void(0);" nctype="items" id="other_shop" class=""><?= __('入驻店铺'); ?></a>
                </dd>
            </dl>
            <dl>
                <dt><?= __('商品类型'); ?></dt>
                <dd>
                    <a href="javascript:void(0);" nctype="items" id="actgoods"><?= __('促销'); ?></a>
                    <a href="javascript:void(0);" nctype="items" id="virtual"><?= __('虚拟'); ?></a>
                </dd>
            </dl>
            <dl>
                <dt><?= __('服务'); ?></dt>
                <dd>
                    <a href="javascript:void(0);" nctype="items" id="priority"><?= __('有货优先'); ?></a>
                </dd>
            </dl>
            <dl>
                <dt><?= __('距离'); ?></dt>
                <dd>
                    <a href="javascript:void(0);" nctype="items" id="distance"><?= __('近距优先'); ?></a>
                </dd>
            </dl>
            <dl>
                <dt><?= __('价格区间'); ?></dt>
                <dd>
                        <span class="inp-balck">
                            <input type="text" id="price_from" nctype="price" pattern="[0-9]*"
                                   class="inp" placeholder="<?= __(''); ?>"/></span> <span class="line"></span>
                    <span class="inp-balck"><input nctype="price" type="text" id="price_to" pattern="[0-9]*" class="inp"
                        placeholder="<?= __(''); ?>">
                    </dd>
            </dl>
            <dl class="borb0">
                <dt><?= __('品牌'); ?></dt>
                <dd>
                    <div id="goods_brands"></div>
                    <a href="javascript:void(0);" nctype="items" class="brands_more"><?= __('查看全部'); ?></a>
                </dd>
            </dl>
           <!--  <dl>
                <dt><?= __('价格区间'); ?></dt>
                <dd>
                    <span class="inp-balck"><input type="text" id="price_from" nctype="price" pattern="[0-9]*" class="inp" placeholder="<?= __('最低价'); ?>"/></span><span class="line"></span><span class="inp-balck"><input nctype="price" type="text" id="price_to" pattern="[0-9]*" class="inp" placeholder="<?= __('最高价'); ?>"/></span>
                </dd>
            </dl> -->
            <!-- <div class="bottom"> <a href="javascript:void(0);" class="btn-l" id="search_submit"><?= __('筛选商品'); ?></a> </div> -->
            <div class="custom-bottom tc">
                <a href="javascript:void(0);" class="custom-btn reset" id="reset"><?= __('重置'); ?></a>
            </div>
            <div class="custom-bottom tc">
                <a href="javascript:void(0);" class="custom-btn search_submit"><?= __('筛选'); ?></a>
            </div>
    </div>
</script>
<!-- 品牌查看全部 -->
<script type="text/html" id="brands_more">
    <div class="goods-brands-box hp100">
        <p class="goods-brands-sel">已选品牌:</p>
        <ul class="goods-brands-sort clearfix borb1">
            <li class="active"><span>字母排序</span></li>
            <li><span>推荐排序</span></li>
        </ul>
        <div class="sorts-items overflow-auto">
            <div class="sort-item-li  active">
            <p class="sort-href">
                <a class="active" href="#A">A</a>
                <?php $letters = range('B', 'Z');
                    foreach ($letters as $letter) { ?>
                         <a href="#<?php echo $letter; ?>"><?php echo $letter;?></a>
                 <?php } ?>
            </p>

            <div class="sort-item-li-zm"></div>
                <div class="bottom">
                    <a href="javascript:void(0);" class="btn-l reset"><?= __('重置'); ?></a>
                </div>
                <div class="bottom">
                    <a href="javascript:void(0);" class="btn-l search_submit p_list"><?= __('筛选'); ?></a>
                </div>
            </div>
            <div class="sort-item-li">
                <div class="sort-item-li-tj"></div>
                <div class="bottom">
                    <a href="javascript:void(0);" class="btn-l reset"><?= __('重置');?></a>
                </div>
                <div class="bottom">
                    <a href="javascript:void(0);" class="btn-l search_submit p_list"><?= __('筛选');?></a>
                </div>
        </div>
        </div>
    </div>
    
</script>
<script type="text/html" id="goods_brand">
    <% if(goods_brand.length>0){%>
    <%for(i=0;i< goods_brand.length;i++){%>
        <a href="javascript:void(0);" nctype="items" class="brand_ids" value="<%=goods_brand[i].brand_id%>"><%=goods_brand[i].brand_name%></a>
    <%}}%>
</script>
<!-- 查看全部品牌[按照字母排序] -->
<script type="text/html" id="goods_brand_all">
    <%for(j=0;j< brand_info.length;j++){ var brand = brand_info[j] %>
        <% if(brand.length > 0){%>
        <dl class="goods-brands-dl">
            <dt><a id="<%=brand[0].brand_initial%>"><%=brand[0].brand_initial%></a></dt>
            <%for(i=0;i< brand.length;i++){%>
                <dd>
                    <p><input type="checkbox" class="brand_name_id" value="<%=brand[i].brand_id%>"><em><%=brand[i].brand_name%></em></p>
                </dd>
            <%} %>
        </dl>
        <%}%>
    <%}%>
</script>
<script type="text/html" id="goods_brands_all">
    <%for(j=0;j< goods_brands_all.length;j++){ var brand = goods_brands_all[j] %>
        <% if(brand.length > 0){%>
        <dl class="goods-brands-dl">
            <dt><a id="<%=brand[0].brand_initial%>"><%=brand[0].brand_initial%></a></dt>
            <%for(i=0;i< brand.length;i++){%>
                <dd>
                    <p><input type="checkbox" class="brand_name_id" value="<%=brand[i].brand_name%>"><em><%=brand[i].brand_name%></em></p>
                </dd>
            <%} %>
        </dl>
        <%}%>
    <%}%>
</script>
<script type="text/html" id="goods_list_tpl">
    <% var goods_list = data.items; %>
    <% if(typeof(goods_list)!=='undefined' && goods_list.length >0){%>
        <% for (var k in goods_list) { var v = goods_list[k];%>
        <li class="item" goods_id="<%=v.goods_id[0].goods_id;%>">
			<div class="pad">
				<span class="goods-pic">
					<a href="product_detail.html?goods_id=<%=v.goods_id[0].goods_id;%>">
						<img src="<%=v.common_image;%>" />
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
        var arrylist = [];
    var brandStr = '';
   $(document).on('click','.brand_name_id',function(){
        var val = $(this).next().text();
        if($(this).prop('checked')){  //选中
            arrylist.push(val)
        }else{ //取消选中
            removeByVal(arrylist , val)
        }
        var size = arrylist.length;
        if(size <= 4){
           brandStr = '已选品牌:' + arrylist.join("、");
        }else{
           const spliceArr = arrylist.slice(0,3);
           brandStr = '已选品牌:' + spliceArr.join("、") + '...等' + size + '个';
        }
        $('.goods-brands-sel').text(brandStr);
    })

   function removeByVal(arrylist , val) {
    for(var i = 0; i < arrylist .length; i++) {
        if(arrylist [i] == val) {
            arrylist .splice(i, 1);
            break;
        }
    }
}

    window.load = loadJs(WapSiteUrl+"/js/tmpl/store_goods_list.js?v=4412");
</script>