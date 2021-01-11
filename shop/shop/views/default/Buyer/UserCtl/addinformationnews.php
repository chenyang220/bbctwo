<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
include $this->view->getTplPath() . '/' . 'buyer_header.php';
?>
<!-- 配置文件 -->
<!--<script type="text/javascript" src="--><?//= $this->view->js_com ?><!--/ueditor/ueditor.config.js"></script>-->
<!-- 编辑器源码文件 -->
<!--<script type="text/javascript" src="--><?//= $this->view->js_com ?><!--/ueditor/ueditor.all.js"></script>-->

<!-- 配置文件 -->
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/ueditor/ueditor.parse.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/upload/addCustomizeButton.js"></script>
<div class="form-style">
    <form method="post" id="form" action="<?= Yf_Registry::get('url') ?>?ctl=Seller_Promotion_InformationNews&met=add&typ=e">
        <dl>
            <dt><i>*</i><?= __('资讯标题') ?>：</dt>
            <dd>
                <input type="text" name="news_title" id="news_title" class="text w450" value="<?= $group_info['title'] ?>" maxlength="20"/>
                <p class="hint"><?= __('资讯标题名称长度最多可输入20个字符') ?></p>
            </dd>
        </dl>
        <dl>
            <dt><?= __('资讯副标题') ?>：</dt>
            <dd>
                <input type="text" name="news_subtitle" id="news_subtitle" class="text w450" value="<?= $group_info['subtitle'] ?>" maxlength="30"/>
                <p class="hint"><?= __('资讯副标题最多可输入30个字符') ?></p>
            </dd>
        </dl>
        
        <dl>
            <dt><i>*</i><?= __('资讯标签') ?>：</dt>
            <dd>
                <select name="newsclass_type" id="newsclass_type" style="margin-right: 5px;">
                    <option value=""><?= __('请选择资讯标签') ?></option>
                    <?php foreach ($classData as $key => $v) {
                        ; ?>
                        <option value="<?= $v['id'] ?>"><?= $v['newsclass_name'] ?></option>
                    <?php }; ?>
                </select>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label><i>*</i><?= __('资讯内容') ?>:</label>
            </dt>
            <dd class="opt">
                <!-- 加载编辑器的容器 -->
                <textarea id="article_desc" style="width:700px;height:300px;" name="content" type="text/plain"></textarea>
            </dd>
        </dl>
        
        <dl>
            <dt></dt>
            <dd>
                <input type="submit" class="button button_blue bbc_seller_submit_btns" value="提交"/> <input type="hidden" name="act" value="add"/>
            </dd>
        </dl>
    </form>
</div>

<!--<script type="text/javascript" src="--><?//= $this->view->js_com ?><!--/webuploader.js" charset="utf-8"></script>-->
<!--<script type="text/javascript" src="--><?//= $this->view->js_com ?><!--/upload/upload_image.js" charset="utf-8"></script>-->
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    var ue = UE.getEditor('article_desc', {
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
<script type="text/javascript">
    $(document).ready(function () {
        console.log(11111);
        $('#form').validator({
            debug: true,
            theme: 'yellow_right',
            timely: true,
            stopOnError: true,
            rules: {
                checkStock: function (element) {
                    var news_title = $("input[name='news_title']").val();
                    
                    var newsclass_type = $("select[name='newsclass_type']").val();
                    var content = $("textarea[name='content']").val();
                    if (!news_title) {
                        return '<?=__("资讯标题不能为空！")?>';
                    }
                  
                    if (!newsclass_type) {
                        return '<?=__("请选择资讯标签！")?>';
                    }
                    if (!content) {
                        return '<?=__("请添写资讯内容！")?>';
                    }
                }
            },
            fields: {
                'news_title': 'required;',
                'newsclass_type': 'required;',
                'content': 'required;',
            },
            valid: function (form) {
                var _this = this;
                // 提交表单之前，hold住表单，并且在以后每次hold住时执行回调
                _this.holdSubmit(function () {
                    Public.tips.error('<?=__('正在处理中...')?>');
                });
                
                var params = $("#form").serialize();
                $.ajax({
                    url: "index.php?ctl=Buyer_User&met=addNews&typ=json",
                    data: params,
                    type: "POST",
                    success: function (e) {
                        console.log(e);
                        if (e.status == 200) {
                            var data = e.data;
                            Public.tips.success('操作成功!');
                            
                            var dest_url = "index.php?ctl=Buyer_User&met=informationnewslist&typ=e";//成功后跳转
                            setTimeout(window.location.href = dest_url, 5000);
                        }
                        else {
                            Public.tips.error(e.msg);
                        }
                        _this.holdSubmit(false);
                    }
                });
            }
        });
    });
</script>

<script src="<?= $this->view->js_com ?>/plugins/jquery.timeCountDown.js"></script>
<script>
    $(function () {
        var _TimeCountDown = $(".fnTimeCountDown");
        _TimeCountDown.fnTimeCountDown();
    })
</script>
</div>

<?php
include $this->view->getTplPath() . '/' . 'buyer_footer.php';
?>

