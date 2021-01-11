<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
	<ul class="store-module-selected">
		<li>
			<em class="img-box"><img src="<?= $this->view->img ?>/template/module-default.png" alt="module"></em>
			<div>
				<dl>
					<dt>店铺模板名称：</dt>
					<dd class="bold">默认模板</dd>
				</dl>
				<dl>
					<dt>店铺风格名称：</dt>
					<dd class="bold">通用风格</dd>
				</dl>
				<dl>
					<dt>店铺名称：</dt>
					<dd>远丰女装旗舰店</dd>
				</dl>
				<p><a class="btn-to-store-index" href="">店铺首页</a></p>
			</div>
			
		</li>
	</ul>
	<div class="store-module-items">
		<h4>可用店铺模板</h4>
		<ul id="style_status">
			<li>
				<em class="img-box"><img src="<?= $this->view->img ?>/template/module-default.png" alt=""></em>
				<p>模板名称：默认模板</p>
				<p>风格名称：通用风格</p>
				<div>
                <button class="<?=$Shop_Base['shop_wap_index'] == 1 ?'active':''?> use_status" data-status=1><?=$Shop_Base['shop_wap_index'] == 1 ?'已使用':'使用'?></button>
                <button class="js-btn-preview">预览</button></div>
			</li>
			<li>
				<em class="img-box"><img src="<?= $this->view->img ?>/template/module-fresh.png" alt=""></em>
				<p>模板名称：生鲜模板</p>
				<p>风格名称：生鲜风格</p>
				<div><button class="<?=$Shop_Base['shop_wap_index'] == 2 ?'active':''?> use_status" data-status=2><?=$Shop_Base['shop_wap_index'] == 2 ?'已使用':'使用'?></button><button class="js-btn-preview">预览</button></div>
			</li>
			<li>
				<em class="img-box"><img src="<?= $this->view->img ?>/template/module-industry.png" alt=""></em>
				<p>模板名称：工业模板</p>
				<p>风格名称：工业风格</p>
				<div><button  class="<?=$Shop_Base['shop_wap_index'] == 3 ?'active':''?> use_status" data-status=3 ><?=$Shop_Base['shop_wap_index'] == 3 ?'已使用':'使用'?></button><button class="js-btn-preview">预览</button></div>
			</li>
		</ul>
	</div>
	<div class="preview-content">
		<i class="mask"></i>
		<div>
			<img class="preview-img" src="" alt="module-img">
		</div>
	</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.slideBox.min.js" charset="utf-8"></script>   
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/upload/upload_image.js" charset="utf-8"></script>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script>
	$(function(){
		$(".js-btn-preview").click(function(){
			var img=$(this).parents("li").find("img").attr("src");
			$(".preview-content").show(200);
			$(".preview-img").attr("src",img);
			return false;
		});
		$(".preview-content .mask").click(function(){
			$(".preview-content").hide(200);
		})
	})

    $(".use_status").click(function(){
        var this_active = $(this).hasClass("active");
        if (this_active) {
            $(this).removeClass("active");
            $(this).html("使用");
        } else {
            $(".use_status").each(function (e,a) {
                $(a).removeClass("active");
                $(a).html("使用");
            });
            $(this).addClass("active");
            $(this).html("已使用");
        }
        var shop_wap_index =  $("#style_status").children().find(".active").attr("data-status");
        $.ajax({
            type: "post",
            url:  "/index.php?ctl=Shop_Index&met=shopIndexTemplate&typ=json",
            data: {shop_wap_index:shop_wap_index},
            dataType: "json",
            success: function (e) {
                if (e.status == 200) {
                      Public.tips.success(e.msg);
                } else {
                     Public.tips.warning(e.msg);
                }
            }
        });

    });
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>