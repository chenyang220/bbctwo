<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link href="<?=$this->view->css?>/explore.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link rel="stylesheet" href="<?=$this->view->css?>/swiper.css">
<link rel="stylesheet" href="<?=$this->view->css?>/iconfont/iconfont.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>

<style>
    .webuploader-pick{ padding:1px; }

</style>
</head>
<body class="<?=$skin?>">
<div class="">
    <form method="post" enctype="multipart/form-data" id="user-edit-form" name="form">
        <input type="hidden" name="report_id" value="<?=($data['report']['report_id'])?>"/>
        <input type="hidden" name="explore_id" value="<?=($data['explore']['explore_base']['explore_id'])?>"/>
        <input type="hidden" name="count" id="count" value="<?=($data['count'])?>"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                     <strong class="red mr10 fz14 normal"><?= __('举报内容'); ?></strong>
                </dt>
                <dd class="opt"><span></span> </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('作者信息：'); ?></label>
                </dt>
                <dd class="opt"><img class="touserlogo iblock mb0 vt" src="<?=$data['explore']['user_info']['user_logo'];?>"><span class="iblock vt ml10"><?=$data['explore']['user_info']['user_account'];?> </span></dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('发表时间：'); ?></label>
                </dt>
                <dd class="opt"><span class=""><?=$data['explore']['explore_base']['explore_create_date'];?></span> </dd>
            </dl>          
           <dl class="row">
                <dt class="tit">
                    <label></label>
                </dt>
                <dd class="opt">
                    <div class="complaint-goods-module">
                        <div class="cf">
                             <div class="swiper-container complaint-swiper fl">
                                <ul class="swiper-wrapper">
                                    <?php foreach ($data['explore']['explore_images'] as $imgk => $imgv){ ?>
                                    <li class="swiper-slide">
                                        <em class="img-box">
                                            <img src="<?=$imgv['images_url'];?>" alt="">
                                        </em>
                                    </li>
                                    <?php }?>
                                </ul>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-next"><i class="iconfont icon-right"></i></div>
                                <div class="swiper-button-prev"><i class="iconfont icon-left"></i></div>
                            </div>
                            <div class="fl complaint-goods-text">
                                <strong class="one-overflow"><?=$data['explore']['explore_base']['explore_title'];?></strong>
                                <p><?=$data['explore']['explore_base']['explore_content'];?></p>
                                <div class="complaint-goods-label">
                                   <?php foreach ($data['explore']['explore_base']['explore_lable'] as $key => $val) { ?>
                                        <span>#<?=$val['lable_content']; ?></span>
                                    <?php  } ?> 
                                </div>
                            </div>
                        </div>
                         <?php if ($data['explore']['goods']['goods']) { ?>
                            <div class="swiper-container swiper-rel-goods-swiper">
                                <ul class="complaint-goods-items cf swiper-wrapper">
                                    <?php foreach ($data['explore']['goods']['goods'] as $key => $val) { ?>
                                    <li class="swiper-slide">
                                        <a href="<?= Yf_Registry::get('shop_api_url') ?>?ctl=Goods_Goods&met=goods&type=goods&cid=<?= ($val['goods_common_id']) ?>" target="view_window">
                                            <em class="img-box"><img src="<?=$val['common_image']; ?>"></em>
                                            <span class="block more-overflow"><?=$val['common_name']; ?></span>
                                            <strong class="block red">￥<?=$val['common_price']; ?></strong>
                                        </a>
                                    </li>
                                    <?php  } ?>

                                </ul>
                                <div class="swiper-button-next swiper-button-next1"><i class="iconfont icon-right"></i></div>
                                <div class="swiper-button-prev swiper-button-prev1"><i class="iconfont icon-left"></i></div>
                            </div>
                        <?php  } ?>
                    </div>

                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                  <label><?= __('举报人：'); ?></label>
                </dt>
                <dd class="opt"><span><?=$data['report']['user_name'];?></span></dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                  <label><?= __('举报原因：'); ?></label>
                </dt>
                <dd class="opt"><span><?=$data['report']['report_reason'];?></span> </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                  <label><?= __('举报时间：'); ?></label>
                </dt>
                <dd class="opt"><span><?=$data['report']['report_time'];?></span> </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                      <label><i class="red">*</i>&nbsp;<?= __('举报处理：'); ?></label>
                </dt>
                <dd class="opt">
                  <input type="radio" id="report_status0" name="report_status" value="1">
                  <label class="mr30" for="report_status0"><?= __('审核通过并下架该心得'); ?></label>
                  <input type="radio" id="report_status1" name="report_status" value="2">
                  <label for="report_status1"><?= __('审核不通过'); ?></label>
                  <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                  <label><?= __('备注：'); ?></label>
                </dt>
                <dd class="opt"><textarea maxlength="100" name="report_handle" class="ui-input w346" id=""></textarea></dd>
            </dl>
        </div>
        <!--弹窗层-->
        <div class="timeSelectBox">
            <div class="TeStSon">
                <p class="TeStSon_p">系统检测到当前心得还有其他<nobr><?=($data['count'])?></nobr>人举报，系统会自动处理此<nobr><?=($data['count'])?></nobr>人的举报，举报状态为：已处理，举报结果为：审核通过并下架!</p>
                <button class="TeStSon_bn"><?= __('我知道了'); ?></button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/Mb/explore/explore.js" charset="utf-8"></script>

<script src="<?=$this->view->js?>/swiper.min.js"></script>
<script>
     var swiper = new Swiper('.swiper-container', {
        pagination : '.swiper-pagination',
        paginationType : 'fraction',
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
    });
    var swiper = new Swiper('.swiper-rel-goods-swiper', {
        slidesPerView:4.3,
          navigation: {
            nextEl: '.swiper-button-next1',
            prevEl: '.swiper-button-prev1',
          },
    });
     

</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>