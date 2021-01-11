<?php
 //免登录鉴权
$token = isset($_GET['token']) ? $_GET['token'] : ''; //令牌
$enterprise_id = isset($_GET['enterId']) ? $_GET['enterId'] : ''; //企业id
if ($token && $enterprise_id != '') {
    include_once __DIR__ . '/simba/src/QuickOauth.class.php';
    include_once __DIR__ .'/simba/src/Api.class.php';
    $quick_oauth = new simba\oauth\QuickOauth();
    $hashkey = $quick_oauth->getHashkey($token);  // 获取hashKey
    $result = $quick_oauth->getAccessToken($token, $enterprise_id, $hashkey);
    $access_token = $result['access_token']; // 放入从授权入口或者快速授权入口获取到的access_token
    $apiObj = new simba\oauth\Api();
    $result = $apiObj->simba_user_info($access_token);
    $u_id = '';
    if($result['msgCode'] == 200){
       $u_id = $result['result']['userNumber'];
    }
}
if ($_GET['qr']) {
    setcookie('is_app_guest', 1, time() + 86400 * 366);
    $_COOKIE['is_app_guest'] = 1;
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?=Web_ConfigModel::value('site_name')?> - <?=Web_ConfigModel::value('title')?></title>
	<meta name="description" content="<?=Web_ConfigModel::value('description')?>" />
	<meta name="Keywords" content="<?=Web_ConfigModel::value('keyword')?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
	<?php if ($_COOKIE['lang_selected']=='en_US') { ?>
      <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/english.css"/>
    <?php } ?>
	<link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/base.css">
	<link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/tips.css">
	<link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/headfoot.css">
	<!-- <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/iconfont/iconfont.css"> -->
	<link rel="stylesheet" href="http://at.alicdn.com/t/font_ucm2vzrmvdfjq0k9.css">
	<link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/palyCenter.css">
	<link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/dialog/green.css">
	<link href="<?= $this->view->css ?>/validator/jquery.validator.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
	<script src="<?=$this->view->js?>/jquery-1.9.1.js" type="text/javascript"></script>
	<script src="<?=$this->view->js?>/respond.js"></script>
    <script src="<?=$this->view->js?>/jquery.cookie.js"></script>
	<script src="<?=$this->view->js?>/cropper.js"></script>
	<script src="<?=$this->view->js?>/jquery.dialog.js"></script>
	<script src="<?=$this->view->js?>/jquery.toastr.min.js"></script>
	<script type="text/javascript" src="<?=$this->view->js?>/validator/jquery.validator.js" charset="utf-8"></script>
	<script type="text/javascript" src="<?=$this->view->js?>/validator/local/zh_CN.js" charset="utf-8"></script>
    <script src="<?=$this->view->js?>/common.js"></script>
    
	<script>
		var BASE_URL = "<?=Yf_Registry::get('base_url')?>";
		var SITE_URL = "<?=Yf_Registry::get('url')?>";
		var INDEX_PAGE = "<?=Yf_Registry::get('index_page')?>";
		var STATIC_URL = "<?=Yf_Registry::get('static_url')?>";
		var U_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";
		var SHOP_URL = "<?=Yf_Registry::get('shop_api_url')?>";
		var UCENTER_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";

		var DOMAIN = document.domain;
		var WDURL = "";
		var SCHEME = "default";
		try
		{
			//document.domain = 'ttt.com';
		} catch (e)
		{
		}
		$(document).ready(function () {
			var onoff = true;
			$(".nav_more").click(function () {
				if (onoff) {
					$(".nav").css("display", "block");
					$(".nav_more_menu").css("top", "2px");
					onoff = false;
				} else {
					$(".nav").css("display", "none");
					$(".nav_more_menu").css("top", "-5px");
					onoff = true;
				}

			})
		});
        
        //提现
        function get_user_identity(event, _this){
            event.preventDefault();
            var ajax_url = '<?=Yf_Registry::get('url').'?ctl=Info&met=getUserInfo&typ=json'?>';
            var user_id = $.cookie('id');
            $.ajax({
                url: ajax_url,
                success:function(result){
                    console.log(result);
                    if(result.status == 200)
                    {
                        if(typeof(result.data['user_id']) == 'undefined'){
                            var notice = '<?=__('请刷新后重试 或者 重新登录')?>';
                        }else if(result.data.user_identity_statu == 2){
                            return window.location.href = _this.href;
                        }else if(result.data.user_identity_statu == 0){
                            var notice = '<?=__('您还未实名认证')?>';
                        }else{
                            var notice = '<?=__('您还未实名认证成功')?>';
                        }
                    } else {
                        var notice = '<?=__('网络错误')?>';
                    }
                    $.dialog({
                        title: '<?=__('提示')?>',
                        content: notice,
                        height: 100,
                        width: 410,
                        lock: true,
                        drag: false,
                        ok: function () {
                            window.location.href = '<?=Yf_Registry::get('url').'?ctl=Info&met=account&typ=e'?>';

                            // window.location.href = UCENTER_URL + '?ctl=User&met=security';
                        }
                    })
                }
            });
        }
	</script>
    <?php
        include $this->view->getTplPath() . '/' . 'translatejs.php';
    ?>
</head>
<body>
<div class="hd_content">
	<div class="head_nav clearfix">
		<div class="wrap">
			<div class="fl user_welcome tc cf">
				<a href="#" style="float:left;"><?=__('欢迎您，')?></a>
                <?php
                if (Perm::checkUserPerm()):
                ?>
				<a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=index"><?=Perm::$row['user_account']?> !</a>
				<a href="<?=Yf_Registry::get('url')?>?ctl=Login&met=loginout"><?=__('退出')?></a>
                <?php else:?>
				<a href="<?=Yf_Registry::get('url')?>?ctl=Login&met=reg"><?=__('注册')?></a>
				<a href="<?=Yf_Registry::get('url')?>?ctl=Login&met=login"><?=__('登录')?></a>
				<?php endif;?>
			</div>
			<div class="fl go_back_shop">
				<?php if(Yf_Utils_Device::isMobile())
				{
                    if(Yf_Utils_Device::isMark())
                    {
                        $shop_url = Yf_Registry::get('shop_app_url');
                    }else{
                        $shop_url = Yf_Registry::get('shop_wap_url');
                    }
				}
				else
				{
					$shop_url = Yf_Registry::get('shop_api_url');
				}?>
				<a href="<?= $shop_url ?>"><?=__('返回商城')?></a>
			</div>
			<div class="nav_more"><?=__('更多')?><span class="nav_menu_icon"><i class="nav_more_menu"></i></span></div>
			<ul class="nav fr">
				<li><a href="<?=Yf_Registry::get('ucenter_api_url')?>?ctl=User&met=getUserInfo"><?=__('资料设置')?></a></li>
				<li><a href="<?=Yf_Registry::get('ucenter_api_url')?>?ctl=User&met=passwd"><?=__('修改密码')?></a></li>
				
			</ul>
		</div>
	</div>
	<div class="wrap">
		<div class="header wrap clearfix">
			
			<div class="header_nav clearfix">
				<ul class="pc_lf fl">
					<li><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=index"><?=__('支付首页')?></a></li>
					<li><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=recordlist"><?=__('交易查询')?></a></li>
					<li><a href="<?= Yf_Registry::get('url') ?>?ctl=Info&met=account"><?=__('账户安全')?></a></li>
				</ul>
				<div class="logo"><img src="<?=Web_ConfigModel::value('site_logo')?>"></div>
				<ul class="pc_rg fr">
					<?php if(Web_ConfigModel::value('yunshan_status') != 1){?>
						<li><a onclick="get_user_identity(event, this)" href="<?=Yf_Registry::get('url')?>?ctl=Info&met=deposit"><?=__('账户充值')?></a></li>
						<li><a onclick="get_user_identity(event, this)" href="<?= Yf_Registry::get('url') ?>?ctl=Info&met=transfer"><?=__('好友转账')?></a></li>
	                    <li><a onclick="get_user_identity(event, this)" href="<?=Yf_Registry::get('url').'?ctl=Info&met=withdraw&typ=e'?>"><?=__('余额提现')?></a></li>
		                    <?php if(Payment_ChannelModel::status('baitiao') == Payment_ChannelModel::ENABLE_YES) {?>
		                    <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Info&met=btinfo"><?=__('白条')?></a></li>
		                    <?php } ?>
                     <?php } ?>	
				</ul>
			</div>
		</div>
	</div>
</div>
