/* 手机端 增加 社区管理 基础设置 */
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('30005', '30004', '举报管理', '', '0', 'Mb_Explore', 'explore', '', '管理员可搜索和查看举报记录，并对举报进行处理', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('30004', '19000', '社区管理', '', '19300', '', '', '', '手机客户端/举报管理', '0', '0000-00-00 00:00:00');
INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('19300', '社区管理', '145', '社区管理', '50');

INSERT INTO `yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('30006', '30004', '社区设置', '', '0', 'Mb_Community', 'explore', '', '社区功能的开启和关闭。关闭后，移动端将无社区功能', '1', '0000-00-00 00:00:00');
ALTER TABLE `yf_goods_cat_nav` ADD `goods_cat_nav_recommend_up` varchar(255) default 'NULL' comment '推荐分类 显示二级';
ALTER TABLE `yf_goods_cat_nav` ADD `goods_cat_nav_recommend_up_display` text   comment '推荐分类 显示二级';
UPDATE `yf_admin_menu` SET `menu_url_note`='      <li>上架，当商品处于非上架状态时，前台将不能浏览该商品，店主可控制商品上架状态</li>
      <li>违规下架，当商品处于违规下架状态时，前台将不能购买该商品，只有管理员可控制商品违规下架状态，并且商品只有重新编辑后才能上架</li>
      <li>商品列表中可以查看商品详细、查看商品SKU。查看商品详细，跳转到商品详细页。查看商品SKU，显示商品的SKU、图片、价格、库存信息。</li>' WHERE (`menu_id`='12008');
      UPDATE `yf_admin_menu` SET `menu_url_note`='      <li>上架，当商品处于非上架状态时，前台将不能浏览该商品，店主可控制商品上架状态</li>
      <li>违规下架，当商品处于违规下架状态时，前台将不能购买该商品，只有管理员可控制商品违规下架状态，并且商品只有重新编辑后才能上架</li>
      <li>商品列表中可以查看商品详细、查看商品SKU。查看商品详细，跳转到商品详细页。查看商品SKU，显示商品的SKU、图片、价格、库存信息。</li>' WHERE (`menu_id`='12009');
      UPDATE `yf_admin_menu` SET `menu_url_note`='      <li>上架，当商品处于非上架状态时，前台将不能浏览该商品，店主可控制商品上架状态</li>
      <li>违规下架，当商品处于违规下架状态时，前台将不能购买该商品，只有管理员可控制商品违规下架状态，并且商品只有重新编辑后才能上架</li>
      <li>商品列表中可以查看商品详细、查看商品SKU。查看商品详细，跳转到商品详细页。查看商品SKU，显示商品的SKU、图片、价格、库存信息。</li>' WHERE (`menu_id`='12010');

-- INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('site_status_wap', '0', 'site', '1', '', 'number');

/* 增加手机端 基础设置  权限 */
-- INSERT INTO `yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('145', '手机端基础设置', '0', '手机端基础设置', '50');
-- 后台菜单表插入举报原因表信息--
INSERT INTO `bbc`.`yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('30004', '19000', '社区管理', '', '19300', '', '', '', '手机客户端/举报管理', '0', '0000-00-00 00:00:00');
INSERT INTO `bbc`.`yf_admin_menu` (`menu_id`, `menu_parent_id`, `menu_name`, `menu_icon`, `rights_id`, `menu_url_ctl`, `menu_url_met`, `menu_url_parem`, `menu_url_note`, `menu_order`, `menu_time`) VALUES ('30005', '30004', '社区管理', '', '0', 'Mb_Explore', 'explore', '', '<li>通过举报管理，可以查看举报资料，也可以根据条件搜索相关举报。</li>', '0', '0000-00-00 00:00:00');

-- 后台权限表插入举报原因表信息--
INSERT INTO `bbc`.`yf_admin_rights_base` (`rights_id`, `rights_name`, `rights_parent_id`, `rights_remark`, `rights_order`) VALUES ('19300', '举报管理', '145', '举报管理', '50');
