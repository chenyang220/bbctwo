<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
    include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/mb.css" rel="stylesheet" type="text/css">
<div class="mb-item-edit-content">
    <form method="post" name="manage-form" id="manage-form" action="">
        <input type="hidden" name="form_submit" value="ok">
        <div class="ncap-form-default">
            <dl class="row max-image">
                <dt class="tit">
                    <label for="column_image"></label>
                </dt>
                <dd class="opt show gpc-upload-input">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image0" alt="<?= __('选择图片'); ?>" class="image-line wp100"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo0" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：640*375px</p>
                    <div class="image-line" id="column_upload0"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url0">
                </dd>
            </dl>
            <dl class="row min-image">
                <dt class="tit"></dt>
                <dd class="opt gpc-upload-input">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image1" alt="<?= __('选择图片'); ?>" class="image-line wp100"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo1" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：300*300px</p>
                    <div class="image-line" id="column_upload1"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url1">
                </dd>
            </dl>
            <dl class="row min-image">
                <dt class="tit"></dt>
                <dd class="opt gpc-upload-input">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image2" alt="<?= __('选择图片'); ?>" class="image-line wp100"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo2" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：300*300px</p>
                    <div class="image-line" id="column_upload2"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url2">
                </dd>
            </dl>
            <dl class="row min-image">
                <dt class="tit"></dt>
                <dd class="opt gpc-upload-input">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image3" alt="<?= __('选择图片'); ?>" class="image-line wp100"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo3" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：300*300px</p>
                    <div class="image-line" id="column_upload3"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url3">
                </dd>
            </dl>
            <dl class="row min-image">
                <dt class="tit"></dt>
                <dd class="opt gpc-upload-input">
                    <img src="../shop_admin/static/common/images/image.png" id="column_image4" alt="<?= __('选择图片'); ?>" class="image-line wp100"/>
                    <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                    <input id="column_logo4" value="" class="ui-input w400 img-path" type="hidden"/>
                    <p class="notic">建议尺寸：300*300px</p>
                    <div class="image-line" id="column_upload4"><?= __('上传图片'); ?>
                        <i class="iconfont icon-tupianshangchuan"></i>
                    </div>
                    <input type="text" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>" class="img-url ui-input w400" id="column_url4">
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
            thumbnailWidth: 750,
            thumbnailHeight: 375,
            imageContainer: '#column_image0',
            uploadButton: '#column_upload0',
            inputHidden: '#column_logo0'
        });
        logo_upload1 = new UploadImage({
            thumbnailWidth: 300,
            thumbnailHeight: 300,
            imageContainer: '#column_image1',
            uploadButton: '#column_upload1',
            inputHidden: '#column_logo1'
        });
        logo_upload2 = new UploadImage({
            thumbnailWidth: 300,
            thumbnailHeight: 300,
            imageContainer: '#column_image2',
            uploadButton: '#column_upload2',
            inputHidden: '#column_logo2'
        });
        logo_upload3 = new UploadImage({
            thumbnailWidth: 300,
            thumbnailHeight: 300,
            imageContainer: '#column_image3',
            uploadButton: '#column_upload3',
            inputHidden: '#column_logo3'
        });
        logo_upload4 = new UploadImage({
            thumbnailWidth: 300,
            thumbnailHeight: 300,
            imageContainer: '#column_image4',
            uploadButton: '#column_upload4',
            inputHidden: '#column_logo4'
        });
    })
</script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/template.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/controllers/special/columnImage.js" charset="utf-8"></script>
<?php include TPL_PATH . '/' . 'footer.php'; ?>

