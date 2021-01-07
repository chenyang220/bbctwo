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
    <link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.datetimepicker.js" charset="utf-8"></script>
    </head>
    <body class="<?=$skin?>">
    <form method="post" id="manage-form" name="settingForm">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('审核状态'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                            <div class="onoff">
                                <input id="application_status1" name="application_status" checked  value="2" type="radio">
                                <label for="application_status1" class="cb-enable selected"><?= __('通过'); ?></label>

                                <input id="application_status0" name="application_status" value="3" type="radio">
                                <label for="application_status0" class="cb-disable"><?= __('拒绝'); ?></label>
                            </div>
                        </li>
                    </ul>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('审核信息'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                            <textarea id="application_info" name="application_info" class="ui-input"></textarea>
                        </li>
                    </ul>
                </dd>
            </dl>
        </div>
    </form>
    <script type="text/javascript" src="<?= $this->view->js ?>/controllers/verifyApplication.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>