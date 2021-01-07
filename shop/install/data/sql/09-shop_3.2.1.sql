-- 首页板块设置
CREATE TABLE `yf_front_forum` (
  `forum_id` int(10) NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(255) NOT NULL COMMENT '板块名称',
  `forum_order` int(10) NOT NULL COMMENT '首页板块顺序',
  `forum_state` tinyint(1) NOT NULL DEFAULT 1  COMMENT '状态1开启2关闭',
  `forum_content` varchar(255) NOT NULL COMMENT '板块内容',
  `forum_style`  int(10) NOT NULL DEFAULT 1 COMMENT '1-长方形 2-正方形',
  PRIMARY KEY (`forum_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='首页板块设置表';

ALTER TABLE `yf_adv_widget_item` ADD COLUMN `item_goods_id`  int(10) NOT NULL COMMENT '商品id' AFTER `item_street`;


ALTER TABLE  `yf_user_info` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号' AFTER  `user_mobile`;
ALTER TABLE  `yf_shop_company` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号' AFTER  `contacts_phone`;
ALTER TABLE  `yf_chain_base` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号' AFTER  `chain_mobile`;
ALTER TABLE  `yf_shop_shipping_address` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号';
ALTER TABLE  `yf_order_base` ADD  `order_seller_area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号' AFTER  `order_seller_contact`;
ALTER TABLE  `yf_points_orderaddress` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号';
ALTER TABLE  `yf_invoice` ADD  `invoice_area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '收票人手机号区号' AFTER  `invoice_rec_phone`;
ALTER TABLE  `yf_user_address` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号' AFTER  `user_address_phone`;
ALTER TABLE  `yf_order_base` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号' AFTER  `order_receiver_contact`;
ALTER TABLE  `yf_shop_base` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号' AFTER  `shop_tel`;
ALTER TABLE  `yf_shop_invoice` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号' AFTER  `invoice_rec_phone`;


ALTER TABLE `yf_app_notify` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_base_cron_copy` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_base_cron_sss` ROW_FORMAT = Dynamic;

CREATE TABLE `yf_base_district_copy`  (
  `district_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '地区id',
  `district_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '地区名称',
  `district_parent_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
  `district_displayorder` smallint(6) NOT NULL DEFAULT 0 COMMENT '排序',
  `district_region` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '区域名称 - 华北、东北、华东、华南、华中、西南、西北、港澳台、海外',
  `district_is_leaf` tinyint(1) NOT NULL DEFAULT 1 COMMENT '无子类',
  `district_is_level` tinyint(1) NOT NULL DEFAULT 1 COMMENT '等级',
  PRIMARY KEY (`district_id`) USING BTREE,
  INDEX `upid`(`district_parent_id`, `district_displayorder`) USING BTREE COMMENT '(null)'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '地区表' ROW_FORMAT = Compact;

ALTER TABLE `yf_chain_goods` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_base_config` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_goods_base` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_goods_common` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_order_base` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_order_goods` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_agent` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_agent_generated_commission` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_agent_level` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_base` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_commission` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_directseller_level` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_distributor` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_distributor_generated_commission` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_distributor_goods_cat` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_distributor_level` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_team` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_team_member` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_shop_type` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_user_base` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_distribution_user_commission` ROW_FORMAT = Dynamic;

CREATE TABLE `yf_express_data`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `logistic_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递单号',
  `shipper_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '快递公司编码，对应express_pinyin',
  `data` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '物流信息，json格式',
  `api_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1表示快递鸟，2其他，需要时再新增',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `from` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1表示快递鸟,2其他',
  `state_data` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '货款状态数据，json格式',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `union_index`(`logistic_code`, `shipper_code`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

ALTER TABLE `yf_front_forum` ADD COLUMN `forum_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '活动类型' AFTER `forum_content`;

ALTER TABLE `yf_goods_base` MODIFY COLUMN `goods_spec` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品规格-JSON存储' AFTER `brand_id`;

ALTER TABLE `yf_goods_base` ADD COLUMN `is_del` int(10) NOT NULL DEFAULT 1 COMMENT '是否删除' AFTER `goods_parent_id`;

ALTER TABLE `yf_goods_common` ADD COLUMN `transport_type_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '运费模板名称' AFTER `transport_type_id`;

ALTER TABLE `yf_goods_common` ADD COLUMN `common_freight` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '运费' AFTER `transport_type_name`;

ALTER TABLE `yf_goods_common` MODIFY COLUMN `common_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商品主图' AFTER `common_video`;

ALTER TABLE `yf_goods_common` MODIFY COLUMN `common_goods_from` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1正常添加，2外部导入。默认为1' AFTER `common_cps_commission`;

ALTER TABLE `yf_goods_common` MODIFY COLUMN `transport_area_id` int(11) NOT NULL DEFAULT 0 COMMENT '售卖区域' AFTER `common_goods_from`;

ALTER TABLE `yf_grade_log` MODIFY COLUMN `admin_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'admin' COMMENT '管理员账号' AFTER `user_name`;

CREATE TABLE `yf_information_news`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '咨讯id',
  `type` int(11) NULL DEFAULT NULL COMMENT '资讯类别id',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '资讯标题',
  `subtitle` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '资讯副标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '资讯内容',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '发布时间',
  `author_id` int(11) NULL DEFAULT NULL COMMENT '发布者id',
  `author_type` enum('1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '发布者类型 1用户 2商家 3平台',
  `number` int(11) NULL DEFAULT NULL COMMENT '阅读数量',
  `auditing` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '审核 1通过 2不通过',
  `status` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '资讯文章删除状态 1正常 2删除',
  `complaint` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '文章是否投诉 1 否 2 是',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

CREATE TABLE `yf_information_newsclass`  (
  `id` int(11) NOT NULL COMMENT '资讯类别id',
  `newsclass_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '资讯名称',
  `sort` int(255) NULL DEFAULT NULL COMMENT '序号',
  `status` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1' COMMENT '资讯类别状态 1 正常 2 删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

ALTER TABLE `yf_member_consume_log` ENGINE = InnoDB;

ALTER TABLE `yf_message_template` ADD COLUMN `baidu_tpl_id` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '百度模板id' AFTER `mold`;

ALTER TABLE `yf_order_goods` MODIFY COLUMN `goods_return_status` tinyint(1) NOT NULL COMMENT '退款状态：0：无退款 1：退款中 2：退款完成' AFTER `order_goods_benefit`;

ALTER TABLE `yf_order_goods` MODIFY COLUMN `goods_refund_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '退货状态:0是无退货,1是退货中,2是退货完成' AFTER `goods_return_status`;

CREATE TABLE `yf_order_refund_goods`  (
  `refund_goods_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '退款商品id',
  `order_return_id` int(10) NOT NULL COMMENT '退货记录ID',
  `order_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单编号',
  `order_goods_id` int(10) NOT NULL DEFAULT 0 COMMENT '订单商品编号',
  `order_goods_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '退款商品名称',
  `order_goods_price` decimal(8, 2) NOT NULL COMMENT '商品单价',
  `order_goods_num` int(10) NOT NULL COMMENT '退货数量',
  `return_cash` decimal(8, 2) NOT NULL COMMENT '退款金额',
  `return_rpt_cash` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '退还平台红包金额',
  `return_commision_fee` decimal(8, 2) NOT NULL COMMENT '退还佣金',
  `order_goods_pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `order_spec_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规格描述',
  `return_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '退货编号',
  `seller_user_id` int(10) UNSIGNED NOT NULL COMMENT '卖家ID',
  `seller_user_account` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '店铺名称',
  `buyer_user_id` int(10) UNSIGNED NOT NULL COMMENT '买家ID',
  `buyer_user_account` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '买家会员名',
  `return_reason_id` int(10) NOT NULL COMMENT '退款理由id',
  `return_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '退款理由',
  `return_message` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '退款备注',
  PRIMARY KEY (`refund_goods_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '退款商品表' ROW_FORMAT = Compact;

ALTER TABLE `yf_order_return` MODIFY COLUMN `behalf_deliver` tinyint(1) NOT NULL DEFAULT 0 COMMENT '分销代发货  0：不代发货 1：代发货' AFTER `return_goods_return`;

CREATE TABLE `yf_order_seq`  (
  `prefix` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'key',
  `n_current_value` bigint(20) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'value',
  `n_increment` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'increment',
  PRIMARY KEY (`prefix`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单序列号表' ROW_FORMAT = Compact;

ALTER TABLE `yf_order_settlement` MODIFY COLUMN `os_commis_cod` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '货到付款佣金(门店自提佣金)' AFTER `os_commis_amount`;

ALTER TABLE `yf_pintuan` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_pintuan_buyer` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_pintuan_detail` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_pintuan_mark` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_pintuan_temp` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_platform_custom_service` MODIFY COLUMN `status` tinyint(3) NOT NULL DEFAULT 0 COMMENT '1-买家删除;2-卖家删除' AFTER `custom_service_status`;

ALTER TABLE `yf_points_log` MODIFY COLUMN `admin_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'admin' COMMENT '管理员账号' AFTER `user_name`;

ALTER TABLE `yf_redpacket_base` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_redpacket_base` MODIFY COLUMN `redpacket_state` tinyint(4) NOT NULL COMMENT '红包状态(1-未用,2-已用,3-过期,4-回收)' AFTER `redpacket_t_orderlimit`;

ALTER TABLE `yf_redpacket_template` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_seller_group` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_seller_log` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_shop_base` MODIFY COLUMN `shop_workingtime` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `shop_template`;

ALTER TABLE `yf_shop_company` MODIFY COLUMN `fee` float(10, 2) NOT NULL DEFAULT 0.00 AFTER `shop_class_commission`;

ALTER TABLE `yf_shop_customer` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_shop_entity` ENGINE = InnoDB;

ALTER TABLE `yf_shop_invoice` ADD COLUMN `area_code` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '国际手机号区号' AFTER `invoice_rec_phone`;

ALTER TABLE `yf_shop_service` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_sub_site` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_test` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_transport_area` MODIFY COLUMN `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '售卖区域模板名' AFTER `id`;

ALTER TABLE `yf_transport_template` ROW_FORMAT = Dynamic;

ALTER TABLE `yf_user_info` MODIFY COLUMN `user_birthday` date NOT NULL DEFAULT '1970-01-01' AFTER `user_sex`;

ALTER TABLE `yf_user_info` MODIFY COLUMN `user_area` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '江苏 苏州市 吴中区' AFTER `user_areaid`;

ALTER TABLE `yf_user_info` MODIFY COLUMN `user_ip` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_statu`;

DROP TABLE `yf_user_extend`;


--
-- 表的结构 `yf_mb_cat_image`
--

CREATE TABLE IF NOT EXISTS `yf_wx_cat_image` (
  `wx_cat_image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cat_id` int(10) unsigned NOT NULL COMMENT 'cat_id',
  `wx_cat_image` varchar(255) NOT NULL COMMENT '分类图片',
  `cat_adv_image` varchar(255) NOT NULL COMMENT '广告图片',
  `cat_adv_url` varchar(255) NOT NULL COMMENT '广告地址',
  PRIMARY KEY (`wx_cat_image_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分类图片' AUTO_INCREMENT=42 ;


--
-- 表的结构 `yf_mb_tpl_layout`
--

CREATE TABLE IF NOT EXISTS `yf_wx_tpl_layout` (
  `wx_tpl_layout_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `wx_tpl_layout_title` varchar(50) NOT NULL COMMENT '标题',
  `wx_tpl_layout_type` varchar(50) NOT NULL COMMENT '类型',
  `wx_tpl_layout_data` text NOT NULL COMMENT '根据不同的类型，所存储的数据也不同，仔细！（json）',
  `wx_tpl_layout_enable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '使用启用 0:未启用 1:启用',
  `wx_tpl_layout_order` tinyint(2) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `sub_site_id` int(11) NOT NULL DEFAULT '0' COMMENT '分站id',
  PRIMARY KEY (`wx_tpl_layout_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='手机端模板' AUTO_INCREMENT=237 ;

ALTER TABLE  `yf_wx_tpl_layout` CHANGE  `wx_tpl_layout_data`  `wx_tpl_layout_data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '根据不同的类型，所存储的数据也不同，仔细！（json）';
ALTER TABLE  `yf_mb_tpl_layout` CHANGE  `mb_tpl_layout_data`  `mb_tpl_layout_data` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '根据不同的类型，所存储的数据也不同，仔细！（json）';


-- 添加首页模板风格 
INSERT INTO `yf_adv_page_layout` (`layout_id`, `layout_name`, `layout_structure`) VALUES ('4', '模板4', '{\"block41\":{\"name\":\"a_con\",\"type\":\"ag\",\"style\":{\"height\":\"451\",\"width\":\"399\"}},\"block42\":{\"name\":\"b_con\",\"style\":{\"height\":\"451\",\"width\":\"399\"},\"child\":{\"block420\":{\"type\":\"ag\",\"style\":{\"height\":\"314\",\"width\":\"399\",\"border-bottom\": \"1px solid #CCC\"}},\"block421\":{\"type\":\"ag\",\"style\":{\"height\":\"136\",\"width\":\"132\"}},\"block422\":{\"type\":\"ag\",\"style\":{\"height\":\"136\",\"width\":\"132\"}},\"block423\":{\"type\":\"ag\",\"style\":{\"height\":\"136\",\"width\":\"132\",\"border-right\":\"none\"}}}},\"block43\":{\"name\":\"c_con\",\"style\":{\"height\":\"451\",\"width\":\"399\",\"border-right\":\"none\"},\"child\":{\"block430\":{\"type\":\"ag\",\"style\":{\"height\":\"225\",\"width\":\"399\",\"border-bottom\":\"1px solid #CCC\"}},\"block431\":{\"type\":\"ag\",\"style\":{\"height\":\"225\",\"width\":\"199\"}},\"block432\":{\"type\":\"ag\",\"style\":{\"height\":\"225\",\"width\":\"199\",\"border-right\":\"none\"}}}}}');
INSERT INTO `yf_adv_page_layout` (`layout_id`, `layout_name`, `layout_structure`) VALUES ('5', '模板5', '{\"block51\":{\"name\":\"a_con\",\"type\":\"ag\",\"style\":{\"height\":\"350\",\"width\":\"1200\",\"border-right\":\"none\"}},\"block52\":{\"name\":\"b_con\",\"style\":{\"height\":\"368\",\"width\":\"1200\",\"border-right\":\"none\"},\"child\":{\"block520\":{\"type\":\"goods\",\"style\":{\"height\":\"368\",\"width\":\"299\"}},\"block521\":{\"type\":\"goods\",\"style\":{\"height\":\"368\",\"width\":\"299\"}},\"block522\":{\"type\":\"goods\",\"style\":{\"height\":\"368\",\"width\":\"299\"}},\"block523\":{\"type\":\"goods\",\"style\":{\"height\":\"368\",\"width\":\"300\",\"border-right\":\"none\"}}}}}	');

UPDATE `yf_adv_page_layout` SET `layout_id`='2', `layout_name`='模版2', `layout_structure`='{\"block1\":{\"name\":\"a_con\",\"style\":{\"height\":\"360\",\"width\":\"210\"},\"child\":{\"block2\":{\"type\":\"category\",\"style\":{\"height\":\"110\",\"width\":\"210\",\"border-bottom\":\"1px solid #CCC\"}},\"block3\":{\"type\":\"ag\",\"style\":{\"height\":\"250\",\"width\":\"210\"}}}},\"block4\":{\"name\":\"b_con\",\"type\":\"ad\",\"style\":{\"height\":\"360\",\"width\":\"326\"}},\"block5\":{\"name\":\"c_con\",\"style\":{\"height\":\"360\",\"width\":\"220\"},\"child\":{\"block6\":{\"type\":\"ag\",\"style\":{\"height\":\"179\",\"width\":\"220\",\"border-bottom\":\"1px solid #CCC\"}},\"block7\":{\"type\":\"ag\",\"style\":{\"height\":\"180\",\"width\":\"220\"}}}},\"block8\":{\"name\":\"d_con\",\"type\":\"ad\",\"style\":{\"height\":\"360\",\"width\":\"220\"}},\"block9\":{\"name\":\"e_con\",\"style\":{\"height\":\"360\",\"width\":\"218\",\"border-right\":\"none\"},\"child\":{\"block10\":{\"type\":\"ag\",\"style\":{\"height\":\"179\",\"width\":\"220\",\"border-bottom\":\"1px solid #CCC\"}},\"block11\":{\"type\":\"ag\",\"style\":{\"height\":\"180\",\"width\":\"220\"}}}},\"block12\":{\"name\":\"f_con\",\"style\":{\"height\":\"180\",\"width\":\"1200\",\"border-right\":\"none\"},\"child\":{\"block13\":{\"type\":\"ag\",\"style\":{\"height\":\"180\",\"width\":\"210\"}},\"block14\":{\"type\":\"ag\",\"style\":{\"height\":\"180\",\"width\":\"326\"}},\"block15\":{\"type\":\"ag\",\"style\":{\"height\":\"180\",\"width\":\"220\"}},\"block16\":{\"type\":\"ag\",\"style\":{\"height\":\"180\",\"width\":\"220\"}},\"block17\":{\"type\":\"ag\",\"style\":{\"height\":\"180\",\"width\":\"220\",\"border-right\":\"none\"}}}}}' WHERE (`layout_id`='2');



-- ----------------------------
-- Table structure for yf_information_news
-- ----------------------------
CREATE TABLE `yf_information_news`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '咨讯id',
  `type` int(11) DEFAULT NULL COMMENT '资讯类别id',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '资讯标题',
  `subtitle` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '资讯副标题',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '资讯内容',
  `create_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '发布时间',
  `author_id` int(11) DEFAULT NULL COMMENT '发布者id',
  `author_type` enum('1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '1' COMMENT '发布者类型 1用户 2商家 3平台',
  `number` int(11) DEFAULT 0 COMMENT '阅读数量',
  `auditing` enum('1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '3' COMMENT '审核 1通过 2不通过 3待审核',
  `status` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '1' COMMENT '资讯文章删除状态 1正常 2删除',
  `complaint` enum('1','2','3') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '1' COMMENT '文章是否投诉 1 否 2 是 3通知商家',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for yf_information_newsclass
-- ----------------------------
CREATE TABLE `yf_information_newsclass`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '资讯类别id',
  `newsclass_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '资讯名称',
  `sort` int(255) DEFAULT NULL COMMENT '序号',
  `status` enum('1','2') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '1' COMMENT '资讯类别状态 1 正常 2 删除',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

UPDATE `yf_web_config` SET `config_value`='1' WHERE (`config_key`='site_status_wap');