<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 15:29
 */
if (!defined('ROOT_PATH')) {exit('No Permission');}?>
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
<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __('积分兑换'); ?> - <?= __('兑换详情'); ?></h3>
                <h5><?= __('积分兑换礼品详情'); ?></h5>
            </div>
        </div>
    </div>

    <div class="ncap-order-style">
        <div class="ncap-order-flow">
            <ol class="num3">
                <li class="<?=$data['points_orderstate'] ==1 ? 'current':''?>">
                    <h5><?= __('提交兑换'); ?></h5>
                    <i class="fa fa-arrow-circle-right"></i>
                    <time><?=@($data['points_addtime'])?></time>
                </li>
                <li class="<?=$data['points_orderstate'] ==2 ? 'current':''?>">
                    <h5><?= __('礼品发货'); ?></h5>
                    <i class="fa fa-arrow-circle-right"></i>
                    <time><?=@($data['points_shippingtime'])?></time>
                </li>
                <li class="<?=$data['points_orderstate'] ==3 ? 'current':''?>">
                    <h5><?= __('确认收货'); ?></h5>
                    <time><?=@($data['points_finnshedtime'])?></time>
                </li>
            </ol>
        </div>
        <div class="ncap-order-details">
            <ul class="tabs-nav">
                <li class="current"><a href="javascript:void(0);"><?= __('兑换详情'); ?></a></li>
            </ul>
            <div class="tabs-panels">
                <div class="misc-info">
                    <h4><?= __('兑换信息'); ?></h4>
                    <dl>
                        <dt><?= __('兑换单号：'); ?></dt>
                        <dd><?=@(sprintf("%.0f",$data['points_order_rid']))?></dd>
                        <dt><?= __('状态：'); ?></dt>
                        <dd><?=@($data['points_orderstate_label'])?></dd>
                        <dt><?= __('兑换时间：'); ?></dt>
                        <dd><?=@($data['points_addtime'])?></dd>
                    </dl>
                </div>
                <div class="addr-note">
                    <h4><?= __('购买'); ?>/<?= __('收货方信息'); ?></h4>
                    <dl>
                        <dt><?= __('会员名称：'); ?></dt>
                        <dd><?=@($data['points_buyername'])?></dd>
                        <dt><?= __('会员'); ?>Email<?= __('：'); ?></dt>
                        <dd><?=@($data['points_buyeremail'])?></dd>
                    </dl>
                    <dl>
                        <dt>收货人：</dt>
                        <dd><em><?=@($data['points_truename'])?></em> <em class="fr phone"><?=@($data['points_mobphone'])?></em></dd>
                    </dl>
                    <dl>
                        <dt><?= __('收货地址：'); ?></dt>
                        <dd><?=@($data['points_address'])?></dd>
                    </dl>
                    <dl>
                        <dt><?= __('留言：'); ?></dt>
                        <dd><?=@($data['points_ordermessage'])?></dd>
                    </dl>
                </div>
                <?php if($data['points_orderstate'] == 2 || $data['points_orderstate'] == 3){ ?>
                <div class="contact-info">
                    <h4><?= __('发货信息'); ?></h4>
                    <dl>
                        <dt><?= __('物流公司：'); ?></dt>
                        <dd><?=@($data['points_logistics'])?></dd>
                        <dt><?= __('物流单号：'); ?></dt>
                        <dd><?=@($data['points_shippingcode'])?></dd>
                        <dt><?= __('发货时间：'); ?></dt>
                        <dd><?=@($data['points_shippingtime'])?></dd>
                    </dl>
                </div>
                <?php } ?>

                <?php  if($data['points_order_goods_list']){  ?>
                <div class="goods-info">
                    <h4><?= __('礼品信息'); ?></h4>
                    <table>
                        <thead>
                        <tr>
                            <th colspan="2"><?= __('兑换礼品'); ?></th>
                            <th><?= __('兑换积分'); ?></th>
                            <th><?= __('兑换数量'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data['points_order_goods_list'] as $key=>$value){  ?>
                        <tr>
                            <td class="w30"><div class="goods-thumb"><a href="" target="_blank" class="order_info_pic"> <img src="<?=$value['points_goodsimage']?>"></a></div></td>
                            <td style="text-align: left;"><a href="<?=Yf_Registry::get('shop_api_url')?>?ctl=Points&met=detail&id=<?=$value['points_goodsid']?>" target="_blank"><?=$value['points_goodsname']?></a></td>
                            <td class="w150"><?=$value['points_goodspoints']?></td>
                            <td class="w150"><?=$value['points_goodsnum']?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="total-amount">
                        <h3><?= __('兑换积分：'); ?><strong class="red_common"><?=@($data['points_allpoints'])?></strong></h3>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
