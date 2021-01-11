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
                <?php
                    $image = Yf_Registry::get('url').'/../shop_admin/static/default/images/image.png';
                    $mobile_wap = $data['mobile_wap']['config_value'] ? $data['mobile_wap']['config_value']:$image;
                    $mobile_app = $data['mobile_app']['config_value'] ? $data['mobile_app']['config_value']:$image;
                    $mobile_wx_code = $data['mobile_wx_code']['config_value'] ? $data['mobile_wx_code']['config_value'] : $image;
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

    <form id="qrcode-setting-form" method="post" enctype="multipart/form-data" name="settingForm">
        <input type="hidden" name="config_type[]" value="qrcode"/>
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label><?= __('Wap端二维码.'); ?></label>
                </dt>
                <dd class="opt">
                    <img id="mobile_wap" name="mobile_wap" alt="<?= __('选择图片'); ?>" src="<?=$mobile_wap?>">
                    <div class="image-line upload-image" id="mobile_wap_upload"><?= __('上传图片'); ?></div>
                    <input id="mobile_wap_code_img" name="qrcode[mobile_wap]" value="<?=$mobile_wap?>" class="ui-input w400" type="hidden">
                    <div class="notic"><?= __('选择上传'); ?>...<?= __('选择文件建议大小'); ?>90px*90px</div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('App下载二维码'); ?></label>
                </dt>
                <dd class="opt">
                    <img id="mobile_app" name="mobile_app" alt="<?= __('选择图片'); ?>" src="<?=$mobile_app?>">
                    <div class="image-line upload-image" id="mobile_app_upload"><?= __('上传图片'); ?></div>
                    <input id="mobile_app_code_img" name="qrcode[mobile_app]" value="<?=$mobile_app?>" class="ui-input w400" type="hidden">
                    <div class="notic"><?= __('选择上传'); ?>...<?= __('选择文件建议大小'); ?>90px*90px</div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('微信二维码'); ?></label>
                </dt>
                <dd class="opt">
                    <img id="mobile_wx_code" name="mobile_wx_code" alt="<?= __('选择图片'); ?>" src="<?=$mobile_wx_code?>">
                    <div class="image-line upload-image" id="mobile_wx_code_upload"><?= __('上传图片'); ?></div>
                    <input id="mobile_wx_code_code_img" name="qrcode[mobile_wx_code]" value="<?=$mobile_wx_code?>" class="ui-input w400" type="hidden">
                    <div class="notic"><?= __('选择上传'); ?>...<?= __('选择文件建议大小'); ?>90px*90px</div>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<script type="text/javascript">
    var mobile_wap_upload = new UploadImage({
            thumbnailWidth: 90,
            thumbnailHeight: 90,
            imageContainer: '#mobile_wap',
            uploadButton: '#mobile_wap_upload',
            inputHidden: '#mobile_wap_code_img'
        });

    var mobile_app_upload =  new UploadImage({
            thumbnailWidth: 90,
            thumbnailHeight: 90,
            imageContainer: '#mobile_app',
            uploadButton: '#mobile_app_upload',
            inputHidden: '#mobile_app_code_img'
        });
    var mobile_wx_code_upload =  new UploadImage({
            thumbnailWidth: 90,
            thumbnailHeight: 90,
            imageContainer: '#mobile_wx_code',
            uploadButton: '#mobile_wx_code_upload',
            inputHidden: '#mobile_wx_code_code_img'
        });

</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>