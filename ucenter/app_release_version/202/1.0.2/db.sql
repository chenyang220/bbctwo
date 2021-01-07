
CREATE TABLE `yf_admin_base_protocol` (
  `protocol_id` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '协议索引Id',
  `cmd_id` smallint(4) NOT NULL DEFAULT '0' COMMENT '协议Id',
  `ctl` varchar(50) NOT NULL DEFAULT '' COMMENT '控制器类名称',
  `met` varchar(50) NOT NULL DEFAULT '' COMMENT '控制器方法',
  `db` enum('master','slave') NOT NULL DEFAULT 'master' COMMENT '连接数据库类型',
  `typ` enum('e','json','msgpcak','amf') NOT NULL DEFAULT 'json' COMMENT '输出数据默认类型',
  `rights_id` mediumint(20) NOT NULL COMMENT '权限Id',
  `log` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否记录日志',
  `struct` varchar(100) NOT NULL DEFAULT '' COMMENT '生成结构体，独立使用',
  `comment` varchar(255) NOT NULL DEFAULT '' COMMENT '注释',
  `0` varchar(255) NOT NULL DEFAULT 'int16_t:cmd_id' COMMENT '第一个字段',
  `1` varchar(255) NOT NULL DEFAULT '',
  `2` varchar(255) NOT NULL DEFAULT '',
  `3` varchar(255) NOT NULL DEFAULT '',
  `4` varchar(255) NOT NULL DEFAULT '',
  `5` varchar(255) NOT NULL DEFAULT '',
  `6` varchar(255) NOT NULL DEFAULT '',
  `7` varchar(255) NOT NULL DEFAULT '',
  `8` varchar(255) NOT NULL DEFAULT '',
  `9` varchar(255) NOT NULL DEFAULT '',
  `10` varchar(255) NOT NULL DEFAULT '',
  `11` varchar(255) NOT NULL DEFAULT '',
  `12` varchar(255) NOT NULL DEFAULT '',
  `13` varchar(255) NOT NULL DEFAULT '',
  `14` varchar(255) NOT NULL DEFAULT '',
  `15` varchar(255) NOT NULL DEFAULT '',
  `16` varchar(255) NOT NULL DEFAULT '',
  `17` varchar(255) NOT NULL DEFAULT '',
  `18` varchar(255) NOT NULL DEFAULT '',
  `19` varchar(255) NOT NULL DEFAULT '',
  `20` varchar(255) NOT NULL DEFAULT '',
  `21` varchar(255) NOT NULL DEFAULT '',
  `22` varchar(255) NOT NULL DEFAULT '',
  `23` varchar(255) NOT NULL DEFAULT '',
  `24` varchar(255) NOT NULL DEFAULT '',
  `25` varchar(255) NOT NULL DEFAULT '',
  `26` varchar(255) NOT NULL DEFAULT '',
  `27` varchar(255) NOT NULL DEFAULT '',
  `28` varchar(255) NOT NULL DEFAULT '',
  `29` varchar(255) NOT NULL DEFAULT '',
  `30` varchar(255) NOT NULL DEFAULT '',
  `31` varchar(255) NOT NULL DEFAULT '',
  `32` varchar(255) NOT NULL DEFAULT '',
  `33` varchar(255) NOT NULL DEFAULT '',
  `34` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`protocol_id`),
  UNIQUE KEY `cmd_id_key` (`cmd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1938 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='基础通信协议表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_log_action` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `user_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '玩家Id',
  `user_account` varchar(100) NOT NULL DEFAULT '' COMMENT '角色账户',
  `user_name` varchar(20) NOT NULL DEFAULT '' COMMENT '角色名称',
  `action_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '行为id == protocal_id -> rights_id',
  `action_type_id` mediumint(9) NOT NULL COMMENT '操作类型id，right_parent_id',
  `log_param` text NOT NULL COMMENT '请求的参数',
  `log_ip` varchar(20) NOT NULL DEFAULT '',
  `log_date` date NOT NULL COMMENT '日志日期',
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录时间',
  PRIMARY KEY (`log_id`),
  KEY `player_id` (`user_id`) COMMENT '(null)',
  KEY `log_date` (`log_date`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=39351 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户行为日志表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_menu` (
  `menu_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '菜单id',
  `menu_parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '菜单id',
  `menu_name` varchar(20) NOT NULL COMMENT '菜单名称',
  `menu_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标class',
  `rights_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
  `menu_url_ctl` varchar(30) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `menu_url_met` varchar(20) NOT NULL DEFAULT '' COMMENT '控制器方法',
  `menu_url_parem` varchar(50) NOT NULL DEFAULT '' COMMENT 'url参数',
  `menu_url_note` varchar(255) NOT NULL DEFAULT '' COMMENT '页面帮助内容',
  `menu_order` tinyint(4) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `menu_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20003 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='菜单表-10个递增';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_menu_copy` (
  `menu_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '菜单id',
  `menu_parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '菜单id',
  `menu_name` varchar(20) NOT NULL COMMENT '菜单名称',
  `menu_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标class',
  `rights_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
  `menu_url_ctl` varchar(30) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `menu_url_met` varchar(20) NOT NULL DEFAULT '' COMMENT '控制器方法',
  `menu_url_parem` varchar(50) NOT NULL DEFAULT '' COMMENT 'url参数',
  `menu_url_note` varchar(255) NOT NULL DEFAULT '' COMMENT '页面帮助内容',
  `menu_order` tinyint(4) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `menu_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20003 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='菜单表-10个递增';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_menu_copy2` (
  `menu_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '菜单id',
  `menu_parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '菜单id',
  `menu_name` varchar(20) NOT NULL COMMENT '菜单名称',
  `menu_icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标class',
  `rights_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
  `menu_url_ctl` varchar(30) NOT NULL DEFAULT '' COMMENT '控制器名称',
  `menu_url_met` varchar(20) NOT NULL DEFAULT '' COMMENT '控制器方法',
  `menu_url_parem` varchar(50) NOT NULL DEFAULT '' COMMENT 'url参数',
  `menu_url_note` varchar(255) NOT NULL DEFAULT '' COMMENT '页面帮助内容',
  `menu_order` tinyint(4) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `menu_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20003 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='菜单表-10个递增';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_rights_base` (
  `rights_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限Id',
  `rights_name` varchar(20) NOT NULL DEFAULT '' COMMENT '权限名称',
  `rights_parent_id` smallint(4) unsigned NOT NULL COMMENT '权限父Id',
  `rights_remark` varchar(255) NOT NULL COMMENT '备注',
  `rights_order` smallint(4) NOT NULL DEFAULT '50' COMMENT '排序',
  PRIMARY KEY (`rights_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16041 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限表 ';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_rights_data_type` (
  `rights_data_type_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限数据类型id',
  `rights_data_type_name` varchar(50) NOT NULL COMMENT '权限数据类型名称',
  `rights_data_type_primary_key` text NOT NULL COMMENT '数据主键',
  PRIMARY KEY (`rights_data_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='数据权限类型表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_rights_group` (
  `rights_group_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限组id',
  `rights_group_name` varchar(50) NOT NULL COMMENT '权限组名称',
  `rights_group_rights_ids` text NOT NULL COMMENT '权限列表',
  `rights_group_add_time` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`rights_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限组表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_test` (
  `config_key` varchar(50) NOT NULL COMMENT '数组下标',
  `config_value` text NOT NULL COMMENT '数组值',
  `config_type` varchar(50) NOT NULL,
  `config_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态值，1可能，0不可用',
  `config_comment` text NOT NULL,
  `config_ct3213` text NOT NULL,
  `config_datatype` enum('string','json','number') NOT NULL DEFAULT 'string' COMMENT '数据类型',
  PRIMARY KEY (`config_key`),
  KEY `index` (`config_key`,`config_type`) COMMENT '(null)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站配置表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_user_base` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_account` varchar(50) NOT NULL COMMENT '用户帐号',
  `user_password` char(32) NOT NULL COMMENT '密码：使用用户中心-此处废弃',
  `user_key` varchar(32) NOT NULL COMMENT '用户Key',
  `user_delete` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否被封禁，0：未封禁，1：封禁',
  `rights_group_id` smallint(4) NOT NULL COMMENT '用户权限组id',
  `sub_site_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属子站， 0总站',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_account` (`user_account`) COMMENT '(null)'
) ENGINE=InnoDB AUTO_INCREMENT=174964 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户基础信息表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_user_info` (
  `user_id` mediumint(8) NOT NULL COMMENT '用户id',
  `user_nickname` varchar(100) NOT NULL COMMENT '昵称',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户数据库主机表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_user_rights_data` (
  `user_rights_data_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL COMMENT '用户id',
  `user_rights_data_value` mediumint(8) NOT NULL COMMENT '主键值',
  `rights_data_type_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '权限类型Id',
  PRIMARY KEY (`user_rights_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户数据权限表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_web_config` (
  `config_key` varchar(50) NOT NULL COMMENT '数组下标',
  `config_value` text NOT NULL COMMENT '数组值',
  `config_type` varchar(50) NOT NULL,
  `config_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态值，1可能，0不可用',
  `config_comment` text NOT NULL,
  `config_datatype` enum('string','json','number') NOT NULL DEFAULT 'string' COMMENT '数据类型',
  PRIMARY KEY (`config_key`),
  KEY `index` (`config_key`,`config_type`) COMMENT '(null)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站配置表';
/*!40101 SET character_set_client = utf8 */;


CREATE TABLE `yf_admin_web_test` (
  `config_key` varchar(50) NOT NULL COMMENT '数组下标',
  `config_value` text NOT NULL COMMENT '数组值',
  `config_type` varchar(50) NOT NULL,
  `config_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态值，1可能，0不可用',
  `config_comment` text NOT NULL,
  `config_ct3213` text NOT NULL,
  `config_datatype` enum('string','json','number') NOT NULL DEFAULT 'string' COMMENT '数据类型',
  PRIMARY KEY (`config_key`),
  KEY `index` (`config_key`,`config_type`) COMMENT '(null)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站配置表'