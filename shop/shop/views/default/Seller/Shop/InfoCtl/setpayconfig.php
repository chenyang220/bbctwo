<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
          <link href="<?= $this->view->css ?>/seller_center.css?ver=<?=VER?>" rel="stylesheet">


<div class="tabmenu">
	<ul>
  	<li ><a href="./index.php?ctl=Seller_Shop_Info&met=info&typ=e&act=category"><?=__('经营类目')?></a></li>
    <?php if($shop['shop_self_support']=="false"){ ?> 
    <li><a href="./index.php?ctl=Seller_Shop_Info&met=info&typ=e&act=info"><?=__('店铺信息')?></a></li>
    <li ><a href="./index.php?ctl=Seller_Shop_Info&met=info&typ=e&act=renew"><?=__('续签申请')?></a></li>
    <?php } ?>
    <!-- <li><a href="./index.php?ctl=Seller_Shop_Info&met=info&typ=e&act=createQrCode"><?=__('收款码')?></a></li>
     -->
    <?php if(Yf_Registry::get('yunshanstatus') == 1) {?>
     <li class="active bbc_seller_bg"><a href="./index.php?ctl=Seller_Shop_Info&met=info&typ=e&act=setpayconfig"><?=__('支付入网')?></a></li>
    <?php } ?>
  </ul>
</div>
<div>
 
    <form  method="post" id="form"   action=""    onsubmit="return false ;" >       
    <div class="form-style">
        <dl>
            <dt><?=__('商户名称：')?></dt>
            <dd><input type="text" class="text" name="payshopname" value="<?=$shoppay["payshopname"]?>" /></dd>
        </dl>
        <dl>
            <dt><?=__('APP支付商户号：')?></dt>
            <dd><input type="text" class="text" name="payshopnumer" value="<?=$shoppay["payshopnumer"]?>" /></dd>
        </dl> 
        
          <dl>
            <dt><?=__('C扫B支付商户号：')?></dt>
            <dd><input type="text" class="text" name="cbpayshopnumer" value="<?=$shoppay["cbpayshopnumer"]?>" /></dd>
        </dl> 
        
          <dl>
            <dt><?=__('小程序支付商户号：')?></dt>
            <dd><input type="text" class="text" name="xcxpayshopnumer" value="<?=$shoppay["xcxpayshopnumer"]?>" /></dd>
        </dl> 
        
        
        <dl style="display:none;">
            <dt><?=__('商户ID：')?></dt>
              <dd><input type="text" class="text" name="payshopcode" value="<?=$shoppay["payshopcode"]?>" /></dd>
        </dl>
        
         <dl style="display:none;">
            <dt><?=__('终端号：')?></dt>
              <dd><input type="text" class="text" name="paytermnumber" value="<?=$shoppay["paytermnumber"]?>" /></dd>
        </dl>
        <dl>
            <dt></dt>
            <dd>
            <input type="submit" class="button bbc_seller_submit_btns" value="<?=__('确认提交')?>" />
            </dd>
        </dl>
    </div>
    </form>
</div>
<script>
// 添加保存
$(".bbc_seller_submit_btns").click(function(){
   var ajax_url = './index.php?ctl=Seller_Shop_Info&met=addEditshppay&typ=json';
   $.ajax({
        url: ajax_url,
        data:$("#form").serialize(),
		    dataType: "json", 
	      type: "post",   //请求方式
        success:function(res){
              if(res.status == 200)
              {
                  Public.tips.success("<?=__('操作成功！')?>");
              }
              else
              {
                  Public.tips.error(res.msg);
              }
        }
  });
});
</script>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>

