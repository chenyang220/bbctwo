<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>

<style>
    .webuploader-pick{ padding:1px; }
    
</style>
</head>
<body class="<?=$skin?>">
<div class="">
   
    <form method="post" enctype="multipart/form-data" id="tag-edit-form" name="form">
	<input id="user_tag_id" name="user_tag_id"  value="<?=$data['user_tag_id']?>" class="ui-input w400" type="hidden"/>
        <div class="ncap-form-default">
			<dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('标签名称'); ?></label>
                </dt>
                <dd class="opt">
					<input id="user_tag_name" name="user_tag_name"  value="<?=$data['user_tag_name']?>" class="ui-input w400" type="text"/>
                    <p class="notic"><?= __('不要超过'); ?>20<?= __('个字符'); ?></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">
                    <label><?= __('标签排序'); ?></label>
                </dt>
                <dd class="opt">					
					<input id="user_tag_sort" name="user_tag_sort" value="<?=$data['user_tag_sort']?>" class="ui-input w400" type="text"/>
                    <p class="notic"><?= __('数字范围为'); ?>1~255<?= __('，数字越小越靠前'); ?></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit"><?= __('推荐'); ?></dt>
                <dd class="opt">
                    <ul class="nofloat">
                        <li>
						<div class="onoff">
								<label title="<?= __('是'); ?>" class="cb-enable <?=($data['user_tag_recommend']==1 ? 'selected' : '')?>" for="user_tag_recommend_enable"><?= __('是'); ?></label>
								<label title="<?= __('否'); ?>" class="cb-disable <?=($data['user_tag_recommend']==0 ? 'selected' : '')?>" for="user_tag_recommend_disabled"><?= __('否'); ?></label>
								<input type="radio" value="1" name="user_tag_recommend" id="user_tag_recommend_enable"  <?=($data['user_tag_recommend']==1 ? 'checked' : '')?> />
								<input type="radio" value="0" name="user_tag_recommend" id="user_tag_recommend_disabled"  <?=($data['user_tag_recommend']==0 ? 'checked' : '')?>/>
						</div>
                        </li>
                    </ul>
					 <p class="notic"><?= __('把标签及其所属会员，推荐到好友查找页面'); ?></p>
                </dd>
			</dl>			
			<dl class="row">
                <dt class="tit">
                    <label><?= __('标签描述'); ?></label>
                </dt>
                <dd class="opt">
					<textarea name="user_tag_content" rows="6" class="tarea"><?=$data['user_tag_content']?></textarea>
                    <p class="notic"></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('标签图片'); ?></label>
                </dt>
                <dd class="opt">
                    <img id="user_tag_image_image" name="user_tag_image_image" alt="<?= __('选择图片'); ?>" src="<?=$data['user_tag_image']?>" height="120px" />

                    <div class="image-line upload-image" id="user_tag_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                    <input id="user_tag_image"  name="user_tag_image" value="<?=$data['user_tag_image']?>" class="ui-input w400" type="hidden"/>
                    <div class="notic"><?= __('建议大小'); ?>: 120px*120px</div>
                </dd>
            </dl>

        </div>
    </form>
</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
<script>
    //图片上传'); ?>
   /*  $(function(){

        user_tag_upload = new UploadImage({
            thumbnailWidth: 120,
            thumbnailHeight: 120,
            imageContainer: '#user_tag_image_image',
            uploadButton: '#user_tag_upload',
            inputHidden: '#user_tag_image'
        });

       
    }) */
	$(function(){

		var agent = navigator.userAgent.toLowerCase();

		if ( agent.indexOf("msie") > -1 && (version = agent.match(/msie [\d]/), ( version == "msie 8" || version == "msie 9" )) ) {
			user_tag_upload = new UploadImage({
				thumbnailWidth: 120,
				thumbnailHeight: 120,
				imageContainer: '#user_tag_image_image',
				uploadButton: '#user_tag_upload',
				inputHidden: '#user_tag_image'
			});
		} else {
			$('#user_tag_upload').on('click', function () {
				$.dialog({
					title: '<?= __('图片裁剪'); ?>',
					content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
					data: { SHOP_URL: SHOP_URL, width: 120, height: 120, callback: callback },
					width: '800px',
					lock: true
				})
			});

			function callback ( respone , api ) {
				$('#user_tag_image_image').attr('src', respone.url);
				$('#user_tag_image').attr('value', respone.url);
				api.close();
			}
		}


	})	
	function initPopBtns()
				{
					var t = "add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"] : ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
					api.button({
						id: "confirm", name: t[0], focus: !0, callback: function ()
						{
							postData(oper, rowData.contract_type_id);
							return cancleGridEdit(),$("#tag-edit-form").trigger("validate"), !1
						}
					}, {id: "cancel", name: t[1]})
				}
			function postData(t, e)
			{
			$_form.validator({
				fields: {
					 'user_tag_name': 'required;',
					 'user_tag_sort': 'integer[+];'
				},
				valid: function (form)
				{
					var me = this;
					// <?= __('提交表单之前，'); ?>hold<?= __('住表单，防止重复提交'); ?>
					me.holdSubmit();
					n = "<?= __('修改'); ?>";
					Public.ajaxPost(SITE_URL + '?ctl=User_Tag&met=editUserTagDetail&typ=json', $_form.serialize(), function (e)
					{
						if (200 == e.status)
						{
							parent.parent.Public.tips({content: n + "<?= __('成功！'); ?>"});
							callback && "function" == typeof callback && callback(e.data, t, window)
						}
						else
						{
							parent.parent.Public.tips({type: 1, content: n + "<?= __('失败！'); ?>" + e.msg})
						}
						// <?= __('提交表单成功后，释放'); ?>hold<?= __('，如果不释放'); ?>hold<?= __('，就变成了只能提交一次的表单'); ?>
						me.holdSubmit(false);
					})
				},
				ignore: ":hidden",
				theme: "yellow_bottom",
				timely: 1,
				stopOnError: !0
			});
		}
		function cancleGridEdit()
		{
			null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
		}
		var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#tag-edit-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
		initPopBtns();
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>