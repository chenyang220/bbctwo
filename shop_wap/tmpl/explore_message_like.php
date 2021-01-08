<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=__('点赞')?></title>
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/heart.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
    <link rel="stylesheet" href="../css/nctouch_common.css">
    <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_uq2l4uwdui.css">
</head>
<body>
	<header id="header" class="">
        <div class="header-wrap">
             <div class="header-l">
                <a  href="javascript:;" onclick="self.location=document.referrer;"><i class="icon-back"></i></a>
            </div>
            <div class="tit"><?=__('点赞')?></div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <!-- 有数据 -->
        <ul class="heart-views-comments"  id="like">
        </ul>
        <!-- 无数据 -->
        <div class="social-nodata hide">
            <em class="img-box"><img src="../images/icons/zan.png" alt="icon"></em>
            <p><?=__('暂时还没有人给你点赞哦')?></p>
        </div>
    </div>
</body>

<script type="text/javascript" src="../js/zepto.min.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/explore_message_like.js"></script>

<script  type="text/html" id="like-more">
    <% if (data.items.length > 0){ for (var i =0;i < data.items.length; i++){ %>
        <%if(data.items[i].message_islook == 0) {%>
            <li class="active jump" explore_id="<%=data.items[i].explore_id%>" active_id="<%=data.items[i].active_id%>" is_del="<%=data.items[i].is_del%>" type="<%=data.items[i].message_type%>">
        <% } else { %>
            <li class="jump" explore_id="<%=data.items[i].explore_id%>" active_id="<%=data.items[i].active_id%>" is_del="<%=data.items[i].is_del%>" type="<%=data.items[i].message_type%>">
        <% } %>
        <div class="heart-views-who">
            <a href="./explore_center.php?user_id=<%=data.items[i].active_user_id%>"><em class="img-box"><img src="<%=data.items[i].active_user_logo%>" alt=""></em></a>
            <div>
                <p class="more-overflow"><a href="./explore_center.php?user_id=<%=data.items[i].active_user_id%>"><strong class="one-overflow"><%=data.items[i].active_user_account%></strong></a><em><?=__('赞了你的')?><%=data.items[i].type%></em>
                    <% if(data.items[i].is_del > 0){ %>
                    <span><?=__('该')?><%=data.items[i].type%><?=__('已删除')?><span>
                    <% }else{ %>
                    <a href="./explore_base.php?explore_id=<%=data.items[i].explore_id%>&active_id=<%=data.items[i].active_id%>&type=<%=data.items[i].message_type%>"><span><%=data.items[i].active_content%></span></a>
                    <% } %>
                </p>
                <p><time><%=data.items[i].message_create_date%></time></p>
            </div>
        </div>
        <div class="heart-comments-obj">
            <% if(data.items[i].explore_del > 0 ){ %>
            <img src="../img/social-img10.png" alt="<?=__('该')?><%=data.items[i].type%><?=__('已删除')?>">
            <% }else{ %>
            <a class="block wp100 hp100" style="background:url(<%=data.items[i].explore_images[0]%>) no-repeat center;background-size:cover;" href="./explore_base.php?explore_id=<%=data.items[i].explore_id%>"></a>
            <% } %>
        </div>
    </li>
    <% }} %>
</script>

</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>