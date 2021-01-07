INSERT INTO `ucenter_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('alipay_app_id', '', 'connect', '1', '', 'string');
INSERT INTO `ucenter_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('alipay_alipayrsaPublicKey', '', 'connect', '1', '', 'string');
INSERT INTO `ucenter_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('alipay_alipayrsaPrivateKey', '', 'connect', '1', '', 'string');
INSERT INTO `ucenter_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('alipay_status', '0', 'connect', '1', '', 'string');

ALTER TABLE `server_id_seq` ROW_FORMAT = Dynamic;

ALTER TABLE `ucenter_base_district` DROP INDEX `area_parent_id`;

ALTER TABLE `ucenter_base_district` MODIFY COLUMN `district_id` mediumint(8) UNSIGNED NOT NULL COMMENT '地区id' FIRST;

ALTER TABLE `ucenter_base_district` MODIFY COLUMN `district_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '地区名称' AFTER `district_id`;

ALTER TABLE `ucenter_base_district` MODIFY COLUMN `district_parent_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id' AFTER `district_name`;

ALTER TABLE `ucenter_base_district` MODIFY COLUMN `district_displayorder` smallint(6) NOT NULL DEFAULT 0 COMMENT '排序' AFTER `district_parent_id`;

ALTER TABLE `ucenter_base_district` MODIFY COLUMN `district_region` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '区域名称 - 华北、东北、华东、华南、华中、西南、西北、港澳台、海外' AFTER `district_displayorder`;

ALTER TABLE `ucenter_base_district` MODIFY COLUMN `district_is_leaf` tinyint(1) NOT NULL DEFAULT 1 COMMENT '无子类' AFTER `district_region`;

ALTER TABLE `ucenter_base_district` MODIFY COLUMN `district_is_level` tinyint(1) NOT NULL DEFAULT 1 COMMENT '等级' AFTER `district_is_leaf`;

ALTER TABLE `ucenter_base_district` MODIFY COLUMN `district_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '地区id' FIRST;

ALTER TABLE `ucenter_base_district` ADD INDEX `upid`(`district_parent_id`, `district_displayorder`) USING BTREE;

ALTER TABLE `ucenter_message_template` ADD COLUMN `baidu_tpl_id` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '百度短信模板id' AFTER `mold`;

ALTER TABLE `ucenter_reg_option` ROW_FORMAT = Dynamic;

ALTER TABLE `ucenter_reg_option` MODIFY COLUMN `option_id` int(11) NOT NULL COMMENT '选项id' AFTER `reg_option_order`;

ALTER TABLE `ucenter_reg_option` MODIFY COLUMN `reg_option_datatype` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'data_type' AFTER `reg_option_placeholder`;

ALTER TABLE `ucenter_reg_option` MODIFY COLUMN `reg_option_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否启用' AFTER `reg_option_value`;

ALTER TABLE `ucenter_reg_option_value` ROW_FORMAT = Dynamic;

ALTER TABLE `ucenter_user_app_server` ADD UNIQUE INDEX `user_name`(`user_name`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD UNIQUE INDEX `user_name_2`(`user_name`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `user_name_3`(`user_name`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `active_time`(`active_time`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `active_time_2`(`active_time`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `user_name_4`(`user_name`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `app_id`(`app_id`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `app_id_2`(`app_id`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `server_id`(`server_id`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `user_name_5`(`user_name`) USING BTREE;

ALTER TABLE `ucenter_user_app_server` ADD INDEX `user_name_6`(`user_name`) USING BTREE;

ALTER TABLE `ucenter_user_info_detail` ADD COLUMN `area_code` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '国际手机号区号' AFTER `user_area`;

ALTER TABLE `ucenter_user_info_detail` DROP COLUMN `user_no_mobile`;

ALTER TABLE `ucenter_user_option` ROW_FORMAT = Dynamic;

DROP TABLE `ucenter_base_app_licence`;

DROP TABLE `ucenter_base_app_licence_domain`;

DROP TABLE `ucenter_base_app_licence_log`;
