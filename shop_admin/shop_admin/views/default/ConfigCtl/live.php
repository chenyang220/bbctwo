<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();


?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
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

    <form method="post" id="live-setting-form" name="settingForm">
        <input type="hidden" name="config_type[]" value="live"/>

        <div class="ncap-form-default">
            <dl class="row banner_image">
                <dt class="tit">
                    <label for="live_image"><?= __('SecretId'); ?></label>
                </dt>
                <dd class="opt show">
                    <input type="text" placeholder="<?= __('请输入腾讯云SecretId'); ?>" class="img-url ui-input w400" name="live[live_secretId]" value="<?= ($data['live_secretId']['config_value']) ?>">
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit">
                    <label for="live_image"><?= __('SecretKey'); ?></label>
                </dt>
                <dd class="opt show">
                    <input type="text" placeholder="<?= __('请输入腾讯云SecretKey'); ?>" class="img-url ui-input w400" name="live[live_secretKey]" value="<?= ($data['live_secretKey']['config_value']) ?>">
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?= $this->view->js_com ?>/template.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>