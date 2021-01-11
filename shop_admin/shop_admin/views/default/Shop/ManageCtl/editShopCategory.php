<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body class="<?=$skin?>">
        <form method="post" enctype="multipart/form-data" id="shop_edit_level" name="form1">
        <div class="ncap-form-default">
            <input type="hidden"  id="shop_class_bind_id" value="<?=$data['shop_class_bind_id']?>" />
            <dl class="row">
                <dt class="tit">
                    <label for="commission_rate"><?= __('分佣比例'); ?>(%)</label>
                </dt>
                <dd class="opt">
                    <input id="commission_rate" name="commission_rate" value="<?=$data['commission_rate']?>" class="ui-input w200" type="text"/>
                </dd>
            </dl>
            <?php if($data['delivery'] == 1){ ?>
                <dl class="row">
                    <dt class="tit"><?= __('同城配送'); ?></dt>
                    <dd class="opt">
                        <ul class="nofloat">
                            <li>
                                <div class="onoff" style="height: 24px;">
                                    <label title="<?= __('开启'); ?>" class="cb-enable <?= ($data['delivery_status'] == 1 ? 'selected' : '') ?> " for="delivery_status_enable"><?= __('开启'); ?></label>
                                    <label title="<?= __('关闭'); ?>" class="cb-disable <?= ($data['delivery_status'] == 0 ? 'selected' : '') ?>" for="delivery_status_disabled"><?= __('关闭'); ?></label>
                                    <input type="radio" value="1" name="operation" id="delivery_status_enable" <?= ($data['delivery_status'] == 1 ? 'checked' : '') ?> />
                                    <input type="radio" value="0" name="operation" id="delivery_status_disabled" <?= ($data['delivery_status'] == 0 ? 'checked' : '') ?> />
                                </div>
                            </li>
                        </ul>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="delivery_price"><?= __('商家配送费设置'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="delivery_price" name="delivery_price" value="<?= $data['delivery_price'] ?>" class="ui-input w200" type="text"/>
                        <span><?= __('元'); ?></span>
                        <p class="notic"><?= __('设置平台收取商家每单的配送费'); ?></p>
                    </dd>
                </dl>
            <?php } ?>
        </div>
    </form>

<script>

function initPopBtns()
{
    var t = "add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"] : ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
    api.button({
        id: "confirm", name: t[0], focus: !0, callback: function ()
        {
            postData(oper, rowData);
           return cancleGridEdit(),$("#shop_edit_level").trigger("validate"), !1
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
                'commission_rate':'required;' ,
            },

        valid: function (form)
        {
            var commission_rate = $.trim($("#commission_rate").val()), 
                shop_class_bind_id = $.trim($("#shop_class_bind_id").val()),
                delivery_price = $.trim($("#delivery_price").val()),
                delivery_status = $("input[name=operation]:checked").val()? $("input[name=operation]:checked").val():0;
    
			n = "add" == t ? "<?= __('新增经营类目'); ?>" : "<?= __('修改经营类目'); ?>";
			params = {
				shop_class_bind_id: shop_class_bind_id, 
				commission_rate: commission_rate,
				delivery_price: delivery_price,
				delivery_status: delivery_status,
			};
			Public.ajaxPost(SITE_URL +"?ctl=Shop_Manage&met=" + ("add" == t ? "add" : "edit")+ "ShopCategoryRow&typ=json", params, function (e)
			{
				if (200 == e.status)
				{
					parent.parent.Public.tips({content: n + "<?= __('成功！'); ?>"});
					 var callback = frameElement.api.data.callback;
                                         callback();
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
function resetForm(t)
{
    $_form.validate().resetForm();
    $("#product_class_id").val("");
    $("#commission_rate").val("");
  
			
}
var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#shop_edit_level"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
initPopBtns();

    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
