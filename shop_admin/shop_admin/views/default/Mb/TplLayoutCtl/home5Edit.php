<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>

<?php
    include TPL_PATH . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/mb.css?v=711" rel="stylesheet" type="text/css">
<div class="mb-item-edit-content">
    <div class="index_block goods-list2">
        <div class="title">
            <h5><?= __('标题：'); ?></h5>
            <input id="home5_title" type="text" class="txt w200" maxlength="8" name="item_data[title]" value="">
        </div>
         <div class="">
			<div nctype="item_image" class="item"> <img class="wp100" nctype="image" name="item_data[rectangle1_image]" src="<?= $this->view->img_com ?>/image.png" alt="">
				<input nctype="image_name" name="item_data[rectangle1_image]" type="hidden" value="s0_04953036568877399.jpg">
				<input nctype="image_type" name="item_data[rectangle1_type]" type="hidden" value="advA">
				<input nctype="image_data" name="item_data[rectangle1_data]" type="hidden" value="">
				<a nctype="btn_edit_item_image" data-name="rectangle1" data-desc="352*173" href="javascript:;"><i class="fa fa-pencil-square-o"></i><?= __('编辑'); ?></a>
			</div>
		</div>
        <div nctype="item_content" class="content">
            <h5 class="mt10"><?= __('内容：'); ?></h5>
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
</div>
<?php
    include TPL_PATH . '/' . 'footer.php';
?>
<script type="text/javascript" src="<?= $this->view->js_com ?>/template.js" charset="utf-8"></script>
<script id="item_goods_template" type="text/html">
    <div nctype="item_image" class="item">
        <div class="goods-pic"><img class="wp100" nctype="image" src="<%=goods_image%>" alt=""></div>
        <div class="goods-name one-overflow" nctype="goods_name"><%=goods_name%></div>
        <div class="goods-price" nctype="goods_price"><%=goods_price%></div>
        <input nctype="goods_id" name="item_data[item][]" type="hidden" value="<%=goods_id%>">
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
        var api = frameElement.api,
            item_data = api.data.item_data,
            item_id = item_data.mb_tpl_layout_id,
            layout_data = item_data.mb_tpl_layout_data,
            callback = api.data.callback;
        var goods_ids = new Array();
        if (typeof layout_data == "undefined" || layout_data == null || layout_data.length == 0) {
            layout_data = {
                rectangle1: {},
                goods_ids: {}
            }
        } else {
            $('#home5_title').val(item_data.mb_tpl_layout_title);
            var goods_html = new String();
            if (!layout_data.rectangle1) {
                layout_data.rectangle1 = {};
            } else if (!layout_data.goods_ids) {
                layout_data.goods_ids = goods_ids;
            }
            for (var i = 0; i < layout_data.goods_ids.length; i++) {
                goods_html = template.render('item_goods_template', layout_data.goods_ids[i]);
                $('[nctype="item_content"]').append(goods_html);
                goods_ids.push(layout_data.goods_ids[i].goods_id);
            }
            render(layout_data.rectangle1,"rectangle1");

        }
        $('[nctype="btn_edit_item_image"]').on('click', function () {
            var name = $(this).data('name');
            var image_spec = $(this).data('desc');
            $.dialog({
                title: '<?= __('编辑'); ?>',
                content: 'url:' + SITE_URL + '?ctl=Mb_TplLayout&met=editHome5Image&typ=e',
                max: true,
                min: false,
                cache: false,
                lock: true,
                width: 600,
                height: 400,
                zIndex: 9999,
                parent: window.parent,
                data: {
                    image_spec: image_spec,
                    image_name: name,
                    dialog_type: 'home5',
                    layout_data: layout_data,
                    callback: function (img_data) {
                        render(img_data, name);
                    }
                }
            })
        });

        function render (img_data, name) {
            var group = {};
            group.image = img_data.image;
            group.image_name = img_data.image_name;
            group.image_type = img_data.image_type;
            group.image_data = img_data.image_data;
            layout_data.rectangle1 = group;
            $('[nctype="image"][name="item_data[' + name + '_image]"]').prop('src', img_data.image);
            $('[nctype="image_name"][name="item_data[' + name + '_image]"]').val(img_data.image_name);
            $('[nctype="image_type"][name="item_data[' + name + '_type]"]').val(img_data.image_type);
            $('[nctype="image_data"][name="item_data[' + name + '_data]"]').val(img_data.image_data);
        }
        $('#btn_mb_special_goods_search').on('click', function () {
            var keyword = $('#txt_goods_name').val();
            //这里不需要判断搜索词是否为空，如果为空则显示所有商品'); ?>
            $("#grid").jqGrid('setGridParam', {
                page: 1,
                postData: {
                    common_state: 1,    //正常状态'); ?>
                    common_verify: 1,   //通过审核'); ?>
                    common_name: keyword
                }
            }).trigger("reloadGrid");
        });
        $('#mb_special_goods_list').on('click', '[nctype="btn_add_goods"]', function () {
            if (goods_ids.length >= 4) {
                parent.Public.tips({
                    type: 1,
                    content: '最多可以添加4个商品!'
                });
                return;
            }
            var item = {},
            rowId = $(this).data('id'),
            rowData = $('#grid').jqGrid('getRowData', rowId);
            common_image = $("#grid").data('gridData')[rowId].common_image;
            goods_ids.push(rowData.common_id);
            item.goods_id = rowData.common_id;
            item.goods_name = rowData.common_name;
            item.goods_price = rowData.common_price;
            item.goods_image = common_image;
            var html = template.render('item_goods_template', item);
            $('[nctype="item_content"]').append(html);
        });
        
        $('.goods-list').on('click', '[nctype="btn_del_item_image"]', function () {
            var goods_id = $(this).prev().val();
            goods_ids.remove(parseInt(goods_id));
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
                return '<span class="set-status ui-label ui-label-success" nctype="btn_add_goods" data-enable="1" data-id="' + row.id + '">' + '添加' + '</span>';
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
                "width": 20
            }, {
                "name": "common_name",
                "index": "common_name",
                "label": "<?= __('商品名称'); ?>",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 40
            }, {
                "name": "common_price",
                "index": "common_price",
                "label": "<?= __('商品价格'); ?>",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 30
            }, {
                "name": "common_image",
                "index": "common_image",
                "label": "<?= __('商品主图'); ?>",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 30,
                "formatter": handle.imageFmatter,
                "classes": 'img_flied'
            }, {
                "name": "shop_id",
                "index": "shop_id",
                "label": "<?= __('操作'); ?>",
                "classes": "ui-ellipsis",
                "align": "center",
                "title": false,
                "width": 20,
                "formatter": handle.addFmatter
            }];
            $('#grid').jqGrid({
                url: SITE_URL + '?ctl=Goods_Goods&met=listCommon&typ=json',
                postData: {
                    common_state: 1,
                    common_verify: 1
                },
                datatype: 'json',
                shrinkToFit: true,
                forceFit: true,
                width: 500,
                height: 300,
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
                        var msg = response.status === 250 ? (searchFlag ? '没有满足条件的结果哦！' : '没有数据哦！') : response.msg;
                        parent.Public.tips({
                            type: 2,
                            content: msg
                        });
                    }
                },
                loadError: function (xhr, status, error) {
                    parent.Public.tips({
                        type: 1,
                        content: '操作失败了哦，请检查您的网络链接!'
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
        api.button({
            id: "confirm", name: '确定', focus: !0, callback: function () {
                postData();
                return false;
            }
        }, {id: "cancel", name: '取消'});
        
        function postData() {
            var layout_title = $('#home5_title').val();
            if(!layout_title){
                parent.Public.tips({
                    type: 1,
                    content: '标题不能为空'
                });
            }else {
                layout_data.goods_ids = goods_ids;
                Public.ajaxPost(SITE_URL + '?ctl=Mb_TplLayout&met=editTplLayout&typ=json', {
                    item_id: item_id,
                    layout_data: layout_data,
                    layout_title: layout_title
                }, function (data) {
                    if (data.status == 200) {
                        typeof callback == 'function' && callback();
                        return true;
                    } else {
                        Public.tips({type: 1, content: data.msg});
                    }
                })
            }
        }
    });
</script>

