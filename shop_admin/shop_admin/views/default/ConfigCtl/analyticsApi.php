<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';


// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();

?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body class="<?=$skin?>">
<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __($menus['father_menu']['menu_name']); ?></h3>
                <h5><?= __($menus['father_menu']['menu_url_note']); ?></h5>
            </div>
            <?php include dirname(__FILE__).'/comm_api_menu.php';?>
        </div>
    </div>
    <?php

  ?>
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>
        </ul>
    </div>
    <form method="post" id="analytics-shop_api-setting-form" name="analyticsSettingForm">
        <input type="hidden" name="config_type[]" value="analytics_api"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('开启状态'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="analytics_statu1" name="analytics_api[analytics_statu]"  value="1" type="radio" <?=(@Yf_Registry::get('analytics_statu')==1 ? 'checked' : '')?>>
                        <label title="<?= __('开启'); ?>" class="cb-enable <?=(@Yf_Registry::get('analytics_statu')==1 ? 'selected' : '')?> " for="analytics_statu1"><?= __('开启'); ?></label>

                        <input id="analytics_statu0" name="analytics_api[analytics_statu]"  value="0" type="radio" <?=(@Yf_Registry::get('analytics_statu')==0 ? 'checked' : '')?>>
                        <label title="<?= __('关闭'); ?>" class="cb-disable <?=(@Yf_Registry::get('analytics_statu')==0 ? 'selected' : '')?>" for="analytics_statu0"><?= __('关闭'); ?></label>
                    </div>
                    <p class="notic"></p>
                </dd>
            </dl>
            
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">Analytics ID</label>
                </dt>
                <dd class="opt">
                    <input id="analytics_api_url" name="analytics_api[analytics_app_id]" value="<?=Yf_Registry::get('analytics_app_id')?>" class="w400 ui-input " type="text"/>

                    <p class="notic">Analytics <?= __('又称数据分析中心，是我们开发的用于整合多个子系统的独立数据分析系统，实现不同平台的数据统计。'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="site_name">Analytics key</label>
                </dt>
                <dd class="opt">
                    <input id="analytics_api_key" name="analytics_api[analytics_api_key]" value="<?=Yf_Registry::get('analytics_api_key')?>" class="ui-input w400" type="text"/>

                    <p class="notic"><?= __('请填写商城系统与 Analytics 通讯的 Key 值，此处的值要与 Analytics 后台应用的值保持一致'); ?></p>
                </dd>
            </dl>
            <dl class="row is-hidden" style="display: none">
                <dt class="tit">
                    <label for="site_name">Analytics name</label>
                </dt>
                <dd class="opt">
                    <input id="analytics_api_key" name="analytics_api[analytics_app_name]" value="<?=Yf_Registry::get('analytics_app_name')?>" class="ui-input w400" type="text"/>

                </dd>
            </dl>
            <dl class="row is-hidden" style="display: none" >
                <dt class="tit">
                    <label for="site_name">Analytics URL</label>
                </dt>
                <dd class="opt">
                    <input id="analytics_api_url" name="analytics_api[analytics_api_url]" value="<?=Yf_Registry::get('analytics_api_url')?>" class="w400 ui-input " type="text"/>

                    <p class="notic"></p>
                </dd>
            </dl>

            <dl class="row" >
                <dt class="tit">
                    <label for="push_time">文件推送时间</label>
                </dt>
                <dd class="opt">
                    <select class="w400" id="analytics_push_time" name="analytics_api[analytics_push_time]"  style="width: 180px; padding: 2px 0;">
                        <?php for($i=0;$i<25;$i++) {
                            if($i < 10) {?>
                                <option value="0<?=$i?>:00" <?php if($i == Yf_Registry::get('analytics_push_time')){ ?> selected="selected" <?php }?> >0<?=$i?>:00</option>
                        <?php }else{ ?>
                                <option value="<?=$i?>:00" <?php if($i == Yf_Registry::get('analytics_push_time')){ ?> selected="selected" <?php }?> ><?=$i?>:00</option>
                        <?php }}?>
                    </select>
                    <p class="notic">请填写每天推送的时间，默认为0点。采用24小时制，只可设置整点时间。例：上午1点选择1，下午1点选择13。</p>
                </dd>
            </dl>

            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp analytics-submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>

</div>

<script type="text/javascript">
</script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>