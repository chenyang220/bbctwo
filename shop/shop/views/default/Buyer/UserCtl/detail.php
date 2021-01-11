<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
include $this->view->getTplPath() . '/' . 'buyer_header.php';
?>

<div class="form-style">
    <?php if ($data['auditing'] == 1 || $data['auditing'] == 2) { ?>
        <div>
        <span class="del" style="float: right">
            <a class=" button button_red bbc_seller_submit_btns" id="delnews" data-id="<?= $data['id'] ?>"/><i class="iconfont icon-lajitong"></i><?= __('删除') ?></a>
        </span>
        </div>
    <?php } ?>
    <form method="post" id="form" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_PinTuan&met=add&typ=e">
        <dl>
            <dt><?= __('审核状态') ?>：</dt>
            <dd>
                <span><?= @$data['auditing'] == 1 ? '已通过':($data['auditing'] == 2 ? '未通过':'待审核') ?></span>
            </dd>
        </dl>
        <dl>
            <dt><?= __('审核时间') ?>：</dt>
            <dd>
                <span><?php if($data['auditing'] == 1|| $data['auditing'] == 2) echo $data['create_time']; ?></span>
            </dd>
        </dl>
        <dl>
            <dt><?=__('咨询标签')?>：</dt>
            <dd>
                <span><?=$data['newscalss']?></span>
            </dd>
        </dl>
        <dl>
            <dt><?= __('咨询标题') ?>：</dt>
            <dd>
                <span><?= $data['title'] ?></span>
            </dd>
        </dl>
        <dl>
            <dt><?= __('咨询副标题') ?>：</dt>
            <dd>
                <span><?= $data['subtitle'] ?></span>
            </dd>
        </dl>
        
        <dl>
            <dt><?=__('资讯内容')?>：</dt>
            <dd>
                <?= $data['content'] ?>
            
            </dd>
        </dl>
    </form>
</div>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
    
    })

</script>

<script type="text/javascript">
    $('#delnews').click(function () {
        var newsid = $('#delnews').data('id');
        
        $.dialog({
            title: '删除',
            content: '您确定要删除吗？',
            height: 100,
            width: 410,
            lock: true,
            drag: false,
            ok: function () {
                $.post(SITE_URL + '?ctl=Seller_Promotion_InformationNews&met=delnews&typ=json', {id: newsid}, function (data) {
                        console.info(data);
                        if (data && 200 == data.status) {
                            ;
                            Public.tips.success('删除成功!');
                            var dest_url = "index.php?ctl=Seller_Promotion_InformationNews&met=index&typ=e";//成功后跳转
                            setTimeout(window.location.href = dest_url, 5000);
                        } else {
                            Public.tips.error('删除失败!');
                        }
                    }
                );
            }
        })
    })
</script>




