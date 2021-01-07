<?php
    include __DIR__ . '/../includes/header.php';
?>
    <!doctype html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-touch-fullscreen" content="yes" />
        <meta name="format-detection" content="telephone=no" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta name="format-detection" content="telephone=no" />
        <meta name="msapplication-tap-highlight" content="no" />
        <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
        <title><?= __('站点关闭'); ?></title>
        <link rel="stylesheet" type="text/css" href="../css/base.css">
        <link rel="stylesheet" type="text/css" href="../css/nctouch_common.css">
        <link rel="stylesheet" type="text/css" href="../css/swiper.min.css">
    </head>
    <body>
    <header id="header">
        <div class="reason"></div>
    </header>

    <footer id="footer" class="bottom"></footer>
    <script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/template.js"></script>
    <script type="text/javascript" src="../js/common.js"></script>
    <script type="text/javascript" src="../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>

    <script>
        $(function() {
            $.post(ApiUrl + "/index.php?ctl=Index&met=getClosedReason&typ=json&ua=wap", function (result) {
                if (result.status == 250) {
                    $(".reason").html(result.msg)
                } else {
                    window.location.href = ShopWapUrl;
                    return false;
                }
            }, "json");
        })

    </script>

    </body>
    </html>
<?php
    include __DIR__ . '/../includes/footer.php';
?>
