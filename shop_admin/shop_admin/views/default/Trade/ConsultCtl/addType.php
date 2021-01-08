<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.datetimepicker.js" charset="utf-8"></script>
</head>
<body class="<?=$skin?>">
    <form method="post" id="manage-form" name="settingForm">

        <div class="ncap-form-default">
			<dl class="row">
                <dt class="tit"><?= __('名称'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
							 <input id="consult_type_name" name="consult_type_name" class="ui-input w200" type="text" />
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('排序'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
						<li>
							<input id="consult_type_sort" name="consult_type_sort" class="ui-input w200" type="text" value="255"/>
						</li>
                    </ul>
					<p class="notic"><?= __('数字范围为'); ?>0~255<?= __('，数字越小越靠前'); ?></p>
                </dd>
            </dl>
        </div>
    </form>
    <script type="text/javascript" charset="utf-8">

	function initPopBtns()
				{
					var t = "add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"] : ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
					api.button({
						id: "confirm", name: t[0], focus: !0, callback: function ()
						{
							postData(oper, rowData.contract_type_id);
							return cancleGridEdit(),$("#manage-form").trigger("validate"), !1
						}
					}, {id: "cancel", name: t[1]})
				}
			function postData(t, e)
			{
			$_form.validator({
				fields: {
					consult_type_name:"required;",
					consult_type_sort:"required;integer[+];",
				},
				valid: function (form)
				{
					var me = this;
					// <?= __('提交表单之前，'); ?>hold<?= __('住表单，防止重复提交'); ?>
					me.holdSubmit();
					n = "<?= __('添加'); ?>";
					Public.ajaxPost(SITE_URL+"?ctl=Trade_Consult&typ=json&met=addTypeBase", $_form.serialize(), function (e)
					{
						if (200 == e.status)
						{
							parent.parent.Public.tips({content: n + "<?= __('成功！'); ?>"});
							callback && "function" == typeof callback && callback(e.data, t, window)
						}
						else
						{
							parent.parent.Public.tips({type: 1, content: n + "<?= __('失败！'); ?>" + e.msg})
						}
						// <?= __('提交表单成功后，释放'); ?>hold<?= __('，如果不释放'); ?>hold<?= __('，就变成了只能提交一次的表单'); ?>
						me.holdSubmit(false);
					})
				},
				ignore: "",
				theme: "yellow_bottom",
				timely: 1,
				stopOnError: !0
			});
		}
		function cancleGridEdit()
		{
			null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
		}
		var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#manage-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
		initPopBtns();
	</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>