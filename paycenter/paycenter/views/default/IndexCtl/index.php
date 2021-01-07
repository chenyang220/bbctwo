<!DOCTYPE html>
<html lang="en" style="width:100%;height:100%;">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1">
	<meta name="description" content="<?=Web_ConfigModel::value('description')?>" />
	<meta name="Keywords" content="<?=Web_ConfigModel::value('keyword')?>" />
	<title><?=Web_ConfigModel::value('site_name')?> - <?=Web_ConfigModel::value('title')?></title>
 	<link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/base.css">
	<link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/palyCenter.css">
	<script src="<?=$this->view->js?>/jquery-1.9.1.js" type="text/javascript"></script>
	<script src="<?=$this->view->js?>/common.js"></script>
	<script src="<?=$this->view->js?>/jquery.cookie.js"></script>
 </head>
<body style="width:100%;height:100%;">
	<div class="index">
		<div class="index-head"><p>PayCenter</p></div>
	</div>
	<div class="center-content">
		<div class="slogan"></div>
		<div class="mid">
			<div class="main-entry"><a href="<?=Yf_Registry::get('url')?>?ctl=Login&met=login" class="login"><i></i><span><?=__('用户登录')?></span></a><a href="<?=Yf_Registry::get('url')?>?ctl=Login&met=reg" class="regist"><i></i><span><?=__('立即注册')?></span></a></div>
		</div>
	</div>
	<div class="back">
		<div class="items">
			<div class="item item1"></div>
			<div class="item item2"></div>
			<div class="item item3"></div>
		</div>
	</div>

	<div class="index-footer">
		<p class="copyright"><?=Web_ConfigModel::value('copyright')?></p>
		<p class="statistics_code"><?php echo Web_ConfigModel::value('icp_number') ?></p>
		<p class="statistics_code"><?php echo Web_ConfigModel::value('statistics_code') ?></p>
	</div>
</body>
</html>	