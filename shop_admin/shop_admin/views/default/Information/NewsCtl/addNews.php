<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<!-- <?= __('配置文件'); ?> -->
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.config.js"></script>
<!-- <?= __('编辑器源码文件'); ?> -->
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.all.js"></script>

<script type="text/javascript" src="<?= $this->view->js_com ?>/upload/addCustomizeButton.js"></script>
<style>
</style>
</head>
<body class="<?=$skin?>">
<form id="article_form" method="post">
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label for="title"><em>*</em><?= __('资讯标题'); ?>:</label>
        </dt>
        <dd class="opt">
          <input type="text" value="" name="title" id="title" class="ui-input" maxlength="20">
          <span class="err"><?= __('最多20个字数'); ?></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
            <label for="subtitle"><?= __('资讯副标题'); ?>:</label>
        </dt>
        <dd class="opt">
            <input type="text" value="" name="subtitle" id="subtitle" class="ui-input" maxlength="30"> <span class="err"><?= __('最多30个字数'); ?></span>
            <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
            <label><em>*</em><?= __('资讯分类'); ?>:</label>
        </dt>
        <dd class="opt">
            <div class="ctn-wrap"><span id="type"></span></div>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label><em>*</em><?= __('文章内容'); ?>:</label>
        </dt>
        <dd class="opt" ">
            <!-- <?= __('加载编辑器的容器'); ?> -->
            <textarea id="article_desc"  name="content" type="text/plain">

            </textarea>
        </dd>
      </dl>
    </div>
  </form>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    
    var ue = UE.getEditor('article_desc', {
        initialFrameWidth: '100%',
        initialFrameHeight: 600,
        toolbars: [
            [
             'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'justifyleft', 'justifycenter', 'justifyright', 'insertunorderedlist', 'insertorderedlist', 'blockquote',
             'emotion', 'insertvideo', 'link', 'removeformat', 'rowspacingtop', 'rowspacingbottom', 'lineheight', 'paragraph', 'fontsize', 'inserttable', 'deletetable', 'insertparagraphbeforetable',
             'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols'
            ]
        ],
        autoClearinitialContent: true,
        //关闭字数统计
        wordCount: false,
        //关闭elementPath
        elementPathEnabled: false
    });
</script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/Information/news/information_addnews.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>