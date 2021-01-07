<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<link href="<?= $this->view->css ?>/wx_common.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
<form method="post" enctype="multipart/form-data" id="publicmsg_add" name="form1">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="words">关键词</label>
            </dt>
            <dd class="opt">
                <input  id="words" name="words"  class="w400 ui-input " type="text"/>
                <p class="notic">多个关键词，以逗号（,）分隔！</p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="wechat_public_match_type">匹配类型</label>
            </dt>
            <dd class="opt">
                <select class="ui-input" id="match_type" name ="match_type" style="width: 130px;height:30px;">
                    <option value="1" selected>精准匹配</option>
                    <option value="2">模糊匹配</option>
                </select>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="wechat_public_match_type">消息类型</label>
            </dt>
            <dd class="opt">
                <select class="ui-input" id="msg_type" name ="msg_type" style="width: 130px;height:30px;">
                    <option value="1" selected>文本消息</option>
                </select>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row" id="content_dl">
            <dt class="tit">
                <label for="content">消息内容</label>
            </dt>
            <dd class="opt">
                 <textarea id="content" name="content"  class="w400 ui-input " style="height: 120px;" type="text"></textarea>
                <p class="notic"></p>
            </dd>
        </dl>

        <div class="bot">
            <a href="javascript:void(0);" class="ui-btn ui-btn-sp im-submit-btn"><?= __('确认提交'); ?></a>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=saveSellerWxMsg&typ=json';
        $(".im-submit-btn").click(function () {
            var words = $("#words").val();
            if(!words){
                Public.tips({type: 1, content: '请填写关键词！'});
                return false;
            }
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: $("#publicmsg_add").serialize(),
                success: function (a) {
                    if (a.status == 200) {
                        Public.tips({type: 3,content: "保存成功！"});
                        window.location.href="index.php?ctl=Seller_Seller_SellerWx&met=wxCallback&typ=e&";
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