<?=__('')?><?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<div class="search">
    <form method="get" id="search_form" action="index.php" >
        <input type="hidden" name="ctl" value="<?=$_GET['ctl']?>">
        <input type="hidden" name="met" value="<?=$_GET['met']?>">
        <input type="hidden" name="typ" value="e">
        <div class="filter-groups">
            <dl>
                <dt><?=__('订单号：')?></dt>
                <dd>
                    <input type="text" name="order_id" class="text wp100" placeholder="<?=__('请输入订单号')?>">
                </dd>
            </dl>
            <dl>
                <dt><?=__('供货商店铺：')?></dt>
                <dd>
                    <input type="text" name="shop_name" class="text wp100" placeholder="<?=__('请输入供货商店铺')?>">
                </dd>
            </dl>
            <dl>
                <dt><?=__('下单时间：')?></dt>
                <dd style="width: 250px;">
                    <input type="text" autocomplete="off" name="start_date" id="start_date" class="text w85" value="<?=request_string('start_date')?>" placeholder="<?=__('开始时间')?>"/><em class="add-on add-on2"><i class="iconfont icon-rili"></i></em>
                    <span class="rili_ge">–</span>
                    <input type="text" autocomplete="off" name="end_date" id="end_date" class="text w85" value="<?=request_string('end_date')?>" placeholder="<?=__('结束时间')?>"/><em class="add-on add-on2"><i class="iconfont icon-rili"></i></em> 
                </dd>
            </dl>
        </div>
        <div class="control-group">
            <a class="button btn_search_goods"  href="javascript:void(0);"><?=__('筛选')?></a>
            <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Supplier_DistLog&met=buy_order&typ=e"><?=__('重新刷新')?></a>

        </div>
        <script type="text/javascript">
            $("a.btn_search_goods").on("click",function(){
                $("#search_form").submit();
            });
        </script>
    </form>
</div>
<script type="text/javascript">
    $(".search").on("click", "a.button", function ()
    {
        $("#search_form").submit();
    });
</script>
<?php
if (!empty($data['items'])){
    ?>
        <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <th width="100"><?=__('订单号')?></th>
                <th width="100"><?=__('供货商店铺')?></th>
                <th width="100"><?=__('供货商账号')?></th>
                <th width="80"><?=__('订单金额')?></th>
                <th width="80"><?=__('订单日期')?></th>
            </tr>
		    <?php foreach ($data['items'] as $item){ ?>
                <tr>
                    <td><?=$item['order_id'] ?></td>
                    <td><?=$item['shop_name'] ?></td>
                    <td><?=$item['seller_user_name']?></td>
                    <td><?=$item['order_payment_amount'] ?></td>
                    <td><?=$item['order_create_time'] ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="99">
                    <div class="page">
                        <?=$page_nav ?>
                    </div>
                </td>
            </tr>
        </table>
<?php }else{ ?>
    <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <th width="100"><?=__('订单号')?></th>
            <th width="100"><?=__('商品名')?></th>
            <th width="80"><?=__('商品图片')?></th>
            <th width="80"><?=__('商品价格')?></th>
            <th width="80"><?=__('订单日期')?></th>
        </tr>
    </table>
    <div class="no_account">
        <img src="<?=$this->view->img?>/ico_none.png">
        <p><?=__('暂无符合条件的数据记录') ?></p>
    </div>
<?php } ?>
<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.dialog.js" charset="utf-8"></script>	
<script>
$(document).ready(function(){
	$('#start_date').datetimepicker({
        controlType: 'select',
        timepicker:false,
        format:'Y-m-d'
    });

    $('#end_date').datetimepicker({
    controlType: 'select',
    timepicker:false,
    format:'Y-m-d'
    });
});    
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>