<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.dialog.js" charset="utf-8"></script>

<div class="exchange">
	<div class="search">
    	<form id="search_form" method="get" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_InformationNews&met=index&typ=e">
            <input type="hidden" name="ctl" value="Seller_Promotion_InformationNews">
            <input type="hidden" name="met" value="index">
            <div class="filter-groups">
                <dl>
                    <dt><?=__('资讯状态：')?></dt>
                    <dd>
                         <select class="wp100" name="auditing">
                            <option value=""><?= __('请选择资讯状态') ?></option>
                            <option value="1" <?= request_string('auditing') == '1'? 'selected':'' ?>><?= __('已通过') ?></option>
                            <option value="2" <?= request_string('auditing') == '2'? 'selected':'' ?>><?= __('未通过') ?></option>
                            <option value="3" <?= request_string('auditing') == '3'? 'selected':'' ?>><?= __('待审核') ?></option>
                        </select>
                    </dd>
                </dl>
                <dl>
                    <dt><?=__('投诉状态：')?></dt>
                    <dd>
                       <select class="wp100" name="complaint">
                            <option value=""><?= __('请选择投诉状态') ?></option>
                            <option value="1" <?= request_string('complaint') =='1'? 'selected':'' ?>><?= __('未投诉') ?></option>
                            <option value="2" <?= request_string('complaint') =='2'? 'selected':'' ?>><?= __('被投诉') ?></option>
                            <option value="3" <?= request_string('complaint') =='3'? 'selected':'' ?>><?= __('已下架') ?></option>
                        </select> 
                    </dd>
                </dl>
                <dl>
                    <dt><?=__('资讯标题：')?></dt>
                    <dd>
                        <input type="text" name="keyword" class="text wp100" placeholder="<?=__('请输入资讯标题')?>" value="<?=request_string('keyword')?>" />
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a>
                <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_InformationNews&met=index&typ=e"><?=__('重新刷新')?></a>
            </div>

    	</form>
	<script type="text/javascript">
	$(".search").on("click","a.button",function(){
		$("#search_form").submit();
	});
	</script>
	</div>

	<table class="table-list-style" id="table_list"  cellpadding="0" cellspacing="0">
		<tr>
			<th class="tl" width="30%"><?=__('资讯标题')?></th>
			<th width="20%"><?=__('提交时间')?></th>
			<th width="10%"><?=__('浏览数')?></th>
			<th width="10%"><?=__('资讯审核状态')?></th>
            <th width="10%"><?= __('投诉状态') ?></th>
			<th width="30%"><?=__('操作')?></th>
		</tr>
        <?php
        if($data['items'])
        {
            foreach($data['items'] as $key=>$value)
            {
        ?>
        <tr class="row_line">
            <td class="tl"><?=@$value['title']?></td>
            <td><?=@$value['create_time']?></td>
            <td><?=@$value['number']?></td>
            <td><?=@$value['auditing'] == 1?'已通过':($value['auditing']==2?'未通过':'待审核') ?></td>
            <td><?= @$value['complaint'] == 1 ? '未投诉':($value['complaint'] == 2 ? '被投诉':'已下架 ') ?></td>
            <?php if($value['auditing']==1){?>
                <td>
                    <span class="edit"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_InformationNews&met=index&op=detail&id=<?=$value['id']?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?=__('详情')?></a></span>
                    <span class="del"><a data-param="{'ctl':'Seller_Promotion_InformationNews','met':'delnews','id':'<?=@$value['id']?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?=__('删除')?></a></span>
                    
                </td>
            <?php }elseif($value['auditing']==3){?>
                 <td>
                    <span class="edit"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_InformationNews&met=index&op=detail&id=<?=$value['id']?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?=__('详情')?></a></span>                  
                </td>
            <?php }else{?>
                 <td>
                    <span class="edit"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_InformationNews&met=index&op=detail&id=<?=$value['id']?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?=__('详情')?></a></span>
                    <span class="edit"><a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_InformationNews&met=index&op=editdetail&id=<?= $value['id'] ?>&typ=e"><i class="iconfont icon-btnclassify2"></i><?= __('编辑') ?></a></span>
                    <span class="del"><a data-param="{'ctl':'Seller_Promotion_InformationNews','met':'delnews','id':'<?=@$value['id']?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?=__('删除')?></a></span>
                    
                </td>
            <?php }?>
        </tr>
        <?php }  }else{ ?>
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

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>



