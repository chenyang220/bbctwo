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
    <link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
    <style>
        body {
            background: #fff;
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
                        <li><a <?php if (!array_diff($menus['this_menu'], $val)) { ?> class="current"<?php } ?> href="<?= Yf_Registry::get('url') ?>?ctl=<?= $val['menu_url_ctl'] ?>&met=<?= $val['menu_url_met'] ?><?php if ($val['menu_url_parem']) { ?>&<?= $val['menu_url_parem'] ?><?php } ?>"><span><?= __($val['menu_name']); ?></span></a></li>
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
    <form method="post" name="manage-form" id="manage-form" action="">
        <input type="hidden" name="form_submit" value="ok">
        <input type="hidden" id="column_id">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="back_image"><?= __('背景图'); ?></label>
                </dt>
                <dd class="opt show">
                    <img src="../shop_admin/static/common/images/image.png" id="back_image" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="back_logo" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：640*1000px</p>
                    <div class="image-line" id="back_upload"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <!--                <input type="button" value="--><? //= __('删除广告位'); ?><!--" class="del-image">-->
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit">
                    <label for="column_image"><?= __('幻灯片'); ?></label>
                </dt>
                <dd class="opt show">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image0" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo0" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">640*340px</p>
                    <div class="image-line" id="column_upload0"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url0">
                    <!--                <input type="button" value="--><? //= __('删除广告位'); ?><!--" class="del-image">-->
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit"></dt>
                <dd class="opt">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image1" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo1" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：640*340px</p>
                    <div class="image-line" id="column_upload1"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url1">
                    <!--                <input type="button" value="--><? //= __('删除广告位'); ?><!--" class="del-image">-->
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit"></dt>
                <dd class="opt">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image2" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo2" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：640*340px</p>
                    <div class="image-line" id="column_upload2"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url2">
                    <!--                <input type="button" value="--><? //= __('删除广告位'); ?><!--" class="del-image">-->
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit"></dt>
                <dd class="opt">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image3" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo3" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：640*340px</p>
                    <div class="image-line" id="column_upload3"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url3">
                    <!--                <input type="button" value="--><? //= __('删除广告位'); ?><!--" class="del-image">-->
                </dd>
            </dl>
            <dl class="row banner_image">
                <dt class="tit"></dt>
                <dd class="opt">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image4" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo4" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：640*340px</p>
                    <div class="image-line" id="column_upload4"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url4">
                    <!--                <input type="button" value="--><? //= __('删除广告位'); ?><!--" class="del-image">-->
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('版式设置'); ?></label>
                </dt>
                <dd class="opt">
                    <input type="radio" name="image" value="1">
                    <ul class="style-flex style-flex1">
                        <li></li>
                    </ul>
                    <input type="radio" name="image" value="2">
                    <ul class="style-flex style-flex2">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
<!--                    <input type="button" value="--><?//= __('选择版式'); ?><!--" id="choose">-->
                    <ul class="gpc-goods-intro-lists gpc-goods-intro-lists2" id="setImage">
                    </ul>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('商品设置'); ?></label>
                </dt>
                <dd class="opt">
                    <input type="button" value="<?= __('推荐商品'); ?>" id="add-goods">
                    <ul class="gpc-goods-intro-lists" id="goods-info"></ul>
                    <!--  <table id="goods-info"> -->
                    <!--          <tr>
                            <th><?= __('商品名称'); ?></th>
                            <th><?= __('商品图片'); ?></th>
                            <th><?= __('商品价格'); ?></th>
                            <th><?= __('操作'); ?></th>
                        </tr> -->
                    <!--                     <div></div> -->
                    <!-- </table> -->
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>
<script>
    //图片上传
    $(function () {
        back_upload = new UploadImage({
            thumbnailWidth: 640,
            thumbnailHeight: 1000,
            imageContainer: '#back_image',
            uploadButton: '#back_upload',
            inputHidden: '#back_logo'
        });
        logo_upload = new UploadImage({
            thumbnailWidth: 640,
            thumbnailHeight: 340,
            imageContainer: '#column_image0',
            uploadButton: '#column_upload0',
            inputHidden: '#column_logo0'
        });
        logo_upload1 = new UploadImage({
            thumbnailWidth: 640,
            thumbnailHeight: 340,
            imageContainer: '#column_image1',
            uploadButton: '#column_upload1',
            inputHidden: '#column_logo1'
        });
        logo_upload2 = new UploadImage({
            thumbnailWidth: 640,
            thumbnailHeight: 340,
            imageContainer: '#column_image2',
            uploadButton: '#column_upload2',
            inputHidden: '#column_logo2'
        });
        logo_upload3 = new UploadImage({
            thumbnailWidth: 640,
            thumbnailHeight: 340,
            imageContainer: '#column_image3',
            uploadButton: '#column_upload3',
            inputHidden: '#column_logo3'
        });
        logo_upload4 = new UploadImage({
            thumbnailWidth: 640,
            thumbnailHeight: 340,
            imageContainer: '#column_image4',
            uploadButton: '#column_upload4',
            inputHidden: '#column_logo4'
        });
    })
</script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/template.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/controllers/wxspecial/selfSet.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>