<?php
include __DIR__ . '/../../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-touch-fullscreen" content="yes"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="msapplication-tap-highlight" content="no"/>
    <meta name="wap-font-scale" content="no">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1"/>
    <title>我的邀请人</title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" href="../../css/iconfont.css">
    <style type="text/css">
        body {
            background: #fff !important;
        }

        .container {
            margin-top: 5rem;
        }

        .logo-img {
            width: 2.9rem;
            height: 2.9rem;
            margin: auto;
        }

        .container span {
            display: block;
            text-align: center;
            font-size: 15px;
            padding: 1rem 0;
        }

        .bgf {
            background: #f2f2f2 !important;
        }


    </style>
</head>
<body>
<header id="header" class="fixed bgf">
    <div class="header-wrap">
        <!-- <div class="header-l"><a href="javascript:history.go(-1)"><i class="back"></i></a></div> -->
        <div class="header-title posr">
            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">我的邀请人</h1>
        </div>
    </div>
</header>
<div class="container wrap">
    <div class="logo-img"><img class="wp100" src="" id="img"></div>
    <span id="name"></span>
    <span id="we_name"></span>
</div>
</body>
</html>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<script type="text/javascript">
    $(function () {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Distribution&met=getParentInfo&typ=json",
            data: {k: getCookie('key'), u: getCookie('id'), parent_id: getQueryString('parent_id')},
            dataType: "json",
            success: function (r) {
                var data = r.data;
                $("#img").attr('src', data.user_logo);
                $("#name").html(data.user_name);
                if (data.wx_name != "") {
                    $("#we_name").html("微信名：" + data.wx_name);
                }
            }
        });
    })
</script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>
