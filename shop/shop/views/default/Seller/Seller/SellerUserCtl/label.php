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
                    <dt><?=__('标签ID：')?></dt>
                    <dd>
                        <input type="text" name="label_id" class="text wp100" placeholder="<?=__('请输入标签ID')?>" value="" />
                    </dd>
                </dl>

                 <dl>
                    <dt><?=__('标签名称：')?></dt>
                    <dd>
                        <input type="text" name="label_name" class="text wp100" placeholder="<?=__('请输入标签名称')?>" value="" />
                    </dd>
                </dl>
            </div>
           
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a>
                <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Seller_SellerUser&met=label&typ=e">重新刷新</a>
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
			<th width="50"><?=__('标签ID')?></th>
			<th width="120"><?=__('标签名称')?></th>
			<th width="120"><?=__('标签排序')?></th>
            <th width="50"><?=__('标签图片')?></th>
			<th width="50"><?=__('标签描述')?></th>
			<th width="60"><?=__('操作')?></th>
		</tr>
        <?php
        if($data['items'])
        {
            foreach($data['items'] as $key=>$value)
            {
        ?>
        <tr>
            <td width="50"></td>
            <td><?=$value['label_id']?></td>
            <td><?=$value['label_name']?></td>
            <td><?=$value['label_sort']?></td>
            <td><img width="20%" src="<?=$value['label_img']?>"></td>
            <td><?=$value['label_desc']?></td>
            <td class="review"><a id="edit" label-id="<?=$value['label_id']?>" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Seller_SellerUser&met=editLabel&typ=e&label_id=<?=$value['label_id']?>"><?=__('编辑')?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a id="remove" label-id="<?=$value['label_id']?>" href="javascript:void(0);"><?=__('删除')?></a></td>
          
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
    $('#remove').click(function(){
         var label_id = $('#remove').attr('label-id');
         $.ajax({

            url: "index.php?ctl=Seller_Seller_SellerUser&met=removeLabel&typ=json",
            data: {label_id:label_id},
            type: "POST",
            success:function(e){
                if(e.status == 200)
                {
                    flag = false;
                    Public.tips.success('删除成功!');
                    location.href="index.php?ctl=Seller_Seller_SellerUser&met=label&typ=e"; //成功后跳转
                }
                else
                {
                    Public.tips.error('删除失败');
                }
                me.holdSubmit(false);
            }
        });
    })
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>



