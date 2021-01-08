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
        <link rel="stylesheet" href="//at.alicdn.com/t/font_562768_apqpm5i6fhf.css">
    </head>

    <body class="edit-heart">
    <!--发布首页-->
    <div id="explore" class="bgf-set">
        <header id="header" class="posf">
            <div class="header-wrap">
                <div class="header-l">
                    <!-- <a class="js-cancel-push" href="javascript:;"> <i class="back"></i> </a> -->
                </div>
                <div class="tit">发表心得</div>
                <a class="delvoucher social-head-right active" href="javascript:void(0)" id="addExplore">发布</a>
            </div>
        </header>
        <div class="nctouch-main-layout bgf">
            <div class="pl-20 pr-20">
                <!-- 上传图片按钮 -->
                <div class="heart-img-input">
                    <span><i class="iconfont icon-add mr-10"></i><em>添加</em></span>
                    <input class="btn-input" type="file" id="uploadImage" name="upfile">
                </div>
                <!-- 上传中图片 -->
                <div class="heart-upimg-swiper swiper-container hide">
                    <ul class="heart-upimg-items swiper-wrapper">
                        <li class="swiper-slide swiper-add-image">
                            <div>
                                <input class="btn-input" type="file" name="upfile">
                                <em class="img-box"><img src="" alt=""></em>
                                <i class="iconfont icon-add"></i>
                                <i class="iconfont icon-forbid"></i>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="heart-tit-input borb1 clearfix"><input class="fz-28" type="text" placeholder="添加标题" maxlength="30" name="explore_title"><b class="fr fz-24" id="num">30</b></div>
                <textarea class="heart-textarea" placeholder="写下你的心得吧..." name="explore_content"></textarea>

                <!-- 添加标签按钮 -->
                <div class="btn-tag bort1 borb1" id="add_lable">
                    <a href="javascript:;">
                        <i class="iconfont icon-addition1 mr-10 fz-32"></i><em class="fz-28">添加标签</em><b class="fz-24 ml-10">（获得更多曝光）</b>
                    </a>
                </div>
                <!-- 已添加标签 -->
                <div class="heart-tags bort1 borb1 hide" id="add_lable_list">
                    <a href="javascript:;" class="clearfix">
                        <div class="fl one-overflow">

                        </div>
                        <i class="fr iconfont icon-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--编辑图片-->
    <div id="edit_image" class="hide">
        <header id="header" class="posf">
            <div class="header-wrap">
                <div class="header-l">
                    <a href="javascript:;"> <i class="back"></i> </a>
                </div>
                <div class="tit file_type"></div>
                <a class="delvoucher fz-28 social-head-right" href="javascript:void(0)" id="image_next">继续</a>
            </div>
        </header>
        <div class="nctouch-main-layout">
            <div class="social-push-img-box">
                <div class="social-edit-img"></div>
                <ul class="heart-relative-infor-items">

                </ul>
                <div class="social-add-information">
                    <span class="block social-infor-tip-tit">点击请添加商品信息</span>
                    <span class="block social-infor-tip-tit2">有机会让更多人看到你的心得</span>
                    <em class="btn-social-infor js-add-information"><i class="iconfont icon-add"></i><b>添加信息</b></em>
                </div>
            </div>
        </div>
        <div class="social-push-infor-alert nctouch-bottom-mask down" id="social-add-information">
            <div class="nctouch-bottom-mask-bg"></div>
            <div class="nctouch-bottom-mask-block">
                <div class="info-add-module">
                    <div class="social-push-infor-title clearfix borb1 pl-20 pr-20">
                        <span class="fl"><i class="iconfont icon-tag"></i><em class="fz-28"><span id="add_or_edit">添加</span>商品信息</em></span>
                        <a class="fr" href="javascript:;" id="get_order_goods"><em class="fz-24">从订单找商品</em><i class="iconfont icon-arrow-right"></i></a>
                    </div>
                    <div class="social-push-goods tc">
                        <div>
                            <input type="text" placeholder="选择品牌" readonly="readonly" id="choose_brand_input">
                            <em class="active"><i class="iconfont icon-quxiao input-quxiao-icon brand"></i></em>
                        </div>
                        <div>
                            <input type="text" placeholder="选择商品" readonly="readonly" id="choose_goods_input">
                            <em><i class="iconfont icon-quxiao input-quxiao-icon"></i></em>
                        </div>
                    </div>
                    <div class="social-infor-btn">
                        <a class="btn-push-cancel" href="javascript:;">取消</a>
                        <a class="btn-push-sure" href="javascript:;">确认</a>
                    </div>
                </div>

                <!-- 图片达到上限 -->
                <div class="flex limit-text-tips hide">
                    <p><i class="iconfont icon-jinggao"></i><span>该图片已达到商品信息添加上限</span></p>
                </div>
            </div>
        </div>
    </div>
    <!--选择标签-->
    <div id="explore_lable" class="hide">
        <header id="header" class="posf">
            <div class="header-wrap">
                <div class="header-l">
                    <a href="javascript:;"> <i class="back"></i> </a>
                </div>
                <div class="tit">添加标签</div>
                <a class="delvoucher social-head-right active" href="javascript:void(0)" id="lable_complete">完成</a>
            </div>
        </header>
        <div class="nctouch-main-layout">
            <div class="bgf iblock wp100">
                <div class="heart-search-tag clearfix">
                    <i class="iconfont icon-search fl"></i>
                    <form action="javascript:return true">
                        <input class="fl" type="search" placeholder="请输入要搜索的标签" id="lable_name_input">
                    </form>
                    <i class="iconfont icon-quxiao fr lable-input-icon"></i>
                </div>
                <div class="swiper-container heart-need-tags">
                    <ul class="swiper-wrapper" id="lable_list">

                    </ul>
                </div>
            </div>
            <div class="bgf recommend-tags">
                <h4 class="label-title"><?=__('推荐标签')?></h4>
                <ul class="recommend-tags-items">

                </ul>
            </div>
        </div>
    </div>
    <!-- 品牌,商品选择 -->
    <div id="choose_brand" class="hide">
        <header id="header" class="posf relative">
            <div class="heart-head-search clearfix">
                <i class="iconfont icon-search fl"></i>
                <span class="one-overflow fl hide search-type"></span>
                <form action="javascript:return true">
                    <input class="fl wp60" type="search" placeholder="搜索任意品牌" id="choose_brand_search">
                </form>
                <i class="iconfont icon-quxiao fr active search-input-icon"></i>
            </div>
            <em class="heart-search-btn">取消</em>

        </header>
        <div class="nctouch-main-layout">
            <div>
                <ul class="heart-search-brand-items bgf" id="search-ul-items">

                </ul>
                <p class="load-completion hide"><i class="iconfont icon-icon03"></i><em>已经到底咯~</em></p>
                <!-- 暂无搜索结果 -->
                <div class="module-tips social-nodata  hide"> <!-- hide隐藏 -->
                    <em class="img-box"><img src="../images/new/tips-img-search.png" alt="img"></em>
                    <p>暂无搜索结果</p>
                </div>
            </div>

        </div>
    </div>

    <!--选择标签渲染-->
    <script type="text/html" id="lable-list-tmpl">
        <% if(lable) { %>
        <li>
            <b><i class="iconfont icon-jing"></i></b>
            <div class="recommend-tags-text clearfix">
                <div class="fl choose-lable">
                    <span class="one-overflow" data-lable_id="<%=lable.lable_id%>"><%=lable.lable_content%></span>
                    <em class="one-overflow block"><%=lable.lable_used_count%>篇文章</em>
                </div>
            </div>
        </li>
        <% } else { %>
        <% if(lable_content){ %>
        <li>
            <b><i class="iconfont icon-jing"></i></b>
            <div class="recommend-tags-text clearfix">
                <div class="fl">
                    <span class="one-overflow" data-lable_id=""><%=lable_content%></span>
                    <em class="one-overflow block">自定义标签</em>
                </div>
                <button class="fr tag-build">点击创建</button>
            </div>
        </li>
        <% } %>
        <% } %>
        <% if(lable_list) { %>
        <% for (var i = 0; i < lable_list.length; i++) {
        var list = lable_list[i];
        %>
        <li>
            <b><i class="iconfont icon-jing"></i></b>
            <div class="recommend-tags-text clearfix">
                <div class="fl choose-lable">
                    <span class="one-overflow" data-lable_id="<%=list.lable_id%>"><%=list.lable_content%></span>
                    <em class="one-overflow block"><%=list.lable_used_count%>篇文章</em>
                </div>
            </div>
        </li>
        <% } %>
        <% } %>
    </script>

    <!--    标签列表渲染-->
    <script type="text/html" id="lable-tmpl">
        <% if(lables.length > 0) { %>
            <% for (var i = 0; i < lables.length; i++) {
                var item = lables[i];
            %>
                <span><i class= "iconfont icon-jing" ></i><em><%=item.lable_content%></em></span>
            <% } %>
        <% } %>
    </script>

    <!--    已经选择的标签渲染-->
    <script type="text/html" id="choose-lable-tmpl">
        <% if(lables.length>0) { %>
            <% for (var i = 0; i < lables.length; i++) {
                var item = lables[i];
            %>
                <li class="swiper-slide lable<%=item.lable_id%>"><input type = "hidden" value = "<%=item.lable_id%>" name = "lable_id" >
                    <span>
                        <i class="iconfont icon-jing"></i>
                            <em><%=item.lable_content%></em >
                        <i class ="iconfont icon-close"></i>
                    </span >
                </li>
            <% } %>
        <% } %>
    </script>

    <!-- 品牌列表渲染 -->
    <script type="text/html" id="search-brand-list-tmpl">
        <% if(items) { %>
        <% for (var i = 0; i < items.length; i++) {
        var item = items[i];
        %>
        <li class="choose_brand_goods" data-brand_id="<%=item.brand_id%>" data-brand_name="<%=item.brand_name%>">
            <em class="img-box"><img src="<%=item.brand_pic%>" alt=""></em>
            <span class="search-brand one-overflow"><%=item.brand_name%></span>
        </li>
        <% } %>
        <% } %>
    </script>


    <!-- 商品列表渲染 -->
    <script type="text/html" id="search-goods-list-tmpl">
        <% if(items) { %>
            <% for (var i = 0; i < items.length; i++) { var item = items[i]; %>
                <li class="choose_goods_common" data-common_id="<%=item.common_id%>" data-common_name="<%=item.common_name%>">
                    <em class="img-box"><img src="<%=item.common_image%>" alt="brand"></em>
                    <span class="search-brand-under one-overflow"><%=item.brand_name%></span>
                    <p><em><b class="more-overflow"><%=item.common_name%></b></em></p>
                </li>
            <% } %>
        <% } %>
    </script>
    <!-- 订单商品列表渲染 -->
    <script type="text/html" id="search-order-goods-list-tmpl">
        <% if(data) { %>
            <% for (var i = 0; i < data.length; i++) { var item = data[i];%>
                <li class="choose_goods_common" data-common_id="<%=item.common_id%>" data-common_name="<%=item.goods_name%>" data-brand_id="<%=item.brand_id%>" data-brand_name="<%=item.brand_name%>">
                    <em class="img-box"><img src="<%=item.goods_image%>" alt="brand"></em>
                    <div>
                        <span class="more-overflow"><%=item.goods_name%></span>
                        <p class="clearfix">
                            <strong>￥<%=item.goods_price%></strong>
                            <time class="fr"><%=item.order_time%></time>
                        </p>
                    </div>
                </li>
            <% } %>
        <% } %>

    </script>
    <!--    图片渲染-->
    <script type="text/html" id="images-list-tmpl">
        <% if(images.length>0 && images) { %>
            <% for (var i = 0; i < images.length; i++) {
                var item = images[i];
            %>
                <li class="swiper-slide swiper-slide-active">
                    <div>
                        <em class="img-box edit-images-goods" style="background:url(<%=item.images_url%>) no-repeat center;background-size:cover;" data-images_id="<%=item.images_id%>"><img class="hide" src="<%=item.images_url%>" data-images_id="<%=item.images_id%>"  alt=""></em>
                        <i class="iconfont icon-forbid active"></i>
                    </div>
                </li>
            <% } %>
        <% } %>
    </script>

    <!--    商品列表渲染-->
    <script type="text/html" id="goods-list-tmpl">
        <% if(data.length>0) { %>
            <% for (var i = 0; i < data.length; i++) {
                var item = data[i];
            %>
                <li class="clearfix" data-image_goods_id="<%=item.common_id%>">
                    <i class="iconfont icon-goods"></i>
                    <span class="one-overflow"><%=item.common_name%></span>
                    <em class="fr edit-goods-common" data-common_id="<%=item.common_id%>" data-brand_id="<%=item.brand_id%>" data-brand_name="<%=item.brand_name%>">编辑</em>
                </li>
            <% } %>
        <% } %>
    </script>
    <div class="loading-box hide">
        <div class="loading-box-img"><img src="../images/new/loading.gif" alt="loading"></div>
    </div>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/zepto.cookie.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>
    <script type="text/javascript" src="../js/jquery.ajaxfileupload.js"></script>
    <script type="text/javascript" src="../js/explore.js"></script>
    </body>
    </html>
<?php
include __DIR__ . '/../includes/footer.php';
?>