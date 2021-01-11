<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
    <style>
        .define_detail {
            text-align: left;
            padding: 15px;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/personalstores.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/goods-detail.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/Group-integral.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css">
    <script type="text/javascript" src="<?= $this->view->js ?>/tuangou-index.js"></script>
    <div class="wrap clearfix zizhi-detail-module">
        <h2 class="weight"><?= __('平台经营者相关资质信息') ?></h2>
        <h3><?= __('根据相关法律法规要求，经营者相关资质信息公示如下') ?>：</h3>
		<div class="zizhi-det-text">
			<p><span><?= __('企业名称') ?>：</span><em><?= $data['shop_company_name'] ?></em></p>
			<p><span><?= __('营业执照注册号') ?>：</span><em><?= $data['business_id'] ?></em></p>
			<p><span><?= __('法定代表人姓名') ?>：</span><em><?= $data['legal_person'] ?></em></p>
			<p><span><?= __('营业执照所在地') ?>：</span><em><?= $data['business_license_location'] ?></em></p>
			<p><span><?= __('企业注册资金') ?>：</span><em><?= $data['company_registered_capital'] ?></em></p>
			<p><span><?= __('营业执照有效期') ?>：</span><em><?= $data['business_licence_start'] ?><?= __('至') ?><?= $data['business_licence_end'] ?></em></p>
			<p><span><?= __('公司地址') ?>：</span><em><?= $data['shop_company_address'] ?><?= $data['company_address_detail'] ?></em></p>
			<p><span><?= __('营业执照经营范围') ?>：</span><em><?= $data['business_sphere'] ?></em></p>
			<img class="wp100 mt20" src="<?= $data['business_license_electronic'] ?>">
		</div>
    </div>


<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>