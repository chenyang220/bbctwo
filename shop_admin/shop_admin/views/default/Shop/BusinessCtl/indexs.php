<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
//当前页父级菜单 同级菜单 当前菜单
$menus = $this->getThisMenus();

?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<script src="<?= $this->view->js_com ?>/plugins/jquery.datetimepicker.js"></script>
</head>
<body class="<?=$skin?>">
<style>

.ui-jqgrid tr.jqgrow .img_flied{padding: 1px; line-height: 0px;}
.img_flied img{width: 100px; height: 30px;}
.ui-datepicker-input {
    width: 160px;
}
</style>
<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __($menus['father_menu']['menu_name']); ?></h3>
                <h5><?= __($menus['father_menu']['menu_url_note']); ?></h5>
            </div>
            <ul class="tab-base nc-row">
              <?php 
              foreach($menus['brother_menu'] as $key=>$val){ 
                  if(in_array($val['rights_id'],$admin_rights)||$val['rights_id']==0){
              ?>
              <li><a <?php if(!array_diff($menus['this_menu'], $val)){?> class="current"<?php }?> href="<?= Yf_Registry::get('url') ?>?ctl=<?=$val['menu_url_ctl']?>&met=<?=$val['menu_url_met']?><?php if($val['menu_url_parem']){?>&<?=$val['menu_url_parem']?><?php }?>"><span><?= __($val['menu_name']); ?></span></a></li>
              <?php 
                  }
              }
              ?>
              
            </ul>
        </div>
       </div>
          <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>
        </ul>
    </div>  
    <div class="mod-toolbar-top cf">
		<div class="left">
                    <div id="assisting-category-select" class="ui-tab-select">
                        <ul class="ul-inline">
                          <li><a class="ui-btn" id="t_type" style="display: none;"><?php echo $type;?></a></li>
                          <li><a class="ui-btn" id="today"><?= __('  当日  '); ?></a></li>
                          <li><a class="ui-btn" id="weekly"><?= __('最近7天 '); ?></a></li>
                          <li><a class="ui-btn" id="month"><?= __('最近30天'); ?></a></li>
                                <li>
                                    <label><?= __('付款日期'); ?>:</label>
                                    <input type="text" value="" class="ui-input ui-datepicker-input" name="filter-fromDate" id="filter-fromDate"> - <input type="text" value="" class="ui-input ui-datepicker-input" name="filter-toDate" id="filter-toDate">
                                </li>
                                <?php if ($type === 'all') {?>
                                  <li>
                                      <span id="substations_select"></span>
                                  </li>
                                <?php } ?>
                                <li>
                                    <span id="source"></span>
                                </li>
                                <li>
                                    <span id="shop_class"></span>
                                </li>
                                <li>
                                  <input type="text" id="searchName" class="ui-input ui-input-ph con" value="<?= __('请输入相关数据'); ?>...">
                                </li>
                                <li><a class="ui-btn" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                         </ul>
                  </div>
		</div>
	       <div class="fr">
            <a class="ui-btn" id="btn-excel" data-action="all"><?= __('导出'); ?><i class="iconfont icon-btn04"></i></a>
            <a class="ui-btn" id="btn-refresh"><?= __('刷新'); ?><i class="iconfont icon-btn01"></i></a>
        </div>
	</div>

  
    <div class="grid-wrap">
        <table id="grid">
        </table>
        <div id="page"></div>
    </div>
</div>




<script type="text/javascript" src="<?=$this->view->js?>/controllers/shop/index/shop_business_index_list.js" charset="utf-8"></script>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>