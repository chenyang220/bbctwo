<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>

<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js?>/libs/jquery/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/libs/jquery/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>

<body>
        <form method="post" enctype="multipart/form-data" id="shop_edit_class" name="form1">
       <div class="ncap-form-default">
           <input type="hidden" id="id" name="id" value="<?=$data['id']?>">
           <input type="hidden" id="withdraw_type" name="withdraw_type" value="<?=$data['withdraw_type']?>">
           <input type="hidden" id="user_id" name="user_id" value="<?=$data['pay_uid']?>">
           <input type="hidden" id="amount" name="amount" value="<?=$data['amount']?>">
           		<?php if($data['withdraw_type'] == 0){ ?>
              <dl class="row">
                <dt class="tit">
                    <label for="cardno"><?= __('银行卡号'); ?></label>
                </dt>
                <dd class="opt">
                    <p><?=$data['cardno']?></p>
                </dd>
              </dl>
              <?php } ?>
             <dl class="row">
                <dt class="tit">
                    <label for="amount"><?= __('金额'); ?></label>
                </dt>
                <dd class="opt">
                         <p><?=$data['amount']?></p>
                </dd>
          
            </dl>
          
             <dl class="row">
                <dt class="tit">
                    <label for="add_time"><?= __('创建时间'); ?></label>
                </dt>
                <dd class="opt">
                      <p><?=date("Y-m-d H:i:s",$data['add_time'])?></p>
                </dd>
          
            </dl>

			<?php if($data['withdraw_type'] == 0){ ?>
            <dl class="row">
                <dt class="tit">
                    <label for="bank"><?= __('银行'); ?></label>
                </dt>
                <dd class="opt">
                      <p><?=$data['bank']?></p>
                </dd>
            </dl>
                     <dl class="row">
                <dt class="tit">
                    <label for="bank_user"><?= __('开户人姓名'); ?></label>
                </dt>
                <dd class="opt">
                      <p><?=$data['cardname']?></p>
                </dd>
            </dl>
            <dl class="row">
              <dt class="tit">
                    <label for="fee"><?= __('手续费'); ?></label>
              </dt>
              <dd class="opt">
                      <p><?=$data['fee']?></p>
              </dd>
            </dl>
            <?php } ?>
               <dl class="row">
              <dt class="tit">
                    <label for="is_succeed"><?= __('状态'); ?></label>
              </dt>
              <dd class="opt">
                  <?php if($data['is_succeed'] < 3){?>
                    <input type="radio" name="is_succeed" value="3" <?php if($data['is_succeed'] == 3){?>checked="checked"<?php }?>><?= __('通过'); ?>
                    <input type="radio" name="is_succeed" value="4" <?php if($data['is_succeed'] == 4){?>checked="checked"<?php }?>><?= __('不通过'); ?>
                    <?php }?>
                  <?php if($data['is_succeed'] == 3){?>
                       <p><?= __('通过'); ?></p>
                    <?php } ?>
                  <?php if($data['is_succeed'] == 4){?>
                      <p><?= __('不通过'); ?></p>
                  <?php } ?>
              </dd>
            </dl>
            <?php if($data['withdraw_type'] == 0){ ?>
           <dl class="row">
               <dt class="tit">
                   <label for="bankflow"><?= __('银行流水账号'); ?></label>
               </dt>
               <dd class="opt">
                   <?php if($data['is_succeed'] < 3){?>
                       <input type="text" name="bankflow" class="ui-input">
                   <?php }else {?>
                       <p><?=$data['bankflow']?></p>
                   <?php }?>
               </dd>
           </dl>
           <?php } ?>
           <dl class="row">
               <dt class="tit">
                   <label for="remark"><?= __('备注'); ?></label>
               </dt>
               <dd class="opt">
                   <?php if($data['is_succeed'] < 3){?>
                       <textarea name="remark" id="remark" class="ui-input"></textarea>
                   <?php }else {?>
                       <p class="ellipsis"><?=$data['remark']?></p>
                   <?php }?>
               </dd>
           </dl>
        </div>
        <?php if($data['withdraw_type'] == 1){ ?>
          <div style="padding-left: 50px;">
          温馨提示：
           <p>1、商户号（或同主体其他非服务商商户号）已入驻90日</p>
           <p>2、截止今日回推30天，商户号（或同主体其他非服务商商户号）连续不间断保持有交易</p>
           <p>3、登录微信支付商户平台-产品中心，开通企业付款。</p>
          </div>
        <?php } ?>
    </form>

    <script>



function initPopBtns()
{
    if('<?=$data['is_succeed']?>' < 3)
    {
        var t = ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
        api.button({
            id: "confirm", name: t[0], focus: !0, callback: function ()
            {
                postData(oper, rowData.id);
                return cancleGridEdit(),$("#shop_edit_class").trigger("validate"), !1
            }
        }, {id: "cancel", name: t[1]})
    }

}
function postData(t, e)
{
 
	$_form.validator({
               messages: {
                    required: "<?= __('请填写该字段'); ?>",
           },
            fields: {
                
            },

        valid: function (form)
        {
            var id = $.trim($("#id").val());
            var withdraw_type = $("#withdraw_type").val();
            var user_id = $("#user_id").val();
            var amount = $("#amount").val();
            var is_succeed = $.trim($("input[name='is_succeed']:checked").val());
            var bankflow = $.trim($("input[name='bankflow']").val());
            var remark = $.trim($("textarea[name='remark']").val());

			params ={
                id:id,
                withdraw_type:withdraw_type,
                user_id:user_id,
                amount:amount,
                is_succeed: is_succeed,
                bankflow: bankflow,
                remark: remark,
			};

			if(withdraw_type == 0)
			{
				var url = SITE_URL +"?ctl=Paycen_PayWithdraw&met=editWithdrawRow&typ=json";
			}else if(withdraw_type == 1){
				var url = SITE_URL +"?ctl=Paycen_PayWithdraw&met=editWithdrawRowWx&typ=json";
			}else{
        var url = SITE_URL +"?ctl=Paycen_PayWithdraw&met=editWithdrawRowZfb&typ=json";
      }
			

			Public.ajaxPost(url, params, function (e)
			{
				if (200 == e.status)
				{
					parent.parent.Public.tips( {content:"<?= __('修改成功！'); ?>"});
                                        callback && "function" == typeof callback && callback(e.data, t, window)
//					 var callback = frameElement.api.data.callback;
//                                            callback();
				}
				else
				{
					parent.parent.Public.tips({type: 1, content:  "<?= __('修改失败！'); ?>" + e.msg})
				}
			})
        },
        ignore: ":hidden",
        theme: "yellow_bottom",
        timely: 1,
        stopOnError: !0
    });
}
function cancleGridEdit()
{
    null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
}
function resetForm(t)
{
    $_form.validate().resetForm();
    $("#user_account").val("");
    $("#add_user_money").val("");
    $("#user_id").val("");
    $("#record_desc").val("");
}
var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#shop_edit_class"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
initPopBtns();

    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
