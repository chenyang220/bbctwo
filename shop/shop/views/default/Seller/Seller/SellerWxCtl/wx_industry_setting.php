<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<link href="<?= $this->view->css ?>/wx_common.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
        <div class="ncap-form-default">
            <dl class="row">
                    <dt class="tit">
                        <label for="main_industry">主行业</label>
                    </dt>
                    <dd class="opt">
                        <select id="main_industry" name="main_industry"  style="border: 1px solid #A8B3B9;width:300px;height:30px;">
                            <?php
                            foreach ($data as $key => $item) {
                                $sel ="";
                                if(!empty($list)){
                                    if($key==$list['main_industry']){
                                        $sel = "selected";
                                    }      
                                }
                                ?>
                           <option value='<?=$key;?>'  <?=$sel;?> > <?=$item['main'];?>/<?=$item['sub'];?> </option>
                           <?php
                            }
                            ?>
                        </select>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="sub_industry">副行业</label>
                    </dt>
                    <dd class="opt">
                        <select id="sub_industry" name="sub_industry"  style="border: 1px solid #A8B3B9;width: 300px;height:30px;">
                            <?php
                            foreach ($data as $key => $item) {
                                $sel ="";
                                if(!empty($list)){
                                    if($key==$list['sub_industry']){
                                        $sel = "selected";
                                    }    
                                } ?>
                                <option value='<?=$key;?>'  <?=$sel;?> > <?=$item['main'];?>/<?=$item['sub'];?> </option>
                            <?php
                            }
                            ?>
                        </select>
                        <p class="notic"></p>
                    </dd>
                </dl>
            <div class="bot">
                <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn">确认提交</a>
            </div>               
        </div>
    </form>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
<script type="text/javascript">
    $(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=saveIndustrySetting&typ=json';
        $(".submit-btn").click(function () {
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