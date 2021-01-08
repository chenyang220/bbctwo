<?php 
include __DIR__.'/../../includes/header.php';
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
	<title>二维码扫描</title>
	<link rel="stylesheet" type="text/css" href="../../css/base.css">
	<link rel="stylesheet" href="../../css/iconfont.css">
		<style>
			html,body{
				width: 100%;
				height: 100%; 
			}
		     .brand{
				 width: 100%;
				 height: 100%;
				 padding-top: 9.2rem;
				 background: url(../../images/Group%203.png) no-repeat;
				 background-size: 100% 100%;
				   
			 }
			 .brand-t{
				 width: 298px;
				 height: 411px;
				 margin-top: 2rem;
				 margin: auto;
				 background: url(../../images/Group%205.png) no-repeat;
				 background-size: 100% 100%;
			 }
			.brand_one{
				padding: 0.5rem;
			}
			.brand_one img{
				display: block;
				margin: auto;
			    width: 52px;
				height: 52px;
				border-radius: 50%;
			}
			.brand_one span{
				font-size:14px;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(74,74,74,1);
				line-height: 2.125rem;
				display: block;
				text-align: center;
			}
			.brand_two img{
				display: block;
				width: 174px;
				height: 174px;
				margin: auto;
				margin-top: 1.8rem;
			}
			.brand_ser{
				text-align: center;
				font-size:16px;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(155,155,155,1);
				margin-top: 1.5rem;
				padding-bottom:5rem;
			}
		</style>
		</style>
	</head>
	<script type="text/javascript">
    //用app电话号码登录
    var u_id = '<?php echo $u_id;?>';
    if (u_id) {
       window.location.href = UCenterApiUrl + '/?ctl=Login&met=oauth&typ=e&u_id=' + u_id + "&return_url=" + WapSiteUrl + "/tmpl/member/distribution_shop_share.html";
    }
</script>
	<body>
		<header id="header" class="fixed bgf">
		    <div class="header-wrap">
		        <!-- <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col9b fz-40"></b></a></div> -->
		        <div class="header-title posr">
		            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">店铺推广海报</h1>
		        </div>
		        <div class="header-r">
		        	 <?php if ($_COOKIE['is_app_guest']) { ?>
                       <a class="header-nav" data-id=""><img  src="../../images/分 享.png"></a>
                    <?php }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'UCBrowser')!==false||strpos($_SERVER['HTTP_USER_AGENT'],'UCWEB')!==false){ ?>
                        <a class="header-nav" data-id=""><img src="../../images/分 享.png"></a>
                    <?php }elseif (strpos($_SERVER['HTTP_USER_AGENT'],'MQQBrowser')!==false){ ?>
                        <a class="header-nav" data-id=""><img src="../../images/分 享.png"></a>
                    <?php }?>
		        	
		        </div>
		    </div>
		</header>
		<div class="brand">
			<div class="brand-t">
				<div class="brand_one">
					<img class="shop_logo" src="">
					<span class="heaer_name"></span>
				</div>
				<div class="brand_two" id="shareCode">
					<img id="shop_logo" src="" />
				</div>
				<div class="brand_ser">长按或扫描查看</div>
			</div>
		</div>
	</body>
</html>
<script type="text/javascript" src="../../js/tmpl/NativeShare.js"></script>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<script type="text/javascript">
	if (getQueryString('k')) {
	   $('#header').hide();
	}
	$.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getDistributionShopInfo&typ=json",
            data: {k: getCookie('key'),u: getCookie('id')},
            dataType: "json",
            success: function (r) {
            	if(r.status==200){
            		$(".shop_logo").attr("src",r.data.distribution_logo);
            		$(".heaer_name").html(r.data.distribution_name);
            		$('.header-nav').data('id',r.data.distribution_shop_id);
            		get_user_qr_img();
            		if (getCookie("is_app_guest")) {
	                        window.location.href="/share_toall.html?goods_id=&title=" + encodeURIComponent(r.data.distribution_name) + "&img=" + r.data.distribution_logo + "&url=" + WapSiteUrl + "/tmpl/member/distribution_shop_detail.html?sid=" + r.data.distribution_shop_id + "&user_id=" + getCookie("id");
	                    }else{
	                        var icon = r.data.distribution_logo;
	                        var title = r.data.distribution_name;
	                        var link = WapSiteUrl + "/tmpl/member/distribution_shop_detail.html?sid=" + r.data.distribution_shop_id;
	                        var desc = r.data.distribution_desc;
	                        var nativeShare = new NativeShare();
	                        var shareData = {
	                            title: title,
	                            desc: desc,
	                            // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
	                            link: link,
	                            icon: icon,
	                            // 不要过于依赖以下两个回调，很多浏览器是不支持的
	                            success: function () {
	                                alert("success");
	                            },
	                            fail: function () {
	                                alert("fail");
	                            }
	                        };
	                        nativeShare.setShareData(shareData);
            			}
            	$(".header-nav").click(function(){
            		try {
                            nativeShare.call();
                        } catch (err) {
                            // 如果不支持，你可以在这里做降级处理
                            alert(err.message);
                        }
            	})   
            	}
        	}
        });

	function get_user_qr_img(){
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=shareDistribution&typ=json",
            data: {k: getCookie('key'), u: getCookie('id'),uuid:getQueryString('uuid')},
            dataType: 'json',
            success: function (result) {
                if(result.status == 200)
                {
                    qrCode = result.data.qrCode;
                    $("#shareCode").find('img').attr('src',qrCode);
                }
            }
        });
    }

</script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>