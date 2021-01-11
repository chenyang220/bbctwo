<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<script type='text/jade' id='thrid_opt'>
    <a class="button invoice_add button_blue bbc_seller_btns" href="javascript:;"><i
                class="iconfont icon-jia"></i><?= __('新增发票模板') ?></a>
</script>
<form id="form" action="./index.php?ctl=Seller_Transport&met=delTransport" method="post">
    <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <th width="100"><?= __('发票模板名称') ?></th>
            <th width="50"><?= __('发票类型') ?></th>
            <th width="100"><?= __('状态') ?></th>
            <th width="120"><?= __('操作') ?></th>
        </tr>
        <?php if ($data['items']) {
            foreach ($data['items'] as $key => $value) { ?>
                <tr class="row_line">
                    <td><span class="number"><?= $value['invoice_name'] ?></span></td>
                    <td><span class="number"><?= $value['invoice_state'] ?></span></td>
                    <td><span class="number"><?= $value['is_use'] ?></span></td>

                    <td class="nscs-table-handle">
                        <span class="edit">
                            <a href="javascript:;" class="invoice_edit">
                                <input type="hidden" value="<?= $value['invoice_id'] ?>">
                                <i class="iconfont icon-zhifutijiao"></i><?= __('编辑') ?>
                            </a>
                        </span>
                        <?php if($value['is_use'] != "启用") {?>
                        <span class="del">
                            <a data-param="{'ctl':'Seller_Shop_Invoice','met':'delInvoice','id':'<?= $value['invoice_id'] ?>'}" href="javascript:void(0)">
                                <i class="iconfont icon-lajitong"></i><?= __('删除') ?>
                            </a>
                        </span>
                        <?php }?>
                    </td>
                </tr>
            <?php } ?>
            <!--- 分页 --->
            <?php if (!empty($page_nav)) { ?>
                <tr>
                    <td colspan="99">
                        <div class="page">
                            <?= $page_nav ?>
                        </div>
                    </td>
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
</form>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
<script type="text/javascript" src="<?= $this->view->js ?>/alert.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/comfirm_goods_cart.js"></script>
<script>
    $(".tabmenu").append($("#thrid_opt").html());
    $(".invoice_add").bind("click", function () {
        url = SITE_URL + "?ctl=Seller_Shop_Invoice&met=setInvoice&typ=e";
        $.dialog({
            title: '新增发票信息',
            content: 'url: ' + url,
            height: 650,
            width: 600,
            lock: true,
            drag: false,
        })
    })
    $(".invoice_edit").bind("click",function(obj){
        var id = $(this).children('input').val();
        url =SITE_URL + "?ctl=Seller_Shop_Invoice&met=setInvoice&typ=e&act=edit&invoice_id="+id;
        // console.log(url);
        $.dialog({
            title: '编辑发票信息',
            content: 'url: ' + url ,
            height: 650,
            width: 600,
            lock: true,
            drag: false,
        })
    })
</script>

