<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
</head>
<body>

    <form id="form" action="#" method="post" >
        <div class="form-style">
            <input type="hidden" name="supplier_id" id="id" value="<?=$data['supplier_id']?>" />
            <dl class="dl">
                <dt><i>*</i><?=__('店铺标签：')?></dt>
                <dd >
                     <select name="label_id">
                        <option value=""><?= __('请选择') ?></option>
                        <?php if (!empty($Label_Base)) { ?>
                            <?php foreach ($Label_Base as $key => $val) { ?>
                                <option value="<?= $val['id']; ?>" <?=$Shop_Base['label_id'] == $val['id'] ? "selected" : '';?>><?= $val['label_name']; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </dd>
            </dl>   
            <dl class="dl">
                <dt><?=__('申请原因：')?></dt>
                <dd><textarea name="label_remarks" id="detail" style="width:300px;height:100px;"><?=$Shop_Base['label_remarks']?></textarea></dd>
            </dl>
            <?php if ($Shop_Base['label_is_check'] == 1) {?>
                <dl>
                    <dt></dt>
                    <dd><input type="submit" class="button bbc_seller_submit_btns" value="<?=__('确认提交')?>" /></dd>
                </dl>
            <?php } else { ?>
                <dl>
                    <dt></dt>
                    <dd style="color: red;"><?=__('请等待平台审核！')?></dd>
                </dl>
            <?php } ?>
        </div>
    </form>
<script>
       $(document).ready(function(){
    
        var ajax_url = './index.php?ctl=Seller_Shop_Info&met=addTsSet&typ=json';
         
        $('#form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: false,
            rules: {
                  tel:[/^1[3-9]\d{9}$/,'<?=__('请输入正确的手机号码')?>'],
            },
            fields: {
                'label_id': 'required',
                'label_remarks':'required',
            },
           valid:function(form){
               
               var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                //表单验证通过，提交表单
                $.ajax({
                    url: ajax_url,
                    data:$("#form").serialize(),
                    success:function(a){
                        if(a.status == 200)
                        {
                            Public.tips.success("<?=__('操作成功,请等待平台审核！')?>");
                            setTimeout('location.href="./index.php?ctl=Seller_Shop_Info&met=tsSet&typ=e"',3000); //成功后跳转
                        }
                        else
                        {
                            Public.tips.error("<?=__('操作失败！')?>");
                        }
                    }
                });
            }

        });
    });
</script>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>