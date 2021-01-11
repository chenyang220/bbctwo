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
                <label for="wechat_public_name">菜单分类</label>
            </dt>
            <dd class="opt">
                <select class="ui-input" id="menu_level" name ="menu_level" style="width: 130px;height:30px;">
                    <option value='0'>请选择</option>
                    <?php  foreach($data as $item){
                        echo "<option value='".$item['id']."'>".$item['menu_name']."</option>";
                    }?>
                </select>
                <p class="notic">不选择，默认一级菜单</p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="wechat_public_start_id">菜单名称</label>
            </dt>
            <dd class="opt">
                <input  id="menu_name" name="menu_name"  class="w400 ui-input " type="text"/>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="wechat_public_wxaccount">菜单类型</label>
            </dt>
            <dd class="opt">
                <select class="ui-input" id="menu_type" name ="menu_type" style="width: 130px;height:30px;"onchange="change_show(this.value);">
                    <option value="1" selected>发送消息</option>
                    <option value="2">跳转网页</option>
                    <option value="3">打开小程序</option>
                </select>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row" id="content_dl">
            <dt class="tit">
                <label for="content">消息内容</label>
            </dt>
            <dd class="opt">
                <input  id="content" name="content"  class="w400 ui-input " type="text"/>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row" id="redirect_url_dl">
            <dt class="tit">
                <label for="redirect_url">跳转URL</label>
            </dt>
            <dd class="opt">
                <input  id="redirect_url" name="redirect_url"  class="w400 ui-input " type="text"/>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row" id="xcx_id_dl">
            <dt class="tit">
                <label for="xcx_id">小程序ID</label>
            </dt>
            <dd class="opt">
                <input  id="xcx_id" name="xcx_id"  class="w400 ui-input " type="text"/>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row" id="xcx_url_dl">
            <dt class="tit">
                <label for="xcx_url">小程序地址</label>
            </dt>
            <dd class="opt">
                <input   id="xcx_url" name="xcx_url" class="w400 ui-input " type="text"/>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row" id="wxxcx_pagepath_dl">
            <dt class="tit">
                <label for="wxxcx_pagepath">小程序页面路径</label>
            </dt>
            <dd class="opt">
                <input   id="wxxcx_pagepath" name="wxxcx_pagepath" class="w400 ui-input " type="text"/>
                <p class="notic"></p>
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label for="sort_num">排序</label>
            </dt>
            <dd class="opt">
                <input   id="sort_num" name="sort_num" class="w400 ui-input " type="text"/>
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
        }, {id: "cancel", name: t[1]});
        var menu_type =  $("#menu_type").val();
        change_show(menu_type);
    }
    function postData(t, e)
    {

        $_form.validator({
            rules: {
                checkName:function(element){
                    if(element.value.length > 10){
                        return '<?= __('菜单名称不能超过'); ?>10<?= __('个字符'); ?>';
                    }
                },
            },
            messages: {
                required: "<?= __('请填写该字段'); ?>",
            },
            fields: {
                'menu_name':'required;checkName' ,
            },

            valid: function (form)
            {
                var menu_name = $.trim($("#menu_name").val()),
                    menu_type =  $("#menu_type").val(),
                    menu_level = $("#menu_level").val(),
                    content = $("#content").val(),
                    redirect_url = $("#redirect_url").val(),
                    xcx_id = $("#xcx_id").val(),
                    xcx_url = $("#xcx_url").val(),
                    wxxcx_pagepath = $("#wxxcx_pagepath").val(),
                    sort_num = $("#sort_num").val(),
                    n = "Add" == t ? "<?= __('新增微信公众号菜单'); ?>" : "<?= __('修改微信公众号菜单'); ?>";
                    if(menu_type==1){
                        params =  {
                            menu_name: menu_name, menu_type: menu_type, menu_level:menu_level, content:content,sort_num:sort_num
                        };
                    }else if(menu_type==2){
                        params =  {
                            menu_name: menu_name, menu_type: menu_type, menu_level:menu_level,redirect_url:redirect_url,sort_num:sort_num
                        };
                    }else if(menu_type==3){
                        params =  {
                            menu_name: menu_name, menu_type: menu_type, menu_level:menu_level,xcx_id:xcx_id,xcx_url:xcx_url,wxxcx_pagepath:wxxcx_pagepath,sort_num:sort_num
                        };
                    }
                Public.ajaxPost(SITE_URL +"?ctl=WxPublic_Menu&met=" + ("Add" == t ? "Add" : "Edit")+ "MenuRow&typ=json", params, function (e)
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

    function change_show(val){
        if(val==1){
            $("#content_dl").show();
            $("#redirect_url_dl").hide();
            $("#xcx_id_dl").hide();
            $("#xcx_url_dl").hide();
            $("#wxxcx_pagepath_dl").hide();
        }else if(val==2){
            $("#content_dl").hide();
            $("#xcx_id_dl").hide();
            $("#xcx_url_dl").hide();
            $("#wxxcx_pagepath_dl").hide();
            $("#redirect_url_dl").show();
        }
        else if(val==3){
            $("#content_dl").hide();
            $("#redirect_url_dl").hide();
            $("#xcx_id_dl").show();
            $("#xcx_url_dl").show();
            $("#wxxcx_pagepath_dl").show();
        }

    }
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
