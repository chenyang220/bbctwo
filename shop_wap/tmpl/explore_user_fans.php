<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?=__('我的粉丝')?></title>
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="../css/base.css">
	<link rel="stylesheet" href="../css/heart.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_8nvkmjugmdc.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
</head>
<body>
	<header id="header" class="">
        <div class="header-wrap">
             <div class="header-l">
                <!-- <a href="javascript:;" class="back"><i class="icon-back"></i></a> -->
            </div>
            <div class="tit"><?=__('我的粉丝')?></div>
        </div>
    </header>
    <div class="discover-main-layout nctouch-main-layout">
        <div>
           <div id="fid-rgt">
            </div>
            <div id="load-completion-di"></div>

        </div>
    </div>
    <!-- 登录弹框提示 -->
    <div class="dialog tc social-login-dialog">
        <div class="table">
            <div class="table-cell">
                <div class="content">
                    <p class="social-login-tips"><?=__('请先登录')?></p>
                    <div><a href="javascript:;" class="social-login login"><?=__('登录')?></a></div>
                </div>
            </div>
        </div>
    </div>
    <script id="fans-no-content" type="text/template">
        <div class="module-tips social-nodata">
            <em class="img-box"><img src="../images/new/tips-img-fans.png" alt="img"></em>
            <p><?=__('还没有粉丝哦')?>~</p>
        </div>
    </script>
    <script id="fans-template" type="text/template">
        <ul class="discover-friends-items pl-20 pr-20 bgf">
            <% for(var i = 0;i<data.user.length;i++){ %>
                <li id="fans-message" >
                    <a href="./explore_center.php?user_id=<%=data.user[i].user_id%>"><em class="img-box"><img src="<%= data.user[i].user_logo%>" alt=""></em></a>
                    <div class="discover-friends-text">
                        <div class="hp100">
                            <span class="one-overflow"><%= data.user[i].user_name%></span>
                        </div>
                        <% if (data.user[i].attention_status == true){ %>
                        <a class="btn-follow btn-attention active" href="javascript:;" user-id="<%= data.user[i].user_id%>">
                            <em><?=__('已关注')?></em>
                        </a>
                        <% } else { %>
                        <a class="btn-follow btn-attention" href="javascript:;" user-id="<%= data.user[i].user_id%>">
                            <i class="iconfont icon-add"></i><em><?=__('关注')?></em>
                        </a>
                       <% } %>

                    </div>
                </li>
            <% } %>
        </ul>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>
    <script type="text/javascript" src="../js/explore_user_fans.js"></script>
    <script>
        $(".login").click(function(){
            window.location.href = ShopWapUrl + "/tmpl/member/login.html";
        });

        $(".back").click(function(){
            if(getQueryString("from") == 'user') {
                history.go(-1);
            } else {
                window.location.href = ShopWapUrl + "/tmpl/explore_message.html";
            }
        });
    </script>
</body>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>