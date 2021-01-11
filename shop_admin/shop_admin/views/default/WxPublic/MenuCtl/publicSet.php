<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单 同级菜单 当前菜单
$menus = $this->getThisMenus();
!$data && $data = array();

?>
    <link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    </head>
    <body class="<?=$skin?>">
    <div class="wrapper page">
        <div class="fixed-bar">
            <div class="item-title">
                <div class="subject">
                    <h3><?=  __($menus['father_menu']['menu_name']);  ?></h3>
                    <h5><?=  __($menus['father_menu']['menu_url_note']);  ?></h5>
                </div>
                <?php include dirname(__FILE__) . '/comm_api_menu.php'; ?>
            </div>
        </div>
        <?php
        ?>
        <!-- 操作说明 -->
        <p class="warn_xiaoma"><span></span><em></em></p>
        <div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
                <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
                <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
            <ul>
                <?= $menus['this_menu']['menu_url_note'] ?>
            </ul>
        </div>
        <form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
            <input type="hidden" name="config_type[]" value="wechat_public"/>
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit"><?= __('状态'); ?></dt>
                    <dd class="opt">
                        <div class="onoff">
                            <input id="wechat_public_status1" name="wechat_public[wechat_public_status]" value="1" type="radio" <?=($data['wechat_public_status']['config_value']==1 ? 'checked' : '')?> >
                            <label title="<?= __('开启'); ?>" class="cb-enable <?=($data['wechat_public_status']['config_value']==1 ? 'selected' : '')?> " for="wechat_public_status1"><?= __('开启'); ?></label>

                            <input id="wechat_public_status0" name="wechat_public[wechat_public_status]" value="0" type="radio"  <?=($data['wechat_public_status']['config_value']==0 ? 'checked' : '')?> >
                            <label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['wechat_public_status']['config_value']==0 ? 'selected' : '')?>" for="wechat_public_status0" onclick="parent.$.dialog.confirm(__('一旦关闭，微信公众号相关功能将停用，确认关闭吗？'), function () {},function () {$('.cb-enable').click();})"><?= __('关闭'); ?></label>
                        </div>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_name">名称</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_name" name="wechat_public[wechat_public_name]" value="<?= $data['wechat_public_name']['config_value']; ?>" class="w400 ui-input " type="text"/>
                         <p class="notic">公开信息->名称</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_start_id">原始ID</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_start_id" name="wechat_public[wechat_public_start_id]" value="<?= $data['wechat_public_start_id']['config_value']?>" class="w400 ui-input " type="text"/>
                        <p class="notic">注册信息->原始ID</p>
                    </dd>
                </dl>

                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_wxaccount">微信号</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_wxaccount" name="wechat_public[wechat_public_wxaccount]" value="<?=  $data['wechat_public_wxaccount']['config_value'] ?>" class="ui-input w400" type="text"/>
                        <p class="notic">公开信息->微信号</p>
                    </dd>
                </dl>

                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_call_url">回调URL</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_call_url" readonly name="wechat_public[wechat_public_call_url]" value="<?= $data['wechat_public_call_url']['config_value'] ?>" class="w400 ui-input " type="text"/>
                        <p class="notic">服务器地址(URL)</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_token">Token</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_token" readonly name="wechat_public[wechat_public_token]" value="<?= $data['wechat_public_token']['config_value']  ?>" class="w400 ui-input " type="text"/>
                        <p class="notic">令牌(Token)</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_appid">AppId</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_appid" name="wechat_public[wechat_public_appid]" value="<?= $data['wechat_public_appid']['config_value'] ?>" class="w400 ui-input " type="text"/>
                        <p class="notic">开发者ID(AppID)</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_secret">Secret</label>
                    </dt>
                    <dd class="opt">
                        <input   id="wechat_public_secret" name="wechat_public[wechat_public_secret]" value="<?= $data['wechat_public_secret']['config_value'] ?>" class="w400 ui-input " type="text"/>
                        <p class="notic">开发者密码(AppSecret)</p>
                    </dd>
                </dl>
                <div class="bot">
                    <a href="javascript:void(0);" class="ui-btn ui-btn-sp im-submit-btn"><?= __('确认提交'); ?></a>
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="<?= $this->view->js ?>/controllers/wxpublic/set.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>