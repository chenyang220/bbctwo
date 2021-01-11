<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<div class="exchange">
	<div class="search fn-clear">
		<form id="search_form" method="get" action="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=index&typ=e">
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
                            <option value="">全部</option>
                            <option value="4" <?= 4== request_int('state')?'selected':''?> >待审核</option>
                            <option value="<?=Discount_BaseModel::NORMAL?>" <?=Discount_BaseModel::NORMAL == request_int('state')?'selected':''?> ><?=Discount_BaseModel::$state_array_map[Discount_BaseModel::NORMAL]?></option>
                            <option value="<?=Discount_BaseModel::END?>" <?=Discount_BaseModel::END == request_int('state')?'selected':''?>><?=Discount_BaseModel::$state_array_map[Discount_BaseModel::END]?></option>
                            <option value="<?=Discount_BaseModel::CANCEL?>" <?=Discount_BaseModel::CANCEL == request_int('state')?'selected':''?>><?=Discount_BaseModel::$state_array_map[Discount_BaseModel::CANCEL]?></option>
                        </select>
                    </dd>
                </dl>
            </div>
            <div class="control-group">
                <a class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a> 
               <a class="button refresh" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=index&typ=e">重新刷新</a>
            </div>            
    	</form>
		<script type="text/javascript">
    	$(".search").on("click","a.button",function(){
    		$("#search_form").submit();
    	});
    	</script>
	</div>
	<table class="table-list-style" id="table_list" width="100%" cellpadding="0" cellspacing="0">
		<tbody>
			<tr>
				<th class="tl" width="200">活动名称</th>
				<th width="100">开始时间</th>
				<th width="100">结束时间</th>
				<th width="200">秒杀时间段</th>
				<th width="100">申请时间</th>
				<th width="80">状态</th>
				<th width="150">操作</th>
			</tr>
	        <?php
	        if($data['items'])
	        {
	            foreach($data['items'] as $key=>$value)
	            {
	        ?>
        	<tr class="row_line">
	            <td class="tl"><?=$value['seckill_name']?></td>
	            <td><?=$value['seckill_start_time']?></td>
	            <td><?=$value['seckill_end_time']?></td>
	            <td><?=$value['seckill_time_slot']?></td>
	            <td><?=$value['apply_time']?></td>
	            <td><?=$value['seckill_states']?></td>
	            <td>
	            	<?php if($value['seckill_state']==0){ ?>
	            	    <span class="del"><a data-param="{'ctl':'Seller_Promotion_Seckill','met':'revoke','id':'<?=$value['seckill_id']?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?=__('撤销')?></a></span>
	            		<span class="edit">
	                	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=index&op=detail&id=<?=$value['seckill_id']?>&typ=e"><i class="iconfont icon-btnclassify2"></i>查看</a>
	                	</span>
	                	
	                	
	            	<?php } ?>

	            	<?php if($value['seckill_state']==1){ ?>
	            		<span class="edit">
	                	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=index&op=detail&id=<?=$value['seckill_id']?>&typ=e"><i class="iconfont icon-btnclassify2"></i>查看</a>
	                	</span>
	                	<span class="edit del_line"><a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=index&op=manage&typ=e&id=<?=$value['seckill_id']?>"><i class="iconfont icon-setting"></i><?=__('管理')?></a></span>
	                	<!--<span class="edit">-->
	                	<!-- <a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=index&typ=e&op=edit&id=<?=$value['seckill_id']?>"><i class="iconfont icon-zhifutijiao"></i>编辑</a>-->
	                	<!--</span>-->
	            	<?php } ?>

	            	<?php if($value['seckill_state']==2||$value['seckill_state']==3){ ?>
	            		<span class="edit">
	                	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=index&op=detail&id=<?=$value['seckill_id']?>&typ=e"><i class="iconfont icon-btnclassify2"></i>查看</a>
	                	</span>
	                	<span class="del"><a data-param="{'ctl':'Seller_Promotion_Seckill','met':'removeSeckillAct','id':'<?=$value['seckill_id']?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?=__('删除')?></a></span>
	            	<?php } ?>
	               
	                <?php if($value['seckill_state']==4||$value['seckill_state']==5){ ?>
	            			<span class="edit">
	                	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Seckill&met=index&typ=e&op=edit&id=<?=$value['seckill_id']?>"><i class="iconfont icon-zhifutijiao"></i>编辑</a>
	                	</span>
	                	<span class="del"><a data-param="{'ctl':'Seller_Promotion_Seckill','met':'removeSeckillAct','id':'<?=$value['seckill_id']?>'}" href="javascript:void(0)"><i class="iconfont icon-lajitong"></i><?=__('删除')?></a></span>
	            	<?php } ?>
	                 
	                
	               
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
               
        </tbody>
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