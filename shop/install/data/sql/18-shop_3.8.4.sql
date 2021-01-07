ALTER TABLE `yf_shop_base` ADD INDEX `shop_type`(`shop_type`) USING BTREE COMMENT '商家 供应商';
ALTER TABLE `yf_shop_base` ADD INDEX `user_id`(`user_id`) USING BTREE COMMENT '用户id';

alter table `yf_express` ADD INDEX  `express_abbreviation`  varchar(50) not Null COMMENT '别名'
UPDATE `yf_express` set express_abbreviationt='圆通快递' where express_id='68' 
UPDATE `yf_express` set express_abbreviationt='中通快递' where express_id='75' 


--定制sql语句---
ALTER TABLE `yf_goods_cat`
ADD COLUMN `cat_is_index`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '0、wap端首页不展示  1、wap端首页展示' AFTER `cat_is_wholesale`;
ALTER TABLE `yf_order_base`
ADD COLUMN `order_detail_url`  varchar(255) NULL DEFAULT NULL COMMENT '订单详情跳转链接' AFTER `distributor_id`;

ALTER TABLE `yf_order_base`
MODIFY COLUMN `order_from`  int(2) NOT NULL DEFAULT 1 COMMENT '1、pc端  2、WAP手机端 3、WEBPOS线下下单 4、旅游  5、酒店  6、出现  7、美食';

ALTER TABLE `pay_union_order`
ADD COLUMN `r_url`  varchar(255) NULL DEFAULT NULL COMMENT '第三方回调地址' AFTER `final_price`;

ALTER TABLE `pay_union_order`
ADD COLUMN `is_result`  int(2) NOT NULL DEFAULT 1 COMMENT '1、未推送  2、已推送' AFTER `r_url`;


ALTER TABLE `pay_union_order`
ADD COLUMN `ord_no`  varchar(255) NULL COMMENT '微信支付内部订单号' AFTER `is_result`;



ALTER TABLE `pay_union_order`
ADD COLUMN `out_no`  varchar(255) NULL COMMENT '开发者流水号' AFTER `ord_no`;




ALTER TABLE `ucenter_user_info`
ADD COLUMN `token`  varchar(255) NULL COMMENT '第三方平台传的token',
ADD COLUMN `enterId`  varchar(255) NULL COMMENT '第三方平台传的enterId';


INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('', '标签管理', '19608', '标签管理', '50');
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('', '添加标签', '19790', '添加标签', '50');
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('', '标签列表', '19790', '标签列表', '50');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('', '16000', '标签管理', '', '19608', '', '', '', '标签管理', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('', '90019', '添加标签', '', '19608', 'Shop_Label', 'addLabel', '', '添加标签', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('', '90019', '标签列表', '', '19608', 'Shop_Label', 'labelList', '', '标签列表', '50', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('', '13000', '入驻特色商城审核', '', '11700', 'Shop_Label', 'checkLabel', '', '管理特色商城审核', '0', '0000-00-00 00:00:00');





CREATE TABLE `yf_label_base` (
`id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '标签ID' ,
`label_name`  varchar(255) NOT NULL COMMENT '标签名称' ,
`create_time`  datetime NOT NULL COMMENT '添加时间' ,
PRIMARY KEY (`id`)
)
;

ALTER TABLE `yf_shop_base`
ADD COLUMN `label_id`  int(10) NULL COMMENT '标签ID',
ADD COLUMN `label_remarks`  varchar(255) NULL COMMENT '特色商城申请原因',
ADD COLUMN `label_is_check`  int(2) NOT NULL DEFAULT 0 COMMENT '商家标签审核 0、未审核 1、已审核';


ALTER TABLE `yf_goods_common`
ADD COLUMN `label_id`  int(10) NULL COMMENT '商品标签ID';



ALTER TABLE `yf_user_address`
ADD COLUMN `order_form`  int(1) NOT NULL DEFAULT 0 COMMENT '7、美食';



ALTER TABLE `yf_points_log`
MODIFY COLUMN `class_id`  tinyint(1) NOT NULL COMMENT '积分类型1.会员注册,2.会员登录3.评价4.购买商品5.6.管理员操作7.积分换购商品8.积分兑换代金券 9、签到';




ALTER TABLE `yf_user_info`
ADD COLUMN `user_sign_day`  int(10) NOT NULL DEFAULT 0 COMMENT '用户连续签到天数';