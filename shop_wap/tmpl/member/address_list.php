<?php 
include __DIR__.'/../../includes/header.php';
?>
<!doctype html>
<html class="hp100">

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
    <title><?= __('地址管理'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_products_detail.css"/>
	<link rel="stylesheet" href="../../css/iconfont.css">
</head>
<script type="text/javascript">
    //用app电话号码登录
    var u_id = '<?php echo $u_id;?>';
    if (u_id) {
       window.location.href = UCenterApiUrl + '/?ctl=Login&met=oauth&typ=e&u_id=' + u_id + "&return_url=" + WapSiteUrl + "/tmpl/member/address_list.html";
    }
</script>
<body class="hp100 bgf">
    
    <header class="borb0" id="header">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="member.html"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1><?= __('地址管理'); ?></h1>
            </div>
            <div class="header-r"> <a id="header-nav" href="address_opera.html"><i class="icon-add"></i></a> </div>
        </div>
    </header>
    <div class="nctouch-main-layout mb20">
        <div class="nctouch-address-list" id="address_list"></div>
    </div>
    <footer id="footer" class="bottom"></footer>
    <script type="text/html" id="saddress_list">
    <% var len = address_list.length %>
        <% if(len != 0){ %>
            <ul>
                <% for (var i in address_list) {%>
                    <li>
                        <dl>
                            <dt>
            					<span class="name one-overflow w4"><%=address_list[i].user_address_contact %></span>
            					<span class="phone ml-40"><%=address_list[i].user_address_phone %></span>
                                <% if(address_list[i].user_address_attribute == 1){ %>
                                    <em class="addr-attribute ml-40"><?= __('公司'); ?></em>
                                <% } else if (address_list[i].user_address_attribute == 2){ %>
                                    <em class="addr-attribute ml-40"><?= __('家'); ?></em>
                                <% } else {%>
                                    <em class="addr-attribute ml-40"><?= __('学校'); ?></em>
                                <% } %>
            				</dt>
                            <dd class="more-overflow">
                                <%=address_list[i].user_address_area %>&nbsp;
                                <%=address_list[i].user_address_address %>   
                            </dd>
                        </dl>
                        <div class="handle">
                            <input type="radio" name="address" class="user_address" data-user_address_id=<%=address_list[i].user_address_id %> <% if (address_list[i].user_address_default == 1) { %> checked
                            <% } %> />  <p class="mrdz"><?= __('默认地址'); ?></p>
                            <span><a href="address_opera_edit.html?user_address_id=<%=address_list[i].user_address_id %>"><i class="edit"></i><?= __('编辑'); ?></a><a href="javascript:;" user_address_id="<%=address_list[i].user_address_id %>" class="deladdress"><i class="iconfont icon-lajitong"></i><?= __('删除'); ?></a></span>
                        </div>
                    </li>
                <%}%>
            </ul>    
           
            <!--<?= __('暂无收货地址需要判断'); ?>-->
            <div class="norecord tc hide">
                <p class="fz-30 col9"><?= __('暂无收货地址'); ?></p>
            </div>
            <div class="goods-option-foot z-xjdz-foot">
            	<a class="btn-l" href="address_opera.html"><?= __('新建地址'); ?></a>
            </div>
            
        <%}else{%>
            <div class="nctouch-norecord address">
               <!-- <div class="norecord-ico"><i></i></div> -->
                <dl>
                    <dt><?= __('您还没有过添加收货地址'); ?></dt>
                    <dd><?= __('正确填写常用收货地址方便购物'); ?></dd>
                </dl>
                <a href="address_opera.html" class="btn"><?= __('添加新地址'); ?></a>
                <!-- <a class="btn-l mt5 goods-option-foot z-xjdz-foot" href="address_opera.html"><?= __('新建地址'); ?></a> -->
            </div>
        <%}%>
    </script>
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    
    <script type="text/javascript" src="../../js/template.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/tmpl/address_list.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</body>

</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>