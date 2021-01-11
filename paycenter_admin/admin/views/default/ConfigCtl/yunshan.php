 <?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body>
<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
           <div class="subject">
			 <h3>银联商务支付配置&nbsp;</h3>
			 <h5>银联商务支付</h5>
		   </div>
			  <ul class="tab-base nc-row">
			  	<li><a  href="index.php?ctl=Payment_Channel&met=index&typ=e"><span><?= __('分类管理'); ?></span></a></li>
                <li><a class="current" href="index.php?ctl=Config&met=yunshan&typ=e&config_type%5B%5D=yunshan"><span>银联商务支付</span></a></li>
			  </ul>
			</div>
		  </div>
		  <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
			<div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
			  <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
			  <span id="explanationZoom" title="收起提示"></span><em class="close_warn">X</em> </div>
			<ul>
			  <li>大华捷通支付和其他支付方式不能同时使用</li>
			</ul>
		  </div>
		  <form style="" method="post" name="form_index" id="yunshan-setting-form">
			 <input type="hidden" name="config_type[]" value="yunshan"/>
			<input name="form_submit" value="ok" type="hidden">
			<span style="display:none" nctype="hide_tag"><a style="padding-left: 5px;">{sitename}</a></span>
			<div class="ncap-form-default">
			  <dl class="row">
				<dt class="tit">
					<label for="yunshan_url"> 支付接口链接前缀</label>
				</dt>
				<dd class="opt">
				  <input id="yunshan_url" name="yunshan[yunshan_url]" value="<?=($data['yunshan_url']['config_value'])?>" class="ui-input w400" type="text" />
				</dd>
			  </dl>
			  <dl class="row">
				<dt class="tit">
					<label for="yunshan_url">主商户ID</label>
				</dt>
				<dd class="opt">
				  <input id="yunshan_url" name="yunshan[yunshan_merid]" value="<?=($data['yunshan_merid']['config_value'])?>" class="ui-input w400" type="text" />
				  <p class="notic"><?= __('联系大华对接人员获取，为商户分配的唯一编号'); ?></p>
				</dd>
			  </dl>
			  <dl class="row">
				<dt class="tit">
					<label for="yunshan_pid"> 支付接口pid</label>
				</dt>
				<dd class="opt">
				  <input id="yunshan_pid" name="yunshan[yunshan_pid]" value="<?=($data['yunshan_pid']['config_value'])?>" class="ui-input w400" type="text" />
				</dd>
			  </dl>
			  <dl class="row">
				<dt class="tit">
					<label for="yunshan_key"> 支付接口key</label>
				</dt>
				<dd class="opt">
				  <input id="yunshan_key" name="yunshan[yunshan_key]" value="<?=($data['yunshan_key']['config_value'])?>" class="ui-input w400" type="text" />
				</dd>
			  </dl>
              
        
             <dl class="row">
				<dt class="tit">
					<label for="yunshantixian_key"> 提现接口key</label>
				</dt>
				<dd class="opt">
				  <input id="yunshantixian_key" name="yunshan[yunshantixian_key]" value="<?=($data['yunshantixian_key']['config_value'])?>" class="ui-input w400" type="text" />
				</dd>
			  </dl>

               <dl class="row">
				<dt class="tit">
					<label for="yunshan_mchid">APP支付商户号</label>
				</dt>
				<dd class="opt">
				  <input id="yunshan_mchid" name="yunshan[yunshan_mchid]" value="<?=($data['yunshan_mchid']['config_value'])?>" class="ui-input w400" type="text" />
				  <p class="notic"><?= __('分账APP支付商户号'); ?></p>
				</dd>
			  </dl>
                            
               <dl class="row">
				<dt class="tit">
					<label for="yunshan_cbmchid">C扫B支付商户号</label>
				</dt>
				<dd class="opt">
				  <input id="yunshan_mchid" name="yunshan[yunshan_cbmchid]" value="<?=($data['yunshan_cbmchid']['config_value'])?>" class="ui-input w400" type="text" />
				  <p class="notic"><?= __('分账C扫B支付商户号'); ?></p>
				</dd>
			  </dl>
                            
               <dl class="row">
				<dt class="tit">
					<label for="yunshan_xcxmchid">小程序支付商户号</label>
				</dt>
				<dd class="opt">
				  <input id="yunshan_mchid" name="yunshan[yunshan_xcxmchid]" value="<?=($data['yunshan_xcxmchid']['config_value'])?>" class="ui-input w400" type="text" />
				  <p class="notic"><?= __('分账小程序支付商户号'); ?></p>
				</dd>
			  </dl>

            <dl class="row">
                <dt class="tit"><?= __('是否启用'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="yunshan_status1" name="yunshan[yunshan_status]"  value="1" type="radio" <?=($data['site_status']['config_value']==1 ? 'checked' : '')?>>
						<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['yunshan_status']['config_value']==1 ? 'selected' : '')?> " for="yunshan_status1"><?= __('开启'); ?></label>

                        <input id="yunshan_status0" name="yunshan[yunshan_status]"  value="0" type="radio" <?=($data['site_status']['config_value']==0 ? 'checked' : '')?>>
						<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['yunshan_status']['config_value']==0 ? 'selected' : '')?>" for="yunshan_status0"><?= __('关闭'); ?></label>
                    </div>
                    <p class="notic"><?= __('如果开启则分类管理中的支付无效'); ?></p>
                </dd>
            </dl>
			  <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn">确认提交</a></div>
			</div>
		  </form>
  </div>
</div>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>