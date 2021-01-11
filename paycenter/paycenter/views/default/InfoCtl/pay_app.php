<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=Web_ConfigModel::value('site_name')?> - <?=Web_ConfigModel::value('title')?></title>
    <meta name="description" content="<?=Web_ConfigModel::value('description')?>" />
    <meta name="Keywords" content="<?=Web_ConfigModel::value('keyword')?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/base.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/tips.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/headfoot.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/iconfont/iconfont.css"> -->
    <link rel="stylesheet" href="http://at.alicdn.com/t/font_ucm2vzrmvdfjq0k9.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/palyCenter.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/dialog/green.css">
    <link href="<?= $this->view->css ?>/validator/jquery.validator.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
    <script src="<?=$this->view->js?>/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="<?=$this->view->js?>/jquery.cookie.js"></script>
    <script src="<?=$this->view->js?>/respond.js"></script>
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
        var SHOP_WAP_URL = "<?=Yf_Registry::get('shop_wap_url')?>";
        var DOMAIN = document.domain;
        var WDURL = "";
        var SCHEME = "default";
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
    </script>
</head>
<!DOCTYPE html>
<html lang="en" style="font-size: 50px;" class="pixel-ratio-2 retina ios ios-11 ios-11-0 ios-gt-10 ios-gt-9 ios-gt-8 ios-gt-7 ios-gt-6"><head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit=cover;">
    <head>

    <title>确认付款</title>
    <script src="<?= $this->view->css ?>/css/jquery.js"></script>
    <link rel="stylesheet" href="<?= $this->view->css ?>/css/base.css">
    <style type="text/css">
            .active ,.show {display:inline-block;}
            .actived{color:red;}
            [v-cloak] {
                  display: none;
                }
                .code-img{width:100px;height: 35px;}
                .money{
                    margin-top: 57px;
                    float: right;
                    margin-right: 15px;
                    font-family: PingFangSC-Regular;
                    font-size: 14px;
                    color: #212121;
                    letter-spacing: 0;
                }
                .money_class{
                    color: #ff3333;
                }
                .img{
                    margin-top:14px;
                    /* height: 8px; */
                    width: 11px;
                }
                .h3{
                    font-family: PingFangSC-Medium;
                    font-size: 16px!important;
                    color: #000000;
                    letter-spacing: -0.09px;
                }
                .img_pay{
                    width: 26px;
                    height: 27.1px;
                    vertical-align: middle;
                }
                .yinlian{
                    width: 35px;
                    height: 23.6px;
                    margin-left: 7px;
                }
                .tt{
                    vertical-align: middle;
                }
                .tuijian{
                    color: #DD2423;
                    margin-left: 22px;
                }
                .submit_able{
                    font-family: PingFangSC-Medium;
                    font-size: 18px;
                    color: #FFFFFF;
                    letter-spacing: -0.12px;
                }
                .hd_content{
                    display: none;
                }
    </style>
    <script>
        var BASE_URL = "<?=Yf_Registry::get('base_url')?>";
        var SITE_URL = "<?=Yf_Registry::get('url')?>";
        var INDEX_PAGE = "<?=Yf_Registry::get('index_page')?>";
        var STATIC_URL = "<?=Yf_Registry::get('static_url')?>";
        var U_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";
        var SHOP_URL = "<?=Yf_Registry::get('shop_api_url')?>";
        var UCENTER_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";
        var SHOP_WAP_URL = "<?=Yf_Registry::get('shop_wap_url')?>";
        var DOMAIN = document.domain;
        var WDURL = "";
        var SCHEME = "default";
    </script>
</head>
<body>
<div id="barba-wrapper" style="width: 100%!important;" class="hp100 wrap">
    <div class="barba-container hp100">

   	    <div id="recharge">
            <div class="common-head bg1 tc">
                <a href="https://m.njjbsm.com/tmpl/member/order_list.html?data-state=wait_pay" class="fl">
                    <img src="<?= $this->view->css ?>/css/Back@3x.png" class="img" alt="">
                </a> 
                <h3 class="h3">确认付款</h3> 
                <a href="javascript:;" class="fr option">
                    <i class="iconfont"></i>
                </a>
            </div>
            <div class="money">
                <span>需支付：<span class="money_class"><?= $uorder_base['trade_payment_amount'] ?>元</span></span>
            </div>
            <div class="hp100 bgf8 common-content">
                <div class="module-dep">
                    <ul class="payment-list">
                        <input type="hidden" id="now_price" value="<?= $uorder_base['trade_payment_amount'] ?>">
                            <li class="li-radio-row clearfix">
                                <div class="img-area tt">
                                    <img class="img_pay iconfont icon-wechat" src="<?= $this->view->css ?>/css/Page1 Copy@2x.png" alt="云闪付支付">
                                </div> 
                                <input type="hidden" name="payway_name" class="payway_name" value="yunshan_app"> 
                                <div class="text-area tt">
                                    <a href="javascript:;" style="font-family: PingFangSC-Regular;
                                        font-size: 14px;
                                        color: #212121;
                                        letter-spacing: 0;">云闪付支付</a>
                                </div> 
                                 <div class="tt">
                                    <img class="tt yinlian iconfont icon-wechat" src="<?= $this->view->css ?>/css/Page 1@3x.png" >
                                    <span class="tuijian">【推荐】</span>
                                </div> 
                                <div class="radio-area fr">
                                    <input name="payment-way" type="radio" class="style-input" value="yunshan_app" checked>
                                </div>
                              <input type="hidden" name="payway_namess" class="payway_namess" value="yunshan_app">

                             </li>
                             
                        <li class="li-radio-row clearfix">
                            <div class="img-area tt">
                                <img class="img_pay iconfont icon-wechat" src="<?= $this->view->css ?>/css/支付宝 copy@3x.png" alt="">
                            </div> 
                            <div class="text-area tt">
                                <a href="javascript:;" style="font-family: PingFangSC-Regular;
                                        font-size: 14px;
                                        color: #212121;
                                        letter-spacing: 0;">支付宝支付</a>
                            </div>
                            <div class="radio-area fr tt">
                                <input name="payment-way" type="radio" class="style-input" value="alipay">
                            </div>
                        </li> 
                        <li class="li-radio-row clearfix">
                            <div class="img-area tt">
                                <img class="img_pay iconfont icon-wechat" src="<?= $this->view->css ?>/css/微信 copy@3x.png" alt="">
                            </div>
                            <div class="text-area tt">
                                <a href="javascript:;" style="font-family: PingFangSC-Regular;
                                    font-size: 14px;
                                    color: #212121;
                                    letter-spacing: 0;">微信支付</a>
                            </div> 
                            <div class="radio-area fr tt">
                                <input name="payment-way" type="radio" class="style-input" value="wx_native">
                            </div>
                        </li>
                        <input type="hidden" name="payway_type" class="payway_type" id="payway_type" value="yunshan_app">
                        <input type="hidden" name="online_payway" class="online_payway" id="online_payway" value="yunshan_app">
                    </ul> 
            <div class="tc" style="position: fixed;bottom: 2rem;width: 100%;"><a style="border: 1px solid transparent!important;height: 1.2rem;" class="btn-common btn_big btn_active submit_able" id="submit">云闪付交易</a></div>
        </div>
    </div>
</div>
    </div>
</div>
</body>


<script>
    function getCookie(name){
        var strcookie=document.cookie;
        var arrcookie=strcookie.split("; ");
        for(var i=0;i<arrcookie.length;i++){
        var arr=arrcookie[i].split("=");
        if(arr[0]==name)return unescape(arr[1]);
        }
        return null;
    }
    $(function(){
        getpirce();
        function getpirce(){
            var now_price = $("#now_price").val();
            $(".btn-common").text("云闪付支付："+now_price+"元");
        }
        $(".style-input").click(function(){
            var type = $(this).val();
            var now_price = $("#now_price").val();
            $("#payway_type").val(type);
            $("#online_payway").val("yunshan_app");
            if(type=='yunshan_app'){
                $(".btn-common").text("云闪付支付："+now_price+"元");
            }else if(type=='wx_native'){
                $(".btn-common").text("微信支付："+now_price+"元");
            }else if(type=='alipay'){
                $(".btn-common").text("支付宝支付："+now_price+"元");
            }
        })
    })
</script>

<script>
  $("input[type='checkbox']").prop('checked', false);
  $("#submit").click(function () {
    var uorder_id = '<?=($uorder)?>';
    var that = this;
    $.post(SITE_URL + "?ctl=Pay&met=checkAvailableMoney&typ=json", {trade_id: uorder_id}, function (data) {
      if (data.status == 200) {
        availableChecked(that);
      }else{
        Public.tips.alert('该商品暂时无法购买，请联系客服');
      }
    });
  });
  function availableChecked(obj) {
      paySubmit($(obj));
  }
  function paySubmit(e) {
    if (e.hasClass("submit_able")) {
      $("body").css("overflow", "hidden");
      $("#mask_box").show();
      var uorder_id = '<?=($uorder)?>';
      data = {trade_id: uorder_id};
      var card_payway = $("input[type='checkbox'][value='cards']").is(':checked');
      var money_payway = $("input[type='checkbox'][value='money']").is(':checked');
      var bt_payway = $("input[type='checkbox'][value='bt']").is(':checked');
      var online_payway = $("input[type='hidden'][name='online_payway']").val();
      var payway_type = $("input[type='hidden'][name='payway_type']").val();
      setTimeout(function () {
        $("#mask_box").hide();
      }, 3000);
      //将选用的付款方式保存如数据库
      data = {card_payway: card_payway, money_payway: money_payway, bt_payway: bt_payway, online_payway: online_payway, uorder_id: uorder_id};
      $.post(SITE_URL + "?ctl=Info&met=checkPayWay&typ=json", data, function (data) {
            if (data.status == 200) {
              var is_app = 1; 
              if (typeof YongLian == 'undefined' || YongLian == null || YongLian == '') {
                is_app = 0;
              }
              //如果选择了在线支付方式
              window.location.href = SITE_URL + "?ctl=Pay&met=yunshanpc&payway_type="+payway_type + "&trade_id=" + uorder_id + "&is_app=" + is_app;
            } else {
              Public.tips.error('支付失败');
            }
          }
      ).error(function () {
        $("#mask_box").hide();
        Public.tips.error('网络连接失败：001');
      });
    }
  }
</script>

<script type="application/javascript">
  //如果当前是手机端微信浏览器，隐藏支付宝
  $(function () {
    
    var $alipay = $("[name=\"payway_name\"][value=\"alipay\"]");
    if ($alipay.get(0)) {
      var ua = navigator.userAgent.toLowerCase();
      
      if (ua.match(/MicroMessenger/i) == "micromessenger") {
        var alipayOption = $alipay.parents("div:eq(1)");
        $(alipayOption).hide();
        
        $("input[type='hidden'][name='online_payway']").val('wx_native');
        $("input[type='hidden'][value='wx_native']").parents('div').addClass('pay_method_sel');
      }
    }
    
    $("form").on('submit', function (e) {
      e.preventDefault();
      $("#submit").trigger("click");
    });
    function notify_url(){
        location.href=SHOP_URL+"?ctl=Buyer_Order&met=physical";
    }
  });
</script>
<?php
    include $this -> view -> getTplPath() . '/' . 'footer.php';
?>