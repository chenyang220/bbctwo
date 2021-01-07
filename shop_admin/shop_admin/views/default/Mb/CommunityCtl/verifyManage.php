<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<style>
body{background: #fff;}
</style>
</head>
<body class="<?=$skin?>">
    <form method="post" name="manage-form" id="manage-form" action="<?= Yf_Registry::get('url') ?>?act=goods&amp;op=goods_lockup">
        <input type="hidden" name="form_submit" value="ok">
        <input type="hidden" name="common_id_input">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('用户名'); ?></dt>
                <dd class="opt" id="user_account"></dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('联系方式'); ?></dt>
                <dd class="opt" id="user_mobile"></dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('标题'); ?></dt>
                <dd class="opt" id="explore_title"></dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('内容'); ?></dt>
                <dd class="opt" id="explore_content"></dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('图片/视频'); ?></dt>
                <dd class="opt" id="image_info"></dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('关联商品'); ?></dt>
                <dd class="opt" id="goods_info"></dd>
            </dl>
			<dl class="row">
				  <dt class="tit">
					<label><?= __('审核通过'); ?></label>
				  </dt>
				  <dd class="opt" id="verify_type">
					<div class="onoff" >
					  <label for="verify_enabled" class="cb-enable selected" title="<?= __('是'); ?>"><?= __('是'); ?></label>
					  <label for="verify_disabled" class="cb-disable" title="<?= __('否'); ?>"><?= __('否'); ?></label>
					  <input id="verify_enabled"     name="explore_status" checked="checked" value="0" type="radio">
					  <input id="verify_disabled" name="explore_status" value="4" type="radio">
					</div>
					<p class="notic"></p>
				  </dd>
			</dl>
            <dl class="row">
                <dt class="tit">
                    <label for="explore_verify_remark"><?= __('审核备注'); ?></label>
                </dt>
                <dd class="opt">
                    <textarea rows="2" class="ui-input w600"  name="explore_verify_remark" id="explore_verify_remark"></textarea>
                </dd>
            </dl>
        </div>
    </form>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/Mb/community/verify_manage.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>