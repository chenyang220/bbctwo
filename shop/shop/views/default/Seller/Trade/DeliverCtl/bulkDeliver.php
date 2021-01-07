<?php if (!defined('ROOT_PATH')){exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<link href="<?=$this->view->css_com?>/webuploader.css" rel="stylesheet">

<div class="bulk-drliver-module pl20 pb80">
	<div>
		<a href="javascript:void(0)" class="js-submit bulk-drliver-btn" id="download"><?=__('下载模板')?></a>
	</div>
	<script src="<?=$this->view->js_com?>/webuploader.js"></script>
	<p>
		<span>1.点击下载模板跳转“发货-待发货“界面，导出发货数据。</span>
		<span>2.导出数据到本地后，在模板Excel表中添加订单发货信息，不支持增加表格列或者更换表头。</span>
		<span>3.请准确填写物流公司，快递物流单号。</span>
		<span>4.请先在商家后台添加默认发货地址。</span>
		<span>5.Excel表中标红字段为必填项。</span>
		<span>6.只有待发货且已支付订单才能操作。</span>
		<span>7.本地批量编辑发货单信息，一次性上传，大大缩短添加操作时间。</span>
	</p>
	<dl class="bulk-deliver-up">
		<dt>上传文件</dt>
		<dd>
			<img class="top" src="<?= $this->view->img ?>/i-update.png" alt="icon">
			<div class="iblock top">
				<div class="handle fz0">
					<input type="text" class="js-file-name top">
					<div id="picker" class="top">
					    <?=__('选取文件')?>
					</div>
				</div>
				<p class="mt20 mb20"><span class="block">最大支持2MExcel文件，单次导入发货量最多为1000条</span><span class="block">发货单导入后，可在“订单-已售订单管理-已发货”中查看</span></p>
				<div>
					<a href="javascript:void(0)" class="js-submit btn-icon-submit"><em><?=__('确定上传')?></em><img src="<?= $this->view->img ?>/i-small-up.png" alt="btn"></a>
					<div id = "import_txt"></div>
				</div>
			</div>
		</dd>
	</dl>
	
</div>
<!-- <div class="ncsc-form-goods">

    <dl>
        <dt><i class="required">*</i><?=__('xls文件')?>：</dt>
        <dd>
            <div class="handle">
                <span class="js-file-name"></span>
                
            </div>
        </dd>
    </dl>
    <dl>
        <dt><?=__('文件格式')?>：</dt>
        <dd>
            <p><?=__('xls文件')?></p>
        </dd>
    </dl>
    <dl>
        <dt>&nbsp;</dt>
        <dd>
            
        </dd>
    </dl>

</div> -->
<script>
    $(function() {
        var cat_id;
        var uploader;
        uploadFile();
        var mycat_name = new Array();

        $("a.js-submit").on('click', function(){
            uploader.upload();
        });
        //文件上传
        function uploadFile() {

            uploader = WebUploader.create({

                pick: "#picker",

                accept: {
                    title: 'TaoBaoImport',
                    extensions: 'csv,xls,xlsx'
                },

                swf: BASE_URL + '/shop/static/common/js/Uploader.swf',

                server: SITE_URL + "?ctl=Upload&action=uploadGoodsExcels&typ=json",

                fileVal: 'upfile',

            });

            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on( 'uploadSuccess', function( file,res ) {
                var data = res.data;
                if ( data.state == "SUCCESS" ) {
                    submit(res.data.url_path);
                } else {
                    window.location.href= SITE_URL + '?ctl=Seller_Trade_Order&met=getPhysicalSend';
                }

            });

            // 文件上传失败，现实上传出错。
            uploader.on( 'uploadError', function( file, reason ) {
                window.location.href= SITE_URL + '?ctl=Seller_Trade_Order&met=getPhysicalSend';
                // submit(res.data.url_path);
                //Public.tips.error("<?//=__('上传成功2')?>//！")
            });

            uploader.on( 'beforeFileQueued', function( file ) {
                if ($.inArray(file.ext, ["xls"]) == -1)
                    return Public.tips.warning("<?=__('请上传xls格式的文件')?>");

                var queued_files = this.getFiles("inited");
                if (  queued_files.length > 0 ) this.removeFile(queued_files[0].id);
            });

            uploader.on( 'fileQueued', function( file ) {
				console.log(file.name);
                $(".js-file-name").val("").val(file.name);
            });
        }

        $("#download").click(function () {
            window.location.href= SITE_URL + '?ctl=Seller_Trade_Deliver&met=deliver';
        })

    })
</script>
<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>
