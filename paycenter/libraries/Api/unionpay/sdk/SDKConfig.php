<?php
namespace com\unionpay\acp\sdk;;
include_once 'log.class.php';
include_once 'common.php';

class SDKConfig {
	
	private static $_config = null;
	public static function getSDKConfig(){
		if (SDKConfig::$_config == null ) {
			SDKConfig::$_config = new SDKConfig();
		}
		return SDKConfig::$_config;
	}
	
	private $frontTransUrl;
	private $backTransUrl;
	private $singleQueryUrl;
	private $batchTransUrl;
	private $fileTransUrl;
	private $appTransUrl;
	private $cardTransUrl;
	private $orderTransUrl;
	private $jfFrontTransUrl;
	private $jfBackTransUrl;
	private $jfSingleQueryUrl;
	private $jfCardTransUrl;
	private $jfAppTransUrl;
	private $qrcBackTransUrl;
	private $qrcB2cIssBackTransUrl;
	private $qrcB2cMerBackTransUrl;
	private $zhrzFrontTransUrl;
	private $zhrzBackTransUrl;
	private $zhrzSingleQueryUrl;
	private $zhrzBatchTransUrl;
	private $zhrzAppTransUrl;
	private $zhrzFaceTransUrl;
	
	private $signMethod;
	private $version;
	private $ifValidateCNName;
	private $ifValidateRemoteCert;
	
	private $signCertPath;
	private $signCertPwd;
	private $validateCertDir;
	private $encryptCertPath;
	private $rootCertPath;
	private $middleCertPath;
	private $frontUrl;
	private $frontFailUrl;
	private $backUrl;
	private $secureKey;
	private $logFilePath;
	private $logLevel;

	function __construct(){

        if(!defined('unionpay_environment')) define('unionpay_environment','dev');
        if(!defined('SDK_SIGN_CERT_PWD'))define('SDK_SIGN_CERT_PWD','123456');
        // ######(以下配置为PM环境：入网测试环境用，生产环境配置见文档说明)#######
        $dir = realpath(__DIR__.'/../../../../paycenter/certs/unionpay');

        $this->signMethod = "01";
        $this->version = "5.1.0";
        $this->ifValidateCNName = false;
        $this->ifValidateRemoteCert = false;
        if(unionpay_environment == 'dev'){
            //测试配置
            $this->frontTransUrl =  "https://gateway.test.95516.com/gateway/api/frontTransReq.do";
            $this->backTransUrl =   "https://gateway.test.95516.com/gateway/api/backTransReq.do";
            $this->singleQueryUrl = "https://gateway.test.95516.com/gateway/api/queryTrans.do";
            $this->batchTransUrl =  "https://gateway.test.95516.com/gateway/api/batchTrans.do";
            $this->fileTransUrl =   "https://filedownload.test.95516.com/";
            $this->appTransUrl =    "https://gateway.test.95516.com/gateway/api/appTransReq.do";
            $this->cardTransUrl =   "https://gateway.test.95516.com/gateway/api/cardTransReq.do";
            $this->orderTransUrl =  "https://gateway.test.95516.com/gateway/api/order.do";

            $this->jfFrontTransUrl =  "https://gateway.test.95516.com/jiaofei/api/frontTransReq.do";
            $this->jfBackTransUrl =   "https://gateway.test.95516.com/jiaofei/api/backTransReq.do";
            $this->jfSingleQueryUrl = "https://gateway.test.95516.com/jiaofei/api/queryTrans.do";
            $this->jfCardTransUrl =   "https://gateway.test.95516.com/jiaofei/api/cardTransReq.do";
            $this->jfAppTransUrl =    "https://gateway.test.95516.com/jiaofei/api/appTransReq.do";

            $this->signCertPath = $dir."/test/acp_test_sign.pfx";
            $this->signCertPwd = SDK_SIGN_CERT_PWD;
            $this->encryptCertPath = $dir."/test/acp_test_enc.cer";
            $this->rootCertPath = $dir."/test/acp_test_root.cer";
            $this->middleCertPath =  $dir."/test/acp_test_middle.cer";

            //$this->frontUrl =  "http://paycenter.microshopcloud.cn/paycenter/api/payment/unionpay/return_url.php";
            //$this->backUrl =  "http://paycenter.microshopcloud.cn/paycenter/api/payment/unionpay/return_url.php";
            $this->logFilePath =  "/var/log/";
            $this->logLevel =  "DEBUG";

        }else{
            //正式配置
            $this->frontTransUrl =  "https://gateway.95516.com/gateway/api/frontTransReq.do";
            $this->backTransUrl =   "https://gateway.95516.com/gateway/api/backTransReq.do";
            $this->singleQueryUrl = "https://gateway.95516.com/gateway/api/queryTrans.do";
            $this->batchTransUrl =  "https://gateway.95516.com/gateway/api/batchTrans.do";
            $this->fileTransUrl =   "https://filedownload.95516.com/";
            $this->appTransUrl =    "https://gateway.95516.com/gateway/api/appTransReq.do";
            $this->cardTransUrl =   "https://gateway.95516.com/gateway/api/cardTransReq.do";
            $this->orderTransUrl =  "https://gateway.95516.com/gateway/api/order.do";

            $this->jfFrontTransUrl =  "https://gateway.95516.com/jiaofei/api/frontTransReq.do";
            $this->jfBackTransUrl =   "https://gateway.95516.com/jiaofei/api/backTransReq.do";
            $this->jfSingleQueryUrl = "https://gateway.95516.com/jiaofei/api/queryTrans.do";
            $this->jfCardTransUrl =   "https://gateway.95516.com/jiaofei/api/cardTransReq.do";
            $this->jfAppTransUrl =    "https://gateway.95516.com/jiaofei/api/appTransReq.do";

            $this->signCertPath = $dir."/test/acp_test_sign.pfx";
            $this->signCertPwd = SDK_SIGN_CERT_PWD;
            $this->encryptCertPath = $dir."/test/acp_test_enc.cer";
            $this->rootCertPath = $dir."/test/acp_test_root.cer";
            $this->middleCertPath =  $dir."/test/acp_test_middle.cer";

            //$this->frontUrl =  "http://paycenter.microshopcloud.cn/paycenter/api/payment/unionpay/return_url.php";
            //$this->backUrl =  "http://paycenter.microshopcloud.cn/paycenter/api/payment/unionpay/return_url.php";
            $this->logFilePath =  "/var/log/";
            $this->logLevel =  "DEBUG";

        }


	}

	public function __get($property_name)
	{
		if(isset($this->$property_name))
		{
			return($this->$property_name);
		}
		else
		{
			return(NULL);
		}
	}

}


