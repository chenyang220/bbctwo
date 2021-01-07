<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
include TPL_PATH . '/' . 'header.php';
?>

<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css?>/page.css">
	<link rel="stylesheet" href="<?=$this->view->css?>/mb.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/layer/layer.min.js"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validation.min.js" charset="utf-8"></script>

<div class="mb-item-edit-content">

    <input type="hidden" name="widget_width" value="<?=$data["width"] ?>" id="widget_width"/>
    <input type="hidden" name="widget_height" value="<?=$data["height"]  ?>" id="widget_height"/>
    <input type="hidden" name="layout_id" value="<?=$data["layout_id"]  ?>" id="layout_id"/>
    <input type="hidden" name="widget_cat" value="<?=$data["met"]  ?>" id="widget_cat"/>
    <input type="hidden" name="widget_name" id="widget_name"value="<?=$data["widget_name"]  ?>" />
    <input type="hidden" name="page_id" id="page_id"  value="<?=$data["page_id"]  ?>" />
    <input type="hidden" name="widget_id" value="<?php if($data['goods']){ $goods = current($data['goods']);  echo $goods['widget_id'];} ?>" id="widget_id"/>

    <div class="index_block goods-list">
        <h5><?= __('内容：'); ?></h5>
        <div nctype="item_content" class="content">
            <?php if($data['goods']){foreach($data['goods'] as $key => $val){ ?>
            <div nctype="item_image" class="item">
                <input type="hidden" id="goods_id" value="<?=$val['content']['goods_info']['common_id']?>">
                <div class="goods-pic"><img width="220px" height="220px" nctype="image" src="<?=$val['content']['goods_info']['common_image']?>" alt=""></div>
                <div class="goods-name" nctype="goods_name"><?=$val['content']['goods_info']['common_name']?></div>
                <div class="goods-price" nctype="goods_price"><?=$val['content']['goods_info']['common_price']?></div>
                <input nctype="goods_id" name="item_data[item][]" type="hidden" value="<?=$val['content']['goods_info']['common_id']?>">
                <a nctype="btn_del_item_image" href="javascript:;"><?= __('删除'); ?></a>
            </div>
            <?php }}?>
        </div>
    </div>

    <div class="search-goods">
        <!--  <h3><?= __('选择商品添加'); ?></h3> -->
        <h5><?= __('商品关键字：'); ?></h5>
        <input id="txt_goods_name" type="text" class="txt w200" name="" style="line-height:22px;">
        <a id="btn_mb_special_goods_search" class="ncap-btn" href="javascript:;" style="vertical-align: top; margin-left: 5px;"><?= __('搜索'); ?></a>
        <div id="mb_special_goods_list">
            <div class="grid-wrap">
                <table id="grid">
                </table>
                <div id="page"></div>
            </div>
        </div>
    </div>

    <tr>
        <td class="foot" colspan="2"><input class="btn-sub-input ml10" id="submit" type="submit" value="<?= __('保存'); ?>" /></td>
    </tr>
</div>
<?php
include TPL_PATH . '/' . 'footer.php';
?>
<script type="text/javascript" src="<?= $this->view->js_com ?>/template.js" charset="utf-8"></script>
<script id="item_goods_template" type="text/html">
    <div nctype="item_image" class="item">
        <div class="goods-pic"><img width="220px" height="220px" nctype="image" src="<%=goods_image%>" alt=""></div>
        <div class="goods-name" nctype="goods_name"><%=goods_name%></div>
        <div class="goods-price" nctype="goods_price"><%=goods_price%></div>
        <input nctype="goods_id" name="item_data[item][]" type="hidden" value="<%=common_id%>">
        <input type="hidden" id="goods_id" value="<%=common_id%>">
        <a nctype="btn_del_item_image" href="javascript:;"><?= __('删除'); ?></a>
    </div>
</script>
<script type="text/javascript">
    Array.prototype.remove = function (val) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] == val) {
                this.splice(i, 1);
            }
        }
    };
    $(document).ready(function () {
        var  widget_width = $.trim($("#widget_width").val());
        var  widget_height = $.trim($("#widget_height").val());
        var  widget_id = $.trim($("#widget_id").val());
        var  page_id = $.trim($("#page_id").val());
        var  widget_name = $.trim($("#widget_name").val());
        var  widget_cat = $.trim($("#widget_cat").val());
        var  layout_id = $.trim($("#layout_id").val());

        var goods_id = '';

        $('#btn_mb_special_goods_search').on('click', function () {
            var keyword = $('#txt_goods_name').val();
            $("#grid").jqGrid('setGridParam', {
                page: 1,
                postData: {
                    common_state: 1,
                    common_verify: 1,
                    common_name: keyword
                }
            }).trigger("reloadGrid");
        });
        $('#mb_special_goods_list').on('click', '[nctype="btn_add_goods"]', function () {
            var item = {},
                rowId = $(this).data('id'),
                rowData = $('#grid').jqGrid('getRowData', rowId);


            console.info(rowData);


            common_image = $("#grid").data('gridData')[rowId].common_image;
            goods_id = rowData.common_id;
            item.goods_id = rowData.common_id;
            item.goods_name = rowData.common_name;
            item.goods_price = rowData.common_price;
            item.goods_image = common_image;
            var html = template.render('item_goods_template', item);
            $('[nctype="item_content"]').html(html);
        });

        $('.goods-list').on('click', '[nctype="btn_del_item_image"]', function () {
            var goods_id = $(this).prev().val();
            $(this).parents('div:eq(0)').remove();
        });
        var handle = {
            imageFmatter: function (val, opt, row) {
                if (row.common_image) {
                    val = '<img width="60px" height="60px" src="' + row.common_image + '">';
                } else {
                    val = '&#160;';
                }
                return val;
            },
            addFmatter: function (val, opt, row) {
                return '<span class="set-status ui-label ui-label-success" nctype="btn_add_goods" data-enable="1" data-id="' + row.id + '">' + "<?= __('添加'); ?>" + '</span>';
            }
        };

        function initGrid() {
            var grid_row = Public.setGrid();
            var colModel = [{
                "name": "common_id",
                "index": "common_id",
                "label": "<?= __('商品'); ?>SPU",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 60
            },{
                "name": "common_name",
                "index": "common_name",
                "label": "<?= __('商品名称'); ?>",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 300
            }, {
                "name": "common_price",
                "index": "common_price",
                "label": "<?= __('商品价格'); ?>",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 100
            }, {
                "name": "common_image",
                "index": "common_image",
                "label": "<?= __('商品主图'); ?>",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 100,
                "formatter": handle.imageFmatter,
                "classes": 'img_flied'
            }, {
                "name": "shop_id",
                "index": "shop_id",
                "label": "<?= __('操作'); ?>",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 70,
                "formatter": handle.addFmatter
            }];
            $('#grid').jqGrid({
                url: SITE_URL + '?ctl=Goods_Goods&met=listCommon&typ=json',
                postData: {
                    common_state: 1,
                    common_verify: 1,
                    product_distributor_flag:1
                },
                datatype: 'json',
                shrinkToFit: true,
                forceFit: true,
                width: 700,
                height: 120,
                colModel: colModel,
                pager: '#page',
                cmTemplate: {
                    sortable: true
                },
                rowNum: 100,
                rowList: [10, 20, 50, 100, 200, 500],
                //scroll: 1,
                jsonReader: {
                    root: "data.items",
                    records: "data.records",
                    total: "data.total",
                    repeatitems: false,
                    id: "common_id"
                },
                loadComplete: function (response) {
                    if (response && response.status == 200) {
                        var gridData = {};
                        data = response.data;
                        for (var i = 0; i < data.items.length; i++) {
                            var item = data.items[i];
                            item['id'] = item.common_id;
                            gridData[item.common_id] = item;
                            $("#grid").data('gridData', gridData);
                        }
                    } else {
                        var msg = response.status === 250 ? (searchFlag ? '<?= __('没有满足条件的结果哦！'); ?>' : '<?= __('没有数据哦！'); ?>') : response.msg;
                        parent.Public.tips({
                            type: 2,
                            content: msg
                        });
                    }
                },
                loadError: function (xhr, status, error) {
                    parent.Public.tips({
                        type: 1,
                        content: '<?= __('操作失败了哦，请检查您的网络链接！'); ?>'
                    });
                },
            }).navGrid('#page', {
                edit: false,
                add: false,
                del: false,
                search: false,
                refresh: false
            });
        }

        initGrid();



        $('.mb-item-edit-content').on('click', '#submit', function () {
            var item_goods_id = $('#goods_id').val()?$('#goods_id').val():goods_id;

            if(!item_goods_id)
            {
                Public.tips({type: 1, content: "<?=__('请添加商品')?>"});
            }
            else
            {
                Public.ajaxPost(SITE_URL + '?ctl=Floor_Adposition&met=goods_add&typ=json', {
                    page_id: page_id,
                    item_goods_id: item_goods_id,
                    widget_id: widget_id,
                    layout_id: layout_id,
                    widget_width: widget_width,
                    widget_height: widget_height,
                    widget_name: widget_name,
                }, function (data) {
                    if (data.status == 200) {
                        var callback = frameElement.api.data.callback;
                        callback();
                    } else {
                        Public.tips({type: 1, content: data.msg});
                    }
                })
            }


        });


    });
</script>

