<?php
include __DIR__ . '/../../includes/header.php';
?>
<!DOCTYPE html>
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
    <meta name="wap-font-scale" content="no">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('提现'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    <link rel="stylesheet" href="../../css/iconfont.css">
</head>
<body>
	<header id="header" class="fixed bgf">
	    <div class="header-wrap">
	        <!-- <div class="header-l absolute"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col3 fz-40"></b></a></div> -->
	        <div class="header-title posr">
	            <h1 id="z-tab-order" data-order_state="all">提现</h1>
	        </div>
	        <div class="header-r absolute right0"><a class="fz-28 col6 wauto" id="header-nav" href="withdraw_log.html">提现记录</a></div>
	    </div>
	</header>
	<div class="nctouch-main-layout">
		<div class="withdrawal bgf">
			<span>提现金额</span>
			<div class="write_po">
				<span>￥</span>
				<input name="withdraw_amount" value="" type="Number">
			</div>
			<p><span class="col3 mr-10 fz-24">可提现余额<i class="user_commission">0.00</i>元，</span><a class="fz-24 col9" href="frozen_fund_details.php">冻结金额30.02元<i class="iconfont icon-arrow-right fz-24"></i></a><b id="fundQues" class="iconfont icon-wenhao ml-10 align-middle"></b></p>
		</div>
		<div class="biao bgf">
			<span class="red">*</span>
			<span>可将佣金直接提现至您的账户金额</span>
		</div>
		<button class="btn-l butt_withdraw bor0 mt20">确认提现</button>
		<!-- tips -->
		<div class="dialog-text-tips">
		  <h4 class="mb10 block">冻结金额说明</h4>
		  <p class="text-cont">推广订单过退款退货期时，冻结金额自动转为可提现金额</p>
		  <i class="iconfont icon-close js-close-dialog"></i>
		</div>
	</div>
</body>
</html>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript">
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getUserCommission&typ=json",
            data: {k: getCookie('key'),u: getCookie('id')},
            dataType: "json",
            async:false,
            success: function (r) {
                    if(r.status==200){
                            $(".user_commission").text(r.data.user_directseller_commission);
                    }   
            }
        });

    var flag = true;
    $(document).on('click', '.butt_withdraw', function () {
        var amount = $('input[name="withdraw_amount"]').val();
        if (isNaN(amount)) {
            alert('请填写正确的数字');
            return false;
        }
        if (Number(amount) <= 0) {
            alert("提现金额不得小于或等于0");
            return false;
        }
        if (flag == false) {
            return false;
        }
        flag = false;

        var commission = 0.00;
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getUserCommission&typ=json",
            data: {k: getCookie('key'),u: getCookie('id')},
            dataType: "json",
            async:false,
            success: function (r) {
                if(r.status==200){
                    commission = r.data.user_directseller_commission;
                }
            }
        });
        
        if (Number(amount) <= Number(commission) && Number(amount) > 0) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=withdraw&typ=json",
                data: {k: getCookie('key'), u: getCookie('id'), amount: amount},
                dataType: "json",
                async:false,
                success: function (r) {
                    if (200 == r.status) {
                        flag = true;
                        alert('提现成功');
                        window.location.reload();
                    } else {
                        flag = false;
                        alert('操作失败，请重试');
                    }
                }
            });
        } else {
            alert("提现金额不能高于当前余额！");
        }
    });
	$("#fundQues").click(function(){
		$(".dialog-text-tips").addClass("active");
	})
	$(".js-close-dialog").click(function(){
		$(".dialog-text-tips").removeClass("active");
	})
</script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>