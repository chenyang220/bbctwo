<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js" charset="utf-8"></script>

<div class="exchange mt10">
    <div class="clearfix bargain-seller-box">
        <img class="w100 fl mr10" src="<?= $bargain_info['goods_image'] ?>" alt="">
        <div class="fl">
            <p class="bargain-goods-det mt10">
               <span><?= __('商品原价') ?>：<?= $bargain_info['goods_price'] ?></span>
                <span><?= __('砍价库存') ?>：<?= $bargain_info['bargain_stock'] ?></span>
            </p>
            <p class="bargain-goods-det mt10">
               <span><?= __('商品底价') ?>：<?= $bargain_info['bargain_price'] ?></span>
               <span><?= __('活动时间') ?>：<?= $bargain_info['start_time'] ?><?= __('至') ?><?= $bargain_info['end_time'] ?></span> 
            </p>
        </div>
    </div>
    <p class="mt10"><?= __('商品名称') ?>：<?= $bargain_info['goods_name'] ?></p>
    <table class="table-list-style w800" id="table_list" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <th width="100"><?= __('发起人') ?></th>
            <th width="100"><?= __('订单号') ?></th>
            <th width="100"><?= __('砍价次数') ?></th>
            <th width="80"><?= __('砍后价') ?></th>
            <th width="80"><?= __('状态') ?></th>
        </tr>
        <?php
        if ($data['items']) {
            foreach ($data['items'] as $key => $value) {
                ?>
                    <td><?= @$value['user_name'] ?></td>
                    <td><?= @$value['order_id'] ?></td>
                    <td><?= @$value['bargain_num'] ?></td>
                    <td><?= Web_ConfigModel::value('monetary_unit') ?><?= @$value['bargain_price'] ?></td>
                    <td><?= @$value['bargain_status_con'] ?></td>
                </tr>
            <?php }
        } else { ?>
            <tr class="row_line">
                <td colspan="99">
                    <div class="no_account">
                        <img src="<?= $this->view->img ?>/ico_none.png">
                        <p><?= __('暂无符合条件的数据记录') ?></p>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php if ($page_nav) { ?>
        <div class="mm">
            <div class="page"><?= $page_nav ?></div>
        </div>
    <?php } ?>
</div>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>



