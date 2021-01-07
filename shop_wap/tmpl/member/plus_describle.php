<?php 
include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="../../css/base.css">
	<link rel="stylesheet" href="../../css/plus.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_tb64sl5akxc.css">
</head>
<body>
	<header id="header" class="posf">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a class="js-cancel-push" href="javascript:history.back(-1);"> <i class="back"></i> </a> -->
            </div>
            <div class="tit">PLUS 会员权益</div>
        </div>
    </header>
    <div class="nctouch-main-layout">
    	<div class="plus-powers-box pl-20 pr-20">
    	    <dl id="pot1">
	    		<dt><i class="iconfont icon-plus-huiyuan "></i><span>会员专享价</span> </dt>
	    		<dd>
	    			<p>开通PLUS会员，即可尊享商城商品价格 <i></i>%的折扣！</p>
	    			<p>PLUS会员除享受超低会员价以外，还可同时与其他优惠叠加使用，例如，店铺优惠券。</p>
	    		</dd>
	    	</dl>
	    	<dl id="pot2">
	    		<dt><i class="iconfont icon-plus-jifen"></i><span>积分加倍</span> </dt>
	    		<dd>
	    			<p>PLUS会员在商城购买符合活动范围的商品，每个订单所累计的积分将直接翻 <i></i> 倍</p>
	    		</dd>
	    	</dl>
	    	<dl id="pot3">
	    		<dt><i class="iconfont icon-plus-huiyuanri"></i><span>超级会员日</span> </dt>
	    		<dd>
	    			<p>每年双11，为超级会员日。PLUS会员将获得<i></i>元无门槛平台红包，使用期限为领取后的7天之内</p>
	    		</dd>
	    	</dl>
	    	<dl id="pot4">
	    		<dt><i class="iconfont icon-plus-hongbao"></i><span>全品类平台红包</span> </dt>
	    		<dd>
	    			<p>开通PLUS会员即送满<i></i>元减<b></b>元平台红包，使用期限为领取后的一个月之内，每月送1张。</p>
	    		</dd>
	    	</dl>
	    	<dl id="pot5">
	    		<dt><i class="iconfont icon-plus-fuwu"></i><span>24小时客服服务</span> </dt>
	    		<dd>
	    			<p>PLUS会员用户享受24小时客服服务。优先解答您问题</p>
	    		</dd>
	    	</dl>
    	</div>
    </div>
	    <script type="text/javascript" src="../../js/zepto.min.js"></script>
		<script type="text/javascript" src="../../js/template.js"></script>
		<script type="text/javascript" src="../../js/common.js"></script>
		<script type="text/javascript" src="../../js/simple-plugin.js"></script>
		<script type="text/javascript" src="../../js/ncscroll-load.js"></script>
		<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</body>
</html>
<?php
    include __DIR__ . '/../../includes/footer.php';
?>
<script type="text/javascript">
    $(function () {
        var t = getCookie("key");
        if (!t) {
            window.location.href = WapSiteUrl + "/tmpl/member/login.html";
            return false;
        }
        $.ajax({
                type: 'post',
                url: ApiUrl + '/index.php?ctl=Plus_User&met=index&typ=json',
                data: {k: t,u:getCookie('id')},
                dataType: 'json',
                async: false,
                success: function(result) {
                    if (result.status == 200) {
                        //开通试用代码
                        $('#pot1 dd i').text(result.data.plus_rate);
                        $('#pot2 dd i').text(result.data.plus_integral);
                        $('#pot3 dd i').text(result.data.plus_general_red);
                        $('#pot4 dd i').text(result.data.plus_red_packet);
                        $('#pot4 dd b').text(result.data.plus_red_packet);
                    }
                }
            });
    });
</script>