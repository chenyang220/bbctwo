-- 数据罗盘插件--
INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('Plugin_Analytics', '1', 'plugin', '1', '', 'string');

-- PLUS会员商品：
CREATE TABLE `yf_plus_goods` (
  `plus_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `shop_id` int(11) NOT NULL COMMENT '店铺ID',
  `goods_common_id` int(11) NOT NULL COMMENT '商品ID',
  `create_time` int(25) NOT NULL COMMENT '添加时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除标识（0：未删除；1：已删除）',
  PRIMARY KEY (`plus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='PLUS会员商品表';

-- PLUS会员购买记录：
CREATE TABLE `yf_plus_user_order` (
  `user_order_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `meal` varchar(100) NOT NULL COMMENT '开通套餐',
  `pay_use` varchar(100) NOT NULL COMMENT '付费模式 1-年度 2-季度 3-月',
  `payment` varchar(100) NOT NULL COMMENT '支付金额',
  `pay_time` int(25) NOT NULL COMMENT '支付时间',
  `method` varchar(100) NOT NULL COMMENT '支付方式',
  `start_date` int(25) NOT NULL COMMENT '会员开始日期',
  `end_date` int(25) NOT NULL COMMENT '会员结束日期',
  `order_status` int(2) unsigned NOT NULL DEFAULT '1' COMMENT '购买状态 1-试用 2-购买',
  `create_time` int(25) NOT NULL COMMENT '添加时间',
  `pay_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '支付状态(1:未支付；2：已支付)',
  `payment_number` varchar(50) NOT NULL DEFAULT '0' COMMENT '支付中心订单号',
  `method_code` varchar(50) NOT NULL DEFAULT '0' COMMENT '支付方式code',
  PRIMARY KEY (`user_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='PLUS会员购买记录表';

-- PLUS会员：
CREATE TABLE `yf_plus_user` (
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `user_status` int(2) unsigned NOT NULL DEFAULT '1' COMMENT '会员状态 1-试用 2-正式会员 3-过期会员',
  `end_date` int(25) NOT NULL COMMENT '会员结束日期',
  `create_time` int(25) NOT NULL COMMENT '添加时间',
  `issue_day` tinyint(2) DEFAULT '0' COMMENT '平台发放plus会员红包日期（每月当前设置值下发）',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='PLUS会员表';

-- PLUS商品标记：
ALTER TABLE  `yf_goods_common` ADD COLUMN `common_is_plus` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品是否参加plus会员活动 0-未参加 1-已参加';

-- 砍价活动：
CREATE TABLE `yf_bargain_base` (
  `bargain_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `shop_id` int(11) NOT NULL COMMENT '店铺ID',
  `shop_name`  varchar(100) NOT NULL COMMENT '店铺名称',
  `goods_id` int(11) NOT NULL COMMENT '商品ID',
  `goods_price` varchar(100) NOT NULL COMMENT '商品原价',
  `bargain_price` varchar(100) NOT NULL COMMENT '砍价底价',
  `bargain_stock` varchar(100) NOT NULL COMMENT '砍价库存',
  `bargain_desc` varchar(100) NOT NULL COMMENT '活动分享描述',
  `start_time` int(25) NOT NULL COMMENT '活动开始时间',
  `end_time` int(25) NOT NULL COMMENT '活动结束时间',
  `join_num` int(25) NOT NULL COMMENT '参与人数',
  `buy_num` int(25) NOT NULL COMMENT '购买人数',
  `bargain_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '活动状态 0-未开始 1-进行中 2-活动结束 3-管理员关闭 4-平台终止 5-过期',
  `bargain_type` tinyint(2) NOT NULL COMMENT '砍价规则 1-共砍刀数 2-最多可砍价格',
  `bargain_num_price` varchar(100) NOT NULL COMMENT '砍价规则 砍价刀数或最多砍价价格',
  `create_time` int(25) NOT NULL COMMENT '添加时间',
  `is_del` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-未删除1-删除',
  `bargain_stock_count` varchar(100) NOT NULL DEFAULT '0' COMMENT '砍价库存 用于显示',
  PRIMARY KEY (`bargain_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='砍价活动表';

-- 砍价活动商品购买会员：
CREATE TABLE `yf_bargain_buy_user` (
  `buy_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `bargain_id` int(11) NOT NULL COMMENT '砍价活动ID',
  `order_id` varchar(100) NOT NULL COMMENT '购买订单号',
  `bargain_num` varchar(100) NOT NULL COMMENT '砍价次数',
  `bargain_price` varchar(100) NOT NULL COMMENT '砍价后价格',
  `bargain_price_count` varchar(100) NOT NULL DEFAULT '0' COMMENT '已经砍掉的价格',
  `address_id` int(11) NOT NULL COMMENT '快递地址',
  `bargain_state` varchar(100) NOT NULL COMMENT '砍价状态 0- 砍价中 1-成功 2-失败',
  `create_time` int(25) NOT NULL COMMENT '添加时间',
  `user_end_time` int(25) NOT NULL COMMENT '结束时间',
  `is_del` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否删除0未删除 1-删除',
  PRIMARY KEY (`buy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='砍价活动商品购买会员表';

-- 砍价活动参与会员：
CREATE TABLE `yf_bargain_join_user` (
  `join_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `buy_id` int(11) NOT NULL COMMENT '参与砍价活动的ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `bargain_id` int(11) NOT NULL COMMENT '砍价活动ID',
  `help_bargain_price` varchar(50) NOT NULL COMMENT '砍掉的价格',
  `is_charter` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否为发起人 1-发起人 0-不是发起人',
  `create_time` int(25) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`join_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='砍价活动参与会员表';

-- 会员当天参与砍价次数：
CREATE TABLE `yf_bargain_join_count` (
  `count_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `join_count` tinyint(2) NOT NULL DEFAULT '1' COMMENT '当天参与次数',
  `join_date` date NOT NULL COMMENT '当天日期',
  PRIMARY KEY (`count_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='会员当天参与砍价次数表';

-- 砍价活动套餐：
CREATE TABLE `yf_bargain_combo` (
  `combo_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '套餐编号',
  `user_id` int(10) NOT NULL COMMENT '会员编号',
  `user_nickname` varchar(100) NOT NULL COMMENT '会员名称',
  `shop_id` int(10) NOT NULL COMMENT '店铺编号',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `combo_start_time` datetime NOT NULL COMMENT '开始时间',
  `combo_end_time` datetime NOT NULL COMMENT '结束时间',
  `paycount` decimal(20,2) NOT NULL COMMENT '支付总额',
  PRIMARY KEY (`combo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='砍价套餐表';

-- 订单详细信息 - 砍价订单标识：
ALTER TABLE  `yf_order_base` ADD COLUMN `order_is_bargain` tinyint(1) NOT NULL DEFAULT '0' COMMENT '砍价订单 0- 不是砍价订单 1-砍价订单';


-- 活动开关设定：
INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('bargain_status', '1', 'promotion', '1', '', 'string');

-- PLUS会员商品是否删标记：
ALTER TABLE  `yf_plus_goods` ADD COLUMN `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除标识（0：未删除；1：已删除）';

-- PC首页导航新增PLUS专区入口
INSERT INTO `yf_platform_nav` VALUES ('38', '0', '0', 'PLUS专区', 'index.php?ctl=Plus_User&amp;met=index', '0', '1', '3', '1', '0');

-- 订单商品表，新增plus会员价
ALTER TABLE  `yf_order_goods` ADD COLUMN `plus_price`  decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'plus会员支付时plus商品价格';


-- plus会员购买记录表，新增支付状态,支付中心订单号
ALTER TABLE  `yf_plus_user_order` ADD COLUMN `pay_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '支付状态(1:未支付；2：已支付)';
ALTER TABLE  `yf_plus_user_order` ADD COLUMN  `payment_number` varchar(50) NOT NULL DEFAULT '0' COMMENT '支付中心订单号';

-- 开通PLUS会员，支付方式code
ALTER TABLE  `yf_plus_user_order` ADD COLUMN  `method_code` varchar(50) NOT NULL DEFAULT '0' COMMENT '支付方式code';

-- 模板消息
INSERT INTO `yf_message_template` (`id`, `code`, `name`, `title`, `content_email`, `type`, `is_phone`, `is_email`, `is_mail`, `content_mail`, `content_phone`, `force_phone`, `force_email`, `force_mail`, `mold`, `baidu_tpl_id`) VALUES ('35', 'bargain success code', '砍价成功通知', '[weburl_name]提醒：砍价成功通知', '', '1', '0', '0', '1', '[user_name]，恭喜您，砍价成功啦，系统已自动生成订单，快去看看吧~', '', '0', '0', '0', '1', '0');

-- 开通PLUS会员，支付方式code
ALTER TABLE  `yf_plus_user_order` ADD COLUMN  `method_code` varchar(50) NOT NULL DEFAULT '0' COMMENT '支付方式code';

-- 新增：微信公众号菜单表
CREATE TABLE `yf_wxpublic_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `menu_name` varchar(50) NOT NULL COMMENT ' 菜单名称',
  `sort_num` smallint(3) DEFAULT NULL COMMENT '菜单顺序',
  `menu_url` varchar(100) DEFAULT NULL COMMENT '菜单跳转网址',
  `menu_msg` text COMMENT '发送消息',
  `wxxcx_id` varchar(50) DEFAULT NULL COMMENT '小程序id',
  `wxxcx_url` varchar(100) DEFAULT NULL COMMENT '小程序地址',
  `parent_menu_id` int(10) DEFAULT NULL COMMENT '父级菜单id',
  `menu_type` tinyint(1) DEFAULT '1' COMMENT '菜单类型（1：发送消息;2.跳转网页；3.打开小程序）',
  `wxxcx_pagepath` varchar(50) DEFAULT NULL COMMENT '小程序页面路径',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='微信公众号菜单表';

-- 新增：微信公众号消息自动回复
CREATE TABLE `yf_wxpublic_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `words` varchar(200) DEFAULT NULL COMMENT '关键词',
  `match_type` tinyint(1) DEFAULT NULL COMMENT '匹配类型（1:精准匹配;2:模糊匹配）',
  `msg_type` tinyint(1) DEFAULT NULL COMMENT '消息类型(1:文本消息)',
  `content` text COMMENT '消息内容',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- 邮件模板表新增：微信公众号模板消息相关字段
ALTER TABLE  `yf_message_template` ADD COLUMN `is_wechart_pulic`  varchar(50) DEFAULT '0' COMMENT '微信公众号消息推送该消息(0:关闭；1:开启)';
ALTER TABLE  `yf_message_template` ADD COLUMN `content_wechart_pulic` text COMMENT '微信公众号内容';
ALTER TABLE  `yf_message_template` ADD COLUMN `force_wechart_public` tinyint(1) DEFAULT NULL COMMENT '微信公众号(0:不强制;1:强制 )';
ALTER TABLE  `yf_message_template` ADD COLUMN `wxgzh_tpl_id` varchar(100) DEFAULT NULL COMMENT '消息模板id（template_id_short）';
ALTER TABLE  `yf_message_template` ADD COLUMN `wechart_pulic_template_id` varchar(100) DEFAULT NULL COMMENT '微信公众号消息模板ID';

-- 微信公众号菜单，新增操作日期字段
ALTER TABLE  `yf_wxpublic_menu` ADD COLUMN `operate_time` int(11) NOT NULL COMMENT '操作日期';

-- 买家子账号表变动
ALTER TABLE `yf_user_sub_user` MODIFY COLUMN `sub_user_id` int(10) unsigned NOT NULL COMMENT '子账号用户id';
ALTER TABLE  `yf_user_sub_user` ADD COLUMN `sub_id` int(10) unsigned NOT NULL COMMENT 'ID';
alter table `yf_user_sub_user` drop primary key;
ALTER TABLE `yf_user_sub_user` MODIFY COLUMN `sub_id` int(11) unsigned NOT NULL AUTO_INCREMENT FIRST ,ADD PRIMARY KEY (`sub_id`);

