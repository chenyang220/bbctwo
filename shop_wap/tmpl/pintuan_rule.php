<!DOCTYPE html>
<html lang="en">
    <?php
        include __DIR__.'/../includes/header.php';
    ?>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<title><?= __('拼团规则'); ?></title>
	<link rel="stylesheet" href="../css/base.css">
	<link rel="stylesheet" href="../css/swiper.min.css">
	<link rel="stylesheet" href="../css/fight-groups.css">
	<script type="text/javascript" src="../js/zepto.min.js"></script>
    <script type="text/javascript" src="../js/swiper.min.js"></script>

	<style>
		html,body,.nctouch-main-layout{height:100%;}
	</style>
</head>
<body>
	<header id="header" class="fixed">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="javascript:history.go(-1)"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1><?= __('拼团规则'); ?></h1>
            </div>
        </div>
    </header>
    <div class="nctouch-main-layout bgf">
    	<div class="pt_rule tc">
    		<dl>
    			<dt><?= __('支付'); ?></dt>
    			<dd><?= __('开团'); ?>/<?= __('参团'); ?></dd>
    		</dl>
    		<strong><?= __('》'); ?></strong>
    		<dl>
    			<dt><?= __('分享'); ?></dt>
    			<dd><?= __('好友参团'); ?></dd>
    		</dl>
    		<strong><?= __('》'); ?></strong>
    		<dl>
    			<dt><?= __('拼团成功立即发货'); ?></dt>
    			<dd><?= __('逾期为成团自动退款'); ?></dd>
    		</dl>
    	</div>
    	<div class="pt_rule_text">
    		<dl>
    			<dt>1<?= __('，拼团时间'); ?></dt>
    			<dd><p><?= __('拼团商品活时间为商家设定，若逾期则视为该商品拼团活动结束。'); ?></p></dd>
    		</dl>
    		<dl>
    			<dt>2<?= __('，拼团限制'); ?></dt>
    			<dd><p><?= __('同一款商品，一个用户账号只能拼团买一件。'); ?></p></dd>
    		</dl>
    		<dl>
    			<dt>3<?= __('，拼团失败'); ?></dt>
    			<dd>
    				<h5><?= __('拼团失败后，系统会为您取消订单且退款'); ?></h5>
    				<p>a.<?= __('在拼团时间内，团味道到要求人数，则拼团失败；'); ?></p>
    				<p>b.<?= __('在拼团时间内，商品售罄前团未到达到要求人数，则拼团失败'); ?></p>
    				<p>c.<?= __('高峰时间，同时支付人数过多，团人数受限，系统会以支付信息先后为参考取团要求人数，超出人员开团，如果用户在'); ?>2<?= __('小时内未选择自己开团，则拼团失败。'); ?></p>
    			</dd>
    		</dl>
    		<dl>
    			<dt>4<?= __('，主动退款'); ?></dt>
    			<dd><p><?= __('若用户参与拼团商品且支付成功，当该商品到达结束时间后尚未成团系统将会主动为您退还，参团所用金额。'); ?></p></dd>
    		</dl>
    		<dl>
    			<dt>5<?= __('，其他说明'); ?></dt>
    			<dd>
    				<p>a.<?= __('拼团商品享受包邮福利；'); ?></p>
    				<p>b.<?= __('拼团商品不参加任何其他平台活动；'); ?></p>
    				<p>c.<?= __('最终解释权由'); ?>xxxx</p>
    			</dd>
    		</dl>
    	</div>
    </div>

</body>
<script type="text/javascript">
    $(function(){
            $.ajax({
            url: ApiUrl + "/index.php?ctl=PinTuan&met=rule&typ=json",
            type: 'get',
            dataType: 'json',
            data: '',
            success: function(data) {
                $('.pt_rule_text').html(data.data.rule);
                $('.pt_rule_text').find('strong').addClass('weight');
                $('.pt_rule_text').find('em').addClass('em-em');
                $('.pt_rule_text').find('h1').addClass('h-font-weight');
                $('.pt_rule_text').find('h2').addClass('h-font-weight');
                $('.pt_rule_text').find('h3').addClass('h-font-weight');
                $('.pt_rule_text').find('h4').addClass('h-font-weight');
                $('.pt_rule_text').find('h5').addClass('h-font-weight');
                $('.pt_rule_text').find('h6').addClass('h-font-weight');
                $('.pt_rule_text').find('ul').css('list-style','');
                $('.pt_rule_text').find('li').css('list-style','');
            }
        });
    });
</script>
<?php
    include __DIR__.'/../includes/footer.php';
?>
</html>