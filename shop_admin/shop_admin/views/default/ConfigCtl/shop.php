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

    <style>
        .webuploader-pick{ padding:1px; }

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

        <form method="post" enctype="multipart/form-data" id="setting-setting-form" name="form">
            <input type="hidden" name="config_type[]" value="setting"/>
            <input type="hidden" name="config_type[]" value="photo"/>

            <div class="ncap-form-default">

                <dl class="row">
                    <dt class="tit">
                        <label><?= __('网站'); ?>Logo</label>
                    </dt>
                    <dd class="opt">
                        <img id="setting_logo_image" name="setting[setting_logo]" alt="<?= __('选择图片'); ?>" src="<?=($data['setting_logo']['config_value'])?>" width="240px" height="60px" />

                        <div class="image-line upload-image"  id="setting_logo_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                        <input id="setting_logo"  name="setting[setting_logo]" value="<?=($data['setting_logo']['config_value'])?>" class="ui-input w400" type="hidden"/>
                        <div class="notic"><?= __('默认网站'); ?>LOGO,<?= __('通用头部显示，最佳显示尺寸为'); ?>240*60<?= __('像素'); ?></div>
                    </dd>
                </dl>


                <dl class="row">
                    <dt class="tit">
                        <label><?= __('会员中心'); ?>Logo</label>

                    </dt>
                    <dd class="opt">

                        <img id="setting_buyer_image" name="setting[setting_buyer_logo]" alt="<?= __('选择图片'); ?>" src="<?=($data['setting_buyer_logo']['config_value'])?>" width="150px" height="40px" />

                        <div class="image-line upload-image" id="buyer_logo_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                        <input id="setting_buyer_logo" name="setting[setting_buyer_logo]" value="<?=($data['setting_buyer_logo']['config_value'])?>" class="ui-input w400" type="hidden"/>
                        <p class="notic"><?= __('网站小尺寸'); ?>LOGO<?= __('，会员个人主页显示，最佳显示尺寸为'); ?>150*40<?= __('像素'); ?></p>
                    </dd>
                </dl>


                <dl class="row">
                    <dt class="tit">
                        <label><?= __('商家中心'); ?>Logo</label>

                    </dt>
                    <dd class="opt" style="width: 30%;">

                        <img id="setting_seller_image" name="setting[setting_seller_logo]" alt="<?= __('选择图片'); ?>" src="<?=($data['setting_seller_logo']['config_value'])?>" width="160px" height="60px" />

                        <div class="image-line upload-image"  id="seller_logo_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                        <input id="setting_seller_logo" name="setting[setting_seller_logo]" value="<?=($data['setting_seller_logo']['config_value'])?>" class="ui-input w400" type="hidden"/>
                        <p class="notic"><?= __('商家中心'); ?>LOGO<?= __('，最佳显示尺寸为'); ?>160*60<?= __('像素，请根据背景色选择使用图片色彩'); ?></p>
                    </dd>
                </dl>

                <dl class="row">
                    <dt class="tit">
                        <label><?= __('供应商中心'); ?>Logo</label>

                    </dt>
                    <dd class="opt" style="width: 30%;">

                        <img id="setting_supplier_image" name="setting[setting_supplier_logo]" alt="<?= __('选择图片'); ?>" src="<?=($data['setting_supplier_logo']['config_value'])?>" width="160px" height="60px" />

                        <div class="image-line upload-image"  id="supplier_logo_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                        <input id="setting_supplier_logo" name="setting[setting_supplier_logo]" value="<?=($data['setting_supplier_logo']['config_value'])?>" class="ui-input w400" type="hidden"/>
                        <p class="notic"><?= __('供应商中心'); ?>LOGO<?= __('，最佳显示尺寸为'); ?>160*60<?= __('像素，请根据背景色选择使用图片色彩'); ?></p>
                    </dd>
                </dl>

                <dl class="row">
                    <dt class="tit">
                        <label><?= __('默认'); ?>PC<?= __('端店铺头像'); ?></label>

                    </dt>
                    <dd class="opt" style="width: 30%;">

                        <img id="photo_head_image" name="photo[photo_shop_head_logo]" alt="<?= __('选择图片'); ?>" src="<?= ($data['photo_shop_head_logo']['config_value']) ?>" width="180px" height="80px"/>

                        <div class="image-line upload-image" id="photo_head_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                        <input id="photo_head_logo" name="photo[photo_shop_head_logo]" value="<?= ($data['photo_shop_head_logo']['config_value']) ?>" class="ui-input w400" type="hidden"/>
                        <p class="notic"><?= __('默认'); ?>PC<?= __('店铺头像，最佳显示尺寸为'); ?>180*80<?= __('像素，请根据背景色选择使用图片色彩'); ?></p>
                    </dd>
                </dl>

                <dl class="row">
                    <dt class="tit">
                        <label><?= __('平台客服联系电话'); ?></label>

                    </dt>
                    <dd class="opt">
                        <input id="setting_phone" name="setting[setting_phone]" value="<?=($data['setting_phone']['config_value'])?>" class="ui-input w400" type="text"/>
                        <p class="notic"><?= __('商家中心左下侧显示，方便商家遇到问题时咨询，多个请用半角逗号'); ?> "," <?= __('隔开'); ?></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><?= __('电子邮件'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="setting_email" name="setting[setting_email]" value="<?=($data['setting_email']['config_value'])?>" class="ui-input w400" type="text"/>

                        <p class="notic"> <?= __('商家中心左下侧显示，方便商家遇到问题时咨询'); ?></p>
                    </dd>
                </dl>

                <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
    <script>
        $(function(){

            function uploadImage() {
                var setting_logo_upload = new UploadImage({
                    thumbnailWidth: 240,
                    thumbnailHeight: 60,
                    imageContainer: '#setting_logo_image',
                    uploadButton: '#setting_logo_upload',
                    inputHidden: '#setting_logo'
                });

                var seller_logo_upload = new UploadImage({
                    thumbnailWidth: 160,
                    thumbnailHeight: 60,
                    imageContainer: '#setting_seller_image',
                    uploadButton: '#seller_logo_upload',
                    inputHidden: '#setting_seller_logo'
                });

                var supplier_logo_upload = new UploadImage({
                    thumbnailWidth: 160,
                    thumbnailHeight: 60,
                    imageContainer: '#setting_supplier_image',
                    uploadButton: '#supplier_logo_upload',
                    inputHidden: '#setting_supplier_logo'
                });

                var buyer_logo_upload = new UploadImage({
                    thumbnailWidth: 150,
                    thumbnailHeight: 40,
                    imageContainer: '#setting_buyer_image',
                    uploadButton: '#buyer_logo_upload',
                    inputHidden: '#setting_buyer_logo'
                });

                var photo_head_upload = new UploadImage({
                    thumbnailWidth: 180,
                    thumbnailHeight: 80,
                    imageContainer: "#photo_head_image",
                    uploadButton: "#photo_head_upload",
                    inputHidden: "#photo_head_logo"
                });
            }


            var agent = navigator.userAgent.toLowerCase();

            if ( agent.indexOf("msie") > -1 && (version = agent.match(/msie [\d]/), ( version == "msie 8" || version == "msie 9" )) ) {
                uploadImage();
            } else {
                cropperImage();
            }

            function cropperImage() {
                var $imagePreview, $imageInput, imageWidth, imageHeight;

                $('#setting_logo_upload, #seller_logo_upload, #supplier_logo_upload,#buyer_logo_upload,#photo_head_upload').on('click', function () {

                    if ( this.id == 'setting_logo_upload' ) {
                        $imagePreview = $('#setting_logo_image');
                        $imageInput = $('#setting_logo');
                        imageWidth = 240, imageHeight = 60;
                    } else if ( this.id == 'seller_logo_upload' ) {
                        $imagePreview = $('#setting_seller_image');
                        $imageInput = $('#setting_seller_logo');
                        imageWidth = 160, imageHeight = 60;
                    } else if ( this.id == 'supplier_logo_upload' ) {
                        $imagePreview = $('#setting_supplier_image');
                        $imageInput = $('#setting_supplier_logo');
                        imageWidth = 160, imageHeight = 60;
                    }else if (this.id == "photo_head_upload") {
                        $imagePreview = $("#photo_head_image");
                        $imageInput = $("#photo_head_logo");
                        imageWidth = 180, imageHeight = 80;
                    } else {
                        $imagePreview = $('#setting_buyer_image');
                        $imageInput = $('#setting_buyer_logo');
                        imageWidth = 150, imageHeight = 40;
                    }
                    $.dialog({
                        title: '<?= __('图片裁剪'); ?>',
                        content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                        data: { SHOP_URL: SHOP_URL, width: imageWidth, height: imageHeight, callback: callback },
                        width: '800px',
                        lock: true
                    })
                });

                function callback ( respone , api ) {
                    $imagePreview.attr('src', respone.url);
                    $imageInput.attr('value', respone.url);
                    api.close();
                }
            }

        })
    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>