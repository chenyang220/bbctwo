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

    <form method="post" enctype="multipart/form-data" id="my_qrcode_img_form" name="form1">
        <input type="hidden" name="config_type[]" value="mycodeDepict"/>
        <input type="hidden" name="config_type[]" value="mycodeBgimg"/>
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="closed_reason"><?= __('描述语'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="myqrcode_describe" name="mycodeDepict[myqrcode_describe]" value="<?=($data['myqrcode_describe']['config_value']?:0);?>" class="ui-input w400" type="text"/>
                    <p class="notic"><?= __('二维码页面展示该描述语！'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('二维码页面背景'); ?></label>

                </dt>
                <dd class="opt" style="width: 30%;">

                    <img id="myqrcode_bgimg" name="mycodeBgimg[myqrcode_bgimg]" alt="<?= __('选择图片'); ?>" src="<?= ($data['myqrcode_bgimg']['config_value']) ?>" width="187px" height="375px"/>

                    <div class="image-line upload-image" id="upload_my_code_img"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                    <input id="mycode_img_val" name="mycodeBgimg[myqrcode_bgimg]" value="<?= ($data['myqrcode_bgimg']['config_value']) ?>" class="ui-input w400" type="hidden"/>
                    <p class="notic">(APP/WAP)端，我的二维码页面背景图片! 375px*812px</p>
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
                thumbnailWidth: 375,
                thumbnailHeight:812,
                imageContainer: "#myqrcode_bgimg",
                uploadButton: "#upload_my_code_img",
                inputHidden: "#mycode_img_val"
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
            $("#upload_my_code_img").on("click", function () {
                if (this.id == "upload_my_code_img") {
                    $imagePreview = $("#myqrcode_bgimg");
                    $imageInput = $("#mycode_img_val");
                    imageWidth = 375, imageHeight = 812;
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