<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
include $this->view->getTplPath() . '/' . 'buyer_header.php';
?>
    <script src="<?= $this->view->js_com ?>/plugins/jquery.timeCountDown.js"></script>
    <script>
        $(function () {
            var _TimeCountDown = $(".fnTimeCountDown");
            _TimeCountDown.fnTimeCountDown();
        })
    </script>
    </div>
    <div class="order_content">
        <div class="order_content_title clearfix">
            <form method="get" id="search_form" action="index.php">
                <input type="hidden" name="ctl" value="<?= $_GET['ctl'] ?>"> <input type="hidden" name="met" value="<?= $_GET['met'] ?>">
                <p class="ser_p" style="margin-right: 200px;">
                    <select name="auditing" style="margin-right: 5px;">
                        <option value=""><?= __('请选择资讯状态') ?></option>
                        <option value="1" <?= request_string('auditing') == '1' ? 'selected':'' ?>><?= __('已通过') ?></option>
                        <option value="2" <?= request_string('auditing') == '2' ? 'selected':'' ?>><?= __('未通过') ?></option>
                        <option value="3" <?= request_string('auditing') == '3' ? 'selected':'' ?>><?= __('待审核') ?></option>
                    </select>
                    <input name="keyword" placeholder="<?= __('请输入资讯标题') ?>" value="<?= @$_GET['keyword'] ?>">
                    <a style="float:right;" class="btn_search_goods" href="javascript:void(0);" style="padding-left: 2px;"><i class="iconfont icon-icosearch icon_size18" style="margin-right:-2px; "></i><?= __('搜索') ?></a>
                </p>
                
                <script type="text/javascript">
                    $("a.btn_search_goods").on("click", function () {
                        $("#search_form").submit();
                    });
                </script>
            </form>
        </div>
        <table class="table-list-style" id="table_list" cellpadding="0" cellspacing="0">
            <tr>
                <th class="tl" width="30%"><?= __('资讯标题') ?></th>
                <th width="20%"><?= __('提交时间') ?></th>
                <th width="10%"><?= __('浏览数') ?></th>
                <th width="10%"><?= __('资讯审核状态') ?></th>
                <th width="10%"><?= __('投诉状态') ?></th>
                <th width="30%"><?= __('操作') ?></th>
            </tr>
            <?php
            if ($data['items']) {
                foreach ($data['items'] as $key => $value) {
                    ?>
                    <tr class="row_line">
                        <td class="tl"><?= @$value['title'] ?></td>
                        <td><?= @$value['create_time'] ?></td>
                        <td><?= @$value['number'] ?></td>
                        <td><?= @$value['auditing'] == 1 ? '已通过':($value['auditing'] == 2 ? '未通过':'待审核') ?></td>
                        <td><?= @$value['complaint'] == 1 ? '未投诉':($value['complaint'] == 2 ? '被投诉':'已下架 ') ?></td>
                        <?php if($value['auditing']==1){?>
                           <td>
                             <span class="edit"><a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_User&met=informationnewslist&op=detail&id=<?= $value['id'] ?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?= __('详情') ?></a></span> 
                             <span class="del"><a data-param="{'ctl':'Seller_Promotion_InformationNews','met':'delnews','id':'<?= @$value['id'] ?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?= __('删除') ?></a></span>
                         </td>
                        <?php }elseif($value['auditing']==3){?>
                             <td>
                                 <span class="edit"><a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_User&met=informationnewslist&op=detail&id=<?= $value['id'] ?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?= __('详情') ?></a></span>
                             </td>
                        <?php }else{?>
                            <td>
                                 <span class="edit"><a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_User&met=informationnewslist&op=detail&id=<?= $value['id'] ?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?= __('详情') ?></a></span>
                                 <span class="edit"><a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_User&met=informationnewslist&op=editdetail&id=<?= $value['id'] ?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?= __('编辑') ?></a></span>
                                 <span class="del"><a data-param="{'ctl':'Seller_Promotion_InformationNews','met':'delnews','id':'<?= @$value['id'] ?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?= __('删除') ?></a></span>
                            </td>
                        <?php }?>
                    </tr>
                <?php }
            } else { ?>
                <tr class="row_line">
                    <td colspan="99">
                        <div class="no_account">
                            <img src="<?= $this->view->img ?>/ico_none.png">
                            <p>暂无符合条件的数据记录</p>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="flip page clearfix">
            <p><!--<a href="#" class="page_first">首页</a><a href="#" class="page_prev">上一页</a><a href="#" class="numla cred">1</a><a href="#" class="page_next">下一页</a><a href="#" class="page_last">末页</a>-->
                <?= $page_nav ?>
            </p>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#start_date').datetimepicker({
                controlType: 'select',
                timepicker: false,
                format: 'Y-m-d'
            });
            
            $('#end_date').datetimepicker({
                controlType: 'select',
                timepicker: false,
                format: 'Y-m-d'
            });
            
            
            window.hide_logistic = function (order_id) {
                $("#info_" + order_id).hide();
                $("#info_" + order_id).html("");
            }
            
            window.show_logistic = function (order_id, express_id, shipping_code) {
                $("#info_" + order_id).show();
                $.post(BASE_URL + "/shop/api/logistic.php", {"order_id": order_id, "express_id": express_id, "shipping_code": shipping_code}, function (da) {
                    
                    if (da) {
                        $("#info_" + order_id).html(da);
                    }
                    else {
                        $("#info_" + order_id).html('<div class="error_msg"><?=__('接口出现异常')?></div>');
                    }
                    
                    
                })
            }
        });
    </script>
    
    <!-- 尾部 -->
<?php
include $this->view->getTplPath() . '/' . 'buyer_footer.php';
?>