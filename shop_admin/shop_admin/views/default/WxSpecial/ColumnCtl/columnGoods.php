<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
    include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/mb.css" rel="stylesheet" type="text/css">
<div class="mb-item-edit-content">
    <div class="index_block goods-list">
        <div nctype="item_content" class="content">
            <h5><?= __('内容：'); ?></h5>
        </div>
    </div>
    
    <div class="search-goods">
        <input id="txt_goods_name" type="text" class="txt w200" name="" style="line-height:22px;" placeholder="商品名称">
        <a id="btn_mb_special_goods_search" class="ncap-btn" href="javascript:;" style="vertical-align: top; margin-left: 5px;"><?= __('搜索'); ?></a>
        <div id="mb_special_goods_list">
            <div class="grid-wrap">
                <table id="grid">
                </table>
                <div id="page"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $this->view->js ?>/controllers/wxspecial/columnGoods.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/template.js" charset="utf-8"></script>
<script id="item_goods_template" type="text/html">
    <div nctype="item_image" class="item">
        <div class="goods-pic"><img width="220px" height="220px" nctype="image" src="<%=goods_image%>" alt=""></div>
        <div class="goods-name" nctype="goods_name"><%=goods_name%></div>
        <div class="goods-price" nctype="goods_price"><%=goods_price%></div>
        <input nctype="goods_id" name="item_data[item][]" type="hidden" value="<%=goods_id%>">
        <input nctype="common_order" name="item_data[item][]" type="hidden" value="0">
        <a nctype="btn_del_item_image" href="javascript:;"><?= __('删除'); ?></a>
    </div>
</script>
<?php include TPL_PATH . '/' . 'footer.php'; ?>

