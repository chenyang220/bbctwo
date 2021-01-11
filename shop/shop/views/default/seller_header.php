<?php
if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
$seller_menu = include_once INI_PATH . '/seller_menu.ini.php';
$shop_base = current($this->shopBase);
if ($shop_base['shop_type'] == 2) {
    unset($seller_menu['12000']['sub']['120008']);
    // unset($seller_menu['13000']);
    unset($seller_menu['14000']['sub']['140011']);
    unset($seller_menu['14000']['sub']['140013']);
    unset($seller_menu['14000']['sub']['140014']);
}
$User_InfoModel = new User_InfoModel();
$user_id = Perm::$userId;
$user_info = $User_InfoModel->getOne($user_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-stand|ie-comp"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="<?php if ($this->description) { ?><?= $this->description ?><?php } ?>"/>
    <meta name="Keywords" content="<?php if ($this->keyword) { ?><?= $this->keyword ?><?php } ?>"/>
    <title><?php if ($this->title) { ?><?= $this->title ?><?php } else { ?><?= Web_ConfigModel::value('site_name') ?><?php } ?></title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <link href="<?= $this->view->css ?>/seller.css?ver=<?= VER ?>" rel="stylesheet">
    <?php if ($_COOKIE['lang_selected']=='en_US') { ?>
       <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/english.css"/>
    <?php } ?>
    <link href="<?= $this->view->css ?>/iconfont/iconfont.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/ztree.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css" rel="stylesheet">
    <link href="<?= $this->view->css ?>/base.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css ?>/sass/custom.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css ?>/tips.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css ?>/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $this->view->css ?>/iconfont/iconfont.css" />
    <script type="text/javascript" src="<?= $this->view->js_com ?>/jquery.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.cookie.js"></script>
    <script src="<?= $this->view->js ?>/select2.min.js"></script>
    <script type="text/javascript">
        var IM_URL = "<?=Yf_Registry::get('im_api_url')?>";
        var IM_STATU = "<?=Yf_Registry::get('im_statu')?>";
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
    <script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/decoration/common.js"></script>
    <script src="<?= $this->view->js ?>/intlTelInput.js"></script>
    <link rel="stylesheet" href="<?= $this->view->css ?>/intlTelInput.css">
    <?php
        include $this->view->getTplPath() . '/' . 'translatejs.php';
    ?>
    <style type="text/css" media="screen">
        /* 取消H5表单上下箭头 */
        /* 谷歌浏览器兼容性 */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0;
        }

        /* 火狐浏览器兼容性 */
        input[type="number"] {
            -moz-appearance: textfield;
        }
        .team-logo{
            height: 60px;
        }
   </style>
   
</head>
<body>
<?php include APP_PATH . '/alert_box.php'; ?>
<?php if (!empty($this->shopinfo)) { ?>
    <div class="shop-closed">
        <i class="iconfont icon-tanhao"></i>
        <dl>
            <dt><?= __('您的店铺已被平台关闭') ?></dt>
            <dd><?= __('关闭原因') ?>：<?= $this->shopinfo['shop_close_reason'] ?></dd>
            <dd><?= __('在此期间') ?>，<?= __('您的店铺以及商品将无法访问') ?>；<?= __('如果您有异议或申诉请及时联系平台管理') ?>。</dd>
        </dl>
    </div>
<?php } ?>
<aside class="header" id="nav-sidebar">
    <!-- 一级导航 -->
    <div class="clearfix aside-first-sidebar">
        <div class="logo">
            <?php if (@$shop_base['shop_type'] == 1) { ?>
               <a href="index.php?ctl=Seller_Index&met=index&typ=e">
                        <img class="team-logo" src="<?php if (!empty($this->web['seller_logo'])) {
                        echo $this->web['seller_logo'];
                    } ?>" alt="logo" /> 
                </a>
            <?php } else{ ?>
                 <a href="index.php?ctl=Seller_Index&met=index&typ=e&is_from=Supplier">
                        <img class="team-logo" src="<?php if (!empty($this->web['supplier_logo'])) {
                        echo $this->web['supplier_logo'];
                    } ?>" alt="logo" />
                </a>
            <?php } ?>
        </div>
        <div class="aside-nav-box">
            <ul class="nav">
                <li class="<?= Seller_Controller::$current_menu['model'] == 'index' ? 'cur bgf' : ''; ?>">
                    <?php if($shop_base['shop_type'] == 1){?>
                        <a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Index&met=index&typ=e"><?= __('概况') ?></a>
                    <?php  }else{ ?>
                       <a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Index&met=index&typ=e&is_from=Supplier"><?= __('概况') ?></a>
                    <?php  } ?>
                </li>
                <?php if (!empty(Seller_Controller::$menu) && is_array(Seller_Controller::$menu)) {
                    foreach (Seller_Controller::$menu as $key => $menu_row) {
                        if ($key === 'statistics' && !Yf_Registry::get('analytics_statu')) {
                            continue;
                        }
                        ?>
                        <li class="<?= (Seller_Controller::$current_menu['model'] == $key) ? 'cur bgf' : '' ?>">
                            <a class="dropdown-toggle" href="<?= sprintf('%s?ctl=%s&met=%s&typ=e', Yf_Registry::get('url'), $menu_row['sub'][key($menu_row['sub'])]['ctl'], $menu_row['sub'][key($menu_row['sub'])]['met']); ?>">
                                <?= __($menu_row['name']) ?>
                            </a>
                            <?php if ($menu_row['name'] == "<?=__('客服消息')?>" && $this->user_info['message'] > 0) { ?>
                                <i class="bbuyer_news"><?= $this->user_info['message'] ?></i>
                            <?php } ?>
                        </li>
                    <?php } } ?>
                <li>
                    <a href="<?= Yf_Registry::get('paycenter_api_url') ?>" target="_blank"><?= __(Yf_Registry::get('paycenter_api_name')) ?></a>
                </li>
            </ul>
            <div class="seller-nav-aside-functions">
                <a href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=index&id=<?= Perm::$shopId ?>" title="<?= __('店铺') ?>" target="_blank"><b class="b-store"></b><em>店铺</em></a>
                <a href="<?= Yf_Registry::get('ucenter_api_url') ?>?ctl=User&met=getUserInfo" title="<?= __('设置') ?>" target="_blank"><i class="iconfont icon-shezhi1"></i><em>设置</em></a>
            </div>
        </div>
        <div class="aside-shared-corner">
            <a id="js-seller-search" href="javascript:;"><i class="iconfont icon-search"></i></a><a href="<?= Yf_Registry::get('url') ?>?ctl=Seller_Message&amp;met=message&amp;typ=e&amp;"><i class="iconfont icon-messages"></i></a>
        </div>

        <!-- 搜索 -->
        <div class="seller-search-box">
            <div class="seller-search-content">
                <div class="seller-search-mask"></div>
                <div class="seller-search-input">
                    <form method="get" target="_blank" class="fz0">
                        <input type="hidden" name="ctl" value="Goods_Goods">
                        <input type="hidden" name="met" value="goodslist">
                        <input type="hidden" name="typ" value="e">
                        <input type="text" nctype="search_text" name="keywords" placeholder="<?= __('输入您想要的商城商品') ?>"
                               class="search-input-text">
                        <input type="submit" nctype="search_submit" class="search-input-btn pngFix" value="">
                    </form>
                </div>
            </div>
        </div>
        <!-- <div class="ncsc-admin">
            <div class="ncsc-admin-function">
                <a href="<?= Yf_Registry::get('url') ?>" title="<?= __('前往商城') ?>"><i class="iconfont icon-fangzi"></i></a>
                <a href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=index&id=<?= Perm::$shopId ?>" title="<?= __('前往店铺') ?>"><i class="iconfont icon-dianpu2"></i></a>
                <a href="<?= Yf_Registry::get('ucenter_api_url') ?>?ctl=User&met=getUserInfo" title="<?= __('基本信息') ?>" target="_blank"><i class="iconfont icon-banshou"></i></a>
                <a href="<?= Yf_Registry::get('url') ?>?ctl=Login&met=loginout" title="<?= __('安全退出') ?>"><i class="iconfont icon-tuichu"></i></a>
            </div>
        </div>-->
    </div> 
    <!-- 二级导航 -->
    <?php if ('Seller_Index' == $ctl && 'index' == $met) { ?>
    <?php }  else { ?>
         <?php if ($ctl == "Seller_Shop_Decoration" && $met == "decoration" && $act == "set") { ?>
        <?php } else { ?>
            <div class="left-layout top">
                <h2 class="seller-current-tit"><?= __($seller_menu[$level_row[1]]['sub'][$level_row[2]]['menu_name']) ?></h2>
                <ul>
                    <?php if($seller_menu[$level_row[1]]['sub']){ ?>
                    <?php foreach ($seller_menu[$level_row[1]]['sub'] as $menu_row) { ?>
                        <li>
                            <a class="<?= ($menu_row['menu_id'] == $level_row[2]) ? 'active' : '' ?>" href="<?= sprintf('%s?ctl=%s&met=%s&typ=e&%s', Yf_Registry::get('url'), $menu_row['menu_url_ctl'], $menu_row['menu_url_met'], $menu_row['menu_url_parem']); ?>">
                                <?= __($menu_row['menu_name']) ?>
                            </a>
                        </li>
                    <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    <?php } ?>
</aside>
<div class="main seller-main-container">
<?php if ('Seller_Index' == $ctl && 'index' == $met) {?>
    <div>
        <header class="seller-header">
             <span class="iblock seller-name">
                <?php if (@$shop_base['shop_type'] == 1) { ?>
                    <?= __('商家中心'); ?>
                <?php } else { ?>
                    <?= __('供应商中心'); ?>
                <?php } ?>
            </span>
            <div class="iblock seller-user">
                <i class="iconfont icon-shouye fz16 align-middle"></i>
                <a class="mr10 align-middle" href="<?=Yf_Registry::get('shop_api_url');?>">返回商城首页</a>
                <em class="img-box"><img class="cter" src="<?= $user_info['user_logo'] ?>" alt=""></em>
                <a class="align-middle" href="<?=Yf_Registry::get('shop_api_url');?>/index.php?ctl=Buyer_Index&met=index"><?= $user_info['user_name'] ?></a>
            </div>

        </header>
        <div class="style-container">
<?php }  else { ?>
            <div class="clearfix seller-inside-box">
               
                <div class="<?php if (!($ctl == "Seller_Shop_Decoration" && $met == "decoration" && $act == "set")) { ?>right-layout iblock top<?php } ?>"<?php if ($ctl == "Seller_Shop_Decoration" && $met == "decoration" && $act == "set") { ?> style="float:left;background: #fff;"<?php } ?>>
                    <?php if (!($ctl == "Seller_Shop_Decoration" && $met == "decoration" && $act == "set")) { ?>
                        <div class="path bgf">
                           <!--  <i class="iconfont icon-diannao"></i> -->
                            <?php if (@$shop_base['shop_type'] == 1) { ?>
                                <?= __('商家管理中心'); ?>
                            <?php } else { ?>
                                <?= __('供应商管理中心'); ?>
                            <?php } ?>
                            <i class="iconfont icon-iconjiantouyou"></i>
                            <?= __($seller_menu[$level_row[1]]['menu_name']) ?>
                            <i class="iconfont icon-iconjiantouyou"></i>
                            <?= __($seller_menu[$level_row[1]]['sub'][$level_row[2]]['menu_name']) ?>
                        </div>
                    <?php } ?>

                    <!-- 帮助中心 -->
                    <div class="inside-page-help">
                        <div class="help-container-head">
                            <i class="iconfont icon-help"></i><span>帮助和服务</span>
                        </div>
                        <div class="help-container-body">
                            <?php if ('Seller_Goods_Spec' == $ctl && 'spec' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li>1、选择店铺经营的商品分类，以读取平台绑定的商品分类-规格类型，如分类："服装"；规格："颜色"、"尺码"。</li>
                                        <li>2、添加所属规格下的规格值，已有规格值可以删除，新增未保存的规格值可以移除；</li>
                                        <li>3、可通过排序0-255改变规格值显示顺序；在发布商品时勾选已绑定的商品规格，还可对规格值进行"别名"修改操作，但不会影响规格值默认名称的设定。</li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Goods' == $ctl && 'format' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li>1、<?=__('关联版式可以把预设内容插入到商品描述的顶部或者底部，方便商家对商品描述批量添加或修改。')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div> 
                            <?php } else if('Seller_Goods_TBImport' == $ctl ) { ?>
                                <?php if ('importFile' == $met || 'importImage' == $met) { ?>
                                    <div class="help-body-title">帮助中心</div>
                                    <div class="help-body-content">
                                        <ul class="help-items">
                                            <li>1、<?=__('如果修改CSV文件请务必使用微软excel软件，且必须保证第一行表头名称含有如下项目: 宝贝名称、宝贝价格、宝贝数量、运费承担、平邮、EMS、快递、橱窗推荐、宝贝描述、新图片。')?></li>
                                            <li>2、<?=__('如果因为淘宝助理版本差异表头名称有出入，请先修改成上述的名称方可导入，不区分全新、二手、闲置等新旧程度，导入后商品类型都是全新。')?></li>
                                            <li>3、<?=__('如果CSV文件超过8M请通过excel软件编辑拆成多个文件进行导入。')?></li>
                                            <li>4、<?=__('每个商品最多支持导入5张图片,暂不支持JIF格式。')?></li>
                                            <li>5、<?=__('必须保证文件编码为UTF-8。')?></li>
                                        </ul>
                                    </div>
                                    <div class="help-body-split-line"></div>
                                 <?php } ?>
                            <?php } else if('Seller_Trade_Deliver' == $ctl && 'deliver' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li>1、<?=__('可以对待发货的订单进行发货操作，发货时可以设置收货人和发货人信息，填写一些备忘信息，选择相应的物流服务，打印发货单。')?></li>
                                        <li>2、<?=__('已经设置为发货中的订单，您还可以继续编辑上次的发货信息。')?></li>
                                        <li>3、<?=__('如果因物流等原因造成买家不能及时收货，您可使用点击延迟收货按钮来延迟系统的自动收货时间。')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Trade_Waybill' == $ctl && 'waybillManage' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li>1、<?=__('未绑定的物流公司后边会出现“选择模板”按钮，在选择模板页面可以绑定可用的打印模板')?>。</li>
                                        <li>2、<?=__('点击“默认”按钮可以设置当前模板为默认打印模板')?>。</li>
                                        <li>3、<?=__('点击“解绑”按钮可以解除当前绑定，重新选择其它模板')?>。</li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div> 
                            <?php } else if('Seller_Trade_Waybill' == $ctl && 'waybillIndex' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li>1、<?=__('商家已经建立的打印模板列表')?>x</li>
                                        <li>2、<?=__('点击右上角的添加模板按钮可以建立商家自己的模板')?></li>
                                        <li>3、<?=__('点击设计按钮可以对运单模板布局进行设计，点击测试按钮可以对模板进行打印测试，点击编辑按钮可以对模板参数进行调整')?></li>
                                        <li>4、<?=__('设计完成后在编辑中修改模板状态为启用后，商家就可以绑定该模板进行运单打印')?></li>
                                        <li>5、<?=__('点击删除按钮可以删除现有模板，删除后该模板将自动解除绑定，请慎重操作')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div> 
                            <?php } else if('Seller_Promotion_GroupBuy' == $ctl && 'index' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <?php if($shop_type){ ?>
                                        <ul class="help-items">
                                            <li><?=__('1、点击新增团购按钮可以添加团购活动')?></li>
                                            <li><?=__('2、如发布虚拟商品的团购活动，请点击新增虚拟团购按钮')?></li>
                                        </ul>
                                    <?php }else{ ?>
                                        <ul class="help-items">
                                            <li><?php if($com_flag){ ?><?=__('套餐过期时间')?>：<em class="red"></em><?=$combo_row['combo_endtime']?>。
                                            <?php }else{ ?>
                                                <?=__('你还没有购买套餐或套餐已经过期，请购买或续费套餐')?>
                                            <?php  } ?></li>
                                            <li><?=__('1、点击套餐管理可购买或续费套餐')?></li>
                                            <li>2、<strong  class="bbc_seller_color"><?=__('相关费用会在店铺的账期结算中扣除')?></strong></li>
                                        </ul>
                                    <?php } ?>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Promotion_Increase' == $ctl && 'index' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <?php if ($shop_type) { ?>
                                        <ul class="help-items">
                                            <li>1、<?= __('点击添加活动按钮可以添加加价购活动，点击编辑按钮可以对加价购活动进行编辑') ?></li>
                                            <li>2、<?= __('点击删除按钮可以删除加价购活动') ?></li>
                                        </ul>
                                    <?php } else { ?>
                                        <ul class="help-items">
                                            <li><?php if ($com_flag) { ?><?= __('套餐过期时间') ?>：<em class="red"></em><?= $combo_row['combo_end_time'] ?>。
                                            <?php } else { ?>
                                                <?= __('你还没有购买套餐或套餐已经过期，请购买或续费套餐') ?>
                                            <?php } ?></li>
                                            <li>1、<?= __('点击套餐管理可以购买或续费套餐') ?></li>
                                            <li>2、<?= __('点击添加活动按钮可以添加加价购活动，点击编辑按钮可以对加价购活动进行编辑') ?></li>
                                            <li>3、<?= __('点击删除按钮可以删除加价购活动') ?></li>
                                            <li>4、<strong class="bbc_seller_color"><?= __('相关费用会在店铺的账期结算中扣除') ?></strong></li>
                                        </ul>
                                    <?php } ?>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Promotion_Discount' == $ctl && 'index' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <?php  if($shop_type){ ?>
                                    <ul class="help-items">
                                        <li><?=__('1、点击添加活动按钮可以添加限时折扣活动，点击管理按钮可以对限时折扣活动内的商品进行管理')?></li>
                                        <li><?=__('2、点击删除按钮可以删除限时折扣活动')?></li>
                                    </ul>
                                    <?php }else{ ?>
                                    <ul class="help-items">
                                        <li> <?php if($com_flag){ ?><?=__('套餐过期时间')?>：<em class="red"></em><?=$combo_row['combo_end_time']?>。
                                        <?php }else{ ?>
                                            <?=__('你还没有购买套餐或套餐已经过期，请购买或续费套餐')?>
                                        <?php  } ?></li>
                                        <li><?=__('1、点击购买套餐和套餐续费按钮可以购买或续费套餐')?></li>
                                        <li><?=__('2、点击添加活动按钮可以添加限时折扣活动，点击管理按钮可以对限时折扣活动内的商品进行管理')?></li>
                                        <li><?=__('3、点击删除按钮可以删除限时折扣活动')?></li>
                                        <li>4、<strong class="bbc_seller_color"><?=__('相关费用会在店铺的账期结算中扣除')?></strong>。</li>
                                    </ul>
                                    <?php } ?>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Promotion_Discount' == $ctl && 'manage' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li><?=__('1、限时折扣商品的时间段不能重叠')?></li>
                                        <li><?=__('2、点击添加商品按钮可以搜索并添加参加活动的商品，点击删除按钮可以删除该商品')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Promotion_Seckill' == $ctl && 'index' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li>1、秒杀新增可以添加秒杀活动。</li>
                                        <li>2、参与秒杀活动的商品暂不支持分销商品。</li>
                                        <li>3、参与秒杀活动的商品不可再参与其他任何促销活动</li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Promotion_MeetConditionGift' == $ctl && 'index' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <?php if($shop_type){ ?>
                                    <ul class="help-items">
                                        <li>1、<?=__('点击添加活动按钮可以添加满即送活动，点击编辑按钮可以对满即送活动进行编辑')?></li>
                                        <li>2、<?=__('点击删除按钮可以删除满即送活动')?></li>
                                    </ul>
                                    <?php  }else{ ?>
                                    <ul class="help-items">
                                        <li><?php if($com_flag){ ?><?=__('套餐过期时间')?>：<em class="red"></em><?=$combo['combo_end_time']?>。
                                        <?php }else{ ?>
                                            <?=__('你还没有购买套餐或套餐已经过期，请购买或续费套餐')?>
                                        <?php  } ?></li>
                                        <li>1、<?=__('点击购买套餐或续费套餐可以购买或续费套餐')?></li>
                                        <li>2、<?=__('已参加限时折扣、团购的商品，可同时参加满即送活动')?>。</li>
                                        <li>3、<strong  class="bbc_seller_color"><?=__('相关费用会在店铺的账期结算中扣除')?></strong>。</li>
                                    </ul>
                                    <?php } ?>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Promotion_MeetConditionGift' == $ctl && 'add' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li>1、<?=__('满即送活动包括店铺所有商品，活动时间不能和已有活动重叠')?></li>
                                        <li>2、<?=__('每个满即送活动最多可以设置3个价格级别，点击新增级别按钮可以增加新的级别，价格级别应该由低到高')?></li>
                                        <li>3、<?=__('每个级别可以有减现金、送礼品2种促销方式，至少需要选择一种')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Promotion_Voucher' == $ctl && 'index' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <?php if($shop_type){ ?>
                                         <ul class="help-items">
                                            <li>1、<?=__('手工设置代金券失效后,用户将不能领取该代金券,但是已经领取的代金券仍然可以使用')?></li>
                                            <li>2、<?=__('代金券模版和已发放的代金券过期后自动失效')?></li>
                                        </ul>
                                    <?php  }else{ ?>
                                    <ul class="help-items">
                                        <li><?php if($com_flag){ ?><?=__('套餐过期时间')?>：<em class="red"></em><?=$combo['combo_end_time']?>。
                                        <?php }else{ ?>
                                            <?=__('你还没有购买套餐或套餐已经过期，请购买或续费套餐')?>
                                        <?php  } ?></li>
                                        <li>1、<?=__('手工设置代金券失效后,用户将不能领取该代金券,但是已经领取的代金券仍然可以使用')?></li>
                                        <li>2、<?=__('代金券模版和已发放的代金券过期后自动失效')?></li>
                                        <li>3、<strong class="bbc_seller_color"><?=__('相关费用会在店铺的账期结算中扣除')?>。</strong></li>
                                    </ul>
                                    <?php } ?>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Distribution_Seller_Setting' == $ctl && 'index' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li><?=__('1、消费限制：用户必须在本店铺消费并且满足设置的消费金额限制时，才能申请成为该店铺的分销员。')?></li>
                                        <li><?=__('2、消费金额限制设置为0，表示消费金额不做限制，用户只要在本店铺成功消费，即可申请成为该店铺的分销员。')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Shop_Setshop' == $ctl && 'slide' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li><?=__('1、最多可上传5张幻灯片图片。')?></li>
                                        <li><?=__('2、支持jpg、jpeg、gif、png格式上传，建议图片宽度1200px、高度在300px到500px之间、大小1.00M以内的图片。提交2~5张图片可以进行幻灯片播放，一张图片没有幻灯片播放效果。')?></li>
                                        <li><?=__('3、操作完成以后，按"提交"按钮，可以在当前页面进行幻灯片展示。')?></li>
                                        <li><?=__('4、跳转链接必须带有')?><b style="color:red;"><?=__('"http://"')?></b></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Shop_Entityshop' == $ctl && 'entityShop' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li><?=__('1、系统借助“百度地图”进行定位，使用时要确保网络能正常访问。')?></li>
                                        <li><?=__('2、由于地图的窗口大小限制，最多可添加20个地址。可在“列表显示”中修改和删除已添加的地址。')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Shop_Info' == $ctl && 'info' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li><?=__('1、店铺到期前 30 天可以申请店铺续签。')?></li>
                                        <li><?=__('1、店铺到期')?> <?=$shop["shop_end_time"]?> <?=__('可以在')?> <?=$frontmonth?> <?=__('开始申请店铺续签')?>。</li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Shop_Chain' == $ctl && 'chain' == $met) { ?>
                                <?php if($act) { ?>
                                    <div class="help-body-title">帮助中心</div>
                                    <div class="help-body-content">
                                        <?php if($act == 'add') { ?>
                                            <ul class="help-items">
                                                <li>1、<?=__('可添加多个门店,同时管理。')?></li>
                                                <li>2、<strong class="bbc_seller_color"><?=__('所填门店信息真实准确。')?></strong></li>
                                            </ul>
                                        <?php } else if($act == 'edit') { ?>
                                            <ul class="help-items">
                                                 <li><strong class="bbc_seller_color"><?=__('所填门店信息真实准确。')?></strong></li>
                                            </ul>
                                        <?php } ?>
                                    </div>
                                
                                    <div class="help-body-split-line"></div>
                                <?php } ?>
                            <?php } else if('Seller_Analysis_General' == $ctl && 'index' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                       <li>1、<?=__('符合以下任何一种条件的订单即为有效订单')?>：1）<?=__('采用在线支付方式支付并且已付款')?>；2）<?=__('采用货到付款方式支付并且交易已完成')?></li>
                                        <li>2、<?=__('以下关于订单和订单商品统计数据的依据为：从所选时间段内的有效订单，最大可查31天')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Message' == $ctl && 'message' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li><?=__('1、可以对消息进行查看和删除。')?></li>
                                        <li><?=__('2、删除消息，删除后其他账户的该条消息也将被删除。')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Message' == $ctl && 'messageManage' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li><?=__('1、短信、邮件接收方式需要正确设置接收号码才能正常接收。')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } else if('Seller_Trade_Waybill' == $ctl && 'waybillDesign' == $met) { ?>
                                <div class="help-body-title">帮助中心</div>
                                <div class="help-body-content">
                                    <ul class="help-items">
                                        <li>1、<?=__('勾选需要打印的项目，勾选后可以用鼠标拖动确定项目的位置、宽度和高度，也可以点击项目后边的微调按钮手工录入')?></li>
                                        <li>2、<?=__('设置完成后点击提交按钮完成设计')?></li>
                                    </ul>
                                </div>
                                <div class="help-body-split-line"></div>
                            <?php } ?>
                            <?php 
                            $phone = Web_ConfigModel::value("setting_phone");
                            if ($phone) {
                                $phone = explode(',', $phone);//电话
                            }

                            $email = Web_ConfigModel::value("setting_email");//邮件

                            if($phone || $email){?>
                                <div class="help-body-title">服务窗口</div>
                                <div class="help-body-service">
                                <?php if($phone){?>
                                    <?php foreach($phone as $k=>$v){?>
                                    <p class="service-hotline"><?=__('客服电话')?>：<?=$v;?></p>
                                   <?php }?>
                                <?php }?>
                                <?php if($email){?>
                                    <p class="service-hotline"><?=__('客服邮箱')?>：<?=$email?></p>
                                <?php }?>
                                </div>
                                <div class="help-body-split-line"></div>
                             <?php }?>
                            <div class="help-body-title">版权信息</div>
                            <div class="help-copyright-content">
                                <p class="about">
                                    <?php if (isset($this->bnav) && $this->bnav) {
                                        foreach ($this->bnav['items'] as $key => $nav) {
                                            if ($key < 10) {
                                                ?>
                                                <a href="<?= $nav['nav_url'] ?>" <?php if ($nav['nav_new_open'] == 1){ ?>target="_blank"<?php } ?>><?= $nav['nav_title'] ?></a>
                                            <?php } else {
                                                return;
                                            }
                                        }
                                    } ?>
                                </p>
                                <p class="copyright">
                                    <?= __(Web_ConfigModel::value('copyright')); ?>
                                </p>
                                <p class="statistics_code"><?php echo Web_ConfigModel::value('icp_number') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="content">
                        <?php
                                if (isset($seller_menu[$level_row[1]]['sub'][$level_row[2]]['sub'])) {
                            ?>
                            <?php if ($ctl == "Seller_Shop_Decoration" && $met == "decoration" && $act == "set") { ?>
                            <?php } else { ?>
                                <div class="tabmenu">
                                    <ul class="clearfix">
                                        <?php
                                        foreach ($seller_menu[$level_row[1]]['sub'][$level_row[2]]['sub'] as $menu_row) {
                                            //不应该根据名称来判断
                                            if ($menu_row['menu_url_met'] == 'combo') {
                                                //自营或者不需要收费
                                                if ((@$this->self_support_flag || @$this->selfSupportFlag)) {
                                                    continue;
                                                } else {
                                                }
                                            }
                                            ?>
                                            <li class="<?= ($menu_row['menu_id'] == $level_row[3]) ? 'active' : '' ?>">
                                                <a href="<?= sprintf('%s?ctl=%s&met=%s&typ=e&%s', Yf_Registry::get('url'), $menu_row['menu_url_ctl'], $menu_row['menu_url_met'], $menu_row['menu_url_parem']); ?>"><?= __($menu_row['menu_name']) ?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
<script>
	// 最新
	var theme_page_color = "<?=$this->theme_page_color?>";//后台传class名，例：front-1 front-2 ...
	document.getElementsByTagName('body')[0].className=theme_page_color;
	// 原版
	// var theme_page_color = "<?=$this->theme_page_color?>"
	// document.documentElement.style.setProperty("--color", theme_page_color);
   </script>