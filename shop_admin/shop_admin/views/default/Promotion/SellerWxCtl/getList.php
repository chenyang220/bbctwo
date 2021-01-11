<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
    <link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<?php if($data['status']==0){?>
    <form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
            <input type="hidden" name="id" value="<?=$data['id']?>">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit"><?= __('支付凭证'); ?></dt>
                    <dd class="opt">
                        <img src="<?=$data['pay_images']?>">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit"><?= __('审核状态'); ?></dt>
                    <dd class="opt">
                        <input name="seller_wxpublic_status" value="1" type="radio" checked>拒绝 
                        <input name="seller_wxpublic_status" value="2" type="radio">通过
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="review_info">审核信息</label>
                    </dt>
                    <dd class="opt">
                        <textarea id="review_info" name="review_info"  class="w400 ui-input "/></textarea>
                    </dd>
                </dl>
                <div class="bot">
                    <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn" id="submitBtn"><?= __('确认提交'); ?></a>
                </div>
            </div>
        </form> 
<?php }else{?>
    <form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit"><?= __('支付凭证'); ?></dt>
                    <dd class="opt">
                        <img src="<?=$data['pay_images']?>">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit"><?= __('审核状态'); ?></dt>
                    <dd class="opt">
                        <?php if($data['status']==1){?>
                            <p>拒绝</p>
                        <?php }else{?>
                            <p>通过</p>
                        <?php }?>    
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="review_info">审核信息</label>
                    </dt>
                    <dd class="opt">
                        <textarea id="review_info" name="review_info"  class="w400 ui-input " disabled/><?= $data['review_info']; ?></textarea>
                    </dd>
                </dl>
            </div>
        </form> 
<?php }?>    
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
<script type="text/javascript">
  // $('.submit-btn').click(function(){  
  //           var wechat_name = $("#review_info").val();
  //           if(!wechat_name){
  //               //alert("请填写审核信息");
  //               parent.Public.tips({content: '请填写审核信息'});
  //               return false;
  //           }
  //           $.ajax({
  //               type: 'POST',
  //               url: SITE_URL + '?ctl=Promotion_SellerWx&met=review&typ=json',
  //               data: $("#wechat-public-api-setting-form").serialize(),
  //               success: function (a) {
  //                   console.log(a);
  //                   if (a.status == 200) {
  //                       //alert("审核成功");
  //                       parent.Public.tips({content: '审核成功'});
  //                       window.location.reload();
  //                   } else {
  //                       parent.Public.tips({content: '审核失败'});
  //                       //alert("审核失败");
  //                   }
  //               }
  //           });
  // })  


$(function ()
{
    var t = "shen";
    if ($('#wechat-public-api-setting-form').length > 0)
    {
        $('#wechat-public-api-setting-form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: true,
            fields: {
                'review_info':'required;'
            },
            valid: function (form)
            {
                parent.$.dialog.confirm('确认审核？', function ()
                    {
                        Public.ajaxPost(SITE_URL + '?ctl=Promotion_SellerWx&met=review&typ=json', $("#wechat-public-api-setting-form").serialize(), function (data)
                        {
                            if (data.status == 200)
                            {
                                parent.Public.tips({content: '审核成功'});
                                callback && "function" == typeof callback && callback(data.data, t, window)
                            }
                            else
                            {
                                parent.Public.tips({type: 1, content: data.msg || '审核失败'});
                            }
                        });
                    },
                    function ()
                    {
                    });
            }
        }).on("click", "a#submitBtn", function (e)
        {
            $(e.delegateTarget).trigger("validate");
        });
    }
});

var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#wechat-public-api-setting-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;

</script>

