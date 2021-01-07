<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'chain_header.php';
?>
<style>
    .nscs-table-handle a.disabled {
        background: #999999;
    }
    .recommend{
        background: #999999;
    }
</style>
 <script type="text/javascript" src="<?= $this->view->js_com?>/jquery.js" charset="utf-8"></script>
<div>
    <div class="search mt0">
        <form id="search_form" class="search_form_reset" method="get" action="#">
            <div class="filter-groups">
                <dl>
                    <dt><?=__('商品名称：')?></dt>
                    <dd>
                        <select name="search_type" class="wp100">
                            <option value="1" selected=""><?=__('商品名称')?></option>
                            <!-- <option value="2"><?=__('商家货号')?></option> -->
                            <option value="3">平台货号</option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt><?=__('关键词：')?></dt>
                    <dd><input class="text wp100 z-box2" name="keyword" value="" type="text"></dd>
                </dl>
            </div>
            <div class="control-group">
                <input class="submit button btn_search_goods" value="<?=__('筛选')?>" type="submit">
            </div>
           
        </form>
    </div>
    <table class="ncsc-default-table table-list-style">
        <thead>
        <tr nc_type="table_header">
            <th>&nbsp;</th>
            <th class="w50">&nbsp;</th>
            <th coltype="editable" column="goods_name" checker="check_required" inputwidth="190px"><?=__('商品名称')?></th>
            <th class="w150">平台货号</th>
            <!-- <th class="w150"><?=__('商家货号')?></th> -->
            <th class="w150"><?=__('商品状态')?></th>
            <th class="w150"><?=__('商品价格')?></th>
            <th class="w120"><?=__('门店库存')?></th>
            <th class="w180"><?=__('操作')?></th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($goods)){
            foreach($goods as $key => $val){?>
                <tr>
                    <td></td>
                    <td><div class="pic-thumb"><a href="<?=Yf_Registry::get('url')?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?=$val['goods_id']?>" target="_blank"><img src="<?=$val['goods_image']?>"></a></div></td>
                    <td class="tl"><dl class="goods-name">
                            <dt style="max-width: 450px !important;"> <a href="<?=Yf_Registry::get('url')?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?=$val['goods_id']?>" target="_blank"><?=$val['goods_name']?></a></dt>
                        </dl></td>
                    <td><?=$val['SPU']?></td>
                    <!-- <td><?=$val['goods_code']?></td> -->
                    <td><?= $val['isValid'] ? "出售中" : "已下架"; ?></td>
                    <td><span><?=format_money($val['goods_price'])?></span></td>
                    <td><span><?=$val['goods_stock']?><?=__('件')?></span></td>
                    <td class="nscs-table-handle">
                        <span>
                            <a href="javascript:void(0);" class="bbc_seller_btns js-edit-goods button button_blue <?= $val['isValid'] ? "" : "disabled"; ?>" nctype="set_stock" data-commonid="<?= $val['SPU'] ?>">
                                <p><?= __('设置库存') ?></p>
                            </a>
                        </span>
                        <span>
 <a href="javascript:void(0);" class="bbc_seller_btns js-edit-goods button button_blue <?= $val['recommend_goods'] == 1 ?  'recommend' :  ''?>" data-commonid="<?= $val['SPU'] ?>" data-goodsid="<?=$val['goods_id']?>" onclick="recommend_goods(<?=$val['goods_id']?>,<?= $val['recommend_goods']?>)">
                    <p><?=$val['recommend_goods'] == 1 ?  __('取消推荐') : __('&nbsp&nbsp&nbsp推荐&nbsp&nbsp&nbsp') ?></p>
                </a>
                        
                            
                        </span>
                    </td>
                </tr>
            <?php }}else{ ?>
            <tr class="row_line">
                <td colspan="99">
                    <div class="no_account">
                        <img src="<?=$this->view->img?>/ico_none.png">
                        <p><?=__('暂无符合条件的数据记录')?></p>
                    </div>
                </td>
            </tr>
        <?php }?>
        <tr style="display:none;">
            <td colspan="20"><div class="ncsc-goods-sku ps-container"></div></td>
        </tr>
        </tbody>
        <tfoot>
        <?php if(!empty($page_nav)){?>
            <tr>
                <td colspan="99">
                    <div class="page">
                        <?=$page_nav?>
                    </div>
                </td>
            </tr>
        <?php }?>
        </tfoot>
    </table>
</div>
    <script>
        function recommend_goods (goods_id,type) {
            $.ajax({
            type: "POST",
            url: "./index.php?ctl=Chain_Goods&met=setRecommendGoods&typ=json",
            dataType: "json",
            async: false,
            data: {"goods_id":goods_id,'type':type},
                success: function (respone) {
                    if (respone.status == 250) {
                        parent.Public.tips.error(respone.msg);
                    } else {
                        if ($("a[data-goodsid=" + respone.data.goods_id  + "]").hasClass('recommend')) {
                            $('a[data-goodsid=' + respone.data.goods_id  + ']').removeClass('recommend');
                            $('a[data-goodsid=' + respone.data.goods_id  + ']').html("<p><?= __('推荐') ?></p>");
                        } else {
                            $('a[data-goodsid=' + respone.data.goods_id  + ']').addClass('recommend');
                            $('a[data-goodsid=' + respone.data.goods_id  + ']').html("<p><?= __('取消推荐') ?></p>");
                        }
                        parent.Public.tips.success(respone.msg);
                        window.location.reload();
                    }
                }
            });
        }

        $(function(){
            $('a[nctype="set_stock"]').click(function(){
                if($(this).hasClass("disabled")) {
                    return false;
                }
                var common_id = $(this).attr('data-commonid');
                $.dialog({
                    title: "<?=__('设置库存')?>",
                    content: "url: <?= Yf_Registry::get('url') ?>?ctl=Chain_Goods&met=goodsStock&common_id="+common_id+"&typ=e",
                    data: { callback: callback},
                    width: 800,
                    lock: true
                })
                //
                function callback ( api ) {
                    api.close();
                    window.location.reload();
                }
            });
        });
        $(document).ready(function(){
            $('#search_form').validator({
                ignore: ':hidden',
                theme: 'yellow_right',
                timely: 1,
                stopOnError: false,
                valid:function(form){
                    window.location.href="<?= Yf_Registry::get('url') ?>?ctl=Chain_Goods&met=goods&"+$("#search_form").serialize()+"&typ=e";
                }

            });
        });
    </script>
<?php
include $this->view->getTplPath() . '/' . 'chain_footer.php';
?>