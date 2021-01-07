<?php if (!defined('ROOT_PATH')){exit('No Permission');}
$chain_menu = include_once INI_PATH . '/chain_menu.ini.php';
$Chain_BaseModel = new Chain_BaseModel();
$chain_base = $Chain_BaseModel->getOne(Perm::$chainId);
$chain_area[]=$chain_base['chain_province'];
$chain_area[]=$chain_base['chain_city'];
$chain_area[]=$chain_base['chain_county'];
$chain_base['chain_area']=implode(' ',$chain_area);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="renderer" content="webkit|ie-stand|ie-comp" />
    <meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="<?php if($this->description){?><?=$this->description ?><?php }?>" />
    <meta name="Keywords" content="<?php if($this->keyword){?><?=$this->keyword ?><?php }?>" />
    <title><?php if($this->title){?><?=$this->title ?><?php }else{?><?= Web_ConfigModel::value('site_name') ?><?php }?></title>
    <link href="<?= $this->view->css?>/seller.css" rel="stylesheet">
    <?php if ($_COOKIE['lang_selected']=='en_US') { ?>
        <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/english.css"/>
    <?php } ?>
    <link href="<?= $this->view->css?>/iconfont/iconfont.css" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css?>/ztree.css" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css?>/base.css" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css?>/tips.css" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css" rel="stylesheet">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">    
    <script type="text/javascript" src="<?= $this->view->js_com?>/jquery.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com?>/plugins/jquery.cookie.js"></script>
    <link href="<?= $this->view->css?>/select2.min.css" rel="stylesheet">
    <script src="<?= $this->view->js ?>/select2.min.js"></script>

    <script type="text/javascript">
        var BASE_URL = "<?=Yf_Registry::get('base_url')?>";
        var SITE_URL = "<?=Yf_Registry::get('url')?>";
        var INDEX_PAGE = "<?=Yf_Registry::get('index_page')?>";
        var STATIC_URL = "<?=Yf_Registry::get('static_url')?>";
        var PAYCENTER_URL = "<?=Yf_Registry::get('paycenter_api_url')?>";
        var UCENTER_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";

        var DOMAIN = document.domain;
        var WDURL = "";
        var SCHEME = "default";
    
        var SYSTEM = SYSTEM || {};
        SYSTEM.skin = 'green';
        SYSTEM.isAdmin = true;
        SYSTEM.siExpired = false;
    </script>
    <script type="text/javascript" src="<?=$this->view->js?>/common.js"></script>
    <?php
    include $this->view->getTplPath() . '/' . 'translatejs.php';
    ?>
</head>
<body>
    <aside class="header" id="nav-sidebar">
        <div class="clearfix aside-first-sidebar">
            <div class="logo">
                <a href="index.php?ctl=Chain_Goods&met=goods&typ=e">
                    <div class="team-logo" style="background-image:url(<?php if(!empty($this->web['seller_logo'])){echo $this->web['seller_logo'];}?>)"></div>
                </a>
            </div>
            <div class="aside-nav-box">
                <ul class="nav">
                   <?php
                    foreach ($chain_menu as $menu_row)
                    {
                        ?>
                        <li class="<?= ($menu_row['menu_id'] == $level_row[1]) ? 'cur bgf' : '' ?>">
                            <a class="dropdown-toggle"
                               href="<?= sprintf('%s?ctl=%s&met=%s&typ=e&%s', Yf_Registry::get('url'), $menu_row['menu_url_ctl'], $menu_row['menu_url_met'], $menu_row['menu_url_parem']); ?>">
                                <?= $menu_row['menu_name'] ?><i></i>
                            </a>
                        </li>
                    <?php
                    }
                    ?> 
                </ul>
            </div>
        </div>
    </aside>
    <div class="main seller-main-container">
        <div class="clearfix seller-inside-box">
            <div class="inside-page-help">
                <div class="help-container-head">
                    <i class="iconfont icon-help"></i><span>帮助和服务</span>
                </div>
                <div class="help-container-body">
                    <?php if ('Chain_Order' == $ctl && 'index' == $met) { ?>
                        <div class="help-body-title">帮助中心</div>
                        <div class="help-body-content">
                            <ul class="help-items">
                                <li><?=__('1、该列表可以查看待自提和已经自提的订单，对于到门店付款的自提订单，请确保收到款后再进行自提出货操作。')?></li>
                            </ul>
                        </div>
                        <div class="help-body-split-line"></div>
                    <?php } else if('Chain_Goods' == $ctl && 'goods' == $met) { ?>
                        <div class="help-body-title">帮助中心</div>
                        <div class="help-body-content">
                            <ul class="help-items">
                               <li><?=__('1、根据线上在售商品列表内容设置门店库存量；门店库存默认值为“0”时，该商品详情页面“门店自提”选项将不会出现您的门店信息，只有当您按所在门店的实际库存情况与线上商品对照设置库存后，才可作为线上销售门店自取点候选。')?></li>
                                <li><?=__('2、选择“库存设置”按钮，如该商品具有多项规格值，请根据规格值内容进行逐一“门店库存”设置，并保存提交。')?></li>
                                <li><?=__('3、如您的门店某商品线下销售引起库存不足，请及时手动调整该商品的库存量，以免消费者在线上下单后到门店自提时产生交易纠纷。')?></li>
                                <li><?=__('4、特殊商品不能设置为门店自提商品（如：虚拟商品）。')?></li>
                            </ul>
                        </div>
                        <div class="help-body-split-line"></div>
                    <?php } ?>
                   <div class="help-body-title">版权信息</div>
                   <div class="help-copyright-content">
                       <p class="copyright">
                           <?php echo Web_ConfigModel::value('copyright'); ?>
                       </p>
                       <p class="statistics_code"><?php echo Web_ConfigModel::value('icp_number') ?></p>
                   </div>
               </div>
            </div>
            <div class="right-layout iblock top pl0">
                <header class="seller-header wp100 pr236 chain-seller-header">
                   <span class="iblock seller-name"><?=__('门店管理')?></span>
                   <div class="iblock seller-user">
                        <em class="img-box"><img class="cter" src="<?=$chain_base['chain_img']?>" alt=""></em>
                        <span><?=$chain_base['chain_name']?></span>
                   </div>
                </header>
                <div class="content">
                    
               <!--  </div>
            </div>
       </div>
       
    </div> -->
<!-- <div class="header">
    <div class="wrapper fn-clear clearfix">
        <div class="ncsc-admin">
            <dl class="ncsc-admin-store">
                <dt class=""><?=$chain_base['chain_name']?></dt>
                <dd class=""><?=$chain_base['chain_area']?></dd>
                <dd class=""><?=$chain_base['chain_mobile']?></dd>
            </dl>
            <div class="pic"><img src="<?=$chain_base['chain_img']?>"></div>
        </div>
    </div>
</div> -->