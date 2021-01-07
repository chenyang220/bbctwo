<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>银联支付</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" /> 
        <script type="text/javascript" src="<?=$this->view->js?>/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="<?=$this->view->js?>/qrcode.min.js"></script>
        <link rel="stylesheet" type="text/css"  href="<?=$this->view->css?>/WxNative.css"/>
    </head>
    <style type="text/css">
        #qrcode>img{
            margin-top: 40px;
            margin-left: 100px;
            border: 1px solid #bcbcbc;
        }
    </style>
    <body>
        <!--导航-->
        <header>
            <div class="clearfix Navigation">
                <span class=" pay-li-cashier">
                    <span class="fz-col"><?=$site_name?></span>
                     收银台
                </span>
                <span class=" pay-li-nickname"><?=$userInfo['user_nickname']?></span>
                <span class=" pay-li-nickname ml14"><a href="<?=$login_out_url?>">退出</a></span>
                <span class=" pay-li-nickname ml14">|</span>
                <span class=" pay-li-nickname ml14"><a href="<?=$shop_url?>">返回商城</a></span>
                <ul class="clearfix pay-order-number">
                    <li class="fl ">
                        订单提交成功，请尽快付款！订单号：
                        <span><?=$QR_code['inorder']?></span>
                    </li>
                    <li class="fr">应付金额<span class="jiage"><?=$QR_code['money']?></span>元</li>
                </ul>

            </div>
        </header>
        <!--支付内容-->
        <div>
            <div class="wx-payment clearfix">
                <div class="fl wx-payment-left">
                    <p>银联商务支付</p>
                    <div id="qrcode"></div>
                    <div class="wx-payment-left-div1">
                        <ul class="clearfix" style="width: 272px;">
                            <li class="pay-icon fl"></li>
                            <li class="pay-text fl">扫描二维码支付<br>请使用微信/支付宝/云闪app</li>
                        </ul>
                    </div>
                    <div class="wx-payment-left-div2">
                        <a href="javascript:history.back(-1)"><img src="paycenter/models/Payment/zuojiantou.png" width="9" height="16">选择其他支付方式</a>
                    </div>
                </div>
                <div class="fl wx-payment-right"></div>
                <div id="merOrderId" style="display: none;"><?=$QR_code['merOrderId']?></div>
                <div id="queryId" style="display: none;"><?=$QR_code['queryId']?></div>
            </div>
        </div>
</body>
</html>
<script>
    $(function(){
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            width : 290,
            height : 290
        });
        function makeCode () {
            var elText = "<?php echo $QR_code['qrUrl']?>";
            qrcode.makeCode(elText);
        }
        makeCode();
    })
</script>
<script type="text/javascript">
    var SITE_URL = "<?=Yf_Registry::get('url')?>";
    var hello = function () {
        var merOrderId = $("#merOrderId").text();
        var queryId = $("#queryId").text();
        $.ajax({
            type: "POST",
            url: SITE_URL + "?ctl=Pay&met=pay_status&typ=json",
            data: {merOrderId: merOrderId, queryId: queryId},
            dataType: "json",
            success: function (data) {
                if (data.status == 'TRADE_SUCCESS') {
                    alert('支付成功');location.href = "<?php echo Yf_Registry::get('shop_api_url');?>"+"/index.php?ctl=Buyer_Order&met=physical";
                    clearInterval(showTimeInterval);
                }
            }
        })
    }
    var showTimeInterval = setInterval(hello, 3000);
</script>