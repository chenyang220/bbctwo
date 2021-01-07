<?php if($_COOKIE['SHOP_ID']){ ?>
<span><?=__('欢迎来到')?><?=$shop_base['shop_name'] ?></span>
<?php }else{ ?>
<span><?=__('欢迎来到')?><?=Web_ConfigModel::value("site_name") ?></span>
<?php }?>

<?php
//PLUS会员处理 @nsy 2019-01-18
$hidden = 'hidden';//默认plus标识不显示
$try="";//试用标识
if($this->userInfo['plus']){
    $nowTimes = time();
    $endDate = $this->userInfo['plus']['end_date'];
    switch ($this->userInfo['plus']['user_status']){
        case 1://试用会员，有效期则显示
            if($endDate>$nowTimes){
                $hidden = '';
                $try="try";
            }
            break;
        case 2://正式会员，有效期则显示
            ($endDate>$nowTimes)&& $hidden = '';
            break;
        default:
            //过期会员
            break;
    }
}
?>
<script>
    $(function(){
        var url = location.href;
        if (url.indexOf('Plus') >=1) {
            $(".plus-back-index").removeClass("hide");
        }else{
            $(".plus-back-index").addClass("hide");
        }
    })
    
</script>
<?php echo empty($this->userInfo) ? '<a href="' . Yf_Registry::get('url') . '?ctl=Login&met=login"> '.__('请登录').' </a> <a href="' . Yf_Registry::get('url') . '?ctl=Login&met=reg">'.__('免费注册').' </a> ' : ' <a href="./index.php?ctl=Buyer_Index&met=index" class="user-name"> ' . $this->userInfo['user_name'] . ' </a> <b class="plus-login-logo '.$hidden.$try.'"></b> '.'<a href="' . Yf_Registry::get('url') . '?ctl=Login&met=loginout"> ['.__('退出').' ]</a><a class="plus-back-index hide" href="'.Yf_Registry::get('url').'"><i class="iconfont icon-shouye"></i><em>返回首页</em></a>' ?>

<?php
$d = ob_get_contents();
ob_end_clean();
ob_start();
$data[] = $d;

$shop_id = @Perm::$shopId;
$user_id = Perm::$userId;
//查询是否上门店账号
$chanin_user_model = new Chain_UserModel();
$chain_user = current($chanin_user_model->getByWhere(array('user_id'=>$user_id)))['chain_user_id'];
$shop_supplier_model = new Shop_SupplierModel();
$shop_base_model = new Shop_BaseModel();
$shop_base = current($shop_base_model->getByWhere(array("shop_id"=>$shop_id)));
$shop_type = $shop_base['shop_type']?:1;
?>

<div class="tright_content">
    <p class="user_head">
		<a href="./index.php?ctl=Buyer_Index&met=index">
			<?php if(@Perm::$userId){ ?>	
				<img src="<?= Yf_Registry::get('ucenter_api_url') ?>?ctl=Index&met=img&user_id=<?= @Perm::$userId ?>"/> 
			<?php } else { ?>
				<img src="<?php echo Yf_Registry::get('base_url').'/image.php/shop/data/upload/pic/uva.png';?>"/> 
			<?php } ?>
		</a>
	</p>
	<p class="hi"><span><?=__('Hi~你好！')?></span></p>
	<?php echo empty($this->userInfo) ? '<p><a href="' . Yf_Registry::get('url') . '?ctl=Login&met=login" class="login">
	<span class="iconfont icon-icondenglu"></span>'.__('请登录').'</a></p><p><a class="register" href="' . Yf_Registry::get('url') . '?ctl=Login&met=reg"><i class="iconfont icon-icoedit"></i>'.__('免费注册').'</a></p>' : '<p><a class="ellipsis" href="./index.php?ctl=Buyer_Index&met=index">' . $this->userInfo['user_name'] . '</a></p>' ?>
<!--    --><?php //var_dump($flag)  ?>
	<div class="prom">
		<p><span class="iconfont icon-tuihuobaozhang"></span><?=__('退货保障')?></p>
		<p><span class="iconfont icon-shandiantuikuan"></span><?=__('极速退款')?></p>
	</div>
    <div class="cooperation">
<!--        <h3><a href="index.php?ctl=Seller_Shop_Settled&met=index" class="apply">--><?//=__('招商入驻')?><!--</a></h3>-->
      <?php if($shop_type == 1){ ?>
            <p><a href="index.php?ctl=Seller_Shop_Settled&met=index" class="apply"><img src="<?= $this->view->img ?>/icon_ruzhu.png"/></a></p>
            <?php if(@Perm::$shopId || $chain_user){ ?>
                <p><a href="index.php?ctl=Seller_Index&met=index" class="apply"><?=__('进入商家中心')?></a></p>
            <?php }else{ ?>
                <p><a href="index.php?ctl=Seller_Shop_Settled&met=index" class="apply"><?=__('申请商家入驻')?></a></p>
            <?php } ?>
       <?php }else{ ?>
            <p><a href="index.php?ctl=Seller_Supplier_Settled&met=index" class="apply"><img src="<?= $this->view->img ?>/icon_ruzhu.png"/></a></p>
            <?php if(@Perm::$shopId || $chain_user){ ?>
                <p><a href="index.php?ctl=Seller_Index&met=index" class="apply"><?=__('进入供应商中心')?></a></p>
            <?php }else{ ?>
                <p><a href="index.php?ctl=Seller_Seller_Supplier_Settled&met=index" class="apply"><?=__('申请供应商入驻')?></a></p>
            <?php } ?>
        <?php } ?> 
    </div>
</div>


<?php
$d = ob_get_contents();
ob_end_clean();
ob_start();

$data[] = $d;
?>
