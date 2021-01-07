<?php
include __DIR__.'/../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title><?= __('选择门店'); ?></title>
	<link rel="stylesheet" href="../css/base.css">
	<link rel="stylesheet" href="../css/swiper.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_common.css">

    <script type="text/javascript" src="../js/zepto.min.js"></script>

    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/swipe.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/iscroll.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/fly/requestAnimationFrame.js"></script>
    <script type="text/javascript" src="../js/fly/zepto.fly.min.js"></script>
    <script type="text/javascript" src="../js/ziti.js"></script>
    <script type="text/javascript" src="../js/jquery.timeCountDown.js" ></script>
</head>
<body>
	<header id="header" class="fixed">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:history.go(-1)"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1><?= __('选择门店'); ?></h1>
            </div>
        </div>
    </header>
    <div class="nctouch-main-layout">
<!--        <div class="style-layout-lr bgf borb1 mb-20 relatives">-->
<!--            <span class="fz-28 col5 pad30"><?= __('选择门店地址'); ?></span>-->
<!--            <input value="" readonly style="border:none" id="area">-->
<!--            <input name="area_info" type="text" class="inp wp100" id="area_info" autocomplete="off" unselectable="on" onfocus="this.blur()" readonly/>-->
<!--            <i class="icon-arrow-r mr-30 mt-30"></i>-->
<!--        </div>-->
        <!-- <?= __('有门店'); ?> -->
        <div id="chain-list" class="fz-0">
        </div>
        <script type="text/html" id="chainList">
            <% if (chain_rows.length > 0) { %>
                <% for(var i = 0; i < chain_rows.length; i++) { %>
               <div class="bgf clearfix ziti-store-addr posr">
                    <div class="fl fz-28 col5">
                        <dl class="clearfix">
                            <dt class="fl"><?= __('店铺名称：'); ?></dt><dd class="z-dhwz fl" style="width: 8rem;"><%= chain_rows[i].chain_name %></dd>
                        </dl>
                        <dl>
                            <dt><?= __('联系电话：'); ?></dt><dd><%= chain_rows[i].chain_mobile %></dd>
                        </dl>
                        <dl class="z-chain-list-dhyc mb-0 mt-20">
                            <dd><%= chain_rows[i].chain_province %> <%= chain_rows[i].chain_city %> <%= chain_rows[i].chain_county %> <%= chain_rows[i].chain_address %></dd>
                        </dl>
                    </div>
                    <div class="fr ziti-btn-area">
                        <div class="top-50">
                            <% if(chain_rows[i].goods_stock==0){%>
                            <a href="javascript:;" class="fz-24 btn-ziti no" onclick="confirm(<%=chain_rows[i].chain_id%>,<%=chain_rows[i].goods_stock%>,<%=is_delivery%>)">
                                <% if(is_delivery ==1){%>
                                    <?= __('马上配送'); ?>
                                <%}else{%>
                                    <?= __('马上自提'); ?>
                                <% } %>
                            </a>
                            <%}else{%>
                            <a href="javascript:;" class="fz-24 btn-ziti" onclick="confirm(<%=chain_rows[i].chain_id%>,<%=chain_rows[i].goods_stock%>,<%=is_delivery%>)">
                                <% if(is_delivery ==1){%>
                                <?= __('马上配送'); ?>
                                <%}else{%>
                                <?= __('马上自提'); ?>
                                <% } %>
                            </a>
                            <% } %>

                        	<p class="fz-24 mt-25"><% if(chain_rows[i].goods_stock==0){%><?= __('已售馨'); ?><%}else{%><em class="col9"><?= __('仅剩'); ?>:</em><%=chain_rows[i].goods_stock %><?= __('件'); ?><% } %></p>
                        </div>
                    </div>
                </div>
                <% } %>
            <% } %>
        </script>
        <!-- <?= __('无门店可自提'); ?> -->
         <div class="norecord tc js-none" style="display: none;">
		            <div class="ziti-store">
		                <i></i>
		            </div>
		            <p class="fz-30 col9"><?= __('该地区无门店'); ?></p>
		  </div>
    </div>
    
</body>
</html>
<?php
include __DIR__.'/../includes/footer.php';
?>