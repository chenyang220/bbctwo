ALTER TABLE `yf_order_settlement`
ADD COLUMN `os_commis_cod`  decimal(10,2) NOT NULL COMMENT '货到付款佣金' AFTER `os_commis_amount`;
ALTER TABLE `yf_shop_base`
ADD COLUMN `wap_shop_logo` varchar(255) NOT NULL COMMENT 'app/wap店铺logo' AFTER `shop_logo`;
ALTER TABLE `yf_goods_common`
ADD COLUMN `common_video`  varchar(255) NOT NULL COMMENT '商品视频' AFTER `common_image`;
ALTER TABLE `yf_goods_common`
ADD COLUMN `is_del`  tinyint(11) NOT NULL DEFAULT '1' COMMENT '是否删除' AFTER `common_edit_time`;
ALTER TABLE `yf_goods_base`
ADD COLUMN `is_del`  tinyint(11) NOT NULL DEFAULT '1' COMMENT '是否删除' AFTER `goods_parent_id`;

CREATE TABLE `yf_shop_invoice` (
  `invoice_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '索引id',
  `user_id` INT(10) UNSIGNED NOT NULL COMMENT '会员ID',
  `shop_id` INT(10) DEFAULT NULL COMMENT '店铺id',
  `invoice_name` VARCHAR(50) DEFAULT NULL COMMENT '店铺发票模板名称',
  `invoice_state` ENUM('1','2','3') CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '1普通发票2电子发票3增值税发票',
  `invoice_title` VARCHAR(50) DEFAULT '' COMMENT '发票抬头[普通发票]',
  `invoice_content` VARCHAR(10) DEFAULT '' COMMENT '发票内容[普通发票]',
  `invoice_company` VARCHAR(50) DEFAULT '' COMMENT '单位名称',
  `invoice_code` VARCHAR(50) DEFAULT '' COMMENT '纳税人识别号',
  `invoice_reg_addr` VARCHAR(50) DEFAULT '' COMMENT '注册地址',
  `invoice_reg_phone` VARCHAR(30) DEFAULT '' COMMENT '注册电话',
  `invoice_reg_bname` VARCHAR(30) DEFAULT '' COMMENT '开户银行',
  `invoice_reg_baccount` VARCHAR(30) DEFAULT '' COMMENT '银行帐户',
  `invoice_rec_name` VARCHAR(20) DEFAULT '' COMMENT '收票人姓名',
  `invoice_rec_phone` VARCHAR(15) DEFAULT '' COMMENT '收票人手机号',
  `invoice_rec_email` VARCHAR(100) DEFAULT '' COMMENT '收票人邮箱',
  `invoice_rec_province` VARCHAR(30) DEFAULT '' COMMENT '收票人省份',
  `invoice_goto_addr` VARCHAR(50) DEFAULT '' COMMENT '送票地址',
  `invoice_province_id` INT(11) DEFAULT NULL,
  `invoice_city_id` INT(11) DEFAULT NULL,
  `invoice_area_id` INT(11) DEFAULT NULL,
  `is_use` TINYINT(11) NOT NULL DEFAULT '1' COMMENT '是否启用 1:关闭 2：启用',
  PRIMARY KEY (`invoice_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商家发票信息表';
