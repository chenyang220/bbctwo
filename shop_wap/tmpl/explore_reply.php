<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/heart.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
    <link rel="stylesheet" href="../css/nctouch_common.css">
    <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_uq2l4uwdui.css">
</head>
<body>
	<header id="header" class="posf">
        <div class="header-wrap">
             <div class="header-l">
                <!-- <a href="javascript:window.history.back();"> <i class="back"></i> </a> -->
            </div>
            <div class="tit"><?=__('共0条回复')?></div>
        </div>
    </header>
    <div class="nctouch-main-layout heart-detail-content">
        <div class="bgf">
             <div id="heart-comment-box"></div>
             <div class="heart-views-edit heart-views-edit-fixed">
                <div class="heart-views-edit-input"><i class="iconfont icon-pen1"></i><input id="reply_content" type="text" to-reply-id = "" placeholder="发表回复"></div>
                <button class="btn-heart-views-send" id="send" explore-id=""><?=__('发送')?></button>
            </div>
        </div>
    </div>
    <!-- 弹窗-删除/回复评论 -->
    <div class="nctouch-bottom-mask down" id="comment-opearate">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block nctouch-bottom-mask-block2  minh-auto">
            <div class="discover-operate-box">
                <div class="discover-operate-content">
                    <h4 class="borb1"><span class="del-content"></span></h4>
                    <ul class="comment-operate">
                        <li class="comment-operate-reply"><span><?=__('回复')?></span></li>
                        <li class="comment-operate-del"><span><?=__('删除')?></span></li>
                    </ul>
                </div>
                <button class="btn-discover-operate"><?=__('取消')?></button>
            </div>
        </div>
    </div>

    <script id="comments-more" type="text/template">
        <ul class="heart-comment-items mb-120 js-comment-opearate">
            <% for(var i = 0;i<data.reply.length;i++){ %>
            <% var list = data.reply%>
            <li id='reply_type' data-id="<%=list[i].user_id %>" comment-id="<%=list[i].comment_id%>" reply-id="<%=list[i].reply_id%>" class="reply<%=list[i].reply_id%>"  type="reply">
                <em class="img-box"><img src="<%=list[i].user_logo%>" alt=""></em>
                <div class="heart-comment-rel">
                    <div class="heart-comment-rel-one">
                        <p>
                            <% if(list[i].to_reply_id > 0){ %>
                            <b><?=__('回复')?></b>
                            <a href="./explore_center.php?user_id=<%=list[i].to_reply_user_id%>">
                                <span class="one-overflow heart-comment-reply-agin">
                                    @<%=list[i].to_reply_user_account%>
                                </span>
                                <% if(list[i].is_author == 1){ %>
                                <b ><?=__('(作者)')?></b>
                                <% } %>
                            </a>
                            <% } %>：
                        </p>
                        <strong class="one-overflow"><%=list[i].user_account%></strong>
                        <em class="comment_content"><%=list[i].reply_content%></em>
                        <p class="clearfix">
                            <time class="time"><%=list[i].reply_adddate%></time>
                            <% if(list[i].is_like == 0) { %>
                            <span class="fr heart-comment-goods like-reply-btn" data-id="<%=list[i].reply_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?=__('赞')?></em></span>
                            <% }else{ %>
                            <span class="fr heart-comment-goods active like-reply-btn" data-id="<%=list[i].reply_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?=__('赞')?></em></span>
                            <% } %>
                        </p>
                    </div>
                </div>
            </li>
            <% } %>
        </ul>
    </script>

    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/iscroll.js"></script>
    <script type="text/javascript" src="../js/explore_reply.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>

</body>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>