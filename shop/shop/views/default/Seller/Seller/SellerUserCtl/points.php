<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<div class="exchange">
    <div class="search">
       <form id="search_form" method="get" action="<?=Yf_Registry::get('url')?>">
            <input type="hidden" name="ctl" value="<?=request_string('ctl')?>">
            <input type="hidden" name="met" value="<?=request_string('met')?>">
            <input type="hidden" name="typ" value="<?=request_string('typ')?>">
            <div class="filter-groups">
               
                <dl>
                    <dt><?=__('会员ID：')?></dt>
                    <dd>
                        <input type="text" name="user_id" class="text wp100" maxlength="5" oninput="this.value=this.value.replace(/[^0-9]/g,'');" placeholder="<?=__('请输入会员ID')?>" value="" />
                    </dd>
                </dl>
                 <dl>
                    <dt><?=__('会员名称：')?></dt>
                    <dd>
                        <input type="text" name="user_name" class="text wp100" placeholder="<?=__('请输入会员名称')?>" value="" />
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a>
                <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Seller_SellerUser&met=points&typ=e">重新刷新</a>
            </div>
       </form>
    </div>
	<script type="text/javascript">
	$(".search").on("click","a.button",function(){
		$("#search_form").submit();
	});
	</script>

	<table class="table-list-style table-promotion-list" width="100%" cellpadding="0" cellspacing="0">
		<tr id="title_tab">
            <th width="50"></th>
			<th width="120"><?=__('序号')?></th>
			<th width="120"><?=__('会员ID')?></th>
			<th width="120"><?=__('会员名称')?></th>
            <th width="50"><?=__('积分')?></th>
			<th width="50"><?=__('操作时间')?></th>
			<th width="60"><?=__('操作描述')?></th>
            <th width="60"><?=__('管理员名称')?></th>
		</tr>
        <?php
        if($data['items'])
        {
            foreach($data['items'] as $key=>$value)
            {
        ?>
        <tr>
            <td></td>                  
            <td><?=$value['points_log_id']?></td>
            <td><?=$value['user_id']?></td>
            <td><?=$value['user_name']?></td>
            <td><?=$value['points_log_points']?></td>
            <td><?=$value['points_log_time']?></td>
            <td><?=$value['points_log_desc']?></td>
            <td><?=$value['admin_name']?></td>
        </tr>
        <?php } }else{ ?>
            <tr class="row_line">
                <td colspan="99">
                    <div class="no_account">
                        <img src="<?=$this->view->img?>/ico_none.png">
                        <p>暂无符合条件的数据记录</p>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php if($page_nav){ ?>
        <div class="mm">
            <div class="page"><?=$page_nav?></div>
        </div>
    <?php }?>
</div>
<script>
    if($('td').hasClass('review'))
    {
        var html = '<th width="25"><?=__('操作')?></th>';
        $('#title_tab').append(html);
    }
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>



