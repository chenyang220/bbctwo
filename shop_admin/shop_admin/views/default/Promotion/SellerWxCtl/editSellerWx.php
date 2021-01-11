<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
    <link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    <form method="post" id="wechat-public-api-setting-form" name="wechatPublicSettingForm">
            <input type="hidden" name="id" value="<?=$data['id']?>">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit"><?= __('公众号名称'); ?></dt>
                    <dd class="opt">
                        <input name="wx_public_name" value="<?=$data['wx_public_name']?>" type="text">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit"><?= __('开通起始时间'); ?></dt>
                    <dd class="opt">
                       
                       <span><?=$data['start_time']?></span>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit"><?= __('开通终止时间'); ?></dt>
                    <dd class="opt">
                       
                       <input name="end_time" value="<?=$data['start_time']?>" type="text" readonly="readonly">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit"><?= __('是否启用'); ?></dt>
                    <dd class="opt">
                        <input name="status" value="2" type="radio" <?php if($data['status']==2){ echo "checked";}?>>是 
                        <input name="status" value="3" type="radio" <?php if($data['status']==3){ echo "checked";}?>>否
                    </dd>
                </dl>
                <div class="bot">
                    <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn" id="submitBtn"><?= __('确认提交'); ?></a>
                </div>
            </div>
        </form> 

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
<script type="text/javascript">
  // $('.submit-btn').click(function(){  
    
  //           $.ajax({
  //               type: 'POST',
  //               url: SITE_URL + '?ctl=Promotion_SellerWx&met=editStatus&typ=json',
  //               data: $("#wechat-public-api-setting-form").serialize(),
  //               success: function (a) {
  //                   console.log(a);
  //                   if (a.status == 200) {
  //                       alert("编辑成功");
  //                       window.location.reload();
  //                   } else {
  //                       alert(a.msg);
  //                   }
  //               }
  //           });
  // })  


  $(function ()
{
    var t = "edit";
    if ($('#wechat-public-api-setting-form').length > 0)
    {
        $('#wechat-public-api-setting-form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: true,
            fields: {
                'wx_public_name':'required;'
            },
            valid: function (form)
            {
                parent.$.dialog.confirm('确认修改？', function ()
                    {
                        Public.ajaxPost(SITE_URL + '?ctl=Promotion_SellerWx&met=editStatus&typ=json', $("#wechat-public-api-setting-form").serialize(), function (data)
                        {
                            if (data.status == 200)
                            {
                                parent.Public.tips({content: '修改成功'});
                                callback && "function" == typeof callback && callback(data.data, t, window)
                            }
                            else
                            {
                                parent.Public.tips({type: 1, content: data.msg || '修改失败'});
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

