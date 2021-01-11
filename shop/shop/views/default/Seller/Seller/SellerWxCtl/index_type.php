<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>注册</title>
		<link href="<?= $this->view->css ?>/bootstrap.min.css" rel="stylesheet">
		<link href="<?= $this->view->css ?>/gloab.css" rel="stylesheet">
		<link href="<?= $this->view->css ?>/seller_wx_index.css" rel="stylesheet">
		<style type="text/css">
	     .du{
	     	padding-left: 61px;
	     }
		</style>
	</head>
	<body class="bgf4">
		<div class="login-box f-mt10 f-pb50">
			<div class="bgf">
				<div class="reg-box-pan display-inline">
					<div class="steps">
						<ul>
							<li class="col-xs-4 on">
								<span class="num"><em class="f-r5"></em><i>1</i></span>
								<span class="line_bg lbg-r"></span>
								<p class="lbg-txt">填写信息</p>
							</li>
							<li class="col-xs-4">
								<span class="num"><em class="f-r5"></em><i>2</i></span>
								<span class="line_bg lbg-l"></span>
								<span class="line_bg lbg-r"></span>
								<p class="lbg-txt">确认信息及支付</p>
							</li>
							<li class="col-xs-4">
								                                                          
								<span class="num"><em class="f-r5"></em><i>3</i></span>
								<span class="line_bg lbg-l"></span>
								<span class="line_bg lbg-r"></span>
								<p class="lbg-txt">上传付款凭证</p>
								
							</li>
							<li class="col-xs-4">
								<span class="num"><em class="f-r5"></em><i>4</i></span>
								<span class="line_bg lbg-l"></span>
								<p class="lbg-txt">提交审核</p>
							</li>
						</ul>
					</div>
					<div class="reg-box" id="verifyCheck" style="margin-top:20px;">
						<div class="part1">
							<div class="item col-xs-19 imp">
								<div>
									<span class="intelligent-label f-fl">公众号名称：</span>

									<input <?php if($edit){ echo "readonly";}?> type="text" maxlength="20" class="txt03 f-r3 required" tabindex="1" id="adminNo" value="<?=$data['wx_public_name']?>" />
								</div>
								<div class="tipes">温馨提示：如需开通商家公众号功能，需先绑定公众号，您可以提交申请，由平台进行审核是否通过！</div>
							</div>
							<div class="item col-xs-19 imp">
								<span class="intelligent-label f-fl">选择服务：</span>

								<select id="select_wx">
									<option value="<?=$sellerWx_price?>">商家公众号 商家掌上店铺 <?=$sellerWx_price?>/年</option>
								</select>


							</div>
							<div class="item col-xs-19 imp">
								<span class="intelligent-label f-fl">申请年限：</span>

								<div class="time_lit"><input class="act" id="input2" type=button value="-"><input class="act1" id="input0" type=text name=amount
									 value="<?= $years?>" style="text-align: center;" ><input class="act" id="input1" type=button value="+"></div>

							</div>
							<div class="item col-xs-19">

								<div class="f-fl item-ifo">
									<a href="javascript:;" class="btn btn-red f-r3" id="btn_part1">下一步</a>
								</div>
							</div>
						</div>
						<div class="part2" style="display:none">
							<div class="alert">确认信息</div>
							<div class="item col-xs-19 " style="height:auto">
								<span >公众号名称</span>
								<span id="wx_name" class="title"><?=$data['wx_public_name']?></span>
							</div>
							<div class="item col-xs-19 " style="height:auto">
								<span>选择服务</span>
								<span class="title du">商家公众号 商家掌上店铺 <?=$sellerWx_price?>/年</span>
							</div>
							<div class="item col-xs-19 " style="height:auto">
								<span>申请年限</span>
								<span id="wx_year" class="title du"><?=$data['years']?>年</span>
							</div>
							<div class="item col-xs-19 " style="height:auto">
								<span>应付金额</span>
								<span id="wx_price" class="title du"><?=$sum_price?>元</span>
							</div>
							<div class="alert">扫码支付</div>
							<div class="item col-xs-19">
								<div class="items col-xs-20 " style="height:auto">
									<div class="mb10">
										<img src="<?=$sellerWx_wxcode?>" alt="上海远丰信息科技(集团)有限公司">
										<span class="mask"></span>
									</div>
									<span style="margin-left: 18px;">微信支付</span>
								</div>

								<div class="items col-xs-20 " style="height:auto">
									<div class="mb10">
										<img src="<?=$sellerWx_alicode?>" alt="上海远丰信息科技(集团)有限公司">
										<span class="mask"></span>
									</div>
									<span style="margin-left: 18px;">支付宝支付</span>
								</div>
							</div>
							<div class="alert" style="margin-top: 180px">线下银行转账</div>
							<div class="item col-xs-19 " style="height:auto">
								<span>开户行</span>
								<span class="title du"><?=$sellerWx_bank?></span>
							</div>
							<div class="item col-xs-19 " style="height:auto">
								<span>收款账户</span>
								<span class="title"><?=$sellerWx_number?></span>
							</div>
							<div class="item col-xs-19 " style="height:auto;margin-bottom: 50px;">
								<span>收款人</span>
								<span class="title du"><?=$sellerWx_user?></span>
							</div>
							<div class="items col-xs-12 inp">
								<div class="item-ifo">
									<a href="javascript:;" class="btn btn-red f-r3" id="btn_part2">上一步</a>
								</div>
								<div class="item-ifo">
									<a href="javascript:;" class="btn btn-blue f-r3" id="btn_part3">我已经支付完成</a>
								</div>
							</div>		
					    </div>
						<div class="part3" style="display:">
							<div class="alert">线下银行转账</div>
							<div class="items col-xs-12">
								<div class="item-ifo sto" style="margin-bottom: 2rem;">
									<img style="width: 100%; height:100%;" id="wx_pay" src="" />
									<input type="hidden" value="" id="imagefile" class="text w145">
									
									
									
								</div>
								<a id="upload_image" style="display: block;margin-left: 270px;margin-bottom: 8rem;">选择图片</a>
							</div>

							<div class="items col-xs-12 inp">
								<div class="item-ifo">
									<a href="javascript:;" class="btn btn-red f-r3" id="btn_part4">上一步</a>
								</div>
								<div class="item-ifo">
									<a href="javascript:;" class="btn btn-blue f-r3" id="btn_part5">提交审核</a>
								</div>
							</div>
						</div>
						<?php if($data['status']==0){ ?>
						<div class="part4 text-center" style="display:none">
							<img src="<?= $this->view->img ?>/duihao.png" / style="width: 78px;height: 78px; margin-top: 10rem;">
							<p class="c-666 f-mt30 f-mb50">提交申请成功，等待审核</p>
							
						</div>
						<?php }else if($data['status']==1){?>
						<div class="part4 text-center" style="display:none">
		                	<img src="<?= $this->view->img ?>/cuohao.png" / style="width: 78px;height: 78px; margin-top: 10rem;">
		                    <p class="c-666 f-mt30 f-mb50">你提交的审核失败，请再次提交</p>
							<p class="c-888 f-mb50">失败原因：<?=$data['review_info']?></p>
							<div style="width: 100%;">
							  
							    <div class="item-ifo" style="margin: auto;">
							       <a href="javascript:;" class="btn btn-blue f-r3" id="btn_part6">上一步</a>                         
							    </div>
							</div> 
		                </div>   
		                <?php }?>    
		                <div class="part6 text-center" style="display:none">
							<img src="<?= $this->view->img ?>/duihao.png" / style="width: 78px;height: 78px; margin-top: 10rem;">
							<p class="c-666 f-mt30 f-mb50">提交申请成功，等待审核</p>
							
						</div>   
	            		</div>
					</div>
				</div>
			</div>
			<div class="m-sPopBg" style="z-index:998;"></div>
			<div class="m-sPopCon regcon">
				<div class="m-sPopTitle"><strong>服务协议条款</strong><b id="sPopClose" class="m-sPopClose" onClick="closeClause()">×</b></div>
				<div class="apply_up_content">
					<pre class="f-r0">
		<strong>同意以下服务条款，提交注册信息</strong>
        </pre>
				</div>
				<center><a class="btn btn-blue btn-lg f-size12 b-b0 b-l0 b-t0 b-r0 f-pl50 f-pr50 f-r3" href="javascript:closeClause();">已阅读并同意此条款</a></center>
			</div>
			
	</body>
</html>
<script src="<?= $this->view->js ?>/jquery2.min.js"></script>
<script src="<?= $this->view->js ?>/register.js"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/upload/upload_image.js" charset="utf-8"></script>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
	$(function(){
		upload_image = new UploadImage({
                            thumbnailWidth: 1200,
                            thumbnailHeight: 360,
                            imageContainer: '#wx_pay',
                            uploadButton: '#upload_image',
                            inputHidden: '#imagefile'
              		});
	})
</script>
<script>



				// function number() {
				// 	var Input0 = document.getElementById('input0');
				// 	var Input1 = document.getElementById('input1');
				// 	var Input2 = document.getElementById('input2');

				// 	Input1.οnclick = function() {
				// 		this.form.amount.value++;
				// 	}

				// 	Input2.οnclick = function() {
				// 		if (Input0.value > 1) {
				// 			this.form.amount.value--;
				// 		}
				// 	}
				// }
				//提交
				$(document).ready(function(){
					//第一步
				    
				});    


				$(function() {

					//图片上传
					

					var step = "<?=$data['step']?>";
					if(!step||step==1){
						$(".part1").show();
						$(".part2").hide();
						$(".part3").hide();
						$(".part4").hide();
						$(".steps li").eq(0).addClass("on");
						
					}else if(step==2){
						$(".part1").hide();
						$(".part2").show();
						$(".part3").hide();
						$(".part4").hide();
						$(".steps li").eq(1).addClass("on");
						
					}
					else if(step==3){
						$(".part1").hide();
						$(".part2").hide();
						$(".part3").show();
						$(".part4").hide();
						$(".steps li").eq(1).addClass("on");
						$(".steps li").eq(2).addClass("on");
						
					}else{
						$(".part1").hide();
						$(".part2").hide();
						$(".part3").hide();
						$(".part4").show();
						$(".steps li").eq(1).addClass("on");
						$(".steps li").eq(2).addClass("on");
						$(".steps li").eq(3).addClass("on");
					}

					$('#input1').click(function(){
						var num = $('#input0').val();
						num++;
						$('#input0').val(num); 
					})

					$('#input2').click(function(){
						var num = $('#input0').val();
						if(num>1){
							num--;
							$('#input0').val(num); 
						}
						
					})

					//第一页的确定按钮
					$("#btn_part1").click(function() {

						var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=saveApplication&typ=json';
				        var wechat_name = $("#adminNo").val();
				        var num         = $('#input0').val();
				        var sellerWx_price       = "<?=$sellerWx_price?>";
				        var sum_price = num*sellerWx_price;
				        if(!wechat_name){
				            alert('请填写公众号名称');
				            return false;
				        }
				        $.ajax({
				            type: 'POST',
				            url: ajax_url,
				            data: {wechat_public_name:wechat_name,year:num,step:1},
				            success: function (a) {
				                if (a.status == 200) {
				                	//Public.tips({type: 3,content: "提交申请成功！"});
				                    $(".part1").hide();
									$(".part2").show();
									$(".step li").eq(1).addClass("on");
									//给第二页赋值
                                    $('#wx_name').text(wechat_name);
                                    $('#wx_year').text(num+'年');
                                    $('#wx_price').text(sum_price+'元');

				                } else {
				                	if(a.msg=='商家已有公众号'){
				                		$(".part1").hide();
										$(".part2").show();
										$(".step li").eq(1).addClass("on");
										//给第二页赋值
	                                    $('#wx_name').text(wechat_name);
	                                    $('#wx_year').text(num+'年');
	                                    $('#wx_price').text(sum_price+'元');
				                	}else{
				                		alert(s.msg);
				                	}

				                    //Public.tips({type: 1, content: a.msg});
				                }
				            }
				        });
					 
						// if(!verifyCheck._click()) return;
						
					});
					//第二页的上一步
					$("#btn_part2").click(function() {
						// if(!verifyCheck._click()) return;
						$(".part2").hide();
						$(".part1").show();
						$(".steps li").eq(1).removeClass("on");
						$(".steps li").eq(0).addClass("on");
					});
					//第二页的确定按钮
					$("#btn_part3").click(function() {

						var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=saveApplication&typ=json';
				        $.ajax({
				            type: 'POST',
				            url: ajax_url,
				            data: {step:2},
				            success: function (a) {
				                if (a.status == 200) {
				                    $(".part2").hide();
									$(".part3").show();
									$(".steps li").eq(2).addClass("on");
				                } else {
				                    //Public.tips({type: 1, content: a.msg});
				                }
				            }
				        });
						// if(!verifyCheck._click()) return;
						
					});
					//第三页上一步
					$("#btn_part4").click(function() {
						// if(!verifyCheck._click()) return;
						$(".part3").hide();
						$(".part2").show();
						$(".steps li").eq(2).removeClass("on");
						$(".steps li").eq(1).addClass("on");
					});
					//第三页的确定按钮
					$("#btn_part5").click(function() {
						// if(!verifyCheck._click()) return;
						var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=saveApplication&typ=json';
				        var pay_images = $('#imagefile').val();
				        $.ajax({
				            type: 'POST',
				            url: ajax_url,
				            data: {pay_images:pay_images,step:3},
				            success: function (a) {
				                if (a.status == 200) {
				                    $(".part3").hide();
									$(".part6").show();
									$(".steps li").eq(3).addClass("on");
				                } else {
				                    //Public.tips({type: 1, content: a.msg});
				                }
				            }
				        });
						
						countdown({
							maxTime: 3,
							ing: function(c) {
								$("#times").text(c);
							},
							after: function() {
								var url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Index&met=index&typ=e';
								window.location.href = url;
							}
						});
					});

					//第三页上一步
					$("#btn_part6").click(function() {
						// if(!verifyCheck._click()) return;
						$(".part4").hide();
						$(".part3").show();
						$(".steps li").eq(3).removeClass("on");
						//$(".step li").eq(1).addClass("on");
					});
				});

				function showoutc() {
					$(".m-sPopBg,.m-sPopCon").show();
				}

				function closeClause() {
					$(".m-sPopBg,.m-sPopCon").hide();
				}
			</script>
			<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>