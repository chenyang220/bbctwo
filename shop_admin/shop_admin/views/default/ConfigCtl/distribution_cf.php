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

    <form method="post" id="distribution-setting-form" name="settingForm">
        <input type="hidden" name="config_type[]" value="distribution_cf"/>
        <div class="ncap-form-default"> 
            <div class="title pl100">
                <h3><?= __('分销员升级条件设置'); ?></h3>
                <p class="notic"><?= __('分销客升级分销掌柜，购买礼包或邀请用户满足其中一个条件即可'); ?></p>
            </div>
            <dl class="row">
				<dt class="tit">邀请人数</dt>
                <dd class="opt">
                    <input id="distribution_invitations" name="distribution_cf[distribution_invitations]" value="<?= $data['distribution_invitations']['config_value'] ?>" class="ui-input" type="text"><i><?= __('人'); ?>
                    <p class="notic"><?= __('通过用户分享链接或二维码注册成为新用户，邀请用户达到此人数后，升级为分销掌柜，享受商家设置的分销掌柜返佣比例。'); ?></p>
                </dd>
            </dl>
            <dl class="row ">
				<dt class="tit">
					购买礼包金额大于等于
				</dt>
                <dd class="opt">
                    <input id="distribution_gprice" name="distribution_cf[distribution_gprice]" value="<?= $data['distribution_gprice']['config_value'] ?>" class="ui-input" type="text"><i><?= __('元的单个礼包'); ?></i>
                    <p class="notic"><?= __('购买单个礼包金额≥此金额时，可升级为分销掌柜,享受商家设置的分销掌柜返佣比例。'); ?></p>
                </dd>
            </dl>
            <div class="title pl100">
                <h3><?= __('邀请用户购买礼包奖励设置'); ?></h3>
            </div>
            <dl class="row">
				<dt class="tit">一级奖励</dt>
                <dd class="opt">
                    <input id="direct_reward" name="distribution_cf[direct_reward]" value="<?= $data['direct_reward']['config_value'] ?>" class="ui-input" type="text"><i><?= __('元'); ?>
                </dd>
            </dl>
            <dl class="row">
				<dt class="tit">二级奖励</dt>
                <dd class="opt">
                    <input id="indirect_reward" name="distribution_cf[indirect_reward]" value="<?= $data['indirect_reward']['config_value'] ?>" class="ui-input" type="text"><i><?= __('元'); ?>
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