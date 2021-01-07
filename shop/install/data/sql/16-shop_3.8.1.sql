-- 门店推荐的商品ID --
ALTER TABLE `yf_chain_base`
ADD COLUMN `recommend_goods`  varchar(255) NULL COMMENT '门店推荐的商品goods_id';

-- 门店所在经度纬度 --
ALTER TABLE `yf_chain_base`
ADD COLUMN `longitude`  varchar(255) NOT NULL DEFAULT 0 COMMENT '经度' AFTER `recommend_goods`,
ADD COLUMN `latitude`  varchar(255) NOT NULL DEFAULT 0 COMMENT '纬度' AFTER `longitude`;



DROP TABLE IF EXISTS `yf_ylfenzhangflowers`;
CREATE TABLE `yf_ylfenzhangflowers`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id号',
  `order_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `uorders` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '合并订单号',
  `vepayshopnumer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商户号',
  `verealcash` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商户提现金额',
  `veyfeecash` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '平台佣金',
  `mchntNo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '平台商户号',
  `createtime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '创建时间',
  `status` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '状态，1待处理2提现成功3天失败4提现中',
  `actdo` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '计划任务处理 1 待处理 2 已经处理过',
  `orderNo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单号',
  `cleardate` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '清算日期（支付通知中有）',
  `banktrace` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '银联交易参考号 （支付通知中有）',
  `paymentType` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付方式',
  `appId` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '结算编号',
  `vedevicetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '提现完成异通知地址',
  `transType` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '固定值  singe ',
  `veflowids` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '银联队长的单号信息 ',
  `vemsg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '提现返回的消息 ',
  `type` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '类型 1 商家  2 平台 ',
  `returstatus` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '是否退款 1 正常 2 退款处理了 ',
  `returcash` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '退款的金额',
  `platemisdo` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '平台提现计划任务是否处理 1 待处理 2 已经处理',
  `veischecks` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '计划任务查询状态 1 待处理 2 已经处理',
  `returntranstype` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '接口返回的提现方式',
  `payType` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '银联支付的支付方式',
  `Access_mode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '银联支付的支付模式，开发传过去的',
  `pay_yunshapc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '银联支付的支付模式，开发传过去的',
  `cbpayshopnumer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'C扫B支付商户号',
  `xcxpayshopnumer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '小程序支付商户号',
  `apayType` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '银联支付的支付方式',
  `apppayshopnumer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'app使用的商户号信息',
  `tixiantime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '接口提现时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '银联分账流水信息' ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `yf_veshoppay`;
CREATE TABLE `yf_veshoppay`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '买家id',
  `shop_id` int(10) NOT NULL COMMENT '店铺id',
  `payshopname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商户名称',
  `payshopnumer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商户号',
  `payshopcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '商户ID',
  `paytermnumber` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '终端号',
  `status` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '状态 1 开始  2 关闭',
  `payscale` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '商户的分佣比例',
  `cbpayshopnumer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT ' C扫B支付商户号',
  `xcxpayshopnumer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '小程序支付商户号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 381 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '银联商户号信息' ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `yf_veaccount_checking`;
CREATE TABLE `yf_veaccount_checking` (
  `account_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `tracedate` varchar(8) NOT NULL COMMENT '支付交易日期,格式为YYYYMMDD',
  `tracetime` varchar(14) NOT NULL COMMENT 'pos支付业务发生的时间格式：yyyyMMddhhmmss',
  `orderno` varchar(25) NOT NULL COMMENT '运单号',
  `ordertype` varchar(20) NOT NULL COMMENT '运单类型',
  `txnamt` varchar(10) NOT NULL COMMENT '交易金额',
  `cod` varchar(10) NOT NULL COMMENT '货款',
  `fee` varchar(10) NOT NULL COMMENT '运费',
  `payway` varchar(20) NOT NULL COMMENT '支付方式',
  `settledate` varchar(8) NOT NULL COMMENT '清算日期',
  `settleamount` varchar(10) NOT NULL COMMENT '清算金额',
  `charge` varchar(10) NOT NULL COMMENT '刷卡手续费',
  `cardid` varchar(20) NOT NULL COMMENT '账号/卡号',
  `bankname` varchar(40) NOT NULL COMMENT '发卡行名称',
  `settletermid` varchar(10) NOT NULL COMMENT '清算终端号',
  `termid` varchar(10) NOT NULL COMMENT '受理终端号',
  `postrace` varchar(10) NOT NULL COMMENT '凭证号',
  `banktrace` varchar(10) NOT NULL COMMENT '检索参考号',
  `txntype` varchar(20) NOT NULL COMMENT '交易类型',
  `codmername` varchar(40) NOT NULL COMMENT '物流商户名称',
  `codmercode` varchar(20) NOT NULL COMMENT '物流商户名号',
  `cardtype` varchar(2) NOT NULL COMMENT '卡类型',
  `status` varchar(2) NOT NULL DEFAULT 0 COMMENT '电商端是否已经可清算',
  `comfirmtime` varchar(20) NOT NULL DEFAULT 0 COMMENT '确定收货时间',
  `billtime` varchar(20) NOT NULL DEFAULT 0 COMMENT '结算时间',
  PRIMARY KEY (`account_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '银联商户对账单' ROW_FORMAT = Compact;

ALTER TABLE `yf_shop_class_bind`
MODIFY COLUMN `product_class_id`  varchar(100) NOT NULL DEFAULT '' COMMENT '商品分类id' AFTER `shop_id`;
ALTER TABLE `yf_shop_class_bind`
MODIFY COLUMN `commission_rate`  varchar(100) NOT NULL DEFAULT 0.00 COMMENT '百分比' AFTER `product_class_id`;


ALTER TABLE `yf_base_district`
ADD COLUMN `district_first_py`  varchar(10) NULL COMMENT '地区首字母拼音' AFTER `district_is_level`;

UPDATE `yf_base_district` SET `district_id`='1', `district_name`='北京', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='B' WHERE (`district_id`='1');
UPDATE `yf_base_district` SET `district_id`='2', `district_name`='天津', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='T' WHERE (`district_id`='2');
UPDATE `yf_base_district` SET `district_id`='3', `district_name`='河北', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='H' WHERE (`district_id`='3');
UPDATE `yf_base_district` SET `district_id`='4', `district_name`='山西', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='S' WHERE (`district_id`='4');

UPDATE `yf_base_district` SET `district_id`='5', `district_name`='内蒙古', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='N' WHERE (`district_id`='5');
UPDATE `yf_base_district` SET `district_id`='6', `district_name`='辽宁', `district_parent_id`='0', `district_displayorder`='0', `district_region`='东北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='L' WHERE (`district_id`='6');
UPDATE `yf_base_district` SET `district_id`='7', `district_name`='吉林', `district_parent_id`='0', `district_displayorder`='0', `district_region`='东北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='J' WHERE (`district_id`='7');
UPDATE `yf_base_district` SET `district_id`='8', `district_name`='黑龙江', `district_parent_id`='0', `district_displayorder`='0', `district_region`='东北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='H' WHERE (`district_id`='8');
UPDATE `yf_base_district` SET `district_id`='9', `district_name`='上海', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华东', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='S' WHERE (`district_id`='9');
UPDATE `yf_base_district` SET `district_id`='10', `district_name`='江苏', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华东', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='J' WHERE (`district_id`='10');
UPDATE `yf_base_district` SET `district_id`='11', `district_name`='浙江', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华东', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='Z' WHERE (`district_id`='11');
UPDATE `yf_base_district` SET `district_id`='12', `district_name`='安徽', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华东', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='A' WHERE (`district_id`='12');
UPDATE `yf_base_district` SET `district_id`='13', `district_name`='福建', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='F' WHERE (`district_id`='13');
UPDATE `yf_base_district` SET `district_id`='14', `district_name`='江西', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华东', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='J' WHERE (`district_id`='14');

UPDATE `yf_base_district` SET `district_id`='15', `district_name`='山东', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华东', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='S' WHERE (`district_id`='15');
UPDATE `yf_base_district` SET `district_id`='16', `district_name`='河南', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华中', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='H' WHERE (`district_id`='16');
UPDATE `yf_base_district` SET `district_id`='17', `district_name`='湖北', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华中', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='H' WHERE (`district_id`='17');
UPDATE `yf_base_district` SET `district_id`='18', `district_name`='湖南', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华中', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='H' WHERE (`district_id`='18');
UPDATE `yf_base_district` SET `district_id`='19', `district_name`='广东', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='G' WHERE (`district_id`='19');
UPDATE `yf_base_district` SET `district_id`='20', `district_name`='广西', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='G' WHERE (`district_id`='20');
UPDATE `yf_base_district` SET `district_id`='21', `district_name`='海南', `district_parent_id`='0', `district_displayorder`='0', `district_region`='华南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='H' WHERE (`district_id`='21');
UPDATE `yf_base_district` SET `district_id`='22', `district_name`='重庆', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='C' WHERE (`district_id`='22');
UPDATE `yf_base_district` SET `district_id`='23', `district_name`='四川', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='X' WHERE (`district_id`='23');
UPDATE `yf_base_district` SET `district_id`='24', `district_name`='贵州', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='G' WHERE (`district_id`='24');
UPDATE `yf_base_district` SET `district_id`='25', `district_name`='云南', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='Y' WHERE (`district_id`='25');
UPDATE `yf_base_district` SET `district_id`='26', `district_name`='西藏', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西南', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='X' WHERE (`district_id`='26');
UPDATE `yf_base_district` SET `district_id`='27', `district_name`='陕西', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='X' WHERE (`district_id`='27');
UPDATE `yf_base_district` SET `district_id`='28', `district_name`='甘肃', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='G' WHERE (`district_id`='28');
UPDATE `yf_base_district` SET `district_id`='29', `district_name`='青海', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='Q' WHERE (`district_id`='29');
UPDATE `yf_base_district` SET `district_id`='30', `district_name`='宁夏', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='N' WHERE (`district_id`='30');
UPDATE `yf_base_district` SET `district_id`='31', `district_name`='新疆', `district_parent_id`='0', `district_displayorder`='0', `district_region`='西北', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='X' WHERE (`district_id`='31');
UPDATE `yf_base_district` SET `district_id`='32', `district_name`='台湾', `district_parent_id`='0', `district_displayorder`='0', `district_region`='港澳台', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='T' WHERE (`district_id`='32');
UPDATE `yf_base_district` SET `district_id`='33', `district_name`='香港', `district_parent_id`='0', `district_displayorder`='0', `district_region`='港澳台', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='X' WHERE (`district_id`='33');
UPDATE `yf_base_district` SET `district_id`='34', `district_name`='澳门', `district_parent_id`='0', `district_displayorder`='0', `district_region`='港澳台', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='A' WHERE (`district_id`='34');
UPDATE `yf_base_district` SET `district_id`='35', `district_name`='海外', `district_parent_id`='0', `district_displayorder`='0', `district_region`='海外', `district_is_leaf`='1', `district_is_level`='0', `district_first_py`='H' WHERE (`district_id`='35');


ALTER TABLE `yf_goods_common` ADD COLUMN `contract_type_id` varchar(100)  NOT NULL COMMENT '消费者权利保障';
ALTER TABLE `yf_goods_base` ADD COLUMN `contract_type_id` varchar(100)  NOT NULL COMMENT '消费者权利保障';



INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('mall_logo', '', 'mall', '1', '商城logo 手机端 小程序通用', 'string');
INSERT INTO `yf_admin_menu` (`menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('11001', '商城logo', '', '0', 'Config', 'mall','config_type%5B%5D=mall&config_type%5B%5D=mall','<li>填写邮件服务器相关参数，并点击“测试”按钮进行效验，保存后生效。</li><li>设置商城logo 手机端/小程序通用</li>','3','');



ALTER TABLE `yf_order_goods` ADD COLUMN `directseller_p_id` int(10) NOT NULL DEFAULT '0' COMMENT '上上级uid';



INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('19030', '19001', '工业首页模板', '', '0', 'Config', 'industrialIndex', 'config_type%5B%5D=mobile', '<li>该模板为工业模板,点击右侧组件的“添加”按钮，增加对应类型版块到页面，其中“广告条版块”只能添加一个。</li>\n            <li>鼠标触及左侧页面对应版块，出现操作类链接，可以对该区域块进行“移动”、“启用/禁用”、“编辑”、“删除”操作。</li>\n            <li>新增加的版块内容默认为“禁用”状态，编辑内容并“启用”该块后将在手机端即时显示。</li>', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('19031', '19001', '生鲜首页模板', '', '0', 'Config', 'freshIndex', 'config_type%5B%5D=mobile', '<li>该模板为生鲜模板,点击右侧组件的“添加”按钮，增加对应类型版块到页面，其中“广告条版块”只能添加一个。</li>\n            <li>鼠标触及左侧页面对应版块，出现操作类链接，可以对该区域块进行“移动”、“启用/禁用”、“编辑”、“删除”操作。</li>\n            <li>新增加的版块内容默认为“禁用”状态，编辑内容并“启用”该块后将在手机端即时显示。</li>', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('19032', '19001', '首页模板设置', '', '0', 'Config', 'mobileIndexSet', 'config_type%5B%5D=mobileIndex', '<li>选择相应的模板，在手机端即对该模板进行显示。</li>', '0', '0000-00-00 00:00:00');

ALTER TABLE `yf_mb_tpl_layout`
ADD COLUMN `tpl_layout_style`  int(2) NOT NULL DEFAULT 1 COMMENT '1、普通模板样式 2、工业模板样式 3、生鲜模板样式';

--预售--
DROP TABLE IF EXISTS `yf_presale_base`;
CREATE TABLE `yf_presale_base` (
  `presale_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `presale_name` varchar(255) DEFAULT NULL COMMENT '活动名称',
  `presale_start_time` datetime DEFAULT NULL COMMENT '定金开始时间',
  `presale_end_time` datetime DEFAULT NULL COMMENT '定金结束时间',
  `presale_final_time` datetime DEFAULT NULL COMMENT '尾款开始时间',
  `presale_lower_limit` int(10) DEFAULT NULL COMMENT '限购',
  `presale_state` tinyint(1) DEFAULT NULL COMMENT '状态 0待审核 1正常 2已结束 3管理员关闭',
  `combo_id` int(11) DEFAULT NULL,
  `apply_time` datetime DEFAULT NULL COMMENT '申请时间',
  `user_nick_name` varchar(255) DEFAULT NULL COMMENT '昵称',
  `shop_id` int(11) DEFAULT NULL COMMENT '店铺id',
  `user_id` varchar(11) DEFAULT NULL COMMENT '会员id',
  `presale_deposit` decimal(10,0) DEFAULT NULL COMMENT '预售定金',
  `shop_name` varchar(255) DEFAULT NULL,
  `presale_final_time_end` datetime DEFAULT NULL,
  PRIMARY KEY (`presale_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `yf_presale_combo`;
CREATE TABLE `yf_presale_combo` (
  `combo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `user_nickname` varchar(255) DEFAULT NULL,
  `shop_name` varchar(255) DEFAULT NULL,
  `combo_start_time` datetime DEFAULT NULL,
  `combo_end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`combo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `yf_presale_goods`;
CREATE TABLE `yf_presale_goods` (
  `presale_goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `presale_id` int(11) DEFAULT NULL COMMENT '预售id',
  `presale_name` varchar(255) DEFAULT NULL COMMENT '预售名称',
  `goods_id` int(11) DEFAULT NULL,
  `common_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `goods_name` varchar(255) DEFAULT NULL,
  `goods_price` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `presale_price` decimal(10,2) DEFAULT NULL COMMENT '预售价格',
  `goods_image` varchar(255) DEFAULT NULL,
  `goods_start_time` datetime DEFAULT NULL COMMENT '预售定金开始时间',
  `goods_end_time` datetime DEFAULT NULL COMMENT '预售定金结束时间',
  `goods_lower_limit` int(11) DEFAULT NULL COMMENT '限购',
  `presale_goods_state` tinyint(1) DEFAULT NULL COMMENT '状态 0待审核 1正常 2已结束 3管理员关闭',
  `goods_final_time` datetime DEFAULT NULL COMMENT '预售尾款开始时间',
  `presale_deposit` decimal(10,2) DEFAULT NULL COMMENT '预售定金',
  `goods_final_time_end` datetime DEFAULT NULL,
  PRIMARY KEY (`presale_goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

ALTER TABLE `yf_order_base`
ADD COLUMN `is_presale` tinyint(1) DEFAULT NULL COMMENT '是否是预售订单 0不是 1是',
ADD COLUMN `presale_deposit` decimal(10,2) DEFAULT NULL COMMENT '预售定金',
ADD COLUMN `final_price` decimal(10,2) DEFAULT NULL COMMENT '预售尾款',
ADD COLUMN `presale_order_id` varchar(255) DEFAULT NULL COMMENT '预售尾款单号',
ADD COLUMN `presale_final_time` datetime DEFAULT NULL COMMENT '预收尾款开始时间',
ADD COLUMN `final_mobile` varchar(20) DEFAULT NULL COMMENT '尾款手机号',
ADD COLUMN `final_message` tinyint(1) DEFAULT '0' COMMENT '尾款通知 0未通知 1通知';


INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('promotion_presale_price', '', 'presale', '1', '预售活动购买价格', 'number');

INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('40027', '16500', '预售管理', '', '15000', '', '', '', '商品预售促销活动相关设定及管理', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('40028', '40027', '活动列表', '', '15000', 'Promotion_Presale', 'list', '', '<li>商家发布的预售活动列表</li><li>取消操作不可恢复，请慎重操作</li><li>点击详细链接查看活动详细信息</li>', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('40029', '40027', '套餐列表', '', '15000', 'Promotion_Presale', 'comboList', '', '<li>商家的预售套餐列表</li>', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('40030', '40027', '设置', '', '15000', 'Config', 'presale', 'config_type%5B%5D=presale', '<li>预售价格设置</li>', '0', '0000-00-00 00:00:00');

ALTER TABLE `yf_goods_common`
ADD COLUMN `common_is_yushou` tinyint(4) DEFAULT NULL COMMENT '是否是预售';




ALTER TABLE `yf_points_order`
ADD COLUMN `is_del`  int(2) NOT NULL DEFAULT 2 COMMENT '1、已删除 2、未删除';

ALTER TABLE `yf_shop_base`
ADD COLUMN `shop_wap_index`  int(2) NOT NULL DEFAULT 1 COMMENT '1、普通店铺首页模板 2、生鲜模板 3、工业模板';


ALTER TABLE `yf_seckill_base`
ADD COLUMN `is_limit` tinyint(1) DEFAULT NULL COMMENT '是否限购 0不是 1是';

ALTER TABLE `yf_seckill_goods`
ADD COLUMN `is_limit` tinyint(1) DEFAULT NULL COMMENT '是否限购 0不是 1是';

INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('Plugin_OpenSearch', '', 'plugin', '1', '', 'string');

ALTER TABLE `yf_shop_base`
ADD COLUMN `shop_wap_banner`  varchar(255) NOT NULL COMMENT 'wap，小程序端店铺条幅' AFTER `shop_banner`;


INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'class', '', '1', '3', '0', '2');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'home5', '', '1', '4', '0', '2');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'enterance', '', '1', '2', '0', '2');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'home1', '', '1', '1', '0', '2');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'adv_list', '', '1', '0', '0', '2');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'home3', '', '0', '99', '0', '2');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'goods', '', '0', '99', '0', '2');



INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'home1', '', '1', '1', '0', '3');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'enterance', '', '1', '2', '0', '3');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'goods', '', '1', '5', '0', '3');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'newGoods', '', '1', '4', '0', '3');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'adv_list', '', '1', '0', '0', '3');
INSERT INTO `yf_mb_tpl_layout` (`mb_tpl_layout_id`, `mb_tpl_layout_title`, `mb_tpl_layout_type`, `mb_tpl_layout_data`, `mb_tpl_layout_enable`, `mb_tpl_layout_order`, `sub_site_id`, `tpl_layout_style`) VALUES ('', '', 'activityA', '', '1', '3', '0', '3');

