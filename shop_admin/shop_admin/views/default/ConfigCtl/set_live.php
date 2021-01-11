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
        <input type="hidden" name="config_type[]" value="set_live"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('直播申请'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="live_is_open1" name="set_live[live_is_open]" value="1" type="radio" <?=($data['live_is_open']['config_value']==1 ? 'checked' : '')?> >
						<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['live_is_open']['config_value']==1 ? 'selected' : '')?> " for="live_is_open1"><?= __('开启'); ?></label>

                        <input id="live_is_open0" name="set_live[live_is_open]" value="0" type="radio"  <?=($data['live_is_open']['config_value']==0 ? 'checked' : '')?> >
						<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['live_is_open']['config_value']==0 ? 'selected' : '')?>" for="live_is_open0"><?= __('关闭'); ?></label>
                    </div>

                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit">
                    <label for="live_image"><?= __('直播轮播图1'); ?></label>
                </dt>
                <dd class="opt show">
                    <?php if($data['live_logo1']['config_value']){ ?>
                        <img src="<?= ($data['live_logo1']['config_value']) ?>" id="live_image1" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <?php }else{ ?>
                        <img src="../shop_admin/static/common/images/image.png" id="live_image1" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <?php } ?>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="live_logo1" value="<?= ($data['live_logo1']['config_value']) ?>" class="ui-input w400 img-path" type="hidden" name="set_live[live_logo1]"/>
                    <p class="notic">建议尺寸：1900*500px</p>
                    <div class="image-line" id="live_upload1"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" name="set_live[live_url1]" value="<?= ($data['live_url1']['config_value']) ?>">
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit">
                    <label for="live_image"><?= __('直播轮播图2'); ?></label>
                </dt>
                <dd class="opt show">
                    <?php if ($data['live_logo2']['config_value']) { ?>
                        <img src="<?= ($data['live_logo2']['config_value']) ?>" id="live_image2" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <?php } else { ?>
                        <img src="../shop_admin/static/common/images/image.png" id="live_image2" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <?php } ?>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="live_logo2" value="<?= ($data['live_logo2']['config_value']) ?>" class="ui-input w400 img-path" type="hidden" name="set_live[live_logo2]"/>
                    <p class="notic">建议尺寸：1900*500px</p>
                    <div class="image-line" id="live_upload2"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" name="set_live[live_url2]" value="<?= ($data['live_url2']['config_value']) ?>">
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit">
                    <label for="live_image"><?= __('直播轮播图3'); ?></label>
                </dt>
                <dd class="opt show">
                    <?php if ($data['live_logo3']['config_value']) { ?>
                        <img src="<?= ($data['live_logo3']['config_value']) ?>" id="live_image3" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <?php } else { ?>
                        <img src="../shop_admin/static/common/images/image.png" id="live_image3" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <?php } ?>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="live_logo3" value="<?= ($data['live_logo3']['config_value']) ?>" class="ui-input w400 img-path" type="hidden" name="set_live[live_logo3]"/>
                    <p class="notic">建议尺寸：1900*500px</p>
                    <div class="image-line" id="live_upload3"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" name="set_live[live_url3]" value="<?= ($data['live_url3']['config_value']) ?>">
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>

<script>
	
    //图片上传
    $(function () {
        logo_upload = new UploadImage({
            thumbnailWidth: 1900,
            thumbnailHeight: 500,
            imageContainer: '#live_image1',
            uploadButton: '#live_upload1',
            inputHidden: '#live_logo1'
        });
        logo_upload2 = new UploadImage({
            thumbnailWidth: 1900,
            thumbnailHeight: 500,
            imageContainer: '#live_image2',
            uploadButton: '#live_upload2',
            inputHidden: '#live_logo2'
        });
        logo_upload3 = new UploadImage({
            thumbnailWidth: 1900,
            thumbnailHeight: 500,
            imageContainer: '#live_image3',
            uploadButton: '#live_upload3',
            inputHidden: '#live_logo3'
        });

        //删除图片
        $(".del-img").click(function(){
            $(this).prev().attr('src','../shop_admin/static/common/images/image.png');
            $(this).next().val('');
        })
    })
</script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/template.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>