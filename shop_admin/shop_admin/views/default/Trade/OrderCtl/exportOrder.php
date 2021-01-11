<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<script type="text/javascript">
    var api = frameElement.api;
    console.log(api);
</script>
<style>
    .webuploader-pick{ padding:1px; }
     .ncap-form-default dd.opt{
    	    width: 100%;
   			height: 158px;
    }
    .ncap-form-default dd.opt div{
    	position: relative;
    	height: 100%;
    }
    .ncap-form-default dd.opt p{
    	    font-size: 22px;
		    position: relative;
		    top: 47%;
		    text-align: center;
		    transform: translateY(-50%);
    }
    .ncap-form-default dd.opt input{
    	 	position: absolute;
		    right: 5%;
		    bottom: 0;
    	    width: 150px;
		    height: 40px;
		    line-height: 40px;
		    font-size: 16px;1
		    border: none;
    }
    .ncap-form-default dd.opt input:nth-child(2){
    	left: 5%;
    	right: inherit;
    }
</style>
</head>
<body class="<?=$skin?>">
<div class="">
    <form method="post" enctype="multipart/form-data" id="user-edit-form" name="form">
        <div class="ncap-form-default pl20">
			<!-- <dl class="row">
                <dd class="opt">
					<input id="user_name" name="user_name"  readonly value="<?= __('请选择导出页数'); ?>" class="ui-input w200" type="text"/>
                </dd>
            </dl> -->
			<dl class="row">
				<dd class="opt">
				  <div>
				  		<p><?= __('请选择导出页数'); ?></p>
                      <input type="button" value="<?= __('导出当前页'); ?>" id="export">
                      <input type="button" value="<?= __('导出全部'); ?>" id="export_all">
				  </div>
				</dd>
			</dl>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
<script>
	$(function(){
        var data = api.data;
        $("#export").click(function(){
            data.is_limit = 1;
            var query = "";
            for (x in data)
            {
                query = query + "&" + x + "=" + data[x];
            }
            window.open(SHOP_URL + "?ctl=Api_Trade_Order&met=getOrderExcel&debug=1&type=0"+query);
            api.close();
        })
        $("#export_all").click(function(){
            var query = "";
            for (x in data)
            {
                query = query + "&" + x + "=" + data[x];
            }
            window.open(SHOP_URL + "?ctl=Api_Trade_Order&met=getOrderExcel&debug=1&type=1"+query);
            api.close();
        })
	})
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>