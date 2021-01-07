<?php if (!defined('ROOT_PATH')) exit('No Permission'); ?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<style>
    .w30 {
        width: 30px;
    }

    .w180 {
        width: 180px;
    }

    .w80 {
        width: 80px !important;
    }

    .w200 {
        width: 200px !important;
    }

    .nscs-table-handle span a {
        color: #777;
        background-color: #FFF;
        display: block;
        padding: 3px 7px;
        margin: 1px;
    }

    .waybill-img-thumb {
        background-color: #fff;
        border: 1px solid #e6e6e6;
        display: inline-block;
        height: 45px;
        padding: 1px;
        vertical-align: top;
        width: 70px;
    }

    .waybill-img-thumb a {
        display: table-cell;
        height: 45px;
        line-height: 0;
        overflow: hidden;
        text-align: center;
        vertical-align: middle;
        width: 70px;
    }

    .waybill-img-thumb a img {
        max-height: 45px;
        max-width: 70px;
    }

    .waybill-img-size {
        color: #777;
        display: inline-block;
        line-height: 20px;
        margin-left: 10px;
        vertical-align: top;
    }

    .table-list-style th {
        padding: 8px 0;
    }

    .table-list-style tbody td {
        color: #999;
        background-color: #FFF;
        text-align: center;
        padding: 10px 0;
    }

    .tabmenu a.ncbtn {
        position: absolute;
        z-index: 1;
        top: -2px;
        right: 0px;
    }
    
    a.ncbtn {
        height: 20px;
        padding: 5px 10px;
        border-radius: 3px;
        color: #FFF;
        font: normal 12px/20px "microsoft yahei", arial;
    }
</style>
<div class = "deliverSetting">
    <form method = "post" id = "form">
        <table class = "table-list-style" width = "100%" cellpadding = "0" cellspacing = "0">
            <thead>
            <tr>
                <th class = "w30"></th>
                <th class = "w120 tl"><?=__('模板名称')?></th>
                <th class = "w120 tl"><?=__('物流公司')?></th>
                <th class = "tl"><?=__('运单图例')?></th>
                <th class = "w80"><?=__('上偏移量')?></th>
                <th class = "w80"><?=__('左偏移量')?></th>
                <th class = "w80"><?=__('启用')?></th>
                <th class = "w200"><?=__('操作')?></th>
            </tr>
            </thead>

            <tbody>
            <?php if ( !empty($shop_express_list) ) { ?>
            <?php foreach ($shop_express_list as $key => $val) { ?>
            <tr class = "bd-line">
                <td></td>
                <td class="tl"><?php echo $val['waybill_tpl_name']; ?></td>
                <td class="tl"><?php echo $val['express_name']; ?></td>
                <td class="tl">
                    <div class="waybill-img-thumb">
                        <a class="nyroModal"
                           rel="gal"
                           href="<?= $val['waybill_tpl_image']; ?>">
                            <img src="<?= $val['waybill_tpl_image']; ?>"></a>
                    </div>
                    <div class="waybill-img-size">
                        <p><?=__('宽度')?>：<?php echo $val['waybill_tpl_width']; ?>(mm)</p>
                        <p><?=__('高度')?>：<?php echo $val['waybill_tpl_height']; ?>(mm)</p>
                    </div>
                </td>

                <td><?php echo $val['waybill_tpl_top']; ?></td>
                <td><?php echo $val['waybill_tpl_left']; ?></td>
                <td><?= $val['waybill_tpl_enable']; ?></td>
                <td class="nscs-table-handle">
                    <span><a href="<?php echo Yf_Registry::get('url') . '?ctl=Seller_Trade_Waybill&met=designTpl&typ=e&waybill_tpl_id=' . $val['waybill_tpl_id']; ?>" class="btn-bittersweet"><i class="iconfont icon-banshou"></i><p><?=__('设计')?></p></a></span>
                    <span><a href="<?php echo Yf_Registry::get('url') . '?ctl=Seller_Trade_Waybill&met=testTpl&typ=e&waybill_tpl_id=' . $val['waybill_tpl_id']; ?>" class="btn-aqua" target="_blank"><i class="iconfont icon-icontianping"></i><p><?=__('测试')?></p></a></span>
                    <span><a href="<?php echo Yf_Registry::get('url') . '?ctl=Seller_Trade_Waybill&met=editTpl&typ=e&waybill_tpl_id=' . $val['waybill_tpl_id']; ?>" class="btn-bluejeans"><i class="iconfont icon-zhifutijiao"></i><p><?=__('编辑')?></p></a></span>
                    <span class="del"><a href="javascript:;" data-param="{'ctl':'Seller_Trade_Waybill','met':'removeTpl','id':'<?=$val['waybill_tpl_id']?>'}" class="btn-grapefruit"><i class="iconfont icon-lajitong"></i><p><?=__('删除')?></p></a></span>
                </td>
                <?php } ?>
                <?php } ?>
            </tr>
            </tbody>
        </table>
        <?php if ( empty($shop_express_list) ) { ?>
            <div class="no_account">
                <img src="<?=$this->view->img?>/ico_none.png">
                <p><?=__('暂无符合条件的数据记录')?></p>
            </div>
        <?php } ?>
        <div class="flip page page_front clearfix">
            <?=$page_nav?>
        </div>
    </form>
</div>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
<script>
    
    $('.tabmenu').children().children('li:gt(1)').hide();

    $(function () {

        //添加标签
        $('.tabmenu').append('<a title="<?=__('建立模板')?>" class="button" href="' + SITE_URL + '?ctl=Seller_Trade_Waybill&met=addTpl&typ=e' + '"><?=__('添加模板')?></a>');

        // $('a[nctype="btn_del"]').on('click', function () {

        //     var _this = $(this),
        //         waybill_tpl_id = _this.data('waybill_tpl_id');

        //     $.post(SITE_URL + '?ctl=Seller_Trade_Waybill&met=removeTpl&typ=json', {waybill_tpl_id: waybill_tpl_id}, function (data) {
        //         if ( data.status == 200 ) {
        //             Public.tips( { content:data.msg, type:3 } );
        //             _this.parents('tr').remove();
        //         } else {
        //             Public.tips( { content:data.msg, type:1 } );
        //         }
        //     })
        // })
    })
</script>
