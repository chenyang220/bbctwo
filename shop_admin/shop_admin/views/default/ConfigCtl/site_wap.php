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

    <form method="post" enctype="multipart/form-data" id="shop-setting-form-wap" name="form1">
        <input type="hidden" name="config_type[]" value="site"/>
        <input type="hidden" name="config_type[]" value="photo"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('站点状态'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="site_status1" name="site[site_status_wap]"  value="1" type="radio" <?=($data['site_status_wap']['config_value']==1 ? 'checked' : '')?>>
						<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['site_status_wap']['config_value']==1 ? 'selected' : '')?> " for="site_status1"><?= __('开启'); ?></label>

                        <input id="site_status0" name="site[site_status_wap]"  value="0" type="radio" <?=($data['site_status_wap']['config_value']==0 ? 'checked' : '')?>>
						<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['site_status_wap']['config_value']==0 ? 'selected' : '')?>" for="site_status0"><?= __('关闭'); ?></label>
                    </div>
                    <p class="notic"><?= __('可暂时将站点关闭，其他人无法访问，但不影响管理员访问后台'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="closed_reason"><?= __('关闭原因'); ?></label>
                </dt>
                <dd class="opt">
                    <textarea name="site[closed_reason_wap]" class="ui-input w346" id="closed_reason_wap"><?= __($data['closed_reason_wap']['config_value'])?></textarea>

                    <p class="notic"><?= __('当网站处于关闭状态时，关闭原因将显示在前台'); ?></p>
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
</div>

<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<script>
    $(function () {
        function uploadImage() {
            var photo_head_upload = new UploadImage({
                thumbnailWidth: 64,
                thumbnailHeight: 64,
                imageContainer: "#photo_head_image_wap",
                uploadButton: "#photo_head_upload_wap",
                inputHidden: "#photo_head_logo_wap"
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

            $("#photo_head_upload_wap").on("click", function () {

                if (this.id == "photo_head_upload_wap") {
                    $imagePreview = $("#photo_head_image_wap");
                    $imageInput = $("#photo_head_logo_wap");
                    imageWidth = 64, imageHeight = 64;
                }
                $.dialog({
                    title: '<?= __('图片裁剪'); ?>',
                    content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                    data: {SHOP_URL: SHOP_URL, width: imageWidth, height: imageHeight, callback: callback},
                    width: "800px",
                    lock: true
                });
            });

            function callback(respone, api) {
                $imagePreview.attr("src", respone.url);
                $imageInput.attr("value", respone.url);
                api.close();
            }
        }


    })
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>