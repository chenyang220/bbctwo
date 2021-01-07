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
                <!-- <li><a class="current"><span><?= __('设置'); ?></span></a></li>
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Shop_Domain&met=indexs"><span><?= __('域名列表'); ?></span></a></li> -->
             
            </ul>
        </div>
    </div>
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em> </div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>
        </ul>
    </div>
    
        <form method="post" enctype="multipart/form-data" id="shop_domain_form" name="form1">
        <input type="hidden" name="config_type[]" value="domain"/>

        <div class="ncap-form-default">
         <dl class="row">
                <dt class="tit"><?= __('是否启动二级域名'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <label for="shop_domain1" class="cb-enable  <?=($data['shop_domain']['config_value'] ? 'selected' : '')?>"><?= __('开启'); ?></label>
                        <label for="shop_domain0" class="cb-disable  <?=(!$data['shop_domain']['config_value'] ? 'selected' : '')?>"><?= __('关闭'); ?></label>
                        <input id="shop_domain1" name="domain[shop_domain]"  <?=($data['shop_domain']['config_value'] ? 'checked' : '')?>  value="1" type="radio">
                        <input id="shop_domain0" name="domain[shop_domain]" <?=(!$data['shop_domain']['config_value'] ? 'checked' : '')?>  value="0" type="radio">
                       
                    </div>
                    <p class="notic"><?= __('启用二级域名需要您的服务器支持泛域名解析'); ?></p>
                </dd>
            </dl>
          <dl class="row">
                <dt class="tit"><?= __('是否可修改'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                         <label for="is_modify1" class="cb-enable  <?=($data['is_modify']['config_value'] ? 'selected' : '')?>"><?= __('开启'); ?></label>
                         <label for="is_modify0" class="cb-disable <?=(!$data['is_modify']['config_value'] ? 'selected' : '')?>"><?= __('关闭'); ?></label>
                         <input id="is_modify1" name="domain[is_modify]"  <?=($data['is_modify']['config_value'] ? 'checked' : '')?>  value="1" type="radio">
                         <input id="is_modify0" name="domain[is_modify]"  <?=(!$data['is_modify']['config_value'] ? 'checked' : '')?>  value="0" type="radio">
                       
                    </div>
                    <p class="notic"><?= __('不可修改时店主填写提交后将不可改动'); ?></p>
                </dd>
            </dl>
         <dl class="row">
                <dt class="tit">
                    <label for="domain_modify_frequency"><?= __('修改次数'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="domain_modify_frequency" name="domain[domain_modify_frequency]" value="<?=($data['domain_modify_frequency']['config_value'])?>" class="ui-input w400" type="text"/>

                    <p class="notic"><?= __('可修改时达到设定的次数后将不能再改动'); ?></p>
                </dd>
            </dl>
               <dl class="row">
                <dt class="tit">
                    <label for="retain_domain"><?= __('保留域名'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="retain_domain" name="domain[retain_domain]" value="<?=($data['retain_domain']['config_value'])?>" class="ui-input w400" type="text"/>

                    <p class="notic"><?= __('保留的二级域名，多个保留域名之间请用'); ?>","<?= __('隔开'); ?></p>
                </dd>
            </dl>
               <dl class="row">
                <dt class="tit">
                    <label for="domain_length"><?= __('长度限制'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="domain_length" name="domain[domain_length]" value="<?=($data['domain_length']['config_value'])?>" class="ui-input w400" type="text"/>

                    <p class="notic"><?= __('如'); ?>"3-12"<?= __('，代表注册的域名长度限制在'); ?>3<?= __('到'); ?>12<?= __('个字符之间'); ?></p>
                </dd>
            </dl>
      
          
            
          
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>
      <script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>