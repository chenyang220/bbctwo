<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js" charset="utf-8"></script>
<?php if(Web_ConfigModel::value('bargain_status') == 1){ ?>
<div class="exchange">
    <div class="search">
        <form id="search_form" method="get" action="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_Bargain&met=index&typ=e">
            <input type="hidden" name="ctl" value="Seller_Promotion_Bargain">
            <input type="hidden" name="met" value="index">
            <div class="filter-groups">
                <dl>
                    <dt><?= __('活动状态：') ?></dt>
                    <dd>
                        <select class="wp100" name="status">
                            <option value=""><?= __('请选择活动状态') ?></option>
                            <option value="<?= Bargain_BaseModel::WILLON ?>" <?= '0' == request_string('status') ? 'selected' : '' ?>><?= __('未开始') ?></option>
                            <option value="<?= Bargain_BaseModel::ISON ?>" <?= Bargain_BaseModel::ISON == request_string('status') ? 'selected' : '' ?>><?= __('进行中') ?></option>
                            <option value="<?= Bargain_BaseModel::ISOFF ?>" <?= Bargain_BaseModel::ISOFF == request_string('status') ? 'selected' : '' ?>><?= __('活动结束') ?></option>
                            <option value="<?= Bargain_BaseModel::ADMINOFF ?>" <?= Bargain_BaseModel::ADMINOFF == request_string('status') ? 'selected' : '' ?>><?= __('管理员关闭') ?></option>
                            <option value="<?= Bargain_BaseModel::PLATOFF ?>" <?= Bargain_BaseModel::PLATOFF == request_string('status') ? 'selected' : '' ?>><?= __('平台终止') ?></option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt><?= __('商品名称：') ?></dt>
                    <dd>
                        <input type="text" name="keyword" class="text wp100" placeholder="<?= __('请输入活动商品名称') ?>" value="<?= request_string('keyword') ?>"/>
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?= __('筛选') ?></a>
                <a class="button refresh" href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_Bargain&met=index&typ=e"><?= __('重新刷新') ?></a>
            </div>
        </form>
    </div>

    <table class="table-list-style" id="table_list" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <th class="tl" width="100"><?= __('活动商品') ?></th>
            <th width="100"><?= __('开始时间') ?></th>
            <th width="100"><?= __('结束时间') ?></th>
            <th width="80"><?= __('参与人数') ?></th>
            <th width="80"><?= __('购买人数') ?></th>
            <th width="80"><?= __('商品原价/底价') ?></th>
            <th width="80"><?= __('砍价库存') ?></th>
            <th width="80"><?= __('剩余库存') ?></th>
            <th width="80"><?= __('活动状态状态') ?></th>
            <th width="200"><?= __('操作') ?></th>
        </tr>

        <?php
        if ($data['items']) {
            foreach ($data['items'] as $key => $value) {
                ?>
                <tr class="row_line">
                    <td class="tl">
                        <img src="<?= @$value['goods_image'] ?>" alt="">
                        <span class="one-overflow w120 tl"><?= @$value['goods_name'] ?></span>
                    </td>
                    <td><?= @$value['start'] ?></td>
                    <td><?= @$value['end'] ?></td>
                    <td><?= @$value['join_num'] ?></td>
                    <td><?= @$value['buy_num'] ?></td>
                    <td><?= Web_ConfigModel::value('monetary_unit')?><?= @$value['goods_price'] ?>/<?= Web_ConfigModel::value('monetary_unit') ?><?= @$value['bargain_price'] ?></td>
                    <td><?= @$value['bargain_stock_count'] ?></td>
                    <td><?= @$value['bargain_stock'] ?></td>
                    <td><?= @$value['bargain_status_con'] ?></td>
                    <td class="fz0">
                        <span class="edit"><a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_Bargain&met=index&op=edit&id=<?= $value['bargain_id'] ?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?= __('查看') ?></a></span>
                        <span class="edit"><a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_Bargain&met=index&op=detail&id=<?= $value['bargain_id'] ?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?= __('活动数据') ?></a></span>
                        <?php if ($value['bargain_status'] == 1) { ?>
                            <span><a class="stop" data-param="{'ctl':'Seller_Promotion_Bargain','met':'editBargain','id':'<?= @$value['bargain_id'] ?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?= __('终止') ?></a></span>
                        <?php }else{ ?>
                            <span class="del"><a data-param="{'ctl':'Seller_Promotion_Bargain','met':'delBargain','id':'<?= @$value['bargain_id'] ?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?= __('删除') ?></a></span>
                        <?php } ?>
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
<?php }else{ ?>
    <div class="notic_close"> <?= __('平台未开启砍价活动') ?></div>
<?php } ?>
</div>
<script type="text/javascript">
    $(function(){
        $(".search").on("click", "a.button", function () {
            $("#search_form").submit();
        });
        $('.stop').click(function () {
            if ($(this).attr("data-dis")) {
                return 0;
            }
            var data_str = $(this).attr('data-param');
            eval("data_str = " + data_str);
            var id = data_str.id;
            var chk_value = [];//定义一个数组
            chk_value.push(id);
            $.dialog.confirm(__('您确定要终止吗?'), function () {
                $.post(SITE_URL + '?ctl=' + data_str.ctl + '&met=' + data_str.met + '&typ=json', {id: chk_value}, function (data) {
                    if (data && 200 == data.status) {
                        Public.tips.success('终止成功!');
                        location.reload();
                    } else {
                        $.dialog.alert('终止失败');
                    }
                });
            });
        });
    })
</script>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>