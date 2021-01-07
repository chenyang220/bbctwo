-- 商家公众号管理
DROP TABLE IF EXISTS `yf_seller_wxpublic`;
CREATE TABLE `yf_seller_wxpublic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `shop_id` int(11) NOT NULL COMMENT '申请商家ID',
  `shop_name` varchar(20) NOT NULL COMMENT '申请商家名称',
  `wx_public_name` varchar(20) NOT NULL COMMENT '申请公众号名',
  `time` datetime NOT NULL COMMENT '申请时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '审核状态 0待审核 1拒绝 2通过 3停用',
  `show_type` int(1) NOT NULL DEFAULT '0' COMMENT '界面展示标志，默认0 1为展示',
  `review_info` text COMMENT '审核信息',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `years` int(10) DEFAULT NULL COMMENT '申请年限',
  `pay_images` varchar(255) DEFAULT NULL COMMENT ' ',
  `step` tinyint(1) DEFAULT '1' COMMENT '步骤',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;





-- 商家公众号详情
DROP TABLE IF EXISTS `yf_seller_wxpublic_list`;
CREATE TABLE `yf_seller_wxpublic_list` (
  `seller_wxpublic_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商家公众号详情ID',
  `shop_id` int(11) NOT NULL COMMENT '商家店铺ID',
  `wechat_public_name` varchar(20) NOT NULL COMMENT '公众号名称',
  `wechat_public_start_id` varchar(50) NOT NULL COMMENT '注册信息->原始ID',
  `wechat_public_wxaccount` varchar(50) NOT NULL COMMENT '公开信息->微信号',
  `wechat_public_call_url` varchar(100) NOT NULL COMMENT '服务器地址(URL)回调地址',
  `wechat_public_token` varchar(100) NOT NULL COMMENT '令牌(Token)',
  `wechat_public_appid` varchar(100) NOT NULL COMMENT '开发者ID(AppID)',
  `wechat_public_secret` varchar(100) NOT NULL COMMENT '开发者密码(AppSecret)',
  `seller_wxpublic_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '公众号开启状态默认开启0 1商家关闭 2平台关闭',
  `wxpublic_access_token` varchar(100) DEFAULT NULL COMMENT '微信验证token',
  PRIMARY KEY (`seller_wxpublic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- 商家公众号菜单设置
DROP TABLE IF EXISTS `yf_seller_wxpublic_menu`;
CREATE TABLE `yf_seller_wxpublic_menu` (
  `wxpublic_menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `shop_id` int(11) NOT NULL COMMENT '商家店铺ID',
  `menu_name` varchar(100) NOT NULL COMMENT '菜单名称',
  `sort_num` smallint(3) DEFAULT NULL COMMENT '菜单顺序',
  `menu_url` varchar(100) DEFAULT NULL COMMENT '菜单跳转网址',
  `menu_msg` text COMMENT '发送消息',
  `parent_menu_id` int(10) DEFAULT NULL COMMENT '父级菜单id',
  `menu_type` tinyint(1) DEFAULT NULL COMMENT '菜单类型（1：发送消息;2.跳转网页）',
  `operate_time` datetime NOT NULL COMMENT '操作日期',
  PRIMARY KEY (`wxpublic_menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 商家公众号自动回复模板
DROP TABLE IF EXISTS `yf_seller_wxpublic_message`;
CREATE TABLE `yf_seller_wxpublic_message` (
  `wxpublic_message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `words` varchar(200) DEFAULT NULL COMMENT '关键词',
  `match_type` tinyint(1) DEFAULT NULL COMMENT '匹配类型（1:精准匹配;2:模糊匹配）',
  `msg_type` tinyint(1) DEFAULT NULL COMMENT '消息类型(1:文本消息)',
  `content` text COMMENT '消息内容',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`wxpublic_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 专题栏目
DROP TABLE IF EXISTS `yf_special_column`;
CREATE TABLE `yf_special_column` (
  `special_column_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `special_column_image` longtext NOT NULL COMMENT '广告位图片内容',
  `goods_common` longtext NOT NULL COMMENT '推荐商品',
  `special_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-标准模板 1-自装修',
  `special_back_img` varchar(255) NOT NULL COMMENT '背景图片',
  PRIMARY KEY (`special_column_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='专题栏目';

DROP TABLE IF EXISTS `yf_special_set`;
CREATE TABLE `yf_special_set` (
  `column_set_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `column_set_image` longtext NOT NULL COMMENT '广告位图片',
  `column_set_type` tinyint(2) NOT NULL COMMENT '展示类型',
  PRIMARY KEY (`column_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自定义专题图片';

-- 商家海报背景图片
DROP TABLE IF EXISTS `yf_bill`;
CREATE TABLE `yf_bill` (
  `bill_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `shop_id` int(10) NOT NULL COMMENT '商家id',
  `bill_image` varchar(255) NOT NULL COMMENT '背景图片',
  PRIMARY KEY (`bill_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商家海报背景图片';

ALTER TABLE `yf_shop_base` MODIFY COLUMN `shop_common_service` text NOT NULL COMMENT '店铺售后服务';

