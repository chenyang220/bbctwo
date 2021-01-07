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
		<title>分销中心</title>
		<link rel="stylesheet" type="text/css" href="../../css/base.css">
		<link rel="stylesheet" href="../../css/iconfont.css">
		<style>
			.account-bg {
				margin: auto;
			    width:2.8181818rem;
			    height:2.8181818rem;
			    border-radius: 100%;
			    border:0.2727rem solid rgba(255,255,255,0.2);
			    
			}
			.userinfo{
				background: url(../../images/dimges/Group%206.png) no-repeat;
				background-size: 100% 100%;
				padding-bottom: 0.3rem;
			}
			
			.mine-propery .mine-propery-item{width:50%;display:inline-block;text-align:center;position:relative;}
			.mine-propery .mine-propery-item:first-child:after{content:"";display:block;width:0.00227rem;background:#bcbcbc;position:absolute;right:0;top:0.454545rem;bottom:0.454545rem;}
			.module-tit{line-height:2rem;}
			.member-nav-header{padding:0 0.454545rem 0 0.954545rem;box-sizing:border-box;line-height:2rem;background:#fff;}
			.member-nav-header .fl{font-size:0.6363rem;color:#000;}
			.member-nav-header .fr text{display:inline-block;vertical-align:top;
			font-size:0.5454rem;color:#9b9b9b;}
			.member-services-items a{float:left;padding-top:0.681818rem;padding-bottom:0.681818rem; width:33.33333333333%;text-align:center;border-right: 1px solid #F1F1F1;border-bottom: 1px solid #F1F1F1;box-sizing: border-box;}
			.member-services-items div{display:flex;justify-content:center;align-items:center; width:100%;height:1.363636rem;}
			.member-services-items img{max-width:1.04545rem;}
			.member-services-items text{font-size:0.5454rem;color:#4a4a4a;display: block;text-align: center;padding-top: 0.2rem;}
			.qiandao{height: 1.1363636rem;line-height: 1.1363636rem;;margin-left: 4.545454rem;}
			.user_main{
				text-align: center;
				color: #FFFFFF;
			}
			.dengji{
				padding: 0rem 0.2rem;
				background:rgba(255,255,255,0.39);
				border-radius:0.3181rem;
				border:1px solid rgba(255,255,255,0.53);
			}
			.ti_xian{
				display: block;
				color: #FFFFFF;
				width:3.4090rem;
				height:1.3636rem;
				line-height:1.3636rem ;
				margin: auto;
				border-radius:0.7272rem;
				border:1px solid rgba(255,255,255,1);
			}
			.nam{
				margin-top: 0.2rem;
			}
			.separate{
				margin-top: 0.3rem;
			}
			.separate_t{
				display: flex;
				justify-content: space-between;
				border:1px solid rgba(242,242,242,1);
				background:rgba(255,255,255,1);
				padding: 0.2727rem 0;
			}
			.separate_t span{
				font-size:0.6363rem;
				line-height: 1.6rem;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(52,52,52,1);
			}
			.bn{
				padding-top: 0.43rem;
				padding-right: 0.45rem;
			}
			.tn{
				padding-right: 0.5rem;
				padding-left: 0.45rem;
			}
			.separate_t .red{
				font-size:1.0909rem;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(242,24,0,1);
			}
			.separate_t .yellow{
				font-size:1.0909rem;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(245,166,35,1);
			}
			.separate_t .blue{
				font-size:1.0909rem;
				font-family:PingFangSC-Regular,PingFang SC;
				font-weight:400;
				color:rgba(74,144,226,1);
			}
			.account-bg img{
				width: 100%;
				height: 100%;
				border-radius: 50%;
			}
		</style>
	</head>
	<script type="text/javascript">
    //用app电话号码登录
    var u_id = '<?php echo $u_id;?>';
    if (u_id) {
       window.location.href = UCenterApiUrl + '/?ctl=Login&met=oauth&typ=e&u_id=' + u_id + "&return_url=" + WapSiteUrl + "/tmpl/member/distribution.html";
    }
</script>
	<body>
		<div class="container wrap">
		  <div class="userinfo">
			  <div class="header-wrap">
			      <!-- <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left colf fz-40"></b></a></div> -->
			    <div class="header-title posr">
			      
			    </div>
			  </div>
		    <div class="account-bg"><img class="user_logo"></div>
			<div class="user_main fz-24 nam"><i class="name"></i><i class="dengji ml-20"></i></div>
          <div>
              <a href="distribution_commission.html?from=all">
                  <div class="user_main fz-60 user_directseller_commission"></div>
                  <div class="user_main fz-24 nam">结算佣金</div>
              </a>
          </div>
			<div class="user_main fz-28 nam"><a class="ti_xian" href="commission_withdraw.html">提现</a></div>
		  </div>
		  <!-- 我的服务 -->
		  <div class="member-services-items clearfix bgf">
		    <a href="distribution_commission.html">
		      <div>
		          <img mode="widthFix" src="../../images/dimges/Group%202.png"></img>
		      </div>
		      <text class="item-text fz-30 c-444 align-middle">今日结算佣金</text>
			  <text class="item-text fz-30 col-red align-middle settlement_income"></text>
		    </a>
		    <a href="distribution_userspread.html?section=1">
		        <div>
		          <img mode="widthFix" src="../../images/dimges/user.png"></img>
		          
		        </div>
		        <text class="item-text fz-30 c-444">今日推广用户</text>
				<text class="item-text fz-30 col-red align-middle user_num"></text>
		    </a>
		    <a href="distribution_order.html?section=1">
		      <div>
		        <img mode="widthFix" src="../../images/dimges/day_order_nums.png"></img>
		        
		      </div>
		      <text class="item-text fz-30 c-444 align-middle">今日推广订单</text>
			  <text class="item-text fz-30 col-red align-middle day_order_nums"></text>
		    </a>
		    <a href="distribution_order.html?section=2">
		        <div>
		          <img mode="widthFix" src="../../images/dimges/income_tatol.png"></img>
		          
		        </div>
		        <text class="item-text fz-30 c-444">今日预估收益</text>
				<text class="item-text fz-30 col-red align-middle income_tatol"></text>
		    </a>
		    <a href="distribution_goods.html?section=1">
		        <div>
		          <img mode="widthFix" src="../../images/dimges/goods_num.png"></img>
		        </div>
		        <text class="item-text fz-30 c-444">今日推广商品</text>
				<text class="item-text fz-30 col-red align-middle day_goods_num"></text>
		    </a>
		   	<a href="distribution_userspread.html?section=0">
		        <div>
		          <img mode="widthFix" src="../../images/dimges/invitors.png"></img>
		        </div>
		        <text class="item-text fz-30 c-444">累计推广用户</text>
				<text class="item-text fz-30 col-red align-middle invitors"></text>
		    </a>
		   <a href="distribution_order.html?section=0">
		        <div>
		          <img mode="widthFix" src="../../images/dimges/promotion_order_nums.png"></img>
		        </div>
		        <text class="item-text fz-30 c-444">累计推广订单</text>
				<text class="item-text fz-30 col-red align-middle promotion_order_nums"></text>
		    </a>
		   <a href="distribution_goods.html?section=0">
		        <div>
		          <img mode="widthFix" src="../../images/dimges/goods_nums.png"></img>
		        </div>
		        <text class="item-text fz-30 c-444">累计推广商品</text>
				<text class="item-text fz-30 col-red align-middle goods_num"></text>
		    </a>
              <a href="javascript:;" id='show'>
                  <div>
                      <img mode="widthFix" src="../../images/dimges/add_invite.png"></img>
                  </div>
                  <text class="item-text fz-30 c-444">我的邀请人</text>
                  <text class="item-text fz-30 col-red align-middle parent">&nbsp</text>
              </a>
		  </div>
		</div>
	</body>
</html>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/distribution.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>
