<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<div class="form-style">
    <form method="post" id="form" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Seller_SellerUser&met=editUsers">
        <dl>
            <dt class="sale_width_reset"><i>*</i><?=__('会员名称')?>：</dt>
            <dd>
                <input type="text" name="user_name" readonly="readonly" value="<?=$data['user_name']?>" class="text w200"/>
            </dd>
        </dl>
        <dl>
            <dt class="sale_width_reset"><i>*</i><?=__('会员邮箱')?>：</dt>
            <dd>
                <input type="text" name="user_email" readonly="readonly" value="<?=$data['user_email']?>" class="text w200"/>
            </dd>
        </dl>
        <dl>
            <dt class="sale_width_reset"><i>*</i><?=__('会员手机')?>：</dt>
            <dd>
                <input type="text" name="user_mobile" readonly="readonly" value="<?=$data['user_mobile']?>" class="text w200"/>
            </dd>
        </dl>
        <dl>
            <dt class="sale_width_reset"><i>*</i><?=__('会员性别')?>：</dt>
            <dd>
                <input type="text" name="user_sex" value="<?php if($data['user_sex']==1){ echo "男";}else{echo "女";}?>" class="text w200"/>
            </dd>
        </dl>

        <dl>
            <dt class="achieve_width_reset"><i>*</i><?=__('会员标签')?>：</dt>
            <dd>
                <select id="user_label" name="user_label" class="w70 vt valid">
                        <option value><?=__('请选择')?></option>
                    <?php
                        foreach($label as $key=>$value)
                        { ?>
                            <option value="<?=$value['label_id']?>" <?php if($value['label_id']==$data['user_label_id']){ echo "selected"; }?> ><?=($value['label_name'])?></option>
                        <?php }?>
                    ?>
                </select>
               
            </dd>
        </dl>

        <dl>
            <dt class="sale_width_reset"><i>*</i><?=__('真实姓名')?>：</dt>
            <dd>
                <input type="text" name="user_realname" value="<?=$data['user_realname']?>" class="text w200"/>
            </dd>
        </dl>
        <dl>
            <dt><?=__('出生日期')?>：</dt>
            <dd>
                <input type="text" value="" autocomplete="off" name="user_birthday" id="start_date" class="text w100"/><em><i class="iconfont icon-rili"></i></em></em>
            </dd>
        </dl>
       

     
      
        <dl>
            <dt></dt>
            <dd>
                <input type="submit" class="button button_blue bbc_seller_submit_btns" value="提交"  />
                <input type="hidden" name="user_id" value="<?=$data['user_id']?>" />
            </dd>
        </dl>
    </form>
</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/upload/upload_image.js" charset="utf-8"></script>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    $(document).ready(function(){
        var flag = true;//控制表单提交
        $('#start_date').datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            onShow:function( ct ){
                this.setOptions({
                    maxDate:$('#query_end_date').val() ? $('#query_end_date').val() : false
                })
            }
        });

       

       

       

        $('#form').validator({
            debug:true,
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: false,
			rules: {
               
            },
            fields: {
                                    
            },
            valid: function(form){
                var me = this;
                // 提交表单之前，hold住表单，并且在以后每次hold住时执行回调
                me.holdSubmit(function(){
                    Public.tips.error('正在处理中...');
                });
                if(flag)
                {
                    $.ajax({
                        url: "index.php?ctl=Seller_Seller_SellerUser&met=editUsers&typ=json",
                        data: $(form).serialize(),
                        type: "POST",
                        success:function(e){
                            if(e.status == 200)
                            {
                                flag = false;
                                Public.tips.success('编辑成功!');
                                location.href="index.php?ctl=Seller_Seller_SellerUser&met=user&typ=e"; //成功后跳转
                            }
                            else
                            {
                                Public.tips.error('编辑失败');
                            }
                            me.holdSubmit(false);
                        }
                    });
                }
            }
        });
    });
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>

