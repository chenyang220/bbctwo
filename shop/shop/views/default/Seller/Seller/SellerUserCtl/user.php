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
                        <input type="text" name="user_id" class="text wp100" placeholder="<?=__('请输入会员ID')?>" maxlength="5" oninput="this.value=this.value.replace(/[^0-9]/g,'');" value="<?=request_string('keyword')?>" />
                    </dd>
                </dl>
                <dl>
                    <dt><?=__('会员名称：')?></dt>
                    <dd>
                        <input type="text" name="user_name" class="text wp100" placeholder="<?=__('请输入会员名称')?>" value="<?=request_string('keyword')?>" />
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a>
                <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Seller_SellerUser&met=user&typ=e">重新刷新</a>
                <a class="button export" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Seller_SellerUser&met=getUserExcel"><?=__('导出')?></a>
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
            <th width="20"></th>
			<th width="50"><?=__('会员ID')?></th>
			<th width="100"><?=__('会员名称')?></th>
			<th width="100"><?=__('会员邮箱')?></th>
            <th width="50"><?=__('会员手机号')?></th>
			<th width="50"><?=__('会员性别')?></th>
			<th width="60"><?=__('会员标签')?></th>
            <th width="50"><?=__('真实姓名')?></th>
            <th width="50"><?=__('出生日期')?></th>
            <th width="80"><?=__('注册日期')?></th>
            <th width="80"><?=__('最后登录时间')?></th>
            
		</tr>
       
        <?php
        if($data['items'])
        {
            foreach($data['items'] as $key=>$value)
            {
        ?>
        <tr>
            <td width="20"></td>
            <td><?=$value['user_id']?></td>
            <td><?=$value['user_name']?></td>
            <td><?=$value['user_email']?></td>
            <td><?=$value['user_mobile']?></td>
            <td><?php if($value['user_sex']==1){echo "男";}else{echo "女";}?></td>
            <td><?=$value['user_label_name']?></td>
            <td><?=$value['user_realname']?></td>
            <td><?=$value['user_birthday']?></td>
            <td><?=$value['user_regtime']?></td>
            <td><?=$value['lastlogintime']?></td>
            <td class="review"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Seller_SellerUser&met=editUser&type=e&user_id=<?=$value['user_id']?>"><?=__('编辑')?></a></td>
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



