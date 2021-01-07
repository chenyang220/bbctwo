<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<a class="bbc_seller_btns button add button_blue" href="<?= Yf_Registry::get('index_page') ?>?ctl=Seller_Seller_SellerWx&met=wxCallback&typ=e&action=add"><i class="iconfont icon-jia"></i><?= __('添加自动回复') ?></a>
    <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <th width="200"><?=__('关键词')?></th>
        <th width="100"><?=__('匹配类型')?></th>
        <th width="150"><?=__('消息类型')?></th>
        <th width="120"><?=__('回复内容')?></th>
        <th width="120"><?=__('操作')?></th>
    </tr>
    <?php if($data){
        foreach ($data as $key => $val) {?>
    <tr>
        <td><?=($val['words'])?></td>
        <?php if($val['match_type']==1){?>
        	<td>精准匹配</td>
        <?php }else{?>
        	<td>模糊匹配</td>
        <?php }?>
        <?php if($val['msg_type']==1){?>	
        <td>文本消息</td>
    	<?php }?>
        <td><?=($val['content'])?></td>
        <td class="operate">
        <span>
        <a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=wxCallback&typ=e&action=edit&id=<?=($val['wxpublic_message_id'])?>"><i class="iconfont icon-chakan"></i><?=__('编辑')?></a>
        <a href="javascript:void(0);" onclick="remove(<?=($val['wxpublic_message_id'])?>)"><i class="iconfont icon-chakan"></i><?=__('删除')?></a>
        </span>
        </td>
    </tr>
    <?php }}else{?>
    <tr>
        <td colspan="99">
         <div class="no_account">
            <img src="<?= $this->view->img ?>/ico_none.png"/>
            <p><?=__('暂无符合条件的数据记录')?></p>
        </div>
        </td>
    </tr>
    <?php }?>
    <tr>
        <td colspan="99">
            <p class="page"><?=$page_nav?></p>
        </td>
    </tr>
</table>
<script type="text/javascript">
    var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=removeSellerWxMsg&typ=json';
    function remove(a){
        parent.$.dialog.confirm("确定删除？", function () {
            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: {wxpublic_message_id:a},
                success: function (e) {
                    if (e.status == 200) {
                        Public.tips({type: 3,content: "删除成功！"});
                        window.location.href="index.php?ctl=Seller_Seller_SellerWx&met=wxCallback&typ=e&";
                    } else {
                        Public.tips({type: 1, content: e.msg});
                    }
                }
            });
        });
    }
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>