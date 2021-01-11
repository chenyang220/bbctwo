<?php if (!defined('ROOT_PATH')){exit('No Permission');}

include $this->view->getTplPath() . '/' . 'buyer_header.php';
?>
</div>

          </ul>
        <div class="tracks-operate">
            <p class="iblock relative">
                <label class="mr30"><input class="mr4 js-selAll" type="checkbox"><?= __('全选'); ?></label>
                <a href="javascript:void(0)" data-param="{'ctl':'Buyer_Favorites','met':'delFootPrint','time':'<?=$key?>'}" class="delete" title="<?=__('删除')?>"><i class="icon-trash iconfont icon-lajitong mar0 fz22 align-middle"></i><?=__('删除')?></a>
                <!-- 点击删除时，如未选中任何一个，则显示以下提示，添加active -->
                <span class="sel-no-tips"><i class="iconfont icon-Prompt"></i><b><?= __('忘记勾选商品？'); ?></b></span>
            </p>
            <button class="js-tracks-operate"><?= __('批量处理'); ?></button>
        </div>
        <div class="tracks_con_time_list">
            <ul class='waterfall li_hover'>

            </ul>
        </div>

        <script id="waterfall-template" type="text/template">
          	<?php foreach($data['items'] as $key=>$v){ ?>
				<?php if(!empty($v['goods'])){?>
                <li>
                 <a class="posr" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?=$v['goods']['goods_id']?>" target="_blank">
                    <?php if($v['goods']['is_del'] == 2){?>
                 	    <p class="old-Failed old-Failed-wh2 "><?= __('此商品已失效'); ?></p>
                    <?php }?>
                 	
                 	<img src="<?php if($v['goods']['common_image']){?><?=$v['goods']['common_image']?><?php }else{?><?=image_thumb($this->web['goods_image'],118,118)?><?php }?>"/>
                 	<em class="f14 c-222 one-overflow block"><?=$v['goods']['common_name']?></em>
                 </a>

                  <span><?=format_money($v['goods']['common_price'])?></span>
	                 <button class="btn-del-footprint"><?= __('删除足迹'); ?></button>
	                 <div class="del-tips">
	                 	<p><i class="iconfont icon-tips"></i><?= __('确定删除么？'); ?></p>
	                 	<div>
	                 		<button class="btn-del-sure" data-common_id="<?=$v['goods']['common_id']?>"><?= __('删除'); ?></button><button class="btn-del-cancel"><?= __('取消'); ?></button>
	                 	</div>
	                 	
	                 </div>
	                 <div class="sel-active " data-common_id="<?=$v['goods']['common_id']?>"><em><i class="iconfont icon-yes"></i></em></div>
                </li>
				<?php }?>
			<?php }?>
        </script>
        <script id="waterfall-template2" type="text/template">
            <?php foreach($data['items'] as $key=>$v){ ?>
        <?php if(!empty($v['goods'])){?>
                <li>
                 <a class="posr" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?=$v['goods']['goods_id']?>" target="_blank">
                    <?php if($v['goods']['is_del'] == 2){?>
                      <p class="old-Failed old-Failed-wh2 "><?= __('此商品已失效'); ?></p>
                    <?php }?>
                  
                  <img src="<?php if($v['goods']['common_image']){?><?=$v['goods']['common_image']?><?php }else{?><?=image_thumb($this->web['goods_image'],118,118)?><?php }?>"/>
                  <em class="f14 c-222 one-overflow block"><?=$v['goods']['common_name']?></em>
                 </a>

                  <span><?=format_money($v['goods']['common_price'])?></span>
                   <button class="btn-del-footprint"><?= __('删除足迹'); ?></button>
                   <div class="del-tips">
                    <p><i class="iconfont icon-tips"></i><?= __('确定删除么？'); ?></p>
                    <div>
                      <button class="btn-del-sure" data-common_id="<?=$v['goods']['common_id']?>"><?= __('删除'); ?></button><button class="btn-del-cancel"><?= __('取消'); ?></button>
                    </div>
                    
                   </div>

                   <div class="sel-active active" data-common_id="<?=$v['goods']['common_id']?>"><em ><i class="iconfont icon-yes"></i></em></div>

                </li>
        <?php }?>
      <?php }?>
        </script>
        <script id="waterfall-template3" type="text/template">
            <?php foreach($data['items'] as $key=>$v){ ?>
        <?php if(!empty($v['goods'])){?>
                <li>
                 <a class="posr" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?=$v['goods']['goods_id']?>" target="_blank">
                    <?php if($v['goods']['is_del'] == 2){?>
                      <p class="old-Failed old-Failed-wh2 ">此商品已失效</p>
                    <?php }?>
                  
                  <img src="<?php if($v['goods']['common_image']){?><?=$v['goods']['common_image']?><?php }else{?><?=image_thumb($this->web['goods_image'],118,118)?><?php }?>"/>
                  <em class="f14 c-222 one-overflow block"><?=$v['goods']['common_name']?></em>
                 </a>

                  <span><?=format_money($v['goods']['common_price'])?></span>
                   <button class="btn-del-footprint"><?= __('删除足迹'); ?></button>
                   <div class="del-tips">
                    <p><i class="iconfont icon-tips"></i><?= __('确定删除么？'); ?></p>
                    <div>
                      <button class="btn-del-sure" data-common_id="<?=$v['goods']['common_id']?>">删除</button><button class="btn-del-cancel">取消</button>
                    </div>
                    
                   </div>

                   <div class="sel-active active" data-common_id="<?=$v['goods']['common_id']?>"><em class="active"><i class="iconfont icon-yes"></i></em></div>

                </li>
        <?php }?>
      <?php }?>
        </script>


        <!-- <div class="tracks_con_more">
			<?php if(!empty($data['items'])){?>
			<?php foreach($data['items'] as $key=>$v){ ?>
            <div class="tracks_con_time_list">
          
              <ul class="clearfix li_hover">
				<?php foreach($val as $k=>$v){ ?>
				<?php if(!empty($v['goods'])){?>
                
                <li data-common_id="<?=$v['goods']['common_id']?>">
                 <a class="posr" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?=$v['goods']['goods_id']?>" target="_blank">
                    <?php if($v['goods']['is_del'] == 2){?>
                 	    <p class="old-Failed old-Failed-wh2 ">此商品已失效</p>
                    <?php }?>
                 	
                 	<img src="<?php if($v['goods']['common_image']){?><?=$v['goods']['common_image']?><?php }else{?><?=image_thumb($this->web['goods_image'],118,118)?><?php }?>"/>
                 	<em class="f14 c-222 one-overflow block"><?=$v['goods']['common_name']?></em>
                 </a>

                  <span><?=format_money($v['goods']['common_price'])?></span>
                  <a class="add_cart" href="javascript:void(0)" title="<?=__('加入购物车')?>" data-param="{'ctl':'Buyer_Cart','met':'addCart','id':'<?=$v['goods']['goods_id']?>','num':'1'}"><i class="iconfont icon-zaiqigoumai f18 vermiddle"></i><?=__('加入购物车')?></a>
	                 <button class="btn-del-footprint">删除足迹</button>
	                 <div class="del-tips">
	                 	<p><i class="iconfont icon-tips"></i>确定删除么？</p>
	                 	<div>
	                 		<button class="btn-del-sure delete" data-param="{'ctl':'Buyer_Favorites','met':'delFootPrint','time':'<?=$key?>','common_id':'<?=$v['goods']['common_id']?>'}">删除</button><button class="btn-del-cancel">取消</button>
	                 	</div>
	                 	
	                 </div>
	                 <div class="sel-active"><em><i class="iconfont icon-yes"></i></em></div>
                </li>
				<?php }?>
				<?php }?>
				
              </ul>

            </div>
			<?php }?>
			<?php }else{ ?>
			 <div class="no_account">
				<img src="<?= $this->view->img ?>/ico_none.png"/>
				<p><?=__('暂无符合条件的数据记录')?></p>
			</div>  
			<div style="clear:both"></div>
			<?php } ?>
			<?php if($page_nav){?>
			<div class="flip page page_front clearfix" style="text-align: center;">
				<?=$page_nav?>
			</div>
			<?php }?>
			<div style="clear:both"></div>
        </div>  -->

    </div>
   </div>
 </div>
</div>
<script src="<?= $this->view->js ?>/bootstrap-waterfall.js"></script>
<script>

  $('.waterfall').data('bootstrap-waterfall-template', $('#waterfall-template').html())
  .waterfall();
  
</script>
<script type="text/javascript">
	function hov(){
		$(this).addClass("active");
	}
	function lev(){
		$(this).removeClass("active");
	}
	//批量处理
    function tracks_all(){
        if($(this).hasClass("active")){
            $(this).removeClass('active');
            $(this).html("<?= __('批量处理');?>");
            $(this).parent().find("p").css("display",'none');
            $('.tracks_con_time_list').find(".sel-active").removeClass("active");
            $(document).on('mouseenter','.tracks_con_time_list ul li',hov).on("mouseleave",'.tracks_con_time_list ul li',lev);
        }else{
            $(this).addClass("active");
            $(this).html("<?= __('完成');?>");
            $(this).parent().find("p").css("display",'inline-block');
            $('.tracks_con_time_list').find(".sel-active").addClass("active");
            $(".tracks_con_time_list li").removeClass("active")
            $(document).off('mouseenter','.tracks_con_time_list ul li').unbind("mouseleave",'.tracks_con_time_list ul li');
            $(".js-selAll").prop("checked",false);
            $('.waterfall').data('bootstrap-waterfall-template', $('#waterfall-template2').html()).waterfall();
        }
    }
	$(".js-tracks-operate").click(tracks_all);
    $(".js-selAll").click(function(){
        if(this.checked==true){
            $('.waterfall').data('bootstrap-waterfall-template', $('#waterfall-template3').html()).waterfall();
             $('.sel-active em').addClass("active");

        }else{
            $('.sel-active em').removeClass("active");
        }

    })
    $(document).on("click",'.li_hover li',function(){
      var liLen,selLen;
      $(".toast-error").hide();
        if($(this).find(".sel-active em").hasClass('active')){
            $(this).find(".sel-active em").removeClass("active");
            $('.js-selAll').attr('checked',false);
             
        }else{
            $(this).find(".sel-active em").addClass("active");
        }
        liLen=$(".li_hover li").length;
        selLen=$(".li_hover li em.active").length;
        if(selLen==liLen){
          $(".js-selAll").attr("checked",true);
        }
    })
	$(document).on('mouseenter','.tracks_con_time_list ul li',hov).on("mouseleave",'.tracks_con_time_list ul li',lev);
	$(document).on("click",'.btn-del-footprint',function(event){
		$(this).parent().find(".del-tips").show();
		$(this).parents("li").removeClass("active").off("mouseenter").unbind("mouseleave");
		event.stopPropagation();
	})
	$(document).on("click",'.btn-del-cancel',function(event){
		$(this).parents(".del-tips").hide();
		$(this).parents("li").on("mouseenter",hov).on("mouseleave",lev);
		event.stopPropagation();
	})

	$(document).on('click','.btn-del-sure',function(event){
		$(this).parents(".del-tips").hide();
		$(this).parents("li").on("mouseenter",hov).on("mouseleave",lev);
		event.stopPropagation();
	})
    $(".add_cart").click(function(){
        eval('data_str =' + $(this).attr('data-param'));
        $.post(SITE_URL  + '?ctl='+data_str.ctl+'&met='+data_str.met+'&typ=json',{goods_id:data_str.id,goods_num:data_str.num},function(data){
            if(data && 200 == data.status){

                Public.tips.success("<?=__('加入成功！')?>");
            }else
            {
                Public.tips.error("<?=__('加入失败！')?>");
            }
        });
    });
    //批量删除足迹
    $(".delete").click(function(){
        eval('data_str =' + $(this).attr('data-param'));
        var common_id = [];
        $(".sel-active").each(function(){
            if($(this).find('em').hasClass('active'))
            {
                common_id.push($(this).data('common_id'));
            }
        })
        if(common_id.length <= 0){
            Public.tips.error("<?=__('请选择要删除的足迹！')?>");
            return false;
        }
        $.dialog.confirm("<?=__('确认删除？')?>",function(){
            $.post(SITE_URL  + '?ctl=Buyer_Favorites&met=delFootPrint&typ=json',{common_id:common_id},function(data){
                if(data && 200 == data.status){
                  window.location.reload();
                }else
                {
                    Public.tips.error("<?=__('删除失败！')?>");
                }
            });
        });
    });
    //单个删除足迹
    $(document).on('click','.btn-del-sure',function(){
        var common_id = [];
        common_id.push($(this).data('common_id'));
        $.post(SITE_URL  + '?ctl=Buyer_Favorites&met=delFootPrint&typ=json',{common_id:common_id},function(data){
            if(data && 200 == data.status){
                window.location.reload();
            }else
            {
                Public.tips.error("<?=__('删除失败！')?>");
            }
        });
    })
</script>

<?php
include $this->view->getTplPath() . '/' . 'buyer_footer.php';
?>