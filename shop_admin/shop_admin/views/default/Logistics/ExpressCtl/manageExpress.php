<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body class="<?=$skin?>">
    <form method="post" id="manage-form" name="settingForm">
        <input type="hidden" name="express_id" id="express_id" value="<?=$data['express_id']?>">

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('物流公司'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
							<span><?=$data['express_name']?></span>
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('物流描述'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
							<span><?=$data['express_pinyin']?></span>
                        </li>
                    </ul>
                </dd>
            </dl>
	    <dl class="row">
                <dt class="tit"><?= __('状态'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
						<div class="onoff" id="express_status">
								<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['express_status']=='1' ? 'selected' : '')?> " for="express_status_enable"><?= __('开启'); ?></label>
								<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['express_status']=='0' ? 'selected' : '')?>" for="express_status_disabled"><?= __('关闭'); ?></label>
								<input type="radio" value="1" name="express_status" id="express_status_enable" <?=($data['express_status']=='1' ? 'checked' : '')?> />
								<input type="radio" value="0" name="express_status" id="express_status_disabled" <?=($data['express_status']=='0' ? 'checked' : '')?> />
						</div>
                        </li>
                    </ul>
                </dd>
         </dl>
		 <dl class="row">
                <dt class="tit"><?= __('常用'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
						<div class="onoff" id="express_commonorder">
								<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['express_commonorder']=='1' ? 'selected' : '')?> " for="express_commonorder_enable"><?= __('开启'); ?></label>
								<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['express_commonorder']=='0' ? 'selected' : '')?>" for="express_commonorder_disabled"><?= __('关闭'); ?></label>
								<input type="radio" value="1" name="express_commonorder" id="express_commonorder_enable" <?=($data['express_commonorder']=='1' ? 'checked' : '')?> />
								<input type="radio" value="0" name="express_commonorder" id="express_commonorder_disabled" <?=($data['express_commonorder']=='0' ? 'checked' : '')?> />
						</div>
                        </li>
                    </ul>
                </dd>
         </dl>
		 <!--<dl class="row">
                <dt class="tit"><?= __('自提'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
						<div class="onoff" id="express_displayorder">
								<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['express_displayorder']=='1' ? 'selected' : '')?> " for="express_displayorder_enable"><?= __('开启'); ?></label>
								<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['express_displayorder']=='0' ? 'selected' : '')?>" for="express_displayorder_disabled"><?= __('关闭'); ?></label>
								<input type="radio" value="1" name="express_displayorder" id="express_displayorder_enable" <?=($data['express_displayorder']=='1' ? 'checked' : '')?> />
								<input type="radio" value="0" name="express_displayorder" id="express_displayorder_disabled" <?=($data['express_displayorder']=='0' ? 'checked' : '')?> />
						</div>
                        </li>
                    </ul>
                </dd>
            </dl>-->	
        </div>
    </form>
<script src="<?= Yf_Registry::get('base_url') ?>/shop_admin/static/default/js/controllers/logistics/express/express_manage.js"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>