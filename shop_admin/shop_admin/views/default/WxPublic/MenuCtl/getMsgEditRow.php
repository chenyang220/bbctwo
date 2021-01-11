<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/intlTelInput.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/intlTelInput.js" charset="utf-8"></script>

<style type="text/css">
    .hidden{display:none;}
    .area_in_sub{height:30px;line-height: 30px;margin-bottom: 10px;}
    .area_in_sub .second{width:95%;display:inline-block;}
    .area_in_sub .second select{width:130px;height:30px;}
    i{color:red;}
</style>

</head>

<body class="<?=$skin?>">
<form method="post" enctype="multipart/form-data" id="publicmsg_add" name="form1">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="words"><i>*</i><?= __('关键词'); ?></label>
                <input id="id" name="id" type="hidden" value="<?=$data['id']?>"/>
            </dt>
            <dd class="opt">
                <input id="words" name="words"   class="ui-input w200" type="text" value="<?=$data['words']?>"/>
                <p class="notic">多个关键词，以逗号（,）分隔！</p>
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label for="match_type"><i>*</i><?= __('匹配类型'); ?></label>
            </dt>
            <dd class="opt wp70">
                <select id="match_type" name ="match_type" style="border: 1px solid #A8B3B9;width: 130px;height:30px;">
                    <option <?php if($data['match_type']=='1'){echo 'selected';};?> value="1">精准匹配</option>
                    <option <?php if($data['match_type']=='2'){echo 'selected';};?>value="2">模糊匹配</option>
                </select>
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label for="msg_type"><i>*</i><?= __('消息类型'); ?></label>
            </dt>
            <dd class="opt wp70">
                <select id="msg_type" name ="msg_type" style="border: 1px solid #A8B3B9;width: 130px;height:30px;">
                    <option value="1" selected>文本消息</option>
                </select>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="content"><i>*</i><?= __('消息内容'); ?></label>
            </dt>
            <dd class="opt">
                <textarea style="width: 450px;height: 200px;" id="content" maxlength="100" name="content"><?=$data['content']?></textarea>
                <p class="notic"></p>
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
                return cancleGridEdit(),$("#publicmsg_add").trigger("validate"), !1
            }
        }, {id: "cancel", name: t[1]})
    }
    function postData(t, e)
    {

        $_form.validator({
            rules: {
                checkName:function(element){
                    if(element.value.length > 25){
                        return '<?= __('关键词不能超过'); ?>50<?= __('个字符'); ?>';
                    }
                },
            },
            messages: {
                required: "<?= __('请填写该字段'); ?>",
            },
            fields: {
                'words':'required;checkName' ,
                'match_type':'required',
                'msg_type':'required',
                'content':'required',
            },

            valid: function (form)
            {

                var words = $.trim($("#words").val()),
                    id = $.trim($("#id").val()),
                    match_type = $("#match_type").val(),
                    msg_type = $("#msg_type").val(),
                    content = $("#content").val(),
                    n = "Add" == t ? "<?= __('添加自动回复'); ?>" : "<?= __('修改自动回复'); ?>";
                params =  {
                   id:id, words: words, match_type: match_type, msg_type:msg_type, content:content
                };
                Public.ajaxPost(SITE_URL +"?ctl=WxPublic_Menu&met=" + ("Add" == t ? "Add" : "Edit")+ "MsgRow&typ=json", params, function (e)
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

    var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#publicmsg_add"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
    initPopBtns();
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
