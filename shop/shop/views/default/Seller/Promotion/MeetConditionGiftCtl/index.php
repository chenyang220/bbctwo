<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.dialog.js"></script>

<div class="exchange">
	<div class="search">
    	<form id="search_form" method="get" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_MeetConditionGift&met=index&typ=e">
            <input type="hidden" name="ctl" value="Seller_Promotion_MeetConditionGift">
            <input type="hidden" name="met" value="index">
            <input type="hidden" name="typ" value="e">
            <div class="filter-groups">
                <dl>
                    <dt><?=__('活动名称：')?></dt>
                    <dd><input type="text" name="keyword" class="text wp100" placeholder="<?=__('请输入活动名称')?>" value="<?=request_string('keyword')?>" /></dd>
                </dl>
                <dl>
                    <dt><?=__('活动状态：')?></dt>
                    <dd>
                       <select name="state" class="wp100">
                            <option value="0"><?=__('全部活动')?></option>
                            <option value="<?=ManSong_BaseModel::NORMAL?>"><?=ManSong_BaseModel::$manSongStateMap[ManSong_BaseModel::NORMAL]?></option>
                            <option value="<?=ManSong_BaseModel::END?>"><?=ManSong_BaseModel::$manSongStateMap[ManSong_BaseModel::END]?></option>
                            <option value="<?=ManSong_BaseModel::CANCEL?>"><?=ManSong_BaseModel::$manSongStateMap[ManSong_BaseModel::CANCEL]?></option>
                        </select> 
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a>
                <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_MeetConditionGift&met=index&typ=e">重新刷新</a>
            </div>
    	</form>
	<script type="text/javascript">
	$(".search").on("click","a.button",function(){
		$("#search_form").submit();
	});
	</script>
	</div>

	<table class="table-list-style table-promotion-list" id="table_list" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<th class="tl" width="250"><?=__('活动名称')?></th>
			<th width="100"><?=__('开始时间')?></th>
			<th width="100"><?=__('结束时间')?></th>
			<th width="60"><?=__('状态')?></th>
			<th width="80"><?=__('操作')?></th>
		</tr>
        <?php
        if($data['items'])
        {
            foreach(@$data['items'] as $key=>$value)
            {
        ?>
        <tr class="line_row">
            <td class="tl"><?=@$value['mansong_name']?></td>
            <td><?=@$value['mansong_start_time']?></td>
            <td><?=@$value['mansong_end_time']?></td>
            <td><?=@$value['mansong_state_label']?></td>
            <td>
                <span class="edit"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_MeetConditionGift&met=index&op=detail&typ=e&id=<?=@$value['mansong_id']?>"><i class="iconfont icon-btnclassify2"></i><?=__('详情')?></a></span>
                <span class="del"><a data-param="{'ctl':'Seller_Promotion_MeetConditionGift','met':'removeManSong','id':'<?=@$value['mansong_id']?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?=__('删除')?></a></span>
            </td>
        </tr>
        <?php
            }
        }else{ ?>
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
    <?php } ?>
</div>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>



