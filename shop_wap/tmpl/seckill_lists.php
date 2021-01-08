<?php
    include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="msapplication-tap-highlight" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="Author" contect="U2FsdGVkX1+liZRYkVWAWC6HsmKNJKZKIr5plAJdZUSg1A==">
<meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1,viewport-fit:cover;">
<title>精品秒杀</title>
<link rel="stylesheet" type="text/css" href="../css/style1.css" />
<link rel="stylesheet" type="text/css" href="../css/base1.css">
 <style>
        .container{
            width:100%;
			margin-top: 2.11rem;
			background: rgba(255,255,255,1);
        }
        #a{
			margin: auto;
            display: flex;
			overflow-x: hidden;
			border-bottom: 1px solid rgba(233,233,233,1);
        }
        #a div{
			padding-top: 0.363636rem;
			padding-bottom: 0.363636rem;
			font-size:0.545454rem;
			font-family:PingFangSC-Regular,PingFang SC;
			font-weight:400;
			color:rgba(74,74,74,1);
            flex-grow: 1;
           /* display: flex;
            align-items: center;
            justify-content: center;
			 */
          
        }
		#a div i{
			display: block;
			text-align: center;
			font-size:18px;
			font-family:PingFangSC-Medium,PingFang SC;
			font-weight:500;
			/* color:rgba(255,46,42,1); */
		}
		.qia{
			font-size:12px !important;
			font-weight:400 !important;
		}
        .content{
            width:100%;
            background: rgba(255,255,255,1);
            display: none;
        }
        .container .active{
            display: block;
        }
		.search{
			margin-top: 0.363636rem;
			margin-bottom: 0.363636rem;
			margin: auto;
			width:14.545454rem;
			height:1.590909rem;
			background:rgba(241,241,241,1);
			border-radius:0.81818181rem;
		}
		.search input{
			padding-left: 0.678rem;
			    border: none;
			    width: 80%;
			    height: 1.590909rem;
			    background: rgba(241,241,241,1);
			    border-radius: 0.81818181rem;
		}
		.zhou{
			margin-top: 0.2rem;
			margin-left: 0.2rem;
			display: block;
			position: relative;
			   width:3.3636rem;
			   height:0.31818rem;
			   background:rgba(233,233,233,1);
			   border-radius:0.1818rem;
		}
		.zhou i{
			   background:rgba(255,86,60,1);
			    border-radius: 0.1818rem;
			    display: block;
			    position: absolute;
			    top: 0;
			    left: 0;
			    z-index: 1;
			    height: 100%;
		}
		.jin{
			font-size:0.5454rem;
			font-family:PingFangSC-Regular,PingFang SC;
			font-weight:400;
			color:rgba(214,214,214,1);
		}
		.m-price{
			font-size: 18px;
			font-family:PingFangSC-Regular,PingFang SC;
			font-weight:500;
		}
		.sop{
			line-height: 1rem;
			padding-left: 0.2rem;
		}
		.nctouch-nav-layout{
			background: rgba(0,0,0,0.56) !important;
			top:2rem !important;
		}
		.nctouch-nav-menu{
			float: left;
			width: 100% !important;
			right: 0 !important;
			background: rgba(241,241,241,1) !important;
			top:0rem !important;
		}
		.nctouch-nav-menu ul{
			background: rgba(241,241,241,1);display: flex;justify-content: space-between;flex-wrap: wrap;padding: 0.6818rem;
		}
		.nctouch-nav-menu ul li{
			display: block;
			width:4.5454rem;
			height:1.3181rem;
			line-height: 1.3181rem;
			text-align: center;
			background:rgba(255,255,255,1);
			border-radius:15px;
			font-size:0.6363rem;
			font-family:PingFangSC-Regular,PingFang SC;
			font-weight:400;
			color:rgba(51,51,51,1);
			margin-bottom: 0.5rem;
		}
    </style>
</head>
<body>
	<header id="header" class="fixed bgf">
	    <div class="header-wrap">
	        <div class="header-l">
            <!-- <a href="javascript:history.back(-1)"> <i class="back"></i> </a> -->
        	</div>
	        <div class="header-title posr">
	            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">精品秒杀</h1>
	        </div>
			<div class="header-r">
			    <a id="header-nav" onclick="openframe()"><i style="background-image: url(../images/dpfl.png)" class="more"></i></a>
			</div>
	    </div>
		<div class="nctouch-nav-layout" id="modal1" >
			<div class="nctouch-nav-menu" id="modal">
			<ul id="cat-list">
				
				
		   </ul>
				
				
			</div>
		</div>
	</header>
	<div class="container">
	    <div id="a">
	        <div class="qie">
				<i>12:00</i>
				<i class="qia">即将开始</i>
			</div>
	        <div class="qie">
	        	<i>12:00</i>
	        	<i class="qia">即将开始</i>
	        </div>
			<div class="qie">
				<i>12:00</i>
				<i class="qia">即将开始</i>
			</div>
			<div class="qie">
				<i>12:00</i>
				<i class="qia">即将开始</i>
			</div>
			<div class="qie">
				<i>12:00</i>
				<i class="qia">即将开始</i>
			</div>
		
	    </div>
	    
	    <div class="content active">

	    	<div class="m-box">
				<div id="product_list" class="list">
					<ul class="goods-secrch-list">
					</ul>
				</div>
				
			<!-- 	<div class="foot clearfix">
					<div class="all-check">全选<div class="gap-check check"></div>
					</div>
				</div> -->
				
			</div>
		</div>

<script type="text/html" id="home_body">
	<% var goods_list = data.items; %>
    <% if(goods_list.length >0){%>
        <%for(i=0;i < goods_list.length;i++){%>
		<li class="goods-item" goods_id="<%=goods_list[i].goods_id;%>">
			<div class="box">
				<div class="img">
					<img src="<%=goods_list[i].goods_image;%>" />
				</div>
				<div class="info clearfix">
					<% if(goods_list[i].goods_name){ var name=goods_list[i].goods_name }else{ var name=goods_list[i].seckill_name } %>
					<div class="title pad30"><%=name;%></div>
					<div class="m-num ">
						<span class="m-price"><%=goods_list[i].seckill_price;%></span>
						<span class="jin sop"><%=goods_list[i].goods_price;%></span>
					</div>
					<div class="m-num ">
						<span class="jin">已售<%=goods_list[i].sold_bai;%>%</span>
						<span class="zhou"><i <%=goods_list[i].bai_style;%>></i></span>
					</div>
					<div class="price fr"><a style="color:#fff;" href="product_detail.html?goods_id=<%=goods_list[i].goods_id;%>">去抢购</a></div>
				</div>
			</div>
		</li>
		<%}%>
	<%}%>
</script>
<script type="text/html" id="home_cat">
	<% var cat_list = data.cat_list; %>
    <% if(cat_list.length >0){%>
        <%for(i=0;i < cat_list.length;i++){%>
			<li ><a class="cat"  href="seckill_list.html?cat_id=<%=cat_list[i].cat_id%>" id="<%=cat_list[i].cat_id%>"><%=cat_list[i].cat_name%></a></li>
		<%}%>
	<%}%>
</script>						
				
	    
	</div>

</body>
<script>

        var a = document.getElementById('a');
        var divObj = a.children;
        var now = new Date();
        var hour = now.getHours();//得到小时
        var o = [];
        var html_time = [];
    	if(hour>=0&&hour<=2){
    		o[0] = 1;
    		o[1] = 2;
    		o[2] = 3;
    		o[3] = 4;
    		o[4] = 5;
    		html_time[0] = '0:00';
    		html_time[1] = '8:00';
    		html_time[2] = '10:00';
    		html_time[3] = '12:00';
    		html_time[4] = '14:00';
    	}
    	if(hour>=8&&hour<=10){
    		o[0] = 2;
    		o[1] = 3;
    		o[2] = 4;
    		o[3] = 5;
    		o[4] = 6;
    		html_time[0] = '8:00';
    		html_time[1] = '10:00';
    		html_time[2] = '12:00';
    		html_time[3] = '13:00';
    		html_time[4] = '16:00';
    	}
    	if(hour>=10&&hour<=12){
    		o[0] = 3;
    		o[1] = 4;
    		o[2] = 5;
    		o[3] = 6;
    		o[4] = 7;
    		
    		html_time[0] = '10:00';
    		html_time[1] = '12:00';
    		html_time[2] = '14:00';
    		html_time[3] = '16:00';
    		html_time[4] = '18:00';
    	}
    	if(hour>=12&&hour<=14){
    		o[0] = 4;
    		o[1] = 5;
    		o[2] = 6;
    		o[3] = 7;
    		o[4] = 8;
    		
    		html_time[0] = '12:00';
    		html_time[1] = '14:00';
    		html_time[2] = '16:00';
    		html_time[3] = '18:00';
    		html_time[4] = '20:00';
    	}
    	if(hour>=14&&hour<=16){
    		o[0] = 5;
    		o[1] = 6;
    		o[2] = 7;
    		o[3] = 8;
    		o[4] = 9;
    		html_time[0] = '14:00';
    		html_time[1] = '16:00';
    		html_time[2] = '18:00';
    		html_time[3] = '20:00';
    		html_time[4] = '22:00';
    	}
    	if(hour>=16&&hour<=18){
    		o[0] = 6;
    		o[1] = 7;
    		o[2] = 8;
    		o[3] = 9;
    		o[4] = 1;
    		html_time[0] = '16:00';
    		html_time[1] = '18:00';
    		html_time[2] = '20:00';
    		html_time[3] = '22:00';
    		html_time[4] = '0:00';
    	}
    	if(hour>=18&&hour<=20){
    		o[0] = 7;
    		o[1] = 8;
    		o[2] = 9;
    		o[3] = 1;
    		o[4] = 2;
    		html_time[0] = '18:00';
    		html_time[1] = '20:00';
    		html_time[2] = '22:00';
    		html_time[3] = '0:00';
    		html_time[4] = '8:00';
    	}
    	if(hour>=20&&hour<=22){
    		o[0] = 8;
    		o[1] = 9;
    		o[2] = 1;
    		o[3] = 2;
    		o[4] = 3;
    		html_time[0] = '20:00';
    		html_time[1] = '22:00';
    		html_time[2] = '0:00';
    		html_time[3] = '8:00';
    		html_time[4] = '10:00';
    	}
    	if(hour>=22&&hour<=24){
    		o[0] = 9;
    		o[1] = 1;
    		o[2] = 2;
    		o[3] = 3;
    		o[4] = 4;
    		html_time[0] = '22:00';
    		html_time[1] = '0:00';
    		html_time[2] = '8:00';
    		html_time[3] = '10:00';
    		html_time[4] = '12:00';
    	}
        // var content = document.getElementsByClassName('content')
        for(var i = 0; i < divObj.length; i++){
    
            divObj[0].lastElementChild.innerHTML  = '抢购中';
    		divObj[i].firstElementChild.innerHTML  = html_time[i];
            divObj[0].style.color = 'red';
            
			// divObj[0].style.borderBottom= '1px solid red'
            divObj[i].setAttribute('index',o[i]) //设置自定义属性
            divObj[0].setAttribute('id','now_time');
    //         divObj[i].onclick = function(){
    //             for(var j = 0; j < divObj.length; j++){
    //                 divObj[j].style.color = 'rgba(74,74,74,1)';
				// 	// divObj[j].style.borderBottom= '1px solid rgba(255,255,255,1)'
    //                 // content[j].classList.remove('active') //移除class
    //             }
    //             alert(1);
    //             this.style.color = "red"
				// // this.style.borderBottom= '1px solid red'
    //             // content[this.getAttribute('index')].classList.add('active') //添加class
               
                
    //         }
        }

	var btn=document.getElementById("header-nav");
	var modal=document.getElementById("modal");
	var modal1=document.getElementById("modal1");
// 　　var close=document.getElementsByClassName("close");
　　//点击按钮，弹出弹框
　　function openframe(){
	　　modal.style.display="block";
	　  modal1.style.display="block";
　　}
　　//点击叉号，关闭弹窗
// 　　function closeframe(){
// 　　modal.style.display="none";
// 　　}
　　//点击其他地方窗口隐藏
　　window.onclick=function(e){
　　if(e.target.offsetParent==modal){
	　　modal.style.display="none";
	    modal1.style.display="none";
　　}
　　}

    </script>
<script type="text/javascript" src="../js/zepto.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/seckill_lists.js"></script>

<!--<script type="text/javascript" src="../js/footer.js"></script>-->

</html>
<?php
    include __DIR__ . '/../includes/footer.php';
?>


