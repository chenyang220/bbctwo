<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
    <div class="exchange">
        <div class="search">
            <form method="get" id="search_form" action="index.php">
                <input type="hidden" name="ctl" value="<?= $_GET['ctl'] ?>"> <input type="hidden" name="met" value="<?= $_GET['met'] ?>"> <input type="hidden" name="typ" value="e">
                <div class="filter-groups">
                   <dl>
                        <dt><?= __('创建时间：') ?></dt>
                        <dd style="width: 250px;">
                            <input type="text" autocomplete="off" name="start_date" id="start_date" class="text w85" value="<?= request_string('start_date') ?>" placeholder="<?= __('开始时间') ?>"/><em class="add-on"><i class="iconfont icon-rili"></i></em>
                            <span class="rili_ge">–</span>
                            <input type="text" autocomplete="off" name="end_date" id="end_date" class="text w85" value="<?= request_string('end_date') ?>" placeholder="<?= __('结束时间') ?>"/><em class="add-on"><i class="iconfont icon-rili"></i></em> 
                        </dd>
                    </dl>
                    <dl>
                         <dt><?= __('状态：') ?></dt>
                         <dd>
                            <select class="wp100" name="state">
                                <option value=""><?= __('请选择') ?></option>
                                、
                                <option value="1" <?= request_int('state') == 1 ? 'selected':'' ?> ><?= __('通过') ?></option>
                                <option value="2" <?= request_int('state') == 2 ? 'selected':'' ?> ><?= __('未审核') ?></option>
                                <option value="3" <?= request_int('state') == 3 ? 'selected':'' ?> ><?= __('未通过') ?></option>
                            </select> 
                         </dd>
                     </dl> 
                </div>
                <div class="control-group">
                    <a class="button btn_search_goods" href="javascript:void(0);"><?= __('筛选') ?></a>
                    <a class="button refresh" href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Supplier_Supplier&met=index&typ=e"><?= __('重新刷新') ?></a>
                </div>
                <script type="text/javascript">
                    $("a.btn_search_goods").on("click", function () {
                        $("#search_form").submit();
                    });
                </script>
            </form>
        </div>
        
        <table class="table-list-style table-promotion-list" id="table_list" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <th class="tc" width="150"><?= __('用户名') ?></th>
                <th width="100"><?= __('联系方式') ?></th>
                <th width="200"><?= __('供货商名称') ?></th>
                <th width="150"><?= __('状态') ?></th>
                <th width="200"><?= __('创建时间') ?></th>
                <th width="150"><?= __('操作') ?></th>
            </tr>
            <?php
            if ($data['items']) {
                foreach ($data['items'] as $key => $val) {
                    ?>
                    <tr class="row_line">
                        <td>
                            <?= ($val['user_name']) ?>
                        </td>
                        <td><?= ($val['mobile']) ?></td>
                        <td><?= ($val['shop_name']) ?></td>
                        <td><?php if ($val['distributor_enable'] == 1) {
                                echo __('已通过');
                            } elseif ($val['distributor_enable'] == 0) {
                                echo __('待审核');
                            } elseif ($val['distributor_enable'] == -1) {
                                echo __('未通过');
                            } ?></td>
                        <td><?= ($val['shop_distributor_time']) ?></td>
                        <td class="nscs-table-handle">
                            <span class="edit">
                                <a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Supplier_Supplier&met=apply&shop_distributor_id=<?= $val['shop_distributor_id'] ?>&act=edit&typ=e">
                                    <i class="iconfont icon-zhifutijiao"></i>
                                    <?= __('编辑') ?>
                                </a>
                            </span>
                            <span style="border-left: solid 1px #E6E6E6" class="del">
<!--                                <a href="javascript:void(0);" data-id='--><?//= $val['shop_distributor_id'] ?><!--' data-type="del" class="audit">-->
                                     <a data-param="{'ctl':'Seller_Supplier_Supplier','met':'del_supplier','id':'<?= $val['shop_distributor_id'] ?>'}" href="javascript:void(0)">
                                        <i class="iconfont icon-lajitong"></i>
                                        <?= __('删除') ?>
                                    </a>
                            </span>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr class="row_line">
                    <td colspan="99">
                        <div class="no_account">
                            <img src="<?= $this->view->img ?>/ico_none.png">
                            <p><?= __('暂无符合条件的数据记录') ?></p>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php if ($page_nav) { ?>
            <div class="mm">
                <div class="page"><?= $page_nav ?></div>
            </div>
        <?php } ?>
    </div>
    
    <link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js" charset="utf-8"></script>
    
    <script>
        $(document).ready(function () {
            var ajax_url = "./index.php?ctl=Seller_Supplier_Supplier&met=edit_statu&typ=json";
            $(".audit").click(function () {
                var shop_distributor_id = $(this).attr("data-id");
                var act = $(this).attr("data-type");
                $.ajax({
                    url: ajax_url,
                    data: {shop_distributor_id: shop_distributor_id, act: act},
                    success: function (a) {
                        if (a.status == 200) {
                            Public.tips.success("<?=__('操作成功！')?>");
                            location.reload();
                        }
                        else {
                            Public.tips.error("<?=__('操作失败')?>");
                        }
                    }
                });
            });
            $("#start_date").datetimepicker({
                controlType: "select",
                timepicker: false,
                format: "Y-m-d"
            });
            
            $("#end_date").datetimepicker({
                controlType: "select",
                timepicker: false,
                format: "Y-m-d"
            });
        });
    </script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>