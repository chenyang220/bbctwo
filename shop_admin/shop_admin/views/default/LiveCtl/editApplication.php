<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
    echo 3333;die;
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
                <dt class="tit"><?= __('店铺名称'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <span><?=$data['shop_name']?></span>
                    </ul>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('开通时间'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <span><?= $data['application_time'] ?></span>
                    </ul>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('终止时间'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <span><?= $data['application_end_time'] ?></span>
                    </ul>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit"><?= __('是否开播'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
                            <div class="onoff">
                                <label title="<?= __('是'); ?>" class="cb-enable <?= ($data['application_status'] == 2 ? 'selected' : '') ?>" for="application_status_enable"><?= __('是'); ?></label>
                                <label title="<?= __('否'); ?>" class="cb-disable <?= ($data['application_status'] == 4 ? 'selected' : '') ?>" for="application_status_disabled"><?= __('否'); ?></label>
                                <input type="radio" value="2" name="application_status" id="application_status_enable" <?= ($data['application_status'] == 1 ? 'checked' : '') ?> />
                                <input type="radio" value="4" name="application_status" id="application_status_disabled" <?= ($data['application_status'] == 0 ? 'checked' : '') ?>/>
                            </div>
                        </li>
                    </ul>
                </dd>
            </dl>
        </div>
    </form>
    <script type="text/javascript" src="<?= $this->view->js ?>/controllers/editApplication.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>