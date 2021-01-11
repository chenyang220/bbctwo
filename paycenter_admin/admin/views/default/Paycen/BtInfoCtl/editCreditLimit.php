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
              <dl class="row">
                <dt class="tit">
                    <label for="user_nickname"><?= __('用户账号：'); ?></label>
                </dt>
                    <dd class="opt">
                        <?=$data['user_name']?>
                    </dd>
              </dl>

              <dl>
                <dt class="tit">
                       <label for="bt_type"><?= __('额度类型:'); ?></label>
                </dt>
                <dd class="opt">
                  <input type="radio" name="bt_type" value="1" <?php if($data['bt_type'] == 1){?>checked="checked"<?php }?>><?= __('定额'); ?>
                  <input type="radio" name="bt_type" value="2" <?php if($data['bt_type'] == 2){?>checked="checked"<?php }?>><?= __('不定额'); ?>
                </dd>
              </dl>

               <dl class="row user_credit_limit">
                   <dt class="tit">
                       <label for="user_credit_limit"><?= __('额度设置:'); ?></label>
                   </dt>
                   <dd class="opt">
                       <input type="text" name="user_credit_limit"  id="user_credit_limit" value="<?=$data['user_credit_limit']?>">
                   </dd>
               </dl>
               <dl class="row">
                   <dt class="tit">
                       <label for="user_credit_cycle"><?= __('还款周期：'); ?></label>
                   </dt>
                   <dd class="opt">
                      <select id="user_credit_cycle" name="user_credit_cycle" style="height: 28px; line-height: 18px;">
                         <?php $cycle = $data['user_credit_cycle'];?>
                          <option value="1" <?php if($cycle == 1){?>selected=''<?php }?>>一个月</option>
                          <option value="2" <?php if($cycle == 2){?>selected=''<?php }?>>二个月</option>
                          <option value="3" <?php if($cycle == 3){?>selected=''<?php }?>>三个月</option>
                          <option value="4" <?php if($cycle == 4){?>selected=''<?php }?>>四个月</option>
                          <option value="5" <?php if($cycle == 5){?>selected=''<?php }?>>五个月</option>
                          <option value="6" <?php if($cycle == 6){?>selected=''<?php }?>>六个月</option>
                      </select>
                       <?= __('白条以月为周期，最低一个月'); ?>
                   </dd>
               </dl>
  
            <input id="user_id"  name="user_id" value="<?=$data['user_id']?>"  type="hidden"/>

          
        </div>
    </form>

<script>
$(function(){
    //初始化白条
    var bt_type = "<?=$data['bt_type'];?>"
        bt_type == 2 && $('.user_credit_limit').css('display','none');
    //点击按钮的界面显示布局
    $('input[name=bt_type]').on('change',function(){
       if($(this).val()==1){
          $('.user_credit_limit').css('display','block');
       }else{
          $('.user_credit_limit').css('display','none');
       }
    })
})


  function initPopBtns()
  {
      var t = "add" == oper ? [__('<?= __('保存'); ?>'), __('<?= __('关闭'); ?>')] : [__('<?= __('确定'); ?>'), __('<?= __('取消'); ?>')];
      api.button({
          id: "confirm", name: t[0], focus: !0, callback: function ()
          {
              
              postData(oper, rowData.shop_class_id);
             return cancleGridEdit(),$("#shop_edit_class").trigger("validate"), !1
          }
      }, {id: "cancel", name: t[1]})
  }
  function postData(t, e)
  {
  	$_form.validator({

          fields: {
              user_credit_limit:"required;range[0~]",
              user_credit_cycle: "required;integer[+]"
          },
          valid: function (form)
          {
              var user_id = $.trim($("#user_id").val()),
                  bt_type = $.trim($('input[name=bt_type]:checked').val());
                  user_credit_limit = $.trim($("#user_credit_limit").val()),
                  user_credit_cycle = $.trim($("#user_credit_cycle").val());


  			params ={
                  bt_type:bt_type,
                  user_id:user_id,
                  user_credit_limit: user_credit_limit,
                  user_credit_cycle: user_credit_cycle,
  			};
  			Public.ajaxPost(SITE_URL +"?ctl=Paycen_PayInfo&met=editCreditInfo&typ=json", params, function (e)
  			{
  				if (200 == e.status)
  				{
  					parent.parent.Public.tips( {content:"<?= __('修改成功！'); ?>"});
  					 var callback = frameElement.api.data.callback;
                                              callback();
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
      $("#shop_class_name").val("");
      $("#shop_class_deposit").val("");
      $("#shop_class_displayorder").val("");
  }
  var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#shop_edit_class"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
  initPopBtns();

      </script>
  <?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
