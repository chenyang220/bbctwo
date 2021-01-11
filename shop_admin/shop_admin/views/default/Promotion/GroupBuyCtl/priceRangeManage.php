<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<style>
.manage-wrap{margin: 20px auto 10px;width:450px;}
input[type="text"]{width:250px}
</style>
</head>
<body class="<?=$skin?>">
<div id="manage-wrap" class="manage-wrap">
	<form id="manage-form" action="#">
		<ul class="mod-form-rows">
			<li class="row-item">
				<div class="label-wrap" style="width:100px;"><label for="range_name"><?= __('价格区间名称'); ?></label></div>
				<div class="ctn-wrap">
                    <input type="text" value="" class="ui-input" name="range_name" id="range_name">
                    <p><?= __('区间名称应该明确，比如'); ?>'1000<?= __('元以下'); ?>'<?= __('和'); ?>'2000<?= __('元'); ?>-3000<?= __('元'); ?>'</p>
				</div>
			</li>
			<li class="row-item">
				<div class="label-wrap" style="width:100px;"><label for="range_start"><?= __('价格区间下限'); ?></label></div>
				<div class="ctn-wrap">
				    <input type="text" value="" class="ui-input" name="range_start" id="range_start">
				    <p><?= __('价格必须为正整数'); ?></p>
				</div>
			</li>
			<li class="row-item">
				<div class="label-wrap" style="width:100px;"><label for="range_end"><?= __('价格区间上限'); ?></label></div>
				<div class="ctn-wrap">
				    <input type="text" value="" class="ui-input" name="range_end" id="range_end">
				    <p><?= __('价格必须为正整数'); ?></p>
				</div>
			</li>
		</ul>
	</form>
</div>
<script src="<?= Yf_Registry::get('base_url') ?>/shop_admin/static/default/js/controllers/promotion/groupbuy/price_range_manage.js"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>