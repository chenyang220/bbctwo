<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script></head>

<style>
    .webuploader-pick {
        padding: 3px 15px;
        background: #f53a59;
    }
</style>
<body>

<form method="post" enctype="multipart/form-data" id="manage-form" name="manage-form" class="form-horizontal">
    <div class="ncap-form-default">
        <input name="payment_channel_id" id="payment_channel_id" value="" type="hidden" />
        <dl class="row">
            <dt class="tit">
                <label for="payment_channel_name"><?= __('支付名称'); ?></label>
            </dt>
            <dd class="opt">

                <input id="payment_channel_name" name="payment_channel_config[payment_channel_name]" value="" class="ui-input w400" type="text" />
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="payment_channel_config[account]"><?= __('支付账号'); ?></label>
            </dt>
            <dd class="opt">
                <input id="account" name="payment_channel_config[account]" value="" class="ui-input w400" type="text" />
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="payment_channel_config[key]"><?= __('支付密钥'); ?></label>
            </dt>
            <dd class="opt">
                <input id="key" name="payment_channel_config[key]" value="" class="ui-input w400" type="text" />
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="payment_channel_config[partner]"><?= __('支付商户号'); ?></label>
            </dt>
            <dd class="opt">
                <input id="partner" name="payment_channel_config[partner]" value="" class="ui-input w400" type="text" />
            </dd>
        </dl>
        <dl class="row js-wx" id="kaifa" style="display: none">
            <dt class="tit">
                <label for="payment_channel_config[appsecret]"><?= __('开发模式下的秘钥'); ?></label>
            </dt>
            <dd class="opt">
                <input id="appsecret" name="payment_channel_config[appsecret]" value="" class="ui-input w400" type="text" disabled="disabled" />
            </dd>
        </dl>
        <dl class="row js-wx" style="display: none">
            <dt class="tit">
                <label for="payment_channel_wechat"><?= __('微信中是否可以使用'); ?></label>
            </dt>
            <dd class="opt">
                <div class="onoff">
                    <label for="enable11" class="cb-enable  "><?= __('开启'); ?></label>
                    <label for="enable10" class="cb-disable  selected"><?= __('关闭'); ?></label>
                    <input id="enable11" name="payment_channel_wechat" value="1" type="radio">
                    <input id="enable10" name="payment_channel_wechat" checked="checked" value="0" type="radio">
                </div>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="payment_channel_enable"><?= __('是否启用'); ?></label>
            </dt>
            <dd class="opt">
                <div class="onoff">
                    <label for="enable1" class="cb-enable"><?= __('开启'); ?></label>
                    <label for="enable0" class="cb-disable  selected"><?= __('关闭'); ?></label>
                    <input id="enable1" name="payment_channel_enable" value="1" type="radio">
                    <input id="enable0" name="payment_channel_enable" checked="checked" value="0" type="radio">
                </div>
            </dd>
        </dl>

        <dl class="row js-wx unionpay" style="display: none">
            <dt class="tit">
                <label><?= __('上传'); ?>CERT<?= __('证书'); ?></label>
                <p class="notic"><?= __('请上传完该文件后选择下文件'); ?></p>
            </dt>
            <dd class="opt">
                <div class="uploadBut" key="apiclient_cert" onclick="Upload_File(this)" style="width: 80px;height: 34px;display: inline-block;vertical-align: top;"><?= __('选择文件'); ?></div>
                <a href="javascript:void(0)" class="ui-btn ui-btn-sp mrb startUpload"><?= __('开始上传'); ?></a>
            </dd>
        </dl>
        <dl class="row js-wx unionpay" style="display: none">
            <dt class="tit">
                <label><?= __('上传'); ?>KEY<?= __('证书'); ?></label>
                <p class="notic"><?= __('请上传完该文件后选择下文件'); ?></p>
            </dt>
            <dd class="opt">
                <div class="uploadBut" key="apiclient_key" onclick="Upload_File(this)" style="width: 80px;height: 34px;display: inline-block;vertical-align: top;"><?= __('选择文件'); ?></div>
                <a href="javascript:void(0)" class="ui-btn ui-btn-sp mrb startUpload"><?= __('开始上传'); ?></a>
            </dd>
        </dl>
    </div>
</form>



<script type="text/javascript">
    var curRow, curCol, curArrears, $grid = $("#grid"), $_form = $("#manage-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;

    console.info(rowData);
    var uploader,
        paymentChannelNameCombo,
        configkey,
        wx = ['wx_native', 'app_wx_native', 'app_h5_wx_native','wxapp'],
        alipay = ['alipay', 'alipayMobile'];

    initPopBtns();
    initField();

    function showWx(name)
    {
        if(rowData.id) {
            $("#account").val(rowData.payment_channel_config["appid"]);
            $("#key").val(rowData.payment_channel_config["key"]);
            $("#partner").val(rowData.payment_channel_config["mchid"]);
            $("#appsecret").val(rowData.payment_channel_config["appsecret"]);
            $("#kaifa").css("display", "block");
        }
        $("label[for='payment_channel_config[account]']").text("<?= __('微信公众号身份'); ?>");
        $("label[for='payment_channel_config[key]']").text("<?= __('商户支付密钥'); ?>");
        $("label[for='payment_channel_config[partner]']").text("<?= __('受理商'); ?>ID");
        $("#appsecret").attr("disabled", false);
        $('.js-wx').show();
        uploader.options.formData.paymentType = name;
    }

    function show()
    {
        var account = rowData.payment_channel_code + "_account";
        var key = rowData.payment_channel_code + "_key";
        var partner = rowData.payment_channel_code + "_partner";
        if(rowData.id && rowData.payment_channel_config) {
            $("#account").val(rowData.payment_channel_config[account]);
            $("#key").val(rowData.payment_channel_config[key]);
            $("#partner").val(rowData.payment_channel_config[partner]);
        }
        $("label[for='payment_channel_config[account]']").text("<?= __('支付账号'); ?>");
        $("label[for='payment_channel_config[key]']").text("<?= __('支付秘钥'); ?>");
        $("label[for='payment_channel_config[partner]']").text("<?= __('支付商户号'); ?>");
        $('.js-wx').hide();
    }

    function showUnionpay()
    {
        var account = rowData.payment_channel_code + "_account";
        var key = rowData.payment_channel_code + "_key";
        var partner = rowData.payment_channel_code + "_partner";
        if(rowData.id && rowData.payment_channel_config) {
            $("#account").val(rowData.payment_channel_config[account]);
            $("#key").val(rowData.payment_channel_config[key]);
            $("#partner").val(rowData.payment_channel_config[partner]);
        }
        $("label[for='payment_channel_config[account]']").text("<?= __('支付账号'); ?>");
        $("label[for='payment_channel_config[key]']").text("<?= __('支付秘钥'); ?>");
        $("label[for='payment_channel_config[partner]']").text("<?= __('支付商户号'); ?>");
        $('.unionpay').show();
    }
    
    function showAlipay()
    {
        var account = 'appid';
        var key = 'rsaPrivateKey';
        var partner = 'alipayrsaPublicKey';
        if(rowData.id && rowData.payment_channel_config){
            $("#account").val(rowData.payment_channel_config[account]);
            $("#key").val(rowData.payment_channel_config[key]);
            $("#partner").val(rowData.payment_channel_config[partner]);
        }
        $("label[for='payment_channel_config[account]']").text("APPID");
        $("label[for='payment_channel_config[key]']").text("<?= __('商户私钥'); ?>");
        $("label[for='payment_channel_config[partner]']").text("<?= __('支付宝公钥'); ?>");
        $('.js-wx').hide();
    }

    function initField() {
            //上传证书
            uploader = WebUploader.create({
            // swf文件路径
            swf: BASE_URL + '/js/Uploader.swf',
            // 文件接收服务端
            server: PAYCENTER_URL + '?ctl=Upload&met=uploadCert&typ=json',
            // 选择文件的按钮。可选
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '.uploadBut',
            fileVal: 'uploadFile',
            // 只允许选择图片文件。
            accept: {
                title: 'certification',
                extensions: 'pem',
                mimeTypes: 'file/!*'
            }
        });

        //当有文件添加进来的时候
        uploader.on( 'fileQueued', function( file ) {
            $('.webUpload_current').parent().append("<span class='"+ configkey+"_url' style='display: inline;padding: 0 10px 0 10px;'>" + file.name + "</span>");
        });
        $('.startUpload').on('click', function () {
            uploader.upload();
        });
        //文件上传成功后
        uploader.on( 'uploadSuccess', function( file, res ) {
            //这里可以得到上传后的文件路径
            $('.' + configkey + '_url').html(res.data.url);
            $('.webUpload_current').parent().append("<input id='" + configkey + "' name='payment_channel_config["+ configkey +"]' value='" +  res.data.url  + "' class='ui-input w400' type='hidden'  />");
        });
        //文件上传失败，显示上传出错。
        uploader.on( 'uploadError', function( file ) {
            $('.' + configkey + '_url').html('<?= __('上传失败'); ?>');
        });

        if (rowData.id) {
            $("#payment_channel_name").val(rowData.payment_channel_name);
            if ($.inArray(rowData.payment_channel_code, wx) > -1) {
                showWx(rowData.payment_channel_code)
            } else if ($.inArray(rowData.payment_channel_code, alipay) > -1) {
                showAlipay();
            } else {
                show();
            }

            $('#payment_channel_id').val(rowData.payment_channel_id);
            $('#payment_channel_config').val(JSON.stringify(rowData.payment_channel_config));
            $('#payment_channel_status').val(rowData.payment_channel_status);
            $('#payment_channel_allow').val(rowData.payment_channel_allow);

            if (rowData.payment_channel_wechat != 0) {
                $("#enable11").attr('checked', true);
                $("#enable10").attr('checked', false);
                $('[for="enable11"]').addClass('selected');
                $('[for="enable10"]').removeClass('selected');
            }
            else {
                $("#enable11").attr('checked', false);
                $("#enable10").attr('checked', true);
                $('[for="enable11"]').removeClass('selected');
                $('[for="enable10"]').addClass('selected');
            }


            if (rowData.payment_channel_enable != 0) {
                $("#enable1").attr('checked', true);
                $("#enable0").attr('checked', false);
                $('[for="enable1"]').addClass('selected');
                $('[for="enable0"]').removeClass('selected');
            }
            else {
                $("#enable1").attr('checked', false);
                $("#enable0").attr('checked', true);
                $('[for="enable1"]').removeClass('selected');
                $('[for="enable0"]').addClass('selected');
            }
            if ($.inArray(rowData.payment_channel_code, wx) > -1) {
                showWx(rowData.payment_channel_code);
            } else if ($.inArray(rowData.payment_channel_code, alipay) > -1) {
                showAlipay();
            } else if(rowData.payment_channel_code=='unionpay'){
                showUnionpay();
            }else{
                show();
            }
        }

    }

    function Upload_File(obj)
    {
        $(".uploadBut").removeClass("webUpload_current");//先全部移除'); ?>
        $(obj).addClass("webUpload_current");//添加当前标识'); ?>
        configkey = $(obj).attr('key');
    }

    function initPopBtns() {
        var btn = "add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"] : ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
        api.button({
            id: "confirm", name: btn[0], focus: !0, callback: function () {
                postData(oper, rowData.id);
                return cancleGridEdit(), $_form.trigger("validate"), !1;
            }
        }, {id: "cancel", name: btn[1]})
    }

    function postData(oper, id) {
        $_form.validator({
            ignore: ':hidden',
            theme: 'yellow_bottom',
            timely: 1,
            stopOnError: true,
            fields: {

            },
            valid: function (form) {
                var me = this;
                // <?= __('提交表单之前，'); ?>hold<?= __('住表单，防止重复提交'); ?>
                me.holdSubmit();

                var paymentMode = '&payment_channel_name=' + rowData.payment_channel_name + '&payment_channel_code=' + rowData.payment_channel_code;
                parent.$.dialog.confirm('<?= __('修改立马生效'); ?>,<?= __('是否继续？'); ?>', function () {
                        var n = "add" == oper ? _("<?= __('新增'); ?>") : _("<?= __('修改'); ?>");
                        Public.ajaxPost(SITE_URL + "?ctl=Payment_Channel&typ=json&met=" + ("add" == oper ? "add" : "edit"), $_form.serialize() + paymentMode, function (resp) {
                            if (200 == resp.status) {
                                resp.data['id'] = resp.data['payment_channel_id'];
                                parent.parent.Public.tips({content: n + "<?= __('成功！'); ?>"});
                                callback && "function" == typeof callback && callback(resp.data, oper, window)
                            }
                            else {
                                parent.parent.Public.tips({type: 1, content: n + "<?= __('失败！'); ?>" + resp.msg})
                            }
                            me.holdSubmit(false);
                        })
                    },
                    function () {
                        me.holdSubmit(false);
                    });
            },
        }).on("click", "a.submit-btn", function (e) {
            $(e.delegateTarget).trigger("validate");
        });
    }

    function cancleGridEdit() {
        null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
    }

    //设置表单元素回车事件
    function bindEventForEnterKey() {
        Public.bindEnterSkip($_form, function () {
            $('#grid tr.jqgrow:eq(0) td:eq(0)').trigger('click');
        });
    }

    function resetForm(t) {
        $('#payment_channel_id').val('');
        $('#payment_channel_code').val('');
        $('#payment_channel_name').val('');
        $('#payment_channel_config').val('');
        $('#payment_channel_status').val('');
        $('#payment_channel_allow').val('');
        $('#payment_channel_wechat').val('');
        $('#payment_channel_enable').val('');

    }

    $(".box-main .form-section:has(label)").each(function (i, el) {
        var $this = $(el),
            $label = $this.find('label'),
            $input = $this.find('.form-control');

        $input.on('focus', function () {
            $this.addClass('form-section-active');
            $this.addClass('form-section-focus');
        });

        $input.on('keydown', function () {
            $this.addClass('form-section-active');
            $this.addClass('form-section-focus');
        });

        $input.on('blur', function () {
            $this.removeClass('form-section-focus');

            if (!$.trim($input.val())) {
                $this.removeClass('form-section-active');
            }
        });

        $label.on('click', function () {
            $input.focus();
        });

        if ($.trim($input.val())) {
            $this.addClass('form-section-active');
        }
    });
</script>


<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
