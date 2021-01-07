<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<script type='text/jade' id='btn_add_puls_goods_id'>
	<a class="bbc_seller_btns button add button_blue" href="<?= Yf_Registry::get('index_page') ?>?ctl=Seller_Goods_PlusGoods&met=addPlusGoods&typ=e"><i class="iconfont icon-jia"></i><?= __('添加Plus会员商品') ?></a>
</script>
<script type="text/javascript">
    $(function () {
        $('.tabmenu').append($('#btn_add_puls_goods_id').html());
    });
</script>
<div class="search">
    <form id="search_form" class="list-filter" method="get" action="<?= Yf_Registry::get('url') ?>">
        <div class="filter-groups">
            <dl>
                <dt>
                  PLUS会员商品名称： 
                </dt>
                <dd>
                    <input class="text wp100" type="text" name="common_name" value="<?= request_string('common_name') ?>"/>
                    <input type="hidden" name="ctl" value="Seller_Goods_PlusGoods">
                    <input type="hidden" name="met" value="index"> 
                </dd>
            </dl>
        </div>
        <div class="control-group">
            <a class="button btn_search_goods" href="javascript:void(0);"><?= __('筛选') ?></a>
            <a class="button refresh" onclick="location.reload()">重新刷新</a>
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
                <th class="tl" width="40%">
                    <label class="checkbox"><input class="checkall" type="checkbox"/></label><?= __('PLUS会员商品名称') ?>
                </th>
                <th width="20%"><?= __('商品价格') ?></th>
                <th width="20%"><?= __('PLUS会员价') ?></th>
                <th width="20%"><?= __('操作') ?></th>
            </tr>
            <?php foreach ($data['items'] as $item) { ?>
                    <tr id="tr_common_id_<?= $item['plus_id']; ?>">
                        <td class="tl th" colspan="99">
                            <label class="checkbox">
                                <input class="checkitem" type="checkbox" name="chk[]" value="<?= $item['plus_id'] ?>">
                            </label>
                            <?= __('编号：') ?><?= $item['plus_id']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="tl">
                            <dl class="fn-clear fn_dl" id="cc_<?=$item['common_id']?>">
                                <dt>
                                    <a href="" target="_blank"><img width="60" src="<?= $item['common_image'] ?>"></a>
                                </dt>
                                <dd>
                                    <h3>
                                        <a href="" target="_blank"><?= $item['common_name'] ?></a>
                                    </h3>
                                </dd>
                            </dl>
                            <script>
                                //var str = '<?= $item["goods_id"]; ?>';
                               // var str = JSON.parse(str);
                               // var goods_id = str[0]['goods_id'];
                                var tte =  $("#cc_<?=$item['common_id']?> a");
                                tte.each(function(){
                                    $(this).attr('href',"index.php?ctl=Goods_Goods&met=goods&type=goods&cid=<?=$item['common_id']?>");
                                });
                            </script>
                        </td>
                        <td><?= format_money($item['common_price']); ?></td>
                        <td><?=$item['plus_price']?></td>
                        <td>
                            <span class="del">
                                <a data-param="{'id':'<?= $item['plus_id'] ?>','ctl':'Seller_Goods_PlusGoods','met':'delPlusGoods'}"
                                   href="javascript:void(0)">
                                    <i class="iconfont icon-lajitong"></i>
                                    <?= __('删除') ?>
                                </a>
                             </span>
                        </td>
                    </tr>
            <?php } ?>
            <tr>
                <td class="toolBar" colspan="1">
                    <input type="hidden" name="act" value="del"/>
                    <label class="checkbox"><input class="checkall" type="checkbox"/></label><?= __('全选') ?>
                    <span>|</span>
                    <label class="del" data-param="{'ctl':'Seller_Goods_PlusGoods','met':'delPlusGoods'}">
                        <i class="iconfont icon-lajitong"></i><?= __('删除') ?>
                    </label>
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
                <th class="tl" width="40%">
                    <label class="checkbox"><input class="checkall" type="checkbox"/></label><?= __('PLUS会员商品名称') ?>
                </th>
                <th width="20%"><?= __('商品价格') ?></th>
                <th width="20%"><?= __('PLUS会员价') ?></th>
                <th width="20%"><?= __('操作') ?></th>
            </tr>
        </table>
    </form>
    <div class="no_account">
        <img src="<?= $this->view->img ?>/ico_none.png">
        <p><?= __('暂无符合条件的数据记录'); ?></p>
    </div>
<?php } ?>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
