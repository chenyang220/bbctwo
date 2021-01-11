<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>

<body class="<?=$skin?>">
<form method="post" enctype="multipart/form-data" id="news_edit_class" name="form1">
    <input name="news_class_id" value="" type="hidden"/>
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="newsclass_name">* <?= __('分类名称'); ?></label>
            </dt>
            <dd class="opt">
                <input id="newsclass_name" name="newsclass_name" value="" class="ui-input w200" type="text"/>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="sort">*<?= __('排序'); ?></label>
            </dt>
            <dd class="opt">
                <input id="sort" name="sort" value="" class="ui-input w200" type="text"/>
            </dd>
        
        </dl>
    
    
    </div>
</form>

<script>
    
    function initPopBtns() {
        var t = "add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"]:["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
        api.button({
            id: "confirm", name: t[0], focus: !0, callback: function () {
                
                postData(oper, rowData.news_class_id);
                return cancleGridEdit(), $("#news_edit_class").trigger("validate"), !1
            }
        }, {id: "cancel", name: t[1]})
    }
    
    function postData(t, e) {
        $_form.validator({
            messages: {
                required: "<?= __('请填写该字段'); ?>",
            },
            fields: {
                'newsclass_name': 'required;',
                'sort': 'required;integer[+0];',
            },
            valid: function (form) {
                var newsclass_name = $.trim($("#newsclass_name").val()),
                    sort = $.trim($("#sort").val()),
                    n = "add" == t ? "<?= __('新增资讯分类'); ?>":"<?= __('修改资讯分类'); ?>";
                params = rowData.news_class_id ? {
                    news_class_id: e,
                    news_class_name: newsclass_name,
                    news_sort: sort,
                }:{
                    news_class_name: newsclass_name,
                    news_sort: sort,
                };
                Public.ajaxPost(SITE_URL + "?ctl=Information_NewsClass&met=" + ("add" == t ? "add":"edit") + "NewsClassrows&typ=json", params, function (e) {

                    if (200 == e.status) {
                        parent.parent.Public.tips({content: n + "<?= __('成功！'); ?>"});
                        callback && "function" == typeof callback && callback(e.data, t, window)
                    }
                    else {
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
    
    function cancleGridEdit() {
        null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
    }
    
    function resetForm(t) {
        $_form.validate().resetForm();
        $("#newsclass_name").val("");
        $("#sort").val("");
    }
    
    var curRow, curCol, curArrears, $grid = $("#grid"), $_form = $("#news_edit_class"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
    initPopBtns();

</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
