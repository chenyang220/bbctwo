<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/mb.css" rel="stylesheet" type="text/css">
<div class="mb-item-edit-content">
    <div class="index_block home2">
        <div class="title">
            <h5><?= __('标题：'); ?></h5>
            <input id="home1_title" type="text" class="txt w200" name="item_data[title]"  maxlength="8" value="">
        </div>
        <div class="content">
            <h5><?= __('内容：'); ?></h5>
            <div class="home2_1">
                <div nctype="item_image" class="item"> <img nctype="image" name="item_data[square_image]" src="<?= $this->view->img_com ?>/image.png" alt="">
                    <input nctype="image_name" name="item_data[square_image]" type="hidden" value="s0_04953036315894578.jpg">
                    <input nctype="image_type" name="item_data[square_type]" type="hidden" value="keyword">
                    <input nctype="image_data" name="item_data[square_data]" type="hidden" value="<?= __('水果'); ?>">
                    <a nctype="btn_edit_item_image" data-name="square" data-desc="320*260" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑1'); ?></a>
                </div>
            </div>
            <div class="home2_2">
                <div class="home2_2_1">
                    <div nctype="item_image" class="item"> <img nctype="image" name="item_data[rectangle1_image]" src="<?= $this->view->img_com ?>/image.png" alt="">
                        <input nctype="image_name" name="item_data[rectangle1_image]" type="hidden" value="s0_04953036568877399.jpg">
                        <input nctype="image_type" name="item_data[rectangle1_type]" type="hidden" value="goods">
                        <input nctype="image_data" name="item_data[rectangle1_data]" type="hidden" value="100374">
                        <a nctype="btn_edit_item_image" data-name="rectangle1" data-desc="320*130" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑2'); ?></a>
                    </div>
                </div>
                <div class="home2_2_2">
                    <div nctype="item_image" class="item"> <img nctype="image" name="item_data[rectangle2_image]" src="<?= $this->view->img_com ?>/image.png" alt="">
                        <input nctype="image_name" name="item_data[rectangle2_image]" type="hidden" value="s0_04953036851456664.jpg">
                        <input nctype="image_type" name="item_data[rectangle2_type]" type="hidden" value="goods">
                        <input nctype="image_data" name="item_data[rectangle2_data]" type="hidden" value="100028">
                        <a nctype="btn_edit_item_image" data-name="rectangle2" data-desc="320*130" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑3'); ?></a>
                    </div>
                </div>
            </div>
			<div class="home3_2">
			    <div class="home3_2_1 iblock wp50">
			        <div nctype="item_image" class="item"> <img class="wp100" nctype="image" name="item_data[rectangle3_image]" src="<?= $this->view->img_com ?>/image.png" alt="">
			            <input nctype="image_name" name="item_data[rectangle3_image]" type="hidden" value="s0_04953036568877399.jpg">
			            <input nctype="image_type" name="item_data[rectangle3_type]" type="hidden" value="goods">
			            <input nctype="image_data" name="item_data[rectangle3_data]" type="hidden" value="100374">
			            <a nctype="btn_edit_item_image" data-name="rectangle3" data-desc="320*130" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑4'); ?></a>
			        </div>
			    </div>
			    <div class="home3_2_2 iblock wp50">
			        <div nctype="item_image" class="item"> <img class="wp100" nctype="image" name="item_data[rectangle4_image]" src="<?= $this->view->img_com ?>/image.png" alt="">
			            <input nctype="image_name" name="item_data[rectangle4_image]" type="hidden" value="s0_04953036851456664.jpg">
			            <input nctype="image_type" name="item_data[rectangle4_type]" type="hidden" value="goods">
			            <input nctype="image_data" name="item_data[rectangle4_data]" type="hidden" value="100028">
			            <a nctype="btn_edit_item_image" data-name="rectangle4" data-desc="320*130" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑5'); ?></a>
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
    $(function(){

        api = frameElement.api,
        item_id = api.data.item_id,
        item_data = api.data.item_data,
        layout_data = item_data.mb_tpl_layout_data,
        callback = api.data.callback;
 
        //init
        $('#home1_title').val(item_data.mb_tpl_layout_title);
        if (layout_data) {
            layout_data.rectangle1 && render(layout_data.rectangle1 ,'rectangle1');
            layout_data.rectangle2 && render(layout_data.rectangle2 ,'rectangle2');
            layout_data.rectangle3 && render(layout_data.rectangle3 ,'rectangle3');
            layout_data.rectangle4 && render(layout_data.rectangle4 ,'rectangle4');
            layout_data.square && render(layout_data.square ,'square');
        } else {
            layout_data = {
                rectangle1: {},
                rectangle2: {},
                rectangle3: {},
                rectangle4: {},
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
            var layout_title = $('#home1_title').val();
            if(!layout_title){
                parent.Public.tips({
                    type: 1,
                    content: '标题不能为空'
                });
            }else {
                Public.ajaxPost(SITE_URL + '?ctl=Mb_TplLayout&met=editTplLayout&typ=json', {
                    item_id: item_id,
                    layout_data: layout_data,
                    layout_title: layout_title
                }, function (data) {
                    if (data.status == 200) {
                        typeof callback == 'function' && callback();
                        return true;
                    } else {
                        Public.tips({type: 1, content: data.msg});
                    }
                })
            }
        }

        var _window = window.parent;

        $('[nctype="btn_edit_item_image"]').on('click', function () {

            var name = $(this).data('name');
            var l_data = {};
            eval('l_data=layout_data.' + name);

            var image_spec = $(this).data('desc');

            $.dialog({
                title: '<?= __('编辑'); ?>',
                content: 'url:' + SITE_URL + '?ctl=Mb_TplLayout&met=editImage&typ=e',
                max: true,
                min: false,
                cache: false,
                lock: true,
                width: 600,
                height: 400,
                zIndex: 9999,
                parent: _window,
                data: {
                    image_spec: image_spec,
                    image_name: name,
                    dialog_type: 'home2',
                    layout_data: l_data,
                    callback: function (img_data) {
                        render(img_data, name);
                    }
                }
            })
        });

        function render (img_data, name) {
            eval('layout_data.'+ name +'=img_data');
            $('[nctype="image"][name="item_data[' + name + '_image]"]').prop('src', img_data.image);
            $('[nctype="image_name"][name="item_data[' + name + '_image]"]').val(img_data.image_name);
            $('[nctype="image_type"][name="item_data[' + name + '_type]"]').val(img_data.image_type);
            $('[nctype="image_data"][name="item_data[' + name + '_data]"]').val(img_data.image_data);
        }
    })
</script>