<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<style>
body{background: #fff;}
</style>
</head>
<body class="<?=$skin?>">

                <form method="post" name="manage-form" id="manage-form">
                    <input type="hidden" name="seckill_id" value="<?=request_int('id')?>">

                    <div class="ncap-form-default">
                        
						<dl class="row">
							  <dt class="tit">
								<label><?= __('审核通过'); ?></label>
							  </dt>
							  <dd class="opt">
								<div class="onoff">
								  <label for="verify_enabled" class="cb-enable selected" title="<?= __('是'); ?>"><?= __('是'); ?></label>
								  <label for="verify_disabled" class="cb-disable" title="<?= __('否'); ?>"><?= __('否'); ?></label>
								  <input id="verify_enabled"     name="seckill_state" checked="checked" value="1" type="radio">
								  <input id="verify_disabled" name="seckill_state" value="4" type="radio">
								</div>
								<p class="notic"></p>
							  </dd>
						</dl>
                        <div class="bot">
	                    <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn" id="submitBtn"><?= __('确认提交'); ?></a>
	                	</div>
                    </div>


                </form>

<script type="text/javascript">




</script>

<script type="text/javascript" src="<?=$this->view->js?>/controllers/promotion/seckill/seckill_shen.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>