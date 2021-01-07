<?php 
include __DIR__.'/../../includes/header.php';
$act = $_GET['act'] ? $_GET['act'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title><?= __('代金券'); ?></title>
	<link rel="stylesheet" href="../../css/base.css">
    <link rel="stylesheet" href="//at.alicdn.com/t/font_562768_3rzbixo5pce.css">
</head>
<script type="text/javascript">
    //用app电话号码登录
    var u_id = '<?php echo $u_id;?>';
    if (u_id) {
       window.location.href = UCenterApiUrl + '/?ctl=Login&met=oauth&typ=e&u_id=' + u_id + "&return_url=" + WapSiteUrl + "/tmpl/member/member_voucher.html";
    }
</script>
<body>
	<header id="header" class="posf">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="member.html"> <i class="back"></i> </a> -->
            </div>
           <span class="header-tab"><a href="member_voucher.html" class="cur">代金券</a><a href="redpacket_list.html">我的红包</a></span>
            <a  class="delvoucher" href="javascript:void(0)"><?= __('清空'); ?></a>
            <div class="header-r">
                <!-- <a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a>  -->
            </div>
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
                    <li><a href="../../tmpl/cart_list.html"><i class="cart"></i><?= __('购物车'); ?><sup></sup></a></li>
                    <li><a href="../../tmpl/member/member.html"><i class="member"></i><?= __('我的商城'); ?></a></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    </header>
    <ul class="v-list-tab bgf borb1">
        <li class="vocher_status_li <?php if($act == 0){echo 'active';}?>"><a href="member_voucher.html?act=0"><?= __('全部'); ?></a></li>
    	<li class="vocher_status_li <?php if($act == 1){echo 'active';}?>"><a href="member_voucher.html?act=1"><?= __('未使用'); ?></a></li>
    	<li class="vocher_status_li <?php if($act == 2){echo 'active';}?>"><a href="member_voucher.html?act=2"><?= __('已失效'); ?></a></li>
    </ul>
    <div class="v-list" id="v_list">
    	
    </div>
    
    <script type="text/html" id="voucher_list">
        <%if(items.length>0){%>
            <ul>
                <% for (var i in items) {%>
                <a href="/tmpl/store.html?shop_id=<%=items[i].voucher_shop_id%>">
                    <%if(items[i].voucher_state == 1){%>
                    <li class="clearfix yes">
                    <%}else if(items[i].voucher_state == 2){%>    
                    <li class="clearfix no">
                    <%}else {%>
                    <li class="clearfix pass">
                    <% } %>    
                        <div class="fl">
                            <div>
                                <p class="tc fz-56 colf"><b class="fz-28 iblock align-top"><?= __('￥'); ?></b><span><%=items[i].voucher_price%></span></p>
                                <div class="tc fz-24"><span><?= __('满'); ?><%=items[i].voucher_limit%><?= __('元使用'); ?></span></div>
                            </div>
                        </div>
                        <div class="fr pt-20 pb-20 pr-30">
                            <h3 class="fz-24 more-overflow"><%=items[i].voucher_shop_name%></h3>
                            <p class="fz-24 colbc mt-20"><%=items[i].voucher_end_date%><?= __('前有效'); ?></p>
                        </div>
                        <%if(items[i].voucher_state ==1){%>
                        <span class="btn-voucher-use fz-24 default-color"><?= __('立即使用'); ?></span>
                        <% } %>    
                       <!--  <i class="icon-pase"></i> -->
                    </li>
                </a>   
                <%}%>
            </ul>
        <%}else{%>
            <div class="no-data tc">
                <div class="table wp100 hp100">
                    <div class="table-cell">
                        <img src="../../images/new/icon-tips.png" alt="img">
                        <span id="end" class="block fz-30 col6"><?= __('您还没有优惠券哦'); ?></span>
                    </div>
                </div>
                
            </div>
        <%}%>
    </script>
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/template.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/tmpl/member_voucher.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    </body></html>

<?php 
include __DIR__.'/../../includes/footer.php';
?>

