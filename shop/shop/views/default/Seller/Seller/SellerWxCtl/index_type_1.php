<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
unset($seller_menu['19000']['sub']['190002']);
unset($seller_menu['19000']['sub']['190003']);
unset($seller_menu['19000']['sub']['190004']);
?>
<?php if(!empty($data)){?>
	<link href="<?= $this->view->css ?>/wx_common.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
	<form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_name">平台审核中，请耐心等待！</label>
                    </dt>
                </dl>
                <div class="bot">
                    <p class="notic">温馨提示：如需开通绑定公众号功能，您可以提交申请，由平台进行审核是否通过！</p>
                </div>               
            </div>
        </form>
<?php }else{?>	
		<link href="<?= $this->view->css ?>/wx_common.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
		<form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label for="wechat_public_name">公众号名称</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_name" name="wechat_public_name" class="w400 ui-input " type="text"/>
                    </dd>
                </dl>
                <div class="bot">
                    <a href="javascript:void(0);" class="ui-btn ui-btn-sp im-submit-btn"><?= __('确认提交'); ?></a>
                    <p class="notic">温馨提示：如需开通绑定公众号功能，您可以提交申请，由平台进行审核是否通过！</p>
                </div>               
            </div>
        </form>
<?php }?>
<script type="text/javascript">
	$(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=saveApplication&typ=json';
        $(".im-submit-btn").click(function () {
            var wechat_name = $("#wechat_public_name").val();
            if(!wechat_name){
                Public.tips({type: 1, content: '请填写公众号名称'});
                return false;
            }
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: $("#wechat-public-api-setting-form").serialize(),
                success: function (a) {
                    if (a.status == 200) {
                    	Public.tips({type: 3,content: "提交申请成功！"});
                        window.location.reload();
                    } else {
                        Public.tips({type: 1, content: a.msg});
                    }
                }
            });
        });
    });    
</script> 
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>