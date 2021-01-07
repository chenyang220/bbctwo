<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<a class="bbc_seller_btns button add button_blue" href="<?= Yf_Registry::get('index_page') ?>?ctl=Seller_Seller_SellerWx&met=wxMenu&typ=e&action=add"><i class="iconfont icon-jia"></i><?= __('新增微信公众号菜单') ?></a>
<a class="bbc_seller_btns button button_blue synchronize" href="javascript:void(0);"><?= __('同步到微信公众号') ?></a>
    <table class="table-list-style" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <th width="200"><?=__('菜单名称')?></th>
        <th width="100"><?=__('菜单类型')?></th>
        <th width="150"><?=__('父级菜单')?></th>
        <th width="120"><?=__('菜单顺序')?></th>
        <th width="120"><?=__('菜单跳转网址')?></th>
        <th width="120"><?=__('发送消息')?></th>
        <th width="120"><?=__('操作')?></th>
    </tr>
    <?php if($data){
        foreach ($data as $key => $val) {?>
    <tr>
        <td><?=($val['menu_name'])?></td>
        <?php if($val['menu_type']==1){?>
        	<td>发送消息</td>
        <?php }else{?>
        	<td>跳转网页</td>
        <?php }?>	
        <td><?=($val['parent_menu_name'])?></td>
        <td><?=($val['sort_num'])?></td>
        <td><?=($val['menu_url'])?></td>
        <td><?=($val['menu_msg'])?></td>
        <td class="operate">
        <span>
        <a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=wxMenu&typ=e&action=edit&id=<?=($val['wxpublic_menu_id'])?>"><i class="iconfont icon-chakan"></i><?=__('编辑')?></a>
        <a href="javascript:void(0);" onclick="remove(<?=($val['wxpublic_menu_id'])?>)"><i class="iconfont icon-chakan"></i><?=__('删除')?></a>
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
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
<script type="text/javascript">
 	var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=removeSellerWxMenu&typ=json';
 	function remove(a){
 		parent.$.dialog.confirm("确定删除？", function () {
	 		$.ajax({
	            type: 'POST',
	            url: ajax_url,
	            data: {wxpublic_menu_id:a},
	            success: function (e) {
	                if (e.status == 200) {
	                	Public.tips({type: 3,content: "删除成功！"});
	                    window.location.href="index.php?ctl=Seller_Seller_SellerWx&met=wxMenu&typ=e&";
	                } else {
	                    Public.tips({type: 1, content: e.msg});
	                }
	            }
	        });
 		});
 	}
 	$(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url') ?>?ctl=Seller_Seller_SellerWx&met=wxPublicCreateMenu&typ=json';
        $(".synchronize").click(function () {
            $.ajax({
                type: 'POST',
                url: ajax_url,
                success: function (a) {
                    if (a.status == 200) {
                        Public.tips({type: 3,content: "成功！"});
                        setTimeout(function(){
                          window.location.href="index.php?ctl=Seller_Seller_SellerWx&met=wxMenu&typ=e&";
                        },3000);
                    } else {
                        Public.tips({type: 1, content: a.msg});
                    }
                }
            });
        });
    });
</script>