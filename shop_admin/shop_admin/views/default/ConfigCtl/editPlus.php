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
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.config.js"></script>
<!-- <?= __('编辑器源码文件'); ?> -->
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/upload/addCustomizeButton.js"></script>
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
    
    <form method="post" enctype="multipart/form-data" id="plus-setting-form" name="form">
        <input type="hidden" name="config_type[]" value="plus"/>
        <div class="ncap-form-default">
            <div class="title">
                <h3><?= __('plus会员设置'); ?></h3>
            </div>
            <dl class="row">
                <dt class="tit">                
                    <label><em>*</em><?= __('plus会员:'); ?></label>
                </dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="plus_switch1" name="plus[plus_switch]" value="1" type="radio" <?=($data['plus_switch']['config_value']==1 ? 'checked' : '')?> >
                        <label title="<?= __('开启'); ?>" class="cb-enable <?=($data['plus_switch']['config_value']==1 ? 'selected' : '')?> " for="plus_switch1"><?= __('开启'); ?></label>

                        <input id="plus_switch0" name="plus[plus_switch]" value="0" type="radio"  <?=($data['plus_switch']['config_value']==0 ? 'checked' : '')?> >
                        <label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['plus_switch']['config_value']==0 ? 'selected' : '')?>" for="plus_switch0" onclick="parent.$.dialog.confirm(__('一旦关闭，PLUS会员将失去所有权益，确认关闭吗？'), function () {},function () {$('.cb-enable').click();})"><?= __('关闭'); ?></label>
                    </div>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('plus会员购买模式:'); ?></label>
                </dt>
                <dd class="opt">
                <select id="plus_shopping_mode" name="plus[plus_shopping_mode]" style="height: 28px; line-height: 18px;">
                    <option value="1" <?php if($data['plus_shopping_mode']['config_value']==1){echo 'selected';}?> ><?= __('按年度收费'); ?></option>
                    <option value="2" <?php if($data['plus_shopping_mode']['config_value']==2){echo 'selected';}?>><?= __('按季度收费'); ?></option>
                    <option value="3" <?php if($data['plus_shopping_mode']['config_value']==3){echo 'selected';}?>><?= __('按月度收费'); ?></option>
                </select>
            </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('plus会员购买价格:'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="plus_shopping_price" name="plus[plus_shopping_price]" value="<?=($data['plus_shopping_price']['config_value'])?>" class="ui-input w200" type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('plus会员免费试用期(天):'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="plus_probationership" name="plus[plus_probationership]" value="<?=($data['plus_probationership']['config_value'])?>" class="ui-input w400" type="text"/>
                    <p class="notic"><?= __('每个会员只可享受一次试用机会'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('plus会员用户协议:'); ?></label>
                </dt>
                <dd class="opt">
                    <!-- <?= __('加载编辑器的容器'); ?> -->
                    <textarea id="plus_agreement"  name="plus[plus_agreement]" type="text/plain">
                    </textarea>
                </dd>
            </dl>
          <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>


<!-- <?= __('实例化编辑器'); ?> -->
<script type="text/javascript">
    //数据初始化
    var plus_agreement = '<?=($data['plus_agreement']['config_value'])?>';
    var ue = UE.getEditor('plus_agreement', {
        initialFrameWidth: '100%',
        initialFrameHeight: 600,
        toolbars: [
            [
             'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'justifyleft', 'justifycenter', 'justifyright', 'insertunorderedlist', 'insertorderedlist', 'blockquote',
             'emotion', 'insertvideo', 'link', 'removeformat', 'rowspacingtop', 'rowspacingbottom', 'lineheight', 'paragraph', 'fontsize', 'inserttable', 'deletetable', 'insertparagraphbeforetable',
             'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols'
            ]
        ],
        autoClearinitialContent: true,
        //关闭字数统计'); ?>
        wordCount: false,
        //关闭'); ?>elementPath
        elementPathEnabled: false
    });
   //编辑器初始化完成再赋值
   ue.ready(function() {
        ue.setContent(plus_agreement);  //赋值给UEditor
    });
</script>
  
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>