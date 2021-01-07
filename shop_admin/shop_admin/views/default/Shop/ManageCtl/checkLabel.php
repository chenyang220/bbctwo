<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<style>
    .col60{color: #F60;}
</style>
<body class="<?=$skin?>">
        <form method="post" enctype="multipart/form-data" id="shop_edit_level" name="form1">
            <input  id="shop_grade_id" value="<?=$value["shop_grade_id"]?>"  type="hidden"/>
            <div class="ncap-form-default">
             <dl class="row">
                <dt class="tit">
                    <label for="shop_grade_desc"><?= __('申请说明'); ?></label>
                </dt>
                <dd class="opt">
                    <textarea style="width:200px;height: 73px;" rows="6" class="tarea" id="shop_grade_desc" ></textarea>
                </dd>
            </dl>
        </div>
    </form>

    <script>

function initPopBtns(rowData)
{

    $("#shop_grade_desc").html(rowData.label_remarks);
    var t =  ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"];
    api.button({
        id: "confirm", name: t[0], focus: !0, callback: function ()
        {
           postData(rowData.shop_id);
           return !1;


           // $("#shop_edit_level").trigger("validate"), !1
        }
    }, {id: "cancel", name: t[1]})
}
function postData(shop_id)
{
    params = {
        shop_id: shop_id, 
    }
    Public.ajaxPost(SITE_URL +"?ctl=Shop_Label&met=check&typ=json", params, 
        function (e){
            if (200 == e.status)
            {
                parent.parent.Public.tips({content: "<?= __('成功！'); ?>"});
                callback && "function" == typeof callback && callback(window)
            }
            else
            {
                parent.parent.Public.tips({type: 1, content: "<?= __('失败！'); ?>" + e.msg})
            }
    })
        
}


var  api = frameElement.api;callback = api.data.callback;rowData = api.data
console.log(api);
console.log(callback);
initPopBtns(rowData);

    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
