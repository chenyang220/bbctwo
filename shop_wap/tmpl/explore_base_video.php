<?php
include __DIR__ . '/../includes/header.php';
?>
    <!DOCTYPE html>
    <html class="hp100" lang="en">
    <head>
        <meta charset="UTF-8">

        <title><?= __('心得详情') ?></title>
        <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1"/>
        <link rel="stylesheet" href="../css/base.css">
        <link rel="stylesheet" href="../css/nctouch_common.css">
        <link rel="stylesheet" href="../css/express.css">
        <link rel="stylesheet" href="../css/heart.css">
        <link rel="stylesheet" href="https://at.alicdn.com/t/font_1176416_z32macx1uy.css">
        <link rel="stylesheet" href="../css/swiper.min.css">
        <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_e3lgd0cov07.css">
        <link rel="stylesheet" href="https://at.alicdn.com/t/font_1176416_hj68rhij2du.css">
        <style>
            body {
                background: #000;
            }

            #explore_images {
                position: relative;
                z-index: 1;
            }

            .video-operate {
                z-index: 1000;
            }

            .video-wrap {
                width: 100%;
                display: none;
            }
        </style>
    </head>
    <body class="hp100">
    <header id="header" class="posf bg-transparent borb0">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:window.history.back();"> <b class="iconfont icon-arrow-left colf"></b></a> -->
            </div>
            <div class="tit tit-nav2">
                <a class="active" href="<?php echo $WapSiteUrl; ?>/tmpl/explore_list.html?type=1">关注</a>
                <a href="<?php echo $WapSiteUrl; ?>/tmpl/explore_list.html?type=2">发现</a></div>
            <div class="header-r header-video">
                <a href="javascript:;">
                    <i class="iconfont icon-xiangji1"></i>
                </a><a href="javascript:;">
                    <i class="iconfont icon-user"></i>
                </a>
            </div>
        </div>
    </header>
    <div class="hp100">
        <div id="explore_images" class="hp100">
        </div>
        <script type="text/html" id="images_list">
			<div class="table wp100 hp100">
				<div class="table-cell">
					<% if (explore_images[0].type == '.mov' || explore_images[0].type == '.mp4'){ %>
					 <video class="video-wrap wp100" controls="controls" id="video-ios" x5-playsinline="true" playsinline="true" webkit-playsinline="true" autoplay="true" src="<%=explore_images[0].images_url%>" poster="<%=explore_images[0].poster_images%>" loop></video>
					<!--  <canvas class="video-wrap" id="video-android" data-autoplay="true"></canvas> -->
					 <div class="msg-wrap" id="msgTxt">loading...</div>
					
					 <% }%>
				</div>
			</div>
            
        </script>

        <script>
            document.addEventListener("WeixinJSBridgeReady", function () {
                document.getElementById('video-ios').play(); //视频自动播放
            }, false);
        </script>

        <script type="text/html" id="info">
            <div class="video-operate-user tc fz-0 pb-20">
                <a href="./explore_center.php?user_id=<%=explore_base.user_id%>"> <em class="img-box"><img class="cter" src="<%= user_info.user_logo %>" alt="user"></em></a>
                <% if(user_info.is_author !== 1) {%>
                <% if(user_info.is_follow !== 1) {%>
                <span class="follow-btn" data-id="<%=explore_base.user_id%>"><i class="iconfont icon-add"></i><em>关注</em></span>
                <!--                        <span class="fr btn-follow follow-btn" data-id="<%=explore_base.user_id%>"><i class="iconfont icon-add"></i><em>--><? //= __('关注') ?><!--</em></span>-->
                <% }else{ %>
                <span class="follow-btn" data-id="<%=explore_base.user_id%>"><em>已关注</em></span>
                <% } %>
                <% } %>

            </div>

            <% if(explore_base.is_like > 0) { %>
            <div class="video-operate-operate-li js-video-zan video-zan active">
                <i class="iconfont icon-zan"></i><span class="block"><b class="num <% if(explore_base.explore_like_count ==  0) { %>hide<% } %>"><%=explore_base.explore_like_count%></b></span>
            </div>
            <% } else {%>
            <div class="video-operate-operate-li js-video-zan video-zan">
                <i class="iconfont icon-zan"></i><span class="block"><b class="num <% if(explore_base.explore_like_count ==  0) { %>hide<% } %>"><%=explore_base.explore_like_count%></b></span>
            </div>
            <% } %>


            <div class="video-operate-operate-li js-video-comments">
                <i class="iconfont icon-xiaoxi"></i><span class="block"><b class="num <% if(comment.sum <= 0){ %>hide<% } %>"><%=comment.sum%></b></span>
            </div>


            <div class="video-operate-operate-li">
                <?php if ($_COOKIE['is_app_guest']) { ?>
                    <a class="heart-detail-share js-heart-share pl-0" href="javascript:;"><i class="iconfont icon-fenxiang"></i><span class="block">分享</span></a>
                <?php } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'UCWEB') !== false) { ?>
                    <a class="heart-detail-share js-heart-share pl-0" href="javascript:;"><i class="iconfont icon-fenxiang"></i><span class="block">分享</span></a>
                <?php } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false) { ?>
                    <a class="heart-detail-share js-heart-share pl-0" href="javascript:;"><i class="iconfont icon-fenxiang"></i><span class="block">分享</span></a>
                <?php } ?>
                <a class="heart-detail-share js-heart-delete hide" href="javascript:;"><i class="iconfont icon-fenxiang"></i><span class="block">分享</span></a>
            </div>

            <div class="video-operate-operate-li js-video-save">
                <% if(collection.start) {%>
                <i class="iconfont icon-star2 active"></i><span class="block">收藏</span>
                <% }else{ %>
                <i class="iconfont icon-star2"></i><span class="block">收藏</span>
                <% } %>
            </div>

        </script>

        <div class="video-operate" id="video-operate">

        </div>


        <div class="video-bottom-operate" id="video-bottom-operate">

        </div>

        <script type="text/html" id="content_info">
            <div class="video-bottom-operate-text">
				<!-- <% if(goods.sum>0){%>
				<a class="btn-see-video-det js-btn-video-det relatives" href="./explore_goods_list.html?explore_id=<%=explore_base.explore_id%>"><?= __('视频同款') ?>
				</a>
				<%}%> -->
                <% if(explore_lable_name){ %>
                <div class="video-signs">
                    <b><i class="iconfont icon-jinghao"></i></b>
                    <em class="iblock fz-24 align-top"><%=explore_lable_name%></em>
                </div>
                <% } %>
                <p><a class="fz-28" href="javascript:;">@<%=user_info.user_account%></a></p>
                <p><a class="fz-24" href="javascript:;"><%=explore_base.explore_title%></a></p>
                <p class="fz-0" id="js-btn-video-det"><em class="fz-24"><%=explore_content%></em></p>
            </div>
            <div class="video-bottom-btn">
                <% if(goods.sum >=1){ var i = 0;%>
                <a class="btn-see-video-det js-btn-video-det relatives" href="./explore_goods_list.html?explore_id=<%=explore_base.explore_id%>"><?= __('视频同款') ?>
                <b class="video-details-num"><%=goods.sum%></b>
                </a>
                <% } %>
                <!--                <a class="btn-see-video-det js-btn-video-det relatives" href="javascript:;">查看详情</a>-->
                <div class="video-bottom-btn-input flex1"><i class="iconfont icon-xie1"></i><input type="search" id="pinglun" class="placeholder-9b" type="text" placeholder='“点赞再多，不然评论互撩”'></div>
				<button onclick="keyup_submit(event)">发送</button>
            </div>
        </script>

        <!-- 查看详情 -->
        <div class="nctouch-bottom-mask video-see-details-mask down" id="video-see-html">

        </div>

        <script type="text/html" id="content_info_s">
            <div class="nctouch-bottom-mask-bg"></div>
            <div class="nctouch-bottom-mask-block">
                <div class="nctouch-bottom-mask-top pt-0 pb-0 pl-0 pr-0">
                    <h4 class="animation-mask-tit">详情</h4>
                    <em class="animation-close"><i class="iconfont icon-close xiangqing"></i></em>
                </div>
                <div class="nctouch-bottom-mask-rolling overflow-auto">
                    <div class="video-details-cont">
                        <p class="video-details-text"><%=explore_base.explore_content%></p>
                        <ul class="video-details-signs">
                            <% for(var j =0;j < explore_lable.length; j++){ %>
                            <li><a href="javascript:;"><b><i class="iconfont icon-jinghao"></i></b><em><%=explore_lable[j].lable_content%></em></a></li>
                            <% } %>
                        </ul>
                    </div>
                </div>
            </div>
        </script>
        <!-- 弹窗-评论列 -->
        <div class="social-push-infor-alert nctouch-bottom-mask down" id="heart-comments-more">

        </div>

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
                                        <span class="fr heart-comment-goods like-comment-btn" data-id="<%=comment[i].comment_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?><%=comment[i].comment_like_count%></em></span>
                                        <% }else{ %>
                                        <span class="fr heart-comment-goods active like-comment-btn" data-id="<%=comment[i].comment_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?><%=comment[i].comment_like_count%></em></span>
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
                                                        <span class="fr heart-comment-goods like-reply-btn" data-id="<%=comment[i].reply.reply_list[j].reply_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?></em><%= comment[i].reply.reply_list[j].comment_like_count %></span>
                                                        <% }else{ %>
                                                        <span class="fr heart-comment-goods active like-reply-btn" data-id="<%=comment[i].reply.reply_list[j].reply_id%>"><i class="iconfont icon-like-b fl"></i><em class="fl"><?= __('赞') ?></em><%= comment[i].reply.reply_list[j].comment_like_count %></span>
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
    </body>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/iscroll.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/share.js"></script>
    <script type="text/javascript" src="../js/touch.js"></script>
    <script type="text/javascript" src="../js/jsmpeg.js"></script>
    <script type="text/javascript" src="../js/explore_base_video.js"></script>
    <script>
        // 选择快递
        $.animationUp({
            valve: "#js-btn-video-det",              // 动作触发
            wrapper: "#video-see-html",    // 动作块
        });
        // 查看评论
        $.animationUp({
            valve: ".js-video-comments",          // 动作触发
            wrapper: "#heart-comments-more",    // 动作块
        });

        // $("#js-btn-video-det").click(function(){
        //     $(".video-see-details-mask").removeClass('down');
        //     $("#video-see-html").addClass('up');
        // })

    </script>

    </html>
<?php
include __DIR__ . '/../includes/footer.php';
?>