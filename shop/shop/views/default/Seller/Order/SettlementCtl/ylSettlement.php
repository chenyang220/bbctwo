<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

    <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <th class="tl w100"><?=__('交易时间')?></th>
        <th width="200"><?=__('运单号')?></th>
        <th width="100"><?=__('交易金额')?></th>
        <th width="100"><?=__('清算金额')?></th>
        <th width="100"><?=__('手续费')?></th>
        <th width="100"><?=__('交易类型')?></th>
        <th width="100"><?=__('账单状态')?></th>
        <th width="150"><?=__('收货时间')?></th>
        <th width="150"><?=__('结算时间')?></th>
    </tr>
    <?php if($list['items']){
        foreach ($list['items'] as $key => $val) {?>
    <tr>
        <td class="tl"><?=($val['tracetime'])?></td>
        <td><p><?=($val['orderno'])?></p></td>
        <td><?=format_money($val['txnamt'])?></td>
        <td><?=format_money($val['settleamount'])?></td>
        <td><?=format_money($val['charge'])?></td>
        <td><?=$val['txntype']?></td>
        <td><?=$val['status']?></td>
        <td><?=$val['comfirmtime']?></td>
        <td><?=$val['billtime']?></td>
    </tr>
    <?php }}else{?>
    <tr>
        <td colspan="99">
         <div class="no_account">
            <img src="<?= $this->view->img ?>/ico_none.png"/>
            <p><?=__('暂无符合条件的数据记录')?></p>
        </div>
        </td>
    </tr>
    <?php }?>
    <tr>
        <td colspan="99">
            <p class="page"><?=$page_nav?></p>
        </td>
    </tr>
</table>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>