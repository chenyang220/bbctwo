<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>

<link href="./ucenter_admin/static/default/css/index.css" rel="stylesheet" type="text/css">
<script src="./ucenter_admin/static/default/js/libs/template.js"></script>
</head>
<body>
<div id="hd" class="cf">
	<div class="fl welcome cf">
		<strong><span id="greetings"></span><?= __('，');?><span id="username"></span></strong>
<!--		<a  id="manageAcct"><?= __('账号管理');?></a>-->
<!--		<a href="radio/newbieRadio/scm_demo.htm" target="_blank" id="newGuide" title="<?= __('新手入门');?>"><?= __('新手入门');?></a>-->
	</div>
</div>
<script>
	var greetings = "", cur_time = new Date().getHours();
	if(cur_time >= 0 && cur_time <= 4 ) {
		greetings = "<?= __('已经夜深了，请注意休息');?>"
	} else if (cur_time > 4 && cur_time <= 7 ) {
		greetings = "<?= __('早上好');?>";
	} else if (cur_time > 7 && cur_time < 12 ) {
		greetings = "<?= __('上午好');?>";
	} else if (cur_time >= 12 && cur_time <= 18 ) {
		greetings = "<?= __('下午好');?>";
	} else {
		greetings = "<?= __('晚上好');?>";
	};
	$("#greetings").text(greetings);

	$(function(){
	 Public.ajaxGet('', {}, function(data){
            if(data.status === 200) {
                $("#username").text(data.data[0].user_account);
            } else if (data.status === 250){
               $("#username").text('');
            } else {
                Public.tips({type: 1, content : data.msg});
            }
        });
	})
</script>
<div id="bd" class="index-body cf">
	<div class="col-main">
		<div class="main-wrap cf">
			<div class="m-top cf" id="profileDom">
				<!--
					  <div class="fr" id="interval">
						  <label class="radio"><input type="radio" name="interval" value="month" checked="checked"><?= __('本月');?></label>
						  <label class="radio"><input type="radio" name="interval" value="year"><?= __('本年');?></label>
						  <label class="radio"><input type="radio" name="interval" value="today"><?= __('今日');?></label>
						  <label class="radio"><input type="radio" name="interval" value="yesterday"><?= __('昨日');?></label>
					</div>
				   -->
				<table width="100%" border="0" cellspacing="0" cellpadding="20">
					<tr>
					    <td><a class="ta t6" tabid="setting-goodsList" data-right="INVENTORY_QUERY" tabTxt="<?= __('商品管理');?>" parentOpen="true" rel="pageTab" href=""></a></td>
						<td><a class="ta t7" tabid="setting-customerList" data-right="BU_QUERY" tabTxt="<?= __('客户管理');?>" parentOpen="true" rel="pageTab" href=""></a></td>
					    <td><a class="ta t9"tabid="setting-vendorList" data-right="PUR_QUERY" tabTxt="<?= __('供应商管理');?>" parentOpen="true" rel="pageTab" href=""></a></td>
						<td><a class="ta t1" tabid="report-initialBalance" data-right="InvBalanceReport_QUERY" tabTxt="<?= __('商品库存余额');?>" parentOpen="true" rel="pageTab" href=""></a></td>
						<td><a class="ta t2 soon" href="javascript:void(0);" tabid="report-cashBankJournal" data-right="SettAcctReport_QUERY" tabTxt="<?= __('现金银行报表');?>" parentOpen="true" rel="pageTab" ></a></td>
						<td><a class="ta t3 soon"  href="javascript:void(0);" tabid="report-contactDebt" data-right="ContactDebtReport_QUERY" tabTxt="<?= __('往来单位欠款表');?>" parentOpen="true" rel="pageTab" ></a></td>
						<td><a class="ta t4" tabid="report-salesSummary" data-right="SAREPORTINV_QUERY" tabTxt="<?= __('销售汇总表（按商品）');?>" parentOpen="true" rel="pageTab" href=""></a></td>
						<td><a class="ta t5" tabid="report-puSummary" data-right="PUREPORTINV_QUERY" tabTxt="<?= __('采购汇总表（按商品）');?>" parentOpen="true" rel="pageTab" href=""></a></td>

					</tr>
				</table>
			</div>
			<ul class="quick-links">
				<li class="purchase-purchase">
					<a tabid="purchase-purchase" data-right="PU_ADD" tabTxt="<?= __('购货单');?>" parentOpen="true" rel="pageTab" href=""><span></span></a>
				</li>
				<li class="sales-sales">
					<a tabid="sales-sales" data-right="SA_ADD" tabTxt="<?= __('销货单');?>" parentOpen="true" rel="pageTab" href=""><span></span></a>
				</li>
				<!--<li class="storage-transfers">
					<a tabid="storage-transfers" data-right="TF_ADD" tabTxt="<?= __('调拨单');?>" parentOpen="true" rel="pageTab" href="/scm/invTf.do?action=initTf"><span></span><?= __('仓库调拨');?></a>
				</li>-->
				<li class="storage-inventory">
					<a tabid="storage-inventory" data-right="PD_GENPD" tabTxt="<?= __('盘点');?>" parentOpen="true" rel="pageTab" href=""><span></span></a>
				</li>
				<li class="storage-inventory">
				<a tabid="sales-salesList" data-right="SA_QUERY" tabTxt="<?= __('销货记录');?>" parentOpen="true" rel="pageTab" href=""><span></span></a>
<!--					<a tabid="storage-inventory" data-right="PD_GENPD" tabTxt="<?= __('盘点');?>" parentOpen="true" rel="pageTab" href="./erp.php?ctl=Goods_WarehouseSku"><span></span><?= __('库存盘点');?></a>-->
				</li>
				<li class="storage-inventory">
				<a tabid="purchase-salesList" data-right="PU_QUERY" tabTxt="<?= __('采购记录');?>" parentOpen="true" rel="pageTab" href=""><span></span></a>

<!--					<a tabid="storage-inventory" data-right="PD_GENPD" tabTxt="<?= __('盘点');?>" parentOpen="true" rel="pageTab" href="./erp.php?ctl=Goods_WarehouseSku"><span></span><?= __('库存盘点');?></a>-->
				</li>
				<li class="storage-otherWarehouse">
				<a href="" rel="pageTab" tabid="money-receiptList" tabTxt="<?= __('收款记录');?>" data-right="RECEIPT_QUERY" parentOpen="true"><span></span></a>

                <!--	<a tabid="storage-otherWarehouse" data-right="IO_ADD" tabTxt="<?= __('其他入库');?>" parentOpen="true" rel="pageTab" href="/scm/invOi.do?action=initOi&type=in"><span></span><?= __('其他入库');?></a>-->
				</li>
				<li class="storage-otherOutbound">
				<a href="" rel="pageTab" tabid="money-paymentList" tabTxt="<?= __('付款记录');?>" data-right="PAYMENT_QUERY" parentOpen="true"><span></span></a>

                <!--	<a tabid="storage-otherOutbound" data-right="OO_ADD" tabTxt="<?= __('其他出库');?>" parentOpen="true" rel="pageTab" href="/scm/invOi.do?action=initOi&type=out"><span></span><?= __('其他出库');?></a>-->
				</li>
				<li class="storage-otherOutbound">
				<a class="soon" href="javascript:void(0);"><span></span></a>
				</li>
				<li class="added-service">
				<a class="soon" href="javascript:void(0);"><span></span></a>
				<!--	<a tabid="setting-addedServiceList" tabTxt="<?= __('增值服务');?>" parentOpen="true" rel="pageTab" href="settings/addedServiceList.jsp"><span></span><?= __('增值服务');?></a>-->
				</li>
				<li class="feedback">
				<a class="soon" href="javascript:void(0);"><span></span></a>
				<!--    <a href="#" id="feedback"><span></span><?= __('意见反馈');?></a>-->
				</li>
			</ul>
		</div>
	</div>
	<!--<div class="col-extra">
		<div class="extra-wrap">
			<h2><?= __('快速查看');?></h2>
			<div class="list">
				<ul>
					<li><a tabid="setting-goodsList" data-right="INVENTORY_QUERY" tabTxt="<?= __('商品管理');?>" parentOpen="true" rel="pageTab" href="./erp.php?ctl=Goods_Base"><?= __('商品管理');?></a></li>
					<li><a tabid="setting-customerList" data-right="BU_QUERY" tabTxt="<?= __('客户管理');?>" parentOpen="true" rel="pageTab" href="./erp.php?ctl=Member_Base"><?= __('客户管理');?></a></li>
					<li><a tabid="setting-vendorList" data-right="PUR_QUERY" tabTxt="<?= __('供应商管理');?>" parentOpen="true" rel="pageTab" href="./erp.php?ctl=Vendor_Base"><?= __('供应商管理');?></a></li>
					<li><a tabid="sales-salesList" data-right="SA_QUERY" tabTxt="<?= __('销货记录');?>" parentOpen="true" rel="pageTab" href="./erp.php?ctl=Sale_Bill&met=indexList"><?= __('销售记录');?></a></li>
					<li><a tabid="purchase-salesList" data-right="PU_QUERY" tabTxt="<?= __('采购记录');?>" parentOpen="true" rel="pageTab" href="./erp.php?ctl=Purchase_Bill&met=purchaseList"><?= __('采购记录');?></a></li>
					<li><a href="./erp.php?ctl=Finance_ReceiptBill&met=manage" rel="pageTab" tabid="money-receiptList" tabTxt="<?= __('收款记录');?>" data-right="RECEIPT_QUERY" parentOpen="true"><?= __('收款记录');?></a></li>
					<li><a href="./erp.php?ctl=Finance_PaymentBill&met=manage" rel="pageTab" tabid="money-paymentList" tabTxt="<?= __('付款记录');?>" data-right="PAYMENT_QUERY" parentOpen="true"><?= __('付款记录');?></a></li>

					<li><a href="/report/sales-detail.jsp" rel="pageTab" tabid="report-salesDetail" tabTxt="<?= __('销售明细表');?>" data-right="SAREPORTDETAIL_QUERY" parentOpen="true"><?= __('销售明细表');?></a></li>
					<li><a href="/report/puDetail.do?action=detail" rel="pageTab" tabid="report-puDetail" tabTxt="<?= __('采购明细表');?>" data-right="PUREOORTDETAIL_QUERY" parentOpen="true"><?= __('采购明细表');?></a></li>
					<li style="border-bottom:none; line-height: 42px; "><a tabid="storage-transfersList" data-right="TF_QUERY" tabTxt="<?= __('调拨记录');?>" parentOpen="true" rel="pageTab" href="/scm/invTf.do?action=initTfList"><?= __('调拨记录');?></a></li>

					<li><a tabid="storage-otherWarehouseList" data-right="IO_QUERY" tabTxt="<?= __('其他入库记录');?>" parentOpen="true" rel="pageTab" href="/scm/invOi.do?action=initOiList&type=in"><?= __('其他入库记录');?></a></li>
					<li><a tabid="storage-otherOutboundList" data-right="OO_QUERY" tabTxt="<?= __('其他出库记录');?>" parentOpen="true" rel="pageTab" href="/scm/invOi.do?action=initOiList&type=out"><?= __('其他出库记录');?></a></li>
					<li><a tabid="report-initialBalance" data-right="InvBalanceReport_QUERY" tabTxt="<?= __('商品库存余额');?>" parentOpen="true" rel="pageTab" href="/report/invBalance.do?action=detail"><?= __('商品库存余额');?></a></li>
					<li><a tabid="report-contactDebt" data-right="ContactDebtReport_QUERY" tabTxt="<?= __('往来单位欠款表');?>" parentOpen="true" rel="pageTab" href="/report/contactDebt.do?action=detail"><?= __('往来单位欠款');?></a></li>

				</ul>
			</div>
		</div>
	</div>-->
</div>
<script id="profile" type="text/html">
	<table width="100%" border="0" cellspacing="0" cellpadding="20">
		<tr>
			<td><a class="tad t1" tabid="report-initialBalance" data-right="InvBalanceReport_QUERY" tabTxt="<?= __('商品库存余额');?>" parentOpen="true" rel="pageTab" href=""><span><?= __('库存总量');?>:<b><#= items[0].total1 #></b></span><span><?= __('库存成本');?>:<b><#= items[0].total2 #></b></span></a></td>
			<td><a class="tad t2" tabid="report-cashBankJournal" data-right="SettAcctReport_QUERY" tabTxt="<?= __('现金银行报表');?>" parentOpen="true" rel="pageTab" href=""><span><?= __('现金');?>:<b><#= items[1].total1 #></b></span><span><?= __('银行存款');?>:<b><#= items[1].total2 #></b></span></a></td>
			<td><a class="tad t3" tabid="report-contactDebt" data-right="ContactDebtReport_QUERY" tabTxt="<?= __('往来单位欠款表');?>" parentOpen="true" rel="pageTab" href=""><span><?= __('客户欠款');?>:<b><#= items[2].total1 #></b></span><span><?= __('供应商欠款');?>:<b><#= items[2].total2 #></b></span></a></td>
			<td><a class="tad t4" tabid="report-salesSummary" data-right="SAREPORTINV_QUERY" tabTxt="<?= __('销售汇总表（按商品）');?>" parentOpen="true" rel="pageTab" href=""><span><?= __('销售收入');?>(<?= __('本月');?>):<b><#= items[3].total1 #></b></span><span><?= __('销售毛利');?>(<?= __('本月');?>):<b><#= items[3].total2 #></b></span></a></td>
			<td><a class="tad t5" tabid="report-puSummary" data-right="PUREPORTINV_QUERY" tabTxt="<?= __('采购汇总表（按商品）');?>" parentOpen="true" rel="pageTab" href=""><span><?= __('采购金额');?>(<?= __('本月');?>):<b><#= items[4].total1 #></b></span><span><?= __('商品种类');?>(<?= __('本月');?>):<b><#= items[4].total2 #></b></span></a></td>
		</tr>
	</table>
	<i></i>
</script>

<script>
	parent.dataReflush = function(){
		if(parent.SYSTEM.isAdmin || parent.SYSTEM.rights.INDEXREPORT_QUERY) {
			template.openTag = '<#';
			template.closeTag = '#>';
			/*
			Public.ajaxGet('/report/index.do?action=getInvData', {finishDate: parent.SYSTEM.endDate, beginDate: parent.SYSTEM.beginDate, endDate: parent.SYSTEM.endDate }, function(data){
				if(data.status === 200) {
					var html = template.render('profile', data.data);
					document.getElementById('profileDom').innerHTML = html;
					reportParam();
				} else {
					parent.Public.tips({type: 1, content : data.msg});
				}
			});
			*/
		};
	};
	parent.dataReflush();
	$('#profileDom').on('click','i',function(){
		parent.dataReflush();
	});
</script>

<script>
	Public.pageTab();
	reportParam();
	function reportParam(){
		$("[tabid^='report']").each(function(){
			var dateParams = "beginDate="+parent.SYSTEM.beginDate+"&endDate="+parent.SYSTEM.endDate;
			var href = this.href;
			href += (this.href.lastIndexOf("?")===-1) ? "?" : "&";
			if($(this).html() === '<?= __('商品库存余额表');?>'){
				this.href = href + "beginDate="+parent.SYSTEM.startDate+"&endDate="+parent.SYSTEM.endDate;
			}
			else{
				this.href = href + dateParams;
			}
		});
	}

	var goodsCombo = Business.goodsCombo($('#goodsAuto'), {
		extraListHtml: ''
	});

	$('#goodsAuto').click(function(){
		var _self = this;
		setTimeout(function(){
			_self.select();
		}, 50);
	});
    $('.soon').click(function(){
          parent.Public.tips({type: 2, content : '<?= __('为防止测试人员乱改数据，演示站功能受限，暂时屏蔽。');?>'});
    });
	$('#invWarning').click(function(){
		if (!Business.verifyRight('INVENTORY_WARNING')) {
			return ;
		};
		$.dialog({
			width: 800,
			height: 410,
			title: '<?= __('商品库存预警');?>',
			content: '',
			cancel: true,
			lock: true,
			cancelVal: '<?= __('关闭');?>'
		});
	});

	$('#stockSearch').click(function(e){
		e.preventDefault();
		var id = goodsCombo.getValue();
		var text = $('#goodsAuto').val();
		Business.forSearch(id, text);
		$('#goodsAuto').val('');
	});

	$("#feedback").click(function(e){
		e.preventDefault();
		parent.tab.addTabItem({tabid: 'myService', text: '<?= __('服务支持');?>', url: '', callback: function(){
			parent.document.getElementById('myService').contentWindow.openTab(3);
		}});
	});

	$('.bulk-import').click(function(e){
		e.preventDefault();
		if (!Business.verifyRights('BaseData_IMPORT')) {
			return ;
		};
		parent.$.dialog({
			width: 560,
			height: 300,
			title: '<?= __('批量导入');?>',
			content: 'url:/import.jsp',
			data: {
				callback: function(row){

				}
			},
			lock: true
		});
	});

	$('#manageAcct').click(function(e){
		e.preventDefault();
		/*var updateUrl = location.protocol + '//' + location.host + '/update_info.jsp',
			url = 'http://service.yuanfeng.com/user/set_password.jsp?updateUrl=' + encodeURIComponent(updateUrl) + '&userName' + parent.SYSTEM.userName;
		*/

		var url = '';
		$.dialog({
            width: 400,
            height: 200,
            max: !1,
            min: !1,
            cache: !1,
            //lock: !0
			title: '<?= __('账号管理');?>',
			content: 'url:' + url
		});
	});
</script>
<?php
include $this->view->getTplPath() . '/'  . 'footer.php';
?>