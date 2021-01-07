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
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_q27xk278xie.css">
</head>
<body>
	<header id="header" class="posf">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a class="js-cancel-push" href="javascript:history.back(-1);"> <i class="back"></i> </a> -->
            </div>
            <div class="tit">购买PLUS会员</div>
        </div>
    </header>
    <div class="nctouch-main-layout">
    	<div class="plus-buy-box pl-20 pr-20">
    		<h3 class="plus-buy-tit tc">PLUS会员购买</h3>
    		<div class="plus-buy-content" id="plus-open"></div>
    	</div>
    </div>

    <script type="text/html" id="plus-open_item">
        <dl>
            <dt>开通套餐</dt>
            <dd>PLUS会员</dd>
        </dl>
        <dl>
            <dt>付费模式</dt>
            <dd><%=plus_shopping_mode%></dd>
        </dl>
        <dl>
            <dt>应付金额</dt>
            <dd><strong class="plus-buy-price">¥<%=plus_shopping_price%></strong></dd>
        </dl>
        <div class="plus-buy-operate">
            <%if(user_identity_statu!=2){%>
              <p class="plus-buy-tips"><i class="iconfont icon-Prompt"></i><em>完成实名认证才可购买PLUS会员</em></p>
            <%}%>
            <div class="tc">
                <%if(user_identity_statu==0 || user_identity_statu==NULL){%>
                  <a class="plus-btn-buy" href="plus_certification.php">先实名 再购买</a>
                <%}else if(user_identity_statu==1){%>
                  <a class="plus-btn-buy" href="javascript:;">实名认证审核中…</a>
                <%}else if(user_identity_statu==3){%>
                  <a class="plus-btn-buy" href="plus_certification.php">审核未通过，重新实名认证</a>
                <%}else{%>
                <a class="plus-btn-buy plus-bottom-btn-open" href="javascript:;">立即支付</a>
                <%}%>
                <p class="tc plus-buy-agreement-tips"><em>购买即视为同意</em><a href="plus_argeement.php">《PLUS会员-用户协议》</a></p>
            </div>
        </div>
    </script>

</body>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript" src="../../js/ncscroll-load-plus.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</html>
<?php 
  include __DIR__.'/../../includes/footer.php';
?>
<script type="text/javascript">
    $(function () {
        var t = getCookie("key");
        if (!t) {
            window.location.href = WapSiteUrl + "/tmpl/member/login.html";
            return false;
        }
        var i = new ncScrollLoad;
        i.loadInit({
            url: ApiUrl + "/index.php?ctl=Plus_User&met=open&typ=json",
            getparam: {k: t, u: getCookie("id")},
            tmplid: "plus-open_item",
            containerobj: $("#plus-open"),
            iIntervalId: true,
            data: {WapSiteUrl: WapSiteUrl}
        });
        //点击开通试用plus会员
        $(document).on("click",".plus-bottom-btn-open",function(){
            $.ajax({
                type: 'post',
                url: ApiUrl + '/index.php?ctl=Plus_User&met=createPlusOrder&typ=json',
                data: {k: t,u:getCookie('id')},
                dataType: 'json',
                async: false,
                success:function(data){
                    if(data.status == 200)
                    {
                        window.location.href = PayCenterWapUrl+"?ctl=Info&met=pay&uorder=" + data.data.uorder+'&order_g_type=physical';
                        return false;
                    } else {
                        //提示框
	                   $.sDialog({
	                        skin: "green",
	                        content: "订单提交失败！",
	                        okBtn: false, 
	                        cancelBtn: false
	                    });
                        return false;
                    }
                },
                failure:function(data)
                {
                    $.sDialog({
	                        skin: "green",
	                        content: "操作失败！",
	                        okBtn: false, 
	                        cancelBtn: false
	                   });
                }
            });
        });
    });
</script>