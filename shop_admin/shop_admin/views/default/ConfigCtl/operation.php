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
                <!-- <li><a class="current"><span><?= __('运营设置'); ?></span></a></li> -->
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

    <form method="post" id="operation-setting-form" name="settingForm">
        <input type="hidden" name="config_type[]" value="operation"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('货到付款'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                            <div class="onoff">
                                <label title="<?= __('开启'); ?>" class="cb-enable <?=($data['cash_on_delivery_status']['config_value']==1 ? 'selected' : '')?> " for="cash_on_delivery_status_enable"><?= __('开启'); ?></label>
                                <label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['cash_on_delivery_status']['config_value']==0 ? 'selected' : '')?>" for="cash_on_delivery_status_disabled"><?= __('关闭'); ?></label>
                                <input type="radio" value="1" name="operation[cash_on_delivery_status]" id="cash_on_delivery_status_enable" <?=($data['cash_on_delivery_status']['config_value']==1 ? 'checked' : '')?> />
                                <input type="radio" value="0" name="operation[cash_on_delivery_status]" id="cash_on_delivery_status_disabled" <?=($data['cash_on_delivery_status']['config_value']==0 ? 'checked' : '')?> />
                            </div>
                        </li>
                    </ul>
                    <p class="notic"><?= __('货到付款功能开启后，商城系统允许消费者选择货到付款选项'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('消费者保障服务'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                <div class="onoff">
                <label title="<?= __('开启'); ?>" class="cb-enable <?=($data['protection_service_status']['config_value']==1 ? 'selected' : '')?> " for="protection_service_enable"><?= __('开启'); ?></label>
                <label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['protection_service_status']['config_value']==0 ? 'selected' : '')?>" for="protection_service_disabled"><?= __('关闭'); ?></label>
                <input type="radio" value="1" name="operation[protection_service_status]" id="protection_service_enable" <?=($data['protection_service_status']['config_value']==1 ? 'checked' : '')?> />
                <input type="radio" value="0" name="operation[protection_service_status]" id="protection_service_disabled" <?=($data['protection_service_status']['config_value']==0 ? 'checked' : '')?> />
                </div>
                        </li>
                    </ul>
                    <p class="notic"><?= __('消费者保障服务开启后，店铺可以申请加入保障服务，为消费者提供商品筛选依据'); ?></p>
                </dd>
            </dl>
            <!--<dl class="row">
                <dt class="tit"><?= __('物流自提服务站'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                            <div class="onoff">
                <label title="<?= __('开启'); ?>" class="cb-enable <?/*=($data['service_station_status']['config_value']==1 ? 'selected' : '')*/?> " for="service_station_enable"><?= __('开启'); ?></label>
                <label title="<?= __('关闭'); ?>" class="cb-disable <?/*=($data['service_station_status']['config_value']==0 ? 'selected' : '')*/?>" for="service_station_disabled"><?= __('关闭'); ?></label>
                <input type="radio" value="1" name="operation[service_station_status]" id="service_station_enable" <?/*=($data['service_station_status']['config_value']==1 ? 'checked' : '')*/?> />
                <input type="radio" value="0" name="operation[service_station_status]" id="service_station_disabled" <?/*=($data['service_station_status']['config_value']==0 ? 'checked' : '')*/?> />
                </div>
                        </li>
                    </ul>
                    <p class="notic"><?= __('现在去设置物流自提服务站使用的快递公司'); ?></p>
                </dd>
            </dl>-->

            <dl class="row">
                <dt class="tit"><?= __('会员折扣仅限自营店铺'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                <div class="onoff" style="height: 24px;">
                <label title="<?= __('开启'); ?>" class="cb-enable <?=($data['rate_service_status']['config_value']==1 ? 'selected' : '')?> " for="rate_service_enable"><?= __('开启'); ?></label>
                <label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['rate_service_status']['config_value']==0 ? 'selected' : '')?>" for="rate_service_disabled"><?= __('关闭'); ?></label>
                <input type="radio" value="1" name="operation[rate_service_status]" id="rate_service_enable" <?=($data['rate_service_status']['config_value']==1 ? 'checked' : '')?> />
                <input type="radio" value="0" name="operation[rate_service_status]" id="rate_service_disabled" <?=($data['rate_service_status']['config_value']==0 ? 'checked' : '')?> />
                </div>
                        </li>
                    </ul>
                    <p class="notic"><?= __('开启状态下仅限自营店铺享受会员折扣，关闭状态下平台所有店铺享受会员折扣'); ?></p>
                </dd>
            </dl>
            <!--
            <dl class="row js-plugin-fenxiao">
                <dt class="tit">
                    <label for="fenxiao_cycle"><?= __('推广员三级分销结算周期'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="fenxiao_cycle" name="operation[fenxiao_cycle]" value="<?= $data['fenxiao_cycle']['config_value'] ?>" class="ui-input" type="text"><i><?= __('天'); ?></i>

                    <p class="notic"><?= __('请输入'); ?>1-100<?= __('的整数推广员三级分销结算周期会按照确认订单后的'); ?>n<?= __('天后进行结算'); ?></p>
                </dd>
            </dl>
             -->
            <!-- <?= __('一级佣金比例'); ?> -->

            <dl class="row">
                <dt class="tit"><?= __('是否开启平台验证退款退货'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="plat_is_open1" name="operation[plat_is_open]" value="1" type="radio" <?= ($data['plat_is_open']['config_value'] == 1 ? 'checked' : '') ?> >
                        <label title="<?= __('开启'); ?>" class="cb-enable <?= ($data['plat_is_open']['config_value'] == 1 ? 'selected' : '') ?> " for="plat_is_open1"><?= __('开启'); ?></label>

                        <input id="plat_is_open0" name="operation[plat_is_open]" value="0" type="radio" <?= ($data['plat_is_open']['config_value'] == 0 ? 'checked' : '') ?> >
                        <label title="<?= __('关闭'); ?>" class="cb-disable <?= ($data['plat_is_open']['config_value'] == 0 ? 'selected' : '') ?>" for="plat_is_open0"><?= __('关闭'); ?></label>
                    </div>

                    <p class="notic"></p>
                </dd>
            </dl>
            <?php if(Yf_Registry::get('yunshanstatus')==1){?>
                <dl class="row">
                    <dt class="tit">
                        <label for="settlement_cycle"><?= __('平台结算周期'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input type="number" min="1"  name="operation[settlement_cycle]" class="ui-input w50" id="settlement_cycle" value="<?=($data['settlement_cycle']['config_value'])?>">
                        <p class="notic"><?= __('单位天,设置后，平台在订单确认收货时起算，经过结算周期后自动发起结算'); ?></p>
                    </dd>
                </dl>
            <?php } ?>
            <div class="bot"><a href="JavaScript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></a></div>
        </div>
    </form>
</div>
    <script type="text/javascript">
        $(function () {
            Public.ajaxGet(SITE_URL + '?ctl=Fenxiao&met=getStatus&typ=json', {}, function (data) {
                if (data.status == 200) {
                    if (data.data.status == 1) {
                        $('dl.js-plugin-fenxiao').show();
                    }
                }
            });
        });
    </script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>