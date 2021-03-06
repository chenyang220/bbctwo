<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
    <link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    </head>
    <body class="<?=$skin?>">
    <div class="wrapper page">
        <div class="fixed-bar">
            <div class="item-title">
                <div class="subject">
                    <h3><?= __('图片空间'); ?></h3>
                    <h5><?= __('商品图片及商家店铺相册管理'); ?></h5>
                </div>
            </div>
        </div>
        <!-- <?= __('操作说明'); ?> -->
        <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
                <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
                <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em> </div>
            <ul>
                <li><?= __('仅删除分组，不删除图片，组内图片将自动归入未分组'); ?></li>
            </ul>
            <div class="fr">
<!--                <a class="ui-btn" id="btn-refresh">--><?//= __('刷新'); ?><!--<i class="iconfont icon-btn01"></i></a>-->
            </div>
        </div>
        <div class="grid-wrap">
            <table id="grid">
            </table>
            <div id="page"></div>
        </div>
    </div>
    
    <script type="text/javascript" src="<?= $this->view->js ?>/controllers/goods/image_list.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>