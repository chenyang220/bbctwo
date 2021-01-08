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

        <form method="post" enctype="multipart/form-data" id="mall-setting-form" name="form">
            <input type="hidden" name="config_type[]" value="mall"/>
          

            <div class="ncap-form-default">

                <dl class="row">
                    <dt class="tit">
                        <label><?= __('商城'); ?>Logo</label>
                    </dt>
                    <dd class="opt">
                        <img id="mall_logo_image" name="mall[mall_logo]" alt="<?= __('选择图片'); ?>" src="<?=($data['mall_logo']['config_value'])?>" width="95px" height="95px" />

                        <div class="image-line upload-image"  id="mall_logo_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                        <input id="mall_logo"  name="mall[mall_logo]" value="<?=($data['mall_logo']['config_value'])?>" class="ui-input w400" type="hidden"/>
                        <div class="notic">小程序，微商城，wap端，app用户登录页显示此logo，建议尺寸95*95</div>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><?= __('商城'); ?>海报头</label>
                    </dt>
                    <dd class="opt">
                        <img id="mall_poster_image" name="mall[mall_poster]" alt="<?= __('选择图片'); ?>" src="<?=($data['mall_poster']['config_value'])?>" width="596px" height="114px" />
                        <div class="image-line upload-image"  id="mall_poster_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>
                        <input id="mall_poster"  name="mall[mall_poster]" value="<?=($data['mall_poster']['config_value'])?>" class="ui-input w400" type="hidden"/>
                        <div class="notic">小程序，微商城，wap端，app的海报头，不传则海报头为空，建议尺寸596 × 114</div>
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
                var mall_logo_upload = new UploadImage({
                    thumbnailWidth: 95,
                    thumbnailHeight: 95,
                    imageContainer: '#mall_logo_image',
                    uploadButton: '#mall_logo_upload',
                    inputHidden: '#mall_logo'
                });
                var mall_poster_upload = new UploadImage({
                    thumbnailWidth: 596,
                    thumbnailHeight: 114,
                    imageContainer: '#mall_poster_image',
                    uploadButton: '#mall_poster_upload',
                    inputHidden: '#mall_poster'
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

                $('#mall_logo_upload').on('click', function () {

                    if ( this.id == 'mall_logo_upload' ) {
                        $imagePreview = $('#mall_logo_image');
                        $imageInput = $('#mall_logo');
                        imageWidth = 95, imageHeight = 95;
                    } 
                    $.dialog({
                        title: '<?= __('图片裁剪'); ?>',
                        content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                        data: { SHOP_URL: SHOP_URL, width: imageWidth, height: imageHeight, callback: callback },
                        width: '800px',
                        lock: true
                    })
                });

                $('#mall_poster_upload').on('click', function () {
                    if ( this.id == 'mall_poster_upload' ) {
                        $imagePreview = $('#mall_poster_image');
                        $imageInput = $('#mall_poster');
                        imageWidth = 596, imageHeight = 114;
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