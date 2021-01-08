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
	<link rel="stylesheet" href="../css/iconfont.css">
	<script src="../js/iconfont.js"></script>
</head>
<body>
<header id="header" class="posf">
    <div class="header-wrap">
        <div class="header-l">
            <!-- <a class="js-cancel-push" href="javascript:window.history.back();"> <i class="back"></i> </a> -->
        </div>
        <div class="tit">砍价专区</div>
    </div>
</header>
<!-- 砍价活动未结束begin -->
<div class="nctouch-main-layout bargain-module-index">
    <div class="bargain-index-content">
        <div class="bargain-index-head clearfix">
            <div class="fl fz0">
                <em class="img-box" id="user_logo"></em>
                <span class="bargain-user"></span>
            </div>
            <div class="fr" id="bargain-rule">
                <a class="js-bargain-rule" href="javascript:;"><i class="iconfont icon-wenhao"></i><span>活动规则</span></a>
            </div>
        </div>
        <div class="bargain-index-goods">
            <em class="img-box" id="goods_image"></em>
            <div>
                <span class="block one-overflow" id="goods_name"></span>
                <strong id="goods_price"></strong>
                <p id="bargain_price"></p>
            </div>
        </div>
        <div class="bargain-index-det tc">
            <!--1, 砍价进度 -->
            <div class="hide" id="rate">
                <p>已砍<strong class="bargain-pri-had" id="bargain_price_count">20.55</strong>元，还差<strong class="bargain-pri-have" id="over_price">79.45</strong>元</p>
                <div class="bargain-index-progress tl"><i style="width:30%;" id="show_rate"></i></div>
                <p class="bargain-index-time fnTimeCountDown">还剩
                    <em id="minute_show" class="hour">00</em><i>:</i>
                    <em id="second_show" class="mini">00</em><i>:</i>
                    <em id="second_show" class="sec">00</em>过期</p>
            </div>
            <!-- 好友看到的界面：砍价成功 -->
            <div class="bargain-success hide" id="friend_success_rate">
                <strong>砍价成功</strong>
                <div class="bargain-index-progress tl"><i style="width:100%;"></i></div>
            </div>
            <!-- 自己看到的砍价成功 -->
            <div class="bargain-success hide" id="self_success_rate">
                <strong>恭喜你，砍价成功，棒棒哒！</strong>
                <div class="bargain-index-progress tl"><i style="width:100%;"></i></div>
            </div>
            <!-- 自己看到的砍价过期 -->
            <div class="bargain-success hide" id="self_failure_rate">
                <strong>未在24小时内完成，砍价已过期</strong>
                <div class="bargain-index-progress tl"><i style="width:70%;"></i></div>
            </div>
            <!-- 帮砍价成功，但好友砍价失败了 -->
            <div class="bargain-success hide" id="friend_failure_rate">
                <strong>未在24小时内完成，砍价已过期</strong>
                <div class="bargain-index-progress tl"><i style="width:70%;"></i></div>
            </div>
            <!-- 自己砍价的页面 -->
            <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { ?>
                <div class="bargain-btn hide share-wechat" id="self-show-btn"><a href="javascript:;">喊好友砍一刀</a></div>
            <?php } else { ?>
                <div class="bargain-btn hide share" id="self-show-btn"><a href="javascript:;">喊好友砍一刀</a></div>
            <?php } ?>
            <!--1, 好友分享帮忙砍价的页面 -->
            <div class="bargain-btn hide" id="friend-show-btn"><a class="js-bargain-help" href="javascript:;">帮好友砍一刀</a></div>

            <!--1, 帮好友砍价成功,砍价进行中 -->
            <div class="bargain-help-module hide" id="friend_join_btn">
                <p class="bargain-help-tips">已帮助好友砍价</p>
                <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { ?>
                    <div class="bargain-btn-share share-wechat"><a href="javascript:;">分享助力好友砍价</a></div>
                <?php } else { ?>
                    <div class="bargain-btn-share share"><a href="javascript:;">分享助力好友砍价</a></div>
                <?php } ?>
                <div class="bargain-btn"><a href="./bargain_list.html">我也要去砍价</a></div>
            </div>

            <!-- 帮好友砍价并且好友砍价成功了 -->
            <div class="bargain-help-module hide" id="friend_success_btn">
                <p class="bargain-help-tips bargain-help-success-tips">已帮助好友砍价</p>
                <div class="bargain-btn"><a href="./bargain_list.html">我也要去砍价</a></div>
            </div>

            <!-- 帮好友砍价并且好友砍价失败了 -->
            <div class="bargain-help-module hide" id="friend_failure_btn">
                <p class="bargain-help-tips bargain-help-success-tips">已帮助好友砍价</p>
                <div class="bargain-btn"><a href="./bargain_list.html">我也要去砍价</a></div>
            </div>

            <!-- 未帮好友砍价但是好友砍价成功了 -->
            <div class="bargain-help-module hide" id="no_join_success">
                <div class="bargain-btn"><a href="./bargain_list.html">我也要去砍价</a></div>
            </div>
            <!-- 未帮好友砍价,好友砍价失败了 -->
            <div class="bargain-help-module hide" id="no_join_failure">
                <div class="bargain-btn"><a href="./bargain_list.html">我也要去砍价</a></div>
            </div>
            <!-- 自己砍价成功了查看订单详情 -->
            <div class="bargain-help-module hide" id="self_success_btn">
                <div class="bargain-btn-share bargain-order-detail"><a href="javascript:;">查看订单详情</a></div>
                <div class="bargain-btn"><a href="./bargain_list.html">查看更多砍价商品</a></div>
            </div>
            <!-- 自己砍价还未成功但过期了 -->
            <div class="bargain-help-module hide" id="self_failure_btn">
                <div class="bargain-btn"><a href="./bargain_list.html">查看更多砍价商品</a></div>
            </div>
            <!-- 微信分享 -->
            <div class="dialog bargain-wechat-share hide" id="wechat">
                <div>听说分享次数越多，砍价成功的机会越大哦~</div>
            </div>
        </div>
    </div>
    <!-- 砍价帮（自己砍价的页面） -->
    <div class="bargain-index-help hide">
        <p class="bargain-index-help-tit tc">
            <i class="iconfont icon-star"></i><span>砍价帮</span><i class="iconfont icon-star"></i>
        </p>
        <ul class="bargain-index-help-li">

        </ul>
    </div>
    <!--活动规则 (好友分享帮忙砍价的页面) -->
    <div class="bargain-index-rule">
        <p class="bargain-index-rule-tit tc"><span>活动规则</span></p>
        <ul class="bargain-rule-items">
            <li>
                <b>1</b>
                <p>选择您心仪的商品，邀请好友一起砍价，只要在24小时内，砍至底价即可获取商品，无须承担任何商品成本及邮寄费用</p>
            </li>
            <li>
                <b>2</b>
                <p>活动期间，对于同一个砍价，只可帮助砍价1次，每天最多帮助3个好友砍价</p>
            </li>
            <li>
                <b>3</b>
                <p>每次砍价金额随机，参与好友越多越容易成功</p>
            </li>
            <li>
                <b>4</b>
                <p>必须在砍价过期前提供收货地址，没有收货地址砍至底价也是砍价失败，将无法为您发货</p>
            </li>
            <li>
                <b>5</b>
                <p>本活动不可与其他促销活动及优惠同时享受</p>
            </li>
        </ul>
    </div>
</div>
<!-- 砍价活动未结束end-->
<!--  砍价活动已结束begin -->
<div class="bargain-activity-end tc hide">
    <img src="../images/bargain/i-time.png" alt="">
    <p>很抱歉，此活动已结束，无法再查看~</p>
</div>
<!--  砍价活动已结束end -->
<!-- 弹框-登录注册提示 -->
<div class="dialog tc login-alert-tips hide dialog_login">
    <div class="table">
        <div class="table-cell">
            <div class="content">
                <p class="login-alert-tips-tit">请先登录/注册</p>
                <div class="login-alert-tips-btn logbtn"><a href="javascript:;" class="">登录</a>&nbsp;/&nbsp;<a href="javascript:;">注册</a></div>
            </div>
        </div>
    </div>
</div>
<!-- 弹框-帮砍价成功提示 -->
<div class="bargain-help-success-alert tc hide friend-help">
    <div class="bargain-help-alert-content">
        <div>
            <strong>哇塞！太棒了！</strong>
            <p>你一出手帮朋友砍了<span id="friend_help_price">0</span>元</p>
            <?php if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { ?>
                <div class="bargain-alert-btn share friend-help-share"><a href="javascript:;">分享助力好友砍价</a></div>
            <?php } else { ?>
                <div class="bargain-alert-btn share-wechat friend-help-share"><a href="javascript:;">分享助力好友砍价</a></div>
            <?php } ?>
            <em class="bargain-alert-close js-bargain-alert-close"><i class="iconfont icon-close-circle"></i></em>
        </div>
    </div>
</div>
<!-- 弹框-自己砍价成功提示 -->
<div class="bargain-help-success-alert tc hide self-bargain">
    <div class="bargain-help-alert-content">
        <div>
            <strong>您已砍<b id="self_bargain_price">0</b>元</strong>
            <p>听说分享次数越多，<br/>砍价成功的机会越大哦</p>
            <?php if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){ ?>
                <div class="bargain-alert-btn share"><a href="javascript:;">喊好友砍一刀</a></div>
            <?php }else{ ?>
                <div class="bargain-alert-btn share-wechat"><a href="javascript:;">喊好友砍一刀</a></div>
            <?php }?>
            <em class="bargain-alert-close js-bargain-alert-close"><i class="iconfont icon-close-circle"></i></em>
        </div>
    </div>
</div>
<!-- 弹框-活动规则 -->
<div class="bargain-rule-alert tc hide">
    <div class="bargain-rule-alert-content tl">
        <p class="bargain-rule-tit tc">活动规则</p>
        <ul class="bargain-rule-items">
            <li>
                <b>1</b>
                <p>选择您心仪的商品，邀请好友一起砍价，只要在24小时内，砍至底价即可获取商品，无须承担任何商品成本及邮寄费用</p>
            </li>
            <li>
                <b>2</b>
                <p>活动期间，对于同一个砍价，只可帮助砍价1次，每天最多帮助3个好友砍价</p>
            </li>
            <li>
                <b>3</b>
                <p>每次砍价金额随机，参与好友越多越容易成功</p>
            </li>
            <li>
                <b>4</b>
                <p>必须在砍价过期前提供收货地址，没有收货地址砍至底价也是砍价失败，将无法为您发货</p>
            </li>
            <li>
                <b>5</b>
                <p>本活动不可与其他促销活动及优惠同时享受</p>
            </li>
        </ul>
        <em class="bargain-alert-close js-bargain-rule-alert-close"><i class="iconfont icon-close-circle"></i></em>
    </div>
</div>
    <script type="text/html" id="join-user-lists-tmpl">
        <% if(join_user.length > 0) { %>
            <% for (var i = 0; i < join_user.length; i++) { var item = join_user[i];  %>
            <li class="clearfix">
                <div class="fl">
                    <em class="img-box align-middle"><img src="<%=item.user_logo%>" alt="helper"></em>
                    <span class="one-overflow wp50 align-middle"><%=item.user_name%></span>
                </div>
                <div class="fr">
                    <svg class="icon fz50" aria-hidden="true">
                        <use xlink:href="#icon-kanjia1"></use>
                    </svg>
                    <span class="align-middle">砍掉</span><strong class="align-middle"><%=item.help_bargain_price%> <span class="align-middle">元</span></strong></div>
            </li>
            <% } %>
        <% } %>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js" ></script>
    <script type="text/javascript" src="../js/animation.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>
    <script type="text/javascript" src="../js/NativeShare.js"></script>
    <script type="text/javascript" src="../js/bargain_detail.js"></script>
</body>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>
</html>