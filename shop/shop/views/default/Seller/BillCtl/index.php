<?php if (!defined('ROOT_PATH')) exit('No Permission'); ?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet">
<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css" rel="stylesheet">
<script src="<?= $this->view->js_com ?>/webuploader.js"></script>
<script src="<?= $this->view->js_com ?>/upload/upload_image.js"></script>
<script src="<?= $this->view->js_com ?>/upload/upload_video.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.parse.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/upload/addCustomizeButton.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js"></script>

<div class="form-style">
    <input type="hidden" id="bill_id" value="<?php if (!empty($info)) { echo $info['bill_id'];} ?>">
    <dl>
        <dt>海报背景图片：</dt>
        <dd style="position:relative">
            <div style="float: left;margin-left: -2px;"><span class="msg-box" for="imagePath"></span></div>
            <div class="image">
                <img id="goodsImage" height="1226px" width="750px" src="<?php if (!empty($info)) {
                    echo $info['bill_image'];
                } ?>"/>
                <input id="imagePath" name="imagePath" type="hidden" value="<?php if (!empty($info)) {
                    echo $info['bill_image'];
                } ?>"/>
            </div>
            <p class="hint">
                支持jpg、gif、png格式上传，建议使用，
                <span class="red">尺寸750*1226像素</span>
            </p>
            <div id="uploadButton" style="width: 81px;height: 28px;float: left;">
                <i class="iconfont icon-tupianshangchuan"></i>
                <?= __('图片上传') ?>
            </div>
        </dd>
    </dl>
    <dl>
        <dt></dt>
        <dd><input type="submit" class="button button_red bbc_seller_submit_btns mt40" value="提交"></dd>
    </dl>
</div>
<script>
    $(function(){
        //图片上传
        var uploadImage = new UploadImage({

            thumbnailWidth: 160,
            thumbnailHeight: 160,
            imageContainer: '#goodsImage',
            uploadButton: '#uploadButton',
            inputHidden: '#imagePath',
            callback: function () {
            }
        });

        $(".bbc_seller_submit_btns").click(function(){
            $.ajax({
                url: SITE_URL + '?ctl=Seller_Bill&met=addOrEditBill&typ=json',
                data: {billImage:$("#imagePath").val()},
                success: function (a) {
                    if (a.status == 200) {
                        Public.tips.success('<?=__('操作成功！')?>');
                    } else {
                        Public.tips.error('<?=__('操作失败！')?>');
                    }
                }
            });
        })

    })
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
?>