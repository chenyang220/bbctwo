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
                 <h3><?= __('供应商入驻'); ?></h3>
                 <h5><?= __('供应商入驻将在入驻展示'); ?></h5>
            </div>
            <ul class="tab-base nc-row">
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Config&met=supplier_slider&config_type%5B%5D=supplier_slider"><span><?= __('幻灯片管理'); ?></span></a></li>
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Supplier_Help&met=help"><span><?= __('供应商入驻'); ?></span></a></li>
                <li><a class="current" ><span><?= __('供应商入驻设置'); ?></span></a></li>
            </ul>
        </div>
    </div>
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <li><?= __('供应商入驻资格设置'); ?></li>
        </ul>
    </div>

    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit"><?= __('入驻资格设置'); ?></dt>
            <dd class="opt opt-reset">
                <?php if($data){?>
                    <input id="business" name="join_setting[supplier_type]"  value="1" type="radio" <?=($data['supplier_type']['config_value']=='1'? 'checked' : '')?>><label title="<?= __('仅允许企业入驻'); ?>"  for="type_business" ><?= __('仅允许企业入驻'); ?></label>
                    <br/>
                    <input id="person" name="join_setting[supplier_type]"  value="2" type="radio" <?=($data['supplier_type']['config_value']=='2'? 'checked' : '')?>><label title="<?= __('仅允许个人入驻'); ?>"  for="type_person" ><?= __('仅允许个人入驻'); ?></label>
                    <br/>
                    <input id="both" name="join_setting[supplier_type]"  value="3" type="radio" <?=($data['supplier_type']['config_value']=='3'? 'checked' : '')?>><label title="<?= __('允许企业和个人入驻'); ?>"  for="type_both" ><?= __('允许企业和个人入驻'); ?></label>
                <?php }else{?>
                    <input id="business" name="join_setting[supplier_type]"  value="1" type="radio" checked="checked"><label title="<?= __('仅允许企业入驻'); ?>"  for="type_business" ><?= __('仅允许企业入驻'); ?></label>
                    <br/>
                    <input id="person" name="join_setting[supplier_type]"  value="2" type="radio" ><label title="<?= __('仅允许个人入驻'); ?>"  for="type_person" ><?= __('仅允许个人入驻'); ?></label>
                    <br/>
                    <input id="both" name="join_setting[supplier_type]"  value="3" type="radio" ><label title="<?= __('允许企业和个人入驻'); ?>"  for="type_both" ><?= __('允许企业和个人入驻'); ?></label>
                <?php }?>
            </dd>
        </dl>
    </div>

    <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
</div>
<script>
$(".submit-btn").click(function(){
    var join_type =  $("input[type='radio']:checked").val();
    var type = '';

    if(join_type == 1)
    {
        type = '<?= __('仅允许企业入驻'); ?>';
    }
    if(join_type == 2)
    {
        type = '<?= __('仅允许个人入驻'); ?>';
    }
    if(join_type == 3)
    {
        type = '<?= __('允许企业和个人入驻'); ?>';
    }
    $.dialog.confirm(type, function ()
        {
            Public.ajaxPost("./index.php?ctl=Config&met=editJoinSetting&typ=json", {join_type: join_type,config_key:'supplier_type',config_type:'supplier_setting'}, function (e)
            {
                if (e && 200 == e.status)
                {
                    parent.Public.tips({content: "<?= __('修改成功！'); ?>"});
                    location.reload();
                }
                else
                {
                    parent.Public.tips({type: 1, content: "<?= __('修改失败！'); ?>" + e.msg})
                }
            })
        })

});
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>