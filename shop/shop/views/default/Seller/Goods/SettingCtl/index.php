<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<div class="form-style">
	<dl>
		<dt>是否支持到货通知<i>*</i>：</dt>
		<dd>
			<label><input class="mr4 align-middle" type="radio"><span>是</span></label>
			<label class="ml20"><input class="mr4 align-middle" type="radio"><span>否</span></label>
		</dd>
	</dl>
	<dl>
		<dt></dt>
		<dd><input type="submit" class="button button_red bbc_seller_submit_btns mt40" value="提交"></dd>
	</dl>
</div>