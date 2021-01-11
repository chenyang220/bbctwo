<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="<?=$this->view->css?>/new-file.css">


<!--平台红包-->
<div class="sale-box">
        <h4 class="sale-title"><?= __('标题：'); ?></h4>
        <div class="relative mb20">
            <input class="sale-tit-input" type="text" value="<?= __('平台红包'); ?>" maxlength="4">
            <em class="sale-tit-limit"><b>1</b>/4</em>
        </div>
        <div>
            <h4 class="sale-title"><?= __('内容：'); ?></h4>
            <div class="items-box">
                <div class="up-img-box"><img class="" src="" alt=""></div>
                <p class="up-tips"><?= __('上传平台红包活动入口图片，推荐图片尺寸'); ?>300x150<?= __('像素'); ?></p>
                <div class="up-img"><input type="file"><span><?= __('图片上传'); ?></span></div>
            </div>
  
        </div>
    </div>