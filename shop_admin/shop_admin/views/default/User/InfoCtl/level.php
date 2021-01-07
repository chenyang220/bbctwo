<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
    <link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
    <link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>

    <style>
        .webuploader-pick {
            padding: 1px;
        }

    </style>
    </head>
    <body class="<?=$skin?>">
    <div class="">
        <form method="post" enctype="multipart/form-data" id="score-form" name="form">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('修改等级为'); ?></label>
                    </dt>
                    <dd class="opt">
                        <select name="user_grade" id="user_grade">
                            <?php foreach($data as $k=>$v){?>
                                <option value="<?= ($v['user_grade_id']) ?>"><?= ($v['user_grade_name']) ?></option>
                            <?php }?>
                        </select>
                    </dd>
                </dl>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="<?= $this->view->js ?>/controllers/user/info/level.js" charset="utf-8"></script>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>