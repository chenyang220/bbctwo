<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
    include $this -> view -> getTplPath() . '/' . 'seller_header.php';
?>
<script type='text/jade' id='thrid_opt'>

<?php
        if($shop_GradeInfo['shop_grade_goods_limit'] >0){
            if (count($goods) < $shop_GradeInfo['shop_grade_goods_limit']) {
?>
               <!-- <a class="bbc_seller_btns1" id="export"><?= __('导出商品') ?></a> -->
                <a class="bbc_seller_btns button add button_blue" href="<?= Yf_Registry::get('index_page') ?>?ctl=Seller_Goods&met=add&typ=e"><i class="iconfont icon-jia"></i><?= __('发布新商品') ?></a>
<?php
            }
        }else{
?>
                <!-- <a class="bbc_seller_btns1" id="export"><?= __('导出商品') ?></a> -->
               <a class="bbc_seller_btns button add button_blue" href="<?= Yf_Registry::get('index_page') ?>?ctl=Seller_Goods&met=add&typ=e"><i class="iconfont icon-jia"></i><?= __('发布新商品') ?></a>
<?php } ?>



</script>
<style type="text/css">
.page{
        display:  inline-block;
        vertical-align:  middle;
    }
.page1{
        display:  inline-block;
        height:  33px;
        vertical-align:  middle;
        margin-top:  20px;
        font-size:  0;
}
.page1>span  {
        color:  #333;
        border:  1px  solid;
        border-color:  #dcdcdc  #dcdcdc  #b8b8b8;
        height:  20px;
        font-size:  12px;
        line-height:  20px;
        margin-left:  2px;
        overflow:  hidden;
        padding:  3px  7px;
        display:  inline-block;
        vertical-align:  middle;
}
.page_judge{
      width:  38px;
}
.page1>input{
        border:  1px  solid;
        border-color:  #dcdcdc  #dcdcdc  #b8b8b8;
        height:  28px;
        font-size:  12px;
        line-height:  20px;
        margin-left:  2px;
        overflow:  hidden;
        padding:  3px  7px;
        display:  inline-block;
        vertical-align:  middle;
        box-sizing:  border-box;
}  
</style>

<script type="text/javascript">
  $(function () {
    $(".tabmenu").append($("#thrid_opt").html());

    //商品导出
    $(".tabmenu").on("click", "#export", function () {
        
        var length = $(".checkitem:checked").length;

        if (length > 0) {
            var check_value = [];//定义一个数组
            
            $("input[name='chk[]']:checked").each(function () {
                check_value.push($(this).val());//将选中的值添加到数组chk_value中
            });
            // console.log(check_value);return;
        }else {
          $.dialog.alert("<?=__('请选择需要导出的商品')?>");return;
        }

        window.open(SITE_URL + "?ctl=Seller_Goods&met=exportGoods&debug=1&check_value="+check_value);
      });

  });
</script>

<div class="search">
    <form id="search_form" class="list-filter" method="get" action="<?= Yf_Registry::get('url') ?>">
        <div class="filter-groups">
            <dl>
                <dt>商品名称：</dt>
                <dd> <input class="text wp100" type="text" name="goods_key" value="<?= ($goods_key ? $goods_key : ''); ?>"/> <input type="hidden" name="ctl" value="Seller_Goods"> <input type="hidden" name="met" value="<?= $met ? $met : 'online'; ?>"> </dd>
            </dl>
        </div>
        <div class="control-group">
            <a class="button btn_search_goods" href="javascript:void(0);"><?= __('筛选') ?></a><a class="button refresh" onclick="location.reload()">重新刷新</a> 
        </div>
    </form>
</div>
<script type="text/javascript">
  $(".search").on("click", "a.button", function () {
    $("#search_form").submit();
  });
</script>
<?php
    if (!empty($goods)) {
        if ($this -> shopBaseInfo['shop_type'] == 2) {
            $ctl = 'Supplier_Goods';
        } else {
            $ctl = 'Goods_Goods';
        }
        ?>
        <form id="form" method="post" action="index.php?ctl=Seller_Goods&met=editGoodsCommon&typ=json">
            <table class="table-list-style table-list-goods" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="tl">
                        <label class="checkbox ml4 mr8"><input class="checkall" type="checkbox"/></label><?= __('商品名称') ?>
                    </th>
                    <th width="80"><?= __('价格') ?></th>
                    <th width="80"><?= __('库存') ?></th>
                    <th width="80"><?= __('发布时间') ?></th>
                    <th width="120"><?= __('操作') ?></th>
                </tr>
                <?php
                    foreach ($goods as $item) {
                        ?>
                        <tr id="tr_common_id_<?= $item['common_id']; ?>">
                            <td class="tl th" colspan="99">
                                <label class="checkbox">
                                    <input <?php if (isset($item['disabled_up']) && $item['disabled_up']) { echo 'disabled'; } ?> class="checkitem" type="checkbox" name="chk[]" value="<?= $item['common_id'] ?>" is_virtual="<?= $item['common_is_virtual'] ?>" common_virtual_date="<?= $item['common_virtual_date'] ?>">
                                </label>
                                <?= __('平台货号') ?>:<?= $item['common_id']; ?>
                                <?php if (isset($item['disabled_up']) && $item['disabled_up']) {
                                    echo '<span style="color:red;">（' . __('供应商下架商品') . '）</span>';
                                } ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="tl">
                                <dl class="fn-clear fn_dl">
                                    <dt>
                                        <i date-type="ajax_goods_list" data-id="237" class="iconfont icon-jia disb"></i>
                                        <?php if ($met == 'offline' || $met == 'OutsideImport' || $met == 'verify' || $met == 'waitReleaseGoods' || $met == 'verifyDeny' || $met == 'lockup') { ?>
                                        <a target="_blank" href="<?php echo Yf_Registry::get('url'); ?>?ctl=Goods_Goods&met=goodspreview&typ=e&gid=<?= $item['goods_id'] ?>">
                                            <?php } else { ?>
                                            <a href="index.php?ctl=<?= $ctl ?>&met=goods&gid=<?= $item['goods_id'] ?>" target="_blank">
                                                <?php } ?>
                                                <p style="display:inline" target="_blank">
                                                    <img width="60" src="<?= $item['common_image']; ?>">
                                                </p>
                                            </a>
                                    </dt>
                                     <dd>
                                    <?php if ($met == 'offline' || $met == 'OutsideImport' || $met == 'verify' || $met == 'waitReleaseGoods' || $met == 'verifyDeny' || $met == 'lockup') { ?>
                                        <a target="_blank" href="<?php echo Yf_Registry::get('url'); ?>?ctl=Goods_Goods&met=goodspreview&typ=e&gid=<?= $item['goods_id'] ?>">
                                    <?php } else { ?>
                                        <a href="index.php?ctl=<?= $ctl ?>&met=goods&gid=<?= $item['goods_id'] ?>" target="_blank">
                                    <?php } ?>
                                           
                                                <h3 class="wp100">
                                                    <?php if ($item['common_parent_id']) { ?>
                                                        <span class="dis_flag"><?= __('分销') ?></span>
                                                    <?php } ?>
                                                    <p target="_blank" class="one-overflow wp100"><?= __($item['common_name']) ?></p>
                                                </h3>
                                                <p class="one-overflow wp100"><?= __($item['cat_name']) ?></p>
                                                <p class="one-overflow wp100"><?= ($item['common_code'] ? sprintf(__('商家货号') . '：%s', $item['common_code']) : '') ?></p>
                                            
                                        </a>
                                    </dd>
                                </dl>
                            </td>
                            <td><?= format_money($item['common_price']); ?></td>
                            <td <?php if ($item['common_stock'] < $item['common_alarm']) { ?> class="colred" <?php } ?>><?= $item['common_stock'] ?> <?= __('件') ?></td>
                            <td><?php $item['common_sell_time'] !== '0000-00-00 00:00:00' ? print($item['common_sell_time']) : print($item['common_add_time']); ?></td>
                            <td>
                                <span class="edit">
                                    <a href="<?php echo Yf_Registry::get('url'); ?>?ctl=Seller_Goods&met=online&typ=e&common_id=<?= $item['common_id'] ?>&action=edit_goods">
                                        <i class="iconfont icon-zhifutijiao"></i>
                                        <?= __('编辑') ?>
                                    </a>
                                </span>
                                <span class="del">
                                    <a data-param="{'id':'<?= $item['common_id'] ?>','ctl':'Seller_Goods','met':'deleteGoodsCommonRows','act':'del'}" href="javascript:void(0)">
                                        <i class="iconfont icon-lajitong"></i>
                                        <?= __('删除') ?>
                                    </a>
                                </span>
                            </td>
                        </tr>
                        <tr class="tr-goods-list" style="display: none;">
                            <td colspan="5" class="tl">
                                <ul class="fn-clear">
                                    <?php
                                        if (!empty($goods_detail_rows[$item['common_id']])):
                                        foreach ($goods_detail_rows[$item['common_id']] as $g_k => $g_v):
                                            ?>
                                            <li>
                                                <?php if ($met == 'offline' || $met == 'OutsideImport' || $met == 'verify' || $met == 'waitReleaseGoods' || $met == 'verifyDeny' || $met == 'lockup') { ?>
                                                <a target="_blank" href="<?php echo Yf_Registry::get('url'); ?>?ctl=Goods_Goods&met=goodspreview&typ=e&gid=<?= $g_v['goods_id'] ?>">
                                                    <?php } else { ?>
                                                    <a href="index.php?ctl=<?= $ctl ?>&met=goods&gid=<?= $g_v['goods_id'] ?>" target="_blank">
                                                        <?php } ?>
                                                        <div class="goods-image">
<!--                                                            <img width="100" src="--><?//= $g_v['goods_image']; ?><!--">-->
                                                            <img width="100" src="<?= $item['common_image']; ?>">
                                                        </div>
                                                        <?php if (!empty($g_v['spec'])) {
                                                            foreach ($g_v['spec'] as $ks => $vs):?>
                                                                <div class="goods_spec">
                                                                    <?= $ks; ?>：
                                                                    <span><?= $vs ?></span>
                                                                </div>
                                                            <?php endforeach;
                                                        } ?>
                                                        <div class="goods-price">
                                                            <?= __('价格') ?>：<span><?= format_money($g_v['goods_price']); ?></span>
                                                        </div>
                                                        <div class="goods-stock">
                                                            <?= __('库存') ?>
                                                            ：<span class="<?php if ($g_v['goods_alarm'] != 0 && ($g_v['goods_stock'] <= $g_v['goods_alarm'])) {echo "stock_wring";} ?>"><?= $g_v['goods_stock'] ?> <?= __('件') ?></span>
                                                        </div>
                                                        <?php if ($met == 'offline' || $met == 'OutsideImport' || $met == 'verify' || $met == 'waitReleaseGoods' || $met == 'verifyDeny' || $met == 'lockup') { ?>
                                                            <span class="view">
                                                                <i class="iconfont icon-chakan"></i>
                                                                <?= __('商品预览') ?>
                                                            </span>
                                                        <?php } else { ?>
                                                            <?= __('查看商品详情') ?>
                                                        <?php } ?>
                                                    </a>
                                            </li>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                
                                </ul>
                            </td>
                        </tr>
                    
                    <?php } ?>
                <tr>
                    <td class="toolBar" colspan="1">
                        <input type="hidden" name="act" value="del"/> <label class="checkbox"><input class="checkall" type="checkbox"/></label><?= __('全选') ?>
                        <span>|</span>
                        <!--<label class="del"><i class="iconfont icon-trash"></i>删除</label>-->
                        <label class="del" data-param="{'ctl':'Seller_Goods','met':'deleteGoodsCommonRows','act':'del'}"><i
                                    class="iconfont icon-lajitong"></i><?= __('删除') ?></label>
                        
                        <?php if ($met == 'online') { ?>
                            <span>|</span>
                            <label class="down"><i class="iconfont icon-xiajia"></i><?= __('下架') ?></label>
                        <?php } elseif ($met != 'verify') { ?>
                            <span>|</span>
                            <label class="up"><i class="iconfont icon-shangjia1"></i><?= __('上架') ?></label>
                        <?php } ?>
                    </td>
                    <td colspan="99">
                        <p class="page"><?=$page_nav?></p>
                    </td>
                </tr>
            </table>
        </form>
    <?php } else { ?>
        <form id="form" method="post" action="index.php?ctl=Seller_Goods&met=editGoodsCommon&typ=json">
            <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <th class="tl">
                        <label class="checkbox"><input class="checkall" type="checkbox"/></label><?= __('商品名称') ?>
                    </th>
                    <th width="80"><?= __('价格') ?></th>
                    <th width="80"><?= __('库存') ?></th>
                    <th width="80"><?= __('发布时间') ?></th>
                    <th width="120"><?= __('操作') ?></th>
                </tr>
            </table>
        </form>
        <div class="no_account">
            <img src="<?= $this -> view -> img ?>/ico_none.png">
            <p><?= __('暂无符合条件的数据记录'); ?></p>
        </div>
    <?php } ?>


<script type="text/javascript">
    $('.page_judge_sub').click(function () {
        var page_judge = $('.page_judge').val();
        var firstRow = (page_judge-1)*10;
        var url = SITE_URL + '?firstRow='+firstRow+'&totalRows=1342&ctl=Seller_Goods&met=online&typ=e';
        window.location.href = url;
    })
    $("label.down").click(function () {
    var length = $(".checkitem:checked").length;
    if (length > 0) {
      var chk_value = [];//定义一个数组
      $("input[name='chk[]']:checked").each(function () {
        chk_value.push($(this).val());//将选中的值添加到数组chk_value中
      });
      $.dialog.confirm("<?=__('您确定要下架吗')?>?", function () {
        $.post(SITE_URL + "?ctl=Seller_Goods&met=editGoodsCommon&typ=json&act=down", {chk: chk_value}, function (data) {
          if (data && 200 == data.status) {
            //$.dialog.alert('删除成功',function(){location.reload();});
            Public.tips({type: 3, content: "<?=__('下架成功')?>！"});
            location.reload();
          }
          else {
            //$.dialog.alert('删除失败');
            Public.tips({type: 1, content: "<?=__('下架失败')?>！"});
          }
        });
      });
    }
    else {
      $.dialog.alert("<?=__('请选择需要操作的记录')?>");
    }
  });
  $("label.up").click(function () {
    var me = '<?php echo $met?>';
    var length = $(".checkitem:checked").length;
    if (length > 0) {
      $.dialog.confirm("<?=__('您确定要上架吗')?>?", function () {
        var chk_value = [];//定义一个数组
        $("input[name='chk[]']:checked").each(function () {
          if ($(this).attr("is_virtual") == 1 && $(this).attr("common_virtual_date") <= "<?php echo date('Y-m-d'); ?>") {
            Public.tips({type: 1, content: "<?=__('请修改虚拟商品过期时间')?>！"});
            return false;
          }
          else {
            chk_value.push($(this).val());
            $.post(SITE_URL + "?ctl=Seller_Goods&met=editGoodsCommon&typ=json&act=up&me=" + me, {chk: chk_value}, function (data) {
              if (data && 200 == data.status) {
                //$.dialog.alert('删除成功',function(){location.reload();});
                Public.tips({type: 3, content: "<?=__('上架成功')?>！"});
                location.reload();
              }
              else {
                //$.dialog.alert('删除失败');
                Public.tips({type: 1, content: "<?=__('上架失败')?>！"+data.msg});
              }
            });
          }
        });
        
      });
    }
    else {
      $.dialog.alert("<?=__('请选择需要操作的记录')?>");
    }
  });
</script>
<script type="text/javascript">
  var offt = true;
  $(document).ready(function () {
    $(".table-list-style .disb").click(function () {
      if (offt) {
        $(this).parent().parent().parent().parent().next().css("display", "table-row");
        $(this).removeClass("icon-jia");
        $(this).addClass("icon-jian");
        offt = false;
      }
      else {
        $(this).parent().parent().parent().parent().next().css("display", "none");
        $(this).removeClass("icon-jian");
        $(this).addClass("icon-jia");
        offt = true;
      }
      
    });
  });
</script>
<script type="text/javascript">
  $(".dropdown").hover(function () {
    $(this).addClass("hover");
  }, function () {
    $(this).removeClass("hover");
  });
</script>

<script type="text/javascript">
  
  $(function () {
    $("#import_goods").on("click", function () {
      $.dialog({
        width: 560,
        height: 300,
        title: "<?=__('批量导入')?>",
        content: "url:" + SITE_URL + "?ctl=Seller_Goods&met=importGoods&typ=e",
        lock: !0
      });
    });
    
    if (<?= empty($no_shangjia) ? 0 : true ?>) {
      var $label = $("i.icon-shangjia1").parent("label");
      $label.prev("span").remove(), $label.remove();
    }
  });

</script>
<?php
    include $this -> view -> getTplPath() . '/' . 'seller_footer.php';
?>



