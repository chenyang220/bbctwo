<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();
?>
    <link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
    <link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    
    <style>
        .webuploader-pick {
            padding: 1px;
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
                    foreach ($menus['brother_menu'] as $key => $val) {
                        if (in_array($val['rights_id'], $admin_rights) || $val['rights_id'] == 0) {
                            ?>
                            <li><a <?php if ($val['menu_name'] == '默认图片') { ?> class="current" nctype="acquiesce"<?php } else { ?> nctype="font" <?php } ?> href="javascript:void(0);"><span><?= __($val['menu_name']); ?></span></a></li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <!-- <?= __('操作说明'); ?> -->
        <p class="warn_xiaoma"><span></span><em></em></p>
        <div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
                <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
                <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
            <ul>
                <?= __($menus['this_menu']['menu_url_note']); ?>
            </ul>
        </div>
        <form style="display: none;" method="post" enctype="multipart/form-data" id="acquiesce-setting-form" name="form_acquiesce">
            <input type="hidden" name="config_type[]" value="photo"/>
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label><?= __('默认商品图片'); ?></label>
                    </dt>
                    <dd class="opt">
                        <img id="photo_goods_image" name="photo[photo_goods_logo]" alt="<?= __('选择图片'); ?>" src="<?= ($data['photo_goods_logo']['config_value']) ?>" width="300px" height="300px"/>
                        
                        <div class="image-line upload-image" id="photo_goods_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>
                        
                        <input id="photo_goods_logo" name="photo[photo_goods_logo]" value="<?= ($data['photo_goods_logo']['config_value']) ?>" class="ui-input w400" type="hidden"/>
                        <div class="notic"><?= __('默认商品图片'); ?>,<?= __('最佳显示尺寸为'); ?>300*300<?= __('像素'); ?></div>
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
                        <label><?= __('默认移动端店铺头像'); ?></label>
                    
                    </dt>
                    <dd class="opt" style="width: 30%;">
                        
                        <img id="photo_head_image_wap" name="photo[photo_shop_head_logo_wap]" alt="<?= __('选择图片'); ?>" src="<?= ($data['photo_shop_head_logo_wap']['config_value']) ?>" width="180px" height="80px"/>
                        
                        <div class="image-line upload-image" id="photo_head_upload_wap"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>
                        
                        <input id="photo_head_logo_wap" name="photo[photo_shop_head_logo_wap]" value="<?= ($data['photo_shop_head_logo_wap']['config_value']) ?>" class="ui-input w400" type="hidden"/>
                        <p class="notic"><?= __('默认'); ?>(APP/WAP)<?= __('店铺头像，最佳显示尺寸为'); ?>64*64<?= __('像素，请根据背景色选择使用图片色彩'); ?></p>
                    </dd>
                </dl>
                <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
            </div>
        </form>
        <form method="post" style="display: none;" enctype="multipart/form-data" id="font-setting-form" name="form_font">
            <input type="hidden" name="config_type[]" value="photo"/>
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label><?= __('已安装如下字体'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="photo_font" name="photo[photo_font]" value="<?= ($data['photo_font']['config_value']) ?>" class="ui-input w400" type="text" disabled/>
                        
                        <p class="notic"></p>
                    </dd>
                </dl>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/controllers/config.js" charset="utf-8"></script>
    <script>
        $(function () {
            $(".tab-base").find("a").bind("click", function () {
                
                $(".tab-base").find("a").removeClass("current");
                $(this).addClass("current");
                $("form").css("display", "none");
                $("form[name=\"form_" + $(this).attr("nctype") + "\"]").css("display", "");
            });
            $("form").css("display", "none");
            $("form[name=\"form_acquiesce\"]").css("display", "");
            
            //图片上传
            function uploadImage() {
                var photo_goods_logo = new UploadImage({
                    thumbnailWidth: 300,
                    thumbnailHeight: 300,
                    imageContainer: "#photo_goods_image",
                    uploadButton: "#photo_goods_upload",
                    inputHidden: "#photo_goods_logo"
                });
                
                var photo_shop_upload = new UploadImage({
                    thumbnailWidth: 200,
                    thumbnailHeight: 60,
                    imageContainer: "#photo_shop_image",
                    uploadButton: "#photo_shop_upload",
                    inputHidden: "#photo_shop_logo"
                });
                
                var photo_head_upload = new UploadImage({
                    thumbnailWidth: 180,
                    thumbnailHeight: 80,
                    imageContainer: "#photo_head_image",
                    uploadButton: "#photo_head_upload",
                    inputHidden: "#photo_head_logo"
                });
                var photo_head_upload = new UploadImage({
                    thumbnailWidth: 64,
                    thumbnailHeight: 64,
                    imageContainer: "#photo_head_image_wap",
                    uploadButton: "#photo_head_upload_wap",
                    inputHidden: "#photo_head_logo_wap"
                });
                var photo_user_upload = new UploadImage({
                    thumbnailWidth: 120,
                    thumbnailHeight: 120,
                    imageContainer: "#photo_user_image",
                    uploadButton: "#photo_user_upload",
                    inputHidden: "#photo_user_logo"
                });
            }
            
            var agent = navigator.userAgent.toLowerCase();
            
            if (agent.indexOf("msie") > -1 && (version = agent.match(/msie [\d]/), (version == "msie 8" || version == "msie 9"))) {
                uploadImage();
            } else {
                cropperImage();
            }
            
            //图片裁剪
            
            function cropperImage() {
                var $imagePreview, $imageInput, imageWidth, imageHeight;
                
                $("#photo_goods_upload, #photo_shop_upload, #photo_head_upload,#photo_user_upload,#photo_head_upload_wap").on("click", function () {
                    
                    if (this.id == "photo_goods_upload") {
                        $imagePreview = $("#photo_goods_image");
                        $imageInput = $("#photo_goods_logo");
                        imageWidth = 300, imageHeight = 300;
                    } else if (this.id == "photo_shop_upload") {
                        $imagePreview = $("#photo_shop_image");
                        $imageInput = $("#photo_shop_logo");
                        imageWidth = 200, imageHeight = 60;
                    } else if (this.id == "photo_head_upload") {
                        $imagePreview = $("#photo_head_image");
                        $imageInput = $("#photo_head_logo");
                        imageWidth = 180, imageHeight = 80;
                    } else if (this.id == "photo_head_upload_wap") {
                        $imagePreview = $("#photo_head_image_wap");
                        $imageInput = $("#photo_head_logo_wap");
                        imageWidth = 64, imageHeight = 64;
                    } else {
                        $imagePreview = $("#photo_user_image");
                        $imageInput = $("#photo_user_logo");
                        imageWidth = 120, imageHeight = 120;
                    }
                    $.dialog({
                        title: '<?= __('图片裁剪'); ?>',
                        content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                        data: {SHOP_URL: SHOP_URL, width: imageWidth, height: imageHeight, callback: callback},    // <?= __('需要截取图片的宽高比例'); ?>
                        width: "800px",
                        height: $(window).height() * 0.9,
                        lock: true
                    });
                });
                
                function callback(respone, api) {
                    $imagePreview.attr("src", respone.url);
                    $imageInput.attr("value", respone.url);
                    api.close();
                }
            }
        });
    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>