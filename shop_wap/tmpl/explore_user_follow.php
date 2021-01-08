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
	<link rel="stylesheet" href="../css/heart.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_8nvkmjugmdc.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
</head>
<body>
	<header id="header" class="">
        <div class="header-wrap">
             <div class="header-l">
                <!-- <a href="javascript:window.history.back();"><i class="icon-back"></i></a> -->
            </div>
            <div class="tit"><?=__('我关注的')?></div>
        </div>
    </header>
    <div class="discover-main-layout nctouch-main-layout">
        <div class="">
             <div id="fid-rgt"></div>
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
    <script id="follow_no_content" type="text/template">
        <div class="module-tips social-nodata">
            <em class="img-box"><img src="../images/new/tips-img-follow.png" alt="img"></em>
            <p><?=__('还没有关注的人')?></p>
        </div>
    </script>
    <script id="follow-template" type="text/template">
        <ul class="discover-friends-items pl-20 pr-20 bgf">
            <% for(var i = 0;i<data.user.length;i++){ %>
            <li id="fans-message" >
                <a href="./explore_center.php?user_id=<%=data.user[i].user_id%>"><em class="img-box"><img src="<%= data.user[i].user_logo%>" alt=""></em></a>
                <b><i class="iconfont icon-gouxuanicon"></i></b>
                <div class="discover-friends-text">
                    <div class="hp100">
                        <span class="one-overflow"><%= data.user[i].user_name%></span>
                        <p class="one-overflow wp70"><%= data.user[i].user_sign%></p>
                    </div>
                    <% if(data.user[i].is_follow == 1) { %>
                        <a class="btn-follow btn-attention active" href="javascript:;" user-id="<%= data.user[i].user_id%>"><em>已关注</em></a>
                    <% }else{ %>
                        <a class="btn-follow btn-attention" href="javascript:;" user-id="<%= data.user[i].user_id%>"><i class="iconfont icon-add"></i><em>关注</em></a>
                    <% } %>
                </div>
            </li>
            <% } %>
        </ul>
    </script>

    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/iscroll.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/explore_user_follow.js"></script>
    <script>
        $(".login").click(function(e){
            e.preventDefault();
            var preUrl=window.location.href;
            window.location.href = ShopWapUrl + "/tmpl/member/login.html?callback=" + preUrl;
        });
    </script>
</body>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>