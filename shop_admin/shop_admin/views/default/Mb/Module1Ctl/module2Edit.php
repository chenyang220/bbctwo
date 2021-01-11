<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="<?=$this->view->css?>/new-file.css">

<!--<?= __('团购'); ?>-->
<div class="sale-box">
        <h4 class="sale-title"><?= __('标题：'); ?></h4>
        <div class="relative mb20">
            <input class="sale-tit-input" type="text" value="<?= __('团购'); ?>" maxlength="4">
            <em class="sale-tit-limit"><b>1</b>/4</em>
        </div>
        <div>
            <h4 class="sale-title"><?= __('内容：'); ?></h4>
            <div class="items-box">
                <ul class="sale-limit-items">
                    <li>
                        <em><img src=""></em>
                        <span>1111111111</span>
                        <strong><?= __('￥'); ?>99.00</strong>
                        <button class="del-contentGood" v-on:click="delSale(index)"><?= __('删除'); ?></button>
                    </li>
                </ul>
            </div>
            <div class="mod-search cf">
                    <div>
                        <ul class="ul-inline clearfix">
                            <li class="fl mr10">
                                <input class="ui-input ui-input-ph matchCon" type="text" placeholder="<?= __('团购名称'); ?>...">
                            </li>
                            <li class="fl mr10">
                                <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon"  placeholder="<?= __('商品名称'); ?>">
                            
                            </li>
                            <li class="fl mr10">
                                <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon"  placeholder="<?= __('店铺名称'); ?>">
                            
                            </li>
                            <li class="fl"> <a class="ui-btn" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                        </ul>
                    </div>
            </div>
            <div class="ui-state-default ui-jqgrid-hdiv ui-corner-top">
                <table class="sale-table sale1" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="activity"><?= __('团购名称'); ?></th>
                                <th class="store"><?= __('商品名称'); ?></th>
                                <th class="img"><?= __('团购图片'); ?></th>
                                <th class="operate"><?= __('操作'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <td>111</td>
                                    <td>aaa</td>
                                    <td><img src=""></td>
                                    <td><button class="btn-addgoods"><?= __('添加'); ?></button></td>
                                </tr>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
    
<!-- <?= __('平台红包'); ?> -->
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