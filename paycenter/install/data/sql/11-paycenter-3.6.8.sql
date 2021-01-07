-- 增加提现类型 --
ALTER TABLE `pay_consume_withdraw` ADD COLUMN `withdraw_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '提现类型 默认为0提现到银行卡 1为提现到微信零钱';
