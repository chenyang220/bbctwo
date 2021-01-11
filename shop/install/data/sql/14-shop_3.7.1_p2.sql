-- 商家微信公众号行业设置
DROP TABLE IF EXISTS `yf_seller_wxpublic_industry`;
CREATE TABLE `yf_seller_wxpublic_industry` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `shop_id` int(10) DEFAULT NULL COMMENT '商店店铺id',
  `industry_id1` int(10) DEFAULT NULL COMMENT '微信公众号主行业',
  `industry_id2` int(10) DEFAULT NULL COMMENT '微信公众号副行业',
  `opt_time` int(10) DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ind_shop_id` (`shop_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商家微信公众号行业设置';

-- 商家微信公众号模板消息推送开关表
DROP TABLE IF EXISTS `yf_seller_wxpublic_tplmsgstate`;
CREATE TABLE `yf_seller_wxpublic_tplmsgstate` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `shop_id` int(10) NOT NULL COMMENT '商家店铺id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（1：开启；0：关闭）',
  `opt_time` int(10) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ind_shop_id` (`shop_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商家微信公众号模板消息推送开关表';

-- 商家微信公众号模板消息表
DROP TABLE IF EXISTS `yf_seller_wxpublic_tplmessage`;
CREATE TABLE `yf_seller_wxpublic_tplmessage` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `shop_id` int(10) DEFAULT NULL COMMENT '卖家店铺id',
  `user_id` int(10) DEFAULT NULL COMMENT '用户id（下单业务，则是buyer_user_id；商家通知则是seller_user_id等等）',
  `user_name` varchar(20) DEFAULT NULL COMMENT '用户姓名（或者是昵称）',
  `type` tinyint(2) DEFAULT NULL COMMENT '通知类型（1：下单支付通知；2：物流发货；）',
  `tpl_data` varchar(500) DEFAULT NULL COMMENT '模板数据',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `send_time` int(10) DEFAULT NULL COMMENT '发送时间',
  `result` varchar(500) DEFAULT NULL COMMENT '返回结果',
  PRIMARY KEY (`id`),
  KEY `ind_shop_id` (`shop_id`) USING BTREE,
  KEY `index_user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 货到付款
ALTER TABLE yf_goods_common  ADD is_commodity  tinyint(1) NOT NULL COMMENT '货到付款';

-- 代金券
ALTER TABLE yf_voucher_base MODIFY COLUMN `voucher_order_id` varchar(50) NOT NULL COMMENT '使用该代金券的订单编号';

-- 地区
ALTER TABLE yf_express  ADD express_abbreviation  varchar(50) NOT NULL COMMENT '简称';


-- 用户地址填加标签
ALTER TABLE `yf_user_address`
ADD COLUMN `user_address_attribute`  int(3) NOT NULL DEFAULT 1 COMMENT '1、公司  ,2、家 ,3、学校';
