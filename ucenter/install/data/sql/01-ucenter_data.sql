-- Adminer 4.3.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
BEGIN;
INSERT INTO `ucenter_message_template` VALUES ('2', 'verification', '绑定验证', '请激活您在[weburl_name]账户', '您绑定邮箱在[weburl_name]账户,验证码为[yzm]', '1', '1', '1', '1', '您在[weburl_name]账户上正进行绑定,验证码为[yzm]。', '您正绑定手机在[weburl_name]账户上,验证码为[yzm]。', '1', '0', '0', '0'), ('3', 'Complaints_of_goods', '商品被投诉', '[weburl_name]提醒：您售出的商品被投诉，等待商家申诉。', '您好，[weburl_name]提醒：您售出的商品被投诉，等待商家申诉。投诉单编号：[order_id]，请尽快处理。', '2', '1', '1', '1', '您好，[weburl_name]提醒：您售出的商品被投诉，等待商家申诉。投诉单编号：[order_id]，请尽快处理。', '您好，[weburl_name]提醒：您售出的商品被投诉，等待商家申诉。投诉单编号：[order_id]，请尽快处理。', '0', '0', '0', '3'), ('4', 'Voucher', '优惠券到账', '优惠券到账', '恭喜您获得[name]优惠券，记得在[end]前使用哦~', '1', '1', '1', '1', '恭喜您获得[name]优惠券，记得在[end]前使用哦~', '恭喜您获得[name]优惠券，记得在[end]前使用哦~', '0', '0', '1', '2'), ('5', 'place_your_order', '下单通知', '下单通知', '您的会员在[date]提交了订单[order_id]，请尽快发货。', '2', '1', '0', '1', '您的会员在[date]提交了订单[order_id]，请尽快发货。', '您的会员在[date]提交了订单[order_id]，请尽快发货。', '1', '1', '1', '0'), ('6', 'ordor_complete_shipping', '发货通知', '发货通知', '您的订单[order_id]于[date]时,已发货啦~', '1', '1', '0', '1', '您的订单[order_id]于[date]时,已发货啦~', '您的订单[order_id]于[date]时,已发货啦~', '1', '1', '0', '1'), ('10', 'welcome', '欢迎信息', '感谢您注册[weburl_name]', '感谢您注册[weburl_name]，欢迎您。', '1', '1', '0', '1', '感谢您注册[weburl_name]，欢迎您。', '感谢您注册[weburl_name]，欢迎您。', '1', '1', '0', '0'), ('11', 'Lift verification', '解除验证', '您在[weburl_name]账户进行解除绑定', '您正在[weburl_name]账户上进行解除绑定操作,验证码为[yzm]。', '0', '0', '0', '0', '您在[weburl_name]账户上正进行解除绑定,验证码为[yzm]。', '您正在[weburl_name]账户上进行解除绑定操作,验证码为[yzm]。', '0', '0', '0', '0');
COMMIT;

BEGIN;
INSERT INTO `ucenter_user_grade` VALUES ('1', '注册会员', '1', '<p>1.可以享受注册会员所能购买的产品及服务</p>\r\n<p>2.享受售后服务（退货、换货、维修）运费优惠</p>', 'image/grade/icon1.png_big.png', '', '0', '0', '0.0', '2016-06-21 15:51:12'), ('2', '铜牌会员', '5', '<p>2.可以享受铜牌会员所能购买的产品及服务</p>\r\n<p>3.享受售后服务（退货、换货、维修）运费优惠</p>', 'image/grade/icon2.png_big.png', '', '0', '0', '99.9', '2016-06-21 15:52:13'), ('3', '银牌会员', '2000', '<p>2.可以享受银牌会员所能购买的产品及服务</p>\r\n<p>3.享受售后服务（退货、换货、维修）运费优惠</p>', 'image/grade/icon3.png_big.png', '', '1', '1000', '99.8', '2016-06-06 15:52:16'), ('4', '金牌会员', '10000', '<p>2.可以享受金牌会员所能购买的产品及服务</p>\r\n<p>3.享受售后服务（退货、换货、维修）运费优惠</p>', 'image/grade/icon4.png_big.png', '', '1', '4000', '98.0', '2016-06-06 15:52:20'), ('5', '钻石会员', '30000', '<p>\r\n	2.可以享受钻石会员所能购买的产品及服务\r\n</p>\r\n<p>\r\n	3.享受售后服务（退货、换货、维修）运费优惠\r\n</p>', 'image/grade/icon5.png_big.png', '', '1', '10000', '97.0', '2016-06-06 15:52:24');
COMMIT;

BEGIN;
INSERT INTO `ucenter_user_info_detail` VALUES ('admin', 'admin', '0', '0', null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, '0', '0', '0', null, null, null, '0', '0', null, null, null, null, null, null, '', null, null, null, '0', '0', '0', '0', '0', '0', '');
COMMIT;

INSERT INTO `ucenter_base_app` (`app_id`, `app_name`, `app_type`, `app_seq`, `app_key`, `app_ip_list`, `app_url`, `app_admin_url`, `app_url_recharge`, `app_url_order`, `app_logo`, `app_hosts`, `return_fields`, `app_status`) VALUES
(101,	'ERP系统',	2,	0,	'',	'',	'',	'',	'',	'',	'',	'',	'',	0),
(102,	'商城系统',	2,	0,	'',	'',	'',	'',	'',	'',	'',	'',	'',	1),
(103,	'ImBuilder',	2,	0,	'',	'',	'',	'',	'',	'',	'',	'',	'',	1),
(104,	'用户中心',	0,	0,	'',	'',	'',	'',	'',	'',	'',	'',	'',	1),
(105,	'网付宝',	0,	0,	'',	'',	'',	'',	'',	'',	'',	'',	'',	1);

-- 2017-05-08 06:17:35
INSERT INTO  `ucenter_user_info` (
`user_id` ,
`user_name` ,
`password` ,
`user_state` ,
`action_time` ,
`action_ip` ,
`session_id`
)
VALUES (
10001 ,  'admin',  '96e79218965eb72c92a549dd5a330112',  '1',  '0', NULL , NULL
);

BEGIN;
INSERT INTO `ucenter_web_config` VALUES ('23123', 'faf', 'msg_tpl', 1, 'fasf', 'string'),('article_description', '5', 'seo', 1, '', 'string'),('article_description_content', '5', 'seo', 1, '', 'string'),('article_keyword', '软沙发', 'seo', 1, '', 'string'),('article_keyword_content', '7', 'seo', 1, '', 'string'),('article_title', '{sitename}-文章{name}', 'seo', 1, '', 'string'),('article_title_content', '7{sitename}', 'seo', 1, '', 'string'),('authenticate', 'faf', 'msg_tpl', 1, '身份验证通知', 'string'),('baseurl', 'demo.bbc-builder.com', 'main', 1, '', 'string'),('bind_email', 'faf', 'msg_tpl', 1, '邮箱验证通知', 'string'),('body_skin', 'image/default/bg.jpg', 'main', 1, '', 'string'),('brand_description', '2313', 'seo', 1, '', 'string'),('brand_description_content', 'trsegt', 'seo', 1, '', 'string'),('brand_keyword', '23123', 'seo', 1, '', 'string'),('brand_keyword_content', '123123', 'seo', 1, '', 'string'),('brand_title', 'j{sitename}23123', 'seo', 1, '', 'string'),('brand_title_content', 'gfnjgmjn', 'seo', 1, '', 'string'),('cacheTime', '1000', 'main', 1, '', 'string'),('captcha_status_goodsqa', '1', 'dumps', 1, '', 'number'),('captcha_status_login', '1', 'dump', 1, '', 'number'),('captcha_status_register', '1', 'dump', 1, '', 'number'),('category_description', '分类', 'seo', 1, '', 'string'),('category_keyword', '分类', 'seo', 1, '', 'string'),('category_title', '商品分类{name}{sitename}', 'seo', 1, '', 'string'),('closecon', '', 'main', 1, '', 'string'),('closed_reason', '11111', 'site', 1, '', 'string'),('closetype', '0', 'main', 1, '', 'string'),('complain_datetime', '2', 'complain', 1, '', 'string'),('consult_header_text', '<p>11111111111111</p>', 'consult', 1, '', 'string'),('copyright', 'BBCBuilder版权所有,正版购买地址:  <a href=\"http://www.bbc-builder.com\">http://www.bbc-builder.com</a>  \r\n<br />Powered by BBCbuilder V2.6.1\r\n', 'site', 1, '', 'string'),('current_db_version', '37965', 'site', 1, '', 'string'),('current_version', '1.0.1', 'site', 1, '', 'string'),('date_format', 'Y-m-d', 'site', 1, '', 'string'),('description', '网上超市，最经济实惠的网上购物商城，用鼠标逛超市，不用排队，方便实惠送上门，网上购物新生活。', 'seo', 1, '', 'string'),('domaincity', '0', 'main', 1, '', 'string'),('domain_length', '3-12', 'domain', 1, '', 'string'),('domain_modify_frequency', '1', 'domain', 1, '', 'number'),('drp_is_open', '0', 'main', 1, '', 'string'),('email', '250314853@qq.com', 'main', 1, '', 'string'),('email_addr', 'yelongsai@163.com', 'email', 1, '', 'string'),('email_host', 'smtp.163.com', 'email', 1, '', 'string'),('email_id', 'ye', 'email', 1, '', 'string'),('email_pass', '325604793069', 'email', 1, '', 'string'),('email_port', '25', 'email', 1, '', 'number'),('enable_gzip', '0', 'main', 1, '', 'string'),('enable_tranl', '1', 'main', 1, '', 'string'),('fafaf', '身份验证通知', 'msg_tpl', 1, '【{$site_name}】您于{$send_time}提交账户安全验证，验证码是：{$verify_code}。', 'string'),('goods_verify_flag', '1', 'goods', 1, '//商品是否需要审核', 'string'),('grade_evaluate', '50', 'grade', 1, '订单评论获取成长值', 'number'),('grade_login', '12', 'grade', 1, '登陆获取成长值', 'number'),('grade_order', '800', 'grade', 1, '订单评论获取成长值上限', 'number'),('grade_recharge', '100', 'grade', 1, '订单每多少获取多少成长值', 'number'),('groupbuy_allow', '1', 'promotion', 1, '是否开启团购', 'number'),('groupbuy_price', '100', 'groupbuy', 1, '', 'number'),('groupbuy_review_day', '0', 'groupbuy', 1, '', 'number'),('guest_comment', '1', 'dumps', 1, '', 'string'),('hot_commen', '31,42,47,34,35,25,44,26,27,46', 'home', 1, '', 'string'),('hot_sell', '42,37,28,41,30,31,42,47,34,35', 'home', 1, '', 'string'),('icp_number', '5.4435234534253', 'site', 1, '', 'string'),('image_allow_ext', 'gif,jpg,jpeg,bmp,png,swf', 'upload', 1, '图片扩展名，用于判断上传图片是否为后台允许，多个后缀名间请用半角逗号 \",\" 隔开。', 'string'),('image_max_filesize', '2000', 'upload', 1, '图片文件大小', 'number'),('image_storage_type', '', 'upload', 1, '图片存放类型-程序内置较优方式', 'string'),('index_catid', '1000,1002,1001,1003,1005', 'home', 1, '', 'string'),('index_liandong1_image', '', 'index_liandong', 1, '首页联动小图1', 'string'),('index_liandong2_image', '', 'index_liandong', 1, '首页联动小图2', 'string'),('index_liandong_url1', '', 'index_liandong', 1, '首页联动小图url1', 'string'),('index_liandong_url2', '', 'index_liandong', 1, '首页联动小图url2', 'string'),('index_live_link1', '', 'index_slider', 1, '首页轮播url1', 'string'),('index_live_link2', '', 'index_slider', 1, '首页轮播url2', 'string'),('index_live_link3', '', 'index_slider', 1, '首页轮播url3', 'string'),('index_live_link4', '', 'index_slider', 1, '首页轮播url4', 'string'),('index_live_link5', '', 'index_slider', 1, '首页轮播url5', 'string'),('index_newsid', '1', 'home', 1, '', 'string'),('index_slider1_image', '', 'index_slider', 1, '首页轮播1', 'string'),('index_slider2_image', '', 'index_slider', 1, '首页轮播2', 'string'),('index_slider3_image', '', 'index_slider', 1, '首页轮播3', 'string'),('index_slider4_image', '', 'index_slider', 1, '首页轮播4', 'string'),('index_slider5_image', '', 'index_slider', 1, '首页轮播5', 'string'),('is_modify', '1', 'domain', 1, '', 'number'),('join_live_link1', '', 'join_slider', 1, '入驻轮播url1', 'string'),('join_live_link2', '', 'join_slider', 1, '入驻轮播url2', 'string'),('join_slider1_image', '', 'join_slider', 1, '入驻轮播1', 'string'),('join_slider2_image', '', 'join_slider', 1, '入驻轮播2', 'string'),('join_tip', 'fdsafasdaddsadad', 'join_slider', 1, '贴心提示', 'string'),('jsd', 'JSD-', 'bill_format', 1, '//结算单', 'string'),('keyword', '网上超市，网上商城，网络购物，进口食品，美容护理，母婴玩具，厨房清洁用品，家用电器，手机数码，电脑软件办公用品，家居生活，服饰内衣，营养保健，钟表珠宝，饰品箱包，汽车生活，图书音像，礼品卡，药品，医疗器械，隐形眼镜等，1号店。', 'seo', 1, '', 'string'),('keywords', '雷山兄弟扛年货回家，年货下单就到家', 'main', 1, '', 'string'),('kuaidi100_app_id', 'kuaidi100fadfda', 'kuaidi100', 1, '', 'string'),('kuaidi100_app_key', 'kuaidi100_statufaf', 'kuaidi100', 1, '', 'string'),('kuaidi100_status', '1', 'kuaidi100', 1, '', 'string'),('kuaidiniao_app_key', 'kuaidiniaofafdaf', 'kuaidiniao', 1, '', 'string'),('kuaidiniao_express', '[\"QFKD\",\"ZTO\",\"DBL\",\"ZENY\"]', 'kuaidiniao', 1, '', 'json'),('kuaidiniao_e_business_id', 'kuaidiniao_e_business_id', 'kuaidiniao', 1, '', 'string'),('kuaidiniao_status', '1', 'kuaidiniao', 1, '', 'string'),('language_id', 'zh_CN', 'site', 1, '', 'string'),('like', '25,44,26,27,46', 'home', 1, '', 'string'),('list_catid', '1', 'home', 1, '', 'string'),('live_link1', '', 'slider', 1, '轮播轮播', 'string'),('live_link2', '', 'slider', 1, '轮播地址', 'string'),('live_link3', '', 'slider', 1, '轮播地址', 'string'),('live_link4', '', 'slider', 1, '轮播地址', 'string'),('logistics_channel', 'kuaidi100', 'logistics', 1, '', 'string'),('logo', '', 'main', 1, '', 'string'),('mlogo', '', 'main', 1, '', 'string'),('modify_mobile', 'faf', 'msg_tpl', 1, '手机验证通知', 'string'),('monetary_unit', '￥', 'site', 1, '', 'string'),('msg_tpl1', '21212', 'msg_tpl', 1, '212', 'string'),('new_pro', '48,32,23,25,28', 'home', 1, '', 'string'),('openstatistics', '1', 'main', 1, '', 'string'),('opensuburl', '0', 'seo', 1, '', 'string'),('order_id_prefix_format', 'DD-', 'bill_format', 1, '//自定义订单前缀', 'string'),('owntel', '021-64966875', 'main', 1, '', 'string'),('photo_font', '\r\nArial,宋体,微软雅黑', 'photo', 1, '水印字体', 'string'),('photo_goods_logo', '', 'photo', 1, '商品默认图片', 'string'),('photo_shop_head_logo', '', 'photo', 1, '店铺默认头像', 'string'),('photo_shop_logo', '', 'photo', 1, '店铺默认标志', 'string'),('photo_user_logo', '', 'photo', 1, '会员默认头像', 'string'),('Plugin_Cron', '0', 'plugin', 1, '', 'string'),('Plugin_Log', '1', 'plugin', 1, '', 'string'),('Plugin_Perm', '1', 'plugin', 1, '', 'string'),('Plugin_Xhprof', '0', 'plugin', 1, '', 'string'),('pointprod_isuse', '1', 'promotion', 1, '积分兑换是否开', 'number'),('pointshop_isuse', '1', 'promotion', 1, '积分中心是否开启', 'number'),('points_avatar', '50', 'points', 1, '', 'string'),('points_checkin', '5', 'points', 1, '', 'string'),('points_consume', '100', 'points', 1, '', 'string'),('points_email', '50', 'points', 1, '', 'string'),('points_evaluate', '21', 'points', 1, '商品评论获取积分', 'string'),('points_evaluate_good', '50', 'points', 1, '', 'string'),('points_evaluate_image', '10', 'points', 1, '', 'string'),('points_login', '15', 'points', 1, '登陆获取积分', 'string'),('points_mobile', '50', 'points', 1, '', 'string'),('points_order', '800', 'points', 1, '订单获取积分上限', 'string'),('points_recharge', '100', 'points', 1, '订单每多少获取多少积分', 'string'),('points_reg', '50', 'points', 1, '注册获取积分', 'string'),('point_description', '收到公司的', 'seo', 1, '', 'string'),('point_description_content', '特温特', 'seo', 1, '', 'string'),('point_keyword', ' nfbgnjgf', 'seo', 1, '', 'string'),('point_keyword_content', '热热', 'seo', 1, '', 'string'),('point_title', 'e{sitename}', 'seo', 1, '', 'string'),('point_title_content', 'g{sitename}', 'seo', 1, '', 'string'),('product_description', '商品', 'seo', 1, '', 'string'),('product_keyword', '商品', 'seo', 1, '', 'string'),('product_title', '商品{sitename}{name}', 'seo', 1, '', 'string'),('promotion_allow', '1', 'promotion', 1, '促销活动是否开启', 'number'),('promotion_discount_price', '12', 'discount', 1, '', 'number'),('promotion_increase_price', '20', 'increase', 1, '', 'number'),('promotion_mansong_price', '15', 'mansong', 1, '', 'number'),('promotion_voucher_buyertimes_limit', '10', 'voucher', 1, '', 'number'),('promotion_voucher_price', '21', 'voucher', 1, '', 'number'),('promotion_voucher_storetimes_limit', '2', 'voucher', 1, '', 'number'),('protection_service_status', '1', 'operation', 1, '', 'string'),('qanggou', '48', 'home', 1, '', 'string'),('qq_app_id', '1', 'connect', 1, '', 'string'),('qq_app_key', '1', 'connect', 1, '', 'string'),('qq_status', '1', 'connect', 1, '', 'string'),('regname', 'register.php', 'main', 1, '', 'string'),('reg_checkcode', '3', 'register', 1, '', 'number'),('reg_lowercase', '0', 'register', 1, '注册-小写字母', 'number'),('reg_number', '0', 'register', 1, '注册-数字', 'number'),('reg_protocol', '<p>\r\n    特别提醒用户认真阅读本《用户服务协议》(下称《协议》) 中各条款。除非您接受本《协议》条款，否则您无权使用本网站提供的相关服务。您的使用行为将视为对本《协议》的接受，并同意接受本《协议》各项条款的约束。 <br />\r\n    <br />\r\n    <strong>一、定义</strong>\r\n</p>\r\n<ol>\r\n    <li>\r\n        \"用户\"指符合本协议所规定的条件，同意遵守本网站各种规则、条款（包括但不限于本协议），并使用本网站的个人或机构。\r\n    </li>\r\n    <li>\r\n        \"卖家\"是指在本网站上出售物品的用户。\"买家\"是指在本网站购买物品的用户。\r\n    </li>\r\n    <li>\r\n        \"成交\"指买家根据卖家所刊登的交易要求，在特定时间内提出最优的交易条件，因而取得依其提出的条件购买该交易物品的权利。\r\n    </li>\r\n</ol>\r\n<p>\r\n    <br />\r\n    <br />\r\n    <strong>二、用户资格</strong><br />\r\n    <br />\r\n    只有符合下列条件之一的人员或实体才能申请成为本网站用户，可以使用本网站的服务。\r\n</p>\r\n<ol>\r\n    <li>\r\n        年满十八岁，并具有民事权利能力和民事行为能力的自然人；\r\n    </li>\r\n    <li>\r\n        未满十八岁，但监护人（包括但不仅限于父母）予以书面同意的自然人；\r\n    </li>\r\n    <li>\r\n        根据中国法律或设立地法律、法规和/或规章成立并合法存在的公司、企事业单位、社团组织和其他组织。\r\n    </li>\r\n</ol>\r\n<p>\r\n    <br />\r\n    无民事行为能力人、限制民事行为能力人以及无经营或特定经营资格的组织不当注册为本网站用户或超过其民事权利或行为能力范围从事交易的，其与本网站之间的协议自始无效，本网站一经发现，有权立即注销该用户，并追究其使用本网站\"服务\"的一切法律责任。<br />\r\n    <br />\r\n    <strong>三.用户的权利和义务</strong>\r\n</p>\r\n<ol>\r\n    <li>\r\n        用户有权根据本协议的规定及本网站发布的相关规则，利用本网站网上交易平台登录物品、发布交易信息、查询物品信息、购买物品、与其他用户订立物品买卖合同、在本网站社区发帖、参加本网站的有关活动及有权享受本网站提供的其他的有关资讯及信息服务。\r\n    </li>\r\n    <li>\r\n        用户有权根据需要更改密码和交易密码。用户应对以该用户名进行的所有活动和事件负全部责任。\r\n    </li>\r\n    <li>\r\n        用户有义务确保向本网站提供的任何资料、注册信息真实准确，包括但不限于真实姓名、身份证号、联系电话、地址、邮政编码等。保证本网站及其他用户可以通过上述联系方式与自己进行联系。同时，用户也有义务在相关资料实际变更时及时更新有关注册资料。\r\n    </li>\r\n    <li>\r\n        用户不得以任何形式擅自转让或授权他人使用自己在本网站的用户账号。\r\n    </li>\r\n    <li>\r\n        用户有义务确保在本网站网上交易平台上登录物品、发布的交易信息真实、准确，无误导性。\r\n    </li>\r\n    <li>\r\n        用户不得在本网站网上交易平台买卖国家禁止销售的或限制销售的物品、不得买卖侵犯他人知识产权或其他合法权益的物品，也不得买卖违背社会公共利益或公共道德的物品。\r\n    </li>\r\n    <li>\r\n        用户不得在本网站发布各类违法或违规信息。包括但不限于物品信息、交易信息、社区帖子、物品留言，店铺留言，评价内容等。\r\n    </li>\r\n    <li>\r\n        用户在本网站交易中应当遵守诚实信用原则，不得以干预或操纵物品价格等不正当竞争方式扰乱网上交易秩序，不得从事与网上交易无关的不当行为，不得在交易平台上发布任何违法信息。\r\n    </li>\r\n    <li>\r\n        用户不应采取不正当手段（包括但不限于虚假交易、互换好评等方式）提高自身或他人信用度，或采用不正当手段恶意评价其他用户，降低其他用户信用度。\r\n    </li>\r\n    <li>\r\n        用户承诺自己在使用本网站网上交易平台实施的所有行为遵守国家法律、法规和本网站的相关规定以及各种社会公共利益或公共道德。对于任何法律后果的发生，用户将以自己的名义独立承担所有相应的法律责任。\r\n    </li>\r\n    <li>\r\n        用户在本网站网上交易过程中如与其他用户因交易产生纠纷，可以请求本网站从中予以协调。用户如发现其他用户有违法或违反本协议的行为，可以向本网站举报。如用户因网上交易与其他用户产生诉讼的，用户有权通过司法部门要求本网站提供相关资料。\r\n    </li>\r\n    <li>\r\n        用户应自行承担因交易产生的相关费用，并依法纳税。\r\n    </li>\r\n    <li>\r\n        未经本网站书面允许，用户不得将本网站资料以及在交易平台上所展示的任何信息以复制、修改、翻译等形式制作衍生作品、分发或公开展示。\r\n    </li>\r\n    <li>\r\n        用户同意接收来自本网站的信息，包括但不限于活动信息、交易信息、促销信息等。\r\n    </li>\r\n</ol>\r\n<p>\r\n    <br />\r\n    <br />\r\n    <strong>四、 本网站的权利和义务</strong>\r\n</p>\r\n<ol>\r\n    <li>\r\n        本网站不是传统意义上的\"拍卖商\"，仅为用户提供一个信息交流、进行物品买卖的平台，充当买卖双方之间的交流媒介，而非买主或卖主的代理商、合伙  人、雇员或雇主等经营关系人。公布在本网站上的交易物品是用户自行上传进行交易的物品，并非本网站所有。对于用户刊登物品、提供的信息或参与竞标的过程，  本网站均不加以监视或控制，亦不介入物品的交易过程，包括运送、付款、退款、瑕疵担保及其它交易事项，且不承担因交易物品存在品质、权利上的瑕疵以及交易  方履行交易协议的能力而产生的任何责任，对于出现在拍卖上的物品品质、安全性或合法性，本网站均不予保证。\r\n    </li>\r\n    <li>\r\n        本网站有义务在现有技术水平的基础上努力确保整个网上交易平台的正常运行，尽力避免服务中断或将中断时间限制在最短时间内，保证用户网上交易活动的顺利进行。\r\n    </li>\r\n    <li>\r\n        本网站有义务对用户在注册使用本网站网上交易平台中所遇到的问题及反映的情况及时作出回复。\r\n    </li>\r\n    <li>\r\n        本网站有权对用户的注册资料进行查阅，对存在任何问题或怀疑的注册资料，本网站有权发出通知询问用户并要求用户做出解释、改正，或直接做出处罚、删除等处理。\r\n    </li>\r\n    <li>\r\n        用  户因在本网站网上交易与其他用户产生纠纷的，用户通过司法部门或行政部门依照法定程序要求本网站提供相关资料，本网站将积极配合并提供有关资料；用户将纠  纷告知本网站，或本网站知悉纠纷情况的，经审核后，本网站有权通过电子邮件及电话联系向纠纷双方了解纠纷情况，并将所了解的情况通过电子邮件互相通知对  方。\r\n    </li>\r\n    <li>\r\n        因网上交易平台的特殊性，本网站没有义务对所有用户的注册资料、所有的交易行为以及与交易有关的其他事项进行事先审查，但如发生以下情形，本网站有权限制用户的活动、向用户核实有关资料、发出警告通知、暂时中止、无限期地中止及拒绝向该用户提供服务：\r\n        <ul>\r\n            <li>\r\n                用户违反本协议或因被提及而纳入本协议的文件；\r\n            </li>\r\n            <li>\r\n                存在用户或其他第三方通知本网站，认为某个用户或具体交易事项存在违法或不当行为，并提供相关证据，而本网站无法联系到该用户核证或验证该用户向本网站提供的任何资料；\r\n            </li>\r\n            <li>\r\n                存在用户或其他第三方通知本网站，认为某个用户或具体交易事项存在违法或不当行为，并提供相关证据。本网站以普通非专业交易者的知识水平标准对相关内容进行判别，可以明显认为这些内容或行为可能对本网站用户或本网站造成财务损失或法律责任。\r\n            </li>\r\n        </ul>\r\n    </li>\r\n    <li>\r\n        在反网络欺诈行动中，本着保护广大用户利益的原则，当用户举报自己交易可能存在欺诈而产生交易争议时，本网站有权通过表面判断暂时冻结相关用户账号，并有权核对当事人身份资料及要求提供交易相关证明材料。\r\n    </li>\r\n    <li>\r\n        根据国家法律法规、本协议的内容和本网站所掌握的事实依据，可以认定用户存在违法或违反本协议行为以及在本网站交易平台上的其他不当行为，本网站有权在本网站交易平台及所在网站上以网络发布形式公布用户的违法行为，并有权随时作出删除相关信息，而无须征得用户的同意。\r\n    </li>\r\n    <li>\r\n        本  网站有权在不通知用户的前提下删除或采取其他限制性措施处理下列信息：包括但不限于以规避费用为目的；以炒作信用为目的；存在欺诈等恶意或虚假内容；与网  上交易无关或不是以交易为目的；存在恶意竞价或其他试图扰乱正常交易秩序因素；该信息违反公共利益或可能严重损害本网站和其他用户合法利益的。\r\n    </li>\r\n    <li>\r\n        用  户授予本网站独家的、全球通用的、永久的、免费的信息许可使用权利，本网站有权对该权利进行再授权，依此授权本网站有权(全部或部份地)  使用、复制、修订、改写、发布、翻译、分发、执行和展示用户公示于网站的各类信息或制作其派生作品，以现在已知或日后开发的任何形式、媒体或技术，将上述  信息纳入其他作品内。\r\n    </li>\r\n</ol>\r\n<p>\r\n    <br />\r\n    <br />\r\n    <strong>五、服务的中断和终止</strong>\r\n</p>\r\n<ol>\r\n    <li>\r\n        在  本网站未向用户收取相关服务费用的情况下，本网站可自行全权决定以任何理由  (包括但不限于本网站认为用户已违反本协议的字面意义和精神，或用户在超过180天内未登录本网站等)  终止对用户的服务，并不再保存用户在本网站的全部资料（包括但不限于用户信息、商品信息、交易信息等）。同时本网站可自行全权决定，在发出通知或不发出通  知的情况下，随时停止提供全部或部分服务。服务终止后，本网站没有义务为用户保留原用户资料或与之相关的任何信息，或转发任何未曾阅读或发送的信息给用户  或第三方。此外，本网站不就终止对用户的服务而对用户或任何第三方承担任何责任。\r\n    </li>\r\n    <li>\r\n        如用户向本网站提出注销本网站注册用户身份，需经本网站审核同意，由本网站注销该注册用户，用户即解除与本网站的协议关系，但本网站仍保留下列权利：\r\n        <ul>\r\n            <li>\r\n                用户注销后，本网站有权保留该用户的资料,包括但不限于以前的用户资料、店铺资料、商品资料和交易记录等。\r\n            </li>\r\n            <li>\r\n                用户注销后，如用户在注销前在本网站交易平台上存在违法行为或违反本协议的行为，本网站仍可行使本协议所规定的权利。\r\n            </li>\r\n        </ul>\r\n    </li>\r\n    <li>\r\n        如存在下列情况，本网站可以通过注销用户的方式终止服务：\r\n        <ul>\r\n            <li>\r\n                在用户违反本协议相关规定时，本网站有权终止向该用户提供服务。本网站将在中断服务时通知用户。但如该用户在被本网站终止提供服务后，再一次直接或间接或以他人名义注册为本网站用户的，本网站有权再次单方面终止为该用户提供服务；\r\n            </li>\r\n            <li>\r\n                一旦本网站发现用户注册资料中主要内容是虚假的，本网站有权随时终止为该用户提供服务；\r\n            </li>\r\n            <li>\r\n                本协议终止或更新时，用户未确认新的协议的。\r\n            </li>\r\n            <li>\r\n                其它本网站认为需终止服务的情况。\r\n            </li>\r\n        </ul>\r\n    </li>\r\n    <li>\r\n        因用户违反相关法律法规或者违反本协议规定等原因而致使本网站中断、终止对用户服务的，对于服务中断、终止之前用户交易行为依下列原则处理：\r\n        <ul>\r\n            <li>\r\n                本网站有权决定是否在中断、终止对用户服务前将用户被中断或终止服务的情况和原因通知用户交易关系方，包括但不限于对该交易有意向但尚未达成交易的用户,参与该交易竞价的用户，已达成交易要约用户。\r\n            </li>\r\n            <li>\r\n                服务中断、终止之前，用户已经上传至本网站的物品尚未交易或交易尚未完成的，本网站有权在中断、终止服务的同时删除此项物品的相关信息。\r\n            </li>\r\n            <li>\r\n                服务中断、终止之前，用户已经就其他用户出售的具体物品作出要约，但交易尚未结束，本网站有权在中断或终止服务的同时删除该用户的相关要约和信息。\r\n            </li>\r\n        </ul>\r\n    </li>\r\n    <li>\r\n        本网站若因用户的行为（包括但不限于刊登的商品、在本网站社区发帖等）侵害了第三方的权利或违反了相关规定，而受到第三方的追偿或受到主管机关的处分时，用户应赔偿本网站因此所产生的一切损失及费用。\r\n    </li>\r\n    <li>\r\n        对违反相关法律法规或者违反本协议规定，且情节严重的用户，本网站有权终止该用户的其它服务。\r\n    </li>\r\n</ol>\r\n<p>\r\n    <br />\r\n    <br />\r\n    <strong>六、协议的修订</strong><br />\r\n    <br />\r\n    本协议可由本网站随时修订，并将修订后的协议公告于本网站之上，修订后的条款内容自公告时起生效，并成为本协议的一部分。用户若在本协议修改之后，仍继续使用本网站，则视为用户接受和自愿遵守修订后的协议。本网站行使修改或中断服务时，不需对任何第三方负责。<br />\r\n    <br />\r\n    <strong>七、 本网站的责任范围 </strong><br />\r\n    <br />\r\n    当用户接受该协议时，用户应明确了解并同意∶\r\n</p>\r\n<ol>\r\n    <li>\r\n        是否经由本网站下载或取得任何资料，由用户自行考虑、衡量并且自负风险，因下载任何资料而导致用户电脑系统的任何损坏或资料流失，用户应负完全责任。\r\n    </li>\r\n    <li>\r\n        用户经由本网站取得的建议和资讯，无论其形式或表现，绝不构成本协议未明示规定的任何保证。\r\n    </li>\r\n    <li>\r\n        基于以下原因而造成的利润、商誉、使用、资料损失或其它无形损失，本网站不承担任何直接、间接、附带、特别、衍生性或惩罚性赔偿（即使本网站已被告知前款赔偿的可能性）：\r\n        <ul>\r\n            <li>\r\n                本网站的使用或无法使用。\r\n            </li>\r\n            <li>\r\n                经由或通过本网站购买或取得的任何物品，或接收之信息，或进行交易所随之产生的替代物品及服务的购买成本。\r\n            </li>\r\n            <li>\r\n                用户的传输或资料遭到未获授权的存取或变更。\r\n            </li>\r\n            <li>\r\n                本网站中任何第三方之声明或行为。\r\n            </li>\r\n            <li>\r\n                本网站其它相关事宜。\r\n            </li>\r\n        </ul>\r\n    </li>\r\n    <li>\r\n        本网站只是为用户提供一个交易的平台，对于用户所刊登的交易物品的合法性、真实性及其品质，以及用户履行交易的能力等，本网站一律不负任何担保责任。用户如果因使用本网站，或因购买刊登于本网站的任何物品，而受有损害时，本网站不负任何补偿或赔偿责任。\r\n    </li>\r\n    <li>\r\n        本  网站提供与其它互联网上的网站或资源的链接，用户可能会因此连结至其它运营商经营的网站，但不表示本网站与这些运营商有任何关系。其它运营商经营的网站均  由各经营者自行负责，不属于本网站控制及负责范围之内。对于存在或来源于此类网站或资源的任何内容、广告、产品或其它资料，本网站亦不予保证或负责。因使  用或依赖任何此类网站或资源发布的或经由此类网站或资源获得的任何内容、物品或服务所产生的任何损害或损失，本网站不负任何直接或间接的责任。\r\n    </li>\r\n</ol>\r\n<p>\r\n    <br />\r\n    <br />\r\n    <strong>八.、不可抗力</strong><br />\r\n    <br />\r\n    因不可抗力或者其他意外事件，使得本协议的履行不可能、不必要或者无意义的，双方均不承担责任。本合同所称之不可抗力意指不能预见、不能避免并不能克服的  客观情况，包括但不限于战争、台风、水灾、火灾、雷击或地震、罢工、暴动、法定疾病、黑客攻击、网络病毒、电信部门技术管制、政府行为或任何其它自然或人  为造成的灾难等客观情况。<br />\r\n    <br />\r\n    <strong>九、争议解决方式</strong>\r\n</p>\r\n<ol>\r\n    <li>\r\n        本协议及其修订本的有效性、履行和与本协议及其修订本效力有关的所有事宜，将受中华人民共和国法律管辖，任何争议仅适用中华人民共和国法律。\r\n    </li>\r\n    <li>\r\n        因  使用本网站服务所引起与本网站的任何争议，均应提交深圳仲裁委员会按照该会届时有效的仲裁规则进行仲裁。相关争议应单独仲裁，不得与任何其它方的争议在任  何仲裁中合并处理，该仲裁裁决是终局，对各方均有约束力。如果所涉及的争议不适于仲裁解决，用户同意一切争议由人民法院管辖。\r\n    </li>\r\n</ol>', 'register', 1, '', 'number'),('reg_pwdlength', '4', 'register', 1, '注册-密码至少长度', 'number'),('reg_symbols', '0', 'register', 1, '注册-符号', 'number'),('reg_uppercase', '0', 'register', 1, '注册-大写字母', 'number'),('required_mysql_version', '5.0', 'site', 1, '', 'string'),('required_php_version', '5.3', 'site', 1, '', 'string'),('reset_pwd', 'faf', 'msg_tpl', 1, '重置密码通知', 'string'),('retain_domain', 'www', 'domain', 1, '', 'string'),('rewrite', '0', 'seo', 1, '', 'string'),('search_words', '茶杯,衣服,美食,电脑,电视,12,67,76,99', 'search', 1, '搜索词', 'string'),('send_chain_code', 'faf', 'msg_tpl', 1, '门店提货通知', 'string'),('send_pickup_code', 'faf', 'msg_tpl', 1, '自提通知', 'string'),('send_vr_code', 'faf', 'msg_tpl', 1, '虚拟兑换码通知', 'string'),('service_station_status', '0', 'operation', 1, '', 'string'),('setting_buyer_logo', '', 'setting', 1, '', 'string'),('setting_email', '552786543@qq.com', 'setting', 1, '', 'string'),('setting_logo', '', 'setting', 1, '', 'string'),('setting_phone', '021-888888,021-112121', 'setting', 1, '', 'string'),('setting_seller_logo', '', 'setting', 1, '', 'string'),('shop_description', '店铺', 'seo', 1, '', 'string'),('shop_domain', '1', 'domain', 1, '', 'string'),('shop_is_open', '1', 'main', 1, '', 'string'),('shop_keyword', '店铺', 'seo', 1, '', 'string'),('shop_title', '店铺{shopname}{sitename}', 'seo', 1, '', 'string'),('site_logo', '', 'site', 1, '', 'string'),('site_name', 'asdfafasdfasdf', 'site', 1, '', 'string'),('site_status', '1', 'site', 1, '', 'number'),('slider1_image', '', 'slider', 1, '团购轮播1', 'string'),('slider2_image', '', 'slider', 1, '团购轮播2', 'string'),('slider3_image', '', 'slider', 1, '团购轮播3', 'string'),('slider4_image', '', 'slider', 1, '团购轮播4', 'string'),('slogo', '', 'main', 1, '', 'string'),('sms_account', '', 'sms', 1, '', 'string'),('sms_pass', '', 'sms', 1, '', 'string'),('sns_description', 'sns', 'seo', 1, '', 'string'),('sns_keyword', 'sns{name}', 'seo', 1, '', 'string'),('sns_title', 'sns{sitename}', 'seo', 1, '', 'string'),('sphinx_search_host', '111123213', 'sphinx', 1, '', 'string'),('sphinx_search_port', '121212', 'sphinx', 1, '', 'string'),('sphinx_statu', '1', 'sphinx', 1, '', 'string'),('statistics_code', '第三方流量统计代码78', 'site', 1, '', 'string'),('stat_is_open', 'fwefe', 'main', 1, '', 'string'),('tg_description', '团购', 'seo', 1, '', 'string'),('tg_description_content', '团购', 'seo', 1, '', 'string'),('tg_keyword', '团购', 'seo', 1, '', 'string'),('tg_keyword_content', '团购', 'seo', 1, '', 'string'),('tg_title', '{sitename}-团购-{name}1', 'seo', 1, '', 'string'),('tg_title_content', '{sitename}-团购{name}', 'seo', 1, '', 'string'),('theme_id', 'default', 'site', 1, '', 'string'),('time_format', 'H:i:s', 'site', 1, '', 'string'),('time_zone_id', 'Asia/Shanghai', 'site', 1, '', 'number'),('title', '用户中心', 'seo', 1, '', 'string'),('user_default_avatar', '', 'site', 1, '', 'string'),('voucher_allow', '1', 'promotion', 1, '代金券功能是否开启', 'number'),('weburl', '', 'main', 1, '', 'string'),('weibo_app_id', '1', 'connect', 1, '', 'string'),('weibo_app_key', '1', 'connect', 1, '', 'string'),('weibo_status', '1', 'connect', 1, '', 'string'),('weixin_app_id', '1', 'connect', 1, '', 'string'),('weixin_app_key', '1', 'connect', 1, '', 'string'),('weixin_status', '1', 'connect', 1, '', 'string');
COMMIT;
-- 2017-05-08 06:08:50