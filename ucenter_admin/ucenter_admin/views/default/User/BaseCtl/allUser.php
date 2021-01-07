<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<?php
include TPL_PATH . '/'  . 'header.php';
?>
	<body>
	<div class="wrapper page">
		<!-- <?= __('操作说明');?> -->
		<p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
			<div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
				<h4 title="<?= __('提示相关设置操作时应注意的要点');?>"><?= __('操作提示');?></h4>
				<span id="explanationZoom" title="<?= __('收起提示');?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
			<ul>
				<li></li>
			</ul>
		</div>
		<div class="fixed-bar">
			<div class="item-title">
				<div class="subject">
					<h3><?= __('用户列表');?></h3>
					<h5><?= __('用户列表相关信息总览');?></h5>
				</div>
				<ul class="tab-base nc-row">
					<li><a class="current"><span><?= __('用户列表');?></span></a></li>
				</ul>
			</div>
		</div>
		<div class="ncap-form-default">
			<div class="mod-search cf">
				<div class="fl">
					<ul class="ul-inline">
						<li>
							<input type="text" id="matchCon" class="ui-input ui-input-ph matchCon" placeholder="<?= __('请输入用户名/手机号码查询');?>" style="width: 180px;">
						</li>
						<li><a class="ui-btn mrb" id="search"><?= __('查询');?><i class="iconfont icon-btn02"></i></a></li>
						<li><a class="ui-btn mrb" id="exploreExcel"><?= __('导出');?><i class="iconfont "></i></a></li>
					</ul>
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
	</div>
	<script src="./ucenter_admin/static/default/js/controllers/message/manage.js" charset="utf-8"></script>
<?php
include TPL_PATH . '/'  . 'footer.php';
?>