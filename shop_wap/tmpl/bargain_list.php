<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>

</body>
</html>
<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="../css/base.css">
	<link rel="stylesheet" href="../css/bargain.css">
  	<link rel="stylesheet" href="../css/nctouch_common.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_bd8mdobvl1.css">
    <link rel="stylesheet" type="text/css" href="../css/intlTelInput.css">
</head>
<body>
    <div id="bargain">
        <header id="header" class="posf">
            <div class="header-wrap">
                <div class="header-l">
                    <!-- <a class="js-cancel-push" href="javascript:window.history.back();"> <i class="back"></i> </a> -->
                </div>
                <div class="tit">砍价专区</div>
            </div>
        </header>
        <div class="nctouch-main-layout bargain_is_on">
            <div class="bargain-progress fz0 tc  bgf hide" id="progress">
                <div class="iblock bargain-progress-lis active">
                    <div class="bargain-progress-li"><i class="iconfont icon-bargain-goods"></i><span>点击心仪商品</span></div>
                </div>
                <div class="iblock bargain-progress-lis">
                    <b></b>
                    <div class="bargain-progress-li"><i class="iconfont icon-bargain-kan"></i><span>邀请好友砍价</span></div>

                </div>
                <div class="iblock bargain-progress-lis">
                    <b></b>
                    <div class="bargain-progress-li"><i class="iconfont icon-chenggong"></i><span>砍完即可获取</span></div>
                </div>
            </div>
            <ul class="bargain-goods-lists bgf">

            </ul>
            <div class="bargain-bottom-btn fz0">
                <a class="active bargain" href="javascript:;"><i class="iconfont icon-shangpin"></i><span class="align-middle">砍价商品</span></a>
                <a class="myBargain" href="javascript:;"><i class="iconfont icon-my-bargain"></i><span class="align-middle">我的砍价</span></a>
            </div>
        </div>
        <div id="bargain_status" class="nctouch-main-layout hide">
            <div class="activity-close-text">砍价活动已关闭</div>
        </div>
    </div>
    
    <div id="add_address" class="hide">
        <header id="header">
            <div class="header-wrap">
                <div class="header-l">
                    <a href="javascript:;"> <i class="back" id="hide_address"></i> </a>
                </div>
                <div class="header-title">
                    <h1><?= __('新增收货地址'); ?></h1>
                </div>
            </div>
        </header>
        <div class="nctouch-main-layout">
            <form>
                <div class="nctouch-inp-con">
                    <ul class="form-box">
                        <li class="form-item">
                            <h4><?= __('收货人：'); ?></h4>
                            <div class="input-box">
                                <input type="text" class="inp" name="true_name" id="true_name" autocomplete="off" oninput="writeClear($(this));"/>
                                <span class="input-del"></span></div>
                        </li>
                        <li class="form-item">
                            <h4><?= __('联系方式：'); ?></h4>
                            <div class="input-box">
                                <input type="tel" class="inp" name="mob_phone" id="re_user_mobile" autocomplete="off" oninput="writeClear($(this));"/>
                                <input type="hidden" name="area_code" id="area_code"/>
                                <span class="input-del"></span></div>
                        </li>
                        <li class="form-item">
                            <h4><?= __('所在地区：'); ?></h4>
                            <div class="input-box">
                                <input name="area_info" type="text" class="inp" id="area_info" unselectable="on" onfocus="this.blur()" autocomplete="off" onchange="btn_check($('form'));" readonly/>
                                <h5 class="form-item-h5"><i class="arrow-r"></i></h5>
                            </div>
                        </li>
                        <li class="form-item">
                            <h4><?= __('详细地址：'); ?></h4>
                            <div class="input-box">
                                <input type="text" class="inp" name="address" id="address" autocomplete="off" placeholder="<?= __('街道、楼牌号等'); ?>" oninput="writeClear($(this));">
                                <span class="input-del"></span></div>
                        </li>
                    </ul>
                    <div class="error-tips"></div>
                    <div class="form-btn"><a class="btn" href="javascript:;"><?= __('保存'); ?></a></div>
                </div>
            </form>
        </div>
    </div>

    <!-- 弹框-选择收货地址 -->
    <div id="bargain-address-alert-html" class="nctouch-bottom-mask down">
    	<div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block">
        	<div class="nctouch-bottom-mask-head bargain-address-alert-head">
        		<span>选择收货地址</span>
        		<a href="javascript:;" class="nctouch-bottom-mask-close"><b class="iconfont icon-close"></b></a>
        	</div>
            <div class="nctouch-bottom-mask-rolling go overflow-auto pb-0" id="bargain-address_roll">
                <ul class="bargain-address-lists">

                </ul>
                <p class="bargain-address-add">
                	<a class="block relatives" href="javascript:;">
                		<span>添加新收获地址</span><i class="iconfont icon-btnrightarrow"></i>
                	</a>
                </p>
            </div>
        </div>
    </div>

    <!-- 弹框-登录提示 -->
	<div class="dialog tc login-alert-tips hide">
	    <div class="table">
	        <div class="table-cell">
	            <div class="content">
	                <p class="login-alert-tips-tit">请先登录</p>
	                <div><a href="javascript:;" class="login-alert-tips-btn logbtn">登录</a></div>
	            </div>
	        </div>
	    </div>
	</div>

    <!-- 弹框-报错信息-->
    <div class="simple-dialog-wrapper bargain-address-sure hide info">
        <div class="table wp100 hp100">
            <div class="table-cell tc">
                <div class="s-dialog-mask"></div>
                <div class="s-dialog-wrapper s-dialog-skin-red">
                    <div class="s-dialog-content dialog-info"></div>
                </div>
            </div>
        </div>
    </div>

    <!--    地址弹框-->
    <div class="simple-dialog-wrapper bargain-address-sure hide address">
        <div class="table wp100 hp100">
            <div class="table-cell tc">
                <div class="s-dialog-mask"></div>
                <div class="s-dialog-wrapper s-dialog-skin-red">
                    <div class="s-dialog-title"><span>请确认您的收货地址</span></div>
                    <div class="s-dialog-content dialog-address"></div>
                    <div class="s-dialog-btn-wapper"><a href="javascript:void(0)" class="s-dialog-btn-ok bor-rem">确认</a><a href="javascript:void(0)" class="s-dialog-btn-cancel">取消</a></div>
                </div>
            </div>
        </div>
        
    </div>

    <script type="text/html" id="bargain-list-tmpl">
        <!-- 砍价列表 -->
        <% if(items.length > 0) { %>
            <% for (var i = 0; i < items.length; i++) { var item = items[i];  %>
                <li>
                    <div class="bargain-goods-item-content">
                        <em class="img-box" style="background:url(<%=item.goods_image%>) no-repeat center;background-size:cover;"></em>
                        <div>
                            <h4 class="more-overflow"><%=item.goods_name%></h4>
                            <span class="block bargain-goods-list-pri">原价 ￥<%=item.goods_price%></span>
                            <span class="block">最低可砍至￥<%=item.bargain_price%></span>
                            <% if(item.is_self == 1){ %>
                                <% if(item.bargain_state == 0){ %>
                                    <a class="bargain-btn-go" href="./bargain_detail.html?buy_id=<%=item.buy_id%>">继续砍价</a>
                                <% }else if(item.bargain_state == 1){ %>
                                    <span class="block">砍价成功</span>
                                    <a class="bargain-btn-go" href="./member/order_detail.html?order_id=<%=item.order_id%>">查看订单详情</a>
                                <% }else { %>
                                    <span class="block">砍价失败</span>
                                    <a class="bargain-btn-go" href="./bargain_list.html">重砍一个</a>
                                <% } %>
                            <% }else{ %>
                                <% if(item.is_join == 1){ %>
                                    <a class="bargain-btn-go" href="./bargain_detail.html?buy_id=<%=item.buy_id%>">继续砍价</a>
                                <% }else{ %>
                                    <% if(item.bargain_status == 1 && item.bargain_stock > 0 ){ %>
                                    <a class="bargain-btn-go js-bargain-go" href="javascript:;" data-bargain_id="<%=item.bargain_id%>">去砍价</a>
                                    <% } %>
                                    <% if(item.bargain_status == 1 && item.bargain_stock <= 0 ){ %>
                                    <a class="bargain-btn-go empty" href="javascript:;" data-bargain_id="<%=item.bargain_id%>">抢光了</a>
                                    <% } %>
                                <% } %>
                            <% } %>
                        </div>
                    </div>
                    <% if((item.is_self == 1 && item.bargain_state == 0) || item.is_join == 1){ %>
                        <div class="clearfix bargain-goods-item-operate">
                            <div class="fl"><span>已砍</span><strong class="bargain-operate-pri-had"><%=item.bargain_price_count%></strong><span>元，还差</span><strong class="bargain-operate-pri-have"><%=item.overPlus%></strong><span>元</span></div>
                            <div class="fr fnTimeCountDown" data-end="<%=item.user_end_date%>">
<!--                                <em id="minute_show" class="day">00</em><span>天</span>-->
                                <em id="minute_show" class="hour">00</em><span>时</span>
                                <em id="second_show" class="mini">00</em><span>分</span>
                                <em id="second_show" class="sec">00</em><span>秒后结束</span>
                            </div>
                        </div>
                    <% } %>
                </li>
            <% } %>
        <% }else{ %>
            <!--空列表-->
            <li>
                <div class="bargain-activity-end tc">
                    <img src="../images/bargain/i-goods.png" alt="">
                    <p>暂无砍价商品哦～</p>
                </div>
            </li>
        <% } %>
    </script>

    <script type="text/html" id="bargain-address-lists-tmpl">
        <% if(address_list) {%>
            <% for (var i  in address_list) { var item = address_list[i];  %>
            <li class="choose_address" data-address_id="<%=item.id%>" data-address_info="<%=item.user_address_contact%>，<%=item.user_address_phone%>，<%=item.user_address_area%> <%=item.user_address_address%>">
                <a class="block" href="javascript:;">
                    <div>
                        <p><span class="mwp50 bargain-address-receiver"><%=item.user_address_contact%></span><em class="mwp50"><%=item.user_address_phone%></em></p>
                        <p><span class="one-overflow"><%=item.user_address_area%> <%=item.user_address_address%></span></p>
                    </div>
                </a>
            </li>
            <% } %>
        <% } %>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/intlTelInput.js"></script>
    <script type="text/javascript" src="../js/animation.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/addtohomescreen.js"></script>
    <script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>
    <script type="text/javascript" src="../js/bargain_list.js"></script>

</body>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>
</html>