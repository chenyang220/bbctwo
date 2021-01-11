/* 首页小迭代 */

/*将 模板风格 移到 电脑端*/
UPDATE `yf_admin_menu` SET `menu_parent_id`='17000',`menu_order`='50' WHERE (`menu_id`='11012');
/*将 帮助设置 移到 电脑端*/
UPDATE `yf_admin_menu` SET `menu_parent_id`='17000',`menu_order`='50' WHERE (`menu_id`='11013');
/*将 页面导航 移到 电脑端*/
UPDATE `yf_admin_menu` SET `menu_parent_id`='17000',`menu_order`='50' WHERE (`menu_id`='11014');

/*删除一级 促销 目录*/
DELETE FROM `yf_admin_menu` WHERE (`menu_id`='17000');

/* 增加一级  电脑端  目录 */
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('17000', '0', '电脑端', 'icon-pc', '0', '', '', '', '', '0', '0000-00-00 00:00:00');


/* 将原来 促销 的下级目录移到 运营 中 */
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='17001');
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='17002');
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='17003');
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='17004');
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='17005');
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='17006');
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='17007');
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='17008');
UPDATE `yf_admin_menu` SET `menu_parent_id`='16000' WHERE (`menu_id`='19010');

/* 电脑端 增加 基础设置 */
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('30001', '17000', '基础设置', '', '19000', '', '', '', '', '1', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('30002', '30001', '基础设置', '', '0', 'Config', 'site_pc', 'config_type%5B%5D=site', '', '1', '0000-00-00 00:00:00');

/* 手机端 增加 基础设置 */
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('19022', '19000', '基础设置', '', '19100', '', '', '', '', '1', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('19023', '19022', '基础设置', '', '0', 'Config', 'site_wap', 'config_type%5B%5D=site', '', '1', '0000-00-00 00:00:00');

INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('site_status_wap', '0', 'site', '1', '', 'number');
INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('closed_reason_wap', 'The server has been hung up.  ', 'site', '1', '', 'string');


/* 小程序 增加 基础设置 */
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('20006', '20000', '基础设置', '', '19200', '', '', '', '', '1', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('20007', '20006', '基础设置', '', '0', 'Config', 'site_wxapp', 'config_type%5B%5D=site', '', '1', '0000-00-00 00:00:00');

INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('site_status_wxapp', '0', 'site', '1', '', 'number');
INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('closed_reason_wxapp', 'The server has been hung up. ', 'site', '1', '', 'string');


/* 增加电脑端 基础设置 权限 */
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('144', '电脑端基础设置', '0', '电脑端基础设置', '50');
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('19000', '显示主目录', '144', '电脑端基础设置_显示主目录', '50');

/* 增加手机端 基础设置  权限 */
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('145', '手机端基础设置', '0', '手机端基础设置', '50');
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('19100', '显示主目录', '145', '手机端基础设置_显示主目录', '50');

/* 增加小程序 基础设置 权限 */
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('146', '小程序基础设置', '0', '小程序基础设置', '50');
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('19200', '显示主目录', '146', '小程序基础设置_显示主目录', '50');

/* 将一级商城设置下的二级商城设置 移动到 一级基础设置 中 */
UPDATE `yf_admin_menu` SET `menu_parent_id`='11001' WHERE (`menu_id`='11039');

/* 将 商城设置 修改为 搜素设置 */
UPDATE `yf_admin_menu` SET `menu_name`='搜索设置' WHERE (`menu_id`='11005');

/* 删除图片设置 */
DELETE FROM `yf_admin_menu` WHERE (`menu_id`='11006');

/* 将设置->基础设置->商城设置  修改为图片设置  并将它移动到 电脑端 中 */
DELETE FROM `yf_admin_menu` WHERE (`menu_id`='11039');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('11039', '17000', '图片设置', '', '5000', '', '', '', '', '1', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('30003', '11039', '图片设置', '', '0', 'Config', 'shop', 'config_type%5B%5D=setting', '<li>网站全局基本设置，商城及其他模块相关内容在其各自栏目设置项内进行操作。</li>', '1', '0000-00-00 00:00:00');

/* 删除 数据库备份 */
DELETE FROM `yf_admin_menu` WHERE menu_id=11038 AND menu_name='数据库备份';

INSERT INTO `yf_admin_menu` VALUES (19016, 16000, '资讯', '', 0, '', '', '', '资讯列表', 0, '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES (19017, 19016, '资讯列表', '', 0, 'Information_News', 'indexs', '', '资讯列表', 0, '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES (19018, 19016, '资讯审核', '', 0, 'Information_News', 'auditing', '', '资讯审核', 0, '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES (19019, 19016, '资讯标签设置', '', 0, 'Information_NewsClass', 'indexs', '', '资讯标签设置', 50, '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES (19021, 19016, '资讯投诉管理', '', 0, 'Information_News', 'complaint', '', '资讯投诉管理', 50, '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` VALUES (20008, 11007, '资讯', '', 0, '', '', '', '', 50, '0000-00-00 00:00:00');

UPDATE `yf_admin_rights_group` SET `rights_group_id`='1', `rights_group_name`='系统管理员    ', `rights_group_rights_ids`='[3000,3110,3120,3130,3140,3160,3170,3180,3190,10400,3200,3210,3220,3230,3240,3250,3260,3270,3280,3290,3300,3310,10000,3320,3330,3340,3350,3360,10900,3370,3371,3380,3390,3400,11000,3410,3420,3430,3440,11200,3700,3710,14300,3720,3730,3740,3750,3760,3770,3780,3790,3800,3810,3820,3830,3840,3850,3860,14400,16000,16010,16020,16030,3870,3880,3890,3900,3910,3920,3930,3940,3950,3960,3970,3980,14500,3990,4000,4010,4020,4030,4040,4050,4060,4070,14600,4080,4090,4100,4110,4120,4130,14700,4140,4150,4160,4170,4180,4190,4200,4210,4220,14800,16040,4230,4240,4250,4260,4270,4280,4290,4300,4310,4320,4330,14900,4340,4350,4360,4370,4380,4390,4400,4410,4420,13500,11100,8880,8890,8900,8910,11600,5000,5010,5020,5030,9400,5040,5050,5060,5070,9500,5080,5090,5100,5110,5120,5130,5140,5150,9600,5160,5170,9700,5180,5190,5200,5210,5220,5230,9800,5240,5250,12300,5260,5270,5280,5290,5300,15600,5310,5320,5330,5340,5350,12400,5360,5370,5380,5390,12600,5400,12700,8000,8010,8020,8030,8040,8050,8060,8070,8080,8090,8100,8110,8120,8130,8140,8150,8160,8170,8180,11700,8190,8200,8210,8220,8230,11800,8240,8250,8260,8270,11900,8280,8290,8300,8310,12000,12100,8320,8330,8340,8350,8360,12200,8500,8510,13600,8520,8530,13700,8540,8550,13800,8560,8570,8580,8590,8600,8610,8620,8630,13900,8640,8650,8660,8670,14000,8690,8700,8710,8720,8730,8740,8750,8760,8770,8780,8790,8800,8810,8820,8830,8840,8850,8860,8870,14200,14210,16050,9000,9100,9200,9300,10100,3010,3020,3030,3040,3060,3070,3080,3090,10200,10300,10500,10600,10700,10800,11300,11400,11500,12800,12900,15500,13000,13100,13200,13300,13400,15400,15000,15100,15200,15300,17000,17100,17200,18000,18100,19000,19100,19200]', `rights_group_add_time`='0' WHERE (`rights_group_id`='1');
-- 后台添加平台红包设置--
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('17034', '17008', '设置', '', '0', 'Config', 'redPacketSet', 'config_type%5B%5D=redpacket', '<li>功能开启后，用户首次打开商城首页会弹窗提示领取红包</li>', '0', '0000-00-00 00:00:00');
-- 后台新增微信公众号,模板消息配置菜单
INSERT INTO `yf_admin_menu` VALUES ('40018', '40013', '模板消息', '', '0', 'WxPublic_Industry', 'tpl', 'config_type%5B%5D=tpl', '微信公众号模板消息设置！', '50', '0000-00-00 00:00:00');
