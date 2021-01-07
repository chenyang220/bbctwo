<?php 
include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title>佣金明细</title>

<!--公共集合样式-->
<link rel="stylesheet" type="text/css" href="../../css/base.css">
<link rel="stylesheet" href="../../css/iconfont.css">
 <style>
    .container{
        width:100%;
		margin-top: 2.11rem;
		/* background: rgba(255,255,255,1); */
    }
	.container div{
		 background: rgba(255,255,255,1);
	}
	.top{
		border:1px solid rgba(241,241,241,1);
		padding-bottom: 0.2rem;
	}
	.to-u{
		padding-top: 0.45rem;
		padding-bottom: 0.45rem;
	}
    #a{
        width:50%;
		margin: auto;
        display: flex;
    }
    #a div{
		padding-top: 0.363636rem;
		padding-bottom: 0.363636rem;
		font-size:0.636363rem;
		font-family:PingFangSC-Regular,PingFang SC;
		font-weight:400;
		color:rgba(74,74,74,1);
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
      
    }
    .content{
        width:100%;
        background: rgba(255,255,255,1);
        display: none;
		margin-top: 0.45rem;
    }
    .container .active{
        display: block;
    }
	.search{
		margin-top: 0.363636rem;
		margin-bottom: 0.363636rem;
		margin: auto;
		width:12.409090rem;
		height:1.5rem;
		background:rgba(241,241,241,1) !important;
		border-radius:0.81818181rem;
	}
	.search input{
		padding-left: 0.678rem;
		    border: none;
		    width: 80%;
		    height: 1.5rem;
		    background: rgba(241,241,241,1);
		    border-radius: 0.81818181rem;
	}
	.search img{
		margin-top: 0.2rem;
		width: 1.1rem;
		height: 1.1rem;
	}
	.count_main{
		/* padding: 0.5454rem 0.4090rem; */
		border-bottom:1px solid rgba(241,241,241,1);
		padding-left: 0.5454rem;
		padding-right: 0.5454rem;
		padding-top: 0.409rem;
		padding-bottom: 0.6rem;
	}
	.of{
		font-size:0.545454rem;
		font-family:PingFangSC-Regular,PingFang SC;
		font-weight:400;
		color:rgba(155,155,155,1);
	}
	.cops{
		display: flex;
		justify-content: space-between;
		margin-top: 1rem;
	}
	.red{
		color: #FF001D;
	}
	.yellow{
		color: #F5A623;
	}
	.selected{
		    color: red !important;
			border-bottom: 2px solid red;
	}

    #button {
        display: flex;
        justify-content: space-around;
        font-size: 12px;
        padding: 0.3rem 0;
        padding-right: 0.4rem;
    }

    #button a {
        width: 2rem;
        height: 0.9rem;
		line-height:0.9rem;
        text-align: center;
        color: #666;
        border: 1px solid #AAAAAA;
        border-radius: 1rem;
    }

    #button a.active {
        background:#FF525F;
        color: #fff !important;
        border: 1px solid #FF525F !important;

    }
</style>
</head>
<body>
	<header id="header" class="fixed bgf">
	    <div class="header-wrap">
            <!-- <div class="header-l"><a href="javascript:history.go(-1)"><i class="back"></i></a></div> -->
	        <div class="header-title posr">
	            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">累计结算佣金</h1>
	        </div>
	    </div>
	</header>
	<div class="container">
		<div class="to-u">
            <div class="search">
                <input placeholder="请输入订单号进行搜索" name="orderkey">
                <img class="search-list" src="../../images/sousuo.png">
            </div>
		</div>
        <div id="button">
            <a href="javascript:;" data-show="all" class="ctime active">全部</a>
            <a href="javascript:;" data-show="s" class="ctime">七天</a>
            <a href="javascript:;" data-show="o" class="ctime">一个月</a>
            <a href="javascript:;" data-show="t" class="ctime">三个月</a>
            <a href="javascript:;" data-show="y" class="ctime">一年</a>
        </div>
		<div class="top">
            <div id="a">
                <div class="one_level">一级佣金</div>
                <div class="second_level">二级佣金</div>
            </div>
		</div>
	    <div class="content active">			
			
		</div>    
	</div>


<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>

<script>
    var from = getQueryString('from');
    var ctime = '';
    if (from == 'all') {
        $("#button").show();
        ctime = 'all';
    } else {
        $("#button").hide();
    }
    $(".one_level").addClass("selected");
    $(".one_level").click(function(){
    	orderkey='';
        $(".one_level").addClass("selected");
        $(".second_level").removeClass("selected");
        get_list(1);
        $('input[name="orderkey"]').val("");
    });
    $(".second_level").click(function(){
    	orderkey='';
        $(".second_level").addClass("selected");
        $(".one_level").removeClass("selected");
        get_list(2);
        $('input[name="orderkey"]').val("");
    });
    get_list(1);
    var orderkey='';
    function get_list(status){
    	$.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Distribution&met=directsellerCommission&typ=json",
            data: {k: getCookie('key'),u: getCookie('id'),status:status,orderkey:orderkey, ctime: ctime},
            dataType: "json",
            success: function (r) {
            	console.log(r);
            	var e = template.render("commission-list", r);
            	$(".content").html(e);
            	var data = r.data;
            	if(data.length > 0){
	            	if(data[0].settlement_level==2){
	            		$(".second_level").addClass("selected");
	        			$(".one_level").removeClass("selected");
	            	}else if(data[0].settlement_level==1){
	            		$(".one_level").addClass("selected");
	        			$(".second_level").removeClass("selected");
	            	} 
            	}
            }
        });
    }
    $('.search-list').click(function(){
	    orderkey=$('input[name="orderkey"]').val();
    	get_list(0);
    })

    //时间icon筛选
    $(".ctime").click(function () {
        ctime = $(this).data('show');
        orderkey = '';
        $('input[name="orderkey"]').val('');
        $(this).addClass('active').siblings().removeClass('active');
        get_list(1);
    })
</script>
<script type="text/html" id="commission-list">	
	<% var commission = data; %>
	<%if(commission.length >0){%>
		<%for(j=0;j < commission.length;j++){%>
			<% var list = commission[j]%>
			<div class="count_main">
				<div class="of">
					<span>订单号：</span>
					<span><%=list.settlement_order_id%></span>
				</div>
				<div class="of mt-30">
					<span>时间：</span>
				    <span><%=list.time%></span>
				</div>
				<div class="of cops">
					<div class="of_money">
						<span>付款金额：</span>
						<span class="red">￥<%=list.order_amount%></span>
						
					</div>
					<div class="of_money">
						<span>结算佣金：</span>
						<span class="yellow">￥<%=list.settlement_amount%></span>
					</div>
				</div>
			</div>
		<%}%>
	<%}%>
</script>
</body>
</html>
<?php
include __DIR__ . '/../../includes/footer.php';
?>