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
<link href="<?= $this->view->css ?>/mb.css" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css ?>/add.css?v=1121" rel="stylesheet" type="text/css">
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
    </div>

    
</div>
<?php
include TPL_PATH . '/' . 'footer.php';
?>
<script>
    var special_id = 0;
    var $item_list = $('#item_list');
    var item_data = new Array();
    var url_item_list = SITE_URL + "?ctl=Mb_TplLayout&met=tplLayoutList&typ=json&tpl_layout_style=2";
    var url_item_add = SITE_URL + "?ctl=Mb_TplLayout&met=addTplLayout&typ=json&tpl_layout_style=2";
    var url_item_del = SITE_URL + "?ctl=Mb_TplLayout&met=removeTplLayout&typ=json&tpl_layout_style=2";
    var url_item_usable = SITE_URL + "?ctl=Mb_TplLayout&met=editUsableTplLayout&typ=json&tpl_layout_style=2";
    var url_item_edit = SITE_URL + "?ctl=Mb_TplLayout&met=getEditPage&typ=e&tpl_layout_style=2";
    var url_item_edit_sort = SITE_URL + "?ctl=Mb_TplLayout&met=editSortTplLayout&typ=json&tpl_layout_style=2";
    $(document).ready(function(){
        Public.ajaxGet(url_item_list, {}, function(data){
            if ( data.status == 200 ) {
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
( item_data.mb_tpl_layout_data ? '<div nctype="item_image" class="item"> <img nctype="image" src="' + img + '" alt=""></div>' : '<div nctype="item_image" class="item"> <img nctype="image" src="' + '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(2).png' + '" alt=""></div>' ) +
                                '</div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'home1':
                    var img = item_data.mb_tpl_layout_data ?　item_data.mb_tpl_layout_data.image : '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(3).png';
                    html = '<div nctype="special_item" class="special-item adv_list '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                               '<div class="item_type">模型版块布局A</div>' +
                                '<div id="item_edit_content"><div class="home1"><div class="title"><span></span></div>' +
                                '<div nctype="item_content" class="content">' +
                                '<div nctype="item_image" class="item"> <img nctype="image" src="' + img +' " alt=""></div></div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down "></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'home2':
                    var f_img = default_img, s_img = default_img, t_img = default_img;
                    if ( item_data.mb_tpl_layout_data ) {
                        f_img = item_data.mb_tpl_layout_data.square ? item_data.mb_tpl_layout_data.square.image : default_img,
                        s_img = item_data.mb_tpl_layout_data.rectangle1 ? item_data.mb_tpl_layout_data.rectangle1.image : default_img,
                        t_img = item_data.mb_tpl_layout_data.rectangle2 ? item_data.mb_tpl_layout_data.rectangle2.image : default_img;
                    }
                    html = '<div nctype="special_item" class="special-item home2 '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">模型版块布局B</div>' +
                                '<div id="item_edit_content"><div class="home2"><div class="title"><span></span></div>' +
                                '<div class="content">' +
                                '<div class="home2_1"><div nctype="item_image" class="item"><img nctype="image" src="' + f_img + '" alt=""></div></div>' +
                                '<div class="home2_2"><div class="home2_2_1"><div nctype="item_image" class="item"><img nctype="image" src="' + s_img + '" alt=""></div></div>' +
                                '<div class="home2_2_2">' +
                                '<div nctype="item_image" class="item"> <img nctype="image" src="' + t_img + '" alt="">' +
                                '</div></div></div></div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'home3' :
                    var layout_html = new String();
                    if (item_data.mb_tpl_layout_data && item_data.mb_tpl_layout_data.length>0) {
                        var layout_data = item_data.mb_tpl_layout_data;
                        for(var i=0; i<layout_data.length; i++) {
                            layout_html += '<div nctype="item_image" class="item"> <img nctype="image" src="' + layout_data[i].image + '" alt=""></div>';
                        }
                    } else {
                        layout_html ='<div nctype="item_image" class="item"> <img nctype="image" src="' + '<?= $this->view->img_com ?>' + '/gy/Rectangle 18@2x.png' + '" alt=""></div>'
                        +'<div nctype="item_image" class="item"> <img nctype="image" src="' + '<?= $this->view->img_com ?>' + '/gy/Rectangle 18 Copy@2x.png' + '" alt=""></div>'
                        +'<div nctype="item_image" class="item"> <img nctype="image" src="' + '<?= $this->view->img_com ?>' + '/gy/Rectangle 18@2x.png' + '" alt=""></div>'
                        +'<div nctype="item_image" class="item"> <img nctype="image" src="' + '<?= $this->view->img_com ?>' + '/gy/Rectangle 18 Copy@2x.png' + '" alt=""></div>';
                    }
                    html = '<div nctype="special_item" class="special-item home3 '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">模型版块布局C</div>' +
                                '<div id="item_edit_content"><div class="home3"><div class="title"><span></span></div>' +
                                '<div nctype="item_content" class="content">' + layout_html + '</div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'home4' :
                    var f_img = default_img, s_img = default_img, t_img = default_img;
                    if ( item_data.mb_tpl_layout_data ) {
                        f_img = item_data.mb_tpl_layout_data.square ? item_data.mb_tpl_layout_data.square.image : default_img,
                        s_img = item_data.mb_tpl_layout_data.rectangle1 ? item_data.mb_tpl_layout_data.rectangle1.image : default_img,
                        t_img = item_data.mb_tpl_layout_data.rectangle2 ? item_data.mb_tpl_layout_data.rectangle2.image : default_img;
                    }
                    html = '<div nctype="special_item" class="special-item home4 '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">模型版块布局D</div>' +
                                '<div id="item_edit_content"><div class="home2"><div class="title"><span></span></div>' +
                                '<div class="content"><div class="home2_2"><div class="home2_2_1"><div nctype="item_image" class="item"> <img nctype="image" src="' + s_img + '" alt="">' +
                                '</div><div class="home2_2_2"><div nctype="item_image" class="item"> <img nctype="image" src="' + t_img + '" alt="">' +
                                '</div></div></div></div>' +
                                '<div class="home2_1">' +
                                '<div nctype="item_image" class="item"> <img nctype="image" src="' + f_img + '" alt="">' +
                                '</div></div></div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a><a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
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
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'enterance' :
                    var enterance_html = new String();
                    if(item_data.mb_tpl_layout_data) {
                        var layout_data = item_data.mb_tpl_layout_data;
                        for( var i=0; i<layout_data.length; i++) {
                            enterance_html += '<li><em><img src="'+ layout_data[i].icons+'"></em><p>'+layout_data[i].navName+'</p></li>';
                        }
                    } else {
                         enterance_html = 
                            '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Group 4@2x.png' +'"></em><p>'+"电器控制"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x.png' +'"></em><p>'+"工业自动化"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Group 5@2x.png' +'"></em><p>'+"进口劳保"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(1).png' +'"></em><p>'+"国产劳保"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Bitmap Copy@2x.png' +'"></em><p>'+"动力工具"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Group 4 Copy 2@2x.png' +'"></em><p>'+"机械测量"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Bitmap Copy 4@2x.png' +'"></em><p>'+"气动元件"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Group 5 Copy 2@2x.png' +'"></em><p>'+"办公清洁"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Bitmap Copy 2@2x.png' +'"></em><p>'+"搬运仓储"+'</p></li>'
                         +  '<li><em><img src="'+ '<?= $this->view->img_com ?>' + '/gy/Bitmap Copy 3@2x.png' +'"></em><p>'+"热销产品"+'</p></li>';
                    }

                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">快捷入口版块</div>' +
                                '<div id="item_edit_content">' +
                                '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content"><ul class="fast-nav-preview clearfix">' + enterance_html + '</ul></div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'activityA' :
                    var activitya_html = new String();
                    if(item_data.mb_tpl_layout_data) {
                        if(item_data.mb_tpl_layout_data["0"]){
                            var layout_data = item_data.mb_tpl_layout_data["0"].content_info;
                            switch(item_data.mb_tpl_layout_data["0"].type){
                                case "groupbuy":
                                for( var i=0; i<layout_data.length; i++) {
                                    activitya_html += ' <ul class="tg-discount clearfix"><li class="fl">'+
                                        '<em class="indexImg"><img src="'+layout_data[i].groupbuy_image_rec +'" /></em></li><li class="fr details-pt"><h4 class="one-overflow">'+layout_data[i].groupbuy_name+'</h4>'+
                                        '<span>￥'+layout_data[i].groupbuy_price+'</span><em>'+layout_data[i].goods_price+'</em><a href="javascript:;">立即去团</a></li></ul>';
                                }
                                break;
                                case "discount":
                                for( var i=0; i<layout_data.length; i++) {
                                    activitya_html += '<li><div class="discount-img"><img src="'+layout_data[i].goods_image+'" /></div><p class="one-overflow">'+layout_data[i].goods_name+'</p><div class="clearfix">'+
                                    '<span>￥'+layout_data[i].discount_price+'</span></div></li>';
                                }
                                var strs=' <div class="xs-discount"><ul class=" clearfix">'+activitya_html+'</ul></div>';
                                activitya_html=strs;
                                break;
                                case "pintuan":
                                for( var i=0; i<layout_data.length; i++) {
                                    activitya_html += ' <ul class="tg-discount clearfix"><li class="fl">'+
                                        '<em class="indexImg"><img src="'+layout_data[i].goods_image +'" /></em></li><li class="fr details-pt"><h4 class="one-overflow">'+layout_data[i].goods_name+'</h4>'+
                                        '<span>￥'+layout_data[i].price+'</span><a href="javascript:;">去拼团</a></li></ul>';
                                }
                                break;
                            }
                                
                            
                        }
                        
                    }

                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">活动版块A</div>' +
                                '<div id="item_edit_content">' +
                                '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content"><div class="actA">' + activitya_html + '</div></div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-module="A"  data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'activityB' :
                    var activityb_html = new String();
                        // if(item_data.mb_tpl_layout_data) {
                        for(var a=0;a<item_data.mb_tpl_layout_data.length;a++){
                            // if(item_data.mb_tpl_layout_data[a].content_info){
                                var actList=item_data.mb_tpl_layout_data[a].content_info;
                                switch(item_data.mb_tpl_layout_data[a].type){
                                    case "groupbuy":
                                    var str_groupbuy=" ";
                                    for( var i=0; i<actList.length; i++) {
                                        str_groupbuy+='<em class="indexImg"><img class="wp100" src="'+actList[i].groupbuy_image +'" /></em>';
                                    }
                                    activityb_html+='<div class="xs-groudbuy  module-half">'+str_groupbuy+'</div>';
                                    break;
                                    case "discount":
                                    var str_discount=" ";
                                    for( var i=0; i<actList.length; i++) {
                                        str_discount += '<li class="item-discount"><div class="discount-img"><img class="wp100" src="'+actList[i].goods_image+'" /></div><p class="one-overflow">'+actList[i].goods_name+'</p><div class="clearfix">'+
                                        '<span>￥'+actList[i].discount_price+'</span></div></li>';
                                    }
                                    activityb_html+='<ul class="xs-discount  module-half">'+str_discount+'</ul>';
                                    break;
                                    case "pintuan":
                                    var str_pintuan=" ";
                                    for( var i=0; i<actList.length; i++) {
                                        str_pintuan +='<em class="indexImg"><img class="wp100" src="'+actList[i].goods_image +'" /></em>';
                                    }
                                    activityb_html+='<div class="xs-pintuan  module-half">'+str_pintuan+'</div>';
                                    break;
                                    case "voucher":
                                    var strs_voucher="";
                                        strs_voucher +='<em class=""><img class="wp100" src="'+actList+'" /></em>';
                                         activityb_html+='<div class="xs-voucher  module-half">'+strs_voucher+'</div>';
                                    break;
                                    case "redpacket":
                                    var strs_redpacket="";
                                        strs_redpacket +='<em class=""><img class="wp100" src="'+actList+'" /></em>';
                                        activityb_html+='<div class="xs-voucher  module-half">'+strs_redpacket+'</div>';
                                    break;
                                    case "seckill":
                                    var str_seckill=" ";
                                    for( var i=0; i<actList.length; i++) {
                                        str_seckill += '<li class="item-discount"><div class="discount-img"><img class="wp100" src="'+actList[i].goods_image+'" /></div><p class="one-overflow">'+actList[i].goods_name+'</p><div class="clearfix">'+
                                        '<span>￥'+actList[i].seckill_price+'</span></div></li>';
                                    }
                                    activityb_html+='<ul class="xs-discount  module-half">'+str_seckill+'</ul>';
                                    break;
                                }
                            // }
                        }

                        
                    // }


                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">活动版块B</div>' +
                                '<div id="item_edit_content">' +
                                '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content"><div class="actA">' + activityb_html + '</div></div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-module="B" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'goodsB' :
                    var goosd_html = new String();
                    if(item_data.mb_tpl_layout_data) {
                        var layout_data = item_data.mb_tpl_layout_data;
                        for( var i=0; i<layout_data.length; i++) {
                            goosd_html += '<li><div class="discount-img"><img src="'+layout_data[i].goods_image+'" /></div><p class="one-overflow">'+layout_data[i].goods_name+'</p><div class="clearfix">'+
                            '<span>￥'+layout_data[i].goods_price+'</span></div></li>';
                        }
                        var strs=' <div class="xs-discount"><ul class=" clearfix">'+goosd_html+'</ul></div>';
                        goosd_html=strs;
                    }
                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                                '<div class="item_type">商品版块B</div>' +
                                '<div id="item_edit_content">' +
                                    '<style type="text/css">' +
                                        '.mb-item-edit-content { background: #EFFAFE url(/default/images/cms_edit_bg_line.png) repeat-y scroll 0 0; }' +
                                    '</style>' +
                                '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content">' + goosd_html + '</div></div></div>' +
                                '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                                    '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                                    '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                                    '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    
                    break;
                case 'advA' :
                    var goosd_html = new String();
                    if(item_data.mb_tpl_layout_data) {
                        var f_img = default_img, s1_img = default_img, s2_img = default_img,t1_img = default_img,t2_img = default_img,t3_img = default_img,t4_img = default_img;
                        if ( item_data.mb_tpl_layout_data ) {
                            f_img = item_data.mb_tpl_layout_data.square ? item_data.mb_tpl_layout_data.square.image : default_img,
                            s1_img = item_data.mb_tpl_layout_data.rectangle1 ? item_data.mb_tpl_layout_data.rectangle1.image : default_img,
                            s2_img = item_data.mb_tpl_layout_data.rectangle2 ? item_data.mb_tpl_layout_data.rectangle2.image : default_img,
                            t1_img = item_data.mb_tpl_layout_data.rectangle3 ? item_data.mb_tpl_layout_data.rectangle3.image : default_img,
                            t2_img = item_data.mb_tpl_layout_data.rectangle4 ? item_data.mb_tpl_layout_data.rectangle4.image : default_img,
                            t3_img = item_data.mb_tpl_layout_data.rectangle5 ? item_data.mb_tpl_layout_data.rectangle5.image : default_img,
                            t4_img = item_data.mb_tpl_layout_data.rectangle6 ? item_data.mb_tpl_layout_data.rectangle6.image : default_img;

                        }
                        goosd_html +='<div class=""><div class="iblock square-box"><em><img src="'+ f_img+'"></em></div><div class="iblock sr-box"><em><img src="'+s1_img+'"></em><em><img src="'+s2_img+'"></em></div><ul class="tb-box"><li><em><img src="'+t1_img+'"></em></li><li><em><img src="'+t2_img+'"></em></li><li><em><img src="'+t3_img+'"></em><[/li><li><em><img src="'+t4_img+'"></em></li></ul></div>';      
                    }
                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                        '<div class="item_type">模型板块布局E</div>' +
                        '<div id="item_edit_content">' +
                        '<style type="text/css">' +
                        '.mb-item-edit-content { background: #EFFAFE url(/default/images/cms_edit_bg_line.png) repeat-y scroll 0 0; }' +
                        '</style>' +
                        '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content">' + goosd_html + '</div></div></div>' +
                        '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                        '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                        '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                        '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                        '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'advB' :
                    var goosd_html = new String();
                     if(item_data.mb_tpl_layout_data) {
                        var t1_img = default_img,t2_img = default_img,t3_img = default_img,t4_img = default_img,t5_img = default_img;
                        if ( item_data.mb_tpl_layout_data ) {
                            t1_img = item_data.mb_tpl_layout_data.rectangle1 ? item_data.mb_tpl_layout_data.rectangle1.image : default_img,
                            t2_img = item_data.mb_tpl_layout_data.rectangle2 ? item_data.mb_tpl_layout_data.rectangle2.image : default_img,
                            t3_img = item_data.mb_tpl_layout_data.rectangle3 ? item_data.mb_tpl_layout_data.rectangle3.image : default_img,
                            t4_img = item_data.mb_tpl_layout_data.rectangle4 ? item_data.mb_tpl_layout_data.rectangle4.image : default_img,
                            t5_img = item_data.mb_tpl_layout_data.rectangle5 ? item_data.mb_tpl_layout_data.rectangle5.image : default_img;

                        }
                        goosd_html +='<div class=""><div class="iblock advb-square-box"><em><img src="'+ t1_img+'"></em></div><ul class="advb-tb-box"><li><em><img src="'+t2_img+'"></em></li><li><em><img src="'+t3_img+'"></em></li><li><em><img src="'+t4_img+'"></em><[/li><li><em><img src="'+t5_img+'"></em></li></ul></div>';
                        
                    }
                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                        '<div class="item_type">模型板块布局F</div>' +
                        '<div id="item_edit_content">' +
                        '<style type="text/css">' +
                        '.mb-item-edit-content { background: #EFFAFE url(/default/images/cms_edit_bg_line.png) repeat-y scroll 0 0; }' +
                        '</style>' +
                        '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content">' + goosd_html + '</div></div></div>' +
                        '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                        '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                        '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                        '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                        '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                    break;
                case 'class' :
                    var goosd_html = new String();
                     // if(item_data.mb_tpl_layout_data) {
                        var t1_img = '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(4).png',t2_img = '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(5).png',t3_img = '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(6).png',t4_img = '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(7).png',t5_img = '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(8).png',t6_img = '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(9).png',t7_img = '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(10).png',t8_img = '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(11).png';


                        var class_name_total_1 = "品牌站",class_name_total_2 = "爆款抢购",class_name_total_3 = "个人防护",class_name_total_4 = "五金工具";
                        var image_name_1 = "博士",image_name_2 = "昆仑润滑",image_name_3 = "圣戈班",image_name_4 = "星宇",image_name_5 = "职安康",image_name_6 = "工盾坊",image_name_7 = "宝工",image_name_8 = "奥本";
                         if (item_data.mb_tpl_layout_data) {
                            t1_img = typeof item_data.mb_tpl_layout_data.classify1.image_1 != 'undefined' ? item_data.mb_tpl_layout_data.classify1.image_1 : t1_img,
                            t2_img = typeof item_data.mb_tpl_layout_data.classify1.image_2 != 'undefined' ? item_data.mb_tpl_layout_data.classify1.image_2 : t2_img,
                            t3_img = typeof item_data.mb_tpl_layout_data.classify2.image_1 != 'undefined' ? item_data.mb_tpl_layout_data.classify2.image_1 : t3_img,
                            t4_img = typeof item_data.mb_tpl_layout_data.classify2.image_2 != 'undefined' ? item_data.mb_tpl_layout_data.classify2.image_2 : t4_img,
                            t5_img = typeof item_data.mb_tpl_layout_data.classify3.image_1 != 'undefined' ? item_data.mb_tpl_layout_data.classify3.image_1 : t5_img,
                            t6_img = typeof item_data.mb_tpl_layout_data.classify3.image_2 != 'undefined' ? item_data.mb_tpl_layout_data.classify3.image_2 : t6_img,
                            t7_img = typeof item_data.mb_tpl_layout_data.classify4.image_1 != 'undefined' ? item_data.mb_tpl_layout_data.classify4.image_1 : t7_img,
                            t8_img = typeof item_data.mb_tpl_layout_data.classify4.image_2 != 'undefined' ? item_data.mb_tpl_layout_data.classify4.image_2 : t8_img;

                            class_name_total_1 = item_data.mb_tpl_layout_data.classify1.class_name_total != '' ? item_data.mb_tpl_layout_data.classify1.class_name_total : class_name_total_1;
                            class_name_total_2 = item_data.mb_tpl_layout_data.classify2.class_name_total != '' ? item_data.mb_tpl_layout_data.classify2.class_name_total : class_name_total_2;
                            class_name_total_3 = item_data.mb_tpl_layout_data.classify3.class_name_total != '' ? item_data.mb_tpl_layout_data.classify3.class_name_total : class_name_total_3;
                            class_name_total_4 = item_data.mb_tpl_layout_data.classify4.class_name_total != '' ? item_data.mb_tpl_layout_data.classify4.class_name_total : class_name_total_4;

                            image_name_1 = item_data.mb_tpl_layout_data.classify1.image_name_1 != '' ? item_data.mb_tpl_layout_data.classify1.image_name_1 : image_name_1;
                            image_name_2 = item_data.mb_tpl_layout_data.classify1.image_name_2 != '' ? item_data.mb_tpl_layout_data.classify1.image_name_2 : image_name_2;
                            image_name_3 = item_data.mb_tpl_layout_data.classify2.image_name_1 != '' ? item_data.mb_tpl_layout_data.classify2.image_name_1 : image_name_3;
                            image_name_4 = item_data.mb_tpl_layout_data.classify2.image_name_2 != '' ? item_data.mb_tpl_layout_data.classify2.image_name_2 : image_name_4;
                            image_name_5 = item_data.mb_tpl_layout_data.classify3.image_name_1 != '' ? item_data.mb_tpl_layout_data.classify3.image_name_1 : image_name_5;
                            image_name_6 = item_data.mb_tpl_layout_data.classify3.image_name_2 != '' ? item_data.mb_tpl_layout_data.classify3.image_name_2 : image_name_6;
                            image_name_7 = item_data.mb_tpl_layout_data.classify4.image_name_1 != '' ? item_data.mb_tpl_layout_data.classify4.image_name_1 : image_name_7;
                            image_name_8 = item_data.mb_tpl_layout_data.classify4.image_name_2 != '' ? item_data.mb_tpl_layout_data.classify4.image_name_2 : image_name_8;                            
                        }

                        goosd_html +='<div class="industry-module clearfix">'+
                        '<dl><dt><span>'+class_name_total_1+'</span></dt><dd class="clearfix"><a href=""><em class="img-box">' +
                        '<img src="'+t1_img+'" alt="img"></em><b>'+image_name_1+'</b></a>'+
                        '<a href="">'+'<em class="img-box"><img src="'+t2_img+'" alt="img"></em><b>'+image_name_2+'</b></a>'+
                        '</dd></dl>'+
                        '<dl><dt><span>'+class_name_total_2+'</span></dt><dd class="clearfix"><a href=""><em class="img-box">' +
                        '<img src="'+t3_img+'" alt="img"></em><b>'+image_name_3+'</b></a>'+
                        '<a href="">'+'<em class="img-box"><img src="'+t4_img+'" alt="img"></em><b>'+image_name_4+'</b></a>'+
                        '</dd></dl>'+
                        '<dl><dt><span>'+class_name_total_3+'</span></dt><dd class="clearfix"><a href=""><em class="img-box">' +
                        '<img src="'+t5_img+'" alt="img"></em><b>'+image_name_5+'</b></a>'+
                        '<a href="">'+'<em class="img-box"><img src="'+t6_img+'" alt="img"></em><b>'+image_name_6+'</b></a>'+
                        '</dd></dl>'+
                        '<dl><dt><span>'+class_name_total_4+'</span></dt><dd class="clearfix"><a href=""><em class="img-box">' +
                        '<img src="'+t7_img+'" alt="img"></em><b>'+image_name_7+'</b></a>'+
                        '<a href="">'+'<em class="img-box"><img src="'+t8_img+'" alt="img"></em><b>'+image_name_8+'</b></a>'+
                        '</dd></dl>'+
                        '</div>';
                    // }
                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                        '<div class="item_type">品类板块布局</div>' +
                        '<div id="item_edit_content">' +
                            '<style type="text/css">' +
                                '.mb-item-edit-content { background: #EFFAFE url(/default/images/cms_edit_bg_line.png) repeat-y scroll 0 0; }' +
                            '</style>' +
                        '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content">' + goosd_html + '</div></div></div>' +
                        '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                            '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                            '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                            '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                            '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
                     break;
                case 'home5' :
                    var goosd_html = new String();
                     // if(item_data.mb_tpl_layout_data) {
                        var t1_img =  '<?= $this->view->img_com ?>' + '/gy/Bitmap@2x(12).png',t2_img = default_img,t3_img = default_img,t4_img = default_img,t5_img = default_img;
                        if ( item_data.mb_tpl_layout_data && item_data.mb_tpl_layout_data.rectangle1 ) {
                            t1_img = item_data.mb_tpl_layout_data.rectangle1.image ? item_data.mb_tpl_layout_data.rectangle1.image : default_img;
                        }
                        var goosd = new String();
                        if(item_data.mb_tpl_layout_data && item_data.mb_tpl_layout_data.goods_ids) {
                            var layout_data = item_data.mb_tpl_layout_data.goods_ids;
                            for( var i=0; i<layout_data.length; i++) {
                                goosd += '<li><a href=""><em class="img-box"><img src="'+layout_data[i].goods_image+'" alt=""></em><span class="one-overflow">'+  layout_data[i].goods_name +'</span></a></li>';
                            }
                        }
                        goosd_html +='<div class="index-industry-module2"><div class="">'+
                                    '<em class="iblock img-box wp100"><img class="wp100" src="'+t1_img+'" alt="img"></em>'+
                                    '<ul class="home-ul">' + goosd + '</ul></div></div>';
                    // }
                    html = '<div nctype="special_item" class="special-item goods '+ (item_data.mb_tpl_layout_enable == 1 ? 'usable' : 'unusable') +'" data-item-id="' + item_data.mb_tpl_layout_id + '">' +
                        '<div class="item_type">模型板块布局G</div>' +
                        '<div id="item_edit_content">' +
                            '<style type="text/css">' +
                                '.mb-item-edit-content { background: #EFFAFE url(/default/images/cms_edit_bg_line.png) repeat-y scroll 0 0; }' +
                            '</style>' +
                        '<div class="goods-list"><div class="title"><span></span></div><div nctype="item_content" class="content">' + goosd_html + '</div></div></div>' +
                        '<div class="handle"><a nctype="btn_move_up" href="javascript:;">' +
                            '<i class="fa fa-arrow-up"></i>上移</a> <a nctype="btn_move_down" href="javascript:;">' +
                            '<i class="fa fa-arrow-down"></i>下移</a> <a nctype="btn_usable" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                            '<i class="fa fa-toggle-on"></i>启用</a> <a nctype="btn_edit_item" data-item-type="' + item_data.mb_tpl_layout_type + '" data-item-id="' + item_data.mb_tpl_layout_id + '" href="javascript:;">' +
                            '<i class="fa fa-pencil-square-o"></i>编辑</a></div></div>';
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
