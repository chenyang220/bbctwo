<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include TPL_PATH . '/' . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
//当前页父级菜单 同级菜单 当前菜单
$menus = $this->getThisMenus();
?>

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
        <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
                <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
                <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em>
            </div>
            <ul>
                <?= __($menus['this_menu']['menu_url_note']); ?>
            </ul>
        </div>

        <form method="post" id="industry_setting_form" name="industrySettingForm">
            <input type="hidden" name="config_type" value="wxgzh_industry"/>
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label for="main_industry">主行业</label>
                    </dt>
                    <dd class="opt">
                        <select id="main_industry" name="main_industry"  style="border: 1px solid #A8B3B9;width:300px;height:30px;">
                            <?php
                            foreach ($data['config_data'] as $key => $item) {
                                $sel ="";
                                if($key== $data['set_data']['main']){
                                    $sel = "selected";
                                }
                                ?>
                           <option value='<?=$key;?>'  <?=$sel;?> > <?=$item['main'];?>/<?=$item['sub'];?> </option>
                           <?php
                            }
                            ?>
                        </select>
                        <p class="notic"></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="sub_industry">副行业</label>
                    </dt>
                    <dd class="opt">
                        <select id="sub_industry" name="sub_industry"  style="border: 1px solid #A8B3B9;width: 300px;height:30px;">
                            <?php
                            foreach ($data['config_data'] as $key => $item) {
                                $sel ="";
                                if($key== $data['set_data']['sub']){
                                    $sel = "selected";
                                } ?>
                                <option value='<?=$key;?>'  <?=$sel;?> > <?=$item['main'];?>/<?=$item['sub'];?> </option>
                            <?php
                            }
                            ?>
                        </select>
                        <p class="notic"></p>
                    </dd>
                </dl>
				<dl class="row">
                    <dt class="tit">
                        <label>上次设置时间:</label>
                    </dt>
                    <dd class="opt">
                        <label><?=date('Y-m-d H:i:s',$data['set_data']['time'])?></label>
						<p class="notic">备注：主副行业,一个月只能设置一次！</p>
                    </dd>
				</dl>
                <div class="bot">
                    <a href="javascript:void(0);" class="ui-btn ui-btn-sp im-submit-btn"><?= __('确认提交'); ?></a>
                </div>
            </div>
        </form>
    </div>
<script>
    $(function () {
      $("a.im-submit-btn").click(function(){
          parent.$.dialog.confirm(__("修改立马生效,是否继续？"), function () {

              Public.ajaxPost(SITE_URL + "?ctl=WxPublic_Industry&met=save&typ=json", $("#industry_setting_form").serialize(), function (data) {
                  if (data.status == 200) {
                      parent.Public.tips({content: "修改操作成功！"});
                  }
                  else {
                      parent.Public.tips({type: 1, content: data.msg || "操作无法成功，请稍后重试！"});
                  }
              });
          });

      });
    });

</script>
<?php
include TPL_PATH . '/' . 'footer.php';
?>
