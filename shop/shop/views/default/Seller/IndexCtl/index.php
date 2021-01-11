<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
$shop_grade = $shop_base['shop_grade'];
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
	<div class="seller-index-main">
		<div class="basic-info clearfix">
			<dl class="fl">
				<dt>
	               <p class="img-box"><img class="cter" src="<?php if(!empty($shop_base['shop_logo'])){ ?><?=$shop_base['shop_logo']?><?php }else{?> <?=$this->web['shop_head_logo']?><?php } ?>" /></p>
				   <a href="index.php?ctl=Seller_Shop_Setshop&met=index&typ=e&"><i class="iconfont icon-bi"></i><?=__('编辑店铺设置')?></a>
				</dt>
				<dd>
					<?php
						if(mb_strlen($shop_base['shop_name'])>45)
						{
							$shop_base['shop_name'] = substr($shop_base['shop_name'],0,45).'...';
						}
					?>
					<h3><?= __($shop_base['shop_name'])?></h3>
					
				</dd>
				<dd>
					<span><b class="letter-space1"><?=__('用户名')?>：</b><em><?= __($shop_base['user_name'])?></em></span>
					<?php if($shop_base['shop_self_support'] == 'false'){ ?>
						<span><?=__('有效期')?>：<em><?=$shop_base['shop_end_time']?></em></span>
					<?php } ?>
					<span><?=__('店铺等级')?>：<em><?=$shop_grade?></em></span>
					<span><b class="letter-space2"><?=__('IP地址')?>：</b><em><?php if(strpos($user_base['user_login_ip'],',') === false){ echo $user_base['user_login_ip'];}else{$ips = explode(',',$user_base['user_login_ip']);echo $ips[0];}?></em></span>
					<span><?=__('最后登录')?>：<em><?=$user_base['user_login_time']?></em></span>
					
				</dd>
			</dl>
			<div class="detail-rate">
				<h5><strong><?=__('同行相比')?></strong><?=__('店铺动态评分')?></h5>
				<ul>
					<li>
						<span> <?php if($shop_detail['com_desc_scores'] > 0){ ?><i class="iconfont  icon-jiantouxiangshang bbc_seller_color"></i><?=__('高于')?><?php }elseif($shop_detail['com_desc_scores'] < 0){ ?><i class="iconfont  icon-jiantouxiangxia "></i><?=__('低于')?><?php }else{ ?><i class="iconfont icon-jiantouxiangshang "></i><?=__('等于')?><?php }?></span><?=__('描述相符：')?><em><?=number_format($shop_detail['shop_desc_scores'],2,'.','')?></em><?=__('分')?>
					</li>
	                <li>
						<span> <?php if($shop_detail['com_service_scores'] > 0){?><i class="iconfont  icon-jiantouxiangshang bbc_seller_color"></i><?=__('高于')?><?php }elseif($shop_detail['com_service_scores'] < 0){ ?><i class="iconfont  icon-jiantouxiangxia "></i><?=__('低于')?><?php }else{ ?><i class="iconfont icon-jiantouxiangshang "></i><?=__('等于')?><?php }?></span><?=__('服务态度：')?><em><?=number_format($shop_detail['shop_service_scores'],2,'.','')?></em><?=__('分')?>
					</li>
					<li>
						<span><?php if($shop_detail['com_send_scores'] > 0){ ?><i class="iconfont  icon-jiantouxiangshang bbc_seller_color"></i><?=__('高于')?><?php }elseif($shop_detail['com_send_scores'] < 0){ ?><i class="iconfont  icon-jiantouxiangxia "></i><?=__('低于')?><?php }else{ ?><i class="iconfont icon-jiantouxiangshang "></i><?=__('等于')?><?php }?></span><?=__('发货速度：')?><em><?=number_format($shop_detail['shop_send_scores'],2,'.','')?></em><?=__('分')?>
					</li>
				</ul>
			</div>
		</div>
		<div class="container fn-clear container_cj bgf">
			<div class="m white-panel">
	        	<div class="pannel_div">
	            	<div class="mt">
						<h3 class="bbc_seller_border"><?=__('店铺及商品提示')?></h3>
						<h5><?=__('您需要关注的店铺信息以及待处理事项')?></h5>
					</div>
					<div class="mc">
						<div class="mc-module1 mc-module1-first">
							<dl class="seller-module-survey">
								<dt><?=__('店铺商品发布情况')?>:</dt>
								<dd><b> <?= $goods_state_normal_num+$goods_state_offline_num+$goods_state_illegal_num ?></b><em>/ <?= $shop_grade_goods_limit ?: __('不限')?></em></dd>
							</dl>
							<dl class="seller-module-survey">
								<dt><?=__('图片空间使用')?>:</dt>
								<dd><strong><?= $shop_album_num ?></strong><em> / <?= $shop_grade_album_limit ?:__('不限');?></em></dd>
							</dl>
						</div>
						<ul class="fn-clear mc-module2">
							<li><a class="<?=$goods_state_normal_num?'num active':''?>" href="./index.php?ctl=Seller_Goods&met=online&typ=e"><?=__('出售中商品')?><em class="bbc_seller_bg"><?=$goods_state_normal_num?$goods_state_normal_num:''?></em></a></li>
							<li><a class="<?=$goods_verify_waiting_num?'num num1 active':''?>" href="./index.php?ctl=Seller_Goods&met=offline&met=verify&typ=e&op=3"><?=__('待审核商品')?><em class="bbc_seller_bg"> <?=$goods_verify_waiting_num?$goods_verify_waiting_num:''?></em></a></li>
							<li><a class="<?=$goods_state_offline_num?'num active':''?>" href="./index.php?ctl=Seller_Goods&met=offline&typ=e&op=1" ><?=__('仓库中商品')?> <em class="bbc_seller_bg"><?=$goods_state_offline_num ? $goods_state_offline_num : ''?></em></a></li>
							<li><a class="<?=$goods_state_illegal_num?'num num1  active':''?>" href="./index.php?ctl=Seller_Goods&met=lockup&typ=e&op=2" ><?=__('违规下架商品')?> <em class="bbc_seller_bg"><?=$goods_state_illegal_num? $goods_state_illegal_num: ''?></em></a></li>
						</ul>
					</div>
				</div>
	        </div>

			<div class="m white-panel">
	        	<div class="pannel_div">
					<div class="mt">
						<h3 class="bbc_seller_border"><?=__('交易提示')?></h3>
						<h5><?=__('您需要立即处理的交易订单')?></h5>
					</div>
					<div class="mc">
						<div class="mc-module1">
							<dl class="seller-module-survey">
								<dt><?=__('近期售出')?>:</dt>
								<dd><a class="seller-a-link-blue" href="./index.php?ctl=Seller_Trade_Order&met=physical&typ=e&"><?=__('交易中的订单')?></a></dd>
							</dl>
							<dl class="seller-module-survey">
								<dt><?=__('维权提示')?>:</dt>
								<dd><a class="seller-a-link-red" href="./index.php?ctl=Seller_Service_Complain&met=index&typ=e&"><?=__('收到维权投诉')?>&nbsp;<?= $complain_unsolved_count ? $complain_unsolved_count : 0 ?> / <?= $complain_all_count ? $complain_all_count : 0?></a></dd>
							</dl>
						</div>
						<ul class="mc-module2" id="order_num_list">
							<li><a href="./index.php?ctl=Seller_Trade_Order&met=getPhysicalNew&typ=e"><?=__('待付款订单')?></a></li>
							<li><a href="./index.php?ctl=Seller_Trade_Deliver&met=deliver&typ=e"><?=__('待发货订单')?></a></li>
							<li><a href="./index.php?ctl=Seller_Service_Return&met=orderReturn&typ=e" class=""><?=__('退款订单')?></a></li>
							<li><a href="./index.php?ctl=Seller_Service_Return&met=goodsReturn&typ=e" class=""><?=__('退货订单')?></a></li>
						</ul>
					</div>
	        	</div>
			</div>

			<div class="m white-panel pb30">
	            <div class="pannel_div">
					<div class="mt">
						<h3 class="bbc_seller_border"><?=__('销售情况统计')?></h3>
						<h5><?=__('按周期统计商家店铺的订单量和订单金额')?></h5>
					</div>
					<div class="mc">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<th width="20%"></th>
								<th width="40%"><?=__('订单量')?></th>
								<th width="40%"><?=__('订单金额')?></th>
							</tr>
							<tr>
								<td><?=__('今日销量')?></td>
								<td><?=@$today['sales_num']?></td>
								<td><?=@format_money($today['order_sales'])?></td>
							</tr>
							<tr>
								<td><?=__('周销量')?></td>
								<td><?=@$week['sales_num']?></td>
								<td><?=@format_money($week['order_sales'])?></td>
							</tr>
							<tr>
								<td><?=__('月销量')?></td>
								<td><?=@$month['sales_num']?></td>
								<td><?=@format_money($month['order_sales'])?></td>
							</tr>
						</table>
					</div>
	            </div>
			</div>

			<div class="m white-panel pb30">
	            <div class="pannel_div">
					<div class="mt">
						<h3 class="bbc_seller_border"><?=__('单品销售排名')?></h3>
						<h5><?=__('掌握30日内最热销的商品及时补充货源')?></h5>
					</div>
					<div class="mc rank">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<th><?=__('排名')?></th>
								<th colspan='2'><?=__('商品信息')?></th>
								<th><?=__('销量')?></th>
							</tr>
							<?php
							if(!empty($shop_top_rows)) {$i =0;
								foreach ($shop_top_rows as $key => $shop_top_row) { ++$i;
									if($i<4){ ?>
									<tr>
										<td><?= $key + 1 ?></td>
										<td width="100"><a target="_blank"
																	 href="<?= Yf_Registry::get('index_page') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= $shop_top_row['goods_id'] ?>"><img
													width="32" src="<?= image_thumb($shop_top_row['goods_image'], 60, 60) ?>"/></a>
										</td>
										<td class="tl"><a target="_blank"
														  href="<?= Yf_Registry::get('index_page') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= $shop_top_row['goods_id'] ?>"><?= $shop_top_row['goods_name'] ?></a>
										</td>
										<td><?= $shop_top_row['goods_num'] ?></td>
									</tr>
									<?php
								  }
								} 
							 }else{ ?>
								<td colspan='4'>
									<div class="seller-index-table-nodata">
										<b></b>
										<p>暂无排名信息</p>
									</div>
								</td>
							<?php } ?>
							
						</table>
					</div>
	            </div>
			</div>
		</div>
		<div class="container bgf">
			<div class="seller-index-module-box">
	            <div class="pannel_div">	
		            <div class="mt">
		                <h3 class="bbc_seller_border"><?=__('店铺运营推广')?></h3>
		                <h5><?=__('合理参加促销活动可以有效提升商品销量')?></h5>
		            </div>
		            <div class="mc">
		                <div class="content clearfix seller-index-store-rank-module">
		                    <?php if($data['promotion_items']['groupbuy_allow_flag']){  ?>
		                    <div class="extension-item">
		                    	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_GroupBuy&met=index&typ=e">
		                    		<i class="iconfont icon-tuan style-color1"></i>
		                    		<div>
		                    			<h5><?=__('团购')?></h5>
		                    			<b><?php if($data['promotion_items']['groupbuy_combo_flag']){ ?><?=__('已开通')?><?php }else{ ?><?=__('未开通')?><?php } ?></b>
		                    		</div>
		                    	</a>
		                    </div>
		                    <?php } ?>

		                    <?php if($data['promotion_items']['promotion_allow_flag']){   ?>
		                    <div class="extension-item">
		                    	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Increase&met=index&typ=e&op=list">
		                    		<i class="iconfont icon-jiage style-color2"></i>
		                    		<div>
		                    			<h5><?=__('加价购')?></h5>
		                    			<b><?php if($data['promotion_items']['promotion_increase_combo_flag']){ ?><?=__('已开通')?><?php }else{ ?><?=__('未开通')?><?php } ?></b>
		                    		</div>
		                    	</a>
		                    </div>
		                    <div class="extension-item">
		                    	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Discount&met=index&typ=e">
		                    		<i class="iconfont icon-zhekou style-color3"></i>
		                    		<div>
		                    			<h5><?=__('限时折扣')?></h5>
		                    			<b><?php if($data['promotion_items']['promotion_discount_combo_flag']){ ?><?=__('已开通')?><?php }else{ ?><?=__('未开通')?> <?php } ?></b>
		                    		</div>
		                    	</a>
		                    </div>
		                    <div class="extension-item">
		                    	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_MeetConditionGift&met=index&typ=e">
		                    		<i class="iconfont icon-liwu style-color1"></i>
		                    		<div>
		                    			<h5><?=__('满即送')?></h5>
		                    			<b><?php if($data['promotion_items']['promotion_mansong_combo_flag']){ ?><?=__('已开通')?><?php }else{ ?><?=__('未开通')?> <?php } ?></b>
		                    		</div>
		                    	</a>
		                    </div>
		                    <?php  }  ?>

		                    <?php if($data['promotion_items']['voucher_allow_flag']){ ?>
		                    <div class="extension-item">
		                    	<a href="<?=Yf_Registry::get('url')?>?ctl=Seller_Promotion_Voucher&met=index&typ=e">
		                    		<i class="iconfont icon-daijinquan2 style-color2"></i>
		                    		<div>
		                    			<h5><?=__('代金券')?></h5>
		                    			<b><?php if($data['promotion_items']['voucher_combo_flag']){ ?><?=__('已开通')?><?php }else{ ?><?=__('未开通')?> <?php } ?></b>
		                    		</div>
		                    	</a>
		                    </div>
		                    <?php } ?>
							
							<?php //if(Web_ConfigModel::value('Plugin_Directseller')&&(@$shop_base != 2)){ ?>
							<!-- <div class="extension-item">
		                    	<a href="<?//=Yf_Registry::get('url')?>?ctl=Distribution_Seller_Setting&met=index&typ=e">
		                    		<i class="iconfont icon-saler style-color3"></i>
		                    		<div>
		                    			<h5><?//=__('销售员')?></h5>
		                    			<b><?//=__('已开通')?></b>
		                    		</div>
		                    	</a>
		                    </div> -->
		                    <?php //} ?>
		                </div>
		            </div>
		        </div>
			</div>
		</div>
	</div>
	<div class="seller-index-aside">
		<?php if($phone || $email){?>
			<ol class="seller-aside-contacts">
				<?php if($phone){?>
				<?php foreach($phone as $k=>$v){?>
				<li>
					<div>
						<em class="img-box tel"></em>
						<h4><?=__('客服电话：')?></h4>
						<p class="desc"><?=$v;?></p>
					</div>
				</li>
				<?php }?>
				<?php }?>
				<?php if($email){?>
				<li>
					<div>
						<em class="img-box emails"></em>
						<h4><?=__('客服邮箱：')?></h4>
						<p class="desc"><?=$email?></p>
					</div>
				</li>
				
				<?php }?>
			</ol>
		<?php }?>
	</div>


	<!-- <script src="<?=$this->view->js?>/pinterest_grid.js"></script>
	<script type="text/javascript">
	    $(function(){
	      $(".container_cj").pinterest_grid({
	        no_columns:2,
	         padding_x:10,
	        padding_y:10,
	        margin_bottom: 50,
	        single_column_breakpoint: 700
	      });
	      
	    });
	</script> -->
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>

<script>
	$(function() {
		//交易提示 初始化

		$.post(SITE_URL + '?ctl=Seller_Trade_Order&met=getOrderNum&typ=json', {}, function (data) {
			if ( data.status == 200 )
			{

				var data = data.data, order_num_list = $('#order_num_list').children();

				if ( data.wait_pay_num > 0 ) {
					$(order_num_list[0]).children('a').addClass('num  active').append('<em class="bbc_seller_bg">' + data.wait_pay_num + '</em>');
				}

				if ( data.payed_num > 0 ) {
					$(order_num_list[1]).children('a').addClass('num  active').append('<em class="bbc_seller_bg">' + data.payed_num + '</em>');
				}

				if ( data.refund_num > 0 ) {
					$(order_num_list[2]).children('a').addClass('num  active').append('<em class="bbc_seller_bg">' + data.refund_num + '</em>');
				}

				if ( data.return_num > 0 ) {
					$(order_num_list[3]).children('a').addClass('num  active').append('<em class="bbc_seller_bg">' + data.return_num + '</em>');
				}
			}
		})
	})
</script>



