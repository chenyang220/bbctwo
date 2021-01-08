<?php 
include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<!-- <link rel="stylesheet" type="text/css" href="static/css/pub.css" /> -->
		<link rel="stylesheet" type="text/css" href="../../css/base.css">
		<link rel="stylesheet" href="../../css/iconfont.css">
		<link rel="stylesheet" href="../../css/style.css" />
		<title>推广用户</title>
		<style>
		.extension_box{padding-top: 89px;overflow-y: auto;min-height: 100vh;background:rgba(241,241,241,1);width: 100%;}
		.extension_top{width: 100%;position: fixed;left: 0;top: 0;background: #FFFFFF;z-index: 2;margin-top: 2rem;}
		.extension_sec{width: 100%;position: relative;text-align: center;padding: 7px 0 5px;border-bottom: 1px solid rgba(241,241,241,1);;}
		.extension_sec1{width: 60.8%;background:rgba(247,247,247,1);border-radius:17px;border:1px solid rgba(241,241,241,1);height: 33px;padding: 0 30px 0 15px;font-size: 14px;color:#333333;}
		.extension_sec2{width: 18px;height: 18px;position: absolute;right:15.5%;top: 15px;}
		.extension_type{width: 100%;display: flex;}
		.extension_type1{flex: 1;text-align: center;font-size: 14px;color:rgba(74,74,74,1);;line-height: 42px;cursor: pointer;}
		.extension_type2{width: 9px;height: 4px;}
		.extension_type3{display: inline-block;padding: 0 10px;font-size: 14px;color: #4A4A4A;cursor: pointer;position: relative;}
		.extension_type3.active{color:  #FD3D53 ;line-height: 40px;border-bottom:2px solid #FD3D53;}
		.extension_type3 i{position: absolute;background:rgba(254,71,92,1);border-radius: 15px;font-size: 12px;color:  #FFFFFF;line-height: 1;right: 0;top: 3px;height: 13px;padding: 0 3px;}
		.userlists{padding: 2.5% 2.6% 0;margin-top: 2rem;}
		.userlist{width: 100%;background:rgba(255,255,255,1);box-shadow:0px 0px 4px 0px rgba(39,1,1,0.08);border-radius:9px;overflow: hidden;margin-bottom: 10px;}
		.uese_header{display: flex;padding: 8px 4.49%;background:rgba(255,251,251,1);align-items: center;}
		.uese_header_img{width: 56px;height:56px;border-radius: 50%;flex: 0 0 auto;}
		.uese_header1{flex: 1;padding-left: 3.6%;}
		.uese_header2{font-size: 15px;color: #4A4A4A;margin-bottom: 6px;}
		.uese_header4{display: inline-block;}
		.uese_header5{width: 13px;height: 13px;vertical-align: middle;}
		.uese_header6{font-size: 12px;color: #9B9B9B;}
		.uese_header6 i{font-size: 12px;color:#4A4A4A;}
		.user_new{width: 100%;display: flex;background: #FFFFFF;border-bottom:1px solid rgba(241,241,241,1);;}
		.user_news{width: 33.33%;text-align: center;display: inline-block;padding: 22px 0 12px;}
		.user_newa{font-size: 14px;color: #4A4A4A;margin-bottom: 10px;}
		.user_news .user_newb{font-size: 0.6363rem;color:#9B9B9B}
		.user_times{padding: 9px 0 8px;width: 100%;text-align:center;font-size: 12px;color: #9B9B9B;}
		.selected{color: #FD3D53;line-height: 40px;border-bottom: 2px solid #FD3D53;}
		</style>
	</head>
	<body>
		<header id="header" class="fixed bgf">
		    <div class="header-wrap">
<!--		        <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col9b fz-40"></b></a></div>-->
                <!-- <div class="header-l"><a href="javascript:history.go(-1)"><i class="back"></i></a></div> -->
		        <div class="header-title posr">
		            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">今日推广用户</h1>
		        </div>
		    </div>
		</header>
		<div class="extension_box">
			<!-- 头部搜索 固定定位-->
			<div class="extension_top">
				<div class="extension_sec">
					<input type="" name="orderkey" id="" value="" placeholder="请输入会员名称" class="extension_sec1" />
					<img src="../../images/icon_search@2x.png" class="extension_sec2">
				</div>
				<div class="extension_type">
					<div class="extension_type1"><span class="extension_type3 member_all">全部会员<i class="all"></i></span></div>
					<div class="extension_type1 "><span class="extension_type3 member_direct">直接用户<i class="direct">0</i></span></div>
					<div class="extension_type1 "><span class="extension_type3 member_indirect">间接用户<i class="indirect">0</i></span></div>
				</div>
			</div>
			<!-- 列表 -->
			<div class="userlists">
				
			</div>
		</div>
	<script type="text/javascript" src="../../js/zepto.min.js"></script>
	<script type="text/javascript" src="../../js/template.js"></script>
	<script type="text/javascript" src="../../js/common.js"></script>
	<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
	<script type="text/javascript">
		var section = getQueryString('section');
		var orderkey = '';
		$.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Distribution&met=distributionNum&typ=json",
            data: {k: getCookie('key'),u: getCookie('id'),section:section},
            dataType: "json",
            success: function (r) {
 				$('.all').html(r.data.all_num);
 				$('.direct').html(r.data.direct_num);
 				$('.indirect').html(r.data.indirect_num);
            }
        });
        if (section == 0) {
			$("#z-tab-order").html("累计推广用户");
        }
        $(".member_all").addClass("selected");
        $(".member_all").click(function(){
	        $(".member_all").addClass("selected");
	        $(".member_direct").removeClass("selected");
	        $(".member_indirect").removeClass("selected");
	        get_list('all');
	    });
        $(".member_direct").click(function(){
	        $(".member_all").removeClass("selected");
	        $(".member_direct").addClass("selected");
	        $(".member_indirect").removeClass("selected");
	        get_list('direct');
	    });
	    $(".member_indirect").click(function(){
	        $(".member_all").removeClass("selected");
	        $(".member_direct").removeClass("selected");
	        $(".member_indirect").addClass("selected");
	        get_list('indirect');
	    });
	    get_list('all');
	    function get_list(genre){
	    	$.ajax({
	            type: "post",
	            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Distribution&met=directsellerList&typ=json",
	            data: {k: getCookie('key'),u: getCookie('id'),genre:genre,orderkey:orderkey,section:section},
	            dataType: "json",
	            success: function (r) {
	 				var e = template.render("member-list", r.data);
            		$(".userlists").html(e);  
	            }
	        });
	    }
	    $('.extension_sec2').click(function(){
	  		orderkey=$.trim($('input[name="orderkey"]').val());
	    	get_list('all');
	    })
	</script>
	<script type="text/html" id="member-list">
		<% var member = items; %>
		<%if(member.length >0){%>
			<%for(j=0;j < member.length;j++){%>
				<% var list = member[j]%>
				<div class="userlist">
					<div class="uese_header">
						<img src="<%=list.user_logo%>" class="uese_header_img">
						<div class="uese_header1">
							<p class="uese_header2 ovfloew_two"><%=list.user_name%></p>
							<div>
								<div class="uese_header4"><img src="../../images/btn_arrow_bott_small@3x.png" class="uese_header5">
									<span class="uese_header6">性别:<i><%=list.user_sex%></i></span></div>
								<div class="uese_header4" style="margin-left: 8px;"><img src="../../images/btn_arrow_bott_small@3x.png" class="uese_header5">
									<span class="uese_header6">手机号:<i>
											<%=list.user_mobile%></i></span></div>
							</div>
						</div>
					</div>
					<div class="user_new">
						<div class="user_news">
							<div class="user_newa"><%=list.invitors%>人</div>
							<div class="user_newb">推广会员数</div>
						</div>
						<div class="user_news">
							<div class="user_newa">
								¥<%=list.commission%></div>
							<div class="user_newb">
								带来佣金</div>
						</div>
						<div class="user_news">
							<div class="user_newa">¥<%=list.expends%></div>
							<div class="user_newb">消费总额</div>
						</div>
					</div>
					<p class="user_times">注册时间：<%=list.user_regtime%></p>
				</div>
			<%}%>
		<%}%>
	</script>	
	</body>
</html>

<?php
include __DIR__ . '/../../includes/footer.php';
?>