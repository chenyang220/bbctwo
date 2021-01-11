<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
    include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/mb.css" rel="stylesheet" type="text/css">
<div class="mb-item-edit-content">
    <form method="post" name="manage-form" id="manage-form" action="">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for=""></label>
                </dt>
                <dd class="opt show">
                    <input type="radio" name="image" value="1"><span>一张图</span>
                    <input type="radio" name="image" value="2"><span>四张图</span>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>
<script>
    $(function(){
        var api = frameElement.api,callback = api.data.callback;
        $(".submit-btn").click(function(){
            var value = $("input[name='image']:checked").val();
            if(value){
                handle.image(value);
            }else{
                Public.tips({type: 1, content: "请选择版式！"});
            }
            typeof callback == 'function' && callback();
        })

        var handle = {
            image: function (t) {
                $.dialog({
                    title: '上传图片',
                    content: "url:./index.php?ctl=Special_Column&met=columnImage",
                    data: {data:t,callback: this.callback},
                    width: 700,
                    height: 500,
                    max: !1,
                    min: !1,
                    cache: !1,
                    lock: !0
                })
            },
            callback: function (t, i, data) {
                console.log(t)
                console.log(i)
                console.log(data)
            },
        };
    })
</script>
<?php include TPL_PATH . '/' . 'footer.php'; ?>

