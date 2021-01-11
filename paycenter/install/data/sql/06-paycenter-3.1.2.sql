ALTER TABLE  `pay_consume_withdraw` ADD  `remark` VARCHAR( 255 )  NULL COMMENT  '平台审核备注';

ALTER TABLE `pay_payment_channel`
MODIFY COLUMN `payment_channel_code`  varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '代码名称' AFTER `payment_channel_id`;

/* 初始化pay_payment_channel表 */
INSERT INTO `pay_payment_channel` VALUES ('1', 'alipay', '支付宝电脑端', 'paycenter/static/default/images/zhifubao.png', '{\"appid\":\"\",\"rsaPrivateKey\":\"\",\"alipayrsaPublicKey\":\"\"}', '0', 'both', '1', '1');
INSERT INTO `pay_payment_channel` VALUES ('4', 'wx_native', '微信支付电脑端', 'paycenter/static/default/images/weixinzhifu.png', '{\"appid\":\"\",\"key\":\"\",\"mchid\":\"\",\"appsecret\":\"\",\"apiclient_key\":\"\",\"apiclient_cert\":\"\"}', '0', 'both', '1', '1');
INSERT INTO `pay_payment_channel` VALUES ('9', 'baitiao', '白条', '', '{\"baitiao_account\":\"12323\",\"baitiao_key\":\"12323\",\"baitiao_partner\":\"12323\"}', '0', 'both', '1', '1');
INSERT INTO `pay_payment_channel` VALUES ('12', 'unionpay', '银联支付', 'paycenter/static/default/images/unionpay.png', '{\"unionpay_account\":\"\",\"unionpay_key\":\"\",\"unionpay_partner\":\"\"}', '0', '', '1', '1');
INSERT INTO `pay_payment_channel` VALUES ('13', 'app_wx_native', '微信支付手机端（原生）', '', '{\"appid\":\"wx918dd3a1bcf41dfa\",\"key\":\"\",\"mchid\":\"\",\"appsecret\":\"\"}', '0', '', '1', '1');
INSERT INTO `pay_payment_channel` VALUES ('14', 'app_h5_wx_native', '微信支付手机端（套壳）', '', '{\"appid\":\"\",\"key\":\"\",\"mchid\":\"\",\"appsecret\":\"\"}', '0', '', '1', '1');
INSERT INTO `pay_payment_channel` VALUES ('15', 'alipayMobile', '支付宝手机端', '', '{\"appid\":\"\",\"rsaPrivateKey\":\"\",\"alipayrsaPublicKey\":\"\"}', '0', '', '1', '1');
INSERT INTO `pay_payment_channel` VALUES ('16', 'wxapp', '小程序支付', '', '{\"appid\":\"wx1975fc0835746e09\",\"key\":\"\",\"mchid\":\"\",\"appsecret\":\"\",\"apiclient_key\":null,\"apiclient_cert\":null}', '0', 'wap', '0', '0');
INSERT INTO `pay_payment_channel` VALUES ('17', 'seller_app_h5_wx_native', '微信套壳支付（商户）', '', '{\"appid\":\"\",\"key\":\"\",\"mchid\":\"\",\"appsecret\":\"\"}', '0', '', '1', '1');
INSERT INTO `pay_payment_channel` VALUES ('18', 'im_wxapp', 'im微信支付', '', '{\"im_wxapp_account\":\"\",\"im_wxapp_key\":\"\\\",\"im_wxapp_partner\":\"\"}', '0', '', '1', '1');