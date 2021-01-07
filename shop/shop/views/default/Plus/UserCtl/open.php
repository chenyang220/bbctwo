<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'site_nav.php';
?>
<style>
	body{background:#fff !important;}
	.footer{margin-top:0 !important; border:0 !important;}
</style>
<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/plus.css" />
	<div class="plus-header">
		<div class="plus-header-nav plus-header-bgf">
			<div class="wrap clearfix">
				<div class="fl">
					<a class="plus-logo" href="javascript:;"></a>
				</div>
				<div class="fr plus-header-exchange"><em class="active" onclick="gotoIndex();">Plus会员首页</em><em onclick="gotoIndex('&flag=1');">Plus会员专享</em></div>
			</div>
		</div>
		<div class="plus-formal-buy">
			<h3 class="plus-formal-buy-tit tc">购买正式版PLUS会员</h3>
			<div class="wrap">
				<div class="plus-formal-buy-content">
					<img src="<?= $this->view->img ?>/plus/plus-img.png" alt="img">
					<div class="plus-formal-buy-content-text">
						<dl>
							<dt>开通套餐：</dt>
							<dd>PLUS会员</dd>
						</dl>
						<dl>
							<dt>付费模式：</dt>
							<dd><?=$plus_shopping_mode;?></dd>
						</dl>
						<dl>
							<dt>应付金额：</dt>
							<dd><strong>￥<?=$plus_shopping_price;?></strong></dd>
						</dl>
                        <?php if ($user_identity_statu!=2){?>
                            <p class="plus-formal-buy-tips"><i class="iconfont icon-Prompt"></i><em>完成实名认证才可购买PLUS会员</em></p>
                        <?php } ?>
                        <?php if ($user_identity_statu==0){?>
                            <a class="plus-buy-btn" target="_blank"  href="<?=$url?>/index.php?ctl=Info&met=certification">去实名认证</a>
                        <?php
                            }elseif ($user_identity_statu==1){?>
                            <a class="plus-buy-btn" target="_blank"  href="<?=$url?>/index.php?ctl=Info&met=account&typ=e">实名认证审核中…</a>
                            <?php
                        }elseif ($user_identity_statu==3){?>
                            <a class="plus-buy-btn" target="_blank"  href="<?=$url?>/index.php?ctl=Info&met=certification">审核未通过，重新实名认证</a>
                        <?php }else{ ?>
                            <a id="plus_open_btn" class="plus-buy-btn" href="javascript:;">去付款</a>
                        <?php
                        }
                        ?>
						<p>
							<a  target="_blank" class="plus-pay-agreement" href="/index.php?ctl=Plus_User&met=agreement">购买即视为同意《PLUS会员-用户协议》</a>
						</p>
						
					</div>
				</div>
			</div>
		</div>
	</div>
<script>
	 $(window).scroll(function() {
        if($(window).scrollTop()>=38){
             $(".plus-header-nav").addClass("active");
         }else{
             $(".plus-header-nav").removeClass("active");
         }
       
    });
    var protocolStr = document.location.protocol;
    var SITE_URL = "<?=Yf_Registry::get('url')?>";
    if (protocolStr === "http:") {
        SITE_URL = SITE_URL.replace(/https:/, "http:");
    } else if (protocolStr === "https:") {
        SITE_URL = SITE_URL.replace(/http:/, "https:");
    } else {
        SITE_URL = SITE_URL.replace(/https:/, "http:");
    }
    function gotoIndex(str=''){
        window.location.href = SITE_URL+"/index.php?ctl=Plus_User&met=index"+str;
    }
	
	//支付
	$("#plus_open_btn").click(function(){
		$.ajax({
			type:"POST",
			url: SITE_URL  + '?ctl=Plus_User&met=createPlusOrder&typ=json',
			data:{
				ts:Date.now(),
                },
			dataType: "json",
			contentType: "application/json;charset=utf-8",
			async:false,
			success:function(data){
				if(data.status == 200)
				{
					window.location.href = "<?=$url?>?ctl=Info&met=pay&uorder=" + data.data.uorder+'&order_g_type=physical';
					return false;
				} else {
					alert('订单提交失败！');
					return false;
				}
			},
			failure:function(data)
			{
				alert('操作失败！');
			}
		});

	});
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>