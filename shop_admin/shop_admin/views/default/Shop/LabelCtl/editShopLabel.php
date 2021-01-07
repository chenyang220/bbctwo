<?php if (!defined('ROOT_PATH')) {exit('No Permission');}
?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
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
        </div>
    </form>

    <script>

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
}
initPopBtns();
resetForm();
    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
