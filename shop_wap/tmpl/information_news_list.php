<?php
include __DIR__.'/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<!--002-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0,minimum-scale=1, user-scalable=no"/>
    <title>资讯列表</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
    <link rel="stylesheet" href="../css/information.css">
    <link rel="stylesheet" href="../css/nctouch_common.css">
<!--     <link rel="stylesheet" href="../css/wapMessageList.css"> -->
</head>
<body>
<div class="MeLtSwitch">
    <header id="header" class="fixed">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:history.go(-1)"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1>资讯中心</h1>
            </div>
        </div>
    </header>



     <div class="infor-head-fixed">
         <div class="swiper-container information-swiper bgf TabControlBox pl-20 pr-20">
            <ul class="swiper-wrapper TabControl NoMovingBar" id="newsclass">
            </ul>
        </div>
        <ul class="infor-head-nav clearfix bgf tc bort1 mb-20">
        </ul>
    </div>
    <div class="nctouch-main-layout infor-main-lists" id="newscentent">
         <ul class="infor-list-items pl-20 pr-20 bgf">
         </ul>
    </div>

<script type="text/html" id="newscentent-tmp">
    <% if (items.length > 0) { %>
              <% for (var i = 0; i < items.length; i++) { %>
                 <% var item = items[i]; %>
                <li id="favitem_<%=items[i].id  %>">
                    <a href="<%=WapSiteUrl%>/tmpl/informationnews_details.html?id=<%=items[i].id%>">
                        <div>
                            <h3 class="one-overflow"><%=items[i].title%></h3>
                            <h4 class="one-overflow"><%=items[i].subtitle%></h4>
                            <p><strong><%=items[i].number%>阅读量</strong><em><%=items[i].authorname%></em></p>
                            <p><time><%=items[i].time%></time></p>
                        </div>
                        <% if(item.content_type== 'embed' || item.content_type== 'img' ) { %>
                        <em class="img-box">
                            <% if(item.content_type == 'embed') { %>
                                <video class="wp100" src="<%= item.content_url%>"></video>
                            <% }else{ %>
                                <img src="<%= item.content_url%>" alt="">
                            <% } %>
                        </em>
                        <% } %>
                    </a>
                </li>
              <% } %>
        

    <%}else{%>
        <div class="norecord tc">
            <div class="ziti-store">
                <i></i>
            </div>
            <p class="fz-30 col9"><?= __('暂无任何资讯新闻'); ?></p >
        </div>
     <%}%>
    
</script>

<script>
    window.onload = function () {
        $(function () {
            // 选项卡状态
            $(document).on("click", ".unfold", function () {
                $(this).removeClass("unfold");
                $(this).addClass("folding");
                $(".TabControl").addClass("UnfoldTabControl");
                $(".TabControlBox").animate({height: "6.1rem"});
                // $(".TbClMoreBox").addClass("TbClMoreRotate");
                
                
            })
            
            $(document).on("click", ".folding", function () {
                
                $(".TabControl").removeClass("UnfoldTabControl");
                $(".TabControlBox").animate({height: "2rem"}, "fast");
                // $(".TbClMoreBox").removeClass("TbClMoreRotate");
                $(this).removeClass("folding");
                $(this).addClass("unfold");
                
            });
            
        })
        // 导航条切换
        $(".TabControl>li").click(function () {
            $(".TabControl>li").removeClass("pitchOnLi");
            $(this).addClass("pitchOnLi");
        })
        
        // 排序
        $(".infor-head-nav li").click(function () {
            $(".infor-head-nav li").removeClass("active");
            $(this).addClass("active");
        })
        
        
        function StyleBn(obj) {
            $(".sortordBox button").removeClass("SdActive");
            $(obj).addClass("SdActive")
        }
        
        $(".sortordBox button").click(function () {
            var obj = this;
            StyleBn(obj);
        })
        // 查看详情
        $(".contentBox>li").click(function () {
            $(".MeLtSwitch").css("display", "none");
            $(".MeParticularsSh").css("display", "block");
// 奖励
            setTimeout(function () {
                $(".popupAwardBox").css("display", "block");
                timedMoney();
            }, 1000);
            
        })
        
        function timedMoney() {
            setTimeout(function () {
                $(".popupAwardBox").css("display", "none");
            }, 2000);
        }
        
        $(".detailsBack").click(function () {
            
            $(".MeParticularsSh").css("display", "none");
            $(".MeLtSwitch").css("display", "block");
        })
        
        // 遮盖
        function preventBubble(event) {
            var e = arguments.callee.caller.arguments[0] || event; //若省略此句，下面的e改为event，IE运行可以，但是其他浏览器就不兼容
            if (e && e.stopPropagation) {
                e.stopPropagation();
            } else if (window.event) {
                window.event.cancelBubble = true;
            }
        }
        
        $(".shareBn").click(function () {
            timedMoney();
            $(".timeSelectBox").css("display", "inline-block");
            $(document.body).css({
                "overflow-x": "hidden",
                "overflow-y": "hidden"
            });
            ShowUp();
            preventBubble();
        })
        $(".timeSelectBox").click(function () {
            ShowDown();
            preventBubble();
            return false;
        })
        $(".cancelBn").click(function () {
            
            ShowDown();
            preventBubble();
            return false;
        })
        
        $(".ShareBox").click(function () {
            console.log("000")
            preventBubble();
            return false;
        })
        
        function ShowUp() {
            $(".ShareBox").animate({bottom: "0"});
        }
        
        function ShowDown() {
            $(".ShareBox").animate({bottom: "-9rem"}, 200, function () {
                $(".timeSelectBox").css("display", "none");
                $(document.body).css({
                    "overflow-x": "auto",
                    "overflow-y": "auto"
                });
            });
        }
        
        
        $(".complainBn").click(function () {
            timedMoney();
            $(".complainTeBox").css("display", "inline-block");
            $(document.body).css({
                "overflow-x": "hidden",
                "overflow-y": "hidden"
            });
            preventBubble();
        })
        $(".complainTeBox").click(function () {
            $(".complainTeBox").css("display", "none");
            $(document.body).css({
                "overflow-x": "auto",
                "overflow-y": "auto"
            });
            preventBubble();
            return false;
        })
        $(".CnConfirm").click(function () {
            $(".complainTeBox").css("display", "none");
            $(document.body).css({
                "overflow-x": "auto",
                "overflow-y": "auto"
            });
            preventBubble();
            return false;
        })
        $(".CnCancel").click(function () {
            $(".complainTeBox").css("display", "none");
            $(document.body).css({
                "overflow-x": "auto",
                "overflow-y": "auto"
            });
            preventBubble();
            return false;
        })
        $(".UpWindows").click(function () {
            preventBubble();
            return false;
        })
        
        
    }
</script>
<script type="text/javascript" src="../../js/ncscroll-load.js"></script>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript" src="../../js/zepto.waypoints.js"></script>
 <script src="../../js/swiper.min.js"></script>
<script type="text/javascript" src="../../js/information_news_list.js"></script>

</body>
</html>
<?php
include __DIR__.'/../includes/footer.php';
?>


