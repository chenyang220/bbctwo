<?php 
include __DIR__.'/../../includes/header.php';
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('追加评价'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
</head>
<body>
<header id="header">
    <div class="header-wrap">
        <!-- <div class="header-l"> <a href="javascript:history.go(-1)"> <i class="back"></i> </a> </div> -->
        <div class="header-title">
            <h1><?= __('追加评价'); ?></h1>
        </div>
    </div>
</header>
<div class="nctouch-main-layout" id="member-evaluation-div"> </div>
<footer id="footer" class="posr"></footer>
<script type="text/html" id="member-evaluation-script">
    <form>
        <ul class="nctouch-evaluation-goods">
            <li class="pad20">
                <div class="fz-30 col5 mb-20"><?= __('追加评价：'); ?></div>
                <!-- <?= __('先统计'); ?> <?= __('字符数'); ?> <?= __('限制评论字数'); ?>200<?= __('字以内'); ?> -->
                <div class="evaluation-inp-block">
                    <!-- <input type="text" class="textarea" id="content" name="content" placeholder="<?= __('请输入追加评价'); ?>"> -->
                    <textarea id="content" name="content" class="text-area bor1" placeholder="<?= __('请输入追加评价'); ?>" maxlength="200" style="position: relative;"></textarea>
                    <div style="position: absolute;right: 10px;font-size: 0.5rem;color: #888;">
                        <span id="words">0</span>
                        <span>/ 200</span>
                    </div>
                    <input type="hidden" name="evaluation_goods_id" id="evaluation_goods_id" value="<%=data.evaluation_goods_id%>"/>
                    <input type="hidden" name="order_goods_id" id="order_goods_id" value="<%=data.goods_base.order_goods_id%>"/>
                </div>

            </li>
        </ul>
        <a class="btn-l mt5 mb5 mt-100"><?= __('提交'); ?></a><!-- <?= __('备注：不能点击时添加'); ?>class:unck -->
        <form>
</script>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>

<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript" src="../../js/tmpl/member_evaluation_again_add.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</body>
</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>