ALTER TABLE `yf_order_goods`
ADD COLUMN `directseller_commission_0_refund`  decimal(10,2) NOT NULL COMMENT '直属一级分佣退还金额' AFTER `directseller_commission_0`;

ALTER TABLE `yf_order_goods`
ADD COLUMN `directseller_commission_1_refund`  decimal(10,2) NOT NULL COMMENT '直属二级分佣退还金额' AFTER `directseller_commission_1`;

ALTER TABLE `yf_order_goods`
ADD COLUMN `directseller_commission_2_refund`  decimal(10,2) NOT NULL COMMENT '直属三级分佣退还金额' AFTER `directseller_commission_2`;

ALTER TABLE `yf_platform_custom_service`
ADD COLUMN `status`  tinyint(2) default '0' NOT NULL  COMMENT '是否删除1买家删,2卖家删';

ALTER TABLE `yf_goods_common`
ADD COLUMN `common_edit_time` datetime NOT NULL COMMENT '修改商品是否限购的时间';

ALTER TABLE `yf_message`
ADD COLUMN `message_isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0正常1删除';

ALTER TABLE yf_chain_base add  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态';

ALTER TABLE  `yf_order_base` CHANGE  `order_from`  `order_from` ENUM(  '1',  '2',  '3' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '1' COMMENT  '手机端';
ALTER TABLE  `yf_redpacket_base` CHANGE  `redpacket_state`  `redpacket_state` TINYINT( 4 ) NOT NULL COMMENT  '红包状态(1-未用,2-已用,3-过期,4-回收)';