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
    <title><?= __('新增收货地址'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../../css/intlTelInput.css">
	<link rel="stylesheet" href="../../css/iconfont.css">
</head>

<body class="bgf hp100">
    <header id="header">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a href="address_list.html"> <i class="back"></i> </a> -->
            </div>
            <div class="header-title">
                <h1><?= __('新增收货地址'); ?></h1>
            </div>
            <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="save"></i></a> </div>
        </div>
    </header>
    <div class="nctouch-main-layout">
        <form>
            <div class="nctouch-inp-con">
                <ul class="form-box">
                    <li class="form-item">
                        <h4><?= __('收货人'); ?></h4>
                        <div class="input-box">
                            <input type="text" class="inp" name="true_name" id="true_name" autocomplete="off" oninput="writeClear($(this));" placeholder="请填写收货人姓名"/>
                            <span class="input-del"></span> </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('手机号码'); ?></h4>
                        <div class="input-box">
                            <input type="tel" class="inp" name="mob_phone" id="re_user_mobile" autocomplete="off" oninput="writeClear($(this));" placeholder="请填写收货人手机号"/>
                            <input type="hidden"  name="area_code" id="area_code" />
                            <span class="input-del"></span> </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('所在地区'); ?></h4>
                        <div class="input-box">
                            <input name="area_info" type="text" class="inp" id="area_info" unselectable="on" onfocus="this.blur()" autocomplete="off" onchange="btn_check($('form'));" readonly placeholder="请选择所在区域省市县"/>
                        	<!-- <h5 class="form-item-h5"><i class="arrow-r"></i></h5> -->
							<i class="iconfont icon-arrow-right item-right-icon"></i>
                        </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('详细地址'); ?></h4>
                        <div class="input-box">
                            <input type="text" class="inp" name="address" id="address" autocomplete="off" placeholder="<?= __('街道、楼牌号等'); ?>" oninput="writeClear($(this));" placeholder="街道、楼牌号等">
                            <span class="input-del"></span> </div>
                    </li>
                    <li class="form-item">
                        <h4><?= __('地址标签'); ?></h4>
                        <div class="input-box addr-attr">
                            <em class="active"><input type="radio" name="address_attribute" value="1" checked="true" /><?= __('公司'); ?></em>
                            <em><input type="radio" name="address_attribute" value="2" /><?= __('家'); ?></em>
                            <em><input type="radio" name="address_attribute" value="3" /><?= __('学校'); ?></em>
                        </div>
                    </li>
                    <li class="default-addr-li">
                        <h4><?= __('设置默认地址'); ?></h4>
                        <div class="input-box clearfix">
                            <label>
                                <input type="checkbox" class="checkbox" name="is_default" id="is_default" value="1" />
                                <span class="power"><i></i></span> 
                            </label>
                        </div>
                    <label class="set-default-addr"><?= __('提醒：每次下单会默认推荐使用该地址'); ?></label>            
                    </li>
					<li class="form-item">
					    <h4><?= __('智能地址填写'); ?></h4>
						<div class="addr-auto-write">
							<textarea id="adresstext" name="address_self_motion"  rows="10" placeholder="请在姓名和手机号之间空格，例如粘贴入'小明 136*******9 *省 *市 *区 **号"></textarea>
							<span>
								<a class="btn-del" id="button1" href="javascript:;">清空</a>
								<a class="btn-get" id="button2" href="javascript:;">识别</a>
							</span>
							
						</div>
					   
					</li>
                </ul>
                <div class="error-tips"></div>
                <div class="form-btn form-btn-color"><a class="btn-l" href="javascript:;"><?= __('保存'); ?></a></div>
            </div>
        </form>
    </div>
    <footer id="footer" class="bottom"></footer>
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/intlTelInput.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    <script type="text/javascript" src="../../js/tmpl/address_opera.js"></script>
</body>

</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>