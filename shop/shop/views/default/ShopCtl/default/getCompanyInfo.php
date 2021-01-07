<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/Group-integral.css"/>
<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css"/>

<script type="text/javascript" src="<?= $this->view->js_com ?>/jquery.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.toastr.min.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>

<div class="bbc-voucher-exchange voucher-exchange">
    <h3 class="tc mb10">网店经营者营业执照信息</h3>
    <span class="block mb10">根据国家工商总局《网络交易管理办法》要求对网店营业执照信息公示如下：</span>
	<div class="zizhi-verify tc">
		<p class="mb20">
			<span>请输入下图中的验证码后查看：</span>
			<input type="text" name="code" id="code" maxlength="6" class='text w110' placeholder="<?= __('请输入验证码') ?>" default="<i class=&quot;i-def&quot;></i><?= __('看不清？点击图片更换验证码') ?>"/>
			<button id="sure">确定</button>
		</p>
		
		<p>
			<img id="code-img" title="<?= __('换一换') ?>" class="img-code form-style-code" src='./libraries/rand_func.php'/>
			<a onClick="get_randfunc();">看不清？换一张</a>
		</p>
		
	</div>
    
</div>

<script type="text/javascript">
    var api = frameElement.api, callback = api.data.callback;
    var shop_id = api.data.shop_id;
    var SITE_URL = "<?=Yf_Registry::get('url')?>";

    function get_randfunc() {
        var sj = new Date();
        url = $("#code-img").attr("src");
        $("#code-img").attr("src",url + '?' + sj);
    }

    $("#sure").click(function () {
        var code = $("#code").val();
        $('.ui_close').click();
        $.ajax({
            url: SITE_URL + "?ctl=Shop&met=getMobileYzm&typ=json",
            data: {code: code},
            dataType: "json",
            contentType: "application/json;charset=utf-8",
            async: false,
            success: function (res) {
                if (res.status == 200) {
                    callback && "function" == typeof callback && callback(res.data, res.status, window, res.msg)
                } else {
                    parent.Public.tips.error(res.msg);
                }
            }
        });
    })

</script>