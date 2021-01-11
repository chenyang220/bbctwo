<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>

<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link href="<?=$this->view->css?>/skin_0.css" rel="stylesheet" type="text/css">
<style>
    html,body{height:100%;}
</style>
<script src="<?=$this->view->js_com?>/template.js"></script>
</head>
<body class="<?=$skin?>">
    <div id="oo"></div>
    <div class="bbccontent hp100">
        <div class="wrapper page relative minhp100">
           
            <div class="info-panel mainIndex_url clearfix pb60">
                <dl class="member">
                    <dt>
                        <div class="ico"><i></i><sub title="<?= __('会员总数'); ?>"><span><em id="statistics_member">0</em></span></sub></div>
                        <h3><?= __('会员'); ?></h3>
                        <h5><?= __('新增会员'); ?></h5>
                    </dt>
                    <dd>
                        <ul>
                            <li class="w50pre normal"><a href="index.php?ctl=User_Info&met=info"><span><?= __('本周新增'); ?></span><sub><em id="statistics_week_add_member">0</em></sub></a></li>
                             <li class="w50pre normal"><a href="index.php?ctl=User_Info&met=info"><span><?= __('本月新增'); ?></span><sub><em id="statistics_month_add_member">0</em></sub></a></li>
                            <!--<li class="w50pre normal"><a href=""><?= __('预存款提现'); ?><sub><em id="statistics_cashlist">0</em></sub></a></li>-->
                        </ul>
                    </dd>
                </dl>
                
                <dl class="shop">
                    <dt>
                        <div class="ico"><i></i><sub title="<?= __('新增店铺数'); ?>"><span><em id="statistics_store">0</em></span></sub></div>
                        <h3><?= __('店铺'); ?></h3>
                        <h5><?= __('新开店铺审核'); ?></h5>
                    </dt>
                    <dd>
                        <ul>
                            <li class="w20pre normal"><a href="index.php?ctl=Shop_Manage&met=join"><span><?= __('开店审核'); ?></span><sub><em id="statistics_store_joinin">0</em></sub></a></li>
                            <li class="w20pre normal"><a href="index.php?ctl=Shop_Manage&met=category"><span><?= __('类目申请'); ?></span><sub><em id="statistics_store_bind_class_applay">0</em></sub></a></li>
                            <li class="w20pre normal"><a href="index.php?ctl=Shop_Manage&met=reopen"><span><?= __('续签申请'); ?></span><sub><em id="statistics_store_reopen_applay">0</em></sub></a></li>
                            <li class="w20pre normal"><a href="index.php?ctl=Shop_Manage&met=indexs"><span><?= __('已到期'); ?></span><sub><em id="statistics_store_expired">0</em></sub></a></li>
                            <li class="w20pre normal"><a href="index.php?ctl=Shop_Manage&met=indexs"><span><?= __('即将到期'); ?></span><sub><em id="statistics_store_expire">0</em></sub></a></li>
                        </ul>
                    </dd>
                </dl>
                
                <dl class="goods">
                    <dt>
                        <div class="ico"><i></i><sub title="<?= __('商品总数'); ?>"><span><em id="statistics_goods">0</em></span></sub></div>
                        <h3><?= __('商品'); ?></h3>
                        <h5><?= __('新增商品'); ?>/<?= __('品牌申请审核'); ?></h5>
                    </dt>
                    <dd>
                        <ul>
                            <li class="w25pre normal"><a href="index.php?ctl=Goods_Goods&met=common"><span><?= __('本周新增'); ?></span><sub title=""><em id="statistics_week_add_product">0</em></sub></a></li>
                            <li class="w25pre normal"><a href="index.php?ctl=Goods_Goods&met=common&common_verify=10"><span><?= __('商品审核'); ?></span><sub><em id="statistics_product_verify">0</em></sub></a></li>
                            <li class="w25pre normal"><a href="index.php?ctl=Trade_Report&met=baseDo"><span><?= __('举报'); ?></span><sub><em id="statistics_inform_list">0</em></sub></a></li>
                            <li class="w25pre normal"><a href="index.php?ctl=Goods_Brand&met=brand"><span><?= __('品牌管理'); ?></span><sub><em id="statistics_brand_apply">0</em></sub></a></li>
                        </ul>
                    </dd>
                </dl>
                <dl class="trade">
                    <dt>
                        <div class="ico"><i></i><sub title="<?= __('订单总数'); ?>"><span><em id="statistics_order">0</em></span></sub></div>
                        <h3><?= __('交易'); ?></h3>
                        <h5><?= __('交易订单及投诉'); ?>/<?= __('举报'); ?></h5>
                    </dt>
                    <dd>
                        <ul>
                            <li class="w18pre normal"><a href="index.php?ctl=Trade_Return&met=refundWait&otyp=1"><span><?= __('退款'); ?></span><sub><em id="statistics_refund">0</em></sub></a></li>
                            <li class="w18pre normal"><a href="index.php?ctl=Trade_Return&met=refundWait&otyp=2"><span><?= __('退货'); ?></span><sub><em id="statistics_return">0</em></sub></a></li>
                            <li class="w25pre normal"><a href="index.php?ctl=Trade_Return&met=refundWait&otyp=3"><span><?= __('虚拟订单退款'); ?></span><sub><em id="statistics_vr_refund">0</em></sub></a></li>
                            <li class="w18pre normal"><a href="index.php?ctl=Trade_Complain&met=complain&state=1"><span><?= __('投诉'); ?></span><sub><em id="statistics_complain_new_list">0</em></sub></a></li>
                             <li class="w20pre normal"><a href="index.php?ctl=Trade_Complain&met=complain&state=4"><span><?= __('待仲裁'); ?></span><sub><em id="statistics_complain_handle_list">0</em></sub></a></li>
                        </ul>
                    </dd>
                </dl>
                <dl class="operation">
                    <dt>
                        <div class="ico"><i></i></div>
                        <h3><?= __('运营'); ?></h3>
                        <h5><?= __('系统运营类设置及审核'); ?></h5>
                    </dt>
                    <dd>
                        <ul>
                            <li class="w15pre none"><a href="?ctl=Config&met=operation&config_type%5B%5D=operation"><span><?= __('设置'); ?></span><sub><em id="statistics_groupbuy_verify_list">0</em></sub></a></li>
                            <li class="w17pre none"><a href="?ctl=Operation_Settlement&met=settlement"><span><?= __('结算管理'); ?></span><sub><em id="statistics_points_order">0</em></sub></a></li>
                            <li class="w17pre none"><a href="?ctl=Operation_Settlement&met=settlement&otyp=1"><span><?= __('虚拟订单'); ?></span><sub><em id="statistics_check_billno">0</em></sub></a></li>
                            <li class="w17pre none"><a href="?ctl=Operation_Custom&met=custom"><span><?= __('平台客服'); ?></span><sub><em id="statistics_pay_billno">0</em></sub></a></li>
                            <!--<li class="w17pre none"><a href="?ctl=Operation_Delivery&met=delivery"><span><?= __('物流自提'); ?></span><sub><em id="statistics_mall_consult">0</em></sub></a></li>-->
                            <li class="w34pre none"><a href="?ctl=Operation_Contract&met=log"><span><?= __('消费者保障服务'); ?></span><sub><em id="statistics_delivery_point">0</em></sub></a></li>
                        </ul>
                    </dd>
                </dl>
                <!--
                <dl class="system">
                    <dt>
                        <div class="ico"><i></i></div>
                        <h3>BBCBuilder</h3>
                        <h5><?= __('远丰商城系统'); ?></h5>
                    </dt>
                    <dd>
                        <ul>
                            <li class="w50pre none"><a href="https://www.yuanfeng.cn/" target="_blank"><?= __('官方网站'); ?><sub></sub></a></li>
                            <li class="w50pre none"><a href="https://www.yuanfengtest.com/" target="_blank"><?= __('官方演示站'); ?><sub></sub></a></li>
                        </ul>
                    </dd>
                </dl>
                -->
                <div class="clear"></div>
                <div class="system-info"></div>
            </div>
            <div class="main-index tc">
                <p>Copyright © 2005-<?= date("Y")?> <a href="javascript:;">远丰电商集团</a>. All rights reserved.</p>
                <p>BBC商城 Version <b id="shop_version_id" style="color:red;font-size:14px;"></b></p>
            </div>
        </div>
    </div>
<script>

  



        window.onload = function () { 


  function getBrowser(n) {  
          var ua = navigator.userAgent.toLowerCase(),  
              s,  
              name = '',  
              ver = 0;  
          //探测浏览器
          (s = ua.match(/msie ([\d.]+)/)) ? _set("ie", _toFixedVersion(s[1])):  
          (s = ua.match(/firefox\/([\d.]+)/)) ? _set("firefox", _toFixedVersion(s[1])) :  
          (s = ua.match(/chrome\/([\d.]+)/)) ? _set("chrome", _toFixedVersion(s[1])) :  
          (s = ua.match(/opera.([\d.]+)/)) ? _set("opera", _toFixedVersion(s[1])) :  
          (s = ua.match(/version\/([\d.]+).*safari/)) ? _set("safari", _toFixedVersion(s[1])) : 0;  
          
          function _toFixedVersion(ver, floatLength) {  
            ver = ('' + ver).replace(/_/g, '.');  
            floatLength = floatLength || 1;  
            ver = String(ver).split('.');  
            ver = ver[0] + '.' + (ver[1] || '0');  
            ver = Number(ver).toFixed(floatLength);  
            return ver;  
          }  
          function _set(bname, bver) {  
            name = bname;  
            ver = bver;  
          }  
          return (n == 'n' ? name : (n == 'v' ? ver : name + ver));  
};  
  
        var neihe = getBrowser("n"); // 所获得的就是浏览器所用内核。
        var banben = getBrowser("v");// 所获得的就是浏览器的版本号。
         var browser = getBrowser();// 所获得的就是浏览器内核加版本号。
         var banben_number = Number(banben);
             //application/vnd.chromium.remoting-viewer 可能为360特有
             var is360 = _mime("type", "application/vnd.chromium.remoting-viewer");
             if (isChrome() && is360 && banben_number < 63.0) { 
                 parent.$.dialog.confirm('页面如有乱码请升级360浏览器', function () {
                         return banben;
                 });
             }
         }
          //测试mime
         function _mime(option, value) {
             var mimeTypes = navigator.mimeTypes;
             for (var mt in mimeTypes) {
                 if (mimeTypes[mt][option] == value) {
                     return true;
                 }
             }
             return false;
         }
         //检测是否是谷歌内核(可排除360及谷歌以外的浏览器)
         function isChrome(){
             var ua = navigator.userAgent.toLowerCase();
             return ua.indexOf("chrome") > 1;
         }


$('.mainIndex_url a').click(function(){
	var aurl = $(this).attr('href');
	var text = $(this).find('span').html();
	var target = $(this).attr('target');
	if (target == '_blank')
	{
		return true;
	}else{
		parent.tab.addTabItem({
			text:text,
			url: SITE_URL + '/' +aurl
		});
	}
	return false;
});

$(document).ready(function(){
	//获取商城版本号 @nsy 2019-10-14
	var url ='<?= Yf_Registry::get('shop_api_url');?>';
	$.post(url + "?ctl=Common&typ=json&met=shopVersion",{ts: Date.now()},function(data){
		if (200==data.status) {
			$('#shop_version_id').html(data.data.version);
		}else{
			$('#shop_version_id').html('3.0.0');
		}
	});
});
</script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/mainIndex.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>