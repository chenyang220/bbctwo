<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<form>
	<div class="form-style">
        <dl>
            <dt><i>*</i>套餐购买数量：</dt>
            <dd>
                <input type="text" class="text w50" maxlength="2" name="month" aria-required="true"><em>个月</em>
                <p class="hint">购买单位为月(30天)，您可以在所购买的周期内发布秒杀活动。</p>
                <p class="hint">每月您需要支付¥100.00。</p>
            </dd>
        </dl>
        <dl>
            <dt></dt>
            <dd>
                <input type="submit" class="button button_red bbc_seller_submit_btns" value="提交">
            </dd>
        </dl>
    </div>
</form>
<table class="table-list-style">
    <tbody>
    <tr>
        <th width="200">活动名称</th>
        <th width="200">活动单价(￥)</th>
        <th width="200">购买时长(月)</th>
        <th width="200">购买总价(￥)</th>
        <th width="200">购买时间</th>
    </tr>
    <tr>
        <td width="200">店铺购买秒杀活动消费</td>
        <td width="200">20.00</td>
        <td width="200">1</td>
        <td width="200">20.00</td>
        <td width="200">2017-09-11 09:57:16</td>
    </tr>
    <tr>
        <td width="200">店铺购买秒杀活动消费</td>
        <td width="200">20.00</td>
        <td width="200">2</td>
        <td width="200">40.00</td>
        <td width="200">2017-11-13 10:08:44</td>
    </tr>

    
    </tbody>
</table>