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
            <div class="tit">系统通知</div>
        </div>
    </header>
    <div class="nctouch-main-layout">
    	<ul class="message-notice-items pl-20 pr-20">

		</ul>
    </div>
    <script type="text/html" id="bargain-info-lists-tmpl">
        <% if(items.length > 0) { %>
            <% for (var i = 0; i < items.length; i++) { var item = items[i];  %>
            <li>
                <a href="javascript:;">
                    <h4><%=item.message_title%></h4>
                    <div class="message-common"><%=item.message_content%></div>
                </a>
            </li>
            <% } %>
        <% } %>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/intlTelInput.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/message_detail.js"></script>
</body>
<?php
include __DIR__ . '/../includes/footer.php';
?>
</html>