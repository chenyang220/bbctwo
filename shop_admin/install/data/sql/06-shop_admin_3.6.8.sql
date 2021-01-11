-- 新增：我二维码菜单
INSERT INTO `yf_admin_menu` VALUES (40021, 19000, '我的二维码', '', 0, '', '', '', 'WAP/APP端我的二维码设置', 50, '0000-0-0 00:00:00');
INSERT INTO `yf_admin_menu` VALUES (40022, 40021, '二维码页面设置', '', 0, 'Config', 'mycode_set', 'config_type%5B%5D=mycode_set', '', 50, '0000-0-0 00:00:00');

-- 短信管理
INSERT INTO `yf_admin_rights_base` (`rights_id`,`rights_name`,`rights_parent_id`,`rights_remark`,`rights_order`) VALUES (`149`,`短信管理`,`0`,`短信管理`,`50`);
INSERT INTO `yf_admin_rights_base` (`rights_id`,`rights_name`,`rights_parent_id`,`rights_remark`,`rights_order`) VALUES (`19700`,`显示主目录`,`149`,`短信管理_显示主目录`,`50`);
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('80001', '11000', '短信管理', '', '19700', 'SmsManagement_RemainingNum', 'obtainRemainingNum', '', '短信管理', '50', '0000-00-00 00:00:00');