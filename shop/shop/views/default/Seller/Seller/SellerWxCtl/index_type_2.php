<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
unset($seller_menu['19000']['sub']['190002']);
unset($seller_menu['19000']['sub']['190003']);
unset($seller_menu['19000']['sub']['190004']);
?>
<?php if($data['status']==1){?>
    <link href="<?= $this->view->css ?>/wx_common.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit" style="padding-left: 8rem;">
                    <label for="wechat_public_name">平台审核未通过，可再次申请！</label>
                </dt>
            </dl>
            <div class="bot">
                <p class="notic"style="color: red;">失败原因：<?=$data['review_info']?></p>
            </div>
            <div class="bot">
                <a href="javascript:void(0);" class="ui-btn ui-btn-sp re-submit-btn">重新申请</a>
            </div>               
        </div>
    </form>
<?php }else{?>
    <link href="<?= $this->view->css ?>/wx_common.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit" style="padding-left: 8rem;">
                    <label for="wechat_public_name">平台审核通过，可设置绑定您的公众号！</label>
                </dt>
            </dl>
            <div class="bot">
                <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn">绑定公众号</a>
            </div>               
        </div>
    </form>
<?php }?>    
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
<script type="text/javascript">
  $(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=removeApplication&typ=json';
        $(".re-submit-btn").click(function () {
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: $("#wechat-public-api-setting-form").serialize(),
                success: function (a) {
                    if (a.status == 200) {
                        window.location.reload();
                    } else {
                        Public.tips({type: 1, content: a.msg});
                    }
                }
            });
        });
    });

    $(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=showType&typ=json';
        $(".submit-btn").click(function () {
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: $("#wechat-public-api-setting-form").serialize(),
                success: function (a) {
                    if (a.status == 200) {
                        window.location.reload();
                    } else {
                        Public.tips({type: 1, content: a.msg});
                    }
                }
            });
        });
    });

</script>