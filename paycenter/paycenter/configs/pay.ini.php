<?php
/**
 * Created by PhpStorm.
 * User: tech0
 * Date: 2018/7/11
 * Time: 10:13
 */
$pay_config = array(
    'pay_code' => array(
        'alipay_code'=>array(
            'alipay', //支付宝支付
        ),
        'wx_code' => array(
            'wx_native',//微信公众号支付
            'app_wx_native',//原生微信手机端支付
            'app_h5_wx_native',//微信套壳支付（买家）
            'seller_app_h5_wx_native',//微信套壳支付（商户）
            'im_wxapp', //im微信支付
            'wxapp', //小程序支付
        ),
        'other' => array(
            'unionpay'  //银联
        ),
       'baitiao'=>'baitiao',//白条支付
       'balance'=>'balance',//余额
       //'unionpay',//银联
    ),
    'pay_type' => array(
      'APP' => 'app_wx_native',
      'APPH5' => 'app_h5_wx_native',
      'APP_H5' => 'seller_app_h5_wx_native',
      'WXAPP' => 'wxapp',
      'IM_WXAPP' => 'im_wxapp',
    ),

);


return $pay_config;