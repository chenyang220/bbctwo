<?php
include __DIR__ . '/../includes/header.php';
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>评论</title>
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
                <!-- <a href="javascript:;" onclick="self.location=document.referrer;"><i class="icon-back"></i></a> -->
            </div>
            <div class="tit"><?=__('评论')?></div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <!-- 有数据 -->
        <ul class="heart-views-comments" id="comment">
        </ul>
        <!-- 无数据 -->
        <div class="social-nodata hide">
            <em class="img-box"><img src="../images/icons/comments.png" alt="icon"></em>
            <p><?=__('暂时还没有人给你评论哦')?></p>
        </div>
    </div>
    </body>

    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/explore_message_comment.js"></script>

    <script  type="text/html" id="comment-more">
        <% if (data.items.length > 0){ for (var i =0;i < data.items.length; i++){ %>
        <%if(data.items[i].message_islook == 0) {%>
        <li class="active jump" explore_id="<%=data.items[i].explore_id%>" active_id="<%=data.items[i].active_id%>" is_del="<%=data.items[i].is_del%>" type="<%=data.items[i].reply_type%>">
            <% } else { %>
        <li  class="jump" explore_id="<%=data.items[i].explore_id%>" active_id="<%=data.items[i].active_id%>" is_del="<%=data.items[i].is_del%>" type="<%=data.items[i].reply_type%>">
            <% } %>
            <div class="heart-views-who">
                <a href="./explore_center.php?user_id=<%=data.items[i].active_user_id%>"><em class="img-box"><img src="<%=data.items[i].active_user_logo%>" alt=""></em></a>
                <div>
                    <p class="more-overflow"><a href="./explore_center.php?user_id=<%=data.items[i].active_user_id%>"><strong class="one-overflow"><%=data.items[i].active_user_account%></strong></a><em><%=data.items[i].type%><?=__('了')?></em>
                        <% if(data.items[i].to_is_del > 0){ %>
                                <span><?=__('该')?><%=data.items[i].to_type%><?=__('已删除')?><span>
                        <% }else{ %>
                                <span><%=data.items[i].to_active_content%></span>
                        <% } %>
                    </p>
                    <p><time><%=data.items[i].message_create_date%></p>
                    <% if(data.items[i].is_del > 0){ %>
                    <p class="heart-comments-text"><?=__('该')?><%=data.items[i].type%><?=__('已删除')?></p>
                    <% }else{ %>
                        <% if(data.items[i].active_content) { %>
                        <p class="heart-comments-text"><%=data.items[i].active_content%></p>
                        <% } %>
                    <% } %>

                </div>
            </div>
            
            <div class="heart-comments-obj">
                <% if(data.items[i].explore_del > 0){ %>
                <img src="../img/social-img10.png" alt="<?=__('该')?><%=data.items[i].to_type%><?=__('已删除')?>">
                <% }else{ %>
                <a class="iblock wp100 hp100" style="background:url(<%=data.items[i].explore_images[0]%>) no-repeat center;background-size:cover;" href="./explore_base.php?explore_id=<%=data.items[i].explore_id%>"></a>
                <% } %>
            </div>
        </li>
        <% }} %>
    </script>

    </html>
<?php
include __DIR__ . '/../includes/footer.php';
?>