<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
   <div class="search">
    <form>
        <div class="filter-groups">
            <dl>
                <dt><?=__('下单时间：')?></dt>
                <dd style="width:390px;">
                    <input style="width:150px;" type="text" class="text hasDatepicker" placeholder="<?=__('起始时间')?>" name="query_start_date" id="query_start_date" value="<?php if (!empty($condition['order_create_time:>='])) {
                    echo $condition['order_create_time:>='];
                    } ?>" readonly="readonly"><label class="add-on"><i class="iconfont icon-rili"></i></label><span class="rili_ge">–</span>
                    <input style="width:150px;" id="query_end_date" class="text hasDatepicker" placeholder="<?=__('结束时间')?>" type="text" name="query_end_date" value="<?php if (!empty($condition['order_create_time:<='])) {
                    echo $condition['order_create_time:<='];
                    } ?>" readonly="readonly"><label class="add-on"><i class="iconfont icon-rili"></i></label>
                </dd>
            </dl>
            <dl>
                <dt><?=__('买家昵称：')?></dt>
                <dd>
                    <input type="text" class="text wp100" placeholder="<?=__('买家昵称')?>" id="buyer_name" name="buyer_name" value="<?php if (!empty($condition['buyer_user_name:LIKE'])) {
                    echo str_replace('%', '', $condition['buyer_user_name:LIKE']);
                } ?>">
                </dd>
            </dl>
            <dl>
                <dt><?=__('订单编号：')?></dt>
                <dd><input type="text" class="text wp100" placeholder="<?=__('请输入订单编号')?>" id="order_sn" name="order_sn" value="<?php if (!empty($condition['order_id'])) {
                    echo $condition['order_id'];
                } ?>"></dd>
            </dl>
        </div>
        <div class="control-group">
            <a onclick="formSub()" class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a><a class="button refresh" onclick="location.reload()">重新刷新</a>
            <input name="ctl" value="Seller_Trade_Order" type="hidden" /><input name="met" value="<?=$_GET['met']?>" type="hidden" />
            <?php if($_GET['met'] == 'physical'){?>
                <span class="search-filter-prev"><input type="checkbox" id="skip_off" value="1" <?php if (!empty($condition['order_status:<>'])) {
                        echo 'checked';
                    } ?> name="skip_off"> <label class="relative_left" for="skip_off"><?=__('不显示已关闭的订单')?></label>
                </span>
            <?php }?>
        </div>
    </form>
</div>
    <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <th class="tl w100"><?=__('结算单号')?></th>
        <th width="200"><?=__('起止时间')?></th>
        <th width="150"><?=__('本期应收')?></th>
        <th width="120"><?=__('结算状态')?></th>
        <th width="120"><?=__('付款日期')?></th>
        <th width="120"><?=__('操作')?></th>
    </tr>
    <?php if($list['items']){
        foreach ($list['items'] as $key => $val) {?>
    <tr>
        <td class="tl"><?=($val['os_id'])?></td>
        <td><p><?=($val['os_start_date'])?></p> | <p><?=($val['os_end_date'])?></p></td>
        <td><?=format_money($val['os_amount'])?></td>
        <td><?=($val['os_state_text'])?></td>
        <td><?=($val['os_pay_date'])?></td>
        <td class="operate">
        <span>
        <a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Order_Settlement&met=normal&op=show&id=<?=($val['os_id'])?>"><i class="iconfont icon-chakan"></i><?=__('查看')?></a>
        </span>
        </td>
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