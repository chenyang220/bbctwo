<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.dialog.js" charset="utf-8"></script>

<div class="exchange">

	<div class="search">
    	<form id="search_form" method="get" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Discount&met=index&typ=e">
            <input type="hidden" name="ctl" value="<?=request_string('ctl')?>">
            <input type="hidden" name="met" value="<?=request_string('met')?>">
            <input type="hidden" name="typ" value="<?=request_string('typ')?>">
            <div class="filter-groups">
                <dl>
                    <dt><?=__('活动名称：')?></dt>
                    <dd>
                       <input type="text" name="keyword" class="text wp100" placeholder="<?=__('请输入活动名称')?>" value="<?=request_string('keyword')?>" /> 
                    </dd>
                </dl>
                <dl>
                    <dt><?=__('活动状态：')?></dt>
                    <dd>
                        <select name="state" class="wp100">
                            <option value="0">全部</option>
                            <option value="<?=Discount_BaseModel::NORMAL?>" <?=Discount_BaseModel::NORMAL == request_int('state')?'selected':''?> ><?=Discount_BaseModel::$state_array_map[Discount_BaseModel::NORMAL]?></option>
                            <option value="<?=Discount_BaseModel::END?>" <?=Discount_BaseModel::END == request_int('state')?'selected':''?>><?=Discount_BaseModel::$state_array_map[Discount_BaseModel::END]?></option>
                            <option value="<?=Discount_BaseModel::CANCEL?>" <?=Discount_BaseModel::CANCEL == request_int('state')?'selected':''?>><?=Discount_BaseModel::$state_array_map[Discount_BaseModel::CANCEL]?></option>
                        </select>
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a> 
               <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Discount&met=index&typ=e">重新刷新</a>
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
			<th class="tl" width="200"><?=__('活动名称')?></th>
			<th width="120"><?=__('开始时间')?></th>
			<th width="120"><?=__('结束时间')?></th>
			<th width="50"><?=__('购买下限')?></th>
			<th width="50"><?=__('状态')?></th>
			<th width="120"><?=__('操作')?></th>
		</tr>
        <?php
        if($data['items'])
        {
            foreach($data['items'] as $key=>$value)
            {
        ?>
        <tr class="row_line">
            <td class="tl"><?=$value['discount_name']?></td>
            <td><?=$value['discount_start_time']?></td>
            <td><?=$value['discount_end_time']?></td>
            <td><?=$value['discount_lower_limit']?></td>
            <td><?=$value['discount_state_label']?></td>
            <td class="nscs-table-handle">
                <?php if($value['discount_state_label'] !="已结束"){?>
                <span class="edit"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Discount&met=add&op=edit&typ=e&id=<?=$value['discount_id']?>"><i class="iconfont icon-zhifutijiao"></i><?=__('编辑')?></a></span>
                <span class="edit del_line"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Discount&met=index&op=manage&typ=e&id=<?=$value['discount_id']?>"><i class="iconfont icon-setting"></i><?=__('管理')?></a></span>
                <?php } ?>
                <span class="del"><a data-param="{'ctl':'Seller_Promotion_Discount','met':'removeDiscountAct','id':'<?=$value['discount_id']?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?=__('删除')?></a></span>
            </td>
        </tr>
        <?php
            }
        }
        else
        {
        ?>
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



