<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=__('通知')?></title>
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
                <a href="javascript:;" onclick="self.location=document.referrer;"><i class="icon-back"></i></a>
            </div>
            <div class="tit"><?=__('通知')?></div>
        </div>
    </header>
    <div class="nctouch-main-layout">
    	<!-- 有数据 -->
    	<ul class="message-notice-items pl-20 pr-20" id="report">
    	</ul>
    	<!-- 无数据 -->
        <div class="social-nodata hide">
            <em class="img-box"><img src="../images/icons/news.png" alt="icon"></em>
            <p><?=__('暂时还没有通知哦')?></p>
        </div>
    </div>
</body>
<script type="text/javascript" src="../js/zepto.min.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/explore_message_report.js"></script>

<script  type="text/html" id="report-more">
    <% if (data.items.length > 0){ for (var i =0;i < data.items.length; i++){ %>
    <li>
        <% if(data.items[i].message_type == 3){ %>
        <a href="./explore_report.php?report_id=<%=data.items[i].active_id%>">
        <% } %>
        <% if(data.items[i].message_type == 7){ %>
        <a href="./explore_base.php?explore_id=<%=data.items[i].explore_id%>">
        <% } %>
            <h4><?=__('亲爱的')?><%=data.items[i].message_user_name%></h4>
            <% if(data.items[i].message_type == 3){ %>
                <div class="message-common"><?=__('您投诉')?><em><%=data.items[i].active_user_account%></em><?=__('用户的')?><strong>“<%=data.items[i].explore_title%>”</strong><?=__('心得标题 有违规行为，')?><%=data.items[i].status%><?=__('，快去看看吧～')?></div>
            <% } %>
            <% if(data.items[i].message_type == 7){ %>
                <div class="message-common"><?=__('接到相关投诉，您发表的')?><strong>“<%=data.items[i].explore_title%>”</strong><?=__('标题，存在违规行为，系统已将此心得下架，其他用户将无法查看此心得。快去看看吧~')?></div>
            <% } %>
            <% if(data.items[i].message_type == 3){ %>
                <% if(data.items[i].report_status == 1){ %>
                    <b class="success"></b>
                <% } %>
                <% if(data.items[i].report_status == 2){ %>
                    <b class="fail"></b>
                <% } %>
            <% } %>
            <% if(data.items[i].message_type == 7){ %>
                <b class="warning"></b>
            <% } %>
        </a>
    </li>
    <% }} %>
</script>

</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>