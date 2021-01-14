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
                      <select name="label_id" id="label_id_select">
                          <option value=""><?= __('请选择') ?></option>
                          <?php if (!empty($Label_Base)) { ?>
                              <?php foreach ($Label_Base as $key => $val) { ?>
                                  <option value="<?= $val['id']; ?>" date-name="<?= $val['label_name']; ?>"><?= $val['label_name']; ?></option>
                              <?php } ?>
                          <?php } ?>
                      </select>
                </dd>
            </dl>  
            <dl class="dl">
                <dt><?=__('已选商品标签：')?></dt>
                <dd id="select_label_name" class="select_cat_name">
                    <?php 
                        if (!empty($label_id_arr)) {
                            foreach ($label_id_arr as $key => $label_id) {
                                ?>
                                    <span><?=$label_name_arr[$label_id]?></span>
                                <?php
                            }
                        }
                    ?>
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
        var label_id_arr = [];
        $('#label_id_select').change(function(){
            if (label_id_arr.length == 0) {
                $("#select_label_name").html('');
            }
              var id = $("select[name='label_id']").val();
              var name = $("select[name='label_id']  option:selected").html();
              var html =  $("#select_label_name").html();
              if (!label_id_arr[id]) {
                  html += "<span>"+ name + "<a href='javascript:void(0)' onclick=del_label_name("+id+")>X</a></span>";
                  $("#select_label_name").html(html);
                  label_id_arr[id] = name;
              }        
        });
        function del_label_name (id) {
             var html = '';
             if (id) {
              delete label_id_arr[id];
             }
             
             for (label_id in label_id_arr) {
                html += "<span>"+ label_id_arr[label_id] + "<a href='javascript:void(0)' onclick=del_label_name("+label_id+")>X</a></span>";
             }
            $("#select_label_name").html(html);
        }

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
                var label_id_str = '';
                for (label_id in label_id_arr) {
                    label_id_str = label_id_str + "," + label_id;
                }

                var label_remarks = $("#detail").val();

               var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                //表单验证通过，提交表单
                $.ajax({
                    url: ajax_url,
                    data:{label_id:label_id_str,label_remarks:label_remarks},
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