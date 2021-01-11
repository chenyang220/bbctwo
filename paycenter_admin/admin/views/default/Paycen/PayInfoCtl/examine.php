<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js?>/libs/jquery/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/libs/jquery/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<?php
$user_id= request_string('user_id');
?>
<body>
<form method="post" enctype="multipart/form-data" id="shop_edit_class" name="form1">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="user_nickname"><?= __('审核状态'); ?></label>
            </dt>
            <?php if($_GET['type'] == 'bt'){ ?>
                <dd class="opt">

                    <input type="radio" name="user_bt_status" value="2" <?php if($data['user_bt_status'] == 2){?>checked="checked"<?php }?>><?= __('通&nbsp过'); ?>
                    <input type="radio" name="user_bt_status" value="3" <?php if($data['user_bt_status'] == 3){?>checked="checked"<?php }?>><?= __('拒&nbsp绝'); ?>
                </dd>
            <?php }else{ ?>
                <dd class="opt">
                    <input type="radio" name="user_identity_statu" value="2" <?php if($data['user_identity_statu'] == 2){?>checked="checked"<?php }?>><?= __('通&nbsp过'); ?>
                    <input type="radio" name="user_identity_statu" value="3" <?php if($data['user_identity_statu'] == 3){?>checked="checked"<?php }?>><?= __('拒&nbsp绝'); ?>
                </dd>
            <?php } ?>
        </dl>




    </div>
</form>
<input id="user_id"  name="user_id" value="<?php echo $user_id;?>"  type="hidden"/>

<script>

    var type = '<?=$_GET['type']?>';

    function initPopBtns()
    {
        var t = "add" == oper ? [__('<?= __('保存'); ?>'), __('<?= __('关闭'); ?>')] : [__('<?= __('确定'); ?>'), __('<?= __('取消'); ?>')];
        api.button({
            id: "confirm", name: t[0], focus: !0, callback: function ()
            {

                postData(oper, rowData.shop_class_id);
                return cancleGridEdit(),$("#shop_edit_class").trigger("validate"), !1
            }
        }, {id: "cancel", name: t[1]})
    }
    function postData(t, e)
    {
        $_form.validator({


            valid: function (form)
            {
                var user_id = $.trim($("#user_id").val());
                if(type == 'bt')
                {
                    var status= $.trim($("input[name='user_bt_status']:checked").val());
                }else
                {
                    var status= $.trim($("input[name='user_identity_statu']:checked").val());
                }


                params ={
                    user_id:user_id,
                    status: status,
                    type:type,
                };
                Public.ajaxPost(SITE_URL +"?ctl=Paycen_PayInfo&met=editInfoFen&typ=json", params, function (e)
                {
                    if (200 == e.status)
                    {
                        parent.parent.Public.tips( {content:"<?= __('审核成功！'); ?>"});
                        var callback = frameElement.api.data.callback;
                        callback();
                    }
                    else
                    {
                        parent.parent.Public.tips({type: 1, content:  "<?= __('审核失败！'); ?>" + e.msg})
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
        $("#shop_class_name").val("");
        $("#shop_class_deposit").val("");
        $("#shop_class_displayorder").val("");
    }
    var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#shop_edit_class"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
    initPopBtns();
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
