<?php if (!defined('ROOT_PATH')) {exit('No Permission');}
?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script> 
</head>

<body class="<?=$skin?>">
        <form method="post" enctype="multipart/form-data" id="shop_edit_class" name="form1">
         <div class="ncap-form-default">
              <dl class="row">
                <dt class="tit">
                    <label for="label_name">* <?= __('标签名称'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="label_name" name="label_name" value="" class="ui-input w200" type="text"/>
                </dd>
              </dl>
              <dl class="row">
                    <dt class="tit">
                        <label for="domain_modify_frequency"><em>*</em><?= __('标签排序'); ?></label>
                    </dt>
                    <dd class="opt">                    
                        <input id="label_tag_sort" name="label_tag_sort" value="" class="ui-input w400" type="text"/>
                        <p class="notic"><?= __('数字代表在特色商城首页的排序，数字越小越靠前'); ?></p>
                    </dd>
                </dl>
                <dl class="row banner_image">
                    <dt class="tit"></dt>
                    <dd class="opt show">
                        <img src="../shop_admin/static/common/images/image.png" id="label_image0" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                        <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                        <input id="label_logo" value="" class="ui-input w400 img-path" type="hidden"/>
                        <p class="notic">建议尺寸：62*62px</p>
                        <div class="image-line" id="label_upload0"><?= __('上传图片'); ?>
                            <i class="iconfont icon-tupianshangchuan"></i>
                        </div>
                    </dd>
                </dl>



        </div>
    </form>

    <script>
logo_upload = new UploadImage({
            thumbnailWidth: 62,
            thumbnailHeight: 62,
            imageContainer: '#label_image0',
            uploadButton: '#label_upload0',
            inputHidden: '#label_logo'
        });
         //删除已上传的图片
        $(".del-img").click(function () {
            $(this).next().val('');
            $(this).prev().prop('src','../shop_admin/static/common/images/image.png');
        })
function initPopBtns()
{
    var t = "add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"] : ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
    api.button({
        id: "confirm", name: t[0], focus: !0, callback: function ()
        {
            postData(oper, rowData.id);
           return cancleGridEdit(),$("#shop_edit_class").trigger("validate"), !1
        }
    }, {id: "cancel", name: t[1]})
}
function postData(t, e)
{
 
	$_form.validator({
           
          messages: {
                    required: "<?= __('请填写该字段'); ?>",
           },
            fields: {
                'label_name':'required;' 
            },

        valid: function (form)
        {
            var label_name = $.trim($("#label_name").val()), 
			n = "add" == t ? "<?= __('新增分类'); ?>" : "<?= __('修改分类'); ?>";
            params = {
                id: e, 
                label_name: label_name,
                label_tag_sort: $("#label_tag_sort").val(),
                label_logo: $("#label_logo").val()
            }
			Public.ajaxPost(SITE_URL +"?ctl=Shop_Label&met=" + ("add" == t ? "add" : "edit")+ "LabelBase&typ=json", params, 
                function (e){
    				if (200 == e.status)
    				{
    					parent.parent.Public.tips({content: n + "<?= __('成功！'); ?>"});
    					console.log(e.data);
    					callback && "function" == typeof callback && callback(e.data, t, window)
    				}
    				else
    				{
    					parent.parent.Public.tips({type: 1, content: n + "<?= __('失败！'); ?>" + e.msg})
    				}
			     })
        },
        ignore: ":hidden",
        theme: "yellow_bottom",
        timely: 1,
        stopOnError: !0
    });
}
function cancleGridEdit()
{
    null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
}

var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#shop_edit_class"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
function resetForm(t)
{
    $("#label_name").val(rowData.label_name);
    $("#label_tag_sort").val(rowData.label_tag_sort);
    $("#label_logo").val(rowData.label_logo);
    $("#label_image0").attr("src",rowData.label_logo);

}
initPopBtns();
resetForm();
    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
