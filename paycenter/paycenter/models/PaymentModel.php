<?php
/**
 * 
 * 通过这个类，统一管理支付类。
 * 
 * @category   Framework
 * @package    Db
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010 远丰仁商
 * @version    1.0
 * @todo       
 */
class PaymentModel
{

    /**
     * 构造函数
     *
     * @access    private
     */
    public function __construct()
    {
    }

    /**
     * 得到支付句柄
     *
     * @param array $channel   使用的支付驱动
     * @param array $reset_config 重新设定支付配置、覆盖支付默认选项
     *
     * @return Object   Payment Object
     *
     * @access public
     */
    public static function create($channel, $reset_config= [],$openid=null,$body=null,$total_fee=null,$uorder_id='')
    {
        if ($channel != 'jh_app_pay') {
            $Payment_ChannelModel = new Payment_ChannelModel();
            $config_row = $Payment_ChannelModel->getChannelConfig($channel);

            //大华捷通支付不需要在此判断支付配置
            if (!$config_row && $channel!='yunshanpc')
            {
                throw new Exception(_('支付配置数据错误!'));
            }

            $config_row = $reset_config ? array_merge($config_row, $reset_config) : $config_row;
            $PaymentModel = null;
            
            //如果开启大华捷通支付、那么走大华捷通支付的小程序支付
            $yunshan_status = Web_ConfigModel::value('yunshan_status');
            if ($channel == 'wxapp' && $yunshan_status == 1) {
                $channel = 'ylwxapp';
            }
        }
        switch ($channel) {
             case 'alipay':
                    if (!Yf_Utils_Device::isMobile())
                    {
                        $PaymentModel = new Payment_Alipay($config_row);
                    }
                    else
                    {
                        $PaymentModel = new Payment_AlipayWap($config_row);
                    }
                    break;
             case 'tenpay':
                $PaymentModel = new Payment_TenpayModel($config_row);
                break;
             case 'tenpay_wap':
                $PaymentModel = new Payment_TenpayWapModel($config_row);
                break;
             case 'unionpay':
                    $PaymentModel = new Payment_UnionPayModel($config_row);
                break;
             case 'paypal':
                    $PaymentModel = new Payment_Paypal($config_row);
                break;
             case 'yunshanpc':
                   $PaymentModel = new Payment_QuickPassPayModel(); // 新增云闪付开发接口功能
                break;
            case 'ylwxapp':
                $config_row['openid'] = $openid;
                $PaymentModel = new Payment_WxappPayModel($config_row);
                break;
             case 'app_h5_wx_native':
             case 'wx_native':
                    //微信变量, 不变动程序,修正数据
                    !defined('APPID_DEF') && define('APPID_DEF', $config_row['appid']);
                    !defined('MCHID_DEF') && define('MCHID_DEF', $config_row['mchid']);
                    !defined('KEY_DEF') && define('KEY_DEF', $config_row['key']);
                    !defined('APPSECRET_DEF') && define('APPSECRET_DEF', $config_row['appsecret']);

                    !defined('SSLCERT_PATH_DEF') && define('SSLCERT_PATH', LIB_PATH . '/Api/wx/cert/apiclient_cert.pem');
                    !defined('SSLKEY_PATH_DEF') && define('SSLKEY_PATH', LIB_PATH . '/Api/wx/cert/apiclient_key.pem');
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false || $_GET['trade_type'] == 'JSAPI') {
                        // $PaymentModel = new Payment_WxNativeModel($config_row);
                        $PaymentModel = new Payment_WxJsModel($config_row);
                    } else {
                        if (Yf_Utils_Device::isMobile()) {
                            $PaymentModel = new Payment_WxJsModel($config_row);
                        } else {
                            $PaymentModel = new Payment_WxNativeModel($config_row);
                        }
                    }
                 break;
             case 'bestpay':
                 $PaymentModel = new Payment_BestpayModel($config_row);   
                 break;
            case 'app_wx_native':
                !defined('APPID_DEF') && define('APPID_DEF', $config_row['appid']);
                !defined('MCHID_DEF') && define('MCHID_DEF', $config_row['mchid']);
                !defined('KEY_DEF') && define('KEY_DEF', $config_row['key']);
                !defined('APPSECRET_DEF') && define('APPSECRET_DEF', $config_row['appsecret']);
                !defined('SSLCERT_PATH_DEF') && define('SSLCERT_PATH', LIB_PATH . '/Api/wx/cert/apiclient_cert.pem');
                !defined('SSLKEY_PATH_DEF') && define('SSLKEY_PATH', LIB_PATH . '/Api/wx/cert/apiclient_key.pem');
                $PaymentModel = new Payment_WxNativeModel($config_row);
                break;
            case 'wxapp':
                !defined('APPID_DEF') && define('APPID_DEF', $config_row['appid']);
                !defined('MCHID_DEF') && define('MCHID_DEF', $config_row['mchid']);
                !defined('KEY_DEF') && define('KEY_DEF', $config_row['key']);
                !defined('APPSECRET_DEF') && define('APPSECRET_DEF', $config_row['appsecret']);

                !defined('SSLCERT_PATH_DEF') && define('SSLCERT_PATH', LIB_PATH . '/Api/wxapp/cert/apiclient_cert.pem');
                !defined('SSLKEY_PATH_DEF') && define('SSLKEY_PATH', LIB_PATH . '/Api/wxapp/cert/apiclient_key.pem');
                $PaymentModel = new Payment_Wxapp($openid,$body,$total_fee,$uorder_id);
                break;
            case 'jh_app_pay':
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false || $_GET['trade_type'] == 'JSAPI') {
                    $type = "jsdk";
                    $PaymentModel = new Payment_JhWxAppModel($type);
                } else {
                    if (Yf_Utils_Device::isMobile()) {
                        $PaymentModel = new Payment_JhWxAppModel();
                    } else {
                        $PaymentModel = new Payment_JhWxAppModel();
                    }
                }
                break;
                default:
        }
        return $PaymentModel;
    }

    /**
     * 得到支付句柄
     *
     * @param array  $channel   使用的支付驱动
     *
     * @return Object   Payment Object
     *
     * @access public
     */
    public static function get($channel)
    {
        return self::create($channel);
    }
}
?>