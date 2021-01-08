<?php
include __DIR__ . '/../../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title>提现记录</title>

<!--公共集合样式-->
<link rel="stylesheet" type="text/css" href="../../css/base.css">
<link rel="stylesheet" href="../../css/iconfont.css">
<link rel="stylesheet" href="css/style.css" />


 <style>
       .container{
           width:100%;
       	margin-top: 2.11rem;
       	/* background: rgba(255,255,255,1); */
       }
       .container div{
       	 background: rgba(255,255,255,1);
       }
	   .withdrawal{
		   padding: 0.454545rem 0.818181rem;
		   border-bottom:1px solid rgba(241,241,241,1);
		   font-size:0.545454rem;
		   font-family:PingFangSC-Regular,PingFang SC;
		   font-weight:400;
		   color:rgba(155,155,155,1);
	   }
	   .write_to{
		   display: flex;
		   justify-content: space-between;
	   }
	   .omb{
		   padding-bottom: 0.718181rem;
	   }
	   .red{
		  color: #FD3D53;
	   }
	   .wrig_t{
		   font-size:0.636363rem;
		   font-family:PingFangSC-Regular,PingFang SC;
		   font-weight:400;
		   color:rgba(74,74,74,1);
	   }
	   .red_bo{
		   color: #D0021B;
		   font-size:0.636363rem;
	   }
	   .write_yua{
		   padding-left: 0.5rem;
	   }
    </style>
</head>
<body>
	<header id="header" class="fixed bgf">
	    <div class="header-wrap">
	        <!-- <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col9b fz-40"></b></a></div> -->
	        <div class="header-title posr">
	            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">提现记录</h1>
	        </div>
	    </div>
	</header>
	<div class="container"> 
	</div>  
</body>
</html>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript">
	$.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getWithdrawLog&typ=json",
        data: {k: getCookie('key'),u: getCookie('id')},
        dataType: "json",
        success: function (r) {
        	var e = template.render("log-list", r);
            $(".container").html(e);
        }
    });
</script>
<script type="text/html" id="log-list">
	<% var log_list = data; %>
	<%if(log_list.length >0){%>
		<%for(j=0;j < log_list.length;j++){%>
			<% var list = log_list[j]%>
			<div class="withdrawal">
			<div class="write_to omb">
				<span class="wrig_t">佣金提现</span>
				<%if(list.withdraw_status==1){%>
				<span class="red">已到账</span>
				<%}else{%>
				<span class="red">未到账</span>
				<%}%>
			</div>	
			<div class="omb">
				<span>申请时间：</span>
				<span><%=list.withdraw_time%></span>
			</div>
			<div class="write_to">
				<div>
					<span>预计到账</span>
					<span class="write_yua">￥<%=list.withdraw_amount%></span>
				</div>
				<span class="red_bo">￥<%=list.withdraw_amount%></span>
			</div>	
			</div>
		<%}%>
	<%}%>
</script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>