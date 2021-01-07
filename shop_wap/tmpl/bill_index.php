<?php
include __DIR__.'/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title><?= __('生成海报'); ?></title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
    <link rel="stylesheet" href="../css/fight-groups.css">
    <link rel="stylesheet" href="../css/nctouch_products_detail.css">
    <script src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <style type="text/css">
        .item2 {
            flex-grow: 1;
            text-align: center;
            margin-top: 10px;
            margin-right: 10px;
        }
        #imgDownload{
            width: 80%;
            margin: auto;
            display: block;
            margin-top: 2rem;
        }
        .main_b{
            display: flex;
            justify-content: space-around;
        }

        body{
            background: #8888888 !important;
        }
        #aa{
            text-align:center;
            font-size: 15px;
            color: #fff;
            display: block;
        }
    </style>
</head>
<body style="background: #888888;">
<img id="imgDownload" src="" style="-webkit-touch-callout:default;" >
<div id="content" class="content">
    <div class="to-poster-box">
    </div>
</div>
<!-- <div class="main_b">
    <div class="box" >
        <img src="../img/wei.png" class="share_app">
        <a class="" style="font-size:10px;">发送好友</a>
    </div>
    <div class="box"  >
        <img src="../img/xia.png" class="aaaa" id="bao_app">
        <a  style="font-size:10px;">保存本地</a>
    </div>
</div> -->

<div class="main_b" style="margin-top:0.5rem">
    <?php if ($_COOKIE['is_app_guest']) { ?>
        <div class="box" >
            <img src="../img/wei.png" class="share_app">
            <a class="" style="font-size:10px;">发送好友</a>
        </div>
        <div class="box"  >
            <img src="../img/xia.png" id="bao_app" >
            <a  style="font-size:10px;">保存本地</a>
        </div>
    <? }else{?>
        <div class="box"  >
<!--            <img src="../img/xia.png" class="aaaa" id="bao_app">-->
            <a  style="font-size:10px;color:#fff">长按图片可以保存到手机</a>
        </div>
    <?php }?>
</div>
<script type="text/html" id="bill_info">
    <div class="to-poster-user">
        <em class="img-box"><img class="wp100" src="<%=user_logo%>" alt="user"></em>
        <div class="iblock">
            <span><%=user_name%></span>
            <em>推荐给你一个宝贝</em>
        </div>
    </div>
    <em class="to-poster-goods-box">
        <img class="wp100" style="height: 320px!important;" src="<%=goods_info.goods_image%>" alt="goods">
    </em>
    <p class="to-poster-goods-name one-overflow"><%=goods_info.goods_name%></p>
    <div class="goods-code-box flex">
        <div class="iblock flex1">
            <p class="to-poster-price"><strong><?= __('￥'); ?><%=goods_info.goods_price%></strong><b class="through"><?= __('￥'); ?><%= goods_info.goods_market_price %></b></p>
            <em class="to-poster-pri-origin more-overflow block"><%=goods_info.goods_promotion_tips%></em>
        </div>
        <div class="iblock to-poster-code">
            <em><img src="<%=qrCode%>" alt="code" class="wp100"></em>
            <span class="block goods-code-tips">扫一扫立即购买</span>
        </div>
    </div>
</script>
</body>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
<script type="text/javascript" src="../js/jquery.timeCountDown.js" ></script>
<script type="text/javascript" src="../js/html2canvas.js" ></script>
<script type="text/javascript" src="../js/bill.js" ></script>
<script type="text/javascript" src="../js/tmpl/poster.js"></script>

<?php
include __DIR__.'/../includes/footer.php';
?>
</html>

