-- 增加支付订单代码名称字段 --
ALTER TABLE `pay_union_order` ADD COLUMN `payment_channel_code` varchar(20) NOT NULL DEFAULT '' COMMENT '代码名称' AFTER `payment_channel_id`;
ALTER TABLE  `pay_user_info` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号';
ALTER TABLE  `pay_user_bank_card` ADD  `area_code` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT  '国际手机号区号';

ALTER TABLE `pay_message_template` ADD COLUMN `baidu_tpl_id` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '百度短信模板id' AFTER `mold`;

ALTER TABLE `pay_transfer_money` ROW_FORMAT = Dynamic;

ALTER TABLE `pay_union_order` ADD COLUMN `pay_union_order` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '代码名称' AFTER `payment_channel_id`;

ALTER TABLE `pay_union_order` MODIFY COLUMN `payment_channel_code` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '支付渠道代码' AFTER `notify_data`;

ALTER TABLE `pay_user_bank_card` ROW_FORMAT = Dynamic;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_provinceid` int(11) NOT NULL DEFAULT 0 AFTER `user_remark`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_cityid` int(11) NOT NULL DEFAULT 0 AFTER `user_provinceid`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_areaid` int(11) NOT NULL DEFAULT 0 AFTER `user_cityid`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_areaid`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_address`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_mobile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_email`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_identity_card` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_avatar`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_identity_font_logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_identity_type`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_identity_face_logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `user_identity_font_logo`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_identity_start_time` date NOT NULL DEFAULT '0000-00-00' AFTER `user_identity_face_logo`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_identity_end_time` date NOT NULL DEFAULT '0000-00-00' AFTER `user_identity_start_time`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_btapply_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `user_bt_status`;

ALTER TABLE `pay_user_info` MODIFY COLUMN `user_btverify_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `user_btapply_time`;

ALTER TABLE `pay_user_info` ADD INDEX `user_id`(`user_id`) USING BTREE;