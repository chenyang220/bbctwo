<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<div class="content">
	<div class="form-style">
		<dl>
			<dt>秒杀活动类型：</dt>
			<dd>精品秒杀</dd>
		</dl>
		<dl>
			<dt>秒杀活动名称：</dt>
			<dd><?=$data['seckill_name']?></dd>
		</dl>
		

		<dl>
			<dt>参与秒杀时间：</dt>
			<dd>
				<span><?=$data['seckill_start_time']?>-<?=$data['seckill_end_time']?> 每天 <?=$data['seckill_time_slot']?></span>
			</dd>
		</dl>
		<dl>
			<dt>每人限购：</dt>
			<dd>
				<span>每人限购<?=$data['seckill_lower_limit']?>件</span>
			</dd>
		</dl>
		<dl>
			<dt>审核状态：</dt>
			<dd><?=$data['seckill_states']?></dd>
		</dl>
		
	</div>
</div>
