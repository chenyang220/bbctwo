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
                    <dt><?=__('团购类型：')?></dt>
                    <dd>
                        <select name="type" class="wp100">
                            <option value=""><?=__('请选择团购类型')?></option>
                            <option><?=__('全部')?></option>
                            <option value="<?=GroupBuy_BaseModel::ONLINEGBY?>" <?=GroupBuy_BaseModel::ONLINEGBY == request_int('type')?'selected':''?>><?=__('线上团')?></option>
                            <option value="<?=GroupBuy_BaseModel::VIRGBY?>"  <?=GroupBuy_BaseModel::VIRGBY == request_int('type')?'selected':''?>><?=__('虚拟团')?></option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt><?=__('活动状态：')?></dt>
                    <dd>
                        <select class="wp100" name="state">
                            <option value=""><?=__('请选择活动状态')?></option>
                            <option value="0"><?=__('全部')?></option>
                            <option value="<?=GroupBuy_BaseModel::UNDERREVIEW?>" <?=GroupBuy_BaseModel::UNDERREVIEW == request_int('state')?'selected':''?>><?=__('审核中')?></option>
                            <option value="<?=GroupBuy_BaseModel::NORMAL?>" <?=GroupBuy_BaseModel::NORMAL == request_int('state')?'selected':''?>><?=__('正常')?></option>
                            <option value="<?=GroupBuy_BaseModel::FINISHED?>" <?=GroupBuy_BaseModel::FINISHED == request_int('state')?'selected':''?>><?=__('已结束')?></option>
                            <option value="<?=GroupBuy_BaseModel::AUDITFAILUER?>" <?=GroupBuy_BaseModel::AUDITFAILUER == request_int('state')?'selected':''?>><?=__('审核失败')?></option>
                            <option value="<?=GroupBuy_BaseModel::CLOSED?>" <?=GroupBuy_BaseModel::CLOSED == request_int('state')?'selected':''?>><?=__('管理员关闭')?></option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt><?=__('团购名称：')?></dt>
                    <dd>
                        <input type="text" name="keyword" class="text wp100" placeholder="<?=__('请输入团购名称')?>" value="<?=request_string('keyword')?>" />
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a>
                <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_GroupBuy&met=index&typ=e">重新刷新</a>
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
            <th width="50"></td>
			<th class="tl" width="300"><?=__('团购名称')?></th>
			<th width="120"><?=__('开始时间')?></th>
			<th width="120"><?=__('结束时间')?></th>
            <th width="50"><?=__('浏览数')?></th>
			<th width="50"><?=__('已购买')?></th>
			<th width="60"><?=__('活动状态')?></th>
		</tr>
        <?php
        if($data['items'])
        {
            foreach($data['items'] as $key=>$value)
            {
        ?>
        <tr>
            <td width="50"><a href="<?=Yf_Registry::get('url')?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?=$value['goods_id']?>&typ=e" target="_blank"><img src="<?=image_thumb($value['groupbuy_image'],30,30)?>" width="30" height="30"></a></td>
            <td class="tl"><a href="<?=Yf_Registry::get('url')?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?=$value['goods_id']?>&typ=e" target="_blank"><?=$value['groupbuy_name']?></a></td>
            <td><?=$value['groupbuy_starttime']?></td>
            <td><?=$value['groupbuy_endtime']?></td>
            <td><?=$value['groupbuy_views']?></td>
            <td><?=$value['groupbuy_buy_quantity']?></td>
            <td><?=$value['groupbuy_state_label']?></td>
            <?php if($value['groupbuy_state'] == 4){  ?>
            <td class="review"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_GroupBuy&met=<?php if($value['groupbuy_type'] == 2){echo 'addVr';}else{echo 'add';}?>&typ=e&groupid=<?=$value['groupbuy_id']?>"><?=__('编辑')?></a></td>
            <?php } elseif ($value['groupbuy_state'] == 3) {?>
            <td class="review"><a href="javascript:void(0)" onclick="del(<?=$value['groupbuy_id']?>)"><?=__('删除')?></a></td>
            <?php } ?>
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


    function del ($groupbuy_id) {
            $.ajax({
               url:SITE_URL  + '?ctl=Seller_Promotion_GroupBuy&met=delGroupBuy&typ=json',
               data:{groupbuy_id:$groupbuy_id},
               dataType: "json",
               contentType: "application/json;charset=utf-8",
               success:function(result){
                if (result.status == 200) {
                    window.location.reload();
                } else {

                }
               }
            });
    }
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>



