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
	<title>店铺编辑</title>
	<link rel="stylesheet" type="text/css" href="../../css/base.css">
	<link rel="stylesheet" href="../../css/iconfont.css">
		<style>
			.shop{
				margin-top: 2.5rem;
			}
			.main_shop{
				margin-top: 0.5rem;
				background:rgba(255,255,255,1);
			}
		 .main_shop_id{
			 border:1px solid rgba(241,241,241,1);
			 display: flex;
			 justify-content: flex-start;
		 }
		 .main_shop_id span{
			 padding-top: 0.7rem;
			 padding-bottom: 0.7rem;
			 font-size:14px;
			 font-family:PingFangSC-Regular,PingFang SC;
			 font-weight:400;
			 color:rgba(74,74,74,1);
			 padding-left: 0.5rem;
			
			

		 }
		 .main_shop_id input{
			margin-left: 0.5rem;
			padding-top: 0.7rem;
			padding-bottom: 0.7rem;
			border: 0;  // 去除未选中状态边框
			outline: none; // 去除选中状态边框
			background-color: rgba(0, 0, 0, 0);// 透明背景
		 }
		 .shop_file{
			 margin-top: 0.7rem;
			 margin-bottom: 0.7rem;
			 margin-left: 0.5rem;
			 text-align: center;
			 width: 111px;
			 height: 64px;
			 
		 }
		 
		 .post-format-content {
		 	position: relative;
		 	background: #111;
		 }
		 .post-thumbnail {
		 	max-width: 100%;
		 	height: auto;
		 	overflow: hidden;
		 }
		 .content-wrap {
		 	padding: 0;
		 	position: absolute;
		 	text-align: center;
		 	width: 100%;
		 	top: 0;
		 	bottom: 0;
		 	display: table-cell;
		 	vertical-align: middle;
		 	overflow: hidden;
		 }
		 .content-wrap h1.entry-title {
		 	display: table;
		 	height: 100%;
		 	text-transform: uppercase;
		 	width: 100%;
		 	margin:0;
		 }
		 .edit-link {
		 	z-index: 2;
		 }
		 .featured-image {
		 	display: table-cell;
		 	position: relative;
		 	transition: opacity .25s ease-in-out, background .25s ease-in-out;
		 	-moz-transition: opacity .25s ease-in-out, background .25s ease-in-out;
		 	-webkit-transition: opacity .25s ease-in-out, background .25s ease-in-out;
		 	vertical-align: middle;
		 	z-index: 1;
		 	color: #fff;
			font-size: 15px;
		 	text-decoration: none;
		 	opacity: 0;
		 	padding: 10%;
		 }
		 .featured-image:hover {
		 	opacity: 0.9;
		 	color: #fff;
		 	background: rgba(0,0,0,0.8);
		 }
		 .post-thumbnail img {
		 	display: block;
		 }
		 img {
		 	max-width: 100%;
		 	height: auto;
		 }
		 #img{
		 	width: 100%;
		 	height: 100%;
		 }
		 #shop_logo{
		 		width: 100%;
		 	height: 100%;
		 }
		  a{
			  display: block;
			  text-align: center;
			  font-size:14px;
			  font-family:PingFangSC-Regular,PingFang SC;
			  font-weight:400;
			  color:rgba(74,144,226,1);
			  margin-bottom: 0.5rem;
			  margin-top: 0.5rem;
		  }
		 .main_shop_us{
			 display: flex;
			 justify-content: space-around;
			 width: 95%;
			 margin: auto;
			 margin-top: 0.5rem;
		 }
		 .button{
			 width:14.863636rem;
			 height:2.22727rem;
			 line-height: 2.22727rem;
			 background:rgba(253,61,83,1);
			 border-radius:25px;
			 display: block;
			 text-align: center;
			 color: #FFFFFF;
			 margin: auto;
			 margin-top: 1.5rem;
			 margin-bottom: 1.5rem;
		 }
		
		.shop_file {
            position: relative;
            display: inline-block;
            border: 1px solid #99D3F5;
            border-radius: 4px;
            overflow: hidden;
            text-decoration: none;
            text-indent: 0;
            line-height: 20px;
        }
        .shop_file input {
            position: absolute;
            font-size: 100px;
            right: 0;
            top: 0;
            opacity: 0;
        }
        .shop_file:hover {
            background: #AADFFD;
            border-color: #78C3F3;
            color: #004974;
            text-decoration: none;
        }
        .main_shop_us input[type="radio"] {
	      display: none;
	    }
	    .post-format-content{
	    	width: 89px;
	    	height: 113px;
	    	overflow: hidden;
	    	border-radius:6px;
	    	border: 1px solid #99D3F5;
	    }
	    .post-format-content img{
	    	width: 100%;
	    }
	    #cover,#cover1,#cover2,#cover3{ 
         position:absolute;left:0px;top:0px;
         background:#2B2B2B;
         width:100%;  /*宽度设置为100%，这样才能使隐藏背景层覆盖原页面*/
         height:100%;
         filter:alpha(opacity=60);  /*设置透明度为60%*/
         opacity:0.6;  /*非IE浏览器下设置透明度为60%*/
          display:none; 
          z-Index:999;  
}
#modal,#modal1,#modal2,#modal3{ 
    position:absolute;
    width:100%;
    height:100%;
    line-height: 900%;
    text-align: center;
    /*background-color:#fff;*/
    color: #fff;
    font-size:12px;
    display:none;
    cursor:pointer;
    z-Index:9999;  
}
#xuan{
	width: 22px;
	height: 18px;
	right: 0px;
	position: absolute;
	bottom: 0px;

}
.mod{
	width: 80% !important;
    margin: auto;
    left: 10%;
    top: 10%;
    z-index: 99999 !important;
    height: 80% !important;
    overflow-y: auto;
}
.cha{
	width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1px solid #fff;
    position: absolute;
    right: 30px;
    top: 30px;
    z-index: 99999;
}
.main_shop_id textarea{
	height: 4rem;
	width: 11rem;
	padding:0.2rem; 
	margin-left: 0.2rem;
	 border: none;
              resize: none;
              cursor: pointer;
}
		</style>
	</head>
	<body>
		<header id="header" class="fixed bgf">
		    <div class="header-wrap">
		        <!-- <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col9b fz-40"></b></a></div> -->
		        <div class="header-title posr">
		            <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">店铺编辑</h1>
		        </div>
		    </div>
		</header>
			               <div id="cover3" style="z-index: 99999" ></div>
					  	   <div id="modal3" class="mod">
                            <img id="img1" src="../../images/小店模板1.png">
                            <img id="img2" src="../../images/poster.png">
                            <img id="img3" src="../../images/poster-bg.png">  
                            </div>
                            <div class="cha" onclick="stopBubble()"><img src="../../images/chacha.png"></div>
	    <form class="shop" id="shop_data">
			<div class="main_shop">
				<div class="main_shop_id">
					<span>店铺名称</span>
					<input name="distribution_name" placeholder="请输入店铺名称">
				</div>
				<div class="main_shop_id">
					<span>店铺logo</span>			
					<a class="shop_file"><input type="file" name="myfile" id="img"><img id="shop_logo"></a>
					<input type="hidden" name="distribution_logo" value="">
				</div>
				<div class="main_shop_id">
					<span>店铺介绍</span>
					<!-- <input type="textarea" placeholder="请输入店铺介绍" name="distribution_desc" value=""> -->
					 <textarea cols="8" rows="20" name="distribution_desc"></textarea>
				</div>
				<div class="main_shop_id">
					<span>联系方式</span>
					<input  placeholder="请输入联系方式" name="distribution_phone" value="">
				</div>
			</div>
			<div class="main_shop">
				<div class="main_shop_id">
					<span>店铺模板</span>
					<!-- <input  placeholder="请输入店铺名称"> -->
				</div>
				<div class="main_shop_us" id="content">
					<div>
					  <div class="post-format-content">
					  	<div id="cover" ></div>
					  	<div id="modal">
                            模板一
                            <img id="xuan" src="../../images/xuan.png">
                            </div>
					  	<input type="radio" id="radio1" name="distribution_template" value="1">
  						<label for="radio1" class="post-thumbnail"><img  src="../../images/小店模板1.png" class="attachment-thumbnail wp-post-image" alt="105694702"> </label>
					  </div>
					  <a onclick="alertBoxFn(1)">预览</a>
					</div>
					<!-- <div>
					  <div class="post-format-content" >
					  		<div id="cover1"></div>
					  	<div id="modal1">
                            模板一
                            <img id="xuan" src="../../images/xuan.png">
                            </div>
					  	<input type="radio" id="radio2" name="distribution_template" value="2">
  						<label for="radio2" class="post-thumbnail"><img  src="../../images/小店模板1.png" class="attachment-thumbnail wp-post-image" alt="105694702"> </label>
					  </div>
					  <a onclick="alertBoxFn(2)">预览</a>
					</div>
					<div>
					  <div class="post-format-content">
					  		<div id="cover2"></div>
					  	<div id="modal2">
                            模板一
                            <img id="xuan" src="../../images/xuan.png">
                            </div>
					  	<input type="radio" id="radio3" name="distribution_template" value="3">
  						<label for="radio3" class="post-thumbnail"><img  src="../../images/小店模板1.png" class="attachment-thumbnail wp-post-image" alt="105694702"> </label>
					  </div>
					  <a onclick="alertBoxFn(3)">预览</a>
					</div> -->
				</div>				
			</div>
			<div>
				<a class="button submit-but">确认</a>
			</div>
		</form>
	</body>
</html>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/distribution_shop_edit.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<script type="text/javascript">
	$.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getDistributionShopInfo&typ=json",
            data: {k: getCookie('key'),u: getCookie('id')},
            dataType: "json",
            success: function (r) {
            	if(r.status==200){
            		$("#shop_logo").attr("src",r.data.distribution_logo);
            		$('input[name="distribution_logo"]').val(r.data.distribution_logo);
                	$('input[name="distribution_name"]').val(r.data.distribution_name);
                	$('textarea[name="distribution_desc"]').val(r.data.distribution_desc);
                	$('input[name="distribution_phone"]').val(r.data.distribution_phone);
                	if(r.data.distribution_template==1){
                		$("#radio1").prop('checked',true);
                		$("#radio2").prop('checked',false);
                		$("#radio3").prop('checked',false);
                		document.getElementById("cover").setAttribute('style', 'display: block !important');
	 	                document.getElementById("modal").setAttribute('style', 'display: block !important');
	 	                document.getElementById("cover2").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal2").setAttribute('style', 'display: none !important');
	 	                document.getElementById("cover1").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal1").setAttribute('style', 'display: none !important');
                	}else if(r.data.distribution_template==2){
                		$("#radio1").prop('checked',false);
                		$("#radio2").prop('checked',true);
                		$("#radio3").prop('checked',false);
                		document.getElementById("cover1").setAttribute('style', 'display: block !important');
	 	                document.getElementById("modal1").setAttribute('style', 'display: block !important');
	 	                document.getElementById("cover").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal").setAttribute('style', 'display: none !important');
	 	                document.getElementById("cover2").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal2").setAttribute('style', 'display: none !important');
                	}else if(r.data.distribution_template==3){
                		$("#radio1").prop('checked',false);
                		$("#radio2").prop('checked',false);
                		$("#radio3").prop('checked',true);
	 	                document.getElementById("cover2").setAttribute('style', 'display: block !important');
	 	                document.getElementById("modal2").setAttribute('style', 'display: block !important');
	 	                document.getElementById("cover").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal").setAttribute('style', 'display: none !important');
	 	                document.getElementById("cover1").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal1").setAttribute('style', 'display: none !important');
                	}
                	
            	}
                
            }
        });
	$(function () {
       $('input[name="distribution_template"]').change(function () {
       		var a =$('input[name="distribution_template"]:checked').val();
       		if(a==1){
       			        document.getElementById("cover").setAttribute('style', 'display: block !important');
	 	                document.getElementById("modal").setAttribute('style', 'display: block !important');
	 	                document.getElementById("cover2").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal2").setAttribute('style', 'display: none !important');
	 	                document.getElementById("cover1").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal1").setAttribute('style', 'display: none !important');

       		}else if(a==2){
       			        document.getElementById("cover1").setAttribute('style', 'display: block !important');
	 	                document.getElementById("modal1").setAttribute('style', 'display: block !important');
	 	                document.getElementById("cover").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal").setAttribute('style', 'display: none !important');
	 	                document.getElementById("cover2").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal2").setAttribute('style', 'display: none !important');

       		}else{
       			       document.getElementById("cover2").setAttribute('style', 'display: block !important');
	 	                document.getElementById("modal2").setAttribute('style', 'display: block !important');
	 	                document.getElementById("cover").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal").setAttribute('style', 'display: none !important');
	 	                document.getElementById("cover1").setAttribute('style', 'display: none !important');
	 	                document.getElementById("modal1").setAttribute('style', 'display: none !important');

       		}
		});
	});
	function alertBoxFn(e) {
		cover3.style.display = "block";
		modal3.style.display = "block";
		if(e==1){
		img1.style.display = "block";
		img2.style.display = "none";
		img3.style.display = "none";
		}else if(e==2){
			img1.style.display = "none";
			img2.style.display = "block";
			img3.style.display = "none";

		}else{
			img1.style.display = "none";
			img2.style.display = "none";
			img3.style.display = "block";

		}
	}
	function stopBubble(e){
		cover3.style.display = "none";
		modal3.style.display = "none";
	}
</script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>