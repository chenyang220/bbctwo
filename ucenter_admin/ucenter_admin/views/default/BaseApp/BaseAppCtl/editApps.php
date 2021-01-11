<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<?php
include TPL_PATH . '/'  . 'header.php';
?>

<link rel="stylesheet" href="./ucenter_admin/static/default/css/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="./ucenter_admin/static/default/js/libs/jquery/plugins/validator/jquery.validator.js"></script>
<script type="text/javascript" src="./ucenter_admin/static/default/js/libs/jquery/plugins/validator/local/zh_CN.js"></script>
<style>
body{background: #fff;}
.mod-form-rows .label-wrap{font-size:12px;}
.mod-form-rows .row-item {padding-bottom: 15px;margin-bottom: 0;}/*<?= __('兼容');?>IE7 <?= __('，重写');?>common<?= __('的演示');?>*/
.manage-wrapper{margin:20px auto 10px;width:600px;}
.manage-wrap .ui-input{width: 198px;}
.base-form{*zoom: 1;}
.base-form:after{content: '.';display: block;clear: both;height: 0;overflow: hidden;}
.base-form li{float: left;width: 290px;}
.base-form li.odd{padding-right:20px;}
.base-form li.last{width:350px}
.manage-wrap textarea.ui-input{width: 588px;height: 32px;overflow:hidden;}
.contacters{margin-bottom: 10px;}
.contacters h3{margin-bottom: 10px;font-weight: normal;}
.remark .row-item{padding-bottom:0;}
.mod-form-rows .ctn-wrap{overflow: visible;}
.grid-wrap .ui-jqgrid{border-width:1px 0 0 1px;}

</style>
</head>
<body>
<div class="manage-wrapper">
    <div id="manage-wrap" class="manage-wrap">
    	<form id="manage-form" action="">
		<input type="hidden" name="app_id" id = "app_id" value="">
    		<ul class="mod-form-rows base-form cf" id="base-form">
    			<li class="row-item odd">
    				<div class="label-wrap"><label for="app_name"><?= __('应用名称');?></label></div>
    				<div class="ctn-wrap">
                        <input type="text" value="" class="ui-input" name="app_name" id="app_name">
                        <p class="notic" style="padding-left: 80px"><?= __('不能为空')?></p>
                    </div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="app_type"><?= __('应用类型');?></label></div>
    				<div class="ctn-wrap">
                        <input type="text" value="" class="ui-input" name="app_type" id="app_type">
                        <p class="notic" style="padding-left: 80px"><?= __('不能为空')?></p>
                    </div>
    			</li>



    			<!--<li class="row-item odd">
    				<div class="label-wrap"><label for="vendor_amount_money"><?= __('顺序号');?></label></div>
    				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="app_seq" id="app_seq"></div>
    			</li>-->
    			<li class="row-item odd">
    				<div class="label-wrap"><label for="app_key"><?= __('应用密钥');?></label></div>
    				<div class="ctn-wrap">
                        <input type="text" value="" class="ui-input" name="app_key" id="app_key">
                        <p class="notic" style="padding-left: 80px"><?= __('不能为空')?></p>
                    </div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="app_ip_list"><?= __('应用');?> IP <?= __('列表');?></label></div>
    				<div class="ctn-wrap">
                        <input type="text" value="" class="ui-input" name="app_ip_list" id="app_ip_list">
                        <p class="notic" style="padding-left: 80px"><?= __('不能为空')?></p>
                    </div>
    			</li>
				<li class="row-item odd">
    				<div class="label-wrap"><label for="app_url"><?= __('应用网址');?></label></div>
    				<div class="ctn-wrap">
                        <input type="text" value="" class="ui-input" name="app_url" id="app_url">
                        <p class="notic" style="padding-left: 80px"><?= __('不能为空') ?></p>
                    </div>
    			</li>
    			<li class="row-item">
    				<div class="label-wrap"><label for="app_admin_url"><?= __('后台网址');?></label></div>
    				<div class="ctn-wrap">
                        <input type="text" value="" class="ui-input" name="app_admin_url" id="app_admin_url">
                        <p class="notic" style="padding-left: 80px"><?= __('不能为空')?></p>
                    </div>
    			</li>
                <li class="row-item odd">
                    <div class="label-wrap"><label for="app_showname"><?= __('显示名称');?></label></div>
                    <div class="ctn-wrap">
                        <input type="text" value="" class="ui-input" name="app_showname" id="app_showname">
                        <p class="notic" style="padding-left: 80px"><?=__('如果填写显示名称，则会在Ucenter头部中的更多中显示。如果不填写，则不显示。')?></p>
                    </div>
                </li>
				<!--<li class="row-item odd"  style="margin-top:10px;">
					<div class="label-wrap"><label for="enable"><?= __('是否启用');?>:</label></div>
					<div class="onoff">
						<label for="enable1" class="cb-enable  "><?= __('是');?></label>
						<label for="enable0" class="cb-disable  selected"><?= __('否');?></label>
						<input id="enable1"  name ="app_status"  value="1" type="radio">
						<input id="enable0"  name ="app_status"   value="0" type="radio">
					</div>
				</li>-->

    		</ul>
    </div>

</div>
 
<script src="./ucenter_admin/static/default/js/controllers/application/baseapp_edit.js"></script>

<?php
include TPL_PATH . '/'  . 'footer.php';
?>