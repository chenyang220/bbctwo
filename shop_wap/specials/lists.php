<?php
    include '/../includes/header.php';
?>
<!DOCTYPE html>
<html class="bgf">
<head>
    <meta charset="UTF-8">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="msapplication-tap-highlight" content="no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="Author" contect="U2FsdGVkX1+liZRYkVWAWC6HsmKNJKZKIr5plAJdZUSg1A==">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit:cover;">
    <title><?= __('商品列表'); ?></title>
    <link rel="stylesheet" type="text/css" href="../css/base.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../css/customize.css">
	<link rel="stylesheet" href="../css/iconfont.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_products_list.css?v=9911">
    <style type="text/css">
        .nctouch-main-layout-a {
            top: 0;
        }
        .secreen-layout .bottom {
            padding: 0.5rem 0;
        }
        .reset {
            background: #70696a;
        }
        
    </style>
</head>
<body class="bgf">
    <div class="customize-feature-page-head clearfix lists">
    	<div class="customize-feature-page-input to-search">
    		<i class="iconfont icon-search"></i>
    		<input class="placeholder-c1" type="text" placeholder="搜你想搜的" readonly>
    	</div>
		<b class="zk-menu" id="menuChange"></b>
    </div>

<!--<?= __('排序条件'); ?>-->
<div class="customize-feature-goods-list-nav">
    <ul id="nav_ul">
        <li><a href="javascript:void(0);" class="current" id="sort_default"><?= __('综合'); ?><i></i></a></li>
        <li><a href="javascript:void(0);" class="" onclick="init_get_list('sale', 'DESC')"><?= __('销量'); ?></a></li>
		<li><a href="javascript:void(0);" class="">价格</a></li>
        
    </ul>
    <div class="ser-adv flex1"><a href="javascript:void(0);" id="search_adv"><?= __('筛选'); ?><i></i></a></div>
</div>

<div id="sort_inner" class="goods-sort-inner hide">
    <span><a href="javascript:void(0);" class="cur" onclick="init_get_list('', '')"><?= __('综合'); ?><i></i></a></span> <span><a href="javascript:void(0);" onclick="init_get_list('evaluate', 'DESC')"><?= __('评价排序'); ?><i></i></a></span> <span><a href="javascript:void(0);" onclick="init_get_list('price', 'DESC')"><?= __('价格从高到低'); ?><i></i></a></span> <span><a href="javascript:void(0);" onclick="init_get_list('price', 'ASC')"><?= __('价格从低到高'); ?><i></i></a></span>
</div>

<div class="customize-feature-goods-list-contents customize-goods-lists bgf">
    <div id="product_list" class="style-change list">
        <ul class="goods-search-list masonry"></ul>
    </div>
</div>
<!--<?= __('筛选部分'); ?>-->
<div class="nctouch-full-mask hide JS-search">
    <div class="nctouch-full-mask-bg"></div>
    <div class="nctouch-full-mask-block">
        <div class="header" style="display: none;">
            <div class="header-wrap">
                <div class="header-l"><a href="javascript:void(0);"><i class="back"></i></a></div>
                <div class="header-title">
                    <h1><?= __('商品筛选'); ?></h1>
                </div>
                <div class="header-r"><a href="javascript:void(0);"class="text reset"><?= __('重置'); ?></a></div>
            </div>
        </div>
		
        <div class="nctouch-main-layout-a secreen-layout" id="list-items-scroll">
        </div>
    </div>
</div>

<div class="fix-block-r">
    <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
</div>

<footer id="footer" class="bottom"></footer>

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
                <!-- <a href="javascript:void(0);" nctype="items" id="plus"><?= __('PLUS专享'); ?></a> -->
            </dd>
        </dl>
        <dl>
            <dt><?= __('服务'); ?></dt>
            <dd>
                <a href="javascript:void(0);" nctype="items" id="priority"><?= __('有货优先'); ?></a>
            </dd>
        </dl>
		<dl>
			<dt>距离</dt>
			<dd>
				<a href="javascript:void(0);" nctype="items" id="distance">近距优先</a>
			</dd>
		</dl>
        <dl>
            <dt><?= __('价格区间'); ?></dt>
            <dd>
                    <span class="inp-balck">
                        <input type="text" id="price_from" nctype="price" pattern="[0-9]*"
                               class="inp" placeholder="<?= __('最低价'); ?>"/></span> <span class="line"></span>
                <span class="inp-balck"><input nctype="price" type="text" id="price_to" pattern="[0-9]*" class="inp"
                                                                                                                                placeholder="<?= __('最高价'); ?>"/></span>
            </dd>
        </dl>
        <dl class="borb0">
            <dt><?= __('品牌'); ?></dt>
            <dd>
                <div id="goods_brands"></div>
                <a href="javascript:void(0);" nctype="items" class="brands_more"><?= __('查看全部'); ?></a>
            </dd>
        </dl>
        <div class="custom-bottom tc">
            <a href="javascript:void(0);" class="custom-btn reset" id="reset"><?= __('重置'); ?></a>
        </div>
        <div class="custom-bottom tc">
            <a href="javascript:void(0);" class="custom-btn search_submit"><?= __('筛选'); ?></a>
        </div>
    </div>
</script>
<!--<?= __('筛选部分'); ?>-->
</body>
<script type="text/html" id="goods_brand">
    <% if(goods_brand.length>0){%>
    <%for(i=0;i< goods_brand.length;i++){%>
        <a href="javascript:void(0);" nctype="items" class="brand_ids" value="<%=goods_brand[i].brand_id%>"><%=goods_brand[i].brand_name%></a>
    <%}}%>
</script>
<script type="text/html" id="home_body">
    <% var common_list = data.items; %>
    <% if(common_list.length >0){%>
    <%for(j=0;j < common_list.length;j++){%>
    <%  var goods_list = common_list[j].good; var pos = (data.page-1)*pagesize+1+j;%>
    <% if(typeof(goods_list)!=='undefined' && goods_list.length >0){%>
    <%for(i=0;i<1;i++){%>
    <li class="item" goods_id="<%=goods_list[i].goods_id;%>" id="goods_pos_<%=pos;%>">
		<div class="pad">
				<span class="goods-pic">
					<a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
                        <img src="<%= image_cdn(goods_list[i].goods_image); %>"/>
                        <% if(common_list[j].promotion_type=='presale'){%>
                        <b class="presale-list-logs">预售</b>
                        <% } %>
                    </a>
				</span>
			<dl class="goods-info relative">
				<dt class="goods-name">
                    <% if(common_list[j].third_url){%>
<a href="javascript:void(0);"  onClick="urlu('<%=common_list[j].third_url%>');">
                    <% } else {%>
                    <a href="<%=data.label_url%>.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
                    <% } %>
					

						<h4><%=goods_list[i].goods_name;%></h4>
						<h6><%=goods_list[i].goods_jingle;%></h6>
					</a>
				</dt>

				<dd class="goods-sale">
                               <% if(common_list[j].third_url){%>
<a href="javascript:void(0);"  onClick="urlu('<%=common_list[j].third_url%>');">
                    <% } else {%>
                                <a href="<%=data.label_url%>.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
                    <% } %>
	
					<% if(common_list[j].label_name){%>
					<p class="label">

						<%for(label_id in common_list[j].label_name){%>
                    	<label class="label-item"><%=common_list[j].label_name[label_id]%></label>
                    	<% } %>
				    </p>                    	
                    <% } %>
					<p>
						<span class="goods-price"><b><?= __('￥'); ?></b><em><%=goods_list[i].goods_price;%></em>
							<%
								if (goods_list[i].sole_flag) {
							%>
								<span class="phone-sale"><i></i><?= __('手机专享'); ?></span>
							<%
								}
							%>
						</span>
						<b class="had-sale"><%=common_list[j].common_salenum;%>人付款</b>
					</p>
								
						
						<% if (goods_list[i].is_virtual == '1') { %> <span class="sale-type"><?= __('虚拟'); ?></span> <% } else { %> <% if (goods_list[i].is_presell == '1') { %> <span class="sale-type"><?= __('预'); ?></span> <% } %> <% if (goods_list[i].is_fcode == '1') { %> <span class="sale-type">F</span> <% } %> <% } %>
						
						<% if(goods_list[i].group_flag || goods_list[i].xianshi_flag){ %> <span class="sale-type"><?= __('降'); ?></span> <% } %> <% if(goods_list[i].have_gift == '1'){ %> <span class="sale-type"><?= __('赠'); ?></span> <% } %> </a>
						<!-- 3.6.7-plus会员价 -->
						<%if(common_list[j].plus_status){%>
						 <em class="plus-pri"><?= __('￥'); ?><%=common_list[j].plus_price;%></em><b class="plus-logo"></b>
						<%}%>
				</dd>
				<dd class="goods-assist fz0">
					<a href="../tmpl/store<%= common_list[j].shop_wap_index == 1 ? '' :common_list[j].shop_wap_index %>.html?shop_id=<%=common_list[j].shop_id %>">
						<span><%=common_list[j].shop_name;%></span><i class="iconfont icon-arrow-right"></i>
					</a>
					<!-- <a href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>&pos=<%=pos%>&pos_page=product_list">
						<span class="goods-sold"><?= __('销量'); ?>
							<em><%=common_list[j].common_salenum;%></em>
						</span>
						<span class="goods-sold"><?= __('评论'); ?>
							<em><%=common_list[j].common_evaluate;%></em>
						</span>
					</a> -->
					<!--<div class="goods-store">
						<% if (goods_list[i].is_own_shop == '1') { %> <span class="mall"><?= __('自营'); ?></span> <% } else { %>
						<a href="javascript:void(0);" data-id='<%=goods_list[i].shop_id;%>'><%=goods_list[i].store_name;%><i></i></a> <% } %>
						<div class="sotre-creidt-layout" style="display: none;"></div>
					</div>-->
				</dd>
			   <% if (common_list[j].common_is_directseller == '1' && data.distributor_open==1) { %>
				 <span class="make_money">
					<li>分享赚</li>
					<% if (data.distributor_type==1) { %>
						<li>￥<%=common_list[j].common_a_first%></li>
					<% }else{ %>
						<li>￥<%=common_list[j].common_c_first%></li>
					<% } %>
				</span>
				<% } %>
			</dl>
       </div>
    </li><%}%><%}%><% } %>
   <!-- <li class="loading">
        <div class="spinner"><i></i></div>
        <?= __('商品数据读取中'); ?>...
    </li> -->
	<%}else {%>
    <div class="nctouch-norecord search">
        <div class="norecord-ico"><i></i></div>
        <dl>
            <dt><?= __('没有找到任何相关信息'); ?></dt>
            <dd><?= __('选择或搜索其它商品分类'); ?>/<?= __('名称'); ?>...</dd>
        </dl>
        <a href="javascript:history.go(-1)" class="btn"><?= __('重新选择'); ?></a>
    </div><%}%>
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
                    <a href="javascript:void(0);" class="btn-l search_submit"><?= __('筛选'); ?></a>
                </div>
			</div>
			<div class="sort-item-li">
                <div class="sort-item-li-tj"></div>
                <div class="bottom">
                    <a href="javascript:void(0);" class="btn-l reset"><?= __('重置');?></a>
                </div>
                <div class="bottom">
                    <a href="javascript:void(0);" class="btn-l search_submit"><?= __('筛选');?></a>
                </div>
		</div>
		</div>
	</div>
	
</script>
<script src="../js/waterfall.js?v=8"></script>
<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/product_list.js?v=9"></script>
<!--<script type="text/javascript" src="../js/footer.js"></script>-->
<script type="text/javascript" src="../js/animation.js?v=8"></script>
</html>
<?php
    include  '/../includes/footer.php';
?>
<script type="text/javascript">
    function urlu(url1) {
       window.location.href=url1;
    }
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
</script>