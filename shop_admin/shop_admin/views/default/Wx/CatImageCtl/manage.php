<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<form method="post" id="shop_api-setting-form" name="settingForm" class="nice-validator n-yellow" novalidate="novalidate">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="site_name"><?= __('分类'); ?></label>
            </dt>
            <dd class="opt">
                <span id="link_category"></span>
                <p class="notic"><?= __('点击广告后查找该分类下的商品'); ?></p>
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label for="site_name"><?= __('展示图片'); ?></label>
            </dt>
            <dd class="opt">
                <img id="textfield1" name="textfield1" alt="<?= __('选择图片'); ?>" src="http://127.0.0.1/yf_shop_admin/shop_admin/static/default/images/default_user_portrait.gif" width="90px" height="90px">

                <div class="image-line upload-image" id="button1"><?= __('上传图片'); ?></div>

                <input id="wx_cat_image" name="" value="" class="ui-input w400" type="hidden">
                <div class="notic"><?= __('展示图片，建议大小'); ?>90x90<?= __('像素'); ?>PNG<?= __('图片。'); ?></div>
            </dd>
        </dl>
    </div>
</form>

<?php
include TPL_PATH . '/' . 'footer.php';
?>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
<script>
    $(function() {
        api = frameElement.api, data = api.data, oper = data.oper, callback = data.callback; var cat_id;

        if ( oper == 'edit' ) {
            //init
            var rowData = data.rowData;

            cat_id = rowData.cat_id;

            $('#textfield1').prop('src', rowData.wx_cat_image);
            $('#wx_cat_image').val(rowData.wx_cat_image);

        }

        api.button({
            id: "confirm", name: '<?= __('确定'); ?>', focus: !0, callback: function () {
                postData();
                return false;
            }
        }, {id: "cancel", name: '<?= __('取消'); ?>'});

        function postData() {

            var param = {
                cat_id: linkCatCombo.getValue(),
                wx_cat_image: $('#wx_cat_image').val(),
            };

            if ( oper == 'edit' ) {
                param.wx_cat_image_id = data.rowData.wx_cat_image_id;
            }

            Public.ajaxPost(SITE_URL + '?ctl=Wx_CatImage&met=' + oper + 'CatImage&typ=json', {
                param: param
            }, function (data) {
                if (data.status == 200) {
                    typeof callback == 'function' && callback(data.data, oper, window);
                    return true;
                } else {
                    Public.tips({type: 1, content: data.msg});
                }
            })
        }

        var linkCatCombo = $("#link_category").combo({
            data: SITE_URL + "?ctl=Category&met=lists&typ=json&type_number=goods_cat&is_delete=2",
            value: "cat_id",
            text: "cat_name",
            width: 210,
            ajaxOptions: {
                formatData: function (e)
                {
                    var rowData_1 = new Array(), rowData = e.data.items;
                    for(var i=0; i<rowData.length; i++) {
                        if (rowData[i].level == 3) {
                            rowData_1.push(rowData[i]);
                        }
                    }
                    return rowData_1;
                }
            },
            defaultSelected: cat_id ? ['cat_id', cat_id] : 0
        }).getCombo();

         new UploadImage({
            thumbnailWidth: 90,
            thumbnailHeight: 90,
            imageContainer: '#textfield1',
            uploadButton: '#button1',
            inputHidden: '#wx_cat_image'
        });

    });
</script>
