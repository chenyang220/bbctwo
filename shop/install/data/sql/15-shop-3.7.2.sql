ALTER TABLE `yf_explore_images` ADD COLUMN `poster_image` varchar(255) NOT NULL COMMENT '视频第一帧';
-- 社区心得
ALTER TABLE yf_explore_base MODIFY COLUMN `explore_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态,  0-正常 1-下架 2-草稿 3-待审核 4-审核未通过';


-- 直播申请表
DROP TABLE IF EXISTS `yf_live_application`;
CREATE TABLE `yf_live_application` (
  `live_application_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `shop_id` int(10) NOT NULL COMMENT '商家店铺id',
  `live_length` int(10) NOT NULL COMMENT '直播期限',
  `application_time` int(10) NOT NULL COMMENT '申请时间',
  `application_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（1：待审核；2：通过；3：失败）',
  `application_info` varchar(255) NOT NULL COMMENT '审核信息',
  `application_status_time` int(10) NOT NULL COMMENT '审核时间',
  `application_end_time` int(10) NOT NULL COMMENT '直播结束时间',
  `times` int(10) NOT NULL COMMENT '申请次数',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0- 未删除  1-删除',
  PRIMARY KEY (`live_application_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='直播申请表';


ALTER TABLE yf_live_goods ADD COLUMN `room_name` varchar(255) NOT NULL COMMENT '房间名';
ALTER TABLE yf_live_goods ADD COLUMN `room_img` varchar(255) NOT NULL COMMENT '房间封面';
