<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

 
    <form  method="post" id="form" >
        <input type='hidden' name='shop_id' value="<?=$re['shop_id']?>">
    <div class="form-style">
        <dl>
            <dt><?=__('店铺名称：')?></dt>
            <dd><?=$re['shop_name']?></dd>
        </dl>
<!--        <dl>
            <dt>店铺分类：</dt>
            <dd></dd>
        </dl>-->
        <dl>
            <dt><?=__('店铺等级：')?></dt>
            <dd><?=$re['shop_grade']?></dd>
        </dl>
<!--        <dl>
            <dt>主营商品：</dt>
            <dd>
                <textarea style="width:300px;height:100px;"></textarea>
              <p class="hint">此处理店铺页logo；<br />建议使用宽200像素-高60像素内的GIF或PNG透明图片；点击下方"确认提交"按钮后生效。</p>
            </dd>
        </dl>-->
        <dl>
            <dt><?=__('PC店铺logo：')?></dt>
            <dd>
                <p class="pic" style="width:266px;height:80px;"><img id="logo_img" src="<?php if(!empty($re['shop_logo'])){ echo Img::url($re['shop_logo']);}else{ echo $this->web['shop_head_logo'];}?>" height="80" width="266" /></p>
                <p class="upload-button"><input type="hidden" id="logo" name="shop[shop_logo]" value="<?=$re['shop_logo']?>" /><div  id='logo_upload' class="lblock upload-bg"><i class="iconfont icon-tupianshangchuan" ></i><?=__('图片上传')?></div></p>                
                <p class="hint"><?=__('此处为PC店铺页logo；')?><br /><?=__('建议使用宽200像素*高60像素内的GIF或PNG透明图片；点击下方"确认提交"按钮后生效。')?></p>
            </dd>
        </dl>
        <dl>
            <dt><?=__('APP/WAP店铺logo：')?></dt>
            <dd>
                <p class="pic" style="width:85px;height:80px;"><img id="wap_logo_img" src="<?php if(!empty($re['wap_shop_logo'])){ echo $re['wap_shop_logo'];}else{ echo $this->web['shop_web_logo'];}?>" height="80" width="85" /></p>
                <p class="upload-button"><input type="hidden" id="wap_logo" name="shop[wap_shop_logo]" value="<?=$re['wap_shop_logo ']?>" /><div  id='wap_logo_upload' class="lblock upload-bg"><i class="iconfont icon-tupianshangchuan" ></i><?=__('图片上传')?></div></p>
                <p class="hint"><?=__('此处为APP/WAP店铺页logo；')?><br /><?=__('建议使用宽64像素*高64像素内的GIF或PNG透明图片；点击下方"确认提交"按钮后生效。')?></p>
            </dd>
        </dl>
        <!--<dl>
            <dt>店铺头像：</dt>
            <dd>
           		<p class="pic" style="width:200px;height:60px;"><img id="logo_img" src="<{if $de.logo}><{$de.logo}><{else}>image/default/seller/default_logo.png<{/if}>" height="60" width="200" /></p>
                <p class="upload-button"><input type="hidden" id="logo" name="shop[banner]" value="<{$de.logo}>" /><a class="button button_black" href="javascript:uploadfile('图片上传','logo',200,60,'shop');"><i class="iconfont icon-upload-alt"></i>图片上传</a></p>                
                <p class="hint">此处为店铺方形头像；<br/> 建议使用宽100像素*高100像素内的方型图片；点击下方"确认提交"按钮后生效。</p>
            </dd>
        </dl>-->
        <dl>
            <dt><?=__('PC店铺条幅：')?></dt>
            <dd>
                <p class="pic" style="max-width:800px;height:150px;"><img class="wp100" id="banner_img" src="<?php if(!empty($re['shop_banner'])){ echo Img::url($re['shop_banner']);} ?>" /></p>
                <p class="upload-button"><input type="hidden" id="banner" name="shop[shop_banner]" value="<?=$re['shop_banner']?>" /><div  id='banner_upload' class="lblock upload-bg"><i class="iconfont icon-tupianshangchuan"></i><?=__('图片上传')?></div></p>
                <p class="hint"><?=__('此处为店铺条幅：')?><br /><?=__('建议使用宽1200像素*高150像素的图片；点击下方"确认提交"按钮后生效。')?></p>
            </dd>
        </dl>

         <dl>
            <dt><?=__('APP/WAP/小程序店铺条幅：')?></dt>
            <dd>
                <p class="pic" style="max-width:800px;height:150px;"><img class="wp100" id="banner_wap_img" src="<?php if(!empty($re['shop_wap_banner'])){ echo Img::url($re['shop_wap_banner']);} ?>" /></p>
                <p class="upload-button"><input type="hidden" id="banner_wap" name="shop[shop_wap_banner]" value="<?=$re['shop_wap_banner']?>" /><div  id='banner_wap_upload' class="lblock upload-bg"><i class="iconfont icon-tupianshangchuan"></i><?=__('图片上传')?></div></p>
                <p class="hint"><?=__('此处为店铺条幅：')?><br /><?=__('建议使用宽1200像素*高150像素的图片；点击下方"确认提交"按钮后生效。')?></p>
            </dd>
        </dl>
        <?php if($shop_domain['shop_domain']['config_value']){
            $domain_list['shop_edit_domain'] = intval($domain_list['shop_edit_domain']);
            $domain_modify_frequency = Web_ConfigModel::Value('domain_modify_frequency');
            $sy_ts = $domain_modify_frequency - $domain_list['shop_edit_domain'];
            ?>
        <dl>
            <dt><?=__('二级域名：')?> </dt>
            <dd>
                <input type="text" class="text" name="shop[shop_domain]" value="<?=$re['shop_domain']?>" <?php if($sy_ts<=0){?>readonly="readonly"<?php }?> />
                <p style="color: red;" class="hint">域名格式：xxx.主域名 示例:slab.yuanfeng.cn</p>
                <?php if($shop_domain['is_modify']['config_value']){ ?>
                    <p class="hint">
                        <?php if($sy_ts > 0){?>
                            <?=__('可留空，域名长度应为:')?>
                            <?= $shop_domain['domain_length']['config_value']?>
                            <?=__('还可以修改')?><?=$sy_ts?>
                            <?=__('次')?>
                        <?php }else{?>
                            <?=__('修改次数已达上线')?>
                        <?php }?>
                    </p>
                <?php }else{ ?>
                <p class="hint"><?=__('不可修改')?></p>
                <?php }?>
            </dd>
        </dl>
        <?php }?>
        <dl>
            <dt><?=__('QQ：')?></dt>
            <dd><input type="text" class="text" name="shop[shop_qq]" value="<?=$re['shop_qq']?>" /></dd>
        </dl>
        <dl>
            <dt><?=__('旺旺：')?></dt>
            <dd><input type="text" class="text" name="shop[shop_ww]" value="<?=$re['shop_ww']?>" /></dd>
        </dl> 
        <dl>
            <dt><?=__('电话：')?></dt>
              <dd><input type="text" class="text" id="re_user_mobile" name="shop[shop_tel]" value="<?=$re['shop_tel']?>" />
                  <input type="hidden"  id="area_code" name="shop[area_code]" value="<?=$re['area_code']?>" />
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
    </form>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/upload/upload_image.js" charset="utf-8"></script>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">

<script>
    $("#re_user_mobile").intlTelInput({
//        utilsScript: "<?//= $this->view->js ?>///utils.js"
    });
    $(document).ready(function(){
         var ajax_url = './index.php?ctl=Seller_Shop_Setshop&met=editShop&typ=json';
        $('#form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: false,
            rules: {
                tel:function(){
                    var area_code = $('#area_code').val();
                    var contacts_phone = $('#re_user_mobile').val();
                    var reg = /^1[3-9]\d{9}$/;
                    if(area_code==86 && !reg.test(contacts_phone)){
                        return '<?=__('请输入正确的手机号码')?>';
                    }
                },
                 qq: [/^\d{5,10}$/, '<?=__('请输入正确qq')?>'],

            },

            fields: {
                'shop[shop_qq]': 'qq',
                'shop[shop_tel]':'tel',
            },
           valid:function(form){
                //表单验证通过，提交表单
                $.ajax({
                    url: ajax_url,
                    data:$("#form").serialize(),
                    success:function(a){
                        if(a.status == 200)
                        {
                           Public.tips.success("<?=__('操作成功！')?>");
                          // setTimeout(' location.href="./index.php?ctl=Seller_Shop_Setshop&met=index&typ=e"',3000); //成功后跳转

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
 <script>
    //图片上传
    $(function(){

        var $imagePreview, $imageInput, imageWidth, imageHeight,shopWidth;
        $('#banner_upload,#banner_wap_upload, #logo_upload,#wap_logo_upload').on('click', function () {

            if ( this.id == 'banner_upload' ) {
                $imagePreview = $('#banner_img');
                $imageInput = $('#banner');
                imageWidth = 1200, imageHeight = 150,shopWidth = 1200;
            } else if ( this.id == 'logo_upload' ) {
                $imagePreview = $('#logo_img');
                $imageInput = $('#logo');
                imageWidth = 200, imageHeight = 60,shopWidth = 800;
            } else if(this.id == 'wap_logo_upload'){
                $imagePreview = $('#wap_logo_img');
                $imageInput = $('#wap_logo');
                imageWidth = 64, imageHeight = 64,shopWidth = 800;
            }else if(this.id == 'banner_wap_upload'){
                $imagePreview = $('#banner_wap_img');
                $imageInput = $('#banner_wap');
                imageWidth = 1200, imageHeight = 150,shopWidth = 1200;
            }
            $.dialog({
                title: '图片裁剪',
                content: "url: <?= Yf_Registry::get('url') ?>?ctl=Upload&met=cropperImage&typ=e",
                data: { width: imageWidth, height: imageHeight, callback: callback },    // 需要截取图片的宽高比例
                width: shopWidth,
                lock: true
            })
        });

        function callback ( respone , api ) {
            $imagePreview.attr('src', respone.url);
            $imageInput.attr('value', respone.url);
            api.close();
        }

        if ( window.isIE8 ) {
            $('#banner_upload,#banner_wap_upload, #logo_upload, #wap_logo_upload').off('click');
            new UploadImage({
                thumbnailWidth: 1200,
                thumbnailHeight: 150,
                imageContainer: '#banner_wap_img',
                uploadButton: '#banner_wap_upload',
                inputHidden: '#banner_wap'
            });
            new UploadImage({
                 thumbnailWidth: 200,
                 thumbnailHeight: 60,
                 imageContainer: '#logo_img',
                 uploadButton: '#logo_upload',
                 inputHidden: '#logo'
             });

            new UploadImage({
                thumbnailWidth: 1200,
                thumbnailHeight: 150,
                imageContainer: '#banner_img',
                uploadButton: '#banner_upload',
                inputHidden: '#banner'
            });
            new UploadImage({
                thumbnailWidth: 200,
                thumbnailHeight: 60,
                imageContainer: '#wap_logo_img',
                uploadButton: '#wap_logo_upload',
                inputHidden: '#wap_logo'
            });
       
        }

    })
</script>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>

