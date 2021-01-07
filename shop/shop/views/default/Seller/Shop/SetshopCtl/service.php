<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<script type="text/javascript" src="<?= $this -> view -> js_com ?>/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?= $this -> view -> js_com ?>/ueditor/ueditor.parse.js"></script>
<script type="text/javascript" src="<?= $this -> view -> js_com ?>/ueditor/ueditor.all.js"></script>
<style>
    #ed_service .edui-default .edui-for-dialogbuttonuploadimage .edui-icon {
        display: none !important;
    }
</style>
    <input type='hidden' name='shop_id' value="<?=$data['shop_id']?>">
    <div class="form-style">
        <dl>
            <dt><?=__('售后服务：')?></dt>
            <dd>
                <textarea class="service" name="service" id="ed_service" style="width: 100%;height:300px;"  type="text/plain"></textarea>
            </dd>
        </dl>
        <dl>
            <dt></dt>
            <dd>
                <div class="ftx-03 mt10" style="color:red;"> 温馨提示：您最多可以输入2000个字符<br></div>
            </dd>
        </dl>
        
        <dl>
            <dt></dt>
            <dd>
            <input type="hidden" name="op" value="edit" />
            <input type="submit" class="button bbc_seller_submit_btns" value="<?=__('确认提交')?>" />
            </dd>
        </dl>
    </div>
<script>
    $(function(){
        var flag = true;
        //售后服务编辑器初始化
        var service = UE.getEditor('ed_service', {
            toolbars: [
                [
                    'bold', 'italic', 'underline', 'forecolor', 'justifyleft', 'justifycenter', 'justifyright', 'removeformat', 'rowspacingtop', 'rowspacingbottom', 'lineheight', 'paragraph', 'fontsize'
                ]
            ],
            autoClearinitialContent: true,
            wordCount: true, //关闭字数统计
            maximumWords:2000, //最大长度
            elementPathEnabled: false, //关闭elementPath
        });
        service.ready(function() {//编辑器初始化完成再赋值
            service.setContent('<?=html_entity_decode(addslashes($data['shop_common_service']))?>');//赋值给UEditor
        });

        $(".bbc_seller_submit_btns").click(function()
        {
            if(flag){
                flag = false;
                //判断内容最大长度200
                var content_length = service.getContentTxt().length;
                if(content_length > 2000) {
                    Public.tips.error('<?=__('您最多可以输入200个字符！')?>');
                    return false;
                }
                var ajax_url = './index.php?ctl=Seller_Shop_Setshop&met=editShopCommonService&typ=json';
                // var common_service = $(".service").text();
                var common_service = UE.getEditor('ed_service').getContent();
                $.post(ajax_url,{"common_service":common_service} ,function(a) {

                    flag = true;
                    if (a.status == 200)
                    {
                        Public.tips.success('<?=__('操作成功！')?>');
                        setTimeout(function(){
                           window.location.href=document.referrer;
                        },500)
                    }
                    else
                    {
                        if (a.msg != 'failure')
                        {
                            Public.tips.error(a.msg);
                        }
                        else
                        {
                            Public.tips.error('<?=__('操作失败！')?>');
                        }

                    }
                })
            }
        })
    })
</script>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>

