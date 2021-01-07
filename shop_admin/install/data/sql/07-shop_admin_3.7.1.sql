
-- 管理员登录Cookie二次验证
DROP TABLE IF EXISTS `yf_admin_cookie_auth`;
CREATE TABLE `yf_admin_cookie_auth` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `ssid` char(64) DEFAULT NULL COMMENT '客户端标识',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `ip` varchar(50) DEFAULT NULL COMMENT '客户端ip',
  `admin_user_id` int(10) DEFAULT NULL COMMENT '管理员id',
  `sys_expire` int(10) DEFAULT NULL COMMENT '登录时系统设置的过期时间戳',
  PRIMARY KEY (`id`),
  KEY `ind_gid` (`ssid`) USING BTREE,
  KEY `ind_auid` (`admin_user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='管理员登录Cookie二次验证';



UPDATE `yf_admin_menu` SET `menu_id`='13022', `menu_parent_id`='13100', `menu_name`='供应商管理', `menu_icon`='', `rights_id`='17200', `menu_url_ctl`='Supplier_Manage', `menu_url_met`='indexs', `menu_url_parem`='', `menu_url_note`='', `menu_order`='0', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='13022');
UPDATE `yf_admin_menu` SET `menu_id`='13023', `menu_parent_id`='13022', `menu_name`='供应商管理', `menu_icon`='', `rights_id`='17200', `menu_url_ctl`='Supplier_Manage', `menu_url_met`='indexs', `menu_url_parem`='', `menu_url_note`='', `menu_order`='0', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='13023');
UPDATE `yf_admin_menu` SET `menu_id`='13024', `menu_parent_id`='13100', `menu_name`='供应商入驻', `menu_icon`='', `rights_id`='17200', `menu_url_ctl`='Supplier_Help', `menu_url_met`='help', `menu_url_parem`='', `menu_url_note`='', `menu_order`='0', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='13024');
UPDATE `yf_admin_menu` SET `menu_id`='40001', `menu_parent_id`='14000', `menu_name`='plus会员', `menu_icon`='', `rights_id`='12300', `menu_url_ctl`='', `menu_url_met`='', `menu_url_parem`='', `menu_url_note`='plus会员', `menu_order`='0', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='40001');
UPDATE `yf_admin_menu` SET `menu_id`='40002', `menu_parent_id`='40001', `menu_name`='plus会员设置', `menu_icon`='', `rights_id`='12300', `menu_url_ctl`='Config', `menu_url_met`='editPlus', `menu_url_parem`='config_type%5B%5D=plus', `menu_url_note`='<li>plus会员设置</li>', `menu_order`='30', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='40002');
UPDATE `yf_admin_menu` SET `menu_id`='40003', `menu_parent_id`='40001', `menu_name`='plus会员权益设置', `menu_icon`='', `rights_id`='12300', `menu_url_ctl`='Config', `menu_url_met`='editPlusQuity', `menu_url_parem`='config_type%5B%5D=plus', `menu_url_note`='<li>plus会员权益设置</li>', `menu_order`='80', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='40003');
UPDATE `yf_admin_menu` SET `menu_id`='40004', `menu_parent_id`='40001', `menu_name`='plus会员管理', `menu_icon`='', `rights_id`='12300', `menu_url_ctl`='User_Plus', `menu_url_met`='editPlusManage', `menu_url_parem`='', `menu_url_note`='<li>plus会员管理</li>', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='40004');
UPDATE `yf_admin_menu` SET `menu_id`='19016', `menu_parent_id`='16000', `menu_name`='资讯', `menu_icon`='', `rights_id`='15400', `menu_url_ctl`='', `menu_url_met`='', `menu_url_parem`='', `menu_url_note`='资讯列表', `menu_order`='0', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='19016');
UPDATE `yf_admin_menu` SET `menu_id`='19017', `menu_parent_id`='19016', `menu_name`='资讯列表', `menu_icon`='', `rights_id`='15400', `menu_url_ctl`='Information_News', `menu_url_met`='indexs', `menu_url_parem`='', `menu_url_note`='资讯列表', `menu_order`='0', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='19017');
UPDATE `yf_admin_menu` SET `menu_id`='19018', `menu_parent_id`='19016', `menu_name`='资讯审核', `menu_icon`='', `rights_id`='15400', `menu_url_ctl`='Information_News', `menu_url_met`='auditing', `menu_url_parem`='', `menu_url_note`='资讯审核', `menu_order`='0', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='19018');
UPDATE `yf_admin_menu` SET `menu_id`='19019', `menu_parent_id`='19016', `menu_name`='资讯标签设置', `menu_icon`='', `rights_id`='15400', `menu_url_ctl`='Information_NewsClass', `menu_url_met`='indexs', `menu_url_parem`='', `menu_url_note`='资讯标签设置', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='19019');
UPDATE `yf_admin_menu` SET `menu_id`='19021', `menu_parent_id`='19016', `menu_name`='资讯投诉管理', `menu_icon`='', `rights_id`='15400', `menu_url_ctl`='Information_News', `menu_url_met`='complaint', `menu_url_parem`='', `menu_url_note`='资讯投诉管理', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='19021');
UPDATE `yf_admin_menu` SET `menu_id`='90001', `menu_parent_id`='16000', `menu_name`='分销设置', `menu_icon`='', `rights_id`='15400', `menu_url_ctl`='', `menu_url_met`='', `menu_url_parem`='', `menu_url_note`='分销系统后台配置', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='90001');
UPDATE `yf_admin_menu` SET `menu_id`='40021', `menu_parent_id`='19000', `menu_name`='我的二维码', `menu_icon`='', `rights_id`='15300', `menu_url_ctl`='', `menu_url_met`='', `menu_url_parem`='', `menu_url_note`='WAP/APP端我的二维码设置', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='40021');
UPDATE `yf_admin_menu` SET `menu_id`='20010', `menu_parent_id`='20000', `menu_name`='专题栏目', `menu_icon`='', `rights_id`='146', `menu_url_ctl`='', `menu_url_met`='', `menu_url_parem`='', `menu_url_note`='', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='20010');


INSERT INTO `yf_admin_rights_base` (`rights_id`,`rights_name`,`rights_parent_id`,`rights_remark`,`rights_order`) VALUES ('147','公众号','0','商家公众号','50');
INSERT INTO `yf_admin_rights_base` (`rights_id`,`rights_name`,`rights_parent_id`,`rights_remark`,`rights_order`) VALUES ('19400','显示主目录','147','商家公众号_显示主目录','50');

INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('17041', '17000', '专题栏目', '', '17000', '', '', '', '', '50', '0000-00-00 00:00:00');
insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('17042','17041','专题设置','','17000','Config','columnSet','config_type%5B%5D=column','专题设置','50','0000-00-00 00:00:00');
insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('17043','17041','标准模板','','17000','Special_Column','index','','专题栏目设置','50','0000-00-00 00:00:00');
insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('17044','17041','模板自装修','','17000','Special_Column','selfSet','','模板自装修','50','0000-00-00 00:00:00');

insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('50001','16000','商家公众号','','19400','','','','商家公众号绑定设定及管理','50','0000-00-00 00:00:00');
insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('50002','50001','商家公众号设置','','19400','Config','seller_wx','config_type%5B%5D=seller_wx','商家公众号价格设置','50','0000-00-00 00:00:00');
insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('50003','50001','全部','','19400','Promotion_SellerWx','list','','商家公众号审核管理','50','0000-00-00 00:00:00');

INSERT INTO `yf_admin_menu` VALUES ('50004', '50001', '待审核', '', '19400', 'Promotion_SellerWx', 'waitList', '', '', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES ('50005', '50001', '审核通过', '', '19400', 'Promotion_SellerWx', 'yesList', '', '', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES ('50006', '50001', '审核失败', '', '19400', 'Promotion_SellerWx', 'noList', '', '', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES ('50007', '50001', '停用', '', '19400', 'Promotion_SellerWx', 'stopList', '', '', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES ('50008', '50001', '即将到期', '', '19400', 'Promotion_SellerWx', 'endList', '', '', '50', '0000-00-00 00:00:00');


INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('60001', '11000', '菜单设置', '', '10600', '', '', '', '', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('60002', '60001', '菜单设置', '', '10600', 'Config', 'setMenu', '', '菜单设置', '50', '0000-00-00 00:00:00');
UPDATE `yf_admin_menu` SET `menu_id`='60001', `menu_parent_id`='11000', `menu_name`='开发设置', `menu_icon`='', `rights_id`='10600', `menu_url_ctl`='', `menu_url_met`='', `menu_url_parem`='', `menu_url_note`='', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='60001');
UPDATE `yf_admin_menu` SET `menu_id`='60002', `menu_parent_id`='60001', `menu_name`='开发设置', `menu_icon`='', `rights_id`='10600', `menu_url_ctl`='Config', `menu_url_met`='setMenu', `menu_url_parem`='', `menu_url_note`='<li>此功能模块仅为了方便技术开发人员快速迭代新功能</li><li>非开发技术人员不得使用以防出错。</li>', `menu_order`='50', `menu_time`='0000-00-00 00:00:00' WHERE (`menu_id`='60002');

INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('50020', '19000', '专题栏目', '', '0', '', '', '', '', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('50100', '50020', '专题设置', '', '19100', 'Config', 'columnWapSet', 'config_type%5B%5D=column', '专题设置', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('50101', '50020', '标准模板', '', '19100', 'Special_Column', 'wapIndex', '', '专题栏目设置', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('50102', '50020', '模板自装修', '', '19100', 'Special_Column', 'wapSelfSet', '', '模板自装修', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('14018', '14001', '发送站内信', '', '5240', 'User_Info', 'sendstation', '', '发送站内信', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('80002', '11001', '二维码设置', '', '0', 'Config', 'qrcode', 'config_type%5B%5D=qrcode', '前台二维码设置', '8', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES ('40023', '16000', '秒杀管理', '', '15000', '', '', '', '商品秒杀活动相关设定及管理', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES ('40024', '40023', '秒杀活动', '', '15000', 'Promotion_Seckill', 'list', '', '<li>商家发布的秒杀活动列表</li>\n           ', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES ('40025', '40023', '秒杀设置', '', '15000', 'Config', 'seckill', 'config_type%5B%5D=seckill', '<li>秒杀价格设置</li>', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES ('40026', '40023', '已开通商家', '', '15000', 'Promotion_Seckill', 'comboList', '', '<li>已开通秒杀活动商家列表</li>', '50', '0000-00-00 00:00:00');
-- 后台主题风格设置添加
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('17051', '17000', '主题风格', '', '10100', '', '', '', '网站全局内容基本选项设置', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('17052', '17051', '主题风格', '', '0', 'Config', 'themeStyle', '', '', '1', '0000-00-00 00:00:00');

-- 后台商家业绩统计
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('13028', '13000', '业绩统计', '', '12300', '', '', '', '', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('13029', '13028', '销售业绩统计', '', '8020', 'Shop_Business', 'indexs', '', '', '0', '0000-00-00 00:00:00');

INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('11072', '11017', '分站商家业绩统计', '', '0', 'Shop_Business', 'indexs', 'type=all', '<li>根据需求，新增、编辑、启用、禁用不同的分站</li>', '0', '0000-00-00 00:00:00');

-- 退款退货菜单
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90007', '16000', '退款退货设置', '', '0', '', '', '', '退款退货设置', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('90008', '90007', '退款退货设置', '', '0', 'Config', 'ReturnSet', 'config_type%5B%5D=plat', '退款退货设置', '50', '0000-00-00 00:00:00');

-- 小程序专题栏目
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('20010', '20000', '专题栏目', '', '0', '', '', '', '', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('20100', '20010', '专题设置', '', '0', 'Config', 'WxColumnSet', 'config_type%5B%5D=column', '专题设置', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('20101', '20010', '标准模板', '', '0', 'WxSpecial_Column', 'index', '', '专题栏目设置', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('20102', '20010', '模板自装修', '', '0', 'WxSpecial_Column', 'selfSet', '', '模板自装修', '50', '0000-00-00 00:00:00');

insert into `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) values('19500','分销设置','0','分销设置','50');
insert into `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) values('19600','分销配置','19500','分销配置','50');

insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('90001','16000','分销设置','','0','','','','分销系统后台配置','50','0000-00-00 00:00:00');
insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('90002','90001','分销配置','','19600','Config','distribution_cf','config_type%5B%5D=distribution_cf','<li>此功能模块为分销系统配置，配置分销的礼包奖励，以及升级分销掌柜的条件</li>','50','0000-00-00 00:00:00');  
-- SNS内容审核菜单 后期打开
insert into `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) values('30007','30004','内容审核','','0','Mb_Community','verifyExplore','','此功能模块为用户上传的SNS内容展示，审核，查询','50','0000-00-00 00:00:00');
