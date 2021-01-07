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
        <link rel="stylesheet" href="../css/nctouch_common.css">
        <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_q9lnqauk2o.css">
        <link rel="stylesheet" href="../css/swiper.min.css">
    </head>
    <body>
    <header id="header" class="">
        <div class="header-wrap">
            <div class="header-l">
<!--                 <a href="javascript:window.history.back();"><i class="icon-back"></i></a>
                <span class="explore-center-head-close"><i class="iconfont icon-close"></i></span> -->
            </div>
            <div class="tit user_info_name"></div>
            <div class="discover-pearsonal-share">
                <a href="explore_find_friends.html"><i class="iconfont icon-ren"></i></a>
                <?php if ($_COOKIE['is_app_guest']) { ?>
                    <a href="javascript:;" id="share"><i class="iconfont icon-fenxiang-copy"></i></a>
                <?php } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'UCBrowser') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'UCWEB') !== false) { ?>
                    <a href="javascript:;" id="share"><i class="iconfont icon-fenxiang-copy"></i></a>
                <?php } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false) { ?>
                    <a href="javascript:;" id="share"><i class="iconfont icon-fenxiang-copy"></i></a>
                <?php } ?>
            </div>
        </div>
    </header>

    <div class="nctouch-main-layout">
        <div class="bgf pb-20">
            <div class="discover-personal-head tc fz0" id="user_self">
                <em class="img-box"></em>
                <p class="discover-personal-sign js-set-sign">
<!--                     <span></span> -->
                    <span class="sign_content"></span>
                    <a class="discover-add-infor js-sign-add" href="javascript:;">点击添加</a>
                    <i class="iconfont icon-bianji1 hide"></i>
                </p>
                <p class="discover-personal-edit"><a href="<?php echo $UCenterApiUrl; ?>"><i class="iconfont icon-bianji1"></i><em>编辑个人资料></em></a></p>
            </div>
            <div class="discover-personal-head tc fz0" id="user_from">
                <em class="img-box"></em>
                <p class="fz-28 user_info_name">Scarlett</p>
                <p class="discover-personal-sign"><span class="sign_content"></span></span></p>
                <p id="follow" class="hide"><span class="btn-follow pl-30 pr-30"><i class="iconfont icon-add"></i><em>关注</em></span></p>
                <p id="isFollow" class="hide"><span class="btn-follow pl-30 pr-30 active"><em>已关注</em></span></p>
            </div>
            <ul class="discover-personal-rel clearfix">
                <li><a href="tmpl/explore_user_follow.php"><strong id="user_follow_count">0</strong><span>关注</span></a></li>
                <li><a href="tmpl/explore_user_fans.php"><strong id="user_fans_count">0</strong><span>粉丝</span></a></li>
                <li><a href="javascript:;"><strong id="user_like">0</strong><span>被赞</span></a></li>
            </ul>
        </div>
        <div class="discover-personal-main">
            <ul class="discover-personal-main-exchange tc js-discover-exchange">
                <li class="active"><span id="count" data-count="">资源(0)</span></li>
                <li><span id="draft_count">草稿(0)</span></li>
                <li><span id="collection">收藏(0)</span></li>
            </ul>
            <div class="discover-content-module waterfall-img-box">
                <ul class="waterfall social-push-items clearfix" id="wenzhang">
                </ul>
            </div>
            <div class="discover-content-module draft-box hide">
                <ul class="draft-items pl-20 pr-20 bgf">
                </ul>
            </div>
            <div class="discover-content-module draft-box hide">
                <ul class="waterfall social-push-items " id="shouchang">
                </ul>
            </div>
        </div>
    </div>
    <script id="waterfall-template" type="text/template">
        <!-- 第一个为写心得 -->
        <li class="waterfall-li heart-enterance">
            <div class="li-box">
            <a class="block" href="./explore.php">
                <em class="img-box"><img src="../images/icons/heart-enterance.png" alt=""></em>
                <div class="btn-heart-edit tc">
                    <strong class="block fz-30">写心得</strong>
                </div>
            </a>
            </div>
        </li>
        <% for(i=0;i<explore_base.length;i++){ var item = explore_base[i]; %>
            <li class="waterfall-li waterfall-li<%=item.explore_id%>">
                <div class="li-box">
                    <% if (explore_base[i].type == '.mov' || explore_base[i].type == '.mp4'){ %>
                        <a class="block" href="./explore_base_video.php?explore_id=<%=item.explore_id%>">
                            <em class="img-box">
                                <video  controls="controls" style="max-width:100%;"   src="<%=item.images_url%>" id="ckplayer_a1" x5-video-player-type="h5" preload="metadata" playsinline="true"
                                        webkit-playsinline="true"  x-webkit-airplay="true" >
                                </video>
                                <% if(item.explore_status == 1){ %>
                                <span class="violation-sign"><b>此内容涉嫌违规，已下架</b></span>
                                <% } %>
                            </em>
                        </a>

                    <% } else { %>

                        <a class="block" href="./explore_base.php?explore_id=<%=item.explore_id%>">
                            <em class="img-box"><img src="<%=item.images_url%>" alt="">
                                <% if(item.explore_status == 1){ %>
                                <span class="violation-sign"><b>此内容涉嫌违规，已下架</b></span>
                                <% } %>
                            </em>
                        </a>

                    <% } %>

                    <div class="push-text-module">
                        <a>
                            <span class="daily-tit more-overflow"><%=item.explore_title%></span>
                        </a>
                        <b class="js-btn-del-article" style='float:right;' data-explore_id="<%=item.explore_id%>"><i class="iconfont icon-lajitong"></i></b>
                        <p class="clearfix">
                            <span class="publisher fl">
                                <img src="<%=user_info.user_logo%>">
                                <em class="one-overflow"><%=user_info.user_name%></em>
                            </span>
                            <a class="fr" href="javascript:;">
                                <span class="praise <% if(item.is_support){ %> active <% } %>" data-explore_id="<%=item.explore_id%>">
                                    <i class="iconfont icon-like-b"></i>
                                    <em><%=item.explore_like_count%></em>
                                </span>
                            </a>
                        </p>
                    </div>
                </div>
            </li>
        <%  } %>
    </script>
    <script id="no-waterfall-template" type="text/template">
        <% for(i=0;i<explore_base.length;i++){ var item = explore_base[i]; %>
        <li>
            <div class="li-box">
                <% if (explore_base[i].type == '.mov' || explore_base[i].type == '.mp4'){ %>
                <a class="block" href="./explore_base_video.php?explore_id=<%=item.explore_id%>">
                    <em class="img-box">
                        <video  controls="controls" style="max-width:100%;"   src="<%=item.images_url%>" id="ckplayer_a1" x5-video-player-type="h5" preload="metadata" playsinline="true"
                                webkit-playsinline="true"  x-webkit-airplay="true" >
                        </video>
                        <% if(item.explore_status == 1){ %>
                        <span class="violation-sign"><b>此内容涉嫌违规，已下架</b></span>
                        <% } %>
                    </em>
                </a>

                <% } else { %>

                <a class="block" href="./explore_base.php?explore_id=<%=item.explore_id%>">
                    <em class="img-box"><img src="<%=item.images_url%>" alt="">
                        <% if(item.explore_status == 1){ %>
                        <span class="violation-sign"><b>此内容涉嫌违规，已下架</b></span>
                        <% } %>
                    </em>
                </a>

                <% } %>
                <div class="push-text-module">
                    <a>
                        <span class="daily-tit more-overflow"><%=item.explore_title%></span>
                    </a>
                    <b class="js-btn-del-article" style='float:right;' data-explore_id="<%=item.explore_id%>"><i class="iconfont icon-lajitong"></i></b>
                    <p class="clearfix">
                            <span class="publisher fl">
                                <img src="<%=user_info.user_logo%>">
                                <em class="one-overflow"><%=user_info.user_name%></em>
                            </span>
                        <a class="fr" href="javascript:;">
                                <span class="praise <% if(item.is_support){ %> active <% } %>" data-explore_id="<%=item.explore_id%>">
                                    <i class="iconfont icon-like-b"></i>
                                    <em><%=item.explore_like_count%></em>
                                </span>
                        </a>
                    </p>
                </div>
            </div>
        </li>
        <% } %>
    </script>



    <script id="waterfall-template_shouchang" type="text/template">
        <% for(i=0;i<explore_base.length;i++){ var item = explore_base[i]; %>
        <li class="waterfall-li">
            <div class="li-box">
                <% if (explore_base[i].type == '.mov' || explore_base[i].type == '.mp4'){ %>
                <a class="block" href="./explore_base_video.php?explore_id=<%=item.explore_id%>">
                    <em class="img-box">
                        <video  controls="controls" style="max-width:100%;"   src="<%=item.images_url%>" id="ckplayer_a1" x5-video-player-type="h5" preload="metadata" playsinline="true"
                                webkit-playsinline="true"  x-webkit-airplay="true" >
                        </video>
                        <% if(item.explore_status == 1){ %>
                        <span class="violation-sign"><b>此内容涉嫌违规，已下架</b></span>
                        <% } %>
                    </em>
                </a>

                <% } else { %>

                <a class="block" href="./explore_base.php?explore_id=<%=item.explore_id%>">
                    <em class="img-box"><img src="<%=item.images_url%>" alt="">
                        <% if(item.explore_status == 1){ %>
                        <span class="violation-sign"><b>此内容涉嫌违规，已下架</b></span>
                        <% } %>
                    </em>
                </a>

                <% } %>

                <div class="push-text-module">
                    <a>
                        <span class="daily-tit more-overflow"><%=item.explore_title%></span>
                    </a>
                    <p class="clearfix">
                            <span class="publisher fl">
                                <img src="<%=user_info.user_logo%>">
                                <em class="one-overflow"><%=explore_base[i].user_account%></em>
                            </span>
                        <a class="fr" href="javascript:;">
                                <span class="praise <% if(item.is_support){ %> active <% } %>" data-explore_id="<%=item.explore_id%>">
                                    <i class="iconfont icon-like-b"></i>
                                    <em><%=item.explore_like_count%></em>
                                </span>
                        </a>
                    </p>
                </div>
            </div>
        </li>
        <% } %>
    </script>

    <!--    草稿-->
    <script id="unnormal-template" type="text/template">
        <% for(i=0;i<explore_base.length;i++){ var item = explore_base[i]; %>
            <li class="draft<%=item.explore_id%>">
                <a href="./explore.php?explore_id=<%=item.explore_id%>"><em class="img-box" style="background:url(<%=item.images_url%>) no-repeat center;background-size:cover;"></em></a>
                <div>
                    <a href="./explore.php?explore_id=<%=item.explore_id%>">
                        <span class="one-overflow">
                            <% if(item.explore_title){ %>
                                <%=item.explore_title%>
                            <% }else{ %>
                                无标题文章
                            <% } %>
                        </span>
                    </a>
                    <% if(item.explore_status == 3){ %>
                    <span> 审核中</span>
                    <% }else if(item.explore_status == 4){ %>
                    <span> 审核失败</span>
                    <span>原因：<%=item.explore_verify_remark%></span>
                    <% } %>
                    <p>
                        <time><%=item.explore_create_time%></time>
                        <b class="js-btn-del-draft" data-explore_id="<%=item.explore_id%>"><i class="iconfont icon-lajitong"></i></b>
                    </p>
                </div>
            </li>
        <% } %>
    </script>
    <!-- 弹框删除心得 -->
    <div class="dialog tc heart-del-dialog hide">
        <div class="table">
            <div class="table-cell">
                <div class="content">
                    <b class="btn-del-dialog"><i class="iconfont icon-close"></i></b>
                    <p class="social-login-tips">确认删除？</p>
                    <div class="dialog-bottom-btn clearfix"><a class="btn-cancel" href="javascript:;">取消</a><a class="btn-confirm-del" href="javascript:;">确认删除</a></div>
                </div>
            </div>
        </div>
    </div>
    <!-- 弹框添加个性签名 -->
    <div class="dialog tc heart-cancel-sign hide">
        <div class="table">
            <div class="table-cell">
                <div class="content">
                    <b class="btn-del-dialog"><i class="iconfont icon-close"></i></b>
                    <div class="dialog-tit">个性签名</div>
                    <div class="dialog-tit-des">有趣的个人介绍会吸引更多粉丝哦</div>
                    <div class="posr social-infor-sign iblock">
                        <textarea id="user_sign" maxlength="30"></textarea>
                        <span class="sign-num-limit"><strong id="num">0</strong>/<em>30</em></span>
                    </div>
                    <div><a class="btn-sign-save" href="javascript:;">保存</a></div>

                </div>
            </div>
        </div>
    </div>

    <!-- 登录弹框提示 -->
    <div class="dialog tc social-login-dialog hide" style="display: block;">
        <div class="table">
            <div class="table-cell">
                <div class="content">
                    <p class="social-login-tips">请先登录</p>
                    <div><a href="javascript:;" class="social-login logbtn">登录</a></div>
                </div>
            </div>
        </div>
    </div>
<!--    alert-->
    <div class="dialog tc hide dialog-info">
        <div class="table">
            <div class="table-cell">
                <div class="content pt-40 pb-40">
                    <div class="dialog-tit mb-0 information">个性签名</div>
                </div>
            </div>
        </div>
    </div>
<!--   用户头像点击放大 -->
    <div id="user-logo-box" class="user-logo-enlarge hide">
        <em class="tc"><img src="" alt="img"></em>
    </div>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/animation.js"></script>
    <script type="text/javascript" src="../js/NativeShare.js"></script>
    <script type="text/javascript" src="../js/explore_center.js"></script>
    </body>
    </html>
<?php
include __DIR__ . '/../includes/footer.php';
?>