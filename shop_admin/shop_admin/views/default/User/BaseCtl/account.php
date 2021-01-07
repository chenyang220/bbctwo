<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<?php
include TPL_PATH . '/'  . 'header.php';
?>
</head>

</head>
<body class="<?=$skin?>">

<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __('云版用户'); ?></h3>
                <h5><?= __('商城云版用户的开通和管理'); ?></h5>
            </div>
            <ul class="tab-base nc-row">
                <li><a class="current"><span><?= __('云版用户'); ?></span></a></li>
            </ul>
        </div>
    </div>
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <li><?= __('对云版用户信息进行操作'); ?></li>
        </ul>
    </div>
    <div class="mod-search cf">
        <div class="fr">
            <a href="#" class="ui-btn ui-btn-sp mrb" id="btn-add"><?= __('新增'); ?><i class="iconfont icon-btn03"></i></a><a class="ui-btn" id="btn-refresh"><?= __('刷新'); ?><i class="iconfont icon-btn01"></i></a>
        </div>
    </div>
    <div class="cf">
        <div class="grid-wrap">
            <table id="grid">
            </table>
            <div id="page"></div>
        </div>
    </div>
</div>
<script src="./shop_admin/static/default/js/controllers/user/base/account.js"></script>
<?php
include TPL_PATH . '/'  . 'footer.php';
?>