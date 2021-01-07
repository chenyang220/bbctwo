-- 资讯
alter table yf_information_news modify column `author_type` enum('1','2','3') DEFAULT '1' COMMENT '发布者类型 1用户 2商家 3平台';
alter table yf_information_news modify column `auditing` enum('1','2','3') DEFAULT '3' COMMENT '审核 1通过 2不通过 3待审核';
alter table yf_information_news modify column `complaint` enum('1','2','3') DEFAULT '1' COMMENT '文章是否投诉 1 否 2 是 3通知商家';
