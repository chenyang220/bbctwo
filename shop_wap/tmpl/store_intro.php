<?php 
include __DIR__.'/../includes/header.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('店铺介绍'); ?></title>
    <link rel="stylesheet" type="text/css" href="../css/base.css">
    <link rel="stylesheet" type="text/css" href="../css/nctouch_store.css">
</head>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script>
<script type="text/javascript">
    var url = '../store_intro/store_intro?shop_id='+<?php echo $_GET['shop_id']?>; 
    wx.miniProgram.redirectTo({url:url})
</script>
<body>
    <header id="header">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:history.go(-1);"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1><?= __('店铺介绍'); ?></h1>
            </div>
            <!-- <div class="header-r"> <a href="javascript:void(0);" id="header-nav"><i class="more"></i><sup></sup></a> </div> -->
        </div>
        <div class="nctouch-nav-layout">
            <div class="nctouch-nav-menu"> <span class="arrow"></span>
                <ul>
                    <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                        <li><a href="../tmpl/store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../tmpl/store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }else{ ?>
                        <li><a href="../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../tmpl/search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }?>
                    <li><a href="../tmpl/cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup></sup></a></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="nctouch-main-layout fixed-Width">
        <div class="" id="store_intro"> </div>
    </div>
    <div class="fix-block-r">
        <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
    </div>
</body>
<script type="text/html" id="store_intro_tpl">
    <div class="nctouch-store-info">
        <div class="store-avatar"><img src="<%= store_info.wap_shop_logo %>" /></div>
        <dl class="store-base">
            <dt><%= store_info.shop_name %></dt>
            <dd class="type">
                <% if(store_info.shop_self_support == 'false'){%><?= __('普通店铺'); ?>
                    <% }else{%>
                    <?= __('平台自营'); ?>
                <% } %>
            </dd>
        </dl>
        <div class="store-collect">
            <a href="javascript:void(0);" id="store_collected"><?= __('已收藏'); ?></a>
            <a href="javascript:void(0);" id="store_notcollect"><?= __('收藏'); ?></a>
            <p>
                <input type="hidden" id="store_favornum_hide" value="<%= store_info.shop_collect %>" />
                <em id="store_favornum"><%= store_info.shop_collect %></em><?= __('粉丝'); ?></p>
        </div>
    </div>
    <% if(!store_info.shop_self_support){%>
        <div class="nctouch-store-block pl-20 pr-20">
            <ul class="credit pb-20">
                <li>
                    <!-- span <?= __('样式名称可以是'); ?>high<?= __('、'); ?>equal<?= __('、'); ?>low -->
                    <h4><?= __('描述相符'); ?></h4>
                    <span class="">
					<strong><%= store_info.shop_desc_scores %></strong>
					<% if(store_info.com_desc_scores == 0){%>
					<?= __('与同行业持平'); ?>
					<% }else{ %>
					<% if (store_info.com_desc_scores > 0){ %> <%= '<?= __('高于'); ?>' %> <%}else{%><%= '<?= __('低于'); ?>' %><%}%><?= __('同行业'); ?>
					<% } %>
					<em><%= store_info.com_desc_scores %></em>
				</span>
                </li>
                <li>
                    <h4><?= __('服务态度'); ?></h4>
                    <span class="">
					<strong><%= store_info.shop_service_scores %></strong>
					<% if(store_info.com_service_scores == 0){%>
					<?= __('与同行业持平'); ?>
					<% }else{ %>
					<% if (store_info.com_service_scores > 0){ %> <%= '<?= __('高于'); ?>' %> <%}else{%><%= '<?= __('低于'); ?>' %><%}%><?= __('同行业'); ?>
					<% } %>
					<em><%= store_info.com_service_scores %></em>
				</span>
                </li>
                <li>
                    <h4><?= __('物流服务'); ?></h4>
                    <span class="">
					<strong><%= store_info.shop_send_scores %></strong>
					<% if(store_info.com_send_scores == 0){%>
					<?= __('与同行业持平'); ?>
					<% }else{ %>
					<% if (store_info.com_send_scores > 0){ %> <%= '<?= __('高于'); ?>' %> <%}else{%><%= '<?= __('低于'); ?>' %><%}%><?= __('同行业'); ?>
					<% } %>
					<em><%= store_info.com_send_scores %></em>
				</span>
                </li>
            </ul>
        </div>
        <% } %>
            <div class="nctouch-store-block pl-20 pr-20">
                <ul class="pb-20">
                        <li>
                            <h4><?= __('企业资质'); ?></h4>
                            <span><a href="company_code.html?shop_id=<%=store_info.shop_id%>"><img src="../../images/intel.gif" alt=""></a></span>
                        </li>
                            <% if(store_info.shop_region){%>
                                <li>
                                    <h4><?= __('所在地'); ?></h4>
                                    <span><%= store_info.shop_region %></span>
                                </li>
                                <% } %>
                                    <% if(store_info.shop_create_time){%>
                                        <li>
                                            <h4><?= __('开店时间'); ?></h4>
                                            <span><%= store_info.shop_create_time %></span>
                                        </li>
                                        <% } %>
                                            <% if(store_info.store_zy){%>
                                                <li>
                                                    <h4><?= __('主营商品'); ?></h4>
                                                    <span><%= store_info.store_zy %></span>
                                                </li>
                                                <% } %>
                </ul>
            </div>
            <div class="nctouch-store-block pl-20 pr-20">
                <ul class="pb-20">
                    <% if(store_info.shop_tel){%>
                        <li>
                            <h4><?= __('联系电话'); ?></h4>
                            <span>
					<%= store_info.shop_tel %>
				</span>
                            <a href="tel:<%= store_info.shop_tel%>" class="call"></a>
                        </li>
                        <% } %>
                            <% if(store_info.shop_workingtime){%>
                                <li>
                                    <h4><?= __('工作时间'); ?></h4>
                                    <span><%= store_info.shop_workingtime %></span>
                                </li>
                                <% } %>
                                    <% if(store_info.shop_qq || store_info.shop_ww){%>
                                        <li>
                                            <h4><?= __('联系方式'); ?></h4>
                                            <span>
					<% if(store_info.shop_qq){%>
					<a href="http://wpa.qq.com/msgrd?v=3&uin=<%= store_info.shop_qq %>&site=qq&menu=yes" target="_blank" class="qq">
						<i></i>QQ<?= __('联系'); ?>
					</a>
					<% }<?= __('　'); ?>%>
				</span>
                                        </li>
                                        <% } %>
                </ul>
            </div>
</script>
<script type="text/javascript" src="../js/zepto.min.js"></script>

<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/tmpl/store_intro.js"></script>

</html>
<?php 
include __DIR__.'/../includes/footer.php';
?>