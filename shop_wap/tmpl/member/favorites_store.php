<?php 
include __DIR__.'/../../includes/header.php';
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
    <title><?= __('店铺收藏'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_products_list.css">
    <link rel="stylesheet" href="../../css/iconfont.css">
</head>
<script type="text/javascript">
    //用app电话号码登录
    var u_id = '<?php echo $u_id;?>';
    if (u_id) {
       window.location.href = UCenterApiUrl + '/?ctl=Login&met=oauth&typ=e&u_id=' + u_id + "&return_url=" + WapSiteUrl + "/tmpl/member/favorites_store.html";
    }
</script>
<body>
    <header id="header" class="fixed">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="member.html"> <i class="back"></i> </a> -->
            </div>
            <div class="header-tab"><a href="favorites.html"><?= __('商品收藏'); ?></a><a href="javascript:void(0);" class="cur"><?= __('店铺收藏'); ?></a></div>
            <!-- <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a> </div> -->
        </div>
        <div class="nctouch-nav-layout">
            <div class="nctouch-nav-menu"> <span class="arrow"></span>
                <ul>
                    <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                        <li><a href="../store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }else{ ?>
                        <li><a href="../../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }?>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <ul class="favorites-store-list" id="favorites_list"></ul>
    </div>
    <div class="fix-block-r">
        <a href="javascript:void(0);" class="gotop-btn gotop hide" id="goTopBtn"><i></i></a>
    </div>
    <footer id="footer" class="bottom"></footer>
    <script type="text/html" id="sfavorites_list">
        <%if(typeof(items)!='undefined' && items.length>0){%>
            <% for (var k in items) { var v = items[k]; %>
                <li id="favitem_<%=v.shop_id %>">
                    <a href="<%=WapSiteUrl%>/tmpl/store.html?shop_id=<%=v.shop_id %>">
                        <div class="store-avatar"><img src="<%=v.wap_shop_logo, 60, 60 %>" /></div>
                        <dl class="store-info">
                            <dt class="store-name one-overflow wp80"><%=v.shop_name %></dt>
                            <dd><span><?= __('粉丝：'); ?><em><%=v.shop.shop_collect %></em><?= __('人'); ?></span><span>&nbsp;</span></dd>
                            <i class="iconfont icon-arrow-right col9"></i>
                        </dl>
                    </a>
                    <a href="javascript:void(0);" nc_type="fav_del" data_id="<%=v.shop_id %>" class="fav-unsave"></a>
                </li>
                <%}%>
                    <li class="loading">
                        <div class="spinner"><i></i></div><?= __('数据读取中'); ?></li>
                    <%}else{%>
                        <div class="nctouch-norecord favorite-store">
                            <div class="norecord-ico"><i></i></div>
                            <dl>
                                <dt><?= __('您还没有关注任何店铺'); ?></dt>
                                <dd><?= __('可以去看看哪些店铺值得收藏'); ?></dd>
                            </dl>
                            <a href="<%=WapSiteUrl%>" class="btn"><?= __('随便逛逛'); ?></a>
                        </div>
                        <%}%>
    </script>
    
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/template.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/ncscroll-load.js"></script>
    <script type="text/javascript" src="../../js/tmpl/favorites_store.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</body>

</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>