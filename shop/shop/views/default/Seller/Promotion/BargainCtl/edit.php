<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<div class="form-style">
    <form method="post" id="form" action="">
        <dl>
            <dt><?= __('参与商品') ?>：</dt>
            <dd>
                <p><span></span></p>
                <table class="table-list-style mb15">
                    <thead>
                    <tr>
                        <th><?= __('商品图片') ?></th>
                        <th><?= __('商品名称') ?></th>
                        <th><?= __('商品规格') ?></th>
                    </tr>
                    </thead>
                    <tbody class="join-act-goods-sku">
                    <tr data-goods-id="<?= @$data['goods_base']['goods_id'] ?>">
                        <td width="50">
                            <div>
                                <div class="pic-thumb">
                                    <img alt="" src="<?= @image_thumb($data['goods_base']['goods_image'], 36, 36) ?>" data-src="<?= @$data['goods_base']['goods_image'] ?>" style="max-width:36px;max-height:36px;border:solid 1px #ccc;"/>
                                </div>
                            </div>
                        </td>
                        <td><a href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= @$data['goods_base']['goods_id'] ?>" target="_blank"> <?= $data['goods_base']['goods_name'] ?> </a></td>
                        <td class="goods-price" width="90">
                            <?php
                            if (is_array($data['goods_base']['spec'])) {
                                foreach ($data['goods_base']['spec'] as $k => $v) {
                                    ?>
                                    <?= $v ?><br/>
                                    <?php
                                }
                            } else {
                                ?>
                                <?= 无 ?>
                            <?php } ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </dd>
        </dl>

        <dl>
            <dt><?= __('活动时间') ?>：</dt>
            <dd>
                <span><?= $data['start_time'] ?><?= __('至') ?><?= $data['end_time'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt><?= __('商品原价') ?>：</dt>
            <dd>
                <span><?= Web_ConfigModel::value('monetary_unit') ?><?= $data['goods_base']['goods_price'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt><?= __('商品底价') ?>：</dt>
            <dd>
                <span><?= Web_ConfigModel::value('monetary_unit') ?><?= $data['bargain_price'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt><?= __('商品库存') ?>：</dt>
            <dd>
                <span><?= $data['goods_base']['goods_stock'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt><?= __('砍价库存') ?>：</dt>
            <dd>
                <span><?= $data['bargain_stock'] ?></span>
            </dd>
        </dl>

        <dl>
            <dt><?= __('砍价规则') ?>：</dt>
            <dd>
                <span>
                    <?php if($data['bargain_type'] == 1){?>
                        <?= __('共') ?><?= $data['bargain_num_price'] ?><?= __('刀砍至底价') ?>
                    <?php }else{ ?>
                        <?= __('每人最多可砍') ?><?= $data['bargain_num_price'] ?><?= __('元') ?>
                    <?php } ?>
                </span>
            </dd>
        </dl>
        <dl>
            <dt><?= __('活动分享描述') ?>：</dt>
            <dd>
                <span><?= $data['bargain_desc'] ?></span>
            </dd>
        </dl>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {

    })

</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>

