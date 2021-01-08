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
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_q27xk278xie.css">
</head>
<body>
	<header id="header" class="posf">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a class="js-cancel-push" href="javascript:history.back(-1);"> <i class="back"></i> </a> -->
            </div>
            <div class="tit">Plus会员-用户协议</div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <div class="plus-agreement-content pl-20 pr-20">
            <!-- <div class="plus-agreement-head">请认真阅读并理解一下内容，其中以加粗方式显著标识的文字，请着重阅读、慎重考虑</div>
            <p>本协议是您与远丰商城（简称“本站”，网址：www.yuanfengtest.com）所有者（以下简称为“远丰”）之间的远丰PLUS会员所订立的契约。请您仔细阅读本协议，您点击“同意并继续”按钮后，本协议即构成双方有约束力的法律文件。</p> -->
            <div class="plus-agreement-li"></div>
            <div class="tc"><a href="javascript:history.back(-1);" class="plus-agreement-btn">同意并继续</a></div>
        </div>
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
  include __DIR__.'/../../includes/footer.php';
?>

<script type="text/javascript">
    $(function() {
        var t = getCookie("key");
        if (!t) {
            window.location.href = WapSiteUrl + "/tmpl/member/login.html";
            return false;
        }
        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?ctl=Plus_User&met=agreement&typ=json',
            data: {k: t,u:getCookie('id')},
            dataType: 'json',
            async: false,
            success: function(data) {
               $('.plus-agreement-li').html(data);

            }
        });
    })
</script>