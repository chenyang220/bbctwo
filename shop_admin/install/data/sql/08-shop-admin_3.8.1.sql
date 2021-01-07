INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90004', '20000', '直播管理', '', '0', '', '', '', '直播管理', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90005', '90004', '直播设置', '', '0', 'Config', 'set_live', 'config_type%5B%5D=set_live', '<li>1.购买直播系统后，此直播模块自动集成到小程序商城系统，即可使用此功能模块.</li>
<li>2.直播申请开启，商家需要向平台申请授权后才能使用，如关闭，商家无需向平台申请即可使用.</li>', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90006', '90004', '申请列表', '', '0', 'Live', 'liveList', '', '<li>直播会消耗平台流量费用，故请谨慎向你的商家授权</li>', '50', '0000-00-00 00:00:00');


INSERT INTO `yf_admin_menu`(`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES (16500, 0, '营销', 'icon-silde08', 0, 'Seller_analytics', 'index', '', '', 10, '0000-00-00 00:00:00');
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '促销设定', `menu_icon` = '', `rights_id` = 14300, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '商城对各类型促销活动设定开关', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 17001;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '团购管理', `menu_icon` = '', `rights_id` = 14400, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '商品团购促销活动相关设定及管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 17002;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '加价购', `menu_icon` = '', `rights_id` = 14500, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '加价购活动套餐及列表', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 17003;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '限时折扣', `menu_icon` = '', `rights_id` = 14600, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '店铺商品限时折扣促销活动设置及管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 17004;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '店铺满即送', `menu_icon` = '', `rights_id` = 14700, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '店铺满即送活动相关设定及管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 17005;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '积分兑换', `menu_icon` = '', `rights_id` = 14800, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '商城积分礼品的发布及兑换礼品的管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 17006;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '店铺代金券', `menu_icon` = '', `rights_id` = 14900, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '商城店铺代金券活动设定与管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 17007;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '平台红包', `menu_icon` = '', `rights_id` = 15400, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '平台红包新增与管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 17008;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '拼团管理', `menu_icon` = '', `rights_id` = 15000, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '商品拼团促销活动相关设定及管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 19010;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '砍价管理', `menu_icon` = '', `rights_id` = 19400, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '砍价管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 19024;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16500, `menu_name` = '秒杀管理', `menu_icon` = '', `rights_id` = 15000, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '商品秒杀活动相关设定及管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 40023;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16000, `menu_name` = '服务保障', `menu_icon` = '', `rights_id` = 14200, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '消费者保障服务查看与管理', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 16007;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16000, `menu_name` = '结算管理', `menu_icon` = '', `rights_id` = 13700, `menu_url_ctl` = '', `menu_url_met` = '', `menu_url_parem` = '', `menu_url_note` = '商品订单结算索引及商家账单表', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 16002;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16002, `menu_name` = '实物订单结算', `menu_icon` = '', `rights_id` = 8520, `menu_url_ctl` = 'Operation_Settlement', `menu_url_met` = 'settlement', `menu_url_parem` = '', `menu_url_note` = '<li>账单计算公式：订单金额(含运费) + 红包金额 - 佣金金额 - 退还红包金额 - 退单金额 + 退还佣金 - 店铺消费 - 分销佣金总额订单金额(含运费) + 红包金额 - 佣金金额 - 退还红包金额 - 退单金额 + 退还佣金 - 店铺消费 - 分销佣金总额</li>\n            <li>账单处理流程为：系统出账 > 商家确认 > 平台审核 > 财务支付(完成结算) 4个环节，其中平台审核和财务支付需要平台介入，请予以关注</li>', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 16009;
UPDATE `yf_admin_menu` SET `menu_parent_id` = 16002, `menu_name` = '虚拟订单结算', `menu_icon` = '', `rights_id` = 8540, `menu_url_ctl` = 'Operation_Settlement', `menu_url_met` = 'settlement', `menu_url_parem` = 'otyp=1', `menu_url_note` = '<li>账单计算公式：订单金额(含运费) - 佣金金额 - 退单金额 + 退还佣金</li>\n            <li>账单处理流程为：系统出账 > 商家确认 > 平台审核 > 财务支付(完成结算) 4个环节，其中平台审核和财务支付需要平台介入，请予以关注</li>', `menu_order` = 0, `menu_time` = '0000-00-00 00:00:00' WHERE `menu_id` = 16010;
DELETE FROM `yf_admin_menu` WHERE `menu_id` = 16003;
DELETE FROM `yf_admin_menu` WHERE `menu_id` = 90007;
DELETE FROM `yf_admin_menu` WHERE `menu_id` = 90008;

INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90009', '11001', '收款账户设置', '', '0', 'Config', 'set_account', 'config_type%5B%5D=set_account', '
请准确填写收款账户信息，显示在商家入驻-合同签订及缴费页面，商家可线下打款', '8', '0000-00-00 00:00:00');


INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90011', '11000', '账号管理', '', '9000', '', '', '', 'PayCenter，UCenter登录账号管理', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90012', '90011', '账号管理', '', '9000', 'User_Account', 'setAccount', '', '账号管理权限建议仅开放给admin管理员，可以给PayCenter，UCenter分配账号', '50', '0000-00-00 00:00:00');

CREATE TABLE `yf_admin_user_account` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_name` varchar(50) NOT NULL COMMENT '用户帐号',
  `user_administrator` varchar(255) NOT NULL COMMENT '管理员名称',
  `user_for` varchar(32) NOT NULL COMMENT 'UCenter、PayCenter',
  `enable` tinyint(1) DEFAULT '1' COMMENT '是否授权 1-是 2-否',
  `user_key` varchar(32) DEFAULT NULL COMMENT '用户key',
  `is_del` tinyint(1) DEFAULT '1' COMMENT '2-删除',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10687 DEFAULT CHARSET=utf8 COMMENT='ucenter，paycenter后台账号表';


-- 分类图片
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90013', '19002', '分类模板', '', '0', 'Config', 'setCat', 'config_type%5B%5D=setCat', '选择分类样式模板', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90014', '20003', '分类模板', '', '0', 'Config', 'setWxCat', 'config_type%5B%5D=setWxCat', '选择分类样式模板', '50', '0000-00-00 00:00:00');


UPDATE `yf_admin_menu` SET `menu_id`='19002', `menu_parent_id`='19000', `menu_name`='分类管理', `menu_icon`='', `rights_id`='15100', `menu_url_ctl`='', `menu_url_met`='', `menu_url_parem`='', `menu_url_note`='手机客户端商品分类图标/图片设置', `menu_order`='0', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='19002');
UPDATE `yf_admin_menu` SET `menu_id`='20003', `menu_parent_id`='20000', `menu_name`='分类管理', `menu_icon`='', `rights_id`='18100', `menu_url_ctl`='', `menu_url_met`='', `menu_url_parem`='', `menu_url_note`='小程序客户端商品分类图标/图片设置', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='20003');


INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90015', '11001', '移动端图片设置', '', '0', 'Config', 'mall', 'config_type%5B%5D=mall&config_type%5B%5D=mall', '<li>填写邮件服务器相关参数，并点击“测试”按钮进行效验，保存后生效。</li><li>设置商城logo 手机端/小程序通用</li>', '3', '0000-00-00 00:00:00');


INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90016', '11001', '营业执照', '', '0', 'Config', 'business', 'config_type%5B%5D=business', '营业执照', '50', '0000-00-00 00:00:00');
