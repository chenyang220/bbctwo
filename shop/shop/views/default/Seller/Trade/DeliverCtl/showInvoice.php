<html>
<head>
    <meta charset="UTF-8">
</head>
<style>
    h3{
        width: 90%;
        margin: 0 auto;
        margin-top: 20px;
        padding-bottom: 10px;
        font-weight: 500;
        border-bottom: 1px solid #999;
    }

    .invoice_table{
        width: 90%;
        margin:  0 auto;
        margin-top: 10px;
        font-size: 12px;
        border: 1px solid #999;
    }
    .invoice_table tr{
        height: 35px
    }

    .invoice_table .tdl{
        width: 125px;
        padding-left: 40px;
        border-bottom: 1px solid #999;
        text-align:justify;
        text-justify:distribute-all-lines;
        text-align-last:justify;
        text-indent:10px;
    }
    .invoice_table .tdr{
        text-align: left;
        padding-left: 20px;
        border-bottom:1px solid #999 ;
    }
</style>
<body marginwidth="0" marginheight="0" style="font-size:12px;">

<?php if ($data['order_invoice_id']) { ?>

    <?php if ($data['invoice']['invoice_state'] != Order_InvoiceModel::INVOICE_NORMAL) { ?>
        <?php if ($data['invoice']['invoice_state'] == Order_InvoiceModel::INVOICE_ELECTRON) { ?>
            <h3>发票信息</h3>
            <table class="invoice_table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tdl"><?= __('发票类型：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_statu_txt'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('发票抬头：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_title'] ?></td>
                </tr>
                <?php if ($data['invoice']['invoice_title'] != '个人') {
                    if ($data['invoice']['invoice_code']) { ?>
                        <tr>
                            <td class="tdl"><?= __('企业税号：') ?></td>
                            <td class="tdr"><?= $data['invoice']['invoice_code'] ?></td>
                        </tr>
                    <?php }
                } ?>
                <tr>
                    <td class="tdl"><?= __('发票内容：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_content'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('收票人手机：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_rec_phone'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('收票人邮箱：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_rec_email'] ?></td>
                </tr>
            </table>
        <?php } ?>
        <?php if ($data['invoice']['invoice_state'] == Order_InvoiceModel::INVOICE_ADDTAX) { ?>
            <h3>发票信息</h3>
            <table class="invoice_table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tdl"><?= __('发票类型：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_statu_txt'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('发票内容：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_content'] ?></td>
                </tr>
            </table>
            <h3>公司信息</h3>
            <table class="invoice_table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tdl"><?= __('单位名称：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_company'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('纳税人识别码：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_code'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('注册地址：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_reg_addr'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('注册电话：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_reg_phone'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('开户银行：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_reg_bname'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('银行账户：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_reg_baccount'] ?></td>
                </tr>
            </table>
            <h3>收票人信息</h3>
            <table class="invoice_table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="tdl"><?= __('收票人姓名：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_rec_name'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('收票人手机：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_rec_phone'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('收票人省份：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_rec_province'] ?></td>
                </tr>
                <tr>
                    <td class="tdl"><?= __('详细地址：') ?></td>
                    <td class="tdr"><?= $data['invoice']['invoice_goto_addr'] ?></td>
                </tr>
            </table>
        <?php } ?>
    <?php } else { ?>
        <h3>发票信息</h3>
        <table class="invoice_table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="tdl"><?= __('发票类型：') ?></td>
                <td class="tdr"><?= $data['invoice']['invoice_statu_txt'] ?></td>
            </tr>
            <tr>
                <td class="tdl"><?= __('发票抬头：') ?></td>
                <td class="tdr"><?= $data['invoice']['invoice_title'] ?></td>
            </tr>
            <?php if ($data['invoice']['invoice_title'] != '个人') {
                if ($data['invoice']['invoice_code']) { ?>
                    <tr>
                        <td class="tdl"><?= __('企业税号：') ?></td>
                        <td class="tdr"><?= $data['invoice']['invoice_code'] ?></td>
                    </tr>
                <?php }
            } ?>
            <tr>
                <td class="tdl"><?= __('发票内容：') ?></td>
                <td class="tdr"><?= $data['invoice']['invoice_content'] ?></td>
            </tr>
        </table>
    <?php } ?>
<?php }  ?>

</body>
</html>