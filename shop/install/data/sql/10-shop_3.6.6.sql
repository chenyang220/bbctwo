-- 后台举报表设置--
CREATE TABLE yf_explore_report (
report_id int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
explore_id int(11) NOT NULL COMMENT '被举报的心得ID',
user_id int(11) NOT NULL COMMENT '举报会员ID',
to_user_id int(11) NOT NULL COMMENT '被举报会员ID',
report_time int(11) NOT NULL COMMENT '举报时间',
report_reason_id int(11) NOT NULL COMMENT '举报原因ID',
report_reason varchar(255) NOT NULL COMMENT '举报原因内容',
report_status tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0-待处理 1-审核通过 2-审核不通过',
report_handle varchar(255) NOT NULL COMMENT '举报处理备注',
report_handle_time int(11) NOT NULL COMMENT '举报处理时间',
PRIMARY KEY (report_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='举报表';

-- 举报原因表--
CREATE TABLE yf_explore_report_reason (
explore_report_reason_id int(10) NOT NULL AUTO_INCREMENT,
explore_report_reason_content varchar(255) NOT NULL COMMENT '举报理由内容',
explore_report_reason_sort int(3) NOT NULL DEFAULT '225' COMMENT '举报理由排序',
PRIMARY KEY (explore_report_reason_id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='举报原因表';

-- 举报原因表插入信息--
INSERT INTO `yf_explore_report_reason` (`explore_report_reason_id`, `explore_report_reason_content`, `explore_report_reason_sort`) VALUES ('1', '色情、政治等敏感信息', '1');
INSERT INTO `yf_explore_report_reason` (`explore_report_reason_id`, `explore_report_reason_content`, `explore_report_reason_sort`) VALUES ('2', '广告信息或骚扰用户', '2');
INSERT INTO `yf_explore_report_reason` (`explore_report_reason_id`, `explore_report_reason_content`, `explore_report_reason_sort`) VALUES ('3', '侵权盗用行为', '3');

-- 心得信息表 --
CREATE TABLE `yf_explore_base` (
  `explore_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `user_account` varchar(100) NOT NULL COMMENT '会员名称',
  `explore_title` text NOT NULL COMMENT '标题',
  `explore_content` text NOT NULL COMMENT '内容',
  `explore_create_time` int(25) NOT NULL COMMENT '添加时间',
  `explore_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态,  0-正常 1-下架',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除  0-未删除 1-已删除',
  `explore_like_count` int(11) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `explore_like_user` varchar(255) NOT NULL DEFAULT '' COMMENT '点赞会员',
  `explore_lable` varchar(255) DEFAULT NULL COMMENT '标签',
  PRIMARY KEY (`explore_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='心得信息表';

-- 心得图片表 --
CREATE TABLE `yf_explore_images` (
  `images_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片id',
  `explore_id` int(10) unsigned NOT NULL COMMENT '心得id',
  `images_url` varchar(255) NOT NULL COMMENT '商品图片',
  `images_displayorder` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `images_create_time` int(25) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`images_id`),
  KEY `explore_id` (`explore_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='心得图片表';

-- 心得图片商品表 --
CREATE TABLE `yf_explore_images_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `images_id` int(10) unsigned NOT NULL  COMMENT '图片id',
  `brand_id` int(10) unsigned NOT NULL COMMENT '品牌id',
  `goods_common_id` int(10) unsigned NOT NULL COMMENT '商品common_id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='心得图片商品表';

-- 心得标签表 --
CREATE TABLE `yf_explore_lable` (
  `lable_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `lable_content` text NOT NULL COMMENT '标签内容',
  `lable_create_time` int(25) NOT NULL COMMENT '添加时间',
  `lable_used_count` int(11) NOT NULL DEFAULT '0' COMMENT '标签使用次数',
  `lable_month_count` int(11) NOT NULL DEFAULT '0' COMMENT '标签最近一个使用次数',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除  0-未删除 1-已删除',
  PRIMARY KEY (`lable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='心得标签表';

INSERT INTO `yf_explore_lable` (`lable_id`, `user_id`, `lable_content`) VALUES ('1', '0', '新人签到');
INSERT INTO `yf_explore_lable` (`lable_id`, `user_id`, `lable_content`) VALUES ('2', '0', '好货推荐');
INSERT INTO `yf_explore_lable` (`lable_id`, `user_id`, `lable_content`) VALUES ('3', '0', '皮你一下');
INSERT INTO `yf_explore_lable` (`lable_id`, `user_id`, `lable_content`) VALUES ('4', '0', '我的神器');
INSERT INTO `yf_explore_lable` (`lable_id`, `user_id`, `lable_content`) VALUES ('5', '0', '今年超火');

-- 用户关注表 --
CREATE TABLE `yf_explore_user` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_account` varchar(50) NOT NULL COMMENT '用户帐号',
  `user_sign` varchar(50) NOT NULL COMMENT '个性签名',
  `user_follow_count` int(11) NOT NULL COMMENT '我关注的人数',
  `user_follow_id` varchar(255) NOT NULL DEFAULT '' COMMENT '我关注的人的user_id',
  `user_fans_count` int(11) NOT NULL COMMENT '关注我的人数',
  `user_fans_id` varchar(255) NOT NULL DEFAULT '' COMMENT '关注我的人的user_id',
  `user_like` int(11) NOT NULL COMMENT '被赞次数（仅心得）',
  `explore_base_count` int(11) NOT NULL COMMENT '发布有效心得数量（仅心得）',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户信息表';


--心得评论表--
CREATE TABLE `yf_explore_comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `user_account` varchar(100) NOT NULL COMMENT '会员名称',
  `explore_id` int(11) NOT NULL COMMENT '心得ID',
  `comment_content` varchar(255) NOT NULL COMMENT '评论内容',
  `comment_addtime` int(25) NOT NULL COMMENT '添加时间',
  `comment_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0正常 1屏蔽 2删除',
  `comment_like_count` int(11) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `comment_like_user` varchar(255) NOT NULL DEFAULT '' COMMENT '点赞会员',
  `is_author` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否作者 0-非作者 1-是作者',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='心得评论表';

--心得评论回复表--
CREATE TABLE `yf_explore_reply` (
  `reply_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `user_account` varchar(100) NOT NULL COMMENT '会员名称',
  `explore_id` int(11) NOT NULL COMMENT '心得ID',
  `commect_id` int(11) NOT NULL COMMENT '评论ID',
  `reply_content` varchar(255) NOT NULL COMMENT '回复内容',
  `reply_addtime` int(25) NOT NULL COMMENT '添加时间',
  `reply_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0正常 1屏蔽 2删除',
  `reply_like_count` int(11) NOT NULL DEFAULT '0' COMMENT '点赞数',
  `reply_like_user` varchar(255) NOT NULL DEFAULT '' COMMENT '点赞会员',
  `is_author` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否作者 0-非作者 1-是作者',
  `to_reply_id` int(11) NOT NULL COMMENT '被回复的回复id，0为对动态信息进行评论',
  `to_reply_user_account` varchar(100) NOT NULL COMMENT '回复的回复者',
  `to_reply_user_id` varchar(100) NOT NULL COMMENT '回复的回复者id',
 `to_reply_is_author` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否作者 0-非作者 1-是作者',
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='心得评论表';

--消息通知表--
CREATE TABLE `yf_explore_message` (
  `message_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `message_user_id` int(10) NOT NULL COMMENT '消息接收者id',
  `message_user_name` varchar(50) NOT NULL COMMENT '消息接收者',
  `message_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '消息类型买家1点赞心得 2评论 3通知 4新增粉丝 5点赞评论 6点赞回复',
  `message_title` varchar(100) NOT NULL COMMENT '消息标题',
  `message_content` text NOT NULL COMMENT '消息内容',
  `active_user_id` int(10) NOT NULL COMMENT '操作用户id(给你点赞的用户id，给你评论的用户id，关注你的用户id)',
 `active_id` int(10) NOT NULL COMMENT '操作对象id(点赞的心得id，评论的id，关注你的用户id，举报id，点赞的评论id，点赞的回复id)',
 `reply_type`  tinyint(1) NOT NULL DEFAULT '0' COMMENT '当消息类型为2评论时，需要判断:0评论心得，1回复回复，2回复评论 ',
  `message_islook` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否读取0未1读取',
`message_create_time` int(25) NOT NULL COMMENT '消息创建时间',
  `message_isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0正常1删除',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统消息表';