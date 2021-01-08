<?php
include __DIR__ . '/../includes/header.php';
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= __('消息') ?></title>
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
                <a href="./explore_list.php"><i class="icon-back"></i></a>
            </div>
            <div class="tit"><?= __('消息') ?></div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <ul class="message-module-items tc fz0">
            <li class="i-zans">
                <a href="./explore_message_like.php">
                    <em><b class="like_sum hide"></b></em>
                    <p><?= __('点赞') ?></p>
                </a>
            </li>
            <li class="i-views">
                <a href="./explore_message_comment.php">
                    <em><b class="comment_sum hide"></b></em>
                    <p><?= __('评论') ?></p>
                </a>
            </li>
            <li class="i-messages">
                <a href="./explore_message_report.php">
                    <em><b class="report_sum hide"></b></em>
                    <p><?= __('通知') ?></p>
                </a>
            </li>
            <li class="i-fans">
                <a href="./explore_user_fans.php">
                    <em><b class="fans_sum hide"></b></em>
                    <p><?= __('新增粉丝') ?></p>
                </a>
            </li>
        </ul>
    </div>
    </body>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script>
        $(function () {
            get_detail();

            function get_detail() {
                $.ajax({
                    url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getUnreadMeaasgeNum&typ=json",
                    type: "POST",
                    data: {
                        k: getCookie("key"),
                        u: getCookie("id"),
                    },
                    dataType: "json",
                    success: function (result) {
                        if (result.status == 200) {

                            if (result.data.like_sum > 0) {
                                $(".like_sum").html(result.data.like_sum);
                                $(".like_sum").removeClass('hide');
                            }

                            if (result.data.comment_sum > 0) {
                                $(".comment_sum").html(result.data.comment_sum);
                                $(".comment_sum").removeClass('hide');
                            }

                            if (result.data.report_sum > 0) {
                                $(".report_sum").html(result.data.report_sum);
                                $(".report_sum").removeClass('hide');
                            }

                            if (result.data.fans_sum > 0) {
                                $(".fans_sum").html(result.data.fans_sum);
                                $(".fans_sum").removeClass('hide');
                            }
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