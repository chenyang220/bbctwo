<!DOCTYPE html>
<html class="root61">
<?php
$re_url = '';
$re_url = Yf_Registry::get('re_url');
$from = $_REQUEST['callback'];
$callback = $from ?: $re_url;
$t = '';
$code = '';
extract($_GET);
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('隐私协议')?></title>
    <link rel="stylesheet" href="<?= $this->view->css ?>/register.css">
    <link rel="stylesheet" href="<?= $this->view->css ?>/base.css">
    <link rel="stylesheet" href="<?= $this->view->css ?>/intlTelInput.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/headfoot.css" />
    <link rel="stylesheet" href="<?= $this->view->css ?>/iconfont/iconfont.css">
    <script src="<?= $this->view->js ?>/jquery-1.9.1.js"></script>
    <script type="text/javascript">
        $(function () {
            function changeDiv(firstDiv, secondDiv) {
                var temp;
                temp = firstDiv.html();
                firstDiv.html(secondDiv.html());
                secondDiv.html(temp);
            }

            if ($(window).width() < 690) {
                changeDiv($(".pas"), $(".mobile"));
            }
        })
    </script>
    <script src="<?= $this->view->js ?>/respond.js"></script>
    <script src="<?= $this->view->js ?>/intlTelInput.js"></script>

    <style type="text/css">
        .form-item .clear-btn {
            right: 10px;
        }

        /* 取消H5表单上下箭头 */
        /* 谷歌浏览器兼容性 */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0;
        }

        /* 火狐浏览器兼容性 */
        input[type="number"] {
            -moz-appearance: textfield;
        }
        .aaa{
            background: red !important;
        }


    </style>
</head>
<body>
<div id="header">
      <a href="javascript:history.go(-1)" class="back-pre"></a>
      <?= __('隐私协议')?>
</div>
<div class="container w">
    <div class="ui-content-text">
        <?= __($reg_row['privacy_protocol']['config_value']) ?>
    </div>
    <?php
    include $this->view->getTplPath() . '/' . 'footer.php';
    ?>
</div>
</body>
</html>
