DROP TABLE IF EXISTS `yf_goods_base`;
CREATE TABLE `yf_goods_base` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `common_id` int(10) unsigned NOT NULL COMMENT '商品公共表id',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名称（+规格名称）',
  `goods_promotion_tips` varchar(200) NOT NULL COMMENT '促销提示',
  `cat_id` int(10) unsigned NOT NULL COMMENT '商品分类id',
  `brand_id` int(10) unsigned NOT NULL COMMENT '品牌id',
  `goods_spec` varchar(255) NOT NULL DEFAULT '' COMMENT '商品规格-JSON存储',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_market_price` decimal(10,2) NOT NULL COMMENT '市场价',
  `goods_stock` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品库存',
  `goods_alarm` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存预警值',
  `goods_code` varchar(50) NOT NULL COMMENT '商家编号货号',
  `goods_barcode` varchar(50) DEFAULT '' COMMENT '商品二维码',
  `goods_is_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品推荐 1是，0否 默认0',
  `goods_click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品点击数量',
  `goods_salenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售数量',
  `goods_collect` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数量',
  `goods_image` varchar(255) NOT NULL DEFAULT '' COMMENT '商品主图',
  `color_id` int(10) NOT NULL DEFAULT '0',
  `goods_evaluation_good_star` tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT '好评星级',
  `goods_evaluation_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价数',
  `goods_max_sale` int(10) NOT NULL DEFAULT '0' COMMENT '单人最大购买数量',
  `goods_is_shelves` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-上架 2-下架',
  PRIMARY KEY (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1675 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品表';

-- ----------------------------
--  Table structure for `yf_goods_brand`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_brand`;
CREATE TABLE `yf_goods_brand` (
  `brand_id` int(10) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(50) NOT NULL,
  `brand_name_cn` varchar(255) NOT NULL DEFAULT '' COMMENT '拼音',
  `cat_id` int(10) unsigned NOT NULL COMMENT '分类id',
  `brand_initial` varchar(1) NOT NULL COMMENT '首字母',
  `brand_show_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '展示方式',
  `brand_pic` varchar(255) NOT NULL,
  `brand_displayorder` smallint(3) NOT NULL DEFAULT '0',
  `brand_enable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `brand_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '上传店铺的id',
  `brand_collect` int(10) NOT NULL COMMENT '收藏数量',
  PRIMARY KEY (`brand_id`),
  KEY `brand_name` (`brand_name`)
) ENGINE=InnoDB AUTO_INCREMENT=255 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品品牌表';

-- ----------------------------
--  Table structure for `yf_goods_cat`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_cat`;
CREATE TABLE `yf_goods_cat` (
  `cat_id` int(9) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL COMMENT ' 分类名称',
  `cat_parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父类',
  `cat_pic` varchar(255) NOT NULL DEFAULT '' COMMENT '分类图片',
  `type_id` int(10) NOT NULL DEFAULT '0' COMMENT '类型id',
  `cat_commission` float NOT NULL DEFAULT '0' COMMENT '分佣比例',
  `cat_is_wholesale` tinyint(1) NOT NULL DEFAULT '0',
  `cat_is_virtual` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许虚拟',
  `cat_templates` varchar(100) NOT NULL DEFAULT '0',
  `cat_displayorder` smallint(3) NOT NULL DEFAULT '255' COMMENT '排序',
  `cat_level` tinyint(1) NOT NULL COMMENT '分类级别',
  `cat_show_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:SPU  2:颜色',
  PRIMARY KEY (`cat_id`),
  KEY `cat_parent_id` (`cat_parent_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10140943 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品分类表';

-- ----------------------------
--  Table structure for `yf_goods_cat_nav`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_cat_nav`;
CREATE TABLE `yf_goods_cat_nav` (
  `goods_cat_nav_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `goods_cat_nav_name` varchar(50) NOT NULL COMMENT '分类别名',
  `goods_cat_nav_brand` varchar(200) NOT NULL COMMENT '推荐品牌',
  `goods_cat_nav_recommend` text NOT NULL COMMENT '推荐分类',
  `goods_cat_nav_pic` varchar(255) NOT NULL COMMENT '分类图片',
  `goods_cat_nav_adv` varchar(255) NOT NULL COMMENT '广告图',
  `goods_cat_id` int(10) NOT NULL COMMENT '商品分类id',
  `goods_cat_nav_recommend_display` text NOT NULL COMMENT '显示用推荐分类',
  PRIMARY KEY (`goods_cat_nav_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='商品分类导航';

-- ----------------------------
--  Table structure for `yf_goods_common`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_common`;
CREATE TABLE `yf_goods_common` (
  `common_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `common_name` varchar(50) NOT NULL COMMENT '商品名称',
  `common_promotion_tips` varchar(50) NOT NULL COMMENT '商品广告词',
  `cat_id` int(10) unsigned NOT NULL COMMENT '商品分类',
  `cat_name` varchar(200) NOT NULL COMMENT '商品分类',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `shop_cat_id` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺分类id 首尾用,隔开',
  `shop_goods_cat_id` varchar(255) NOT NULL DEFAULT '0' COMMENT '店铺商品分类id  -- json',
  `goods_id` text NOT NULL COMMENT 'goods_id -- json [goods_id: xx, color_id: xx]',
  `shop_self_support` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否自营',
  `shop_status` tinyint(1) unsigned NOT NULL DEFAULT '3' COMMENT '店铺状态-3：开店成功 2:待审核付款 1:待审核资料  0:关闭',
  `common_property` text NOT NULL COMMENT '属性',
  `common_spec_name` varchar(255) NOT NULL COMMENT '规格名称',
  `common_spec_value` text NOT NULL COMMENT '规格值',
  `brand_id` int(10) unsigned NOT NULL COMMENT '品牌id',
  `brand_name` varchar(100) NOT NULL COMMENT '品牌名称',
  `type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类型id',
  `common_image` varchar(255) NOT NULL COMMENT '商品主图',
  `common_packing_list` text NOT NULL,
  `common_service` text NOT NULL,
  `common_state` tinyint(3) unsigned NOT NULL COMMENT '商品状态 0下架，1正常，10违规（禁售）',
  `common_state_remark` varchar(255) NOT NULL COMMENT '违规原因',
  `common_verify` tinyint(3) unsigned NOT NULL COMMENT '商品审核 1通过，0未通过，10审核中',
  `common_verify_remark` varchar(255) NOT NULL COMMENT '审核失败原因',
  `common_add_time` datetime NOT NULL COMMENT '商品添加时间',
  `common_sell_time` datetime NOT NULL COMMENT '上架时间',
  `common_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `common_market_price` decimal(10,2) NOT NULL COMMENT '市场价',
  `common_cost_price` decimal(10,2) NOT NULL COMMENT '成本价',
  `common_stock` int(10) unsigned NOT NULL COMMENT '商品库存',
  `common_limit` smallint(3) NOT NULL DEFAULT '0' COMMENT '每人限购 0 代表不限购',
  `common_alarm` int(10) unsigned NOT NULL DEFAULT '0',
  `common_code` varchar(50) NOT NULL COMMENT '商家编号',
  `common_platform_code` varchar(100) NOT NULL DEFAULT '0' COMMENT '平台货号',
  `common_cubage` decimal(10,2) NOT NULL COMMENT '商品重量',
  `common_collect` int(10) NOT NULL DEFAULT '0' COMMENT '商品收藏量',
  `common_evaluate` int(10) NOT NULL DEFAULT '0' COMMENT '商品评论数',
  `common_salenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品销量',
  `common_invoices` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开具增值税发票 1是，0否',
  `common_is_return` tinyint(1) NOT NULL DEFAULT '1' COMMENT '7天无理由退货 1=不支持  2=支持',
  `common_formatid_top` int(10) unsigned NOT NULL COMMENT '顶部关联板式',
  `common_formatid_bottom` int(10) unsigned NOT NULL COMMENT '底部关联板式',
  `common_is_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品推荐 1:推荐 2:推荐',
  `common_is_virtual` tinyint(1) NOT NULL DEFAULT '0' COMMENT '虚拟商品',
  `common_virtual_date` datetime NOT NULL COMMENT '虚拟商品有效期',
  `common_virtual_refund` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支持过期退款',
  `transport_type_id` int(10) NOT NULL DEFAULT '0' COMMENT '0--> 固定运费   非零：transport_type_id  运费类型',
  `transport_type_name` varchar(30) NOT NULL COMMENT '运费模板名称',
  `common_freight` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `common_location` text NOT NULL COMMENT '商品所在地 json',
  `common_is_tuan` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品是否参加团购活动',
  `common_is_xian` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品是否参加限时折扣活动',
  `common_is_jia` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商品是否参加加价购活动',
  `common_shop_contract_1` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障-由店铺映射到商品，用来检索使用',
  `common_shop_contract_2` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障-由店铺映射到商品，用来检索使用',
  `common_shop_contract_3` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障-由店铺映射到商品，用来检索使用',
  `common_shop_contract_4` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障-由店铺映射到商品，用来检索使用',
  `common_shop_contract_5` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消费者保障-由店铺映射到商品，用来检索使用',
  `common_shop_contract_6` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`common_id`),
  KEY `cat_id` (`cat_id`),
  KEY `shop_id` (`shop_id`),
  KEY `type_id` (`type_id`),
  KEY `common_verify` (`common_verify`),
  KEY `common_state` (`common_state`),
  KEY `common_name` (`common_name`),
  KEY `shop_name` (`shop_name`),
  KEY `brand_name` (`brand_name`),
  KEY `brand_id` (`brand_id`),
  KEY `shop_status` (`shop_status`)
) ENGINE=InnoDB AUTO_INCREMENT=482 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品公共内容表-未来可分表';

-- ----------------------------
--  Table structure for `yf_goods_common_detail`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_common_detail`;
CREATE TABLE `yf_goods_common_detail` (
  `common_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `common_body` text NOT NULL COMMENT '商品内容',
  PRIMARY KEY (`common_id`)
) ENGINE=InnoDB AUTO_INCREMENT=482 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品公共内容详情表';

-- ----------------------------
--  Table structure for `yf_goods_evaluation`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_evaluation`;
CREATE TABLE `yf_goods_evaluation` (
  `evaluation_goods_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '会员ID',
  `member_name` varchar(50) NOT NULL COMMENT '会员名',
  `order_id` varchar(50) NOT NULL COMMENT '订单ID',
  `shop_id` int(10) NOT NULL COMMENT '商家ID',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `common_id` int(10) NOT NULL,
  `goods_id` int(10) NOT NULL COMMENT '商品ID',
  `goods_name` varchar(50) NOT NULL COMMENT '商品名',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `goods_image` varchar(255) NOT NULL COMMENT '商品图片',
  `scores` tinyint(1) NOT NULL COMMENT '1-5分',
  `result` enum('good','neutral','bad') NOT NULL COMMENT '结果',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `image` text NOT NULL COMMENT '晒单图片',
  `isanonymous` tinyint(1) NOT NULL COMMENT '是否匿名评价',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL COMMENT '状态 0禁止显示 1显示 2置顶',
  `explain_content` varchar(255) NOT NULL COMMENT '解释内容',
  `update_time` datetime NOT NULL,
  `evaluation_from` enum('1','2') NOT NULL DEFAULT '1' COMMENT '手机端',
  PRIMARY KEY (`evaluation_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品评论表';

-- ----------------------------
--  Table structure for `yf_goods_format`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_format`;
CREATE TABLE `yf_goods_format` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `position` tinyint(1) unsigned NOT NULL,
  `content` text NOT NULL,
  `shop_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='关联板式表';

-- ----------------------------
--  Table structure for `yf_goods_images`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_images`;
CREATE TABLE `yf_goods_images` (
  `images_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品图片id',
  `common_id` int(10) unsigned NOT NULL COMMENT '商品公共内容id',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `images_color_id` int(10) unsigned NOT NULL COMMENT '颜色规格值id',
  `images_image` varchar(255) NOT NULL COMMENT '商品图片',
  `images_displayorder` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `images_is_default` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '默认主题，1是，0否',
  PRIMARY KEY (`images_id`)
) ENGINE=InnoDB AUTO_INCREMENT=962 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品图片';

-- ----------------------------
--  Table structure for `yf_goods_property`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_property`;
CREATE TABLE `yf_goods_property` (
  `property_id` int(6) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `property_name` varchar(100) NOT NULL COMMENT '属性名称',
  `type_id` int(10) NOT NULL COMMENT '所属类型id',
  `property_item` text NOT NULL COMMENT '属性值列',
  `property_is_search` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被搜索。0为不搜索、1为搜索',
  `property_format` enum('text','select','checkbox') NOT NULL COMMENT '显示类型',
  `property_displayorder` smallint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`property_id`),
  KEY `catid` (`property_format`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品属性值表';

-- ----------------------------
--  Table structure for `yf_goods_property_index`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_property_index`;
CREATE TABLE `yf_goods_property_index` (
  `goods_property_index_id` int(11) NOT NULL AUTO_INCREMENT,
  `common_id` int(10) unsigned NOT NULL COMMENT '商品公共表id',
  `property_id` int(10) unsigned NOT NULL COMMENT '属性id',
  `property_value_id` int(10) unsigned NOT NULL COMMENT '属性值id',
  PRIMARY KEY (`goods_property_index_id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品与属性对应表';

-- ----------------------------
--  Table structure for `yf_goods_property_value`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_property_value`;
CREATE TABLE `yf_goods_property_value` (
  `property_value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `property_value_name` varchar(100) NOT NULL COMMENT '属性值名称',
  `property_id` int(10) unsigned NOT NULL COMMENT '所属属性id',
  `property_value_displayorder` smallint(3) unsigned NOT NULL DEFAULT '1' COMMENT '属性值排序',
  PRIMARY KEY (`property_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品属性值表';

-- ----------------------------
--  Table structure for `yf_goods_recommend`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_recommend`;
CREATE TABLE `yf_goods_recommend` (
  `goods_recommend_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '商品推荐id',
  `goods_cat_id` int(10) NOT NULL COMMENT '商品分类id',
  `common_id` varchar(50) NOT NULL COMMENT '推荐商品id，最多四个',
  `recommend_num` int(5) NOT NULL COMMENT '推荐数量',
  PRIMARY KEY (`goods_recommend_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='商品推荐表';

-- ----------------------------
--  Table structure for `yf_goods_service`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_service`;
CREATE TABLE `yf_goods_service` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `yf_goods_spec`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_spec`;
CREATE TABLE `yf_goods_spec` (
  `spec_id` int(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spec_name` varchar(100) NOT NULL COMMENT '规格名称',
  `cat_id` int(10) unsigned NOT NULL COMMENT '快捷定位',
  `spec_displayorder` smallint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `spec_readonly` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '不可删除',
  PRIMARY KEY (`spec_id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品规格表';

-- ----------------------------
--  Table structure for `yf_goods_spec_value`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_spec_value`;
CREATE TABLE `yf_goods_spec_value` (
  `spec_value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `spec_value_name` varchar(100) NOT NULL COMMENT '规格值名称',
  `spec_id` int(10) unsigned NOT NULL COMMENT '所属规格id',
  `type_id` int(10) NOT NULL,
  `cat_id` int(10) NOT NULL,
  `shop_id` int(10) NOT NULL,
  `spec_value_displayorder` smallint(3) NOT NULL DEFAULT '1' COMMENT '排序',
  PRIMARY KEY (`spec_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=732 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品规格值表';

-- ----------------------------
--  Table structure for `yf_goods_state`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_state`;
CREATE TABLE `yf_goods_state` (
  `goods_state_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品状态id',
  `goods_state_name` varchar(50) NOT NULL DEFAULT '' COMMENT '产品状态状态',
  `goods_state_text_1` varchar(255) NOT NULL DEFAULT '' COMMENT '产品状态',
  `goods_state_text_2` varchar(255) NOT NULL DEFAULT '' COMMENT '产品状态',
  `goods_state_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`goods_state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='产品状态表';

-- ----------------------------
--  Table structure for `yf_goods_type`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_type`;
CREATE TABLE `yf_goods_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type_name` varchar(100) NOT NULL COMMENT '类型名称',
  `type_displayorder` tinyint(1) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `cat_id` int(10) NOT NULL DEFAULT '-1' COMMENT '仅仅定位，无用',
  `cat_name` varchar(255) NOT NULL DEFAULT '',
  `type_draft` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '草稿：只允许存在一条记录',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品类型表-要取消各种快捷定位';

-- ----------------------------
--  Table structure for `yf_goods_type_brand`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_type_brand`;
CREATE TABLE `yf_goods_type_brand` (
  `type_brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  `brand_id` int(10) unsigned NOT NULL COMMENT '规格id',
  PRIMARY KEY (`type_brand_id`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品类型与品牌对应表';

-- ----------------------------
--  Table structure for `yf_goods_type_spec`
-- ----------------------------
DROP TABLE IF EXISTS `yf_goods_type_spec`;
CREATE TABLE `yf_goods_type_spec` (
  `type_spec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL COMMENT '类型id',
  `spec_id` int(10) unsigned NOT NULL COMMENT '规格id',
  PRIMARY KEY (`type_spec_id`)
) ENGINE=InnoDB AUTO_INCREMENT=266 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品类型与规格对应表';

-- ----------------------------
--  Table structure for `yf_grade_log`
-- ----------------------------
DROP TABLE IF EXISTS `yf_grade_log`;
CREATE TABLE `yf_grade_log` (
  `grade_log_id` int(10) NOT NULL AUTO_INCREMENT,
  `points_log_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型 1获取2消费',
  `class_id` tinyint(1) NOT NULL COMMENT '1''会员登录'',2''购买商品'',3''评价''',
  `user_id` int(10) NOT NULL COMMENT '会员编号',
  `user_name` varchar(50) NOT NULL COMMENT '会员名称',
  `admin_name` varchar(100) NOT NULL COMMENT '管理员名称',
  `grade_log_grade` int(10) NOT NULL DEFAULT '0' COMMENT '获得经验',
  `freeze_grade` int(10) NOT NULL DEFAULT '0' COMMENT '冻结经验',
  `grade_log_time` datetime NOT NULL COMMENT '创建时间',
  `grade_log_desc` varchar(100) NOT NULL COMMENT '描述',
  `grade_log_flag` varchar(20) NOT NULL COMMENT '标记',
  PRIMARY KEY (`grade_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1725 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='会员经验日志表';

-- ----------------------------
--  Table structure for `yf_groupbuy_area`
-- ----------------------------
DROP TABLE IF EXISTS `yf_groupbuy_area`;
CREATE TABLE `yf_groupbuy_area` (
  `groupbuy_area_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '地区编号',
  `groupbuy_area_name` varchar(50) NOT NULL COMMENT '地区名称',
  `groupbuy_area_parent_id` int(10) unsigned NOT NULL COMMENT '父地区编号',
  `groupbuy_area_sort` tinyint(1) unsigned NOT NULL COMMENT '排序',
  `groupbuy_area_deep` tinyint(1) unsigned NOT NULL COMMENT '深度',
  PRIMARY KEY (`groupbuy_area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='团购地区表';

-- ----------------------------
--  Table structure for `yf_groupbuy_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_groupbuy_base`;
CREATE TABLE `yf_groupbuy_base` (
  `groupbuy_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '团购ID',
  `groupbuy_name` varchar(255) NOT NULL COMMENT '活动名称',
  `groupbuy_starttime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `groupbuy_endtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `common_id` int(10) unsigned NOT NULL COMMENT '商品公共表ID',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品原价',
  `groupbuy_price` decimal(10,2) NOT NULL COMMENT '团购价格',
  `groupbuy_rebate` decimal(10,2) NOT NULL COMMENT '折扣',
  `groupbuy_virtual_quantity` int(10) unsigned NOT NULL COMMENT '虚拟购买数量',
  `groupbuy_upper_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买上限',
  `groupbuy_buyer_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已购买人数',
  `groupbuy_buy_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `groupbuy_intro` text NOT NULL COMMENT '本团介绍',
  `groupbuy_state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '团购状态 1.审核中 2.正常 3.结束 4.审核失败 5.管理员关闭',
  `groupbuy_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐 0.未推荐 1.已推荐',
  `groupbuy_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '团购类型：1-线上团（实物）；2-虚拟团',
  `groupbuy_views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  `groupbuy_cat_id` int(10) unsigned NOT NULL COMMENT '团购类别编号',
  `groupbuy_scat_id` int(10) NOT NULL,
  `groupbuy_city_id` int(10) NOT NULL,
  `groupbuy_area_id` int(10) unsigned NOT NULL COMMENT '团购地区编号',
  `groupbuy_image` varchar(200) NOT NULL COMMENT '团购图片',
  `groupbuy_image_rec` varchar(200) NOT NULL COMMENT '团购推荐位图片',
  `groupbuy_remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`groupbuy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8 COMMENT='团购商品表';

-- ----------------------------
--  Table structure for `yf_groupbuy_cat`
-- ----------------------------
DROP TABLE IF EXISTS `yf_groupbuy_cat`;
CREATE TABLE `yf_groupbuy_cat` (
  `groupbuy_cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '类别编号',
  `groupbuy_cat_name` varchar(20) NOT NULL COMMENT '类别名称',
  `groupbuy_cat_parent_id` int(10) unsigned NOT NULL COMMENT '父类别编号',
  `groupbuy_cat_sort` tinyint(1) unsigned NOT NULL COMMENT '排序',
  `groupbuy_cat_deep` tinyint(1) unsigned NOT NULL COMMENT '深度',
  `groupbuy_cat_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '团购类型 1-实物，2-虚拟商品',
  PRIMARY KEY (`groupbuy_cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='团购类别表';

-- ----------------------------
--  Table structure for `yf_groupbuy_combo`
-- ----------------------------
DROP TABLE IF EXISTS `yf_groupbuy_combo`;
CREATE TABLE `yf_groupbuy_combo` (
  `combo_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '团购套餐编号',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `user_nickname` varchar(50) NOT NULL COMMENT '用户名',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `combo_starttime` datetime NOT NULL COMMENT '套餐开始时间',
  `combo_endtime` datetime NOT NULL COMMENT '套餐结束时间',
  PRIMARY KEY (`combo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='团购套餐表';

-- ----------------------------
--  Table structure for `yf_groupbuy_price_range`
-- ----------------------------
DROP TABLE IF EXISTS `yf_groupbuy_price_range`;
CREATE TABLE `yf_groupbuy_price_range` (
  `range_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '价格区间编号',
  `range_name` varchar(20) NOT NULL COMMENT '区间名称',
  `range_start` int(10) unsigned NOT NULL COMMENT '区间下限',
  `range_end` int(10) unsigned NOT NULL COMMENT '区间上限',
  PRIMARY KEY (`range_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='团购价格区间表';

-- ----------------------------
--  Table structure for `yf_increase_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_increase_base`;
CREATE TABLE `yf_increase_base` (
  `increase_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '加价购活动编号',
  `increase_name` varchar(50) NOT NULL COMMENT '活动名称',
  `combo_id` int(10) unsigned NOT NULL COMMENT '套餐编号',
  `increase_start_time` datetime NOT NULL COMMENT '活动开始时间',
  `increase_end_time` datetime NOT NULL COMMENT '活动结束时间',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `user_nickname` varchar(50) NOT NULL COMMENT '用户名',
  `increase_state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '活动状态(1-正常/2-已结束/3-管理员关闭)',
  PRIMARY KEY (`increase_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='加价购活动表';

-- ----------------------------
--  Table structure for `yf_increase_combo`
-- ----------------------------
DROP TABLE IF EXISTS `yf_increase_combo`;
CREATE TABLE `yf_increase_combo` (
  `combo_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '加价购套餐编号',
  `combo_start_time` datetime NOT NULL COMMENT '开始时间',
  `combo_end_time` datetime NOT NULL COMMENT '结束时间',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `user_nickname` varchar(50) NOT NULL COMMENT '用户名',
  PRIMARY KEY (`combo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='加价购套餐表';

-- ----------------------------
--  Table structure for `yf_increase_goods`
-- ----------------------------
DROP TABLE IF EXISTS `yf_increase_goods`;
CREATE TABLE `yf_increase_goods` (
  `increase_goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '加价购商品表id',
  `increase_id` int(10) unsigned NOT NULL COMMENT '限时活动编号',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品编号',
  `common_id` int(10) NOT NULL,
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `goods_start_time` datetime NOT NULL COMMENT '开始时间',
  `goods_end_time` datetime NOT NULL COMMENT '结束时间',
  PRIMARY KEY (`increase_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8 COMMENT='加价购商品表';

-- ----------------------------
--  Table structure for `yf_increase_redemp_goods`
-- ----------------------------
DROP TABLE IF EXISTS `yf_increase_redemp_goods`;
CREATE TABLE `yf_increase_redemp_goods` (
  `redemp_goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '加价购换购商品表',
  `rule_id` int(10) unsigned NOT NULL COMMENT '加价购规则编号',
  `increase_id` int(10) unsigned NOT NULL COMMENT '加价购活动编号',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品编号',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `redemp_price` decimal(10,2) NOT NULL COMMENT '换购价',
  PRIMARY KEY (`redemp_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=utf8 COMMENT='加价购换购商品表';

-- ----------------------------
--  Table structure for `yf_increase_rule`
-- ----------------------------
DROP TABLE IF EXISTS `yf_increase_rule`;
CREATE TABLE `yf_increase_rule` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '加价购规则编号',
  `increase_id` int(10) unsigned NOT NULL COMMENT '活动编号',
  `rule_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '规则级别价格',
  `rule_goods_limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '限定换购数量，0为不限定数量',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COMMENT='加价购规则表';

-- ----------------------------
--  Table structure for `yf_invoice`
-- ----------------------------
DROP TABLE IF EXISTS `yf_invoice`;
CREATE TABLE `yf_invoice` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '索引id',
  `user_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `invoice_state` enum('1','2','3') CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '1普通发票2电子发票3增值税发票',
  `invoice_title` varchar(50) DEFAULT '' COMMENT '发票抬头[普通发票]',
  `invoice_content` varchar(10) DEFAULT '' COMMENT '发票内容[普通发票]',
  `invoice_company` varchar(50) DEFAULT '' COMMENT '单位名称',
  `invoice_code` varchar(50) DEFAULT '' COMMENT '纳税人识别号',
  `invoice_reg_addr` varchar(50) DEFAULT '' COMMENT '注册地址',
  `invoice_reg_phone` varchar(30) DEFAULT '' COMMENT '注册电话',
  `invoice_reg_bname` varchar(30) DEFAULT '' COMMENT '开户银行',
  `invoice_reg_baccount` varchar(30) DEFAULT '' COMMENT '银行帐户',
  `invoice_rec_name` varchar(20) DEFAULT '' COMMENT '收票人姓名',
  `invoice_rec_phone` varchar(15) DEFAULT '' COMMENT '收票人手机号',
  `invoice_rec_email` varchar(100) DEFAULT '' COMMENT '收票人邮箱',
  `invoice_rec_province` varchar(30) DEFAULT '' COMMENT '收票人省份',
  `invoice_goto_addr` varchar(50) DEFAULT '' COMMENT '送票地址',
  `invoice_province_id` int(11) DEFAULT NULL,
  `invoice_city_id` int(11) DEFAULT NULL,
  `invoice_area_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COMMENT='买家发票信息表';

-- ----------------------------
--  Table structure for `yf_log_action`
-- ----------------------------
DROP TABLE IF EXISTS `yf_log_action`;
CREATE TABLE `yf_log_action` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `user_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '玩家Id',
  `user_account` varchar(100) NOT NULL DEFAULT '' COMMENT '角色账户',
  `user_name` varchar(20) NOT NULL DEFAULT '' COMMENT '角色名称',
  `action_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '行为id == protocal_id -> rights_id',
  `action_type_id` mediumint(9) NOT NULL COMMENT '操作类型id，right_parent_id',
  `log_param` text NOT NULL COMMENT '请求的参数',
  `log_ip` varchar(20) NOT NULL DEFAULT '',
  `log_date` date NOT NULL COMMENT '日志日期',
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录时间',
  PRIMARY KEY (`log_id`),
  KEY `player_id` (`user_id`) COMMENT '(null)',
  KEY `log_date` (`log_date`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=132591 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户行为日志表';

-- ----------------------------
--  Table structure for `yf_mansong_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_mansong_base`;
CREATE TABLE `yf_mansong_base` (
  `mansong_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '满送活动编号',
  `mansong_name` varchar(50) NOT NULL COMMENT '活动名称',
  `combo_id` int(10) unsigned NOT NULL COMMENT '套餐编号',
  `mansong_start_time` datetime NOT NULL COMMENT '活动开始时间',
  `mansong_end_time` datetime NOT NULL COMMENT '活动结束时间',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `user_nickname` varchar(50) NOT NULL COMMENT '用户名',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `mansong_state` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '活动状态(1-正常/2-已结束/3-管理员关闭，取消)',
  `mansong_remark` varchar(200) NOT NULL COMMENT '备注',
  PRIMARY KEY (`mansong_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='满就送活动表';

-- ----------------------------
--  Table structure for `yf_mansong_combo`
-- ----------------------------
DROP TABLE IF EXISTS `yf_mansong_combo`;
CREATE TABLE `yf_mansong_combo` (
  `combo_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '满就送套餐编号',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户编号',
  `shop_id` int(11) unsigned NOT NULL COMMENT '店铺编号',
  `user_nickname` varchar(50) NOT NULL COMMENT '用户名',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `combo_start_time` datetime NOT NULL COMMENT '开始时间',
  `combo_end_time` datetime NOT NULL COMMENT '结束时间',
  PRIMARY KEY (`combo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='满就送套餐表';

-- ----------------------------
--  Table structure for `yf_mansong_rule`
-- ----------------------------
DROP TABLE IF EXISTS `yf_mansong_rule`;
CREATE TABLE `yf_mansong_rule` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则编号',
  `mansong_id` int(10) unsigned NOT NULL COMMENT '活动编号',
  `rule_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '级别价格',
  `rule_discount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '减现金优惠金额',
  `goods_name` varchar(50) NOT NULL COMMENT '礼品名称',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品编号',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='满就送活动规则表';

-- ----------------------------
--  Table structure for `yf_mb_cat_image`
-- ----------------------------
DROP TABLE IF EXISTS `yf_mb_cat_image`;
CREATE TABLE `yf_mb_cat_image` (
  `mb_cat_image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cat_id` int(10) unsigned NOT NULL COMMENT 'cat_id',
  `mb_cat_image` varchar(255) NOT NULL COMMENT '分类图片',
  `cat_adv_image` varchar(255) NOT NULL COMMENT '广告图片',
  `cat_adv_url` varchar(255) NOT NULL COMMENT '广告地址',
  PRIMARY KEY (`mb_cat_image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分类图片';

-- ----------------------------
--  Table structure for `yf_mb_tpl_layout`
-- ----------------------------
DROP TABLE IF EXISTS `yf_mb_tpl_layout`;
CREATE TABLE `yf_mb_tpl_layout` (
  `mb_tpl_layout_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mb_tpl_layout_title` varchar(50) NOT NULL COMMENT '标题',
  `mb_tpl_layout_type` varchar(50) NOT NULL COMMENT '类型',
  `mb_tpl_layout_data` text NOT NULL COMMENT '根据不同的类型，所存储的数据也不同，仔细！（json）',
  `mb_tpl_layout_enable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '使用启用 0:未启用 1:启用',
  `mb_tpl_layout_order` tinyint(2) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  PRIMARY KEY (`mb_tpl_layout_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='手机端模板';

-- ----------------------------
--  Table structure for `yf_member_agreement`
-- ----------------------------
DROP TABLE IF EXISTS `yf_member_agreement`;
CREATE TABLE `yf_member_agreement` (
  `member_agreement_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '会员协议id',
  `member_agreement_title` varchar(30) NOT NULL COMMENT '会员协议标题',
  `member_agreement_content` varchar(255) NOT NULL COMMENT '会员协议内容',
  `member_agreement_time` datetime NOT NULL COMMENT '会员协议添加时间',
  `member_agreement_pic` varchar(100) NOT NULL COMMENT '会员协议图片',
  PRIMARY KEY (`member_agreement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员协议表';

-- ----------------------------
--  Table structure for `yf_member_consume_log`
-- ----------------------------
DROP TABLE IF EXISTS `yf_member_consume_log`;
CREATE TABLE `yf_member_consume_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` int(10) NOT NULL,
  `desc` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `yf_message`
-- ----------------------------
DROP TABLE IF EXISTS `yf_message`;
CREATE TABLE `yf_message` (
  `message_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `message_user_id` int(10) NOT NULL COMMENT '消息接收者id',
  `message_user_name` varchar(50) NOT NULL COMMENT '消息接收者',
  `message_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '消息类型买家1订单信息2成长记录3账户信息4其他',
  `message_title` varchar(100) NOT NULL COMMENT '消息标题',
  `message_content` text NOT NULL COMMENT '消息内容',
  `message_islook` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否读取0未1读取',
  `message_create_time` datetime NOT NULL COMMENT '消息创建时间',
  `message_mold` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0买家1商家',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=328 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统消息表';

-- ----------------------------
--  Table structure for `yf_message_setting`
-- ----------------------------
DROP TABLE IF EXISTS `yf_message_setting`;
CREATE TABLE `yf_message_setting` (
  `setting_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `shop_id` int(10) NOT NULL COMMENT '店铺id',
  `message_template_all` varchar(255) NOT NULL COMMENT '选择开启的所有模板id',
  `setting_time` datetime NOT NULL COMMENT '设置时间',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='消息设置表';

-- ----------------------------
--  Table structure for `yf_message_template`
-- ----------------------------
DROP TABLE IF EXISTS `yf_message_template`;
CREATE TABLE `yf_message_template` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL COMMENT '主题',
  `content_email` text NOT NULL COMMENT '邮件内容',
  `type` tinyint(1) NOT NULL COMMENT '0商家1用户',
  `is_phone` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0关闭1开启',
  `is_email` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0关闭1开启',
  `is_mail` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0关闭1开启',
  `content_mail` text NOT NULL COMMENT '站内信内容',
  `content_phone` text NOT NULL COMMENT '短信内容',
  `force_phone` tinyint(1) NOT NULL DEFAULT '0' COMMENT '手机短信0不强制1强制',
  `force_email` tinyint(1) NOT NULL DEFAULT '0' COMMENT '邮件0不强制1强制',
  `force_mail` tinyint(1) NOT NULL DEFAULT '0' COMMENT '站内信0不强制1强制',
  `mold` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0常用提示1订单提示2卡券提示3售后提示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='邮件模板';

-- ----------------------------
--  Table structure for `yf_number_seq`
-- ----------------------------
DROP TABLE IF EXISTS `yf_number_seq`;
CREATE TABLE `yf_number_seq` (
  `prefix` varchar(20) NOT NULL DEFAULT '' COMMENT '前缀',
  `number` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '增长值',
  PRIMARY KEY (`prefix`),
  UNIQUE KEY `prefix` (`prefix`) COMMENT '(null)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='编号管理表';

-- ----------------------------
--  Table structure for `yf_order_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_base`;
CREATE TABLE `yf_order_base` (
  `order_id` varchar(50) NOT NULL COMMENT '订单号',
  `shop_id` int(10) NOT NULL COMMENT '卖家店铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '卖家店铺名称',
  `buyer_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '买家id',
  `buyer_user_name` varchar(50) NOT NULL COMMENT '买家姓名',
  `seller_user_id` int(10) unsigned NOT NULL COMMENT '卖家id',
  `seller_user_name` varchar(50) NOT NULL,
  `order_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '订单日期',
  `order_create_time` datetime NOT NULL COMMENT '订单生成时间',
  `order_receiver_name` varchar(50) NOT NULL COMMENT '收货人的姓名',
  `order_receiver_address` varchar(255) NOT NULL COMMENT '收货人的详细地址',
  `order_receiver_contact` varchar(50) NOT NULL COMMENT '收货人的联系方式',
  `order_receiver_date` datetime NOT NULL COMMENT '收货时间（最晚收货时间）',
  `payment_id` varchar(50) NOT NULL COMMENT '支付方式id',
  `payment_name` varchar(50) NOT NULL COMMENT '支付方式名称',
  `payment_time` datetime NOT NULL COMMENT '支付(付款)时间',
  `payment_number` varchar(20) NOT NULL COMMENT '支付单号',
  `payment_other_number` varchar(20) NOT NULL COMMENT '第三方支付平台交易号 - 最终支付的支付单号',
  `order_seller_name` varchar(50) NOT NULL COMMENT '发货人的姓名',
  `order_seller_address` varchar(255) NOT NULL COMMENT '发货人的地址',
  `order_seller_contact` varchar(50) NOT NULL COMMENT '发货人的联系方式',
  `order_shipping_time` datetime NOT NULL COMMENT '配送时间',
  `order_shipping_express_id` smallint(3) NOT NULL DEFAULT '0' COMMENT '配送公司ID',
  `order_shipping_code` varchar(50) NOT NULL COMMENT '物流单号',
  `order_shipping_message` varchar(255) NOT NULL COMMENT '卖家备注',
  `order_finished_time` datetime NOT NULL COMMENT '订单完成时间',
  `order_invoice` varchar(100) NOT NULL COMMENT '发票信息',
  `order_goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品总价格',
  `order_payment_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付金额',
  `order_discount_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '折扣价格',
  `order_point_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '买家使用积分',
  `order_shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费价格',
  `order_buyer_evaluation_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '买家评价状态 0-未评价 1-已评价',
  `order_buyer_evaluation_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '评价时间',
  `order_seller_evaluation_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '卖家评价状态 0为评价，1已评价',
  `order_seller_evaluation_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '评价时间',
  `order_message` varchar(255) NOT NULL DEFAULT '' COMMENT '订单留言',
  `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态',
  `order_points_add` int(10) NOT NULL DEFAULT '0' COMMENT '订单赠送积分',
  `voucher_id` int(10) NOT NULL COMMENT '代金券id',
  `voucher_price` int(10) NOT NULL COMMENT '代金券面额',
  `voucher_code` varchar(32) NOT NULL COMMENT '代金券编码',
  `order_refund_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态:0是无退款,1是退款中,2是退款完成',
  `order_return_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退货状态:0是无退货,1是退货中,2是退货完成',
  `order_refund_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `order_return_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退货数量',
  `order_from` enum('1','2') NOT NULL DEFAULT '1' COMMENT '手机端',
  `order_commission_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易佣金',
  `order_commission_return_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易佣金退款',
  `order_is_virtual` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟订单',
  `order_virtual_code` varchar(100) NOT NULL DEFAULT '' COMMENT '虚拟商品兑换码',
  `order_virtual_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '虚拟商品是否使用 0-未使用 1-已使用',
  `order_shop_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '卖家删除',
  `order_buyer_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '买家删除',
  `order_cancel_identity` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单取消者身份   1-买家 2-卖家 3-系统',
  `order_cancel_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '订单取消原因',
  `order_cancel_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '订单取消时间',
  `order_shop_benefit` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺优惠',
  PRIMARY KEY (`order_id`),
  KEY `shop_id` (`shop_id`),
  KEY `buyer_user_id` (`buyer_user_id`),
  KEY `seller_user_id` (`seller_user_id`),
  KEY `order_status` (`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单详细信息';

-- ----------------------------
--  Table structure for `yf_order_base1`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_base1`;
CREATE TABLE `yf_order_base1` (
  `order_id` varchar(50) NOT NULL COMMENT '订单id',
  `order_number` varchar(50) NOT NULL COMMENT '订单单号',
  `order_status` tinyint(1) NOT NULL COMMENT '订单状态',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付金额',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品总价格',
  `order_freight` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `order_create_time` datetime NOT NULL COMMENT '创建日期',
  `buyer_id` int(10) NOT NULL COMMENT '买家ID',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单表';

-- ----------------------------
--  Table structure for `yf_order_cancel_reason`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_cancel_reason`;
CREATE TABLE `yf_order_cancel_reason` (
  `cancel_reason_id` int(20) NOT NULL AUTO_INCREMENT,
  `cancel_reason_content` varchar(100) DEFAULT '' COMMENT '取消订单的原因',
  `cancel_identity` tinyint(1) DEFAULT '0' COMMENT '取消订单者的身份 1-买家 2-卖家',
  PRIMARY KEY (`cancel_reason_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单取消原因表';

-- ----------------------------
--  Table structure for `yf_order_delivery`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_delivery`;
CREATE TABLE `yf_order_delivery` (
  `order_delivery_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) DEFAULT NULL,
  `user_id` mediumint(8) unsigned DEFAULT NULL,
  `money` decimal(20,2) NOT NULL DEFAULT '0.00',
  `shipping_id` varchar(50) DEFAULT NULL,
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_no` varchar(50) DEFAULT NULL,
  `ship_name` varchar(50) DEFAULT NULL,
  `ship_addr` varchar(100) DEFAULT NULL,
  `ship_zip` varchar(20) DEFAULT NULL,
  `ship_tel` varchar(30) DEFAULT NULL,
  `ship_mobile` varchar(50) DEFAULT NULL,
  `start_time` int(10) unsigned DEFAULT NULL,
  `end_time` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`order_delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='送货地址';

-- ----------------------------
--  Table structure for `yf_order_goods`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_goods`;
CREATE TABLE `yf_order_goods` (
  `order_goods_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `order_id` varchar(50) NOT NULL COMMENT '订单id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `common_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品common_id',
  `buyer_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '买家id',
  `goods_name` varchar(100) NOT NULL COMMENT '商品名称',
  `goods_class_id` int(10) NOT NULL COMMENT '商品对应的类目ID',
  `spec_id` int(10) NOT NULL COMMENT '规格id',
  `order_spec_info` varchar(50) NOT NULL COMMENT '规格描述',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `order_goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `goods_image` varchar(255) NOT NULL COMMENT '商品图片',
  `order_goods_returnnum` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '退货数量',
  `order_goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品金额',
  `order_goods_payment_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '实付金额',
  `order_goods_discount_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `order_goods_adjust_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手工调整金额',
  `order_goods_point_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分费用',
  `order_goods_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品佣金',
  `shop_id` mediumint(10) NOT NULL DEFAULT '0' COMMENT '店铺ID',
  `order_goods_status` tinyint(1) NOT NULL COMMENT '订单状态',
  `order_goods_evaluation_status` tinyint(1) NOT NULL COMMENT '评价状态 0为评价，1已评价',
  `order_goods_benefit` varchar(255) NOT NULL DEFAULT '' COMMENT '订单商品优惠',
  `goods_refund_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '退货状态:0是无退货,1是退货中,2是退货完成',
  `order_goods_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '时间',
  PRIMARY KEY (`order_goods_id`),
  KEY `order_id` (`order_id`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=920 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单商品表';

-- ----------------------------
--  Table structure for `yf_order_goods_snapshot`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_goods_snapshot`;
CREATE TABLE `yf_order_goods_snapshot` (
  `order_goods_snapshot_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` varchar(50) NOT NULL COMMENT '订单ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `shop_id` int(10) DEFAULT NULL COMMENT '店铺ID',
  `common_id` int(10) NOT NULL COMMENT '商品common_id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `goods_image` varchar(255) DEFAULT '0' COMMENT '分类ID',
  `goods_price` float(10,2) DEFAULT '0.00' COMMENT '价格',
  `freight` float(10,2) DEFAULT '0.00' COMMENT '运费',
  `snapshot_create_time` datetime DEFAULT NULL,
  `snapshot_uptime` datetime DEFAULT NULL COMMENT '更新时间',
  `snapshot_detail` text,
  PRIMARY KEY (`order_goods_snapshot_id`)
) ENGINE=InnoDB AUTO_INCREMENT=307 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='快照表';

-- ----------------------------
--  Table structure for `yf_order_goods_virtual_code`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_goods_virtual_code`;
CREATE TABLE `yf_order_goods_virtual_code` (
  `virtual_code_id` varchar(50) NOT NULL COMMENT '虚拟码',
  `order_id` varchar(50) NOT NULL COMMENT '订单id',
  `order_goods_id` int(10) NOT NULL COMMENT '订单商品id',
  `virtual_code_status` int(10) NOT NULL DEFAULT '0' COMMENT '虚拟码状态 0:未使用 1:已使用 2:冻结',
  `virtual_code_usetime` datetime NOT NULL COMMENT '虚拟兑换码使用时间',
  PRIMARY KEY (`virtual_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='虚拟兑换码';

-- ----------------------------
--  Table structure for `yf_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_log`;
CREATE TABLE `yf_order_log` (
  `order_log_id` int(20) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) DEFAULT NULL,
  `admin_id` smallint(5) DEFAULT NULL,
  `admin_name` varchar(30) DEFAULT NULL,
  `order_log_text` longtext,
  `order_log_time` int(10) unsigned DEFAULT NULL,
  `order_log_behavior` varchar(20) DEFAULT '',
  `order_log_result` enum('success','failure') DEFAULT 'success',
  PRIMARY KEY (`order_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `yf_order_payment`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_payment`;
CREATE TABLE `yf_order_payment` (
  `order_payment_id` int(20) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(50) DEFAULT NULL,
  `user_id` mediumint(8) unsigned DEFAULT NULL,
  `order_payment_money` decimal(20,2) NOT NULL DEFAULT '0.00',
  `payment_type` enum('online','offline') DEFAULT 'online',
  `payment_id` smallint(4) DEFAULT '0',
  `payment_name` varchar(100) DEFAULT NULL,
  `order_payment_ip` varchar(20) DEFAULT NULL,
  `order_payment_start_time` int(10) unsigned DEFAULT NULL,
  `order_payment_end_time` int(10) unsigned DEFAULT NULL,
  `order_payment_status` tinyint(1) DEFAULT '1',
  `order_payment_trade_no` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`order_payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `yf_order_return`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_return`;
CREATE TABLE `yf_order_return` (
  `order_return_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '退货记录ID',
  `order_number` varchar(50) NOT NULL COMMENT '订单编号',
  `order_amount` decimal(8,2) NOT NULL COMMENT '订单总额',
  `order_goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '退货商品编号，0为退款',
  `order_goods_name` varchar(255) NOT NULL COMMENT '退款商品名称',
  `order_goods_price` decimal(8,2) NOT NULL COMMENT '商品单价',
  `order_goods_num` int(10) NOT NULL COMMENT '退货数量',
  `order_goods_pic` varchar(255) NOT NULL,
  `return_code` varchar(50) NOT NULL COMMENT '退货编号',
  `return_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-退款申请 2-退货申请 3-虚拟退款',
  `seller_user_id` int(10) unsigned NOT NULL COMMENT '卖家ID',
  `seller_user_account` varchar(50) NOT NULL COMMENT '店铺名称',
  `buyer_user_id` int(10) unsigned NOT NULL COMMENT '买家ID',
  `buyer_user_account` varchar(50) NOT NULL COMMENT '买家会员名',
  `return_add_time` datetime NOT NULL COMMENT '添加时间',
  `return_reason_id` int(10) NOT NULL COMMENT '退款理由id',
  `return_reason` varchar(255) NOT NULL COMMENT '退款理由',
  `return_message` varchar(300) NOT NULL COMMENT '退货备注',
  `return_real_name` varchar(30) NOT NULL COMMENT '收货人',
  `return_addr_id` int(10) NOT NULL COMMENT '收货地址id',
  `return_addr_name` varchar(30) NOT NULL COMMENT '收货地址',
  `return_addr` varchar(150) NOT NULL COMMENT '收货地址详情',
  `return_post_code` int(6) NOT NULL COMMENT '邮编',
  `return_tel` varchar(20) NOT NULL COMMENT '联系电话',
  `return_mobile` varchar(20) NOT NULL COMMENT '联系手机',
  `return_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-新发起等待卖家审核 2-卖家审核通过 3-卖家审核不通过 4-卖家收到货物 5-平台审核通过',
  `return_cash` decimal(8,2) NOT NULL COMMENT '退款金额',
  `return_shop_time` datetime NOT NULL COMMENT '商家处理时间',
  `return_shop_message` varchar(300) NOT NULL COMMENT '商家备注',
  `return_finish_time` datetime NOT NULL COMMENT '退款完成时间',
  `return_commision_fee` decimal(8,2) NOT NULL COMMENT '退还佣金',
  `return_platform_message` varchar(255) NOT NULL COMMENT '平台留言',
  `return_goods_return` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需要退货 0-不需要，1-需要',
  PRIMARY KEY (`order_return_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='退货表';

-- ----------------------------
--  Table structure for `yf_order_return_reason`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_return_reason`;
CREATE TABLE `yf_order_return_reason` (
  `order_return_reason_id` int(10) NOT NULL AUTO_INCREMENT,
  `order_return_reason_content` varchar(255) NOT NULL COMMENT '投诉理由内容',
  `order_return_reason_sort` int(3) NOT NULL DEFAULT '225' COMMENT '投诉理由排序',
  PRIMARY KEY (`order_return_reason_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `yf_order_settlement`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_settlement`;
CREATE TABLE `yf_order_settlement` (
  `os_id` varchar(11) NOT NULL COMMENT '结算单编号(年月店铺ID)',
  `os_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始日期',
  `os_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束日期',
  `os_order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `os_shipping_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `os_order_return_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退单金额',
  `os_commis_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `os_commis_return_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退还佣金',
  `os_shop_cost_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '店铺促销活动费用',
  `os_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应结金额',
  `os_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '生成结算单日期',
  `os_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '结算单年月份',
  `os_state` enum('1','2','3','4') NOT NULL DEFAULT '1' COMMENT '1默认(已出账)2店家已确认3平台已审核4结算完成',
  `os_pay_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '付款日期',
  `os_pay_content` varchar(200) NOT NULL DEFAULT '' COMMENT '支付备注',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `shop_name` varchar(50) NOT NULL DEFAULT '' COMMENT '店铺名',
  `os_order_type` tinyint(1) NOT NULL COMMENT '结算订单类型 1-虚拟订单 2-实物订单',
  PRIMARY KEY (`os_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单结算表';

-- ----------------------------
--  Table structure for `yf_order_settlement_stat`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_settlement_stat`;
CREATE TABLE `yf_order_settlement_stat` (
  `date` mediumint(9) unsigned NOT NULL,
  `settlement_year` smallint(6) NOT NULL COMMENT '年',
  `start_time` int(11) NOT NULL COMMENT '开始日期',
  `end_time` int(11) NOT NULL COMMENT '结束日期',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `return_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退单金额',
  `commission_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `commission_return_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退还佣金',
  `shop_cost_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '店铺促销活动费用',
  `result_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '本期应结',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='月销量统计表';

-- ----------------------------
--  Table structure for `yf_order_state`
-- ----------------------------
DROP TABLE IF EXISTS `yf_order_state`;
CREATE TABLE `yf_order_state` (
  `order_state_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT '状态id',
  `order_state_name` varchar(50) NOT NULL COMMENT '订单状态',
  `order_state_text_1` varchar(255) NOT NULL,
  `order_state_text_2` varchar(255) NOT NULL,
  `order_state_text_3` varchar(255) NOT NULL,
  `order_state_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`order_state_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单状态表';

-- ----------------------------
--  Table structure for `yf_payment_channel`
-- ----------------------------
DROP TABLE IF EXISTS `yf_payment_channel`;
CREATE TABLE `yf_payment_channel` (
  `payment_channel_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `payment_channel_code` varchar(20) NOT NULL DEFAULT '' COMMENT '代码名称',
  `payment_channel_name` varchar(100) NOT NULL DEFAULT '' COMMENT '支付名称',
  `payment_channel_config` text NOT NULL COMMENT '支付接口配置信息',
  `payment_channel_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '接口状态',
  `payment_channel_allow` enum('pc','wap','both') NOT NULL DEFAULT 'pc' COMMENT '类型',
  `payment_channel_wechat` tinyint(4) NOT NULL DEFAULT '1' COMMENT '微信中是否可以使用',
  `payment_channel_enable` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用',
  PRIMARY KEY (`payment_channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='支付渠道表';

-- ----------------------------
--  Table structure for `yf_platform_custom_service`
-- ----------------------------
DROP TABLE IF EXISTS `yf_platform_custom_service`;
CREATE TABLE `yf_platform_custom_service` (
  `custom_service_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '平台咨询ID',
  `custom_service_type_id` int(10) unsigned NOT NULL COMMENT '平台咨询类型ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户Id',
  `user_account` varchar(50) NOT NULL COMMENT '用户账号',
  `custom_service_question` varchar(255) NOT NULL COMMENT '咨询内容',
  `custom_service_question_time` datetime NOT NULL,
  `user_id_admin` int(10) unsigned NOT NULL COMMENT '平台客服id-管理员id',
  `custom_service_answer` varchar(255) NOT NULL COMMENT '咨询回复',
  `custom_service_answer_time` datetime NOT NULL,
  `custom_service_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否回复  1   2:已经回复',
  PRIMARY KEY (`custom_service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='平台客服-平台咨询表';

-- ----------------------------
--  Table structure for `yf_platform_custom_service_type`
-- ----------------------------
DROP TABLE IF EXISTS `yf_platform_custom_service_type`;
CREATE TABLE `yf_platform_custom_service_type` (
  `custom_service_type_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '平台咨询类型ID',
  `custom_service_type_sort` int(3) NOT NULL DEFAULT '255' COMMENT '平台咨询类型排序',
  `custom_service_type_name` varchar(50) NOT NULL COMMENT '平台咨询类型名',
  `custom_service_type_desc` varchar(255) NOT NULL COMMENT '平台咨询类型备注',
  PRIMARY KEY (`custom_service_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='平台咨询类别表';

-- ----------------------------
--  Table structure for `yf_platform_nav`
-- ----------------------------
DROP TABLE IF EXISTS `yf_platform_nav`;
CREATE TABLE `yf_platform_nav` (
  `nav_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '索引ID',
  `nav_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类别，0自定义导航，1商品分类，2文章导航，3活动导航，默认为0',
  `nav_item_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类别ID，对应着nav_type中的内容，默认为0',
  `nav_title` varchar(100) NOT NULL COMMENT '导航标题',
  `nav_url` varchar(255) NOT NULL COMMENT '导航链接',
  `nav_location` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导航位置，0头部，1中部，2底部，默认为0',
  `nav_new_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否以新窗口打开，0为否，1为是，默认为0',
  `nav_displayorder` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `nav_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `nav_readonly` tinyint(4) NOT NULL DEFAULT '0' COMMENT '不可修改-团购、积分等等',
  PRIMARY KEY (`nav_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='页面导航表';

-- ----------------------------
--  Table structure for `yf_platform_report`
-- ----------------------------
DROP TABLE IF EXISTS `yf_platform_report`;
CREATE TABLE `yf_platform_report` (
  `report_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(10) NOT NULL COMMENT '会员id',
  `user_account` varchar(50) NOT NULL COMMENT '会员名',
  `goods_id` int(10) NOT NULL COMMENT '被举报的商品id',
  `goods_name` varchar(100) NOT NULL COMMENT '被举报的商品名称',
  `goods_image` varchar(255) NOT NULL,
  `subject_id` int(10) NOT NULL COMMENT '举报主题id',
  `subject_name` varchar(50) NOT NULL COMMENT '举报主题',
  `report_content` varchar(100) NOT NULL COMMENT '举报信息',
  `report_image` varchar(255) NOT NULL COMMENT '图片',
  `report_time` datetime NOT NULL COMMENT '举报时间',
  `shop_id` int(10) NOT NULL COMMENT '被举报商品的店铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '被举报商品的店铺',
  `report_state` tinyint(1) NOT NULL COMMENT '举报状态(1未处理/2已处理)',
  `report_result` tinyint(1) NOT NULL COMMENT '举报处理结果(1无效举报/2恶意举报/3有效举报)',
  `report_message` varchar(100) NOT NULL COMMENT '举报处理信息',
  `report_handle_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '举报处理时间',
  `report_handle_admin` varchar(50) NOT NULL DEFAULT '0' COMMENT '管理员',
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='举报表';

-- ----------------------------
--  Table structure for `yf_platform_report_subject`
-- ----------------------------
DROP TABLE IF EXISTS `yf_platform_report_subject`;
CREATE TABLE `yf_platform_report_subject` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '举报主题id',
  `subject_name` varchar(100) NOT NULL COMMENT '举报主题内容',
  `type_id` int(11) NOT NULL COMMENT '举报类型id',
  `type_name` varchar(50) NOT NULL COMMENT '举报类型名称 ',
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='举报主题表';

-- ----------------------------
--  Table structure for `yf_platform_report_subject_type`
-- ----------------------------
DROP TABLE IF EXISTS `yf_platform_report_subject_type`;
CREATE TABLE `yf_platform_report_subject_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '举报类型id',
  `name` varchar(50) NOT NULL COMMENT '举报类型名称 ',
  `desc` varchar(100) NOT NULL COMMENT '举报类型描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='举报类型表';

-- ----------------------------
--  Table structure for `yf_points_cart`
-- ----------------------------
DROP TABLE IF EXISTS `yf_points_cart`;
CREATE TABLE `yf_points_cart` (
  `points_cart_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `points_user_id` int(10) NOT NULL COMMENT '会员编号',
  `points_goods_id` int(10) NOT NULL COMMENT '积分礼品序号',
  `points_goods_name` varchar(10) NOT NULL COMMENT '积分礼品名称',
  `points_goods_points` int(10) NOT NULL COMMENT '积分礼品兑换积分',
  `points_goods_choosenum` int(10) NOT NULL COMMENT '选择积分礼品数量',
  `points_goods_image` varchar(255) NOT NULL COMMENT '积分礼品图片',
  PRIMARY KEY (`points_cart_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COMMENT='积分礼品兑换购物车';

-- ----------------------------
--  Table structure for `yf_points_goods`
-- ----------------------------
DROP TABLE IF EXISTS `yf_points_goods`;
CREATE TABLE `yf_points_goods` (
  `points_goods_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '积分礼品索引id',
  `points_goods_name` varchar(100) NOT NULL COMMENT '积分礼品名称',
  `points_goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分礼品原价',
  `points_goods_points` int(10) NOT NULL COMMENT '积分礼品兑换所需积分',
  `points_goods_image` varchar(255) NOT NULL COMMENT '积分礼品默认封面图片',
  `points_goods_tag` varchar(100) NOT NULL COMMENT '积分礼品标签',
  `points_goods_serial` varchar(50) NOT NULL COMMENT '积分礼品货号',
  `points_goods_storage` int(10) NOT NULL DEFAULT '0' COMMENT '积分礼品库存数',
  `points_goods_shelves` tinyint(1) NOT NULL COMMENT '积分礼品上架 0表示下架 1表示上架',
  `points_goods_recommend` tinyint(1) NOT NULL COMMENT '积分礼品是否推荐,1-是、0-否',
  `points_goods_add_time` datetime NOT NULL COMMENT '积分礼品添加时间',
  `points_goods_keywords` varchar(100) NOT NULL COMMENT '积分礼品关键字',
  `points_goods_description` varchar(200) NOT NULL COMMENT '积分礼品描述',
  `points_goods_body` text NOT NULL COMMENT '积分礼品详细内容',
  `points_goods_salenum` int(10) NOT NULL DEFAULT '0' COMMENT '积分礼品售出数量',
  `points_goods_view` int(10) NOT NULL DEFAULT '0' COMMENT '积分商品浏览次数',
  `points_goods_limitgrade` int(10) NOT NULL DEFAULT '0' COMMENT '换购针对会员等级限制，默认为0,所有等级都可换购',
  `points_goods_islimit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否限制每会员兑换数量，0不限制，1限制，默认0',
  `points_goods_limitnum` int(10) NOT NULL COMMENT '每会员限制兑换数量',
  `points_goods_islimittime` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否限制兑换时间 0为不限制 1为限制',
  `points_goods_starttime` datetime NOT NULL COMMENT '兑换开始时间',
  `points_goods_endtime` datetime NOT NULL COMMENT '兑换结束时间',
  `points_goods_sort` int(10) NOT NULL DEFAULT '0' COMMENT '礼品排序',
  PRIMARY KEY (`points_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='积分礼品表';

-- ----------------------------
--  Table structure for `yf_points_log`
-- ----------------------------
DROP TABLE IF EXISTS `yf_points_log`;
CREATE TABLE `yf_points_log` (
  `points_log_id` int(10) NOT NULL AUTO_INCREMENT,
  `points_log_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型 1获取积分2积分消费',
  `class_id` tinyint(1) NOT NULL COMMENT '积分类型1.会员注册,2.会员登录3.评价4.购买商品5.6.管理员操作7.积分换购商品8.积分兑换代金券',
  `user_id` int(10) NOT NULL COMMENT '会员编号',
  `user_name` varchar(50) NOT NULL COMMENT '会员名称',
  `admin_name` varchar(100) NOT NULL COMMENT '管理员名称',
  `points_log_points` int(10) NOT NULL DEFAULT '0' COMMENT '可用积分',
  `freeze_points` int(10) NOT NULL DEFAULT '0' COMMENT '冻结积分',
  `points_log_time` datetime NOT NULL COMMENT '创建时间',
  `points_log_desc` varchar(100) NOT NULL COMMENT '描述',
  `points_log_flag` varchar(20) NOT NULL COMMENT '标记',
  PRIMARY KEY (`points_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1980 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='会员积分日志表';

-- ----------------------------
--  Table structure for `yf_points_order`
-- ----------------------------
DROP TABLE IF EXISTS `yf_points_order`;
CREATE TABLE `yf_points_order` (
  `points_order_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '兑换订单编号',
  `points_order_rid` varchar(50) NOT NULL COMMENT '兑换订单号',
  `points_buyerid` int(10) NOT NULL COMMENT '兑换会员id',
  `points_buyername` varchar(50) NOT NULL COMMENT '兑换会员姓名',
  `points_buyeremail` varchar(100) NOT NULL COMMENT '兑换会员email',
  `points_addtime` datetime NOT NULL COMMENT '兑换订单生成时间',
  `points_paymenttime` datetime NOT NULL COMMENT '支付(付款)时间',
  `points_shippingtime` datetime NOT NULL COMMENT '配送时间',
  `points_shippingcode` varchar(50) NOT NULL COMMENT '物流单号',
  `points_logistics` varchar(50) NOT NULL COMMENT '物流公司名称',
  `points_finnshedtime` datetime NOT NULL COMMENT '订单完成时间',
  `points_allpoints` int(10) NOT NULL DEFAULT '0' COMMENT '兑换总积分',
  `points_orderamount` decimal(10,2) NOT NULL COMMENT '兑换订单总金额',
  `points_shippingcharge` tinyint(1) NOT NULL DEFAULT '0' COMMENT '运费承担方式 0表示平台 1表示买家',
  `points_shippingfee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费金额',
  `points_ordermessage` varchar(300) NOT NULL DEFAULT '无' COMMENT '订单留言',
  `points_orderstate` int(4) NOT NULL DEFAULT '1' COMMENT '订单状态：1(已下单，等待发货);2(已发货，等待收货);3(确认收货)4(取消):',
  PRIMARY KEY (`points_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='积分兑换订单表';

-- ----------------------------
--  Table structure for `yf_points_orderaddress`
-- ----------------------------
DROP TABLE IF EXISTS `yf_points_orderaddress`;
CREATE TABLE `yf_points_orderaddress` (
  `points_oaid` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `points_orderid` varchar(20) NOT NULL COMMENT '订单id',
  `points_truename` varchar(10) NOT NULL COMMENT '收货人姓名',
  `points_areaid` int(10) NOT NULL COMMENT '地区id',
  `points_areainfo` varchar(100) NOT NULL COMMENT '地区内容',
  `points_address` varchar(200) NOT NULL COMMENT '详细地址',
  `points_zipcode` varchar(20) NOT NULL COMMENT '邮政编码',
  `points_telphone` varchar(20) NOT NULL COMMENT '电话号码',
  `points_mobphone` varchar(20) NOT NULL COMMENT '手机号码',
  PRIMARY KEY (`points_oaid`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='兑换订单地址表';

-- ----------------------------
--  Table structure for `yf_points_ordergoods`
-- ----------------------------
DROP TABLE IF EXISTS `yf_points_ordergoods`;
CREATE TABLE `yf_points_ordergoods` (
  `points_recid` int(10) NOT NULL AUTO_INCREMENT COMMENT '订单礼品表索引',
  `points_orderid` varchar(50) NOT NULL COMMENT '订单id',
  `points_goodsid` int(10) NOT NULL COMMENT '礼品id',
  `points_goodsname` varchar(100) NOT NULL COMMENT '礼品名称',
  `points_goodspoints` int(10) NOT NULL COMMENT '礼品兑换积分',
  `points_goodsnum` int(10) NOT NULL COMMENT '礼品数量',
  `points_goodsimage` varchar(255) NOT NULL COMMENT '礼品图片',
  PRIMARY KEY (`points_recid`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='兑换订单商品表';

-- ----------------------------
--  Table structure for `yf_rec_position`
-- ----------------------------
DROP TABLE IF EXISTS `yf_rec_position`;
CREATE TABLE `yf_rec_position` (
  `position_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `position_title` varchar(30) NOT NULL COMMENT '推荐位标题',
  `position_type` tinyint(1) NOT NULL COMMENT '推荐位类型 0-图片 1-文字',
  `position_pic` varchar(255) NOT NULL COMMENT '推荐位图片',
  `position_content` varchar(255) NOT NULL COMMENT '文字展示',
  `position_alert_type` tinyint(1) NOT NULL COMMENT '弹出方式 0 本窗口 1 新窗口',
  `position_url` varchar(255) NOT NULL COMMENT '跳转网址',
  `position_code` varchar(255) NOT NULL COMMENT '调用代码',
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `yf_report_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_report_base`;
CREATE TABLE `yf_report_base` (
  `report_id` int(10) NOT NULL AUTO_INCREMENT,
  `report_type_id` int(10) NOT NULL,
  `report_type_name` varchar(50) NOT NULL,
  `report_subject_id` int(10) NOT NULL,
  `report_subject_name` varchar(50) NOT NULL,
  `report_message` varchar(255) NOT NULL,
  `report_pic` text NOT NULL COMMENT '举报证据，逗号分隔',
  `report_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-未处理 2-已处理',
  `user_id` int(10) NOT NULL,
  `user_account` varchar(50) NOT NULL,
  `shop_id` int(10) NOT NULL,
  `shop_name` varchar(50) NOT NULL,
  `goods_id` int(10) NOT NULL,
  `goods_name` varchar(255) NOT NULL,
  `goods_pic` varchar(255) NOT NULL,
  `report_date` datetime NOT NULL,
  `report_handle_message` varchar(255) NOT NULL,
  `report_handle_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-未处理 1-有效 2-无效',
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `yf_report_subject`
-- ----------------------------
DROP TABLE IF EXISTS `yf_report_subject`;
CREATE TABLE `yf_report_subject` (
  `report_subject_id` int(10) NOT NULL AUTO_INCREMENT,
  `report_subject_name` varchar(50) NOT NULL,
  `report_type_id` int(10) NOT NULL,
  `report_type_name` varchar(50) NOT NULL,
  PRIMARY KEY (`report_subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `yf_report_type`
-- ----------------------------
DROP TABLE IF EXISTS `yf_report_type`;
CREATE TABLE `yf_report_type` (
  `report_type_id` int(10) NOT NULL AUTO_INCREMENT,
  `report_type_name` varchar(50) NOT NULL,
  `report_type_desc` varchar(255) NOT NULL,
  PRIMARY KEY (`report_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `yf_search_word`
-- ----------------------------
DROP TABLE IF EXISTS `yf_search_word`;
CREATE TABLE `yf_search_word` (
  `search_id` int(11) NOT NULL AUTO_INCREMENT,
  `search_keyword` varchar(80) DEFAULT NULL,
  `search_char_index` varchar(80) DEFAULT NULL,
  `search_nums` int(11) DEFAULT '0',
  PRIMARY KEY (`search_id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='搜索热门词';

-- ----------------------------
--  Table structure for `yf_seller_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_seller_base`;
CREATE TABLE `yf_seller_base` (
  `seller_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '卖家id',
  `seller_name` varchar(50) NOT NULL COMMENT '卖家用户名',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `rights_group_id` int(10) unsigned NOT NULL COMMENT '卖家组ID',
  `seller_is_admin` tinyint(3) unsigned NOT NULL COMMENT '是否管理员(0-不是 1-是)',
  `seller_login_time` datetime NOT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`seller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2377 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='卖家用户表';

-- ----------------------------
--  Table structure for `yf_seller_log`
-- ----------------------------
DROP TABLE IF EXISTS `yf_seller_log`;
CREATE TABLE `yf_seller_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` varchar(50) NOT NULL COMMENT '日志内容',
  `create_time` int(10) unsigned NOT NULL COMMENT '日志时间',
  `seller_id` int(10) unsigned NOT NULL COMMENT '卖家id',
  `seller_name` varchar(50) NOT NULL COMMENT '卖家帐号',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `seller_ip` varchar(50) NOT NULL COMMENT '卖家ip',
  `url` varchar(50) NOT NULL COMMENT '日志url',
  `status` tinyint(1) unsigned NOT NULL COMMENT '日志状态(0-失败 1-成功)',
  PRIMARY KEY (`id`),
  KEY `shop_id` (`shop_id`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=411 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='卖家日志表';

-- ----------------------------
--  Table structure for `yf_seller_rights_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_seller_rights_base`;
CREATE TABLE `yf_seller_rights_base` (
  `rights_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限Id',
  `rights_name` varchar(20) NOT NULL DEFAULT '' COMMENT '权限名称',
  `rights_parent_id` smallint(4) unsigned NOT NULL COMMENT '权限父Id',
  `rights_remark` varchar(255) NOT NULL COMMENT '备注',
  `rights_order` smallint(4) NOT NULL DEFAULT '50' COMMENT '排序',
  PRIMARY KEY (`rights_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限表 ';

-- ----------------------------
--  Table structure for `yf_seller_rights_group`
-- ----------------------------
DROP TABLE IF EXISTS `yf_seller_rights_group`;
CREATE TABLE `yf_seller_rights_group` (
  `rights_group_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限组id',
  `rights_group_name` varchar(50) NOT NULL COMMENT '权限组名称',
  `rights_group_rights_ids` text NOT NULL COMMENT '权限列表',
  `rights_group_add_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`rights_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限组表';

-- ----------------------------
--  Table structure for `yf_service`
-- ----------------------------
DROP TABLE IF EXISTS `yf_service`;
CREATE TABLE `yf_service` (
  `service_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT '消费者保障id',
  `service_name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `service_desc` text NOT NULL COMMENT '消费者保障描述',
  `service_deposit` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '保证金',
  `service_icon` varchar(200) NOT NULL DEFAULT '' COMMENT '项目图标',
  `service_url` varchar(200) NOT NULL DEFAULT '' COMMENT '说明文章链接地址',
  `service_displayorder` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
  `service_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启',
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='消费者保障服务表';

-- ----------------------------
--  Table structure for `yf_shared_bindings`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shared_bindings`;
CREATE TABLE `yf_shared_bindings` (
  `shared_bindings_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '分享绑定id',
  `shared_bindings_name` varchar(50) NOT NULL COMMENT '分享绑定的名字',
  `shared_bindings_ulr` varchar(50) NOT NULL COMMENT '绑定的url',
  `shared_bindings_statu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开启状态0否1开启',
  `shared_bindings_appid` varchar(50) NOT NULL COMMENT '应用标识',
  `shared_bindings_key` varchar(100) NOT NULL COMMENT '应用密钥',
  `shared_bindings_appcode` text NOT NULL COMMENT '域名验证信息',
  PRIMARY KEY (`shared_bindings_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `yf_shop_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_base`;
CREATE TABLE `yf_shop_base` (
  `shop_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `shop_grade_id` int(10) NOT NULL DEFAULT '0' COMMENT '店铺等级',
  `shop_class_id` int(10) NOT NULL DEFAULT '0' COMMENT '店铺绑定分类，如果是自营店铺就为0.',
  `shop_all_class` tinyint(1) NOT NULL DEFAULT '0' COMMENT '绑定所有经营类目',
  `shop_self_support` enum('true','false') NOT NULL DEFAULT 'false' COMMENT '是否自营',
  `shop_create_time` datetime NOT NULL COMMENT '开店时间',
  `shop_end_time` datetime NOT NULL COMMENT '有效期截止时间',
  `shop_latitude` varchar(20) NOT NULL DEFAULT '' COMMENT '纬度',
  `shop_longitude` varchar(20) NOT NULL DEFAULT '' COMMENT '经度',
  `shop_settlement_cycle` mediumint(4) NOT NULL DEFAULT '30' COMMENT '结算周期-天为单位-如果您想设置结算周期为一个月，则可以输入30',
  `shop_points` int(10) NOT NULL DEFAULT '0' COMMENT '积分',
  `shop_logo` varchar(255) NOT NULL COMMENT '店铺logo',
  `shop_banner` varchar(255) NOT NULL COMMENT '店铺banner',
  `shop_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '店铺状态-3：开店成功 2:待审核付款 1:待审核资料  0:关闭',
  `shop_close_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '关闭原因',
  `shop_praise_rate` float(10,2) NOT NULL DEFAULT '0.00',
  `shop_desccredit` float(10,2) NOT NULL DEFAULT '0.00',
  `shop_servicecredit` float(10,2) NOT NULL DEFAULT '0.00',
  `shop_deliverycredit` float(10,2) NOT NULL DEFAULT '0.00',
  `shop_collect` int(10) NOT NULL DEFAULT '0',
  `shop_template` varchar(255) NOT NULL DEFAULT 'default' COMMENT '店铺绑定模板',
  `shop_workingtime` text NOT NULL COMMENT '工作时间',
  `shop_slide` text NOT NULL,
  `shop_slideurl` text NOT NULL,
  `shop_domain` varchar(20) NOT NULL COMMENT '二级域名',
  `shop_region` varchar(50) NOT NULL DEFAULT '' COMMENT '店铺默认配送区域',
  `shop_address` varchar(255) NOT NULL DEFAULT '' COMMENT '详细地址',
  `shop_qq` varchar(20) NOT NULL COMMENT 'qq',
  `shop_ww` varchar(20) NOT NULL DEFAULT '' COMMENT '旺旺',
  `shop_tel` varchar(12) NOT NULL DEFAULT '' COMMENT '卖家电话',
  `shop_free_shipping` int(10) NOT NULL DEFAULT '0' COMMENT '免运费额度',
  `shop_account` varchar(255) NOT NULL COMMENT '商家账号',
  `shop_payment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未付款，1已付款',
  `joinin_year` int(10) NOT NULL DEFAULT '0' COMMENT '加入时间',
  `is_renovation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启装修(0:否，1：是)',
  `is_only_renovation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否仅显示装修(1：是，0：否）',
  `is_index_left` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否左侧显示',
  `shop_print_desc` varchar(500) DEFAULT NULL COMMENT '打印订单页面下方说明',
  `shop_stamp` varchar(200) DEFAULT NULL COMMENT '店铺印章-将出现在打印订单的右下角位置',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺表';

-- ----------------------------
--  Table structure for `yf_shop_class`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_class`;
CREATE TABLE `yf_shop_class` (
  `shop_class_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '店铺分类id',
  `shop_class_name` varchar(50) NOT NULL COMMENT '店铺分类名称',
  `shop_class_deposit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '保证金数额(元)',
  `shop_class_displayorder` smallint(3) NOT NULL DEFAULT '255' COMMENT '显示次序',
  PRIMARY KEY (`shop_class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺分类表-平台设置';

-- ----------------------------
--  Table structure for `yf_shop_class_bind`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_class_bind`;
CREATE TABLE `yf_shop_class_bind` (
  `shop_class_bind_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺id',
  `product_class_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类id',
  `commission_rate` decimal(4,0) NOT NULL DEFAULT '0' COMMENT '百分比',
  `shop_class_bind_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  PRIMARY KEY (`shop_class_bind_id`)
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺经营类目\r\n';

-- ----------------------------
--  Table structure for `yf_shop_company`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_company`;
CREATE TABLE `yf_shop_company` (
  `shop_id` int(10) NOT NULL,
  `shop_company_name` varchar(50) NOT NULL DEFAULT '' COMMENT '公司名称',
  `shop_company_address` varchar(50) NOT NULL DEFAULT '' COMMENT '公司所在地',
  `company_address_detail` varchar(100) NOT NULL DEFAULT '' COMMENT '公司详细地址',
  `company_employee_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '员工总数',
  `company_registered_capital` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册资金',
  `company_phone` varchar(255) NOT NULL DEFAULT '' COMMENT '公司电话',
  `contacts_phone` varchar(255) NOT NULL DEFAULT '' COMMENT '联系人电话',
  `contacts_email` varchar(255) NOT NULL DEFAULT '' COMMENT '联系人email',
  `contacts_name` varchar(255) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `legal_person` varchar(50) NOT NULL COMMENT '法定代表人姓名',
  `legal_person_number` varchar(50) NOT NULL COMMENT '法人身份证号',
  `legal_person_electronic` varchar(255) NOT NULL COMMENT '法人身份证电子版',
  `business_license_location` varchar(255) NOT NULL COMMENT '营业执照所在地',
  `establish_date` date NOT NULL COMMENT '成立日期',
  `business_licence_start` date NOT NULL COMMENT '法定经营范围开始时间',
  `business_licence_end` date NOT NULL COMMENT '法定经营范围结束时间',
  `business_sphere` varchar(255) NOT NULL COMMENT '业务范围',
  `business_license_electronic` varchar(255) NOT NULL COMMENT '营业执照电子版',
  `organization_code` varchar(20) NOT NULL COMMENT '组织机构代码',
  `organization_code_start` date NOT NULL COMMENT '组织机构代码证有效期开始时间',
  `organization_code_end` date NOT NULL COMMENT '组织机构代码证有效期结束时间',
  `organization_code_electronic` varchar(255) NOT NULL COMMENT '组织机构代码证电子版',
  `general_taxpayer` varchar(255) NOT NULL COMMENT '一般纳税人证明',
  `bank_account_name` varchar(50) NOT NULL COMMENT '银行开户名',
  `bank_account_number` varchar(20) NOT NULL COMMENT '公司银行账号',
  `bank_name` varchar(50) NOT NULL COMMENT '开户银行支行名称',
  `bank_code` varchar(20) NOT NULL COMMENT '开户银行支行联行号',
  `bank_address` varchar(255) NOT NULL COMMENT '开户银行支行所在地',
  `bank_licence_electronic` varchar(255) NOT NULL COMMENT '开户银行许可证电子版',
  `tax_registration_certificate` varchar(20) NOT NULL COMMENT '税务登记证号',
  `taxpayer_id` varchar(20) NOT NULL COMMENT '纳税人识别号',
  `tax_registration_certificate_electronic` varchar(255) NOT NULL COMMENT '税务登记证号电子版',
  `payment_voucher` varchar(255) NOT NULL COMMENT '付款凭证',
  `payment_voucher_explain` varchar(255) NOT NULL COMMENT '付款凭证说明',
  `shop_class_ids` text NOT NULL COMMENT '店铺经营类目ID集合',
  `shop_class_names` text NOT NULL COMMENT '店铺经营类目名称集合',
  `shop_class_commission` text NOT NULL COMMENT '店铺经营类目佣金比例',
  `fee` float(10,2) NOT NULL COMMENT '收费标准',
  `deposit` float(10,2) NOT NULL COMMENT '保证金',
  `business_id` varchar(20) NOT NULL DEFAULT '0' COMMENT '营业执照号',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺公司信息表';

-- ----------------------------
--  Table structure for `yf_shop_contract`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_contract`;
CREATE TABLE `yf_shop_contract` (
  `contract_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '关联ID',
  `contract_type_id` int(10) NOT NULL COMMENT '服务id',
  `shop_id` int(10) NOT NULL COMMENT '商铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '商铺名称',
  `contract_type_name` varchar(50) NOT NULL COMMENT '服务类别名称',
  `contract_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1-可以使用 2-永久不能使用',
  `contract_use_state` tinyint(1) NOT NULL DEFAULT '2' COMMENT '加入状态：1--已加入 2-已退出',
  `contract_cash` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '保障金余额',
  `contract_log_id` int(10) NOT NULL COMMENT '保证金当前日志id',
  PRIMARY KEY (`contract_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='消费者保障服务店铺关联表';

-- ----------------------------
--  Table structure for `yf_shop_contract_log`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_contract_log`;
CREATE TABLE `yf_shop_contract_log` (
  `contract_log_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `contract_id` int(10) NOT NULL COMMENT '服务id',
  `contract_type_id` int(10) NOT NULL COMMENT '服务id',
  `contract_type_name` varchar(50) NOT NULL COMMENT '服务名称',
  `shop_id` int(10) NOT NULL COMMENT '店铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `contract_log_operator` varchar(50) NOT NULL COMMENT '操作人',
  `contract_log_date` datetime NOT NULL COMMENT '日志生成时间',
  `contract_log_desc` varchar(255) NOT NULL COMMENT '日志内容',
  `contract_cash` decimal(10,2) NOT NULL COMMENT '支付保证金金额,有正负',
  `contract_log_type` tinyint(1) NOT NULL DEFAULT '4' COMMENT '1-保证金操作 2-加入操作 3-退出操作 4-其它操作',
  `contract_log_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-待审核(加入/退出) 2-保证金待审核(加入) 3-审核通过(加入/退出) 4-审核不通过(加入/退出) 5-已缴纳保证金',
  `contract_cash_pic` varchar(255) NOT NULL COMMENT '保证金图片',
  PRIMARY KEY (`contract_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COMMENT='消费者保障服务保证金缴纳日志表';

-- ----------------------------
--  Table structure for `yf_shop_contract_type`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_contract_type`;
CREATE TABLE `yf_shop_contract_type` (
  `contract_type_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '服务id',
  `contract_type_name` varchar(50) NOT NULL COMMENT '服务名称',
  `contract_type_cash` decimal(10,2) NOT NULL COMMENT '保证金金额',
  `contract_type_logo` varchar(255) NOT NULL COMMENT '服务logo',
  `contract_type_desc` text NOT NULL COMMENT '服务介绍',
  `contract_type_url` varchar(100) NOT NULL COMMENT '服务介绍文章链接',
  `contract_type_sort` int(3) NOT NULL COMMENT '显示顺序',
  `contract_type_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '服务状态：1-开启，2-关闭',
  PRIMARY KEY (`contract_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='消费者保障服务类型表';

-- ----------------------------
--  Table structure for `yf_shop_cost`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_cost`;
CREATE TABLE `yf_shop_cost` (
  `cost_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `user_account` varchar(50) NOT NULL COMMENT '用户账号',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `cost_price` float(10,2) NOT NULL COMMENT '费用',
  `cost_desc` varchar(255) NOT NULL COMMENT '描述',
  `cost_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0未结算 1已结算',
  `cost_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`cost_id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺费用表';

-- ----------------------------
--  Table structure for `yf_shop_custom_service`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_custom_service`;
CREATE TABLE `yf_shop_custom_service` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `shop_id` int(10) NOT NULL COMMENT '店铺id',
  `name` varchar(20) NOT NULL COMMENT '客服名称',
  `tool` tinyint(1) NOT NULL COMMENT '客服工具',
  `number` varchar(30) NOT NULL COMMENT '客服账号',
  `type` tinyint(1) NOT NULL COMMENT '客服类型 0-售前客服 1-售后客服',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺客服表';

-- ----------------------------
--  Table structure for `yf_shop_decoration`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_decoration`;
CREATE TABLE `yf_shop_decoration` (
  `decoration_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '装修编号',
  `decoration_name` varchar(50) NOT NULL COMMENT '装修名称',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `decoration_setting` varchar(500) NOT NULL COMMENT '装修整体设置(背景、边距等)',
  `decoration_nav` varchar(5000) NOT NULL COMMENT '装修导航',
  `decoration_banner` varchar(255) NOT NULL COMMENT '装修头部banner',
  PRIMARY KEY (`decoration_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='店铺装修表';

-- ----------------------------
--  Table structure for `yf_shop_decoration_album`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_decoration_album`;
CREATE TABLE `yf_shop_decoration_album` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片编号',
  `image_name` varchar(50) NOT NULL COMMENT '图片名称',
  `image_origin_name` varchar(50) NOT NULL COMMENT '图片原始名称',
  `image_width` int(10) unsigned NOT NULL COMMENT '图片宽度',
  `image_height` int(10) unsigned NOT NULL COMMENT '图片高度',
  `image_size` int(10) unsigned NOT NULL COMMENT '图片大小',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `upload_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺装修相册表';

-- ----------------------------
--  Table structure for `yf_shop_decoration_block`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_decoration_block`;
CREATE TABLE `yf_shop_decoration_block` (
  `block_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '装修块编号',
  `decoration_id` int(10) unsigned NOT NULL COMMENT '装修编号',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺编号',
  `block_layout` varchar(50) NOT NULL COMMENT '块布局',
  `block_content` text COMMENT '块内容',
  `block_module_type` varchar(50) DEFAULT NULL COMMENT '装修块模块类型',
  `block_full_width` tinyint(3) unsigned DEFAULT NULL COMMENT '是否100%宽度(0-否 1-是)',
  `block_sort` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '块排序',
  PRIMARY KEY (`block_id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8 COMMENT='店铺装修块表';

-- ----------------------------
--  Table structure for `yf_shop_domain`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_domain`;
CREATE TABLE `yf_shop_domain` (
  `shop_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_sub_domain` varchar(100) NOT NULL COMMENT '二级域名',
  `shop_edit_domain` int(10) NOT NULL COMMENT '编辑次数',
  `shop_self_domain` varchar(100) NOT NULL COMMENT '自定义域名',
  PRIMARY KEY (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='二级域名表';

-- ----------------------------
--  Table structure for `yf_shop_entity`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_entity`;
CREATE TABLE `yf_shop_entity` (
  `entity_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '实体店铺id',
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '店铺id',
  `entity_name` char(60) NOT NULL DEFAULT '0' COMMENT '实体店铺名称',
  `lng` varchar(20) NOT NULL DEFAULT '0' COMMENT '经度',
  `lat` varchar(20) NOT NULL DEFAULT '0' COMMENT '纬度',
  `province` varchar(255) NOT NULL DEFAULT '' COMMENT '省份',
  `entity_xxaddr` varchar(255) NOT NULL COMMENT '详细地址',
  `entity_tel` varchar(30) NOT NULL COMMENT '实体店铺联系电话',
  `entity_transit` varchar(255) NOT NULL COMMENT '公交信息',
  `city` varchar(255) NOT NULL COMMENT '市',
  `district` varchar(255) NOT NULL COMMENT '区\r\n',
  `street` varchar(255) NOT NULL COMMENT '街道',
  PRIMARY KEY (`entity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `yf_shop_evaluation`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_evaluation`;
CREATE TABLE `yf_shop_evaluation` (
  `evaluation_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评价id',
  `shop_id` int(10) NOT NULL COMMENT '店铺ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '买家id',
  `order_id` varchar(50) NOT NULL COMMENT '订单ID',
  `evaluation_desccredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '描述相符评分',
  `evaluation_servicecredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '服务态度评分',
  `evaluation_deliverycredit` tinyint(1) unsigned NOT NULL DEFAULT '5' COMMENT '发货速度评分',
  `evaluation_create_time` datetime NOT NULL COMMENT '评价时间',
  PRIMARY KEY (`evaluation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺评分表';

-- ----------------------------
--  Table structure for `yf_shop_express`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_express`;
CREATE TABLE `yf_shop_express` (
  `user_express_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '店铺物流id',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `waybill_tpl_id` int(10) unsigned NOT NULL COMMENT '绑定关系-运单',
  `express_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '快递公司id',
  `user_is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为默认',
  `user_tpl_item` text COMMENT '显示项目--json',
  `user_tpl_top` int(255) NOT NULL DEFAULT '0' COMMENT '运单模板上偏移量，单位为毫米(mm)',
  `user_tpl_left` int(255) NOT NULL DEFAULT '0' COMMENT '运单模板左偏移量，单位为毫米(mm)',
  PRIMARY KEY (`user_express_id`)
) ENGINE=InnoDB AUTO_INCREMENT=228 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='默认物流公司表';

-- ----------------------------
--  Table structure for `yf_shop_extend`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_extend`;
CREATE TABLE `yf_shop_extend` (
  `shop_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='扩展表';

-- ----------------------------
--  Table structure for `yf_shop_goods_cat`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_goods_cat`;
CREATE TABLE `yf_shop_goods_cat` (
  `shop_goods_cat_id` int(10) NOT NULL AUTO_INCREMENT,
  `shop_goods_cat_name` varchar(50) NOT NULL,
  `shop_id` int(10) NOT NULL,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `shop_goods_cat_displayorder` smallint(3) NOT NULL DEFAULT '0',
  `shop_goods_cat_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`shop_goods_cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺商品分类表';

-- ----------------------------
--  Table structure for `yf_shop_grade`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_grade`;
CREATE TABLE `yf_shop_grade` (
  `shop_grade_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '店铺等级id',
  `shop_grade_name` varchar(50) NOT NULL,
  `shop_grade_fee` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '收费标准-收费标准，单：元/年，必须为数字，在会员开通或升级店铺时将显示在前台',
  `shop_grade_desc` varchar(255) NOT NULL COMMENT '申请说明',
  `shop_grade_goods_limit` mediumint(8) NOT NULL DEFAULT '0' COMMENT '可发布商品数 0:无限制',
  `shop_grade_album_limit` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '可上传图片数',
  `shop_grade_template` varchar(50) NOT NULL COMMENT '店铺可选模板',
  `shop_grade_function_id` varchar(50) NOT NULL COMMENT '可用附加功能-function_editor_multimedia',
  `shop_grade_sort` mediumint(8) NOT NULL DEFAULT '0' COMMENT '级别-数值越大表明级别越高',
  PRIMARY KEY (`shop_grade_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='店铺等级表';

-- ----------------------------
--  Table structure for `yf_shop_help`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_help`;
CREATE TABLE `yf_shop_help` (
  `shop_help_id` int(10) NOT NULL,
  `help_sort` tinyint(1) unsigned DEFAULT '255' COMMENT '排序',
  `help_title` varchar(100) NOT NULL COMMENT '标题',
  `help_info` text COMMENT '帮助内容',
  `help_url` varchar(100) DEFAULT '' COMMENT '跳转链接',
  `update_time` date NOT NULL COMMENT '更新时间',
  `page_show` tinyint(1) unsigned DEFAULT '1' COMMENT '页面类型:1为店铺,2为会员,默认为1',
  PRIMARY KEY (`shop_help_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `yf_shop_nav`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_nav`;
CREATE TABLE `yf_shop_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '导航ID',
  `title` varchar(50) NOT NULL COMMENT '导航名称',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '卖家店铺ID',
  `detail` text COMMENT '导航内容',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '导航排序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '导航是否显示',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `url` varchar(255) DEFAULT NULL COMMENT '店铺导航的外链URL',
  `target` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '店铺导航外链是否在新窗口打开：0不开新窗口1开新窗口，默认是0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='卖家店铺导航信息表';

-- ----------------------------
--  Table structure for `yf_shop_points_log`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_points_log`;
CREATE TABLE `yf_shop_points_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned NOT NULL COMMENT '    店铺id             ',
  `shop_name` text NOT NULL COMMENT '店铺名称',
  `points` int(10) unsigned NOT NULL COMMENT '积分',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `desc` varchar(255) NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
--  Table structure for `yf_shop_renewal`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_renewal`;
CREATE TABLE `yf_shop_renewal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(10) unsigned NOT NULL COMMENT '会员id',
  `member_name` varchar(50) NOT NULL COMMENT '会员名称(不用存废弃)',
  `shop_id` int(10) unsigned NOT NULL COMMENT '店铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `shop_grade_id` int(10) unsigned NOT NULL COMMENT '店铺等级id',
  `shop_grade_name` varchar(50) NOT NULL COMMENT '店铺等级名称',
  `shop_grade_fee` decimal(10,2) NOT NULL COMMENT '店铺等级费用',
  `renew_time` int(10) unsigned NOT NULL COMMENT '续费时长',
  `renew_cost` decimal(10,2) NOT NULL COMMENT '续费总费用',
  `create_time` datetime NOT NULL COMMENT '申请时间',
  `start_time` datetime NOT NULL COMMENT '有效期开始时间',
  `end_time` datetime NOT NULL COMMENT '有效期结束时间',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理员id',
  `admin_name` varchar(50) NOT NULL COMMENT '管理员名称',
  `desc` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='续费申请表\r\n';

-- ----------------------------
--  Table structure for `yf_shop_shipping_address`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_shipping_address`;
CREATE TABLE `yf_shop_shipping_address` (
  `shipping_address_id` int(10) NOT NULL AUTO_INCREMENT,
  `shop_id` int(10) unsigned NOT NULL COMMENT '所属店铺',
  `shipping_address_contact` varchar(50) NOT NULL COMMENT '联系人',
  `shipping_address_province_id` int(10) NOT NULL COMMENT '省份ID',
  `shipping_address_city_id` int(10) NOT NULL COMMENT '城市ID',
  `shipping_address_area_id` int(10) NOT NULL COMMENT '区县ID',
  `shipping_address_area` varchar(255) NOT NULL COMMENT '所在地区-字符串组合',
  `shipping_address_address` varchar(255) NOT NULL COMMENT '街道地址-不必重复填写地区',
  `shipping_address_phone` varchar(20) NOT NULL COMMENT '联系电话',
  `shipping_address_company` varchar(30) NOT NULL COMMENT '公司',
  `shipping_address_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认地址',
  `shipping_address_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '时间',
  PRIMARY KEY (`shipping_address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='发货地址表';

-- ----------------------------
--  Table structure for `yf_shop_supplier`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_supplier`;
CREATE TABLE `yf_shop_supplier` (
  `supplier_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '供货商id',
  `shop_id` int(10) NOT NULL COMMENT '店铺id',
  `supplier_name` varchar(50) NOT NULL COMMENT '供货商名称',
  `contacts` varchar(50) NOT NULL COMMENT '联系人',
  `contacts_tel` varchar(12) NOT NULL COMMENT '联系电话',
  `remarks` text NOT NULL COMMENT '备注信息',
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='供货商表\r\n';

-- ----------------------------
--  Table structure for `yf_shop_template`
-- ----------------------------
DROP TABLE IF EXISTS `yf_shop_template`;
CREATE TABLE `yf_shop_template` (
  `shop_temp_name` varchar(100) NOT NULL COMMENT '店铺模板名称  --根据模板名称来找寻对应的文件',
  `shop_style_name` varchar(255) NOT NULL COMMENT '风格名称',
  `shop_temp_img` varchar(255) NOT NULL COMMENT '模板对应的图片',
  PRIMARY KEY (`shop_temp_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺模板表';

-- ----------------------------
--  Table structure for `yf_sub_site`
-- ----------------------------
DROP TABLE IF EXISTS `yf_sub_site`;
CREATE TABLE `yf_sub_site` (
  `sub_site_id` int(4) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sub_site_parent_id` int(11) NOT NULL COMMENT '父id',
  `sub_site_name` varchar(60) NOT NULL DEFAULT '' COMMENT '分站名称',
  `sub_site_domain` varchar(20) NOT NULL DEFAULT '' COMMENT '分站域名前缀',
  `sub_site_district_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '地区id， 逗号分隔',
  `sub_site_logo` varchar(100) NOT NULL COMMENT 'logo',
  `sub_site_copyright` text NOT NULL COMMENT '版权',
  `sub_site_template` varchar(50) NOT NULL COMMENT '模板',
  `sub_site_is_open` int(1) NOT NULL DEFAULT '1' COMMENT '是否开启',
  `sub_site_des` text NOT NULL COMMENT '描述',
  `sub_site_web_title` varchar(100) NOT NULL COMMENT 'SEO标题',
  `sub_site_web_keyword` varchar(100) NOT NULL COMMENT 'SEO关键字',
  `sub_site_web_des` varchar(100) NOT NULL COMMENT 'SEO描述',
  PRIMARY KEY (`sub_site_id`),
  KEY `domain` (`sub_site_domain`) COMMENT '(null)'
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='城市分站表';

-- ----------------------------
--  Table structure for `yf_test`
-- ----------------------------
DROP TABLE IF EXISTS `yf_test`;
CREATE TABLE `yf_test` (
  `test_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '数组下标',
  `test_name` varchar(255) NOT NULL COMMENT '数组值',
  `test_sax` varchar(255) NOT NULL,
  PRIMARY KEY (`test_id`),
  KEY `index` (`test_id`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=23346 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='网站配置表';

-- ----------------------------
--  Table structure for `yf_transport_item`
-- ----------------------------
DROP TABLE IF EXISTS `yf_transport_item`;
CREATE TABLE `yf_transport_item` (
  `transport_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `transport_type_id` mediumint(8) unsigned NOT NULL COMMENT '自定义物流模板ID',
  `logistics_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'EMS,平邮,快递-忽略类型，买家不是必须知道，而且可选会给卖家制造障碍。',
  `transport_item_default_num` float(3,1) NOT NULL COMMENT '默认数量',
  `transport_item_default_price` decimal(6,2) NOT NULL COMMENT '默认运费',
  `transport_item_add_num` float(3,1) NOT NULL DEFAULT '1.0' COMMENT '增加数量',
  `transport_item_add_price` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '增加运费',
  `transport_item_city` text NOT NULL COMMENT '区域城市id-需要特别处理，快速查询- 如果全国，则需要使用*来替代，提升效率',
  PRIMARY KEY (`transport_item_id`),
  KEY `temp_id` (`transport_type_id`,`logistics_type`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='自定义物流模板内容表-只处理区域及运费。';

-- ----------------------------
--  Table structure for `yf_transport_offpay_area`
-- ----------------------------
DROP TABLE IF EXISTS `yf_transport_offpay_area`;
CREATE TABLE `yf_transport_offpay_area` (
  `offpay_area_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `offpay_area_city_ids` text NOT NULL COMMENT '区域城市id-需要特别处理，快速查询-'',''分割',
  PRIMARY KEY (`offpay_area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=523 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='到付区域。';

-- ----------------------------
--  Table structure for `yf_transport_type`
-- ----------------------------
DROP TABLE IF EXISTS `yf_transport_type`;
CREATE TABLE `yf_transport_type` (
  `transport_type_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '物流及售卖区域id',
  `transport_type_name` varchar(20) NOT NULL DEFAULT '' COMMENT '物流及售卖区域模板名',
  `shop_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `transport_type_pricing_method` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1按重量  2按件数    3按体积   计算价格方式-不使用',
  `transport_type_time` datetime NOT NULL COMMENT '最后编辑时间',
  `transport_type_price` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '启用固定价格后起作用',
  PRIMARY KEY (`transport_type_id`),
  KEY `user_id` (`shop_id`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='自定义物流运费及售卖区域类型表';

-- ----------------------------
--  Table structure for `yf_upload_album`
-- ----------------------------
DROP TABLE IF EXISTS `yf_upload_album`;
CREATE TABLE `yf_upload_album` (
  `album_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品图片Id',
  `album_name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品图片地址',
  `album_cover` varchar(100) NOT NULL DEFAULT '' COMMENT '封面',
  `album_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `album_num` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '内容数量',
  `album_is_default` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '默认相册，1是，0否',
  `album_displayorder` smallint(4) NOT NULL DEFAULT '255' COMMENT '排序',
  `album_type` enum('video','other','image') NOT NULL DEFAULT 'image' COMMENT '附件册',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属用户id',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户相册表';

-- ----------------------------
--  Table structure for `yf_upload_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_upload_base`;
CREATE TABLE `yf_upload_base` (
  `upload_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品图片Id',
  `album_id` bigint(20) NOT NULL,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `shop_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '店铺id',
  `upload_url_prefix` varchar(255) NOT NULL DEFAULT '',
  `upload_path` varchar(255) NOT NULL DEFAULT '',
  `upload_url` varchar(255) NOT NULL COMMENT '附件的url   upload_url = upload_url_prefix  + upload_path',
  `upload_thumbs` text NOT NULL COMMENT 'JSON存储其它尺寸',
  `upload_original` varchar(255) NOT NULL DEFAULT '' COMMENT '原附件',
  `upload_source` varchar(255) NOT NULL DEFAULT '' COMMENT '源头-网站抓取',
  `upload_displayorder` tinyint(4) NOT NULL DEFAULT '50' COMMENT '排序',
  `upload_type` enum('video','other','image') NOT NULL DEFAULT 'image' COMMENT 'image|video|',
  `upload_image_spec` int(10) NOT NULL DEFAULT '0' COMMENT '规格',
  `upload_size` int(10) NOT NULL COMMENT '原文件大小',
  `upload_mime_type` varchar(100) NOT NULL DEFAULT '' COMMENT '上传的附件类型',
  `upload_metadata` text NOT NULL,
  `upload_name` text NOT NULL COMMENT '附件标题',
  `upload_time` int(10) NOT NULL COMMENT '附件日期',
  PRIMARY KEY (`upload_id`),
  KEY `album_id` (`user_id`,`album_id`,`upload_type`)
) ENGINE=InnoDB AUTO_INCREMENT=840 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户附件表-图片、视频';

-- ----------------------------
--  Table structure for `yf_user_address`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_address`;
CREATE TABLE `yf_user_address` (
  `user_address_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '所属店铺',
  `user_address_contact` varchar(50) NOT NULL,
  `user_address_province_id` int(10) NOT NULL,
  `user_address_city_id` int(10) NOT NULL,
  `user_address_area_id` int(10) NOT NULL,
  `user_address_area` varchar(255) NOT NULL COMMENT '所在地区-字符串组合',
  `user_address_address` varchar(255) NOT NULL COMMENT '街道地址-不必重复填写地区',
  `user_address_phone` varchar(20) NOT NULL COMMENT '联系电话',
  `user_address_company` varchar(30) NOT NULL COMMENT '公司',
  `user_address_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认地址0不是1是',
  `user_address_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=304 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户地址表';

-- ----------------------------
--  Table structure for `yf_user_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_base`;
CREATE TABLE `yf_user_base` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_number` varchar(50) NOT NULL DEFAULT '' COMMENT '用户编号',
  `user_account` varchar(50) NOT NULL DEFAULT '' COMMENT '用户帐号',
  `user_passwd` char(50) NOT NULL DEFAULT '' COMMENT '密码：使用用户中心-此处废弃',
  `user_key` char(32) NOT NULL DEFAULT '' COMMENT '用户Key',
  `user_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否被封禁，0：未封禁，1：封禁',
  `user_login_times` mediumint(8) unsigned NOT NULL DEFAULT '1' COMMENT '登录次数',
  `user_login_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后登录时间',
  `user_login_ip` varchar(30) NOT NULL COMMENT '最后登录ip',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_account` (`user_account`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=123213214 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户基础信息表';

-- ----------------------------
--  Table structure for `yf_user_buy`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_buy`;
CREATE TABLE `yf_user_buy` (
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `common_id` int(10) NOT NULL COMMENT '商品commonid',
  `buy_num` int(10) DEFAULT '0' COMMENT '用户购买数量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户购买商品数量表';

-- ----------------------------
--  Table structure for `yf_user_extend`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_extend`;
CREATE TABLE `yf_user_extend` (
  `user_meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Meta id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_meta_key` varchar(255) NOT NULL COMMENT '键',
  `user_meta_value` longtext NOT NULL COMMENT '值',
  `user_meta_datatype` enum('string','json','number') NOT NULL DEFAULT 'string' COMMENT '数据类型',
  PRIMARY KEY (`user_meta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`user_meta_key`),
  CONSTRAINT `yf_user_extend_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `shop_user_base` (`user_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户数据扩展表';

-- ----------------------------
--  Table structure for `yf_user_favorites_brand`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_favorites_brand`;
CREATE TABLE `yf_user_favorites_brand` (
  `favorites_brand_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `brand_id` int(10) NOT NULL COMMENT '品牌id',
  `favorites_brand_time` datetime NOT NULL COMMENT '收藏时间',
  PRIMARY KEY (`favorites_brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='收藏品牌';

-- ----------------------------
--  Table structure for `yf_user_favorites_goods`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_favorites_goods`;
CREATE TABLE `yf_user_favorites_goods` (
  `favorites_goods_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `favorites_goods_time` datetime NOT NULL,
  PRIMARY KEY (`favorites_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='收藏的商品';

-- ----------------------------
--  Table structure for `yf_user_favorites_shop`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_favorites_shop`;
CREATE TABLE `yf_user_favorites_shop` (
  `favorites_shop_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `shop_id` int(10) NOT NULL COMMENT '店铺id',
  `shop_name` varchar(50) NOT NULL,
  `shop_logo` varchar(255) NOT NULL,
  `favorites_shop_time` datetime NOT NULL,
  PRIMARY KEY (`favorites_shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='收藏的店铺';

-- ----------------------------
--  Table structure for `yf_user_footprint`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_footprint`;
CREATE TABLE `yf_user_footprint` (
  `footprint_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `common_id` int(10) NOT NULL COMMENT '商品id',
  `footprint_time` date NOT NULL COMMENT '时间',
  PRIMARY KEY (`footprint_id`),
  KEY `user_id` (`user_id`,`common_id`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品访问足迹表';

-- ----------------------------
--  Table structure for `yf_user_friend`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_friend`;
CREATE TABLE `yf_user_friend` (
  `user_friend_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NOT NULL COMMENT '会员ID',
  `friend_id` int(10) NOT NULL COMMENT '朋友id = user_id',
  `friend_name` varchar(100) NOT NULL COMMENT '好友会员名称 = user_name',
  `friend_image` varchar(100) NOT NULL COMMENT '朋友头像',
  `friend_addtime` datetime NOT NULL COMMENT '添加时间',
  `friend_state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '关注状态 1为单方关注 2为双方关注--暂时不用',
  PRIMARY KEY (`user_friend_id`),
  KEY `user_id` (`user_id`),
  KEY `friend_id` (`friend_id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8 COMMENT='好友表';

-- ----------------------------
--  Table structure for `yf_user_grade`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_grade`;
CREATE TABLE `yf_user_grade` (
  `user_grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_grade_name` varchar(50) NOT NULL,
  `user_grade_demand` int(10) NOT NULL DEFAULT '0' COMMENT '条件',
  `user_grade_treatment` text NOT NULL COMMENT '权益',
  `user_grade_blogo` varchar(255) NOT NULL COMMENT '大图',
  `user_grade_logo` varchar(255) NOT NULL COMMENT 'LOGO',
  `user_grade_valid` int(1) NOT NULL DEFAULT '0' COMMENT '有效期',
  `user_grade_sum` int(11) NOT NULL DEFAULT '0' COMMENT '年费',
  `user_grade_rate` float(3,1) NOT NULL DEFAULT '0.0' COMMENT '折扣率',
  `user_grade_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`user_grade_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户等级表';

-- ----------------------------
--  Table structure for `yf_user_info`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_info`;
CREATE TABLE `yf_user_info` (
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `user_realname` varchar(30) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `user_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `user_email` varchar(50) NOT NULL DEFAULT '' COMMENT '用户Email',
  `user_type_id` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '用户类别',
  `user_level_id` smallint(4) unsigned NOT NULL DEFAULT '1' COMMENT '用户安全等级',
  `user_active_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '激活时间',
  `user_remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注消息',
  `user_name` varchar(30) NOT NULL COMMENT '用户名',
  `user_sex` tinyint(1) NOT NULL DEFAULT '0',
  `user_birthday` date NOT NULL,
  `user_mobile_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '手机验证0没验证1验证',
  `user_email_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '邮箱验证0没验证1验证',
  `user_cash` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '资金-废除',
  `user_freeze_cash` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结资金-废除',
  `user_provinceid` int(11) NOT NULL,
  `user_cityid` int(11) NOT NULL,
  `user_areaid` int(11) NOT NULL,
  `user_area` varchar(255) NOT NULL,
  `user_logo` varchar(120) NOT NULL DEFAULT '',
  `user_hobby` varchar(255) NOT NULL DEFAULT '0' COMMENT '--废除',
  `user_points` int(10) NOT NULL DEFAULT '0' COMMENT '-废除',
  `user_freeze_points` int(10) NOT NULL DEFAULT '0' COMMENT '-废除',
  `user_growth` int(10) NOT NULL DEFAULT '0' COMMENT '成长值-废除',
  `user_statu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '登录状态0允许登录1禁止登录',
  `user_ip` varchar(10) NOT NULL,
  `user_lastip` varchar(10) NOT NULL,
  `user_regtime` datetime NOT NULL,
  `user_logintime` datetime NOT NULL,
  `lastlogintime` datetime NOT NULL,
  `user_invite` varchar(50) NOT NULL,
  `user_grade` tinyint(2) NOT NULL DEFAULT '1' COMMENT '用户等级',
  `user_update_date` datetime NOT NULL COMMENT '更新时间',
  `user_drp_id` int(10) NOT NULL DEFAULT '0',
  `user_qq` varchar(50) NOT NULL DEFAULT '' COMMENT '用户qq',
  `user_report` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可以举报商品0不可以1可以',
  `user_buy` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可以购买商品0不可以1可以',
  `user_talk` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许发表言论0不可以1可以',
  `user_ww` varchar(50) NOT NULL DEFAULT '' COMMENT '阿里旺旺',
  `user_am` varchar(500) NOT NULL COMMENT '公告id',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户信息表';

-- ----------------------------
--  Table structure for `yf_user_message`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_message`;
CREATE TABLE `yf_user_message` (
  `user_message_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户消息id',
  `user_message_receive_id` int(10) NOT NULL COMMENT '接收id',
  `user_message_receive` varchar(50) NOT NULL COMMENT '接收者用户',
  `user_message_send_id` int(10) NOT NULL COMMENT '发送者id',
  `user_message_send` varchar(50) NOT NULL COMMENT '发送者',
  `user_message_content` text NOT NULL COMMENT '发送内容',
  `message_islook` tinyint(1) DEFAULT '0' COMMENT '是否读取0未1读取',
  `user_message_pid` int(10) NOT NULL COMMENT '回复消息上级id',
  `user_message_time` datetime NOT NULL COMMENT '发送时间',
  PRIMARY KEY (`user_message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COMMENT='用户消息表';

-- ----------------------------
--  Table structure for `yf_user_privacy`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_privacy`;
CREATE TABLE `yf_user_privacy` (
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `user_privacy_email` tinyint(1) NOT NULL DEFAULT '0' COMMENT '邮箱设置0公开1好友可见2保密',
  `user_privacy_realname` tinyint(1) NOT NULL DEFAULT '0' COMMENT '真实姓名设置0公开1好友可见2保密',
  `user_privacy_sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别设置0公开1好友可见2保密',
  `user_privacy_birthday` tinyint(1) NOT NULL DEFAULT '0' COMMENT '生日设置0公开1好友可见2保密',
  `user_privacy_area` tinyint(1) NOT NULL DEFAULT '0' COMMENT '所在地区设置0公开1好友可见2保密',
  `user_privacy_qq` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'QQ设置0公开1好友可见2保密',
  `user_privacy_ww` tinyint(1) NOT NULL DEFAULT '0' COMMENT '阿里旺旺设置0公开1好友可见2保密',
  `user_privacy_mobile` tinyint(1) NOT NULL DEFAULT '0' COMMENT '手机设置0公开1好友可见2保密',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户信息隐私设置表';

-- ----------------------------
--  Table structure for `yf_user_resource`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_resource`;
CREATE TABLE `yf_user_resource` (
  `user_id` int(10) unsigned NOT NULL,
  `user_blog` int(10) NOT NULL DEFAULT '0' COMMENT '微博数量',
  `user_friend` int(10) NOT NULL DEFAULT '0' COMMENT '好友数量',
  `user_fan` int(10) NOT NULL DEFAULT '0' COMMENT '粉丝数量',
  `user_growth` int(10) NOT NULL DEFAULT '0' COMMENT '成长值',
  `user_points` int(10) NOT NULL DEFAULT '0' COMMENT '积点',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户信息表';

-- ----------------------------
--  Table structure for `yf_user_tag`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_tag`;
CREATE TABLE `yf_user_tag` (
  `user_tag_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户标签id',
  `user_tag_sort` int(10) NOT NULL COMMENT '标签排序',
  `user_tag_name` varchar(50) NOT NULL COMMENT '标签名称',
  `user_tag_image` varchar(255) NOT NULL COMMENT '标签图片',
  `user_tag_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐0不推荐1推荐',
  `user_tag_content` text NOT NULL COMMENT '标签描述',
  `user_tag_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`user_tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户标签表';

-- ----------------------------
--  Table structure for `yf_user_tag_rec`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_tag_rec`;
CREATE TABLE `yf_user_tag_rec` (
  `tag_rec_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '兴趣标签id',
  `user_tag_id` int(10) NOT NULL COMMENT '标签id',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `tag_rec_time` datetime NOT NULL COMMENT '选择标签时间',
  PRIMARY KEY (`tag_rec_id`)
) ENGINE=InnoDB AUTO_INCREMENT=280 DEFAULT CHARSET=utf8 COMMENT='用户兴趣标签表';

-- ----------------------------
--  Table structure for `yf_user_type`
-- ----------------------------
DROP TABLE IF EXISTS `yf_user_type`;
CREATE TABLE `yf_user_type` (
  `user_type_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '客户类别Id',
  `user_type_name` varchar(20) NOT NULL DEFAULT '' COMMENT '客户类别名称',
  `user_type_remark` varchar(50) NOT NULL DEFAULT '' COMMENT '客户类别注释',
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户分类表';

-- ----------------------------
--  Table structure for `yf_voucher_base`
-- ----------------------------
DROP TABLE IF EXISTS `yf_voucher_base`;
CREATE TABLE `yf_voucher_base` (
  `voucher_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券编号',
  `voucher_code` varchar(32) NOT NULL COMMENT '代金券编码',
  `voucher_t_id` int(11) NOT NULL COMMENT '代金券模版编号',
  `voucher_title` varchar(50) NOT NULL COMMENT '代金券标题',
  `voucher_desc` varchar(255) NOT NULL COMMENT '代金券描述',
  `voucher_start_date` datetime NOT NULL COMMENT '代金券有效期开始时间',
  `voucher_end_date` datetime NOT NULL COMMENT '代金券有效期结束时间',
  `voucher_price` int(11) NOT NULL COMMENT '代金券面额',
  `voucher_limit` decimal(10,2) NOT NULL COMMENT '代金券使用时的订单限额',
  `voucher_shop_id` int(11) NOT NULL COMMENT '代金券的店铺id',
  `voucher_state` tinyint(4) NOT NULL COMMENT '代金券状态(1-未用,2-已用,3-过期,4-收回)',
  `voucher_active_date` datetime NOT NULL COMMENT '代金券发放日期',
  `voucher_type` tinyint(4) NOT NULL COMMENT '代金券类别',
  `voucher_owner_id` int(11) NOT NULL COMMENT '代金券所有者id',
  `voucher_owner_name` varchar(50) NOT NULL COMMENT '代金券所有者名称',
  `voucher_order_id` varchar(25) NOT NULL COMMENT '使用该代金券的订单编号',
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=225 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='代金券表';

-- ----------------------------
--  Table structure for `yf_voucher_combo`
-- ----------------------------
DROP TABLE IF EXISTS `yf_voucher_combo`;
CREATE TABLE `yf_voucher_combo` (
  `combo_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '套餐编号',
  `user_id` int(10) NOT NULL COMMENT '会员编号',
  `user_nickname` varchar(100) NOT NULL COMMENT '会员名称',
  `shop_id` int(10) NOT NULL COMMENT '店铺编号',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `combo_start_time` datetime NOT NULL COMMENT '开始时间',
  `combo_end_time` datetime NOT NULL COMMENT '结束时间',
  PRIMARY KEY (`combo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='代金券套餐表';

-- ----------------------------
--  Table structure for `yf_voucher_price`
-- ----------------------------
DROP TABLE IF EXISTS `yf_voucher_price`;
CREATE TABLE `yf_voucher_price` (
  `voucher_price_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '代金券面值编号',
  `voucher_price` int(11) NOT NULL COMMENT '代金券面值',
  `voucher_price_describe` varchar(255) NOT NULL COMMENT '代金券描述',
  `voucher_defaultpoints` int(11) DEFAULT '0' COMMENT '代金券默认的兑换所需积分，可以为0',
  PRIMARY KEY (`voucher_price_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='代金券面额表';

-- ----------------------------
--  Table structure for `yf_voucher_template`
-- ----------------------------
DROP TABLE IF EXISTS `yf_voucher_template`;
CREATE TABLE `yf_voucher_template` (
  `voucher_t_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '代金券模版编号',
  `voucher_t_title` varchar(50) NOT NULL COMMENT '代金券模版名称',
  `voucher_t_desc` varchar(255) NOT NULL COMMENT '代金券模版描述',
  `shop_class_id` int(10) NOT NULL,
  `voucher_t_start_date` datetime NOT NULL COMMENT '代金券模版有效期开始时间',
  `voucher_t_end_date` datetime NOT NULL COMMENT '代金券模版有效期结束时间',
  `voucher_t_price` int(10) NOT NULL COMMENT '代金券模版面额',
  `voucher_t_limit` decimal(10,2) NOT NULL COMMENT '代金券使用时的订单限额',
  `shop_id` int(10) NOT NULL COMMENT '代金券模版的店铺id',
  `shop_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `voucher_t_creator_id` int(10) NOT NULL COMMENT '代金券模版的创建者id',
  `voucher_t_state` tinyint(4) NOT NULL DEFAULT '1' COMMENT '代金券模版状态(1-有效,2-失效)',
  `voucher_t_total` int(10) NOT NULL COMMENT '模版可发放的代金券总数',
  `voucher_t_giveout` int(10) NOT NULL COMMENT '模版已发放的代金券数量',
  `voucher_t_used` int(10) NOT NULL COMMENT '模版已经使用过的代金券',
  `voucher_t_add_date` datetime NOT NULL COMMENT '模版的创建时间',
  `voucher_t_update_date` datetime NOT NULL COMMENT '模版的最后修改时间',
  `combo_id` int(10) NOT NULL COMMENT '套餐编号',
  `voucher_t_points` int(10) NOT NULL DEFAULT '0' COMMENT '兑换所需积分',
  `voucher_t_eachlimit` int(10) NOT NULL DEFAULT '1' COMMENT '每人限领张数',
  `voucher_t_styleimg` varchar(200) NOT NULL COMMENT '样式模版图片',
  `voucher_t_customimg` varchar(200) NOT NULL COMMENT '自定义代金券模板图片',
  `voucher_t_access_method` tinyint(1) NOT NULL DEFAULT '1' COMMENT '代金券领取方式，1-积分兑换(默认)，2-卡密兑换，3-免费领取',
  `voucher_t_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '推荐状态，0-为不推荐，1-推荐',
  `voucher_t_user_grade_limit` tinyint(4) NOT NULL DEFAULT '1' COMMENT '领取代金券的用户等级限制',
  PRIMARY KEY (`voucher_t_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='代金券模版表';

-- ----------------------------
--  Table structure for `yf_waybill_tpl`
-- ----------------------------
DROP TABLE IF EXISTS `yf_waybill_tpl`;
CREATE TABLE `yf_waybill_tpl` (
  `waybill_tpl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `waybill_tpl_name` varchar(20) NOT NULL COMMENT '模板名称',
  `user_id` int(10) unsigned NOT NULL COMMENT '所属用户',
  `shop_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属店铺id',
  `express_id` mediumint(8) NOT NULL COMMENT '物流公司id',
  `waybill_tpl_width` int(11) NOT NULL DEFAULT '0' COMMENT '运单宽度，单位为毫米(mm)',
  `waybill_tpl_height` int(11) NOT NULL DEFAULT '0' COMMENT '运单高度，单位为毫米(mm)',
  `waybill_tpl_top` int(255) NOT NULL DEFAULT '0' COMMENT '运单模板上偏移量，单位为毫米(mm)',
  `waybill_tpl_left` int(255) NOT NULL DEFAULT '0' COMMENT '运单模板左偏移量，单位为毫米(mm)',
  `waybill_tpl_image` varchar(200) NOT NULL DEFAULT '' COMMENT '模板图片-请上传扫描好的运单图片，图片尺寸必须与快递单实际尺寸相符',
  `waybill_tpl_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用0否1是',
  `waybill_tpl_build-in` tinyint(1) NOT NULL DEFAULT '1' COMMENT '系统内置0否1是',
  `waybill_tpl_item` text COMMENT '显示项目--json',
  PRIMARY KEY (`waybill_tpl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='运单模板';

-- ----------------------------
--  Table structure for `yf_web_config`
-- ----------------------------
DROP TABLE IF EXISTS `yf_web_config`;
CREATE TABLE `yf_web_config` (
  `config_key` varchar(50) NOT NULL COMMENT '数组下标',
  `config_value` text NOT NULL COMMENT '数组值',
  `config_type` varchar(50) NOT NULL,
  `config_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态值，1可能，0不可用',
  `config_comment` text NOT NULL,
  `config_datatype` enum('string','json','number') NOT NULL DEFAULT 'string' COMMENT '数据类型',
  PRIMARY KEY (`config_key`),
  KEY `index` (`config_key`,`config_type`) COMMENT '(null)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='网站配置表';
