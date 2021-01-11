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

    <form method="post" enctype="multipart/form-data" id="account-setting-form" name="form2">
        <input type="hidden" name="config_type[]" value="set_account"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="company_name"><em>*</em><?= __('单位名称'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="company_name" name="set_account[company_name]" value="<?= __($data['company_name']['config_value']); ?>" class="ui-input w346" type="text"/>
                    <p class="notic"><?= __('请填写收款单位名称'); ?></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="bank_name"><em>*</em><?= __('开户行'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="bank_name" name="set_account[bank_name]" value="<?=($data['bank_name']['config_value'])?>" class="ui-input w346" type="text"/>
                    <p class="notic"><?= __('请填写开户行名称'); ?></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label for="bank_account"><em>*</em><?= __('银行账号'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="bank_account" name="set_account[bank_account]" value="<?=($data['bank_account']['config_value'])?>" class="ui-input w346" type="text"/>

                    <p class="notic"><?= __('请填写银行账号信息'); ?></p>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js?v=1" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>