
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
        <h3 class="plus-formal-buy-tit tc">PLUS会员正式用户协议</h3>
        <div class="wrap">
            <div class="plus-pay-agreement-content">
                <div class="plus-pay-agreement-head">【请认真阅读并理解以下内容，其中以加粗方式显著标识的文字，请着重阅读、慎重考虑。】</div>
                <?php
                $str = '&nbsp;';
                if(strlen($plus_agreement)<200){
                    $plus_agreement.=str_repeat($str, 203);
                }
                echo $plus_agreement;
                ?>
                <div class="agreement-btn">
                    <a href="javascript:window.opener=null;window.close();">同意并继续</a>
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
        window.location.href = SITE_URL+"?ctl=Plus_User&met=index"+str;
    }
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>