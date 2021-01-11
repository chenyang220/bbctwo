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
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
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

    <form method="post" enctype="multipart/form-data" id="plus-quity-setting-form" name="form">
        <input type="hidden" name="config_type[]" value="plus"/>
        <div class="ncap-form-default">
            <div class="title">
                <h3><?= __('plus会员权益设置'); ?></h3>
            </div>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('享受会员折扣:  plus会员商品折扣'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="plus_rate" data-type="number" name="plus[plus_rate]" value="<?=($data['plus_rate']['config_value'])?>" class="ui-input w100" type="text"/><span><?=__(''); ?></span>
                    <p class="notic"><?=__('保留2位小数,须高于当前系统最高会员折扣'); ?><i id="user_grade_rate_min"></i>%</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('享受积分加倍:  消费赠送积分倍数  X'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="plus_integral" name="plus[plus_integral]" value="<?=($data['plus_integral']['config_value'])?>" class="ui-input w100" type="text"/><span><?=__(''); ?></span>
                    <p class="notic"><?=__('须填写>1的数字'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('享受超级会员日:  赠送平台无门槛通用红包'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="plus_general_red" name="plus[plus_general_red]" value="<?=($data['plus_general_red']['config_value'])?>" class="ui-input w100" type="text"/><span><?=__('元'); ?></span>
                    <p class="notic"></p>
                </dd>
                <dt class="tit">
                    <label for="redpacket_t_sdate"><em>*</em><?= __('有效期'); ?></label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?=($data['plus_general_date']['config_value'])?>" class="ui-input ui-datepicker-input" name="plus[plus_general_date]" id="filter-fromDate">
                    <span class="err"></span>
                    <p class="notic"><?= __('有效期为7天之内'); ?></p>
                </dd>

            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('享受开通即送平台红包:  赠送平台红包'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="plus_quota" name="plus[plus_quota]" value="<?=($data['plus_quota']['config_value'])?>"  class="ui-input w100" type="text"/><span><?=__('元，消费满'); ?></span>
                    <input id="plus_red_packet" name="plus[plus_red_packet]" value="<?=($data['plus_red_packet']['config_value'])?>" class="ui-input w100" type="text"/><span><?=__('元使用，使用期限为领取后的30天之内。'); ?></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('客服服务:'); ?></label>
                </dt>
                <dd class="opt">
                    <span><?=__('24小时客服服务。'); ?></span>
                    <p class="notic"></p>
                </dd>
            </dl>
            <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>
<script>
    $(".bot").click(function () {
        var date = $("#filter-fromDate").val();
        $("#filter-fromDate").attr("value",date);
    })
</script>

<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>

<script src="<?= Yf_Registry::get('base_url') ?>/shop_admin/static/default/js/controllers/promotion/redpacket/redpacket_temp_manage.js"></script>


<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css" rel="stylesheet" type="text/css">
<script src="<?= $this->view->js_com ?>/plugins/jquery.datetimepicker.js"></script>
<script src="<?= Yf_Registry::get('base_url') ?>/shop_admin/static/default/js/controllers/promotion/redpacket/redpacket_temp_manage.js"></script>
<script src="<?= Yf_Registry::get('base_url') ?>/shop_admin/static/default/js/controllers/trade/order/order_list.js"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>

<script type="text/javascript">
    $.ajax({
        type: 'post',
        url: SITE_URL + '/index.php?ctl=User_Grade&met=getGradeLists&typ=json',
        dataType: 'json',
        success: function (result) {
            $('#user_grade_rate_min').text(result.data.user_grade_rate_min);
        }
    });
</script>