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
        <form method="post" enctype="multipart/form-data" id="account-form" name="form">
            <input type="hidden" value="<?= $info['user_id']; ?>" name="user_id" id="user_id">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('管理员名称'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="user_administrator" name="user_administrator" value="<?=$info['user_administrator']; ?>" class="ui-input w400" type="text"/>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><?= __('类别'); ?></label>
                    </dt>
                    <dd class="opt">
                        <?php if($info){ ?>
                            <input id="user_for" name="user_for" value="<?= $info['user_for']; ?>" disabled class="ui-input w400" type="text"/>
                        <?php }else{ ?>
                            <select name="user_for" id="user_for">
                                <option value="PayCenter" >PayCenter</option>
                                <option value="UCenter" >UCenter</option>
                            </select>
                        <?php }?>
                        <p class="notic"><?= __('类别添加后不可修改'); ?></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit"><?= __('账号'); ?></dt>
                    <dd class="opt">
                        <input id="user_name" name="user_name" value="<?= $info['user_name']; ?>" <?php if($info){ ?> disabled <?php }?> class="ui-input w400" type="text"/>
                        <p class="notic"><?= __('用户名添加后不可修改'); ?></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><?= __('密码'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="password" name="password" value="" class="ui-input w400" type="text"/>
                        <p class="notic"><?= __('若不修改密码则不填'); ?></p>
                    </dd>
                </dl>
            </div>
        </form>
    </div>
    <script src="<?= Yf_Registry::get('base_url') ?>/shop_admin/static/default/js/controllers/user/account/editAccount.js"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>