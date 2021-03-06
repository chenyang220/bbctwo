<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

    <link href="<?= $this->view->css ?>/seller_center.css?ver=<?= VER ?>" rel="stylesheet">
    <link href="<?= $this->view->css ?>/base.css?ver=<?= VER ?>" rel="stylesheet">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?=VER?>" rel="stylesheet">
    <script src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js" charset="utf-8"></script>
 </head>
<body>
<script type='text/jade' id='thrid_opt'>
    <a class="bbc_seller_btns1" id="export"><?= __('导出订单') ?></a>
</script>    
<div class="search">
    <form class="msearch-form" method="POST">
        <div class="filter-groups">
            <dl>
                <dt><?=__('下单时间：')?></dt>
                <dd style="width: 250px;">
                    <input type="text" class="text w85 hasDatepicker" placeholder="<?=__('起始时间')?>" name="query_start_date" id="query_start_date" value="<?php if (!empty($condition['order_create_time:>='])) {
                    echo $condition['order_create_time:>='];
                    } ?>" readonly="readonly"><label class="add-on"><i class="iconfont icon-rili"></i></label><span class="rili_ge">–</span>
                    <input id="query_end_date" class="text w85 hasDatepicker" placeholder="<?=__('结束时间')?>" type="text" name="query_end_date" value="<?php if (!empty($condition['order_create_time:<='])) {
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
                <dd>
                    <input type="text" class="text wp100" placeholder="<?=__('请输入订单编号')?>" id="order_sn" name="order_sn" value="<?php if (!empty($condition['order_id'])) {
                    echo $condition['order_id'];
                    } ?>"> 
                </dd>
            </dl>
        </div>
        <div class="control-group">
           <a onclick="formSub()" class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a>
            <a class="button refresh" href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Trade_Deliver&met=<?=$_GET['met']?>&typ=e">重新刷新</a>
            <input name="ctl" value="Seller_Trade_Deliver" type="hidden" /><input name="met" value="<?=$_GET['met']?>" type="hidden" />
            <?php if($_GET['met'] == 'deliver'){?>
                <span class="search-filter-prev"><input type="checkbox" id="skip_off" value="1" <?php if (!empty($condition['order_status:<>'])) {
                        echo 'checked';
                    } ?> name="skip_off"> <label class="relative_left" for="skip_off"><?=__('不显示已关闭的订单')?></label>
                </span>
            <?php } ?>
        </div>
    </form>
</div>

<table class="ncsc-default-table order deliver ncsc-default-table2 table-outer">
    
    <?php if ( !empty($data['items']) ) { ?>
    <?php foreach( $data['items'] as $key => $val ) { ?>
    <tbody>
    <tr class="bor0">
        <td colspan="21" class="sep-row"></td>
    </tr>
    <tr>
        <th colspan="21">
            <span class="ml5"><?=__('订单编号')?>：<strong><?= $val['order_id']; ?></strong></span><span><?=__('下单时间')?>：<em class="goods-time"><?= $val['order_create_time']; ?></em></span>
         <span class="fr mr10">
                <a href="<?= $val['delivery_url']; ?>" target="_blank" class="ncbtn-mini bbc_seller_btns" title="<?=__('打印发货单')?>">
                    <i class="icon-print"></i><?=__('打印发货单')?>
                </a>
         </span>
        </th>
    </tr>
    
    <?php if ( !empty($val['goods_list']) ) { ?>
    
    <tr>
        <td>
            <table width="100%" style="border-collapse: collapse;" class="table-inset">
            <?php foreach ( $val['goods_list'] as $k => $v ) { unset($v['spec_text']);?>
                <tr>
                    <td class="bdl w10"></td>
                    <td class="w50">
                        <div class="pic-thumb">
                            <a href="<?= $v['goods_image']; ?>" target="_blank">
                                <img src="<?= $v['goods_image']; ?>" >
                            </a>
                        </div>
                    </td>
                    <td class="tl">
                        <dl class="goods-name">
                            <dt>
                                <?php if($v['order_goods_source_id']){ ?>
                                <span class="dis_flag"><?=__('分销')?></span>
                                <?php } ?>
                                <a target="_blank" href="<?= $v['goods_link']; ?>"><?= $v['goods_name']; ?></a>
                            </dt>
                            <dd>
                                <strong>￥<?= $v['goods_price']; ?></strong>&nbsp;x&nbsp;<em><?= $v['order_goods_num']; ?></em><?=__('件')?>
                            </dd>
                            <?php if(isset($v['order_spec_info']) && $v['order_spec_info']){ ?>
                                <dd class="block"><strong class="normal"><?=__('规格')?>：</strong>&nbsp;&nbsp;<em><?= $v['order_spec_info']; ?></em>
                                </dd>
                            <?php }?>
                            <?=$v['return_txt']?>
                        </dl>
                    </td> 
                </tr>
            <?php } ?>    
            </table>
        </td>
        
        <!-- S 合并TD -->
        <td class="order-info w500 table-cell" rowspan="<?= $val['goods_cat_num']; ?>">
            <dl>
                <dt><?=__('买家')?>：</dt>
                <dd><?= $val['buyer_user_name']; ?> <span member_id="<?= $val['buyer_user_id']; ?>"></span>
                </dd>
            </dl>
            <dl>
                <dt><?=__('收货人')?>：</dt>
                <dd>
                    <div class="alert alert-info alert-bg">
                        <p><span class="ml30" title="<?=__('收货人')?>"> <i class="icon iconfont icon-user mr4 vermiddle"></i><?= $val['order_receiver_name']; ?></span></p>
                        <p><span class="ml30" title="<?=__('电话')?>"><i class="icon iconfont icon-phone mr4 vermiddle"></i><?= $val['order_receiver_contact']; ?></span></p>
                        <p><span class="mt5" title="<?=__('收货地址')?>"><i class="icon iconfont icon-icolocate mr4 vermiddle"></i><?= $val['order_receiver_address']; ?></span></p>
                        <?php if($val['order_message']){ ?><p><span class="mt5" title="<?=__('订单留言')?>"><i class="icon iconfont icon-icoordermsg"></i><?= $val['order_message']; ?></span></p><?php } ?>
                    </div>
                </dd>
            </dl>
            <dl>
                <dt><?=__('运费')?>：</dt>
                <dd>
                    <?= $val['shipping_info']; ?>
                    <span>
                        <?php if ( ($val['order_status'] == Order_StateModel::ORDER_PAYED) || ($val['order_status'] == Order_StateModel::ORDER_WAIT_PREPARE_GOODS  && $val['payment_id'] == PaymentChannlModel::PAY_CONFIRM) ) { ?>
                            <?php if ($data['items'][$key]['deilve_able']) { ?>
                                <?=$val['set_html']?>
                            <?php } else { ?>
                                <span style="padding-left: 50px; font-size: 12px; color: #999">发票：</span><?=$val['set_html']?>
                            <?php } ?>
                        <?php }elseif ( $val['order_status'] == Order_StateModel::ORDER_WAIT_CONFIRM_GOODS ) { ?>
                            <a href="javascript:void(0)" class="ncbtn-mini ncbtn-bittersweet ml5 fr bbc_seller_btns" dialog_id="seller_order_delay_receive"
                               data-order_id="<?= $val['order_id'] ?>"
                               data-order_receiver_date="<?= $val['order_receiver_date']; ?>"
                               data-buyer_user_name="<?= $val['buyer_user_name'] ?>" >
                                <i class="icon-time"></i><?=__('延迟收货')?></a>
                            <a href="<?= $val['send_url']; ?>" class="ncbtn-mini ncbtn-aqua fr bbc_seller_btns mr10"><i class="icon-edit"></i><?=__('编辑发货')?></a>
                        <?php } ?>
                    </span>
                </dd>
            </dl>
        </td>
        <!-- E 合并TD -->
    </tr>
    <!-- S 赠品列表 -->
    <!-- E 赠品列表 -->
    <?php } ?>

    <!-- S 赠品列表 -->
    <!-- E 赠品列表 -->

    </tbody>
    <?php } ?>
    <?php } ?>
</table>

<div class="page">
    <?= $data['page_nav'] ?>
</div>

<?php if ( empty($val['goods_list']) ) { ?>
    <div class="no_account">
        <img src="<?=$this->view->img?>/ico_none.png">
        <p><?=__('暂无符合条件的数据记录')?></p>
    </div>
<?php } ?>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>

<script>
    $('.tabmenu').find('li:gt(6)').hide();
    $(".tabmenu").append($("#thrid_opt").html());
    $(function () {
        //发票详情弹窗
        $(".edit_invoice").bind("click", function () {
            var $this = $(this),
                order_id = $this.data('order_id'),

            url = SITE_URL + "?ctl=Seller_Trade_Deliver&met=showInvoice&typ=e&order_id="+order_id;

            // console.log(order_id);
            $.dialog({
                title: '发票详情',
                content: 'url: ' + url,
                height: 680,
                width: 600,
                lock: true,
                drag: false,
            })
        })

        //时间
        $('#query_start_date').datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            onShow:function( ct ){
                this.setOptions({
                    maxDate:$('#query_end_date').val() ? $('#query_end_date').val() : false
                })
            }
        });
        $('#query_end_date').datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            onShow:function( ct ){
                this.setOptions({
                    minDate:$('#query_start_date').val() ? $('#query_start_date').val() : false
                })
            },
        });

        //搜索

        var URL;

        $('input[type="submit"]').on('click', function (e) {

            e.preventDefault();

            URL = createQuery();
            window.location = URL;
        });

        $('#skip_off').on('click', function () {

            URL = createQuery();
            window.location = URL;
        });

        function createQuery () {

            var url = SITE_URL + '?' + location.href.match(/ctl=\w+&met=\w+/) + '&';

            $('#query_start_date').val() && (url += 'query_start_date=' + $('#query_start_date').val() + '&');
            $('#query_end_date').val() && (url += 'query_end_date=' + $('#query_end_date').val() + '&');
            $('#buyer_name').val() && (url += 'buyer_name=' + $('#buyer_name').val() + '&');
            $('#order_sn').val() && (url += 'order_sn=' + $('#order_sn').val() + '&');
            $('#skip_off').prop('checked') && (url += 'skip_off=1&');
            return url;
        }


        //延迟收货
        $('a[dialog_id="seller_order_delay_receive"]').on('click', function () {

            var $this = $(this),
                order_id = $this.data('order_id'),
                buyer_name = $this.data('buyer_user_name'),
                order_receiver_date = $this.data('order_receiver_date'),
                url = SITE_URL + '?ctl=Seller_Trade_Deliver&met=delayReceive&typ=';

            $.dialog({
                title: '<?=__('延迟收货')?>',
                content: 'url: ' + url + 'e',
                data: { order_id: order_id, order_receiver_date: order_receiver_date, buyer_name: buyer_name },
                height: 250,
                width: 500,
                lock: true,
                drag: false,
                ok: function () {

                    var delay_days = $(this.content.document.getElementsByName('delay_date')).val();

                    $.post(url + 'json', { order_id: order_id, order_receiver_date: order_receiver_date, delay_days: delay_days }, function ( data ) {
                            if ( data.status == 200 ) {
                                $this.data('order_receiver_date', data.order_receiver_date);
                                Public.tips({ content: data.msg, type: 3 });
                            } else {
                                Public.tips({ content: data.msg, type: 1 });
                            }
                    })
                }
            })
        });

        //取消订单
        $('a[dialog_id="seller_order_cancel_order"]').on('click', function () {

            var order_id = $(this).data('order_id'),
                url = SITE_URL + '?ctl=Seller_Trade_Order&met=orderCancel&typ=';

            $.dialog({
                title: '<?=__('取消订单')?>',
                content: 'url: ' + url + 'e',
                data: { order_id: order_id },
                height: 250,
                width: 400,
                lock: true,
                drag: false,
                ok: function () {

                    var form_ser = $(this.content.order_cancel_form).serialize();

                    $.post(url + 'json', form_ser, function (data) {
                        if ( data.status == 200 ) {
                            parent.Public.tips({
                                content: '<?=__('修改成功')?>',
                                type: 3
                            }), window.location.reload();
                            return true;
                        } else {
                            parent.Public.tips({
                                content: '<?=__('修改失败')?>',
                                type: 1
                            });
                            return false;
                        }
                    })
                }
            })
        });
    });

    function formSub(){
        $('.msearch-form').submit();
    }

    var queryConditions={};
    $("#export").click(function ()
    {
        queryConditions.query_start_date = $("input[name='query_start_date']").val();
        queryConditions.query_end_date = $("input[name='query_end_date']").val();
        queryConditions.buyer_name = $("input[name='buyer_name']").val();
        queryConditions.order_id = $("input[name='order_sn']").val();
        var firstRow = getQueryString('firstRow');
        queryConditions.start_limit = Number(firstRow);
        queryConditions.order_status = "<?php echo $condition['order_status']?>";  
        queryConditions.limit = 10; 
        $.dialog({
            title: '订单导出',
            content: "url:"+SITE_URL + '?ctl=Seller_Trade_Order&met=exportOrder',
            data: queryConditions,
            width: 800,
            height: $(window).height()*0.5,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    });
</script>
