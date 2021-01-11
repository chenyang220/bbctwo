<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>

<link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css ?>/shop_table.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js"
        charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js"
        charset="utf-8"></script>
</head>
<body class="<?=$skin?>">
<style>
    
    .ui-jqgrid tr.jqgrow .img_flied {
        padding: 1px;
        line-height: 0px;
    }
    
    .img_flied img {
        width: 100px;
        height: 30px;
    }

</style>
<form id="article_form" method="post">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="title"><?= __('发布方'); ?></label>
            </dt>
            <dd class="opt">
                <?= $data["authorname"] ?>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="subtitle"><?= __('发布时间'); ?></label>
            </dt>
            <dd class="opt">
                <?= $data["create_time"] ?>
                <p class="notic"></p>
            </dd>
        </dl>
    </div>
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="title"><?= __('资讯标题'); ?></label>
            </dt>
            <dd class="opt">
                <?= $data["title"] ?>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="subtitle"><?= __('资讯副标题'); ?></label>
            </dt>
            <dd class="opt">
                <?= $data["subtitle"] ?>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label><?= __('资讯分类'); ?>:</label>
            </dt>
            <dd class="opt">
                <?= $data["newsclass_name"] ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label><?= __('文章内容'); ?></label>
            </dt>
            <dd class="opt">
               <?= $data['content']?>
            </dd>
        </dl>
    </div>
</form>
<script>
    $(function () {
        if ($('#shop_verify-form').length > 0) {
            $('#shop_verify-form').validator({
                ignore: ':hidden',
                theme: 'yellow_bottom',
                timely: 1,
                stopOnError: true,
                fields: {},
                valid: function (form) {
                    parent.$.dialog.confirm(__('<?= __('修改立马生效'); ?>,<?= __('是否继续？'); ?>'), function () {
                            Public.ajaxPost(SITE_URL + '?ctl=Shop_Manage&met=verifyShop&typ=json', $('#shop_verify-form').serialize(), function (data) {
                                if (data.status == 200) {
                                    parent.Public.tips({content: '<?= __('修改操作成功！'); ?>'});
                                    setTimeout(function () {
                                        frameElement.api.close();
                                    }, 3000)
                                } else {
                                    parent.Public.tips({type: 1, content: data.msg || '<?= __('操作无法成功，请稍后重试！'); ?>'});
                                    window.location.reload();
                                }
                            });
                        },
                        function () {
                        
                        });
                }
            }).on("click", "a.submit-btn", function (e) {
                $(e.delegateTarget).trigger("validate");
            });
        }
    });
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
