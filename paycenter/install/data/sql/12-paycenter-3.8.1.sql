-- 白条--
ALTER TABLE pay_user_resource add COLUMN bt_type TINYINT(1) DEFAULT 1 COMMENT '额度类型 1：定额 2不定额度';
ALTER TABLE pay_user_resource modify  column user_credit_cycle TINYINT(1) DEFAULT 1  COMMENT '白条还款周期 单位月';
ALTER TABLE pay_consume_record add  column certificate varchar(255) DEFAULT ''  COMMENT '还款凭证';


ALTER TABLE pay_user_info ADD COLUMN shop_company_name VARCHAR(255) DEFAULT '' COMMENT '公司名称';
ALTER TABLE pay_user_info ADD COLUMN address_area VARCHAR(255) DEFAULT '' COMMENT '所在地区';
ALTER TABLE pay_user_info ADD COLUMN company_address_detail VARCHAR(255) DEFAULT '' COMMENT '公司详细地址';
ALTER TABLE pay_user_info ADD COLUMN company_phone VARCHAR(255) DEFAULT '' COMMENT '公司电话';
ALTER TABLE pay_user_info ADD COLUMN contacts_name VARCHAR(255) DEFAULT '' COMMENT '联系人姓名';

ALTER TABLE pay_user_info ADD COLUMN contacts_phone VARCHAR(255) DEFAULT '' COMMENT '联系人号码';
ALTER TABLE pay_user_info ADD COLUMN threeinone tinyint(1) DEFAULT 0 COMMENT '是否多证合一';
ALTER TABLE pay_user_info ADD COLUMN business_id VARCHAR(255) DEFAULT '' COMMENT '营业执照号';
ALTER TABLE pay_user_info ADD COLUMN business_license_location VARCHAR(255) DEFAULT '' COMMENT '营业执照所在地';
ALTER TABLE pay_user_info ADD COLUMN business_licence_start VARCHAR(255) DEFAULT '' COMMENT '营业执照有效期';
ALTER TABLE pay_user_info ADD COLUMN business_licence_end VARCHAR(255) DEFAULT '' COMMENT '营业执照有效期';
ALTER TABLE pay_user_info ADD COLUMN business_license_electronic VARCHAR(255) DEFAULT '' COMMENT '营业执照电子版';
ALTER TABLE pay_user_info ADD COLUMN organization_code VARCHAR(255) DEFAULT '' COMMENT '组织机构代码';
ALTER TABLE pay_user_info ADD COLUMN organization_code_electronic VARCHAR(255) DEFAULT '' COMMENT '组织机构代码证电子版';
ALTER TABLE pay_user_info ADD COLUMN taxpayer_id VARCHAR(255) DEFAULT '' COMMENT '纳税人识别号';
ALTER TABLE pay_user_info ADD COLUMN tax_registration_certificate VARCHAR(255) DEFAULT '' COMMENT '税务登记证号';
ALTER TABLE pay_user_info ADD COLUMN tax_registration_certificate_electronic VARCHAR(255) DEFAULT '' COMMENT '税务登记证号电子版';
ALTER TABLE pay_consume_trade add COLUMN repayment_time VARCHAR(30)  DEFAULT '0000-00-00 00:00:00' COMMENT '还款最后期限';
ALTER TABLE pay_consume_trade add COLUMN trade_payment_status TINYINT(1) DEFAULT 0 COMMENT '还款转态 0 未还款 1 已还款';
INSERT INTO pay_web_config(config_key,config_value,config_type,config_enable) VALUES ('bt_statement','','bt',1);
INSERT INTO pay_web_config(config_key,config_value,config_type,config_enable) VALUES ('bt_name','','bt',1);
INSERT INTO pay_web_config(config_key,config_value,config_type,config_enable) VALUES ('bt_time','','bt',1);
UPDATE  pay_web_config  set config_value = 0 where config_key="remote_image_status";

ALTER TABLE pay_user_info CHANGE user_identity_end_time user_identity_end_time VARCHAR(30);
-- 提现表新增字段
ALTER TABLE `pay_consume_withdraw` ADD COLUMN `withdraw_identity` varchar(50) DEFAULT NULL COMMENT '支付宝账号';
ALTER TABLE `pay_consume_withdraw` ADD COLUMN `withdraw_name` varchar(10) DEFAULT NULL COMMENT '支付宝所属人姓名';

--预售支付表新增字段
ALTER TABLE `pay_union_order`
ADD COLUMN  `is_presale` tinyint(1) DEFAULT NULL COMMENT '是否是预售订单 0不是 1是',
ADD COLUMN  `presale_union_order_id` varchar(20) DEFAULT NULL COMMENT '尾款支付单号',
ADD COLUMN  `presale_deposit` decimal(10,2) DEFAULT NULL COMMENT '预售定金',
ADD COLUMN  `final_price` decimal(10,2) DEFAULT NULL COMMENT '预售尾款';
