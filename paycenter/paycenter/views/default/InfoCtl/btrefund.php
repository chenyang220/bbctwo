<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>

<link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/ui.min.css">
<link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.datetimepicker.js" charset="utf-8"></script>
    <div class="pc_user_about">
        <div class="recharge-content-top content-public clearfix">
            <ul class="tab">
                <li><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btinfo">白条概览</a></li>
                <li><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btbill">白条账单</a></li>
                <li class="active"><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btrefund"><?=__('白条还款')?></a></li>
            </ul>
        </div>
    </div>
    <div id="outerdiv" style="position:fixed;top:0;left:0;background:rgba(0,0,0,0.7);z-index:2;width:100%;height:100%;display:none;">
        <div id="innerdiv" style="position:absolute;">
            <img id="bigimg" style="border:5px solid #fff;" src="" />
        </div>
    </div>
    <div class="main_cont wrap clearfix">
        <form id="form" name="form" action="" method="post" >
            <input name="from"  type="hidden" id='page_from' value="bt"/>
            <div class="account_left fl">
                <div class="account_mes">
                    <h4><?=__('白条还款')?></h4>
                    <table class="account_table">
                        <tbody>
                        <tr>
                            <td class="check_name"><?=__('还款凭证：')?></td>
                            <td>
                                <div class="user-avatar">
                        <span>
                            <input type="hidden" id="img_input">
                               <img  id="image_img"  src="<?=$data['certificate']?Img::url($data['certificate']):'holder.js/240x151?text=预览区'; ?>"  width="" height="151" nc_type="avatar">
                        </span>
                                </div>
                                <p class="hint mt5"><span style="color:orange;"><?=__('正面照尺寸为')?><span class="phosize">240x151</span><?=__('像素，请根据系统操作提示进行裁剪并生效。')?></span></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="check_name"><?=__('上传凭证：')?></td>
                            <td>
                                <div > 
                                    <a href="javascript:void(0);">
                                        <span>
                                            <input name="certificate" id="certificate" type="hidden" value="<?=$data['certificate']?>" />
                                        </span>
                                        <p id='upload' style="float:left;" class="bbc_btns"><i class="iconfont icon-upload-alt"></i><?=__('图片上传')?></p>
                                    </a> </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="hidden" name="debug" value="1"></td>
                            <td><input type="submit" value="<?=__('提交')?>" class="submit btn_active"></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="account_right fr">
                <div class="account_right_con">
                    <ul class="cert_instructions">
                        <li>
                            <h5><?=__('上传还款凭证')?></h5>
                            <p><?=__('上传还款凭证后，管理员会在后台进行审核，审核成功后，减除响应的金额，可一次全部还款和部分还款，管理员将在1~2个工作日进行审核，如果成功将扣除还款部分。')?></p>
                        </li>
                        <li>
                            <h5><?=__('逾期影响')?></h5>
                            <p><?=__('账期结束后，必须还清所有的欠款，否则无法继续通过白条购买，白条功能将被禁用。还款后白条功能将恢复使用。')?></p>
                        </li>
                        <li>
                            <h5><?=__('温馨提示')?></h5>
                            <p><?=__('请及时还清白条账单，避免不必要的麻烦。')?></p>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
    <link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?=$this->view->js?>/webuploader.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/upload/upload_image.js" charset="utf-8"></script>
    <link href="<?= $this->view->css ?>/webuploader.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="<?php echo $this->view->js; ?>/holder.js" ></script>
<script>
    //图片上传
    $(function(){
        $('#upload').on('click', function () {
            $.dialog({
                title: "<?= __('图片裁剪'); ?>",
                content: "url: <?= Yf_Registry::get('url') ?>?ctl=Upload&met=cropperImage&typ=e",
                data: { width: 0, height: 0, callback: callback },    // 需要截取图片的宽高比例
                width: '800px',
                /*height: '310px',*/
                lock: true
            })
        });

        function callback ( respone , api ) {
            $('#image_img').attr('src', respone.url);
            $('#certificate').attr('value', respone.url);
            api.close();
        }

    })
    $(document).ready(function(){
        var ajax_url = '<?= Yf_Registry::get('url');?>?ctl=Info&met=editCreditReturn&typ=json';
        $('#form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: false,
            fields : {
                'certificate':'required;',
            },
            valid:function(form){
                //表单验证通过，提交表单
                $.ajax({
                    url: ajax_url,
                    data:$("#form").serialize(),
                    success:function(a){
                        if(a.status == 200)
                        {
                            $("input[type='submit']").hide();
                            Public.tips.success("<?=__('操作成功')?>");
                            location.href= "<?= Yf_Registry::get('url');?>?ctl=Info&met=btinfo";
                        }
                        else
                        {
                            if(typeof(a.msg) != 'undefined'){
                                Public.tips.error(a.msg);
                            }else{
                                Public.tips.error("<?=__('操作失败')?>");
                            }
                            return false;
                        }
                    }
                });
            }
        });
    });

$(document).on('click','#image_img',function(){
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
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>