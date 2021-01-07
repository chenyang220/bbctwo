<head>
    <meta charset="UTF-8">
    <title><?=Web_ConfigModel::value('site_name')?> - <?=Web_ConfigModel::value('title')?></title>
    <meta name="description" content="<?=Web_ConfigModel::value('description')?>" />
    <meta name="Keywords" content="<?=Web_ConfigModel::value('keyword')?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/base.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/tips.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/headfoot.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/iconfont/iconfont.css"> -->
    <link rel="stylesheet" href="http://at.alicdn.com/t/font_ucm2vzrmvdfjq0k9.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/palyCenter.css">
    <link rel="stylesheet" type="text/css" href="<?=$this->view->css?>/dialog/green.css">
    <link href="<?= $this->view->css ?>/validator/jquery.validator.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
    <script src="<?=$this->view->js?>/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="<?=$this->view->js?>/respond.js"></script>
    <script src="<?=$this->view->js?>/jquery.cookie.js"></script>
    <script src="<?=$this->view->js?>/cropper.js"></script>
    <script src="<?=$this->view->js?>/jquery.dialog.js"></script>
    <script src="<?=$this->view->js?>/jquery.toastr.min.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/validator/local/zh_CN.js" charset="utf-8"></script>
    <script src="<?=$this->view->js?>/common.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/qrcode.min.js"></script>
    <script src="<?=$this->view->js?>/jquery.base64.js" type="text/javascript"></script>
    <script>
        var BASE_URL = "<?=Yf_Registry::get('base_url')?>";
        var SITE_URL = "<?=Yf_Registry::get('url')?>";
        var INDEX_PAGE = "<?=Yf_Registry::get('index_page')?>";
        var STATIC_URL = "<?=Yf_Registry::get('static_url')?>";
        var U_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";
        var SHOP_URL = "<?=Yf_Registry::get('shop_api_url')?>";
        var UCENTER_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";
        var DOMAIN = document.domain;
        var WDURL = "";
        var SCHEME = "default";
    </script>
</head>
<body>
<script>
    var Access_mode = "<?php echo $trade_row['Access_mode']?>";
    var merOrderId = "<?php echo $merOrderId;?>"
    if(Access_mode=='mobile_phone'){
        // var QR_code = '<?php echo $QR_code?>';
        // var pay_way = "alipay";
        // var data = JSON.parse(QR_code);
        // var input_data  = {pay_way:pay_way,data:data}
        // input_data = JSON.stringify(input_data);
        // var info = $.base64.encode(input_data);
        // console.log(input_data);
        // console.log(info);
        //location.href= "<?php echo $QR_code['data']['payData']['payPageUrl']?>";
    }else if(Access_mode=='mobile_APP'){
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        var pay_way = '<?php echo $pay_way?>';
        var QR_code = '<?php echo $QR_code?>';
        var data = JSON.parse(QR_code);
        var input_data  = {pay_way:pay_way,merOrderId:merOrderId,data:data}
            input_data = JSON.stringify(input_data);
        var info = $.base64.encode(input_data);
        if (isAndroid==true){
            YongLian.unionPay(info);
        }else if(isiOS==true){
            window.webkit.messageHandlers.unionPay.postMessage(info); 
        }
    }
    function notify_url(){
        location.href=SHOP_URL+"?ctl=Buyer_Order&met=physical";
    }
    function alipay(){
        order_id = "<?php echo $data['inorder']?>";
        location.href = "<?=Yf_Registry::get('shop_wap_url')?>"+"/tmpl/member/order_detail.html?fanhui=1&order_id="+order_id;
    }
</script>
</div>
</body>
</html>
