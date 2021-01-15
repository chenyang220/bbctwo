<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
} ?>
<?php
include TPL_PATH . '/' . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单 同级菜单 当前菜单
$menus = $this->getThisMenus();
?>
<link href="<?= $this->view->css ?>/mb.css?v=813811" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css ?>/add.css?v=11232" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css ?>/new-file.css?v=1" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/qs.min.js" charset="utf-8"></script>
<div class="wrapper page">

    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __($menus['father_menu']['menu_name']); ?></h3>
                <h5><?= __($menus['father_menu']['menu_url_note']); ?></h5>
            </div>
            <ul class="tab-base nc-row">
                <?php
                foreach($menus['brother_menu'] as $key=>$val){
                    if(in_array($val['rights_id'],$admin_rights)||$val['rights_id']==0){
                ?>
                <li><a <?php if(!array_diff($menus['this_menu'], $val)){?> class="current"<?php }?> href="<?= Yf_Registry::get('url') ?>?ctl=<?=$val['menu_url_ctl']?>&met=<?=$val['menu_url_met']?><?php if($val['menu_url_parem']){?>&<?=$val['menu_url_parem']?><?php }?>"><span><?= __($val['menu_name']); ?></span></a></li>
                <?php
                    }
                }
                ?>
            </ul>
        </div>
    </div>


    <!-- 操作说明 -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="收起提示"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>

        </ul>
    </div>

    <div class="mb-special-layout">
        <div class="mb-item-box">
            <div id="item_list" class="item-list">

            </div>
        </div>

        <div class="module-list">
            <div class="module_adv_list"> <span>广告条版块</span> <a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="adv_list"></a> </div>
            <div class="module_goods"> <span>商品版块A</span> <a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="goods"></a> </div>
        </div>
    </div>


</div>
<?php
include TPL_PATH . '/' . 'footer.php';
?>
<script>
    var special_id = 0;
    var $item_list = $('#item_list');
    var item_data = new Array();
    var url_item_list = SITE_URL + "?ctl=Mb_TplLayout&met=tplLayoutList&typ=json&tpl_layout_style=4";
    var url_item_add = SITE_URL + "?ctl=Mb_TplLayout&met=addTplLayout&typ=json&tpl_layout_style=4";
    var url_item_del = SITE_URL + "?ctl=Mb_TplLayout&met=removeTplLayout&typ=json&tpl_layout_style=4";
    var url_item_usable = SITE_URL + "?ctl=Mb_TplLayout&met=editUsableTplLayout&typ=json&tpl_layout_style=4";
    var url_item_edit = SITE_URL + "?ctl=Mb_TplLayout&met=getEditPage&typ=e&tpl_layout_style=4";
    var url_item_edit_sort = SITE_URL + "?ctl=Mb_TplLayout&met=editSortTplLayout&typ=json&tpl_layout_style=4";
    $(document).ready(function(){
        Public.ajaxGet(url_item_list, {}, function(data){
            if ( data.status == 200 ) {
                console.log(data.data.items);
                if ( data.data.items && data.data.items.length > 0 ) {
                    var item_list = data.data.items, list_html = '';
                    for(var i=0; i<item_list.length; i++) {
                        list_html += createLayoutHtml(item_list[i]);
                        item_data[item_list[i].mb_tpl_layout_id] = item_list[i];
                    }
                    $item_list.append(list_html);
                } else {
                    Public.tips({type: 2, content: '暂无数据！'})
                }

            } else {
                Public.tips({type: 1, content: '读取数据失败！'})
            }
        });

        function createLayoutHtml(item_data) {
            var html = new String();
            var default_img = '<?= $this->view->img_com ?>' + '/image.png';
            switch (item_data.mb_tpl_layout_type) {
                case 'adv_list':
                    var img = (item_data.mb_tpl_layout_data && item_data.mb_tpl_layout_data.length > 0) ? item_data.mb_tpl_layout_data[0].image : default_img;
                    html = '<div nctype="special_item" class="special-item adv_list '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">广告条版块</div>' +
                                '<div id="item_edit_content"><div class="adv_list"><div nctype="item_content" class="content">' +
                                ( item_data.mb_tpl_layout_data ? '<div nctype="item_image" class="item"> <img nctype="image" src="' + img + '" alt=""></div>' : '' ) +
                                '</div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a> <a nctype="btn_del_item" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;"><i class="fa fa-trash-o"></i>删除</a></div></div>';
                    break;
                case 'goods' :
                    var goosd_html = new String();
                    if(item_data.mb_tpl_layout_data) {
                        var layout_data = item_data.mb_tpl_layout_data;
                        for( var i=0; i<layout_data.length; i++) {
                            goosd_html += '<div nctype="item_image" class="item">' +
                                                '<div class="goods-pic"><img nctype="goods_image" src="' + layout_data[i].goods_image + '" alt=""></div>' +
                                                '<div class="goods-name" nctype="goods_name">' + layout_data[i].goods_name + '</div>' +
                                           '<div class="goods-price" nctype="goods_price">￥' + layout_data[i].goods_price + '</div></div>';
                        }
                    }

                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">商品版块A</div>' +
                                '<div id="item_edit_content">' +
                                    '<style type="text/css">' +
                                        '.mb-item-edit-content { background: #EFFAFE url(/default/images/cms_edit_bg_line.png) repeat-y scroll 0 0; }' +
                                    '</style>' +
                                '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content">' + goosd_html + '</div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a> <a nctype="btn_del_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-trash-o"></i>删除</a></div></div>';
                    break;
            }
            return html;
        }
        //添加模块
        $('[nctype="btn_add_item"]').on('click', function() {
            var data = {
                special_id: special_id,
                item_type: $(this).attr('data-module-type')
            };
            $.post(url_item_add, data, function(data) {
                if(data.status === 200) {
                    location.reload();
                } else {
                    if(data.msg == 'failure') {
                        Public.tips({type: 1, content: '操作失败，请稍后重试'})
                    } else {
                        Public.tips({type: 1, content: data.msg})
                    }

                }
            }, "json");
        });

        //删除模块
        $('#item_list').on('click', '[nctype="btn_del_item"]', function() {
            var $this = $(this);
            var item_id = $this.attr('data-item-id');
            $.dialog.confirm("确认删除？", function ()
            {
                $.post(url_item_del, {item_id: item_id, special_id: special_id} , function(data) {
                    if(data.status === 200) {
                        $this.parents('.special-item').remove();
                    } else {
                        if(data.msg == 'failure') {
                            Public.tips({type: 1, content: '操作失败，请稍后重试'})
                        } else {
                            Public.tips({type: 1, content: data.msg})
                        }
                    }
                }, "json");
            })
        });
        //编辑模块
        $('#item_list').on('click', '[nctype="btn_edit_item"]', function() {

            var item_id = $(this).attr('data-item-id');
            var item_type = $(this).attr('data-item-type');
            var item_module=$(this).attr('data-item-module');
            //var act_type = $(this).attr('data-item-actype');
            var title = $(this).parents('div[nctype="special_item"]').find('div.item_type').html();
            $.dialog({
                title: title,
                dialogClass:"editDialog",
                content: 'url:' +  url_item_edit + '&item_type=' + item_type,
                data: {module:item_module, item_id: item_id, item_data: item_data[item_id], callback: function (){ window.location.reload(); } },
                max: false,
                min: false,
                cache: false,
                lock: true,
                parent: window,
                width:750,
                height:500
            })
        });

        //上移
        $('#item_list').on('click', '[nctype="btn_move_up"]', function() {
            var $current = $(this).parents('[nctype="special_item"]');
            $prev = $current.prev('[nctype="special_item"]');
            if($prev.length > 0) {
                $prev.before($current);
                update_item_sort();
            } else {
                showError('已经是第一个了');
            }
        });

        //下移
        $('#item_list').on('click', '[nctype="btn_move_down"]', function() {
            var $current = $(this).parents('[nctype="special_item"]');
            $next = $current.next('[nctype="special_item"]');
            if($next.length > 0) {
                $next.after($current);
                update_item_sort();
            } else {
                showError('已经是最后一个了');
            }
        });

        var update_item_sort = function() {
            var item_id_string = '';
            $item_list = $('#item_list').find('[nctype="special_item"]');
            $item_list.each(function(index, item) {
                item_id_string += $(item).attr('data-item-id') + ',';
            });
            $.post(url_item_edit_sort, {special_id: special_id, item_id_string: item_id_string}, function(data) {
                if(data.status == 250) {
                    if(data.msg == 'failure') {
                        Public.tips({type: 1, content: '操作失败，请稍后重试'})
                    } else {
                        Public.tips({type: 1, content: data.msg})
                    }
                }
            }, 'json');
        };

        //启用/禁用控制
        $('#item_list').on('click', '[nctype="btn_usable"]', function() {
            var $current = $(this).parents('[nctype="special_item"]');
            var item_id = $current.attr('data-item-id');
            var usable = '';
            if($current.hasClass('usable')) {
                $current.removeClass('usable');
                $current.addClass('unusable');
                usable = 'unusable';
                $(this).html('<i class="fa fa-toggle-off"></i>启用');
            } else {
                $current.removeClass('unusable');
                $current.addClass('usable');
                usable = 'usable';
                $(this).html('<i class="fa fa-toggle-on"></i>禁用');
            }

            $.post(url_item_usable, {item_id: item_id, usable: usable, special_id: special_id}, function(data) {
                if(data.status == 250) {
                    if(data.msg == 'failure') {
                        Public.tips({type: 1, content: '操作失败，请稍后重试'})
                    } else {
                        Public.tips({type: 1, content: data.msg})
                    }
                }
            }, 'json');
        });

    });
</script>

<script id="item_goods_template" type="text/html">
    <div nctype="item_image" class="item">
        <div class="goods-pic"><img nctype="image" src="<%=goods_image%>" alt=""></div>
        <div class="goods-name" nctype="goods_name"><%=goods_name%></div>
        <div class="goods-price" nctype="goods_price"><%=goods_price%></div>
        <input nctype="goods_id" name="item_data[item][]" type="hidden" value="<%=goods_id%>">
        <a nctype="btn_del_item_image" href="javascript:;">删除</a>
    </div>
</script>
<script src="<?= $this->view->js_com ?>/jquery.ajaxContent.pack.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#btn_mb_special_goods_search').on('click', function() {
            var url = '<?= Yf_Registry::get('url') ?>?act=mb_special&op=goods_list';
            var keyword = $('#txt_goods_name').val();
            if(keyword) {
                $('#mb_special_goods_list').load(url + '&' + $.param({keyword: keyword}));
            }
        });

        $('#mb_special_goods_list').on('click', '[nctype="btn_add_goods"]', function() {
            var item = {};
            item.goods_id = $(this).attr('data-goods-id');
            item.goods_name = $(this).attr('data-goods-name');
            item.goods_price = $(this).attr('data-goods-price');
            item.goods_image = $(this).attr('data-goods-image');
            var html = template.render('item_goods_template', item);
            $('[nctype="item_content"]').append(html);
        });
    });
</script>
