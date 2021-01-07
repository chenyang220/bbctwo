<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>

<style>
    .webuploader-pick{ padding:1px; }
    
</style>
</head>
<body class="<?=$skin?>">
<div class="">
    <form method="post" enctype="multipart/form-data" id="tpl-add-form" name="form">
        <div class="ncap-form-default">
			<dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('模板名称'); ?></label>
                </dt>
                <dd class="opt">
					<input id="waybill_tpl_name" name="waybill_tpl_name"  value="" class="ui-input w400" type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('物流公司'); ?></label>
                </dt>
                <dd class="opt">					
					<select name="express_id">
					<?php foreach($data['items'] as $key=>$val){?>
					 <option value ="<?=$val['express_id']?>"><?=$val['express_name']?></option>
					<?php }?>
					</select>
                    <p class="notic"></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('宽度'); ?></label>
                </dt>
                <dd class="opt">
					<input id="waybill_tpl_width" name="waybill_tpl_width" value="" class="ui-input w400" type="text"/>
                    <p class="notic"> <?= __('运单宽度，单位为毫米'); ?>(mm)<?= __('。'); ?></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('高度'); ?></label>
                </dt>
                <dd class="opt">
					<input id="waybill_tpl_height" name="waybill_tpl_height" value="" class="ui-input w400" type="text"/>
                    <p class="notic"><?= __('运单高度，单位为毫米'); ?>(mm)</p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('上偏移量'); ?></label>
                </dt>
                <dd class="opt">
					<input id="waybill_tpl_top" name="waybill_tpl_top" value="" class="ui-input w400" type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('左偏移量'); ?></label>
                </dt>
                <dd class="opt">
					<input id="waybill_tpl_left" name="waybill_tpl_left" value="" class="ui-input w400" type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('模板图片'); ?></label>
                </dt>
                <dd class="opt">
                    <img id="waybill_tpl_image_image" name="waybill_tpl_image_image" alt="<?= __('选择图片'); ?>" src="<?=$this->view->img?>/image.png" width="200px" height="100px" />

                    <div class="image-line upload-image" id="waybill_tpl_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                    <input id="waybill_tpl_image"  name="waybill_tpl_image" value="" class="ui-input w400" type="hidden"/>
                    <div class="notic"><?= __('请上传扫描好的运单图片，图片尺寸必须与快递单实际尺寸相符'); ?></div>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit"><?= __('状态'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
						<div class="onoff" id="waybill_tpl_enable">
								<label title="<?= __('开启'); ?>" class="cb-enable selected" for="waybill_tpl_enable_enable"><?= __('开启'); ?></label>
								<label title="<?= __('关闭'); ?>" class="cb-disable " for="waybill_tpl_enable_disabled"><?= __('关闭'); ?></label>
								<input type="radio" value="1" name="waybill_tpl_enable" id="waybill_tpl_enable_enable"  checked />
								<input type="radio" value="0" name="waybill_tpl_enable" id="waybill_tpl_enable_disabled"  />
						</div>
                        </li>
                    </ul>
                </dd>
         </dl>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
<script>
    $(function () {
        var waybill_tpl_upload = new UploadImage({
            thumbnailWidth: 800,
            thumbnailHeight: 500,
            imageContainer: '#waybill_tpl_image_image',
            uploadButton: '#waybill_tpl_upload',
            inputHidden: '#waybill_tpl_image'
        });

    })
    function initPopBtns() {
        var t = "add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"] : ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
        api.button({
            id: "confirm", name: t[0], focus: !0, callback: function () {
                postData(oper, rowData.contract_type_id);
                return cancleGridEdit(), $("#tpl-add-form").trigger("validate"), !1
            }
        }, {id: "cancel", name: t[1]})
    }
    function postData(t, e) {
        $_form.validator({
            fields: {
                'waybill_tpl_name': 'required;',
                'express_id': 'required;integer[+]',
                'waybill_tpl_width': 'required;integer[+];',
                'waybill_tpl_height': 'required;integer[+];',
                'waybill_tpl_top': 'required;integer;',
                'waybill_tpl_left': 'required;integer;',
                'waybill_tpl_image': 'required;'
            },
            valid: function (form) {
                var me = this;
                me.holdSubmit();
                n = "<?= __('增加'); ?>";
                Public.ajaxPost(SITE_URL + '?ctl=Logistics_Waybill&met=addWaybillTplDetail&typ=json', $_form.serialize(), function (e) {
                    if (200 == e.status) {
                        parent.parent.Public.tips({content: n + "<?= __('成功！'); ?>"});
                        callback && "function" == typeof callback && callback(e.data, t, window)
                    }
                    else {
                        parent.parent.Public.tips({type: 1, content: n + "<?= __('失败！'); ?>" + e.msg})
                    }
                    me.holdSubmit(false);
                })
            },
            ignore: ":hidden",
            theme: "yellow_bottom",
            timely: 1,
            stopOnError: !0
        });
    }
    function cancleGridEdit() {
        null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
    }
    var curRow, curCol, curArrears, $grid = $("#grid"), $_form = $("#tpl-add-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
    initPopBtns();
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>