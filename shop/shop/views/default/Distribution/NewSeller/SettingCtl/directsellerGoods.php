<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<style>
    .dialog_close_button {
        font-family: Verdana;
        font-size: 14px;
        line-height: 20px;
        font-weight: 700;
        color: #999;
        text-align: center;
        display: block;
        width: 20px;
        height: 20px;
        position: absolute;
        z-index: 1;
        top: 5px;
        right: 5px;
        cursor: pointer;
    }

    .dialog_close_button:hover {
        text-decoration: none;
        color: #333;
    }

    #dialog_directseller {
        border: 1px solid #ccc;
    }

    #dialog_directseller dl {
        padding: 5px;
    }
</style>
<script type='text/jade' id='thrid_opt'>
	<a class="bbc_seller_btns button add button_blue" href="<?= Yf_Registry::get('index_page') ?>?ctl=Distribution_NewSeller_Setting&met=addDirectsellerGoods&typ=e"><i class="iconfont icon-jia"></i><?= __('添加分销商品') ?></a>
</script>
<div class="search">
    <form id="search_form" class="search_form_reset" method="get" action="<?= Yf_Registry::get('url') ?>">
        <div class="filter-groups">
            <dl>
                <dt><?= __('商品名称：') ?></dt>
                <dd>
                   <input class="text wp100" type="text" name="common_name" value="<?= request_string('common_name') ?>"
               placeholder="<?= __('请输入商品名称') ?>"/>
               <input type="hidden" name="ctl" value="Distribution_NewSeller_Setting">
                 <input type="hidden" name="met" value="<?= $met ? $met : 'directsellerGoods'; ?>"> 
                </dd>
            </dl>
        </div>
        <div class="control-group">
            <a class="button btn_search_goods" href="javascript:void(0);"><?= __('筛选') ?></a>
            <a class="button refresh" onclick="location.reload()"><?= __('重新刷新') ?></a>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(".search").on("click", "a.button", function () {
        $("#search_form").submit();
    });
</script>
<?php if (!empty($data['items'])) { ?>
    <form id="form" method="post" action="">
        <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <th class="tl">
                    <label class="checkbox"><input class="checkall" type="checkbox"/></label><?= __('商品名称') ?>
                </th>
                <th width="80"><?= __('价格') ?></th>
                <th width="80"><?= __('库存') ?></th>
                <th width="300"><?= __('佣金比例') ?></th>
                <th width="120"><?= __('操作') ?></th>
            </tr>
            <?php foreach ($data['items'] as $item) { ?>
                <?php if($item['common_is_directseller']) {?>
                <tr id="tr_common_id_<?= $item['common_id']; ?>">
                    <td class="tl th" colspan="99">
                        <label class="checkbox">
                            <input class="checkitem" type="checkbox" name="chk[]" value="<?= $item['common_id'] ?>">
                        </label>
                        <?= __('平台货号：') ?><?= $item['common_id']; ?>
                    </td>
                </tr>
                <tr>
                    <td class="tl">
                        <dl class="fn-clear fn_dl">
                            <dt>
                                <i date-type="ajax_goods_list" data-id="237" class="iconfont icon-jia disb"></i>
                                <a href="index.php?ctl=Goods_Goods&met=goods&type=goods&gid=<?= $item['goods_id'] ?>" target="_blank"><img width="60" src="<?= $item['common_image'] ?>"></a>
                            </dt>
                            <dd>
                                <h3>
                                    <a href="index.php?ctl=Goods_Goods&met=goods&type=goods&gid=<?= $item['goods_id'] ?>" target="_blank"><?= $item['common_name'] ?></a>
                                </h3>
                                <p><?= $item['cat_name'] ?></p>
                                <p><?= ($item['common_code'] ? sprintf("商家货号：%s", $item['common_code']) : '') ?></p>
                            </dd>
                        </dl>
                    </td>
                    <td><?= format_money($item['common_price']); ?></td>
                    <td><?= $item['common_stock'] ?> <?= __('件') ?></td>
                    <td>
                        <p><?= __('分销客一级：') ?><?= $item['common_c_first'] ?> %</p>
                        <p><?= __('分销客二级：') ?><?= $item['common_c_second'] ?> %</p>
                        <p><?= __('分销掌柜一级：') ?><?= $item['common_a_first'] ?> %</p>
                        <p><?= __('分销掌柜二级：') ?><?= $item['common_a_second'] ?> %</p>
                    </td>
                    <td>
                        <span class="edit">
                            <a href="javascript:void(0)" common-id='<?= $item['common_id'] ?>'
                               c_first_rate='<?= $item['common_c_first'] ?>'
                               c_second_rate='<?= $item['common_c_second'] ?>'
                               a_first_rate='<?= $item['common_a_first'] ?>'
                               a_second_rate='<?= $item['common_a_second'] ?>' id="set_commission"><i
                                        class="iconfont icon-setting"></i><?= __('设置') ?></a></span>
                        <span class="del">
                            <a data-param="{'id':'<?= $item['common_id'] ?>','ctl':'Distribution_NewSeller_Setting','met':'delGoods'}"
                               href="javascript:void(0)">
                                <i class="iconfont icon-lajitong"></i>
                                <?= __('删除') ?>
                            </a>
                        </span>
                    </td>
                </tr>
                <tr class="tr-goods-list" style="display: none;">
                    <td colspan="5" class="tl">
                        <ul class="fn-clear">
                            <?php if (!empty($goods_detail_rows[$item['common_id']])):
                                foreach ($goods_detail_rows[$item['common_id']] as $g_k => $g_v):
                                    ?>
                                    <li>
                                        <div class="goods-image">
                                            <a herf="" target="_blank">
                                                <img width="100" src="<?= $g_v['goods_image']; ?>">
                                            </a>
                                        </div>
                                        <?php if (!empty($g_v['spec'])) {
                                            foreach ($g_v['spec'] as $ks => $vs):?>
                                                <div class="goods_spec"><?= $ks; ?>：<span><?= $vs ?></span></div>
                                            <?php endforeach;
                                        } ?>
                                        <div class="goods-price">
                                            <?= __('价格：') ?><span><?= format_money($g_v['goods_price']); ?></span></div>
                                        <div class="goods-stock"><?= __('库存：') ?>
                                            <span><?= $g_v['goods_stock'] ?> <?= __('件') ?></span></div>
                                        <a href="index.php?ctl=Goods_Goods&met=goods&type=goods&gid=<?= $g_v['goods_id'] ?>"
                                           target="_blank"><?= __('查看商品详情') ?></a>
                                    </li>
                                <?php
                                endforeach;
                            endif;
                            ?>

                        </ul>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
            <tr>
                <td class="toolBar" colspan="1">
                    <input type="hidden" name="act" value="del"/>
                    <label class="checkbox"><input class="checkall" type="checkbox"/></label><?= __('全选') ?>
                    <span>|</span>
                    <label class="down"><i class="iconfont icon-xiajia"></i><?= __('下架') ?></label>
                </td>
                <td colspan="99">
                    <div class="page">
                        <?= $page_nav ?>
                    </div>
                </td>
            </tr>
        </table>
    </form>
<?php } else { ?>
    <form id="form" method="post" action="">
        <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <th class="tl">
                    <label class="checkbox"><input class="checkall" type="checkbox"/></label><?= __('商品名称') ?>
                </th>
                <th width="80"><?= __('价格') ?></th>
                <th width="80"><?= __('库存') ?></th>
                <th width="100"><?= __('佣金比例') ?></th>
                <th width="120"><?= __('操作') ?></th>
            </tr>
        </table>
    </form>
    <div class="no_account">
        <img src="<?= $this->view->img ?>/ico_none.png">
        <p><?= __('暂无符合条件的数据记录'); ?></p>
    </div>
<?php } ?>
<!--设置分佣比例-->
<div id="dialog_directseller" class="eject_con" style="display:none;">
    <input id="common_id" type="hidden" value=""/>
    <dl style="margin-top:15px;">
        <dt><?= __('分销客一级') ?>：</dt>
        <dd><input id="common_c_first_rate" name='common_c_first_rate' type="text" class="text w70"> %</dd>
    </dl>
    <dl>
        <dt><?= __('分销客二级') ?>：</dt>
        <dd><input id="common_c_second_rate" name='common_c_second_rate' type="text" class="text w70"> %</dd>
    </dl>
    <dl>
        <dt><?= __('分销掌柜一级') ?>：</dt>
        <dd><input id="common_a_first_rate" name='common_a_first_rate' type="text" class="text w70"> %</dd>
    </dl>
    <dl>
        <dt><?= __('分销掌柜二级') ?>：</dt>
        <dd><input id="common_a_second_rate" name='common_a_second_rate' type="text" class="text w70"> %</dd>
    </dl>
    <div class="eject_con mb10">
        <div class="bottom"><a id="btn_directseller_shop_name" class="button bbc_seller_submit_btns"
                               href="javascript:void(0);"><?= __('提交') ?></a></div>
    </div>
</div>
<script>
    //设置分佣比例
    $('.table-list-style').on('click', '#set_commission', function () {
        var common_id = $(this).attr('common-id');
        $('#common_id').val(common_id);

        var c_first_rate = $(this).attr('c_first_rate');
        $('#common_c_first_rate').val(c_first_rate);
        var c_second_rate = $(this).attr('c_second_rate');
        $('#common_c_second_rate').val(c_second_rate);
        var a_first_rate = $(this).attr('a_first_rate');
        $('#common_a_first_rate').val(a_first_rate);
        var a_second_rate = $(this).attr('a_second_rate');
        $('#common_a_second_rate').val(a_second_rate);


        $('#dialog_directseller').yf_show_dialog({width: 450, title: "<?=__('设置分佣比例')?>"});
    });

    //提交分佣比例
    $('#btn_directseller_shop_name').on('click', function () {
        var common_id = $('#common_id').val();
        var c_first_rate = $('#common_c_first_rate').val();
        var c_second_rate = $('#common_c_second_rate').val();
        var a_first_rate = $('#common_a_first_rate').val();
        var a_second_rate = $('#common_a_second_rate').val();
        if (a_first_rate != '') {
            $.post(SITE_URL + '?ctl=Distribution_NewSeller_Setting&met=editDirectsellerGoods&typ=json',
                {
                    common_id: common_id,
                    c_first_rate:c_first_rate,
                    c_second_rate:c_second_rate,
                    a_first_rate:a_first_rate,
                    a_second_rate:a_second_rate
                },
                function (d) {
                    if (d.status == 200) {
                        var data = d.data;
                        Public.tips.success("<?=__('修改成功!')?>");
                        location.href = SITE_URL + '?ctl=Distribution_NewSeller_Setting&met=directsellerGoods';
                    } else {
                        Public.tips.error("<?=__('操作失败！')?>");
                        location.href = SITE_URL + '?ctl=Distribution_NewSeller_Setting&met=directsellerGoods';
                    }
                }, 'json'
            );
        } else {
            $('#dialog_directseller_shop_name_error').show();
        }
    });
</script>
<script type="text/javascript">
    $('label.down').click(function () {
        var length = $('.checkitem:checked').length;
        if (length > 0) {
            var chk_value = [];//定义一个数组
            $("input[name='chk[]']:checked").each(function () {
                chk_value.push($(this).val());//将选中的值添加到数组chk_value中
            });
            $.dialog.confirm("<?=__('您确定要下架吗?')?>", function () {
                $.post(SITE_URL + '?ctl=Seller_Goods&met=editGoodsCommon&typ=json&act=down', {chk: chk_value}, function (data) {
                    if (data && 200 == data.status) {
                        //$.dialog.alert('删除成功',function(){location.reload();});
                        Public.tips({type: 3, content: "<?=__('下架成功！')?>"});
                        location.reload();
                    }
                    else {
                        //$.dialog.alert('删除失败');
                        Public.tips({type: 1, content: "<?=__('下架失败！')?>"});
                    }
                });
            });
        }
        else {
            $.dialog.alert("<?=__('请选择需要操作的记录')?>");
        }
    });
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
