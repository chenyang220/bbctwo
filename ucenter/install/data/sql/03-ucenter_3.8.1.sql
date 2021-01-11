ALTER TABLE `ucenter_user_info_detail` ADD COLUMN `user_is_shop` varchar(255) NOT NULL DEFAULT '1' COMMENT '商家二级域名注册会员标识';

DROP TABLE IF EXISTS `ucenter_login_limit`;
CREATE TABLE `ucenter_login_limit` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `account` varchar(30) DEFAULT NULL COMMENT '账号',
  `type` tinyint(1) DEFAULT NULL COMMENT '类型（1：普通用户；2：管理员）',
  `login_time` int(10) DEFAULT NULL COMMENT '登录时间',
  `ip` varchar(20) DEFAULT NULL COMMENT '登录ip地址',
  PRIMARY KEY (`id`),
  KEY `index_accout` (`account`) USING BTREE,
  KEY `index_type` (`type`) USING BTREE,
  KEY `index_login_time` (`login_time`) USING BTREE,
  KEY `uindex_atl` (`account`,`type`,`login_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='账户登录失败记录表';
