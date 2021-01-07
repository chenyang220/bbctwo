<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css ?>/iconfont/iconfont.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css_com ?>/ztree.css" rel="stylesheet" type="text/css">

<link href="<?= $this->view->css ?>/base.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css ?>/seller_center.css?ver=<?=VER?>" rel="stylesheet">
<form method="post" id="shop_api-setting-form" name="settingForm" class="nice-validator n-yellow" novalidate="novalidate">
    <div class="ncap-form-default">
        <!--<dl class="row addcat">
            <dt class="tit"><?/*=__('搜索分类：')*/?></dt>
            <dd class="opt">
                <input type="text" class="text w150 heigh ui-input" style="" id="searchName" name="searchName" placeholder="<?/*=__('按经营类目名称搜索')*/?>"><a class="button button-only align-middle button-classic-search" id="searchButton" href="javascript:void(0);"><i class="iconfont icon-btn02"></i></a>
            </dd>
        </dl>-->
        <dl class="row addcat">
            <dt class="tit"><?=__('选择分类：')?></dt>
            <input type="hidden" name="cat_id" id="cat_id">
            <dd class="opt">
                <p id="cat_name"></p>
                <div class="notic"><?= __('请选择第三级分类'); ?></div>
            </dd>
        </dl>

        <dl class="row editcat" style="display: none">
            <dt class="tit"><?=__('已选择分类：')?></dt>
            <dd class="opt">
                <p id="cat_name2"></p>
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label for="site_name"><?= __('展示图片:'); ?></label>
            </dt>
            <dd class="opt">
                <!--                shop_admin/static/default/images/icons/category.png-->
                <img id="textfield1" name="textfield1" alt="<?= __('选择图片'); ?>" src="" width="90px" height="90px">

                <div class="image-line upload-image" id="button1"><?= __('上传图片'); ?></div>

                <input id="mb_cat_image" name="" value="" class="ui-input w400" type="hidden">
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
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.combo.js"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.ztree.all.js"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.ztree.exhide.js"></script>
<script>
    $(function() {
        api = frameElement.api, data = api.data, oper = data.oper, callback = data.callback; var cat_id;

        if ( oper == 'edit' ) {
            $('.editcat').show();
            $('.addcat').hide();
            //init
            var rowData = data.rowData;

            $('#textfield1').prop('src', rowData.app_cat_image);
            $('#mb_cat_image').val(rowData.mb_cat_image);
            $('#cat_id').val(rowData.cat_id);
            $('#cat_name').val(rowData.cat_name);
            $('#cat_name2').html(rowData.cat_name);

            cat_id = rowData.cat_id;
        }

        api.button({
            id: "confirm", name: '<?= __('确定'); ?>', focus: !0, callback: function () {
                postData();
                return false;
            }
        }, {id: "cancel", name: '<?= __('取消'); ?>'});

        function postData() {
            var post_cat_id = $('#cat_id').val();
            if(!(post_cat_id > 0)){
                Public.tips({type: 1, content: '请选择第三级分类'});
                return false;
            }
            var param = {
                cat_id: post_cat_id,
                mb_cat_image: $('#mb_cat_image').val(),
            };

            if ( oper == 'edit' ) {
                param.mb_cat_image_id = data.rowData.mb_cat_image_id;
            }

            Public.ajaxPost(SITE_URL + '?ctl=Supplier_CatImage&met=' + oper + 'CatImage&typ=json', {
                param: param
            }, function (data) {
                if (data.status == 200) {
                    typeof callback == 'function' && callback(data.data, oper, window);
                    return true;
                } else {
                    Public.tips({type: 1, content: data.msg});
                    return false;
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
            inputHidden: '#mb_cat_image'
        });

    });
</script>
<script>
    var categoryTree;
    $(function() {
        //商品类别
        var opts = {
            width : 180,
            //inputWidth : (SYSTEM.enableStorage ? 145 : 208),
            inputWidth : 190,
            defaultSelectValue : '-1',
            //defaultSelectValue : rowData.categoryId || '',
            showRoot : true,
            rootTxt: "<?=__('添加经营类目')?>",
            disExpandAll: false,
            searchByName: "#searchName",
            searchButton: "#searchButton"
        }

        categoryTree = Public.categoryTree($('#cat_name'), opts);
        // $('#cat_name').css('height','30px');
        $('#cat_name').change(function(){
            var i = $(this).data('id');
            $('#cat_id').val(i);
        });
    });

    ///根据文本框的关键词输入情况自动匹配树内节点 进行模糊查找
    function AutoMatch(txtObj) {
        if (txtObj.value.length > 0) {
            var zTree = categoryTree.zTree;
            console.log(zTree);
            var nodeList = zTree.getNodesByParamFuzzy("name", txtObj.value);
            $.fn.zTree.init($("#cat_name"), setting, nodeList);
        } else {

        }
    }
</script>