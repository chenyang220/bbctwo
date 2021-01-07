<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
    <link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
    <link href="<?=$this->view->css?>/shop_table.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    </head>
    <body>
    <style>

        .ui-jqgrid tr.jqgrow .img_flied{padding: 1px; line-height: 0px;}
        .img_flied img{width: 100px; height: 30px;}

    </style>
    <div style="overflow: hidden;
		    padding: 10px 3% 0;
		    text-align: left;" >
        <?php
        if (empty($data)){
            echo "<span>".__('暂无信息')."</span>";
        }else{ ?>
        	      <div id="outerdiv" style="position:fixed;top:0;left:0;background:rgba(0,0,0,0.7);z-index:2;width:100%;height:100%;display:none;">
			      <div id="innerdiv" style="position:absolute;">
			          <img id="bigimg" style="border:5px solid #fff;" src="" />
			      </div>
			    </div>
            <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
                <thead>
                <tr>
                    <th colspan="20"><?= __('公司及联系人信息'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?= __('公司名称'); ?><?= __('：'); ?></th>
                    <td><?=$data['shop_company_name']?></td>
                    <th><?= __('联系人号码'); ?><?= __('：'); ?></th>
                    <td><?=$data['contacts_phone']?></td>
                </tr>
                <tr>
                    <th><?= __('所在地区'); ?><?= __('：'); ?></th>
                    <td><?=$data['address_area']?></td>
                    <th><?= __('公司详细地址:'); ?></th>
 					<td><?=$data['company_address_detail']?></td>
                </tr>
                <tr>
                    <th><?= __('公司电话'); ?><?= __('：'); ?></th>
                    <td><?=$data['company_phone']?></td>
                    <th><?= __('联系人姓名:'); ?></th>
 					<td><?=$data['contacts_name']?></td>
                </tr>
                </tr>
                </tbody>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
                <thead>
                <tr>
                    <th colspan="20"><?= __('营业执照信息（副本)'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?= __('是否多证合一'); ?><?= __('：'); ?></th>
                    <td><?php if($data['threeinone']){ echo '是';}else{ echo '否';}?></td>
                    <th><?= __('营业执照号'); ?><?= __('：'); ?></th>
                    <td><?=$data['business_id']?></td>
                </tr>
                <tr>
                    <th><?= __('营业执照所在地'); ?><?= __('：'); ?></th>
                    <td><?=$data['business_license_location']?></td>
                    <th><?= __('营业执照有效期:'); ?></th>
 					<td><?=$data['business_licence_start']?>至<?=$data['business_licence_end']?></td>
                </tr>
                <tr>
                    <th><?= __('营业执照电子版'); ?><?= __('：'); ?></th>
                    <td> <img class="pimg" src="<?php if(isset($data['business_license_electronic'])){echo $data['business_license_electronic'];}?>"></td>
                </tr>
                </tbody>
            </table>
            <?php if(!$data['threeinone']){?>
            <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
                <thead>
                <tr>
                    <th colspan="20"><?= __('组织机构代码证'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?= __('组织机构代码'); ?><?= __('：'); ?></th>
                    <td><?=$data['organization_code']?></td>

                </tr>
                <tr>
                    <th><?= __('组织机构代码证电子版'); ?><?= __('：'); ?></th>
                    <td><img class="pimg" src="<?php if(isset($data['organization_code_electronic'])){echo $data['organization_code_electronic'];}?>"></td>
                </tr>
                </tbody>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
                <thead>
                <tr>
                    <th colspan="20"><?= __('税务登记证'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th><?= __('纳税人识别号'); ?><?= __('：'); ?></th>
                    <td><?=$data['taxpayer_id']?></td>
                    <th><?= __('税务登记证号'); ?><?= __('：'); ?></th>
                    <td><?=$data['tax_registration_certificate']?></td>

                </tr>
                <tr>
                    <th><?= __('税务登记证号电子版'); ?><?= __('：'); ?></th>
                    <td><img class="pimg" src="<?php if(isset($data['tax_registration_certificate_electronic'])){echo $data['organization_code_electronic'];}?>"><?=$data['tax_registration_certificate_electronic']?></td>
                </tr>
                </tbody>
            </table>
          <?php } }?>
    </div>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>

<script type="text/javascript">
	$(document).on('click','.pimg',function(){
    var _this = $(this);//将当前的pimg元素作为_this传入函数  
    imgShow("#outerdiv", "#innerdiv", "#bigimg", _this);  
});
function imgShow(outerdiv, innerdiv, bigimg, _this){  
    var src = _this.attr("src");//获取当前点击的pimg元素中的src属性  
    $(bigimg).attr("src", src);//设置#bigimg元素的src属性  
  
        /*获取当前点击图片的真实大小，并显示弹出层及大图*/  
    $("<img/>").attr("src", src).load(function(){  
        var windowW = $(window).width();//获取当前窗口宽度  
        var windowH = $(window).height();//获取当前窗口高度  
        var realWidth = this.width;//获取图片真实宽度  
        var realHeight = this.height;//获取图片真实高度  
        var imgWidth, imgHeight;  
        var scale = 0.8;//缩放尺寸，当图片真实宽度和高度大于窗口宽度和高度时进行缩放  
          
        if(realHeight>windowH*scale) {//判断图片高度  
            imgHeight = windowH*scale;//如大于窗口高度，图片高度进行缩放  
            imgWidth = imgHeight/realHeight*realWidth;//等比例缩放宽度  
            if(imgWidth>windowW*scale) {//如宽度扔大于窗口宽度  
                imgWidth = windowW*scale;//再对宽度进行缩放  
            }  
        } else if(realWidth>windowW*scale) {//如图片高度合适，判断图片宽度  
            imgWidth = windowW*scale;//如大于窗口宽度，图片宽度进行缩放  
                        imgHeight = imgWidth/realWidth*realHeight;//等比例缩放高度  
        } else {//如果图片真实高度和宽度都符合要求，高宽不变  
            imgWidth = realWidth;  
            imgHeight = realHeight;  
        }  
          $(bigimg).css("width",imgWidth);//以最终的宽度对图片缩放  
          
        var w = (windowW-imgWidth)/2;//计算图片与窗口左边距  
        var h = (windowH-imgHeight)/2;//计算图片与窗口上边距  
        $(innerdiv).css({"top":h, "left":w});//设置#innerdiv的top和left属性  
        $(outerdiv).fadeIn("fast");//淡入显示#outerdiv及.pimg  
    });  
          
        $(outerdiv).click(function(){//再次点击淡出消失弹出层  
            $(this).fadeOut("fast");  
        });  
    }
</script>