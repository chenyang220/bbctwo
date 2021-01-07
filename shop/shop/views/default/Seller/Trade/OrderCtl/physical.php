<?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>

<link href="<?= $this->view->css ?>/seller_center.css?ver=<?= VER ?>" rel="stylesheet">
<link href="<?= $this->view->css ?>/base.css?ver=<?= VER ?>" rel="stylesheet">
<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css?ver=<?=VER?>" rel="stylesheet">
<script src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js" charset="utf-8"></script>
<script src="<?= $this->view->js ?>/seller_order.js" charset="utf-8"></script>
</head>
<body>
<script type='text/jade' id='thrid_opt'>
                <a class="bbc_seller_btns1" id="export"><?= __('导出订单') ?></a>
</script>
<div class="search">
	<form>
		<div class="filter-groups">
			<dl>
				<dt><?=__('下单时间：')?></dt>
				<dd style="width:390px;">
					<input style="width:150px;" type="text" class="text hasDatepicker" placeholder="<?=__('起始时间')?>" name="query_start_date" id="query_start_date" value="<?php if (!empty($condition['order_create_time:>='])) {
					echo $condition['order_create_time:>='];
					} ?>" readonly="readonly"><label class="add-on"><i class="iconfont icon-rili"></i></label><span class="rili_ge">–</span>
					<input style="width:150px;" id="query_end_date" class="text hasDatepicker" placeholder="<?=__('结束时间')?>" type="text" name="query_end_date" value="<?php if (!empty($condition['order_create_time:<='])) {
					echo $condition['order_create_time:<='];
					} ?>" readonly="readonly"><label class="add-on"><i class="iconfont icon-rili"></i></label>
				</dd>
			</dl>
			<dl>
				<dt><?=__('买家昵称：')?></dt>
				<dd>
					<input type="text" class="text wp100" placeholder="<?=__('买家昵称')?>" id="buyer_name" name="buyer_name" value="<?php if (!empty($condition['buyer_user_name:LIKE'])) {
					echo str_replace('%', '', $condition['buyer_user_name:LIKE']);
				} ?>">
				</dd>
			</dl>
			<dl>
				<dt><?=__('订单编号：')?></dt>
				<dd><input type="text" class="text wp100" placeholder="<?=__('请输入订单编号')?>" id="order_sn" name="order_sn" value="<?php if (!empty($condition['order_id'])) {
					echo $condition['order_id'];
				} ?>"></dd>
			</dl>
		</div>
		<div class="control-group">
			<a onclick="formSub()" class="button btn_search_goods" href="javascript:void(0);"><?=__('筛选')?></a><a class="button refresh" onclick="location.reload()">重新刷新</a>
			<input name="ctl" value="Seller_Trade_Order" type="hidden" /><input name="met" value="<?=$_GET['met']?>" type="hidden" />
			<?php if($_GET['met'] == 'physical'){?>
				<span class="search-filter-prev"><input type="checkbox" id="skip_off" value="1" <?php if (!empty($condition['order_status:<>'])) {
						echo 'checked';
					} ?> name="skip_off"> <label class="relative_left" for="skip_off"><?=__('不显示已关闭的订单')?></label>
				</span>
			<?php }?>
		</div>
	</form>
</div>

<table class="ncsc-default-table order ncsc-default-table2">
	<thead>
	<tr>
		<th class="w10"></th>
		<th colspan="2"><?=__('商品')?></th>
		<th class="w100"><?=__('单价')?><!--（<?/*=Web_ConfigModel::value('monetary_unit')*/?>）--></th>
		<th class="w40"><?=__('数量')?></th>
		<th class="w100"><?=__('买家')?></th>
		<th class="w100"><?=__('订单金额')?></th>
		<th class="w90"><?=__('交易状态')?></th>
		<th class="w120"><?=__('操作')?></th>
	</tr>
	</thead>

	<?php if ( !empty($data['items']) ) { ?>
	<?php foreach ( $data['items'] as $key => $val ) { ?>
	<tbody>
	<tr>
		<td colspan="20" class="sep-row"></td>
	</tr>
	<tr>
		<th colspan="20">
			<span class="ml10"><?=__('订单编号')?>：<em><?= $val['order_id']; ?></em></span> 
			<span><?=__('下单时间')?>：<em class="goods-time"><?= $val['order_create_time']; ?></em></span>
			<?php if(Web_ConfigModel::value('yunshan_status',0) == 1 &&$val['payment_other_number']){?>
			<span><?=__('支付单')?>：<em class="goods-time"><?= $val['payment_other_number']; ?></em></span>
		    <?php } ?>
			<span class="fr mr5"> <a href="<?= $val['delivery_url']; ?>" class="ncbtn-mini bbc_seller_btns" target="_blank" title="<?=__('打印发货单')?>"><i class="icon-print"></i><?=__('打印发货单')?></a></span>
		</th>
	</tr>

	<!-- S商品列表 -->
	<?php 
			if( !empty($val['goods_list']) ) { 
			$dist_num = 0;	//进货单统计
	 		foreach( $val['goods_list'] as $k => $v ) { 
 	?>
	<tr>
		<td class="bdl"></td>
		<td class="w70">
			<div class="ncsc-goods-thumb">
				<a href="<?= $v['goods_link']; ?>" target="_blank"><img src="<?= $v['goods_image']; ?>"></a>
			</div>
		</td>
		<td class="tl">
			<dl class="goods-name">
				<dt>
					<?php if($v['order_goods_source_id']){ ?>
						<span class="dis_flag"><?=__('分销')?></span>
					<?php } ?>
					<a target="_blank" href="<?= $v['goods_link']; ?>"><?= $v['goods_name']; ?></a>
					<a target="_blank" class="blue ml5" href="<?= $v['goods_link']; ?>">
                    <?php if($val['is_pintuan'] == 1){ ?>
						<span class="dis_flag"><?=__('拼团')?></span>
					<?php } ?>
                    <?php if($val['is_pintuan'] == 2){ ?>
						<span class="dis_flag"><?=__('单独购买')?></span>
					<?php } ?>
                    <?=__('[交易快照]')?></a>
					<?=$v['return_txt']?>
				</dt>
				<dd><strong>￥<?= $v['goods_price']; ?></strong>&nbsp;x&nbsp;<em><?= $v['order_goods_num']; ?></em><?=__('件')?></dd>
				<?php if(isset($v['order_spec_info']) && $v['order_spec_info']){ ?>
                    <!--取消【规格：】显示-->
					<dd class='block'>
                        <!--<strong><?/*=__('规格')*/?>：</strong>&nbsp;&nbsp;-->
                        <em class="order-props-limit one-overflow"><?= $v['order_spec_info']; ?></em>
                    </dd>
				<?php }?>
				<?php if ($v['order_goods_benefit']) {
				        $arr_benefit = explode(' ',$v['order_goods_benefit']);
				        foreach ($arr_benefit as $r){ ?>
                            <span class="td_sale bbc_btns"><p><?= ($r) ?></p></span>
                        <?php        } ?>

                <?php } ?>
				<!-- S消费者保障服务 -->
				<!-- E消费者保障服务 -->
				<?php if($val['order_goods_benefit']){?>
					<em class="td_sale bbc_btns small_details">$val['order_goods_benefit']</em>
				<?php }?>
			</dl>
		</td>
		<td><p><?= @format_money($v['goods_price']); ?></p>
		</td>
		<td><?= $v['order_goods_num']; ?></td>
		
		<?php if($v['order_goods_source_id']){$dist_num++;}?>
		<!-- S 合并TD -->
		<?php if ( $k == 0 ) { ?>
		<td class="bdl" rowspan="<?= $val['goods_cat_num']; ?>">
			<div class="buyer"><?= $val['buyer_user_name']; ?><p member_id="<?= $val['buyer_user_id']; ?>"></p>
				<div class="buyer-info"><em></em>
					<div class="con">
						<h3><i></i><span><?=__('联系信息')?></span></h3>
						<dl>
							<dt><?=__('姓名')?>：</dt>
							<dd><?= $val['buyer_user_name']; ?></dd>
						</dl>
						<dl>
							<dt><?=__('电话')?>：</dt>
							<dd><?= $val['order_receiver_contact']; ?></dd>
						</dl>
						<dl>
							<dt><?=__('地址')?>：</dt>
							<dd><?= $val['order_receiver_address']; ?></dd>
						</dl>
					</div>
				</div>
			</div>
		</td>
		<td class="bdl" rowspan="<?= $val['goods_cat_num']; ?>" style="width: 126px;">
			<p class="ncsc-order-amount"><?= @format_money($val['order_payment_amount']); ?></p>
			<p class="goods-freight"><?= $val['shipping_info']; ?></p>
			<p class="goods-pay" title="<?=__('支付方式')?>：<?=$val['payment_name']?>"><?=$val['payment_name']?></p>
			<?php if ( $val['order_shop_benefit'] && $val['order_benefits']) { ?>
				<span class="td_sale bbc_btns">
					<?php foreach($val['order_benefits'] as $v){ ?>
						<p><?=($v)?></p>
					<?php } ?>
				</span>
			<?php } ?>
		</td>
		<td class="bdl bdr" rowspan="<?= $val['goods_cat_num']; ?>">
			<p><?= $val['order_stauts_text']; ?></p>
			<?php if($val['payment_id'] == PaymentChannlModel::PAY_CONFIRM){?>
				<p class="bbc_color"><?=__('货到付款')?></p>
			<?php }?>
			<!-- 订单查看 -->
			<p><a href="<?= $val['info_url']; ?>" target="_blank"><?=__('订单详情')?></a></p>
			<!-- 标明订单是否是货到付款订单 -->
			
			<!-- 物流跟踪 -->
            <?php if($val['order_status'] == Order_StateModel::ORDER_WAIT_CONFIRM_GOODS || $val['order_status'] == Order_StateModel::ORDER_FINISH){ ?>
				
				  <div  style ="position:relative;display: block;    width: 84px;height: 20px;"  onmouseover="show_logistic('<?=($val['order_id'])?>','<?=($val['order_shipping_express_id'])?>','<?=($val['order_shipping_code'])?>')" onmouseout="hide_logistic('<?=($val['order_id'])?>')"  >
				<a style="position:relative;  display: block;"   >
					<i class="iconfont icon-icowaitproduct rel_top2"></i><?=__('物流信息')?>
                </a>
					<div style="display: none;" id="info_<?=($val['order_id'])?>" class="prompt-01">
						   <?php if(count($val['wuliuPc'])<=0){?>
                                <div class="error_msg">暂无物流信息</div>"
                           <?php }else{?>
						        <div class="AtSeBnBox">
                                <?php for ($i=0; $i <count($val['wuliuPc']); $i++) { ?>
                                    <div class=""><span>包裹<?=$i+1?></span></div>
                                <?php }  ?>
                                </div>
                                <div class="showCententList" style="border: 1px solid;">
                                    <?php foreach($val['wuliuPc'] as $key=>$vv_pc){?>
                                    <div class="SwCtSon">
                                        <div class="SwCtHd">物流单号：<span><?php echo $vv_pc['shiping_code'];?></span></div>
                                        <div class="row audiImgList">
                                            <?php foreach($vv_pc['image'] as $k=>$v){?>
                                            <div class="col-xs-2"><img src="<?php echo $v;?>" alt=""></div>
                                            <?php }?>
                                        </div>
                                        <div class="SwCt_text">
                                            <div class="SwCtText_p"><?php echo $vv_pc['content'];?></div>
                                        </div>
                                    </div>
                                    <?php }?>
                                </div>
                           <?php }?>                                
					 </div>			
				</div>
			<?php }?>
			<p></p>
		</td>

		<!-- 取消订单 -->
		<td class="bdl bdr" rowspan="<?= $val['goods_cat_num']; ?>">
			<!-- 修改价格 -->
			<!-- 发货 -->
			<?php if ($val['order_status'] == Order_StateModel::ORDER_PAYED  && $val['shiping_codes'] ) {  ?>                
                <a class="ncbtn ncbtn-mint mt10 bbc_seller_btns" href="<?=Yf_Registry::get('url')?>?ctl=Seller_Trade_Order&met=send&typ=e&order_id=<?=$val['order_id']?>"><i class="icon-truck"></i>继续发货</a>
				<p style="color: red;font-weight: 400;">已部分发货</p>                           
            <?php }elseif(!$dist_num) { ?>
            	<p><?= $val['set_html']; ?></p>
            <?php } ?>
            <!-- 订单删除 -->
            <?php if(($val['order_status'] == Order_StateModel::ORDER_CANCEL || $val['order_status'] == Order_StateModel::ORDER_FINISH) && $val['order_shop_hidden'] == 0):?>
                      
                        <p><a onclick="hideOrder('<?=$val['order_id']?>')"><i class="iconfont icon-lajitong fz20 align-middle"></i><span class="align-middle"><?=__('删除订单')?></span></a></p>
                      
            <?php endif; ?>
            <?php if(($val['order_status'] == Order_StateModel::ORDER_CANCEL || $val['order_status'] == Order_StateModel::ORDER_FINISH) && $val['order_shop_hidden'] == 1):?>
                      
                        <p><a onclick="restoreOrder('<?=$val['order_id']?>')"><i class="iconfont icon-huanyuan fz20 align-middle"></i><?=__('还原订单')?></a></p>
                      
            <?php endif; ?>
            <?php if(($val['order_status'] == Order_StateModel::ORDER_CANCEL || $val['order_status'] == Order_StateModel::ORDER_FINISH) && $val['order_shop_hidden'] == 1):?>
                      
                        <p><a onclick="delOrder('<?=$val['order_id']?>')"><i class="iconfont icon-lajitong fz20 align-middle"></i><?=__('彻底删除')?></a></p>
                      
            <?php endif; ?>

			<!-- 已发货和已完成的货到付款订单添加商家确认收款按钮 -->
			<?php if(($val['order_status'] == Order_StateModel::ORDER_WAIT_CONFIRM_GOODS || $val['order_status'] == Order_StateModel::ORDER_FINISH) && $val['order_shop_hidden'] == 0 && $val['payment_id'] == PaymentChannlModel::PAY_CONFIRM):?>
				<?php if($val['payment_time'] > 0){?>
					<p><a class=""><?=__('已收款')?></a></p>
				<?php }else{?>
					<p><a onclick="confirmCollection('<?=$val['order_id']?>')" class="btn-get-cold"><?=__('确认收款')?></a></p>
				<?php }?>

			<?php endif; ?>
			<!-- 锁定 -->
		</td>
		<!-- E 合并TD -->
	</tr>
	<?php } ?>
	<?php } ?>
	<?php } ?>
	</tbody>
	<?php } ?>
	<?php } ?>
</table>

<?php if ( empty($data['items']) ) { ?>
<div class="no_account">
	<img src="<?=$this->view->img?>/ico_none.png">
	<p><?=__('暂无符合条件的数据记录')?></p>
</div>
<?php } ?>
<div class="page">
	<?= $data['page_nav']; ?>
</div>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>

<script>
var queryConditions={};
$('.tabmenu').find('li:gt(6)').hide();

$(".tabmenu").append($("#thrid_opt").html());
	$(function () {

		//时间
		$('#query_start_date').datetimepicker({
            controlType: 'select',
            format: 'Y-m-d H:i:s',
			onShow:function( ct ){
				this.setOptions({
					maxDate:$('#query_end_date').val() ? $('#query_end_date').val() : false
				})
			}
		});
		$('#query_end_date').datetimepicker({
            controlType: 'select',
            format: 'Y-m-d H:i:s',
			onShow:function( ct ){
				this.setOptions({
					minDate:$('#query_start_date').val() ? $('#query_start_date').val() : false
				})
			},
		});

		//搜索

		var URL;

		$('input[type="submit"]').on('click', function (e) {
			e.preventDefault();

			URL = createQuery();
			window.location = URL;
		});

		$('#skip_off').on('click', function () {
			URL = createQuery();
			window.location = URL;
		});

		function createQuery () {
			var url = SITE_URL + '?' + location.href.match(/ctl=\w+&met=\w+/) + '&';

			$('#query_start_date').val() && (url += 'query_start_date=' + $('#query_start_date').val() + '&');
			$('#query_end_date').val() && (url += 'query_end_date=' + $('#query_end_date').val() + '&');
			$('#buyer_name').val() && (url += 'buyer_name=' + $('#buyer_name').val() + '&');
			$('#order_sn').val() && (url += 'order_sn=' + $('#order_sn').val() + '&');
			$('#skip_off').prop('checked') && (url += 'skip_off=1&');

			return url;
		}

		//取消订单
		$('a[dialog_id="seller_order_cancel_order"]').on('click', function () {

			var order_id = $(this).data('order_id'),
				url = SITE_URL + '?ctl=Seller_Trade_Order&met=orderCancel&typ=';

			$.dialog({
				title: '<?=__('取消订单')?>',
				content: 'url: ' + url + 'e',
				data: { order_id: order_id },
				height: 250,
				width: 400,
				lock: true,
				drag: false,
				ok: function () {

					var form_ser = $(this.content.order_cancel_form).serialize();

					$.post(url + 'json', form_ser, function (data) {
						if ( data.status == 200 ) {
							parent.Public.tips({
								content: '<?=__('修改成功')?>',
								type: 3
							}), window.location.reload();
							return true;
						} else {
							parent.Public.tips({
								content: '<?=__('修改失败')?>',
								type: 1
							});
							return false;
						}
					})
				}
			})
		});
	});

	function formSub(){
		$('.filter-groups').parents('form').submit();
	}

	window.hide_logistic = function (order_id)
	{
		$("#info_"+order_id).hide();
		// $("#info_"+order_id).html("");
	}


	function getQueryString(name){
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r!=null) return r[2]; return '';
    }



	//导出
    $("#export").click(function ()
    {
    	queryConditions.query_start_date = $("input[name='query_start_date']").val();
    	queryConditions.query_end_date = $("input[name='query_end_date']").val();
    	queryConditions.buyer_name = $("input[name='buyer_name']").val();
    	queryConditions.order_id = $("input[name='order_sn']").val();
    	var firstRow = getQueryString('firstRow');
		queryConditions.start_limit = Number(firstRow);
		queryConditions.order_status = "<?=$condi['order_status'];?>";  
		queryConditions.limit = 10; 
        $.dialog({
            title: '订单导出',
            content: "url:"+SITE_URL + '?ctl=Seller_Trade_Order&met=exportOrder',
            data: queryConditions,
            width: 800,
            height: $(window).height()*0.5,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    });

	window.show_logistic = function (order_id,express_id,shipping_code)
	{
		$("#info_"+order_id).show();
		// $.post(BASE_URL + "/shop/api/logistic.php",{"order_id":order_id,"express_id":express_id,"shipping_code":shipping_code} ,function(da) {
		// 	console.log(da);
		// 	if(da)
		// 	{
		// 		$("#info_"+order_id).html(da);
		// 	}
		// 	else
		// 	{
		// 		$("#info_"+order_id).html('<div class="error_msg"><?=__('接口出现异常')?></div>');
		// 	}

		// })
	}


</script>
<script type="text/javascript">
    
    $(".AtSeBnBox div").click(function(){
        var a = $(this).index();
        displayContent(a);
    });
    function displayContent(a) {
        $(".AtSeBnBox div").removeClass("activeSize");
        $(".AtSeBnBox div").eq(a).addClass("activeSize");
        // $(".showCententList").children("div").hide();
        // $(".showCententList").children("div").eq(a).show();
    }
    //    默认显示
    // displayContent(0);
</script>
<style type="text/css">
    @charset "utf-8";
.header-l a i {
    display: inline-block;
    width: 0.409rem;
    height: 0.75rem;
    background: url(../images/bbc-bg45.png) no-repeat center;
    background-size: cover;
    margin-top: 0.6rem;
}
/*.publishCenterBox{
    width:400px;
    display: block;
    margin: 40px auto 0 auto;
    background-color: #fff;
    padding-top:15px;
    padding-bottom:20px;
}*/

.AtSeBnBox {
    width: 100%;
    border-bottom: 1px solid #999;
    font-size: 0;
    white-space: nowrap;
    overflow: auto;
}

/*作为IT界最前端的技术达人，页面上的每一个元素的样式我们都必须较真，就是滚动条我们也不会忽略。
下面我给大家分享一下如何通过CSS来控制滚动条的样式，代码如下：*/
/*定义滚动条轨道*/
.AtSeBnBox::-webkit-scrollbar-track
{
    border-top:1px solid #999;
    background-color: #F5F5F5;
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.22);
}
/*定义滚动条高宽及背景*/
.AtSeBnBox::-webkit-scrollbar
{
    width: 5px;
    height:10px;
    background-color: rgba(0, 0, 0, 0.34);
    border-top:1px solid red;
}
/*定义滚动条*/
.AtSeBnBox::-webkit-scrollbar-thumb
{
    background-color: #c1c1c1;
    border-radius: 10px;
}

.AtSeBnBox>div {
    display: inline-block;
    font-size: 16px;
    line-height: 20px;
    padding: 8px 21px;
    border-right: 1px solid #999;
    border-top: 1px solid #999;
    cursor: pointer;
}
.AtSeBnBox>div:first-child{
    border-left: 1px solid #999;
}
eBnBox p{
    font-size: 18px;
    line-height: 22px;
    color: #666;
}
.SwCtHd{
    font-size: 16px;
    line-height: 22px;
    margin: 10px 0;
    color: #666;
}
.SwCtHd>span{
    font-size: 16px;
    line-height: 22px;
    color: #999;
}


.showCententList{
    padding-top: 15px;
    /*padding: 0 15px;*/
}
.audiImgList{
    border-top: 1px solid #999;
    font-size: 0;
    white-space: nowrap;
    overflow: auto;
    padding-bottom: 4px;
}

/*定义滚动条轨道*/
.audiImgList::-webkit-scrollbar-track
{
    margin-top:10px;
    background-color: #F5F5F5;
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.22);
}
/*定义滚动条高宽及背景*/
.audiImgList::-webkit-scrollbar
{
    width: 5px;
    height:10px;
    background-color: rgba(0, 0, 0, 0.34);
    border-top:1px solid red;
}
/*定义滚动条*/
.audiImgList::-webkit-scrollbar-thumb
{
    background-color: #c1c1c1;
    border-radius: 10px;
}

.audiImgList>div{
    margin-right: 10px;
    margin-top: 10px;
}
.audiImgList>div{
    display: inline-block;
}
.col-xs-2 {
    width: 16.66666667%;
}
.audiImgList img {
    width: 100%;
    min-height: 80px;
}
.SwCtSon{
    width: 100%;
}
.activeSize{
    background: #f51616;
}
 .activeSize>p{
     color: #fff;

}

.SwCt_text{
    border: 1px solid #999;
    margin-top: 30px;
    max-height: 250px;
    overflow: auto;
}
.SwCtText_p{
    font-size: 16px;
    line-height: 20px;
    color: #666;
}

</style>
