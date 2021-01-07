<?php
include __DIR__ . '/../includes/header.php';
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?=__('发现好友')?></title>
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <link rel="stylesheet" href="../css/base.css">
        <link rel="stylesheet" href="../css/heart.css">
        <link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_8nvkmjugmdc.css">
        <link rel="stylesheet" href="../css/swiper.min.css">
    </head>
    <body>
    <header id="header" class="discover-box-header">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:window.history.back();"> <b class="iconfont icon-arrow-left"></b> </a> -->
            </div>
            <div class="tit"><?=__('发现好友')?></div>
        </div>

    </header>
    <div class="discover-box-head">
        <div class="header-inp clearfix"><i class="iconfont icon-search fl"></i><input class="search-input discover-search" type="text" placeholder="<?=__('搜索好友')?>"><i class="iconfont icon-close clean-btn hide"></i></div>
    </div>
    <div class="discover-main-layout">
        <!-- 发现好友列表 -->
        <ul class="discover-list-items">

        </ul>
        <!-- 搜索好友列表 -->
        <div class="hide">
            <ul class="discover-search-friends pl-20 pr-20 bgf">

            </ul>
            <p class="load-completion"><i class="iconfont icon-icon03"></i><em><?=__('已经到底咯')?>~</em></p>
        </div>
        <!-- 还没有关注的人 -->
        <div class="module-tips social-nodata hide"> <!-- hide隐藏 -->
            <em class="img-box"><img src="../images/new/tips-img-fans.png" alt="img"></em>
            <p><?=__('还没有好友哦')?>~</p>
        </div>
    </div>
    </body>
    
    <script id="find-fiends-template" type="text/template">
        <% if(items){ %>
            <% for(i=0;i<items.length;i++){ var item = items[i]; %>
                <li>
                    <a href="./explore_center.html?user_id=<%=item.user_id%>">
                        <div class="discover-list-head">
                            <em class="img-box"><img src="<%=item.user_logo%>" alt="user"></em>
                            <p><span class="one-overflow wp50"><%=item.user_name%></span><em class="one-overflow wp70"><%=item.user_sign%></em></p>
                        </div>
                    </a>
                    <div class="swiper-container discover-list-swiper">
                        <div class="swiper-wrapper">
                            <% if(item.explore_base){ %>
                                <% for(v=0;v<item.explore_base.length;v++){ var explore_base = item.explore_base[v]; %>
                                        
                            <a href="./explore_base.html?explore_id=<%=explore_base.explore_id%>"><div class="swiper-slide" style="background:url(<%=explore_base.images_url%>) no-repeat center;background-size:cover;"><em class="img-box"></em></div></a>
                                    
                                <% } %>
                            <% } %>
                        </div>
                    </div>
                    <div class="clearfix discover-list-bottom">
                        <span class="fl"><?=__('文章')?><%=item.explore_base_count%>·<?=__('被赞')?><%=item.user_like%></span>
                        <a class="fr follow <% if(item.isFollow == 1){ %>isFollow active<% } %>" href="javascript:;" data-user_id="<%=item.user_id%>">
                            <% if(item.isFollow == 1){ %>
                                <em><?=__('已关注')?></em>
                            <% }else{ %>
                            <i class="iconfont icon-add"></i><em><?=__('关注')?></em>
                            <% } %>
                        </a>
                    </div>
                </li>
            <% } %>
        <% } %>
    </script>
    <script id="search-fiends-template" type="text/template">
        <% if(items){ %>
            <% for(i=0;i<items.length;i++){ var item = items[i]; %>
                <li>
                    <a href="./explore_center.html?user_id=<%=item.user_id%>">
                        <em class="img-box"><img src="<%=item.user_logo%>" alt=""></em>
                    </a>
                    <div>
                         <a href="./explore_center.html?user_id=<%=item.user_id%>"><span class="one-overflow"><%=item.user_name%></span></a>
                        <p><em><%=item.explore_base_count%><?=__('篇文章')?></em><em><%=item.user_fans_count%><?=__('粉丝')?></em></p>
                        <a href="javascript:;" class="follow <% if(item.isFollow == 1){ %>isFollow active <% } %>" data-user_id="<%=item.user_id%>">
                            <% if(item.isFollow == 1){ %>
                                <em><?=__('已关注')?></em>
                            <% }else{ %>
                                <i class="iconfont icon-add"></i><em><?=__('关注')?></em>
                            <% } %>
                        </a>
                    </div>
                </li>
            <% } %>
        <% } %>
    </script>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../../js/explore_find_friends.js"></script>
    </html>
<?php
include __DIR__ . '/../includes/footer.php';
?>