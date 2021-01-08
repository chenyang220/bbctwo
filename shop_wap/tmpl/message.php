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
	<!-- <link rel="stylesheet" href="../css/iconfont.css"> -->
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_ua6c7mpihef.css">
</head>
<body>
	<header id="header" class="">
        <div class="header-wrap">
             <div class="header-l">
                <!-- <a href="javascript:window.history.back();"><i class="icon-back"></i></a> -->
            </div>
            <div class="tit">消息中心</div>
        </div>
    </header>
    <div class="nctouch-main-layout">
    	<ul class="news-center-box">
    		<li onclick="go_detail()">
    			<a href="javascript:;">
    				<i class="iconfont icon-tongzhi"></i>
    				<span>系统通知</span>
    				<b class="" id="look"></b>
    				<i class="iconfont fr icon-arrow-right"></i>
    			</a>
    		</li>
    		<li onclick="go_message()">
    			<a href="javascript:;">
    				<i class="iconfont icon-news2"></i>
    				<span>IM消息中心</span>
    				<b></b>
    				<i class="iconfont fr icon-arrow-right"></i>
    			</a>
    		</li>
    	</ul>
    </div>

    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/intlTelInput.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script>
        $(function () {
            var k = getCookie("key");
            var u = getCookie("id");
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Buyer_Message&met=getMessageCount&typ=json",
                data: {k:k,u:u},
                dataType: "json",
                success: function (res) {
                    if (res.status == 200) {
                        $("#look").addClass('active');
                    }else{
                        $("#look").removeClass('active');
                    }
                }
            });
        })
        function go_message() {
            if (!getCookie("key")) {
                window.location.href = ShopWapUrl + "/tmpl/member/login.html";
            } else {
                window.location.href = ImApiUrl;
            }
        }

        function go_detail() {
            if (getCookie("key")) {
                $.ajax({
                    type: "post",
                    url: ApiUrl + "/index.php?ctl=Buyer_Message&met=editMessage&typ=json",
                    data: {k: getCookie("key"), u: getCookie("id"),user_id: getCookie("id")},
                    dataType: "json",
                    success: function (res) {
                        if (res.status == 200) {
                            window.location.href = ShopWapUrl + "/tmpl/message_detail.html";
                        }
                    }
                });
            } else {
                window.location.href = ShopWapUrl + "/tmpl/member/login.html";
            }
        }

    </script>
</body>
<?php
include __DIR__ . '/../includes/footer.php';
?>
</html>

