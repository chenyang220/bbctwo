<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link href="<?=$this->view->css?>/shop_table.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body class="<?=$skin?>">
<style>

.ui-jqgrid tr.jqgrow .img_flied{padding: 1px; line-height: 0px;}
.img_flied img{width: 100px; height: 30px;}

</style>
<div style="   overflow: hidden;
    padding: 10px 3% 0;
    text-align: left;" >
    <?php
    foreach ($data['base'] as $key => $value) {
    if(!empty($value['shop_company_name']) && $value['shop_business']){
    ?>

  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20"><?= __('公司及联系人信息'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150"><?= __('公司名称：'); ?></th>
        <td colspan="20"><?=$value['shop_company_name']?></td>
      </tr>
      <tr>
        <th><?= __('公司所在地：'); ?></th>
        <td><?=$value['shop_company_address']?></td>
        <th><?= __('公司详细地址：'); ?></th>
        <td colspan="20"><?=$value['company_address_detail']?></td>
      </tr>
      <tr>
        <th><?= __('公司电话：'); ?></th>
        <td><?=$value['company_phone']?></td>
        <th><?= __('员工总数：'); ?></th>
        <td><?=$value['company_employee_count']?>&nbsp;<?= __('人'); ?></td>
        <th><?= __('注册资金：'); ?></th>
        <td><?=$value['company_registered_capital']?>&nbsp;<?= __('万元'); ?> </td>
      </tr>
      <tr>
        <th><?= __('联系人姓名：'); ?></th>
        <td><?=$value['contacts_name']?></td>
        <th><?= __('联系人电话：'); ?></th>
        <td><?=$value['contacts_phone']?></td>
        <th><?= __('电子邮箱：'); ?></th>
        <td><?=$value['contacts_email']?></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20"><?= __('营业执照信息（副本）'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150"><?= __('营业执照号：'); ?></th>
        <td><?=$value['business_id']?></td>
      </tr>
      <tr>
        <th><?= __('营业执照所在地：'); ?></th>
        <td><?=$value['business_license_location']?></td>
      </tr>
      <tr>
        <th><?= __('营业执照有效期：'); ?></th>
        <td> <?=$value['business_licence_start']?> - <?=$value['business_licence_end']?></td>
      </tr>
    
      <tr>
        <th><?= __('营业执照'); ?><br />
          <?= __('电子版：'); ?></th>
        <td colspan="20"><a href="<?=$value['business_license_electronic']?>" target="_blank"><img src="<?=$value['business_license_electronic']?>" alt="" /></a></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin" <?php echo !$value['organization_code_electronic'] && !$value['tax_registration_certificate_electronic'] ? 'style="display:none;"' : '';?> >
    <thead>
      <tr>
        <th colspan="20"><?= __('组织机构代码证'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th><?= __('组织机构代码：'); ?></th>
        <td colspan="20"><?=trim($value['organization_code'],"'")?></td>
      </tr>
      <tr>
        <th><?= __('组织机构代码证'); ?><br/>
          <?= __('电子版：'); ?></th>
          
         <td colspan="20"><a href="<?=trim($value['organization_code_electronic'],"'")?>" target="_blank"><img src="<?=trim($value['organization_code_electronic'],"'")?>" alt="" /></a></td>
      </tr>
    </tbody>
  </table>
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin" <?php echo !$value['organization_code_electronic'] && !$value['tax_registration_certificate_electronic'] ? 'style="display:none;"' : '';?> >
        <thead>
          <tr>
            <th colspan="20"><?= __('税务登记证'); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th class="w150"><?= __('税务登记证号：'); ?></th>
            <td><?=$value['tax_registration_certificate']?></td>
          </tr>
          <tr>
            <th><?= __('纳税人识别号：'); ?></th>
            <td><?=$value['taxpayer_id']?></td>
          </tr>
          <tr>
            <th><?= __('税务登记证号'); ?><br />
              <?= __('电子版：'); ?></th>
            <td><a nctype="nyroModal"  href="<?=$value['tax_registration_certificate_electronic']?>" target="_blank"> <img src="<?=$value['tax_registration_certificate_electronic']?>" alt="" /> </a></td>
          </tr>
        </tbody>
    </table>
  
	<?php $other_image = explode(',',$value['company_apply_image']); ?>
	<table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
		<thead>
			<tr><th colspan="20"><?= __('其他证明：'); ?></th></tr>
		</thead>
		
		<tbody>
			<?php 
				if($other_image){ 
				foreach($other_image as $k=>$v){
			?>
			<tr>
				<th><?= __('其他证明'); ?><?php echo ($k+1);?><?= __('：'); ?></th>
				<td colspan="20"><a href="<?=$v?>" target="_blank"><img src="<?=$v?>" alt="" /></a></td>
			</tr>
				<?php }} ?>
		</tbody>
	</table>
	
	<table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
		<thead>
			<tr><th colspan="20"><?= __('开户银行信息：'); ?></th></tr>
		</thead>
		<tbody>
			<tr>
				<th class="w150"><?= __('银行开户名：'); ?></th>
				<td><?=$value['bank_account_name']?></td>
			</tr>
			<tr>
				<th><?= __('公司银行账号：'); ?></th>
        <td><?=trim($value['bank_account_number'],"'")?></td>
      </tr>
      <tr>
        <th><?= __('开户银行支行名称：'); ?></th>
        <td><?=$value['bank_name']?></td>
      </tr>
      <tr>
        <th><?= __('支行联行号：'); ?></th>
        <td><?=trim($value['bank_code'],"'")?></td>
      </tr>
      <tr>
        <th><?= __('开户银行所在地：'); ?></th>
        <td colspan="20"><?=$value['bank_address']?></td>
      </tr>
      <tr>
        <th><?= __('开户银行许可证'); ?><br/>
          <?= __('电子版：'); ?></th>
        <td colspan="20"><a href="<?=$value['bank_licence_electronic']?>" target="_blank"><img src="<?=$value['bank_licence_electronic']?>" alt="" /></a></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20"><?= __('一般纳税人证明：'); ?></th>
      </tr>
    </thead>
		
		<tbody>
			<tr>
				<th><?= __('一般纳税人证明：'); ?></th>
        <td colspan="20"><a href="<?=$value['general_taxpayer']?>" target="_blank"><img src="<?=$value['general_taxpayer']?>" alt="" /></a></td>
			</tr>
		</tbody>
	</table>  
  
  
  <form id="form_store_verify" action="index.php?act=store&op=store_joinin_verify" method="post">
    <input id="verify_type" name="verify_type" type="hidden" />
    <input name="member_id" type="hidden" value="2" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20"><?= __('店铺经营信息'); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150"><?= __('商家账号：'); ?></th>
          <td><?=$value['user_name']?></td>
        </tr>
        <tr>
          <th class="w150"><?= __('店铺名称：'); ?></th>
          <td><?=$value['shop_name']?></td>
        </tr>
        <tr>
          <th><?= __('店铺等级：'); ?></th>
          <?php if(!empty($value['shop_grade'])){ foreach ($value['shop_grade'] as $keys=>$val){ ?>
          
          <td><?=$val['shop_grade_name']?><?= __('（开店费用：'); ?><?=$val['shop_grade_fee']?> <?= __('元'); ?>/<?= __('年）'); ?></td>
          
        
        </tr>
        <tr>
          <th class="w150"><?= __('开店时长：'); ?></th>
          <td><?=$value['joinin_year']?> <?= __('年'); ?></td>
        </tr>
        <tr>
          <th><?= __('店铺分类：'); ?></th>
           <?php foreach ($value['shop_class'] as $keyss=>$vals){ ?>
          <td><?=$vals['shop_class_name']?><?= __('（开店保证金：'); ?><?=$vals['shop_class_deposit']?> <?= __('元）'); ?></td>
  
        </tr>
        <tr>
          <th><?= __('应付总金额：'); ?></th>
          <td>    <?=$val['shop_grade_fee']*$value['joinin_year']+$vals['shop_class_deposit'] ?> <?= __('元'); ?>
            </td>
        </tr>
          <?php }}} ?>
        <tr>
          <th><?= __('经营类目：'); ?></th>
          <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" id="table_category" class="type">
              <thead>
                <tr>
                  <th><?= __('分类'); ?>1</th>
                  <th><?= __('分类'); ?>2</th>
                  <th><?= __('分类'); ?>3</th>
                  <th><?= __('分类'); ?>4</th>
                  <th><?= __('比例'); ?></th>
                </tr>
              </thead>
              <tbody>
              <?php if(!empty($value["classbind"]['items']['product_parent_name'])){ foreach($value["classbind"]['items']['product_parent_name'] as $keys => $vals){
                  ?>
                  <tr>
                  <?php $i=0; foreach ($vals as $keyss => $valss) { ?>
                        <td><?=@$valss['cat_name']?></td>
                   <?php $i++; }?>
                  <?php if($i==1){ ?>
                        <td></td>
                        <td></td>
                        <td></td>
                  <?php }elseif($i==2){?>
                        <td></td>
                        <td></td>
                  <?php }elseif($i==3){ ?>
                         <td></td>
                    <?php }else{}?>  
                  <td><?=@$value["classbind"]['items']['commission_rate'][$keys]?>%</td>
                </tr>
              <?php } }?>
                
                </tbody>
              </table>
                
          </td>
        </tr>
                <tr>
          <th><?= __('付款凭证：'); ?></th>
          <td><a nctype="nyroModal"  href="<?=$value['payment_voucher']?>" target="_blank"> <img src="<?=$value['payment_voucher']?>" alt="" /> </a></td>
        </tr>
        <tr>
          <th><?= __('付款凭证说明：'); ?></th>
          <td><?=$value['payment_voucher_explain']?></td>
        </tr>
                      </tbody>
    </table>
      </form>


	<form id="shop_verify-form">
	<table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
		<thead>
			<tr><th colspan="20"><?= __('开店信息审核'); ?></th></tr>
		</thead>
		<?php if($value['shop_status'] == 1){ ?>
        <tr>
            <th><?= __('公司基本信息审核：'); ?></th>
			<td>
				<div class="onoff">
					<?php if(in_array($value['shop_status'], array(4,5,6))){ ?>
						<label for="verify_enabled1" class="cb-enable" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled1" class="cb-disable selected" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled1" name="shop_verify1" value="2" type="radio">
						<input id="verify_disabled1" name="shop_verify1" checked="checked" value="4" type="radio">
					<?php }else{ ?>
						<label for="verify_enabled1" class="cb-enable selected" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled1" class="cb-disable" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled1" name="shop_verify1" checked="checked" value="2" type="radio">
						<input id="verify_disabled1" name="shop_verify1" value="4" type="radio">
					<?php } ?>
                        
				</div> 
                <span><?= __('（包括'); ?> <?= __('公司及联系人信息，营业执照信息（副本），组织机构代码证，一般纳税人证明，其他证明）'); ?></span>
                
			</td>
        </tr>
        <tr>
            <th><?= __('银行税务信息审核：'); ?></th>
            
			<td>
				<div class="onoff">
					<?php if(in_array($value['shop_status'], array(4,5,6))){ ?>
						<label for="verify_enabled2" class="cb-enable" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled2" class="cb-disable selected" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled2" name="shop_verify2" value="2" type="radio">
						<input id="verify_disabled2" name="shop_verify2" checked="checked" value="5" type="radio">
					<?php }else{ ?>
						<label for="verify_enabled2" class="cb-enable selected" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled2" class="cb-disable" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled2" name="shop_verify2" checked="checked" value="2" type="radio">
						<input id="verify_disabled2" name="shop_verify2" value="5" type="radio">
					<?php } ?>
				</div>
                <span><?= __('（包括'); ?> <?= __('开户银行信息'); ?>,<?= __('税务登记证）'); ?></span>
			</td>
        </tr>
        <tr>
			<th><?= __('店铺经营信息审核：'); ?></th>
			<td>
				<div class="onoff">
					<?php if(in_array($value['shop_status'], array(4,5,6))){ ?>
						<label for="verify_enabled3" class="cb-enable" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled3" class="cb-disable selected" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled3" name="shop_verify3" value="2" type="radio">
						<input id="verify_disabled3" name="shop_verify3" checked="checked" value="6" type="radio">
					<?php }else{ ?>
						<label for="verify_enabled3" class="cb-enable selected" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled3" class="cb-disable" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled3" name="shop_verify3" checked="checked" value="2" type="radio">
						<input id="verify_disabled3" name="shop_verify3" value="6" type="radio">
					<?php } ?>
				</div>
			</td>
		</tr>
        <?php }else if(($value['shop_status'] == 7 || $value['shop_status'] == 2 ) && $value['shop_payment'] == 1){ ?>
        <tr>
			<th><?= __('付款信息审核：'); ?></th>
			<td>
				<div class="onoff">
					<?php if($value['shop_status'] == 7){ ?>
						<label for="verify_enabled4" class="cb-enable" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled4" class="cb-disable selected" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled4" name="shop_verify4" value="3" type="radio">
						<input id="verify_disabled4" name="shop_verify4" checked="checked" value="7" type="radio">
					<?php }else{ ?>
						<label for="verify_enabled4" class="cb-enable selected" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled4" class="cb-disable" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled4" name="shop_verify4" checked="checked" value="3" type="radio">
						<input id="verify_disabled4" name="shop_verify4" value="7" type="radio">
					<?php } ?>
				</div>
			</td>
		</tr>
        <?php }else{?>
        <tr>
            <th><?= __('公司基本信息审核：'); ?></th>
			<td>
				
					<?php if($value['shop_status'] == 4){ ?>
						<?= __('拒绝'); ?>
					<?php }else{ ?>
						<?php if($value['shop_status'] != 3 && $value['shop_status'] != 2){ ?>
                        <?= __('待审核'); ?>
                        <?php }else{ ?>
						<?= __('通过'); ?>
                        <?php } ?>
					<?php } ?>
                        
                        <span style="color:gray;"><?= __('（包括'); ?> <?= __('公司及联系人信息，营业执照信息（副本），组织机构代码证，一般纳税人证明，其他证明）'); ?></span>
                
			</td>
        </tr>
        <tr>
            <th><?= __('银行税务信息审核：'); ?></th>
            
			<td>
				
					<?php if($value['shop_status'] == 5){ ?>
						<?= __('拒绝'); ?>
					<?php }else{ ?>
						<?php if($value['shop_status'] != 3 && $value['shop_status'] != 2){ ?>
                        <?= __('待审核'); ?>
                        <?php }else{ ?>
						<?= __('通过'); ?>
                        <?php } ?>
					<?php } ?>
				
                <span style="color:gray;"><?= __('（包括'); ?> <?= __('开户银行信息'); ?>,<?= __('税务登记证）'); ?></span>
			</td>
        </tr>
        <tr>
			<th><?= __('店铺经营信息审核：'); ?></th>
			<td>
			
					<?php if($value['shop_status'] == 6){ ?>
						<?= __('拒绝'); ?>
					<?php }else{ ?>
						<?php if($value['shop_status'] != 3 && $value['shop_status'] != 2){ ?>
                        <?= __('待审核'); ?>
                        <?php }else{ ?>
						<?= __('通过'); ?>
                        <?php } ?>
					<?php } ?>
		
			</td>
		</tr>
        <tr>
			<th><?= __('付款信息审核：'); ?></th>
			<td>
			
					<?php if($value['shop_status'] == 7){ ?>
						<?= __('拒绝'); ?>
					<?php }else{ ?>
                        <?php if($value['shop_status'] != 3){ ?>
                        <?= __('待审核'); ?>
                        <?php }else{ ?>
						<?= __('通过'); ?>
                        <?php } ?>
					<?php } ?>
			
			</td>
		</tr>
        
        <?php } ?>
		<tr>
			<th><?= __('审核信息：'); ?></th>
			<td>
				<textarea rows="2" class="ui-input w600"  name="shop_verify_reason" id="shop_verify_reason"><?=$value['shop_verify_reason']?></textarea>
			</td>
		</tr>
		<?php if(($value['shop_status'] == 1) || (($value['shop_status'] == 2 || $value['shop_status'] == 7) && $value['shop_payment'] == 1)){ ?>
		<tr>
			<th><?= __('操作：'); ?></th>
			<input type="hidden" name="shop_id" id="shop_id" value="<?=$value['shop_id'] ?>" />
			<td><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></td>
		</tr>
        <?php } ?>
	</table>
	</form>

    
    
    <?php }else{ ?>
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20"><?= __('个人实名信息'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150"><?= __('用户名：'); ?></th>
        <td colspan="20"><?=$value['user_name']?></td>
      </tr>
      <tr>
        <th class="w150"><?= __('真实姓名：'); ?></th>
        <td colspan="20"><?=$value['contacts_name']?></td>
      </tr>
      <tr>
        <th class="w150"><?= __('手机号码：'); ?></th>
        <td colspan="20"><?=$value['contacts_phone']?></td>
      </tr>
      <tr>
        <th class="w150"><?= __('邮箱：'); ?></th>
        <td colspan="20"><?=$value['contacts_email']?></td>
      </tr>
      <tr>
        <th class="w150"><?= __('常用地址：'); ?></th>
        <td colspan="20"><?=$value['shop_company_address'].' '.$value['company_address_detail']?></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20"><?= __('个人证件信息'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150"><?= __('证件类型：'); ?></th>
        <td><?=$value['legal_identity_type_content']?></td>
      </tr>
      <tr>
        <th><?= __('证件号码：'); ?></th>
        <td><?=$value['legal_person_number']?></td>
      </tr>
      <tr>
        <th><?= __('证件正面照'); ?></th>
        <td colspan="20"><a href="<?=$value['legal_person_electronic2']?>" target="_blank"><img src="<?=$value['legal_person_electronic2']?>" alt="" /></a></td>
      </tr>
      <tr>
        <th><?= __('证件背面照'); ?></th>
        <td colspan="20"><a href="<?=$value['legal_person_electronic']?>" target="_blank"><img src="<?=$value['legal_person_electronic']?>" alt="" /></a></td>
      </tr>
      <tr>
        <th><?= __('证件有效期：'); ?></th>
        <td> <?=$value['business_licence_start']?> - <?=$value['business_licence_end']?></td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20"><?= __('开户银行信息：'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150"><?= __('银行开户名：'); ?></th>
        <td><?=$value['bank_account_name']?></td>
      </tr>
      <tr>
        <th><?= __('公司银行账号：'); ?></th>
        <td><?=$value['bank_account_number']?></td>
      </tr>
      <tr>
        <th><?= __('开户银行支行名称：'); ?></th>
        <td><?=$value['bank_name']?></td>
      </tr>
      <tr>
        <th><?= __('支行联行号：'); ?></th>
        <td><?=$value['bank_code']?></td>
      </tr>
      <tr>
        <th><?= __('开户银行所在地：'); ?></th>
        <td colspan="20"><?=$value['bank_address']?></td>
      </tr>
    </tbody>
  </table>
  <form id="form_store_verify" action="index.php?act=store&op=store_joinin_verify" method="post">
    <input id="verify_type" name="verify_type" type="hidden" />
    <input name="member_id" type="hidden" value="2" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20"><?= __('店铺经营信息'); ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150"><?= __('商家账号：'); ?></th>
          <td><?=$value['user_name']?></td>
        </tr>
        <tr>
          <th class="w150"><?= __('店铺名称：'); ?></th>
          <td><?=$value['shop_name']?></td>
        </tr>
        <tr>
          <th><?= __('店铺等级：'); ?></th>
          <?php if(!empty($value['shop_grade'])){ foreach ($value['shop_grade'] as $keys=>$val){ ?>

          <td><?=$val['shop_grade_name']?><?= __('（开店费用：'); ?><?=$val['shop_grade_fee']?> <?= __('元'); ?>/<?= __('年）'); ?></td>


        </tr>
        <tr>
          <th class="w150"><?= __('开店时长：'); ?></th>
          <td><?=$value['joinin_year']?> <?= __('年'); ?></td>
        </tr>
        <tr>
          <th><?= __('店铺分类：'); ?></th>
           <?php foreach ($value['shop_class'] as $keyss=>$vals){ ?>
          <td><?=$vals['shop_class_name']?><?= __('（开店保证金：'); ?><?=$vals['shop_class_deposit']?> <?= __('元）'); ?></td>

        </tr>
        <tr>
          <th><?= __('应付总金额：'); ?></th>
          <td>    <?=$val['shop_grade_fee']*$value['joinin_year']+$vals['shop_class_deposit'] ?> <?= __('元'); ?>
            </td>
        </tr>
          <?php }}} ?>
        <tr>
          <th><?= __('经营类目：'); ?></th>
          <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" id="table_category" class="type">
              <thead>
                <tr>
                  <th><?= __('分类'); ?>1</th>
                  <th><?= __('分类'); ?>2</th>
                  <th><?= __('分类'); ?>3</th>
                  <th><?= __('分类'); ?>4</th>
                  <th><?= __('比例'); ?></th>
                </tr>
              </thead>
              <tbody>
              <?php if(!empty($value["classbind"]['items']['product_parent_name'])){ foreach($value["classbind"]['items']['product_parent_name'] as $keys => $vals){
                  ?>
                  <tr>
                  <?php $i=0; foreach ($vals as $keyss => $valss) { ?>
                        <td><?=@$valss['cat_name']?></td>
                   <?php $i++; }?>
                  <?php if($i==1){ ?>
                        <td></td>
                        <td></td>
                        <td></td>
                  <?php }elseif($i==2){?>
                        <td></td>
                        <td></td>
                  <?php }elseif($i==3){ ?>
                         <td></td>
                    <?php }else{}?>
                  <td><?=@$value["classbind"]['items']['commission_rate'][$keys]?>%</td>
                </tr>
              <?php } }?>

                </tbody>
              </table>

          </td>
        </tr>
                <tr>
          <th><?= __('付款凭证：'); ?></th>
          <td><a nctype="nyroModal"  href="<?=$value['payment_voucher']?>" target="_blank"> <img src="<?=$value['payment_voucher']?>" alt="" /> </a></td>
        </tr>
        <tr>
          <th><?= __('付款凭证说明：'); ?></th>
          <td><?=$value['payment_voucher_explain']?></td>
        </tr>
                      </tbody>
    </table>
      </form>
    
    <form id="shop_verify-form">
	<table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
        
		<thead>
			<tr><th colspan="20"><?= __('开店信息审核'); ?></th></tr>
		</thead>
        <?php if($value['shop_status'] == 1){ ?>
		<tr>
            <th><?= __('个人基本信息审核：'); ?></th>
			<td>
				<div class="onoff">
					<?php if($value['shop_status'] == 4){ ?>
						<label for="verify_enabled1" class="cb-enable" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled1" class="cb-disable selected" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled1" name="shop_verify1" value="2" type="radio">
						<input id="verify_disabled1" name="shop_verify1" checked="checked" value="4" type="radio">
					<?php }else{ ?>
						<label for="verify_enabled1" class="cb-enable selected" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled1" class="cb-disable" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled1" name="shop_verify1" checked="checked" value="2" type="radio">
						<input id="verify_disabled1" name="shop_verify1" value="4" type="radio">
					<?php } ?>
                        
				</div> 
                <span><?= __('（包括'); ?> <?= __('个人实名信息，个人证件信息）'); ?></span>
                
			</td>
        </tr>
        <tr>
            <th><?= __('开户银行信息：'); ?></th>
            
			<td>
				<div class="onoff">
					<?php if($value['shop_status'] == 5){ ?>
						<label for="verify_enabled2" class="cb-enable" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled2" class="cb-disable selected" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled2" name="shop_verify2" value="2" type="radio">
						<input id="verify_disabled2" name="shop_verify2" checked="checked" value="5" type="radio">
					<?php }else{ ?>
						<label for="verify_enabled2" class="cb-enable selected" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled2" class="cb-disable" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled2" name="shop_verify2" checked="checked" value="2" type="radio">
						<input id="verify_disabled2" name="shop_verify2" value="5" type="radio">
					<?php } ?>
				</div>
			</td>
        </tr>
        <tr>
			<th><?= __('店铺经营信息审核：'); ?></th>
			<td>
				<div class="onoff">
					<?php if($value['shop_status'] == 6){ ?>
						<label for="verify_enabled3" class="cb-enable" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled3" class="cb-disable selected" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled3" name="shop_verify3" value="2" type="radio">
						<input id="verify_disabled3" name="shop_verify3" checked="checked" value="6" type="radio">
					<?php }else{ ?>
						<label for="verify_enabled3" class="cb-enable selected" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled3" class="cb-disable" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled3" name="shop_verify3" checked="checked" value="2" type="radio">
						<input id="verify_disabled3" name="shop_verify3" value="6" type="radio">
					<?php } ?>
				</div>
			</td>
		</tr>
        <?php }else if(($value['shop_status'] == 7 || $value['shop_status'] == 2 ) && $value['shop_payment'] == 1){ ?>
        <tr>
			<th><?= __('付款信息审核：'); ?></th>
			<td>
				<div class="onoff">
					<?php if($value['shop_status'] == 7){ ?>
						<label for="verify_enabled4" class="cb-enable" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled4" class="cb-disable selected" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled4" name="shop_verify4" value="3" type="radio">
						<input id="verify_disabled4" name="shop_verify4" checked="checked" value="7" type="radio">
					<?php }else{ ?>
						<label for="verify_enabled4" class="cb-enable selected" title="<?= __('通过'); ?>"><?= __('通过'); ?></label>
						<label for="verify_disabled4" class="cb-disable" title="<?= __('拒绝'); ?>"><?= __('拒绝'); ?></label>
						<input id="verify_enabled4" name="shop_verify4" checked="checked" value="3" type="radio">
						<input id="verify_disabled4" name="shop_verify4" value="7" type="radio">
					<?php } ?>
				</div>
			</td>
		</tr>
        <?php }else{ ?>
            <tr>
            <th><?= __('个人基本信息审核：'); ?></th>
			<td>
				
					<?php if($value['shop_status'] == 4){ ?>
						<?= __('拒绝'); ?>
					<?php }else{ ?>
						<?php if($value['shop_status'] != 3 && $value['shop_status'] != 2){ ?>
                        <?= __('待审核'); ?>
                        <?php }else{ ?>
						<?= __('通过'); ?>
                        <?php } ?>
					<?php } ?>
                        
                    <span style="color:gray;"><?= __('（包括'); ?> <?= __('个人实名信息，个人证件信息）'); ?></span>
                
			</td>
        </tr>
        <tr>
            <th><?= __('开户银行信息：'); ?></th>
            
			<td>
			
					<?php if($value['shop_status'] == 5){ ?>
						<?= __('拒绝'); ?>
					<?php }else{ ?>
						<?php if($value['shop_status'] != 3 && $value['shop_status'] != 2){ ?>
                        <?= __('待审核'); ?>
                        <?php }else{ ?>
						<?= __('通过'); ?>
                        <?php } ?>
					<?php } ?>
		
			</td>
        </tr>
        <tr>
			<th><?= __('店铺经营信息审核：'); ?></th>
			<td>
		
					<?php if($value['shop_status'] == 6){ ?>
						<?= __('拒绝'); ?>
					<?php }else{ ?>
                        <?php if($value['shop_status'] != 3 && $value['shop_status'] != 2){ ?>
                        <?= __('待审核'); ?>
                        <?php }else{ ?>
						<?= __('通过'); ?>
                        <?php } ?>
					<?php } ?>
			
			</td>
		</tr>
        <tr>
			<th><?= __('付款信息审核：'); ?></th>
			<td>
			
					<?php if($value['shop_status'] == 7){ ?>
						<?= __('拒绝'); ?>
					<?php }else{ ?>
                        <?php if($value['shop_status'] != 3){ ?>
                        <?= __('待审核'); ?>
                        <?php }else{ ?>
						<?= __('通过'); ?>
                        <?php } ?>
					<?php } ?>
			
			</td>
		</tr>
       <?php }?>
		<tr>
			<th><?= __('审核信息：'); ?></th>
			<td>
				<textarea rows="2" class="ui-input w600"  name="shop_verify_reason" id="shop_verify_reason"><?=$value['shop_verify_reason']?></textarea>
			</td>
		</tr>

        <?php if(($value['shop_status'] == 1) || (($value['shop_status'] == 2 || $value['shop_status'] == 7) && $value['shop_payment'] == 1)){ ?>
        
		<tr>
			<th><?= __('操作：'); ?></th>
			<input type="hidden" name="shop_id" id="shop_id" value="<?=$value['shop_id'] ?>" />
			<td><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></td>
		</tr>
        <?php }?>
	</table>
	</form>

    
    <?php }}?>

</div>
<script>
	$(function ()
	{
		if ($('#shop_verify-form').length > 0)
		{
			$('#shop_verify-form').validator({
				ignore: ':hidden',
				theme: 'yellow_bottom',
				timely: 1,
				stopOnError: true,
				fields: {

				},
				valid: function (form)
				{
					parent.$.dialog.confirm(__('<?= __('修改立马生效'); ?>,<?= __('是否继续？'); ?>'), function ()
						{
							Public.ajaxPost(SITE_URL + '?ctl=Supplier_Manage&met=verifyShop&typ=json', $('#shop_verify-form').serialize(), function (data)
							{
								if (data.status == 200)
								{
									parent.Public.tips({content: '<?= __('修改操作成功！'); ?>'});
									window.location.reload();
								}
								else
								{
									parent.Public.tips({type: 1, content: data.msg || '<?= __('操作无法成功，请稍后重试！'); ?>'});
									window.location.reload();
								}
							});
						},
						function ()
						{

						});
				}
			}).on("click", "a.submit-btn", function (e)
			{
				$(e.delegateTarget).trigger("validate");
			});
		}
	});
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>