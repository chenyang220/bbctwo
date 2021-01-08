<?php 
 include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="../../css/base.css">
    <link rel="stylesheet" href="../../css/plus.css">
    <link rel="stylesheet" href="../../css/iconfont.css">
</head>
<body>
    <header id="header" class="posf plus-header">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a class="js-cancel-push" href="javascript:history.back(-1);"> <b class="iconfont icon-arrow-left colf"></b> </a> -->
            </div>
            <div class="tit">会员尊享</div>
        </div>
        <div class="plus-head-goods-search tc mt-10">
            <form action="plus_goods.php"><div class="plus-goods-search-module tl"><i class="iconfont icon-search"></i><input class="wp80 align-middle" type="text" name="keyword" placeholder="请输入PLUS商品名称" id="search_goods"></div></form>
        </div>
    </header>  
    <div class="nctouch-main-layout plus-goods-layout" id="plus-goods-items"> </div>
        <!-- 有搜索商品 -->
        <script type="text/html" id="plus-goods-item">
         <%if(typeof(items)!='undefined' && items.length>0){%>
        <ul class="plus-goods-items tl pl-20 pr-20" style="margin-bottom: 54px;">
           <% for (var k in items) { var v = items[k]; %>
              <li>
                    <a href="../product_detail.html?cid=<%=v.common_id%>">
                        <em class="img-box"><img src="<%=v.common_image %>" alt=""></em>
                        <div>
                            <span class="block one-overflow"><%=v.common_name %></span>
                            <em class="block">￥<%=v.common_price %></em>
                            <p><strong>￥<%=v.plus_price %></strong><b></b></p>
                        </div>
                    </a>
                </li>  
              <%}%>
         </ul>
        <%}else{%>
         <!-- 无搜索结果 -->
         <div class="nodata">
             <img src="../images/i-search.png" alt="">
             <p>暂无搜索结果</p>
         </div>
         <%}%>
    </script>
    <div class="plus-normal-bottom-fixed">
        <a class="" href="plus_index.html"><i class="iconfont icon-huiyuan2"></i><em>PLUS会员首页</em></a><a class="active" href="javascript:void(0);"><i class="iconfont icon-goods"></i><em>会员尊享商品</em></a>
    </div>
</body>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript" src="../../js/ncscroll-load.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</html>
<?php
    include __DIR__ . '/../../includes/footer.php';
?>

<script type="text/javascript">
    $(function () {
        var t = getCookie("key");
        if (!t) {
            window.location.href = WapSiteUrl + "/tmpl/member/login.html";
            return false;
        }

       $(document).on("click",".icon-search",function(){
         searchplusgoods();
       })
        //搜索
        searchplusgoods();
        function searchplusgoods() {
            var searchstr = $("#search_goods").val();
            var i = new ncScrollLoad;
            var url = ApiUrl + "/index.php?ctl=Plus_User&met=index&typ=json";
            if (searchstr !=null && searchstr!='' &&searchstr!='undefined') {
                url = url + '&words=' + searchstr;
            }
            i.loadInit({
                url: url,
                getparam: {k: t, u: getCookie("id")},
                tmplid: "plus-goods-item",
                containerobj: $("#plus-goods-items"),
                iIntervalId: true,
                data: {WapSiteUrl: WapSiteUrl}
            });
       }
    });
</script>