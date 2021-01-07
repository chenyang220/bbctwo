<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';

?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body class="<?=$skin?>">

<div class="">
  <div class="homepage-focus" nctype="sellerTplContent" style="margin-top:0px;">
    <div class="title">
      <ul class="tab-base nc-row">
        <li><a href="javascript:void(0);" class="current"><?= __('站内信模板'); ?></a></li>
        <li><a href="javascript:void(0);"><?= __('手机短信模板'); ?></a></li>
        <li><a href="javascript:void(0);"><?= __('邮件模板'); ?></a></li>
		<li><a href="javascript:void(0);"><?= __('微信公众号模板'); ?></a></li>
      </ul>
    </div>
    <!-- <?= __('站内信'); ?> S -->
    <form class="tab-content" method="post" name="mail_form" id="mail_form">
      <input name="id" value="<?=($data['id'])?>" type="hidden">
      <input name="type" value="<?=($data['type'])?>" type="hidden">
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label><?= __('站内信开关'); ?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="message_switch1" class="cb-enable <?=($data['is_mail']==1 ? 'selected' : '')?>"><?= __('开启'); ?></label>
              <label for="message_switch0" class="cb-disable <?=($data['is_mail']==0 ? 'selected' : '')?>"><?= __('关闭'); ?></label>
              <input id="message_switch1" name="is_mail" <?=($data['is_mail']==1 ? 'checked' : '')?> value="1" type="radio">
              <input id="message_switch0" name="is_mail" <?=($data['is_mail']==0 ? 'checked' : '')?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('强制接收'); ?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="message_forced1" class="cb-enable <?=($data['force_mail']==1 ? 'selected' : '')?>" ><?= __('是'); ?></label>
              <label for="message_forced0" class="cb-disable <?=($data['force_mail']==0 ? 'selected' : '')?>"><?= __('否'); ?></label>
              <input id="message_forced1" name="force_mail" <?=($data['force_mail']==1 ? 'checked' : '')?> value="1" type="radio">
              <input id="message_forced0" name="force_mail" <?=($data['force_mail']==0 ? 'checked' : '')?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('消息内容'); ?></label>
          </dt>
          <dd class="opt">
            <textarea name="content_mail" rows="6" class="tarea"><?=($data['content_mail'])?></textarea>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
      </div>
    </form>
    <!-- <?= __('站内信'); ?> E --> 
    <!-- <?= __('短消息'); ?> S -->
    <form class="tab-content" method="post" name="phone_form" id="phone_form" style="display:none;">
      <input name="id" value="<?=($data['id'])?>" type="hidden">
      <input name="type" value="<?=($data['type'])?>" type="hidden">
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label><?= __('短信开关'); ?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="message_phone1" class="cb-enable <?=($data['is_phone']==1 ? 'selected' : '')?>"><?= __('开启'); ?></label>
              <label for="message_phone0" class="cb-disable <?=($data['is_phone']==0 ? 'selected' : '')?>"><?= __('关闭'); ?></label>
              <input id="message_phone1" name="is_phone" <?=($data['is_phone']==1 ? 'checked' : '')?> value="1" type="radio">
              <input id="message_phone0" name="is_phone" <?=($data['is_phone']==0 ? 'checked' : '')?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('强制接收'); ?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="message_for1" class="cb-enable <?=($data['force_phone']==1 ? 'selected' : '')?>" ><?= __('是'); ?></label>
              <label for="message_for0" class="cb-disable <?=($data['force_phone']==0 ? 'selected' : '')?>"><?= __('否'); ?></label>
              <input id="message_for1" name="force_phone" <?=($data['force_phone']==1 ? 'checked' : '')?> value="1" type="radio">
              <input id="message_for0" name="force_phone" <?=($data['force_phone']==0 ? 'checked' : '')?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('短信内容'); ?></label>
          </dt>
          <dd class="opt">
            <textarea name="content_phone" rows="6" class="tarea"><?=($data['content_phone'])?></textarea>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('百度模板'); ?>ID</label>
          </dt>
          <dd class="opt">
            <input name="baidu_tpl_id" class="baidu_tpl_id" value="<?=($data['baidu_tpl_id'])?>" />
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
      </div>
    </form>
    <!-- <?= __('短消息'); ?> E --> 
    <!-- <?= __('邮件'); ?> S -->
    <form class="tab-content" method="post" name="email_form" id="email_form" style="display:none;">
      <input name="id" value="<?=($data['id'])?>" type="hidden">
      <input name="type" value="<?=($data['type'])?>" type="hidden">
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label><?= __('邮件开关'); ?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
			<label for="message_email1" class="cb-enable <?=($data['is_email']==1 ? 'selected' : '')?>" ><?= __('是'); ?></label>
              <label for="message_email0" class="cb-disable <?=($data['is_email']==0 ? 'selected' : '')?>"><?= __('否'); ?></label>
              <input id="message_email1" name="is_email" <?=($data['is_email']==1 ? 'checked' : '')?> value="1" type="radio">
              <input id="message_email0" name="is_email" <?=($data['is_email']==0 ? 'checked' : '')?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('强制接收'); ?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
             <label for="message_email3" class="cb-enable <?=($data['force_email']==1 ? 'selected' : '')?>" ><?= __('是'); ?></label>
              <label for="message_email4" class="cb-disable <?=($data['force_email']==0 ? 'selected' : '')?>"><?= __('否'); ?></label>
              <input id="message_email3" name="force_email" <?=($data['force_email']==1 ? 'checked' : '')?> value="1" type="radio">
              <input id="message_email4" name="force_email" <?=($data['force_email']==0 ? 'checked' : '')?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('邮件标题'); ?></label>
          </dt>
          <dd class="opt">
            <textarea name="title" rows="6" class="tarea"><?=($data['title'])?></textarea>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('邮件内容'); ?></label>
          </dt>
          <dd class="opt">
			<script id="container" style="width:600px;height:500px;" name="content_email" type="text/plain">
				<?=($data['content_email'])?>
			</script>
          </dd>
		  <span class="err"></span>
          <p class="notic"> </p>
        </dl> 
        <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
      </div>
    </form>
	<!-- 微信公众号 -->
    <form class="tab-content" method="post" id="wechart_public_form"  name = "wechart_public_form"  style="display:none;">
      <input name="id" value="<?=($data['id'])?>" type="hidden">
      <input name="type" value="<?=($data['type'])?>" type="hidden">
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label><?= __('开关'); ?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="wxpact_switch1" class="cb-enable <?=($data['is_wechart_pulic']==1 ? 'selected' : '')?>"><?= __('开启'); ?></label>
              <label for="wxpact_switch0" class="cb-disable <?=($data['is_wechart_pulic']==0 ? 'selected' : '')?>"><?= __('关闭'); ?></label>
              <input id="wxpact_switch1" name="is_wechart_pulic" <?=($data['is_wechart_pulic']==1 ? 'checked' : '')?> value="1" type="radio">
              <input id="wxpact_switch0" name="is_wechart_pulic" <?=($data['is_wechart_pulic']==0 ? 'checked' : '')?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label><?= __('强制接收'); ?></label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="wxpact_forced1" class="cb-enable <?=($data['force_wechart_public']==1 ? 'selected' : '')?>" ><?= __('是'); ?></label>
              <label for="wxpact_forced0" class="cb-disable <?=($data['force_wechart_public']==0 ? 'selected' : '')?>"><?= __('否'); ?></label>
              <input id="wxpact_forced1" name="force_wechart_public" <?=($data['force_wechart_public']==1 ? 'checked' : '')?> value="1" type="radio">
              <input id="wxpact_forced0" name="force_wechart_public" <?=($data['force_wechart_public']==0 ? 'checked' : '')?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <?php
            $code = $data['code'];
            $tpl_msg_map = $data['tpl_msg_map'];
            $wxgzh_tpl_id ="";
            if(isset($tpl_msg_map[$code]) && $tpl_msg_map[$code]){
              $wxgzh_tpl_id = $tpl_msg_map[$code];
            }

            //处理模板内容
             $content_wechart_pulic =  unserialize(base64_decode($data['content_wechart_pulic']));
        ?>
        <dl class="row">
          <dt class="tit">
            <label><?= __('消息内容'); ?></label>
          </dt>
          <dd class="opt">
            <textarea name="content_wechart_pulic" rows="6" class="tarea" readonly><? if($wxgzh_tpl_id){ echo  "<pre>".print_r($content_wechart_pulic)."<pre>";}?></textarea>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label><?= __('模板ID'); ?></label>
            </dt>
            <dd class="opt">
                <input name="wxgzh_tpl_id" readonly class="ui-input" style="width:300px;" value="<?=($wxgzh_tpl_id)?>" />
                <span class="err"></span>
                <p class="notic">template_id_short:模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式</p>
            </dd>
        </dl>
        <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
      </div>
    </form>
  </div>
</div>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.all.js"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/upload/addCustomizeButton.js"></script>
    <script type="text/javascript">
        var ue = UE.getEditor('container', {
            toolbars: [
                [
                 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'justifyleft', 'justifycenter', 'justifyright', 'insertunorderedlist', 'insertorderedlist', 'blockquote',
                 'emotion', 'insertvideo', 'link', 'removeformat', 'rowspacingtop', 'rowspacingbottom', 'lineheight', 'paragraph', 'fontsize', 'inserttable', 'deletetable', 'insertparagraphbeforetable',
                 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols'
                ]
            ],
            autoClearinitialContent: true,
            wordCount: false,
            elementPathEnabled: false
        });

        ue.ready(function() {//编辑器初始化完成再赋值'); ?>
            ue.setContent('<?=addslashes($data['content_email']) ?>');  //赋值给'); ?>UEditor
        });
    </script>
<script>
$(function(){
    $('div[nctype="sellerTplContent"] > .title > ul').find('a').click(function(){
        $(this).addClass('current').parent().siblings().find('a').removeClass('current');
        var _index = $(this).parent().index();
        var _form = $('div[nctype="sellerTplContent"]').find('form');
        _form.hide();
        _form.eq(_index).show();
    });
});
</script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/news/message/template_set.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>