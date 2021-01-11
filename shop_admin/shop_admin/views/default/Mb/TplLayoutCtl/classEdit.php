<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/mb.css?v=87" rel="stylesheet" type="text/css">

<div class="mb-item-edit-content">
    <div class="index_block home2">
        <div class="content">
            <h5><?= __('内容：'); ?></h5>
            <div class="iblock wp50 mt20">
                <div >
					<div class="clearfix">
						<h5 class="fl" id="classify1_class_name_total"><?= __('品牌'); ?></h5>
						<a class="fr mr47 fz12" nctype="btn_edit_item_image" data-name="classify1" data-desc="320*130" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑'); ?></a>
					</div>
                    <div nctype="item_image" class="item module-item iblock"> 
                        <img nctype="image" name="item_data[classify1_image_1]" src="<?= $this->view->img_com ?>/image.png" alt="" class="img wp100">
                        <h5 class="mt10" id="classify1_image_name_1"><?= __(''); ?></h5>
                    </div>
                    <div nctype="item_image" class="item module-item iblock"> 
                        <img nctype="image" name="item_data[classify1_image_2]" src="<?= $this->view->img_com ?>/image.png" alt="" class="img wp100">
                        <h5 class="mt10" id="classify1_image_name_2"><?= __(''); ?></h5>
                    </div>
                     
                </div>
            </div>
            <div class="iblock wp50 mt20">
                <div >
					<div class="clearfix">
						<h5 class="fl" id="classify2_class_name_total"><?= __('品牌'); ?></h5>
						<a class="fr mr47 fz12" nctype="btn_edit_item_image" data-name="classify2" data-desc="320*130" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑'); ?></a>
					</div>
                    <div nctype="item_image" class="item module-item iblock"> 
                        <img nctype="image" name="item_data[classify2_image_1]" src="<?= $this->view->img_com ?>/image.png" alt="" class="img wp100">
                        <h5 class="mt10" id="classify2_image_name_1"><?= __(''); ?></h5>
                    </div>
                    <div nctype="item_image" class="item module-item iblock"> 
                        <img nctype="image" name="item_data[classify2_image_2]" src="<?= $this->view->img_com ?>/image.png" alt="" class="img wp100">
                        <h5 class="mt10" id="classify2_image_name_2"><?= __(''); ?></h5>
                    </div>
                    
                </div>
            </div>
            <div class="iblock wp50 mt20">
                <div >
					<div class="clearfix">
						<h5 class="fl" id="classify3_class_name_total"><?= __('品牌'); ?></h5>
						<a class="fr mr47 fz12" nctype="btn_edit_item_image" data-name="classify3" data-desc="320*130" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑'); ?></a>
					</div>
                    <div nctype="item_image" class="item module-item iblock"> 
                        <img nctype="image" name="item_data[classify3_image_1]" src="<?= $this->view->img_com ?>/image.png" alt="" class="img wp100">
                        <h5 class="mt10" id="classify3_image_name_1"><?= __(''); ?></h5>

                    </div>
                    <div nctype="item_image" class="item module-item iblock"> 
                        <img nctype="image" name="item_data[classify3_image_2]" src="<?= $this->view->img_com ?>/image.png" alt="" class="img wp100">
                        <h5 class="mt10" id="classify3_image_name_2"><?= __(''); ?></h5>
                    </div>
                    
                </div>
            </div>
            <div class="iblock wp50 mt20">
                <div>
					<div class="clearfix">
						<h5 class="fl" id="classify4_class_name_total"><?= __('品牌'); ?></h5>
						<a class="fr mr47 fz12" nctype="btn_edit_item_image" data-name="classify4" data-desc="320*130" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑'); ?></a>
					</div>
                    <div nctype="item_image" class="item module-item iblock"> 
                        <img nctype="image" name="item_data[classify4_image_1]" src="<?= $this->view->img_com ?>/image.png" alt="" class="img wp100">
                        <h5 class="mt10" id="classify4_image_name_1"><?= __(''); ?></h5>
                    </div>
                    <div nctype="item_image" class="item module-item iblock"> 
                        <img nctype="image" name="item_data[classify4_image_2]" src="<?= $this->view->img_com ?>/image.png" alt="" class="img wp100">
                        <h5 class="mt10" id="classify4_image_name_2"><?= __(''); ?></h5>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include TPL_PATH . '/' . 'footer.php';
?>

<script>
    var img_src = {};
    var tplLayout_group = {};
    $(function(){
        api = frameElement.api,
        item_id = api.data.item_id,
        item_data = api.data.item_data,
        layout_data = item_data.mb_tpl_layout_data,
        callback = api.data.callback;
        if (layout_data) {
            layout_data.classify1 && render(layout_data.classify1 ,'classify1');
            layout_data.classify2 && render(layout_data.classify2 ,'classify2');
            layout_data.classify3 && render(layout_data.classify3 ,'classify3');
            layout_data.classify4 && render(layout_data.classify4 ,'classify4');
        } else {
            layout_data = {
                classify1: {
                    class_name_total:"",
                    image_name_1:"",
                    image_name_2:"",
                },
                classify2: {
                    class_name_total:"",
                    image_name_1:"",
                    image_name_2:"",
                },
                classify3: {
                    class_name_total:"",
                    image_name_1:"",
                    image_name_2:"",
                },
                classify4: {
                    class_name_total:"",
                    image_name_1:"",
                    image_name_2:"",
                },
                square: {}
            }
        }

        api.button({
            id: "confirm", name: '<?= __('确定'); ?>', focus: !0, callback: function () {
                postData();
                return false;
            }
        }, {id: "cancel", name: '<?= __('取消'); ?>'});

        function postData() {
            Public.ajaxPost(SITE_URL + '?ctl=Mb_TplLayout&met=editTplLayout&typ=json', {
                item_id: item_id,
                layout_data: layout_data
            }, function (data) {
                if (data.status == 200) {
                    typeof callback == 'function' && callback();
                    return true;
                } else {
                    Public.tips({type: 1, content: data.msg});
                }
            })
        }
        var _window = window.parent;

        $('[nctype="btn_edit_item_image"]').on('click', function () {
            var name = $(this).data('name');
            var l_data = {};
            eval('l_data=layout_data.' + name);
            var image_spec = $(this).data('desc');
            $.dialog({
                title: '<?= __('编辑'); ?>',
                content: 'url:' + SITE_URL + '?ctl=Mb_TplLayout&met=editClassImage&typ=e',
                max: true,
                min: false,
                cache: false,
                lock: true,
                width: 700,
                height: 600,
                zIndex: 9999,
                parent: _window,
                data: {
                    image_spec: image_spec,
                    image_name: name,
                    dialog_type: 'class',
                    layout_data: l_data,
                    callback: function (img_data) {
                        render(img_data, name);
                    }
                }
            })
        });
        
        function render (img_data, name) {
            var img_group = {};
            img_group.class_name_total = img_data.class_name_total;
            img_group.image_1 = img_data.image_1;
            img_group.image_name_1 = img_data.image_name_1;
            img_group.image_type_1 = img_data.image_type_1;
            img_group.image_data_1 = img_data.image_data_1;
            img_group.image_2 = img_data.image_2;
            img_group.image_name_2 = img_data.image_name_2;
            img_group.image_type_2 = img_data.image_type_2;
            img_group.image_data_2 = img_data.image_data_2;
            for(let key  in layout_data){
                if (key == name) {
                    console.log(img_group);
                      layout_data[key] =  img_group;
                } 
            }
            $('[nctype="image"][name="item_data[' + name + '_image_1]"]').prop('src', img_data.image_1);
            $('[nctype="image_name"][name="item_data[' + name + '_image_1]"]').val(img_data.image_1);
            $('[nctype="image"][name="item_data[' + name + '_image_2]"]').prop('src', img_data.image_2);
            $('[nctype="image_name"][name="item_data[' + name + '_image_2]"]').val(img_data.image_2);
            $("#" + name + "_class_name_total").html(img_data.class_name_total);
            $("#" + name + "_image_name_1").html(img_data.image_name_1);
            $("#" + name + "_image_name_2").html(img_data.image_name_2);
        }
    })
</script>