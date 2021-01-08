<?php 
include __DIR__.'/../../includes/header.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('编辑收货地址'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../../css/intlTelInput.css">
</head>
<style>
	.nctouch-main-layout .nctouch-main-layout-a{top: 4rem;}
</style>
<body>
    <header id="header">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="address_list.html"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1><?= __('编辑收货地址'); ?></h1>
            </div>
            <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="save"></i></a> </div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <form>
            <div class="nctouch-inp-con">
                <ul class="form-box bgf">
                    <li class="form-item">
                        <h4><?= __('收货人姓名'); ?></h4>
                        <div class="input-box">
                            <input type="text" class="inp" name="true_name" id="true_name" autocomplete="off" oninput="writeClear($(this));" />
                            <span class="input-del"></span> </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('联系手机'); ?></h4>
                        <div class="input-box">
                            <input type="tel" class="inp" name="mob_phone" id="re_user_mobile" autocomplete="off" oninput="writeClear($(this));" />
                            <input type="hidden"  name="area_code" id="area_code"  />
                            <span class="input-del"></span> </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('地区选择'); ?></h4>
                        <div class="input-box">
                            <input type="text" class="inp" name="area_info" id="area_info" unselectable="on" onfocus="this.blur()" autocomplete="off" onchange="btn_check($('form'));" readonly/>
                        </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('详细地址'); ?></h4>
                        <div class="input-box">
                            <input type="text" class="inp" name="address" id="address" autocomplete="off" oninput="writeClear($(this));">
                            <span class="input-del"></span> </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('地址标签'); ?></h4>
                        <div class="input-box" style="font-size: 0.59rem;top: 10px;">
                            <input type="radio" name="address_attribute" value="1" checked="true" /><?= __('公司'); ?>
                            <input type="radio" name="address_attribute" value="2" /><?= __('家'); ?>
                            <input type="radio" name="address_attribute" value="3" /><?= __('学校'); ?>
                        </div>
                    </li>
                    <li>
                        <h4><?= __('默认地址'); ?></h4>
                        <div class="input-box">
                            <label>
                                <input type="checkbox" name="is_default" id="is_default" value="1" />
                                <span class="power"><i></i></span> </label>
                        </div>
                    </li>
                    <li class="form-item">
                        <div>
                            <div class="header-title" style="width:100%;border: solid 1px #d8d3d3;height: 30px;">
                                <h2 style="margin-left: 10px;font-size: 0.59rem;line-height: 30px;">智能地址填写</h2>
                            </div>
                            <textarea id="adresstext" name="address_self_motion"  rows="10" placeholder="请在姓名和手机号之间空格，例如粘贴入'小明 136*******9 *省 *市 *区 **号"  style="line-height: 20px;height:100px;width: 100%;border:solid 1px #cbc9c9;border-top:none;"></textarea>
                        </div>
                    </li>
                    <li class="form-item">
                          <div class="form-btn" style="float:right;margin-right: 20px;margin-top: 10px;">
                            <a id="button1" style="vertical-align: middle;display: inline-block !important;padding: 0.1rem 0.5rem 0.15rem;font-size: 0.6rem;color: #555 !important;background-color: #FFF; border: solid 1px #CCC;" href="javascript:;">清除</a>
                            <a id="button2" style="vertical-align: middle;display: inline-block !important;padding: 0.1rem 0.5rem 0.15rem;font-size: 0.6rem;color: #555 !important;background-color: #FFF; border: solid 1px #CCC;" href="javascript:;">提交</a>
                        </div>
                    </li>
                </ul>
                <div class="error-tips"></div>
                <div class="form-btn ok"><a class="btn" href="javascript:;"><?= __('保存地址'); ?></a></div>
            </div>
        </form>
    </div>
    <footer id="footer" class="bottom"></footer>
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/tmpl/address_opera_edit.js"></script>
    <script type="text/javascript" src="../../js/intlTelInput.js"></script>
</body>

</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>