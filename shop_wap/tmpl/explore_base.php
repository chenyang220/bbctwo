<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('心得详情') ?></title>
    <link rel="stylesheet" href="../css/swiper.min.css">
    <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_e3lgd0cov07.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/nctouch_common.css">
    <link rel="stylesheet" href="../css/express.css">
    <link rel="stylesheet" href="../css/heart.css">
    <link rel="stylesheet" href="https://at.alicdn.com/t/font_1176416_z32macx1uy.css">
</head>
<body>
<div>
    <header id="header" class="posf heart-detail-header">
        <div class="header-wrap">
            <div class="fl">
                <a href="javascript:window.history.back();"> <i class="iconfont icon-arrow-left"></i> </a>
            </div>
<!--            <a class="fr heart-detail-share js-heart-share" href="javascript:;"><i class="iconfont icon-share mr-10"></i></a>-->
            <?php if ($_COOKIE['is_app_guest']) { ?>
                <a class="fr heart-detail-share js-heart-share" href="javascript:;">分享<i class="iconfont icon-share mr-10"></i></a>
            <?php } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'UCWEB') !== false) { ?>
                <a class="fr heart-detail-share js-heart-share" href="javascript:;">分享<i class="iconfont icon-share"></i></a>
            <?php } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false) { ?>
                <a class="fr heart-detail-share js-heart-share" href="javascript:;">分享<i class="iconfont icon-share"></i></a>
            <?php } ?>
            <a class="fr heart-detail-share js-heart-delete hide" href="javascript:;">分享<i class="iconfont icon-delete"></i><em></a>
            <div id="explore_head_user" class="hide"></div>

        </div>
    </header>
    <p class="goods-shelves hide"><span><?= __('此内容存在违规，已被系统下架，其他用户将无法查看！') ?></span></p>
    <div class="swiper-container heart-banner-swiper">
        <ul class="swiper-wrapper" id="explore_images">
        </ul>
        <div class="swiper-pagination heart-banner-pagination"></div>
    </div>
    <div class="heart-detail-content mb-120">
        <div class="bgf mb-20" id="explore_base">
        </div>
        <div class="bgf mb-20" id="explore_goods">
        </div>
        <div class="bgf" id="explore_comment">
        </div>
    </div>
    <div class="heart-detail-bottom" id="explore_info">
    </div>
</div>
<!-- 商品违规下架 -->
<div class="explore_report_content hide"><!-- hide隐藏 -->
    <header id="header" class="posf">
        <div class="header-wrap">
            <div class="fl">
                <div class="header-l"><a href="javascript:history.back()"> <i class="back"></i> </a></div>
            </div>

        </div>
    </header>
    <div class="nctouch-main-layout">
        <div class="module-tips social-nodata">
            <em class="img-box"><img src="../images/new/tips-img3.png" alt="img"></em>
            <p class="warning"><?= __('此内容涉嫌违规，已下架') ?></p>
        </div>
    </div>

</div>

<!-- 弹窗-评论列 -->
<div class="social-push-infor-alert nctouch-bottom-mask down" id="heart-comments-more">
</div>

<!-- 弹窗-分享 -->
<div class="nctouch-bottom-mask down" id="heart-share">
    <div class="nctouch-bottom-mask-bg"></div>
    <div class="nctouch-bottom-mask-block height-auto">
        <div class="heart-share-area tc">
            <h4><span><?= __('分享到') ?></span></h4>
            <ul class="clearfix borb1" id="share-box">

            </ul>
            <ul class="clearfix mt-30">
                <li class="jubao is_jubao">
                    <a href="javascript:;" class="js-heart-jubao">
                        <span><i class="iconfont icon-jubao"></i></span>
                        <h5><?= __('举报') ?></h5>
                    </a>
                </li>
                <li class="jubao edit hide">
                    <a href="javascript:;" class="js-heart-edit">
                        <span><i class="iconfont icon-pen2"></i></span>
                        <h5><?= __('编辑') ?></h5>
                    </a>
                </li>
                <li class="jubao delete hide">
                    <a href="javascript:;" class="js-heart-del">
                        <span><i class="iconfont icon-del1"></i></span>
                        <h5><?= __('删除') ?></h5>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>

<!-- 弹窗-举报 -->
<div class="nctouch-bottom-mask down" id="heart-jubao">
    <div class="nctouch-bottom-mask-bg"></div>
    <div class="nctouch-bottom-mask-block nctouch-bottom-mask-block2  minh-auto">
        <div class="discover-operate-box">
            <div class="discover-operate-content">
                <h4 class="borb1"><span><?= __('请选择举报原因') ?></span><i class="iconfont icon-close" id="jubao-close"></i></h4>
                <ul class="js-jubao-reason" id="reason">
                </ul>
            </div>
            <button class="btn-discover-operate" id="jubao-btn"><?= __('举报') ?></button>

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
                    <li class="comment-operate-del"><span><?= __('删除') ?></span></li>
                </ul>
            </div>
            <button class="btn-discover-operate"><?= __('取消') ?></button>
        </div>
    </div>
</div>
<!-- 登录弹框提示 -->
<div class="dialog tc social-login-dialog">
    <div class="table">
        <div class="table-cell">
            <div class="content">
                <p class="social-login-tips"><?= __('请先登录') ?></p>
                <div><a href="javascript:;" class="social-login login"><?= __('登录') ?></a></div>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="images_list">
    <% for (var i =0;i < explore_images.length; i++){ %>
        <% if (explore_images[i].type == '.mov' || explore_images[i].type == '.mp4'){ %>
            <li class="swiper-slide">
                <video  controls="controls"  src="<%=explore_images[i].images_url%>" id="ckplayer_a1" x5-video-player-type="h5" preload="metadata" playsinline="true"
                        webkit-playsinline="true"  x-webkit-airplay="true"   alt="video">
                </video>
            </li>
        <% }else{ %>
            <li class="swiper-slide">
                <img src="<%=explore_images[i].images_url%>" alt="img">
            </li>
        <% } %>
    <% } %>
</script>

<script type="text/html" id="explore">
    <div class="clearfix heart-content-publisher borb1">
        <a href="./explore_center.php?user_id=<%=explore_base.user_id%>"><em class="img-box fl"><img src="<%=user_info.user_logo%>" alt=""></em></a>
        <div class="fl">
            <a href="./explore_center.php?user_id=<%=explore_base.user_id%>"><span class="one-overflow"><%=user_info.user_account%></span></a>
            <p><%=explore_base.explore_create_date%></p>
        </div>
        <% if(user_info.is_author !== 1) {%>
        <% if(user_info.is_follow !== 1) {%>
        <span class="fr btn-follow follow-btn" data-id="<%=explore_base.user_id%>"><i class="iconfont icon-add"></i><em><?= __('关注') ?></em></span>
        <% }else{ %>
        <span class="fr btn-follow active follow-btn" data-id="<%=explore_base.user_id%>"><em><?= __('已关注') ?></em></span>
        <% } %>
        <% } %>
    </div>
    <div class="heart-publish-text borb1">
        <h3><%=explore_base.explore_title%></h3>
        <pre><%=explore_base.explore_content%></pre>
    </div>
    <div class="heart-publish-tags swiper-container">
        <ul class="swiper-wrapper">
            <% if(explore_lable) {
            for (var i =0;i < explore_lable.length; i++){
            %>

            <li class="swiper-slide">
                        <span>
                            <i class="iconfont icon-jing"></i>
                            <em><%=explore_lable[i].lable_content%></em>
            </span>
            </li>
            <% } } %>
        </ul>
    </div>
</script>
<!-- 滑动显示头部信息 -->
<script type="text/html" id="head-infor">
    <div class="fl">
        <a class="head-heart-user" href="./explore_center.php?user_id=<%=explore_base.user_id%>">
            <em class="img-box"><img src="<%=user_info.user_logo%>" alt=""></em>
            <span><%=user_info.user_account%></span>
        </a>
    </div>
    <% if(user_info.is_author !== 1) {%>
    <% if(user_info.is_follow !== 1) {%>
    <a href="javascript:;" class="btn-follow fr discover-head-follow follow-btn" data-id="<%=explore_base.user_id%>"><i class="iconfont icon-add"></i><em>关注</em></a>
    <% }else{ %>
    <a href="javascript:;" class="btn-follow fr discover-head-follow follow-btn active" data-id="<%=explore_base.user_id%>"><em><?= __('已关注') ?></em></a>
    <% } %>
    <% } %>

</script>

<script type="text/html" id="goods">
    <ul class="push-rel-goods-links">
        <% if(goods.goods) {
        for (var i =0;i < goods.goods.length; i++){
        %>
        <li class="clearfix">
            <a href="product_detail.html?cid=<%=goods.goods[i].goods_common_id%>">
                <em class="img-box"><img src="<%=goods.goods[i].common_image%>" alt=""></em>
                <div>
                    <span class="one-overflow"><%=goods.goods[i].common_name%></span>
                    <i class="iconfont icon-arrow-right fr"></i>
                    <strong class="fr"><?= __('￥'); ?><%=goods.goods[i].common_price%></strong>
                </div>
            </a>
        </li>
        <% } } %>

        <% if(goods.sum > 5){ %>
        <li class="tc">
            <a class="fz-24 col-red" href="./explore_goods_list.html?explore_id=<%=explore_base.explore_id%>"><?=__('查看全部')?><%=goods.sum%><?=__('个商品')?>&nbsp;》</a>
        </li>
        <% } %>
    </ul>
</script>

<script type="text/html" id="comment">
    <div class="heart-comments-tit borb1"><span><?= __('评论') ?></span><% if(comment.sum > 0){ %><em>(<%=comment.sum%>)</em><% } %></div>
    <div class="heart-comment-box">
        <ul class="heart-comment-items">
            <% if(comment.sum == 0){ %>
            <!-- 无评论 -->
            <li>
                <a href="./explore_center.php?user_id=<%=explore_base.user_id%>"><em class="img-box"><img src="<%=user_info.user_logo%>" alt=""></em></a>
                <div class="heart-comment-rel">
                    <span class="comment-tips"><?= __('“点赞再多，不如评论互撩”') ?></span>
                </div>
            </li>
            <% }else{ %>
            <!-- 有评论 -->
            <% for (var i =0;i < comment.comment.length; i++){ %>
            <li>
                <a href="./explore_center.php?user_id=<%=comment.comment[i].user_id%>"><em class="img-box"><img src="<%=comment.comment[i].user_logo%>" alt=""></em></a>
                <div class="heart-comment-rel">
                    <div class="heart-comment-rel-one js-more-comment">
                        <a href="./explore_center.php?user_id=<%=comment.comment[i].user_id%>"><strong class="one-overflow"><%=comment.comment[i].user_account%></strong><% if(comment.comment[i].is_author > 0){ %> <b class="heart-comment-more-author"><?= __('(作者)') ?> </b><% } %></a>
                        <p><em><%=comment.comment[i].comment_content%></em></p>
                        <p class="clearfix">
                            <time class="time"><%=comment.comment[i].comment_adddate%></time>
                            <% if(comment.comment[i].is_like == 0){ %>
                            <span class="fr heart-comment-goods like-comment-btn" data-id="<%=comment.comment[i].comment_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?></em></span>
                            <% } else { %>
                            <span class="fr heart-comment-goods active like-comment-btn" data-id="<%=comment.comment[i].comment_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?></em></span>
                            <% }%>
                        </p>
                    </div>
                    <!-- 回复 -->
                    <% if(comment.comment[i].reply.sum > 0 ){ %>
                    <div class="heart-comment-reply">
                        <% for (var j =0;j < comment.comment[i].reply.reply_list.length; j++){ %>
                        <p>
                            <a href="./explore_center.php?user_id=<%=comment.comment[i].reply.reply_list[j].user_id%>"><strong><%=comment.comment[i].reply.reply_list[j].user_account%></strong><% if(comment.comment[i].reply.reply_list[j].is_author > 0){ %> <b class="heart-comment-more-author"><?= __('(作者)') ?> </b><% } %></a>
                            <% if(comment.comment[i].reply.reply_list[j].to_reply_id > 0){ %>
                            <b><?= __('回复') ?></b>
                            <a href="./explore_center.php?user_id=<%=comment.comment[i].reply.reply_list[j].to_reply_user_id%>"><span class="one-overflow heart-comment-reply-agin">@<%=comment.comment[i].reply.reply_list[j].to_reply_user_account%></span><% if(comment.comment[i].reply.reply_list[j].to_reply_is_author > 0){ %> <b class="heart-comment-more-author"><?= __('(作者)') ?></b> <% } %></a>
                            <% } %>：
                            <em class="js-more-comment"><%=comment.comment[i].reply.reply_list[j].reply_content%></em>
                        </p>
                        <% } %>
                        <% if(comment.comment[i].reply.sum > 2 ){ %><p class="heart-comment-reply-more tc bort1-eb"><a class="js-more-comment" href="javascript:;"><?= __('共') ?><%=comment.comment[i].reply.sum%><?= __('条回复') ?>&nbsp;></a></p><% } %>
                    </div>
                    <% } %>
                </div>
            </li>
            <% } }%>
        </ul>
        <% if(comment.sum > 3){ %><p class="heart-comment-more tc"><a class="js-more-comment" href="javascript:;"><?= __('查看全部') ?><%=comment.sum%><?= __('条评论') ?>&nbsp;></a></p><% } %>
    </div>
</script>

<script type="text/html" id="info">
    <% if(explore_base.is_like > 0) { %>
    <span class="like-explore-btn active"><i class="iconfont icon-like-b"></i><em><?= __('赞') ?></em><b class="num <% if(explore_base.explore_like_count ==  0) { %>hide<% } %>"><%=explore_base.explore_like_count%></b></span>
    <% } else {%>
    <span class="like-explore-btn"><i class="iconfont icon-like-b"></i><em><?= __('赞') ?></em><b class="num <% if(explore_base.explore_like_count ==  0) { %>hide<% } %>"><%=explore_base.explore_like_count%></b></span>
    <% } %>

    <span class="js-more-comment js-more-comment-num"><i class="iconfont icon-pinglun"></i><em><?= __('评论') ?></em><b class="num <% if(comment.sum <= 0){ %>hide<% } %>"><%=comment.sum%></b></span>
    <% if(collection.start) {%>
        <span class="video-operate-operate-li js-video-save active mt0" id="shouchang"><i class="iconfont icon-star2"></i><em><?= __('收藏') ?></em></span>
    <% }else{ %>
        <span class="video-operate-operate-li js-video-save mt0" id="shouchang"><i class="iconfont icon-star2"></i><em><?= __('收藏') ?></em></span>
    <% } %>

    <% if(goods.sum > 0){ %>
    <div class="fr btn-heart-goods-buy">
        <% if(goods.sum ==1){ var i = 0;%>
        <a href="./product_detail.html?cid=<%=goods.goods[i].goods_common_id%>"><?= __('立即购买') ?></a>
        <% }else{ %>
        <a href="./explore_goods_list.html?explore_id=<%=explore_base.explore_id%>"><?= __('立即购买') ?></a>
        <% } %>
        <% if(goods.sum > 0){ %><b><%=goods.sum%></b><% } %>
    </div>
    <% } %>

</script>

<script type="text/html" id="comments-more">
    <div class="nctouch-bottom-mask-bg"></div>
    <div class="nctouch-bottom-mask-block">
        <div class="heart-commemt-more-tit clearfix borb1 pl-20 pr-20">
            <span><%=sum%><?= __('条评论') ?></span>
            <i class="iconfont icon-close comment-close"></i>
        </div>
        <div class="nctouch-bottom-mask-rolling overflow-auto" id="js-comment-scroll">
            <% if(sum == 0) { %>
            <div class="empty-comment-tips">
                <img src="../images/empty-comment.png" alt="">
                <p><?= __('“点赞再多，不如评论互撩”') ?></p>
            </div>
            <% } else { %>
            <ul class="heart-comment-items js-comment-opearate">
                <% for (var i =0;i < comment.length; i++){ %>
                <li data-id="<%=comment[i].user_id %>" comment-id="<%=comment[i].comment_id%>" class="comment<%=comment[i].comment_id%>" type="comment">
                    <a href="./explore_center.php?user_id=<%=comment[i].user_id%>"><em class="img-box"><img src="<%=comment[i].user_logo%>" alt=""></em></a>
                    <div class="heart-comment-rel">
                        <div class="heart-comment-rel-one">
                            <a href="./explore_center.php?user_id=<%=comment[i].user_id%>"><strong class="one-overflow"><%=comment[i].user_account%></strong><% if(comment[i].is_author > 0){ %><b class="heart-comment-more-author"><?= __('(作者)') ?></b><% } %></a>
                            <em class="comment_content"><%=comment[i].comment_content%></em>
                            <p class="clearfix">
                                <time class="time"><%=comment[i].comment_adddate%></time>
                                <% if(comment[i].is_like == 0) { %>
                                <span class="fr heart-comment-goods like-comment-btn" data-id="<%=comment[i].comment_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?></em></span>
                                <% }else{ %>
                                <span class="fr heart-comment-goods active like-comment-btn" data-id="<%=comment[i].comment_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?></em></span>
                                <% } %>
                            </p>
                        </div>

                        <!-- 回复 -->
                        <% if(comment[i].reply.sum > 0) {%>
                        <div class="heart-comment-more-reply">
                            <ul class="heart-comment-items heart-comment-more-items">
                                <% for (var j =0;j < comment[i].reply.reply_list.length; j++){ %>
                                <li data-id="<%=comment[i].reply.reply_list[j].user_id %>" comment-id="<%=comment[i].comment_id%>" reply-id="<%=comment[i].reply.reply_list[j].reply_id%>" class="reply<%=comment[i].reply.reply_list[j].reply_id%>" type="reply">
                                    <a href="./explore_center.php?user_id=<%=comment[i].reply.reply_list[j].user_id%>"><em class="img-box"><img src="<%=comment[i].reply.reply_list[j].user_logo%>" alt=""></em></a>
                                    <div class="heart-comment-rel">
                                        <div class="heart-comment-rel-one">
                                            <a href="./explore_center.php?user_id=<%=comment[i].reply.reply_list[j].user_id%>"><strong class="one-overflow"><%=comment[i].reply.reply_list[j].user_account%></strong><% if(comment[i].reply.reply_list[j].is_author > 0){ %><b class="heart-comment-more-author"><?= __('(作者)') ?></b><% } %></a>
                                            <% if(comment[i].reply.reply_list[j].to_reply_id > 0){ %>
                                            <b><?= __('回复') ?></b>
                                            <a href="./explore_center.php?user_id=<%=comment[i].reply.reply_list[j].to_reply_user_id%>"><span class="one-overflow heart-comment-reply-agin">@<%=comment[i].reply.reply_list[j].to_reply_user_account%></span><% if(comment[i].reply.reply_list[j].to_reply_is_author > 0){ %> <b class="heart-comment-more-author"><?= __('(作者)') ?></b> <% } %></a>
                                            <% } %>：
                                            <em class="reply_content"><%=comment[i].reply.reply_list[j].reply_content%></em>
                                            <p class="clearfix">
                                                <time class="time"><%=comment[i].reply.reply_list[j].reply_adddate%></time>
                                                <% if(comment[i].reply.reply_list[j].is_like == 0) { %>
                                                <span class="fr heart-comment-goods like-reply-btn" data-id="<%=comment[i].reply.reply_list[j].reply_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?></em></span>
                                                <% }else{ %>
                                                <span class="fr heart-comment-goods active like-reply-btn" data-id="<%=comment[i].reply.reply_list[j].reply_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?></em></span>
                                                <% } %>
                                            </p>
                                        </div>
                                    </div>
                                </li>
                                <% } %>
                            </ul>
                            <% if(comment[i].reply.sum > 2) {%><p class="heart-comment-reply-more tc bort1-eb"><a href="./explore_reply.php?comment_id=<%= comment[i].comment_id%>&explore_id=<%= comment.explore_id%>"><?= __('共') ?><%=comment[i].reply.sum%><?= __('条回复') ?>&nbsp;&gt;</a></p><% } %>
                        </div>
                        <% } %>
                    </div>
                </li>
                <% } %>
            </ul>
            <% } %>
        </div>
        <div class="heart-views-edit">
            <div class="heart-views-edit-input"><i class="iconfont icon-pen1"></i><input id="comment_content" type="text" placeholder="<?= __('“点赞再多，不如评论互撩”') ?>"></div>
            <button class="btn-heart-views-send" id="send"><?= __('发送') ?></button>
        </div>
    </div>
</script>

<script type="text/html" id="reason_list">
    <%  for (var i =0;i < data.length; i++){ %>
    <li data-id="<%=data[i].explore_report_reason_id%>"><span><%=data[i].explore_report_reason_content%></span><i class="iconfont icon-yes-bold"></i></li>
    <% } %>
    <li data-id="0"><span><?= __('其他') ?></span><i class="iconfont icon-yes-bold"></i><textarea id="reason_content" placeholder="请填写“其他”举报原因" maxlength="100"></textarea></li>
</script>



</body>

<script type="text/javascript" src="../js/zepto.min.js"></script>
<script type="text/javascript" src="../js/iscroll.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/swiper.min.js"></script>
<script type="text/javascript" src="../js/share.js"></script>
<script type="text/javascript" src="../js/explore_base.js"></script>

</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>



