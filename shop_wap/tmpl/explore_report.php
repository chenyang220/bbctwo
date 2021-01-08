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
                <!-- <a href="javascript:history.go(-1)"><i class="icon-back"></i></a> -->
            </div>
            <div class="tit"><?=__('投诉成功通知')?></div>
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

<script  type="text/html" id="report-info">
    <li>
        <div class="message-notice-complain">
            <dl>
                <dt><?=__('投诉对象')?>：</dt>
                <dd><em><%=data.to_user_account%></em></dd>
            </dl>
            <dl>
                <dt><?=__('审核结果')?>：</dt>
                <% if(data.report_status == 1){ %>
                <dd><?=__('确认投诉对象有违规行为')?></dd>
                <% } %>
                <% if(data.report_status == 2){ %>
                <dd><?=__('根据您投诉的信息，我们暂时无法认定投诉对象存在违规行为')?></dd>
                <% } %>
            </dl>
            <dl>
                <% if(data.report_status == 1) { %>
                <dt><?=__('处理方式')?>：</dt>
                <dd><?=__('已对投诉对象发表的文章进行下架处理，您的反馈能帮助我们净化社区的网络环境，改善每位用户的体验，感谢您的支持！')?></dd>
                <% } %>
                <% if(data.report_status == 2) { %>
                <dd><?=__('投诉不通过，可能是投诉对象未违规，也可能是因为信息有点我们暂时无法做出判断，感谢您的支持和理解！')?></dd>
                <% } %>
            </dl>
        </div>
    </li>
</script>

<script>
    $(function () {
        get_detail();

        function get_detail() {
            $.ajax({
                url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getReportInfo&typ=json",
                type: "POST",
                data: {
                    k: getCookie("key"),
                    u: getCookie("id"),
                    report_id:getQueryString('report_id')
                },
                dataType: "json",
                success: function (e) {
                    if (e.status == 200) {

                        if(e.data.report_status == 2){
                            $(".tit").html('投诉不通过通知');
                        }

                        var report_html = template.render('report-info', e);
                        $("#report").append(report_html);

                    } else {
                        $.sDialog({
                            skin: "red",
                            content: '获取数据失败，请刷新重试！',
                            okBtn: false,
                            cancelBtn: false
                        });
                    }
                }
            });
        }
    })
</script>

</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>