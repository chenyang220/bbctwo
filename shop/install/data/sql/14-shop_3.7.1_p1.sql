-- 新增商品分类退货期
ALTER TABLE `yf_goods_cat` ADD  `return_goods_limit` tinyint(2) default '-1' NOT NULL COMMENT '分类商品退货期';
-- 分类写死
insert into `yf_goods_cat` (`cat_id`, `cat_name`, `cat_parent_id`, `cat_pic`, `type_id`, `cat_commission`, `cat_is_wholesale`, `cat_is_virtual`, `cat_templates`, `cat_displayorder`, `cat_level`, `cat_show_type`, `return_goods_limit`) values('9000','升级礼包','0','','-1','0','0','1','0','0','0','0','0');
insert into `yf_goods_cat` (`cat_id`, `cat_name`, `cat_parent_id`, `cat_pic`, `type_id`, `cat_commission`, `cat_is_wholesale`, `cat_is_virtual`, `cat_templates`, `cat_displayorder`, `cat_level`, `cat_show_type`, `return_goods_limit`) values('9001','升级礼包','9000','','0','0','0','1','0','0','0','0','-1');
insert into `yf_goods_cat` (`cat_id`, `cat_name`, `cat_parent_id`, `cat_pic`, `type_id`, `cat_commission`, `cat_is_wholesale`, `cat_is_virtual`, `cat_templates`, `cat_displayorder`, `cat_level`, `cat_show_type`, `return_goods_limit`) values('9002','分销升级礼包','9001','','0','0','0','1','0','0','0','0','-1');
-- 分销小店表

CREATE TABLE `yf_distribution_shop` (
  `distribution_shop_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分销小店ID',
  `user_id` int(11) unsigned NOT NULL COMMENT '归属用户ID',
  `distribution_name` varchar(50) DEFAULT NULL COMMENT '分销小店名称',
  `distribution_logo` varchar(255) DEFAULT NULL COMMENT '分销小店LOGO',
  `distribution_desc` varchar(255) DEFAULT NULL COMMENT '分销小店介绍',
  `distribution_phone` varchar(20) DEFAULT NULL COMMENT '分销小店联系方式',
  `distribution_template` tinyint(1) DEFAULT '1' COMMENT '分销小店模板',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`distribution_shop_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;


-- 分销商品表
CREATE TABLE `yf_distribution_goods` (
  `distribution_goods_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分销精选商品id',
  `distribution_shop_id` int(11) NOT NULL COMMENT '分销店铺ID',
  `goods_common_id` int(11) NOT NULL COMMENT '分销商品ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `shop_id` int(11) NOT NULL COMMENT '商家店铺ID',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐标志0不推荐1推荐，默认0',
  PRIMARY KEY (`distribution_goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=275 DEFAULT CHARSET=utf8;

-- 发送邮件的配置参数修改
UPDATE `yf_web_config` SET `config_key`='email_addr', `config_value`='yelongsai@163.com', `config_type`='email', `config_enable`='1', `config_comment`='', `config_datatype`='string' WHERE (`config_key`='email_addr');
UPDATE `yf_web_config` SET `config_key`='email_host', `config_value`='smtp.163.com', `config_type`='email', `config_enable`='1', `config_comment`='', `config_datatype`='string' WHERE (`config_key`='email_host');
UPDATE `yf_web_config` SET `config_key`='email_id', `config_value`='远丰电商', `config_type`='email', `config_enable`='1', `config_comment`='', `config_datatype`='string' WHERE (`config_key`='email_id');
UPDATE `yf_web_config` SET `config_key`='email_pass', `config_value`='325604793069', `config_type`='email', `config_enable`='1', `config_comment`='', `config_datatype`='string' WHERE (`config_key`='email_pass');
UPDATE `yf_web_config` SET `config_key`='email_port', `config_value`='25', `config_type`='email', `config_enable`='1', `config_comment`='', `config_datatype`='number' WHERE (`config_key`='email_port');
UPDATE `yf_web_config` SET `config_key`='email_test', `config_value`='', `config_type`='email', `config_enable`='1', `config_comment`='', `config_datatype`='string' WHERE (`config_key`='email_test');


ALTER TABLE `yf_special_set` ADD COLUMN `is_from`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '0、pc端  1、wap端 2、小程序端';
ALTER TABLE `yf_special_column` ADD COLUMN `is_from`  tinyint(2) NOT NULL DEFAULT 0 COMMENT '0、pc端  1、wap端 2、小程序端';

-- 商品表新增分佣比例字段
ALTER TABLE `yf_goods_common` ADD COLUMN `common_c_first` decimal(4,2) DEFAULT '0.00' COMMENT '分销客一级分佣比例';
ALTER TABLE `yf_goods_common` ADD COLUMN `common_c_second` decimal(4,2) DEFAULT '0.00' COMMENT '分销客二级分佣比例';
ALTER TABLE `yf_goods_common` ADD COLUMN `common_a_first` decimal(4,2) DEFAULT '0.00' COMMENT '分销掌柜一级分佣比例';
ALTER TABLE `yf_goods_common` ADD COLUMN `common_a_second` decimal(4,2) DEFAULT '0.00' COMMENT '分销掌柜二级分佣比例';

-- 会员表增加分销员身份标志
ALTER TABLE `yf_user_info` ADD COLUMN `distributor_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分销员身份标志0分销客1分销掌柜 默认0';

-- 订单商品表增加分销升级礼包订单标志
ALTER TABLE `yf_order_goods` ADD COLUMN `identity_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分销升级商品订单标志0普通商品1升级礼包商品 默认0';


ALTER TABLE `yf_goods_common`
ADD COLUMN `transport_template_id`  int(10) NOT NULL COMMENT '运费模板ID';


ALTER TABLE `yf_groupbuy_base`
ADD COLUMN `is_del`  int(2) NOT NULL DEFAULT 0 COMMENT '0、未删除  1、已删除';


ALTER TABLE `yf_user_info` ADD COLUMN  `district_id` int(11) DEFAULT NULL COMMENT '地区id';
ALTER TABLE `yf_order_return` ADD COLUMN  `district_id` int(11) DEFAULT NULL COMMENT '地区id';

-- 会员佣金结算记录
CREATE TABLE `yf_settlement_income` (
  `income_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `settlement_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算金额',
  `settlement_order_id` varchar(50) NOT NULL COMMENT '结算订单号',
  `settlement_time` datetime NOT NULL COMMENT '结算时间',
  `settlement_level` int(11) NOT NULL COMMENT '分佣级别',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `income_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分佣类别1分销订单分佣2礼包订单奖励',
  PRIMARY KEY (`income_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- 结算佣金提现日志
CREATE TABLE `yf_withdraw_log` (
  `withdraw_log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `withdraw_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `withdraw_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '提现状态0失败1成功默认0',
  `withdraw_time` datetime NOT NULL COMMENT '提现发起时间',
  `user_id` int(11) NOT NULL COMMENT '提现发起人ID',
  PRIMARY KEY (`withdraw_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 用户表添加邀请下级总数字段
ALTER TABLE `yf_user_info` ADD COLUMN`subordinate_num` int(10) NOT NULL DEFAULT '0' COMMENT '邀请注册的下级总人数';  


INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('mobile_app', '', 'qrcode', '1', '', 'string');
INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('mobile_wap', '', 'qrcode', '1', '', 'string');
INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('mobile_wx_code', '', 'qrcode', '1', '', 'string');

ALTER TABLE `yf_order_base` ADD COLUMN`order_send_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配送方式';  

ALTER TABLE `yf_order_base` ADD COLUMN`contact_name` varchar(255) NOT NULL COMMENT '送货员联系姓名';  
ALTER TABLE `yf_order_base` ADD COLUMN`contact_mobile` varchar(255) NOT NULL COMMENT '送货员联系电话';  
ALTER TABLE `yf_order_base` ADD COLUMN`contact_remarks` varchar(255) NOT NULL COMMENT '送货备注';  

-- 秒杀
ALTER TABLE `yf_order_base` ADD COLUMN`is_seckill` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是秒杀订单 1-是';  
ALTER TABLE `yf_order_goods` ADD COLUMN`seckill_goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '秒杀商品表id';  
ALTER TABLE `yf_goods_common` ADD COLUMN`common_is_miao` tinyint(1) NOT NULL DEFAULT '0' COMMENT '秒杀标识';  


INSERT INTO `yf_web_config` VALUES ('promotion_seckill_price', '1000', 'seckill', '1', '秒杀活动价格', 'string');
INSERT INTO `yf_web_config` VALUES ('promotion_seckill_time', '30', 'seckill', '1', '秒杀订单取消时间', 'string');
INSERT INTO `yf_web_config` VALUES ('seckill_allow', '1', 'promotion', '1', '秒杀活动是否开启', 'string');
INSERT INTO `yf_web_config` VALUES ('seckill_is_home', '', 'seckill_is_home', '1', '秒杀是否首页推荐', 'string');



CREATE TABLE `yf_seckill_base` (
  `seckill_id` int(11) NOT NULL AUTO_INCREMENT,
  `seckill_name` varchar(255) DEFAULT NULL COMMENT '活动名称',
  `seckill_start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `seckill_end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `shop_id` int(11) DEFAULT NULL COMMENT '商家id',
  `user_nick_name` varchar(255) DEFAULT NULL COMMENT '用户名',
  `shop_name` varchar(255) DEFAULT NULL COMMENT '店铺名',
  `seckill_time_slot` tinyint(1) DEFAULT NULL COMMENT '秒杀时间段',
  `seckill_lower_limit` int(11) DEFAULT NULL COMMENT '限购',
  `seckill_state` tinyint(1) DEFAULT '0' COMMENT '状态， 0-待审核/1-正常/2-结束/3-管理员关闭',
  `order_cancel_time` int(11) DEFAULT NULL COMMENT '订单取消时间',
  `combo_id` int(11) DEFAULT NULL,
  `apply_time` datetime DEFAULT NULL COMMENT '申请时间',
  `day_start_time` int(11) DEFAULT NULL COMMENT '每日开始时间',
  `day_end_time` int(11) DEFAULT NULL COMMENT '每日结束时间',
  `seckill_start_date` date DEFAULT NULL,
  `seckill_end_date` date DEFAULT NULL,
  PRIMARY KEY (`seckill_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

CREATE TABLE `yf_seckill_goods` (
  `seckill_goods_id` int(11) NOT NULL AUTO_INCREMENT,
  `seckill_id` int(11) DEFAULT NULL COMMENT '活动id',
  `seckill_name` varchar(255) DEFAULT NULL COMMENT '活动名字',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品id',
  `common_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL COMMENT '店铺id',
  `goods_name` varchar(255) DEFAULT NULL COMMENT '商品名',
  `goods_price` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `seckill_price` decimal(10,2) DEFAULT NULL COMMENT '秒杀价',
  `goods_image` varchar(255) DEFAULT NULL COMMENT '商品图',
  `goods_start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `goods_end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `goods_lower_limit` int(11) DEFAULT NULL COMMENT '限购',
  `seckill_goods_state` tinyint(4) DEFAULT NULL COMMENT '状态',
  `goods_time_slot` int(11) DEFAULT NULL COMMENT '秒杀时间段',
  `goods_stock` int(11) DEFAULT NULL COMMENT '商品库存',
  `seckill_stock` int(11) DEFAULT NULL COMMENT '秒杀库存',
  `seckill_sold` int(11) DEFAULT '0' COMMENT '已售',
  `seckill_stock_s` int(11) DEFAULT NULL COMMENT '秒杀库存复制体',
  `day_start_time` int(11) DEFAULT NULL,
  `day_end_time` int(11) DEFAULT NULL,
  `goods_start_date` date DEFAULT NULL,
  `goods_end_date` date DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL COMMENT '分类id',
  PRIMARY KEY (`seckill_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

CREATE TABLE `yf_seckill_combo` (
  `combo_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `user_nickname` varchar(255) DEFAULT NULL,
  `shop_name` varchar(255) DEFAULT NULL,
  `combo_start_time` datetime DEFAULT NULL,
  `combo_end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`combo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- 秒杀

-- 运费按体积计算

ALTER TABLE `yf_goods_common` ADD COLUMN `common_length` decimal(10,2) NOT NULL COMMENT '体积 长';
ALTER TABLE `yf_goods_common` ADD COLUMN `common_width` decimal(10,2) NOT NULL COMMENT '体积 宽';
ALTER TABLE `yf_goods_common` ADD COLUMN `common_height` decimal(10,2) NOT NULL COMMENT '体积 高';
ALTER TABLE `yf_transport_template` ADD COLUMN `rule_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '运费规则 1-重量 3-体积';
ALTER TABLE `yf_transport_rule` ADD COLUMN `rule_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '运费规则 1-重量 3-体积';
-- SNS审核备注
ALTER TABLE `yf_explore_base` ADD COLUMN `explore_verify_remark` text COMMENT '审核备注';
-- SNS点赞数
ALTER TABLE `yf_explore_base` ADD COLUMN `like_num` int(10) NOT NULL DEFAULT '0' COMMENT '点赞数';
UPDATE `yf_explore_base` SET explore_status='3' WHERE explore_status='0';
-- SNS
ALTER TABLE `yf_explore_images` ADD COLUMN `poster_image` varchar(255) NOT NULL;

-- 直播商品
CREATE TABLE `yf_live_goods` (
  `live_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `roomId` varchar(30) NOT NULL DEFAULT '' COMMENT '直播间主键',
  `goodsIds` varchar(50) NOT NULL DEFAULT '' COMMENT '直播商品',
  `user_id` int(10) NOT NULL COMMENT '直播人',
  PRIMARY KEY (`live_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- yf_seller_wxpublic_message新增字段
ALTER TABLE `yf_seller_wxpublic_message` ADD COLUMN  `shop_id` tinyint(1) DEFAULT NULL COMMENT '商户id';
ALTER TABLE `yf_user_info` ADD COLUMN  `user_is_shop` tinyint(1) DEFAULT NULL COMMENT '一级域名商户id';

-- 分商品拆单发货
ALTER TABLE `yf_order_goods`
MODIFY COLUMN `goods_refund_status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '退货状态:0是无退货,1是退货中,2是退货完成,3商家拒绝退货' AFTER `goods_return_status`,
ADD COLUMN `order_goods_shiping`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '物流单号' AFTER `seckill_goods_id`,
ADD COLUMN `order_goods_express`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '物流公司' AFTER `order_goods_shiping`,
ADD COLUMN `order_goods_is_receiving`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '是否已经发货成功0代发货 1已发货2已收货' AFTER `order_goods_express`;

ALTER TABLE `yf_order_base`
ADD COLUMN `is_receivng`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '是否已经收获完成,0未发货或部分发货 ,1全部发完,2全部收货完成' AFTER `is_seckill`,
ADD COLUMN `shiping_codes`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单物流的单号以逗号分割' AFTER `is_receivng`;