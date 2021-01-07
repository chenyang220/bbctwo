<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body>

<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __('互联登录');?>&nbsp;</h3>
                <h5><?= __('公共平台账号登录配置');?></h5>
            </div>
            <ul class="tab-base nc-row">
                <li><a class="current"><span><?= __('互联登录');?></span></a></li>
            </ul>
        </div>
    </div>
    <?php

  ?>

    <p class="warn_xiaoma"><span></span><em></em></p>
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点');?>"><?= __('操作提示');?></h4>
            <span id="explanationZoom" title="<?= __('收起提示');?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em>
        </div>
        <ul>
            <li><?= __('可以将其它大型平台的登录方式和');?>Ucenter<?= __('进行整合');?>,<?= __('实现微信');?>,<?= __('新浪微薄等等可以在网站前台快速登录。');?></li>
        </ul>
    </div>

    <form method="post" id="connect-qq-setting-form" name="settingForm">
        <input type="hidden" name="config_type[qq]" value="connect"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">QQ AppId</label>
                </dt>
                <dd class="opt">
                    <input id="qq_app_id" name="connect[app_id]" value="<?=($data['qq']['app_id'])?>" class="w400 ui-input " type="text"/>

                    <p class="notic">QQ<?= __('联合登录');?>[<a href="http://connect.qq.com/" target="_blank"><?= __('申请');?>API</a>]</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="site_name">QQ AppKey</label>
                </dt>
                <dd class="opt">
                    <input id="qq_app_key" name="connect[app_key]" value="<?=($data['qq']['app_key'])?>" class="w400 ui-input " type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="site_name"><?= __('是否开启');?></label>
                </dt>
                <dd class="opt">
                        <input id="qq_status" name="connect[status]"  value="1" type="radio" <?=($data['qq']['status']=='1' ? 'checked' : '')?>>
						<label title="<?= __('开启');?>"  for="qq_status"><?= __('开启');?>QQ<?= __('互联');?></label>

						&nbsp;&nbsp;
                        <input id="qq_status" name="connect[status]"  value="0" type="radio" <?=($data['qq']['status']=='1' ? '' : 'checked')?>>
						<label title="<?= __('开启');?>"  for="qq_status"><?= __('关闭');?>QQ<?= __('互联');?></label>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交');?></a></div>
        </div>
    </form>

   <form method="post" id="connect-weibo-setting-form" name="settingForm">
        <input type="hidden" name="config_type[weibo]" value="connect"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">Weibo AppId</label>
                </dt>
                <dd class="opt">
                    <input id="weibo_app_id" name="connect[app_id]" value="<?=($data['weibo']['app_id'])?>" class="w400 ui-input " type="text"/>

                    <p class="notic"><?= __('微博联合登录');?>[<a href="http://open.weibo.com/connect" target="_blank"><?= __('申请');?>API</a>]</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="site_name">Weibo AppKey</label>
                </dt>
                <dd class="opt">
                    <input id="weibo_app_key" name="connect[app_key]" value="<?=($data['weibo']['app_id'])?>" class="w400 ui-input " type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="site_name"><?= __('是否开启');?></label>
                </dt>
                <dd class="opt">
                        <input id="weibo_status" name="connect[status]"  value="1" type="radio" <?=($data['weibo']['status']=='1' ? 'checked' : '')?>>
						<label title="<?= __('开启');?>"  for="weibo_status"><?= __('开启');?>Weibo<?= __('互联');?></label>
                        &nbsp;&nbsp;<input id="weibo_status" name="connect[status]"  value="0" type="radio" <?=($data['weibo']['status']=='1' ? '' : 'checked')?>>
						<label title="<?= __('关闭');?>"  for="weibo_status"><?= __('关闭');?>Weibo<?= __('互联');?></label>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交');?></a></div>
        </div>
    </form>


    <form method="post" id="connect-weixin-setting-form" name="settingForm">
        <input type="hidden" name="config_type[weixin]" value="connect"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">Weixin AppId</label>
                </dt>
                <dd class="opt">
                    <input id="weixin_app_id" name="connect[app_id]" value="<?=($data['weixin']['app_id'])?>" class="w400 ui-input " type="text"/>

                    <p class="notic"><?= __('微信联合登录');?>[<a href="https://open.weixin.qq.com/" target="_blank"><?= __('申请');?>API</a>]</p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="site_name">Weixin AppKey</label>
                </dt>
                <dd class="opt">
                    <input id="weixin_app_key" name="connect[app_key]" value="<?=($data['weixin']['app_key'])?>" class="w400 ui-input " type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="site_name"><?= __('是否开启');?></label>
                </dt>
                <dd class="opt">
                        <input id="weixin_status" name="connect[status]"  value="1" type="radio" <?=($data['weixin']['status']=='1' ? 'checked' : '')?>>
						<label title="<?= __('开启');?>"  for="weixin_status"><?= __('开启');?>Weixin<?= __('互联');?></label>
						&nbsp;&nbsp;
                        <input id="weixin_status" name="connect[status]"  value="0" type="radio" <?=($data['weixin']['status']=='1' ? '' : 'checked')?>>
						<label title="<?= __('关闭');?>"  for="weixin_status"><?= __('关闭');?>Weixin<?= __('互联');?></label>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交');?></a></div>
        </div>
    </form>
    <form method="post" id="connect-wechat-setting-form" name="settingForm">
        <input type="hidden" name="config_type[wechat]" value="connect"/>
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">Wechat AppId</label>
                </dt>
                <dd class="opt">
                    <input id="wechat_app_id" name="connect[app_id]" value="<?=($data['wechat']['app_id'])?>" class="w400 ui-input " type="text"/>

                    <p class="notic"><?= __('微信公众号');?>/<?= __('微信浏览器登录');?>[<a href="https://mp.weixin.qq.com/" target="_blank"><?= __('申请');?>API</a>]</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">Wechat AppKey</label>
                </dt>
                <dd class="opt">
                    <input id="wechat_app_key" name="connect[app_key]" value="<?=($data['wechat']['app_key'])?>" class="w400 ui-input " type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="site_name"><?= __('是否开启');?></label>
                </dt>
                <dd class="opt">
                        <input id="wechat_status" name="connect[status]"  value="1" type="radio" <?=($data['wechat']['status']=='1' ? 'checked' : '')?>>
						<label title="<?= __('开启');?>"  for="wechat_status"><?= __('开启');?>Wechat<?= __('互联');?></label>
						&nbsp;&nbsp;
                        <input id="wechat_status" name="connect[status]"  value="0" type="radio" <?=($data['wechat']['status']=='1' ? '' : 'checked')?>>
						<label title="<?= __('关闭');?>"  for="wechat_status"><?= __('关闭');?>Wechat<?= __('互联');?></label>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交');?></a></div>
        </div>
    </form>

</div>

<script type="text/javascript">
</script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>