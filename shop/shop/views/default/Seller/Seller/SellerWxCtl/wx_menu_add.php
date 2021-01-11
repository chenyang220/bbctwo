<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<link href="<?= $this->view->css ?>/wx_common.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
<form method="post" enctype="multipart/form-data" id="publicmsg_add" name="form1">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="wechat_public_name">菜单分类</label>
            </dt>
            <dd class="opt">
                <select class="ui-input" id="menu_level" name ="menu_level" style="width: 130px;height:30px;">
                    <option value='0'>请选择</option>
                    <?php  foreach($data as $item){ ?>
                        <option value="<?=$item['id']?>"><?=$item['menu_name']?></option>;
                    <?php }?>
                </select>
                <p class="notic">不选择，默认一级菜单</p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="wechat_public_start_id">菜单名称</label>
            </dt>
            <dd class="opt">
                <input  id="menu_name" name="menu_name"  class="w400 ui-input " type="text"/>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="wechat_public_wxaccount">菜单类型</label>
            </dt>
            <dd class="opt">
                <select class="ui-input" id="menu_type" name ="menu_type" style="width: 130px;height:30px;"onchange="change_show(this.value);">
                    <option value="1" selected>发送消息</option>
                    <option value="2">跳转网页</option>
                </select>
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row" id="content_dl">
            <dt class="tit">
                <label for="content">消息内容</label>
            </dt>
            <dd class="opt">
                <input  id="content" name="content"  class="w400 ui-input " type="text" />
                <p class="notic"></p>
            </dd>
        </dl>
        <dl class="row" id="redirect_url_dl">
            <dt class="tit">
                <label for="redirect_url">跳转URL</label>
            </dt>
            <dd class="opt">
                <input  id="redirect_url" name="redirect_url"  class="w400 ui-input " type="text"/>
                <p class="notic">必须加(http:// 或者 https://)</p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="sort_num">排序</label>
            </dt>
            <dd class="opt">
                <input   id="sort_num" name="sort_num" class="w400 ui-input " type="text" />
                <p class="notic"></p>
            </dd>
        </dl>
        <div class="bot">
            <a href="javascript:void(0);" class="ui-btn ui-btn-sp im-submit-btn"><?= __('确认提交'); ?></a>
        </div>
    </div>
</form>
<script>
	$("#content_dl").show();
    $("#redirect_url_dl").hide();
    function change_show(val){
        if(val==1){
            $("#content_dl").show();
            $("#redirect_url_dl").hide();
        }else if(val==2){
            $("#content_dl").hide();
            $("#redirect_url_dl").show();
        }
    }
    $(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=saveSellerWxMenu&typ=json';
        $(".im-submit-btn").click(function () {
            var menu_name = $("#menu_name").val();
            if(!menu_name){
                Public.tips({type: 1, content: '请填写菜单名称'});
                return false;
            }
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: $("#publicmsg_add").serialize(),
                success: function (a) {
                    if (a.status == 200) {
                        Public.tips({type: 3,content: "保存成功！"});
                        window.location.href="index.php?ctl=Seller_Seller_SellerWx&met=wxMenu&typ=e&";
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