<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>

<!--备注： 此处功能暂未做 -->
<form>
	<div class="ncap-form-default pt30">
		<dl class="row">
			<dt class="tit">用户名：</dt>
			<dd class="opt"><input class="ui-input w200" type="text" value="admin"> </dd>
		</dl>
		<dl class="row">
			<dt class="tit">原密码：</dt>
			<dd class="opt"><input class="ui-input w200" type="text"> </dd>
		</dl>
		<dl class="row">
			<dt class="tit">新密码：</dt>
			<dd class="opt"><input class="ui-input w200" type="text"></dd>
		</dl>
		<dl class="row">
			<dt class="tit">确认密码：</dt>
			<dd class="opt"><input class="ui-input w200" type="text"></dd>
		</dl>
	</div>
	
</form>