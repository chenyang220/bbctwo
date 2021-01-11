<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
    include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<div class="exchange">
    <div class="search">

        <form id="search_form" method="get" action="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_Increase&met=index&typ=e">
            <input type="hidden" name="ctl" value="Seller_Promotion_Increase">
            <input type="hidden" name="met" value="index">
            <div class="filter-groups">
                <dl>
                    <dt><?= __('活动名称：') ?></dt>
                    <dd><input type="text" name="keyword" class="text wp100" placeholder="<?= __('请输入活动名称') ?>" value="<?= request_string('keyword') ?>" /></dd>
                </dl>
                <dl>
                    <dt><?= __('活动状态：') ?></dt>
                    <dd>
                        <select name="state" class="wp100">
                            <option value=""><?= __('请选择活动状态') ?></option>
                            <option value="0" <?= request_int('state') == 0 ? 'selected' : '' ?>><?= __('全部') ?></option>
                            <option value="<?= Increase_BaseModel::NORMAL ?>" <?= Increase_BaseModel::NORMAL == request_int('state') ? 'selected' : '' ?>><?= __('正常') ?></option>
                            <option value="<?= Increase_BaseModel::FINISHED ?>" <?= Increase_BaseModel::FINISHED == request_int('state') ? 'selected' : '' ?>><?= __('已结束') ?></option>
                            <option value="<?= Increase_BaseModel::CLOSED ?>" <?= Increase_BaseModel::CLOSED == request_int('state') ? 'selected' : '' ?>><?= __('管理员关闭') ?></option>
                        </select>
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?= __('筛选') ?></a>
                <a class="button refresh" href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_Increase&met=index&typ=e">重新刷新</a>
            </div>
        </form>
        <script type="text/javascript">
            $(".search").on("click", "a.button", function () {
                $("#search_form").submit();
            });
        </script>
    </div>
    
    <table class="table-list-style" id="table_list" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <th class="tl" width="200"><?= __('活动名称') ?></th>
            <th width="100"><?= __('开始时间') ?></th>
            <th width="100"><?= __('结束时间') ?></th>
            <th width="80"><?= __('状态') ?></th>
            <th width="70"><?= __('操作') ?></th>
        </tr>
        <?php if ($data['items']) {
                foreach ($data['items'] as $key => $value) {
        ?>
                    <tr class="row_line">
                        <td class="tl"><?= $value['increase_name'] ?></td>
                        <td><?= $value['increase_start_time'] ?></td>
                        <td><?= $value['increase_end_time'] ?></td>
                        <td><?= $value['increase_state_label'] ?></td>
                        <td>
                            <?php if($value['increase_state_label'] != "已结束") {?>
                            <span>
                                <a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_Increase&met=index&typ=e&op=edit&id=<?= $value['increase_id'] ?>">
                                    <i class="iconfont icon-zhifutijiao"></i>
                                    <?= __('编辑') ?>
                                </a>
                            </span>
                            <?php }?>
                            <span>
                                <a onclick="delIncrease('<?= $value['id'] ?>')" href="javascript:void(0)">
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
                            <p><?= __('暂无符合条件的数据记录')?></p>
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
<script>
    window.delIncrease = function (e) {
        $.dialog({
            title: __('系统提示'),
            content: __('您确定要删除吗？'),
            icon: 'confirm.gif',
            height: 100,
            width: 210,
            lock: true,
            drag: false,
            ok: function () {
                $.post(SITE_URL + '?ctl=Seller_Promotion_Increase&met=removeIncreaseAct&typ=json', {id: e}, function (data) {
                    if (data && 200 == data.status) {
                        Public.tips.success(__('删除成功！'));
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500)
                    } else {
                        Public.tips.error(__('删除失败！'));
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500)
                    }
                })
            }
        })
    }
</script>
<?php
    include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>



