-- 骑手APP
INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('Plugin_Delivery', '', 'plugin', '0', '', 'string');
-- 骑手APP 商家经营类目
ALTER TABLE `yf_shop_class_bind`
ADD COLUMN `delivery_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '同城配送状态0-关闭 1-开启';
ALTER TABLE `yf_shop_class_bind`
ADD COLUMN `delivery_price` decimal(10,2) DEFAULT NULL COMMENT '每单配送费';
-- goods_common
ALTER TABLE `yf_goods_common`
ADD COLUMN `common_is_delivery` tinyint(4) DEFAULT NULL DEFAULT '0' COMMENT '是否是同城配送 0-否 1-是';

-- order_base
ALTER TABLE `yf_order_base`
ADD COLUMN `is_delivery` tinyint(1) DEFAULT '0' COMMENT '是否同城配送0-否 1-是';
ALTER TABLE `yf_order_base`
ADD COLUMN `distributor_id` int(10) DEFAULT NULL COMMENT '骑手id';


ALTER TABLE `yf_shop_class_bind` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT '店铺id';
ALTER TABLE `yf_shop_class_bind` ADD INDEX `product_class_id`(`product_class_id`) USING BTREE COMMENT '商品分类id';
ALTER TABLE `yf_goods_base` ADD INDEX `common_id`(`common_id`) USING BTREE COMMENT 'common_id';
ALTER TABLE `yf_goods_base` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT 'shop_id';
ALTER TABLE `yf_goods_base` ADD INDEX `cat_id`(`cat_id`) USING BTREE COMMENT 'cat_id';
ALTER TABLE `yf_goods_base` ADD INDEX `brand_id`(`brand_id`) USING BTREE COMMENT 'brand_id';
ALTER TABLE `yf_goods_base` ADD INDEX `is_del`(`is_del`) USING BTREE COMMENT 'is_del';
ALTER TABLE `yf_goods_common` ADD INDEX `is_del`(`is_del`) USING BTREE COMMENT 'is_del';
ALTER TABLE `yf_goods_images` ADD INDEX `common_id`(`common_id`) USING BTREE COMMENT 'common_id';
ALTER TABLE `yf_goods_evaluation` ADD INDEX `common_id`(`common_id`) USING BTREE COMMENT 'common_id';
ALTER TABLE `yf_goods_evaluation` ADD INDEX `scores`(`scores`) USING BTREE COMMENT 'scores';
ALTER TABLE `yf_mansong_base` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT 'shop_id';
ALTER TABLE `yf_mansong_base` ADD INDEX `mansong_state`(`mansong_state`) USING BTREE COMMENT 'mansong_state';
ALTER TABLE `yf_user_favorites_goods` ADD INDEX `user_id`(`user_id`) USING BTREE COMMENT 'user_id';
ALTER TABLE `yf_user_favorites_goods` ADD INDEX `goods_id`(`goods_id`) USING BTREE COMMENT 'goods_id';
ALTER TABLE `yf_shop_entity` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT 'shopId';
ALTER TABLE `yf_seller_base` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT 'shopId';
ALTER TABLE `yf_seller_base` ADD INDEX `user_id`(`user_id`) USING BTREE COMMENT 'user_id';

ALTER TABLE `yf_chain_goods` ADD INDEX `chain_id`(`chain_id`) USING BTREE COMMENT 'chain_id';
ALTER TABLE `yf_chain_goods` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT 'shop_id';
ALTER TABLE `yf_chain_goods` ADD INDEX `goods_id`(`goods_id`) USING BTREE COMMENT 'goods_id';

ALTER TABLE `yf_order_goods` ADD INDEX `common_id`(`common_id`) USING BTREE COMMENT 'common_id';
ALTER TABLE `yf_order_goods` ADD INDEX `goods_id`(`goods_id`) USING BTREE COMMENT 'goods_id';

ALTER TABLE `yf_consult_base` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT 'shop_id';
ALTER TABLE `yf_consult_base` ADD INDEX `goods_id`(`goods_id`) USING BTREE COMMENT 'goods_id';
ALTER TABLE `yf_shop_custom_service` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT 'shop_id';

ALTER TABLE `yf_plus_goods` ADD INDEX `shop_id`(`shop_id`) USING BTREE COMMENT 'shop_id';
ALTER TABLE `yf_plus_goods` ADD INDEX `goods_common_id`(`goods_common_id`) USING BTREE COMMENT 'goods_common_id';