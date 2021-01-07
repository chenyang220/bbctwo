<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link href="<?=$this->view->css?>/complain.css" rel="stylesheet" type="text/css">
</head>
<body class="<?=$skin?>">
<div class="manage-wrap">
    <div class="ncap-form-default" style="padding-top:0px;">
    <form id="voucher_t_info" action="" method="post">
    <input type="hidden" name="bargain_id" id="bargain_id" value="<?=($data['bargain_id'])?>">
        <dl class="row">
             <dt class="tit"><?= __('参与商品：'); ?></dt>
             <dd class="opt"><img width="80" src="<?=($data['goods_image'])?>"></dd>
        </dl>
        <dl class="row">
            <dt class="tit"><?= __('商品名称：'); ?></dt>
            <dd class="opt"><span><?= ($data['goods_name']) ?></span></dd>
        </dl>
        <dl class="row">
            <dt class="tit"><?= __('活动时间：'); ?></dt>
            <dd class="opt"><span><?= ($data['start_time']) ?></span></dd>
        </dl>
        <dl class="row">
            <dt class="tit"><?= __('商品原价：'); ?></dt>
            <dd class="opt"><span><?= ($data['end_time']) ?></span></dd>
        </dl>
        <dl class="row">
            <dt class="tit"><?= __('商品底价：'); ?></dt>
            <dd class="opt"><span><?= ($data['bargain_price']) ?></span></dd>
        </dl>
        <dl class="row">
            <dt class="tit"><?= __('商品库存：'); ?></dt>
            <dd class="opt"><span><?= ($data['goods_stock']) ?></span></dd>
        </dl>
        <dl class="row">
            <dt class="tit"><?= __('砍价库存：'); ?></dt>
            <dd class="opt"><span><?= ($data['bargain_stock']) ?></span></dd>
        </dl>
        <dl class="row">
            <dt class="tit"><?= __('砍价规则：'); ?></dt>
            <dd class="opt">
                <?php if($data['bargain_type'] == 1){ ?>
                    <span><?= __('共'); ?><?= ($data['bargain_num_price']) ?><?= __('刀砍至底价'); ?></span>
                <?php }else{ ?>
                    <span><?= __('每人最多可砍'); ?><?= ($data['bargain_num_price']) ?><?= __('元'); ?></span>
                <?php } ?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit"><?= __('活动分享描述：'); ?></dt>
            <dd class="opt"><span><?= ($data['bargain_desc']) ?></span></dd>
        </dl>
    </form>
    </div>
</div>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>