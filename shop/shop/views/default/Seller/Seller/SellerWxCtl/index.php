<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<link href="<?= $this->view->css ?>/wx_common.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">

<?php if($expire==1){ ?>


<div id="xufei" style="border: 1px solid #999;
    position: absolute;
    left: 30%;
    top: 30%;
    z-index: 999;
    text-align: center;
    height: 200px;
    line-height: 46px;
    background: #fff;">
<h1>到期提醒</h1> 
<p>温馨提示：您的商家公众号将于<?=$sellerWx_day?>天后到期，为了能正常使用此功能请您安排续费，谢谢！</p>   
<button id="xu" style="background: #009688;width: 60px;height: 30px;border: 0;">续费</button>
<button id="no" style="background: #009688;width: 60px;height: 30px;border: 0;">关闭</button>
</div>

<?php }?> 
<form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
            <div class="ncap-form-default form-style">
                <dl class="row">
                    <dt class="tit"><?= __('状态'); ?></dt>
                    <dd class="opt">
                        <div class="onoff">
                            <input id="wechat_public_status1" name="seller_wxpublic_status" value="0" type="radio" <?=($info['seller_wxpublic_status']==0 ? 'checked' : '')?> >
                            <label title="<?= __('开启'); ?>" class="cb-enable <?=($info['seller_wxpublic_status']==0 ? 'selected' : '')?> " for="wechat_public_status1"><?= __('开启'); ?></label>

                            <input id="wechat_public_status0" name="seller_wxpublic_status" value="1" type="radio"  <?=($info['seller_wxpublic_status']==1 ? 'checked' : '')?> >
                            <label title="<?= __('关闭'); ?>" class="cb-disable <?=($info['seller_wxpublic_status']==1 ? 'selected' : '')?>" for="wechat_public_status0" onclick="parent.$.dialog.confirm(__('一旦关闭，微信公众号相关功能将停用，确认关闭吗？'), function () {},function () {$('.cb-enable').click();})"><?= __('关闭'); ?></label>
                        </div>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <i>*</i>
                        <label for="wechat_public_name">名称</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_name" name="wechat_public_name" value="<?= $info['wechat_public_name']; ?>" class="w400 ui-input " type="text"/>
                         <p class="notic">公开信息->名称</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <i>*</i>
                        <label for="wechat_public_start_id">原始ID</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_start_id" name="wechat_public_start_id" value="<?= $info['wechat_public_start_id']?>" class="w400 ui-input " type="text"/>
                        <p class="notic">注册信息->原始ID</p>
                    </dd>
                </dl>

                <dl class="row">
                    <dt class="tit">
                        <i>*</i>
                        <label for="wechat_public_wxaccount">微信号</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_wxaccount" name="wechat_public_wxaccount" value="<?=  $info['wechat_public_wxaccount'] ?>" class="ui-input w400" type="text"/>
                        <p class="notic">公开信息->微信号</p>
                    </dd>
                </dl>

                <dl class="row">
                    <dt class="tit">
                        <i>*</i>
                        <label for="wechat_public_call_url">回调URL</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_call_url" disabled name="wechat_public_call_url" value="<?= $info['wechat_public_call_url'] ?>" class="w400 ui-input " type="text"/>
                        <p class="notic">服务器地址(URL)，不用填写，自动生成</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <i>*</i>
                        <label for="wechat_public_token">Token</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_token" disabled name="wechat_public_token" value="<?= $info['wechat_public_token']  ?>" class="w400 ui-input " type="text"/>
                        <p class="notic">令牌(Token)，不用填写，自动生成</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <i>*</i>
                        <label for="wechat_public_appid">AppId</label>
                    </dt>
                    <dd class="opt">
                        <input  id="wechat_public_appid" name="wechat_public_appid" value="<?= $info['wechat_public_appid'] ?>" class="w400 ui-input " type="text"/>
                        <p class="notic">开发者ID(AppID)</p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <i>*</i>
                        <label for="wechat_public_secret">Secret</label>
                    </dt>
                    <dd class="opt">
                        <input   id="wechat_public_secret" name="wechat_public_secret" value="<?= $info['wechat_public_secret'] ?>" class="w400 ui-input " type="text"/>
                        <p class="notic">开发者密码(AppSecret)</p>
                    </dd>
                </dl>
                <div class="bot">
                    <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a>
                </div>
            </div>
        </form> 
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
<script type="text/javascript">
    $(function (){
        $('#xu').click(function(){
            url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=index&typ=e&edit=1';
            location.href = url;
        })

        $('#no').click(function(){
            $('#xufei').hide();
        })
        //自定义radio样式
        $(".cb-enable").click(function(){
            var parent = $(this).parents('.onoff');
            $('.cb-disable',parent).removeClass('selected');
        var cb_id = $('.cb-enable',parent).attr('for');
            $(this).addClass('selected');
            $('input[type="radio"]',parent).attr('checked', false);
        $('#'+cb_id,parent).attr('checked', true);
        });
        $(".cb-disable").click(function(){
            var parent = $(this).parents('.onoff');
            $('.cb-enable',parent).removeClass('selected');
        var cb_id = $('.cb-disable',parent).attr('for');
            $(this).addClass('selected');
            $('input[type="radio"]',parent).attr('checked', false);
        $('#'+cb_id,parent).attr('checked', true);
        });
    })

    $(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=saveSellerWxList&typ=json';
        $(".submit-btn").click(function () {
            var wechat_name = $("#wechat_public_name").val();
            if(!wechat_name){
                Public.tips({type: 1, content: '请填写公众号名称'});
                return false;
            }
            var wechat_public_start_id =$("#wechat_public_start_id").val();
            if(!wechat_public_start_id){
                Public.tips({type: 1, content: '请填写原始ID'});
                return false;
            }
            var wechat_public_wxaccount =$("#wechat_public_wxaccount").val();
            if(!wechat_public_wxaccount){
                Public.tips({type: 1, content: '请填写微信号'});
                return false;
            }
            var wechat_public_appid =$("#wechat_public_appid").val();
            if(!wechat_public_appid){
                Public.tips({type: 1, content: '请填写开发者ID'});
                return false;
            }
            var wechat_public_secret =$("#wechat_public_secret").val();
            if(!wechat_public_appid){
                Public.tips({type: 1, content: '请填写开发者密码'});
                return false;
            }
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: $("#wechat-public-api-setting-form").serialize(),
                success: function (a) {
                    if (a.status == 200) {
                        Public.tips({type: 3,content: "提交成功！"});
                    } else {
                        Public.tips({type: 1, content: a.msg});
                    }
                }
            });
        });
    });
</script>


