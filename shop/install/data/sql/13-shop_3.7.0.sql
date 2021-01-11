INSERT INTO `yf_web_config` (`config_key`, `config_value`, `config_type`, `config_enable`, `config_comment`, `config_datatype`) VALUES ('site_pc_status', '1', 'site', '1', '', 'number');
ALTER TABLE `yf_order_settlement` ADD COLUMN `os_commis_return_amount_fenxiao`  decimal(10) NOT NULL COMMENT '退还分销佣金' AFTER `os_directseller_amount`;
alter table yf_explore_images add type varchar(50) not Null;
alter table yf_explore_images add fengmianimages varchar(255) not Null;