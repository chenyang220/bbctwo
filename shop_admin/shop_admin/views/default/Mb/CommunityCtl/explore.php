<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
<style>
    .js-plugin-fenxiao {
        display: none;
    }
</style>
</head>
<body class="<?=$skin?>">
<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __($menus['father_menu']['menu_name']); ?></h3>
                <h5><?= __($menus['father_menu']['menu_url_note']); ?></h5>
            </div>
            <ul class="tab-base nc-row">
                <?php
foreach($menus['brother_menu'] as $key=>$val){
    if(in_array($val['rights_id'],$admin_rights)||$val['rights_id']==0){
        ?>
        <li><a <?php if(!array_diff($menus['this_menu'], $val)){?> class="current"<?php }?> href="<?= Yf_Registry::get('url') ?>?ctl=<?=$val['menu_url_ctl']?>&met=<?=$val['menu_url_met']?><?php if($val['menu_url_parem']){?>&<?=$val['menu_url_parem']?><?php }?>"><span><?= __($val['menu_name']); ?></span></a></li>
    <?php
    }
}
?>

            </ul>
        </div>
    </div>
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>
        </ul>
    </div>

    <form method="post" id="communitySet-form" name="settingForm">
        <input type="hidden" name="config_type[]" value="explore"/>
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('社区功能'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                            <div class="onoff">
                                <label title="<?= __('开启'); ?>" class="cb-enable <?=($data['exploreset']['config_value']==1 ? 'selected' : '')?> " for="redpacketset_enable"><?= __('开启'); ?></label>
                                <label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['exploreset']['config_value']==0 ? 'selected' : '')?>" for="redpacketset_disabled"><?= __('关闭'); ?></label>
                                <input type="radio" value="1" name="explore[exploreset]" id="redpacketset_enable" <?=($data['exploreset']['config_value']==1 ? 'checked' : '')?> />
                                <input type="radio" value="0" name="explore[exploreset]" id="redpacketset_disabled" <?=($data['exploreset']['config_value']==0 ? 'checked' : '')?> />
                            </div>
                        </li>
                    </ul>
                    <p class="notic"><?= __('关闭后，移动端不展示此模块功能'); ?></p>
                </dd>
            </dl>
           <dl class="row">
                <dt class="tit"><?= __('内容审核'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                            <div class="onoff">
                                <label title="<?= __('开启'); ?>" class="cb-enable <?=($data['explorereview']['config_value']==1 ? 'selected' : '')?> " for="review_enable"><?= __('开启'); ?></label>
                                <label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['explorereview']['config_value']==0 ? 'selected' : '')?>" for="review_disabled"><?= __('关闭'); ?></label>
                                <input type="radio" value="1" name="explore[explorereview]" id="review_enable" <?=($data['explorereview']['config_value']==1 ? 'checked' : '')?> />
                                <input type="radio" value="0" name="explore[explorereview]" id="review_disabled" <?=($data['explorereview']['config_value']==0 ? 'checked' : '')?> />
                            </div>
                        </li>
                    </ul>
                    <p class="notic"><?= __('开启内容审核后，用户发布的内容需要平台审核后展示'); ?></p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></a></div>
        </div>
    </form>
</div>

    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>