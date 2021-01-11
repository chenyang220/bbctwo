<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<div class="form-style">
    <form method="post" id="form" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Seller_SellerUser&met=addLabels">
        <dl>
            <dt class="sale_width_reset"><i>*</i><?=__('标签名称')?>：</dt>
            <dd>
                <input type="text" name="label_name"  value="<?=$data['label_name']?>" class="text w200"/>
            </dd>
        </dl>
        <dl>
            <dt class="sale_width_reset"><i>*</i><?=__('标签排序')?>：</dt>
            <dd>
                <input type="text" name="label_sort"  value="<?=$data['label_sort']?>" class="text w200"/>
            </dd>
        </dl>
        <dl>
            <dt class="sale_width_reset"><i>*</i><?=__('标签描述')?>：</dt>
            <dd>
                <textarea name="label_desc"  class="sale_width_reset"><?=$data['label_desc']?></textarea>
            </dd>
        </dl>
        <dl>
            <dt><i>*</i><?=__('标签图片')?>：</dt>
            <dd>
                <div id="" class="ncsc-upload-thumb voucher-pic">
                    <p><i class="icon-picture"></i></p>
                </div>
                <p class="pic image_review" style="width:200px;height:200px;">
                    <img id="image_review" src="<?=$data['label_img']?>" height="200" width="200" />
                </p>
                <p class="upload-button">
                    <input type="hidden" id="label_img" name="label_img" value="<?=$data['label_img']?>" />
                    <div  id='image_upload' class="lblock bbc_img_btns"><i class="iconfont icon-tupianshangchuan" ></i><?=__('图片上传')?></div>
                </p>
                
            </dd>
        </dl>

       

      
       

     
      
        <dl>
            <dt></dt>
            <dd>
                <input type="submit" class="button button_blue bbc_seller_submit_btns" value="提交"  />
                <input type="hidden" name="label_id" value="<?=$data['label_id']?>" />
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
        var combo_end_time = $.trim("<?=$combo['combo_end_time']?>");
        var maxdate =  new Date(Date.parse(combo_end_time.replace(/-/g, "/")));

		var currDate = new Date();
		var date = currDate.getDate();
		// date = date + 1;
		currDate.setDate(date);
        $('#start_date').datetimepicker({
            controlType: 'select',
            timepicker:false,
            format:'Y-m-d',
            minDate:currDate,
            onShow:function( ct ){
                this.setOptions({
                    maxDate:($('#end_date').val() && (new Date(Date.parse($('#end_date').val().replace(/-/g, "/"))) < maxdate))?(new Date(Date.parse($('#end_date').val().replace(/-/g, "/")))):maxdate
                })
            }
        });
        $('#end_date').datetimepicker({
            controlType: 'select',
            timepicker:false,
            format:'Y-m-d',
            maxDate:maxdate,
            onShow:function( ct ){
                this.setOptions({
                    minDate:($('#start_date').val() && (new Date(Date.parse($('#start_date').val().replace(/-/g, "/")))) > (new Date()))?(new Date(Date.parse($('#start_date').val().replace(/-/g, "/")))):(new Date())
                })
            }
        });

       

        //图片上传
        $('#image_upload').on('click', function () {
            $.dialog({
                title: '图片裁剪',
                content: "url: <?= Yf_Registry::get('url') ?>?ctl=Upload&met=cropperImage&typ=e",
                data: {width:200,height:200 , callback: callback },    // 需要截取图片的宽高比例
                width: '800px',
                lock: true
            })
        });


        function callback( respone , api ) {
            $('#image_review').attr('src', respone.url);
            $('.image_review').show();
            $('#label_img').attr('value', respone.url);
            api.close();
        }
       

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
                        url: "index.php?ctl=Seller_Seller_SellerUser&met=editLabels&typ=json",
                        data: $(form).serialize(),
                        type: "POST",
                        success:function(e){
                            if(e.status == 200)
                            {
                                flag = false;
                                Public.tips.success('修改成功!');
                                location.href="index.php?ctl=Seller_Seller_SellerUser&met=label&typ=e"; //成功后跳转
                            }
                            else
                            {
                                Public.tips.error('修改失败');
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

