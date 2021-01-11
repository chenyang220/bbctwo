<?php
include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/mb.css" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<style>
    .upload-image {
        line-height: 24px !important;
        height: 24px !important;
        padding: 0px 6px !important;
    }
    #button1{
        display: inline-block;
        vertical-align: top;
    }
    #button2{
        display: inline-block;
        vertical-align: top;
    }
</style>

<div id="dialog_item_edit_image" style="display: block; z-index: 1100; width: 600px; left: 546.5px; top: 84.5px;" class="ui-draggable">
    <div class="dialog_body" style="position: relative;">
        <div class="dialog_content">
            <div class="s-tips"><i class="fa fa-lightbulb-o"></i><?= __('请按提示尺寸制作上传图片，已达到手机客户端及'); ?>Wap<?= __('手机商城最佳显示效果。'); ?></div>
            <form id="form_image" action="">
                <div class="ncap-form-default">
                    <dl class="row">
                        <dt class="tit"><?= __('品类总称：'); ?></dt>
                        <dd class="opt">
                             <input id="class_name_total" type="text" class="txt w200 marginright marginbot vatop" > 
                        </dd>
                    </dl>
                    <dl class="row">
                        <div class="upload-thumb"><img id="class1_item_image" src="<?= $this->view->img_com ?>/image.png" alt="" style="display: none;"></div>
                    </dl>
                    <dl class="row">
                        <dt class="tit"><?= __('选择要上传的图片：'); ?></dt>
                        <dd class="opt">
                            <div class="input-file-show"><span class="type-file-box">
                                <input type="text" name="textfield1" id="textfield1" class="type-file-text">
                                <div name="button1" id="button1" class=""><?= __('选择上传'); ?></div>
                            </span></div>
                            <p id="dialog_image_desc" class="notic"><?= __('推荐图片尺寸'); ?>640*340</p>
                        </dd>
                    </dl>
                    <dl class="row">
                        <dt class="tit"><?= __('图片名称：'); ?></dt>
                        <dd class="opt">
                             <input id="img1_name" type="text" class="txt w200 marginright marginbot vatop" > 
                        </dd>
                    </dl>
                    <dl class="row">
                        <dt class="tit"><?= __('操作类型：'); ?></dt>
                        <dd class="opt">
                            <select id="class1_item_image_type" name="" class="vatop">
                                <option value="">-<?= __('请选择'); ?>-</option>
                                <option value="keyword"><?= __('关键字'); ?></option>
                                <!--<option value="special"><?= __('专题编号'); ?></option>-->
                                <option value="goods"><?= __('商品编号'); ?></option>
                                <option value="url"><?= __('链接'); ?></option>
                            </select>
                            <input id="class1_item_image_data" type="text" class="txt w200 marginright marginbot vatop">
                            <p id="dialog_item_image_desc" class="notic"><?= __('操作类型一共三种，对应点击以后的操作。'); ?></p>
                        </dd>
                    </dl>
                    <dl class="row">
                        <div class="upload-thumb"><img id="class2_item_image" src="<?= $this->view->img_com ?>/image.png" alt="" style="display: none;"></div>
                    </dl>
                    <dl class="row">
                        <dt class="tit"><?= __('选择要上传的图片：'); ?></dt>
                        <dd class="opt">
                            <div class="input-file-show"><span class="type-file-box">
                                <input type="text" name="textfield2" id="textfield2" class="type-file-text">
                                <div name="button2" id="button2" class=""><?= __('选择上传'); ?></div>
                            </span></div>
                            <p id="dialog_image_desc" class="notic"><?= __('推荐图片尺寸'); ?>640*340</p>
                        </dd>
                    </dl>
                    <dl class="row">
                        <dt class="tit"><?= __('图片名称：'); ?></dt>
                        <dd class="opt">
                             <input id="img2_name" type="text" class="txt w200 marginright marginbot vatop" > 
                        </dd>
                    </dl>
                    <dl class="row">
                        <dt class="tit"><?= __('操作类型：'); ?></dt>
                        <dd class="opt">
                            <select id="class2_item_image_type" name="" class="vatop">
                                <option value="">-<?= __('请选择'); ?>-</option>
                                <option value="keyword"><?= __('关键字'); ?></option>
                                <!--<option value="special"><?= __('专题编号'); ?></option>-->
                                <option value="goods"><?= __('商品编号'); ?></option>
                                <option value="url"><?= __('链接'); ?></option>
                            </select>
                            <input id="class2_item_image_data" type="text" class="txt w200 marginright marginbot vatop">
                            <p id="dialog_item_image_desc" class="notic"><?= __('操作类型一共三种，对应点击以后的操作。'); ?></p>
                        </dd>
                    </dl>
                </div>
            </form>
        </div>
    </div>
    <div style="clear:both;"></div>
</div>

<?php
include TPL_PATH . '/' . 'footer.php';
?>

<script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>

<script>
    $(function() {
        var api = frameElement.api,
            image_name_1 = api.data.image_name_1,
            image_name_2 = api.data.image_name_2,
            dialog_type = api.data.dialog_type,
            callback = api.data.callback,
            layout_data = api.data.layout_data,
            img_data = {};
        $('#class1_image_desc').text('<?= __('推荐图片尺寸'); ?>'+api.data.image_spec);
        image_spec = api.data.image_spec.split('*');
        image_spec_width = image_spec[0];
        image_spec_height = image_spec[1];
        if ( dialog_type ) {
            if ( layout_data ) {
                $("#class_name_total").val(layout_data.class_name_total);
                $("#textfield1").val(layout_data.image_1);
                $('#class1_item_image').show().prop('src', layout_data.image_1);
                $('#class1_item_image_data').val(layout_data.image_data_1);
                $('#class1_item_image_type').val(layout_data.image_type_1);
                $("#img1_name").val(layout_data.image_name_1);

                $("#textfield2").val(layout_data.image_2);
                $('#class2_item_image').show().prop('src', layout_data.image_2);
                $('#class2_item_image_data').val(layout_data.image_data_2);
                $('#class2_item_image_type').val(layout_data.image_type_2);
                $("#img2_name").val(layout_data.image_name_2);
            }
            else {
                $('#class1_item_image').show();
                $('#class2_item_image').show();
            }
        }


        $('#class1_item_image_type').on('change', function () {
            change_image_type_desc($(this).val());
        });
        $('#class2_item_image_type').on('change', function () {
            change_image_type_desc($(this).val());
        });


        function change_image_type_desc(type) {
            var desc_array = {};
            var desc = '<?= __('操作类型一共四种，对应点击以后的操作。'); ?>';
            if (type != '') {
                desc_array['keyword'] = '<?= __('点击图片会直接跳转到产品列表页，并使用此处填写的关键字进行搜索。'); ?>';
                desc_array['special'] = '<?= __('专题编号会跳转到指定的专题，输入框填写专题编号。'); ?>';
                desc_array['goods'] = '<?= __('商品编号会跳转到指定的商品详细页面，输入框填写商品编号。'); ?>';
                desc_array['url'] = '<?= __('链接会跳转到指定链接，输入框填写完整的'); ?>URL<?= __('。'); ?>';
                desc = desc_array[type];
            }
            $('#dialog_item_image_desc').text(desc);
        }

        new UploadImage({
            thumbnailWidth: image_spec_width,
            thumbnailHeight: image_spec_height,
            uploadButton: '#button1',
            inputHidden: '#textfield1',
            imageContainer: '#class1_item_image',
            callback: function(res) {
                 $('#class1_item_image').prop('src',res.url);
                $("#textfield1").val(res.url);
            }
        });
        new UploadImage({
            thumbnailWidth: image_spec_width,
            thumbnailHeight: image_spec_height,
            uploadButton: '#button2',
            inputHidden: '#textfield2',
            imageContainer: '#class2_item_image',
            callback: function(res) {
                $('#class2_item_image').prop('src',res.url);
                $("#textfield2").val(res.url);
            }
        });
        api.button({
            id: "confirm", name: '<?= __('确定'); ?>', focus: !0, callback: function ()
            {
                
                img_data.class_name_total = $('#class_name_total').val();
                img_data.image_1 = $('#textfield1').val() ? $('#textfield1').val() : $('#class1_item_image').prop('src');
                img_data.image_name_1 = $('#img1_name').val();
                img_data.image_type_1 = $('#class1_item_image_type').val();
                img_data.image_data_1 = $('#class1_item_image_data').val();

                img_data.image_2 = $('#textfield2').val() ? $('#textfield2').val() : $('#class2_item_image').prop('src');
                img_data.image_name_2 = $('#img2_name').val();
                img_data.image_type_2 = $('#class2_item_image_type').val();
                img_data.image_data_2 = $('#class2_item_image_data').val();
                callback(img_data);
            }
        }, {id: "cancel", name: '<?= __('取消'); ?>'});
    })
</script>
