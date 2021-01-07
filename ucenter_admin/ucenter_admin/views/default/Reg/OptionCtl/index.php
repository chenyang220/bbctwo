<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link href="<?=$this->view->css?>/jquery/plugins/validator/jquery.validator.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body>

<div class="wrapper page">
    <!-- <?= __('操作说明');?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title=""><?= __('操作提示');?></h4>
            <span id="explanationZoom" title="<?= __('收起提示');?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <li><?= __('用户注册，设置注册密码长度与密码复杂度。');?></li>
        </ul>
    </div>
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __('注册设置');?></h3>
                <h5><?= __('用户注册设置');?></h5>
            </div>
            <ul class="tab-base nc-row">
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Config&met=register&config_type%5B%5D=register"><span><?= __('注册设置');?></span></a></li>
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Config&met=regimg&config_type%5B%5D=register_img"><span><?= __('注册图片设置');?></span></a></li>
                <li><a class="current"><span><?= __('注册项设置');?></span></a></li>
            </ul>
        </div>
    </div>

    <div class="mod-search cf">
        <div class="fr">
            <a href="#" class="ui-btn ui-btn-sp mrb" id="btn-add"><?= __('新增');?><i class="iconfont icon-btn03"></i></a>
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

<script type="text/javascript" src="<?=$this->view->js?>/controllers/reg/option_list.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>





