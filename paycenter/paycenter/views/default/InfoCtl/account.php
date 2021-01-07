<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
<style>
    .xgwigth{
        text-align: right;
        width: 50%;
    }
    .account_table tr td {
        text-align: center;
    }
</style>

<div class="main_cont wrap clearfix">
	<div class="account_left fl">
		<div class="account_mes">
			<h4><?=__('基本信息')?></h4>
			<table class="account_table">
				<tbody>
				<tr>
					<td><?=__('用户名称')?></td>
					<td><div class="pay-information <?php if($user_info['user_nickname']){ echo "active";}?>"><?=$user_info['user_nickname']?></div></td>
					<td class="account_ahref"><a href="<?=Yf_Registry::get('ucenter_api_url')?>?ctl=User&met=getUserInfo"><?=__('修改信息')?></a></td>
				</tr>
				<tr>
					<td><?=__('手机号码')?></td>
					<td><div class="pay-information <?php if($listarr['details'][$listarr['user_name']]['user_mobile']){ echo "active";}?>"><?=$listarr['details'][$listarr['user_name']]['user_mobile']?></div></td>
					<td class="account_ahref"></td>
				</tr>
				<tr>
					<td><?=__('绑定邮箱')?></td>
					<td><div class="pay-information <?php if($listarr['details'][$listarr['user_name']]['user_email']){ echo "active";}?>"><?=$listarr['details'][$listarr['user_name']]['user_email']?></div></td>
					<td class="account_ahref"></td>
				</tr>
				<tr>
					<td><?=__('所在地区')?></td>
					<td><div class="pay-information <?php if($listarr['details'][$listarr['user_name']]['user_area']){ echo "active";}?>"><?=$listarr['details'][$listarr['user_name']]['user_area']?></div></td>
					<td class="account_ahref"></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="account_mes">
			<h4><?=__('支付密码')?></h4>
			<table class="account_table">
				<tbody>
				<tr>
					<td><?=__('支付密码')?></td>
                    <?php if(!empty($user_base_info) && !empty($user_base_info['user_pay_passwd'])){ ?>
                        <td class="xgwigth"><?=__('安全级别：')?><em><?=__('高')?></em></td>
					   <td class="account_ahref"><a href="#" onclick="checkCer()"><?=__('修改')?></a>|<a href="#" onclick="checkCer()"><?=__('找回支付密码')?></a></td>
                    <?php }else{?>
                        <td class="account_ahref"><a href="#" onclick="checkCer()"><?=__('设置')?></a>
                    <?php }?>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="account_mes">
			<h4><?=__('实名认证')?></h4>
			<table class="account_table">
				<tbody>
				<tr>
					<td><?=__('真实姓名')?></td>
					<td><div  class="pay-information <?php if($user_info['user_realname']){ echo "active";}?>"><?=$user_info['user_realname']?></div></td>
					<td class="account_ahref">
                        <?php if($user_info['user_identity_statu'] == 0){?>
                            <a href="<?= Yf_Registry::get('url') ?>?ctl=Info&met=certification"><?=__('去实名认证')?></a>
                        <?php }elseif($user_info['user_identity_statu'] == 1){?>
                                <?=__('待审核')?><a href="<?= Yf_Registry::get('url') ?>?ctl=Info&met=certification"><?=__('重填实名认证')?></a>
                        <?php }elseif($user_info['user_identity_statu'] == 2){?>
                                <a href="<?= Yf_Registry::get('url') ?>?ctl=Info&met=certification" ><?=__('修改实名认证')?></a>
                        <?php }else{?>
                                <?=__('认证失败')?><a href="<?= Yf_Registry::get('url') ?>?ctl=Info&met=certification"><?=__('去实名认证')?></a>
                        <?php }?>
                    </td>
				</tr>
				<tr>
					<td><?=__('证件类型')?></td>
					<td><div class="pay-information active"><?php if($user_info['user_identity_type']==1){?><?=__('身份证')?><?php }elseif($user_info['user_identity_type']==2){?><?=__('护照')?><?php }else{ ?><?=__('军官证')?><?php }?></div></td>
					<td class="account_ahref"></td>
				</tr>
				<tr>
					<td><?=__('证件号码')?></td>
					<td><div class="pay-information <?php if($user_info['user_identity_card']){ echo "active";}?>"><?=$user_info['user_identity_card']?></div></td>
					<td class="account_ahref"></td>
				</tr>
				<tr>
                  <td class="check_name"><?=__('正面照')?></td>
                  <td>
                    <div class="user-avatar"><span><img  id="image_img"  src="<?=$user_info['user_identity_face_logo'] ?>" width="" height="151" nc_type="avatar"></span></div>
                  </td>
				  <td class="account_ahref"></td>
                </tr>
				<tr>
                  <td class="check_name"><?=__('背面照')?></td>
                  <td>
                    <div class="user-avatar"><span><img  id="image_img"  src="<?=$user_info['user_identity_font_logo'] ?>" width="" height="151" nc_type="avatar"></span></div>
                  </td>
				  <td class="account_ahref"></td>
                </tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="account_right fr">
		<div class="account_right_con">
			<ul class="cert_instructions">
				<li>
					<h5><?=__('什么是实名认证？')?></h5>
					<p><?=__('实名认证，是利用其国家级身份认证平台“身份通实名认证平台”推出的实名身份认证服务。在Pay Center平台进行实名认证无需繁琐步骤，只需如实填写您的姓名和身份证号，就能完成实名认证。')?></p>
				</li>
				<li>
					<h5><?=__('为什么要实名认证')?></h5>
					<p><?=__('只有通过身份通实名身份认证的用户，才能使用Pay Center服务，从而实现真正的、全面的实名制平台。为保护用户隐私，用户之间只有在得到对方授权的情况下才可以交换实名认证信息。为保护用户信息，用户提供的身份证信息，将直接传输到“全国公民身份信息系统”系统数据库中，并即时返回认证结果，Pay Center并不保留用户的身份证号码。')?></p>
				</li>
				<li>
					<h5><?=__('温馨提示')?></h5>
					<p><?=__('通过实名认证表示该用户提交了真实存在的身份证，但我们无法完全确认该证件是否为其本人持有，您还需要通过和对方交换实名信息来获取对方全名及身份证照片，并与对方照片或本人进行比对，核实对方是否该身份证的持有人。实名认证也不能代表除身份证信息外的其他信息是否真实。因此，Pay Center提醒广大家庭用户在使用过程中，须保持谨慎理性，增强防范意识，避免产生经济等其他往来。')?></p>
				</li>
			</ul>
		</div>
	</div>
</div>


<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
<script>
	function checkCer(){

		if("<?php echo $user_status_info['user_identity_statu_con']?>" == "未认证"){

			Public.tips.alert("<?= __('根据国家法规对支付服务实名制的要求，您需要进行身份信息完善，请前去实名认证'); ?>");

			setTimeout(function(){
				window.location.href="<?php echo $url ?>";

			},2000);
		}else if("<?php echo $user_status_info['user_identity_statu_con']?>" == "待审核"){
            Public.tips.alert("<?= __('您的实名认证待审核，请通过后再试'); ?>");
            return false;
        }else{
			window.location.href="<?php echo $url ?>";
		}


	}


</script>
