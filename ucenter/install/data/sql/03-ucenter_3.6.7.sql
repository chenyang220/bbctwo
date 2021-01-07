ALTER TABLE `ucenter_base_app`
ADD COLUMN `app_showname`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '显示名称' AFTER `app_name`;
